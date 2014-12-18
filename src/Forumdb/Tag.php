<?php
namespace Weleoka\Forumdb;
 
/**
 * Model for Comments.
 *
 */
class Tag extends \Weleoka\Forumdb\ForumdbModel
{
	
	
	
/*
 * Increase the number of posts, both questions and answers under certain tag.
 *
 * @param string
 *
 * @return void
 */	
	public function increasePostCount ($tag)
	{	
		$taglog = $this->findTag($tag);
		$parameters['postCount'] = $taglog->postCount + 1;
		$this->update($parameters);
	}					
					
					
					
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
			$html .= '<a href="' . $show . '">' . $tag->tag . '</a>, ';	
		}
		return $html;
	}


/*
 * Find specific tag row.
 *
 * @return object 
 */
public function findTag($acronym)
    {    
        $this->db->select()
        		->from($this->getSource())
        		->where('tag = ?');
        $this->db->execute([$acronym]);
        return $this->db->fetchInto($this);
    }
    
    
    
/*
 * Generate HTML list of top 3 tags with links.
 *
 * @return tagsArray
 */
	public function topTags()
	{		      
      $this->db->select('tag, postCount')
      			->from($this->getSource())
      			->orderBy('postCount DESC');
      $this->db->execute();
      $this->db->setFetchModeClass(__CLASS__);
      $tags = $this->db->fetchAll();

      $html = 'Populäraste forumkategorierna:<br>';
		$i = 0;
  		foreach ($tags as $tag) {
			$show = $this->url->create('forumdb/view/' . $tag->tag);
			$html .= '<a href="' . $show . '">' . $tag->tag . '</a> med ' . $tag->postCount . ' inlägg.';
			$i++;
			if ($i >= 3) { break; };
			$html .= '<br>';
		}
		return $html;
	}


/* ------------------------------ RETIRED FUNCTIONS-------------------------------------*/
/*
 * Generate array of all tags formatted for Anax-MVC Navbar.
 *
 * @return array tagsArray
 *
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
                    
                    



	public function findByName($acronym)
   {
	echo "searching for user";
        $this->db->select()
        		->from($this->getSource())
        		->where('acronym = ?');
        $this->db->execute([$acronym]);
        return $this->db->fetchInto($this);
   }
*/		
	
}




