<?php
    // Include the database connection file
    include 'includes/dbconnection.php';

    // Initialize variables for the edit form
    $editMode = false;
    $editId = '';
    $editName = '';
    $editEmail = '';

    // Check if edit button is clicked
    if (isset($_GET['edit'])) {
        $editId = $_GET['edit'];
        $editMode = true;

        // Fetch data of the selected patient for editing
        $result = $db->query("SELECT * FROM patients WHERE patient_id = $editId");

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $editName = $row['name'];
            $editEmail = $row['email'];
        }
    }

    // Process form submission for adding new patient
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];

        $stmt = $db->prepare("INSERT INTO patients (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();
        $stmt->close();

        // Redirect back to the patient form
        header("Location: patient_form.php");
        exit();
    }

    // Process form submission for editing existing patient
    if (isset($_POST['edit'])) {
        $id = $_POST['patient_id']; // Change 'id' to 'patient_id'
        $name = $_POST['name'];
        $email = $_POST['email'];

        $stmt = $db->prepare("UPDATE patients SET name=?, email=? WHERE patient_id=?");
        $stmt->bind_param("ssi", $name, $email, $id);
        $stmt->execute();
        $stmt->close();

        // Redirect back to the patient form
        header("Location: patient_form.php");
        exit();
    }

    // Fetch and display patient data from the database
    $result = $db->query("SELECT * FROM patients");

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <title>Patient Form</title>
    </head>
    <body>
        <div class="container">
            <?php if (!$editMode): ?>
                <h2>Patient Form</h2>
                <!-- Form to add new patient -->
                <form action="patient_form.php" method="post">
                    <label for="name">Name:</label>
                    <input type="text" name="name" required>
                    <label for="email">Email:</label>
                    <input type="email" name="email" required>

                    <!-- Add button and Back link in the same form -->
                    <button type="submit" name="add" style="width: 100px; height: 40px; margin-right: 10px;">Add</button>
<a href="index.php" class="button" style="width: 100px; height: 20px; background-color: #4CAF50; color: white; text-decoration: none; border: none; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;">Back</a>


                </form>

            <?php endif; ?>

            <?php if ($editMode): ?>
                <!-- Form to edit existing patient -->
                <h2>Edit Patient</h2>
                <form action="patient_form.php" method="post">
                    <input type="hidden" name="patient_id" value="<?php echo $editId; ?>"> <!-- Change 'id' to 'patient_id' -->
                    <label for="name">Name:</label>
                    <input type="text" name="name" value="<?php echo $editName; ?>" required>
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo $editEmail; ?>" required>
                    <button type="submit" name="edit">Edit</button>
                </form>
            <?php endif; ?>

            <!-- Display added patients -->
            <h2>Patients</h2>

            <?php
            if ($result->num_rows > 0) {
                echo '<table>';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Name</th>';
                echo '<th>Email</th>';
                echo '<th>Action</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td><a href='patient_form.php?edit={$row['patient_id']}' class='button edit'>Edit</a> | <a href='includes/patientprocess.php?delete={$row['patient_id']}' class='button delete'>Delete</a>";
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p>No patients found.</p>';
            }

            $result->free(); // Free the result set
            ?>
        
        </div>
    </body>
    </html>

    <?php
    $db->close();
    ?>
