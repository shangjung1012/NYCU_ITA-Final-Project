<?php
// reset_compare.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['compare_list'] = [];
    echo 'success';
} else {
    echo 'invalid_request';
}
?>
