<!-- ===================================
     FILE: menu.php
     MÔ TẢ: Trang Menu đầy đủ (Dynamic)
     =================================== -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Thực đơn Coffee House - Cà phê, Trà sữa, Đồ ăn vặt">
    <title>Thực đơn - Coffee House</title>
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/user-style.css">
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/user-menu-style.css">
</head>
<body>
    <?php
    $currentPage = 'menu';
    include __DIR__ . '/header.php';
    ?>

    <!-- MENU HERO -->
    <section class="menu-hero">
        <div class="menu-hero-content">
            <h1>THỰC ĐƠN</h1>
            <p>Khám phá hương vị đặc biệt từ Coffee House</p>
        </div>
    </section>

    <!-- CATEGORIES SHOWCASE -->
    <section class="categories-section">
        <?php if (isset($categories) && !empty($categories)): ?>
            <?php
            $index = 0;
            foreach ($categories as $category):
                $isReverse = ($index % 2 != 0) ? 'reverse' : '';
                $index++;
            ?>
            <div class="category-showcase <?= $isReverse ?>" id="category-<?= $category->id ?>">
                <div class="category-content">
                    <div class="category-text">
                        <h2><?= strtoupper(htmlspecialchars($category->name)) ?></h2>
                        <p><?= htmlspecialchars($category->description) ?></p>
                        <button class="btn-discover" onclick="window.location.href='/COFFEE_PHP/User/categoryProducts?id=<?= $category->id ?>'">
                            KHÁM PHÁ THÊM
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; color: var(--text-light);">
                <h3>Hiện tại chưa có danh mục nào</h3>
                <p>Vui lòng quay lại sau để khám phá thực đơn của chúng tôi!</p>
            </div>
        <?php endif; ?>
    </section>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
