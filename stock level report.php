<!-- stock level report.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Level Report</title>
    <link rel="stylesheet" href="css/stocklevelreport.css">  
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="content">
        <h1 class="table-title">Stock Level Report</h1>
        <table>
            <thead>
                <tr>
                    <th>Item No.</th>
                    <th>Item Description</th>
                    <th>Stock on Hand</th>
                    <th>Minimum Stock Level</th>
                    <th>Maximum Stock Level</th>
                    <th>Status</th>
                    <th>Action Required</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>001</td>
                    <td>Microscope, Model XYZ</td>
                    <td>5</td>
                    <td>2</td>
                    <td>10</td>
                    <td>Below Reorder Level</td>
                    <td>Reorder Soon</td>
                </tr>
                <tr>
                    <td>002</td>
                    <td>Glass Beakers, 1000 mL</td>
                    <td>50</td>
                    <td>5</td>
                    <td>100</td>
                    <td>Store in cool, dry place</td>
                    <td>Reorder Soon</td>
                </tr>
            </tbody>
        </table>

        <div class="button-container">
            <button class="generate-btn">Generate</button>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
