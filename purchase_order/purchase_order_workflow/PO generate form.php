<?php
ob_start();

require('../../fpdf.php');

// Get all PO data
require '../../db_connection.php';

session_start();

$PO_no = $session_user_id = "";

// Connect to the user_management database
$conn = connectToDatabase('form_data');
$conn2 = connectToDatabase('user_management');

$PO_no = $_GET['PO_no'];

$session_user_id = $_SESSION['user_id'];

// Get specific lab_id
$stmt = $conn->prepare("SELECT lab_id FROM purchase_order WHERE PO_no = ?");
$stmt->bind_param("s", $PO_no);
$stmt->execute();
$lab_id_result = $stmt->get_result();

$lab_id = 0;
if ($row = $lab_id_result->fetch_assoc()) {
    $lab_id = $row['lab_id'];
}

$stmt = $conn->prepare("SELECT lab_name FROM lab_connection WHERE lab_id = ?");
$stmt->bind_param("s", $lab_id);
$stmt->execute();
$lab_name_result = $stmt->get_result();

$lab_name = "";
if ($row = $lab_name_result->fetch_assoc()) {
    $lab_name = $row['lab_name'];
}

// Get supplier details
$sql = "
    SELECT p.PO_no, s.*
    FROM purchase_order p
    JOIN suppliers s ON p.supplier_id = s.supplier_id
    WHERE p.PO_no = ?;
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $PO_no);
$stmt->execute();
$suppliers_result = $stmt->get_result();
$suppliers_row = $suppliers_result->fetch_assoc();

// Get lab details
$sql = "
    SELECT p.PO_no, l.*
    FROM purchase_order p
    JOIN laboratories l ON p.lab_id = l.lab_id
    WHERE p.PO_no = ?;
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $PO_no);
$stmt->execute();
$lab_result = $stmt->get_result();
$lab_row = $lab_result->fetch_assoc();

// Get user details
$user_id = null;
$stmt = $conn->prepare("SELECT user_id FROM purchase_order WHERE PO_no = ?");
$stmt->bind_param("s", $PO_no);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();

$stmt = $conn2->prepare("SELECT first_name, last_name, middle_initial FROM users WHERE id = ?");
$stmt->bind_param("s", $user_id); // "s" because item_id is a string
$stmt->execute();
$users_result = $stmt->get_result();
$users_row = $users_result->fetch_assoc();

// Get tax value
$stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_name = 'tax'");
$stmt->execute();
$tax_result = $stmt->get_result();

$tax_rate = $tax_rate_percent = 0;
if ($row = $tax_result->fetch_assoc()) {
    $tax_rate = (float)$row['setting_value'];
}
$tax_rate_percent = 100 * $tax_rate;

// Get PO details
$stmt = $conn->prepare("SELECT * FROM purchase_order WHERE PO_no = ?");
$stmt->bind_param("s", $PO_no);
$stmt->execute();
$PO_result = $stmt->get_result();
$PO_row = $PO_result->fetch_assoc();

// Get PO items details
$sql = "
    SELECT poi.item_id, poi.quantity, poi.unit_price, poi.total_price,
           i.item_id, i.item_name, i.item_desc, i.unit_of_measure
    FROM purchase_order_items poi
    JOIN items i ON poi.item_id = i.item_id
    WHERE poi.PO_no = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $PO_no);
$stmt->execute();
$PO_result_items = $stmt->get_result();


$stmt->close();
$conn->close();
$conn2->close();



// PDF maker
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Purchase Order Form', 0, 1, 'C');
        $this->Ln(10);
    }

    function Table($header, $data, $colWidths, $subtotal, $tax, $tax_rate_percent, $shipping_cost, $grand_total)
    {
        $tableWidth = array_sum($colWidths);
        $startX = ($this->GetPageWidth() - $tableWidth) / 2;

        // Header
        $this->SetFont('Arial', 'B', 10);
        $this->SetX($startX);
        foreach ($header as $i => $col) {
            $this->Cell($colWidths[$i], 10, $col, 1, 0, 'C');
        }
        $this->Ln();

        // Data rows
        $this->SetFont('Arial', '', 10);
        foreach ($data as $row) {
            $this->SetX($startX);
            $cellHeight = 8;
            $colCount = count($colWidths);

            // Step 1: Compute max number of lines needed across all columns
            $maxLines = 1;
            for ($i = 0; $i < $colCount; $i++) {
                $lines = $this->NbLines($colWidths[$i], $row[$i]);
                if ($lines > $maxLines) $maxLines = $lines;
            }
            $rowHeight = $maxLines * $cellHeight;

            // Step 2: Current cursor Y position
            $y = $this->GetY();

            // Step 3: Draw each cell with a fixed height
            for ($i = 0; $i < $colCount; $i++) {
                $x = $this->GetX();

                // Save current position
                $this->MultiCell($colWidths[$i], $cellHeight, $row[$i], 1, 'L');

                // Calculate how tall this cell was
                $nb = $this->NbLines($colWidths[$i], $row[$i]);
                $actualHeight = $nb * $cellHeight;

                // If shorter than full row, fill the gap with empty border
                if ($actualHeight < $rowHeight) {
                    $this->SetXY($x, $y + $actualHeight);
                    $this->MultiCell($colWidths[$i], $rowHeight - $actualHeight, '', 1);
                }

                // Move to next column start
                $this->SetXY($x + $colWidths[$i], $y);
            }

            // Step 4: Move to next row baseline
            $this->SetY($y + $rowHeight);
        }

        // // Add 5 empty rows
        // $emptyRows = 5;
        // for ($i = 0; $i < $emptyRows; $i++) {
        //     $this->SetX($startX);
        //     for ($j = 0; $j < count($colWidths); $j++) {
        //         $this->Cell($colWidths[$j], 8, '', 1);
        //     }
        //     $this->Ln();
        // }

        // Add 1 empty row
        $this->SetX($startX);
        for ($j = 0; $j < count($colWidths); $j++) {
            $this->Cell($colWidths[$j], 8, '', 1);
        }
        $this->Ln();


        // Bottom 4 rows: Subtotal, Tax, Shipping, Grand Total
        $summaryLabels = ['Subtotal:', 'Tax (' . $tax_rate_percent . '%):', 'Shipping Cost:', 'Grand Total:'];
        $summaryValues = [$subtotal, $tax, $shipping_cost, $grand_total];
        // $summaryLabels = ['Subtotal', 'Tax', 'Shipping Cost', 'Grand Total'];
        // $summaryValues = ['0', '0', '0', '0'];
        $this->SetFont('Arial', 'B', 10);
        for ($i = 0; $i < 4; $i++) {
            $this->SetX($startX);
            $totalCols = count($colWidths);
            $totalWidth = 0;

            // Skip to column 5
            for ($j = 0; $j < $totalCols; $j++) {
                if ($j == 4) {
                    $this->Cell($colWidths[$j], 8, $summaryLabels[$i], 1, 0, 'R');
                } elseif ($j == 5) {
                    $this->SetFont('Arial', '', 10);
                    $this->Cell($colWidths[$j], 8, $summaryValues[$i], 1, 0, 'L');
                    $this->SetFont('Arial', 'B', 10);
                } else {
                    $this->Cell($colWidths[$j], 8, '', 1);
                }
            }
            $this->Ln();
        }

        $this->Ln(10);
    }

    function BottomInfo($info)
    {
        $this->SetFont('Arial', '', 10);
        foreach ($info as $label => $value) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(50, 6, $label . ':', 0, 0);
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 6, $value, 0, 1);
        }
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c] ?? 0;
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }


}

// Sample data
$header = ['Item', 'Description', 'Qty', 'Unit', 'Price (PHP)', 'Total'];
$colWidths = [15, 75, 15, 25, 30, 30];

$data = [];

while ($row = $PO_result_items->fetch_assoc()) {
    $item_id = $row['item_id'];
    $description = $row['item_name'] . ', ' . $row['item_desc'];
    $quantity = $row['quantity'];
    $unit = $row['unit_of_measure']; // or fetch this if it's in DB
    $unit_price = number_format($row['unit_price'], 2);
    $total_price = number_format($row['total_price'], 2);

    $data[] = [$item_id, $description, $quantity, $unit, $unit_price, $total_price];
}


$bottomInfo1 = [
    'Purchase Order No' => $PO_row['PO_no'],
    'Date' => $PO_row['date_created'],
    'Status' => $PO_row['status'],
];

$bottomInfo2 = [
    'Supplier Name' => $suppliers_row['supplier_name'],
    'Supplier Address' => $suppliers_row['supplier_address'],
    'Phone Number' => $suppliers_row['supplier_phone_no'],
    'Email' => $suppliers_row['supplier_email'],
    'Contact Person' => $suppliers_row['supplier_contact_person'],
];

$bottomInfo3 = [
    'Laboratory Name' => $lab_row['lab_name'],
    'Laboratory Address' => $lab_row['lab_address'],
    'Phone Number (Lab)' => $lab_row['lab_phone_no'],
    'Email (Lab)' => $lab_row['lab_email'],
    'Contact Person (Lab)' => $lab_row['lab_contact_person']
];


// Create PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->Table($header, $data, $colWidths, $PO_row['subtotal'], $PO_row['tax'], $tax_rate_percent, $PO_row['shipping_cost'], $PO_row['grand_total']);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, '--- Laboratory Purchase Order ---', 0, 1); // Your custom text
$pdf->BottomInfo($bottomInfo1);

$pdf->Ln(3); // Small space before text
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, '--- Supplier Information ---', 0, 1); // Your custom text
$pdf->BottomInfo($bottomInfo2);

$pdf->Ln(3);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, '--- Laboratory Information ---', 0, 1);
$pdf->BottomInfo($bottomInfo3);


$stampPath = "";
if ($PO_row['final_status'] == 'Approved') {
    $stampPath = '../../approved.png'; // or rejected.png, etc.
} else {
    $stampPath = '../../rejected.png'; // or rejected.png, etc.
}

$stampWidth = 40; // in mm
$stampHeight = 20;

// Get bottom-right coordinates
$pageWidth = $pdf->GetPageWidth();
$pageHeight = $pdf->GetPageHeight();

// Margin offset (optional)
$margin = 15;

// Bottom-right position
$x = $pageWidth - $stampWidth - $margin;
$y = $pageHeight - $stampHeight - $margin;

// Place the image
$pdf->Image($stampPath, $x, $y, $stampWidth, $stampHeight);

$file_name = "purchase_order" . $PO_no . ".pdf";

// $pdf->Output('purchase_order.pdf', 'I'); // open in browser tab
$pdf->Output($file_name, 'D'); // Open in browser tab
exit;
?>
