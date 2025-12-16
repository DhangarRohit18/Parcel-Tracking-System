<?php
session_start();
if(!isset($_SESSION['admin_id'])) header("Location: admin_login.php");

include '../includes/db_connect.php';
include '../includes/header.php';

if(isset($_POST['reassign'])){
  $parcel_id = intval($_POST['parcel_id']);
  $new_agent = intval($_POST['new_agent']);
  $admin_id = $_SESSION['admin_id'];

  $conn->query("UPDATE parcels SET assigned_agent=$new_agent WHERE parcel_id=$parcel_id");

  $conn->query("INSERT INTO assignments (parcel_id, agent_id, assigned_by_admin) VALUES ($parcel_id, $new_agent, $admin_id)");
  
  echo "<script>alert('Parcel reassigned successfully!'); window.location='manage_parcels.php';</script>";
}

$where = "1=1";
$filters_applied = false;
$status_filter = '';

if(isset($_GET['status']) && !isset($_GET['filter'])){
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $where .= " AND p.status = '$status'";
    $filters_applied = true;
    $status_filter = $status;
}

if(isset($_GET['filter'])){
    if(!empty($_GET['agent_id'])){
        $aid = intval($_GET['agent_id']);
        $where .= " AND p.assigned_agent = $aid";
        $filters_applied = true;
    }
    if(!empty($_GET['status'])){
        $status = mysqli_real_escape_string($conn, $_GET['status']);
        $where .= " AND p.status = '$status'";
        $filters_applied = true;
        $status_filter = $status;
    }
    if(!empty($_GET['warehouse'])){
        $wh = intval($_GET['warehouse']);
        $where .= " AND p.current_warehouse_id = $wh";
        $filters_applied = true;
    }
    if(!empty($_GET['parcel_id'])){
        $pid = intval($_GET['parcel_id']);
        $where .= " AND p.parcel_id = $pid";
        $filters_applied = true;
    }
}

$query = "SELECT p.*, u.name AS user_name, a.name AS agent_name, w.city_name AS warehouse_city
          FROM parcels p
          LEFT JOIN users u ON p.user_id = u.user_id
          LEFT JOIN agents a ON p.assigned_agent = a.agent_id
          LEFT JOIN warehouses w ON p.current_warehouse_id = w.warehouse_id
          WHERE $where
          ORDER BY p.booking_date DESC";

$result = $conn->query($query);
$agents = $conn->query("SELECT * FROM agents WHERE is_active=1 ORDER BY name ASC");
$warehouses = $conn->query("SELECT * FROM warehouses WHERE is_active=1 ORDER BY city_name ASC");
?>

<div class="min-h-screen py-10 px-6">
  <div class="w-full max-w-7xl mx-auto">

    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">
          Manage Parcels
        </h2>
        <?php if(isset($_GET['status']) && !isset($_GET['filter'])): ?>
          <p class="text-gray-600 mt-1">Showing: <span class="font-semibold text-blue-600"><?php echo htmlspecialchars($_GET['status']); ?></span> Parcels</p>
        <?php endif; ?>
      </div>
      <a href="admin_dashboard.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-full font-medium shadow-md hover:scale-105 transition-all">
        ← Back
      </a>
    </div>

    <div class="glass-card p-6 mb-6">
      <h3 class="font-semibold text-gray-700 mb-3"><i class="fa-solid fa-filter"></i> Filter Parcels</h3>
      <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="hidden" name="filter" value="1">
        
        <div>
          <label class="text-sm font-medium text-gray-600">Parcel ID</label>
          <input type="number" name="parcel_id" value="<?php echo $_GET['parcel_id'] ?? ''; ?>" 
                 class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                 placeholder="Enter ID">
        </div>

        <div>
          <label class="text-sm font-medium text-gray-600">Agent</label>
          <select name="agent_id" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">All Agents</option>
            <?php 
            $agents->data_seek(0);
            while($ag = $agents->fetch_assoc()): 
            ?>
              <option value="<?php echo $ag['agent_id']; ?>" <?php echo (isset($_GET['agent_id']) && $_GET['agent_id']==$ag['agent_id'])?'selected':''; ?>>
                <?php echo htmlspecialchars($ag['name']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div>
          <label class="text-sm font-medium text-gray-600">Status</label>
          <select name="status" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">All Status</option>
            <option value="Booked" <?php echo ($status_filter=='Booked')?'selected':''; ?>>Booked</option>
            <option value="Picked" <?php echo ($status_filter=='Picked')?'selected':''; ?>>Picked</option>
            <option value="In Transit" <?php echo ($status_filter=='In Transit')?'selected':''; ?>>In Transit</option>
            <option value="Delivered" <?php echo ($status_filter=='Delivered')?'selected':''; ?>>Delivered</option>
          </select>
        </div>

        <div>
          <label class="text-sm font-medium text-gray-600">Location</label>
          <select name="warehouse" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">All Locations</option>
            <?php 
            $warehouses->data_seek(0);
            while($wh = $warehouses->fetch_assoc()): 
            ?>
              <option value="<?php echo $wh['warehouse_id']; ?>" <?php echo (isset($_GET['warehouse']) && $_GET['warehouse']==$wh['warehouse_id'])?'selected':''; ?>>
                <?php echo $wh['city_name']; ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="flex items-end gap-2 md:col-span-4">
          <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2 px-6 rounded-lg font-medium hover:shadow-lg transition-all">
            <i class="fa-solid fa-search"></i> Apply
          </button>
          <a href="manage_parcels.php" class="bg-gray-500 text-white py-2 px-4 rounded-lg font-medium hover:bg-gray-600 transition-all">
            Clear
          </a>
        </div>
      </form>
    </div>

    <div class="glass-card p-6">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-600">
          <thead class="text-xs uppercase bg-gradient-to-r from-blue-600 to-purple-600 text-white">
            <tr>
              <th class="px-6 py-3">ID</th>
              <th class="px-6 py-3">User</th>
              <th class="px-6 py-3">Parcel Name</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Location</th>
              <th class="px-6 py-3">Agent</th>
              <th class="px-6 py-3">Reassign</th>
            </tr>
          </thead>
          <tbody>
            <?php if($result->num_rows > 0): ?>
              <?php while($r = $result->fetch_assoc()): ?>
              <tr class="bg-white border-b hover:bg-gray-50">
                <td class="px-6 py-4 font-medium"><?php echo $r['parcel_id']; ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($r['user_name']); ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($r['parcel_name']); ?></td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                    <?php echo $r['status']; ?>
                  </span>
                </td>
                <td class="px-6 py-4"><?php echo $r['warehouse_city'] ?? 'N/A'; ?></td>
                <td class="px-6 py-4"><?php echo $r['agent_name'] ?? 'Not Assigned'; ?></td>
                <td class="px-6 py-4">
                  <form method="POST" class="flex gap-2 items-center">
                    <input type="hidden" name="parcel_id" value="<?php echo $r['parcel_id']; ?>">
                    <select name="new_agent" required class="p-1 border border-gray-300 rounded text-xs focus:ring-2 focus:ring-blue-500 outline-none">
                      <option value="">Select</option>
                      <?php 
                      $agents->data_seek(0);
                      while($ag = $agents->fetch_assoc()): 
                      ?>
                        <option value="<?php echo $ag['agent_id']; ?>" <?php echo ($r['assigned_agent'] == $ag['agent_id']) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($ag['name']); ?>
                        </option>
                      <?php endwhile; ?>
                    </select>
                    <button type="submit" name="reassign" class="bg-gradient-to-r from-green-500 to-green-700 text-white px-2 py-1 rounded text-xs font-medium hover:shadow-md transition-all">
                      Assign
                    </button>
                  </form>
                </td>
              </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No parcels found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
