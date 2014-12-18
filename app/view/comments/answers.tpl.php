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

        <?php $i = 0; foreach ($answers as $answer) : ?>
				<?php
						$answerID = $answer['id'];
						$voteUp = $this->url->create('forumdb/voteup/' . $answerID);
            		$voteDown = $this->url->create('forumdb/votedown/' . $answerID);
            //		$edit = $this->url->create('forumdb/edit/' . $answerID);
            //    $delete = $this->url->create('forumdb/delete/' . $answerID);
            		$commentA  = $this->url->create('forumdb/commenta/' . $answerID);
            		$userHome = $this->url->create('users/id/' . $answer['userID']);
            		$questionHome = $this->url->create('forumdb/id/' . $answer['parentID']);
            ?>

		  <?php if ($i % 2 == 0 ) : ?>
        <div class="commentUnit even">
		  <?php else : ?>        
		  <div class="commentUnit odd">
		  <?php endif; $i++; ?>
        		  	<div class="commentBox">

                	<div class="gravatar">

                    <img src="http://www.gravatar.com/avatar/<?=md5($answer['email']);?>.jpg?s=60"><br>
						  <a href="<?=$voteUp?>" style="font-size:2em">+</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						  <a href="<?=$voteDown?>" style="font-size:2em">&ndash;</a>
                	</div>

                	<div class="commentData">

                    <a href="<?=$userHome?>"><?=$answer['name']?></a>
                    Re:
						  <a href="<?=$questionHome?>"><?=mb_substr($answer['parentTitle'], 0, 35)?></a>						  
						  
                    <time><?php echo $answer['timestamp']; ?></time>

                	</div>

                	<p class="commentContent">
                    <?=$answer['content']?>
                	</p>

				<?php if (!isset( $cleanView )) : ?>
					<?php if (isset($_SESSION['user'])) : ?> 
                	<div class="commentButtonsDiv">

	                    <div class="commentButtons">
									<form action="<?=$commentA?>" method="post">
										<input type="text" name="comment" placeholder="Kommentera" value="" maxlength="100" >
										<input class="hidden" type="submit" value="Submit" >
									</form>
							  </div>

                	</div>
					<?php endif; ?>
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
        		<?php elseif ($numberOfcomments == 1) : ?>
        				  Kommentarer (1).
        		<?php else : ?>
        				  Kommentarer (<?php echo $numberOfcomments; ?>).
        		<?php endif; ?>

       				  <div id="commentsDiv">
            		  		<?php foreach ($comments as $comment) : ?>
						  			<?php $userHome = $this->url->create('users/id/' . $comment['userID']); ?>
										<div id="commentBox">

                    					<a href="<?=$userHome?>"><?=$comment['name']?></a>

                    					<time><?php echo $comment['timestamp']; ?></time>

                    					<p class="commentContent">
                    						<?=$comment['content']?>
                    					</p>

                	  				</div>
                	  		<?php endforeach; ?>
                	  </div>
            <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

