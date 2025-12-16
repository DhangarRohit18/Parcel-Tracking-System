<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';
$query = "SELECT 
    warehouse_id,
    city_name,
    state_name,
    latitude,
    longitude,
    address,
    is_active
FROM warehouses
WHERE is_active = 1
ORDER BY city_name ASC";

$result = $conn->query($query);

$warehouses = [];
while ($row = $result->fetch_assoc()) {
    $warehouses[] = [
        'warehouse_id' => $row['warehouse_id'],
        'city_name' => $row['city_name'],
        'state_name' => $row['state_name'],
        'latitude' => floatval($row['latitude']),
        'longitude' => floatval($row['longitude']),
        'address' => $row['address'],
        'is_active' => $row['is_active']
    ];
}

echo json_encode([
    'total_warehouses' => count($warehouses),
    'warehouses' => $warehouses
]);

$conn->close();
?>
