<?php
namespace Weleoka\Forumdb;
 
/**
 * Model for Comments.
 *
 */
class Question extends \Weleoka\Forumdb\ForumdbModel
{
	 /**
     * Find all questions under specific tag. Order DESC.
     *
     * @param string $tag
     *
     * @return object
     */
	public function getbyTag($tag = null)
	{
      $this->db->select('id, title, userID, name, content, email, timestamp, tag, answerCount')
      			->from($this->getSource())
      			->where('tag = ?')
      			->orderBy('timestamp DESC');
      $this->db->execute([$tag]);
      $this->db->setFetchModeClass(__CLASS__);
      return $this->db->fetchAll();	
	}	
	
	
	
/*
 * Generate HTML list of 3 latest questions with links.
 *
 * @return tagsArray
 */
	public function newPosts()
	{	      
      $this->db->select('id, title')
      			->from($this->getSource())
      			->orderBy('timestamp DESC');
      $this->db->execute();
      $this->db->setFetchModeClass(__CLASS__);
      $posts = $this->db->fetchAll();

      $html = 'Senaste frågorna:<br>';
		$i = 0;
  		foreach ($posts as $post) {
  			$title['title'] = $post->title;
			$show = $this->url->create('forumdb/id/' . $post->id);
			$html .= '<a href="' . $show . '">' . mb_substr($title['title'], 0, 45) . '</a><br>';
			$i++;
			if ($i >= 3) { break; };
		}
		return $html;
	}
	
	
	public function topQuestions()
	{



	}	
	
	
	
/* ------------------------------ RETIRED FUNCTIONS-------------------------------------*/


	 /**
     * Remove one specific entry (based on $id).
     *
     * @return void
     *
	public function deleteAction($id)
	{
		if (!isset($id)) {
        die("Missing id");
    	}
 	   $one = $this->find($id);
 	   $tab = $one->tab;

    	$res = $this->delete($id);
	   
	  	$url = $this->url->create('forumdb/view/' . $tab . '');
	   $this->response->redirect($url);	        
	}		
		
*/		
		
/*	
		    	$this->db->select()
             ->from($this->getSource());

    	$this->db->execute();
    	$this->db->setFetchModeClass(__CLASS__);
    	return $this->db->fetchAll();
	            'id' 				=> ['integer', 'primary key', 'not null', 'auto_increment'],
            'title'			=> ['varchar(100)'],
            'userID'			=> ['integer'],
            'name'      	=> ['varchar(20)', 'not null'],
            'content'   	=> ['text(1000)'],
            'email'  		=> ['varchar(80)'],
            'timestamp' 	=> ['datetime'],
            'tag'				=> ['varchar(80)'],
            'answerCount' 	=> ['integer'],
	
	*/
	
	
	
	
}




