<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Test Print</title>
  <style>
    body {
      font-family: 'Courier New', monospace;
      font-size: 14px;
      margin: 20px;
    }
    .center {
      text-align: center;
      font-weight: bold;
      font-size: 16px;
    }
    .section {
      border-top: 1px dashed #000;
      border-bottom: 1px dashed #000;
      padding: 5px 0;
      margin: 8px 0;
    }
    .footer {
      margin-top: 20px;
      text-align: center;
      font-style: italic;
    }
  </style>
</head>
<body>
  <div class="center">TEST PRINT</div>
  <div>Printer IP: 192.168.18.200</div>
  <div>Time: <?= date('Y-m-d H:i:s') ?></div>

  <div class="section">
    This is a test print from Laravel!
  </div>

  <div class="footer">
    ------------------------------<br>
    <strong>End of Test Print</strong>
  </div>
</body>
</html>
