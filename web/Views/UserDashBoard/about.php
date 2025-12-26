<!-- ===================================
     FILE: about.php
     MÔ TẢ: Trang Giới thiệu chi tiết về Coffee House
     =================================== -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Giới thiệu về Coffee House - Câu chuyện, sứ mệnh và giá trị của chúng tôi">
    <title>Về Coffee House - Câu chuyện của chúng tôi</title>
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/user-style.css">
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/user-about.css">
</head>
<body>
    <?php
    $currentPage = 'about';
    include __DIR__ . '/header.php';
    ?>

    <!-- PAGE HERO -->
    <section class="page-hero">
        <div>
            <h1>CÂU CHUYỆN CỦA CHÚNG TÔI</h1>
            <p style="font-size: 1.2rem; color: white;">Hành trình mang cà phê Việt Nam đến gần hơn với mọi người</p>
        </div>
    </section>

    <!-- STORY SECTION -->
    <section class="story-section">
        <!-- Our Beginning -->
        <div class="story-content">
            <div class="story-image">
                <img src="https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=800" alt="Our Beginning">
            </div>
            <div class="story-text">
                <h2>KHỞI ĐẦU CỦA CHÚNG TÔI</h2>
                <p>
                    Coffee House ra đời từ năm 2015 với một ước mơ đơn giản: mang đến cho người Việt 
                    một không gian cà phê hiện đại, thoải mái nhưng vẫn giữ được bản sắc văn hóa 
                    cà phê truyền thống.
                </p>
                <p>
                    Từ một cửa hàng nhỏ với 10 bàn ghế, chúng tôi đã không ngừng nỗ lực để mở rộng 
                    và phát triển. Ngày nay, Coffee House tự hào có mặt tại nhiều vị trí đắc địa, 
                    phục vụ hàng ngàn khách hàng mỗi ngày.
                </p>
            </div>
        </div>

        <!-- Our Philosophy -->
        <div class="story-content reverse">
            <div class="story-image">
                <img src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800" alt="Our Philosophy">
            </div>
            <div class="story-text">
                <h2>TRIẾT LÝ KINH DOANH</h2>
                <p>
                    Chúng tôi tin rằng cà phê không chỉ là một loại đồ uống, mà là cầu nối kết 
                    nối con người với nhau. Mỗi tách cà phê tại Coffee House được pha chế với 
                    tâm huyết và sự chăm chút tỉ mỉ.
                </p>
                <ul>
                    <li>Chất lượng là ưu tiên số 1</li>
                    <li>Không gian thoải mái, sang trọng</li>
                    <li>Dịch vụ tận tâm, chu đáo</li>
                    <li>Giá cả hợp lý, phù hợp túi tiền</li>
                    <li>Không ngừng đổi mới và sáng tạo</li>
                </ul>
            </div>
        </div>

        <!-- Our Coffee -->
        <div class="story-content">
            <div class="story-image">
                <img src="https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=800" alt="Our Coffee">
            </div>
            <div class="story-text">
                <h2>CÀ PHÊ CỦA CHÚNG TÔI</h2>
                <p>
                    Coffee House sử dụng 100% hạt cà phê Arabica và Robusta nguyên chất từ Tây Nguyên - 
                    vùng đất nổi tiếng với cà phê chất lượng cao. Mỗi hạt cà phê được tuyển chọn kỹ 
                    lưỡng, rang xay theo công thức riêng.
                </p>
                <p>
                    Chúng tôi làm việc trực tiếp với nông dân địa phương, đảm bảo nguồn gốc rõ ràng 
                    và giá trị công bằng cho người trồng cà phê. Điều này không chỉ đảm bảo chất lượng 
                    mà còn góp phần phát triển bền vững cho cộng đồng.
                </p>
            </div>
        </div>
    </section>

    <!-- VALUES SECTION -->
    <section class="story-section" style="background: var(--off-white);">
        <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 20px;">GIÁ TRỊ CỐT LÕI</h2>
        <p style="text-align: center; max-width: 800px; margin: 0 auto 50px; color: var(--text-light);">
            Những giá trị mà chúng tôi luôn hướng đến và thực hiện trong mọi hoạt động kinh doanh
        </p>
        
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">☕</div>
                <h3>CHẤT LƯỢNG</h3>
                <p>Cam kết cung cấp sản phẩm và dịch vụ chất lượng cao nhất, không bao giờ thỏa hiệp</p>
            </div>

            <div class="value-card">
                <div class="value-icon">💚</div>
                <h3>TẬN TÂM</h3>
                <p>Phục vụ khách hàng bằng cả trái tim, luôn lắng nghe và thấu hiểu nhu cầu</p>
            </div>

            <div class="value-card">
                <div class="value-icon">🌱</div>
                <h3>BỀN VỮNG</h3>
                <p>Cam kết bảo vệ môi trường và phát triển bền vững cùng cộng đồng</p>
            </div>

            <div class="value-card">
                <div class="value-icon">✨</div>
                <h3>SÁNG TẠO</h3>
                <p>Không ngừng đổi mới, sáng tạo để mang đến trải nghiệm tốt nhất</p>
            </div>

            <div class="value-card">
                <div class="value-icon">🤝</div>
                <h3>TRUNG THỰC</h3>
                <p>Minh bạch trong mọi hoạt động, xây dựng niềm tin với khách hàng</p>
            </div>

            <div class="value-card">
                <div class="value-icon">🎯</div>
                <h3>TẬN TÂM</h3>
                <p>Luôn đặt khách hàng làm trung tâm trong mọi quyết định kinh doanh</p>
            </div>
        </div>
    </section>

    <!-- TIMELINE SECTION -->
    <section class="timeline">
        <h2>HÀNH TRÌNH PHÁT TRIỂN</h2>
        
        <div class="timeline-container">
            <div class="timeline-item">
                <div class="timeline-content">
                    <h3>Ý tưởng ra đời</h3>
                    <p>Những người sáng lập bắt đầu với ý tưởng tạo ra một không gian cà phê khác biệt</p>
                </div>
                <div class="timeline-dot">2015</div>
                <div></div>
            </div>

            <div class="timeline-item">
                <div></div>
                <div class="timeline-dot">2016</div>
                <div class="timeline-content">
                    <h3>Cửa hàng đầu tiên</h3>
                    <p>Khai trương cửa hàng đầu tiên tại trung tâm TP.HCM với 10 bàn ghế</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-content">
                    <h3>Mở rộng quy mô</h3>
                    <p>Mở thêm 5 chi nhánh mới và đạt 10,000 khách hàng thường xuyên</p>
                </div>
                <div class="timeline-dot">2018</div>
                <div></div>
            </div>

            <div class="timeline-item">
                <div></div>
                <div class="timeline-dot">2020</div>
                <div class="timeline-content">
                    <h3>Chuyển đổi số</h3>
                    <p>Ra mắt ứng dụng đặt hàng online và hệ thống giao hàng tận nơi</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-content">
                    <h3>Hôm nay</h3>
                    <p>20+ cửa hàng trên toàn quốc, phục vụ hơn 50,000 khách hàng mỗi tháng</p>
                </div>
                <div class="timeline-dot">2024</div>
                <div></div>
            </div>
        </div>
    </section>

    <!-- TEAM SECTION -->
    <section class="team-section">
        <h2>ĐỘI NGŨ CỦA CHÚNG TÔI</h2>
        
        <div class="team-grid">
            <div class="team-member">
                <div class="team-photo">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300" alt="Nguyễn Văn A">
                </div>
                <h3>NGUYỄN VĂN A</h3>
                <p class="role">CEO & Founder</p>
                <p>Người sáng lập với 15 năm kinh nghiệm trong ngành F&B</p>
            </div>

            <div class="team-member">
                <div class="team-photo">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=300" alt="Trần Thị B">
                </div>
                <h3>TRẦN THỊ B</h3>
                <p class="role">Head Barista</p>
                <p>Chuyên gia pha chế với nhiều giải thưởng quốc tế</p>
            </div>

            <div class="team-member">
                <div class="team-photo">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=300" alt="Lê Văn C">
                </div>
                <h3>LÊ VĂN C</h3>
                <p class="role">Operations Manager</p>
                <p>Đảm bảo vận hành mượt mà tại tất cả các chi nhánh</p>
            </div>

            <div class="team-member">
                <div class="team-photo">
                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=300" alt="Phạm Thị D">
                </div>
                <h3>PHẠM THỊ D</h3>
                <p class="role">Marketing Director</p>
                <p>Xây dựng thương hiệu và kết nối với khách hàng</p>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: 80px 5%; text-align: center; color: var(--text-dark);">
        <h2 style="color: var(--text-dark); margin-bottom: 20px;">HÃY ĐẾN VÀ TRẢI NGHIỆM</h2>
        <p style="font-size: 1.2rem; margin-bottom: 30px; color: var(--text-dark);">
            Ghé thăm Coffee House để cảm nhận không khí thân thiện và thưởng thức cà phê tuyệt vời
        </p>
        <a href="index.php#location" class="btn" style="background: var(--text-dark); color: var(--white);">Tìm cửa hàng gần nhất</a>
        <a href="index.php#menu" class="btn btn-secondary" style="margin-left: 15px;">Xem thực đơn</a>
    </section>

    <?php include __DIR__ . '/footer.php'; ?>

    <!-- JAVASCRIPT -->
    <script src="../../../Public/Js/user-main.js"></script>
</body>
</html>
