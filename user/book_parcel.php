<?php
include '../includes/db_connect.php';
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: user_login.php");
    exit();
}

$message = '';

$warehouses = $conn->query("SELECT * FROM warehouses WHERE is_active=1 ORDER BY city_name ASC");

if(isset($_POST['book'])){
    $uid = $_SESSION['user_id'];
    $parcel_name = mysqli_real_escape_string($conn, $_POST['parcel_name']);
    $sender = mysqli_real_escape_string($conn, $_POST['sender']);
    $receiver = mysqli_real_escape_string($conn, $_POST['receiver']);
    $weight = floatval($_POST['weight']);
    $price = $weight * 50; // ₹50 per kg
    $origin_warehouse = intval($_POST['origin_warehouse']);

    $sql = "INSERT INTO parcels (user_id, parcel_name, sender_address, receiver_address, weight, price, status, current_warehouse_id)
            VALUES ('$uid', '$parcel_name', '$sender', '$receiver', '$weight', '$price', 'Booked', '$origin_warehouse')";

    if($conn->query($sql)){
        $parcel_id = $conn->insert_id;

        $conn->query("INSERT INTO parcel_location_history (parcel_id, warehouse_id, status, remarks) 
                      VALUES ('$parcel_id', '$origin_warehouse', 'Booked', 'Parcel booked by user')");
        
        $message = "✅ Parcel booked successfully! Tracking ID: <b>$parcel_id</b>";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="min-h-screen flex items-center justify-center px-4 py-12">

  <div class="w-full max-w-2xl glass-card p-8 relative overflow-hidden">
    
    <div class="absolute -top-8 -right-8 w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 opacity-25 blur-2xl rounded-full"></div>
    <div class="absolute -bottom-10 -left-10 w-28 h-28 bg-gradient-to-tr from-purple-500 to-pink-500 opacity-25 blur-2xl rounded-full"></div>

    <div class="flex justify-between items-center mb-6 relative z-10">
      <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">Book a Parcel</h2>
      
      <a href="dashboard.php"
         class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-full font-medium shadow-md transition-all duration-300 hover:scale-105 hover:shadow-lg">
        ← Back
      </a>
    </div>

    <?php if($message != ''): ?>
      <div class="mb-5 p-4 rounded-lg 
        <?php echo (strpos($message, '✅') !== false) ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5 relative z-10">
      <div>
        <label class="font-semibold text-gray-700">Parcel Name</label>
        <input type="text" name="parcel_name" value="General Parcel" class="w-full mt-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
      </div>

      <div>
        <label class="font-semibold text-gray-700">Sender Address</label>
        <textarea name="sender" rows="3" class="w-full mt-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required></textarea>
      </div>

      <div>
        <label class="font-semibold text-gray-700">Receiver Address</label>
        <textarea name="receiver" rows="3" class="w-full mt-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none" required></textarea>
      </div>

      <div>
        <label class="font-semibold text-gray-700">Package Weight (in kg)</label>
        <input type="number" step="0.1" name="weight" class="w-full mt-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none" required>
        <p class="text-sm text-gray-500 mt-1">Pricing: ₹50 per kg</p>
      </div>

      <div>
        <label class="font-semibold text-gray-700">Origin Warehouse</label>
        <select name="origin_warehouse" class="w-full mt-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none" required>
          <option value="">Select Warehouse City</option>
          <?php while($wh = $warehouses->fetch_assoc()): ?>
            <option value="<?php echo $wh['warehouse_id']; ?>">
              <?php echo $wh['city_name'] . ', ' . $wh['state_name']; ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <button type="submit" name="book"
        class="w-full mt-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-xl font-semibold shadow-md hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
        📦 Book Parcel
      </button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
