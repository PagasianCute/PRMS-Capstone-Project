<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    .container {
      max-width: 250px; /* Adjust the maximum width as needed */
      overflow: hidden; /* Hide overflow content */
      display: flex;
      align-items: center;
    }

    .container img {
      height: 30px;
      width: 30px;
      margin: 10px -10px 10px 10px;
      vertical-align: middle; /* Align the image vertically in the middle of the line */
    }

    .text {
      white-space: pre-wrap; /* Allow text to wrap */
      margin: 0; /* Remove default margin */
    }
  </style>
  <title>Text and Image</title>
</head>
<body>

<div class="container">
  <p class="text">This is some long text that will break and go to the next line<img src="../../assets/Verified.png" alt="Description of the image"></p>
</div>

</body>
</html>
