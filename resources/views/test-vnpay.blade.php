<!DOCTYPE html>
<html>
<head>
    <title>Test VNPay Callback</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .result { margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test VNPay Callback</h1>
        
        <form id="callbackForm">
            <div class="form-group">
                <label>Callback URL:</label>
                <input type="text" id="callbackUrl" value="http://localhost:8000/payment/vnpay/callback">
            </div>
            
            <div class="form-group">
                <label>Test Parameters (JSON):</label>
                <textarea id="testParams" rows="10">{
    "vnp_Amount": "25000000",
    "vnp_BankCode": "NCB",
    "vnp_BankTranNo": "VNP15202978",
    "vnp_CardType": "ATM",
    "vnp_OrderInfo": "Thanh toan don hang #1",
    "vnp_PayDate": "20251014143000",
    "vnp_ResponseCode": "00",
    "vnp_TmnCode": "58X4B4HP",
    "vnp_TransactionNo": "15202978",
    "vnp_TxnRef": "1_1728912345",
    "vnp_SecureHash": "test_hash"
}</textarea>
            </div>
            
            <button type="button" onclick="testCallback()">Test Callback</button>
        </form>
        
        <div id="result" class="result" style="display: none;"></div>
    </div>

    <script>
        async function testCallback() {
            const url = document.getElementById('callbackUrl').value;
            const params = JSON.parse(document.getElementById('testParams').value);
            
            // Convert to URL parameters
            const urlParams = new URLSearchParams(params);
            const fullUrl = url + '?' + urlParams.toString();
            
            try {
                const response = await fetch(fullUrl);
                const result = await response.text();
                
                document.getElementById('result').innerHTML = `
                    <h3>Response:</h3>
                    <p><strong>Status:</strong> ${response.status}</p>
                    <p><strong>URL:</strong> ${fullUrl}</p>
                    <pre>${result}</pre>
                `;
                document.getElementById('result').style.display = 'block';
            } catch (error) {
                document.getElementById('result').innerHTML = `
                    <h3>Error:</h3>
                    <p>${error.message}</p>
                `;
                document.getElementById('result').style.display = 'block';
            }
        }
    </script>
</body>
</html>
