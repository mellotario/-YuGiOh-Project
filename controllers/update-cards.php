<?php
include './includes/authenticate.php';
include './includes/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $id = $data['id'];
    $name = $data['name'];
    $description = $data['description'];
    $image_url = $data['image_url'];
    $atk = $data['atk'];
    $def = $data['def'];
    $level = $data['level'];
    $race = $data['race'];
    $created_at = $data['created_at'];
    $updated_at = $data['updated_at'];

    $stmt = $db->prepare("UPDATE cards SET name = ?, description = ?, image_url = ?, atk = ?, def = ?, level = ?, race = ?, created_at = ?, updated_at = ? WHERE id = ?");
    $stmt->execute([$name, $description, $image_url, $atk, $def, $level, $race, $created_at, $updated_at, $id]);

    echo json_encode(["message" => "Card updated successfully"]);
} else {
    echo json_encode(["message" => "Method not allowed"]);
}
?>
