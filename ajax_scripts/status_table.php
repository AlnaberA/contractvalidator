
<?php
require_once('mcl_Oci.php');
$xdm = new mcl_Oci('xdm');
$contract_name = $_POST['document_select_val'];

$sql = "SELECT * FROM VAL_TABLE_STATUS WHERE CONTRACTNAME = '{$contract_name}' AND SUPERVISOR = 0 ORDER BY USER_NAME";

$table = '
<div class="container">
  <div class="page-header"> <h2 style="color: #808080"><strong>Status</strong></h2></div><br>
  <table class="table table-bordered" id="status_table">
    <thead>
  	  <tr>
        <td>Name</td>
        <td>Contract Name</td>
        <td>Date Assigned</td>
        <td>Date Signed</td>
        <td>Validated</td>
  	  </tr>
    </thead>
    <tbody>';
    while ($row = $xdm->fetch($sql))
      {
  	    $table .= "<tr>";
  	 	      $table .= "<td>".$row['USER_NAME']."</td>" ;
            $table .= "<td>".$row['CONTRACTNAME']."</td>" ;
            $table .= "<td>".$row['DATEASSIGNED']."</td>" ;
  	        $table .= "<td>".$row['DATESIGNED']."</td>" ;
            if ($row['VALIDATED'] == "1"){
                $table.= "<td style='background-color: #e6ffe6'>Yes</td>";
            }
            else if ($row['VALIDATED'] == "0"){
                $table.= "<td style='background-color: #ffe6e6;'>No</td>";
            };
        $table .= "</tr>";
      }

$table .= '</tbody>      
        
      </table>
</div>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<div style="text-align:center;">
        <button type="button" class="btn btn-danger" id="status_table_delButton" data-name="'.$contract_name.'">Cancel Document</button>
</div>';
$table .="";
echo $table;

?>


