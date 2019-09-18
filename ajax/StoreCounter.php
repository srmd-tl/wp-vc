<?php 
require_once($_SERVER["DOCUMENT_ROOT"]."/wordpress/wp-load.php");

$postClass=$_SERVER["DOCUMENT_ROOT"]."/wordpress/wp-content/plugins/viewCounter/controllers/Post.php";
require_once($postClass);

//Get the request method from the $_SERVER
$requestType = $_SERVER['REQUEST_METHOD'];


 
//Check if request is post 
if($requestType=="POST")
{

 	if(file_exists($postClass)){
 		$postObject=new Post();
	}
	else
	{	
		die("Post Controller Class not Found!");
	}
	$ip=$postObject->getUserIpAddr();
	echo $postObject->insertIntoIPTable($_POST['post'],$ip);
	exit();
}
die("Sorry child you cannot do this");



?>