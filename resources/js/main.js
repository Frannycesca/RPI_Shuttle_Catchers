function getSchedule(route, loc) {
  $.getJSON("rpi_shuttle_schedule.json", function (data) {
    var today = new Date();
    var dayOfWeek = today.getDay();
    var tmp;

    if (route === "east") {
      if (dayOfWeek === 0 || dayOfWeek === 6) { // it's the weekend
        tmp = data.Weekend_East;
      } else if (dayOfWeek > 0 && dayOfWeek < 6) { // it's a week day
        tmp = data.Weekday_East;
      }
    }
    else if (route === "west") {
      if (dayOfWeek === 0) { // it's sunday
        tmp = data.Sunday_West;
      } else if (dayOfWeek === 6) { // it's saturday
        tmp = data.Saturday_West;
      } else { // it's a week day
        tmp = data.Weekday_West;
      }
    }

    var hour = today.getHours();
    var minute = today.getMinutes();

    var pickupDate = $("#pickupDate").val();
    var date = (today.getMonth() + 1) + "/" + today.getDate() + "/" + today.getFullYear();

    if (hour >= 23 && pickupDate === date) {
      $("#setAlarm").hide();
    } else {
      var minute = today.getMinutes();
      if (minute < 10) {
        minute = "0" + minute;
      }

      $("#pickupTime").html('<option>Please select a pick-up time</option>');

      for (var i = 0; i < tmp.length; i++) {
        if (tmp[i].location === loc) {

          var times = tmp[i].times;
          for (var j = 0; j < times.length; j++) {

            if ($("#pickupDate").val() === date && hour > 7 ) {
              var military = hour + ":" + minute;
              var temp;
              var arr = times[j].split(" ");
              var arr2 = arr[0].split(":");

              if (arr[arr.length - 1] === "PM") {
                if (arr2[0] !== "12") {
                  arr2[0] = parseInt(arr2[0], 10) + 12;
                }
              } else {
                if (arr2[0] === "12")  {
                  arr2[0] = "00";
                }
              }
              temp = arr2[0] + ":" + arr2[1];

              if ( temp > military ) {
                $("#pickupTime").append("<option value='" + times[j] + "'>" + times[j] + "</option>");
              }
            } else {
              $("#pickupTime").append("<option value='" + times[j] + "'>" + times[j] + "</option>");
            }
          }
        }
      }
    }


  });
}

function removeSchedule(sched_id){
  var rcs_id = $("#rcsid").html();

  $.ajax({
      url: "removeSchedule.php",
      data: {
        rcsid: rcs_id,
        schedid: sched_id
      },
      success: function (data) {
        if(data === ""){
          alert("Schedule Removed");
          location.reload();
        } else{
          alert(data);
        }
      }
    });
}

$(document).ready(function () {

  var eastRoutes = ["Union", "Colonie", "Brinsmade", "Sunset 1 & 2", "E-lot", "B-lot", "9th/Sage", "West lot", "Sage"];
  var westRoutes = ["Union", "Sage Ave", "Blitman", "City Station", "Poly Tech", "15th & Collage"];

  var oneDay = 24 * 3600 * 1000;
  var d = new Date();
  var tmp = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
  var today = tmp;

  var alertType;

  $("#singleAlert").click(function(){
    alertType = "single";
    $(this).addClass("active");
    $("#recurringAlert").removeClass("active");
    $("#scheduleForm").show();
    $("#pickupDay").hide();
    $("label[for='pickupDay']").hide();
    $("#pickupDate").show();
    $("label[for='pickupDate']").show();
    
    $("#pickupDate").append("<option value='" + tmp + "'>Today</option>");

    for (var i = 0; i < 7; i++) {
      d.setMilliseconds(d.getMilliseconds() + oneDay);
      tmp = (d.getMonth() + 1) + "/" + d.getDate();
      tmp2 = tmp + "/"+ d.getFullYear();
      $("#pickupDate").append("<option value='" + tmp2 + "'>" + tmp + "</option>");
    }
  });

  $("#recurringAlert").click(function(){
    alertType = "recurring";
    $(this).addClass("active");
    $("#singleAlert").removeClass("active");
    $("#scheduleForm").show();
    $("#pickupDate").hide();
    $("label[for='pickupDate']").hide();
    $("#pickupDay").show();
    $("label[for='pickupDay']").show();
    
  });

  $("#route").change(function () {
    $("#pickupLoc").html("<option>Please select a location</option>");
    if (this.value === "east") {
      for (var i = 0; i < eastRoutes.length; i++) {
        $("#pickupLoc").append("<option value='" + eastRoutes[i] + "'>" + eastRoutes[i] + "</option>");
      }

    } else if (this.value == "west"){
      for (var i = 0; i < westRoutes.length; i++) {
        $("#pickupLoc").append("<option value='" + westRoutes[i] + "'>" + westRoutes[i] + "</option>");
      }
    } 
  });


  $("#pickupDate").change(function () {
    getSchedule($("#route").val(), $("#pickupLoc").val());
  });

  $("#pickupDay").change(function () {
    getSchedule($("#route").val(), $("#pickupLoc").val());
  });

  $("#setAlarm").click(function () {
    $("#error").css('visibility', 'hidden');
    $("#route").removeClass("error");
    $("#pickupLoc").removeClass("error");
    $("#walkingSpeed").removeClass("error");
    $("#pickupDate").removeClass("error");
    $("#pickupDay").removeClass("error");
    $("#pickupTime").removeClass("error");

    var route = $("#route").val();
    var pickupLoc = $("#pickupLoc").val();
    var walkingSpeed = $("#walkingSpeed").val();
    var pickupDate;
    if(alertType === "single"){
      pickupDate = $("#pickupDate").val();
    } else {
      pickupDate = $("#pickupDay").val();
    }
    
    var pickupTime = $("#pickupTime").val();

    var error = false;
    if (route === "Please select a route") {
      $("#route").addClass("error");
      error = true;
    }
    if (pickupLoc === "Please select a location") {
      $("#pickupLoc").addClass("error");
      error = true;
    }
    if (walkingSpeed == '' || walkingSpeed.length == 0) {
      $("#walkingSpeed").addClass("error");
      error = true;
    }
    if (pickupDate === "Please select a pick-up date") {
      $("#pickupDate").addClass("error");
      error = true;
    } else if (pickupDate === "Please select a pick-up day") {
      $("#pickupDay").addClass("error");
      error = true;
    }
    if (pickupTime === "Please select a pick-up time") {
      $("#pickupTime").addClass("error");
      error = true;
    }

    if(error === false){
        var alertTimes = [];
        $("input[name='alert_times[]']:checked").each(function () {
          alertTimes.push(this.value);
        });

        $.ajax({
          url: "http://shuttlecatchers.myrpi.org/setAlarm.php",
          data: {
            Route: route,
            PickupLoc: pickupLoc,
            WalkingSpeed: walkingSpeed,
            PickupDate: pickupDate,
            PickupTime: pickupTime,
            AlertTimes: JSON.stringify(alertTimes),
            AlertType: alertType
          },
          success: function (data) {
            alert(data);
            location.reload();
          }
        });
      } else {
        $("#error").css('visibility', 'visible');
      }

  });

  $("#savePhone").click(function () {

    var phone = $("#phoneNumber").val();
    var carrier = $("#phoneCarrier :selected").val();
    
    var phoneRe = /^[2-9]\d{2}[2-9]\d{2}\d{4}$/;
    var digits = phone.replace(/\D/g, "");

    if (digits.match(phoneRe) !== null) {
      var rcs_id = $("#rcsid").html();

      $.ajax({
        url: "addNumber.php",
        data: {
          rcsid: rcs_id,
          phonenumber: digits,
          phonecarrier: carrier
        },
        success: function (data) {
          if(data === ""){
            location.reload();
          } else{
            alert("Error adding phone number");
          }
        }
      });
    } else {
      alert("Invalid phone number");
    }
   
  });

});