<?php
namespace Weleoka\Forumdb;
 
/**
 * Model for Comments.
 *
 */
class CommentA extends \Weleoka\Forumdb\ForumdbModel
{
	
/**
 * Find and return comments specific to answer.
 *
 * @return this
 */
	public function findQcomments($id)
	{
		if (isset($id)){
    		$this->db->select()
         	    ->from("comment")
            	 ->where("type = 'questionComment'")
             	 ->andWhere("parentID = ?");

    		$this->db->execute([$id]);
    		return $this->db->fetchInto($this);
 		} else {
			echo "No comments found, sorry";
 		}
	}	

/**
 * Find and return comments specific to answer.
 *
 * @return this
 */
	public function findAcomments($id)
	{
		if (isset($id)){
    		$this->db->select()
         	    ->from("comment")
            	 ->where("type = 'answerComment'")
             	 ->andWhere("parentID = ?");

    		$this->db->execute([$id]);
    		return $this->db->fetchInto($this);
 		} else {
			echo "No comments found, sorry";
 		}
	}
/*
	public function findAll($parentID)
	{
	  	$this->db->select()
	  			->from($this->getSource())
	  			->where('parentID = ?');
     	$this->db->execute($parentID);
     	return $this->db->fetchInto($this);
	}
*/
/*
public function findByName($acronym)
    {
echo "user created";     
        $this->db->select()->from($this->getSource())->where('acronym = ?');
        $this->db->execute([$acronym]);
        return $this->db->fetchInto($this);
    }

*/
}