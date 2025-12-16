<?php
session_start();
if(!isset($_SESSION['agent_id'])){
  header("Location: agent_login.php");
  exit();
}

include '../includes/db_connect.php';
include '../includes/header.php';

$aid = $_SESSION['agent_id'];

$where = "assigned_agent = $aid";
$filters_applied = false;
$status_filter = '';

if(isset($_GET['status']) && !isset($_GET['filter'])){
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    if($status == 'Pending') {
        $where .= " AND p.status NOT IN ('Delivered', 'In Transit')";
    } else {
        $where .= " AND p.status = '$status'";
    }
    $filters_applied = true;
    $status_filter = $status;
}

if(isset($_GET['filter'])){
    if(!empty($_GET['status'])){
        $status = mysqli_real_escape_string($conn, $_GET['status']);
        if($status == 'Pending') {
            $where .= " AND p.status NOT IN ('Delivered', 'In Transit')";
        } else {
            $where .= " AND p.status = '$status'";
        }
        $filters_applied = true;
        $status_filter = $status;
    }
    if(!empty($_GET['user_id'])){
        $uid = intval($_GET['user_id']);
        $where .= " AND p.user_id = $uid";
        $filters_applied = true;
    }
    if(!empty($_GET['weight_min'])){
        $wt_min = floatval($_GET['weight_min']);
        $where .= " AND p.weight >= $wt_min";
        $filters_applied = true;
    }
    if(!empty($_GET['weight_max'])){
        $wt_max = floatval($_GET['weight_max']);
        $where .= " AND p.weight <= $wt_max";
        $filters_applied = true;
    }
    if(!empty($_GET['warehouse'])){
        $wh = intval($_GET['warehouse']);
        $where .= " AND p.current_warehouse_id = $wh";
        $filters_applied = true;
    }
}

$query = "SELECT p.*, u.name AS user_name, w.city_name AS warehouse_city
          FROM parcels p
          LEFT JOIN users u ON p.user_id = u.user_id
          LEFT JOIN warehouses w ON p.current_warehouse_id = w.warehouse_id
          WHERE $where
          ORDER BY p.booking_date DESC";

$result = $conn->query($query);
$warehouses = $conn->query("SELECT * FROM warehouses WHERE is_active=1 ORDER BY city_name ASC");
$users = $conn->query("SELECT user_id, name FROM users ORDER BY name ASC");
?>

<div class="min-h-screen py-12 px-6">
  <div class="w-full max-w-7xl mx-auto glass-card p-6 relative overflow-hidden">

    <div class="flex justify-between items-center mb-6 relative z-10">
      <div>
        <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">Assigned Parcels</h2>
        <?php if(isset($_GET['status']) && !isset($_GET['filter'])): ?>
          <p class="text-gray-600 mt-1">Showing: <span class="font-semibold text-blue-600"><?php echo htmlspecialchars($_GET['status']); ?></span> Parcels</p>
        <?php endif; ?>
      </div>
      <a href="dashboard.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-full font-medium shadow-md hover:scale-105 transition-all">
        ← Back
      </a>
    </div>

    <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-200 relative z-10">
      <h3 class="font-semibold text-gray-700 mb-3"><i class="fa-solid fa-filter"></i> Filter Parcels</h3>
      <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <input type="hidden" name="filter" value="1">
        
        <div>
          <label class="text-sm font-medium text-gray-600">Status</label>
          <select name="status" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">All Status</option>
            <option value="Booked" <?php echo ($status_filter=='Booked')?'selected':''; ?>>Booked</option>
            <option value="Picked" <?php echo ($status_filter=='Picked')?'selected':''; ?>>Picked</option>
            <option value="In Transit" <?php echo ($status_filter=='In Transit')?'selected':''; ?>>In Transit</option>
            <option value="Delivered" <?php echo ($status_filter=='Delivered')?'selected':''; ?>>Delivered</option>
            <option value="Pending" <?php echo ($status_filter=='Pending')?'selected':''; ?>>Pending</option>
          </select>
        </div>
        
        <div>
          <label class="text-sm font-medium text-gray-600">User</label>
          <select name="user_id" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">All Users</option>
            <?php 
            $users->data_seek(0);
            while($u = $users->fetch_assoc()): 
            ?>
              <option value="<?php echo $u['user_id']; ?>" <?php echo (isset($_GET['user_id']) && $_GET['user_id']==$u['user_id'])?'selected':''; ?>>
                <?php echo htmlspecialchars($u['name']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div>
          <label class="text-sm font-medium text-gray-600">Min Weight (kg)</label>
          <input type="number" step="0.1" name="weight_min" value="<?php echo $_GET['weight_min'] ?? ''; ?>" 
                 class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                 placeholder="Min">
        </div>

        <div>
          <label class="text-sm font-medium text-gray-600">Max Weight (kg)</label>
          <input type="number" step="0.1" name="weight_max" value="<?php echo $_GET['weight_max'] ?? ''; ?>" 
                 class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                 placeholder="Max">
        </div>

        <div>
          <label class="text-sm font-medium text-gray-600">Warehouse</label>
          <select name="warehouse" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">All Warehouses</option>
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

        <div class="flex items-end gap-2 md:col-span-5">
          <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2 px-6 rounded-lg font-medium hover:shadow-lg transition-all">
            <i class="fa-solid fa-search"></i> Apply
          </button>
          <a href="assigned_parcels.php" class="bg-gray-500 text-white py-2 px-4 rounded-lg font-medium hover:bg-gray-600 transition-all">
            Clear
          </a>
        </div>
      </form>
    </div>

    <div class="overflow-x-auto relative z-10 rounded-xl border border-gray-200 shadow-md">
      <table class="w-full text-sm text-left text-gray-600">
        <thead class="text-xs uppercase bg-gradient-to-r from-blue-600 to-purple-600 text-white">
          <tr>
            <th class="px-6 py-3">ID</th>
            <th class="px-6 py-3">User</th>
            <th class="px-6 py-3">Parcel Name</th>
            <th class="px-6 py-3">Weight</th>
            <th class="px-6 py-3">Status</th>
            <th class="px-6 py-3">Location</th>
          </tr>
        </thead>
        <tbody>
          <?php if($result->num_rows > 0): ?>
            <?php while($r = $result->fetch_assoc()): ?>
            <tr class="bg-white border-b hover:bg-gray-50">
              <td class="px-6 py-4 font-medium"><?php echo $r['parcel_id']; ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($r['user_name']); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($r['parcel_name']); ?></td>
              <td class="px-6 py-4"><?php echo $r['weight']; ?> kg</td>
              <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                  <?php echo $r['status']; ?>
                </span>
              </td>
              <td class="px-6 py-4"><?php echo $r['warehouse_city'] ?? 'N/A'; ?></td>
            </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="px-6 py-8 text-center text-gray-500">No parcels assigned.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
