<?php
// Check if the user is authenticated
session_start();
if ($_SESSION["authenticated"] !== true) {
    header("Location: index.php"); // PHP docs
} else if (empty($_SESSION['coursesToReport'])) { //ensure the array is not somehow empty
    header("Location: courselist.php");
    exit();
} else {
    require('config_db.php'); // include db setup (https://www.geeksforgeeks.org/how-to-include-content-of-a-php-file-into-another-php-file/)
    require_once('functions.php');

    $coursesToReport = $_SESSION['coursesToReport'];
}

//ensures the session variable is deleted, in case of page reload
unset($_SESSION['coursesToReport']); //https://stackoverflow.com/questions/41867077/remove-session-variable-on-page-refresh

function getCourse(PDO $pdo, $id)
{
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = :id');
    $values = [
        'id' => $id
    ];
    $stmt->execute($values);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getCourseModules(PDO $pdo, $course_id)
{
    $stmt = $pdo->prepare('SELECT * FROM modules WHERE course_id = :id ORDER BY stage DESC');
    $values = [
        'id' => $course_id
    ];
    $stmt->execute($values);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Course Report</title>
    <link rel="stylesheet" href="./layout.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php include("imports.html"); ?>
</head>

<body>
    <?php include("header.html"); ?>
    <main id="main-report">
        <?php foreach ($coursesToReport as $courseId): ?>
            <?php
            $course = getCourse($pdo, $courseId);
            $courseModules = getCourseModules($pdo, $courseId);
            $start_dates = json_decode($course['start_dates'], true);
            $created_at = $date = new DateTime($course['created_at']);
            $updated_at = $date = new DateTime($course['updated_at']);
            ?>
            <div class="section-group report" data-id="<?php echo $courseId ?>"
                data-coursename="<?php echo $course['course_name'] ?>">
                <div class="first-col">
                    <h3>
                        <?php echo $course['course_name'] ?>
                    </h3>
                    <table id="courses">
                        <tbody id='table-contents'>
                            <tr>
                                <td class="label">Course Name</td>
                                <td>
                                    <?php echo $course['course_name'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Subject Area</td>
                                <td>
                                    <?php echo $course['subject'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Level</td>
                                <td>
                                    <?php echo $course['level'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Start Dates</td>
                                <td class="cell-with-list">
                                    <?php echo implode(', ', $start_dates) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Date Added</td>
                                <td class="cell-with-list">
                                    <?php echo $created_at->format('d/m/Y') ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Last Updated</td>
                                <td class="cell-with-list">
                                    <?php echo $updated_at->format('d/m/Y') ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="view-more"
                        data-details="<?php echo htmlspecialchars(json_encode($course), ENT_QUOTES, 'UTF-8') ?>"
                        data-modules="<?php echo htmlspecialchars(json_encode($courseModules), ENT_QUOTES, 'UTF-8') ?>">View
                        More
                    </button>
                </div>
                <div class="second-col">
                    <h3> Modules Chart</h3>
                    <div>
                        <?php if (count($courseModules) > 0): ?>
                            <canvas id="chart<?php echo $courseId ?>"></canvas>
                        <?php else: ?>
                            <p>No modules</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (count($coursesToReport) > 1): ?>
            <h3 id="combined-header"> Modules Comparison Chart</h3>
            <div class="section-group">
                <canvas id="comparison-chart" style="width: 90%; margin: 0 auto;"></canvas>
            </div>
        <?php endif; ?>

        <div class="overlay">
            <!-- element to show further course details on click of row -->
            <div class="overlay-content">
                <div id="close-btn">
                    <!-- div elements were used to define the close icon  -->
                    <div id="line1"></div>
                    <div id="line2"></div>
                </div>
                <div id="course-content"></div> <!-- element containing the course details -->
            </div>
        </div>
    </main>

    <footer>&copy; CSYM019 2023</footer>
</body>

</html>