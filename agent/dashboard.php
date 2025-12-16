<?php
session_start();
include '../includes/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['agent_id'])) {
    header("Location: agent_login.php");
    exit();
}

$aid = $_SESSION['agent_id'];

$assigned = $conn->query("SELECT COUNT(*) AS total FROM parcels WHERE assigned_agent=$aid")->fetch_assoc()['total'];
$delivered = $conn->query("SELECT COUNT(*) AS total FROM parcels WHERE assigned_agent=$aid AND status='Delivered'")->fetch_assoc()['total'];
$in_transit = $conn->query("SELECT COUNT(*) AS total FROM parcels WHERE assigned_agent=$aid AND status='In Transit'")->fetch_assoc()['total'];
$pending = $conn->query("SELECT COUNT(*) AS total FROM parcels WHERE assigned_agent=$aid AND status NOT IN ('Delivered', 'In Transit')")->fetch_assoc()['total'];

$warehouse_info = $conn->query("SELECT w.* FROM agents a JOIN warehouses w ON a.warehouse_id = w.warehouse_id WHERE a.agent_id = $aid")->fetch_assoc();
?>

<div class="min-h-screen py-12 px-6">
  <div class="w-full max-w-6xl mx-auto glass-card p-8 relative overflow-hidden">

    <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-green-500 to-blue-600 rounded-full opacity-20 blur-2xl"></div>
    <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-gradient-to-tr from-blue-500 to-teal-500 rounded-full opacity-20 blur-2xl"></div>

    <div class="flex flex-col md:flex-row items-center justify-between mb-8 relative z-10">
      <div>
        <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">
          Welcome, <?php echo htmlspecialchars($_SESSION['agent_name']); ?>
        </h2>
        <p class="text-gray-600 mt-1">
          <i class="fa-solid fa-warehouse"></i> Base: <?php echo $warehouse_info['city_name'] . ', ' . $warehouse_info['state_name']; ?>
        </p>
      </div>
      <a href="logout.php"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-red-500 to-pink-500 text-white px-5 py-2 rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10 text-center relative z-10">
      
      <a href="assigned_parcels.php" class="p-6 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 shadow-lg hover:shadow-xl transition-transform hover:-translate-y-1 cursor-pointer">
        <i class="fa-solid fa-truck text-blue-600 fa-3x mb-3"></i>
        <h5 class="font-semibold text-gray-700">Assigned Parcels</h5>
        <h3 class="text-3xl font-bold text-blue-700"><?php echo $assigned; ?></h3>
      </a>

      <a href="assigned_parcels.php?status=In Transit" class="p-6 rounded-2xl bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 shadow-lg hover:shadow-xl transition-transform hover:-translate-y-1 cursor-pointer">
        <i class="fa-solid fa-truck-fast text-yellow-600 fa-3x mb-3"></i>
        <h5 class="font-semibold text-gray-700">In Transit</h5>
        <h3 class="text-3xl font-bold text-yellow-700"><?php echo $in_transit; ?></h3>
      </a>

      <a href="assigned_parcels.php?status=Delivered" class="p-6 rounded-2xl bg-gradient-to-br from-green-50 to-green-100 border border-green-200 shadow-lg hover:shadow-xl transition-transform hover:-translate-y-1 cursor-pointer">
        <i class="fa-solid fa-circle-check text-green-600 fa-3x mb-3"></i>
        <h5 class="font-semibold text-gray-700">Delivered</h5>
        <h3 class="text-3xl font-bold text-green-700"><?php echo $delivered; ?></h3>
      </a>

      <a href="assigned_parcels.php?status=Pending" class="p-6 rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 shadow-lg hover:shadow-xl transition-transform hover:-translate-y-1 cursor-pointer">
        <i class="fa-solid fa-clock text-purple-600 fa-3x mb-3"></i>
        <h5 class="font-semibold text-gray-700">Pending</h5>
        <h3 class="text-3xl font-bold text-purple-700"><?php echo $pending; ?></h3>
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-center relative z-10">
      
      <a href="assigned_parcels.php" class="transform hover:-translate-y-1 transition-all">
        <div class="p-6 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 shadow-lg hover:shadow-xl">
          <i class="fa-solid fa-boxes-packing fa-3x text-blue-600 mb-3"></i>
          <h5 class="font-semibold text-gray-700">Assigned Parcels</h5>
        </div>
      </a>

      <a href="update_location.php" class="transform hover:-translate-y-1 transition-all">
        <div class="p-6 rounded-2xl bg-gradient-to-br from-green-50 to-green-100 border border-green-200 shadow-lg hover:shadow-xl">
          <i class="fa-solid fa-location-dot fa-3x text-green-600 mb-3"></i>
          <h5 class="font-semibold text-gray-700">Update Location</h5>
        </div>
      </a>

    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
