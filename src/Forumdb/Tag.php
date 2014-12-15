<?php
namespace Weleoka\Forumdb;
 
/**
 * Model for Comments.
 *
 */
class Tag extends \Weleoka\Forumdb\ForumdbModel
{
	
/*
 * Get list of tags into array.
 *
 * @return tagsArray
 */
	public function getTags() {
		
		$tags = $this->query()
            ->execute();

		foreach ($tags as $tag) {
			$aaa[$tag->tag] = $tag->tag;
		} 

		return $aaa;
	}
	
	
	
/*
 * Generate HTML list of tags with links.
 *
 * @return tagsArray
 */
	public function listTags() 
	{
		$html = '';
		$tags = $this->query()
            ->execute();
		foreach ($tags as $tag) {
			$show = $this->url->create('forumdb/view/' . $tag->tag);
			$html .= '<a href="' . $show . '"><button class="smallButton" >' . $tag->tag . '</button></a>';	
		}
		return $html;
	}



/*
 * Generate HTML list of tags with links.
 *
 * @return tagsArray
 */
	public function menuTags () 
	{
		$tags = $this->query()
            ->execute();
      $menu = array($tag => $tag);
      dump ($menu);
	//	foreach ($tags as $tag) {
	//		$menu = $this->url->create('forumdb/view/' . $tag->tag);
	//		$html .= '<a href="' . $show . '"><button class="smallButton" >' . $tag->tag . '</button></a>';	
	//	}
	/*	
		        // This is a menu item of the submenu
                    'item 1'  => [
                        'text'  => 'Kategorier.',   
                        'url'   => 'forumdb/viewtags',  
                        'title' => 'Forumkategorier.'
                    ],
*/	
	}
	
	
	
	
}




