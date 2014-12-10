<hr>

<?php 
		if (isset($title)) {
				echo "<h4>" . $title . "</h4>";
			//	unset($title);
		}
?>
 <div class="commentAll">

        <div id="comment-<?=$comment->id?>" class="commentUnit">

            <div class="commentBox">

                <div class="gravatar">
                    
                    <img src="http://www.gravatar.com/avatar/<?=md5($comment->email);?>.jpg?s=60">

                </div>

                <div class="commentData">

                    <?php $userHome = $this->url->create('users/id/' . $comment->userID); ?>
                    <?php $question = $this->url->create('commentsdb/id/' . $comment->id); ?>                 
							
                    <a href="<?=$userHome?>"><?=$comment->name?></a>: 
                    <a href="<?=$question?>"><?=$comment->question?></a> 

                    <time><?php echo $comment->timestamp; ?></time>

                </div>
					<p class="commentContent">
                    <?=nl2br($comment->content)?>
                </p>
					

            </div>

        </div>
</div>
<?php

?>
