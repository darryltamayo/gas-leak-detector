<?php
/**
 * Quick API Connection Test
 * Tests if the API endpoints are reachable and responding
 */

header('Content-Type: text/html; charset=utf-8');

// Get current URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$currentDomain = $protocol . $_SERVER['HTTP_HOST'];
$baseUrl = $currentDomain . dirname($_SERVER['REQUEST_URI']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Connection Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            margin-bottom: 30px;
            text-align: center;
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        .test-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            margin: 10px 0;
            transition: transform 0.2s;
        }
        .test-button:hover {
            transform: translateY(-2px);
        }
        .result {
            margin-top: 20px;
            padding: 20px;
            border-radius: 10px;
            display: none;
        }
        .result.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            display: block;
        }
        .result.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            display: block;
        }
        .result.info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            display: block;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            margin-top: 10px;
        }
        .loading {
            text-align: center;
            padding: 20px;
            display: none;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 API Connection Test</h1>
        
        <div class="info-box">
            <strong>Server:</strong> <?php echo htmlspecialchars($currentDomain); ?><br>
            <strong>API Base:</strong> <?php echo htmlspecialchars($baseUrl); ?>/api.php<br>
            <strong>Purpose:</strong> Test if ESP32 can reach your API endpoints
        </div>

        <button class="test-button" onclick="testEndpoint('debug')">
            🔍 Test Debug Endpoint
        </button>

        <button class="test-button" onclick="testEndpoint('heartbeat')">
            💓 Test Heartbeat Endpoint
        </button>

        <button class="test-button" onclick="testEndpoint('status')">
            📊 Test Status Endpoint
        </button>

        <button class="test-button" onclick="testEndpoint('device/authorized-numbers')">
            📱 Test Authorized Numbers Endpoint
        </button>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Testing connection...</p>
        </div>

        <div id="result"></div>
    </div>

    <script>
        const baseUrl = '<?php echo $baseUrl; ?>';
        
        async function testEndpoint(endpoint) {
            const resultDiv = document.getElementById('result');
            const loadingDiv = document.getElementById('loading');
            
            // Show loading
            resultDiv.innerHTML = '';
            loadingDiv.style.display = 'block';
            
            try {
                const url = `${baseUrl}/api.php?action=${endpoint}`;
                const response = await fetch(url);
                const contentType = response.headers.get('content-type');
                
                let data;
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    data = { raw: await response.text() };
                }
                
                // Hide loading
                loadingDiv.style.display = 'none';
                
                // Display result
                if (response.ok || response.status === 401) {
                    // 401 is OK - means API is working but needs auth
                    resultDiv.className = 'result success';
                    resultDiv.innerHTML = `
                        <h3>✅ API Endpoint Reachable!</h3>
                        <p><strong>Endpoint:</strong> ${endpoint}</p>
                        <p><strong>Status Code:</strong> ${response.status} ${response.statusText}</p>
                        <p><strong>Response:</strong></p>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                        ${response.status === 401 ? '<p><em>Note: 401 is expected (needs API key). This means the API is working!</em></p>' : ''}
                    `;
                } else {
                    resultDiv.className = 'result error';
                    resultDiv.innerHTML = `
                        <h3>❌ API Error</h3>
                        <p><strong>Endpoint:</strong> ${endpoint}</p>
                        <p><strong>Status Code:</strong> ${response.status} ${response.statusText}</p>
                        <p><strong>Response:</strong></p>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    `;
                }
            } catch (error) {
                loadingDiv.style.display = 'none';
                resultDiv.className = 'result error';
                resultDiv.innerHTML = `
                    <h3>❌ Connection Failed</h3>
                    <p><strong>Endpoint:</strong> ${endpoint}</p>
                    <p><strong>Error:</strong> ${error.message}</p>
                    <p>This means the API is not reachable. Check:</p>
                    <ul>
                        <li>Is the web server running?</li>
                        <li>Is the api.php file uploaded?</li>
                        <li>Are there any .htaccess issues?</li>
                    </ul>
                `;
            }
        }

        // Test debug endpoint on load
        window.onload = () => {
            const resultDiv = document.getElementById('result');
            resultDiv.className = 'result info';
            resultDiv.innerHTML = `
                <h3>ℹ️ Ready to Test</h3>
                <p>Click any button above to test the API endpoints.</p>
                <p><strong>What to expect:</strong></p>
                <ul>
                    <li>✅ <strong>200 OK</strong> or <strong>401 Unauthorized</strong> = API is working!</li>
                    <li>❌ <strong>404 Not Found</strong> = API file missing or wrong path</li>
                    <li>❌ <strong>500 Error</strong> = PHP error, check error logs</li>
                    <li>❌ <strong>Connection Failed</strong> = Server not reachable</li>
                </ul>
            `;
        };
    </script>
</body>
</html>
