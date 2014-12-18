<?php
namespace Weleoka\Forumdb;
 
/**
 * Model for Comments.
 *
 */
class CommentQ extends \Weleoka\Forumdb\ForumdbModel
{

/*
 * Find all comments to question.
 *
 * @param integer $id
 *
 * @return array
 */
 	public function findCommentQ ($id)
 	{	
 		$all = $this->query()
            ->where('parentID = ?')
            ->execute([$id]);
		return object_to_array($all);	
 	}	   

}