<?php
session_start();
include '../includes/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$total_users = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$total_agents = $conn->query("SELECT COUNT(*) AS c FROM agents")->fetch_assoc()['c'];
$total_parcels = $conn->query("SELECT COUNT(*) AS c FROM parcels")->fetch_assoc()['c'];
$delivered = $conn->query("SELECT COUNT(*) AS c FROM parcels WHERE status='Delivered'")->fetch_assoc()['c'];
$in_transit = $conn->query("SELECT COUNT(*) AS c FROM parcels WHERE status='In Transit'")->fetch_assoc()['c'];
$total_warehouses = $conn->query("SELECT COUNT(*) AS c FROM warehouses WHERE is_active=1")->fetch_assoc()['c'];
?>

<div class="min-h-screen py-10 px-6">
  <div class="w-full max-w-7xl mx-auto">

    <div class="flex justify-between items-center mb-8">
      <div>
        <h2 class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">
          Admin Dashboard
        </h2>
        <p class="text-gray-600 mt-1">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
      </div>
      <a href="logout.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-red-500 to-pink-500 text-white px-5 py-2 rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition-all">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-10">
      
      <a href="manage_users.php" class="glass-card p-6 text-center hover:scale-105 transition-transform cursor-pointer">
        <i class="fa-solid fa-users fa-3x text-blue-600 mb-3"></i>
        <h5 class="font-semibold text-gray-700">Users</h5>
        <p class="text-3xl font-bold text-blue-700"><?php echo $total_users; ?></p>
      </a>

      <a href="manage_agents.php" class="glass-card p-6 text-center hover:scale-105 transition-transform cursor-pointer">
        <i class="fa-solid fa-user-tie fa-3x text-green-600 mb-3"></i>
        <h5 class="font-semibold text-gray-700">Agents</h5>
        <p class="text-3xl font-bold text-green-700"><?php echo $total_agents; ?></p>
      </a>

      <a href="manage_parcels.php" class="glass-card p-6 text-center hover:scale-105 transition-transform cursor-pointer">
        <i class="fa-solid fa-box fa-3x text-yellow-600 mb-3"></i>
        <h5 class="font-semibold text-gray-700">Parcels</h5>
        <p class="text-3xl font-bold text-yellow-700"><?php echo $total_parcels; ?></p>
      </a>

      <a href="manage_parcels.php?status=In Transit" class="glass-card p-6 text-center hover:scale-105 transition-transform cursor-pointer">
        <i class="fa-solid fa-truck-fast fa-3x text-orange-600 mb-3"></i>
        <h5 class="font-semibold text-gray-700">In Transit</h5>
        <p class="text-3xl font-bold text-orange-700"><?php echo $in_transit; ?></p>
      </a>

      <a href="manage_parcels.php?status=Delivered" class="glass-card p-6 text-center hover:scale-105 transition-transform cursor-pointer">
        <i class="fa-solid fa-circle-check fa-3x text-teal-600 mb-3"></i>
        <h5 class="font-semibold text-gray-700">Delivered</h5>
        <p class="text-3xl font-bold text-teal-700"><?php echo $delivered; ?></p>
      </a>

      <a href="manage_warehouses.php" class="glass-card p-6 text-center hover:scale-105 transition-transform cursor-pointer">
        <i class="fa-solid fa-warehouse fa-3x text-purple-600 mb-3"></i>
        <h5 class="font-semibold text-gray-700">Warehouses</h5>
        <p class="text-3xl font-bold text-purple-700"><?php echo $total_warehouses; ?></p>
      </a>
    </div>

    <div class="glass-card p-8">
      <h3 class="text-2xl font-bold text-gray-800 mb-6">Quick Actions</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <a href="manage_users.php" class="p-6 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 text-center">
          <i class="fa-solid fa-users fa-3x text-blue-600 mb-3"></i>
          <h5 class="font-semibold text-gray-700">Manage Users</h5>
        </a>

        <a href="manage_agents.php" class="p-6 bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 text-center">
          <i class="fa-solid fa-user-tie fa-3x text-green-600 mb-3"></i>
          <h5 class="font-semibold text-gray-700">Manage Agents</h5>
        </a>

        <a href="manage_parcels.php" class="p-6 bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 text-center">
          <i class="fa-solid fa-box fa-3x text-yellow-600 mb-3"></i>
          <h5 class="font-semibold text-gray-700">Manage Parcels</h5>
        </a>

        <a href="add_agent.php" class="p-6 bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 text-center">
          <i class="fa-solid fa-user-plus fa-3x text-purple-600 mb-3"></i>
          <h5 class="font-semibold text-gray-700">Add Agent</h5>
        </a>

      </div>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
