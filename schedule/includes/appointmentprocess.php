<?php
include 'dbconnection.php';

// Add new appointment
if (isset($_POST['add'])) {
    // Retrieve data from the form
    $patientId = $_POST['patient_id'];
    $scheduleId = $_POST['schedule_id'];
    $status = $_POST['status'];

    // Insert data into the appointments table
    $insertQuery = "INSERT INTO appointments (patient_id, schedule_id, status) VALUES ('$patientId', '$scheduleId', '$status')";
    $resultInsert = $db->query($insertQuery);

    if ($resultInsert === TRUE) {
        echo "Appointment added successfully!";
    } else {
        echo "Error adding appointment: " . $db->error;
    }

    // Redirect to the appointment_form.php page
    header("Location: ../appointment_form.php");
    exit();
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
    header("Location: ../appointment_form.php");
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
    header("Location: ../appointment_form.php");
    exit();
}
?>