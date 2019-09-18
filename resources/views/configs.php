
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<div class="container">
	
   	<?php session_start(); if($_SESSION['message']??false):?>
		<p><?=$_SESSION['message']?></p>
      <?php unset($_SESSION['message']); endif;?>
	  <form method="post" action="<?=plugins_url()?>/viewCounter/controllers/Config.php">
	    <div class="form-row d-flex justify-content-center">
	      <div class="col-sm-6 my-1">
	        <label class="sr-only" for="inlineFormInputName">Timer</label>
	        <input type="text" class="form-control" id="inlineFormInputName" placeholder="After How Long View Will Be Count?" name="duration">
	      </div>
	      <div class="col-auto my-1">
	        <button type="submit" class="btn btn-primary">Add</button>
	      </div>
	   </div>
	  </form>
</div>