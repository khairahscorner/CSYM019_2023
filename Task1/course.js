/**
 * This function is the main function to run that populates the table element in the html file with data from the json file
 */
function populateTable() {
  const messageArea = document.getElementById("message"); //this method is used to target and select the HTML element with id "message", which is used later in the code
  const xhr = new XMLHttpRequest(); //the XMLHttpRequest object to be used to process the data

  //confirm that the XMLHttpRequest object was successfully created
  if (xhr) {
    xhr.open("GET", "course.json", true); //configures the object to commence the request
    xhr.send(); //this sends the request to the server

    //onreadystatechange listens for changes to the status of the request, and executes the callback function if any change is detected.
    xhr.onreadystatechange = function () {

      //checks for whether the request is successfully completed and the response status is OK (200)
      if (xhr.readyState == 4 && xhr.status == 200) {
        const results = JSON.parse(xhr.responseText).courses; //save the `courses` property of the request's response directly to a variable
        processData(results); //function to call to process the response
      }
      else {
        messageArea.style.display = "inline-block"; //changes the display of the HTML element to show the error messages (when there are no errors, the element is not shown)
        document.getElementById("message").innerHTML = "Could not load table."; //change the inner contents of the element
      }
    };
  }
  else {
    messageArea.style.display = "inline-block"; //changes the display of the HTML element to show the error messages (when there are no errors, the element is not shown)
    document.getElementById("message").innerHTML = "Could not load table. Please try again later"; //change the inner contents of the element
  }

  //after table is first loaded, then a function with timeout is attached for subsequent updates
  updateTableAtIntervals();
}

/**
 * this function is used to process the data. It is defined as an arrow function because arrow functions are also functions.
 * @param results response object
 */
const processData = (results) => {
  const tableElement = document.getElementById("courses"); // get table element
  const messageArea = document.getElementById("message"); //get HTML element with id "message"

  tableElement.innerHTML = ""; //reset table view to ensure table is already refreshed with new data
  messageArea.innerHTML = ""; //reset the inner contents of the message element
  messageArea.style.display = "none"; //changes the display of the message element to hide the element since there are no errors
  
  const headings = document.createElement("thead"); //creates a thead element for the tsble headings
  const headingsRow = document.createElement("tr"); //creates a header row element for table headings

  headings.appendChild(headingsRow); //inserts the row as a child of thead element
  tableElement.appendChild(headings); //inserts the thead element as child of the table

  //for loop to iterate and create 7 columns for the table and append the columns to the header row
  for (let i = 0; i < 7; i++) {
    const column = document.createElement("th"); //create a th element
    headingsRow.appendChild(column); //append the element to the header row element
  }

  // add corresponding text to each header column
  headingsRow.childNodes[0].innerHTML = "S/N";
  headingsRow.childNodes[1].innerHTML = "Subject";
  headingsRow.childNodes[2].innerHTML = "Course Name";
  headingsRow.childNodes[3].innerHTML = "Level";
  headingsRow.childNodes[4].innerHTML = "Start Dates";
  headingsRow.childNodes[5].innerHTML = "Duration";
  headingsRow.childNodes[6].innerHTML = "Actions"; //last th element in the header row

  const courseList = document.createElement("tbody"); //create tbody for the data listing

  // for every line of data in the results, create a row, populate the row by creating td (columns) and adding corresponding data
  for (let i = 0; i < results.length; i++) {
    currentRow = document.createElement("tr"); //create a row

    //columns for numbering, the subject logo course name, and level
    currentRow.innerHTML += `<td>${i + 1}</td>`; //add the current line to give a number that can be used form numbering

    //second column is for course logos, which are urls in the response data
    currentRow.innerHTML += `<td><img src="icons/${results[i].courseDetails.icon}" alt="course logo" title="${results[i].courseDetails.subject}" class="table-icon"/></td>`; //the title attribute is added to show the subject area when the icon is hovered on; the image element also includes a class for css styling
    
    currentRow.innerHTML += `<td>${results[i].courseDetails.courseName}</td>`; //third column is for course names
    currentRow.innerHTML += `<td>${results[i].keyFacts.level}</td>`; // 4th column to show level whether postgraduate or undergraduate

    let stringToAppendForDates = formatStringForCourseStartDates(results[i].keyFacts.startDates); //function to format the startDates array that returns a string
    currentRow.innerHTML += `<td class="cell-with-list">${stringToAppendForDates}</td>`; //5th column shows the available start dates for the course

    let stringToAppendForDuration = formatStringForCourseDuration(results[i].keyFacts.duration); //function to format the duration object that returns a string
    currentRow.innerHTML += `<td class="cell-with-list">${stringToAppendForDuration}</td>`; //6th column shows the available duration for the course

    currentRow.innerHTML += `<td><button><a href="${results[i].courseDetails.url}" target="_blank"> View </a></button></td>`; // last column was added button with link to course page 

    courseList.appendChild(currentRow); //append the row to the tbody
  }

  tableElement.appendChild(courseList);   //append the tbody to the table
};


/**
 * this function formats the startDates array such that each available start date for the course is shown as different lines within the same td element
 * @param startDates start dates array
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
 * @param duration course duration object 
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


/**
 * timeout function to update the table at specific intervals
 */
function updateTableAtIntervals() {
  //settimeout method executes after exceeding 5 minutes, 
  setTimeout(function () {
    console.log("Now updating"); //sample message to show when table is updated
    populateTable(); //update the table
  }, 300000);

  // setInterval(populateTable, 300000);
}

document.addEventListener("DOMContentLoaded", populateTable); // load table immediately the page is loaded


// window.onload = populateTable;
