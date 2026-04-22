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
        $userId  = (int) session()->get('user_id');

        return view('billing/dashboard', [
            'currentUser' => (string) session()->get('full_name'),
            'clients'     => $clients->getClientsForUser($userId),
        ]);
    }

    public function createClient()
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        return view('billing/client_create', [
            'currentUser' => (string) session()->get('full_name'),
        ]);
    }

    public function storeClient()
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        $rules = [
            'full_name'    => 'required|min_length[3]|max_length[120]',
            'address'      => 'required|min_length[3]|max_length[255]',
            'meter_number' => 'required|min_length[3]|max_length[50]|is_unique[clients.meter_number]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $clients = new ClientModel();
        $userId  = (int) session()->get('user_id');

        $accountNo = $this->generateAccountNumber($clients);
        if ($accountNo === null) {
            return redirect()->back()->withInput()->with('errors', ['Unable to generate account number. Please try again.']);
        }

        $data = [
            'account_no'   => $accountNo,
            'full_name'    => trim((string) $this->request->getPost('full_name')),
            'address'      => trim((string) $this->request->getPost('address')),
            'meter_number' => trim((string) $this->request->getPost('meter_number')),
            'created_by'   => $userId,
        ];

        if (! $clients->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $clients->errors());
        }

        $clientId = (int) $clients->getInsertID();

        helper('audit');
        log_action(
            'CREATE',
            'BILLING',
            'clients',
            $clientId,
            sprintf('Created client %s (%s) with account %s.', $data['full_name'], $data['meter_number'], $accountNo)
        );

        return redirect()->to('/billing/dashboard')->with('success', 'Client added successfully.');
    }

    public function compute(int $clientId)
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        $clients = new ClientModel();
        $client  = $clients->findClientForUser($clientId, (int) session()->get('user_id'));

        if (! $client) {
            return redirect()->to('/billing/dashboard')->with('errors', ['Client not found or access denied.']);
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
        $client  = $clients->findClientForUser($clientId, (int) session()->get('user_id'));

        if (! $client) {
            return redirect()->to('/billing/dashboard')->with('errors', ['Client not found or access denied.']);
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
        $description = sprintf(
            'Computed bill #%d for client %s (%s): %d kW, total PHP %s.',
            $billId,
            (string) ($client['full_name'] ?? 'Unknown Client'),
            (string) ($client['account_no'] ?? 'No Account'),
            $totalKw,
            number_format((float) $totalAmount, 2, '.', '')
        );
        log_action('COMPUTE', 'BILLING', 'bills', $billId, $description);

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
            'logs'        => $logs->getBillingLogsForUser((int) session()->get('user_id')),
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

    private function generateAccountNumber(ClientModel $clients): ?string
    {
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $candidate = sprintf('ACC-%s-%04d', date('Ymd'), random_int(0, 9999));

            $exists = $clients->where('account_no', $candidate)->first();
            if (! $exists) {
                return $candidate;
            }
        }

        return null;
    }
}