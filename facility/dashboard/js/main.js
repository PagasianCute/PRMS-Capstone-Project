function reloadNotifications() {
  const notificationsContainer = document.querySelector(
    ".notifications-container"
  );
  if (notificationsContainer) {
    let offset = 0;
    const limit = 10;

    function loadNotificationsInternal() {
      const xhr = new XMLHttpRequest();
      xhr.open(
        "GET",
        `server/load_notification.php?limit=${limit}&offset=${offset}`,
        true
      );
      xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            const data = xhr.response;
            if (data.trim() !== "") {
              // Append new notifications to existing content
              const notificationsElement =
                notificationsContainer.querySelector(".notifications");
              notificationsElement.innerHTML = data;
              offset += limit;
            }
          }
        }
      };
    
      xhr.send();
    }

    function loadNotificationsInternal2() {
      const xhr = new XMLHttpRequest();
      xhr.open(
        "GET",
        `server/load_notification.php?limit=${limit}&offset=${offset}`,
        true
      );
      xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            const data = xhr.response;
            if (data.trim() !== "") {
              // Append new notifications to existing content
              const notificationsElement =
                notificationsContainer.querySelector(".notifications");
              notificationsElement.innerHTML += data;
              offset += limit;
            }
          }
        }
      };
    
      xhr.send();
    }

    loadNotificationsInternal();

    notificationsContainer.addEventListener("scroll", function () {
      if (
        notificationsContainer.scrollTop + notificationsContainer.clientHeight >=
        notificationsContainer.scrollHeight - 10
      ) {
        loadNotificationsInternal2();
      }
    });
  } else {
    console.error("Element with class 'notifications-container' not found");
  }

  $("#markAllAsReadBtn").on("click", function () {
    markAllAsRead();
  });

  function markAllAsRead() {
    $.ajax({
      url: "server/mark_all_as_read.php",
      type: "POST",
      data: {
        fcltid: fclt_id,
      },
      success: function (response) {
        console.log(response);
        offset = 0;
        loadNotificationsInternal();
      },
      error: function (error) {
        console.error("Error marking all as read: " + error.responseText);
      },
    });
  }
}

document.addEventListener("DOMContentLoaded", function () {
  reloadNotifications(); // Call this function to load notifications initially
});

// Call reloadNotifications() whenever you want to refresh the notifications
