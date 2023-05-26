<?php
session_start(); // function to start a session/resume an existing one, to retrieve stored session variables (PHP Documentation)

//checks if there is no set "authentication" session variable which means there is no logged in user
if ($_SESSION["authenticated"] !== true) {
    header("Location: index.php");
    exit();
} else {
    require_once('config_db.php'); // include db setup from another PHP file (rohanmittal1366, 2022)
    require_once('functions.php'); // include php script containing functions

    //if the page does not have a query params - id (rep. courseId)
    if (!isset($_GET['id'])) {
        header("Location: courselist.php");
        exit();
    } else {
        $course_id = $_GET['id']; // variable to save the course id
        $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = :id'); //prepared SQL command to check if a course with the id exists
        //store the values to be injected into the prepared statement
        $values = [
            'id' => $course_id
        ];
        $stmt->execute($values); // send the query to the database with the required values
        $currentCourse = $stmt->fetch(PDO::FETCH_ASSOC); // return the results as an associated array with keys (PHP Documentation, )

        // declare variables to be used to save select elements like status selected, error, etc
        $selectedStatus = '';
        $selectedType = '';
        $error = '';
        $isEdit = false; // variable to check if module is in edit mode

        $statusOptions = ['Compulsory', 'Designated']; // arrays of options for module status
        $typeOptions = ['regular', 'dissertation', 'placement']; // arrays of options for module type

        $requiredFields = ['code', 'title', 'credits', 'status']; // array defining required fields

        // if a course with the id from the query params exists
        if ($currentCourse) {
            $stmt = $pdo->prepare('SELECT * FROM modules WHERE course_id = :id'); // prepare SQL command to get all its modules
            $values = [
                'id' => $course_id
            ];
            $stmt->execute($values);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // return the results as an associated array with keys (PHP Documentation, )

            // if the page also has a query params - edit
            if (isset($_GET['edit'])) {
                $isEdit = true; // then page is in edit mode
                $selectedModuleCode = $_GET['edit']; //module code for the course to be edited

                // prepare SQL statement to confirm a module with the code actually exists
                $stmt = $pdo->prepare('SELECT * FROM modules WHERE module_code = :code');
                $stmt->bindParam(':code', $selectedModuleCode);
                $stmt->execute();
                $currentModule = $stmt->fetch(PDO::FETCH_ASSOC);

                // if the module exists for the course
                if ($currentModule) {
                    // save the module data for the select elements
                    $selectedType = $currentModule['type'];
                    $selectedStage = $currentModule['stage'];
                    $selectedStatus = $currentModule['status'];
                    prepopulateModuleFields($currentModule); // prepopulate the rest of the form fields with the function (from functions.php)
                }
                // if module does not exist
                else {
                    header("Location: modules.php?id=" . $course_id); // reload the page without the edit query params
                    exit(); // end script
                }
            }
            // if page is not in edit mode for any module
            else {
                $selectedType = $typeOptions[0]; // set default values for select elements
                $selectedStage = 'stage1';
            }

            // if submit button with name "submit" is clicked (to add/update module) (Eldaw M, 2023)
            if (isset($_POST['submit'])) {
                //if value entered for credits field is at least 0 and not empty
                if ($_POST['credits'] !== "" && $_POST['credits'] >= 0) {
                    $requiredFields = array_values(array_diff($requiredFields, ['credits'])); // remove credits from the required array ( PHP: How to remove specific element from an array?)
                }

                $missingFields = checkIfAnyMissing($requiredFields); // use the function defined in functions.php to get missing fields

                if (!empty($missingFields)) { //if the returned array is not empty
                    $error = 'Fill ALL required fields - ' . implode(', ', $missingFields); // show error
                } else {
                    //check if the module is being edited
                    if ($isEdit === true) {
                        //function returns an error string if another module exists with the updated code, hence the assignment
                        $error = updateModuleFunc($pdo, $course_id, $selectedModuleCode);
                    } else {
                        //function returns an error string if another module already exists with the  code, hence the assignment
                        $error = insertModuleFunc($pdo, $course_id);
                    }
                }
            }

            // if submit button with name "delete" is clicked (to delete module) (Eldaw M, 2023)
            if (isset($_POST['delete'])) {
                $selectedModuleCode = $_POST['moduleToDelete']; // variable to target the code of the module to be deleted

                $stmt = $pdo->prepare('DELETE FROM modules WHERE module_code = :code');
                $values = [
                    'code' => $selectedModuleCode
                ];
                $stmt->execute($values);
                header("Refresh:0"); // refresh page to show update after successfully executing the query
            }

            // if submit button with name "cancel" is clicked (to cancel updating a module)
            if (isset($_POST['cancel'])) {
                header("Location: modules.php?id=" . $course_id); // refresh the page to remove the edit params
                exit(); //end script
            }
        }
        // if no id query params in page url, 
        else {
            header("Location: courselist.php"); //redirect to courselist page
            exit(); // exit script
        }
    }
}
?>

<!-- HTML code -->
<!DOCTYPE html>
<html>

<head>
    <title>Modules</title>
    <link rel="stylesheet" href="./layout.css">
</head>

<body>
    <?php include("header.html"); ?>
    <!-- import header elements -->
    <main>
        <div class="section-group">
            <div class="cols">
                <h3>
                    Modules for
                    <?php echo $currentCourse['course_name'] ?>
                    <!-- show the name of the course that its modules are being listed -->
                </h3>
                <?php if ($results): ?>
                    <!-- if the course has modules -->
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
                            <!-- loop through every module result, and create a table row with the specified columns -->
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
                    <!-- else, show message to user  -->
                    <p class='error'> No rows available</p>
                <?php endif; ?>
            </div>
            <div class="cols">
                <h3>
                    <?php echo $isEdit === true ? 'Update Module' : 'Add New Module'; ?>
                    <!-- update heading accordingly  -->
                </h3>
                <?php
                echo "<p class='error'>" . $error . "</p>"; // show error if variable exists
                ?>
                <form id="moduleform" action="" method="POST">
                    <!-- form to target the submit buttons, action is empty to submit to the current page  -->
                    <div class="form-input-wrapper">
                        <label for="code"><span class="required">*</span>Module Code</label>
                        <input type="text" name="code"
                            value="<?php echo isset($_POST['code']) ? $_POST['code'] : ''; ?>" />
                        <!-- set value if prepopulate value exists -->
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
                                    $isSelected = ($selectedStage === 'stage' . $i . '') ? "selected" : ""; // if prepopulated value exists, select the matching option
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
                    <!-- show this section only if the course for the modules is postgraduate  -->
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
                        <!-- show this button only if the page is in edit mode  -->
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