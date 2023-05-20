<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Check if the user is authenticated
session_start();
if ($_SESSION["authenticated"] !== true) {
    header("Location: index.php"); // PHP docs
} else {
    require('config_db.php'); // include db setup (https://www.geeksforgeeks.org/how-to-include-content-of-a-php-file-into-another-php-file/)

    if (isset($_POST['submit'])) { // Muawya code
        // $stmt = $pdo->prepare('SELECT * FROM courses WHERE course_name = :cname');
        // $values = [
        //     "cname" => " MSc"
        // ];
        // $stmt->execute($values);
        // $result = $stmt->fetch(PDO::FETCH_ASSOC); //(PHP doc) - does it find a row match, return as array with keys

        // if ($result) {
        //     $error = "Course already exists in the db with the same name";
        // } else {
        $stmt = $pdo->prepare('INSERT INTO courses (level, duration_fulltime, duration_placement, start_dates, location, icon_url, course_name, subject, link_url, summary, highlights, english_req, fees_year, fees_uk_fulltime, fees_intl_fulltime, faqs, related_courses, created_at)
        VALUES (:level, :dft, :dp, :dates, :location, :icon, :cname, :subject, :link, :summary, :highlights, :english_req, :fyear,  :ukft, :intlft, :faqs, :rel, :created)');

        $faqs = [
            ['question' => 'How will I learn?', 'answer' => "Typically you will have 10 hours (approx) of "],
            ['question' => 'How will I be assessed?', 'answer' => "The course uses a combination of assessment methods including reports"],
        ];
        $created = new DateTime();

        $values = [
            'level' => "Postgraduate",
            'dft' => 2,
            'dp' => 1,
            'dates' => json_encode(["September", "February"]),
            'location' => "Waterside",
            "icon" => "accounting.png",
            "cname" => "Accounting MSc",
            "subject" => "Accounting and Finance",
            "link" => "https://www.northampton.ac.uk/courses/financial-and-investment-analysis-msc/",
            "summary" => "MSc Financial and Investment Analysis will develop you into a specialist investment professional that can take up challenging roles in investment banking, risk management and fund management. This programme will prepare you to write the CFA® professional exams. This course has an Industry Placement Option.",
            "highlights" => json_encode([
                "Industry Placement Option available",
                "Hands-on application of skills learnt through simulated trading via the Bloomberg terminals",
                "Exemptions are available from main professional bodies ACCA and CIMA"
            ]),
            "english_req" => "Minimum standard – IELTS 6.5 (or equivalent) for study at postgraduate level",
            "fyear" => "23/24",
            "ukft" => 8010,
            "intlft" => 16500,
            "faqs" => json_encode($faqs),
            "rel" => json_encode(["Accounting and Finance MSc"]),
            "created" => $created->format('Y-m-d H:i:s')
        ];

        $stmt->execute($values);
        // }
    }
    // else {
    //     $error = "Erreor";
    // }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Course Form</title>
    <link rel="stylesheet" href="./layout.css">
</head>

<body>
    <?php include("header.html"); ?>
    <main>
        <!-- <?php
        if (isset($error)) {
            echo "<p class='error'> $error</p>";
        }
        ?> -->
        <h3>New Course Form</h3>
        <div class="sketch">
            <img src="./sampleEntryForm.png" alt="course formt">
        </div>
        <form action="" method="POST" class="addmore">
            <input type="submit" name="submit" value="Add New Course" />
        </form>
    </main>

    <footer>&copy; CSYM019 2023</footer>
</body>

</html>