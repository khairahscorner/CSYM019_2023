/**
 * This function is the main function to run that populates the table element in the html file with data from the json file.
 * It is used in the function that is responsible for the live updates of the courses.
 */
function populateTable() {
  $.ajax({
    url: "course.json",
    type: "GET",
    dataType: "json",
  })
    .done(function (response) {
      const tableElement = $("#courses"); // get table element
      const messageArea = $("#message"); //get HTML element with id "message"
      const courseList = $("#table-contents"); //get tbody for the data listing

      courseList.html(""); //reset table view to ensure table is already refreshed with new data

      messageArea.html(""); //reset the inner contents of the message element
      messageArea.css("display", "none"); //changes the display of the message element to hide the element since there are no errors
      tableElement.css("display", "inline-block"); //changes the display of the table element to ensure the table shows

      // the .each function is used to process each course and create row of data for them
      $.each(response.courses, function (index, course) {
        let currentRow = $("<tr>"); //create a row
        
        currentRow.append(`<td>${index + 1}</td>`); //first column is for numbering the rows according to index

        //second column is for course logos, which are image urls in the data, the title attribute is added to show the subject area when the icon is hovered on; the image element also includes a class for css styling
        currentRow.append(`<td><img src="icons/${course.courseDetails.icon}" alt="course logo" title="${course.courseDetails.subject}" class="table-icon"/></td>`);

        currentRow.append(`<td>${course.courseDetails.courseName}</td>`); //third column is for course names
        currentRow.append(`<td>${course.keyFacts.level}</td>`); // 4th column to show level whether postgraduate or undergraduate

        let stringToAppendForDates = formatStringForCourseStartDates(course.keyFacts.startDates); //function to format the startDates array that returns a string
        currentRow.append(`<td class="cell-with-list">${stringToAppendForDates}</td>`); //5th column shows the available start dates for the course

        let stringToAppendForDuration = formatStringForCourseDuration(course.keyFacts.duration); //function to format the duration object that returns a string
        currentRow.append(`<td class="cell-with-list">${stringToAppendForDuration}</td>`); //6th column shows the available duration for the course

        currentRow.append(`<td><button><a href="${course.courseDetails.url}" target="_blank"> View </a></button></td>`); // last column was added button with link to course page 

        courseList.append(currentRow); //append the row to the tbody
      })

      tableElement.append(courseList);   //append the tbody to the table
    })
    .fail(function () {
      $("#message").css("display", "inline-block"); //changes the display to show the error messages
      $("#message").html("Could not load table. Please try again later"); //change the inner contents of the element
      $("#courses").css("display", "none"); // hides the table
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

$(document).ready(function () {
  populateTable(); //to load the table initially without any delay

  // function for updating the table at specific intervals
  (function updateTableAtIntervals() {
    //settimeout method that executes every 5 minutes
    setTimeout(function () {
      console.log("Now updating"); //sample message to show when table is updated

      populateTable(); // the function that populates the table

      updateTableAtIntervals(); // the function calls itself here, creating a recursive cycle
    }, 300000);
  })(); //the function is also self-executing since it is invoked via the () and keeps executing from the recursion
})
