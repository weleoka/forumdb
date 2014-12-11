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
	
	
	

}




