<?php
// function to prepopulate the course form fileds withexisting course data when editing a course, takes the course details as param
function prepopulateCourseFields($result)
{
    //check if user has edited each field, if so, store the _POST value as the new value; if not, store and show the prepopulated value
    // same applies to all fields
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
    $_POST['eng-req'] = isset($_POST['eng-req']) ? $_POST['eng-req'] : $result['english_req'];
    $_POST['fees-year'] = isset($_POST['fees-year']) ? $_POST['fees-year'] : $result['fees_year'];
    $_POST['fees-uk-ft'] = isset($_POST['fees-uk-ft']) ? $_POST['fees-uk-ft'] : $result['fees_uk_fulltime'];
    $_POST['fees-uk-pt'] = isset($_POST['fees-uk-pt']) ? $_POST['fees-uk-pt'] : $result['fees_uk_parttime'];
    $_POST['fees-uk-foundation'] = isset($_POST['fees-uk-foundation']) ? $_POST['fees-uk-foundation'] : $result['fees_uk_foundation'];
    $_POST['fees-intl-ft'] = isset($_POST['fees-intl-ft']) ? $_POST['fees-intl-ft'] : $result['fees_intl_fulltime'];
    $_POST['fees-intl-pt'] = isset($_POST['fees-intl-pt']) ? $_POST['fees-intl-pt'] : $result['fees_intl_parttime'];
    $_POST['fees-intl-foundation'] = isset($_POST['fees-intl-foundation']) ? $_POST['fees-intl-foundation'] : $result['fees_intl_foundation'];
    $_POST['fees-placement'] = isset($_POST['fees-placement']) ? $_POST['fees-placement'] : $result['fees_withplacement'];

    /* for fields stored in db as json, ensure to decode back to php JSON objects(arrays), format (trim, join as string using new line separator) or show empty string if field is null in db
    - implode is used to join elements in an array as string (PHP Documentation, )
    - json_decode is used to decode the db field to JSON object */
    $_POST['highlights'] = isset($_POST['highlights']) ? $_POST['highlights'] : (is_null($result['highlights']) ? "" : implode("\n", array_map('trim', json_decode($result['highlights'], true))));
    $_POST['req-summary'] = isset($_POST['req-summary']) ? $_POST['req-summary'] : (is_null($result['req_summary']) ? "" : implode("\n", array_map('trim', json_decode($result['req_summary'], true))));
    $_POST['req-foundation'] = isset($_POST['req-foundation']) ? $_POST['req-foundation'] : (is_null($result['req_foundation']) ? "" : implode("\n", array_map('trim', json_decode($result['req_foundation'], true))));
    $_POST['fees-extras'] = isset($_POST['fees-extras']) ? $_POST['fees-extras'] : (is_null($result['fees_extras']) ? "" : implode("\n", array_map('trim', json_decode($result['fees_extras'], true))));
    $_POST['related'] = isset($_POST['related']) ? $_POST['related'] : (is_null($result['related_courses']) ? "" : implode("\n", array_map('trim', json_decode($result['related_courses'], true))));
}

// function to store and bind the values to be injected into the prepared statements for the course form
function bindCourseFieldsAndExecute(PDOStatement $stmt)
{
    $nullVal = null;
    /* for fields to be stored in db as json, they are formatted (trim, converted to array using new line separator) or stored as null if field input is empty; then encoded to JSON string
    - explode function is used to split string into an array (PHP Documentation, )
    - array_map is used to return an array after applying a function to each element(in this case, trimming the strings)
    - json_encode used to convert the array to JSON string */
    $startDates = json_encode($_POST['startDates']);
    $highlights = (trim($_POST['highlights']) !== "") ? json_encode(array_map('trim', explode("\n", $_POST['highlights']))) : $nullVal;
    $reqsummary = (trim($_POST['req-summary']) !== "") ? json_encode(array_map('trim', explode("\n", $_POST['req-summary']))) : $nullVal;
    $reqf = (trim($_POST['req-foundation']) !== "") ? json_encode(array_map('trim', explode("\n", $_POST['req-foundation']))) : $nullVal;
    $extras = (trim($_POST['fees-extras']) !== "") ? json_encode(array_map('trim', explode("\n", $_POST['fees-extras']))) : $nullVal;
    $related = (trim($_POST['related']) !== "") ? json_encode(array_map('trim', explode("\n", $_POST['related']))) : $nullVal;

    // select file name for the file input field, this is what is saved in the db
    $selectedFileName = ($_FILES['icon-url']['name'] !== "") ? $_FILES['icon-url']['name'] : $_POST['icon-url'];

    /* bind the values to question-mark placeholders to insert into the prepared statement (PHP documentation, )
    format some values to be stored as null if no user input */
    $stmt->bindParam(1, $_POST['levelSelect']);
    $stmt->bindValue(2, (($_POST['ucas-reg'] !== "") ? $_POST['ucas-reg'] : $nullVal));
    $stmt->bindValue(3, (($_POST['ucas-foundation'] !== "") ? $_POST['ucas-foundation'] : $nullVal));
    $stmt->bindValue(4, (($_POST['duration-ft'] !== "") ? $_POST['duration-ft'] : $nullVal));
    $stmt->bindValue(5, (($_POST['duration-pt'] !== "") ? $_POST['duration-pt'] : $nullVal));
    $stmt->bindValue(6, (($_POST['duration-foundation'] !== "") ? $_POST['duration-foundation'] : $nullVal));
    $stmt->bindParam(7, $_POST['duration-placement']);
    $stmt->bindParam(8, $startDates);
    $stmt->bindValue(9, (($_POST['location'] !== "") ? $_POST['location'] : $nullVal));
    $stmt->bindParam(10, $selectedFileName);
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
    $stmt->bindParam(28, $related);
}

/* function for updating a course - called in newcourse.php
takes the PDO object and the id of the course to be updated as param */
function updateCourseFunc(PDO $pdo, $id)
{
    // prepare SQL command to check for whether a course exists with the same name and the id is not the same as the course being updated
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE trim(course_name) = ? AND id != ?');
    $name = trim($_POST['course-name']); // format the course name of any trailing spaces

    // bind the values for the placeholders in the statement
    $stmt->bindParam(1, $name);
    $stmt->bindParam(2, $id);
    $stmt->execute(); // send the query to the database
    $matchedAnotherCourse = $stmt->fetch(PDO::FETCH_ASSOC); // return the results as an associated array with keys (PHP Documentation, )

    // if exists, return false to show update was not made
    if ($matchedAnotherCourse) {
        return false;
    }
    // else, course can be updated
    else {
        // prepare SQL command to use for the update
        $stmt = $pdo->prepare('UPDATE courses 
                SET level = ?, ucas_regular = ?, ucas_foundation = ?, duration_fulltime = ?, duration_parttime = ?, duration_foundation = ?, duration_placement = ?, 
                    start_dates = ?, location = ?, icon_url = ?, course_name = ?, subject = ?, link_url = ?, summary = ?, highlights = ?, req_summary = ?, req_foundation = ?, 
                    english_req = ?, fees_year = ?, fees_uk_fulltime = ?, fees_uk_parttime = ?, fees_uk_foundation = ?, fees_intl_fulltime = ?, fees_intl_parttime = ?, 
                    fees_intl_foundation = ?, fees_withplacement = ?, fees_extras = ?, related_courses = ?
                WHERE id = ?');
        bindCourseFieldsAndExecute($stmt); // run function to bind the values
        $stmt->bindParam(29, $id); // bind last param for the course id to be updated
        // if sending the query to db returns a success, return a true boolean to show update is successful
        if ($stmt->execute()) {
            return true;
        }
    }
}

// function to add a new course to db - called in newcourse.php takes the PDO object
function insertCourseFunc(PDO $pdo)
{
    // prepare SQL command to check for whether a course already exists with the same name
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE course_name = :cname');
    //store the values to be injected into the prepared statement
    $values = [
        "cname" => $_POST['course-name']
    ];
    $stmt->execute($values); // send the query to the database with the required values
    $courseExists = $stmt->fetch(PDO::FETCH_ASSOC); // return the results as an associated array with keys (PHP Documentation, )

    // if another course already exists
    if ($courseExists) {
        return "Another course already exists in the db with the same name"; //return error to be shown
    }
    // else, add the new course
    else {
        // SQL command to be used
        $stmt = $pdo->prepare('INSERT INTO courses 
                    (level, ucas_regular, ucas_foundation, duration_fulltime, duration_parttime, duration_foundation, duration_placement,
                        start_dates, location, icon_url, course_name, subject, link_url, summary, highlights, req_summary, req_foundation, 
                        english_req, fees_year, fees_uk_fulltime, fees_uk_parttime, fees_uk_foundation, fees_intl_fulltime, fees_intl_parttime, 
                        fees_intl_foundation, fees_withplacement, fees_extras, related_courses)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        bindCourseFieldsAndExecute($stmt); // run function to bind the values
        // if sending the query to db returns a success, redirect to courselist page which will show the added course
        if ($stmt->execute()) {
            header("Location: courselist.php");
            exit();
        }
    }
}

// function to store and bind the values to be injected into the prepared statements for the module form
function bindModuleFieldsAndExecute(PDOStatement $stmt, $course_id)
{
    $nullVal = null;
    /* bind the values to question-mark placeholders to insert into the prepared statement (PHP documentation, )
    format some values to be stored as null if no user input */
    $stmt->bindParam(1, $_POST['code']);
    $stmt->bindValue(2, (($_POST['stage'] !== "") ? $_POST['stage'] : $nullVal));
    $stmt->bindParam(3, $_POST['title']);
    $stmt->bindParam(4, $_POST['credits']);
    $stmt->bindParam(5, $_POST['status']);
    $stmt->bindParam(6, $_POST['type']);
    $stmt->bindValue(7, ((trim($_POST['prereq']) !== "") ? $_POST['prereq'] : $nullVal));
    $stmt->bindParam(8, $course_id);
}

// function to prepopulate the module form fileds withexisting module data when editing a modue, takes the module details as param
function prepopulateModuleFields($result)
{
    //check if user has edited each field, if so, store the _POST value as the new value; if not, store and show the prepopulated value
    $_POST['code'] = isset($_POST['code']) ? $_POST['code'] : $result['module_code'];
    $_POST['title'] = isset($_POST['title']) ? $_POST['title'] : $result['title'];
    $_POST['credits'] = isset($_POST['credits']) ? $_POST['credits'] : $result['credits'];
    $_POST['prereq'] = isset($_POST['prereq']) ? $_POST['prereq'] : $result['prereq'];
    $_POST['stage'] = isset($_POST['stage']) ? $_POST['stage'] : $result['stage'];
    $_POST['status'] = isset($_POST['status']) ? $_POST['status'] : $result['status'];
    $_POST['type'] = isset($_POST['type']) ? $_POST['type'] : $result['type'];
}

/* function for updating a module - called in modules.php
takes the PDO object, the id of the module to be updated and the id of the course it belongs to, as params */
function updateModuleFunc(PDO $pdo, $course_id, $selectedModuleCode)
{
    // prepare SQL command to check for whether another module exists with the same module code and is not the same as the module being updated
    $stmt = $pdo->prepare('SELECT * FROM modules WHERE module_code = ? AND module_code != ?');
    $stmt->bindParam(1, $_POST['code']); // this binds the current value in the module code field
    $stmt->bindParam(2, $selectedModuleCode); //selectedModule is the current code of module being edited
    $stmt->execute(); // send the query to the database
    $matchedAnotherModule = $stmt->fetch(PDO::FETCH_ASSOC); // return the results as an associated array with keys (PHP Documentation, )
    // if another module exists
    if ($matchedAnotherModule) {
        return "Module already exists"; // return error
    }
    // else, module can be updated
    else {
        // prepare SQL command to use for the update
        $stmt = $pdo->prepare('UPDATE modules 
                            SET module_code = ?, stage = ?, title = ?, credits = ?, status = ?, type = ?, prereq = ?, course_id = ?
                            WHERE module_code = ?');
        bindModuleFieldsAndExecute($stmt, $course_id); // bind the values
        $stmt->bindParam(9, $selectedModuleCode); // bind the last placeholder in the query

        // if sending the query to db returns a success, reload the modules page with the updates
        if ($stmt->execute()) {
            header("Location: modules.php?id=" . $course_id);
            exit();
        }
    }
}

/* function to add a new module to db - called in modules.php
takes the PDO object and id of the course the module belongs to */
function insertModuleFunc(PDO $pdo, $course_id)
{
    // prepare SQL command to check for whether a module already exists with the same code
    $stmt = $pdo->prepare('SELECT * FROM modules WHERE module_code = :code');

    //store the values to be injected into the prepared statement 
    $values = [
        "code" => $_POST['code']
    ];
    $stmt->execute($values); // send the query to the database with the required values
    $moduleExists = $stmt->fetch(PDO::FETCH_ASSOC); // return the results as an associated array with keys (PHP Documentation, )

    // if module already exists
    if ($moduleExists) {
        return "Module already exists"; // return error to be shown
    }
    // else, add new module
    else {
        // SQL command to be used
        $stmt = $pdo->prepare('INSERT INTO modules VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        bindModuleFieldsAndExecute($stmt, $course_id); // run function to bind the values
        // if sending the query to db returns a success, refresh the page
        if ($stmt->execute()) {
            header("Refresh:0");
            exit();
        }
    }
}

// function to check if any elements in an array is empty - array given as params
function checkIfAnyMissing($requiredFields)
{
    // use filter function to use the map callback func. on the array
    $missingFields = array_filter(array_map(function ($each) {
        return empty($_POST[$each]) ? $each : '';
    }, $requiredFields));
    // return an array containing the filtered elements
    return $missingFields;
}

?>