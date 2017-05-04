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
<div style="margin-bottom: 100px;">
	<br><br>
	<button type="button" class="btn btn-danger" onClick="window.close()">Back</button>
</div>
</center>

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</html>