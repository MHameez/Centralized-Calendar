<?php

include ('dbconnect.inc.php');


  //$AccName=$_POST['accountName'];
    $AccName=$_POST['id'];
    $Email="hameezrox@yahoo.com";


  $sql="INSERT INTO `user`(`username`,`email`) VALUES('$AccName','$Email')";
  $result=mysqli_query($con,$sql);

  if($result) {
    echo ('successfully INSERTED');
  } else {
    echo ('Not inserted!!');
  }
  mysqli_close($con)
?>
