<?
	require_once('mcl_Oci.php');
	$xdm = new mcl_Oci("xdm");

	$sql = "SELECT * FROM VAL_ADMINS WHERE PERMISSIONS = 'ADMIN' ORDER BY NAME";

	$admin_id = array();
	$admins = array();

	while($row = $xdm->fetch($sql)){   
	  array_push($admin_id, $row['USER_ID']);
	  array_push($admins, $row);
	} 

	$users = array();
	$sql = "SELECT * FROM VAL_ADMINS ORDER BY PERMISSIONS, NAME";
	while($row = $xdm->fetch($sql)){   
	  array_push($users, $row);
	} 

?>