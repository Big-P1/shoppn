<div style="font-family:Arial,Helvetica,sans-serif;max-width:640px;margin:0 auto;color:#33232e;">
    <h2 style="color:#c73675;margin-bottom:4px;">Shoppn</h2>
    <p>Hi <?php echo htmlspecialchars($order['customer_name']); ?>, thank you for your order. Here is your receipt.</p>

    <p style="margin:0;"><strong>Invoice:</strong> INV-<?php echo (int) $order['invoice_no']; ?></p>
    <p style="margin:0;"><strong>Order number:</strong> #<?php echo (int) $order['order_id']; ?></p>
    <p style="margin:0 0 16px;"><strong>Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>

    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:#fce8f0;">
                <th style="text-align:left;padding:8px;">Product</th>
                <th style="text-align:center;padding:8px;">Qty</th>
                <th style="text-align:right;padding:8px;">Unit price</th>
                <th style="text-align:right;padding:8px;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item) { ?>
                <tr>
                    <td style="padding:8px;border-bottom:1px solid #f0dce5;"><?php echo htmlspecialchars($item['product_title']); ?></td>
                    <td style="padding:8px;border-bottom:1px solid #f0dce5;text-align:center;"><?php echo (int) $item['qty']; ?></td>
                    <td style="padding:8px;border-bottom:1px solid #f0dce5;text-align:right;">GHS <?php echo number_format((float) $item['product_price'], 2); ?></td>
                    <td style="padding:8px;border-bottom:1px solid #f0dce5;text-align:right;">GHS <?php echo number_format((float) $item['line_total'], 2); ?></td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="padding:10px 8px;text-align:right;font-weight:bold;">Total paid</td>
                <td style="padding:10px 8px;text-align:right;font-weight:bold;">GHS <?php echo number_format((float) $order['amt'], 2); ?></td>
            </tr>
        </tfoot>
    </table>

    <p style="color:#756571;font-size:13px;margin-top:20px;">Payment status: <?php echo htmlspecialchars(ucfirst($order['order_status'])); ?>. Keep this email as your receipt.</p>
</div>
