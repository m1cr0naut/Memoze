/* This JavaScript file implements date/time delivery selections in half-hour
 * increments for the coming 24-hour period. */

// builds a list of 48 half-hour scheduling periods for interface drop-down
function getDates(n) {
  // get current timestamp and push it to the next 30-minute increment
  var last = new Date();
  last.setMilliseconds(0);
  last.setSeconds(0);
  if(last.getMinutes() < 30) {
    last.setMinutes(0);
  } else {
    last.setMinutes(30);
  }

  // return array of half-hour schedules
  var dateSelect = [];
  for(var i = 0; i < n; i++) {
    var ourDate = new Date(last.getTime() + 30*60*1000);
    dateSelect.push(ourDate);
    last=dateSelect[i];
  }

  return dateSelect;
}

// helper func that builds readable schedule date values
function readableDate(date) {
  var months = [
    "Jan", "Feb", "Mar", "Apr",
    "May", "Jun", "Jul", "Aug",
    "Sep", "Oct", "Nov", "Dec",
  ];

  var day = date.getDate();
  var monthIndex = date.getMonth();
  var year = date.getFullYear();
  var hours = date.getHours();
  var mins = date.getMinutes();
  if (mins < 10) {
    mins = "0" + mins;
  }
  return (months[monthIndex] + ' ' + day + ', ' + year + ' at ' + hours + ":" + mins);
}

// sets drop-down with visible and backend schedule values
function setDates(arr, element) {
  for (var i = 0; i < arr.length; i++) {
    var option = document.createElement('option');
    option.value = arr[i].getTime();
    option.text = readableDate(arr[i]);
    element.appendChild(option);
  }
}
