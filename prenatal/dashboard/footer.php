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
let tooltips = [];

toggleButton.addEventListener('click', () => {
  // Check if the sidebar has the "active" class and toggle it
  if (sidebar.classList.contains("active")) {
    sidebar.classList.remove("active");
    sidebar.classList.add("collapse");
    content.style.marginLeft = content.style.marginLeft === '100px' ? '100px' : '80px';

    // Reinitialize tooltips when sidebar becomes inactive
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
  } else {
    sidebar.classList.add("active");
    sidebar.classList.remove("collapse");
    content.style.marginLeft = content.style.marginLeft === '100px' ? '100px' : '250px';
    
    // Dispose tooltips when sidebar becomes active
    tooltips.forEach(tooltip => tooltip.dispose());
    tooltips = []; // Clear the tooltips array
  }
});

// Check the screen width and auto-collapse the sidebar
function checkScreenWidth() {
  if (window.innerWidth <= 1400) {
    sidebar.classList.remove("active");
    sidebar.classList.add("collapse");
    content.style.marginLeft = content.style.marginLeft === '100px' ? '100px' : '80px';

    // Reinitialize tooltips when sidebar becomes inactive
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

  } else {
    sidebar.classList.add("active");
    sidebar.classList.remove("collapse");
    content.style.marginLeft = content.style.marginLeft === '100px' ? '100px' : '250px';
    
    // Dispose tooltips when sidebar becomes active
    tooltips.forEach(tooltip => tooltip.dispose());
    tooltips = []; // Clear the tooltips array
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