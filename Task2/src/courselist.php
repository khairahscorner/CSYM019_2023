<?php
// Check if the user is authenticated
session_start();
if ($_SESSION["authenticated"] !== true) {
    header("Location: index.php"); // PHP docs
} else {
    require('config_db.php'); // include db setup (https://www.geeksforgeeks.org/how-to-include-content-of-a-php-file-into-another-php-file/)

    $stmt = $pdo->prepare('SELECT * FROM courses ORDER BY course_name');
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_POST['submit'])) {
        if (isset($_POST['isAllChecked'])) {
            foreach ($results as $row) {
                echo "All ID: " . $row['id'];
            }
        } else if (!empty($_POST['selectedRows'])) { //https://www.formget.com/php-checkbox/
            $selectedCourses = $_POST['selectedRows'];
            foreach ($selectedCourses as $id) {
                echo "ID: " . $id;
            }
        } else {
            $error = "Select at least one course to generate a report for";
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
    <link rel="stylesheet" href="./layout.css">
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="script.js" type="text/javascript"></script>
</head>

<body>
    <?php include("header.html"); ?>
    <main>
        <h3> All Courses</h3>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id='table-contents'>
                            <?php foreach ($results as $index => $row) {
                                $start_dates = json_decode($row['start_dates'], true);
                                echo '
                                    <tr>
                                        <td><input class="checkbox" type="checkbox" name="selectedRows[]" value="' . $row['id'] . '"></td>
                                        <td><img src="./icons/' . $row['icon_url'] . '" alt="course logo" title="' . $row['subject'] . '" class="table-icon"/></td>
                                        <td>' . $row['course_name'] . '</td>
                                        <td>' . $row['level'] . '</td>
                                        <td class="cell-with-list">' . implode(', ', $start_dates) . '</td>
                                        <td>' . $row['location'] . '</td>
                                        <td><a class="edit" href="newcourse.php?id=' . $row['id'] . '">Edit</a></td>
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