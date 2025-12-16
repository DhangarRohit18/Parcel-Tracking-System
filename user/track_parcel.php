<?php
include '../includes/db_connect.php';
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: user_login.php");
    exit();
}

$parcel_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$uid = $_SESSION['user_id'];

$parcel = null;
if($parcel_id > 0){
    $query = "SELECT p.*, w.city_name AS current_city, u.name AS user_name, a.name AS agent_name
              FROM parcels p
              LEFT JOIN warehouses w ON p.current_warehouse_id = w.warehouse_id
              LEFT JOIN users u ON p.user_id = u.user_id
              LEFT JOIN agents a ON p.assigned_agent = a.agent_id
              WHERE p.parcel_id = $parcel_id AND p.user_id = $uid";
    $result = $conn->query($query);
    if($result->num_rows > 0){
        $parcel = $result->fetch_assoc();
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="min-h-screen py-12 px-6">
  <div class="w-full max-w-6xl mx-auto">

    <?php if(!$parcel_id): ?>
      
      <div class="glass-card p-8 mb-6">
        <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 mb-6 text-center">
          Track Your Parcel
        </h2>
        <form method="GET" class="max-w-md mx-auto">
          <div class="flex gap-3">
            <input type="number" name="id" placeholder="Enter Parcel ID" required
                   class="flex-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
              Track
            </button>
          </div>
        </form>
        <div class="text-center mt-4">
          <a href="dashboard.php" class="text-blue-600 hover:underline">← Back to Dashboard</a>
        </div>
      </div>
    <?php elseif($parcel): ?>
      
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">
          Tracking: #<?php echo $parcel['parcel_id']; ?>
        </h2>
        <a href="my_parcels.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-full font-medium shadow-md hover:scale-105 transition-all">
          ← Back to Parcels
        </a>
      </div>

      <div class="glass-card p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <p class="text-sm text-gray-500">Parcel Name</p>
            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($parcel['parcel_name']); ?></p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Status</p>
            <p class="font-semibold text-gray-800">
              <span class="px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                <?php echo $parcel['status']; ?>
              </span>
            </p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Weight</p>
            <p class="font-semibold text-gray-800"><?php echo $parcel['weight']; ?> kg</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Price</p>
            <p class="font-semibold text-gray-800">₹<?php echo $parcel['price']; ?></p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Assigned Agent</p>
            <p class="font-semibold text-gray-800"><?php echo $parcel['agent_name'] ?? 'Not Assigned'; ?></p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Booking Date</p>
            <p class="font-semibold text-gray-800"><?php echo date('d M Y, h:i A', strtotime($parcel['booking_date'])); ?></p>
          </div>
        </div>
      </div>

      <div class="glass-card p-6 mb-6 text-center" id="currentLocationBox">
        <h3 class="text-xl font-bold text-gray-700 mb-2">
          <i class="fa-solid fa-location-dot text-red-500"></i> Current Location
        </h3>
        <p class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">
          <?php echo $parcel['current_city'] ?? 'Location not updated'; ?>
        </p>
        <p class="text-sm text-gray-500 mt-2">Last updated: <span id="lastUpdated">Loading...</span></p>
      </div>

      <div class="glass-card p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-700 mb-4">
          <i class="fa-solid fa-map"></i> Parcel Journey Map
        </h3>
        <div id="map" style="height: 500px; border-radius: 1rem;"></div>
      </div>

      <div class="glass-card p-6" id="locationHistory">
        <h3 class="text-xl font-bold text-gray-700 mb-4">
          <i class="fa-solid fa-clock-rotate-left"></i> Location History
        </h3>
        <div id="historyTimeline" class="space-y-4">
          <p class="text-center text-gray-500">Loading history...</p>
        </div>
      </div>

    <?php else: ?>
      <div class="glass-card p-8 text-center">
        <i class="fa-solid fa-exclamation-triangle fa-3x text-yellow-500 mb-4"></i>
        <h3 class="text-xl font-bold text-gray-700 mb-2">Parcel Not Found</h3>
        <p class="text-gray-500 mb-4">The parcel ID you entered doesn't exist or doesn't belong to you.</p>
        <a href="track_parcel.php" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-xl font-semibold hover:shadow-lg transition-all">
          Try Again
        </a>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php if($parcel): ?>
<script>
const parcelId = <?php echo $parcel_id; ?>;
let map, currentMarker, polyline;
let autoRefreshInterval;

function initMap() {
    map = L.map('map').setView([20.5937, 78.9629], 5); 
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);
}

async function loadParcelPath() {
    try {
        const response = await fetch(`../api/get_parcel_path.php?parcel_id=${parcelId}`);
        const data = await response.json();
        
        if(data.error) {
            console.error(data.error);
            return;
        }

        document.getElementById('currentLocationBox').querySelector('p.text-2xl').textContent = 
            data.current_location || 'Unknown';
        document.getElementById('lastUpdated').textContent = new Date().toLocaleString();

        if(currentMarker) map.removeLayer(currentMarker);
        if(polyline) map.removeLayer(polyline);

        const pathCoordinates = [];
        const bounds = [];

        data.path_history.forEach((loc, index) => {
            if(loc.latitude && loc.longitude) {
                const coords = [loc.latitude, loc.longitude];
                pathCoordinates.push(coords);
                bounds.push(coords);

                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background: ${index === data.path_history.length - 1 ? '#ef4444' : '#3b82f6'}; 
                                  color: white; border-radius: 50%; width: 30px; height: 30px; 
                                  display: flex; align-items: center; justify-content: center; 
                                  font-weight: bold; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                            ${index + 1}
                           </div>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });

                const marker = L.marker(coords, {icon: icon}).addTo(map);
                marker.bindPopup(`
                    <div style="font-family: 'Poppins', sans-serif;">
                        <h4 style="margin: 0; font-weight: bold; color: #1e40af;">${loc.city_name}, ${loc.state_name}</h4>
                        <p style="margin: 5px 0; font-size: 0.9em;">
                            <strong>Status:</strong> ${loc.status}<br>
                            <strong>Time:</strong> ${new Date(loc.timestamp).toLocaleString()}<br>
                            ${loc.remarks ? `<strong>Remarks:</strong> ${loc.remarks}` : ''}
                        </p>
                    </div>
                `);

                if(index === data.path_history.length - 1) {
                    currentMarker = marker;
                }
            }
        });

        if(pathCoordinates.length > 1) {
            polyline = L.polyline(pathCoordinates, {
                color: '#6366f1',
                weight: 4,
                opacity: 0.7,
                dashArray: '10, 10',
                lineJoin: 'round'
            }).addTo(map);
        }

        if(bounds.length > 0) {
            map.fitBounds(bounds, {padding: [50, 50]});
        }

        displayHistory(data.path_history);

    } catch (error) {
        console.error('Error loading parcel path:', error);
    }
}

function displayHistory(history) {
    const timeline = document.getElementById('historyTimeline');
    if(history.length === 0) {
        timeline.innerHTML = '<p class="text-center text-gray-500">No location history available.</p>';
        return;
    }

    timeline.innerHTML = history.map((loc, index) => `
        <div class="flex gap-4 items-start">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full ${index === history.length - 1 ? 'bg-red-500' : 'bg-blue-500'} text-white flex items-center justify-center font-bold text-sm">
                    ${index + 1}
                </div>
                ${index < history.length - 1 ? '<div class="w-0.5 h-12 bg-gray-300"></div>' : ''}
            </div>
            <div class="flex-1 pb-6">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="font-bold text-gray-800">${loc.city_name}, ${loc.state_name}</h4>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs font-medium">${loc.status}</span>
                    </p>
                    ${loc.remarks ? `<p class="text-sm text-gray-500 mt-2">${loc.remarks}</p>` : ''}
                    <p class="text-xs text-gray-400 mt-2">
                        <i class="fa-solid fa-clock"></i> ${new Date(loc.timestamp).toLocaleString()}
                    </p>
                </div>
            </div>
        </div>
    `).join('');
}

function startAutoRefresh() {
    autoRefreshInterval = setInterval(() => {
        loadParcelPath();
    }, 10000); // 10 seconds
}

document.addEventListener('DOMContentLoaded', function() {
    initMap();
    loadParcelPath();
    startAutoRefresh();
});

window.addEventListener('beforeunload', function() {
    if(autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});
</script>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
