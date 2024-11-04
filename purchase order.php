<!-- purchase order.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order</title>
    <link rel="stylesheet" href="css/purchaseorder.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="content">
        <h1 class="table-title">Purchase Order Form</h1>
        <table>
            <thead>
                <tr>
                    <th>PO No.</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total Price(PHP)</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>IMN0001</td>
                    <td>12/31/2024</td>
                    <td>Submitted</td>
                    <td>140,000.00</td>
                    <td><a href=/ALIMS/dashboard.php>click here</a></td>
                </tr>
                <tr>
                    <td>MIC0001</td>
                    <td>10/15/2024</td>
                    <td>Delivered</td>
                    <td>140,000.00</td>
                    <td><a href=/ALIMS/dashboard.php>click here</a></td>
                </tr>
            </tbody>
        </table>

    </div>

    <?php include 'footer.php'; ?>
</body>
</html>