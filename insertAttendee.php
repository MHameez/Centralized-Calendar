<?php
  /**
   * This service is responsible for insertion of a tuple to attendee table
   *
   * Calling this service:
   * requires eventId(*), userId(*), isOwner(optiona 1 if owner):
   * http://localhost:1234/Centralized/syncEvents.php?userId=220&userId=57&isOwner=1 // attendee as owner
   * http://localhost:1234/Centralized/syncEvents.php?userId=220&userId=57&isOwner=1 // attendee as Participant
   *
   */
  include('DatabaseHelper.class.php');
  $connection = new DatabaseHelper();


  $eventId = null; $userId = null; $email = null; $isOwner = 0;

  if(isset($_GET['eventId'])) { // optional if email is set
    $eventId = $_GET['eventId'];
  }
  if(isset($_GET['userId'])) { // optional if email is set
    $userId = $_GET['userId'];
  }
  if(isset($_GET['isOwner'])) { // always optional
    $isOwner = $_GET['isOwner'];
  }
  if(isset($_GET['email'])) { // manadatory if event and user is not set
    $email = $_GET['email'];
  }

  if($userId!=null && $eventId!=null) {
    $connection->connect(); // open a conn to the Cloud DB
    $sql = "INSERT INTO `attendee`(`E_ID`,`U_ID`,`isOwner`) VALUES('$eventId','$userId','$isOwner')";
    $result = mysqli_query($connection->myconn, $sql);
    if($result) {
      echo json_encode(array('result' => 'success'));
    }
    $connection->close(); // close the db conn
  }

  if($userId == null && $eventId == null && $email != null) { // use the email to get userId & latest eventID
    $connection->connect(); // open a conn to the Cloud DB

    // fetch the userId using email
    $sql = "SELECT U_ID FROM user WHERE email='$email'";
    $result = mysqli_query($connection->myconn, $sql);
    if($result) {
          $userId = mysqli_fetch_array($result)[0]; // userId

          // fecth latest event ID
          $sql = "SELECT E_ID FROM event ORDER BY E_ID DESC LIMIT 1";
          $result = mysqli_query($connection->myconn, $sql);
          if($result) {
                $eventId = mysqli_fetch_array($result)[0]; // eventId

                // insert new attnedee!!
                $sql = "INSERT INTO `attendee`(`U_ID`,`E_ID`,`isOwner`) VALUES('$userId','$eventId','$isOwner')";
                $result = mysqli_query($connection->myconn, $sql);
                if($result) {
                  echo json_encode(array('result' => 'success'));
                }
          }
    }
    $connection->close(); // close the db conn
  }
?>
