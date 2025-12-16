<?php
include '../includes/db_connect.php';
$message = '';

$warehouses = $conn->query("SELECT * FROM warehouses WHERE is_active=1 ORDER BY city_name ASC");

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $warehouse_id = intval($_POST['warehouse_id']);

    $sql = "INSERT INTO agents (name, email, password, phone, warehouse_id, is_active) 
            VALUES ('$name', '$email', '$password', '$phone', '$warehouse_id', 1)";
    
    if($conn->query($sql)){
        $message = "Registration Successful! <a href='agent_login.php' class='text-blue-600 underline'>Login Here</a>";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="min-h-screen flex items-center justify-center px-6 py-10">
  <div class="w-full max-w-lg glass-card p-8 relative overflow-hidden">

    <div class="absolute -top-10 -right-10 w-28 h-28 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full opacity-20 blur-2xl"></div>
    <div class="absolute -bottom-10 -left-10 w-28 h-28 bg-gradient-to-tr from-pink-500 to-purple-500 rounded-full opacity-20 blur-2xl"></div>

    <h3 class="text-3xl font-bold text-center mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">
      Agent Registration
    </h3>

    <?php if($message != ''): ?>
      <div class="mb-4 text-center text-green-700 font-medium bg-green-50 border border-green-200 rounded-lg py-2">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <div>
        <label class="block text-gray-700 font-medium mb-1">Full Name</label>
        <input type="text" name="name" required
          class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Email</label>
        <input type="email" name="email" required
          class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-400 transition-all duration-200">
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Password</label>
        <input type="password" name="password" required
          class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-200">
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Phone</label>
        <input type="text" name="phone" required
          class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 transition-all duration-200">
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Assigned Warehouse</label>
        <select name="warehouse_id" required
          class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
          <option value="">Select Warehouse City</option>
          <?php while($wh = $warehouses->fetch_assoc()): ?>
            <option value="<?php echo $wh['warehouse_id']; ?>">
              <?php echo $wh['city_name'] . ', ' . $wh['state_name']; ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <button type="submit" name="register"
        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300">
        Register
      </button>
    </form>

    <div class="text-center mt-6 text-gray-600">
      <p>Already registered? 
        <a href="agent_login.php" class="text-blue-600 font-medium hover:underline">Login Here</a>
      </p>
      <p class="mt-3">
        <a href="../index.php" class="text-gray-500 hover:text-gray-700">← Back to Home</a>
      </p>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
