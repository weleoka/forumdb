<hr>

<?php
		if (isset($title)) {
				echo "<h4>" . $title . "</h4>";
			//	unset($title);
		}
?>
 <div class="commentAll">
        <div id="comment-<?=$question->id?>" class="commentUnit">
            <div class="commentBox">
                <div class="gravatar">
                    <img src="http://www.gravatar.com/avatar/<?=md5($comment->email);?>.jpg?s=60">
                </div>
                <div class="commentData">
                    <?php
                    		  $userHome = $this->url->create('users/id/' . $question->userID);
                          $commentQ  = $this->url->create('forumdb/commentq/' . $question->id);
                    ?>
                    <a href="<?=$userHome?>"><?=$question->name?></a>:
                    <?=$question->title?>
                    <time><?php echo $question->timestamp; ?></time>
                </div>
					 <p class="commentContent">
                    <?=nl2br($question->content)?>
                </p>

					 <?php if (isset($_SESSION['user'])) : ?> 
                <div class="commentButtonsDiv">

 	                   <div class="commentButtons">
									<form action="<?=$commentQ?>" method="post">
										<input type="text" name="comment" placeholder="Kommentera" value="" maxlength="100" >
										<input class="hidden" type="submit" value="Submit" >
									</form>
							  </div>

                </div>
					 <?php endif; ?>


    	  <?php $numberOfcomments = count($comments); ?>

        <?php  if ($numberOfcomments == 0) : ?>
        				  Inga kommentarer.
        <?php elseif ($numberOfcomments == 1) : ?>
        				  En kommentar.
        <?php else : ?>
        				  <?php echo $numberOfcomments; ?>
        				  kommentarer.
        <?php endif; ?>
        				  <div id="commentsDiv">
            		  		<?php foreach ($comments as $comment) : ?>
						  			<?php $userHome = $this->url->create('users/id/' . $comment['userID']); ?>
										<div id="commentBox"> 
										                   			
                    					<a href="<?=$userHome?>"><?=$comment['name']?></a>

                    					<time><?php echo $comment['timestamp']; ?></time>

                    					<p class="commentContent">
                    						<?=nl2br($comment['content'])?>
                    					</p>
                	  					
                	  				</div>
                	  		<?php endforeach; ?>
                	  </div>
			 	</div>
        </div>
</div>
<?php

?>
