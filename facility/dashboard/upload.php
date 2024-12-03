<?php
session_start();

include_once '../../db/db_conn.php';  // Adjust the path accordingly
$user_id = $_SESSION["usersid"];

function setUserDataInSession($userData) {
    $_SESSION["usersfname"] = $userData["fname"];
    $_SESSION["usersmname"] = $userData["mname"];
    $_SESSION["userslname"] = $userData["lname"];
    $_SESSION["usersuid"] = $userData["username"];
    $_SESSION["usersrole"] = $userData["role"];
    $_SESSION["usersimg"] = $userData["img"];
    $_SESSION["email"] = $userData["staff_email"];
    $_SESSION["usersbday"] = $userData["birth_date"];
    $_SESSION["usersname"] = $userData["lname"] . ', ' . $userData["fname"] . ' ' . $userData["mname"];
    $_SESSION["usersconact"] = $userData["contact"];
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include your database connection code here

    // Check if the file is selected for upload
    $imgPath = '';

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        // Generate a random string
        $randomString = uniqid();

        // Process the uploaded file
        $uploadDir = '../../assets/';
        $originalFileName = basename($_FILES['profile_image']['name']);
        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

        // Combine the random string and the original file name with an underscore
        $newFileName = $randomString . '_' . $originalFileName;

        $uploadFile = $uploadDir . $newFileName;

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
            // File upload success
            $imgPath = $uploadFile;
            echo "File uploaded successfully!\n";
        } else {
            // File upload failed
            echo "Error uploading file.\n";
        }
    }

    // Extract form data
    $id = $_POST['id'];
    $usersfname = $_POST['usersfname'];
    $usersmname = $_POST['usersmname'];
    $userslname = $_POST['userslname'];
    $usersuid = $_POST['usersuid'];
    $birthdate = $_POST['birthdate'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $currentPassword = $_POST["currentpassword"];
    $newPassword = $_POST["newpassword"];
    $confirmPassword = $_POST["confirmpassword"];

    $proceedWithProfileUpdate = true;

// Check if the password needs to be updated
    if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
    // Sanitize and validate user input to prevent SQL injection
    $user_id = mysqli_real_escape_string($conn, $user_id);

    // Use prepared statements to avoid SQL injection
    $selectQuery = "SELECT pwd FROM staff WHERE staff_id = ?";
    $stmtSelect = mysqli_prepare($conn, $selectQuery);

    if ($stmtSelect) {
        mysqli_stmt_bind_param($stmtSelect, "s", $user_id);
        mysqli_stmt_execute($stmtSelect);

        $result = mysqli_stmt_get_result($stmtSelect);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            $hashed_password = $user['pwd'];

            // Verify the current password
            if (password_verify($currentPassword, $hashed_password)) {
                // Set the flag to indicate that password update is required
                $updatePassword = true;
            } else {
                echo "Current password is incorrect.";
                $proceedWithProfileUpdate = false; // Do not proceed with the profile update
            }
        } else {
            echo "User not found.";
            $proceedWithProfileUpdate = false; // Do not proceed with the profile update
        }

        mysqli_stmt_close($stmtSelect);
    } else {
        echo "Error preparing select statement: " . mysqli_error($conn);
        $proceedWithProfileUpdate = false; // Do not proceed with the profile update
    }
}


// Proceed with the staff profile update only if the flag is set
if ($proceedWithProfileUpdate) {
    // Update the staff profile (excluding the password) using the extracted data
    if (!empty($imgPath)) {
        // Update the img field only if a new file is uploaded
        $stmt = $conn->prepare("UPDATE staff SET 
                                fname = ?, 
                                mname = ?, 
                                lname = ?, 
                                username = ?, 
                                birth_date = ?, 
                                staff_email = ?, 
                                img = ?,
                                contact_num = ?
                                WHERE staff_id = ?");
        $stmt->bind_param('ssssssssi', $usersfname, $usersmname, $userslname, $usersuid, $birthdate, $email, $newFileName, $contact, $id);
    } else {
        // Update without modifying the img field
        $stmt = $conn->prepare("UPDATE staff SET 
                                fname = ?, 
                                mname = ?, 
                                lname = ?, 
                                username = ?, 
                                birth_date = ?, 
                                staff_email = ?,
                                contact_num = ?
                                WHERE staff_id = ?");
        $stmt->bind_param('sssssssi', $usersfname, $usersmname, $userslname, $usersuid, $birthdate, $email, $contact, $id);
    }

    // Execute the staff profile update
    if ($stmt->execute()) {
        // Database update success
        echo "Staff profile updated successfully!";

        // Update the session data
        $newUserData = array(
            "fname" => $usersfname,
            "mname" => $usersmname,
            "lname" => $userslname,
            "username" => $usersuid,
            "role" => $_SESSION["usersrole"],
            "img" => (!empty($imgPath)) ? $newFileName : $_SESSION["usersimg"],
            "staff_email" => $email,
            "birth_date" => $birthdate,
            "contact" => $contact
        );
        setUserDataInSession($newUserData);

        // Update the password if required
        if ($updatePassword) {
            // Hash the new password before updating in the database
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Use prepared statement for the password update query
            $updatePasswordQuery = "UPDATE staff SET pwd = ? WHERE staff_id = ?";
            $stmtUpdatePassword = mysqli_prepare($conn, $updatePasswordQuery);

            if ($stmtUpdatePassword) {
                mysqli_stmt_bind_param($stmtUpdatePassword, "ss", $hashedNewPassword, $user_id);
                mysqli_stmt_execute($stmtUpdatePassword);

                echo "Password updated successfully!";
            } else {
                echo "Error preparing password update statement: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmtUpdatePassword);
        }
    } else {
        // Database update failed
        echo "Error updating staff profile: " . $conn->error;
    }

    // Close the main connection
    mysqli_close($conn);
} else {
    // Respond with an error message if the password check fails
    echo "Password check failed. Update aborted.";
}
    // Close the main connection
    mysqli_close($conn);
} else {
    // Respond with an error message if the request method is not POST
    echo "Invalid request method!";
}