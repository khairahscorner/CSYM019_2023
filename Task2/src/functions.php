<?php
function prepopulateCourseFields($result)
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
    $_POST['highlights'] = isset($_POST['highlights']) ? $_POST['highlights'] : (is_null($result['highlights']) ? "" : implode("\n", array_map('trim', json_decode($result['highlights'], true))));
    $_POST['req-summary'] = isset($_POST['req-summary']) ? $_POST['req-summary'] : (is_null($result['req_summary']) ? "" : implode("\n", array_map('trim', json_decode($result['req_summary'], true))));
    $_POST['req-foundation'] = isset($_POST['req-foundation']) ? $_POST['req-foundation'] : (is_null($result['req_foundation']) ? "" : implode("\n", array_map('trim', json_decode($result['req_foundation'], true))));
    $_POST['eng-req'] = isset($_POST['eng-req']) ? $_POST['eng-req'] : $result['english_req'];
    $_POST['fees-year'] = isset($_POST['fees-year']) ? $_POST['fees-year'] : $result['fees_year'];
    $_POST['fees-uk-ft'] = isset($_POST['fees-uk-ft']) ? $_POST['fees-uk-ft'] : $result['fees_uk_fulltime'];
    $_POST['fees-uk-pt'] = isset($_POST['fees-uk-pt']) ? $_POST['fees-uk-pt'] : $result['fees_uk_parttime'];
    $_POST['fees-uk-foundation'] = isset($_POST['fees-uk-foundation']) ? $_POST['fees-uk-foundation'] : $result['fees_uk_foundation'];
    $_POST['fees-intl-ft'] = isset($_POST['fees-intl-ft']) ? $_POST['fees-intl-ft'] : $result['fees_intl_fulltime'];
    $_POST['fees-intl-pt'] = isset($_POST['fees-intl-pt']) ? $_POST['fees-intl-pt'] : $result['fees_intl_parttime'];
    $_POST['fees-intl-foundation'] = isset($_POST['fees-intl-foundation']) ? $_POST['fees-intl-foundation'] : $result['fees_intl_foundation'];
    $_POST['fees-placement'] = isset($_POST['fees-placement']) ? $_POST['fees-placement'] : $result['fees_withplacement'];
    $_POST['fees-extras'] = isset($_POST['fees-extras']) ? $_POST['fees-extras'] : (is_null($result['fees_extras']) ? "" : implode("\n", array_map('trim', json_decode($result['fees_extras'], true))));
    $_POST['related'] = isset($_POST['related']) ? $_POST['related'] : (is_null($result['related_courses']) ? "" : implode("\n", array_map('trim', json_decode($result['related_courses'], true))));
}

function bindCourseFieldsAndExecute(PDOStatement $stmt)
{
    $nullVal = null;
    $highlights = (trim($_POST['highlights']) !== "") ? json_encode(array_map('trim', explode("\n", $_POST['highlights']))) : $nullVal;
    $reqsummary = (trim($_POST['req-summary']) !== "") ? json_encode(array_map('trim', explode("\n", $_POST['req-summary']))) : $nullVal;
    $reqf = (trim($_POST['req-foundation']) !== "") ? json_encode(array_map('trim', explode("\n", $_POST['req-foundation']))) : $nullVal;
    $extras = (trim($_POST['fees-extras']) !== "") ? json_encode(array_map('trim', explode("\n", $_POST['fees-extras']))) : $nullVal;
    $related = (trim($_POST['related']) !== "") ? json_encode(array_map('trim', explode("\n", $_POST['related']))) : $nullVal;
    $startDates = json_encode($_POST['startDates']);
    $selectedFileName = ($_FILES['icon-url']['name'] !== "") ? $_FILES['icon-url']['name'] : $_POST['icon-url'];

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
    $stmt->bindParam(28, $nullVal); //faqs, to be edited
    $stmt->bindParam(29, $related);
}

function updateCourseFunc(PDO $pdo, $id)
{
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE trim(course_name) = ? AND id != ?');
    $name = trim($_POST['course-name']);
    $stmt->bindParam(1, $name);
    $stmt->bindParam(2, $id);
    $stmt->execute();
    $matchedAnotherCourse = $stmt->fetch(PDO::FETCH_ASSOC); //(PHP doc) - does it find a row match, return as array with keys
    if ($matchedAnotherCourse) {
        return false;
    } else {
        $stmt = $pdo->prepare('UPDATE courses 
                SET level = ?, ucas_regular = ?, ucas_foundation = ?, duration_fulltime = ?, duration_parttime = ?, duration_foundation = ?, duration_placement = ?, 
                    start_dates = ?, location = ?, icon_url = ?, course_name = ?, subject = ?, link_url = ?, summary = ?, highlights = ?, req_summary = ?, req_foundation = ?, 
                    english_req = ?, fees_year = ?, fees_uk_fulltime = ?, fees_uk_parttime = ?, fees_uk_foundation = ?, fees_intl_fulltime = ?, fees_intl_parttime = ?, 
                    fees_intl_foundation = ?, fees_withplacement = ?, fees_extras = ?, faqs = ?, related_courses = ?
                WHERE id = ?');
        bindCourseFieldsAndExecute($stmt);
        $stmt->bindParam(30, $id);
        if ($stmt->execute()) {
            return true;
        }
    }
}

function insertCourseFunc(PDO $pdo)
{
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE course_name = :cname');
    $values = [
        "cname" => $_POST['course-name']
    ];
    $stmt->execute($values);
    $courseExists = $stmt->fetch(PDO::FETCH_ASSOC); //(PHP doc) - does it find a row match, return as array with keys
    if ($courseExists) {
        return "Another course already exists in the db with the same name";
    } else {
        $stmt = $pdo->prepare('INSERT INTO courses 
                    (level, ucas_regular, ucas_foundation, duration_fulltime, duration_parttime, duration_foundation, duration_placement,
                        start_dates, location, icon_url, course_name, subject, link_url, summary, highlights, req_summary, req_foundation, 
                        english_req, fees_year, fees_uk_fulltime, fees_uk_parttime, fees_uk_foundation, fees_intl_fulltime, fees_intl_parttime, 
                        fees_intl_foundation, fees_withplacement, fees_extras, faqs, related_courses)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        bindCourseFieldsAndExecute($stmt);
        if ($stmt->execute()) {
            header("Location: courselist.php"); //PHP documentation on header()
            exit();
        }
    }
}

function bindModuleFieldsAndExecute(PDOStatement $stmt, $course_id)
{
    $nullVal = null;
    $stmt->bindParam(1, $_POST['code']);
    $stmt->bindValue(2, (($_POST['stage'] !== "") ? $_POST['stage'] : $nullVal));
    $stmt->bindParam(3, $_POST['title']);
    $stmt->bindParam(4, $_POST['credits']);
    $stmt->bindParam(5, $_POST['status']);
    $stmt->bindParam(6, $_POST['type']);
    $stmt->bindValue(7, ((trim($_POST['prereq']) !== "") ? $_POST['prereq'] : $nullVal));
    $stmt->bindParam(8, $course_id);
}

function prepopulateModuleFields($result)
{
    $_POST['code'] = isset($_POST['code']) ? $_POST['code'] : $result['module_code'];
    $_POST['title'] = isset($_POST['title']) ? $_POST['title'] : $result['title'];
    $_POST['credits'] = isset($_POST['credits']) ? $_POST['credits'] : $result['credits'];
    $_POST['prereq'] = isset($_POST['prereq']) ? $_POST['prereq'] : $result['prereq'];
    $_POST['stage'] = isset($_POST['stage']) ? $_POST['stage'] : $result['stage'];
    $_POST['status'] = isset($_POST['status']) ? $_POST['status'] : $result['status'];
    $_POST['type'] = isset($_POST['type']) ? $_POST['type'] : $result['type'];
}

//(PHP doc) - does it find a row match, return as array with keys
function updateModuleFunc(PDO $pdo, $course_id, $selectedModuleCode)
{
    $stmt = $pdo->prepare('SELECT * FROM modules WHERE module_code = ? AND module_code != ?');
    $stmt->bindParam(1, $_POST['code']);
    $stmt->bindParam(2, $selectedModuleCode);
    $stmt->execute();
    $matchedAnotherModule = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($matchedAnotherModule) {
        return "Module already exists";
    } else {
        $stmt = $pdo->prepare('UPDATE modules 
                            SET module_code = ?, stage = ?, title = ?, credits = ?, status = ?, type = ?, prereq = ?, course_id = ?
                            WHERE module_code = ?');
        bindModuleFieldsAndExecute($stmt, $course_id);
        $stmt->bindParam(9, $selectedModuleCode);
        if ($stmt->execute()) {
            header("Location: modules.php?id=" . $course_id); //PHP documentation on header()
            exit();
        }
    }
}

function insertModuleFunc(PDO $pdo, $course_id)
{
    $stmt = $pdo->prepare('SELECT * FROM modules WHERE module_code = :code');
    $values = [
        "code" => $_POST['code']
    ];
    $stmt->execute($values);
    $moduleExists = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($moduleExists) {
        return "Module already exists";
    } else {
        $stmt = $pdo->prepare('INSERT INTO modules VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        bindModuleFieldsAndExecute($stmt, $course_id);
        if ($stmt->execute()) {
            header("Refresh:0"); //PHP documentation on header()    
            exit();
        }
    }
}

function checkIfAnyMissing($requiredFields)
{
    $missingFields = array_filter(array_map(function ($each) {
        return empty($_POST[$each]) ? $each : '';
    }, $requiredFields));

    return $missingFields;
}

?>