<?php
/**
 * ESP32 API Connection Tester
 * 
 * This page simulates ESP32 API calls to test authentication and connectivity
 */

// Get the base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . '://' . $host;

// Detect if we're in subdirectory
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$basePath = $scriptPath;

$apiBaseUrl = $baseUrl . $basePath . '/api';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESP32 API Tester</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
            text-align: center;
        }

        .header h1 {
            color: #2d3748;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .header p {
            color: #718096;
            font-size: 1.1rem;
        }

        .config-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }

        .config-section h2 {
            color: #2d3748;
            font-size: 1.5rem;
            margin-bottom: 20px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Courier New', monospace;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .test-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }

        .test-section h3 {
            color: #2d3748;
            font-size: 1.3rem;
            margin-bottom: 15px;
        }

        .test-section p {
            color: #718096;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            display: inline-block;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .button:active {
            transform: translateY(0);
        }

        .result-box {
            margin-top: 15px;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            white-space: pre-wrap;
            word-wrap: break-word;
            max-height: 400px;
            overflow-y: auto;
        }

        .result-success {
            background: #f0fdf4;
            border: 2px solid #86efac;
            color: #166534;
        }

        .result-error {
            background: #fef2f2;
            border: 2px solid #fca5a5;
            color: #991b1b;
        }

        .result-info {
            background: #eff6ff;
            border: 2px solid #93c5fd;
            color: #1e40af;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-success {
            background: #22c55e;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
        }

        .status-error {
            background: #ef4444;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
        }

        .info-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .info-box strong {
            color: #92400e;
        }

        .code-block {
            background: #1e293b;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 10px 0;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔌 ESP32 API Connection Tester</h1>
            <p>Test ESP32 API endpoints and diagnose authentication issues</p>
        </div>

        <!-- Configuration Section -->
        <div class="config-section">
            <h2>⚙️ Configuration</h2>
            <div class="info-box">
                <strong>⚠️ Important:</strong> Enter your actual Device ID and API Key from the database
            </div>
            
            <div class="form-group">
                <label>Device ID:</label>
                <input type="text" id="deviceId" value="ESP32_OUTLET_001" placeholder="ESP32_OUTLET_001">
            </div>
            
            <div class="form-group">
                <label>Device API Key:</label>
                <input type="text" id="apiKey" value="IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T" placeholder="Your device API key">
            </div>

            <div class="form-group">
                <label>API Base URL:</label>
                <input type="text" id="apiBaseUrl" value="<?= htmlspecialchars($apiBaseUrl) ?>" readonly>
            </div>
        </div>

        <!-- Test Sections -->
        <div class="grid">
            <!-- Test 1: Heartbeat -->
            <div class="test-section">
                <h3>💓 Test 1: Heartbeat</h3>
                <p>This is what ESP32 sends every 30 seconds to stay connected</p>
                <button class="button" onclick="testHeartbeat()">
                    Test Heartbeat
                    <span id="heartbeat-loading" class="loading" style="display:none;"></span>
                </button>
                <div id="heartbeat-result"></div>
            </div>

            <!-- Test 2: Authorized Numbers -->
            <div class="test-section">
                <h3>📋 Test 2: Authorized Numbers</h3>
                <p>Fetch list of authorized SIM numbers for SMS control</p>
                <button class="button" onclick="testAuthorizedNumbers()">
                    Test Authorized Numbers
                    <span id="auth-loading" class="loading" style="display:none;"></span>
                </button>
                <div id="auth-result"></div>
            </div>

            <!-- Test 3: Status -->
            <div class="test-section">
                <h3>📊 Test 3: Get Status</h3>
                <p>Get current outlet status and control mode</p>
                <button class="button" onclick="testStatus()">
                    Test Get Status
                    <span id="status-loading" class="loading" style="display:none;"></span>
                </button>
                <div id="status-result"></div>
            </div>

            <!-- Test 4: Schedules -->
            <div class="test-section">
                <h3>📅 Test 4: Get Schedules</h3>
                <p>Fetch active schedules for this device</p>
                <button class="button" onclick="testSchedules()">
                    Test Get Schedules
                    <span id="schedules-loading" class="loading" style="display:none;"></span>
                </button>
                <div id="schedules-result"></div>
            </div>
        </div>

        <!-- Database Verification -->
        <div class="test-section">
            <h2>🔍 Database Verification</h2>
            <p>Check if device exists in database with correct API key</p>
            <button class="button" onclick="verifyDatabase()">
                Verify Database
                <span id="db-loading" class="loading" style="display:none;"></span>
            </button>
            <div id="db-result"></div>
        </div>

        <!-- Run All Tests -->
        <div class="test-section">
            <h2>🚀 Run All Tests</h2>
            <p>Run all tests sequentially and show complete results</p>
            <button class="button" onclick="runAllTests()">
                Run All Tests
                <span id="all-loading" class="loading" style="display:none;"></span>
            </button>
            <div id="all-result"></div>
        </div>
    </div>

    <script>
        const baseUrl = document.getElementById('apiBaseUrl').value;

        function getDeviceId() {
            return document.getElementById('deviceId').value;
        }

        function getApiKey() {
            return document.getElementById('apiKey').value;
        }

        function showLoading(id, show) {
            const loader = document.getElementById(id + '-loading');
            if (loader) {
                loader.style.display = show ? 'inline-block' : 'none';
            }
        }

        function displayResult(elementId, data, success, details = null) {
            const el = document.getElementById(elementId);
            const resultClass = success ? 'result-success' : 'result-error';
            const statusClass = success ? 'status-success' : 'status-error';
            
            let html = `<div class="result-box ${resultClass}">`;
            html += `<span class="status-indicator ${statusClass}"></span>`;
            html += `<strong>${success ? '✅ SUCCESS' : '❌ FAILED'}</strong>\n\n`;
            
            if (details) {
                html += `<strong>Request Details:</strong>\n${details}\n\n`;
            }
            
            html += `<strong>Response:</strong>\n`;
            html += JSON.stringify(data, null, 2);
            html += `</div>`;
            
            el.innerHTML = html;
        }

        async function testHeartbeat() {
            showLoading('heartbeat', true);
            const deviceId = getDeviceId();
            const apiKey = getApiKey();

            try {
                const payload = {
                    device_id: deviceId,
                    api_key: apiKey,
                    ip_address: '<?= $_SERVER['REMOTE_ADDR'] ?? 'unknown' ?>',
                    rssi: -55,
                    outlet_states: [{outlet: 1, state: "off"}]
                };

                const response = await fetch(baseUrl + '/heartbeat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                const details = `POST ${baseUrl}/heartbeat\nStatus: ${response.status}\nPayload: ${JSON.stringify(payload, null, 2)}`;
                displayResult('heartbeat-result', data, response.ok, details);
            } catch (error) {
                displayResult('heartbeat-result', {error: error.message}, false);
            } finally {
                showLoading('heartbeat', false);
            }
        }

        async function testAuthorizedNumbers() {
            showLoading('auth', true);
            const deviceId = getDeviceId();
            const apiKey = getApiKey();

            try {
                const url = `${baseUrl}/device/authorized-numbers?device_id=${encodeURIComponent(deviceId)}&api_key=${encodeURIComponent(apiKey)}`;
                
                const response = await fetch(url, {
                    method: 'GET'
                });

                const data = await response.json();
                const details = `GET ${url}\nStatus: ${response.status}`;
                displayResult('auth-result', data, response.ok, details);
            } catch (error) {
                displayResult('auth-result', {error: error.message}, false);
            } finally {
                showLoading('auth', false);
            }
        }

        async function testStatus() {
            showLoading('status', true);
            const deviceId = getDeviceId();
            const apiKey = getApiKey();

            try {
                const url = `${baseUrl}/status?device_id=${encodeURIComponent(deviceId)}&api_key=${encodeURIComponent(apiKey)}`;
                
                const response = await fetch(url, {
                    method: 'GET'
                });

                const data = await response.json();
                const details = `GET ${url}\nStatus: ${response.status}`;
                displayResult('status-result', data, response.ok, details);
            } catch (error) {
                displayResult('status-result', {error: error.message}, false);
            } finally {
                showLoading('status', false);
            }
        }

        async function testSchedules() {
            showLoading('schedules', true);
            const deviceId = getDeviceId();
            const apiKey = getApiKey();

            try {
                const url = `${baseUrl}/schedules?device_id=${encodeURIComponent(deviceId)}&api_key=${encodeURIComponent(apiKey)}`;
                
                const response = await fetch(url, {
                    method: 'GET'
                });

                const data = await response.json();
                const details = `GET ${url}\nStatus: ${response.status}`;
                displayResult('schedules-result', data, response.ok, details);
            } catch (error) {
                displayResult('schedules-result', {error: error.message}, false);
            } finally {
                showLoading('schedules', false);
            }
        }

        async function verifyDatabase() {
            showLoading('db', true);
            const deviceId = getDeviceId();

            try {
                const response = await fetch('test-esp32-api-db.php?device_id=' + encodeURIComponent(deviceId));
                const data = await response.json();
                displayResult('db-result', data, data.success || false);
            } catch (error) {
                displayResult('db-result', {error: error.message}, false);
            } finally {
                showLoading('db', false);
            }
        }

        async function runAllTests() {
            showLoading('all', true);
            const resultEl = document.getElementById('all-result');
            resultEl.innerHTML = '<div class="result-box result-info">Running all tests...</div>';

            const tests = [
                {name: 'Heartbeat', fn: testHeartbeat},
                {name: 'Authorized Numbers', fn: testAuthorizedNumbers},
                {name: 'Status', fn: testStatus},
                {name: 'Schedules', fn: testSchedules},
                {name: 'Database Verification', fn: verifyDatabase}
            ];

            for (const test of tests) {
                await test.fn();
                await new Promise(resolve => setTimeout(resolve, 1000)); // Wait 1 second between tests
            }

            resultEl.innerHTML = '<div class="result-box result-success"><strong>✅ All tests completed!</strong>\n\nCheck individual test results above.</div>';
            showLoading('all', false);
        }

        // Show initial info
        window.addEventListener('load', () => {
            console.log('ESP32 API Tester loaded');
            console.log('API Base URL:', baseUrl);
        });
    </script>
</body>
</html>
