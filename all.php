<?php
require 'header.php';
require 'functions.php';
?>

<body>
<div class="container">
  <h3>All Messages</h3>
  <small>A list of every message a user has entered into the database.</small>
</div>
  <?php

  $sql = "SELECT guest_users.id, guest_users.first, guest_users.last, guest_message.comment, guest_message.date_sent 
    FROM guest_users LEFT JOIN guest_message ON (guest_users.id = guest_message.user_id) ORDER by id DESC LIMIT 200";

  try {
    $db = new PDO(DB_PATH, DB_LOGIN, DB_PW);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $result = $db->prepare($sql);
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);

    if ($result->rowCount() > 0) {
      while ($row = $result->fetch()) {
        echo '
          <div class="allMessages">
          <table border="0" cellspacing="0" cellpadding="4" width="100">
          <tr>
          <td><small>#' . $row['id'] .'</small></td>
          </tr>
          <tr>
          <td><b>Date:</b></td>
          <td nowrap>' . $row['date_sent'] . '</td>
          </tr>
          <tr>
          <td><b>From:</b></td>
          <td nowrap>' . $row['first'] . '&nbsp;' . $row['last'] . '</td>
          </tr>
          <tr>
          <td><b>Message:</b></td>
          <td class="addedMessageText">' . $row['comment'] . '</td>
          </tr>
        
          </table>
          </div>
          <br>';
      }
    } else {
      echo '<font color="#D00000"><b>There have been no messages left in the guestbook yet.</b></font>';
    }
  } catch (PDOException $e) {
    echo 'Exception : ' . $e->getMessage();
    echo "<br/>";
    $db = NULL;
  }
  ?>
</body>

<?php
require 'footer.php';
?>