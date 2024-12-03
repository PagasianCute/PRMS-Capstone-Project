<?php
include_once 'header.php';
?>
  <div class="container">
  <h1>Dynamic Input Fields</h1>

  <form id="myForm" method="post" action="insert_data.php">
    <div id="inputFieldsContainer">
      <!-- Existing input fields will be placed here -->
    </div>
    <button type="button" onclick="addInputField()">Add</button>
    <button type="submit">Submit</button>
    <a href="display_form.php">display</a>
  </form>
  </div>
  <script>
    function addInputField() {
      const labelName = prompt('Enter the label name for the input field:');
      if (labelName) {
        const container = document.getElementById('inputFieldsContainer');
        const inputField = document.createElement('div');
        inputField.innerHTML = `
          <label>${labelName}</label>
        `;
        container.appendChild(inputField);

        // Append a hidden input field with the label name to be submitted to the server
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'labels[]'; // Use an array to store multiple labels
        hiddenInput.value = labelName;
        container.appendChild(hiddenInput);
      }
    }
  </script>
<?php
include_once 'footer.php';
?>
