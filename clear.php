<?php
session_start();
session_unset();
session_destroy();
echo "All sessions cleared. <a href='login.php'>Return to Login</a>";
?>