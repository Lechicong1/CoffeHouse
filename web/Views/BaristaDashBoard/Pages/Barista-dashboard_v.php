<div class="barista-container">
    <div class="barista-header">
        <h1>BARISTA STATION</h1>
        <div class="barista-info">
            <span>Barista: <?php echo isset($_SESSION['user']) ? $_SESSION['user']['fullname'] : 'Staff'; ?></span>
        </div>
    </div>

    <div class="kanban-board">
        <!-- Cột 1: NEW ORDERS -->
        <div class="kanban-column new-orders">
            <div class="column-header">
                <h2>NEW ORDERS</h2>
            </div>
            <div class="column-content">
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <?php if ($order->status === 'PENDING'): ?>
                            <div class="order-card">
                                <div class="card-header">
                                    <span class="order-id">#<?php echo $order->order_code; ?></span>
                                    <span class="order-time"><?php echo date('H:i', strtotime($order->created_at)); ?></span>
                                </div>
                                <div class="card-body">
                                    <ul class="item-list">
                                        <?php if (isset($order->items)): ?>
                                            <?php foreach ($order->items as $item): ?>
                                                <li>
                                                    <span class="qty"><?php echo $item['quantity']; ?>x</span>
                                                    <span class="name"><?php echo $item['product_name']; ?> (<?php echo $item['size_name']; ?>)</span>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                    <?php if ($order->note): ?>
                                        <div class="order-note">Note: <?php echo $order->note; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer">
                                    <form action="index.php?url=Barista/acceptOrder" method="POST">
                                        <input type="hidden" name="order_id" value="<?php echo $order->id; ?>">
                                        <button type="submit" class="btn btn-accept">Nhận đơn</button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cột 2: IN PROGRESS -->
        <div class="kanban-column in-progress">
            <div class="column-header">
                <h2>IN PROGRESS</h2>
            </div>
            <div class="column-content">
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <?php if ($order->status === 'PREPARING'): ?>
                            <div class="order-card">
                                <div class="card-header">
                                    <span class="order-id">#<?php echo $order->order_code; ?></span>
                                    <span class="order-time"><?php echo date('H:i', strtotime($order->created_at)); ?></span>
                                </div>
                                <div class="card-body">
                                    <ul class="item-list">
                                        <?php if (isset($order->items)): ?>
                                            <?php foreach ($order->items as $item): ?>
                                                <li>
                                                    <span class="qty"><?php echo $item['quantity']; ?>x</span>
                                                    <span class="name"><?php echo $item['product_name']; ?> (<?php echo $item['size_name']; ?>)</span>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                    <?php if ($order->note): ?>
                                        <div class="order-note">Note: <?php echo $order->note; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer">
                                    <form action="index.php?url=Barista/completeOrder" method="POST">
                                        <input type="hidden" name="order_id" value="<?php echo $order->id; ?>">
                                        <button type="submit" class="btn btn-complete">Hoàn thành</button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cột 3: COMPLETED -->
        <div class="kanban-column completed">
            <div class="column-header">
                <h2>COMPLETED</h2>
            </div>
            <div class="column-content">
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <?php if ($order->status === 'READY'): ?>
                            <div class="order-card">
                                <div class="card-header">
                                    <span class="order-id">#<?php echo $order->order_code; ?></span>
                                    <span class="order-time"><?php echo date('H:i', strtotime($order->created_at)); ?></span>
                                </div>
                                <div class="card-body">
                                    <ul class="item-list">
                                        <?php if (isset($order->items)): ?>
                                            <?php foreach ($order->items as $item): ?>
                                                <li>
                                                    <span class="qty"><?php echo $item['quantity']; ?>x</span>
                                                    <span class="name"><?php echo $item['product_name']; ?> (<?php echo $item['size_name']; ?>)</span>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <span class="status-done">Done</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
