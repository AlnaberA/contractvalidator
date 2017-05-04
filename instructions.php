<?php
  require_once('secure.php');
  $person = $user['usid'];
  require_once('mcl_Oci.php');
  include_once('selectAdmins1.php');

  $pending_documents = 0;
  $sql_count = "SELECT * FROM VAL_TABLE_STATUS WHERE USERID = '{$person}' AND VALIDATED = '0' AND SUPERVISOR = '0' ORDER BY CONTRACTNAME";
  while($row_count = $xdm->fetch($sql_count)){
    $pending_documents++;
  }

?>



<!DOCTYPE html>
<html>
<head>
	<title>Validation Instructions</title>  
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
  	<link rel="stylesheet" href="css/design.css">
  	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
  	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>



<body>
	<!--NavBar-->
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php">Validator</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li><a href="index.php">Home</a></li>
        <li><a href="upload.php">Upload Form</a></li>
        <li><a href="Documents.php">Pending Documents <span class="label label-danger" style="font-size: 8pt;"><?echo $pending_documents?></span></a></li>
        <li><a href="status.php">Validation Status</a></li>
        <li class="active"><a href="#">Instructions</a></li>
              <? if (in_array($person, $admin_id)){ ?>
                <li><a href="admin.php">Admin</a></li>
              <? } ?>
        </ul>
       <ul class="nav navbar-nav navbar-right">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <? echo $name ?><span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li style="padding-bottom: 5px"><center><?mcl_Header::logout_btn();?></center></li>
          </ul>
      </div>
    </div>
  </nav>

<legend>Instructions</legend>

<img id="instrpart1" src="instructionsImages/instructions_upload_tabpart1.png" alt="instruction img 1" 
	style="height: 5%; width: 75%">
<strong><hr></strong><br>

<img id="instrpart2" src="instructionsImages/instructions_upload_tabpart2.png" alt="instruction img 2" 
	style="height: 1%; width: 75%">
<strong><hr></strong><br>

<img id="instrpart3" src="instructionsImages/instructions_pendingDocuments_tabpart1.png" alt="instruction img 3" 
	style="height: 5%; width: 75%">
<strong><hr></strong><br>

<img id="instrpart4" src="instructionsImages/instructions_PendingDocuments_tabpart2.png" alt="instruction img 4" 
	style="height: 5%; width: 75%">
<strong><hr></strong><br>

<img id="instrpart4" src="instructionsImages/instructions_View_page.png" alt="instruction img 4" 
	style="height: 10%; width: 75%">
<strong><hr></strong><br>

<img id="instrpart4" src="instructionsImages/instructions_validationStatus_tabpart1.png" alt="instruction img 4" 
	style="height: 10%; width: 75%">




</body>
</html>