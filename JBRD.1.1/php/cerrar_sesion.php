<?php
session_name('jbrd');
session_start();
session_destroy();
header("Location: ../index.php");
exit();
?>
 