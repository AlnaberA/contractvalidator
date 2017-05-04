<?
  require_once('secure.php');
  require_once('mcl_Oci.php');
  include_once('selectAdmins1.php');

  $person = $user['usid'];
  $name = $user['name'];

  $sql_ContractName = "SELECT * FROM VAL_1 WHERE WKID = '{$person}' ORDER BY  DOC_NAME";

  $pending_documents = 0;
  $sql_count = "SELECT * FROM VAL_TABLE_STATUS WHERE USERID = '{$person}' AND VALIDATED = '0' AND SUPERVISOR = '0' ORDER BY CONTRACTNAME";
  while($row_count = $xdm->fetch($sql_count)){
    $pending_documents++;
  }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="css/design.css">
</head>

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
        <li class="active"><a href="status.php">Validation Status</a></li>
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
            </li>
          </ul>
        </ul>
      </div>
    </div>
  </nav>
  <!--Select Box-->
<div class="container">
  <div style="display: block; margin: 0 auto;" align="center">
    <h6 style="color:#808080">*You may only select from documents that you have assigned for validation</h6>
    <select name="contracts" id="document_name" style="width: 35%;">
      <option></option>
      <? while ($row = $xdm->fetch($sql_ContractName))
      {?>
          <option><?echo $row{'DOC_NAME'}?></option>  
      <?}?>
    </select>
  </div>
</div>

<div id="loading-image" style='margin-bottom: 50px;'><img src="img/gear.gif"/></div>
<div id="document_status_table"></div>

<script>
$(document).ready(function(){
  $('#document_name').select2({
      placeholder: 'Select Document Name...'
  });

  $(document).on('change', '#document_name', function(e){
     $('#document_status_table').hide()
     $('#loading-image').show();
     var documentSelected = $("option:selected", this);
     var document_select_val = this.value;

     $.ajax({
       type:"POST",
       url:"ajax_scripts/status_table.php",
       data: {
         document_select_val: document_select_val
       },
       success:function(data){
         $('#document_status_table').html(data).slideDown('1800');
         $('#status_table').DataTable({
                "bLengthChange": false,
                "info": false,
                'pageLength': 10,
                "aaSorting": []
          });

          $('#approval_table').css('width', '100%');
          $('#loading-image').hide(); 
       }
     });
  });

   $(document).on('click', '#status_table_delButton', function(e){
     var deleteTable = this.getAttribute('data-name');

     alert(deleteTable);

     $.ajax({
       type:"POST",
       url:"ajax_scripts/status_table_delbutton.php",
       data: {
         deleteTable: deleteTable
       },
       success:function(data){
          alert('Table successfully deleted.');
       		location.reload(); 
       }
       
     });
	});
});
</script>

<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
</html>
