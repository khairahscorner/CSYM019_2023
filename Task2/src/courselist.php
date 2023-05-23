<?php
// Check if the user is authenticated
session_start();
if ($_SESSION["authenticated"] !== true) {
    header("Location: index.php"); // PHP docs
    exit();
} else {
    require_once('config_db.php'); // include db setup (https://www.geeksforgeeks.org/how-to-include-content-of-a-php-file-into-another-php-file/)

    $stmt = $pdo->prepare('SELECT * FROM courses ORDER BY course_name');
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_POST['submit'])) {
        if (!isset($_POST['isAllChecked']) && empty($_POST['selectedRows'])) {
            $error = "Select at least one course to generate a report for";
        } else {
            $selectedCourses = [];
            if (isset($_POST['isAllChecked'])) {
                foreach ($results as $row) {
                    array_push($selectedCourses, $row['id']); //https://www.php.net/manual/en/function.array-push.php
                }
            } else if (!empty($_POST['selectedRows'])) { //https://www.formget.com/php-checkbox/
                $selectedCourses = $_POST['selectedRows'];

            }
            $_SESSION['coursesToReport'] = $selectedCourses;
            header("Location: report.php");
        }
    }

    if (isset($_POST['delete'])) {
        if (isset($_POST['isAllChecked'])) {
            foreach ($results as $row) {
                $stmt = $pdo->prepare('DELETE FROM courses WHERE id = :id');
                $values = [
                    'id' => $row['id']
                ];
                $stmt->execute($values);
            }
            header("Refresh:0");
        } else if (!empty($_POST['selectedRows'])) { //https://www.formget.com/php-checkbox/
            $selectedCourses = $_POST['selectedRows'];
            foreach ($selectedCourses as $id) {
                $stmt = $pdo->prepare('DELETE FROM courses WHERE id = :id');
                $values = [
                    'id' => $id
                ];
                $stmt->execute($values);
            }
            header("Refresh:0");

        } else {
            $error = "Select at least one course to delete";
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>All Courses</title>
    <?php include("imports.html"); ?>
</head>

<body>
    <?php include("header.html"); ?>
    <main>
        <h3 class="main-heading">All Courses</h3>
        <section id="table-section">
            <?php if ($results): ?>
                <form action="" method="POST">
                    <table id="courses">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAll" name="isAllChecked"></th>
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
                            <?php foreach ($results as $index => $row) {
                                $start_dates = json_decode($row['start_dates'], true);
                                $icon = is_null($row['icon_url']) ? "logo.svg" : $row['icon_url'];
                                echo '
                                    <tr>
                                        <td><input class="checkbox" type="checkbox" name="selectedRows[]" value="' . $row['id'] . '"></td>
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
                <p class='error'> No rows available</p>
            <?php endif; ?>

        </section>

        <?php
        echo "<p class='error'>" . $error . "</p>";
        ?>
    </main>

    <footer>&copy; CSYM019 2023</footer>
</body>

</html>