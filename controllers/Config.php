<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/wordpress/wp-load.php");

if($_POST['duration']??false)
{
	// Start the session
	session_start();
	$configObject=new Config();
	$configObject->addTimeConfig($_POST['duration']);
	$_SESSION['message']="Duration Updated";
	header("location:".$_SERVER["HTTP_REFERER"]);

}

class Config
{
	public function addTimeConfig($duration)
	{

		return update_user_meta(1,"vc_timer",$duration,false);


	}
}
?>