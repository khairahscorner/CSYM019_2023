<?php
session_start(); // function to start a session/resume an existing one, to retrieve stored session variables (PHP Documentation)

//checks if there is no set "authentication" session variable which means there is no logged in user
if ($_SESSION["authenticated"] !== true) {
    header("Location: index.php");
    exit();
}
// else, check if the session variable storing the ids of the courses to generate report for is empty
else if (empty($_SESSION['coursesToReport'])) {
    header("Location: courselist.php"); // redirect back to course list page if so
    exit(); // end script
} else {
    require_once('config_db.php'); // include db setup from another PHP file (rohanmittal1366, 2022)
    require_once('functions.php'); // include php script containing functions

    $coursesToReport = $_SESSION['coursesToReport']; // retrieve the session variable used to store the course ids
}

//ensures the session variable is deleted, in case of page reload
unset($_SESSION['coursesToReport']); //remove the session variable

// function to retrieve a course with SQL commands, takes PDO object and course id as params
function getCourse(PDO $pdo, $id)
{
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = :id');
    $values = [
        'id' => $id
    ];
    $stmt->execute($values);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// function to get all the modules for a course with SQL command, takes PDO object and course id as params
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- include chartjs (Chart.js, ) -->
    <?php include("imports.html"); ?>
</head>

<body>
    <?php include("header.html"); ?>
    <!-- add header element -->
    <main id="main-report">
        <!-- for each course selected,  -->
        <?php foreach ($coursesToReport as $courseId): ?>
            <?php
            $course = getCourse($pdo, $courseId); // get the course of the id
            $courseModules = getCourseModules($pdo, $courseId); // get the modules associated with the course
            $start_dates = json_decode($course['start_dates'], true); // format start dates
            $created_at = $date = new DateTime($course['created_at']); //convert date to datetime object
            ?>
            <!-- store the course id & name as data attributes (to be used later in js script) -->
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
                                    <!-- convert the array into comma-separated string-->
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Date Added</td>
                                <td class="cell-with-list">
                                    <?php echo $created_at->format('d/m/Y') ?>
                                    <!-- format date-->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- store the course details & modules as data attributes (to be used later in js script to show full course info) -->
                    <button type="button" class="view-more"
                        data-details="<?php echo htmlspecialchars(json_encode($course), ENT_QUOTES, 'UTF-8') ?>"
                        data-modules="<?php echo htmlspecialchars(json_encode($courseModules), ENT_QUOTES, 'UTF-8') ?>">View
                        More
                    </button>
                </div>
                <div class="second-col">
                    <h3> Modules Chart</h3>
                    <div>
                        <!-- if the course at least one module -->
                        <?php if (count($courseModules) > 0): ?>
                            <canvas id="chart<?php echo $courseId ?>"></canvas>
                            <!-- canvas element for each course, with its id-->
                        <?php else: ?>
                            <!-- else sohw message to user -->
                            <p>No modules</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- show only if more than one course was selected -->
        <?php if (count($coursesToReport) > 1): ?>
            <h3 id="combined-header"> Modules Comparison Chart</h3>
            <div class="section-group">
                <canvas id="comparison-chart" style="width: 90%; margin: 0 auto;"></canvas>
                <!-- canvas for the comparison bar chart-->
            </div>
        <?php endif; ?>

        <div class="overlay">
            <!-- overlay element to show further course details on click of view more button -->
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