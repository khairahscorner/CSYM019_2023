function populateTable() {
  const xhr = new XMLHttpRequest();
  const messageArea = document.getElementById("message");

  if (xhr) {
    xhr.open("GET", "course.json", true);
    xhr.send();

    //arrow function
    xhr.onreadystatechange = () => {
      if (xhr.readyState == 4 && xhr.status == 200) {
        const tableElement = document.getElementById("courses");

        //reset table view and hide error message area if shown
        tableElement.innerHTML = "";
        messageArea.innerHTML = "";
        messageArea.style.display = "none";

        //create thead element for table headers
        const headings = document.createElement("thead");
        const headingsRow = document.createElement("tr");

        headings.appendChild(headingsRow);
        tableElement.appendChild(headings);

        //create 7 columns for the table
        for (let i = 0; i < 7; i++) {
          const column = document.createElement("th");
          headingsRow.appendChild(column);
          column.innerHTML = `header ${i}`;
        }

        // add text to each header column
        headingsRow.childNodes[0].innerHTML = "S/N";
        headingsRow.childNodes[1].innerHTML = "Subject";
        headingsRow.childNodes[2].innerHTML = "Course Name";
        headingsRow.childNodes[3].innerHTML = "Level";
        headingsRow.childNodes[4].innerHTML = "Start Dates";
        headingsRow.childNodes[5].innerHTML = "Duration";
        headingsRow.childNodes[6].innerHTML = "Actions";

        //create tbody for the data listing
        const courseList = document.createElement("tbody");

        const results = JSON.parse(xhr.responseText).courses;

        for (let i = 0; i < results.length; i++) {
          currentRow = document.createElement("tr");
          currentRow.innerHTML += `<td>${i + 1}</td>`;
          currentRow.innerHTML += `<td><img src="icons/${results[i].courseDetails.icon}" alt="course logo" title="${results[i].courseDetails.subject}" class="table-icon"/></td>`;
          currentRow.innerHTML += `<td>${results[i].courseDetails.courseName}</td>`;
          currentRow.innerHTML += `<td>${results[i].keyFacts.level}</td>`;

          let startDates = results[i].keyFacts.startDates;
          let stringToAppendForDates = "";
          for (let j = 0; j < startDates.length; j++) {
            stringToAppendForDates += `<p>${startDates[j]}</p>`;
          }
          currentRow.innerHTML += `<td class="cell-with-list">${stringToAppendForDates}</td>`;

          let duration = results[i].keyFacts.duration;
          let stringToAppendForDuration = "";

          stringToAppendForDuration += `<p>Full Time: ${
            duration.fullTime == 1
              ? `${duration.fullTime} year`
              : `${duration.fullTime} years`
          }</p>`;
          if (duration.partTime) {
            stringToAppendForDuration += `<p>Part Time: ${
              duration.partTime == 1
                ? `${duration.partTime} year`
                : `${duration.partTime} years`
            }</p>`;
          }
          if (duration.withFoundation) {
            stringToAppendForDuration += `<p>With Foundation: ${duration.withFoundation} years</p>`;
          }
          if (duration.withPlacement) {
            stringToAppendForDuration += "<p>Placement option available</p>";
          }

          currentRow.innerHTML += `<td class="cell-with-list">${stringToAppendForDuration}</td>`;

          currentRow.innerHTML += `<td><button><a href="${results[i].courseDetails.url}" target="_blank"> View </a></button></td>`;

          courseList.appendChild(currentRow);
        }
        tableElement.appendChild(courseList);
      } else {
        messageArea.style.display = "inline-block";
        document.getElementById("message").innerHTML = "Could not load table.";
      }
    };
  } else {
    messageArea.style.display = "inline-block";
    document.getElementById("message").innerHTML =
      "Could not load table. Please try again later";
  }

  //after table is first loaded, then the timeout function is attached
  updateTableAtIntervals();
}

// Update table every 5 minutes
function updateTableAtIntervals() {
  setTimeout(function () {
    console.log("Now updating");
    populateTable();
  }, 300000);

  // setInterval(populateTable, 300000);
}

// load table immediately the page is loaded
document.addEventListener("DOMContentLoaded", populateTable);

// window.onload = populateTable;
