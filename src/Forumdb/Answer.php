<?php
namespace Weleoka\Forumdb;
 
/**
 * Model for Comments.
 *
 */
class Answer extends \Weleoka\Forumdb\ForumdbModel
{


/*
public function findByName($acronym)
    {
echo "user created";     
        $this->db->select()->from($this->getSource())->where('acronym = ?');
        $this->db->execute([$acronym]);
        return $this->db->fetchInto($this);
    }

*/


	  /**
     * View all posts; answers by certain user.
     *
     * @return void
     */
	public function viewAllposts($id)
	{    
  	  	  $all = $this->query()
           		->where('userID = "' . $id . '"')
           		->execute();

    	  $array = object_to_array($all);
    	  return $array;
   }


}