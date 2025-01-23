<?php
session_start();

// Debugging session variables
// Uncomment during debugging to verify session data
// echo "<pre>"; print_r($_SESSION); echo "</pre>";

include "php_scripts/rotaScript.php";

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit;
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Coffee Shop System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="stylesheet.css">
    <script src="https://kit.fontawesome.com/4a263b5a4b.js" crossorigin="anonymous"></script>
</head>

<body>
    <header>
        <h2 class="logo">coffee.co</h2>
        <div class="navbar">
            <a href="<?php echo isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] ? 'adminPage.php' : 'ClientPage.php'; ?>">Home</a>
            <a href="index.php">Logout</a>
            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']): ?>
                <a href="salesData.php"> Sales data </a>
            <?php endif; ?>
            <a href="inventory.php">Inventory</a>
            <a href="rota.php">Rota</a>
            <a href="wages.php">Wages</a>
        </div>
    </header>
    <h1 class="login_txt">Rota Dashboard</h1>
    <section class="inventory-section">
        <article class="inventory-table">
            <table class="inventory-dataTable" border="1">
                <thead>
                    <tr>
                        <th colspan="7" class="inventory-table-header">Rota for this month</th>
                    </tr>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Role</th>
                        <th>Shift Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Hours Worked</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($shiftDetails)): ?>
                        <?php foreach ($shiftDetails as $shift): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($shift['forname']); ?></td>
                                <td><?php echo htmlspecialchars($shift['surname']); ?></td>
                                <td><?php echo htmlspecialchars($shift['roleType']); ?></td>
                                <td><?php echo htmlspecialchars($shift['shiftDate']); ?></td>
                                <td><?php echo htmlspecialchars($shift['shiftStartTime']); ?></td>
                                <td><?php echo htmlspecialchars($shift['shiftEndTime']); ?></td>
                                <td><?php echo htmlspecialchars($shift['hoursWorked']); ?> hours</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8"><?php echo $errorMessage; ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']): ?>
                <div class="inventory-form-container">
                    <!-- Add New Shift Form -->
                    <form class="inventory-add-items" method="POST" action="rota.php">
                        <h3>Add New Shift</h3>
                        <label for="shift_date">Shift Date</label>
                        <input type="date" id="shift_date" name="shift_date" required>

                        <label for="start_time">Start Time</label>
                        <input type="time" id="start_time" name="start_time" required>

                        <label for="end_time">End Time</label>
                        <input type="time" id="end_time" name="end_time" required>

                        <label for="employee_assigned">Employee Assigned</label>
                        <select name="employee_assigned" id="employee_assigned" required>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?= $employee['userID'] ?>">
                                    <?= htmlspecialchars($employee['forname'] . ' ' . $employee['surname']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="role_type">Role Type</label>
                        <select name="role_type" id="role_type" required>
                            <?php foreach ($roleTypes as $role): ?>
                                <option value="<?= $role['roleTypeID'] ?>">
                                    <?= htmlspecialchars($role['roleType']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <input class="inventory-submit-inven" type="submit" value="Add Shift">
                    </form>

                </div>

                <div class="inventory-form-container">
                    <!-- Update Shift Form -->
                    <form class="inventory-add-items" method="POST" action="rota.php">
                        <h3>Update A Shift</h3>
                        <label for="shift_id_update">Select Shift</label>
                        <select name="shift_id_update" id="shift_id_update" required>
                            <?php foreach ($shiftDetails as $shift): ?>
                                <option value="<?= $shift['rotaID'] ?>">
                                    <?= htmlspecialchars($shift['forname'] . ' ' . $shift['surname'] . ' - ' . $shift['shiftDate']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="shift_date_update">Shift Date</label>
                        <input type="date" id="shift_date_update" name="shift_date_update" required>

                        <label for="start_time_update">Start Time</label>
                        <input type="time" id="start_time_update" name="start_time_update" required>

                        <label for="end_time_update">End Time</label>
                        <input type="time" id="end_time_update" name="end_time_update" required>

                        <input class="inventory-submit-inven" type="submit" value="Update">
                    </form>
                </div>

                <div class="inventory-form-container">
                    <!-- Delete Shift Form -->
                    <form class="inventory-add-items" method="POST" action="rota.php">
                        <h3>Delete A Shift</h3>
                        <label for="shift_id_delete">Select Shift</label>
                        <select name="shift_id_delete" id="shift_id_delete" required>
                            <?php foreach ($shiftDetails as $shift): ?>
                                <option value="<?= $shift['rotaID'] ?>">
                                    <?= htmlspecialchars($shift['forname'] . ' ' . $shift['surname'] . ' - ' . $shift['shiftDate']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <input class="inventory-submit-inven" type="submit" value="Delete">
                    </form>
                </div>
            <?php endif; ?>


        </article>
    </section>
    <footer class="mainfooter">
        <a class="logofooter"><i class="fa-regular fa-copyright"></i> 2025 coffee.co All Rights Reserved.</a>
    </footer>
</body>

</html>