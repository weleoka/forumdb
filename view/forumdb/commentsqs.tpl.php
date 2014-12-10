<hr>

<?php 
		if (isset($title)) {
				echo "<h4>" . $title . "</h4>";
			//	unset($title);
		}
?>
 <div class="commentAll">
    <h4>
        <?php  if (count($comments) == 0) : ?> 
        Inga frågor. 
        <?php elseif (count($comments) == 1) : ?>
        1 fråga.
        <?php else : ?>
        <?php echo count($comments); ?>
        frågor.
        <?php endif; ?>
    </h4>

        <?php foreach ($comments as $comment) : ?>

        <div id="comment-<?=$id?>" class="commentUnit">

            <div class="commentBox">

                <div class="gravatar">
                    
                    <img src="http://www.gravatar.com/avatar/<?=md5($comment['email']);?>.jpg?s=60">

                </div>

                <div class="commentData">

                    <?php $userHome = $this->url->create('users/id/' . $comment['userID']); ?>
                    <?php $question = $this->url->create('commentsdb/id/' . $comment['id']); ?>                 
							
                    <a href="<?=$userHome?>"><?=$comment['name']?></a>: 
                    <a href="<?=$question?>"><?=$comment['question']?></a> 

                    <time><?php echo $comment['timestamp']; ?></time>

                </div>
					<p class="commentContent">
                    <?=mb_substr(nl2br($comment['content']), 0, 25)?>...
                    <a class="read-more" href="<?=$question?>">Läs mer</a>
                </p>
					

            </div>

        </div>
        <?php endforeach; ?>
</div> 

