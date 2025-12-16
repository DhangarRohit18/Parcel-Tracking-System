<?php
include '../includes/db_connect.php';
$message = '';

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);

    $sql = "INSERT INTO users (name, email, password, phone, address, city, state) 
            VALUES ('$name', '$email', '$password', '$phone', '$address', '$city', '$state')";
    
    if($conn->query($sql)){
        $message = "✅ Registration Successful! <a href='user_login.php' class='text-blue-600 font-semibold hover:underline'>Login Here</a>";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="min-h-screen flex items-center justify-center px-4">
  <div class="w-full max-w-lg glass-card p-8 relative overflow-hidden">
    
    <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full opacity-20 blur-2xl"></div>
    <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-gradient-to-tr from-purple-500 to-pink-500 rounded-full opacity-20 blur-2xl"></div>

    <h2 class="text-3xl font-bold text-center bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 mb-6">
      User Registration
    </h2>

    <?php if($message != ''): ?>
      <div class="mb-4 text-blue-800 text-center font-medium bg-blue-100/70 border border-blue-200 rounded-lg py-2">
        <?= $message ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
        <input type="text" name="name" required
          class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white/80 backdrop-blur-md">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" required
          class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white/80 backdrop-blur-md">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" name="password" required
          class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white/80 backdrop-blur-md">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
        <input type="text" name="phone" required
          class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white/80 backdrop-blur-md">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
        <textarea name="address" required
          class="w-full border border-gray-300 rounded-xl px-3 py-2 h-24 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white/80 backdrop-blur-md"></textarea>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
          <input type="text" name="city" required
            class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white/80 backdrop-blur-md">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
          <input type="text" name="state" required
            class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white/80 backdrop-blur-md">
        </div>
      </div>

      <button type="submit" name="register"
        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 ease-in-out transform hover:-translate-y-0.5">
        Register
      </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
      Already have an account?
      <a href="user_login.php" class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 font-semibold hover:underline">Login Here</a>
    </p>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
