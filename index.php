<?php
  require_once('secure.php');
  $person = $user['usid'];

  require_once('mcl_Oci.php');
  $prod = new mcl_Oci("prod");
  $xdm = new mcl_Oci('xdm');

  $sql = "SELECT * FROM VALIDATIONS WHERE PERSONID = '{$person}'";
  $check = "No";
  $row = $prod->fetch($sql);

  include_once('selectAdmins1.php');

  $pending_documents = 0;
  $sql_count = "SELECT * FROM VAL_TABLE_STATUS WHERE USERID = '{$person}' AND VALIDATED = '0' AND SUPERVISOR = '0' ORDER BY CONTRACTNAME";
  while($row_count = $xdm->fetch($sql_count)){
    $pending_documents++;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>

  <title>Validations Forms</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="css/design.css">


  <!--DataTables script--><!--Change made by Alaa on 9/23/2016-->
  <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
  <!--end-->

</head>
<body>

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
        <li class="active"><a href="#">Home</a></li>
        <li><a href="upload.php">Upload Form</a></li>
        <li><a href="Documents.php">Pending Documents <span class="label label-danger" style="font-size: 8pt;"><?echo $pending_documents?></span></a></li>
        <li><a href="status.php">Validation Status</a></li>
        <li><a href="instructions.php">Instructions</a></li>
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
  </nav><br><br><br>


<div class="container text-left">
    <h1 style="color: #B1B1B1;"><strong>Curent Forms In The Archive</strong></h1>
 <?
  $sql = "SELECT * FROM VAL_1  ORDER BY NAME ASC";
  $data = array();

    $table =
      "<center>
          <table class='table table-striped table-condensed' id='tableAll';>";

  $table .= "
       <thead>
        <tr>
          <th> Form Name:</th>
          <th> Name of the Uploader: </th>
        </tr>
      </thead>
      <tbody>";

  //Counts the number of rows as they are outputted
  $count = 0;
  while($row = $xdm->fetch($sql))
  {
    $editID = $row['ID'];

    $data[] = $row['TITLE'];
    $table .= "<tr class='dataRows'>";
      $table .= "<td class='doc'>".$row['DOC_NAME']."</td>";

      $table .= "<td class='name'>".$row['NAME']."</td>";

    $table .= "</tr>";

    $count += 1;
  }

  if ($count == 0){
      $table .= "<tr>
              <td colspan = '6' style='text-align: center;'><div class='alert alert-warning'>There are no forms to validate.</div></td>
             </tr>";
  }

  $table.=
        "</tbody>
      </table>
    </center>";

  echo $table;

?>
</div>

<? if ($pending_documents > 0){ ?>
    <div class="alert alert-danger bounce fragment" id="fragment">
      <a href="#" class="close" id="close_alert" data-dismiss="alert" aria-label="close">&times;</a>
      You have documents pending your validation
    </div>
<? } ?>

</body>
</html>
<script>
$(document).ready(function() {
    $('#tableAll').DataTable({
      'pageLength': 14,
      "bLengthChange": false,
      "info": false,
    });

    $('#tableAll').css('width', '100%');

    $('[data-toggle="tooltip"]').tooltip();
});
</script>