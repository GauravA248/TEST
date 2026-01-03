<?php
$conn = new mysqli("localhost", "root", "", "jeevchi");

if ($conn->connect_error) {
    die("Database connection failed");
}
