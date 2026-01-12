<link rel="stylesheet" href="/COFFEE_PHP/Public/Css/voucher-list.css">

<?php if (empty($vouchers)): ?>
  <div class="empty-state">
    <div style="font-size: 48px; margin-bottom: 12px;"></div>
    <div>Không có voucher phù hợp</div>
  </div>
<?php else: ?>
  <?php foreach ($vouchers as $v): ?>
    <div class="voucher-card"
         data-id="<?= (int)$v->id ?>"
         data-name="<?= htmlspecialchars($v->name, ENT_QUOTES, 'UTF-8') ?>"
         data-point-cost="<?= (int)$v->point_cost ?>"
         data-discount-type="<?= htmlspecialchars($v->discount_type, ENT_QUOTES, 'UTF-8') ?>"
         data-discount-value="<?= (float)$v->discount_value ?>">
      
      <div class="voucher-header">
        <div class="voucher-name"><?= htmlspecialchars($v->name, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="voucher-badge">
          <?php if (strtoupper($v->discount_type) === 'PERCENT'): ?>
            <?php if (!empty($v->max_discount_value)): ?>
              -<?= number_format((float)$v->max_discount_value, 0, ',', '.') ?>₫
            <?php else: ?>
              -<?= (float)$v->discount_value ?>%
            <?php endif; ?>
          <?php else: ?>
            -<?= number_format((float)$v->discount_value, 0, ',', '.') ?>₫
          <?php endif; ?>
        </div>
      </div>
      
      <div class="voucher-details">
        <div class="voucher-detail-item">
          <span class="voucher-detail-icon"></span>
          <span><?= (int)$v->point_cost ?> điểm</span>
        </div>
        <?php if ($v->min_bill_total > 0): ?>
        <div class="voucher-detail-item">
          <span class="voucher-detail-icon"></span>
          <span>Đơn tối thiểu: <?= number_format((float)$v->min_bill_total, 0, ',', '.') ?>₫</span>
        </div>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
