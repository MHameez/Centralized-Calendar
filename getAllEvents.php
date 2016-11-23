<?php
 /** calling this service:
   * Note: when multiple emials are given it will get events for each of those emails.
  */
  include('DatabaseHelper.class.php'); //Include the coonnection class
  $connection = new DatabaseHelper();
  $connection->connect();

  $evtIds = array();
  $events = array(); // Warning! array of events with duplicate events

  if(isset($_GET['email'])) {
    $email = array();$email = $_GET['email'];

    for($i = 0; $i < count($email); $i++) { // get evetns for email ids passed
      $tempMail = $email[$i];
      $sql = "SELECT U_ID FROM user WHERE email='{$tempMail}'";
      $res = mysqli_query($connection->myconn, $sql);

      if(mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_array($res); // get the userId using email
        $userId = $row['U_ID'];

        // now get all attendee record for this user..
        $sql = "SELECT E_ID FROM attendee WHERE U_ID={$userId}";
        $res = mysqli_query($connection->myconn, $sql);
        while($row = mysqli_fetch_array($res)) {
          array_push($evtIds, $row['E_ID']); // here we are getting all the vents asosciated with all emial ids passed
        }
      }
    } // end of this we have accumilted all evetn ids

    // now populate the evets array
    $ids = join("','",$evtIds);
    // debug: echo "<br/> $ids";

    $sql = "SELECT * FROM event WHERE E_ID IN ('$ids')";
    $res = mysqli_query($connection->myconn, $sql);
    while($row = mysqli_fetch_array($res)) {
      array_push($events, array('eventId'=>$row['E_ID'], 'gcalId'=>$row['gEvent_id'], 'title'=>$row['title'], 'startDate'=>$row['start'], 'attendee'=>$row['attendee']));
    }
    echo json_encode(array("result"=>$events));
  }
  $connection->close(); // close the conn
?>
