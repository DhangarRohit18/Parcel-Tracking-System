<?php
session_start();
if(!isset($_SESSION['agent_id'])){
    header("Location: agent_login.php");
    exit();
}

include '../includes/db_connect.php';
include '../includes/header.php';

$aid = $_SESSION['agent_id'];
$message = '';
$parcel_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$warehouses = $conn->query("SELECT * FROM warehouses WHERE is_active=1 ORDER BY city_name ASC");

if(isset($_POST['update'])){
    $pid = intval($_POST['parcel_id']);
    $new_warehouse = intval($_POST['warehouse_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    $stmt = $conn->prepare("UPDATE parcels SET current_warehouse_id=?, status=? WHERE parcel_id=? AND assigned_agent=?");
    $stmt->bind_param("isii", $new_warehouse, $status, $pid, $aid);
    
    if($stmt->execute()){

        $hist = $conn->prepare("INSERT INTO parcel_location_history (parcel_id, warehouse_id, status, remarks, updated_by_agent) VALUES (?, ?, ?, ?, ?)");
        $hist->bind_param("iissi", $pid, $new_warehouse, $status, $remarks, $aid);
        $hist->execute();
        
        $message = "✅ Location updated successfully!";
        $parcel_id = 0; // Reset
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}

$parcel = null;
if($parcel_id > 0){
    $query = "SELECT p.*, u.name AS user_name, w.city_name AS current_city
              FROM parcels p
              JOIN users u ON p.user_id = u.user_id
              LEFT JOIN warehouses w ON p.current_warehouse_id = w.warehouse_id
              WHERE p.parcel_id = $parcel_id AND p.assigned_agent = $aid";
    $res = $conn->query($query);
    if($res->num_rows > 0){
        $parcel = $res->fetch_assoc();
    }
}
?>

<div class="min-h-screen py-12 px-6">
  <div class="w-full max-w-3xl mx-auto glass-card p-8">

    <div class="flex justify-between items-center mb-6">
      <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">
        Update Parcel Location
      </h2>
      <a href="assigned_parcels.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-full font-medium shadow-md hover:scale-105 transition-all">
        ← Back
      </a>
    </div>

    <?php if($message): ?>
      <div class="mb-4 p-4 rounded-lg <?php echo strpos($message, '✅') !== false ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>

    <?php if(!$parcel_id): ?>
      
      <form method="GET" class="mb-6">
        <label class="block font-semibold text-gray-700 mb-2">Select Parcel to Update</label>
        <div class="flex gap-3">
          <select name="id" required class="flex-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">Choose a parcel...</option>
            <?php
            $parcels = $conn->query("SELECT p.parcel_id, p.parcel_name, u.name AS user_name FROM parcels p JOIN users u ON p.user_id = u.user_id WHERE p.assigned_agent = $aid AND p.status != 'Delivered' ORDER BY p.parcel_id DESC");
            while($p = $parcels->fetch_assoc()){
                echo "<option value='{$p['parcel_id']}'>#{$p['parcel_id']} - {$p['parcel_name']} (User: {$p['user_name']})</option>";
            }
            ?>
          </select>
          <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
            Select
          </button>
        </div>
      </form>
    <?php else: ?>
      
      <?php if($parcel): ?>
        <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
          <h4 class="font-bold text-gray-800 mb-2">Parcel Details</h4>
          <p><strong>ID:</strong> #<?php echo $parcel['parcel_id']; ?></p>
          <p><strong>Name:</strong> <?php echo htmlspecialchars($parcel['parcel_name']); ?></p>
          <p><strong>User:</strong> <?php echo htmlspecialchars($parcel['user_name']); ?></p>
          <p><strong>Current Location:</strong> <?php echo $parcel['current_city'] ?? 'Not set'; ?></p>
          <p><strong>Current Status:</strong> <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 text-sm"><?php echo $parcel['status']; ?></span></p>
        </div>

        <form method="POST" class="space-y-5">
          <input type="hidden" name="parcel_id" value="<?php echo $parcel['parcel_id']; ?>">

          <div>
            <label class="font-semibold text-gray-700">New Warehouse Location</label>
            <select name="warehouse_id" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
              <option value="">Select Warehouse</option>
              <?php 
              $warehouses->data_seek(0);
              while($wh = $warehouses->fetch_assoc()): 
              ?>
                <option value="<?php echo $wh['warehouse_id']; ?>" <?php echo ($parcel['current_warehouse_id'] == $wh['warehouse_id']) ? 'selected' : ''; ?>>
                  <?php echo $wh['city_name'] . ', ' . $wh['state_name']; ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div>
            <label class="font-semibold text-gray-700">Update Status</label>
            <select name="status" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
              <option value="">Select Status</option>
              <option value="Picked" <?php echo ($parcel['status'] == 'Picked') ? 'selected' : ''; ?>>Picked</option>
              <option value="In Transit" <?php echo ($parcel['status'] == 'In Transit') ? 'selected' : ''; ?>>In Transit</option>
              <option value="Out for Delivery" <?php echo ($parcel['status'] == 'Out for Delivery') ? 'selected' : ''; ?>>Out for Delivery</option>
              <option value="Delivered" <?php echo ($parcel['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
              <option value="Delayed">Delayed</option>
            </select>
          </div>

          <div>
            <label class="font-semibold text-gray-700">Remarks (Optional)</label>
            <textarea name="remarks" rows="3" class="w-full mt-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 outline-none" placeholder="Add any notes..."></textarea>
          </div>

          <button type="submit" name="update" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-xl font-semibold shadow-md hover:shadow-xl hover:scale-[1.02] transition-all">
            <i class="fa-solid fa-location-dot"></i> Update Location & Status
          </button>
        </form>
      <?php else: ?>
        <div class="text-center text-red-600">
          <i class="fa-solid fa-exclamation-triangle fa-3x mb-3"></i>
          <p>Parcel not found or not assigned to you.</p>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
