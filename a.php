<?php
// update_password.php
require_once "layouts/config.php";

$password = "123456";
$new_hash = password_hash($password, PASSWORD_BCRYPT);

echo "New Hash: " . $new_hash . "<br>";

$sql = "UPDATE users SET password = ? WHERE username = 'Admin'";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $new_hash);

if(mysqli_stmt_execute($stmt)) {
    echo "Password updated successfully!<br>";
    echo "New Hash: " . $new_hash . "<br>";
    
    // Test the new hash
    if (password_verify($password, $new_hash)) {
        echo "Verification: SUCCESS!";
    } else {
        echo "Verification: FAILED!";
    }
} else {
    echo "Error updating password: " . mysqli_error($link);
}

mysqli_close($link);
?>