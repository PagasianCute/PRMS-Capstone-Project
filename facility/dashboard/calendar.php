<?php
include_once 'header.php'
?>
    
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="js/calendar_event.js"></script>

    <style>

  .calendar-contain {
    position: relative;
    left: 0;
    right: 0;
    border-radius: 10px;
    width: 100%;
    padding: 20px;
    overflow: hidden;
    margin: 1rem auto;
    background-color: #ffffff;
    color: #040605;
  }
  @media screen and (min-width: 55em) {
    .calendar-contain {
      margin: auto;
      top: 5%;
    }
  }
  
  .title-bar {
    position: relative;
    width: 100%;
    display: table;
    text-align: right;
    background-color: #ffffff;
    padding: 0.5rem;
    margin-bottom: 0;
    margin-bottom: 20px;
  }
  .title-bar:after {
    display: table;
    clear: both;
  }
  
  .title-bar__prev{
    position: relative;
    float: left;
    text-align: left;
    cursor: pointer;
    width: 22px;
    height: 30px;
  }
  .title-bar__prev:after {
      content: "";
      display: inline;
      position: absolute;
      width: 14px;
      height: 14px;
      right: 0;
      left: 2px;
      top: 7px;
      margin: auto;
      border-top: 1.5px solid black;
      border-right: 1.5px solid black;
      -webkit-transform: rotate(224deg);
      transform: rotate(224deg);
  }
  .title-bar__next{
    position: relative;
    float: right;
    text-align: right;
    cursor: pointer;
    width: 22px;
    height: 30px;
  }
  .title-bar__next:after {
      content: "";
      display: inline;
      position: absolute;
      width: 14px;
      height: 14px;
      right: 2px;
      top: 7px;
      margin: auto;
      border-top: 1.5px solid black;
      border-right: 1.5px solid black;
      -webkit-transform: rotate(44deg);
      transform: rotate(44deg);
  }
  .title-bar__year {
    display: block;
    position: relative;
    float: left;
    font-size: 1rem;
    line-height: 30px;
    width: 47%;
    padding: 0 0.5rem;
    text-align: center;
  }
  .title-bar__year select{
    padding: 2px 6px;
    font-size: 16px;
  }
  @media screen and (min-width: 55em) {
    .title-bar__year {
      width: 47%;
    }
  }
  
  .title-bar__month {
    position: relative;
    float: left;
    font-size: 1rem;
    margin-right: 0;
    padding: 0 0.5rem;
    text-align: center;
  }
  .title-bar__month select{
    padding: 2px 6px;
    font-size: 16px;
  }
  @media screen and (min-width: 55em) {
    .title-bar__month {
      width: 47%;
    }
  }
  
  .calendar__sidebar {
    width: 10px;
    margin: 0 auto;
    float: none;
    padding-bottom: 0.7rem;
  }
  @media screen and (min-width: 55em) {
    .calendar__sidebar {
      position: absolute;
      height: 100%;
      width: 30%;
      float: left;
      margin-bottom: 0;
    }
  }
  
  .calendar__sidebar .content {
    padding: 2rem 1.5rem 2rem 4rem;
    color: #040605;
  }
  
  .sidebar__list {
    list-style: none;
    margin: 0;
    padding-left: 1rem;
    padding-right: 1rem;
  }
  .sidebar__list-item {
    margin: 1.2rem 0;
    color: #2d4338;
    font-weight: 100;
    font-size: 1rem;
  }

  .sidebar__list-item h5{
    color: #fa4255;
  }

  
  .list-item__time {
    display: inline-block;
    font-size: 2rem;
  }
  @media screen and (min-width: 55em) {
    .list-item__time {
      margin-right: 1rem;
    }
  }
  
  .sidebar__list-item--complete {
    color: rgba(4, 6, 5, 0.3);
  }
  .sidebar__list-item--complete .list-item__time {
    color: rgba(4, 6, 5, 0.3);
  }
  
  .sidebar__heading {
    font-size: 1.5rem;
    font-weight: bold;
    padding-left: 1rem;
    padding-right: 1rem;
    margin-bottom: 2rem;
    margin-top: 0.5rem;
  }
  .sidebar__heading span {
    float: right;
    font-weight: 300;
  }
  
  .calendar__heading-highlight {
    color: #2d444a;
    font-weight: 900;
  }
  
  .calendar__days {
    display: -webkit-box;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
            flex-flow: column wrap;
    -webkit-box-align: stretch;
            align-items: stretch;
    width: 100%;
    float: none;
    height: 70vh;
    font-size: 12px;
    padding: 0.8rem 1.5rem 1rem 0.5rem;
    border-radius: 10px;
  }
  @media screen and (min-width: 55em) {
    .calendar__days {
      width: 70%;
      float: right;
    }
  }
  
  .calendar__top-bar {
    display: -webkit-box;
    display: flex;
    -webkit-box-flex: 32px;
    flex: 32px 0 0;
    text-align: center;
  }
  
  .top-bar__days {
    width: 100%;
    padding: 0 5px;
    color: #2d4338;
    font-weight: 100;
    -webkit-font-smoothing: subpixel-antialiased;
    font-size: 1rem;
  }
  
  .calendar__week {
    display: -webkit-box;
    display: flex;
    -webkit-box-flex: 1;
            flex: 1 1 0;
  }
  
  .calendar__day {
    display: -webkit-box;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
            flex-flow: column wrap;
    -webkit-box-pack: justify;
            justify-content: space-between;
    width: 100%;
    padding: 1rem 0.2rem 0.2rem 1.5rem;
    cursor: pointer;
    margin-left: 20px;
    margin-bottom: 20px;
    background-color: #4285f4;
    color: #ffffff;
    border-radius: 15px;
  }
  
  .calendar__day.event .calendar__date, .calendar__day.event .calendar__task{
    color: #ffffff;
  }
  #date-today{
    background-color: rgb(250, 66, 85, 0.9);
  }
  #with-event{
    background-color: #796eff;
  }
  
  .calendar__date {
    color: #ffffff;
    font-size: 1.7rem;
    font-weight: 600;
    line-height: 0.7;
  }
  @media screen and (min-width: 55em) {
    .calendar__date {
      font-size: 2.3rem;
    }
  }
  
  .calendar__week .inactive .calendar__date,
  .calendar__week .inactive .task-count {
    color: #ffffff;
  }
  .calendar__week .today .calendar__date {
    color: #ffffff;
  }
  .calendar__week .today .calendar__day {
    background-color: #ffffff;
  }
  
  .calendar__task {
    color: #ffffff;
    display: -webkit-box;
    display: flex;
    font-size: 0.8rem;
  }
  @media screen and (min-width: 55em) {
    .calendar__task {
      font-size: 1rem;
    }
  }
  .calendar__task.calendar__task--today {
    color: #ffffff;
  }
    </style>

    <script>
        function getCalendar(target_div, year, month) {
            $.ajax({
                type: 'POST',
                url: 'calendar_functions.inc.php',
                data: 'func=getCalender&year=' + year + '&month=' + month,
                success: function (html) {
                    $('#' + target_div).html(html);
                }
            });
        }

        function getEvents(date) {
            $.ajax({
                type: 'POST',
                url: 'calendar_functions.inc.php',
                data: 'func=getEvents&date=' + date,
                success: function (html) {
                    $('#event_list').html(html);
                }
            });

            // Add date to event form
            $('#event_date').val(date);
        }

        function getCalendarEvents(target_div, year, month, date) {
            $.ajax({
                type: 'POST',
                url: 'calendar_functions.inc.php',
                data: 'func=getCalender&year=' + year + '&month=' + month,
                success: function (html) {
                    $('#' + target_div).html(html);
                    getEvents(date);
                }
            });
        }

        $(document).ready(function () {
            $('.month-dropdown, .year-dropdown').on('change', function () {
                getCalendar('calendar_div', $('.year-dropdown').val(), $('.month-dropdown').val());
            });

            $('.add-event-btn').on('click', function () {
                $('#event_add_frm').slideToggle();
                $('#deleteEventBtn').hide(); // Hide delete button when adding a new event
            });

            $('#deleteEventBtn').on('click', function () {
                var eventDate = $('#event_date').val();

                $.ajax({
                    type: 'POST',
                    url: 'calendar_functions.inc.php',
                    data: {
                        func: 'deleteEvent',
                        event_id: eventDate
                    },
                    success: function (response) {
                        if (response == 1) {
                            getEvents(eventDate);
                            $('#event_add_frm').slideUp(); // Hide the form after deletion
                        } else {
                            alert('Failed to delete event. Please try again.');
                        }
                    }
                });
            });

            // Modified submit event handler
            $('#eventAddFrm').submit(function (event) {
                event.preventDefault(); // Prevent the default form submission

                $(':input[type="submit"]').prop('disabled', true);
                $('#event_add_frm').css('opacity', '0.5');

                $.ajax({
                    type: 'POST',
                    url: 'calendar_functions.inc.php',
                    data: $('#eventAddFrm').serialize() + '&func=addEvent',
                    success: function (status) {
                        if (status == 1) {
                            //$('#eventAddFrm')[0].reset();
                            $('#event_title').val('');
                            swal("Success!", "Event added successfully.", "success");
                        } else {
                            swal("Failed!", "Something went wrong, please try again.", "error");
                        }

                        $(':input[type="submit"]').prop('disabled', false);
                        $('#event_add_frm').css('opacity', '');

                        var date = $('#event_date').val();
                        var dateSplit = date.split("-");
                        getCalendarEvents('calendar_div', dateSplit[0], dateSplit[1], date);
                        // Keep the form open after submission
                        // $('#event_add_frm').slideUp();
                    }
                });
            });
        });
    </script>
</head>
<body>

<div id="calendar_div">
    <?php include_once 'calendar_functions.inc.php'; echo getCalender(); ?>
</div>

<!-- Add a delete button -->
<button class="btn btn-danger" id="deleteEventBtn" style="display: none;">Delete Event</button>

<?php
include_once 'footer.php'
?>