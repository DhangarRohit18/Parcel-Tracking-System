<?php
session_start();
include '../includes/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_warehouse'])) {
    $city_name = $_POST['city_name'];
    $state_name = $_POST['state_name'];
    $address = $_POST['address'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    
    $stmt = $conn->prepare("INSERT INTO warehouses (city_name, state_name, address, latitude, longitude, is_active) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("sssdd", $city_name, $state_name, $address, $latitude, $longitude);
    
    if ($stmt->execute()) {
        $success = "Warehouse added successfully!";
    } else {
        $error = "Failed to add warehouse: " . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_warehouse'])) {
    $id = $_POST['warehouse_id'];
    $city_name = $_POST['city_name'];
    $state_name = $_POST['state_name'];
    $address = $_POST['address'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    
    $stmt = $conn->prepare("UPDATE warehouses SET city_name=?, state_name=?, address=?, latitude=?, longitude=? WHERE warehouse_id=?");
    $stmt->bind_param("sssddi", $city_name, $state_name, $address, $latitude, $longitude, $id);
    
    if ($stmt->execute()) {
        $success = "Warehouse updated successfully!";
    } else {
        $error = "Failed to update warehouse: " . $conn->error;
    }
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("UPDATE warehouses SET is_active=0 WHERE warehouse_id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success = "Warehouse deleted successfully!";
    } else {
        $error = "Failed to delete warehouse: " . $conn->error;
    }
}

if (isset($_GET['activate_id'])) {
    $id = $_GET['activate_id'];
    $stmt = $conn->prepare("UPDATE warehouses SET is_active=1 WHERE warehouse_id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success = "Warehouse activated successfully!";
    } else {
        $error = "Failed to activate warehouse: " . $conn->error;
    }
}

$warehouses = $conn->query("SELECT * FROM warehouses ORDER BY is_active DESC, city_name ASC");
?>

<div class="min-h-screen py-10 px-6">
  <div class="w-full max-w-7xl mx-auto">

    <div class="flex justify-between items-center mb-8">
      <div>
        <h2 class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-pink-600">
          Manage Warehouses
        </h2>
        <p class="text-gray-600 mt-1">Add, edit, and manage warehouse locations</p>
      </div>
      <a href="admin_dashboard.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white px-5 py-2 rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition-all">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
      </a>
    </div>

    <?php if (isset($success)): ?>
      <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
        <i class="fa-solid fa-check-circle mr-2"></i><?php echo $success; ?>
      </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
        <i class="fa-solid fa-exclamation-circle mr-2"></i><?php echo $error; ?>
      </div>
    <?php endif; ?>

    <div class="mb-6">
      <button onclick="openAddModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition-all">
        <i class="fa-solid fa-plus"></i> Add New Warehouse
      </button>
    </div>

    <div class="glass-card p-6">
      <h3 class="text-2xl font-bold text-gray-800 mb-6">Warehouses List</h3>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b-2 border-gray-300">
              <th class="text-left p-4 font-semibold text-gray-700">ID</th>
              <th class="text-left p-4 font-semibold text-gray-700">City</th>
              <th class="text-left p-4 font-semibold text-gray-700">State</th>
              <th class="text-left p-4 font-semibold text-gray-700">Address</th>
              <th class="text-left p-4 font-semibold text-gray-700">Latitude</th>
              <th class="text-left p-4 font-semibold text-gray-700">Longitude</th>
              <th class="text-left p-4 font-semibold text-gray-700">Status</th>
              <th class="text-left p-4 font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $warehouses->fetch_assoc()): ?>
              <tr class="border-b hover:bg-gray-50 transition-colors">
                <td class="p-4"><?php echo $row['warehouse_id']; ?></td>
                <td class="p-4 font-semibold"><?php echo htmlspecialchars($row['city_name']); ?></td>
                <td class="p-4"><?php echo htmlspecialchars($row['state_name']); ?></td>
                <td class="p-4"><?php echo htmlspecialchars($row['address'] ?? 'N/A'); ?></td>
                <td class="p-4"><?php echo number_format($row['latitude'], 6); ?></td>
                <td class="p-4"><?php echo number_format($row['longitude'], 6); ?></td>
                <td class="p-4">
                  <?php if ($row['is_active']): ?>
                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                      <i class="fa-solid fa-circle-check"></i> Active
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">
                      <i class="fa-solid fa-circle-xmark"></i> Inactive
                    </span>
                  <?php endif; ?>
                </td>
                <td class="p-4">
                  <div class="flex gap-2">
                    <button onclick='openEditModal(<?php echo json_encode($row); ?>)' class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 transition-colors">
                      <i class="fa-solid fa-edit"></i> Edit
                    </button>
                    <?php if ($row['is_active']): ?>
                      <a href="?delete_id=<?php echo $row['warehouse_id']; ?>" onclick="return confirm('Are you sure you want to deactivate this warehouse?')" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fa-solid fa-trash"></i> Delete
                      </a>
                    <?php else: ?>
                      <a href="?activate_id=<?php echo $row['warehouse_id']; ?>" class="bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fa-solid fa-check"></i> Activate
                      </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 p-8">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-2xl font-bold text-gray-800">Add New Warehouse</h3>
      <button onclick="closeAddModal()" class="text-gray-500 hover:text-gray-700">
        <i class="fa-solid fa-times fa-2x"></i>
      </button>
    </div>
    
    <form method="POST" action="">
      <div class="space-y-4">
        <div>
          <label class="block text-gray-700 font-semibold mb-2">City Name</label>
          <input type="text" name="city_name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g., Mumbai">
        </div>
        
        <div>
          <label class="block text-gray-700 font-semibold mb-2">State Name</label>
          <input type="text" name="state_name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g., Maharashtra">
        </div>
        
        <div>
          <label class="block text-gray-700 font-semibold mb-2">Address</label>
          <textarea name="address" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g., SmartCourier Hub, Andheri East"></textarea>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Latitude</label>
            <input type="number" step="0.000001" name="latitude" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g., 40.712776">
          </div>
          
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Longitude</label>
            <input type="number" step="0.000001" name="longitude" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g., -74.005974">
          </div>
        </div>
      </div>
      
      <div class="flex gap-4 mt-8">
        <button type="submit" name="add_warehouse" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
          <i class="fa-solid fa-plus mr-2"></i>Add Warehouse
        </button>
        <button type="button" onclick="closeAddModal()" class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-400 transition-all">
          Cancel
        </button>
      </div>
    </form>
  </div>
</div>

<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 p-8">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-2xl font-bold text-gray-800">Edit Warehouse</h3>
      <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
        <i class="fa-solid fa-times fa-2x"></i>
      </button>
    </div>
    
    <form method="POST" action="">
      <input type="hidden" name="warehouse_id" id="edit_warehouse_id">
      
      <div class="space-y-4">
        <div>
          <label class="block text-gray-700 font-semibold mb-2">City Name</label>
          <input type="text" name="city_name" id="edit_city_name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
        </div>
        
        <div>
          <label class="block text-gray-700 font-semibold mb-2">State Name</label>
          <input type="text" name="state_name" id="edit_state_name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
        </div>
        
        <div>
          <label class="block text-gray-700 font-semibold mb-2">Address</label>
          <textarea name="address" id="edit_address" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Latitude</label>
            <input type="number" step="0.000001" name="latitude" id="edit_latitude" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
          </div>
          
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Longitude</label>
            <input type="number" step="0.000001" name="longitude" id="edit_longitude" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
          </div>
        </div>
      </div>
      
      <div class="flex gap-4 mt-8">
        <button type="submit" name="edit_warehouse" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
          <i class="fa-solid fa-save mr-2"></i>Save Changes
        </button>
        <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-400 transition-all">
          Cancel
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openAddModal() {
  document.getElementById('addModal').style.display = 'flex';
}

function closeAddModal() {
  document.getElementById('addModal').style.display = 'none';
}

function openEditModal(warehouse) {
  document.getElementById('edit_warehouse_id').value = warehouse.warehouse_id;
  document.getElementById('edit_city_name').value = warehouse.city_name;
  document.getElementById('edit_state_name').value = warehouse.state_name;
  document.getElementById('edit_address').value = warehouse.address || '';
  document.getElementById('edit_latitude').value = warehouse.latitude;
  document.getElementById('edit_longitude').value = warehouse.longitude;
  document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
  document.getElementById('editModal').style.display = 'none';
}

window.onclick = function(event) {
  const addModal = document.getElementById('addModal');
  const editModal = document.getElementById('editModal');
  if (event.target === addModal) {
    closeAddModal();
  }
  if (event.target === editModal) {
    closeEditModal();
  }
}
</script>

<?php include '../includes/footer.php'; ?>
