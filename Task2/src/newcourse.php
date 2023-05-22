<?php
// Check if the user is authenticated
session_start();
if ($_SESSION["authenticated"] !== true) {
    header("Location: index.php"); // PHP docs
    exit();
} else {
    require_once('config_db.php'); // include db setup (https://www.geeksforgeeks.org/how-to-include-content-of-a-php-file-into-another-php-file/)
    require_once('functions.php');

    $levelOptions = ["Undergraduate", "Postgraduate"];
    $startDateOptions = ["February", "June", "September"];
    $isEdit = false;
    $requiredFields = ['course-name', 'subject', 'location', 'startDates', 'levelSelect', 'duration-ft', 'fees-year', 'fees-uk-ft', 'fees-intl-ft', 'summary', 'highlights'];
    $error = "";
    $success = "";

    $selectedStartDates = [];
    $initialSelectedLevel = "";
    $initialSelectedPlacement;

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $isEdit = true;

        $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = :id');
        $values = [
            'id' => $id
        ];
        $stmt->execute($values);
        $result = $stmt->fetch(PDO::FETCH_ASSOC); //(PHP doc) - does it find a row match, return as array with keys
        if ($result) {
            $initialSelectedLevel = $result['level'];
            $selectedStartDates = json_decode($result['start_dates'], true);
            $initialSelectedPlacement = $result['duration_placement'];
            prepopulateCourseFields($result);
        } else {
            header("Location: courselist.php"); // PHP docs
            exit();
        }
    }

    if (isset($_POST['submit'])) {
        $selectedStartDates = isset($_POST['startDates']) ? $_POST['startDates'] : [];
        $initialSelectedLevel = $_POST['levelSelect'];
        $missingFields = array_filter(array_map(function ($each) {
            return empty($_POST[$each]) ? $each : '';
        }, $requiredFields));

        if (!empty($missingFields)) {
            $error = 'Fill ALL required fields - ' . implode(', ', $missingFields);
        } else {
            if ($isEdit === true) {
                //function returns boolean, hence the assignment
                $returned = updateCourseFunc($stmt, $pdo, $id);
                $returned === true ? $success = "Successfully updated" : $error = "Another course already exists in the db with the same name";
            } else {
                //function returns an error string if another course exists with the same title, hence the assignment
                $error = insertCourseFunc($stmt, $pdo);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Course Form</title>
    <link rel="stylesheet" href="./layout.css">
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="script.js" type="text/javascript"></script>
</head>

<body>
    <?php include("header.html"); ?>
    <main>
        <?php
        echo "<p class='error'>" . $error . "</p>";
        ?>
        <?php
        echo "<p class='info'>" . $success . "</p>";
        ?>
        <h3>
            <?php echo $isEdit === true ? 'Update Course Form' : 'New Course Form' ?>
        </h3>
        <p class="required">*required fields</p>
        <form action="" method="POST" id="courseform">
            <div class="form-input-wrapper">
                <label for="course-name"><span class="required">*</span>Course Name</label>
                <input type="text" name="course-name"
                    value="<?php echo isset($_POST['course-name']) ? $_POST['course-name'] : ''; ?>" />
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
                                <p class="info">*Enter each requirement seperated by comma</p>
                                <textarea name="req-foundation">
                                <?php echo isset($_POST['req-foundation']) ? $_POST['req-foundation'] : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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

            <div id="general-fields">
                <p class="fields-heading">Others</p>
                <div class="form-input-group">
                    <div>
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
                            <input type="text" name="fees-year"
                                value="<?php echo isset($_POST['fees-year']) ? $_POST['fees-year'] : ''; ?>" />
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
                                <p class="info">*Enter each, seperated by comma</p>
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
                                <p class="info">*Enter each highlight seperated by comma</p>
                                <textarea name="highlights">
                                <?php echo isset($_POST['highlights']) ? $_POST['highlights'] : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="form-input-wrapper">
                            <label for="req-summary">Entry Req. Summary</label>
                            <div class="form-textarea-wrapper">
                                <p class="info">*Enter each requirement seperated by comma</p>
                                <textarea name="req-summary">
                                <?php echo isset($_POST['req-summary']) ? $_POST['req-summary'] : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="form-input-wrapper">
                            <label for="related">Related Courses</label>
                            <div class="form-textarea-wrapper">
                                <p class="info">*Enter each course name seperated by comma</p>
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