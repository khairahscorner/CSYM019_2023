/**
 * This function is the main function to run that populates the table element in the html file with data from the json file.
 * It is used in the function that is responsible for the live updates of the courses.
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

/**
 * this function formats the startDates array such that each available start date for the course is shown as different lines within the same td element
 * @param {Array} startDates start dates array
 * @returns string that can be appended to the innerHTML of the current row
 */
function formatStringForCourseStartDates(startDates) {
  let stringToAppendForDates = ""; //string to be returned containing the formatted start dates

  // iterate through the length of the startDates array
  for (let j = 0; j < startDates.length; j++) {
    stringToAppendForDates += `<div>${startDates[j]}</div>`; // create the string for div elements that'd form each line.
  }
  return stringToAppendForDates;  //return string with the final startDates contents
}

/**
 * this function formats the duration object such that the fullTime/partTime/Foundation course duration are shown as different lines within the same td element
 * @param {object} duration course duration object 
 * @returns string that can be appended to the innerHTML of the current row 
 */
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

function formatStringForUKFees(fees, rate) {
  let stringToAppendForFees = ""; //string to be returned
  stringToAppendForFees += `<div>Full Time: ${fees.fullTime * rate}</div>`;
  //condition to check if the course has a foundation year option
  if (fees.withFoundation) {
    stringToAppendForFees += `<div>Foundation Year: ${fees.withFoundation * rate}</div>`;
  }
  //condition to check if part time exists
  if (fees.partTime) {
    stringToAppendForFees += fees.partTime.length ? (
      `<div>Part Time: ${fees.partTime[0] * rate} (Year 1), ${fees.partTime[1] * rate} (Year 2)</div>`
    ) : (
      `<div>Part Time: ${fees.partTime * rate} per 20 credits</div>`
    )
  }
  return stringToAppendForFees; //return string
}

function formatStringForInternationalFees(fees, rate) {
  let stringToAppendForFees = ""; //string to be returned
  stringToAppendForFees += `<div>Full Time: ${fees.international.fullTime * rate}</div>`;

  //condition to check if part time exists
  if (fees.international.partTime) {
    stringToAppendForFees += `<div>Part Time: ${fees.international.partTime * rate}</div>`
  }
  //condition to check if the course has a foundation year option
  if (fees.international.withFoundation) {
    stringToAppendForFees += `<div>Foundation Year: ${fees.international.withFoundation * rate}</div>`;
  }
  //condition to check if the course has a placement year option
  if (fees.withPlacement) {
    stringToAppendForFees += `<div>Placement: ${fees.withPlacement * rate}</div>`;
  }
  return stringToAppendForFees; //return string
}

//main jquery function
$(document).ready(function () {
  populateTable(); // used to load the table immediately the page is loaded, without any DELAY

  // function for updating the table at specific intervals
  (function updateTableAtIntervals() {
    //settimeout method that executes every 5 minutes
    setTimeout(function () {
      console.log("Now updating"); //sample message to show when table is updated
      populateTable(); // the function that populates the table

      updateTableAtIntervals(); // the function calls itself here, creating a recursive cycle
    }, 300000);
  })(); //the function is also self-executing since it is invoked via the () and keeps executing from the recursion

  let rate = 1.0;
  $('#course-content').on('change', '#currency', function () {
    rate = parseInt($(this).val());
    console.log(rate);

  });

  $('#table-contents').on('click', '.view', function () { //targets all rows in the table and executes the function on click of each
    let selectedCourseDetails = JSON.parse($(this).closest('tr').attr('data-details')); // retrieve the data-course attribute value and parse back to json

    const details = $("#course-content"); // targets the element for displaying more course details
    details.html(""); //resets the element for a new view

    // appends course name and subject area
    details.append(`<h1 class="details-heading">${selectedCourseDetails.courseDetails.courseName} - <span class="subject">${selectedCourseDetails.courseDetails.subject}</span></h1>`)
    details.append(`<div class="divider"></div>`)
    // appends first section containing "key facts"
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
          <td class="inner">${formatStringForUKFees(selectedCourseDetails.fees.uk, rate)}</td>
          <td>${formatStringForInternationalFees(selectedCourseDetails.fees, rate)}</td>
        </tr>
      </tbody>
    </table>`)
    // appends second section
    details.append(`<h2 class="section-head">Overview</h2>`)
    details.append(`<div class="summary">${selectedCourseDetails.courseDetails.summary}</div>`)

    // appends another section
    details.append(`<h2 class="section-head">Highlights</h2>`)
    let highlights = $('<ul>');
    $.each(selectedCourseDetails.courseDetails.highlights, function (index, value) {
      highlights.append($('<li>').text(value));
    });
    details.append(highlights);

    // appends another section
    details.append(`<h2 class="section-head">Modules</h2>`)

    // appends another section
    details.append(`<h2 class="section-head">Entry Requirements</h2>`)

    // appends another section
    details.append(`<h2 class="section-head">FAQs</h2>`)

    // appends another section
    details.append(`<h2 class="section-head">Related Courses</h2>`)


    $('.overlay').fadeIn();
  });

  $('#close-btn').click(function () { //on click of close button, closes the further details section
    $('.overlay').fadeOut();
  });
})
