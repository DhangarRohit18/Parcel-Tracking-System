<?php
session_start();
if(!isset($_SESSION['admin_id'])) header("Location: admin_login.php");

include '../includes/db_connect.php';
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_agent'])) {
    $id = $_POST['agent_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $warehouse_id = $_POST['warehouse_id'];
    $is_active = $_POST['is_active'];
    
    $stmt = $conn->prepare("UPDATE agents SET name=?, email=?, phone=?, warehouse_id=?, is_active=? WHERE agent_id=?");
    $stmt->bind_param("sssiii", $name, $email, $phone, $warehouse_id, $is_active, $id);
    
    if ($stmt->execute()) {
        $success = "Agent updated successfully!";
    } else {
        $error = "Failed to update agent: " . $conn->error;
    }
}
if(isset($_GET['delete_agent']))
{
  $id = intval($_GET['delete_agent']);
  $conn->query("DELETE FROM agents WHERE agent_id=$id");
  echo "<script>alert('Agent deleted successfully!'); window.location='manage_agents.php';</script>";
}

$res = $conn->query("SELECT a.*, w.city_name FROM agents a LEFT JOIN warehouses w ON a.warehouse_id = w.warehouse_id ORDER BY a.created_at DESC");
$warehouses = $conn->query("SELECT warehouse_id, city_name FROM warehouses WHERE is_active=1 ORDER BY city_name");
?>
<div class="min-h-screen py-10 px-6">
  <div class="w-full max-w-7xl mx-auto">
    <?php if (isset($success)): ?>
      <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
        <i class="fa-solid fa-check-circle mr-2"></i><?php echo $success; ?>
      </div>
    <?php endif;?>
    
    <?php if (isset($error)): ?>
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
        <i class="fa-solid fa-exclamation-circle mr-2"></i><?php echo $error; ?>
      </div>
    <?php endif; ?>

    <div class="flex justify-between items-center mb-6">
      <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">
        Manage Agents
      </h2>
      <div class="flex gap-3">
        <a href="add_agent.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-green-700 text-white px-4 py-2 rounded-full font-medium shadow-md hover:scale-105 transition-all">
          <i class="fa-solid fa-plus"></i> Add Agent
        </a>
        <a href="admin_dashboard.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-full font-medium shadow-md hover:scale-105 transition-all">
          ← Back
        </a>
      </div>
    </div>

    <div class="glass-card p-6">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-600">
          <thead class="text-xs uppercase bg-gradient-to-r from-blue-600 to-purple-600 text-white">
            <tr>
              <th class="px-6 py-3">ID</th>
              <th class="px-6 py-3">Name</th>
              <th class="px-6 py-3">Email</th>
              <th class="px-6 py-3">Phone</th>
              <th class="px-6 py-3">Warehouse</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while($r=$res->fetch_assoc()){ ?>
            <tr class="bg-white border-b hover:bg-gray-50">
              <td class="px-6 py-4"><?php echo $r['agent_id']; ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($r['name']); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($r['email']); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($r['phone']); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($r['city_name']); ?></td>
              <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $r['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                  <?php echo $r['is_active'] ? 'Active' : 'Inactive'; ?>
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex gap-2">
                  <button onclick='openEditModal(<?php echo json_encode($r); ?>)' class="bg-gradient-to-r from-blue-500 to-blue-700 text-white px-3 py-1 rounded-full text-xs font-medium hover:shadow-md transition-all">
                    <i class="fa-solid fa-edit"></i> Edit
                  </button>
                </div>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 p-8">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-2xl font-bold text-gray-800">Edit Agent</h3>
      <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
        <i class="fa-solid fa-times fa-2x"></i>
      </button>
    </div>
    
    <form method="POST" action="">
      <input type="hidden" name="agent_id" id="edit_agent_id">
      
      <div class="space-y-4">
        <div>
          <label class="block text-gray-700 font-semibold mb-2">Name</label>
          <input type="text" name="name" id="edit_name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
          <label class="block text-gray-700 font-semibold mb-2">Email</label>
          <input type="email" name="email" id="edit_email" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
          <label class="block text-gray-700 font-semibold mb-2">Phone</label>
          <input type="text" name="phone" id="edit_phone" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
          <label class="block text-gray-700 font-semibold mb-2">Warehouse</label>
          <select name="warehouse_id" id="edit_warehouse_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            <?php 
            $warehouses_list = $conn->query("SELECT warehouse_id, city_name FROM warehouses WHERE is_active=1 ORDER BY city_name");
            while($w = $warehouses_list->fetch_assoc()) { 
              echo "<option value='{$w['warehouse_id']}'>{$w['city_name']}</option>";
            }
            ?>
          </select>
        </div>
        
        <div>
          <label class="block text-gray-700 font-semibold mb-2">Status</label>
          <select name="is_active" id="edit_is_active" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>
      </div>
      
      <div class="flex gap-4 mt-8">
        <button type="submit" name="edit_agent" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
          <i class="fa-solid fa-save mr-2"></i>Save Changes
        </button>
        <button type="button" onclick="confirmDelete()" class="flex-1 bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
          <i class="fa-solid fa-trash mr-2"></i>Delete Agent
        </button>
        <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-400 transition-all">
          Cancel
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditModal(agent) {
  document.getElementById('edit_agent_id').value = agent.agent_id;
  document.getElementById('edit_name').value = agent.name;
  document.getElementById('edit_email').value = agent.email;
  document.getElementById('edit_phone').value = agent.phone;
  document.getElementById('edit_warehouse_id').value = agent.warehouse_id;
  document.getElementById('edit_is_active').value = agent.is_active;
  document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
  document.getElementById('editModal').style.display = 'none';
}

function confirmDelete() {
  const agentId = document.getElementById('edit_agent_id').value;
  if (confirm('Are you sure you want to delete this agent? This action cannot be undone.')) {
    window.location.href = '?delete_agent=' + agentId;
  }
}

window.onclick = function(event) {
  const editModal = document.getElementById('editModal');
  if (event.target === editModal) {
    closeEditModal();
  }
}
</script>

<?php include '../includes/footer.php'; ?>
