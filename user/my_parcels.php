<?php
include '../includes/db_connect.php';
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: user_login.php");
    exit();
}

$uid = $_SESSION['user_id'];

$where = "user_id = $uid";
$filters_applied = false;
$status_filter = '';

if(isset($_GET['status']) && !isset($_GET['filter'])){
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    if($status == 'Pending') {
        $where .= " AND status != 'Delivered'";
    } else {
        $where .= " AND status = '$status'";
    }
    $filters_applied = true;
    $status_filter = $status;
}

if(isset($_GET['filter'])){
    if(!empty($_GET['status'])){
        $status = mysqli_real_escape_string($conn, $_GET['status']);
        if($status == 'Pending') {
            $where .= " AND status != 'Delivered'";
        } else {
            $where .= " AND status = '$status'";
        }
        $filters_applied = true;
        $status_filter = $status;
    }
    if(!empty($_GET['parcel_id'])){
        $pid = intval($_GET['parcel_id']);
        $where .= " AND parcel_id = $pid";
        $filters_applied = true;
    }
    if(!empty($_GET['parcel_name'])){
        $pname = mysqli_real_escape_string($conn, $_GET['parcel_name']);
        $where .= " AND parcel_name LIKE '%$pname%'";
        $filters_applied = true;
    }
    if(!empty($_GET['warehouse'])){
        $wh_id = intval($_GET['warehouse']);
        $where .= " AND current_warehouse_id = $wh_id";
        $filters_applied = true;
    }
    if(!empty($_GET['min_weight'])){
        $min_wt = floatval($_GET['min_weight']);
        $where .= " AND weight >= $min_wt";
        $filters_applied = true;
    }
    if(!empty($_GET['max_weight'])){
        $max_wt = floatval($_GET['max_weight']);
        $where .= " AND weight <= $max_wt";
        $filters_applied = true;
    }
    if(!empty($_GET['min_price'])){
        $min_pr = floatval($_GET['min_price']);
        $where .= " AND price >= $min_pr";
        $filters_applied = true;
    }
    if(!empty($_GET['max_price'])){
        $max_pr = floatval($_GET['max_price']);
        $where .= " AND price <= $max_pr";
        $filters_applied = true;
    }
}

$query = "SELECT p.*, w.city_name AS current_city, a.name AS agent_name 
          FROM parcels p
          LEFT JOIN warehouses w ON p.current_warehouse_id = w.warehouse_id
          LEFT JOIN agents a ON p.assigned_agent = a.agent_id
          WHERE $where 
          ORDER BY p.booking_date DESC";

$result = $conn->query($query);

$warehouses = $conn->query("SELECT * FROM warehouses WHERE is_active=1 ORDER BY city_name ASC");
?>

<?php include '../includes/header.php'; ?>

<div class="min-h-screen py-12 px-6">
  <div class="w-full max-w-7xl mx-auto glass-card p-6 relative overflow-hidden">

    <div class="absolute -top-8 -right-8 w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 opacity-25 blur-2xl rounded-full"></div>
    <div class="absolute -bottom-10 -left-10 w-28 h-28 bg-gradient-to-tr from-purple-500 to-pink-500 opacity-25 blur-2xl rounded-full"></div>

    <div class="flex justify-between items-center mb-6 relative z-10">
      <div>
        <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">My Parcels</h2>
        <?php if(isset($_GET['status']) && !isset($_GET['filter'])): ?>
          <p class="text-gray-600 mt-1">Showing: <span class="font-semibold text-blue-600"><?php echo htmlspecialchars($_GET['status']); ?></span> Parcels</p>
        <?php endif; ?>
      </div>
      <a href="dashboard.php"
         class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-full font-medium shadow-md transition-all duration-300 hover:scale-105 hover:shadow-lg">
        ← Back
      </a>
    </div>

    <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-200 relative z-10">
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
          <label class="text-sm font-medium text-gray-600">Parcel Name</label>
          <input type="text" name="parcel_name" value="<?php echo $_GET['parcel_name'] ?? ''; ?>" 
                 class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                 placeholder="Enter name">
        </div>

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
          <label class="text-sm font-medium text-gray-600">Current Location</label>
          <select name="warehouse" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">All Locations</option>
            <?php 
            $warehouses->data_seek(0);
            while($wh = $warehouses->fetch_assoc()): 
            ?>
              <option value="<?php echo $wh['warehouse_id']; ?>" 
                      <?php echo (isset($_GET['warehouse']) && $_GET['warehouse']==$wh['warehouse_id'])?'selected':''; ?>>
                <?php echo $wh['city_name']; ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div>
          <label class="text-sm font-medium text-gray-600">Min Weight (kg)</label>
          <input type="number" step="0.1" name="min_weight" value="<?php echo $_GET['min_weight'] ?? ''; ?>" 
                 class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                 placeholder="Min">
        </div>

        <div>
          <label class="text-sm font-medium text-gray-600">Max Weight (kg)</label>
          <input type="number" step="0.1" name="max_weight" value="<?php echo $_GET['max_weight'] ?? ''; ?>" 
                 class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                 placeholder="Max">
        </div>

        <div>
          <label class="text-sm font-medium text-gray-600">Min Price (₹)</label>
          <input type="number" step="0.01" name="min_price" value="<?php echo $_GET['min_price'] ?? ''; ?>" 
                 class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                 placeholder="Min">
        </div>

        <div>
          <label class="text-sm font-medium text-gray-600">Max Price (₹)</label>
          <input type="number" step="0.01" name="max_price" value="<?php echo $_GET['max_price'] ?? ''; ?>" 
                 class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                 placeholder="Max">
        </div>

        <div class="flex items-end gap-2 md:col-span-4">
          <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2 px-4 rounded-lg font-medium hover:shadow-lg transition-all">
            <i class="fa-solid fa-search"></i> Apply Filters
          </button>
          <a href="my_parcels.php" class="bg-gray-500 text-white py-2 px-4 rounded-lg font-medium hover:bg-gray-600 transition-all">
            <i class="fa-solid fa-times"></i> Clear
          </a>
        </div>
      </form>
    </div>

    <?php if($filters_applied): ?>
      <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded-lg text-green-700">
        <i class="fa-solid fa-check-circle"></i> Filters applied. Showing filtered results.
      </div>
    <?php endif; ?>

    <div class="overflow-x-auto relative z-10 rounded-xl border border-gray-200 shadow-md">
      <table class="w-full text-sm text-left text-gray-600">
        <thead class="text-xs uppercase bg-gradient-to-r from-blue-600 to-purple-600 text-white">
          <tr>
            <th scope="col" class="px-6 py-3">ID</th>
            <th scope="col" class="px-6 py-3">Parcel Name</th>
            <th scope="col" class="px-6 py-3">Sender</th>
            <th scope="col" class="px-6 py-3">Receiver</th>
            <th scope="col" class="px-6 py-3">Weight</th>
            <th scope="col" class="px-6 py-3">Price</th>
            <th scope="col" class="px-6 py-3">Status</th>
            <th scope="col" class="px-6 py-3">Current Location</th>
            <th scope="col" class="px-6 py-3">Agent</th>
            <th scope="col" class="px-6 py-3">Date</th>
            <th scope="col" class="px-6 py-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr class="bg-white border-b hover:bg-gray-50 transition duration-200">
              <td class="px-6 py-4 font-medium text-gray-800"><?php echo $row['parcel_id']; ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($row['parcel_name']); ?></td>
              <td class="px-6 py-4"><?php echo nl2br(htmlspecialchars(substr($row['sender_address'], 0, 30))).'...'; ?></td>
              <td class="px-6 py-4"><?php echo nl2br(htmlspecialchars(substr($row['receiver_address'], 0, 30))).'...'; ?></td>
              <td class="px-6 py-4"><?php echo $row['weight']; ?> kg</td>
              <td class="px-6 py-4">₹<?php echo $row['price']; ?></td>
              <td class="px-6 py-4">
                <?php
                  $status = $row['status'];
                  $badgeClass = match($status) {
                      'Delivered' => 'bg-green-100 text-green-800 border border-green-300',
                      'In Transit' => 'bg-yellow-100 text-yellow-800 border border-yellow-300',
                      'Picked' => 'bg-blue-100 text-blue-800 border border-blue-300',
                      'Booked' => 'bg-purple-100 text-purple-800 border border-purple-300',
                      default => 'bg-gray-100 text-gray-700 border border-gray-300',
                  };
                ?>
                <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $badgeClass; ?>">
                  <?php echo $status; ?>
                </span>
              </td>
              <td class="px-6 py-4"><?php echo $row['current_city'] ?? 'N/A'; ?></td>
              <td class="px-6 py-4"><?php echo $row['agent_name'] ?? 'Not Assigned'; ?></td>
              <td class="px-6 py-4 text-gray-600"><?php echo date('d-M-Y', strtotime($row['booking_date'])); ?></td>
              <td class="px-6 py-4">
                <a href="track_parcel.php?id=<?php echo $row['parcel_id']; ?>"
                   class="inline-flex items-center gap-1 bg-gradient-to-r from-blue-500 to-blue-700 text-white px-3 py-1.5 rounded-full text-xs font-medium shadow-sm transition-all duration-300 hover:scale-105 hover:shadow-md">
                  <i class="fa-solid fa-map-location-dot"></i> Track
                </a>
              </td>
            </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="11" class="px-6 py-8 text-center text-gray-500">
                <i class="fa-solid fa-inbox fa-3x mb-3 text-gray-300"></i>
                <p>No parcels found.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
