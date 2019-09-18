<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/wordpress/wp-load.php");
	if($_POST['customComment']??false)
	{
		$postObject=new Post();
		$postObject->insertCustomCommentToPostMeta($_POST['postId'],$_POST['comment']);

	}
	if($_POST['previousComment']??false)
	{
		$postObject=new Post();
		$postObject->updateCustomCommentFromPostMeta($_POST['previousComment'],$_POST['postId'],$_POST['comment']);
	}

Class Post
{
	/*Get Posts By AuthorID*/
	public function getPostByAuthor($author)
	{
		$my_query = new WP_Query( ["author"=>$author] );
		return $my_query;
	}
	/*End Get Posts By AuthorID*/
	

	/* Get Posts By AuthorName*/
	public function getPostByAuthorName($author)
	{
		$my_query = new WP_Query( ["author_name"=>$author] );
		return $my_query;
	}
	/*End Get Posts By AuthorName*/

	/* Get Posts By getPostByCustomMeta*/
	public function getPostByCustomMeta($metaKey,$metaValue)
	{
		$args = array(
		    'meta_key'   => $metaKey,
		    'meta_value' => $metaValue
		);
		$query = new WP_Query( $args);
		return $query;
	}
	/*End Get Posts By getPostByCustomMeta*/

	/*Get Client Ip*/
	public function getUserIpAddr(){
	    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
	        //ip from share internet
	        $ip = $_SERVER['HTTP_CLIENT_IP'];
	    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	        //ip pass from proxy
	        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    }else{
	        $ip = $_SERVER['REMOTE_ADDR'];
	    }
	    return $ip;
	}
	/*End Get Client Ip*/
	
	/*Get Current Day Views*/	
	public function getCurrentDatePostViewsCount($postId)
	{
		global $wpdb;
		$currentDate=date("Y-m-d");
		$table = $wpdb->prefix.'ips';
		$data = $wpdb->get_row( "SELECT count(*) as views FROM $table WHERE post_id = $postId AND created_at = '$currentDate' ");
		return $data;
	}
	/*End Get Current Day Views*/

	/*Inset INto Custom IP Table*/
	public function insertIntoIPTable($postId,$ip)
	{
		global $wpdb;
		$currentDate=date("Y-m-d");
		$table = $wpdb->prefix.'ips';
		$data = $wpdb->get_row( "SELECT * FROM $table WHERE post_id = $postId AND created_at = '$currentDate' AND ip = '$ip' " );
		//Check if user donot visit from same ip on same date
		if(!$data)
		{
			$data = array('ip' => $ip, 'post_id' => $postId,"created_at"=>$currentDate);
			$wpdb->insert($table,$data);
			//Get the total count of posts;
			$count=get_post_meta($postId,"count",true);
			//Check if no post meta count found in database
			if($count)
			{
				//Update The post meta count by increment 1
				update_post_meta($postId,"count",$count+1);
			}
			else
			{
				//create The post meta count by  1
				update_post_meta($postId,"count",1);

			}
			return $wpdb->insert_id;
		}
		//
		else
		{
			var_dump($data);
			
		}
		
	}
	/*End Inset INto Custom IP Table*/

	/*Insert custom comment into Post meta*/
	public function insertCustomCommentToPostMeta($postId,$comment)
	{
		add_post_meta($postId,"custom_comment",$comment);
		header("location:" . $_SERVER['HTTP_REFERER']);
	}
	/*End Insert custom comment into Post meta*/

	/*Update custom comment into Post meta*/
	public function updateCustomCommentFromPostMeta($previousComment,$postId,$comment)
	{
		if(strlen($comment)==0)
		delete_post_meta($postId,"custom_comment",$previousComment);
		update_post_meta($postId,"custom_comment",$comment,$previousComment);
	}
	/*End Update custom comment into Post meta*/

}


?>

