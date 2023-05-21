$(document).ready(function () {
    $("#checkAll").on("click", function () {
        $(".checkbox").each(function () {
            $(this).prop('checked', $('#checkAll').prop('checked')); // sets the value of the checked ppty of esch of the checkboxes to the val of the head checkbox
        });
    });

    $('.checkbox').change(function () {
        var isAnyUnchecked = $('.checkbox').filter(':not(:checked)').length > 0;
        $('#checkAll').prop('checked', !isAnyUnchecked);
    });

    if ($('#level-select').val() === "Undergraduate" || $('#level-select').val() === "Postgraduate") {
        $('#general-fields').show();
    }
    else {
        $('#general-fields').hide();
    }

    $('#level-select').change(function () {
        showRightFields($(this).val());
    });
})

function showRightFields(type) {
    $('#general-fields').show();
    if (type === "Undergraduate") {
        $('#undergraduate-fields').show();
        $('#postgraduate-fields').hide();
    }
    else if (type === "Postgraduate") {
        $('#undergraduate-fields').hide();
        $('#postgraduate-fields').show();
    }
    else {
        $('#undergraduate-fields').hide();
        $('#postgraduate-fields').hide();
        $('#general-fields').hide();
    }
}
