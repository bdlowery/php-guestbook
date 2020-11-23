<?php
require 'header.php';
?>

<body>
  <div class="container">
    <h2>Sierra College <br>
      PHP Class Guestbook <br>
    </h2>
    A PHP project that saves data (messages) entered from the form by the user into a MySQL database. This is then displayed back to the user in a guestbook format. <br>
    <small>*Includes a custom CSS style, form validation, sessions, and user input sanitization in the form of prepared statements to prevent SQL Injection.</small>
    <br>
    <br>
    <a class="btn btn-alt" href="add.php"><b>ADD A COMMENT</b></a>
  </div>
</body>

<?php
require 'footer.php';
?>