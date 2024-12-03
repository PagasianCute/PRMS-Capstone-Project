$(document).ready(function () {
    $(document).on('click', '#editEventModalBtn', function () {
        var eventDate = $(this).data('event-date');
        $('#editEventModal').modal('show');
        $('#editEventModal #eventDate').val(eventDate);

        $.ajax({
            type: 'POST',
            url: 'calendar_functions.inc.php',
            data: {
                func: 'getEventTitle',
                event_date: eventDate
            },
            success: function (response) {
                $('#editEventModal #eventTitle').val(response);
            }
        });
    });

    $('#saveChangesBtn').on('click', function () {
        var eventDate = $('#editEventModal #eventDate').val();
        var eventTitle = $('#editEventModal #eventTitle').val();

        $.ajax({
            type: 'POST',
            url: 'calendar_functions.inc.php',
            data: {
                func: 'editEvent',
                event_id: eventDate,
                event_title: eventTitle
            },
            success: function (response) {
                if (response == 1) {
                    $('#editEventModal').modal('hide');
                    getEvents(eventDate);
                } else {
                    alert('Failed to edit event. Please try again.');
                }
            }
        });
    });

    $(document).on('click', '#deleteEventModalBtn', function () {
        var eventDate = $(this).data('event-date');
        $('#deleteEventModal').modal('show');
        $('#deleteEventModal #eventDate').val(eventDate);
    });

    $('#confirmDeleteBtn').on('click', function () {
        var eventDate = $('#deleteEventModal #eventDate').val();

        $.ajax({
            type: 'POST',
            url: 'calendar_functions.inc.php',
            data: {
                func: 'deleteEvent',
                event_id: eventDate
            },
            success: function (response) {
                if (response == 1) {
                    $('#deleteEventModal').modal('hide');
                    getEvents(eventDate);
                } else {
                    alert('Failed to delete event. Please try again.');
                }
            }
        });
    });
});
$(document).ready(function () {
    $(document).on('click', '#editEventModalBtn', function () {
        var eventDate = $(this).data('event-date');
        $('#editEventModal').modal('show');
        $('#editEventModal #eventDate').val(eventDate);

        $.ajax({
            type: 'POST',
            url: 'calendar_functions.inc.php',
            data: {
                func: 'getEventTitle',
                event_date: eventDate
            },
            success: function (response) {
                $('#editEventModal #eventTitle').val(response);
            }
        });
    });

    $('#saveChangesBtn').on('click', function () {
        var eventDate = $('#editEventModal #eventDate').val();
        var eventTitle = $('#editEventModal #eventTitle').val();

        $.ajax({
            type: 'POST',
            url: 'calendar_functions.inc.php',
            data: {
                func: 'editEvent',
                event_id: eventDate,
                event_title: eventTitle
            },
            success: function (response) {
                if (response == 1) {
                    $('#editEventModal').modal('hide');
                    getEvents(eventDate);
                } else {
                    alert('Failed to edit event. Please try again.');
                }
            }
        });
    });

    $(document).on('click', '#deleteEventModalBtn', function () {
        var eventDate = $(this).data('event-date');
        $('#deleteEventModal').modal('show');
        $('#deleteEventModal #eventDate').val(eventDate);
    });

    $('#confirmDeleteBtn').on('click', function () {
        var eventDate = $('#deleteEventModal #eventDate').val();

        $.ajax({
            type: 'POST',
            url: 'calendar_functions.inc.php',
            data: {
                func: 'deleteEvent',
                event_id: eventDate
            },
            success: function (response) {
                if (response == 1) {
                    $('#deleteEventModal').modal('hide');
                    getEvents(eventDate);
                } else {
                    alert('Failed to delete event. Please try again.');
                }
            }
        });
    });
});
