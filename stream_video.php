<!--?php
$data = json_decode(file_get_contents("php://input"), true);
$image_data = $data['image'];

// Process image with the model
$response = file_get_contents('http://localhost:5000/predict-emotion', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode(['image' => $image_data])
    ]
]));

header("Content-Type: application/json");
echo $response;
?>-->

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connect to the database if necessary, or perform any other setup.
    $conn = new mysqli("localhost", "root", "", "sms_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the JSON input data
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['image'])) {
        // Image data in Base64 format
        $imageData = $input['image'];

        // Optional: Decode Base64 data
        $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));

        // Optional: Save the image (for testing purposes)
        $filename = 'uploads/' . uniqid('frame_', true) . '.jpg';
        file_put_contents($filename, $decodedImage);

        // Optionally, you could add code here to process the image, like sending it to
        // an emotion detection model or storing data in a database for monitoring, etc.

        // Respond with success message
        echo json_encode(["status" => "success", "message" => "Frame received successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "No image data received."]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
