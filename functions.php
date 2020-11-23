<?php
function validate_inputs($guestFirstName, $guestLastName, $guestMessage) {
  $error_messages = array(); # Create empty array for error messsages.
  if ( strlen($guestFirstName) == 0 ) {
    array_push($error_messages, "You must enter your first name.");
  }
  if ( strlen($guestLastName) == 0 ) {
    array_push($error_messages, "You must enter your last name.");
  }

  if ( strlen($guestMessage) == 0 ) {
    array_push($error_messages, "You must enter a message.");
  }

  return $error_messages;
  
}

define("DB_PATH", "mysql:host=localhost;dbname=guestbook");
define("DB_LOGIN", "root");
define("DB_PW", "root");
