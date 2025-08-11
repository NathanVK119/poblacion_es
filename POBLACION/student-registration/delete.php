<?php
include __DIR__ . '/../shared-source/database-connection/connect.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "DELETE FROM `poblacion` WHERE lrn = '$id'";
    $result = mysqli_query($con, $sql);
    
    if($result) {
        header("Location: view.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($con);
    }
} else {
    echo "No ID provided";
}
?>
