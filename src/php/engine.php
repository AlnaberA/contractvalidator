<?php
require_once('mcl_Oci.php');
// $lnx_dir = "{$_SERVER['DOCUMENT_ROOT']}/";
// require_once("{$lnx_dir}/src/php/auth.php");
// $user = auth::check();
// $authorized = ($user["status"] == "authorized" ? 1 : 0);
// if(!$authorized) {
// 	die('Unauthorized');
// }

$dqm = new mcl_Oci('dqm');

//allows class to be called via ajax
if(isset($_REQUEST['f'])) {
	$method = $_REQUEST['f'];
	$data = @engine::$method();
	echo json_encode($data);
}
 
class engine {
	function get_storm() {
		global $dqm;
		$sql = "SELECT 
				    TO_CHAR(STORM_START_DT_TM, 'MM/DD/YYYY HH24:MI:SS') AS STORM_START_DT_TM, 
				    TO_CHAR(STORM_END_DT_TM, 'MM/DD/YYYY HH24:MI:SS')   AS STORM_END_DT_TM, 
				    JOB_NUM                                             AS STORM 
				FROM 
				    SPM_SUMMARY@INSERVICE
				WHERE 
				    STORM_START_DT_TM = 
				    ( 
				    SELECT 
				        MAX(STORM_START_DT_TM) 
				    FROM 
				        SPM_SUMMARY@INSERVICE
    )";
		$storm = array('storm'=>false, 'start'=>'', 'end'=>'');
		while($row = $dqm->fetch($sql)) {
			$storm['storm'] = true;
			$storm['start'] = $row['STORM_START_DT_TM'];
			$storm['end'] = $row['STORM_END_DT_TM'];
			$storm['id'] = $row['STORM'];
		}
		return $storm;
	}

	function capture_open_jobs($end_dt = null) {
		global $dqm;
		$storm = engine::get_storm();
		$sql = 
		"SELECT
		    CIRCUIT,
		    SC,
		    EID,
		    XDTS,
		    JOB_TYPE,
		    OUTAGE_TIME,
		    ERT,
		    JOB_NUM,
		    LISTAGG(CREW, ', ') WITHIN GROUP (ORDER BY CREW) AS CREW,
		    CREW_SC,
		    NUM_CUST,
		    SNAPSHOT_DTS
		FROM
		(
		SELECT DISTINCT 
		    A.FEEDER AS CIRCUIT, 
		    A.DGROUP AS SC, 
		    A.EID, 
		    TO_CHAR(TO_DATE(SUBSTR(NVL(A.RESTORE_DTS, XDTS), 1, 14), 'YYYYMMDDHH24MISS'), ' 		MM/DD/YYYY HH24:MI:SS') AS XDTS, 
		    TYCOD                                                                                		                   AS JOB_TYPE, 
		    TO_CHAR(TO_DATE(SUBSTR(NVL(A.OFF_DTS, AD_TS), 1, 14), 'YYYYMMDDHH24MISS'), ' 		MM/DD/YYYY HH24:MI:SS')    AS OUTAGE_TIME, 
		    CASE 
		        WHEN LENGTH(A.EST_REP_TIME) = 16 
		        THEN TO_CHAR(TO_DATE(SUBSTR(A.EST_REP_TIME, 1, 14), 'YYYYMMDDHH24MISS'), ' 		MM/DD/YYYY HH24:MI:SS') 
		        ELSE NULL 
		    END                                                  AS ERT, 
		    A.NUM_1                                              AS JOB_NUM, 
		    NVL(NVL(A.PRIM_UNIT, A.DISPASS_UNIT), C.UNID)        AS CREW, 
		    REGEXP_REPLACE(U.DGROUP, 'SUP', ' Foreign')          AS CREW_SC, 
		    DECODE(TYCOD,'XCURR',1,'ONELEG',1,'SDXL',1,NUM_CUST) AS NUM_CUST, 
		    TO_CHAR(SYSDATE, 'MM/DD/YYYY HH24:MI:SS')            AS SNAPSHOT_DTS 
		FROM 
		    AEVEN@inservice A, 
		    ( 
		    SELECT 
		        NUM_1, 
		        UNID 
		    FROM 
		        CD_UNITS@INSERVICE 
		    ) 
		    C, 
		    ( 
		    SELECT 
		        UNID, 
		        DGROUP, 
		        UNITYP 
		    FROM 
		        DEF_UNIT@inservice 
		    ) 
		    U 
		WHERE 
		    DECODE(TYCOD,'XCURR',1,'ONELEG',1,'SDXL',1,NUM_CUST) > 0 
		    AND A.CURENT                                         = 'T' 
		    AND EVENT_STATUS                                    <> 'C' 
		    AND A.DGROUP NOT LIKE '%98' 
		    AND A.DGROUP NOT LIKE '%99' 
		    AND TYCOD IN ('CP','FUSE','ISO','OHCKT','OHTRANS','ONELEG','RECLO','SDXL','UGCKT','UGTRANS','UGPSC','XCURR') 
		    AND NOT EXISTS 
		    ( 
		    SELECT 
		        P_EID 
		    FROM 
		        XREF@inservice X 
		    WHERE 
		        A.EID      = X.S_EID 
		        AND X_TYPE = 'X' 
		    ) 
		    AND RESTORE_DTS IS NULL 
		    AND XDTS IS NULL 
		    AND OPEN_AND_CURENT = 'T' 
		    AND 
		    (
		        NVL(A.PRIM_UNIT, A.DISPASS_UNIT) IS NOT NULL 
		        OR C.UNID IS NOT NULL
		    ) --only  pulls jobs with crews    
		    AND 
		    (
		        NVL(A.PRIM_UNIT, A.DISPASS_UNIT) = U.UNID 
		        or A.NUM_1                       = C.NUM_1
		    ) 
		    AND C.UNID    = U.UNID 
		    AND U.UNITYP IN ('GHOST','1MAN','2MAN','3MAN','CON1M','CON2M','CON3M','CONUG','FOR3M','SPLCNG','SPSPEC','TOWER', 'OHFS', 'UGFS') --only pulls line 
		    ".(($storm['storm']) ? "AND TO_DATE(SUBSTR(NVL(OFF_DTS, AD_TS), 1, 14), 'YYYYMMDDHH24MISS') >= TO_DATE('{$storm['start']}', 'MM/DD/YYYY HH24:MI:SS') " : "")."
		    ".(($end_dt) ? "AND TO_DATE(SUBSTR(NVL(OFF_DTS, AD_TS), 1, 14), 'YYYYMMDDHH24MISS') <= TO_DATE('{$end_dt}', 'MM/DD/YYYY HH24:MI:SS') " : "")."
		)
		GROUP BY
		    CIRCUIT,
		    SC,
		    EID,
		    XDTS,
		    JOB_TYPE,
		    OUTAGE_TIME,
		    ERT,
		    JOB_NUM,
		    CREW_SC,
		    NUM_CUST,
		    SNAPSHOT_DTS
		";
		$data = array();
		// $cust_out = 0;
		while($row = $dqm->fetch($sql)) {
			$data[] = $row;
		}

		$err = $dqm->error();
		return ($err) ? $err : $data;
		return $data;
	}

	function close_report($report_num = null) {
		global $dqm, $user;
		$report_num = ($report_num) ? $report_num : json_decode($_REQUEST['report_num'], true);
		$update_stmt = $dqm->parse("UPDATE CWT_REPORTS SET CLOSE_DTS = sysdate, CLOSE_USER = :close_user WHERE REPORT_NUM = :report_num");
		$update_stmt = $dqm->bind($update_stmt, array(':close_user'=>$user['usid'], ':report_num'=>$report_num));
		$dqm->commit();
		$err = $dqm->error();
		return ($err) ? $err : true;
	}

	function delete_report($report_num = null) {
		global $dqm, $user;
		$report_num = ($report_num) ? $report_num : json_decode($_REQUEST['report_num'], true);
		$del_report = $dqm->parse("DELETE FROM CWT_REPORTS WHERE REPORT_NUM = :report_num");
		$del_report = $dqm->bind($del_report, array(':report_num'=>$report_num));
		$data_stmt = $dqm->parse("DELETE FROM CWT_DATA WHERE REPORT_NUM = :report_num");
		$data_stmt = $dqm->bind($data_stmt, array(':report_num'=>$report_num));
		$data_stmt = $dqm->parse("DELETE FROM CWT_DATA WHERE REPORT_NUM = :report_num");
		$data_stmt = $dqm->bind($data_stmt, array(':report_num'=>$report_num));
		$dqm->commit();
		$err = $dqm->error();
		return ($err) ? $err : true;
	}

	function save_new($data = false, $end_dts = false) {
		global $dqm, $user;
		$data = ($data) ? $data : json_decode($_REQUEST['data'], true);
		$end_dts =
		$storm = engine::get_storm();
		$stmt_report = $dqm->parse(
			"INSERT INTO CWT_REPORTS(REPORT_NUM, CREATE_DTS, CREATE_USER, OPEN_DTS, OPEN_USER, LAST_DTS, LAST_USER, REPORT_TITLE, STORM_START, STORM_END, SNAPSHOT_DTS, STORM)
			 VALUES(CWT_REPORT_SEQ.NEXTVAL, sysdate, :user_id, :open_dts, :user_id, :open_dts, :user_id, :title, :storm_start, :storm_end, sysdate, :storm) RETURNING REPORT_NUM INTO :report_num"
		);
		$stmt_report = $dqm->bind($stmt_report, array(
			':user_id'=>$user['usid'],
			':open_dts'=>$_REQUEST['open_dts'],
			':title'=>$_REQUEST['title'],
			':storm_start'=>$storm['start'],
			':storm_end'=>$storm['storm_end'],
			':storm'=>$storm['id'],
			':report_num'=>array(0, SQLT_INT)
		), true, true);

		$report_num = $stmt_report[':report_num'];

		$stmt_crew = $dqm->parse("INSERT INTO CWT_CREW_COUNT(REPORT_NUM) VALUES(:report_num)");
		$stmt_crew = $dqm->bind($stmt_crew, array(':report_num'=>$report_num));

		foreach($data as $key=>$val) {
			$stmt_row = $dqm->parse("INSERT INTO CWT_DATA(REPORT_NUM, LINE_NUM, CIRCUIT, DGROUP, JOB_CODE, OUT_DTS, JOB_NUM, CREW, CUST_OUT, CREW_SC, ERT, EID)
						VALUES(:report_num, :line, :circuit, :dgroup, :job_code, :out_dts, :job_num, :crew, :cust_out, :crew_sc, :ert, :eid)");
			$stmt_row = $dqm->bind($stmt_row, array(
				':report_num'=>$report_num,
				':line'=>$key,
				':circuit'=>$val['CIRCUIT'],
				':dgroup'=>$val['SC'],
				':job_code'=>$val['JOB_TYPE'],
				':out_dts'=>$val['OUTAGE_TIME'],
				':job_num'=>$val['JOB_NUM'],
				':crew'=>$val['CREW'],
				':cust_out'=>$val['NUM_CUST'],
				':crew_sc'=>$val['CREW_SC'],
				':ert'=>$val['ERT'],
				':eid'=>$val['EID']
			));
		}

		$dqm->commit();
		$err = $dqm->error();
		return ($err) ? $err : true;
	}

	//rewrite as proc
	function update_report($report_num = null) {
		global $dqm;
		$report_num = ($report_num === null) ? $_REQUEST['report_num'] : $report_num;
		$stmt_get_data = "SELECT EID, LINE_NUM FROM CWT_DATA WHERE REPORT_NUM = {$report_num}";
		while($update_row = $dqm->fetch($stmt_get_data)) {
			// return $update_row;
			$stmt_get_update =
			"SELECT DISTINCT 
			    A.FEEDER AS CIRCUIT, 
			    A.DGROUP AS SC, 
			    A.EID, 
			    TO_CHAR(TO_DATE(SUBSTR(NVL(RESTORE_DTS, XDTS), 1, 14), 'YYYYMMDDHH24MISS'), 'MM/DD/YYYY HH24:MI:SS') AS XDTS, 
			    TYCOD                                                                                                AS JOB_TYPE, 
			    TO_CHAR(TO_DATE(SUBSTR(NVL(OFF_DTS, AD_TS), 1, 14), 'YYYYMMDDHH24MISS'), 'MM/DD/YYYY HH24:MI:SS')    AS OUTAGE_TIME, 
			    CASE 
			        WHEN LENGTH(EST_REP_TIME) = 16 
			        THEN TO_CHAR(TO_DATE(SUBSTR(EST_REP_TIME, 1, 14), 'YYYYMMDDHH24MISS'), 'MM/DD/YYYY HH24:MI:SS') 
			        ELSE NULL 
			    END                                                                                                  AS ERT, 
			    NUM_1                                                                                                AS JOB_NUM, 
			    NVL(PRIM_UNIT, DISPASS_UNIT)                                                                         AS CREW, 
			    REGEXP_REPLACE(U.DGROUP, 'SUP', ' Foreign')                                                          AS CREW_SC, 
			    DECODE(TYCOD,'XCURR',1,'ONELEG',1,'SDXL',1,NUM_CUST)                                                 AS NUM_CUST,
			    TO_CHAR(SYSDATE, 'MM/DD/YYYY HH24:MI:SS')                                                            AS SNAPSHOT_DTS
			FROM 
			    AEVEN@inservice A,
			    (SELECT UNID, DGROUP, UNITYP FROM DEF_UNIT@inservice) U 
			WHERE 
			    A.CURENT                                         = 'T' 
			    AND A.EID = {$update_row['EID']}
			    AND NVL(A.PRIM_UNIT, A.DISPASS_UNIT)             = U.UNID
			";
			$err = $dqm->error();
			if($err) return $err;
			while($row = $dqm->fetch($stmt_get_update)) {
				$dqm->query("UPDATE CWT_DATA
								SET
									CREW = '{$row['CREW']}',
									CUST_OUT = {$row['NUM_CUST']},
									CREW_SC = '{$row['CREW_SC']}',
									ERT = TO_DATE('{$row['ERT']}', 'MM/DD/YYYY HH24:MI:SS'),
									XDTS = TO_DATE('{$row['XDTS']}', 'MM/DD/YYYY HH24:MI:SS')
								WHERE
									EID = {$row['EID']}");
			}
		}

		$stmt_snapshot_dts = $dqm->parse("UPDATE CWT_REPORTS SET SNAPSHOT_DTS = SYSDATE WHERE REPORT_NUM = :report");
		$stmt_snapshot_dts = $dqm->bind($stmt_snapshot_dts, array(':report'=>$report_num));

		$dqm->commit();
		$err = $dqm->error();
		return ($err) ? $err : true;
	}

	function view_report($report_num = null) {
		global $dqm;
		$report_num = ($report_num === null) ? $_REQUEST['report_num'] : $report_num;
		$stmt_report = "SELECT * FROM CWT_REPORTS WHERE REPORT_NUM = {$report_num}";
		$stmt_data = "SELECT 
						    D.*, 
						    CASE 
						        WHEN EXISTS 
						        (
						        SELECT 1 
						        FROM CWT_DATA 
						        WHERE 
						            CIRCUIT    = D.CIRCUIT 
						            AND NOTES IN ('comp', 'willcomp')
						            AND NOT EXISTS (
						                            SELECT 1 
						                            FROM CWT_DATA
						                             WHERE CIRCUIT = D.CIRCUIT 
						                             AND NVL(NOTES, 'blank') IN ('partcomp', 'nocomp', 'blank')
						                            AND REPORT_NUM = {$report_num}
						                            )
						        ) 
						        THEN 'Y' 
						        ELSE NULL
						    END AS COMPLETE 
						FROM 
						    CWT_DATA D 
						WHERE 
						    REPORT_NUM = {$report_num} 
						ORDER BY 
						    CIRCUIT, 
    					CUST_OUT DESC";
		$stmt_crew = "SELECT * FROM CWT_CREW_COUNT WHERE REPORT_NUM = {$report_num}";
		$data = array();
		while($row = $dqm->fetch($stmt_report)) {
			$data['metadata'] = $row;
		}
		while($row = $dqm->fetch($stmt_data)) {
			$data['rows'][$row['LINE_NUM']] = $row;
		}
		$data['crews'] = engine::fetch_crew_new($report_num);
		$data['crew_structure'] = engine::$crew_structure;
		$pullouts = engine::get_pullouts();
		foreach($pullouts as $id=>$val) {
			if($val['hidden']!='Y')
				$data['crew_structure']['centers'][$val['name']] = $id;
		}
		return $data;
	}

	function report_by_circuit($report_num) {
		global $dqm;
		$sql = 
			"SELECT 
			    CIRCUIT, 
			    LISTAGG(NVL(SUBSTR(STATUS, 1, 500), 'No Comment')||' ['||CUST_OUT||']', ' | ') WITHIN GROUP (ORDER BY CUST_OUT DESC) AS COMMENTS,
			    LISTAGG(NVL(NOTES, 'No Status Selected'), ' | ') WITHIN GROUP (ORDER BY NOTES DESC) AS NOTES
			FROM 
			    (
			    SELECT DISTINCT 
			        CASE NOTES
			            WHEN 'comp' THEN 'Complete'
			            WHEN 'willcomp' THEN 'Partial, complete tonight'
			            WHEN 'partcomp' THEN 'Partial, not complete tonight'
			            WHEN 'nocomp' THEN 'Not Complete'
			            ELSE 'No Status Selected'
			        END AS NOTES,
			        CIRCUIT,
			        STATUS,
			        SUM(CUST_OUT) AS CUST_OUT 
			    FROM 
			        CWT_DATA 
			    WHERE 
			        REPORT_NUM = {$report_num} 
			    GROUP BY 
			        CIRCUIT, 
			        STATUS,
			        NOTES
			    ) 
			GROUP BY 
			    CIRCUIT
			";
		$data = array();
		while($row = $dqm->fetch($sql)) {
			$data[$row['CIRCUIT']] = $row['COMMENTS'];
		}
		$err = $dqm->error();
		if($err) return $err;
		return $data;
	}

	function view_open_report() {
		global $dqm;
		$report_num = null;
		$sql = "select max(report_num) as m from cwt_reports where close_dts is null";
		while($row = $dqm->fetch($sql)) {
			$report_num = $row['M'];
		}
		if($report_num === null)
			return array('no_report'=>true);
		else {
			return engine::view_report($report_num);
		}
	}

	function update_field() {
		global $dqm, $user;
		$stmt_update = $dqm->parse("UPDATE CWT_DATA
		                           SET
		                               {$_REQUEST['field']} = :val,
		                               UPDATE_USER = :username,
		                               UPDATE_DTS = sysdate
		                           WHERE REPORT_NUM = :report AND LINE_NUM = :line");
		$stmt_update = $dqm->bind($stmt_update, array(
			':val'=>$_REQUEST['val'],
			':username'=>$user['usid'],
			':report'=>$_REQUEST['report_num'],
			':line'=>$_REQUEST['line']
		));
		$dqm->commit();
		$err = $dqm->error();
		return ($err) ? $err : true;
	}

	function update_status2() {
		global $dqm, $user;
		// return($_REQUEST);
		$stmt_update = $dqm->parse("UPDATE CWT_DATA 
		                           SET 
		                               ICS_STATUS = :status
		                           WHERE REPORT_NUM = :report AND LINE_NUM = :line");
		$stmt_update = $dqm->bind($stmt_update, array(
			':status'=>$_REQUEST['status'],
			':report'=>$_REQUEST['report_num'],
			':line'=>$_REQUEST['line']
		));
		$dqm->commit();
		$err = $dqm->error();
		return ($err) ? $err : true;
	}

	function update_crew_new() {
		global $dqm, $user;
		$stmt_update = $dqm->parse("
			MERGE INTO CWT_CREWS_NEW USING DUAL ON (
		        REPORT_NUM = :report_num
				AND SERVICE_CENTER = :service_center
				AND CREW_TYPE = UPPER(:crew_type)
			)
			WHEN MATCHED THEN UPDATE  
				SET CREW_COUNT = :crew_count
				WHERE REPORT_NUM = :report_num
					AND SERVICE_CENTER = :service_center
					AND CREW_TYPE = UPPER(:crew_type)
			WHEN NOT MATCHED THEN
				INSERT (
					REPORT_NUM,
					SERVICE_CENTER,
					CREW_TYPE,
					CREW_COUNT
				) VALUES(
					:report_num,
					:service_center,
					:crew_type,
					:crew_count
				)
		");
		$stmt_update = $dqm->bind($stmt_update, array(
			':report_num'=>$_REQUEST['report_num'],
			':crew_type'=>$_REQUEST['crew_type'],
			':service_center'=>$_REQUEST['service_center'],
			':crew_count'=>$_REQUEST['crew_count']
		));
		$dqm->commit();
		$err = $dqm->error();
		return ($err) ? $err : true; 
	}
 
	function fetch_crew_new($report_num) {
		global $dqm;
		$crews = array();
		$sql = "SELECT * FROM CWT_CREWS_NEW WHERE REPORT_NUM = {$report_num}";
		while($row = $dqm->fetch($sql)) {
			$crews[$row['SERVICE_CENTER'].'_'.$row['CREW_TYPE']] = $row['CREW_COUNT'];
		}
		return $crews;
	}

	function view_all_reports() {
		global $dqm;
		$stmt_report = "SELECT * FROM CWT_REPORTS ORDER BY REPORT_NUM DESC";
		$data = array();
		while($row = $dqm->fetch($stmt_report)) {
			$data[] = $row;
		}
		return $data;
	}

	function update_crew() {
		global $dqm, $user;
		$update_stmt = $dqm->parse("UPDATE CWT_CREW_COUNT SET {$_REQUEST['field']} = :val WHERE REPORT_NUM = :report_num");
		$dqm->bind($update_stmt, array(':val'=>$_REQUEST['val'], ':report_num'=>$_REQUEST['report_num']));
		$dqm->commit();
		$err = $dqm->error();
		return($err) ? $err : true;
	}

	function get_crew_structure() {
		// $storm = engine::getStorm();
		return self::$crew_structure;
	}

	function generate_sectionalizers($report_num) {
		global $dqm;
		$stmt = "SELECT DISTINCT 
				    CIRCUIT 
				FROM 
				    CWT_DATA 
				WHERE 
				    REPORT_NUM = {$report_num}
				ORDER BY CIRCUIT";
		$sectionalizers = array();
		while($row = $dqm->fetch($stmt)) {
			$sectionalizers[$row['CIRCUIT']] = engine::get_sectionalizers($row['CIRCUIT']);
		}
		return $sectionalizers;
	}

	function get_sectionalizers($feeder) {
		global $dqm;
		$stmt = 
			"SELECT 
			    * 
			FROM 
			    ( 
			    SELECT 
			        FUSE.OBJECTID, 
			        DECODE(LENGTH(EC.GLNX), 5, ('0' || EC.GLNX), 4, ('00' || EC.GLNX), EC.GLNX) || EC.GLNY AS GLN, 
			        SECTIONALIZING || ' - ' || COMMENTS                                                    AS LOCATION, 
			        SECTIONALIZING, 
			        FEEDERID 
			    FROM 
			        EQUIPMENT_CHAIN@INSERVICE EC, 
			        ELEDTE.FUSE_MV_VIEW@ESRI FUSE 
			    WHERE 
			        EC.GLNX      = FUSE.GLNX(+) 
			        AND EC.GLNY  = FUSE.GLNY(+) 
			        AND FEEDERID = '{$feeder}'
			        AND SECTIONALIZING IS NOT NULL 
			        AND SECTIONALIZING <> ' ' 
			    UNION 
			    SELECT 
			        DPD.OBJECTID, 
			        DECODE(LENGTH(EC.GLNX), 5, ('0' || EC.GLNX), 4, ('00' || EC.GLNX), EC.GLNX) || EC.GLNY AS GLN, 
			        SECTIONALIZING || ' - ' || COMMENTS                                                    AS LOCATION, 
			        SECTIONALIZING, 
			        FEEDERID 
			    FROM 
			        EQUIPMENT_CHAIN@INSERVICE EC, 
			        ELEDTE.DYNAMICPROTDEV_MV_VIEW@ESRI DPD 
			    WHERE 
			        EC.GLNX      = DPD.GLNX(+) 
			        AND EC.GLNY  = DPD.GLNY(+) 
			        AND FEEDERID = '{$feeder}'
			        AND SECTIONALIZING IS NOT NULL 
			        AND SECTIONALIZING <> ' ' 
			    ) 
			ORDER BY 
			    SUBSTR(SECTIONALIZING, 0, 1) ASC, 
			    LPAD(SUBSTR(SECTIONALIZING, 1), 5) ASC";
		// $stmt = $dqm->parse($stmt);
		// $stmt = $dqm->bind($stmt, array(':feeder'=>$feeder));
		$sectionalizers = array();
		// $sectionalizers['sql'] = $stmt;
		while($row = $dqm->fetch($stmt)) {
			$sectionalizers[$row['OBJECTID']] = $row['LOCATION'];
		}
		return $sectionalizers;
	}

	function get_pullouts() {
		global $dqm;
		$return = array();
		$sql = "SELECT * FROM CWT_CENTERS ORDER BY NAME";
		while($row = $dqm->fetch($sql))
			$return[$row['CODE']] = array('name'=>$row['NAME'], 'hidden'=>$row['HIDDEN']);
		return $return;
	}

	function update_pullout() {
		global $dqm;
		$field = $_REQUEST['field'];
		$code = $_REQUEST['code'];
		$val = $_REQUEST['val'];
		$sql = "UPDATE CWT_CENTERS SET {$field} = '{$val}' WHERE CODE = '{$code}'";
		$dqm->query($sql);
		$err = $dqm->error();
		return ($err) ? $err : true;
	}

	public static $crew_structure = array(
		'centers'=> array(
			'ANN ARBOR'=>'ANN',
			'CANIFF'=>'CAN',
			'HOWELL'=>'HWL',
			'LAPEER'=>'LAP',
			'MARYSVILLE'=>'MAR',
			'MT CLEMENS'=>'MTC',
			'NAE'=>'NAE',
			'NEWPORT'=>'NPT',
			'PONTIAC'=>'PON',
			'REDFORD'=>'RFD',
			'SHELBY'=>'SBY',
			'TROMBLY'=>'TBY',
			'WESTERN WAYNE'=>'WWS'
		),
		'crew_types'=> array('LS', 'B', 'C', 'CONTRACT', 'EFLS')
	);

	public static $statuses = array(
		'comp'=>'100% restored',
		'willcomp'=>'100% will be restored tonight',
		'partcomp'=>'Partially restored',
		'nocomp'=>'Nothing restored'
	);
}

?>