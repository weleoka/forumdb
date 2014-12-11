<hr>

<?php
		if (isset($title)) {
				echo "<h4>" . $title . "</h4>";
			//	unset($title);
		}
?>
 <div class="commentAll">
    <h3>
        <?php  if (count($answers) == 0) : ?>
        Inga svar.
        <?php elseif (count($answers) == 1) : ?>
        Ett svar.
        <?php else : ?>
        <?php echo count($answers); ?>
        svar.
        <?php endif; ?>
    </h3>

        <?php foreach ($answers as $answer) : ?>
				<?php
						$answerID = $answer['id'];
						$voteUp = $this->url->create('forumdb/voteup/' . $answerID);
            		$voteDown = $this->url->create('forumdb/votedown/' . $answerID);
            //		$edit = $this->url->create('forumdb/edit/' . $answerID);
            //    $delete = $this->url->create('forumdb/delete/' . $answerID);
            		$commentA  = $this->url->create('forumdb/commenta/' . $answerID);
            		$userHome = $this->url->create('users/id/' . $answerID);
            ?>


        		<div id="answer-<?=$id?>" class="commentUnit">

            	<div class="commentBox">

                	<div class="gravatar">

                    <img src="http://www.gravatar.com/avatar/<?=md5($answer['email']);?>.jpg?s=60"><br>
						  <a href="<?=$voteUp?>" style="font-size:2em">+</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						  <a href="<?=$voteDown?>" style="font-size:2em">&ndash;</a>
                	</div>

                	<div class="commentData">

                    <a href="<?=$userHome?>"><?=$answer['name']?></a>

                    <time><?php echo $answer['timestamp']; ?></time>

                	</div>

                	<p class="commentContent">
                    <?=nl2br($answer['content'])?>
                	</p>


                	<div class="commentButtonsDiv">

	                    <div class="commentButtons">
									<form action="<?=$commentA?>" method="post">
										<input type="text" name="comment" placeholder="Kommentera" value="" maxlength="100" >
										<input class="hidden" type="submit" value="Submit" >
									</form>
							  </div>

                	</div>

        		<?php
        					$this->db->select()
        				            ->from('commenta')
        				    			->where('parentID = ?')
            						->execute($this->db->getSQL(), [ $answer['id'] ]);
            		   $this->db->setFetchModeClass(__CLASS__);
    						$res = $this->db->fetchAll();
							$comments = object_to_array($res);
				?>


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
    <?php endforeach; ?>
</div>

