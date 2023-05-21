<?php
// Check if the user is authenticated
session_start();
if ($_SESSION["authenticated"] !== true) {
    header("Location: index.php"); // PHP docs
    exit();
} else {
    require('config_db.php'); // include db setup (https://www.geeksforgeeks.org/how-to-include-content-of-a-php-file-into-another-php-file/)

    $levelOptions = ["Undergraduate", "Postgraduate"];
    $startDateOptions = ["February", "June", "September"];
    $isEdit = false;
    $requiredFields = ['course-name', 'subject', 'location', 'startDates', 'levelSelect', 'duration-ft', 'fees-year', 'fees-uk-ft', 'fees-intl-ft', 'summary', 'highlights'];
    $error = "";

    $selectedStartDates = [];
    $initialSelectedLevel = "";
    $initialSelectedPlacement;

    if (isset($_GET['id']) && isset($_GET['type'])) {
        $id = $_GET['id'];
        $initialSelectedLevel = $_GET['type'];
        $isEdit = true;

        $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = :id');
        $values = [
            'id' => $id
        ];
        $stmt->execute($values);
        $result = $stmt->fetch(PDO::FETCH_ASSOC); //(PHP doc) - does it find a row match, return as array with keys
        if ($result) {
            $selectedStartDates = json_decode($result['start_dates'], true);
            $initialSelectedPlacement = $result['duration_placement'];
            prepopulateFields($result);
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
                $stmt = $pdo->prepare('UPDATE courses 
                SET level = ?, ucas_regular = ?, ucas_foundation = ?, duration_fulltime = ?, duration_parttime = ?, duration_foundation = ?, duration_placement = ?, start_dates = ?, location = ?, icon_url = ?, course_name = ?, subject = ?, link_url = ?, summary = ?, highlights = ?, req_summary = ?, req_foundation = ?, english_req = ?, fees_year = ?, fees_uk_fulltime = ?, fees_uk_parttime = ?, fees_uk_foundation = ?, fees_intl_fulltime = ?, fees_intl_parttime = ?, fees_intl_foundation = ?, fees_withplacement = ?, fees_extras = ?, faqs = ?, related_courses = ?
                WHERE id = ' . $id . '');

                bindFields($stmt);
                if ($stmt->execute()) {
                    header('Location: courselist.php'); //PHP documentation on header()
                    exit();
                }
            } else {
                $stmt = $pdo->prepare('SELECT * FROM courses WHERE course_name = :cname');
                $values = [
                    "cname" => $_POST['course-name']
                ];
                $stmt->execute($values);
                $courseExists = $stmt->fetch(PDO::FETCH_ASSOC); //(PHP doc) - does it find a row match, return as array with keys
                if ($courseExists) {
                    $error = "Course already exists in the db with the same name";
                } else {
                    $stmt = $pdo->prepare('INSERT INTO courses 
                    (level, ucas_regular, ucas_foundation, duration_fulltime, duration_parttime, duration_foundation, duration_placement, start_dates, location, icon_url, course_name, subject, link_url, summary, highlights, req_summary, req_foundation, english_req, fees_year, fees_uk_fulltime, fees_uk_parttime, fees_uk_foundation, fees_intl_fulltime, fees_intl_parttime, fees_intl_foundation, fees_withplacement, fees_extras, faqs, related_courses)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

                    bindFields($stmt);
                    if ($stmt->execute()) {
                        header('Location: courselist.php'); //PHP documentation on header()
                        exit();
                    }
                }
            }
        }
    }

}

function prepopulateFields($result)
{
    $_POST['course-name'] = isset($_POST['course-name']) ? $_POST['course-name'] : $result['course_name'];
    $_POST['subject'] = isset($_POST['subject']) ? $_POST['subject'] : $result['subject'];
    $_POST['location'] = isset($_POST['location']) ? $_POST['location'] : $result['location'];
    $_POST['levelSelect'] = isset($_POST['levelSelect']) ? $_POST['levelSelect'] : $result['level'];
    $_POST['ucas-reg'] = isset($_POST['ucas-reg']) ? $_POST['ucas-reg'] : $result['ucas_regular'];
    $_POST['ucas-foundation'] = isset($_POST['ucas-foundation']) ? $_POST['ucas-foundation'] : $result['ucas_foundation'];
    $_POST['duration-ft'] = isset($_POST['duration-ft']) ? $_POST['duration-ft'] : $result['duration_fulltime'];
    $_POST['duration-pt'] = isset($_POST['duration-pt']) ? $_POST['duration-pt'] : $result['duration_parttime'];
    $_POST['duration-foundation'] = isset($_POST['duration-foundation']) ? $_POST['duration-foundation'] : $result['duration_foundation'];
    $_POST['duration-placement'] = isset($_POST['duration-placement']) ? $_POST['duration-placement'] : $result['duration_placement'];
    $_POST['icon-url'] = isset($_POST['icon-url']) ? $_POST['icon-url'] : $result['icon_url'];
    $_POST['link-url'] = isset($_POST['link-url']) ? $_POST['link-url'] : $result['link_url'];
    $_POST['summary'] = isset($_POST['summary']) ? $_POST['summary'] : $result['summary'];
    $_POST['highlights'] = isset($_POST['highlights']) ? $_POST['highlights'] : (is_null($result['highlights']) ? "" : implode(', ', array_map('trim', json_decode($result['highlights'], true))));
    $_POST['req-summary'] = isset($_POST['req-summary']) ? $_POST['req-summary'] : (is_null($result['req_summary']) ? "" : implode(', ', array_map('trim', json_decode($result['req_summary'], true))));
    $_POST['req-foundation'] = isset($_POST['req-foundation']) ? $_POST['req-foundation'] : (is_null($result['req_foundation']) ? "" : implode(', ', array_map('trim', json_decode($result['req_foundation'], true))));
    $_POST['eng-req'] = isset($_POST['eng-req']) ? $_POST['eng-req'] : $result['english_req'];
    $_POST['fees-year'] = isset($_POST['fees-year']) ? $_POST['fees-year'] : $result['fees_year'];
    $_POST['fees-uk-ft'] = isset($_POST['fees-uk-ft']) ? $_POST['fees-uk-ft'] : $result['fees_uk_fulltime'];
    $_POST['fees-uk-pt'] = isset($_POST['fees-uk-pt']) ? $_POST['fees-uk-pt'] : $result['fees_uk_parttime'];
    $_POST['fees-uk-foundation'] = isset($_POST['fees-uk-foundation']) ? $_POST['fees-uk-foundation'] : $result['fees_uk_foundation'];
    $_POST['fees-intl-ft'] = isset($_POST['fees-intl-ft']) ? $_POST['fees-intl-ft'] : $result['fees_intl_fulltime'];
    $_POST['fees-intl-pt'] = isset($_POST['fees-intl-pt']) ? $_POST['fees-intl-pt'] : $result['fees_intl_parttime'];
    $_POST['fees-intl-foundation'] = isset($_POST['fees-intl-foundation']) ? $_POST['fees-intl-foundation'] : $result['fees_intl_foundation'];
    $_POST['fees-placement'] = isset($_POST['fees-placement']) ? $_POST['fees-placement'] : $result['fees_withplacement'];
    $_POST['fees-extras'] = isset($_POST['fees-extras']) ? $_POST['fees-extras'] : (is_null($result['fees_extras']) ? "" : implode(', ', array_map('trim', json_decode($result['fees_extras'], true))));
    // $_POST['faqs'] = isset($_POST['faqs']) ? $_POST['faqs'] : implode(', ', json_decode($result['faqs'], true));
    $_POST['related'] = isset($_POST['related']) ? $_POST['related'] : (is_null($result['related_courses']) ? "" : implode(', ', array_map('trim', json_decode($result['related_courses'], true))));
}

function bindFields(PDOStatement $stmt)
{
    $nullVal = null;
    $highlights = (trim($_POST['highlights']) !== "") ? json_encode(array_map('trim', explode(",", $_POST['highlights']))) : $nullVal;
    $reqsummary = (trim($_POST['req-summary']) !== "") ? json_encode(array_map('trim', explode(",", $_POST['req-summary']))) : $nullVal;
    $reqf = (trim($_POST['req-foundation']) !== "") ? json_encode(array_map('trim', explode(",", $_POST['req-foundation']))) : $nullVal;
    $extras = (trim($_POST['fees-extras']) !== "") ? json_encode(array_map('trim', explode(",", $_POST['fees-extras']))) : $nullVal;
    $related = (trim($_POST['related']) !== "") ? json_encode(array_map('trim', explode(",", $_POST['related']))) : $nullVal;
    $startDates = json_encode($_POST['startDates']);

    $stmt->bindParam(1, $_POST['levelSelect']);
    $stmt->bindValue(2, (($_POST['ucas-reg'] !== "") ? $_POST['ucas-reg'] : $nullVal));
    $stmt->bindValue(3, (($_POST['ucas-foundation'] !== "") ? $_POST['ucas-foundation'] : $nullVal));
    $stmt->bindValue(4, (($_POST['duration-ft'] !== "") ? $_POST['duration-ft'] : $nullVal));
    $stmt->bindValue(5, (($_POST['duration-pt'] !== "") ? $_POST['duration-pt'] : $nullVal));
    $stmt->bindValue(6, (($_POST['duration-foundation'] !== "") ? $_POST['duration-foundation'] : $nullVal));
    $stmt->bindParam(7, $_POST['duration-placement']);
    $stmt->bindParam(8, $startDates);
    $stmt->bindValue(9, (($_POST['location'] !== "") ? $_POST['location'] : $nullVal));
    $stmt->bindParam(10, $nullVal);
    $stmt->bindParam(11, $_POST['course-name']);
    $stmt->bindParam(12, $_POST['subject']);
    $stmt->bindValue(13, (($_POST['link-url'] !== "") ? $_POST['link-url'] : $nullVal));
    $stmt->bindValue(14, ((trim($_POST['summary']) !== "") ? $_POST['summary'] : $nullVal));
    $stmt->bindParam(15, $highlights);
    $stmt->bindParam(16, $reqsummary);
    $stmt->bindParam(17, $reqf);
    $stmt->bindValue(18, ((trim($_POST['eng-req']) !== "") ? $_POST['eng-req'] : $nullVal));
    $stmt->bindParam(19, $_POST['fees-year']);
    $stmt->bindParam(20, $_POST['fees-uk-ft']);
    $stmt->bindValue(21, (($_POST['fees-uk-pt'] !== "") ? $_POST['fees-uk-pt'] : $nullVal));
    $stmt->bindValue(22, (($_POST['fees-uk-foundation'] !== "") ? $_POST['fees-uk-foundation'] : $nullVal));
    $stmt->bindParam(23, $_POST['fees-intl-ft']);
    $stmt->bindValue(24, (($_POST['fees-intl-pt'] !== "") ? $_POST['fees-intl-pt'] : $nullVal));
    $stmt->bindValue(25, (($_POST['fees-intl-foundation'] !== "") ? $_POST['fees-intl-foundation'] : $nullVal));
    $stmt->bindValue(26, (($_POST['fees-placement'] !== "") ? $_POST['fees-placement'] : $nullVal));
    $stmt->bindParam(27, $extras);
    $stmt->bindParam(28, $nullVal); //faqs, to be edited
    $stmt->bindParam(29, $related);
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
        <!-- <?php
        if (isset($error)) {
            echo "<p class='error'> $error</p>";
        }
        ?> -->
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
                                <p class="required">*Enter each requirement seperated by comma</p>
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
                                <p class="required">*Enter each, seperated by comma</p>
                                <textarea name="fees-extras" height="20">
                                <?php echo isset($_POST['fees-extras']) ? $_POST['fees-extras'] : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="second-col">
                        <div class="form-input-wrapper">
                            <label for="summary"><span class="required">*</span>Course Summary</label>
                            <textarea name="summary">
                            <?php echo isset($_POST['summary']) ? $_POST['summary'] : ''; ?></textarea>
                        </div>
                        <div class="form-input-wrapper">
                            <label for="eng-req">English Requirements</label>
                            <textarea name="eng-req">
                            <?php echo isset($_POST['eng-req']) ? $_POST['eng-req'] : ''; ?></textarea>
                        </div>
                        <div class="form-input-wrapper">
                            <label for="highlights"><span class="required">*</span>Highlights</label>
                            <div class="form-textarea-wrapper">
                                <p class="required">*Enter each highlight seperated by comma</p>
                                <textarea name="highlights">
                                <?php echo isset($_POST['highlights']) ? $_POST['highlights'] : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="form-input-wrapper">
                            <label for="req-summary">Entry Req. Summary</label>
                            <div class="form-textarea-wrapper">
                                <p class="required">*Enter each requirement seperated by comma</p>
                                <textarea name="req-summary">
                                <?php echo isset($_POST['req-summary']) ? $_POST['req-summary'] : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="form-input-wrapper">
                            <label for="related">Related Courses</label>
                            <div class="form-textarea-wrapper">
                                <p class="required">*Enter each course name seperated by comma</p>
                                <textarea name="related">
                                <?php echo isset($_POST['related']) ? $_POST['related'] : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" name="submit">
                <?php echo $isEdit === true ? 'Update Course' : 'Add New Course' ?>
            </button>
        </form>

        <?php
        echo "<p class='error'>" . $error . "</p>";
        ?>
    </main>

    <footer>&copy; CSYM019 2023</footer>
</body>

</html>