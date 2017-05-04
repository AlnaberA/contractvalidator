<?
	require_once('mcl_Oci.php');
	$xdm = new mcl_Oci("xdm");

	$name = $_POST['name'];
	$user_id = $_POST['user_id'];
	$permissions = $_POST['permissions'];

	$sql = "INSERT INTO VAL_ADMINS (NAME, USER_ID, PERMISSIONS) 
		    VALUES ('{$name}', '{$user_id}', '{$permissions}')";

	$xdm->query($sql);
?>