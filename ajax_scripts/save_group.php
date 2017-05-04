<?
	require_once('mcl_Oci.php');
	$xdm = new mcl_Oci("xdm");

	$emails = preg_replace('/\s+/', '', $_POST['email']);
	$group_name = $_POST['group_name'];

	$sql = "INSERT INTO VAL_EMAILGROUPS (ID, GROUP_NAME, PEOPLE_INGROUP)
			VALUES (seq_val_groups.nextval, '{$group_name}', '{$emails}')";

	$xdm->query($sql);

?>