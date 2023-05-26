<?php
session_start(); // function to start a session/resume an existing one, to retrieve stored session variables (PHP Documentation)

//checks if there is no set "authentication" session variable which means there is no logged in user
if ($_SESSION["authenticated"] !== true) {
    header("Location: index.php");
    exit();
} else {
    require_once('config_db.php'); // include db setup from another PHP file (rohanmittal1366, 2022)
    require_once('functions.php'); // include php script containing functions

    $levelOptions = ["Undergraduate", "Postgraduate"]; // arrays of options for course level
    $startDateOptions = ["February", "June", "September"]; // arrays of options for start dates
    $yearOptions = ["2023/2024"]; // arrays of options for fee year
    
    // array defining required fields
    $requiredFields = ['course-name', 'subject', 'location', 'startDates', 'levelSelect', 'duration-ft', 'fees-year', 'fees-uk-ft', 'fees-intl-ft'];
    $isEdit = false; // variable to check if module is in edit mode
    $error = ""; // variable to show errors
    $success = "";

    // declare variables to be used to save select elements like status selected, year, etc
    $selectedStartDates = [];
    $selectedYear = "";
    $initialSelectedLevel = "";
    $initialSelectedPlacement = 0; // variable to represent boolean (0 or 1) for placement

    //if the page has a query params - id (rep. courseId), it is for editing the course
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $isEdit = true; // set the edit variable to true

        //prepare SQL command to check if a course with the id actually exists
        $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = :id');
        $values = [
            'id' => $id
        ];
        $stmt->execute($values);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        //if a course exists
        if ($result) {
            // save the module data for the select elements
            $initialSelectedLevel = $result['level'];
            $selectedYear = $result['fees_year'];
            $selectedStartDates = json_decode($result['start_dates'], true);
            $initialSelectedPlacement = $result['duration_placement'];
            prepopulateCourseFields($result); // prepopulate the rest of the form fields with the function (from functions.php)
        }
        // if course does not exist
        else {
            header("Location: courselist.php"); // redirect back to course listing page
            exit(); // end script
        }
    }

    // if submit button with name "submit" is clicked (to add/update course) (Eldaw M, 2023)
    if (isset($_POST['submit'])) {
        // save select and checkbox values into variables
        $selectedStartDates = isset($_POST['startDates']) ? $_POST['startDates'] : [];
        $initialSelectedLevel = $_POST['levelSelect'];
        $selectedYear = $_POST['fees-year'];

        $missingFields = checkIfAnyMissing($requiredFields); // use the function defined in functions.php to get missing fields

        if (!empty($missingFields)) { //if the returned array is not empty
            $error = 'Fill ALL required fields - ' . implode(', ', $missingFields); //show error
        } else {
            if ($isEdit === true) { //check if the course is being edited
                //function returns boolean, hence the assignment
                $returned = updateCourseFunc($pdo, $id);
                // check if value is true (show success message), else show error
                $returned === true ? $success = "Successfully updated" : $error = "Another course already exists in the db with the same name";
            } else {
                //function returns an error string if another course exists with the same title, hence the assignment
                $error = insertCourseFunc($pdo);
            }
        }
    }

    // if file is selected for the file input field (TutorialsPoint, )
    if (isset($_FILES['icon-url'])) {
        // get the file parameters
        $fileError = "";
        $file_name = $_FILES['icon-url']['name'];
        $file_size = $_FILES['icon-url']['size'];
        $file_tmp = $_FILES['icon-url']['tmp_name'];
        $file_type = $_FILES['icon-url']['type'];

        move_uploaded_file($file_tmp, $file_name); //function to move the file to the server (i.e. upload to the current file's directory)
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Course Form</title>
    <?php include("imports.html"); ?>  <!-- add imports -->
</head>

<body>
    <?php include("header.html"); ?>  <!-- import header elements -->
    <main>
        <?php
        echo "<p class='error'>" . $error . "</p>"; //if error variable exists, show error here
        ?>
        <?php
        echo "<p class='info'>" . $success . "</p>"; //if success varaible exists, show here
        ?>
        <h3>
            <?php echo $isEdit === true ? 'Update Course Form' : 'New Course Form' ?>  <!-- update header accordingly -->
        </h3>
        <p class="required">*required fields</p>
         <!-- form to target the submit buttons, action is empty to submit to the current page, type allows picking file inputs  -->
        <form action="" method="POST" id="courseform" enctype="multipart/form-data">
            <div class="form-input-wrapper">
                <label for="course-name"><span class="required">*</span>Course Name</label>
                <input type="text" name="course-name"
                    value="<?php echo isset($_POST['course-name']) ? $_POST['course-name'] : ''; ?>" />  <!-- set value if prepopulate value exists -->
            </div>
            <div class="form-input-wrapper">
                <label for="subject"><span class="required">*</span>Subject</label>
                <input type="text" name="subject"
                    value="<?php echo isset($_POST['subject']) ? $_POST['subject'] : ''; ?>" />
            </div>
            <div class="form-input-wrapper">
                <label for="location"><span class="required">*</span>Location</label>
                <input type="text" name="location"
                    value="<?php echo isset($_POST['location']) ? $_POST['location'] : ''; ?>" />
            </div>
            <div class="form-input-wrapper">
                <label for="startDates"><span class="required">*</span>Start Dates:</label>
                <?php
                foreach ($startDateOptions as $start) {
                    // if prepopulated value exists, select the matching option
                    $isselected = in_array($start, $selectedStartDates);
                    echo '<span class="form-check"><input type="checkbox" name="startDates[]"  value="' . $start . '"' . ($isselected ? 'checked' : '') . '>' . $start . '</span>';
                }
                ?>
            </div>
            <div class="form-input-wrapper">
                <label for="level-select"><span class="required">*</span>Choose the level:</label>
                <select id="level-select" name="levelSelect">
                    <option></option>
                    <?php
                    foreach ($levelOptions as $level) {
                        $isSelected = ($level === $initialSelectedLevel) ? "selected" : "";
                        echo "<option value='$level' $isSelected>$level</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- this section is only shown if the course level is undergraduate -->
            <div id="undergraduate-fields"
                style="display: <?php echo $initialSelectedLevel === "Undergraduate" ? "block;" : "none;" ?>">
                <p class="fields-heading">Undergraduate Specific Fields</p>
                <div class="form-input-group">
                    <div>
                        <div class="form-input-wrapper">
                            <label for="ucas-reg">UCAS code (regular)</label>
                            <input type="text" name="ucas-reg"
                                value="<?php echo isset($_POST['ucas-reg']) ? $_POST['ucas-reg'] : ''; ?>" />
                        </div>
                        <div class="form-input-wrapper">
                            <label for="ucas-foundation">UCAS code (with foundation)</label>
                            <input type="text" name="ucas-foundation"
                                value="<?php echo isset($_POST['ucas-foundation']) ? $_POST['ucas-foundation'] : ''; ?>" />
                        </div>
                        <div class="form-input-wrapper">
                            <label for="duration-foundation">Duration (with Foundation)</label>
                            <input type="number" name="duration-foundation" min="0"
                                value="<?php echo isset($_POST['duration-foundation']) ? $_POST['duration-foundation'] : ''; ?>" />
                        </div>
                        <div class="form-input-wrapper">
                            <label for="fees-uk-foundation">UK Fees (Foundation Year)</label>
                            <input type="number" name="fees-uk-foundation" min="0"
                                value="<?php echo isset($_POST['fees-uk-foundation']) ? $_POST['fees-uk-foundation'] : ''; ?>" />
                        </div>
                        <div class="form-input-wrapper">
                            <label for="fees-intl-foundation">International Fees (Foundation Year)</label>
                            <input type="number" name="fees-intl-foundation" min="0"
                                value="<?php echo isset($_POST['fees-intl-foundation']) ? $_POST['fees-intl-foundation'] : ''; ?>" />
                        </div>
                    </div>
                    <div class="second-col">
                        <div class="form-input-wrapper">
                            <label for="req-foundation">Entry Req. Foundation</label>
                            <div class="form-textarea-wrapper">
                                <p class="info">*Enter each requirement on a new line (i.e press the "Enter" key) </p>
                                <textarea name="req-foundation">
                                <?php echo isset($_POST['req-foundation']) ? $_POST['req-foundation'] : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- this section is only shown if the course level is postgraduate -->
            <div id="postgraduate-fields"
                style="display: <?php echo $initialSelectedLevel === "Postgraduate" ? "block;" : "none;" ?>">
                <p class="fields-heading">Postgraduate Specific Fields</p>
                <div class="form-input-group">
                    <div>
                        <div class="form-input-wrapper">
                            <label for="duration-placement">Has Placement?</label>
                            <select name="duration-placement">
                                <option value="0" <?php echo $isSelected = ($initialSelectedPlacement === '0') ? "selected" : ""; ?>>No</option>
                                <option value="1" <?php echo $isSelected = ($initialSelectedPlacement === '1') ? "selected" : ""; ?>>Yes</option>
                            </select>
                        </div>
                        <div class="form-input-wrapper">
                            <label for="fees-placement">Placement Fees</label>
                            <input type="number" name="fees-placement" min="0"
                                value="<?php echo isset($_POST['fees-placement']) ? $_POST['fees-placement'] : ''; ?>" />
                        </div>
                    </div>
                </div>

            </div>

            <!-- show other sections -->
            <div id="general-fields">
                <p class="fields-heading">Others</p>
                <div class="form-input-group">
                    <div>
                        <div class="form-input-wrapper">
                            <label for="icon-url">Course Icon</label>
                            <div class="form-textarea-wrapper">
                                <p class="info img">
                                    <?php echo isset($_FILES['icon-url']['name']) ? $_FILES['icon-url']['name'] : $_POST['icon-url'] ?>
                                </p>
                                <input type="file" name="icon-url" accept="image/png" />
                            </div>
                        </div>
                        <div class="form-input-wrapper">
                            <label for="duration-ft"><span class="required">*</span>Duration (FullTime)</label>
                            <input type="number" name="duration-ft" min="0"
                                value="<?php echo isset($_POST['duration-ft']) ? $_POST['duration-ft'] : ''; ?>" />
                        </div>
                        <div class="form-input-wrapper">
                            <label for="duration-pt">Duration (PartTime)</label>
                            <input type="number" name="duration-pt" min="0"
                                value="<?php echo isset($_POST['duration-pt']) ? $_POST['duration-pt'] : ''; ?>" />
                        </div>
                        <div class="form-input-wrapper">
                            <label for="link-url">Link to website</label>
                            <input type="text" name="link-url"
                                value="<?php echo isset($_POST['link-url']) ? $_POST['link-url'] : ''; ?>" />
                        </div>
                        <div class="form-input-wrapper">
                            <label for="fees-year"><span class="required">*</span>Fees Year</label>
                            <select name="fees-year">
                                <option></option>
                                <?php
                                foreach ($yearOptions as $year) {
                                    $isSelected = ($year === $selectedYear) ? "selected" : "";
                                    echo "<option value='$year' $isSelected>$year</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-input-wrapper">
                            <label for="fees-uk-ft"><span class="required">*</span>UK Fees (FullTime)</label>
                            <input type="number" name="fees-uk-ft" min="0"
                                value="<?php echo isset($_POST['fees-uk-ft']) ? $_POST['fees-uk-ft'] : ''; ?>" />
                        </div>
                        <div class="form-input-wrapper">
                            <label for="fees-uk-pt">UK Fees (PartTime)</label>
                            <input type="number" name="fees-uk-pt" min="0"
                                value="<?php echo isset($_POST['fees-uk-pt']) ? $_POST['fees-uk-pt'] : ''; ?>" />
                        </div>
                        <div class="form-input-wrapper">
                            <label for="fees-intl-ft"><span class="required">*</span>International Fees
                                (FullTime)</label>
                            <input type="number" name="fees-intl-ft" min="0"
                                value="<?php echo isset($_POST['fees-intl-ft']) ? $_POST['fees-intl-ft'] : ''; ?>" />
                        </div>
                        <div class="form-input-wrapper">
                            <label for="fees-intl-pt">International Fees (PartTime)</label>
                            <input type="number" name="fees-intl-pt" min="0"
                                value="<?php echo isset($_POST['fees-intl-pt']) ? $_POST['fees-intl-pt'] : ''; ?>" />
                        </div>
                        <div class="form-input-wrapper">
                            <label for="fees-extras">Fees Extras</label>
                            <div class="form-textarea-wrapper">
                                <p class="info">*Enter each on a new line (i.e press the "Enter" key) </p>
                                <textarea name="fees-extras" height="20">
                                <?php echo isset($_POST['fees-extras']) ? $_POST['fees-extras'] : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="second-col">
                        <div class="form-input-wrapper">
                            <label for="summary">Course Summary</label>
                            <textarea name="summary">
                            <?php echo isset($_POST['summary']) ? $_POST['summary'] : ''; ?></textarea>
                        </div>
                        <div class="form-input-wrapper">
                            <label for="eng-req">English Requirements</label>
                            <textarea name="eng-req">
                            <?php echo isset($_POST['eng-req']) ? $_POST['eng-req'] : ''; ?></textarea>
                        </div>
                        <div class="form-input-wrapper">
                            <label for="highlights">Highlights</label>
                            <div class="form-textarea-wrapper">
                                <p class="info">*Enter each highlight on a new line (i.e press the "Enter" key) </p>
                                <textarea name="highlights">
                                <?php echo isset($_POST['highlights']) ? $_POST['highlights'] : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="form-input-wrapper">
                            <label for="req-summary">Entry Req. Summary</label>
                            <div class="form-textarea-wrapper">
                                <p class="info">*Enter each requirement on a new line (i.e press the "Enter" key) </p>
                                <textarea name="req-summary">
                                <?php echo isset($_POST['req-summary']) ? $_POST['req-summary'] : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="form-input-wrapper">
                            <label for="related">Related Courses</label>
                            <div class="form-textarea-wrapper">
                                <p class="info">*Enter each course name on a new line (i.e press the "Enter" key) </p>
                                <textarea name="related">
                                <?php echo isset($_POST['related']) ? $_POST['related'] : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" name="submit" class="mb-15">
                <?php echo $isEdit === true ? 'Update Course' : 'Add New Course' ?>
            </button>
        </form>
    </main>

    <footer>&copy; CSYM019 2023</footer>
</body>

</html>