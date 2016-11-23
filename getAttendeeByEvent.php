<?php
  /**
   * calling this service: http://localhost:1234/Centralized/getAttendeeById.php?eventId=157
   */
  include('DatabaseHelper.class.php');
  $connection = new DatabaseHelper();

  $eventId = null;
  if(isset($_GET['eventId'])) { // mandatory
    $connection->connect();

    $eventId = $_GET['eventId'];
    $sql = "SELECT * FROM attendee WHERE E_ID='$eventId'";
    $res = mysqli_query($connection->myconn, $sql);

    $result = array();
    while($row = mysqli_fetch_array($res)) {
      array_push($result, array('attendeeId' => $row['ATT_ID'], 'userId' => $row['U_ID'], 'eventId' => $row['E_ID'], 'isOwner' => $row['isOwner']));
    }
    echo json_encode(array("result"=>$result));
    $connection->close();
  }
?>
