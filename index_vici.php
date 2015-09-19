<?php 
	
	define('BASEPATH', str_replace("\\", "/", $system_path));
	
	require(BASEPATH."console.php"); 
	require(BASEPATH."application/config/database.php"); 
	$server_ip = $db['default']['hostname'];
	$server_dbname = $db['default']['database'];
	$server_login = $db['default']['username']; 
	$server_pass = $db['default']['password'];
	
	//echo 'server_ip: '.$server_ip.':'.$server_dbname.'<br>';

	$action = $_REQUEST["action"];	
	
	if($action == "Authenticate"){
		$usr =  $_REQUEST["user_name"];
		$passwd = $_REQUEST["user_password"];
		redirectTo('../index.php/login/vici_login/'.$usr.'/'.$passwd);			
		
	}else if($action == "SynGroups"){
		
		$group_id =  $_REQUEST["group_id"];
		$group_name = $_REQUEST["group_name"];		
		
	
		syn_groups($server_ip, $server_dbname, $server_login, $server_pass, $group_id,$group_name);			
		
	}else if($action == "SynUsers"){
	

		
		$user_name =  $_REQUEST["user_name"];
		$user_pass = $_REQUEST["user_pass"];		
		$user_fullname =  $_REQUEST["user_fullname"];
		$user_level = $_REQUEST["user_level"];						
		$user_active = $_REQUEST["user_active"];
		$user_group =  $_REQUEST["user_group"];	
				
		//redirect('login/syn_users/'.$user_name.'/'.$user_pass.'/'.$user_fullname.'/'.$user_level.'/'.$user_active.'/'.$user_group);	
	
		syn_users($server_ip, $server_dbname, $server_login, $server_pass, $user_name,$user_pass,$user_fullname,$user_level,$user_active,$user_group);
	
	}else if($action == "SynCallRecordingLogs"){//ͬ��¼��·��
		
		$record_id = $_REQUEST["recording_id"];
		$history_dbname = $_REQUEST["history_dbname"];
		
		syn_recording_logs($server_ip, $server_dbname, $server_login, $server_pass,$history_dbname,$record_id);
	}else if($action == "SynMissedCalls"){ //ͬ��©������

		syn_missed_calls();
	}else if($action == "CrmSearch"){
		
		$usr =  $_REQUEST["user"];
		$passwd = $_REQUEST["pass"];
		if(isset($_REQUEST["iocheck"])){
			$iocheck = $_REQUEST["iocheck"];
		}else{
			$iocheck = 'out';
		}
		$agent_id = $_REQUEST["user"];
		$phone_number = $_REQUEST["phone"];
		$uniqueid = $_REQUEST["uniqueid"];
		
		//$url = 'index.php/login/call_search/'.$usr.'/'.$passwd.'/'.$iocheck.'/'.$agent_id.'/'.$phone_number.'/'.$uniqueid;
		$url = 'index.php/communicate/connected/callEvent/'.$agent_id.'/0/'.$phone_number.'/'.$uniqueid;

		
		redirectTo($url);			

	}else if($action == "GetHttpPort"){
		$server_port = getenv("SERVER_PORT");
		echo $server_port;

	}
	
	else {
		$query_string = $_SERVER['QUERY_STRING'];
		if($query_string)
		{
			redirectTo('../index.php?'.$query_string);
		}else
		{
			redirectTo('../index.php');
		}
	
	}

function redirectTo($url){

  // echo '<Script>window.self.location="'.$url.'";</Script>';
   echo '<Script>location.href="'.$url.'";</Script>'; 

}
function syn_missed_calls(){
	require('/var/www/html/mysqldb.php');
	require('/var/www/html/config.php');
	$db = new db();
	$db->connect($db_config);
	$sql="SELECT closecallid,lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch, length_in_sec,status,phone_code,phone_number,user,comments,processed,queue_seconds,user_group,xfercallid, term_reason,uniqueid,agent_only,queue_position,ring_sec,drop_history FROM ".$db_config["database"].".vicidial_closer_log where drop_history ='0';";
	$res=$db->row_query($sql);

	//echo $row_q['drop_history'];
	foreach($res as $row_q){
		$history= $row_q['drop_history'];
		if($db->isNeedSyn($row_q)){
			$db->SynRecords($row_q,2);
			$db->row_query("UPDATE ".$db_config["database"].".vicidial_closer_log SET drop_history='1' where drop_history='0'");
		}
	}
}

	
function syn_recording_logs($server_ip, $server_dbname, $server_login, $server_pass,$history_dbname,$record_id){
	
	echo $server_ip.' '.$server_dbname.' '.$server_login.' '.$server_pass.'<br />';
	$linkV=mysql_connect($server_ip, $server_login,$server_pass);
	mysql_query(" SET NAMES 'utf8' ");
	
	if (!$linkV) {die("Could not connect: $server_ip|$server_dbname|$server_login|$server_pass" . mysql_error());}
	echo "Connected successfully\n<BR>\n";
	mysql_select_db("$history_dbname", $linkV);

	$stmt='select vicidial_id,filename, location from recording_log where recording_id="'.$record_id.'";';
	$rslt=mysql_query($stmt, $linkV);


	if ($DB) {echo "$stmt\n";}
	if (!$rslt) {die('Could not execute: ' . mysql_error());}

	$row=mysql_fetch_row($rslt);
	//echo $row[0].'  '.$row[1].'  '.$row[2].'<br />';

	echo $history_dbname.' '.$row[0];
	
	if($row[0]>0)
	{
		mysql_select_db("$server_dbname", $linkV);
		$stmt='UPDATE cc_call_history SET file_name="'.$row[1].'",location="'.$row[2].'"where call_id="'.$row[0].'";';
		$rslt=mysql_query($stmt, $linkV);
		if ($DB) {echo "$stmt\n";}
		if (!$rslt) {die('Could not execute: ' . mysql_error());}
		echo "sucessfully";

	}else{
		echo "error";
	}
	

	//$group_found_count = $row[0];



}

function syn_groups($server_ip, $server_dbname ,$server_login, $server_pass, $group_id_enc, $group_name_enc){
	
	
	$group_id = urldecode($group_id_enc);
	
	$group_name = urldecode($group_name_enc);
	
	echo 'server_ip: '.$server_ip.':'.$server_dbname.'<br>';
	echo 'syn group_id: '.$group_id.':'.$group_name.'<br>';
	

	
	### connect to your vtiger database
	$linkV=mysql_connect($server_ip, $server_login,$server_pass);
	mysql_query(" SET NAMES 'utf8' ");
	//$linkV=mysql_connect("172.17.1.69", "cron","1234");
	if (!$linkV) {die("Could not connect: $server_ip|$server_dbname|$server_login|$server_pass" . mysql_error());}
	echo "Connected successfully\n<BR>\n";
	mysql_select_db("$server_dbname", $linkV);
	
	
	##########################
	### BEGIN Group export

	$stmt='SELECT count(*) from department where department_group="'.$group_id.'";';
	$rslt=mysql_query($stmt, $linkV);
	if ($DB) {echo "$stmt\n";}
	if (!$rslt) {die('Could not execute: ' . mysql_error());}
	$row=mysql_fetch_row($rslt);
	$group_found_count = $row[0];

	### group exists in vtiger, grab groupid, update description
	if ($group_found_count > 0)
	{		
		$stmt='UPDATE department SET department_name="'.$group_name.'" where department_group="'.$group_id.'";';
	}

	### group doesn't exist in vtiger, insert it
	else
	{
		#### BEGIN CREATE NEW GROUP RECORD IN VTIGER
		$stmt = 'insert into department (department_group,department_name) values ("'.$group_id.'", "'.$group_name.'");';
		#### END CREATE NEW GROUP RECORD IN VTIGER
	}
	//echo $stmt;
	$rslt=mysql_query($stmt, $linkV);
	if ($DB) {echo "$stmt\n";}
	if (!$rslt) {die('Could not execute: ' . mysql_error());}

### END Group export
##########################


	
	echo "DONE\n";



	
}



function syn_users($server_ip, $server_dbname ,$server_login, $server_pass, $user_name_enc, $user_pass_enc, $user_fullname_enc, $user_level_enc, $user_active_enc, $user_group_enc){
	
	$user_name  = urldecode($user_name_enc);
	$user_pass  = urldecode($user_pass_enc);
	$user_fullname  = urldecode($user_fullname_enc);
	$user_level  = urldecode($user_level_enc);
	$user_active  = urldecode($user_active_enc);
	$user_group  = urldecode($user_group_enc);
	
	
	echo 'syn syn_users: '.$user_name.':'.$user_pass.':'.$user_fullname.':'.$user_level.':'.$user_active.':'.$user_group.'<br>';

	
	### connect to your vtiger database
	$linkV=mysql_connect($server_ip, $server_login,$server_pass);
	mysql_query(" SET NAMES 'utf8' ");
	//$linkV=mysql_connect("172.17.1.69", "cron","1234");
	if (!$linkV) {die("Could not connect: $server_ip|$server_dbname|$server_login|$server_pass" . mysql_error());}
	echo "Connected successfully\n<BR>\n";
	mysql_select_db("$server_dbname", $linkV);
	
	
	switch($user_level)
	{		
		case 5:

				$user_role_id=55;
		break;
		
		default :
				$user_role_id=56;
	
	}
	
	

	$stmt='SELECT department_id from department where department_group="'.$user_group.'";';
	$rslt=mysql_query($stmt, $linkV);
	if ($DB) {echo "$stmt\n";}
	if (!$rslt) {die('Could not execute: ' . mysql_error());}
	$row=mysql_fetch_array($rslt);
	$user_department_id= $row['department_id'];
	
	
		
	##########################
	### BEGIN uaers export

	$stmt='SELECT count(*) from agents where code="'.$user_name.'";';
	$rslt=mysql_query($stmt, $linkV);
	if ($DB) {echo "$stmt\n";}
	if (!$rslt) {die('Could not execute: ' . mysql_error());}
	$row=mysql_fetch_row($rslt);
	$user_found_count = $row[0];

	### user exists in vtiger, grab groupid, update description
	if ($user_found_count > 0)
	{		
			$stmt='UPDATE agents SET name="'.$user_fullname.'", passwd="'.$user_pass.'", role_id="'.$user_role_id.'", department_id="'.$user_department_id.'" where code="'.$user_name.'";';
	}else{
			$stmt = 'insert into agents (code, name, passwd, role_id, department_id,out_display_number_id) values ("'.$user_name.'", "'.$user_fullname.'", "'.$user_pass.'" , "'.$user_role_id.'", "'.$user_department_id.'", "1");';
	}
//echo $stmt;
	
	$rslt=mysql_query($stmt, $linkV);
	if ($DB) {echo "$stmt\n";}
	if (!$rslt) {die('Could not execute: ' . mysql_error());}
	
	
	echo "DONE\n";
	
	
}




?>


