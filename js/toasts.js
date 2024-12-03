// toasts.js

// Function to create and show a new toast
function showToast(title, message) {
  var toastId = "toast" + new Date().getTime();
  var toastElement = document.createElement("div");
  toastElement.id = toastId;
  toastElement.className = "toast";
  toastElement.role = "alert";
  toastElement.setAttribute("aria-live", "assertive");
  toastElement.setAttribute("aria-atomic", "true");

  toastElement.innerHTML = `
      <div class="toast-header">
        <strong class="me-auto">${title}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
        ${message}
      </div>
    `;

  document.querySelector(".toast-container").appendChild(toastElement);

  var newToast = new bootstrap.Toast(document.getElementById(toastId));
  newToast.show();
}

function MessageshowToast(title, message, fclt_id) {
  var toastId = "toast" + new Date().getTime();
  var toastElement = document.createElement("div");
  toastElement.id = toastId;
  toastElement.className = "toast";
  toastElement.role = "alert";
  toastElement.setAttribute("aria-live", "assertive");
  toastElement.setAttribute("aria-atomic", "true");

  toastElement.innerHTML = `
      <div class="toast-header">
        <strong class="me-auto">${title}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
        ${message}
        <div class="mt-2 pt-2 border-top">
          <a class="btn btn-primary btn-sm" href="facility/dashboard/messages.php?contact_id=${fclt_id}" role="button">View</a>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="toast">Close</button>
        </div>
      </div>
    `;

  document.querySelector(".toast-container").appendChild(toastElement);

  var newToast = new bootstrap.Toast(document.getElementById(toastId));
  newToast.show();
}