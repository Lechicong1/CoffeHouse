<?php
echo "PHP hoạt động OK!<br>";
echo "URL: " . ($_GET['url'] ?? 'Không có URL parameter');
phpinfo();
?>

