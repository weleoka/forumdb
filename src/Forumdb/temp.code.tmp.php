<?php 
 /*   $aaa = new \stdClass();
foreach ($one as $item => $value)
{
    $aaa->$item = $value;
}
	*/
	
	
		public function findAll()
	{
	  	$this->db->select()->from($this->getSource());
     	$this->db->execute();
     	return $this->db->fetchInto($this);
	}
	
	gives GMT time
						//	$now = gmdate('Y-m-d H:i:s'); // returns UTC
						
						
						
       	
			$formOptions = [
            // 'start'           => false,  // Only return the start of the form element
            // 'columns' 	      => 1,      // Layout all elements in two columns
            // 'use_buttonbar'   => true,   // Layout consequtive buttons as one element wrapped in <p>
            // 'use_fieldset'    => true,   // Wrap form fields within <fieldset>
            // 'legend'          => isset($this->form['legend']) ? $this->form['legend'] : null,   // Use legend for fieldset
            // 'wrap_at_element' => false,  // Wraps column in equal size or at the set number of elements
        	]; 
        	
        	
        	--- from sidebarGen
        	//                 <p><i class="fa fa-check-square-o"></i><a href="' . $url . '/users/active"> ingen info</a></p>
//                 <p><i class="fa fa-square-o"></i><a href="' . $url . '/users/inactive"> ingen info</a></p>
//                 <p><i class="fa fa-trash-o"></i><a href="' . $url . '/users/deleted"> ingen info</a></p>



&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp



  $this->views->add('comments/commentsqs', [
            'questions' => $array,
            'tag'      	=> $tag,
            'title'	  	=> 'Alla användarens frågor.',
        ]);

  
        $this->views->add('comments/commentsqs', [
            'questions' => $array,
            'tag'      	=> $tag,
            'title'	  	=> 'Alla användarens svar på frågor.',
        ]);
        
        
        
        
        
        FROM USERSCONTROLLER IDACTION
        
        			$questions = new \Weleoka\Forumdb\Question();
			$userQuestions = $questions->viewAllposts($id);
			$this->views->add('forumdb/comments', [
				'comments' => $userQuestions,
				'title' => 'Visar användarens frågor och svar: ',
			], 'main');