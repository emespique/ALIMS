<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Lab Supplies Inventory Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .content {
            padding: 20px;
        }
        .table-title {
            font-size: 1.2em;
            margin-bottom: 10px;
            font-weight: bold;
            text-align: center;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 1em;
        }
        .header-container div {
            border: 1px solid #8A1538;
            padding: 10px;
            width: 32%;
            text-align: center;
            background-color: #F2F2F2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #8A1538;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #8A1538;
            color: white;
        }
        td {
            background-color: #F2F2F2;
        }
        .button-container {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        button {
            background-color: #8A1538;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #7A1230;
        }
        .hidden {
            display: none;
        }
        .editable-cell {
            background-color: #ffffff;
            border: 1px solid #8A1538;
            color: #333;
        }
        /* Add Row Button Styling */
        #addRowButton {
            background-color: #335628;
        }
        #addRowButton:hover {
            background-color: #2A4A1E;
        }
        .delete-icon {
            color: #8A1538;
            cursor: pointer;
            font-size: 1.2em;
            margin-left: 10px;
            vertical-align: middle;
        }
        .delete-icon:hover {
            color: #7A1230;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="content">
        <div class="table-title">General Lab Supplies Inventory Form</div>

        <!-- Static Header Section -->
        <div class="header-container">
            <div id="dateField">Date: </div>
            <div>Lab Name: MRL - Molecular</div>
            <div>Personnel: EMM</div>
        </div>

        <!-- Inventory Table -->
        <table id="inventoryTable">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Item Code</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Location</th>
                    <th>Supplier</th>
                    <th>Cost</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td contenteditable="false">Glass beakers</td>
                    <td contenteditable="false">SU001</td>
                    <td contenteditable="false">500</td>
                    <td contenteditable="false">pairs</td>
                    <td contenteditable="false">Supply Cabinet 1</td>
                    <td contenteditable="false">SafetyLab Inc.</td>
                    <td contenteditable="false">PHP 7,000.00</td>
                    <td contenteditable="false">Latex, size M <span class="delete-icon hidden" onclick="deleteRow(this)">🗑️</span></td>
                </tr>
                <tr>
                    <td contenteditable="false">Pipette Tips</td>
                    <td contenteditable="false">SU002</td>
                    <td contenteditable="false">1000</td>
                    <td contenteditable="false">tips</td>
                    <td contenteditable="false">Shelf C</td>
                    <td contenteditable="false">LabEquip Solutions</td>
                    <td contenteditable="false">PHP 9,876.00</td>
                    <td contenteditable="false">Sterile, 10μL capacity <span class="delete-icon hidden" onclick="deleteRow(this)">🗑️</span></td>
                </tr>
            </tbody>
        </table>

        <!-- Buttons -->
        <div class="button-container">
            <button id="editButton" onclick="enterEditMode()">Edit</button>
            <button id="addRowButton" class="hidden" onclick="addRow()">+ Add Row</button>
            <button id="saveButton" class="hidden" onclick="saveChanges()">Save</button>
            <button id="cancelButton" class="hidden" onclick="cancelEdit()">Cancel</button>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        // Set the current date in the dateField
        window.onload = function() {
            const dateField = document.getElementById('dateField');
            const today = new Date();
            const formattedDate = today.toLocaleDateString("en-US", {
                year: 'numeric', month: 'long', day: 'numeric'
            });
            dateField.textContent = `Date: ${formattedDate}`;
        };

        let originalTableData = [];
        let newRows = [];

        function enterEditMode() {
            // Store the initial table data if not already stored
            if (originalTableData.length === 0) {
                originalTableData = Array.from(document.querySelectorAll('#inventoryTable tbody tr'))
                    .map(row => Array.from(row.cells).map(cell => cell.innerHTML));
            }

            const cells = document.querySelectorAll('#inventoryTable td');
            cells.forEach(cell => {
                cell.contentEditable = "true";
                cell.classList.add('editable-cell');
            });

            document.querySelectorAll('.delete-icon').forEach(icon => {
                icon.classList.remove('hidden');
                icon.contentEditable = "false";
            });

            document.getElementById('editButton').classList.add('hidden');
            document.getElementById('addRowButton').classList.remove('hidden');
            document.getElementById('saveButton').classList.remove('hidden');
            document.getElementById('cancelButton').classList.remove('hidden');
        }

        function addRow() {
            const tableBody = document.querySelector('#inventoryTable tbody');
            const newRow = document.createElement('tr');
            const columns = 7; // Number of non-Notes columns

            for (let i = 0; i < columns; i++) {
                const newCell = document.createElement('td');
                newCell.contentEditable = "true";
                newCell.classList.add('editable-cell');
                newRow.appendChild(newCell);
            }

            // Add the Notes cell with delete icon
            const notesCell = document.createElement('td');
            notesCell.contentEditable = "true";
            notesCell.classList.add('editable-cell');
            notesCell.innerHTML = '<span class="delete-icon hidden" onclick="deleteRow(this)">🗑️</span>';
            newRow.appendChild(notesCell);

            tableBody.appendChild(newRow);

            // Track this new row so it can be removed if canceled
            newRows.push(newRow);
        }

        function saveChanges() {
            const cells = document.querySelectorAll('#inventoryTable td');
            cells.forEach(cell => {
                cell.contentEditable = "false";
                cell.classList.remove('editable-cell');
            });

            document.querySelectorAll('.delete-icon').forEach(icon => icon.classList.add('hidden'));

            // Clear originalTableData and update it to the new saved state
            originalTableData = Array.from(document.querySelectorAll('#inventoryTable tbody tr'))
                .map(row => Array.from(row.cells).map(cell => cell.innerHTML));

            // Clear newRows array as these are now part of saved data
            newRows = [];

            document.getElementById('editButton').classList.remove('hidden');
            document.getElementById('addRowButton').classList.add('hidden');
            document.getElementById('saveButton').classList.add('hidden');
            document.getElementById('cancelButton').classList.add('hidden');
        }

        function cancelEdit() {
            const tableBody = document.querySelector('#inventoryTable tbody');
            tableBody.innerHTML = ''; // Clear all current rows

            // Repopulate table with original rows
            originalTableData.forEach(rowData => {
                const row = document.createElement('tr');
                rowData.forEach(cellData => {
                    const cell = document.createElement('td');
                    cell.innerHTML = cellData;
                    cell.contentEditable = "false";
                    cell.classList.remove('editable-cell');
                    row.appendChild(cell);
                });
                tableBody.appendChild(row);
            });

            // Clear any rows added in edit mode
            newRows = [];

            // Hide editing buttons and show the edit button again
            document.getElementById('editButton').classList.remove('hidden');
            document.getElementById('addRowButton').classList.add('hidden');
            document.getElementById('saveButton').classList.add('hidden');
            document.getElementById('cancelButton').classList.add('hidden');
        }

        function deleteRow(deleteIcon) {
            const row = deleteIcon.closest('tr');
            row.remove();
        }
    </script>
</body>
</html>
