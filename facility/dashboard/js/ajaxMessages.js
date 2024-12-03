document.addEventListener("DOMContentLoaded", function () {
  const contactList = document.querySelector(".contacts .contacts-list");
  const searchBar = document.querySelector(".contacts #search-input");
  const receiver = document.querySelector("#receiver_id");
  const form = document.querySelector(".type-area");
  const inputField = form.querySelector(".input-field");
  const chatBox = document.querySelector(".message-container");
  const contactHeader = document.querySelector(".messages-header");
  let isFirstContactLoaded = false;
  const urlParams = new URLSearchParams(window.location.search);
  const contactIdFromUrl = urlParams.get("contact_id");
  let shouldLoadMessagesOnContactClick = true;

  var channel = pusher.subscribe(fclt_id);
  channel.bind("message", function (data) {
    loadMessages();
    loadContacts();
  });
  channel.bind("seen", function (data) {
    loadContacts();
  });

  loadContacts();
  loadContactHeader();

  function loadContacts() {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "server/message_function.php", true);
    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          let data = xhr.response;
          contactList.innerHTML = data;

          // After loading contacts, automatically load messages for the first contact
          loadFirstContactMessages();
        }
      }
    };
    xhr.send();
  }

  function loadFirstContactMessages() {
    // Get the first contact displayed
    const firstContact = document.querySelector(
      ".referral-card.message-contact"
    );

    if (contactIdFromUrl && shouldLoadMessagesOnContactClick) {
      // Load messages for the specified contact ID
      receiver.value = contactIdFromUrl;
      loadContactHeader();
      loadMessages();

      if (firstContact && !isFirstContactLoaded) {
        const dataContactId = firstContact.getAttribute("data-contact-id");
        const dataMessagetId = firstContact.getAttribute("data-message-id");
        if (dataMessagetId) {
          MessageStatus(dataMessagetId, dataContactId);
          //console.log(dataMessagetId);
          isFirstContactLoaded = true;
        }
      }
    } else {
      if (firstContact && !isFirstContactLoaded) {
        firstContact.click();
        isFirstContactLoaded = true;
      }
      urlParams.delete("contact_id");

      // Create a new URL without the 'contact_id' parameter
      const newUrl = `${window.location.origin}${
        window.location.pathname
      }?${urlParams.toString()}`;

      // Replace the current URL with the modified URL
      history.replaceState({}, document.title, newUrl);
    }
  }

  function loadMessages() {
    let formData = new FormData(form);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "server/load_message_function.php", true);
    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          let data = xhr.response;
          chatBox.innerHTML = data;
          chatBox.scrollTop = chatBox.scrollHeight;

          var tooltipTriggerList = [].slice.call(
            chatBox.querySelectorAll('[data-toggle="tooltip"]')
          );
          var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
          });
        }
      }
    };

    xhr.send(formData);
  }

  function loadContactHeader() {
    let formData = new FormData(form);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "server/load_header.php", true);
    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          let data = xhr.response;
          contactHeader.innerHTML = data;
          if (mobile) {
            const orientation = window.orientation;
            if (orientation === 0) {
              $("#back-btn").removeClass("d-none");
            } else if (orientation === 90 || orientation === -90) {
              $("#back-btn").addClass("d-none");
            }
          } else {
            $("#back-btn").addClass("d-none");
          }
        }
      }
    };

    xhr.send(formData);
  }

  searchBar.addEventListener("input", function (e) {
    e.preventDefault();
    let searchterm = searchBar.value.trim();

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "server/search.php", true);
    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          let data = xhr.response;
          contactList.innerHTML = data;
        }
      }
    };
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("searchTerm=" + encodeURIComponent(searchterm));
  });

  if (mobile) {
    console.log("Mobile device detected");

    checkOrientation();

    window.addEventListener("orientationchange", function () {
      checkOrientation();
    });
  } else {
    console.log("Desktop device detected");
  }

  function checkOrientation() {
    const orientation = window.orientation;

    if (orientation === 0) {
      console.log("Portrait orientation");
      mobileUIContactsPortrait();
    } else if (orientation === 90 || orientation === -90) {
      console.log("Landscape orientation");
      mobileUILandscape();
    }
  }

  function clickCheckOrientation() {
    const orientation = window.orientation;

    if (orientation === 0) {
      console.log("Portrait orientation");
      //mobileUIContactsPortrait();
      mobileUIMessagePortrait();
    } else if (orientation === 90 || orientation === -90) {
      console.log("Landscape orientation");
      mobileUILandscape();
    }
  }

  function mobileUIContactsPortrait() {
    console.log("mobileUIContactsPortrait");
    $(".contacts").removeClass("col-lg-4 col-md-5 col-sm-6");
    $(".contacts").addClass("col-md-12 col-sm-12 col-lg-12");
    $(".contacts").removeClass("d-none");
    $(".messages").addClass("d-none");
  }

  function mobileUILandscape() {
    console.log("mobileUILandscape");
    $(".contacts").removeClass("col-md-12 col-sm-12 col-lg-12");
    $(".messages").addClass("col-lg-8 col-md-7 col-sm-6");
    $(".contacts").addClass("col-lg-4 col-md-5 col-sm-6");
    $(".messages").removeClass("d-none");
  }

  function mobileUIMessagePortrait() {
    console.log("mobileUIMessagePortrait");
    $(".messages").addClass("col-md-12 col-sm-12 col-lg-12");
    $(".messages").removeClass("col-lg-8 col-md-7 col-sm-6");
    $(".messages").removeClass("d-none");
    $(".contacts").addClass("d-none");
  }

  contactList.addEventListener("click", function (event) {
    if (mobile) {
      console.log("Mobile device detected");
      clickCheckOrientation();
    } else {
      console.log("Desktop device detected");
    }
    const referralCard = event.target.closest(".referral-card");

    if (referralCard) {
      const dataContactId = referralCard.getAttribute("data-contact-id");
      const dataMessagetId = referralCard.getAttribute("data-message-id");
      if (dataContactId) {
        receiver.value = dataContactId;
        //console.log(dataContactId);
      }
      if (dataMessagetId) {
        MessageStatus(dataMessagetId, dataContactId);
        //console.log(dataMessagetId);
        shouldLoadMessagesOnContactClick = false;
      }
    }
    loadMessages();
    loadContactHeader();
    searchBar.value = "";
  });

  function mobileUIMessagePortrait() {
    $(".messages").addClass("col-md-12 col-sm-12 col-lg-12");
    $(".messages").removeClass("col-lg-8 col-md-7 col-sm-6");
    $(".messages").removeClass("d-none");
    $(".contacts").addClass("d-none");
  }

  function MessageStatus(dataMessagetId, dataContactId) {
    const formData = new FormData();
    formData.append("dataMessagetId", dataMessagetId);
    formData.append("dataContactId", dataContactId);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "server/seen_function.php", true);

    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          let data = xhr.response;
          console.log(data);
          loadContacts();
        }
      }
    };

    xhr.send(formData);
  }

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    $("#send-button").addClass("d-none");
    $("#sending-button").removeClass("d-none");

    let formData = new FormData(form);
    formData.append("message", inputField.value.trim());

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "server/send_message_function.php", true);
    xhr.onload = () => {
      $("#send-button").removeClass("d-none");
      $("#sending-button").addClass("d-none");
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          // Handle the response if needed
          console.log(xhr.responseText);
          inputField.value = "";
          loadMessages();
          loadContacts();
        }
      }
    };

    xhr.send(formData);
  });

  $(document).on("click", "#back-btn", function () {
    // Handle the click event here
    console.log("Back button clicked");
    $(".messages").addClass("d-none");
    $(".contacts").removeClass("d-none");
  });
});
