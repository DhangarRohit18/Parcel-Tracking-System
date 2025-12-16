<?php
include '../includes/db_connect.php';
session_start();
$message = '';

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE email='$email'";
    $result = $conn->query($sql);

    if($result->num_rows == 1){
        $row = $result->fetch_assoc();

        if($password == $row['password']){
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_email'] = $row['email'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $message = "Incorrect Password!";
        }
    } else {
        $message = "Email not registered!";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="min-vh-100 d-flex align-items-center justify-content-center px-4">
  <div class="w-full max-w-md glass-card p-8">
    
    <div class="text-center mb-4">
      <i class="fa-solid fa-user-shield fa-3x mb-3" style="color: #667eea;"></i>
      <h3 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">
        Admin Login
      </h3>
    </div>

    <?php if($message != ''): ?>
      <div class="mb-4 text-center text-red-600 font-medium bg-red-50 border border-red-200 rounded-lg py-2">
        <?= $message; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="admin@smartcourier.com" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" name="password" class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="admin123" required>
      </div>

      <button type="submit" name="login" 
        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg hover:scale-[1.02] transition-all duration-300">
        Login
      </button>

      <p class="text-center text-sm">
        <a href="../index.php" class="text-gray-500 hover:text-blue-600 transition">← Back to Home</a>
      </p>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
