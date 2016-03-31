<?php

namespace Weleoka\Forumdb;

/**
 * Model for Comments.
 *
 */
class Answer extends \Weleoka\Forumdb\ForumdbModel
{

	/*
	 * Find all answers to question.
	 *
	 * @param integer $id
	 *
	 * @return array
	 */
    public function findAnswers ($id)
    {
        $all = $this->query()
           ->where('parentID = ?')
           ->execute([$id]);

        return object_to_array($all);

    }
}
