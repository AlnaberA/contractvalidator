<?
require_once('mcl_Oci.php');
$xdm = new mcl_Oci("xdm");

$user_id = $_POST['id'];
$doc_name = $_POST['doc_name'];
$esign = $_POST['esign'];
$today = date("m/d/Y", strtotime("now"));

$sql = "UPDATE VAL_TABLE_STATUS
		SET DATESIGNED = '{$today}',
			VALIDATED = '1',
			ESIGN = '{$esign}'
		WHERE CONTRACTNAME = '{$doc_name}' AND USERID='{$user_id}' AND SUPERVISOR = '0'";

$xdm->query($sql);
?>
