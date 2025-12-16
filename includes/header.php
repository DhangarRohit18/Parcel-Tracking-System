<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCourier V2 - Advanced Parcel Tracking</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  
  <style>
    html {
      scroll-behavior: smooth;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
    }

    .glass-card {
      backdrop-filter: blur(16px) saturate(180%);
      background-color: rgba(255, 255, 255, 0.85);
      border: 1px solid rgba(255, 255, 255, 0.5);
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
      border-radius: 1.5rem;
    }

    #map {
      height: 400px;
      width: 100%;
      border-radius: 1rem;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    ::-webkit-scrollbar {
      width: 10px;
    }
    
    ::-webkit-scrollbar-track {
      background: #f1f1f1;
    }
    
    ::-webkit-scrollbar-thumb {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 5px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
  </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg shadow-sm"
     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            backdrop-filter: blur(10px);
            padding: 0.75rem 0;">
  <div class="container">
    
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2 text-white"
       href="../index.php"
       style="font-size: 1.4rem; letter-spacing: 0.5px;">
      <i class="fa-solid fa-truck-fast"></i>
      <span>SmartCourier V2</span>
    </a>

    <button class="navbar-toggler text-white border-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center gap-2">
        <li class="nav-item">
          <a href="../index.php" class="nav-link text-white fw-semibold px-3 py-2 rounded"
             style="transition: all 0.3s ease;"
             onmouseover="this.style.background='rgba(255,255,255,0.15)';"
             onmouseout="this.style.background='transparent';">
            <i class="fa-solid fa-house me-1"></i> Home
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
