<?php
require_once '../config.php';
requireLogin();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_bus':
                $stmt = $pdo->prepare("INSERT INTO buses (line_number, route_name, frequency, type, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_POST['line_number'], $_POST['route_name'], $_POST['frequency'], $_POST['type'], $_POST['status']]);
                header('Location: database.php?success=bus_added');
                exit;
            case 'add_station':
                $stmt = $pdo->prepare("INSERT INTO bus_stations (name, location, city, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['name'], $_POST['location'], $_POST['city'], $_POST['status']]);
                header('Location: database.php?success=station_added');
                exit;
            case 'edit_bus':
                $stmt = $pdo->prepare("UPDATE buses SET line_number = ?, route_name = ?, frequency = ?, type = ?, status = ? WHERE id = ?");
                $stmt->execute([$_POST['line_number'], $_POST['route_name'], $_POST['frequency'], $_POST['type'], $_POST['status'], $_POST['id']]);
                header('Location: database.php?success=bus_updated');
                exit;
            case 'edit_station':
                $stmt = $pdo->prepare("UPDATE bus_stations SET name = ?, location = ?, city = ?, status = ? WHERE id = ?");
                $stmt->execute([$_POST['name'], $_POST['location'], $_POST['city'], $_POST['status'], $_POST['id']]);
                header('Location: database.php?success=station_updated');
                exit;
            case 'delete_bus':
                $stmt = $pdo->prepare("DELETE FROM buses WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                header('Location: database.php?success=bus_deleted');
                exit;
            case 'delete_station':
                $stmt = $pdo->prepare("DELETE FROM bus_stations WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                header('Location: database.php?success=station_deleted');
                exit;
        }
    }
}

// Fetch existing data
$buses = $pdo->query("SELECT * FROM buses ORDER BY line_number")->fetchAll(PDO::FETCH_ASSOC);
$stations = $pdo->query("SELECT * FROM bus_stations ORDER BY city, name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Management - Transit Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f6fa;
            display: flex;
            height: 100vh;
            color: #333;
        }

        .sidebar {
            width: 260px;
            background-color: #2a3042;
            display: flex;
            flex-direction: column;
            color: #a6b0cf;
            position: relative;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            color: #fff;
            font-size: 22px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .sidebar-header h2 i {
            color: #556ee6;
            margin-right: 10px;
            font-size: 24px;
        }

        .sidebar-menu {
            padding: 20px 0;
            flex: 1;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin: 8px 0;
        }

        .sidebar li a {
            text-decoration: none;
            color: #a6b0cf;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            font-size: 15px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar li a i {
            margin-right: 10px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .sidebar li a:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .sidebar li a.active {
            color: #fff;
            background-color: rgba(85, 110, 230, 0.25);
            border-left: 3px solid #556ee6;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #f5f6fa;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #495057;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .data-section {
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.03);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .section-header {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #495057;
            margin: 0;
        }

        .section-content {
            padding: 20px;
        }

        .btn {
            background-color: #556ee6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background-color: #4458ca;
        }

        .btn-outline {
            background-color: transparent;
            color: #556ee6;
            border: 1px solid #556ee6;
        }

        .btn-outline:hover {
            background-color: #556ee6;
            color: white;
        }

        .btn-danger {
            background-color: #f46a6a;
        }

        .btn-danger:hover {
            background-color: #e35c5c;
        }

        .btn-success {
            background-color: #34c38f;
        }

        .btn-success:hover {
            background-color: #2fb380;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        th {
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fc;
            white-space: nowrap;
        }

        td {
            color: #74788d;
        }

        tr:hover {
            background-color: #f8f9fc;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background-color: rgba(52, 195, 143, 0.1);
            color: #34c38f;
        }

        .status-inactive {
            background-color: rgba(244, 106, 106, 0.1);
            color: #f46a6a;
        }

        .status-maintenance {
            background-color: rgba(241, 180, 76, 0.1);
            color: #f1b44c;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            margin: 50px auto;
            padding: 20px;
            width: 500px;
            border-radius: 5px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #495057;
            margin: 0;
        }

        .close {
            font-size: 24px;
            cursor: pointer;
            color: #74788d;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #495057;
            font-weight: 500;
        }

        input, select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus {
            border-color: #556ee6;
            outline: none;
        }

        .alert {
            padding: 12px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background-color: rgba(52, 195, 143, 0.1);
            color: #34c38f;
            border: 1px solid rgba(52, 195, 143, 0.2);
        }

        .alert-danger {
            background-color: rgba(244, 106, 106, 0.1);
            color: #f46a6a;
            border: 1px solid rgba(244, 106, 106, 0.2);
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .search-box {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            width: 200px;
            margin-right: 10px;
        }

        .search-box:focus {
            border-color: #556ee6;
            outline: none;
        }

        .logout-btn {
            color: #f46a6a;
            text-decoration: none;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            font-size: 15px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .logout-btn i {
            margin-right: 10px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .logout-btn:hover {
            color: #fff;
            background-color: rgba(244, 106, 106, 0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-chart-line"></i> Transit Admin</h2>
        </div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="road.php"><i class="fas fa-road"></i> Roads</a></li>
                <li><a href="bus.php"><i class="fas fa-bus"></i> Buses</a></li>
                <li><a href="map.php"><i class="fas fa-map-marked-alt"></i> Map</a></li>
                <li><a href="database.php" class="active"><i class="fas fa-database"></i> Database</a></li>
            </ul>
        </div>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Database Management</h1>
            <div class="header-actions">
                <button class="btn btn-outline" onclick="exportData()"><i class="fas fa-download"></i> Export Data</button>
                <button class="btn" onclick="refreshData()"><i class="fas fa-sync-alt"></i> Refresh</button>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                switch ($_GET['success']) {
                    case 'bus_added':
                        echo 'Bus added successfully!';
                        break;
                    case 'station_added':
                        echo 'Station added successfully!';
                        break;
                    case 'bus_updated':
                        echo 'Bus updated successfully!';
                        break;
                    case 'station_updated':
                        echo 'Station updated successfully!';
                        break;
                    case 'bus_deleted':
                        echo 'Bus deleted successfully!';
                        break;
                    case 'station_deleted':
                        echo 'Station deleted successfully!';
                        break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="data-section">
            <div class="section-header">
                <h3>Buses</h3>
                <div>
                    <input type="text" class="search-box" id="busSearch" placeholder="Search buses..." onkeyup="searchTable('busSearch', 'busesTable')">
                    <button class="btn" onclick="openModal('bus')"><i class="fas fa-plus"></i> Add Bus</button>
                </div>
            </div>
            <div class="section-content">
                <div class="table-container">
                    <table id="busesTable">
                        <thead>
                            <tr>
                                <th>Line Number</th>
                                <th>Route Name</th>
                                <th>Frequency</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($buses as $bus): ?>
                            <tr data-bus-id="<?php echo $bus['id']; ?>">
                                <td><?php echo htmlspecialchars($bus['line_number']); ?></td>
                                <td><?php echo htmlspecialchars($bus['route_name']); ?></td>
                                <td><?php echo htmlspecialchars($bus['frequency']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($bus['type'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo htmlspecialchars($bus['status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($bus['status'])); ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <button class="btn btn-outline" onclick="editBus(<?php echo $bus['id']; ?>)"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger" onclick="deleteBus(<?php echo $bus['id']; ?>)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="data-section">
            <div class="section-header">
                <h3>Bus Stations</h3>
                <div>
                    <input type="text" class="search-box" id="stationSearch" placeholder="Search stations..." onkeyup="searchTable('stationSearch', 'stationsTable')">
                    <button class="btn" onclick="openModal('station')"><i class="fas fa-plus"></i> Add Station</button>
                </div>
            </div>
            <div class="section-content">
                <div class="table-container">
                    <table id="stationsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Location</th>
                                <th>City</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stations as $station): ?>
                            <tr data-station-id="<?php echo $station['id']; ?>">
                                <td><?php echo htmlspecialchars($station['name']); ?></td>
                                <td><?php echo htmlspecialchars($station['location']); ?></td>
                                <td><?php echo htmlspecialchars($station['city']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo htmlspecialchars($station['status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($station['status'])); ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <button class="btn btn-outline" onclick="editStation(<?php echo $station['id']; ?>)"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger" onclick="deleteStation(<?php echo $station['id']; ?>)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Bus Modal -->
    <div id="busModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Bus</h3>
                <span class="close" onclick="closeModal('bus')">&times;</span>
            </div>
            <form action="database.php" method="POST" id="busForm">
                <input type="hidden" name="action" value="add_bus">
                <input type="hidden" name="id" id="busId">
                <div class="form-group">
                    <label for="line_number">Line Number</label>
                    <input type="text" id="line_number" name="line_number" required>
                </div>
                <div class="form-group">
                    <label for="route_name">Route Name</label>
                    <input type="text" id="route_name" name="route_name" required>
                </div>
                <div class="form-group">
                    <label for="frequency">Frequency</label>
                    <input type="text" id="frequency" name="frequency" required>
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="city">City</option>
                        <option value="intercity">Intercity</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Save Bus</button>
            </form>
        </div>
    </div>

    <!-- Add/Edit Station Modal -->
    <div id="stationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Station</h3>
                <span class="close" onclick="closeModal('station')">&times;</span>
            </div>
            <form action="database.php" method="POST" id="stationForm">
                <input type="hidden" name="action" value="add_station">
                <input type="hidden" name="id" id="stationId">
                <div class="form-group">
                    <label for="name">Station Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" required>
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Save Station</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(type) {
            document.getElementById(type + 'Modal').style.display = 'block';
            document.getElementById(type + 'Form').reset();
            document.getElementById(type + 'Form').action.value = 'add_' + type;
            document.getElementById(type + 'Id').value = '';
        }

        function closeModal(type) {
            document.getElementById(type + 'Modal').style.display = 'none';
        }

        function editBus(id) {
            const row = document.querySelector(`tr[data-bus-id="${id}"]`);
            const form = document.getElementById('busForm');
            form.action.value = 'edit_bus';
            form.id.value = id;
            form.line_number.value = row.cells[0].textContent;
            form.route_name.value = row.cells[1].textContent;
            form.frequency.value = row.cells[2].textContent;
            form.type.value = row.cells[3].textContent.toLowerCase();
            form.status.value = row.cells[4].querySelector('.status-badge').textContent.toLowerCase();
            document.getElementById('busModal').style.display = 'block';
        }

        function editStation(id) {
            const row = document.querySelector(`tr[data-station-id="${id}"]`);
            const form = document.getElementById('stationForm');
            form.action.value = 'edit_station';
            form.id.value = id;
            form.name.value = row.cells[0].textContent;
            form.location.value = row.cells[1].textContent;
            form.city.value = row.cells[2].textContent;
            form.status.value = row.cells[3].querySelector('.status-badge').textContent.toLowerCase();
            document.getElementById('stationModal').style.display = 'block';
        }

        function deleteBus(id) {
            if (confirm('Are you sure you want to delete this bus?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'database.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_bus';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = id;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteStation(id) {
            if (confirm('Are you sure you want to delete this station?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'database.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_station';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = id;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function searchTable(inputId, tableId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toUpperCase();
            const table = document.getElementById(tableId);
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let found = false;
                const td = tr[i].getElementsByTagName("td");
                
                for (let j = 0; j < td.length - 1; j++) {
                    const cell = td[j];
                    if (cell) {
                        const txtValue = cell.textContent || cell.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                tr[i].style.display = found ? "" : "none";
            }
        }

        function exportData() {
            // Implement export functionality
            alert('Export functionality to be implemented');
        }

        function refreshData() {
            location.reload();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html> 