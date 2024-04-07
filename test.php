<?php

// Database configuration
$dbHost = '127.0.0.1';
$dbUsername = 'root';
$dbPassword = '';
$dbDatabase = 'yearbook';

// Create database connection
$dbConn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);

// Check connection
if ($dbConn->connect_error) {
    die("Connection failed: " . $dbConn->connect_error);
}
echo "Database connected successfully\n";

// Path to the file containing the data
$filename = 'C:\\Users\\Debayan\\Desktop\\Test\\2024 Batch.xlsx - Sheet1.csv';  // Ensure path is correctly escaped

// Prepare the INSERT statement
$sql = "INSERT INTO users (name, rollno, department, dob, HOR, password) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $dbConn->prepare($sql);

if (!$stmt) {
    die("Error preparing statement: " . $dbConn->error);
}

// Open the file for reading
if (($handle = fopen($filename, "r")) !== FALSE) {
    // Skip the first line if it contains header names
    fgetcsv($handle);

    // Read one line at a time until the end of the file
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Map the CSV fields to the database columns
        $name = $data[3]; // Assuming 'Name' is in the 4th column
        $rollno = $data[2]; // Assuming 'Rollno' is in the 3rd column
        $department = $data[4]; // Assuming 'Dept' is in the 5th column
        $dob = $data[5]; // Assuming 'DOB' is in the 6th column
        $hor = $data[6]; // Assuming 'Hall' is in the 7th column
        $password = password_hash($dob, PASSWORD_DEFAULT); // Hashing the DOB for password

        // Bind the parameters to the SQL query
        if (!$stmt->bind_param("ssssss", $name, $rollno, $department, $dob, $hor, $password)) {
            die("Binding parameters failed: " . $stmt->error);
        }

        // Execute the statement
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error . "\n";
        } else {
            echo "New record created successfully for " . $name . "\n";
        }
    }

    // Close the file
    fclose($handle);
}

// Close statement and connection
$stmt->close();
$dbConn->close();

?>
