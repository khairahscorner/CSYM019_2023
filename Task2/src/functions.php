<?php
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
function bindFieldsAndExecute(PDOStatement $stmt)
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

    if ($stmt->execute()) {
        header('Location: courselist.php'); //PHP documentation on header()
        exit();
    }
}
?>