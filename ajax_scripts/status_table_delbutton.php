<?
require_once('mcl_Oci.php');
$xdm = new mcl_Oci('xdm');

$doc_name = $_POST['deleteTable'];

$sql="DELETE FROM VAL_TABLE_STATUS WHERE CONTRACTNAME = '{$doc_name}' ";
$xdm->fetch($sql);

$sql="DELETE FROM VAL_1 WHERE DOC_NAME = '{$doc_name}'";
$xdm->fetch($sql);

?>