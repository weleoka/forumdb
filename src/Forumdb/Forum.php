<?php
namespace Weleoka\Forumdb;

/**
 * Model for Comments.
 *
 */
class Forum extends \Weleoka\Forumdb\ForumdbModel
{


/*
 * Get user from SESSION into object.
 *
 * @return usr
 */
	public function getUser()
	{
		if (isset($_SESSION['user'])) {
			$usr = new \stdClass();
			foreach ($_SESSION['user'] as $item => $value)
			{
				$usr->$item = $value;
			}
			return $usr;
		}
		$this->AddFeedback('Din session har tagit slut, var god logga in på nytt');
	}



/*
 * Reset the users session TTL. The default timeout is dictaded by class: User.
 *
 * @param object $contributor
 *
 * @return void
 */
	public function resetTTL ()
	{
		$_SESSION['timeout']['startPoint'] = time();
	}



/*
 * log the users contribution to forum to their contributionCount.
 *
 * @param object $contributor
 *
 * @return void
 */
	public function userContributionLog ($contributor)
	{
		$count = $contributor->contributionCount + 1;		
		$_SESSION['user']['contributionCount'] = $count;
/*		
		$sql = "
			UPDATE phpmvc_user
			SET
    			contributionCount = ?
			WHERE id = ?
			;";
		$this->db->execute($sql, [$count , $contributor->id]);
		*/
		$this->db->update(
    		'user',
    		['contributionCount'],
    		"id = ?"
		);
		$this->db->execute([$count , $contributor->id]);

	}

/*
 * Check in user is logged on.
 *
 * @return boolean
 */
	public function userIsAuthenticated()
	{
		$user = new \Weleoka\Users\User();
		return $user->isAuthenticated();
	}



/*
 * Check if current user is admin.
 *
 * @return boolean
 */
	public function userIsAdmin()
	{
		$user = new \Weleoka\Users\User();
		return $user->isAdmin();
	}



/*
 * Redirect user who accesses controller actions without authentification.
 *
 * @return void
 */
	public function kickOutBaddie()
	{
		$this->AddFeedback('Du är inte inloggad.');
		$url = $this->url->create('users/login/');
		header('Refresh: 3; URL='. $url);
	}



	 /**
     * Add output to display to the user what happened with the comment.
     *
     * @param string $str the string to add as output.
     *
     * @return $this CForm.
     */
    public function AddFeedback($str)
    {
        if (isset($str)) {
            $_SESSION['user-feedback'] =  $str;
        } else {
            $_SESSION['user-feedback'] = null;
        }
        return $this;
    }



 /*
 * Generate HTML list of top 3 active users with links.
 *
 * @return tagsArray
 */
	public function topUsers()
	{
      $this->db->select('id, name, contributionCount')
      			->from('user')
      			->orderBy('contributionCount DESC');
      $this->db->execute();
      $this->db->setFetchModeClass(__CLASS__);
      $users = $this->db->fetchAll();

      $html = 'Aktivaste användarna:<br>';
		$i = 0;
  		foreach ($users as $user) {
			$show = $this->url->create('users/id/' . $user->id);
			$html .= '<a href="' . $show . '">' . $user->name . '</a> med ' . $user->contributionCount . ' inlägg.<br>';
			$i++;
			if ($i >= 3) { break; };
		}
		return $html;
	}




/* ---------------------------- SIDEBAR & NAV-MENU generation FOR FORUM -----------------------------------*/

/**
 * Generate sidebar content.
 *
 * @param
 *
 * @return sidebar
 */
	public function sidebarGen($tag = null)
	{
	  $url = $this->url->create('');

     $categories = $this->forumTags();

	  if ($this->userIsAuthenticated()) {
	  		$sidebar = '<p><i class="fa fa-plus">    </i> <a href="' . $url . '/forumdb/add/' . $tag . '"> Ny fråga</a></p>';
	  } else {
			$sidebar = '<i class="fa fa-square-o"></i><a href="' . $url . '/users/login"> Logga in</a> för att skriva inlägg';
	  }
     $sidebar .= '<p>Forum kategorier:</p>
					  <p><i class="fa fa-list-ol"></i><a href="' . $url . '/forumdb/view"> Alla</a></p>';
					  foreach ( $categories as $category ) {
                 $sidebar .= '<p><i class="fa fa-anchor"></i><a href="' . $url . '/forumdb/view/' . $category->tag . '"> ' . $category->tag . '</a></p>';
                 };

     if ($this->userIsAdmin()) {
     		$sidebar .= '--- admin ---';
     		$sidebar .= '<p><i class="fa fa-plus"></i><a href="' . $url . '/forumdb/addtag"> Lägg till tag</a></p>';
     		$sidebar .= '<p><i class="fa fa-refresh"></i><a href="' . $url . '/setupComments"> Nolställ DB</a></p>';
	  }

	  return $sidebar;
	}







/*
 * Get object containing all forum tags, for use in the above functions.
 *
 * @return object
 */
	public function forumTags()
	{
     $this->db->select('tag')
         	  ->from('tag');
     $this->db->execute();
 	  $this->db->setFetchModeClass(__CLASS__);
     return $this->db->fetchAll();
	}
	
	
/******************************* RETIRED CODE **********************************************/
/*
		$this->db->select('contributionCount')
      			->from('user')
      			->where('id = ?');
      $this->db->execute([$contributor->id]);
      $this->db->setFetchModeClass(__CLASS__);
      $user = $this->db->fetchAll();
  */  
    //  $user = $this->db->fetchInto($this);
	//	dump ($user);
		

}
