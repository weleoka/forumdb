<hr>

<?php
		if (isset($title)) {
				echo "<h4>" . $title . "</h4>";
			//	unset($title);
		}
?>
 <div class="commentAll">
    <h3>
        <?php  if (count($comments) == 0) : ?>
        Inga frågor.
        <?php elseif (count($comments) == 1) : ?>
        1 fråga.
        <?php else : ?>
        <?php echo count($comments); ?>
        frågor.
        <?php endif; ?>
    </h3>

        <?php foreach ($comments as $comment) : ?>

        <div id="comment-<?=$id?>" class="commentUnit">

            <div class="commentBox">

                <div class="gravatar">

                    <img src="http://www.gravatar.com/avatar/<?=md5($comment['email']);?>.jpg?s=60">

                </div>

                <div class="commentData">

                    <?php $userHome = $this->url->create('users/id/' . $comment['userID']); ?>

                    <a href="<?=$userHome?>"><?=$comment['name']?></a>

                    <time><?php echo $comment['timestamp']; ?></time>

                </div>

                <p class="commentContent">
                    <?=nl2br($comment['content'])?>
                </p>



					<?php $edit = $this->url->create('forumdb/edit/' . $comment['id']);
                     $delete = $this->url->create('forumdb/delete/' . $comment['id']);?>
                <div class="commentButtonsDiv">

                    <div class="commentButtons">
								<a href="<?=$edit?>"><button class="smallButton" >Redigera</button></a>
								<a href="<?=$delete?>"><button class="smallButton" >Ta bort</button></a>
                    </div>

                </div>

            </div>

        </div>
        <?php endforeach; ?>
</div>

