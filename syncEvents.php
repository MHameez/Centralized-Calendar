<?php
  /**
   * all the  attributes request params for this service is/are passed via POST method (http verb)
   * hence the URL will remain as: http://localhost:1234/Centralized/syncEvents.php
   *
   * Functions of this service
   * 1. if the user is new, make a new entry for him/her in the User table.
   * 2. update all the vents to the Event table
   * 3. update all attendee(s) detail to attendee table (including the owner, can be a different user than active user)
   */

  include('DatabaseHelper.class.php');
  $connection = new DatabaseHelper();
  $connection->connect(); // open a conn to the Cloud DB

  $eventId=$_POST['id'];
  $title=$_POST['title'];
  $start=$_POST['start'];
  $currentUser=$_POST['currentUser'];
  // debug: print_r($_POST);

  // 1. check if the user is new
  $sql = "SELECT * FROM user WHERE email='{$currentUser}'";
  $res = mysqli_query($connection->myconn, $sql); // execute the query

  if( mysqli_num_rows($res) > 0) { // existing user

    // 2. add the event
    $sql = "INSERT INTO `event`(`gEvent_id`,`title`,`start`,`attendee`) VALUES('$eventId','$title','$start','$currentUser')";
    $result = mysqli_query($connection->myconn, $sql);
    if($result) {
      // 3. ! return the eventId created. Attnedee service counts on this.
      $sql = "SELECT E_ID FROM event WHERE gEvent_id='{$eventId}'";
      $res = mysqli_query($connection->myconn, $sql);

      if(mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_array($res);
        $arr = array('eventId' => $row['E_ID']);
        echo json_encode(array('result'=>$arr));
      }
    }
  } else { // new user

    // 4. insert the new user to user table
    $sql = "INSERT INTO `user`(`email`) VALUES('$currentUser')";
    $result = mysqli_query($connection->myconn, $sql);

    if($result) {
      // 5. insert the new event
      $sql = "INSERT INTO `event`(`gEvent_id`,`title`,`start`,`attendee`) VALUES('unknown','$title','$start','$currentUser')";
      $result = mysqli_query($connection->myconn, $sql);
    }

    if($result) {
      // 6.! return the eventId created. Attnedee service counts on this.
      echo json_encode(array("result"=>$result));
    }
  }
  $connection->close(); // close the db conn
?>
