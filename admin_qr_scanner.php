<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QR Product Scanner - Sawasdee POS</title>
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #dee2e6, #f8f9fa);
      padding-top: 2rem;
    }
    #reader {
      width: 100%;
      max-width: 500px;
      margin: auto;
    }
    .qr-container {
      text-align: center;
      padding: 1rem;
    }
    .alert-info {
      max-width: 500px;
      margin: 1rem auto;
    }
  </style>
</head>
<body>
<div class="qr-container">
  <h3>📷 Scan Product QR Code</h3>
  <p class="text-muted">Using back camera when available (mobile-friendly)</p>
  <div id="reader"></div>
  <div id="result" class="alert alert-info mt-3" style="display: none;"></div>
</div>

<script>
const html5QrCode = new Html5Qrcode("reader");

Html5Qrcode.getCameras().then(devices => {
  if (devices && devices.length) {
    let backCamera = devices.find(d => d.label.toLowerCase().includes('back'));
    let cameraId = backCamera ? backCamera.id : devices[0].id;

    html5QrCode.start(
      cameraId,
      { fps: 10, qrbox: 250 },
      function(decodedText, decodedResult) {
        document.getElementById('result').innerHTML = `<strong>Scanned:</strong> ${decodedText}`;
        document.getElementById('result').style.display = 'block';

        // Redirect to result page
        window.location.href = 'admin_qr_result.php?code=' + encodeURIComponent(decodedText);
      },
      function(errorMessage) {
        // ignore scan errors
      }
    );
  } else {
    document.getElementById("result").innerHTML = "❌ No cameras found.";
    document.getElementById("result").style.display = "block";
  }
}).catch(err => {
  document.getElementById("result").innerHTML = "❌ Camera access denied or unavailable.";
  document.getElementById("result").style.display = "block";
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
