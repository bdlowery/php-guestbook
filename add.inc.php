<?php 
session_start();
//Check to see if the submit button has been pressed AND if the unique number set in the form is equal to the unique number set in the session.
if(isset($_POST['submit']) && $_POST['randNumCheck'] == $_SESSION['rand']) {
  require 'functions.php';

  //Set a variable for each input in the table.
  $guestFirstName = $_POST['firstname'];
  $guestLastName = $_POST['lastname'];
  $guestMessage = $_POST['message'];

  //remove whitespace from beginning and end for each input.
  $guestFirstName = trim($guestFirstName);
  $guestLastName = trim($guestLastName);
  $guestMessage = trim($guestMessage);

  


  //check if any of the input fields were left empty.
  if(empty($guestFirstName) || empty($guestLastName) || empty($guestMessage)) {
    //if any of these fields are empty, send the user back to the add page with an error message
    //the error message in this case is "emptyfields", and sends them back with some of the information the user entered so they...
    //don't need to retype any information that was correct.

    header("Location: ../guestbook-php/add.php?error=emptyfields&firstname=" . $guestFirstName . "&lastname=" . $guestLastName);
    exit();
  } 
  //check for valid first name. only want letters, no numbers or special characters (hyphens allowed)
  //if when searching the firstname field there is anything other than lowercase or uppercase letters return an error.
  elseif(!preg_match("/^[a-zA-Z-]*$/", $guestFirstName)) {
    header("Location: ../guestbook-php/add.php?error=invalidfirstname&lastname=" . $guestLastName);
    exit();
  }
  //same concept as first name.
  //return an error if there is anything but letters in the field (or a hyphen or apostrophe), but this time return the first name back to the user since
  //for this elseif to run the firstname field would have had to been correct.
  elseif(!preg_match("/^[a-zA-Z-\']*$/", $guestLastName)) {
    header("Location: ../guestbook-php/add.php?error=invalidlastname&firstname=". $guestFirstName);
    exit();
  }

  //after firstname, and lastname are validated check the message. 
  //if the message contains anything but what's specified, return an error.
  elseif(!preg_match("/^[a-zA-Z0-9\s:,.!?\";_()+=*\'-]*$/", $guestMessage )) {
    header("Location: ../guestbook-php/add.php?error=invalidmessage&firstname=". $guestFirstName . "&lastname=" . $guestLastName);
    exit();
  }
  //else, put the information into the database
  else {

    try {
      //open the database
      $db = new PDO(DB_PATH, DB_LOGIN, DB_PW);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      //insert the first name, last name, and message into the database

      $stmt = $db->prepare("INSERT INTO guest_users (first, last) VALUES(:f,:l);");
      $stmt->bindParam('f', $guestFirstName);
      $stmt->bindParam('l', $guestLastName);
      $stmt->execute();

      $insertID = $db->lastInsertId();

      $stmt2 = $db->prepare("INSERT INTO guest_message (comment, user_id, date_sent) VALUES (:m,:i, now());");
      $stmt2->bindParam('m', $guestMessage);
      $stmt2->bindParam('i', $insertID);
      $stmt2->execute();

      
      

      header("Location: ../guestbook-php/add.php?messagesent=success&token=".$_SESSION['rand']);
      exit();

  } catch (PDOException $e) {
    echo 'Exception : ' . $e->getMessage();
    echo "<br/>";
    $db = NULL;
  }
} 
}

//send the user back to the add page if they try and access this page without clicking the submit button

else {
  header("Location: ../guestbook-php/add.php");
  exit();
}
