<?php
require 'header.php';
require 'functions.php';
session_start();
?>

<body>

  <?php
  //messagesent is sent into the url when an entry is successfully entered into the database.
  //This will check to see if messagesent is set, and if it is NOT sent it will display the form to enter a comment.
  if (!isset($_GET['messagesent'])) {
  ?>
    <br>

    <form class="addMessageForm" action="add.inc.php" method="post">
      <?php
      //creates a unique random number that will be attached with the form and the user.
      //when the user submits the form, this random number is set in a session variable and is unique to the user that pressed the submit button.
      $randNum = rand();
      $_SESSION['rand'] = $randNum;
      ?>
      <!--Attaches the unique number to the form. 
          When the form is pressed, this number will be sent along with the form. -->
      <input type="hidden" value="<?php echo $randNum;  ?>" name="randNumCheck">

      <div class="addMessage">
        <table border="0" cellspacing="0" cellpadding="4" width="100">
          <tr valign="top">
            <td>
              <table border="0" cellspacing="0" cellpadding="2">
                <tr>
                  <td nowrap><b>First Name:</b></td>
                  <td><input type="text" name="firstname" size="30" maxlength="25" placeholder="First name here" class="form" value="<?php
                  if (isset($_GET['error']) && $_GET['error'] != 'invalidfirstname') {
                    if ($_GET['error'] == 'emptyfields' || $_GET['error'] == 'invalidlastname' || $_GET['error'] == 'invalidmessage') {
                      echo $_REQUEST['firstname'];
                    }
                  }
                  ?>"></td>
                </tr>
                <tr>
                  <td nowrap><b>Last Name:</b></td>
                  <td><input type="text" name="lastname" size="30" maxlength="25" placeholder="Last name here" class="form" value="<?php
                  if (isset($_GET['error']) && $_GET['error'] != 'invalidlastname') {
                    if ($_GET['error'] == 'emptyfields' || $_GET['error'] == 'invalidfirstname' || $_GET['error'] == 'invalidmessage') {
                      echo $_REQUEST['lastname'];
                    }
                  }
                  ?>"></td>
                </tr>
                <tr valign="top">
                  <td nowrap>
                    <font color="#D00000"><b>Message:</b></font>
                  </td>
                  <td><textarea name="message" cols="48" rows="10" wrap="virtual" placeholder="message here" class="form"></textarea></td>
                </tr>
                <tr>
                  <td colspan="2" align="right"><input type="submit" name="submit" value="Submit"></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </div>

    </form>
    
  <?php
  }
  //if the post button is NOT set, and an error is NOT set, and the message has NOT been sent, display the most recent 5 messages in the database
  //to the page.
  if (!isset($_POST['submit']) && (!isset($_GET['error']) && (!isset($_GET['messagesent'])))) {
  ?>
  <div class="container">
  <h3>5 Most Recent Messages</h3>
</div>
    <?php
    //Join 2 tables together, the guest_users table and the guest_messages table.
    //The tables are joined by the guest_users primary key and the guest_message foreign key.
    //order in descending order to display the 5 most recent entries into the database to the user.
    $sql = "SELECT guest_users.id, guest_users.first, guest_users.last, guest_message.comment, guest_message.date_sent 
    FROM guest_users LEFT JOIN guest_message ON (guest_users.id = guest_message.user_id) ORDER by id DESC LIMIT 5";

    try {
      $db = new PDO(DB_PATH, DB_LOGIN, DB_PW);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


      $result = $db->prepare($sql);
      $result->execute();
      $result->setFetchMode(PDO::FETCH_ASSOC);

      //checks the database, if there is atleast 1 entry in the database fetch the data and display it to the user.
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
  <?php
  }
  //Check to see if an 'error' has been sent back to the user.
  if (isset($_GET['error'])) {
    if ($_GET['error'] == "emptyfields") {
      echo ' <div class="errorMessages"><table border="0" cellspacing="0" width="100"> <tr><td nowrap> - FILL IN <font color="#D00000" nowrap><b>ALL FIELDS</b></font> </td></tr> </table></div><br/>';
    } elseif ($_GET['error'] == "invalidfirstname") {
      echo ' <div class="errorMessages"><table border="0" cellspacing="0" width="100"> <tr><td nowrap> - THE <font color="#D00000"><b>FIRST NAME</b></font> YOU ENTERED IS INVALID.... LETTERS OR HYPHENS ONLY </td></tr> </table></div><br/>';
    } elseif ($_GET['error'] == "invalidlastname") {
      echo ' <div class="errorMessages"><table border="0" cellspacing="0" width="100"> <tr><td nowrap> - THE <font color="#D00000"><b>LAST NAME</b></font> YOU ENTERED IS INVALID.... LETTERS, HYPHENS, OR APOSTROPHE\'S ONLY </td></tr> </table></div><br/>';
    } elseif ($_GET['error'] == "invalidmessage") {
      echo ' <div class="errorMessages"><table border="0" cellspacing="0" width="100"> <tr><td nowrap> - THE <font color="#D00000"><b>MESSAGE</b></font> YOU ENTERED IS IN AN INVALID FORMAT. </td></tr> </table></div><br/>';
    }
  }
  //This if statement will first check if 'messagesent' has been set, AND if a 'token' has been set. If both are true,
  //AND if 'messagesent' = 'success' AND 'token' = the unique number sent with the form when the user clicked the submit button
  if ((isset($_GET['messagesent']) && isset($_GET['token'])) && ($_GET['messagesent'] == "success" && $_GET['token'] == $_SESSION['rand'])) {
    try {
      //open the database
      $db = new PDO(DB_PATH, DB_LOGIN, DB_PW);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      //Joins 2 tables together - guest_users and guest_message - by guest_users Primary Key, and the guest_message Foreign Key.
      //This will ONLY display the message the user has entered on it's own screen because the token must match the session variable.
      $displayOne = "SELECT guest_users.id, guest_users.first, guest_users.last, guest_message.comment, guest_message.date_sent 
                    FROM guest_users LEFT JOIN guest_message ON (guest_users.id = guest_message.user_id) ORDER by id DESC LIMIT 1";

      $query1 = $db->prepare($displayOne);
      $query1->execute();
      $query1->setFetchMode(PDO::FETCH_ASSOC);


      //Checks to see if there is atleast 1 entry in the database. If true, display the entry to the user.
      if ($query1->rowCount() > 0) {
        while ($row = $query1->fetch()) {
          echo '
    <h3>Comment Added</h3>
    <div class="messageAdded">
      <table border="0" cellspacing="0" cellpadding="4" width="100">
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
    <br>

    <div class="container">
    <a class="btn btn-alt" href="index.php">HOMEPAGE</a> </td>
    <a class="btn btn-alt" href="add.php">ADD ANOTHER COMMENT</a> </td>
    <a class="btn btn-alt" href="all.php">VIEW ALL COMMENTS</a> </td>
    </div>';
          session_destroy();
        }
      }
    } catch (PDOException $e) {
      echo 'Exception : ' . $e->getMessage();
      echo "<br/>";
      $db = NULL;
    }
  } elseif((isset($_GET['messagesent']) && isset($_GET['token'])) && ($_GET['messagesent'] == "success" && $_GET['token'] != $_SESSION['rand'])) {
    echo ' <br><div class="errorMessages"><table border="0" cellspacing="0" width="100"> <tr><td nowrap> - YOUR <font color="#D00000"><b>TOKEN</b></font> IS EITHER INVALID OR HAS EXPIRED. PLEASE TRY AGAIN. </td></tr> </table></div><br/>

    <div class="container">
    <a class="btn btn-alt" href="index.php">HOMEPAGE</a> </td>
    <a class="btn btn-alt" href="add.php">ADD ANOTHER COMMENT</a> </td>
    <a class="btn btn-alt" href="all.php">VIEW ALL COMMENTS</a> </td>
    </div>';
  }
  ?>
</body>


<?php
require 'footer.php';
?>