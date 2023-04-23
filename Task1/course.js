/**
 * This function is the main function to run that populates the table element in the html file with data from the json file.
 * It is also used in the function that is responsible for the live updates of the courses.
 */
function populateTable() {
  $.ajax({ // jquery method for ajax requests
    url: "course.json",
    type: "GET",
    dataType: "json",
  })
    .done(function (response) { //replacement method for success() in jquery >3.0
      const tableElement = $("#courses"); // get table element
      const messageArea = $("#message"); //get HTML element with id "message"
      const courseList = $("#table-contents"); //get tbody for the data listing

      courseList.html(""); //reset table view to ensure table is already refreshed with new data

      messageArea.html(""); //reset the inner contents of the message element
      messageArea.css("display", "none"); //changes the display of the message element to hide the element since there are no errors
      $("#table-section").css("display", "block"); //changes the display of the table section to ensure the table and note shows

      // the .each function is used to process each course and create row of data for them
      $.each(response.courses, function (index, course) {
        let currentRow = $("<tr>"); //create a row
        currentRow.addClass("view");
        currentRow.attr("data-details", JSON.stringify(course)); // save the course details in an attribute for future use

        currentRow.append(`<td>${index + 1}</td>`); //first column is for numbering the rows according to index

        //second column is for course logos, which are image urls in the data, the title attribute is added to show the subject area when the icon is hovered on; the image element also includes a class for css styling
        currentRow.append(`<td><img src="icons/${course.courseDetails.icon}" alt="course logo" title="${course.courseDetails.subject}" class="table-icon"/></td>`);

        currentRow.append(`<td>${course.courseDetails.courseName}</td>`); //third column is for course names
        currentRow.append(`<td>${course.keyFacts.level}</td>`); // 4th column to show level whether postgraduate or undergraduate

        let stringToAppendForDates = formatStringForCourseStartDates(course.keyFacts.startDates); //function to format the startDates array that returns a string
        currentRow.append(`<td class="cell-with-list">${stringToAppendForDates}</td>`); //5th column shows the available start dates for the course

        currentRow.append(`<td>${course.keyFacts.location}</td>`); //6th column is for course delivery location

        let stringToAppendForDuration = formatStringForCourseDuration(course.keyFacts.duration); //function to format the duration object that returns a string
        currentRow.append(`<td class="cell-with-list">${stringToAppendForDuration}</td>`); //7th column shows the available duration for the course

        courseList.append(currentRow); //append the row to the tbody
      })

      tableElement.append(courseList);   //append the tbody to the table
    })
    .fail(function () { //replacement method for error() in jquery >3.0
      $("#message").css("display", "inline-block"); //changes the display to show the error messages
      $("#message").html("Could not load table. Please try again later"); //change the inner contents of the element
      $("#table-section").css("display", "none"); // hides the table section
    });
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

//function to format the duration such that each course duration is shown as different lines within the same td element
function formatStringForCourseDuration(duration) {
  let stringToAppendForDuration = ""; //string to be returned containing the formatted duration

  //condition to check if value for full time is 1 so the string is "1 year", while others take "years"
  stringToAppendForDuration += `<div>Full Time: ${duration.fullTime == 1
    ? `${duration.fullTime} year`
    : `${duration.fullTime} years`
    }</div>`; // ternary operator that checks if the expression is true, selects the first option if true, and the second one if not

  //condition to check if part time exists and if the value is 1 so the string is "1 year", while others take "years"
  if (duration.partTime) {
    stringToAppendForDuration += `<div>Part Time: ${duration.partTime == 1
      ? `${duration.partTime} year`
      : `${duration.partTime} years`
      }</div>`; // ternary operator that checks if the expression is true, selects the first option if true, and the second one if not
  }

  //condition to check if the course has a foundation year option
  if (duration.withFoundation) {
    stringToAppendForDuration += `<div>With Foundation: ${duration.withFoundation} years</div>`;
  }

  //condition to check if the course has a placement year option
  if (duration.withPlacement) {
    stringToAppendForDuration += "<div>Placement option available</div>";
  }

  return stringToAppendForDuration; //return string with the final duration contents
}

//function to format the UK fees in the overlay page for each course
function formatStringForUKFees(fees) {
  let stringToAppendForFees = ""; //string to be returned
  stringToAppendForFees += `<div>Full Time: <span class="uk-fulltime">${fees.fullTime}</span></div>`;

  //condition to check if the course has a foundation year option
  if (fees.withFoundation) {
    stringToAppendForFees += `<div>Foundation Year: <span class="uk-foundation">${fees.withFoundation}</span></div>`;
  }
  //condition to check if part time exists
  if (fees.partTime) {
    stringToAppendForFees += fees.partTime.length ? (
      `<div>Part Time: <span class="uk-parttime1">${fees.partTime[0]}</span> (Year 1), <span class="uk-parttime2">${fees.partTime[1]}</span> (Year 2)</div>`
    ) : (
      `<div>Part Time: <span class="uk-parttime">${fees.partTime}</span> per 20 credits</div>`
    )
  }
  return stringToAppendForFees; //return string
}

//function to format the international fees in the overlay page for each course
function formatStringForInternationalFees(fees) {
  let stringToAppendForFees = ""; //string to be returned
  stringToAppendForFees += `<div>Full Time: <span class="intl-fulltime">${fees.international.fullTime}</span></div>`;

  //condition to check if part time exists
  if (fees.international.partTime) {
    stringToAppendForFees += `<div>Part Time: <span class="intl-parttime">${fees.international.partTime}</span></div>`
  }
  //condition to check if the course has a foundation year option
  if (fees.international.withFoundation) {
    stringToAppendForFees += `<div>Foundation Year: <span class="intl-foundation">${fees.international.withFoundation}</span></div>`;
  }
  //condition to check if the course has a placement year option
  if (fees.withPlacement) {
    stringToAppendForFees += `<div>Placement: <span class="placement">${fees.withPlacement}</span></div>`;
  }
  return stringToAppendForFees; //return string
}

//function that updates all UK fees in the table when currency is changed
function updateUKFees(fees, rate) {
  // Math.ceil rounds the values to the nearest whole number
  $(".uk-fulltime").text(Math.ceil(fees.fullTime * rate));
  if (fees.withFoundation) {
    $(".uk-foundation").text(Math.ceil(fees.withFoundation * rate));
  }
  if (fees.partTime) {
    if (fees.partTime.length) {
      $(".uk-parttime1").text(Math.ceil(fees.partTime[0] * rate));
      $(".uk-parttime2").text(Math.ceil(fees.partTime[1] * rate));
    }
    else {
      $(".uk-parttime").text(Math.ceil(fees.partTime * rate));
    }
  }
}

//function that updates all international fees in the table when currency is changed
function updateInternationalFees(fees, rate) {
  // Math.ceil rounds the values to the nearest whole number
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

//main jquery function
$(document).ready(function () {
  populateTable(); // used to load the table immediately the page is loaded, without any DELAY

  // function for updating the table at specific intervals
  (function updateTableAtIntervals() {
    setTimeout(function () {  //settimeout method that executes every 5 minutes
      console.log("Now updating"); //sample message to show when table is updated
      populateTable(); // the function that populates the table
      updateTableAtIntervals(); // the function calls itself here, creating a recursive cycle
    }, 300000);
  })(); //the function is also self-executing since it is invoked via the () and keeps executing from the recursion

  $('#table-contents').on('click', '.view', function () { //targets all rows in the table and executes the function on click of each
    let selectedCourseDetails = JSON.parse($(this).closest('tr').attr('data-details')); // retrieve the data-course attribute value and parse back to json

    const details = $("#course-content"); // targets the element for displaying more course details
    details.html(""); //resets the element for a new view

    // appends course name and subject area
    details.append(`<h1 class="details-heading">${selectedCourseDetails.courseDetails.courseName} - <span class="subject">${selectedCourseDetails.courseDetails.subject}</span></h1>`)
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
          <th>Duration</th>
          <th>UK Fees</th>
          <th>International Fees</th>
        </tr>
      </thead>
      <tbody id="table-contents">
        <tr>
          <td>${selectedCourseDetails.keyFacts.level}</td>
          <td>${formatStringForCourseStartDates(selectedCourseDetails.keyFacts.startDates)}</td>
          <td>${formatStringForCourseDuration(selectedCourseDetails.keyFacts.duration)}</td>
          <td class="inner">${formatStringForUKFees(selectedCourseDetails.fees.uk)}</td>
          <td>${formatStringForInternationalFees(selectedCourseDetails.fees)}</td>
        </tr>
      </tbody>
    </table>`)

    // appends Overview section - summary of course
    details.append(`<h2 class="section-head">Overview</h2>`)
    details.append(`<div class="summary">${selectedCourseDetails.courseDetails.summary}</div>`)

    // appends Highlights section - highlights is an array so .each is used to append each of them
    details.append(`<h2 class="section-head">Highlights</h2>`)
    let highlights = $('<ul>');
    $.each(selectedCourseDetails.courseDetails.highlights, function (index, value) {
      highlights.append($('<li>').text(value));
    });
    details.append(highlights);

    // appends Modules section - formatted to show different fields in the modules
    details.append(`<h2 class="section-head">Modules</h2>`)
    if (selectedCourseDetails.courseDetails.modules.length) { //if course is postgraduate
      details.append("<strong>Stage 1:</strong>")
      let modules = $('<ul>');
      $.each(selectedCourseDetails.courseDetails.modules, function (index, module) {
        modules.append($('<li>').html(`${module.title} (${module.moduleCode}) - ${module.credits} credits: <strong>${module.status}</strong>`));
      });
      details.append(modules);
    }
    else { //if undergraduate
      details.append("<strong>Stage 1:</strong>")
      let modules = $('<ul>');
      $.each(selectedCourseDetails.courseDetails.modules.stage1, function (index, module) {
        modules.append($('<li>').html(`${module.title} (${module.moduleCode}) - ${module.credits} credits: <strong>${module.status}</strong>`));
      });
      details.append(modules);

      details.append("<strong>Stage 2:</strong>")
      modules = $('<ul>');
      $.each(selectedCourseDetails.courseDetails.modules.stage2, function (index, module) {
        modules.append($('<li>').html(`${module.title} (${module.moduleCode}) - ${module.credits} credits: <strong>${module.status}</strong>`));
      });
      details.append(modules);

      details.append("<strong>Stage 3:</strong>")
      modules = $('<ul>');
      $.each(selectedCourseDetails.courseDetails.modules.stage3, function (index, module) {
        modules.append($('<li>').html(`${module.title} (${module.moduleCode}) - ${module.credits} credits: <strong>${module.status}</strong>`));
      });
      details.append(modules);
    }

    // appends Entry Requirements section - formatted to show different fields in the Entry Requirements object
    details.append(`<h2 class="section-head">Entry Requirements</h2>`)
    if (selectedCourseDetails.keyFacts.level == "Postgraduate") { //checks if course is postgraduate
      details.append(`<div>${selectedCourseDetails.entryRequirements.summary}</div>`)
      details.append("<div class='sub-head'>English Language Requirements:</div>")
      details.append(`<div>${selectedCourseDetails.entryRequirements.englishReq}</div>`)
    }
    else { //for undergraduate
      details.append("<div class='sub-head'>Standard:</div>")
      let reqs = $('<ul>');
      $.each(selectedCourseDetails.entryRequirements.summary, function (index, req) {
        reqs.append($('<li>').html(req));
      });
      details.append(reqs);

      details.append("<div class='sub-head'>Foundation Year:</div>")
      reqs = $('<ul>');
      $.each(selectedCourseDetails.entryRequirements.withFoundation, function (index, req) {
        reqs.append($('<li>').html(req));
      });
      details.append(reqs);
      details.append("<div class='sub-head'>English Language Requirements:</div>")
      details.append(`<div>${selectedCourseDetails.entryRequirements.englishReq}</div>`)
    }

    // appends FAQs section - FAQs is an array of question/answer objects so .each is used to append each of them
    details.append(`<h2 class="section-head">FAQs</h2>`)
    faqs = $('<div>');
    $.each(selectedCourseDetails.faqs, function (index, qna) {
      faqs.append(`<div class="question">${index + 1}) ${qna.question} </div>`); //shows question
      faqs.append(`<div class="answer">${qna.answer}`); //shows answer
    });
    details.append(faqs);

    // appends relatedCourses section - related courses is an array so .each is used to append each of them
    details.append(`<h2 class="section-head">Related Courses</h2>`)
    relatedCourses = $('<ul>');
    $.each(selectedCourseDetails.relatedCourses, function (index, course) {
      relatedCourses.append($('<li>').html(course));
    });
    details.append(relatedCourses);

    //appends button that links to school course page
    details.append(`<button><a href="${selectedCourseDetails.courseDetails.url}" target="_blank">View More</a></button>`);

    //event listener for change in currency
    $('#course-content').on('change', '#currency', function () {
      rate = parseFloat($(this).val()); //get the value of the newly selected currency, parseFloat ensures it's a number
      updateUKFees(selectedCourseDetails.fees.uk, rate); //calls function to convert uk fees with the new selected rate
      updateInternationalFees(selectedCourseDetails.fees, rate) //calls function to convert international fees with the new selected rate
    });

    $('.overlay').fadeIn(); // show the overlay page with transition 
  });

  $('#close-btn').click(function () { //on click of close button, closes the overlay section
    $('.overlay').fadeOut();
  });
})
