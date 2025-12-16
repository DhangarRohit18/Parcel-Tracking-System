<?php
include '../includes/db_connect.php';
session_start();
$message = '';

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM agents WHERE email='$email' AND is_active=1";
    $result = $conn->query($sql);

    if($result->num_rows == 1){
        $row = $result->fetch_assoc();
        if(password_verify($password, $row['password'])){
            $_SESSION['agent_id'] = $row['agent_id'];
            $_SESSION['agent_name'] = $row['name'];
            $_SESSION['agent_email'] = $row['email'];
            $_SESSION['warehouse_id'] = $row['warehouse_id'];
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "❌ Incorrect Password!";
        }
    } else {
        $message = "❌ Email not registered or account inactive!";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="min-h-screen flex items-center justify-center px-6 py-10">
  <div class="w-full max-w-md glass-card p-8 relative overflow-hidden">

    <div class="absolute -top-10 -right-10 w-28 h-28 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full opacity-20 blur-2xl"></div>
    <div class="absolute -bottom-10 -left-10 w-28 h-28 bg-gradient-to-tr from-pink-500 to-purple-500 rounded-full opacity-20 blur-2xl"></div>

    <h3 class="text-3xl font-bold text-center mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">
      Agent Login
    </h3>

    <?php if($message != ''): ?>
      <div class="mb-4 text-center text-red-600 font-medium bg-red-50 border border-red-200 rounded-lg py-2">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <div>
        <label class="block text-gray-700 font-medium mb-1">Email</label>
        <input type="email" name="email" required
          class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Password</label>
        <input type="password" name="password" required
          class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-400 transition-all duration-200">
      </div>

      <button type="submit" name="login"
        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300">
        Login
      </button>
    </form>

    <div class="text-center mt-6 text-gray-600">
      <p>Don't have an account? 
        <a href="agent_register.php" class="text-blue-600 font-medium hover:underline">Register Here</a>
      </p>
      <p class="mt-3">
        <a href="../index.php" class="text-gray-500 hover:text-gray-700">← Back to Home</a>
      </p>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
