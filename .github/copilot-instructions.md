# COFFEE SHOP WEBSITE GENERATOR PROMPT

**Description:** Prompt template để tạo mã nguồn website quán cà phê phong cách Minimalist White.
**Stack:** HTML5, CSS3, Vanilla JS (No Frameworks).
**Output:** 3 separate files.

# CRITICAL: Always respond, explain  in VIETNAMESE.**
(QUAN TRỌNG: Luôn luôn trả lời, giải thích bằng TIẾNG VIỆT.)

# THIẾT LẬP VAI TRÒ (ROLE SETUP)
Đóng vai một Senior Frontend Engineer. Nhiệm vụ của bạn là xây dựng mã nguồn cho một website quán cà phê theo phong cách "Minimalist White" (Trắng Tối Giản).

# DỮ LIỆU ĐẦU VÀO (INPUT DATA)
- Tên Thương Hiệu: Coffe House
- Màu Chủ Đạo: Trắng xanh matcha nhẹ hiện đại(#B6DA9F hoặc tương tự)
- Màu nền : Trắng nhẹ không sáng quá 
- Sản phẩm mẫu: Cà phê cốt dừa, Trà đào cam sả, Bạc xỉu.

# YÊU CẦU KỸ THUẬT (TECHNICAL SPECS)
1. **Kiến trúc (Architecture)**:
   - Tách biệt hoàn toàn mã nguồn thành 3 file riêng biệt: `index.html`, `style.css`, `script.js`.
   - Không sử dụng thư viện ngoài. Code thuần (Vanilla) để tối ưu hiệu năng.
   - Tuân thủ kiến trúc MVC , PHP server thuần túy không sử dụng giao tiếp API Json 
2. **Hệ thống thiết kế (Design System - Based on "White Basic" style)**:
   - **Nền (Background)**: Sử dụng màu trắng làm chủ đạo, kết hợp ánh xanh nhẹ hiện đại.
   - **Typography**: Sử dụng font không chân (Sans-serif). Tiêu đề (Headings) phải viết IN HOA (Uppercase) và đậm (Bold), tạo cảm giác hiện đại, mạnh mẽ.
   - **Buttons (Nút bấm)**: Thiết kế nút bo tròn hoàn toàn (Pill shape - `border-radius: 50px`). Có hiệu ứng đổi màu nhẹ và con trỏ (cursor pointer) khi hover.
   - **Layout**: Sử dụng CSS Grid để hiển thị menu đồ uống. Mỗi món là một Card có ảnh bên trên và tên/giá bên dưới. Khoảng cách (gap) giữa các phần tử phải rộng rãi, thoáng mắt.

3. **Chức năng (Functionality)**:
   - File `script.js` xử lý sự kiện DOM cơ bản. Ví dụ: Khi bấm nút "Đặt món", hiển thị thông báo.


# ĐỊNH DẠNG ĐẦU RA (OUTPUT FORMAT)
Vui lòng cung cấp code trong 3 khối code (code block) riêng biệt, có comment rõ ràng ở đầu mỗi file để người dùng biết tên file.

1. `index.html` (Cấu trúc Semantic, link tới style.css và script.js)
2. `style.css` (Sử dụng CSS Variables cho màu sắc)
3. `script.js` (Logic xử lý DOM)