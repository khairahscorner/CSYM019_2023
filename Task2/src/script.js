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

    $('#main-report').on('click', '.view-more', function () {
        //targets all rows in the table and executes the function on click of each
        let selectedCourseDetails = JSON.parse($(this).closest('button').attr('data-details')); // retrieve the data-course attribute value and parse back to json
        let selectedCourseModules = JSON.parse($(this).closest('button').attr('data-modules')); // retrieve the data-modules attribute value and parse back to json
        populateOverlay(selectedCourseDetails, selectedCourseModules);
    });

    $('#close-btn').click(function () { //on click of close button, closes the overlay section
        $('.overlay').fadeOut();
    });

    let allCoursesToPlot = [];
    let allModulesToPlotData = [];
    let allModulesToPlotLabels = [];

    $('.report').each(function () {
        let courseId = $(this).data('id');
        let courseName = $(this).data('coursename');
        let modulesData = $(this).find('.view-more').data('modules');
        if (modulesData.length > 0) {
            let data = [];
            let labels = [];
            let chartId = `chart${courseId}`;

            $.each(modulesData, function (_, module) {
                data.push(module.credits);
                labels.push(module.module_code);
            });
            allCoursesToPlot.push(courseName);
            allModulesToPlotData.push(data);
            allModulesToPlotLabels.push(labels);

            plotChart(chartId, data, labels);
        }
    });

    if ($('.report').length > 1) {
        plotComparisonChart(allCoursesToPlot, allModulesToPlotData, allModulesToPlotLabels);
    }
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

// function to format the startDates array so each start date is shown as different lines within the same td element
function formatStringForCourseStartDates(startDates) {
    let stringToAppendForDates = ""; //string to be returned containing the formatted start dates

    // iterate through the length of the startDates array
    for (let j = 0; j < startDates.length; j++) {
        stringToAppendForDates += `<div>${startDates[j]}</div>`; // create the string for div elements that'd form each line.
    }
    return stringToAppendForDates;  //return string with the final startDates contents
}

//function that updates all UK fees in the table when currency is changed
function updateFees(fees, rate) {
    // Math.ceil rounds the values to the nearest whole number
    $(".uk-fulltime").text(Math.ceil(fees.fullTime * rate));
    if (fees.withFoundation) {
        $(".uk-foundation").text(Math.ceil(fees.withFoundation * rate));
    }
    if (fees.partTime) {
        $(".uk-parttime").text(Math.ceil(fees.partTime * rate));
    }
    $(".intl-fulltime").text(Math.ceil(fees.international.fullTime * rate));
    if (fees.international.partTime) {
        $(".intl-parttime").text(Math.ceil(fees.international.partTime * rate));
    }
    //condition to check if the course has a foundation year option
    if (fees.international.withFoundation) {
        $(".intl-foundation").text(Math.ceil(fees.international.withFoundation * rate));
    }
    //condition to check if the course has a placement year option
    if (fees.withPlacement) {
        $(".placement").text(Math.ceil(fees.withPlacement * rate));
    }
}

function filterModules(modules, str) {
    modules.filter((module) => module.stage === str);
    return modules;
}

// https://www.chartjs.org/docs/latest/charts/doughnut.html
function plotChart(chartId, data, labels) {
    const ctx = document.getElementById(chartId);

    bar_data = {
        label: "Modules",
        data,
        borderWidth: 1,
        borderColor: "#EEEEEE",
        backgroundColor: "#333333",
    };

    new Chart(ctx, {
        type: "pie",
        data: {
            labels,
            datasets: [
                {
                    label: "Credits",
                    data: bar_data.data,
                    hoverOffset: 4,
                },
            ],
        },
        options: {
            radius: 100,
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });
}

//chart.js reference
function plotComparisonChart(courses, modulesData, modulesLabels) {
    const ctx = document.getElementById('comparison-chart');
    console.log(courses[0]);
    console.log(modulesData[0].length);
    console.log(modulesLabels[0][1]);

    let bgColors = ['#5f0808', '#8a4308', '#766c15', '#024619', '#0054a7', '#57228d', '#640b63'];

    let datasets = [];
    for (let i = 0; i < courses.length; i++) {
        let moduleColors = bgColors[i % bgColors.length];
        let dataset = {
            label: courses[i],
            data: modulesData[i],
            backgroundColor: moduleColors,
            borderColor: "#EEEEEE",
            borderWidth: 1
        };
        datasets.push(dataset);
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['chart1', 'chart2', 'chart3'],
            datasets: datasets
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function populateOverlay(selectedCourseDetails, selectedCourseModules) {
    const details = $("#course-content"); // targets the element for displaying more course details
    details.html(""); //resets the element for a new view

    // appends course name and subject area
    details.append(`<h1 class="details-heading">${selectedCourseDetails.course_name} - <span class="subject">${selectedCourseDetails.subject}</span></h1>`)
    details.append(`<div class="divider"></div>`)

    // appends "key facts" heading and select element for currency selection
    details.append(`<h2 class="section-head">
        Key Facts 
        <div id="currency-wrapper">
          <label for="currency">Currency:</label>
          <select name="select-currency" id="currency">
            <option selected value="1">£</option>
            <option value="1.12">€</option>
            <option value="1.24">$</option>
          </select>
        </div></h2>`)

    let rate = parseFloat($("#currency").val()); //get default selected rate (£)

    // appends first section containing "key facts" as a table
    details.append(`<table>
        <thead>
          <tr>
            <th>Level</th>
            <th>Start Dates</th>
            <th>Duration (Years)</th>
            <th>UK Fees (${selectedCourseDetails.fees_year})</th>
            <th>International Fees (${selectedCourseDetails.fees_year})</th>
          </tr>
        </thead>
        <tbody id="table-contents">
          <tr>
          <td>${selectedCourseDetails.level}</td>
          <td>${formatStringForCourseStartDates(JSON.parse(selectedCourseDetails.start_dates))}</td>
          <td>
          <div>Full Time: ${selectedCourseDetails.duration_fulltime}</div>
          ${selectedCourseDetails.duration_parttime ? `<div>Part Time: ${selectedCourseDetails.duration_parttime}</div>` : ''}
          ${selectedCourseDetails.duration_foundation ? `<div>With Foundation: ${selectedCourseDetails.duration_foundation}</div>` : ''}
          ${selectedCourseDetails.duration_placement === 1 ? `<div>Placement Option available</div>` : ''}
          ${selectedCourseDetails.fees_withplacement === 1 ? `<div>Placement Fee: <span class="placement">${selectedCourseDetails.fees_withplacement}</span></div>` : ''}
          </td>
          <td>
          <div>Full Time: <span class="uk-fulltime">${selectedCourseDetails.fees_uk_fulltime}</span></div>
          ${selectedCourseDetails.fees_uk_parttime ? `<div>Part Time: <span class="uk-parttime">${selectedCourseDetails.fees_uk_parttime}</span>` : ''}
          ${selectedCourseDetails.fees_uk_foundation ? `<div>Foundation Year: <span class="uk-foundation">${selectedCourseDetails.fees_uk_foundation}</span></div>` : ''}
          </td>
          <td>
          <div>Full Time: <span class="intl-fulltime">${selectedCourseDetails.fees_intl_fulltime}</span></div>
          ${selectedCourseDetails.fees_intl_parttime ? `<div>Part Time: <span class="intl-parttime">${selectedCourseDetails.fees_intl_parttime}</span>` : ''}
          ${selectedCourseDetails.fees_intl_foundation ? `<div>Foundation Year: <span class="intl-foundation">${selectedCourseDetails.fees_intl_foundation}</span></div>` : ''}
          </td>
          </tr>
        </tbody>
      </table>`)

    // appends Overview section - summary
    if (selectedCourseDetails.summary) {
        details.append(`<h2 class="section-head">Overview</h2>`)
        details.append(`<div class="summary">${selectedCourseDetails.summary}</div>`)
    }

    // appends Highlights section - highlights is an array so .each is used to append each of them
    if (selectedCourseDetails.highlights) {
        details.append(`<h2 class="section-head">Highlights</h2>`)
        let highlights = $('<ul>');
        $.each(JSON.parse(selectedCourseDetails.highlights), function (_, value) {
            highlights.append($('<li>').text(value));
        });
        details.append(highlights);
    };

    // appends Modules section - formatted to show different fields in the modules
    if (selectedCourseModules.length > 0) {
        details.append(`<h2 class="section-head">Modules</h2>`)
        if (selectedCourseDetails.level.toLowerCase() === "postgraduate") { //if course is postgraduate
            let modules = $('<ul>');
            $.each(selectedCourseModules, function (_, module) {
                modules.append($('<li>').html(`${module.title} (${module.module_code}) - ${module.credits} credits: <strong>${module.status}</strong>`));
            });
            details.append(modules);
        }
        else { //if undergraduate
            let lastStage = selectedCourseModules[0].stage; //identify last stage
            let num = lastStage[lastStage.length - 1]; //get number to know max number of stages
            for (let i = 1; i <= num; i++) {
                details.append(`<strong>Stage ${i}:</strong>`);
                let modules = $('<ul>');
                $.each(filterModules(selectedCourseModules, `stage${i}`), function (_, module) {
                    modules.append($('<li>').html(`${module.title} (${module.module_code}) - ${module.credits} credits: <strong>${module.status}</strong>`));
                });
                details.append(modules);
            }
        }
    }

    // appends Entry Requirements section - formatted to show different fields in the Entry Requirements object
    if (selectedCourseDetails.req_summary) {
        details.append(`<h2 class="section-head">Entry Requirements</h2>`)
        if (selectedCourseDetails.level.toLowerCase() === "undergraduate") { //checks if course is postgraduate
            details.append("<div class='sub-head'>Standard:</div>")
        }
        let reqs = $('<ul>');
        $.each(JSON.parse(selectedCourseDetails.req_summary), function (_, req) {
            reqs.append($('<li>').html(req));
        });
        details.append(reqs);
    }
    if (selectedCourseDetails.req_foundation) {
        details.append("<div class='sub-head'>Foundation Year:</div>")
        reqs = $('<ul>');
        $.each(JSON.parse(selectedCourseDetails.req_foundation), function (_, req) {
            reqs.append($('<li>').html(req));
        });
        details.append(reqs);
    }
    if (selectedCourseDetails.english_req) {
        details.append("<div class='sub-head'>English Language Requirements:</div>")
        details.append(`<div class='text'>${selectedCourseDetails.english_req}</div>`)
    }

    // appends relatedCourses section - related courses is an array so .each is used to append each of them
    if (selectedCourseDetails.related_courses) {
        details.append(`<h2 class="section-head">Related Courses</h2>`)
        relatedCourses = $('<ul>');
        $.each(JSON.parse(selectedCourseDetails.related_courses), function (_, course) {
            relatedCourses.append($('<li>').html(course));
        });
        details.append(relatedCourses);
    }

    //event listener for change in currency
    $('#course-content').on('change', '#currency', function () {
        rate = parseFloat($(this).val()); //get the value of the newly selected currency, parseFloat ensures it's a number
        fees = {
            fullTime: selectedCourseDetails.fees_uk_fulltime,
            partTime: selectedCourseDetails.fees_uk_parttime,
            withFoundation: selectedCourseDetails.fees_uk_foundation,
            withPlacement: selectedCourseDetails.fees_withplacement,
            international: {
                fullTime: selectedCourseDetails.fees_intl_fulltime,
                partTime: selectedCourseDetails.fees_intl_parttime,
                withFoundation: selectedCourseDetails.fees_intl_foundation
            }
        }
        updateFees(fees, rate); //calls function to convert all fees with the new selected rate
    });

    $('.overlay').fadeIn(); // show the overlay page with transition 
}
