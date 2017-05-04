<?
	// PLEASE DO NOT GO TO THIS PAGE IN WEB BROWSER OR THE SCRIPT WILL RUN. 

	// To test - change DATEASSIGNED of the required CONTRACTNAME under your USER_NAME in the VAL_TABLE_STATUS table to either exactly 1 week or exactly 1 month ago. Also change the test emails under each possible email to your own. You can also uncomment the test echos in each email section to see what values are being passed.

	require_once('mcl_Oci.php');
	$xdm = new mcl_Oci("xdm");
	$prod = new mcl_Oci("prod");
	$weekago = date("m/d/Y", strtotime("-1 week"));
	$monthago = date("m/d/Y", strtotime("-1 month"));

	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-Type: text/html; charset=ISO-8859-1' . "\r\n";
	$headers .= 'From: Validations Dashboard' . "\r\n";

/* -------------------------------------1ST ESCALATION EMAIL TO EMPLOYEE--------------------------------------------------- */

	//Select everything that hasn't been validated within a week of it being assigned 
	$sql = "SELECT * FROM VAL_TABLE_STATUS WHERE VALIDATED = '0' AND DATEASSIGNED = '{$weekago}'";
	while($row = $xdm->fetch($sql)){

		//Select the individual employee who hasn't validated the document within our set of data
		$select_employee = "SELECT USER_ID, EMAIL_ID, SUPERVISOR_USER_ID FROM EMPLOYEE@MAXIMO WHERE USER_ID = '{$row['USERID']}'";
		$employee = $prod->fetch($select_employee);

		/*//Test echo
		echo $row['CONTRACTNAME']." ".$row['USER_NAME']." ".$row['DATEASSIGNED']."<br>";*/

		//Declare email subject and body
		$subject = 'Escalation: You have not validated "'.$row['CONTRACTNAME'].'" within a week';

		$message = 
		'The following document has been waiting your validation for 1 week: <strong>'.$row["CONTRACTNAME"].'</strong><br><br>
		Step 1: Go to the following site: <a href="http://lnx825:63372/">Validations Dashboard</a><br>
		Step 2: Login using your DTE Energy login information.<br>
		Step 3: Navigate to the Pending Documents tab.<br>
		Step 4: View, sign, and agree that you have read the uploaded document.';

		//Get the email of the employee we selected
		$to = $employee['EMAIL_ID'];

		//TEST: To be removed
		$to = "brian.atiyeh@dteenergy.com";

		//Send email
		mail($to, $subject, $message, $headers);

	} //End while loop

	
/* -------------------------------------2ND ESCALATION EMAIL TO EMPLOYEE--------------------------------------------------- */

	//Select everything that hasn't been validated within a month of it being assigned
	$sql2 = "SELECT * FROM VAL_TABLE_STATUS WHERE VALIDATED = '0' AND DATEASSIGNED = '{$monthago}'";
	while($row2 = $xdm->fetch($sql2)){

		//Select the individual employee who hasn't validated the document within our set of data
		$select_employee2 = "SELECT USER_ID, EMAIL_ID, SUPERVISOR_USER_ID FROM EMPLOYEE@MAXIMO WHERE USER_ID = '{$row2['USERID']}'";
		$employee2 = $prod->fetch($select_employee2);

		/*//Test echo
		echo $row2['CONTRACTNAME']." ".$row2['USER_NAME']." ".$row2['DATEASSIGNED']." ".$employee2['SUPERVISOR_USER_ID']."<br>";*/

		//Declare subject and body of email to be sent to the employee
		$subject = 'ESCALATION: You have not validated "'.$row['CONTRACTNAME'].'" within a month';

		$message = 
		'The following document has been waiting your validation for 1 month: <strong>'.$row2["CONTRACTNAME"].'</strong><br><br>
		Step 1: Go to the following site: <a href="http://lnx825:63372/">Validations Dashboard</a><br>
		Step 2: Login using your DTE Energy login information.<br>
		Step 3: Navigate to the Pending Documents tab.<br>
		Step 4: View, sign, and agree that you have read the uploaded document.';

		//Get the email of the employee we selected
		$to = $employee2['EMAIL_ID'];

		//TEST: To be removed
		$to = "brian.atiyeh@dteenergy.com";

		//Send email
		mail($to, $subject, $message, $headers);

/* -------------------------------------------EMAIL TO SUPERVISOR AFTER 2ND ESCALATION----------------------------------------------------------- */
		
		//Select the supervisor of the individual employee
		$supervisor_id = $employee2['SUPERVISOR_USER_ID'];
		$select_supervisor = "SELECT USER_ID, EMAIL_ID FROM EMPLOYEE@MAXIMO WHERE USER_ID = '{$employee2['SUPERVISOR_USER_ID']}'";
		$supervisor_row = $prod->fetch($select_supervisor);

		//Store the email of the supervisor in variable
		$supervisor = $supervisor_row['EMAIL_ID'];

		/*//Test echo
		echo $supervisor."<br>";*/

		//Declare subject and body of email to be sent to the supervisor
		$subject = 'ESCALATION: '.$row2["USER_NAME"].' has not validated a document';

		$message = 
		'<strong>'.$row2["USER_NAME"].'</strong> has not validated the following document for 1 month: <strong>'.$row2["CONTRACTNAME"].'</strong><br>';

		//test so we arent emailing supervisors
		$test = "brian.atiyeh@dteenergy.com";

		//Send email (change $test to $supervisor)
		mail($test, $subject, $message, $headers);

	} //End while loop

?>