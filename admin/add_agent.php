<?php
session_start();
if(!isset($_SESSION['admin_id'])) header("Location: admin_login.php");

include '../includes/db_connect.php';
include '../includes/header.php';

$message = '';
$warehouses = $conn->query("SELECT * FROM warehouses WHERE is_active=1 ORDER BY city_name ASC");

if(isset($_POST['add'])){
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $warehouse_id = intval($_POST['warehouse_id']);

  $sql = "INSERT INTO agents (name, email, password, phone, warehouse_id, is_active) 
          VALUES ('$name', '$email', '$password', '$phone', '$warehouse_id', 1)";
  
  if($conn->query($sql)){
    echo "<script>alert('Agent added successfully!'); window.location='manage_agents.php';</script>";
  } else {
    $message = "Error: " . $conn->error;
  }
}
?>

<div class="min-h-screen py-12 px-6">
  <div class="w-full max-w-2xl mx-auto glass-card p-8">

    <div class="flex justify-between items-center mb-6">
      <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">
        Add New Agent
      </h2>
      <a href="manage_agents.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-full font-medium shadow-md hover:scale-105 transition-all">
        ← Back
      </a>
    </div>

    <?php if($message): ?>
      <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-700 border border-red-300">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <div>
        <label class="font-semibold text-gray-700">Name</label>
        <input name="name" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
      </div>

      <div>
        <label class="font-semibold text-gray-700">Email</label>
        <input name="email" type="email" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
      </div>

      <div>
        <label class="font-semibold text-gray-700">Password</label>
        <input name="password" type="password" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
      </div>

      <div>
        <label class="font-semibold text-gray-700">Phone</label>
        <input name="phone" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
      </div>

      <div>
        <label class="font-semibold text-gray-700">Assigned Warehouse</label>
        <select name="warehouse_id" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
          <option value="">Select Warehouse</option>
          <?php while($wh = $warehouses->fetch_assoc()): ?>
            <option value="<?php echo $wh['warehouse_id']; ?>">
              <?php echo $wh['city_name'] . ', ' . $wh['state_name']; ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <button type="submit" name="add" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-xl font-semibold shadow-md hover:shadow-xl hover:scale-[1.02] transition-all">
        Add Agent
      </button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
