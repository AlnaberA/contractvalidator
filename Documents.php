<?php
  require_once('secure.php');
  $person = $user['usid'];

  require_once('mcl_Oci.php');
  $prod = new mcl_Oci("prod");
  $xdm = new mcl_Oci("xdm");

  include_once('selectAdmins1.php');

  $pending_documents = 0;
  $sql_count = "SELECT * FROM VAL_TABLE_STATUS WHERE USERID = '{$person}' AND VALIDATED = '0' AND SUPERVISOR = '0' ORDER BY CONTRACTNAME";
  while($row_count = $xdm->fetch($sql_count)){
    $pending_documents++;
  }


 /* $sql = "SELECT * FROM VALIDATIONS WHERE PERSONID = '{$person}'";
  $check = "No";
  $row = $prod->fetch($sql);*/

/*  $sql_Contract = "SELECT * FROM VAL_CONTRACT WHERE CONTRACT = '{$contract}'";
  $sql_AssignedTo = "SELECT * FROM VAL_CONTRACT WHERE ASSIGNED_TO = '{$assigned_to}'";*/


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=9">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <meta name="description" content="">
  <meta name="author" content="">
<!--   <link rel="icon" href="../../favicon.ico"> -->

  <title>Current Uploaded Forms Table</title>
  <!-- Jquery api -->
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="css/design.css">


  <!--DataTables script-->
  <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
  <!--end-->


  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <script>
      $(document).ready(function(){
          $('#info_table').DataTable({
            "bLengthChange": false,
            "info": false,
            'pageLength': 10,
            "aaSorting": []
          });
      });
  </script>
</head>

<body>
  <!-- Navbar -->
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
        <li class="active"><a href="Documents.php">Pending Documents <span class="label label-danger" style="font-size: 8pt;"><?echo $pending_documents?></span></a></li>
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
          </li>
        </ul>
      </div>
    </div>
  </nav>

<br>
<center><h1 style="color: #808080"><strong>Pending Forms</strong></h1>

<center>
  <br>
  <br>
  <center>
    <div style="width: 60%;">
    <table class='table table-striped table-condensed table-bordered' id="info_table">
      <thead>
        <tr>
          <td style="text-align: center;"><strong>Assigned by:</strong></td>
          <td style="text-align: center;"><strong>Document Name:</strong></td>
          
          <? if ($row['SUPERVISOR'] = '0') { ?>
          		<td style="text-align: center;"></td>
          <? } 
             
             else if($row['SUPERVISOR'] = '1'){?>
             	<td style="text-align: center;"></td>
             <? } ?>
        </tr>
      </thead>
      <tbody>

      <?
  			  $sql = "SELECT * FROM VAL_TABLE_STATUS WHERE USERID = '{$person}' AND VALIDATED = '0' ORDER BY CONTRACTNAME";

              $sql_supervisors = "SELECT SUPERVISOR_ID FROM VAL_1";

              $supervisors = array();

              while($row_super = $xdm->fetch($sql_supervisors)){   
                array_push($supervisors, $row_super['SUPERVISOR_ID']);
              } 

              /*print_r($supervisors);
              echo '-';
              echo $person;*/
              
              $count = 0;
              while($row = $xdm->fetch($sql)){
                $count++;
                $get_assigner = "SELECT NAME FROM VAL_1 WHERE DOC_NAME = '{$row['CONTRACTNAME']}'";
                $val_1_row = $xdm->fetch($get_assigner);
                $assigner = $val_1_row['NAME'];
              ?>
                  <tr class='dataRows'>
                    <td class='name' style="text-align: center;"><?echo $assigner;?></td>
                    <td class='doc' style="text-align: center;"><?echo $row["CONTRACTNAME"];?></td>

                    <? if ($row['SUPERVISOR'] == '0') { ?>
                      <td class='view' style='width: 13.5%; text-align: center;' onClick="window.open('view.php?DOC=<?=$row["FILENAME"];?>','mywindow','width=400,height=200,toolbar=yes, location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,copyhistory=yes, resizable=yes')">
                      <button name='view_btn' class='btn btn-success btn-md'>View and Sign</button></td>
                    <? } 
                       else if($row['SUPERVISOR'] == '1'){?>
                       <td class='view' style='width: 13.5%; text-align: center;' onClick="window.open('view_supervisor.php?DOC=<?=$row["FILENAME"];?>','mywindow','width=400,height=200,toolbar=yes, location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,copyhistory=yes, resizable=yes')">
                      <button name='view_btn' class='btn btn-success btn-md'>View</button></td>
                    <? } 
                       else { ?>
                          <td class='noFile' style='width: 13.5%; text-align: center;'><button name='view_btn' class='btn btn-success btn-md disabled' data-toggle='tooltip' title='No File Uploaded'>No file</button></a></td>
                    <? }?>

              <? } //end of while loop

                if ($count == 0){?>
                  <tr>
                    <td colspan="3"><div class="alert alert-info" style="text-align: center;"><strong>You have no forms waiting your validation</strong></div></td>
                  </tr>
            <? } ?>

            <?
           
            ?>
    </tbody>
</table>
</div>
</body>
</html>


