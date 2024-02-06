<?php
include 'includes/dbconnection.php';

// Fetch patients for dropdown
$sqlPatients = "SELECT patient_id, name FROM patients";
$resultPatients = $db->query($sqlPatients);
$patients = $resultPatients->fetch_all(MYSQLI_ASSOC);

// Fetch schedules for dropdown
$sqlSchedule = "SELECT schedule_id, schedule_time, schedule_date FROM schedule";
$resultSchedule = $db->query($sqlSchedule);
$schedules = $resultSchedule->fetch_all(MYSQLI_ASSOC);

// Fetch appointments
$sqlAppointments = "SELECT id, patient_id, schedule_id, status FROM appointments";
$resultAppointments = $db->query($sqlAppointments);

// Check for errors
if ($resultAppointments === FALSE) {
    die("Error executing the query: " . $db->error);
}

// Handle appointment edits
if (isset($_POST['edit'])) {
    $editAppointmentId = $_POST['edit_appointment_id'];
    $editPatientId = $_POST['edit_patient_id'];
    $editScheduleId = $_POST['edit_schedule_id'];
    $editStatus = $_POST['edit_status'];

    $editQuery = "UPDATE appointments 
                  SET patient_id = '$editPatientId', 
                      schedule_id = '$editScheduleId', 
                      status = '$editStatus' 
                  WHERE id = '$editAppointmentId'";

    $resultEdit = $db->query($editQuery);

    if ($resultEdit === TRUE) {
        echo "Appointment updated successfully!";
    } else {
        echo "Error updating appointment: " . $db->error;
    }

    // Redirect to the appointment_form.php page after updating
    header("Location: appointment_form.php");
    exit();
}

// Handle appointment deletion
if (isset($_GET['delete'])) {
    $deleteAppointmentId = $_GET['delete'];

    $deleteQuery = "DELETE FROM appointments WHERE id = '$deleteAppointmentId'";
    $resultDelete = $db->query($deleteQuery);

    if ($resultDelete === TRUE) {
        echo "Appointment deleted successfully!";
    } else {
        echo "Error deleting appointment: " . $db->error;
    }

    // Redirect to the appointment_form.php page after deleting
    header("Location: appointment_form.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    
    <title>Appointment Form</title>
    
</head>
<body>
<style>
        body {
            font-family: Arial, sans-serif;
            background: radial-gradient(circle at top left, #2ecc71, #3498db);
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center; /* Center the text horizontally */
        }

        .container {
            max-width: 800px; /* Increase the max-width for better readability */
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.9);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            margin-bottom: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px; /* Adjust the margin as needed */
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #17ac12;
        }

        .button {
            display: inline-block;
            padding: 10px;
            text-decoration: none;
            background-color: #041404;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }

        .button:hover {
            background-color: #45a049;
        }
    </style>
<div class="container">
    <h2>Appointment Form</h2>

    <!-- Appointment Form -->
    <form action="includes/appointmentprocess.php" method="post">
        <input type="hidden" name="id" id="id">

        <label for="patient_id">Patient Name:</label>
        <select name="patient_id" id="patient_id" required>
            <?php
            foreach ($patients as $patient) {
                echo "<option value='{$patient['patient_id']}'>{$patient['name']}</option>";
            }
            ?>
        </select>

        <label for="schedule_id">Schedule Time:</label>
        <select name="schedule_id" id="schedule_id" required>
            <?php
            foreach ($schedules as $schedule) {
                echo "<option value='{$schedule['schedule_id']}'>{$schedule['schedule_time']}</option>";
            }
            ?>
        </select>

        <label for="schedule_date">Schedule Date:</label>
        <select name="schedule_date" id="schedule_date" required>
            <?php
            foreach ($schedules as $schedule) {
                echo "<option value='{$schedule['schedule_id']}'>{$schedule['schedule_date']}</option>";
            }
            ?>
        </select>

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="Pending">Pending</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Cancelled">Cancelled</option>
        </select>

        <!-- Submit and Reset Buttons -->
        <div class="buttons">
            <button type="submit" name="add">Add Appointment</button>
            <a href="index.php" class="button" style=" background-color: #4CAF50; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;"> Back </a>
        </div>
    </form>
    </div>
    <!-- Display Appointments Table -->
    <div class= "container" >
    <h2>Appointments</h2>
    
    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Schedule Date</th>
                <th>Schedule Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($resultAppointments->num_rows > 0) {
                while ($row = $resultAppointments->fetch_assoc()) {
                    // Fetch additional details based on foreign keys (e.g., patient name, schedule details)
                    $patientId = $row['patient_id'];
                    $scheduleId = $row['schedule_id'];

                    // Fetch patient name
                    $patientQuery = "SELECT name FROM patients WHERE patient_id = $patientId";
                    $resultPatient = $db->query($patientQuery);
                    $patient = $resultPatient->fetch_assoc();

                    // Fetch schedule details
                    $scheduleQuery = "SELECT schedule_time, schedule_date FROM schedule WHERE schedule_id = $scheduleId";
                    $resultSchedule = $db->query($scheduleQuery);
                    $schedule = $resultSchedule->fetch_assoc();

                    echo "<tr>";
                    echo "<td>{$patient['name']}</td>";
                    echo "<td>{$schedule['schedule_date']}</td>";
                    echo "<td>{$schedule['schedule_time']}</td>";
                    echo "<td>{$row['status']}</td>";
                    echo "<td>";
                    echo "<form action='includes/appointmentprocess.php' method='post'>";
                    echo "<a href='edit_form.php?id={$row['id']}' class='button edit'>Edit</a> | <a href='includes/appointmentprocess.php?delete={$row['id']}' class='button delete'>Delete</a>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No appointments found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    </div>

</body>
</html>
