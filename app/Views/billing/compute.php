<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Compute Bill</title>
</head>
<body>
    <h2>Compute Electric Bill</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p>
        <a href="<?= site_url('billing/dashboard') ?>">Back to Dashboard</a>
        |
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <?php if (session()->getFlashdata('errors')): ?>
        <div style="color:red;">
            <ul>
                <?php foreach ((array) session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <h3>Client Information</h3>
    <p>Account No: <strong><?= esc((string) $client['account_no']) ?></strong></p>
    <p>Client Name: <strong><?= esc((string) $client['full_name']) ?></strong></p>
    <p>Meter No: <strong><?= esc((string) $client['meter_number']) ?></strong></p>

    <h3>Consumption Input</h3>
    <p>Tier rates:</p>
    <ul>
        <li>1-200 kW at 10.00 per kW</li>
        <li>201-500 kW at 13.00 per kW</li>
        <li>501+ kW at 15.00 per kW</li>
    </ul>

    <form method="post" action="<?= site_url('billing/compute/' . $client['id']) ?>">
        <?= csrf_field() ?>

        <div>
            <label for="total_kw">Total Consumption (kW)</label><br>
            <input id="total_kw" type="number" min="1" name="total_kw" value="<?= esc((string) old('total_kw')) ?>" required>
        </div>

        <div id="preview-message" style="margin-top:10px;"></div>

        <div id="preview-box" style="margin-top:12px; display:none;">
            <h4>Live Computation Preview</h4>
            <p>Total Amount: <strong id="preview-total">0.00</strong></p>

            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tier Range</th>
                        <th>kW Used</th>
                        <th>Rate per kW</th>
                        <th>Line Total</th>
                    </tr>
                </thead>
                <tbody id="preview-lines"></tbody>
            </table>
        </div>

        <br>
        <button type="submit">Compute Bill</button>
    </form>

    <script>
        (function () {
            var form = document.querySelector('form');
            var kwInput = document.getElementById('total_kw');
            var previewMessage = document.getElementById('preview-message');
            var previewBox = document.getElementById('preview-box');
            var previewTotal = document.getElementById('preview-total');
            var previewLines = document.getElementById('preview-lines');
            var csrfTokenName = '<?= csrf_token() ?>';
            var csrfHash = '<?= csrf_hash() ?>';
            var previewUrl = '<?= site_url('billing/compute-preview') ?>';
            var debounceHandle = null;

            function updateFormCsrf() {
                var csrfInput = form.querySelector('input[name="' + csrfTokenName + '"]');
                if (csrfInput) {
                    csrfInput.value = csrfHash;
                }
            }

            function showMessage(message, color) {
                previewMessage.style.color = color;
                previewMessage.textContent = message;
            }

            function clearPreview() {
                previewBox.style.display = 'none';
                previewTotal.textContent = '0.00';
                previewLines.innerHTML = '';
            }

            function tierText(line) {
                var max = line.max_kw === null ? 'above' : String(line.max_kw);
                return String(line.min_kw) + ' - ' + max;
            }

            function renderPreview(payload) {
                previewLines.innerHTML = '';

                for (var i = 0; i < payload.lines.length; i++) {
                    var line = payload.lines[i];
                    var row = document.createElement('tr');

                    var tdRange = document.createElement('td');
                    tdRange.textContent = tierText(line);
                    row.appendChild(tdRange);

                    var tdUsed = document.createElement('td');
                    tdUsed.textContent = String(line.kw_used);
                    row.appendChild(tdUsed);

                    var tdRate = document.createElement('td');
                    tdRate.textContent = String(line.rate_per_kw);
                    row.appendChild(tdRate);

                    var tdLineTotal = document.createElement('td');
                    tdLineTotal.textContent = String(line.line_total);
                    row.appendChild(tdLineTotal);

                    previewLines.appendChild(row);
                }

                previewTotal.textContent = String(payload.total_amount);
                previewBox.style.display = 'block';
            }

            function requestPreview(totalKw) {
                var formData = new FormData();
                formData.append('total_kw', totalKw);
                formData.append(csrfTokenName, csrfHash);

                fetch(previewUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { status: response.status, body: data };
                    });
                })
                .then(function (result) {
                    if (result.body && result.body.csrfHash) {
                        csrfHash = result.body.csrfHash;
                        updateFormCsrf();
                    }

                    if (!result.body || !result.body.ok) {
                        clearPreview();

                        if (result.body && result.body.errors) {
                            var firstKey = Object.keys(result.body.errors)[0];
                            showMessage(result.body.errors[firstKey], 'red');
                            return;
                        }

                        showMessage((result.body && result.body.message) ? result.body.message : 'Preview failed.', 'red');
                        return;
                    }

                    showMessage('Preview updated.', 'green');
                    renderPreview(result.body);
                })
                .catch(function () {
                    clearPreview();
                    showMessage('Unable to load preview right now.', 'red');
                });
            }

            kwInput.addEventListener('input', function () {
                var value = kwInput.value.trim();

                if (debounceHandle !== null) {
                    clearTimeout(debounceHandle);
                }

                if (value === '') {
                    clearPreview();
                    showMessage('', 'black');
                    return;
                }

                debounceHandle = setTimeout(function () {
                    requestPreview(value);
                }, 250);
            });

            updateFormCsrf();

            if (kwInput.value.trim() !== '') {
                requestPreview(kwInput.value.trim());
            }
        })();
    </script>
</body>
</html>
