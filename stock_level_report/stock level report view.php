<!-- edit_min_max_stock.php --> 
<?php
// Start the session and include the database connection
require '../header.php';
require '../db_connection.php';

// Connect to the user_management database
$conn = connectToDatabase('form_data');

// Fetch user ID from GET or POST request
$item_id = isset($_GET['item_id']) ? $_GET['item_id'] : (isset($_POST['item_id']) ? $_POST['item_id'] : '');

$user_id = $_SESSION['user_id'];
// Connect to the form database
$conn = connectToDatabase('form_data');

echo "<script>console.log(" . json_encode($item_id) . ");</script>";

// Get all items
$stmt = $conn->prepare("SELECT *, (pathology_stock + immunology_stock + microbiology_stock) AS total_stock FROM items WHERE item_id = ?");
$stmt->bind_param("s", $item_id); // "s" because item_id is a string
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();
$supplier_id = $row['supplier_id'];

$supplier_stmt = $conn->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
$supplier_stmt->bind_param("s", $supplier_id); 
$supplier_stmt->execute();
$supplier_result = $supplier_stmt->get_result();

$supplier_row = $supplier_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Item</title>
    <link rel="stylesheet" type="text/css" href="../css/main.css?version=<?php echo time() - 1000000000; ?>">
    <link rel="stylesheet" type="text/css" href="../css/accounts.css?version=<?php echo time() - 1000000000; ?>">
    <link rel="stylesheet" type="text/css" href="../css/stocklevelreport.css?version=<?php echo time() - 1000000000; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script defer src="../js/user_validation.js"></script>
</head>
<body>
    <?php include '../header.php'; ?> 
    

    <div class="content">
        <h1 class="table-title">Stock Level Report: <?php echo htmlspecialchars($row['item_id']); ?> - <?php echo htmlspecialchars($row['item_name']); ?></h1>         
        <div class="add-item">
            <button onclick="window.location.href='stock level report.php'">Go back ←</button>
        </div> 
        <table>
            <thead>
                <tr>
                    <th>Item No.</th>
                    <th>Item Name</th>
                    <th>Item Description</th>
                    <th>Item Type</th>
                    <th>Unit of Measure</th>
                    <th>Price Per Unit</th>
                    <th>Weight Per Unit (kg)</th>
                    <th>Expiry Date</th>
                    <th>Min. Stock</th>
                    <th>Max. Stock</th>
                    <th>Date Added</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['item_desc']); ?></td>
                    <td><?php echo htmlspecialchars($row['item_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['unit_of_measure']); ?></td>
                    <td><?php echo htmlspecialchars($row['price_per_unit']); ?></td>
                    <td><?php echo htmlspecialchars($row['weight_per_unit']); ?></td>
                    <td>
                      <?php
                        if ($row['expiry_date'] == null) {
                          echo('N.A.');
                        } else {
                          $date = new DateTime($row['expiry_date']);
                          echo $date->format('m/d/y'); 
                        }
                      ?>
                    </td>
                    <td>
                        <?php if ($user_id == 1): ?>
                        <div class="stock-wrapper">
                            <span>Click here →</span>
                            <button class="toggle-details" onclick="toggleDetails(this)">&#x25B6;</button>
                            <div class="details-flyout" style="display: none;">
                                <h4><u>Min. Stock Details</u></h4>
                                <p><strong>Pathology:</strong> <?php echo htmlspecialchars($row['pathology_min_stock']); ?></p>
                                <p><strong>Immunology:</strong> <?php echo htmlspecialchars($row['immunology_min_stock']); ?></p>
                                <p><strong>Microbiology:</strong> <?php echo htmlspecialchars($row['microbiology_min_stock']); ?></p>
                            </div>
                        </div>
                        <?php 
                            elseif ($user_id == 2):
                                echo htmlspecialchars($row['pathology_min_stock']);
                            elseif ($user_id == 3):
                                echo htmlspecialchars($row['immunology_min_stock']);
                            else:
                                echo htmlspecialchars($row['microbiology_min_stock']);
                            endif; 
                        ?>
                    </td>
                    <td>
                      <?php if ($user_id == 1): ?>
                      <div class="stock-wrapper">
                          <span>Click here →</span>
                          <button class="toggle-details" onclick="toggleDetails(this)">&#x25B6;</button>
                          <div class="details-flyout" style="display: none;">
                              <h4><u>Max. Stock Details</u></h4>
                              <p><strong>Pathology:</strong> <?php echo htmlspecialchars($row['pathology_max_stock']); ?></p>
                              <p><strong>Immunology:</strong> <?php echo htmlspecialchars($row['immunology_max_stock']); ?></p>
                              <p><strong>Microbiology:</strong> <?php echo htmlspecialchars($row['microbiology_max_stock']); ?></p>
                          </div>
                      </div>
                      <?php 
                            elseif ($user_id == 2):
                                echo htmlspecialchars($row['pathology_max_stock']);
                            elseif ($user_id == 3):
                                echo htmlspecialchars($row['immunology_max_stock']);
                            else:
                                echo htmlspecialchars($row['microbiology_max_stock']);
                            endif; 
                        ?>
                    </td>
                    <td>
                      <?php $date = new DateTime($row['date_created']);
                          echo $date->format('m/d/y g:i A');
                      ?>
                    </td>
                    <td>
                      <?php $date = new DateTime($row['date_updated_latest']);
                          echo $date->format('m/d/y g:i A');
                      ?>
                    </td>
                </tr>

            </tbody>
        </table>

        <br>

        <div class="info-columns-view-stock">
            <div class="column-view-stock">
                <h2 class="table-title">Supplier Details</h2>
                <p><strong>ID: </strong><?php echo htmlspecialchars($supplier_row['supplier_id']); ?></p>
                <p><strong>Name: </strong><?php echo htmlspecialchars($supplier_row['supplier_name']); ?></p>
                <p><strong>Address: </strong><?php echo htmlspecialchars($supplier_row['supplier_address']); ?></p>
                <p><strong>Phone Number: </strong><?php echo htmlspecialchars($supplier_row['supplier_phone_no']); ?></p>
                <p><strong>Email: </strong><?php echo htmlspecialchars($supplier_row['supplier_email']); ?></p>
                <p><strong>Contact Person: </strong><?php echo htmlspecialchars($supplier_row['supplier_contact_person']); ?></p>
                <!-- <p><strong>Shipping Cost: </strong><?php echo htmlspecialchars($supplier_row['shipping_costs_by_dist']); ?></p>
                <p><strong>Weight Cost (per kg): </strong><?php echo htmlspecialchars($supplier_row['shipping_weight_cost_per_kg']); ?></p> -->
            </div>

            <?php if ($user_id == 1): ?>
                <div class="column-view-stock">
                      <div class="stock-line">
                          <h2 class="table-title">Stock Count</h2>
                          <p><strong>Pathology: </strong><?php echo htmlspecialchars($row['pathology_stock']); ?></p>
                          <p><strong>Immunology: </strong><?php echo htmlspecialchars($row['immunology_stock']); ?></p>
                          <p><strong>Microbiology: </strong><?php echo htmlspecialchars($row['microbiology_stock']); ?></p>
                          <hr>
                          <p><strong>Total: </strong><?php echo htmlspecialchars($row['total_stock']); ?></p>
                      </div>
                </div>
            <?php endif ?>
        </div>


    </div>
    
    <?php include '../footer.php'; ?>

    <script>
    let openFlyout = null;

    function toggleDetails(button) {
        const wrapper = button.closest('.stock-wrapper');
        const flyout = wrapper.querySelector('.details-flyout');

        // Hide other flyouts
        document.querySelectorAll('.details-flyout').forEach(d => {
            if (d !== flyout) d.style.display = 'none';
        });

        // Toggle current flyout
        const isVisible = flyout.style.display === 'block';
        flyout.style.display = isVisible ? 'none' : 'block';
        openFlyout = isVisible ? null : flyout;

        // Match parent bg color
        const bgColor = getComputedStyle(button.closest('tr')).backgroundColor;
        flyout.style.backgroundColor = bgColor;
    }

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.stock-wrapper')) {
            document.querySelectorAll('.details-flyout').forEach(f => f.style.display = 'none');
            openFlyout = null;
        }
    });
    </script>

</body>
</html>