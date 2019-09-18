<?php
$postClass=$_SERVER["DOCUMENT_ROOT"]."/wordpress/wp-content/plugins/viewCounter/controllers/Post.php";
require_once($postClass);

//First Time Load
  $postObject=new Post();

 if(file_exists($postClass)&&(!isset($_POST['filterByAuthorOrView']))){
 	$postObject=new Post();
 	$posts=$postObject->getPostByAuthor(explode('_',$_GET['page'])[1]??null);

}
//
//Apply Filter
  if($_POST['filterByAuthorOrView']??false&&$_POST["filter"]??false)
  {

    $postObject=new Post();
    $posts=$postObject->getPostByAuthorName($_POST["filter"]);
    if(sizeof($posts->posts)==0)
    {
        $posts=$postObject->getPostByCustomMeta("count",$_POST["filter"]);
        if(sizeof($posts->posts)==0)
        {
          $posts=null;
        }
      
    }

  }
  //

  //Dropdown based author post
  if($_POST['dropdown']??false)
  {
    $posts=$postObject->getPostByAuthor($_POST["authorDropdown"]);

  }

  //End Dropdown based author post



?>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<div class="container">
  <form method="POST" action="<?=$_SERVER['PHP_SELF']?>?page=vc" id="dropdownForm"> 
    <div class="form-row d-flex justify-content-center">
      <div class="col-sm-3 my-1">
        <select class="form-control" id="authorDropdown" name="authorDropdown">
          <option disabled="" selected="">Select Author</option>
          <?php
          $authors = get_users( [ 'role__in' => [ 'author','administrator' ] ] );   
          foreach($authors as $author):;?> 
          <option value="<?=$author->ID?>"><?=$author->display_name?></option>
        <?php endforeach; ?>
        </select>
        <input type="hidden" name="dropdown" value="true">
      </div>
  
    </div>
   
  </form>
  <form method="post" action="<?=$_SERVER['PHP_SELF']?>?page=vc">
    <div class="form-row d-flex justify-content-end">
      <div class="col-sm-3 my-1">
        <label class="sr-only" for="inlineFormInputName">Name</label>
        <input type="text" class="form-control" id="inlineFormInputName" placeholder="Filter by views or author" name="filter">
      </div>
      <div class="col-auto my-1">
        <input type="hidden" name="filterByAuthorOrView" value="1">
        <button type="submit" class="btn btn-primary">Search</button>
      </div>
  </div>
  </form>
<table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Post ID</th>
      <th scope="col">Post Name</th>
      <th scope="col">Today's View</th>
      <th scope="col">Total Views</th>
   
      <th scope="col">Admin Comments</th>
        <?php if(  current_user_can('administrator')):?>
      <th scope="col">Action</th>
      <?php endif;?>
    </tr>
  </thead>
  <tbody>
 
<?php 
if ( $posts->posts??false ) : ?>
 
    <!-- pagination here -->
 
    <!-- the loop -->
    <?php
    foreach($posts->posts as $post):;?>
      <tr>

	        <td><?= $post->ID; ?></td>
          <td><?= $post->post_title; ?></td>
          <td><?=  $postObject->getCurrentDatePostViewsCount($post->ID)->views; ?></td>
	        <td><?=  get_post_meta($post->ID,"count",true); ?></td>

          <td  >
            <?php  foreach(get_post_meta($post->ID,"custom_comment") as $comment):;?>
              <p <?php if( current_user_can('administrator')):?>contenteditable=true class="editComment" <?php endif; ?>data-prev-val="<?=$comment?>" data-post-id="<?=$post->ID?>"><?=$comment?></p>
              <?php endforeach; ?>
          </td>

          <?php if(  current_user_can('administrator')):?>
          <td>
            <button type="button" class="btn btn-sm btn-primary" name="addComment" data-post-id="<?=$post->ID?>">
               Comment
            </button>
          </td>
          <?php endif;?>

	    </tr>
    <?php endforeach; ?>
    <!-- end of the loop -->
 
    <!-- pagination here -->
 
    <?php wp_reset_postdata(); ?>
 
<?php else : ?>
    <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
<?php endif; ?>

 
  </tbody>
</table>

 

  <!-- The Modal -->
  <form class="form-inline" method="POST" action="<?=plugins_url()?>/viewCounter/controllers/Post.php">
  <div class="modal" id="myModal">
    <div class=" modal-dialog modal-sm">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Add Comment</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
           <div class="form-group  mb-2">
              <textarea name="comment" class="form-group"></textarea>
            </div>
            <input type="hidden" name="postId" >
            <input type="hidden" name="customComment" value="1">
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button  class="btn btn-success">Save</button>
          <button  type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        
      </div>
    </div>
  </div>
</form>


<script type="text/javascript">
  
  $(function(){
    /*Add Comment*/
    $("button[name='addComment']").click(function(){
      $("input[name='postId']").val($(this).data('postId'))
      $("#myModal").modal();
    });
    /*End Add Comment*/

    /*Get Selected Author Posts*/
    $("#authorDropdown").change(function(){
      
      $("#dropdownForm").submit();
    });

    /*End Get Selected Author Posts*/

    /*Ajax call on comment edit comment*/
    $(".editComment").focusout(function(){
      url = "<?=plugins_url()?>"+"/viewCounter/controllers/Post.php";
      data={"previousComment":$(this).data('prevVal'),"comment":$(this).text(),"postId":$(this).data('postId')};
      $.post(url,data)
       .done(function(){
        alert("Comment is updated!");
       });
    });
  });
</script>
</div>