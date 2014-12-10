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
}




