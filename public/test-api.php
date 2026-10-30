<!DOCTYPE html>
<html>
<head>
    <title>API Test - Smart AC Outlet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2D7A5B;
            border-bottom: 3px solid #2D7A5B;
            padding-bottom: 10px;
        }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #2D7A5B;
        }
        button {
            background: #2D7A5B;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background: #25694B;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .result {
            margin-top: 15px;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .success {
            background: #d4edda;
            border: 1px solid #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #dc3545;
            color: #721c24;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #0c5460;
            color: #0c5460;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        input {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            max-width: 500px;
            font-family: monospace;
        }
        label {
            display: block;
            margin: 10px 0 5px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 Smart AC Outlet - API Test Tool</h1>
        
        <div class="warning">
            <strong>⚠️ SECURITY WARNING:</strong> This is a diagnostic tool. Delete this file after fixing your API issues!
        </div>
        
        <p><strong>Purpose:</strong> Test API endpoints and diagnose connection issues with your ESP32 device.</p>
        
        <div class="test-section">
            <h2>Configuration</h2>
            <label for="apiKey">API Key:</label>
            <input type="text" id="apiKey" value="IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T" placeholder="Enter your API key">
            
            <label for="deviceId">Device ID:</label>
            <input type="text" id="deviceId" value="ESP32_OUTLET_001" placeholder="Enter your device ID">
        </div>
        
        <div class="test-section">
            <h2>Test 1: Debug Endpoint</h2>
            <p>Check if API routing is working and .env is loaded</p>
            <button onclick="testDebug()">🔍 Test Debug Endpoint</button>
            <div id="debugResult"></div>
        </div>
        
        <div class="test-section">
            <h2>Test 2: Heartbeat</h2>
            <p>Simulate ESP32 heartbeat (what ESP32 sends every 60 seconds)</p>
            <button onclick="testHeartbeat()">💓 Send Heartbeat</button>
            <div id="heartbeatResult"></div>
        </div>
        
        <div class="test-section">
            <h2>Test 3: Get Status</h2>
            <p>Get current outlet status from database</p>
            <button onclick="testStatus()">📊 Get Status</button>
            <div id="statusResult"></div>
        </div>
        
        <div class="test-section">
            <h2>Test 4: Get Schedules</h2>
            <p>Get active schedules for the device</p>
            <button onclick="testSchedules()">📅 Get Schedules</button>
            <div id="schedulesResult"></div>
        </div>
        
        <div class="test-section">
            <h2>Test 5: Validate SIM</h2>
            <p>Check if a phone number is authorized</p>
            <label for="phoneNumber">Phone Number:</label>
            <input type="text" id="phoneNumber" value="+639171234567" placeholder="+639171234567">
            <br><br>
            <button onclick="testValidateSim()">📱 Validate SIM</button>
            <div id="validateSimResult"></div>
        </div>
        
        <div class="test-section">
            <h2>ESP32 Connection Simulator</h2>
            <p>Simulate what ESP32 does when it connects:</p>
            <button onclick="simulateESP32Connection()">🤖 Simulate ESP32 Full Connection</button>
            <div id="esp32SimResult"></div>
        </div>
        
    </div>

    <script>
        const baseUrl = window.location.origin + '/ac_controller_system/api';
        
        function getApiKey() {
            return document.getElementById('apiKey').value;
        }
        
        function getDeviceId() {
            return document.getElementById('deviceId').value;
        }
        
        function showResult(elementId, data, success) {
            const element = document.getElementById(elementId);
            const className = success ? 'success' : 'error';
            element.innerHTML = `<div class="result ${className}">${JSON.stringify(data, null, 2)}</div>`;
        }
        
        function showInfo(elementId, message) {
            const element = document.getElementById(elementId);
            element.innerHTML = `<div class="result info">${message}</div>`;
        }
        
        async function testDebug() {
            showInfo('debugResult', 'Testing...');
            try {
                const response = await fetch(`${baseUrl}/debug?api_key=${getApiKey()}`);
                const data = await response.json();
                showResult('debugResult', data, response.ok);
            } catch (error) {
                showResult('debugResult', { error: error.message }, false);
            }
        }
        
        async function testHeartbeat() {
            showInfo('heartbeatResult', 'Sending heartbeat...');
            try {
                const response = await fetch(`${baseUrl}/heartbeat`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-API-KEY': getApiKey()
                    },
                    body: JSON.stringify({
                        device_id: getDeviceId()
                    })
                });
                const data = await response.json();
                showResult('heartbeatResult', data, response.ok);
            } catch (error) {
                showResult('heartbeatResult', { error: error.message }, false);
            }
        }
        
        async function testStatus() {
            showInfo('statusResult', 'Getting status...');
            try {
                const response = await fetch(`${baseUrl}/status?device_id=${getDeviceId()}&api_key=${getApiKey()}`);
                const data = await response.json();
                showResult('statusResult', data, response.ok);
            } catch (error) {
                showResult('statusResult', { error: error.message }, false);
            }
        }
        
        async function testSchedules() {
            showInfo('schedulesResult', 'Getting schedules...');
            try {
                const response = await fetch(`${baseUrl}/schedules?device_id=${getDeviceId()}&api_key=${getApiKey()}`);
                const data = await response.json();
                showResult('schedulesResult', data, response.ok);
            } catch (error) {
                showResult('schedulesResult', { error: error.message }, false);
            }
        }
        
        async function testValidateSim() {
            showInfo('validateSimResult', 'Validating SIM...');
            const phoneNumber = document.getElementById('phoneNumber').value;
            try {
                const response = await fetch(`${baseUrl}/sms/validate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-API-KEY': getApiKey()
                    },
                    body: JSON.stringify({
                        phone_number: phoneNumber
                    })
                });
                const data = await response.json();
                showResult('validateSimResult', data, response.ok);
            } catch (error) {
                showResult('validateSimResult', { error: error.message }, false);
            }
        }
        
        async function simulateESP32Connection() {
            const resultDiv = document.getElementById('esp32SimResult');
            resultDiv.innerHTML = '<div class="result info">Simulating ESP32 connection sequence...</div>';
            
            const steps = [];
            
            // Step 1: Heartbeat
            try {
                resultDiv.innerHTML += '<br>Step 1: Sending heartbeat...';
                const response = await fetch(`${baseUrl}/heartbeat`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-API-KEY': getApiKey()
                    },
                    body: JSON.stringify({
                        device_id: getDeviceId()
                    })
                });
                const data = await response.json();
                steps.push({ step: 'Heartbeat', success: response.ok, data });
                resultDiv.innerHTML += response.ok ? ' ✅' : ' ❌';
            } catch (error) {
                steps.push({ step: 'Heartbeat', success: false, error: error.message });
                resultDiv.innerHTML += ' ❌';
            }
            
            // Step 2: Get Status
            try {
                resultDiv.innerHTML += '<br>Step 2: Getting status...';
                const response = await fetch(`${baseUrl}/status?device_id=${getDeviceId()}&api_key=${getApiKey()}`);
                const data = await response.json();
                steps.push({ step: 'Get Status', success: response.ok, data });
                resultDiv.innerHTML += response.ok ? ' ✅' : ' ❌';
            } catch (error) {
                steps.push({ step: 'Get Status', success: false, error: error.message });
                resultDiv.innerHTML += ' ❌';
            }
            
            // Step 3: Get Schedules
            try {
                resultDiv.innerHTML += '<br>Step 3: Downloading schedules...';
                const response = await fetch(`${baseUrl}/schedules?device_id=${getDeviceId()}&api_key=${getApiKey()}`);
                const data = await response.json();
                steps.push({ step: 'Get Schedules', success: response.ok, data });
                resultDiv.innerHTML += response.ok ? ' ✅' : ' ❌';
            } catch (error) {
                steps.push({ step: 'Get Schedules', success: false, error: error.message });
                resultDiv.innerHTML += ' ❌';
            }
            
            // Summary
            const allSuccess = steps.every(s => s.success);
            resultDiv.innerHTML += `<br><br><div class="result ${allSuccess ? 'success' : 'error'}">`;
            resultDiv.innerHTML += `<strong>Simulation ${allSuccess ? 'SUCCESSFUL' : 'FAILED'}</strong><br><br>`;
            resultDiv.innerHTML += `Full Results:<br>${JSON.stringify(steps, null, 2)}`;
            resultDiv.innerHTML += '</div>';
        }
    </script>
</body>
</html>
