<?php 
		if (isset($title)) {
				echo "<h4>" . $title . "</h4>";
			//	unset($title);
		}
?>
 <div class="commentAll">
    <h4>
        <?php  if (count($questions) == 0) : ?> 
        Inga frågor. 
        <?php elseif (count($questions) == 1) : ?>
        1 fråga.
        <?php else : ?>
        <?php echo count($questions); ?>
        frågor.
        <?php endif; ?>
    </h4>

        <?php $i = 0; foreach ($questions as $question) : ?>
	
		  <?php if ($i % 2 == 0 ) : ?>
        <div class="commentUnit even">
		  <?php else : ?>        
		  <div class="commentUnit odd">
		  <?php endif; $i++; ?>

            <div class="commentBox">

                <div class="gravatar">
                   <img src="http://www.gravatar.com/avatar/<?=md5($question['email']);?>.jpg?s=60"><br>
                </div>

                <div class="commentData">

                    <?php $userHome = $this->url->create('users/id/' . $question['userID']); ?>
                    <?php $questionHome = $this->url->create('forumdb/id/' . $question['id']); ?>
                    <?php $tagHome = $this->url->create('forumdb/view/' . $question['tag']); ?>                
							
                    <a href="<?=$userHome?>"><?=$question['name']?></a>: 
                    <a href="<?=$questionHome?>"><?=$question['title']?></a> 
                    <?=$question['answerCount']?> svar.

                    <time><?php echo $question['timestamp']; ?></time>

                </div>
					 <p class="commentContent">
                    <?=mb_substr($question['content'], 0, 35)?>
                    <a class="read-more" href="<?=$questionHome?>"> Se hela frågan</a>

                   
					<?php	if (!isset($tag)) : ?>
                   <br> Se alla frågor i kategorin: <a href="<?=$tagHome?>"><?=$question['tag']?></a>
					<?php endif; ?>
                </p>
                
                <div class="commentButtonsDiv">

                </div>

            </div>

        </div>
        <?php endforeach; ?>
</div> 

