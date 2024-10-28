<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chemical Inventory Form</title>
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
        <div class="table-title">Chemical Inventory Form</div>

        <div class="header-container">
            <div id="dateField">Date: </div>
            <div>Lab Name: MRL - Immunology</div>
            <div>Personnel: EMM</div>
        </div>

        <table id="inventoryTable">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Item Code</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Location</th>
                    <th>Expiry Date</th>
                    <th>Supplier</th>
                    <th>Cost</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td contenteditable="false">Acetone</td>
                    <td contenteditable="false">CH001</td>
                    <td contenteditable="false">2</td>
                    <td contenteditable="false">liters</td>
                    <td contenteditable="false">Chemical Shelf 1</td>
                    <td contenteditable="false">10/2024</td>
                    <td contenteditable="false">ChemLab Supplies</td>
                    <td contenteditable="false">PHP 5,000.00</td>
                    <td contenteditable="false">Flammable, store in cool place <span class="delete-icon hidden" onclick="deleteRow(this)">🗑️</span></td>
                </tr>
                <tr>
                    <td contenteditable="false">Sodium Chloride</td>
                    <td contenteditable="false">CH002</td>
                    <td contenteditable="false">4</td>
                    <td contenteditable="false">kg</td>
                    <td contenteditable="false">Shelf A</td>
                    <td contenteditable="false">06/2025</td>
                    <td contenteditable="false">Lab Co. Supplies</td>
                    <td contenteditable="false">PHP 1,200.00</td>
                    <td contenteditable="false">General Lab use <span class="delete-icon hidden" onclick="deleteRow(this)">🗑️</span></td>
                </tr>
            </tbody>
        </table>

        <div class="button-container">
            <button id="editButton" onclick="enterEditMode()">Edit</button>
            <button id="addRowButton" class="hidden" onclick="addRow()">+ Add Row</button>
            <button id="saveButton" class="hidden" onclick="saveChanges()">Save</button>
            <button id="cancelButton" class="hidden" onclick="cancelEdit()">Cancel</button>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
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
            const columns = 8;

            for (let i = 0; i < columns; i++) {
                const newCell = document.createElement('td');
                newCell.contentEditable = "true";
                newCell.classList.add('editable-cell');
                newRow.appendChild(newCell);
            }

            const notesCell = document.createElement('td');
            notesCell.contentEditable = "true";
            notesCell.classList.add('editable-cell');
            notesCell.innerHTML = '<span class="delete-icon hidden" onclick="deleteRow(this)">🗑️</span>';
            newRow.appendChild(notesCell);

            tableBody.appendChild(newRow);
            newRows.push(newRow);
        }

        function saveChanges() {
            const cells = document.querySelectorAll('#inventoryTable td');
            cells.forEach(cell => {
                cell.contentEditable = "false";
                cell.classList.remove('editable-cell');
            });

            document.querySelectorAll('.delete-icon').forEach(icon => icon.classList.add('hidden'));

            originalTableData = Array.from(document.querySelectorAll('#inventoryTable tbody tr'))
                .map(row => Array.from(row.cells).map(cell => cell.innerHTML));

            newRows = [];

            document.getElementById('editButton').classList.remove('hidden');
            document.getElementById('addRowButton').classList.add('hidden');
            document.getElementById('saveButton').classList.add('hidden');
            document.getElementById('cancelButton').classList.add('hidden');
        }

        function cancelEdit() {
            const tableBody = document.querySelector('#inventoryTable tbody');
            tableBody.innerHTML = '';

            originalTableData.forEach(rowData => {
                const row = document.createElement('tr');
                rowData.forEach(cellData => {
                    const cell = document.createElement('td');
                    cell.innerHTML = cellData;
                    cell.contentEditable = "false";
                    row.appendChild(cell);
                });
                tableBody.appendChild(row);
            });

            newRows = [];

            document.getElementById('editButton').classList.remove('hidden');
            document.getElementById('addRowButton').classList.add('hidden');
            document.getElementById('saveButton').classList.add('hidden');
            document.getElementById('cancelButton').classList.add('hidden');
        }

        function deleteRow(icon) {
            const row = icon.closest('tr');
            row.remove();
        }
    </script>
</body>
</html>