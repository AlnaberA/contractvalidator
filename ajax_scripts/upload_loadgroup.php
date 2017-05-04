<?
require_once('mcl_Oci.php');
$xdm = new mcl_Oci('xdm');

$group_name = $_POST['group_name'];
$sql = "SELECT * FROM VAL_EMAILGROUPS WHERE GROUP_NAME = '{$group_name}'";
$row = $xdm->fetch($sql);

$group_list = $row['PEOPLE_INGROUP'];

echo $group_list;
?>