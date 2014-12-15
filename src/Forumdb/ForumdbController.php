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

        $this->commentQs = new \Weleoka\Forumdb\CommentQ();
        $this->commentQs->setDI($this->di);

        $this->commentAs = new \Weleoka\Forumdb\CommentA();
        $this->commentAs->setDI($this->di);

        $this->tags = new \Weleoka\Forumdb\Tag();
        $this->tags->setDI($this->di);
    }



/**
 * List question with id.
 *
 * @param int $id of user to display
 *
 * @return void
 */
	public function idAction($id = null)
	{
	//		$pageTimeGeneration = microtime(true);

	// Find the question by question ID and set windowbar title..
			$question = $this->questions->find($id);			
			$this->theme->setTitle("Fråga: " . $question->title);
			
	// Find the questionComments, then add question and questionComments to view..
		   $all = $this->commentQs->query()
            ->where('parentID = ?')
            ->execute([$question->id]);
			$comments = object_to_array($all);
			$this->views->add('comments/question', [
				'question' => $question,
				'comments' => $comments,
				'title' => 'Visar frågan: ',
			]);

	// Find Answers to Question found above, then add answers to view.
		   $all = $this->answers->query()
            ->where('parentID = ?')
            ->execute([$question->id]);
			$answers = object_to_array($all);			
         $this->views->add('comments/answers', [
            'answers' => $answers,
            'title'	  => '',
         ]);			

	// Check if there is a user logged on then generate Submit new Answer form.
			if (isset($_SESSION['user'])) {
				$form = $this->form;
				$form = $form->create([], [
					'content' => [
						'type'        => 'textarea',
						'label'       => '',
						'placeholder' => 'Skriv ett svar',
						'validation'  => ['not_empty'],
					],
					'submit' => [
						'type'      => 'submit',
						'class'		=> 'bigButton',
						'callback'  => function($form) use ($question){

             		$user = $this->forum->getUser();

						$this->answers->save([
								'userID'			=> $user->id,
								'parentID'  	=> $question->id,
								'parentTitle' 	=> $question->title,
                        'name'			=> $user->name,
                        'content'		=> $this->textFilter->doFilter($form->Value('content') , 'shortcode, markdown'),
                        'email'			=> $user->email,
                        'timestamp' 	=> getTime(),
						]);
	// For each answer added increase the answerCount of question.
						$parameters['answerCount'] = $question->answerCount + 1;			
						$this->questions->update($parameters);

						return true;
						}
					],
				]);

	// Check the status of the form ($question->tag is from the beginning of this function).
				$status = $form->check();

				if ($status === true) {
					$this->forum->AddFeedback('Ditt svar har sparats.');
         		$url = $this->url->create('forumdb/id/' . $question->id . '');
		header('Refresh: 3; URL='. $url);	
	//	$this->response->redirect($url);

				} else if ($status === false) {
					$this->forum->AddFeedback('Ditt svar kunde inte sparas.');
					$url = $this->url->create('forumdb/id/' . $question->id . '');
		header('Refresh: 3; URL='. $url);	
	//	$this->response->redirect($url);
				}

	//Here starts the rendering phase of actions if user login status true.
	      	$this->views->add('kmom03/page1', [
	    			'content' => $this->sidebarGen($question->tag),
       			],'sidebar');

				$this->views->add('comments/add', [
					'content' =>$form->getHTML(),
					'title' => 'Skriv ett svar',
				]);

	// If user login status false then this is what we do.
    		} else {
    			$url = $this->url->create('');

				$this->views->add('me/page', [
        			'content' => '<i class="fa fa-square-o"></i><a href="' . $url . '/users/login/' . $id . '"> Logga in</a> för att skriva inlägg',
    			]);
    		}

	// This is my silly litte lazy timer for this function. Start it at beginning of function.
   // 		if(isset($pageTimeGeneration)) {
  	//			echo "<p>Page generated in " . round(microtime(true)-$pageTimeGeneration, 5) . " seconds</p>";
  	//		}
	}



    /**
     * View all comments questions under specific tag.
     *
     * @return void
     */
	public function viewAction($tag = null)
	{
		  $this->theme->setTitle("Alla Frågor");

		  if (isset($tag)) {
    	  		$all = $this->questions->query()
            		->where('tag = ?')
            		->execute([$tag]);
            $category = $tag;
        } else {
				$all = $this->questions->query()
						->execute();
				$category = "Allt";
        }
    	  $array = object_to_array($all);

        $this->views->add('comments/questions', [
            'questions' => $array,
            'tag'      	=> $tag,
            'title'	  	=> 'Frågor i kategori: ' . $category . '.',
        ]);

        $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen($tag),
        ],'sidebar');
	}



/* ---------------------------- QUESTION ADD, (EDIT) (and DELETE) -----------------------------------*/

    /**
     * Add a question.
     *
     * @return void
     */
	public function addAction($tag = null)
	{
		if (!isset($tag)) {		
			$selectOptions = $this->tags->getTags();
		} else {
			$selectOptions = array($tag => $tag);		
		};
      $form = $this->form;
				$form = $form->create([], [
					'title' => [
						'type'        => 'text',
						'label'       => '',
						'placeholder' => 'Frågans titel.',
						'validation'  => ['not_empty'],
					],
					'content' => [
						'type'        => 'textarea',
						'label'       => '',
						'placeholder' => 'Kommentar eller fråga',
						'validation'  => ['not_empty'],
					],
				  'tag' => [
    					'type' 		=> 'select',
    					'class'		=> isset( $tag ) ? 'hidden' : '',
    					'label' 		=> isset( $tag ) ? '' : 'välj kategori: ',
    					'options' 	=> $selectOptions,

  					],
					'skicka' => [
						'type'      => 'submit',
						'class'		=> 'bigButton',
						'callback'  => function($form) use ($tag){
						
						$user = $this->forum->getUser();

						$this->questions->save([
								'title'		=> $this->textFilter->doFilter($form->Value('title') , 'shortcode, markdown'),
								'userID'		=> $user->id,
                        'name'		=> $user->name,
                        'content'	=> $this->textFilter->doFilter($form->Value('content') , 'shortcode, markdown'),
                        'email'		=> $user->email,
                        'timestamp' => getTime(),
                        'tag'			=> $form->Value('tag'),
                        'answerCount'	=> 0,
						]);
						return true;
					}
				],
			]);

			// Check the status of the form
			$status = $form->check();

			if ($status === true) {
         // What to do if the form was submitted?
				$this->forum->AddFeedback('Frågan har sparats.');
         	$url = $this->url->create('forumdb/view/' . $tag . '');
		header('Refresh: 3; URL='. $url);	
	//	$this->response->redirect($url);

			} else if ($status === false) {
      	// What to do when form could not be processed?
				$this->forum->AddFeedback('Frågan kunde inte sparas.');
				$url = $this->url->create('forumdb/add/' . $tag . '');
		header('Refresh: 3; URL='. $url);	
	//	$this->response->redirect($url);
			}

			//Here starts the rendering phase of the add action
			$this->theme->setTitle("Skriv nytt inlägg");

	      $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen( is_string($tag) ? $tag : null ),
       		],'sidebar');
       		
			$this->views->add('me/page', [
				'content' => 'asdfasfasdfasfd',
			],'featured-1');
			
			$this->views->add('comments/add', [
				'content' => $form->getHTML(),
				'title' => 'Skapa en nytt inlägg.',
			],'main');
	}



/* ---------------------------- SIDEBAR FOR FORUM -----------------------------------*/

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
	  $categories = $this->tags->getTags();
     $sidebar = '<p><i class="fa fa-plus">    </i> <a href="' . $url . '/forumdb/add/' . $tag . '"> Ny fråga</a></p>
					  <p>Forum kategorier:</p>
					  <p><i class="fa fa-list-ol"></i><a href="' . $url . '/forumdb/view"> Alla</a></p>';
					  foreach ( $categories as $category ) {
                 $sidebar .= '<p><i class="fa fa-anchor"></i><a href="' . $url . '/forumdb/view/' . $category . '"> ' . $category . '</a></p>';
                 };
     $user = new \Weleoka\Users\User();
     if ($user->isAdmin()) {
     		$sidebar .= '--- admin ---';
     		$sidebar .= '<p><i class="fa fa-plus"></i><a href="' . $url . '/forumdb/addtag"> Lägg till tag</a></p>';
     		$sidebar .= '<p><i class="fa fa-refresh"></i><a href="' . $url . '/setupComments"> Nolställ DB</a></p>';
	  }
	  $this->createMenu();
	  return $sidebar;
	}



/**
 * Generate navigation menu choices from forum categories.
 *
 * @param
 *
 * @return array menu
 */
	public function createMenu()   
   {
	   $categories = $this->tags->getTags();
   
	   $i = 1;
		foreach ($categories as $category) {
		$out['item ' . $i]  ['text'] 	= $category;
		$out['item ' . $i]  ['url']	= 'forumdb/view/' . $category;
		$out['item ' . $i]  ['title']	= $category;
		$i++;
		}	  
//	  	dump ($out);
		return $out;	   
   }
   /*
   			$_SESSION['timeout']['startPoint'] = time();
			$_SESSION['timeout']['TTL'] = 600;	
     // This is a menu item of the submenu
                    'item 1'  => [
                        'text'  => 'Kategorier.',   
                        'url'   => 'forumdb/viewtags',  
                        'title' => 'Forumkategorier.'
                    ],
*/
/* ------------------------------ COMMENT HANDLING -----------------------------------*/

/*
 * Insert comment on Question.
 *
 * @return array
 */
	public function commentqAction ($id)
	{
		$comment = $_POST['comment'];
		$user = $this->forum->getUser();

		$this->commentQs->save([
				'parentID'  => $id,
				'userID'		=> $user->id,
            'name'		=> $user->name,
            'content'	=> $comment,
            'timestamp' => getTime(),
		]);

		$this->forum->AddFeedback('Din kommentar sparades.');
		$url = $this->url->create('forumdb/id/' . $id . '');
		header('Refresh: 2; URL='. $url);	
	//	$this->response->redirect($url);
	}

/*
 * Insert comment on Answer.
 *
 * @return array
 */
	public function commentaAction ($id)
	{
		$comment = $_POST['comment'];
		$user = $this->forum->getUser();

		$this->commentAs->save([
				'parentID'  => $id,
				'userID'		=> $user->id,
            'name'		=> $user->name,
            'content'	=> $comment,
            'timestamp' => getTime(),
		]);

		$this->forum->AddFeedback('Din kommentar sparades.');
		$url = $this->url->create('forumdb/id/' . $id . '');
		header('Refresh: 2; URL='. $url);
		// $this->response->redirect($url);
	}



/* ------------------------------ TAG HANDLING -------------------------------------*/

/*
 * Insert tag into table, admin action.
 *
 * @return array
 */
	public function addtagAction() {
		$form = $this->form;
				$form = $form->create([], [
					'tag' => [
						'type'        => 'text',
						'placeholder' => 'Ny tag',
						'validation'  => ['not_empty'],
					],
					'submit' => [
						'type'      => 'submit',
						'class'		=> 'bigButton',
						'callback'  => function($form) {

						$this->tags->save([
                        'tag'   => $form->Value('tag'),
						]);

						return true;
					}
				],
			]);

			// Check the status of the form
			$status = $form->check();

			if ($status === true) {
         // What to do if the form was submitted?
				$this->forum->AddFeedback('Den nya taggen är nu sparad.');
         	$url = $this->url->create('forumdb/addtag');
			   $this->response->redirect($url);

			} else if ($status === false) {
      	// What to do when form could not be processed?
				$this->forum->AddFeedback('Den nya taggen kunde inte skapas.');
				$url = $this->url->create('forumdb/addtag');
			   $this->response->redirect($url);
			}

			//Here starts the rendering phase of the add action
			$this->theme->setTitle("Lägg till tag");

			$tagsHTML = $this->tags->listTags();

	      $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen(),
       		],'sidebar');

			$this->views->add('comments/add', [
				'content' => $tagsHTML . $form->getHTML(),
				'title' => '<h2>Skapa en ny tag.</h2>',
			]);
	}



	 /**
     * View all the different tags.
     *
     * @return void
     */
	public function viewtagsAction()
	{
		  $tagsHTML = $this->tags->listTags();

		  $this->views->add('kmom03/page1', [
	    		'content' => $tagsHTML,
       		],'main');

        $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen(),
        ],'sidebar');
	}





/* ------------------------------ RETIRED FUNCTIONS-------------------------------------*/

    /**
     * Remove one specific entry (based on $id).
     *
     * @return void
     */
/*	public function deleteAction($id)
	{
		if (!isset($id)) {
        die("Missing id");
    	}
 	   $one = $this->forum->find($id);
 	   $tab = $one->tab;

    	$res = $this->forum->delete($id);

 	 	$this->forum->AddFeedback("Posten är nu permanent borttagen.");

	  	$url = $this->url->create('forumdb/view/' . $tab . '');
	   $this->response->redirect($url);
	}
*/




    /**
     * Edit a question.
     *
     * @param id of question to edit.
     *
     * @return void
     */
/*	public function editAction($id)
	{
      $form = $this->form;

			$comment = $this->questions->find($id);
			$tag = $comment->tag;

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

						$this->comments->save([
								'content'	=> $form->Value('content'),
                        'timestamp' => getTime(),
						]);

						return true;
					}
				],

			]);

			// Check the status of the form
			$status = $form->check();

			if ($status === true) {
         // What to do if the form was submitted?
				$this->forum->AddFeedback("Ändringar sparades.");
         	$url = $this->url->create('forumdb/view/' . $tag . '');
			   $this->response->redirect($url);

			} else if ($status === false) {
      	// What to do when form could not be processed?
				$this->forum->AddFeedback("Ändringarna kunde inte sparas till databasen.");
				$url = $this->url->create('forumdb/edit/' . $tag . '');
			   $this->response->redirect($url);
			}
			//Here starts the rendering phase of the add action
			$this->theme->setTitle("Lägg till kommentar");

	      $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen($tag),
       		],'sidebar');

			$this->views->add('comments/edit', [
				'content' =>$form->getHTML(),
				'title' => 'Redigera kommentar',
			]);
	}

*/
}