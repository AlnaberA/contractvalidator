<?
require_once('secure.php');
require_once('mcl_Oci.php');
$xdm = new mcl_Oci("xdm");
$prod = new mcl_Oci("prod");

$userid= $user[usid];
$name= $user[name];
$position= $user[details][title];

$report = $_POST['RPT'];
$doc_name = trim($_POST['doc_name']);
$subject = $_POST['subject'];
$message = $_POST['body'];
$supervisor_message = $_POST['supervisor_body'];
$from = "From: ".$_POST['from'];
$email = preg_replace('/\s+/', '', $_POST['email']);

$supervisor_email = preg_replace('/\s+/', '', $_POST['supervisor_email']);


$file = $_FILES["DOC"]["name"];
$fileSize = $_FILES["DOC"]["size"];
$today = date("m/d/Y", strtotime("now"));

if ($file != null){
    $fileFull = $_FILES['DOC'];
    $file_name = $_FILES["DOC"]["name"];
    $file_tmp = $fileFull['tmp_name'];
    $file_ext = substr($file_name, strpos($file_name, ".") + 1);
    
    $file_name_new = preg_replace("/[^a-zA-Z0-9.]/", "", $file_name);
    $file_destination = 'Upload/' . $file_name_new;

    move_uploaded_file($file_tmp, $file_destination);
}

$delimiters = array(";",",","|","/");

//exploded output with multiple delimiter 
function multiexplode ($delimiters,$string) {   
    //Str replace takes 3 parameters and replaces all delimeters with the first index of the delimiters array
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    
    //Returns array
    return  $launch;
}

//Explode takes string from textarea and splits it by semicolon into an array
$to = multiexplode($delimiters, $email);
$supervisor_to = multiexplode($delimiters, $supervisor_email);

$employee_ids = array();
foreach($to as $emp_email){
    $email_search = "SELECT * FROM EMPLOYEE@MAXIMO WHERE EMAIL_ID = '{$emp_email}'";
    $row = $prod->fetch($email_search);

    echo $row['USER_ID'];
    array_push($employee_ids, $row['USER_ID']);
}

    print_r($employee_ids);

$supervisor_ids = array();
foreach($supervisor_to as $super_email){
    $email_search = "SELECT * FROM EMPLOYEE@MAXIMO WHERE EMAIL_ID = '{$super_email}'";
    $row = $prod->fetch($email_search);

    echo $row['USER_ID'];
    array_push($supervisor_ids, $row['USER_ID']);
}

    print_r($supervisor_ids);

//Implode converts the $to array into a string and places a new line character after every index
$to = implode("\n", $to);
$employee_ids_string = implode(";", $employee_ids);

$supervisor_to = implode("\n", $supervisor_to);
$supervisor_ids_string = implode(";", $supervisor_ids);

echo $userid." ".$name." ".$position." ".$file_name_new." ".$email." ".$employee_ids_string." ".$today." ".$doc_name;

$sql = "INSERT INTO VAL_1 (ID, WKID, NAME, POSITIONN, DOC, ASSIGNEDTO, IDS_ASSIGNEDTO, DATE_ASSIGNED, DOC_NAME, SUPERVISOR, SUPERVISOR_ID) 
            VALUES (seq_name.nextval, '{$userid}', '{$name}','{$position}', '{$file_name_new}', '{$email}', '{$employee_ids_string}', '{$today}', '{$doc_name}', '{$supervisor_email}', '{$supervisor_ids_string}')";

$xdm->query($sql);

//Inserts a row into VAL_TABLE_STATUS for every employee that needs to validate this document with validate status false
foreach($employee_ids as $employee){
    $select_name = "SELECT USER_ID, LAST_NAME, GIVEN_NAME, EMAIL_ID FROM EMPLOYEE@MAXIMO WHERE USER_ID = '{$employee}'";
    $row = $prod->fetch($select_name);
    $user_name = $row['GIVEN_NAME']." ".$row['LAST_NAME'];

    $sql = "INSERT INTO VAL_TABLE_STATUS (USERID, CONTRACTNAME, DATEASSIGNED, DATESIGNED, VALIDATED, ESIGN, USER_NAME, FILENAME, SUPERVISOR)
            VALUES ('{$employee}', '{$doc_name}', '{$today}', '', '0', '', '{$user_name}', '{$file_name_new}', '0')";

    $xdm->query($sql);
}

foreach($supervisor_ids as $supervisor){
    $select_name = "SELECT USER_ID, LAST_NAME, GIVEN_NAME, EMAIL_ID FROM EMPLOYEE@MAXIMO WHERE USER_ID = '{$supervisor}'";
    $row = $prod->fetch($select_name);
    $user_name = $row['GIVEN_NAME']." ".$row['LAST_NAME'];

    $sql = "INSERT INTO VAL_TABLE_STATUS (USERID, CONTRACTNAME, DATEASSIGNED, DATESIGNED, VALIDATED, ESIGN, USER_NAME, FILENAME, SUPERVISOR)
            VALUES ('{$supervisor}', '{$doc_name}', '{$today}', '', '0', '', '{$user_name}', '{$file_name_new}', '1')";

    $xdm->query($sql);
}

$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-Type: text/html; charset=ISO-8859-1' . "\r\n";
$headers .= $from; 

//test so we aren't emailing random people
//$to = "brian.atiyeh@dteenergy.com";
mail($to, $subject, $message, $headers);
mail($supervisor_to, $subject, $supervisor_message, $headers);

header("Location: Documents.php");

?>
