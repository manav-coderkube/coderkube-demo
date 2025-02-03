<?php
require 'db_connect.php';
require 'vendor/autoload.php'; // Include DomPDF

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_POST['order_id'])) {
    echo json_encode(["success" => false, "message" => "Order ID is required"]);
    exit;
}

$order_id = $_POST['order_id'];

// Fetch order details
$stmt = $conn->prepare("SELECT o.item_id, o.user_id, o.total_amount, o.order_date, 
                               i.item_name, i.item_image, i.item_price
                        FROM tbl_orders o 
                        JOIN tbl_items i ON o.item_id = i.item_id 
                        WHERE o.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Order not found"]);
    exit;
}

$order = $result->fetch_assoc();
$item_id = $order['item_id'];
$user_id = $order['user_id'];
$order_date = $order['order_date'];
$item_name = $order['item_name'];
$item_image = $order['item_image'];
$image_path = "http://".$_SERVER['HTTP_HOST']."/uploads/".$item_image;
$subtotal = $order['item_price'];
$sgst = ($subtotal * 9) / 100; // 9% SGST
$cgst = ($subtotal * 9) / 100; // 9% CGST
$discount = ($subtotal * 10) / 100; // 10% Discount
$delivery_charge = ($subtotal < 500) ? 100 : 50;
$total = $order['total_amount'];

$image_path = "uploads/".$item_image;
if (file_exists($image_path)) {
    $image_data = file_get_contents($image_path);
    $base64 = 'data:image/' . pathinfo($image_path, PATHINFO_EXTENSION) . ';base64,' . base64_encode($image_data);
} else {
    $base64 = ''; // Fallback in case image is missing
}

// Fetch bill details
$stmt = $conn->prepare("SELECT buy_id, purchase_date FROM tbl_buy WHERE item_id = ? AND user_id = ?");
$stmt->bind_param("ii", $item_id, $user_id);
$stmt->execute();
$bill_result = $stmt->get_result();

if ($bill_result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Bill not found"]);
    exit;
}

$bill = $bill_result->fetch_assoc();
$bill_id = $bill['buy_id'];
$purchase_date = $bill['purchase_date'];

// Generate HTML invoice with updated layout
$html = "
<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .invoice-container { max-width: 800px; margin: auto; border: 2px solid #ddd; padding: 20px; }
        h2 { text-align: center; }
        .company-name { text-align: center; }
        .details { margin-bottom: 20px; display: flex; justify-content: space-between; }
        .details .left { flex: 1; }
        .details .right { max-width: 150px; }
        .details p { margin: 5px 0; }
        .details img { max-width: 100%; display: block; margin: 10px auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        .total { font-size: 18px; font-weight: bold; text-align: right; }
    </style>
</head>
<body>
    <div class='invoice-container'>
        <h2>Order Invoice</h2>
        <p class='company-name'>OnStore</p>
        <p class='company-name'>Your Trusted Online Store</p>
        <div class='details'>
            <div class='left'>
                <p><strong>Bill ID:</strong> {$bill_id}</p>
                <p><strong>Order Date:</strong> {$order_date}</p>
                <p><strong>Purchase Date:</strong> {$purchase_date}</p>
                <p><strong>Item Name:</strong> {$item_name}</p>
            </div>
            <div class='right'>
                <img src='{$base64}' alt='Product Image'>
            </div>
        </div>
        <table>
            <tr><th>Subtotal</th><td>$" . number_format($subtotal, 2) . "</td></tr>
            <tr><th>SGST (9%)</th><td>$" . number_format($sgst, 2) . "</td></tr>
            <tr><th>CGST (9%)</th><td>$" . number_format($cgst, 2) . "</td></tr>
            <tr><th>Discount (10%)</th><td>-$" . number_format($discount, 2) . "</td></tr>
            <tr><th>Delivery Charge</th><td>$" . number_format($delivery_charge, 2) . "</td></tr>
            <tr><th>Total</th><td>$" . number_format($total, 2) . "</td></tr>
        </table>
    </div>
</body>
</html>
";

// Configure DomPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Send PDF as a response
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Invoice_' . $bill_id . '.pdf"');
echo $dompdf->output();
exit;
?>
