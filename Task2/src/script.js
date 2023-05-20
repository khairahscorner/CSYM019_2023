$(document).ready(function () {
    $("#checkAll").on("click", function () {
        $(".checkbox").each(function () {
            $(this).prop('checked', $('#checkAll').prop('checked')); // sets the value of the checked ppty of esch of the checkboxes to the val of the head checkbox
        });
    });

    $('.edit-btn').on("click", function() {
        let courseToEdit = $(this).data('id');
        header(`Location: newcourse.php?id=${courseToEdit}`);
    });

})
