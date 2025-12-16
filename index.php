<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCourier V2 | Advanced Parcel Tracking</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    html {
      scroll-behavior: smooth;
    }

    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', sans-serif;
    }

    .main-card {
      background-color: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 24px;
      box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
      padding: 50px 40px;
      text-align: center;
      max-width: 450px;
      width: 100%;
      border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .title {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-weight: 700;
      font-size: 2.2rem;
    }

    .subtitle {
      color: #475569;
      font-size: 1rem;
      margin-bottom: 30px;
    }

    .btn-custom {
      border: none;
      border-radius: 12px;
      width: 100%;
      padding: 12px 0;
      font-weight: 600;
      color: #fff;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-custom:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }

    footer {
      text-align: center;
      margin-top: 40px;
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.9);
    }
  </style>
</head>
<body>
  <div>
    <div class="main-card">
      <h1 class="title mb-2"><i class="fa-solid fa-truck-fast"></i> SmartCourier V2</h1>
      <p class="subtitle">Advanced Parcel Tracking with Real-Time Maps</p>

      <div class="d-flex flex-column gap-3">
        <a href="user/user_login.php" class="btn btn-custom">
          <i class="fa-solid fa-user"></i> User Login
        </a>
        <a href="agent/agent_login.php" class="btn btn-custom">
          <i class="fa-solid fa-user-tie"></i> Agent Login
        </a>
        <a href="admin/admin_login.php" class="btn btn-custom">
          <i class="fa-solid fa-user-shield"></i> Admin Login
        </a>
      </div>
    </div>

    <footer>
      © 2025 SmartCourier V2 | All Rights Reserved
    </footer>
  </div>
</body>
</html>
