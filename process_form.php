<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Replace with your DB details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "webhook";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Get field values from the form
    $fname = $conn->real_escape_string($_POST["firstName"]);
    $lname = $conn->real_escape_string($_POST["lastName"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $message = $conn->real_escape_string($_POST["message"]);
    
    // Insert record
    $sql = "INSERT INTO form_data (firstname, lastname, email, message) VALUES ('$fname', '$lname', '$email', '$message')";
    
    if ($conn->query($sql) === TRUE) {
        // Send webhook notification
        $webhook_url = "http://localhost/webhook/webhook.php"; // Adjust URL if needed
        $webhook_data = array(
            'action' => 'insert', // Indicate that a new record is inserted
            'data' => array(
                'firstName' => $fname,
                'lastName' => $lname,
                'email' => $email,
                'message' => $message
            )
        );

        // Initialize cURL session
        $curl = curl_init($webhook_url);

        // Set cURL options
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($webhook_data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true); // Verbose mode for debugging
        
        // Execute cURL request
        $response = curl_exec($curl);

        // Check for errors
        if($response === false){
            echo 'cURL Error: ' . curl_error($curl);
        }

        // Close cURL session
        curl_close($curl);

        // Optionally, log the response for debugging
        // file_put_contents('webhook_response.log', $response);

        echo "Data submitted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $conn->close();
    exit; // Stop further execution to prevent additional content being sent
}
?>
