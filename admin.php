<?
  require_once('secure.php');
  require_once('mcl_Oci.php');
  $xdm = new mcl_Oci("xdm");OEPCOM

  $person = $user['usid'];

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
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="description" content="">
  <meta name="author" content="">


  <title>Admin Validations</title>
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="css/design.css">

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
        <li> <a href="index.php">Home</a></li>
        <li><a href="upload.php">Upload Form</a></li>
        <li><a href="Documents.php">Pending Documents <span class="label label-danger" style="font-size: 8pt;"><?echo $pending_documents?></span></a></li>
        <li><a href="status.php">Validation Status</a></li>
        <li><a href="instructions.php">Instructions</a></li>
        <? if (in_array($person, $admin_id)){ ?>
          <li class="active"><a href="admin.php">Admin</a></li>
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


    <div class="container">
      <div class="page-header"> <h2 style="color: #808080"><strong>Current Admins</strong></h2></div>
        <div id="users"></div>
    </div>

  <center>
      <div style="width: 60%; text-align: left;">
      <table class="table table-bordered" id= "info_table2">
        <thead>
          <tr>
            <th ><strong>Name</strong></font></th>
            <th><strong>User ID</strong></font></th>
            <th><strong>Permissions</strong></font></th>
            <th></th>
           </tr>
        </thead>
      <tbody>
    <?
    $sql = "SELECT * FROM VAL_ADMINS";
    while ($row = $xdm->fetch($sql))
        {
    ?>
          <tr>
          <? echo "<td>".$row['NAME']."</td>" ; ?>
          <? echo "<td>".$row['USER_ID']."</td>" ; ?>
          <? echo "<td>".$row['PERMISSIONS']."</td>" ; ?>
          <? echo "<td class='view' style='width: 10%;'><button id='delete_user_btn' class='btn btn-danger btn-sm' data-userid=".$row['USER_ID'].">Delete</button></a></td>" ; ?>
          </tr>
    <?
        }
    ?>
        </tbody>
      </table>
    </div>
    </center>

<center><button type="button" class="btn btn-primary" data-toggle='modal' data-target='#AddUser'>Add User</button></center><br><br>


<!--This part of the code is the view table for those who have validated and who needs to validate-->
<div class="container">
      <div class="page-header"> <h2 style="color: #808080"><strong>Validation Statuses Admin Table</strong></h2></div><br>
</div>

<div class="container" style="margin-bottom: 100px;">
  <table class="table table-bordered" id="status_table">
    <thead>
  	  <tr>
        <td>Name</td>
        <td>Contract Name</td>
        <td>Date Assigned</td>
        <td>Validated</td>
  	  </tr>
    </thead>

    <tbody>
  		  <?
      $sql = "SELECT * FROM VAL_TABLE_STATUS ORDER BY CONTRACTNAME, VALIDATED";
      while ($row = $xdm->fetch($sql)){ ?>
  	    <tr>
  	 	      <? echo "<td>".$row['USER_NAME']."</td>";
               echo "<td>".$row['CONTRACTNAME']."</td>";
               echo "<td>".$row['DATEASSIGNED']."</td>";

               if ($row['VALIDATED'] == "1"){
                  echo "<td style='background-color: #e6ffe6'>Yes</td>";
               }

               else if ($row['VALIDATED'] == "0"){
                  echo "<td style='background-color: #ffe6e6;'>No</td>";
               } ?>
        </tr>
   <? } ?>

    </tbody>
  </table>
</div>


<!--Scripts Need to be moved to a different javascript file-->
  <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
  <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>

   <script>
    $(document).ready(function(){
      $(document).on('click', '#add_user_btn', function(){
        var name = $('input[name=name]').val();
        var user_id = $('input[name=user_id]').val();
        var permissions = $("#permission option:selected").text();

        $.ajax({
          type:"POST",
          url:"add_a_user.php",
          data: {
            name: name,
            user_id: user_id,
            permissions: permissions
          },
          success:function(data){
            alert('User succesfully added');
            location.reload();
          }
        });
      });

      $(document).on('click', '#delete_user_btn', function(){
        var user_id = this.getAttribute('data-userid');

        $.ajax({
          type:"POST",
          url:"remove_a_user.php",
          data: {
            user_id: user_id
          },
          success:function(data){
            alert('User succesfully deleted');
            location.reload();
          }
        });
      });
    });
  </script>

  <script>
      $(document).ready(function(){
          $('#info_table2').DataTable({
            "bLengthChange": false,
            "info": false,
            'pageLength': 6,
            "aaSorting": []
          });

          $('#info_table2').css('width', '100%');

          $('#status_table').DataTable({
            "bLengthChange": false,
            "info": false,
            'pageLength': 10,
            "aaSorting": []
          });

      });
  </script>
</body>

<div id="AddUser" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="margin-bottom: 0px;">Add User</h4>
      </div>
      <div class="modal-body">
        <form method="post" enctype="multipart/form-data" id="user_form">
          <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Name">
          </div>
          <div class="form-group">
            <label for="user_id">ID:</label>
            <input type="text" class="form-control" id="user_id" name="user_id" placeholder="DTE ID">
          </div>
          <div class="form-group">
            <label for="permission">Permissions:</label>
            <select class="form-control" id="permission" name="permission">
              <option>ADMIN</option>
            </select>
          </div>
        </form>
        <button id="add_user_btn" class="btn btn-primary btn-sm">Add User</button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


</html>
