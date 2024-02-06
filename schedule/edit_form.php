<?php
include 'includes/dbconnection.php';

// Fetch appointment details based on the ID from the URL
if (isset($_GET['id'])) {
    $editAppointmentId = $_GET['id'];

    $editQuery = "SELECT id, patient_id, schedule_id, status FROM appointments WHERE id = '$editAppointmentId'";
    $resultEdit = $db->query($editQuery);

    if ($resultEdit === FALSE) {
        die("Error executing the query: " . $db->error);
    }

    if ($resultEdit->num_rows > 0) {
        $appointment = $resultEdit->fetch_assoc();
    } else {
        die("Appointment not found");
    }
} else {
    die("Invalid request. Please provide an appointment ID.");
}

// Fetch patients for dropdown
$sqlPatients = "SELECT patient_id, name FROM patients";
$resultPatients = $db->query($sqlPatients);
$patients = $resultPatients->fetch_all(MYSQLI_ASSOC);

// Fetch schedules for dropdown
$sqlSchedule = "SELECT schedule_id, schedule_time, schedule_date FROM schedule";
$resultSchedule = $db->query($sqlSchedule);
$schedules = $resultSchedule->fetch_all(MYSQLI_ASSOC);

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
        // Redirect to appointment_form.php on successful update
        header("Location: appointment_form.php");
        exit(); // Ensure that subsequent code is not executed
    } else {
        echo "Error updating appointment: " . $db->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        select, input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .buttons {
            margin-top: 15px;
        }

        button {
            padding: 10px;
            margin-right: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Appointment</h2>

    <!-- Edit Appointment Form -->
    <form action="appointment_form.php" method="post">
        <input type="hidden" name="edit_appointment_id" value="<?php echo $appointment['id']; ?>">

        <label for="patient_id">Patient Name:</label>
        <select name="edit_patient_id" id="edit_patient_id" required>
            <?php
            foreach ($patients as $patient) {
                $selected = ($patient['patient_id'] == $appointment['patient_id']) ? 'selected' : '';
                echo "<option value='{$patient['patient_id']}' $selected>{$patient['name']}</option>";
            }
            ?>
        </select>

        <label for="edit_schedule_id">Schedule Time:</label>
        <select name="edit_schedule_id" id="edit_schedule_id" required>
            <?php
            foreach ($schedules as $schedule) {
                $selected = ($schedule['schedule_id'] == $appointment['schedule_id']) ? 'selected' : '';
                echo "<option value='{$schedule['schedule_id']}' $selected>{$schedule['schedule_time']}</option>";
            }
            ?>
        </select>

        <label for="edit_status">Status:</label>
        <select name="edit_status" id="edit_status" required>
            <option value="Pending" <?php echo ($appointment['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="Confirmed" <?php echo ($appointment['status'] == 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
            <option value="Cancelled" <?php echo ($appointment['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
        </select>

        <!-- Submit Button -->
        <div class="buttons">
            <button type="submit" name="edit">Update Appointment</button>
        </div>
    </form>
</div>

</body>
</html>
