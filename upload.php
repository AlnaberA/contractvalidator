<?php
  include_once('selectAdmins1.php');
  require_once('secure.php');
  include('database.php');
  $person = $user['usid'];
  $name = $user['name'];

  $pending_documents = 0;
  $sql_count = "SELECT * FROM VAL_TABLE_STATUS WHERE USERID = '{$person}' AND VALIDATED = '0' AND SUPERVISOR = '0' ORDER BY CONTRACTNAME";
  while($row_count = $xdm->fetch($sql_count)){
    $pending_documents++;
  }

  $select_groups = "SELECT * FROM VAL_EMAILGROUPS ORDER BY GROUP_NAME";

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Uploads</title>
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
  <link rel="stylesheet" href="css/design.css">


  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

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
        <li><a href="index.php">Home</a></li>
        <li class="active"><a href="upload.php">Upload Form</a></li>
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
  </nav><br><br>

<div class="container">
  <h1>Upload a File for Validations</h1><hr>
  <h2 style="margin-bottom: 0px;"> <p><label for="email">Select Workers to Email: </label><a href="http://lnx826:63258/" target="_blank">Email List Generator Here</a></h2>
  <h6 style="margin-top: 0px; color: #949494">*Stuck filling out this page? Go to the <a href="instructions.php">Instruction Page</a>!</h6>


  <form onsubmit="return checkEmailInput();" action="upload_submitbutton.php?" id="submit_doc_form" method="post" enctype="multipart/form-data">

    <p><legend>Document Name/Emails:</legend></p>

    <!--Document Input-->
    <div class="form-group">
      <label for="doc_name">Document Name: </label>
      <input type="text" name="doc_name" class="form-control" id="doc_name" placeholder="Document Name">
    </div><br>

    <div style="float:left;">
        <b>Email List:</b><br>
        <h6 style="color: #949494; margin-top: 0px; margin-bottom: 5px;">*Please make sure there are semicolons or commas between each email. Example: alaa.al-naber@dteenergy.com; brian.atiyeh@dteenergy.com</h6>
    </div>

    <!--Save group Button-->
    <div style="float:right; margin-bottom: 5px;">
      <div class="btn-group">
        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Save Group <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li style="width: 220px;">
              <div class="input-group" style="width: 90%; margin-left: 10px;">
                <b> Name of group:</b>
                <input type="text" name="group_name" id="group_name" class="form-control" placeholder="Name of Group" style="margin-bottom: 5px;">
                <button type="button" id="save_group_btn" class="btn btn-default btn-xs" style="margin-right: 100%;">Save</button>
              </div>
          </li>
        </ul>
      </div>

      <!--Load group Button-->
      <div class="btn-group">
        <button type="button" data-toggle="modal" data-target="#upload_loadbutton_Modal"
        name="upload_loadbutton" id="upload_loadbutton" class="btn btn-primary btn-sm" aria-haspopup="true" aria-expanded="false">Load Group</button>
      </div>
    </div>

    <!-- Textarea for email entry -->
    <div class="form-group">
      <textarea maxlength = "4000" onKeyPress="return taLimit(this)" onKeyUp="return taCount(this,'myCounter')"  rows="5" cols="40"  class="form-control" id="email" name="email" id="email" placeholder="Enter the employees in the text box"></textarea>
      <div style="float:right; height: 10px;">
        Remaining characters: <b><span id=myCounter>4000</span></b>
      </div>
    </div><br>

    <h3 style="margin-bottom: 5px;">Email Content:</h3><hr style="margin-top: 0px;">


      <!--From- of email Input-->
    <div class="form-group">
      <label for="from">From: </label>
      <input type="text" name="from" class="form-control" id="from" value="">
    </div><br>



      <!--Subject- of email Input-->
    <div class="form-group">
      <label for="subject">Subject: </label>
      <input type="text" name="subject" class="form-control" id="subject" value="There is a new document for you to read">
    </div><br>

    <!--Body- of email Input-->
    <div class="form-group">
      <label for="body">Message: </label>
      <textarea maxlength = "4000" rows="15" cols="40"  class="form-control" id="body" name="body">
       You have a new document to read<br><br>
       Step 1: Go to the following link <a href="http://lnx825:63372/">http://lnx825:63372/</a><br>
       Step 2: Login using your dteenergy information.<br>
       Step 3: Navigate to the uploaded files tab.<br>
       Step 4: Click the view button next to the correct document name.<br>
       Step 5: Read, check agree, and enter your esignature at the bottom of the page.<br><br>
       If you do not see the document then the assigner has canceled the document.</textarea>
    </div>

    <br>

    <!--Supervisor option radio button-->
    <div class="radio">
	<b>Do you want to CC someone?</b><br>
		<label><input class="radio-inline" type="radio" id="radioY" value="Yes" name="optradio">Yes</label>
		<label><input class="radio-inline" type="radio" id="radioN" value="No" name="optradio">No</label>
	</div>

    <!--Supervisor email list-->
    <div id="supervisor_info" style="float:left; display:none;" >
        <b>CC List:</b><br>
        <h6 style="color: #949494; margin-top: 0px; margin-bottom: 5px;">*This list will only be able to view the document<br>
        *Please make sure there are semicolons or commas between each email.</h6>
    </div>

    <!--Supervisor email input-->
    <div id="supervisor_info_email" class="form-group" style="display:none;">
      <textarea maxlength = "4000" rows="5" cols="40"  class="form-control" id="supervisor_email" name="supervisor_email" placeholder="Enter the employees in the text box"></textarea>
    </div><br>

    <!--Body of supervisor email Input-->
    <div id="supervisor_info_body" class="form-group" style="display:none;">
      <label for="body">CC Message: </label>
      <textarea maxlength = "4000" rows="15" cols="40"  class="form-control" id="supervisor_body" name="supervisor_body">
       You have a new document to read<br><br>
       Step 1: Go to the following link <a href="http://lnx825:63372/">http://lnx825:63372/</a><br>
       Step 2: Login using your dteenergy information.<br>
       Step 3: Navigate to the uploaded files tab.<br>
       Step 4: Click the view button next to the correct document name.<br><br>
       If you do not see the document then the assigner has canceled the document.</textarea>
    </div>

    <br>
    <!-- File Input -->
    <input type="file" name="DOC" id="DOC" style="visibility: hidden;">
    <label>Choose a File: </label>
    <div class="input-append form-inline form-group input-group" style="width: 30%;">
      <input type="text" id="subfile" name="subfile" class="form-control" aria-describedby="browse_btn" readonly>
      <span class="input-group-btn" id="browse_btn"><a class="btn btn-primary" onclick="$('#DOC').click();">Browse</a></span>
    </div><br><br>

    <!-- Submit Button -->
    <input type="submit" name="submit" class="btn btn-success" id="submit" value="Submit" style="margin-bottom: 100px;">
  </form>
</div>
</body>

<!-- Modal for loading a group-->
<div class="modal fade" id="upload_loadbutton_Modal" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" style="margin-bottom: 0px;">Load Group</h4>
      </div>
      <div class="modal-body">
        <table class='table table-condensed table-bordered' id="savedGroups_table" style="text-align: center;">
          <thead>
            <th>Group name</th>
            <th></th>
          </thead>
          <tbody>
            <? while ($row = $xdm->fetch($select_groups)) { ?>
                   <tr>
                   <? echo "<td>".$row['GROUP_NAME']."<td>"; ?>
                   <button type="button" name = "SelectButton" id="group_SelectButton" class="btn btn-info btn-sm group_SelectButton" data-id="<?echo $row['GROUP_NAME']?>">Select</button>
                   </tr>
            <? } ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js"></script>

<!--Text area script-->
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script>
tinymce.init({ 
  selector:'textarea#body',
});
tinymce.init({ 
  selector:'textarea#supervisor_body',
});
</script>
<!--End script-->

<!--Supervisor slidedown-->
<script>
jQuery(function() {
		jQuery("input[name=optradio]").change(function() {

			if($(this).val() == "Yes") {
				jQuery("#supervisor_info").slideDown()
				jQuery("#supervisor_info_email").slideDown()
				jQuery("#supervisor_info_body").slideDown()
			}

			if($(this).val() == "No") {
				jQuery("#supervisor_info").slideUp();
				jQuery("#supervisor_info_email").slideUp()
				jQuery("#supervisor_info_body").slideUp()
			}
		});
	});


</script>



<!--Character counter script-->
<script>
  maxL=4000;
  var bName = navigator.appName;

  /*Function taLimit is used  for the Key Press event for the text box or text area. When a key is pressed this function checks if the total number of characters typed equals the limit allowed (value maxL defined in the javascript code). If the limit is reached then it return false thus not allowing any further key press event.*/
  function taLimit(taObj) {
    if (taObj.value.length==maxL) return false;
    return true;
  }

  /*Function taCount is used for the Key Up event. We use this to change the value of the counter displayed and to truncate the excess characters if any (example if someone has cut and pasted the value into the field when we have allowed paste). To disable paste add the property onpaste="return false" to the field. We have used the inner text property of the span element to change the counter displayed.*/
  function taCount(taObj,Cnt) {
    objCnt=createObject(Cnt);
    objVal=taObj.value;

    if (objVal.length>maxL)
      objVal=objVal.substring(0,maxL);

    if (objCnt) {
      if(bName == "Netscape"){
        objCnt.textContent=maxL-objVal.length;}
      else{objCnt.innerText=maxL-objVal.length;}
    }

    return true;
  }

  function createObject(objId) {
    if (document.getElementById)
      return document.getElementById(objId);

    else if (document.layers)
      return eval("document." + objId);

    else if (document.all)
      return eval("document.all." + objId);

    else
      return eval("document." + objId);
  }
</script>

<!--Save and load emails script-->
<script>
$(document).ready(function(){
  $(document).on('click','#save_group_btn', function(){
  var email = $('#email').val();
  var group_name = $('input[name=group_name]').val();

  if (email != ''){
    $.ajax({
      type: "POST",
      url: "ajax_scripts/save_group.php",
      data:{
        email:email,
        group_name: group_name
      },
      success:function(data){
          alert("Group successfully saved");
      }
    });
  }

  else
    alert("Please enter emails to save for the group");
  });

  $('#submit_doc_form').validate({
      ignore: [],
      rules: {
          doc_name: {
              required: true
          },
          email: {
              required: true
          },
          subfile: {
              required: true
          },
          from: {
              required: true
          },
          subject: {
              required: true
          },
          body: {
              required: true
          }
      },
      highlight: function (element) {
          $(element).closest('.form-group').removeClass('has-success').addClass('has-error').addClass('remove-margin');
      }
  });

  $('#DOC').change(function(){
    $('#subfile').val($(this).val());
  });

  $('#showHidden').click(function(){
    $('#DOC').css('visibility', 'visible');
  });

  $('#savedGroups_table').DataTable({
    "bLengthChange": false,
    "info": false,
    'pageLength': 10,
    "aaSorting": []
  });
  $('#savedGroups_table').css('width', '100%');


  $(document).on('click','.group_SelectButton', function(){
    var group_name = this.getAttribute('data-id');

    $.ajax({
          type: "POST",
          url: "ajax_scripts/upload_loadgroup.php",
          data:{
            group_name: group_name
          },
          success:function(data){
              $("#email").val(data);
              $("#upload_loadbutton_Modal").modal("hide");
              taCount(document.getElementById("email"),'myCounter');
          }
    });
  });
});
</script>

<!-- Checking email input  -->
<script>
/*Fully working for all the test cases I could think of (doesn't submit form if it fails either)*/
function checkEmailInput() {
    var input = document.getElementById("email").value;
    var delimeters = [';', ',', '/', '|'];
    var emailLength = 14; //Length of dteenergy.com
    var pos = input.indexOf("@", pos) + emailLength;
    var flag = true;

    while(flag == true && pos < input.length - 1){ 
      if(delimeters.indexOf(input.charAt(pos)) == -1){ //Changed this to searching for a delimeter in our list of delimeters in an array. -1 means it isn't in our array
        alert('Error: Invalid input. Please ensure there is a semicolon or comma seperating each email.');
        flag = false;
        return flag;
      }

      pos = input.indexOf("@", pos) + emailLength;
    }

    return flag;
}

</script>
</html>
