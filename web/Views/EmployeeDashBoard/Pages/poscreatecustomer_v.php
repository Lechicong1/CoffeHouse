<!-- Modal Tìm / Tạo Khách Hàng -->
<div id="posCustomerModal" style="display:none; position:fixed; z-index:1200; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.4); align-items:center; justify-content:center;">
    <div style="background:#fff; padding:20px; border-radius:12px; min-width:560px; max-width:760px; box-shadow:0 20px 60px rgba(0,0,0,0.3); max-height:90vh; overflow-y:auto;">

        <!-- Header -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <strong style="font-size:18px; color:#333;">Tìm / Tạo Khách Hàng</strong>
            <button id="closePosCustomerBtn" type="button" style="background:none; border:none; font-size:26px; cursor:pointer; color:#999;">&times;</button>
        </div>

        <!-- Tab Buttons -->
        <div style="display:flex; gap:8px; margin-bottom:16px; border-bottom:2px solid #f0f0f0;">
            <button id="tabFindBtn" class="tab-btn active" style="padding:8px 16px; border:none; background:none; cursor:pointer; border-bottom:3px solid #4caf50; font-weight:bold; color:#333;">
                Tìm Khách
            </button>
            <button id="tabCreateBtn" class="tab-btn" style="padding:8px 16px; border:none; background:none; cursor:pointer; border-bottom:3px solid transparent; color:#999;">
                Tạo Mới
            </button>
        </div>

        <!-- Tab Content: Find Customer -->
        <div id="tabFindContent" style="display:block;">
            <div style="display:flex; gap:8px; margin-bottom:12px; align-items:center;">
                <input id="posPhoneFind" type="text" placeholder="Nhập SĐT để tìm" style="flex:1; padding:8px; border:1px solid #ddd; border-radius:8px;" />
                <button id="posFindBtnModal" class="btn" style="padding:8px 16px; border-radius:8px; border:1px solid #064528; background:#064528; color:#fff; cursor:pointer;">
                    Tìm
                </button>
            </div>
            <div id="posCustomerList" style="max-height:300px; overflow:auto; border:1px solid #f0f0f0; padding:8px; border-radius:8px; margin-bottom:12px; background:#fafafa;">
                <p style="text-align:center; color:#999; padding:20px 0;">Nhập số điện thoại và bấm Tìm</p>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button id="posClearBtn" class="btn" style="padding:8px 16px; border-radius:8px; border:1px solid #ddd; background:#fff; cursor:pointer;">
                    Bỏ chọn
                </button>
                <button id="posApplyBtn" class="btn btn-success" style="padding:8px 16px; border-radius:8px; border:none; background:#4caf50; color:#fff; cursor:pointer;">
                    Chọn
                </button>
            </div>
        </div>

        <!-- Tab Content: Create Customer -->
        <div id="tabCreateContent" style="display:none;">
            <div style="margin-bottom:12px;">
                <label style="display:block; margin-bottom:4px; font-weight:500; color:#333;">
                    Số điện thoại <span style="color:red;">*</span>
                </label>
                <input id="posPhoneCreate" type="text" placeholder="Nhập số điện thoại" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px;" />
            </div>
            <div style="margin-bottom:12px;">
                <label style="display:block; margin-bottom:4px; font-weight:500; color:#333;">
                    Tên khách hàng
                </label>
                <input id="posFullnameCreate" type="text" placeholder="Khách lẻ" value="Khách lẻ" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px;" />
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; margin-bottom:4px; font-weight:500; color:#333;">
                    Email
                </label>
                <input id="posEmailCreate" type="email" placeholder="example@email.com" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px;" />
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button id="posCreateBtn" class="btn btn-success" style="padding:10px 24px; border-radius:8px; border:none; background:#4caf50; color:#fff; cursor:pointer; font-weight:500; font-size:14px;">
                    Tạo Khách Hàng
                </button>
            </div>
        </div>

        <!-- Message Area -->
        <div id="posCustomerMessage" style="min-height:18px; margin-top:12px; padding:8px; border-radius:6px; font-size:14px;"></div>
    </div>
</div>

