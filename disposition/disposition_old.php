<!-- disposition.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disposition</title>
    <link rel="stylesheet" href="css/disposition.css"> 
</head>
<body>
    <?php include '../header.php'; ?>

    <main class="content">
        <div class="table-title">Disposition</div> 
        <table>
            <thead>
                <tr>
                    <th>Item No.</th>
                    <th>Item Description</th>
                    <th>Quantity</th>
                    <th>Reason for Disposition</th>
                    <th>Disposition Method</th>
                    <th>Date of Disposition</th>
                    <th>Dispositioned by</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>001</td>
                    <td>Ethanol, 95%, 500 mL</td>
                    <td>2 bottles</td>
                    <td>Expired</td>
                    <td>Hazardous Waste Disposal</td>
                    <td>09/01/2024</td>
                    <td>Name</td>
                    <td>Properly labeled, disposed via approved vendor</td>
                </tr>
                <tr>
                    <td>002</td>
                    <td>Centrifuge, Model ABC</td>
                    <td>1 unit</td>
                    <td>Malfunctioned, Unrepairable</td>
                    <td>Decommission and Recycle</td>
                    <td>08/30/2024</td>
                    <td>Name</td>
                    <td>Decommissioned, parts recycled</td>
                </tr>
                <tr>
                    <td>003</td>
                    <td>Safety Goggles, Anti-Fog</td>
                    <td>5 pairs</td>
                    <td>Damaged</td>
                    <td>General Waste Disposal</td>
                    <td>08/28/2024</td>
                    <td>Name</td>
                    <td>Disposed of according to lab protocol</td>
                </tr>
            </tbody>
        </table>
    </main>

    <?php include '../footer.php'; ?>

    <script src="script.js"></script> 
</body>
</html>
