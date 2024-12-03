</div>
</div>
<!-- Include Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Include Boxicons -->
<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

<!-- Include Pusher JavaScript -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

<!-- Include Bootstrap-datepicker JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>



<script>

// JavaScript to toggle the sidebar
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const toggleButton = document.getElementById('toggleButton');

    toggleButton.addEventListener('click', () => {
      sidebar.style.left = sidebar.style.left === '0px' ? '-250px' : '0px';
      content.style.marginLeft = content.style.marginLeft === '0px' ? '250px' : '0px';
    });

    // Check the screen width and auto-collapse the sidebar
    function checkScreenWidth() {
      if (window.innerWidth <= 1400) {
        sidebar.style.left = '-250px';
        content.style.marginLeft = '0';
      } else {
        sidebar.style.left = '0px';
        content.style.marginLeft = '250px';
      }
    }

    // Call the function on page load and window resize
    window.addEventListener('load', checkScreenWidth);
    window.addEventListener('resize', checkScreenWidth);


var pusher = new Pusher('4c140a667948d3f0c3b4', {
    cluster: 'ap1'
});

var channel = pusher.subscribe('my-channel');
channel.bind('my-event', function (data) {

});

</script>

</body>
</html>