<?php
namespace Weleoka\Forumdb;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class ForumdbController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

public function initialize()
    {
        $this->forum = new \Weleoka\Forumdb\Forum();
        $this->forum->setDI($this->di);

        $this->questions = new \Weleoka\Forumdb\Question();
        $this->questions->setDI($this->di);
        
        $this->answers = new \Weleoka\Forumdb\Answer();
        $this->answers->setDI($this->di);
        
        $this->comments = new \Weleoka\Forumdb\Comment();
        $this->comments->setDI($this->di);
    }

 /**
 * List comment with id.
 *
 * @param int $id of user to display
 *
 * @return void
 */
	public function idAction($id = null)
	{ 	
			$one = $this->questions->find($id);
			
			$this->theme->setTitle("Se specifik Fråga");
			
         $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen(),
       		],'sidebar');	
       		
			$this->views->add('comments/commentsq', [
				'comment' => $one,
				//'title' => 'Visar information för: ',
			]);

			$tab = $one->tab;
			if (isset($_SESSION['user'])) {
				$form = $this->form;
				$form = $form->create([], [
					'content' => [
						'type'        => 'textarea',
						'label'       => 'Kommentar',
						'required'    => true,
						'placeholder' => 'Kommentar',
						'validation'  => ['not_empty'],
					],
					'submit' => [
						'type'      => 'submit',
						'class'		=> 'bigButton',
						'callback'  => function($form) use ($tab){
						$now = date_create()->format('Y-m-d H:i:s'); // returns local time

					//	$now = gmdate('Y-m-d H:i:s'); // returns UTC
             		$user = $this->questions->getUser();
						
						$this->answers->save([
								'userID'		=> $user->id,
								'question'	=> null,
								'parentID'  => $one->id,
                        'name'		=> $user->name,
                        'content'	=> $form->Value('content'),
                        'email'		=> $user->email,
                        'timestamp' => $now,
                        'tab' 		=> $tab,
						]);
						return true;
					}
				],
			]);

			// Check the status of the form
			$status = $form->check();

			if ($status === true) {
         // What to do if the form was submitted?
				$this->comments->AddFeedback('Ditt svar har sparats.');
         	$url = $this->url->create('' . $tab . '');
			   $this->response->redirect($url);	         	
				
			} else if ($status === false) {
      	// What to do when form could not be processed?
				$this->comments->AddFeedback('Ditt svar kunde inte sparas.');
				$url = $this->url->create('forumdb/add/' . $tab . '');
			   $this->response->redirect($url);	 
			}
          
			//Here starts the rendering phase of the add action
			$this->theme->setTitle("Lägg till kommentar");
 
	      $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen($tab),
       		],'sidebar');
       	
			$formOptions = [
            // 'start'           => false,  // Only return the start of the form element
            // 'columns' 	      => 1,      // Layout all elements in two columns
            // 'use_buttonbar'   => true,   // Layout consequtive buttons as one element wrapped in <p>
            // 'use_fieldset'    => true,   // Wrap form fields within <fieldset>
            // 'legend'          => isset($this->form['legend']) ? $this->form['legend'] : null,   // Use legend for fieldset
            // 'wrap_at_element' => false,  // Wraps column in equal size or at the set number of elements
        	];       	

			$this->views->add('comments/add', [
				'content' =>$form->getHTML($formOptions),
				'title' => 'Skapa en ny kommentar',
			]);
    		} else { 
    			$url = $this->url->create('');
    					
				$this->views->add('me/page', [
        			'content' => '<i class="fa fa-square-o"></i><a href="' . $url . '/users/login/' . $id . '"> Logga in</a> för att skriva inlägg',
    			]);
    		}
	}

 /*   $aaa = new \stdClass();
foreach ($one as $item => $value)
{
    $aaa->$item = $value;
}
	*/	
    /**
     * View all comments.
     *
     * @return void
     */
	public function viewAction($tab = null, $redirect = null)
	{
    	  $all = $this->questions->query()
            ->where('tab = "' . $tab . '"')
            ->execute();
    	  $array = object_to_array($all);
		  $this->theme->setTitle("Alla Frågor");
        $this->views->add('comments/comments', [
            'comments' => $array,
            'tab'      => $tab, 
            'redirect' => $redirect,
            'title'	  => 'Alla frågor',
        ]);

        $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen($tab),
       ],'sidebar');
	}

	
	
    /**
     * Add a comment.
     *
     * @return void
     */
	public function addAction($tab = null, $question = null)
	{
       $form = $this->form;
				$form = $form->create([], [
					'content' => [
						'type'        => 'textarea',
						'label'       => 'Kommentar',
						'required'    => true,
						'placeholder' => 'Kommentar',
						'validation'  => ['not_empty'],
					],
					'submit' => [
						'type'      => 'submit',
						'class'		=> 'bigButton',
						'callback'  => function($form) use ($tab){
						$now = date_create()->format('Y-m-d H:i:s'); // returns local time
						$user = $this->comments->getUser();
					//	$now = gmdate('Y-m-d H:i:s'); // returns UTC
             
						$this->comments->save([
								'userID'		=> $_SESSION['user']['id'],
                        'name'		=> $_SESSION['user']['name'],
                        'content'	=> $form->Value('content'),
                        'email'		=> $_SESSION['user']['email'],
                        'timestamp' => $now,
                        'tab' 		=> $tab,
						]);
						return true;
					}
				],
			]);

			// Check the status of the form
			$status = $form->check();

			if ($status === true) {
         // What to do if the form was submitted?
				$this->comments->AddFeedback('Kommentaren har sparats.');
         	$url = $this->url->create('' . $tab . '');
			   $this->response->redirect($url);	         	
				
			} else if ($status === false) {
      	// What to do when form could not be processed?
				$this->comments->AddFeedback('Kommentaren kunde inte sparas.');
				$url = $this->url->create('forumdb/add/' . $tab . '');
			   $this->response->redirect($url);	 
			}
          
			//Here starts the rendering phase of the add action
			$this->theme->setTitle("Lägg till kommentar");
 
	      $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen($tab),
       		],'sidebar');
       	
			$formOptions = [
            // 'start'           => false,  // Only return the start of the form element
            // 'columns' 	      => 1,      // Layout all elements in two columns
            // 'use_buttonbar'   => true,   // Layout consequtive buttons as one element wrapped in <p>
            // 'use_fieldset'    => true,   // Wrap form fields within <fieldset>
            // 'legend'          => isset($this->form['legend']) ? $this->form['legend'] : null,   // Use legend for fieldset
            // 'wrap_at_element' => false,  // Wraps column in equal size or at the set number of elements
        	];       	
       	if (isset($question)) {
			$this->views->add('me/page', [
				'content' =>$question,
			]);       	
       	}
			$this->views->add('comments/add', [
				'content' =>$form->getHTML($formOptions),
				'title' => 'Skapa en ny kommentar',
			]);
	}


    /**
     * Edit a comment.
     *
     * @param id of comment to edit.
     *
     * @return void
     */
	public function editAction($id)
	{
      $form = $this->form;

			$comment = $this->comments->find($id);
			$tab = $comment->tab;

				$form = $form->create([], [

					'content' => [
						'type'        => 'textarea',
						'label'       => 'Kommentar',
						'required'    => true,
						'placeholder' => 'Kommentar',
						'validation'  => ['not_empty'],
						'value' => $comment->content,
					],
					'submit' => [
						'type'      => 'submit',
						'class'		=> 'bigButton',
						'callback'  => function($form) use ($comment) {

						$now = gmdate('Y-m-d H:i:s');
             
						$this->comments->save([
								'content'	=> $form->Value('content'),
                        'timestamp' => $now,                      
						]);

						return true;
					}
				],

			]);

			// Check the status of the form
			$status = $form->check();

			if ($status === true) {
         // What to do if the form was submitted?
				$form->AddOutput("Kommentarens ändringar sparades.");
         	$url = $this->url->create('forumdb/view/' . $tab . '');
			   $this->response->redirect($url);	         	
				
			} else if ($status === false) {
      	// What to do when form could not be processed?
				$form->AddOutput("Kommentaren kunde inte sparas till databasen.");
				$url = $this->url->create('forumdb/edit/' . $tab . '');
			   $this->response->redirect($url);	 
			}
          
			//Here starts the rendering phase of the add action
			$this->theme->setTitle("Lägg till kommentar");
 
	      $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen($tab),
       		],'sidebar');
       	
			$formOptions = [
            // 'start'           => false,  // Only return the start of the form element
            // 'columns' 	      => 1,      // Layout all elements in two columns
            // 'use_buttonbar'   => true,   // Layout consequtive buttons as one element wrapped in <p>
            // 'use_fieldset'    => true,   // Wrap form fields within <fieldset>
            // 'legend'          => isset($this->form['legend']) ? $this->form['legend'] : null,   // Use legend for fieldset
            // 'wrap_at_element' => false,  // Wraps column in equal size or at the set number of elements
        	];       	
       	
			$this->views->add('comments/edit', [
				'content' =>$form->getHTML($formOptions),
				'title' => 'Redigera kommentar',
			]);
	}
 


    /**
     * Remove one specific comment (based on $id).
     *
     * @return void
     */
	public function deleteAction($id)
	{
		if (!isset($id)) {
        die("Missing id");
    	}
 	 	// $comment = $this->comments->find($id);
 	   $one = $this->comments->find($id);
 	   $tab = $one->tab;

    	$res = $this->comments->delete($id);

 	 	$feedback = "Kommentaren är nu permanent borttagen.";
	   
	  	$url = $this->url->create('forumdb/view/' . $tab . '');
	   $this->response->redirect($url);	
	 	// $this->viewAction($feedback, $tab);         
	}


    
/**
 * Generate sidebar content.
 *
 * @param 
 *
 * @return sidebar
 */
	public function sidebarGen($tab = null) 
	{
	  $user = new \Weleoka\Users\User();
	  $url = $this->url->create('');
     $sidebar = '<p><i class="fa fa-plus">    </i> <a href="' . $url . '/forumdb/add/' . $tab . '"> Ny kommentar</a></p>                 
                 <p><i class="fa fa-list-ol"></i><a href="' . $url . '/forumdb/view/' . $tab . '"> Alla</a></p>';
     if ($user->isAdmin()) {
     		$sidebar .= '<p><i class="fa fa-refresh"></i><a href="' . $url . '/setupComments"> Nolställ DB</a></p>';
	  }	  
	  return $sidebar;	           
//                 <p><i class="fa fa-check-square-o"></i><a href="' . $url . '/users/active"> ingen info</a></p>
//                 <p><i class="fa fa-square-o"></i><a href="' . $url . '/users/inactive"> ingen info</a></p>
//                 <p><i class="fa fa-trash-o"></i><a href="' . $url . '/users/deleted"> ingen info</a></p>
	}
}