<?php
$plain_password = 'passwordadminanda'; // Ganti dengan password yang Anda inginkan
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
echo "Password Asli: " . $plain_password . "<br>";
echo "Password Hash: " . $hashed_password;
// Contoh Output: $2y$10$Kj3..... (salin hash ini)
?>