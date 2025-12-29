<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Barista Dashboard'; ?></title>
    <base href="http://localhost/COFFEE_PHP/">
    <link rel="stylesheet" href="Public/Css/barista-style.css">
    <!-- Font Awesome for icons if needed -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <?php
            if (isset($data['page'])) {
                require_once './web/Views/BaristaDashBoard/Pages/' . $data['page'] . '.php';
            }
        ?>
    </div>
    <script src="Public/Js/barista-logic.js"></script>
</body>
</html>
