<?
	require_once('mcl_Oci.php');
	$xdm = new mcl_Oci("xdm");

	$user_id = $_POST['user_id'];

	$sql = "DELETE FROM VAL_ADMINS WHERE USER_ID = '{$user_id}'";

	$xdm->query($sql);
?>