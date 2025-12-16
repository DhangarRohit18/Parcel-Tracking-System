<?php
session_start();
include '../includes/db_connect.php';
if(!isset($_SESSION['user_id'])){
    header("Location: user_login.php");
    exit();
}
include '../includes/header.php';

$uid = $_SESSION['user_id'];

$total = $conn->query("SELECT COUNT(*) AS total FROM parcels WHERE user_id=$uid")->fetch_assoc()['total'];
$delivered = $conn->query("SELECT COUNT(*) AS total FROM parcels WHERE user_id=$uid AND status='Delivered'")->fetch_assoc()['total'];
$pending = $conn->query("SELECT COUNT(*) AS total FROM parcels WHERE user_id=$uid AND status!='Delivered'")->fetch_assoc()['total'];
$in_transit = $conn->query("SELECT COUNT(*) AS total FROM parcels WHERE user_id=$uid AND status='In Transit'")->fetch_assoc()['total'];
?>

<div class="min-h-screen flex flex-col items-center justify-center px-6 py-10">

  <div class="w-full max-w-6xl glass-card p-8 relative overflow-hidden">

    <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full opacity-20 blur-2xl"></div>
    <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-gradient-to-tr from-purple-500 to-pink-500 rounded-full opacity-20 blur-2xl"></div>

    <div class="flex flex-col md:flex-row items-center justify-between mb-8">
      <h3 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">
        Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
      </h3>
      <a href="logout.php"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-red-500 to-pink-500 text-white px-5 py-2 rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </a>
    </div>

    <p class="text-center text-gray-600 mb-8">Manage all your parcel tracking activities at one place.</p>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10 text-center">
      
      <a href="my_parcels.php" class="p-6 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 shadow-lg hover:shadow-xl transition-transform hover:-translate-y-1 cursor-pointer">
        <i class="fa-solid fa-box fa-3x text-blue-600 mb-3"></i>
        <h5 class="font-semibold text-gray-700">Total Parcels</h5>
        <h3 class="text-3xl font-bold text-blue-700"><?php echo $total; ?></h3>
      </a>

      <a href="my_parcels.php?status=In Transit" class="p-6 rounded-2xl bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 shadow-lg hover:shadow-xl transition-transform hover:-translate-y-1 cursor-pointer">
        <i class="fa-solid fa-truck-fast fa-3x text-yellow-600 mb-3"></i>
        <h5 class="font-semibold text-gray-700">In Transit</h5>
        <h3 class="text-3xl font-bold text-yellow-700"><?php echo $in_transit; ?></h3>
      </a>

      <a href="my_parcels.php?status=Delivered" class="p-6 rounded-2xl bg-gradient-to-br from-green-50 to-green-100 border border-green-200 shadow-lg hover:shadow-xl transition-transform hover:-translate-y-1 cursor-pointer">
        <i class="fa-solid fa-circle-check fa-3x text-green-600 mb-3"></i>
        <h5 class="font-semibold text-gray-700">Delivered</h5>
        <h3 class="text-3xl font-bold text-green-700"><?php echo $delivered; ?></h3>
      </a>

      <a href="my_parcels.php?status=Pending" class="p-6 rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 shadow-lg hover:shadow-xl transition-transform hover:-translate-y-1 cursor-pointer">
        <i class="fa-solid fa-clock fa-3x text-purple-600 mb-3"></i>
        <h5 class="font-semibold text-gray-700">Pending</h5>
        <h3 class="text-3xl font-bold text-purple-700"><?php echo $pending; ?></h3>
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">

      <a href="book_parcel.php" class="transform hover:-translate-y-1 transition-all">
        <div class="p-6 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 shadow-lg hover:shadow-xl">
          <i class="fa-solid fa-box-open fa-3x text-blue-600 mb-3"></i>
          <h5 class="font-semibold text-gray-700">Book Parcel</h5>
        </div>
      </a>

      <a href="my_parcels.php" class="transform hover:-translate-y-1 transition-all">
        <div class="p-6 rounded-2xl bg-gradient-to-br from-green-50 to-green-100 border border-green-200 shadow-lg hover:shadow-xl">
          <i class="fa-solid fa-list fa-3x text-green-600 mb-3"></i>
          <h5 class="font-semibold text-gray-700">My Parcels</h5>
        </div>
      </a>

      <a href="track_parcel.php" class="transform hover:-translate-y-1 transition-all">
        <div class="p-6 rounded-2xl bg-gradient-to-br from-pink-50 to-pink-100 border border-pink-200 shadow-lg hover:shadow-xl">
          <i class="fa-solid fa-location-dot fa-3x text-pink-600 mb-3"></i>
          <h5 class="font-semibold text-gray-700">Track Parcel</h5>
        </div>
      </a>

    </div>

    <div class="text-center mt-10">
      <a href="book_parcel.php"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300">
        <i class="fa-solid fa-plus"></i> Book New Parcel
      </a>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
