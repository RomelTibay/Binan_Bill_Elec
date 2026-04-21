<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\BillLineModel;
use App\Models\BillModel;
use App\Models\ClientModel;
use App\Models\RateTierModel;

class Billing extends BaseController
{
    public function index()
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        $clients = new ClientModel();

        return view('billing/dashboard', [
            'currentUser' => (string) session()->get('full_name'),
            'clients'     => $clients->getClientsForList(),
        ]);
    }

    public function createClient()
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        return redirect()->to('/billing/dashboard')->with('errors', ['Normal users cannot manage client accounts.']);
    }

    public function storeClient()
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        return redirect()->to('/billing/dashboard')->with('errors', ['Normal users cannot manage client accounts.']);
    }

    public function compute(int $clientId)
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        $clients = new ClientModel();
        $client  = $clients->find($clientId);

        if (! $client) {
            return redirect()->to('/billing/dashboard')->with('errors', ['Client not found.']);
        }

        return view('billing/compute', [
            'currentUser' => (string) session()->get('full_name'),
            'client'      => $client,
        ]);
    }

    public function computeTool()
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        return view('billing/compute_tool', [
            'currentUser' => (string) session()->get('full_name'),
        ]);
    }

    public function storeCompute(int $clientId)
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        $rules = [
            'total_kw' => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $clients = new ClientModel();
        $client  = $clients->find($clientId);

        if (! $client) {
            return redirect()->to('/billing/dashboard')->with('errors', ['Client not found.']);
        }

        $totalKw = (int) $this->request->getPost('total_kw');
        $computed = $this->buildTierComputation($totalKw);

        if (! $computed['ok']) {
            return redirect()->back()->withInput()->with('errors', [$computed['message']]);
        }

        $lineRows    = $computed['lines'];
        $totalAmount = $computed['total_amount'];

        $billModel     = new BillModel();
        $billLineModel = new BillLineModel();
        $db            = \Config\Database::connect();

        $db->transStart();

        $billModel->insert([
            'client_id'     => $clientId,
            'computed_by'   => (int) session()->get('user_id'),
            'billing_date'  => date('Y-m-d H:i:s'),
            'total_kw'      => $totalKw,
            'total_amount'  => number_format(round($totalAmount, 2), 2, '.', ''),
        ]);

        $billId = (int) $billModel->getInsertID();

        foreach ($lineRows as $line) {
            $billLineModel->insert([
                'bill_id'      => $billId,
                'tier_id'      => $line['tier_id'],
                'kw_used'      => $line['kw_used'],
                'rate_per_kw'  => $line['rate_per_kw'],
                'line_total'   => $line['line_total'],
            ]);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('errors', ['Failed to save bill computation.']);
        }

        helper('audit');
        log_action('COMPUTE', 'BILLING', 'bills', $billId);

        return redirect()->to('/billing/dashboard')->with('success', 'Bill computed successfully.');
    }

    public function previewCompute()
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return $this->response->setStatusCode(403)->setJSON([
                'ok'        => false,
                'message'   => 'Access denied.',
                'csrfToken' => csrf_token(),
                'csrfHash'  => csrf_hash(),
            ]);
        }

        $rules = [
            'total_kw' => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'        => false,
                'message'   => 'Please enter a valid kW value.',
                'errors'    => $this->validator->getErrors(),
                'csrfToken' => csrf_token(),
                'csrfHash'  => csrf_hash(),
            ]);
        }

        $totalKw = (int) $this->request->getPost('total_kw');
        $computed = $this->buildTierComputation($totalKw);

        if (! $computed['ok']) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok'        => false,
                'message'   => $computed['message'],
                'csrfToken' => csrf_token(),
                'csrfHash'  => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'ok'          => true,
            'total_kw'    => $totalKw,
            'total_amount'=> $computed['total_amount'],
            'lines'       => $computed['lines'],
            'csrfToken'   => csrf_token(),
            'csrfHash'    => csrf_hash(),
        ]);
    }

    public function history()
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        $billModel = new BillModel();

        return view('billing/history', [
            'currentUser' => (string) session()->get('full_name'),
            'bills'       => $billModel->getHistoryForUser((int) session()->get('user_id')),
        ]);
    }

    public function billDetail(int $billId)
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        $billModel = new BillModel();
        $bill      = $billModel->getBillForUser($billId, (int) session()->get('user_id'));

        if (! $bill) {
            return redirect()->to('/billing/history')->with('errors', ['Bill not found or access denied.']);
        }

        $billLines = new BillLineModel();

        return view('billing/bill_detail', [
            'currentUser' => (string) session()->get('full_name'),
            'bill'        => $bill,
            'lines'       => $billLines->getLinesForBill($billId),
        ]);
    }

    public function actionTrails()
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        $logs = new AuditLogModel();

        return view('billing/action_trails', [
            'currentUser' => (string) session()->get('full_name'),
            'logs'        => $logs->getLogsForUser((int) session()->get('user_id')),
        ]);
    }

    private function buildTierComputation(int $totalKw): array
    {
        $tierModel = new RateTierModel();
        $tiers     = $tierModel->getOrderedTiers();

        if (empty($tiers)) {
            return [
                'ok'      => false,
                'message' => 'Rate tiers are not configured.',
            ];
        }

        $lines = [];
        $totalAmount = 0.0;

        foreach ($tiers as $tier) {
            $minKw = (int) $tier['min_kw'];
            $maxKw = $tier['max_kw'] !== null ? (int) $tier['max_kw'] : $totalKw;

            if ($totalKw < $minKw) {
                continue;
            }

            $kwUsed = min($totalKw, $maxKw) - $minKw + 1;
            if ($kwUsed <= 0) {
                continue;
            }

            $rate      = (float) $tier['rate_per_kw'];
            $lineTotal = round($kwUsed * $rate, 2);
            $totalAmount += $lineTotal;

            $lines[] = [
                'tier_id'     => (int) $tier['id'],
                'min_kw'      => $minKw,
                'max_kw'      => $tier['max_kw'] !== null ? (int) $tier['max_kw'] : null,
                'kw_used'     => $kwUsed,
                'rate_per_kw' => number_format($rate, 2, '.', ''),
                'line_total'  => number_format($lineTotal, 2, '.', ''),
            ];
        }

        if (empty($lines)) {
            return [
                'ok'      => false,
                'message' => 'Unable to compute bill from the configured tiers.',
            ];
        }

        return [
            'ok'           => true,
            'total_amount' => number_format(round($totalAmount, 2), 2, '.', ''),
            'lines'        => $lines,
        ];
    }
}