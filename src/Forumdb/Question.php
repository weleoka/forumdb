<?php
namespace Weleoka\Forumdb;
 
/**
 * Model for Comments.
 *
 */
class Question extends \Weleoka\Forumdb\ForumdbModel
{

	
	 /**
     * Remove one specific entry (based on $id).
     *
     * @return void
     */
	public function dddeleteAction($id)
	{
		if (!isset($id)) {
        die("Missing id");
    	}
 	 	// $comment = $this->comments->find($id);
 	   $one = $this->find($id);
 	   $tab = $one->tab;

    	$res = $this->delete($id);
	   
	  	$url = $this->url->create('forumdb/view/' . $tab . '');
	   $this->response->redirect($url);	
	 	// $this->viewAction($feedback, $tab);         
	}
}