<?php
	require_once('mcl_Oci.php');
	require_once('secure.php');
	$xdm = new mcl_Oci("xdm");
	$person = $user['usid'];
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

</head>

<body>
<?
	$filename = $_GET['DOC'];
	$sql = "SELECT * FROM VAL_1 WHERE DOC = '{$filename}'";
	$row = $xdm->fetch($sql);

	$doc_name = $row['DOC_NAME'];

	$path = 'Upload/'.$filename;
		
	if (file_exists($path)){
	?>
   <br><br>
  <h2><center>  <? echo $filename; ?> </center></h2>
  <center><object data="<?= $path ?>" type="application/pdf" width="1000px" height="900px"></center>
     
</center>
     </object>
    
<? }else{ ?>
<script>
		alert('Error: File not found.');
		window.location = 'Documents.php';
</script>
<? } ?>

<center>
	<div class="container" style="margin-bottom: 100px;">
	    <form style = 'width: 500px;text-align:left;' method = 'POST'>
	    	<p style="text-align: center; margin-bottom: 15px;"> I hereby attest that I have read and understand the information provided to me regarding the updates to the Forms uploaded </p>
		    <div class="checkbox">
			  <input id="checkAccepted" name="checkbox" type="checkbox" style="margin-left: 0px;"/>
			  <label for="check">I have read and accepted the terms of conditions.</label><!-- for must be the id of input -->
			</div>

		   	E-Signature:<input type = "text" name = "esign" class="form-control" required>(Enter Name)<br><br>
			<button type="button" name="enter" class="btn btn-primary" id="btnAgree" style="float:left;">Agree</button>
				<button type="button" class="btn btn-danger" onClick="window.close()" style="float:right;">Cancel</button>

				<input type="hidden" id="doc_name" name="doc_name" value="<?echo $doc_name?>">
				<input type="hidden" id="id" name="id" value="<?echo $person;?>">
		</form>
	</div>
</center>

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!--Ajax script for agree button to change state-->
<script> 
	$(function() {
	  var chk = $('#checkAccepted');
	  var btn = $('#btnAgree');

	  chk.on('change', function() {
	    btn.prop("disabled", !this.checked);//true: disabled, false: enabled
	  }).trigger('change'); //page load trigger event
	});

	$(document).ready(function(){ 
	  $(document).on('click','#btnAgree', function(){
	      var esign = $('input[name=esign]').val();
	      var id = $('input[name=id]').val();
	      var doc_name = $('input[name=doc_name]').val();

	      $.ajax({
	        type: "POST",
	        url: "ajax_scripts/view_submit.php",
	        data:{
	          esign: esign,
	          id: id,
	          doc_name: doc_name
	        },
	        success:function(data){
	        	alert("Successfully validated document");
	            window.location = "Documents.php";
	        }
	      });
	  });
	});
</script>   

</html>