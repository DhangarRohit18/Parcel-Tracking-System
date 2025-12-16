<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

if (!isset($_GET['parcel_id'])) {
    echo json_encode(['error' => 'Parcel ID required']);
    exit;
}

$parcel_id = intval($_GET['parcel_id']);

$query = "SELECT 
    plh.history_id,
    plh.parcel_id,
    plh.status,
    plh.remarks,
    plh.timestamp,
    w.warehouse_id,
    w.city_name,
    w.state_name,
    w.latitude,
    w.longitude,
    w.address AS warehouse_address
FROM parcel_location_history plh
JOIN warehouses w ON plh.warehouse_id = w.warehouse_id
WHERE plh.parcel_id = ?
ORDER BY plh.timestamp ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $parcel_id);
$stmt->execute();
$result = $stmt->get_result();

$locations = [];
while ($row = $result->fetch_assoc()) {

    if ($row['latitude'] === null || $row['longitude'] === null) {
        $locations[] = [
            'error' => 'Coordinates not available',
            'city_name' => $row['city_name'],
            'status' => $row['status'],
            'timestamp' => $row['timestamp']
        ];
        continue;
    }
    
    $locations[] = [
        'history_id' => $row['history_id'],
        'warehouse_id' => $row['warehouse_id'],
        'city_name' => $row['city_name'],
        'state_name' => $row['state_name'],
        'latitude' => floatval($row['latitude']),
        'longitude' => floatval($row['longitude']),
        'warehouse_address' => $row['warehouse_address'],
        'status' => $row['status'],
        'remarks' => $row['remarks'],
        'timestamp' => $row['timestamp']
    ];
}

$currentQuery = "SELECT 
    p.parcel_id,
    p.parcel_name,
    p.status AS current_status,
    w.city_name AS current_city,
    w.latitude AS current_lat,
    w.longitude AS current_lng
FROM parcels p
LEFT JOIN warehouses w ON p.current_warehouse_id = w.warehouse_id
WHERE p.parcel_id = ?";

$stmt2 = $conn->prepare($currentQuery);
$stmt2->bind_param("i", $parcel_id);
$stmt2->execute();
$currentResult = $stmt2->get_result();
$currentData = $currentResult->fetch_assoc();

echo json_encode([
    'parcel_id' => $parcel_id,
    'parcel_name' => $currentData['parcel_name'] ?? 'Unknown',
    'current_status' => $currentData['current_status'] ?? 'Unknown',
    'current_location' => $currentData['current_city'] ?? 'Unknown',
    'current_coordinates' => [
        'lat' => $currentData['current_lat'] ? floatval($currentData['current_lat']) : null,
        'lng' => $currentData['current_lng'] ? floatval($currentData['current_lng']) : null
    ],
    'path_history' => $locations,
    'total_locations' => count($locations)
]);

$stmt->close();
$stmt2->close();
$conn->close();
?>
