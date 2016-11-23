<?php
  /**
   * Calling this service:
   * for owner by Event Id: http://localhost:1234/Centralized/getEventOwner.php?eventId=213 or
   * for owners of all events: http://localhost:1234/Centralized/getEventOwner.php
   */

  include('DatabaseHelper.class.php');
  $connection = new DatabaseHelper();
  $connection->connect(); // open a conn to the Cloud DB

  // get all unqiue event owners (one could own many events)
  $sql = "SELECT attendee.*,user.email FROM attendee INNER JOIN user ON attendee.U_ID=user.U_ID WHERE attendee.isOwner=1 GROUP BY(U_ID)";

  if(isset($_GET['eventId'])) { // if eventId is set change the query string
    $evtId = $_GET['eventId'];
    $sql = "SELECT attendee.*,user.email FROM attendee INNER JOIN user ON attendee.U_ID=user.U_ID WHERE attendee.isOwner=1 AND attendee.E_ID={$evtId}";
  }

  $res = mysqli_query($connection->myconn, $sql); // execute the query
  $result = array();
  while($row = mysqli_fetch_array($res)) {
    array_push($result, array('attendeeId'=>$row['ATT_ID'], 'userId'=>$row['U_ID'], 'eventId'=>$row['E_ID'], 'email'=>$row['email']));
  }
  echo json_encode(array("result"=>$result));

  $connection->close(); // close the db conn
?>
