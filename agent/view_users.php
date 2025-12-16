<?php
session_start();
if(!isset($_SESSION['agent_id'])){
    header("Location: agent_login.php");
    exit();
}

include '../includes/db_connect.php';
include '../includes/header.php';

$where = "1=1";
$filters_applied = false;

if(isset($_GET['filter'])){
    if(!empty($_GET['city'])){
        $city = mysqli_real_escape_string($conn, $_GET['city']);
        $where .= " AND city LIKE '%$city%'";
        $filters_applied = true;
    }
    if(!empty($_GET['name'])){
        $name = mysqli_real_escape_string($conn, $_GET['name']);
        $where .= " AND name LIKE '%$name%'";
        $filters_applied = true;
    }
}

$query = "SELECT * FROM users WHERE $where ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<div class="min-h-screen py-12 px-6">
  <div class="w-full max-w-7xl mx-auto glass-card p-6">

    <div class="flex justify-between items-center mb-6">
      <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">View Users</h2>
      <a href="dashboard.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-full font-medium shadow-md hover:scale-105 transition-all">
        ← Back
      </a>
    </div>

    <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
      <h3 class="font-semibold text-gray-700 mb-3"><i class="fa-solid fa-filter"></i> Filter Users</h3>
      <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="hidden" name="filter" value="1">
        
        <div>
          <label class="text-sm font-medium text-gray-600">Name</label>
          <input type="text" name="name" value="<?php echo $_GET['name'] ?? ''; ?>" 
                 class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                 placeholder="Search by name">
        </div>

        <div>
          <label class="text-sm font-medium text-gray-600">City</label>
          <input type="text" name="city" value="<?php echo $_GET['city'] ?? ''; ?>" 
                 class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                 placeholder="Search by city">
        </div>

        <div class="flex items-end gap-2">
          <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2 px-4 rounded-lg font-medium hover:shadow-lg transition-all">
            <i class="fa-solid fa-search"></i> Apply
          </button>
          <a href="view_users.php" class="bg-gray-500 text-white py-2 px-4 rounded-lg font-medium hover:bg-gray-600 transition-all">
            Clear
          </a>
        </div>
      </form>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-md">
      <table class="w-full text-sm text-left text-gray-600">
        <thead class="text-xs uppercase bg-gradient-to-r from-blue-600 to-purple-600 text-white">
          <tr>
            <th class="px-6 py-3">ID</th>
            <th class="px-6 py-3">Name</th>
            <th class="px-6 py-3">Email</th>
            <th class="px-6 py-3">Phone</th>
            <th class="px-6 py-3">City</th>
            <th class="px-6 py-3">State</th>
            <th class="px-6 py-3">Registered</th>
          </tr>
        </thead>
        <tbody>
          <?php if($result->num_rows > 0): ?>
            <?php while($r = $result->fetch_assoc()): ?>
            <tr class="bg-white border-b hover:bg-gray-50">
              <td class="px-6 py-4 font-medium"><?php echo $r['user_id']; ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($r['name']); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($r['email']); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($r['phone']); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($r['city'] ?? 'N/A'); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($r['state'] ?? 'N/A'); ?></td>
              <td class="px-6 py-4"><?php echo date('d-M-Y', strtotime($r['created_at'])); ?></td>
            </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="px-6 py-8 text-center text-gray-500">No users found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
