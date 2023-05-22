<?php
// Check if the user is authenticated
session_start();
if ($_SESSION["authenticated"] !== true) {
    header("Location: index.php"); // PHP docs
    exit();
} else {
    require_once('config_db.php'); // include db setup (https://www.geeksforgeeks.org/how-to-include-content-of-a-php-file-into-another-php-file/)
    require_once('functions.php');

    if (!isset($_GET['id'])) {
        header("Location: courselist.php"); // PHP docs
        exit();
    } else {
        $course_id = $_GET['id'];
        $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = :id');
        $values = [
            'id' => $course_id
        ];
        $stmt->execute($values);
        $currentCourse = $stmt->fetch(PDO::FETCH_ASSOC);
        $selectedStatus = '';
        $selectedType = '';
        $error = '';
        $success = '';
        $isEdit = false;
        $statusOptions = ['Compulsory', 'Designated'];
        $typeOptions = ['regular', 'dissertation', 'placement'];
        $requiredFields = ['code', 'title', 'credits', 'status'];

        if ($currentCourse) {
            $stmt = $pdo->prepare('SELECT * FROM modules WHERE course_id = :id');
            $values = [
                'id' => $course_id
            ];
            $stmt->execute($values);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (isset($_GET['edit'])) {
                $isEdit = true;
                $selectedModuleCode = $_GET['edit'];
                $stmt = $pdo->prepare('SELECT * FROM modules WHERE module_code = :code');
                $stmt->bindParam(':code', $selectedModuleCode);
                $stmt->execute();
                $currentModule = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($currentModule) {
                    $selectedType = $currentModule['type'];
                    $selectedStage = $currentModule['stage'];
                    $selectedStatus = $currentModule['status'];
                    prepopulateModuleFields($currentModule);
                } else {
                    header("Location: modules.php?id=" . $course_id); // PHP docs
                    exit();
                }
            } else {
                $selectedType = $typeOptions[0];
                $selectedStage = 'stage1';
            }

            if (isset($_POST['submit'])) {
                $missingFields = array_filter(array_map(function ($each) {
                    return empty($_POST[$each]) ? $each : '';
                }, $requiredFields));

                $isAnyMissing = checkIfAnyMissing($requiredFields);

                if (!empty($isAnyMissing)) {
                    $error = 'Fill ALL required fields - ' . implode(', ', $missingFields);
                } else {
                    if ($isEdit === true) {
                        //function returns an error string if another module exists with the updated code, hence the assignment
                        $error = updateModuleFunc($pdo, $course_id, $selectedModuleCode);
                    } else {
                        //function returns boolean, hence the assignment
                        $error = insertModuleFunc($pdo, $course_id);
                    }
                }
            }
            if (isset($_POST['delete'])) {
                $selectedModuleCode = $_POST['moduleToDelete'];
                $stmt = $pdo->prepare('DELETE FROM modules WHERE module_code = :code');
                $values = [
                    'code' => $selectedModuleCode
                ];
                $stmt->execute($values);
                header("Refresh:0");
            }
            if (isset($_POST['cancel'])) {
                header("Location: modules.php?id=" . $course_id); // PHP docs
                exit();
            }
        } else {
            header("Location: courselist.php"); // PHP docs
            exit();
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Modules</title>
    <link rel="stylesheet" href="./layout.css">
</head>

<body>
    <?php include("header.html"); ?>
    <main>
        <div class="section-group">
            <div class="cols">
                <h3>
                    Modules for
                    <?php echo $currentCourse['course_name'] ?>
                </h3>
                <?php if ($results): ?>
                    <table id="courses">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Module Code</th>
                                <th>Title</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody id='table-contents'>
                            <?php foreach ($results as $index => $row) {
                                echo '
                                    <tr>
                                        <td>' . ($index + 1) . '</td>
                                        <td>' . $row['module_code'] . '</td>
                                        <td>' . $row['title'] . '</td>
                                        <form action="" method="POST">
                                        <td>
                                        <a class="linkaction" href="modules.php?id=' . $course_id . '&edit=' . $row['module_code'] . '">Edit</a>
                                        </td>
                                        <td>
                                        <input class="linkaction" type="hidden" name="moduleToDelete" value="' . $row['module_code'] . '">
                                        <input class="linkaction" type="submit" name="delete" value="Delete" />
                                        </td>
                                        </form>
                                    </tr>
                                ';
                            }
                            ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class='error'> No rows available</p>
                <?php endif; ?>
            </div>
            <div class="cols">
                <h3>
                    <?php echo $isEdit === true ? 'Update Module' : 'Add New Module'; ?>
                </h3>
                <?php
                echo "<p class='error'>" . $error . "</p>";
                ?>
                <?php
                echo "<p class='info'>" . $success . "</p>";
                ?>
                <form id="moduleform" action="" method="POST">
                    <div class="form-input-wrapper">
                        <label for="code"><span class="required">*</span>Module Code</label>
                        <input type="text" name="code"
                            value="<?php echo isset($_POST['code']) ? $_POST['code'] : ''; ?>" />
                    </div>
                    <div class="form-input-wrapper">
                        <label for="title"><span class="required">*</span>Title</label>
                        <input type="text" name="title"
                            value="<?php echo isset($_POST['title']) ? $_POST['title'] : ''; ?>" />
                    </div>
                    <?php if ($currentCourse['level'] === "Undergraduate"): ?>
                        <div class="form-input-wrapper">
                            <label for="stage"><span class="required">*</span>Stage</label>
                            <select name="stage">
                                <?php
                                for ($i = 1; $i <= $currentCourse['duration_fulltime']; $i++) {
                                    $isSelected = ($selectedStage === 'stage' . $i . '') ? "selected" : "";
                                    echo "<option value='stage$i' $isSelected>Stage $i</option>";
                                }
                                ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="form-input-wrapper">
                        <label for="credits"><span class="required">*</span>Credits</label>
                        <input type="number" name="credits" min="0"
                            value="<?php echo isset($_POST['credits']) ? $_POST['credits'] : ''; ?>" />
                    </div>
                    <div class="form-input-wrapper">
                        <label for="prereq">Prerequisite</label>
                        <input type="text" name="prereq"
                            value="<?php echo isset($_POST['prereq']) ? $_POST['prereq'] : ''; ?>" />
                    </div>
                    <div class="form-input-wrapper">
                        <label for="status"><span class="required">*</span>Status</label>
                        <select name="status">
                            <option></option>
                            <?php
                            foreach ($statusOptions as $status) {
                                $isSelected = ($status === $selectedStatus) ? "selected" : "";
                                echo "<option value='$status' $isSelected>$status</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <?php if ($currentCourse['level'] === "Postgraduate"): ?>
                        <div class="form-input-wrapper">
                            <label for="type"><span class="required">*</span>Type</label>
                            <select name="type">
                                <?php
                                foreach ($typeOptions as $type) {
                                    $isSelected = ($type === $selectedType) ? "selected" : "";
                                    echo "<option value='$type' $isSelected>$type</option>";
                                }
                                ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="form-input-wrapper">
                        <button type="submit" name="submit">
                            <?php echo $isEdit === true ? 'Update' : 'Add New'; ?>
                        </button>
                        <?php if ($isEdit === true): ?>
                            <button id="cancel" type="submit" name="cancel">
                                Cancel
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>&copy; CSYM019 2023</footer>
</body>

</html>