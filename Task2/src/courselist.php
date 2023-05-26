<?php
session_start(); // function to start a session/resume an existing one, to retrieve stored session variables (PHP Documentation)

//checks if there is no set "authentication" session variable which means there is no logged in user
if ($_SESSION["authenticated"] !== true) {
    header("Location: index.php");
    exit();
} else {
    require_once('config_db.php'); // include db setup from another PHP file (rohanmittal1366, 2022)
    $stmt = $pdo->prepare('SELECT * FROM courses ORDER BY course_name'); // prepare SQL command to use for the courses table (Eldaw M, 2023)
    $stmt->execute(); // send the query to the database
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // return the results as an associated array with keys (PHP Documentation, )

    // if submit button with name "submit" is clicked (to generate course report) (Eldaw M, 2023)
    if (isset($_POST['submit'])) {

        if (!isset($_POST['isAllChecked']) && empty($_POST['selectedRows'])) { //if the table header checkbox is not checked and none of the table rows was checked
            $error = "Select at least one course to generate a report for"; //show error to user
        }
        //if not i.e either the header checkbox has been used to select all the courses or at least one course row is checked
        else {
            $selectedCourses = []; //array to store selected courses
            // if the table header checkbox is checked i.e. all courses selected
            if (isset($_POST['isAllChecked'])) {
                // loop through each of the results fetched from the courses table
                foreach ($results as $row) {
                    array_push($selectedCourses, $row['id']); //push the course id into the array (PHP documentation, )
                }
            }
            // if individual courses were checked instead i.e variable for storing multiple checkbox is not empty (Agarwal, 2020)
            else if (!empty($_POST['selectedRows'])) {
                $selectedCourses = $_POST['selectedRows']; // save the ids of the selected rows (the array) into the selected courses array
            }
            $_SESSION['coursesToReport'] = $selectedCourses; //save these IDs array as a session variable (so it can be retrieved easily from another page)
            header("Location: report.php"); //go to the report page
        }
    }

    // the submit button with name "delete" was clicked (Eldaw M, 2023)
    if (isset($_POST['delete'])) {
        // if the table header checkbox is checked i.e. all courses selected
        if (isset($_POST['isAllChecked'])) {
            foreach ($results as $row) {
                $stmt = $pdo->prepare('DELETE FROM courses WHERE id = :id'); // prepare SQL command to use for the deletion (Eldaw M, 2023)

                //store the values to be injected into the prepared statement
                $values = [
                    'id' => $row['id']
                ];
                $stmt->execute($values); // send the query to the database with the required values
            }
            header("Refresh:0"); //refresh the page
        }
        // if individual courses were checked instead (Agarwal, 2020)
        else if (!empty($_POST['selectedRows'])) {
            $selectedCourses = $_POST['selectedRows'];
            foreach ($selectedCourses as $id) {
                $stmt = $pdo->prepare('DELETE FROM courses WHERE id = :id'); // prepare SQL command to use for the deletion (Eldaw M, 2023)

                //store the values to be injected into the prepared statement
                $values = [
                    'id' => $id
                ];
                $stmt->execute($values); // send the query to the database with the required values
            }
            header("Refresh:0"); //refresh the page
        }
        //if not i.e either the header checkbox has been used to select all the courses or at least one course row is checked
        else {
            $error = "Select at least one course to delete"; //show error to user
        }
    }
}
?>

<!-- html code for the page  -->
<!DOCTYPE html>
<html>

<head>
    <title>All Courses</title>
    <?php include("imports.html"); ?>
    <!-- include import elements (rohanmittal1366, 2022)  -->
</head>

<body>
    <?php include("header.html"); ?>
    <!-- include header elements (rohanmittal1366, 2022)  -->
    <main>
        <h3 class="main-heading">All Courses</h3>
        <section id="table-section">
            <?php if ($results): ?>
                <!-- if there are courses in the results from the initial SQL command, show table -->
                <form action="" method="POST">
                    <table id="courses">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAll" name="isAllChecked"></th>
                                <th>S/N</th>
                                <th>Icon</th>
                                <th>Course Name</th>
                                <th>Level</th>
                                <th>Start Dates</th>
                                <th>Location</th>
                                <th>Modules List</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id='table-contents'>
                            <!-- loop through every course result, and create a table row with the specified columns -->
                            <?php foreach ($results as $index => $row) {
                                $start_dates = json_decode($row['start_dates'], true); // convert stored value in db back to php json object (in this case, an array) to use  (PHP Documentation, )
                                $icon = is_null($row['icon_url']) ? "logo.svg" : $row['icon_url']; //show default image if course does not have an icon
                                echo '
                                    <tr>
                                        <td><input class="checkbox" type="checkbox" name="selectedRows[]" value="' . $row['id'] . '"></td>
                                        <td>' . ($index + 1) . '</td>
                                        <td><img src="' . $icon . '" alt="course logo" title="' . $row['subject'] . '" class="table-icon"/></td>
                                        <td>' . $row['course_name'] . '</td>
                                        <td>' . $row['level'] . '</td>
                                        <td class="cell-with-list">' . implode(', ', $start_dates) . '</td>
                                        <td>' . $row['location'] . '</td>
                                        <td><a class="linkaction" href="modules.php?id=' . $row['id'] . '">View modules</a></td>
                                        <td><a class="linkaction" href="newcourse.php?id=' . $row['id'] . '">Edit</a></td>
                                    </tr>
                                ';
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="btn-group">
                        <button type="submit" name="submit">Create Course Report</button>
                        <button type="submit" name="delete">Delete</button>
                    </div>
                </form>
            <?php else: ?>
                <!-- else, show message to user  -->
                <p class='error'> No rows available</p>
            <?php endif; ?>
        </section>

        <!-- show errors here if error variable has been set -->
        <?php
        echo "<p class='error'>" . $error . "</p>";
        ?>
    </main>

    <footer>&copy; CSYM019 2023</footer>
</body>

</html>