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
 * Generate navigation menu choices from forum categories(tags).
 *
 * @param
 *
 * @return array menu
 */
	public function createMenu()
   {
		$this->forum = new \Weleoka\Forumdb\Forum();
      $this->forum->setDI($this->di);
        	   
	   $categories = $this->forum->forumTags();

	   $i = 1;
		foreach ($categories as $category) {
   			$out['item ' . $i++] = [
      						'text'  => $category->tag,
      						'url'   => 'forumdb/view/' . $category->tag,
      						'title' => $category->tag,
   			];
		}	
		return $out;
   }



/**
 * Get popular forum categories and generate view, public action.
 *
 * @param int $id of user to display
 *
 * @return void
 */
	public function popularAction()
	{
      	$this->views->add('kmom03/page1', [
	    			'content' => $this->tags->topTags(),
      	],'featured-1');

      	$this->views->add('kmom03/page1', [
	    			'content' => $this->forum->topUsers(),
      	],'featured-2');
      	
      	$this->views->add('kmom03/page1', [
	    			'content' => '
								<figure class="right">
    									<br><img class="featured" src="img/cutter_small.png">
								<figcaption></figcaption>
								</figure>' . $this->questions->newPosts(),
      	],'featured-3');
	}



/**
 * List question with id, public and user action.
 *
 * @param int $id of user to display
 *
 * @return void
 */
	public function idAction($id = null)
	{
	// Find the question by question ID and set windowbar title..
			$question = $this->questions->find($id);
			$taglog = $this->tags->findTag($question->tag);
			dump ($taglog->questionCount + 1);
			$this->theme->setTitle("Fråga: " . $question->title);
	// Find the questionComments, then add question and questionComments to view..
		   $all = $this->commentQs->query()
            ->where('parentID = ?')
            ->execute([$question->id]);
			$comments = object_to_array($all);
			$url = $this->url->create('forumdb/view/' . $question->tag . '');
			$this->views->add('forumView/question', [
				'question' => $question,
				'comments' => $comments,
				'title' => $question->title . ' från kategorin: <a href="' . $url . '">' . $question->tag . '</a>',
			]);

	// Find Answers to Question found above, then add answers to view.
		   $all = $this->answers->query()
            ->where('parentID = ?')
            ->execute([$question->id]);
			$answers = object_to_array($all);
         $this->views->add('forumView/answers', [
            'answers' => $answers,
            'title'	  => '',
         ]);

	// Check if there is a user logged on then generate Submit new Answer form.
			if ($this->forum->userIsAuthenticated()) {
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
   // This logs the contribution and resets session TTL.
						$this->forum->userContributionLog($user);
						$this->forum->resetTTL();

						$this->answers->save([
								'userID'			=> $user->id,
								'parentID'  	=> $question->id,
								'parentTitle' 	=> $question->title,
                        'name'			=> $user->name,
                        'content'		=> $this->textFilter->doFilter($form->Value('content') , 'markdown'),
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
	// Update the total number of posts under forum tag.
					$this->forum->resetTTL();				
					$taglog = $this->tags->findTag($question->tag);
					$parameters['postCount'] = $taglog->postCount + 1;
					$this->tags->update($parameters);					
	// Give feedback and redirect browser.				
					$this->forum->AddFeedback('Ditt svar har sparats.');
         		$url = $this->url->create('forumdb/id/' . $question->id . '');
					//	header('Refresh: 3; URL='. $url);
					$this->response->redirect($url);

				} else if ($status === false) {
					$this->forum->AddFeedback('Ditt svar kunde inte sparas.');
					$url = $this->url->create('forumdb/id/' . $question->id . '');
					//	header('Refresh: 3; URL='. $url);
					$this->response->redirect($url);
				}

	//Here starts the rendering phase of actions if user login status true.
	      	$this->views->add('kmom03/page1', [
	    			'content' => $this->forum->sidebarGen($question->tag),
       			],'sidebar');

				$this->views->add('forumView/add', [
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
	}



    /**
     * View all comments questions under specific tag, public action.
     *
     * @return void
     */
	public function viewAction($tag = null)
	{	  		  
		  if (isset($tag)) {
		  		$tag = urldecode($tag);
		  	   $this->theme->setTitle("Kategori: " . $tag);
				$all = $this->questions->getbyTag($tag);
            $category = $tag;
    	  		$this->views->add('forumView/add', [        
        				'content' => null,
        				'tag'		=> $tag,
    	  ]);            
            
        } else {
        		$this->theme->setTitle("Alla Frågor");
				$all = $this->questions->query()
						->execute();
				$category = "Allt";
				$this->views->add('forumView/add', [        
        				'content' => null,
        				'tag'		=> null,
    	  ]);     
        }
    	  $array = object_to_array($all);

        $this->views->add('forumView/questions', [
            'questions' => $array,
            'tag'      	=> $tag,
            'title'	  	=> 'Frågor i kategori: ' . $category . '.',
        ], 'main');

        $this->views->add('kmom03/page1', [
	    		'content' => $this->forum->sidebarGen($tag),
        ],'sidebar');
	}



/* ---------------------------- QUESTION ADD, (EDIT) (and DELETE) -----------------------------------*/

    /**
     * Add a question, user action.
     *
     * @return void
     */
	public function addAction($tag = null)
	{
				
		if (!isset($tag)) {
			$selectOptions = $this->tags->getTags();
		} else {
			$tag = urldecode($tag);
			$selectOptions = array($tag => $tag);
		}
		if ($this->forum->userIsAuthenticated()) {
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
    					'class'		=> isset( $tag ) ? 'hidden' 	: '',
    					'label' 		=> isset( $tag ) ? '' 			: 'välj kategori: ',
    					'options' 	=> $selectOptions,

  					],
					'skicka' => [
						'type'      => 'submit',
						'class'		=> 'bigButton',
						'callback'  => function($form) use ($tag){

   // This logs the contribution and resets session TTL.
   					$user = $this->forum->getUser();
						$this->forum->userContributionLog($user);
						$this->forum->resetTTL();

						$this->questions->save([
								'title'		=> $this->textFilter->doFilter($form->Value('title') , 'bbcode'),
								'userID'		=> $user->id,
                        'name'		=> $user->name,
                        'content'	=> $this->textFilter->doFilter($form->Value('content') , 'markdown'),
                        'email'		=> $user->email,
                        'timestamp' => getTime(),
                        'tag'			=> $form->Value('tag'),
                        'answerCount'	=> 0,
						]);
						// log the question to tag table in questionCount
						$taglog = $this->tags->findTag($form->Value('tag'));
						$parameters['postCount'] = $taglog->postCount + 1;
						$this->tags->update($parameters);
						return true;
					}
				],
			]);

			// Check the status of the form
			$status = $form->check();

			if ($status === true) {
         // What to do if the form was submitted?
         	$this->forum->resetTTL();
				$this->forum->AddFeedback('Frågan har sparats.');
         	$url = $this->url->create('forumdb/view/' . $tag . '');
				// header('Refresh: 3; URL='. $url);
				$this->response->redirect($url);

			} else if ($status === false) {
      	// What to do when form could not be processed?
      		$this->forum->resetTTL();
				$this->forum->AddFeedback('Frågan kunde inte sparas.');
				$url = $this->url->create('forumdb/add/' . $tag . '');
				// header('Refresh: 3; URL='. $url);
				$this->response->redirect($url);
			}

			//Here starts the rendering phase of the add action
			$this->theme->setTitle("Skriv nytt inlägg");

	      $this->views->add('kmom03/page1', [
	    		'content' => $this->forum->sidebarGen( is_string($tag) ? $tag : null ),
       		],'sidebar');

			$this->views->add('forumView/add', [
				'content' => $form->getHTML(),
				'title' => 'Skapa en nytt inlägg.',
			],'main');
		} else
			$this->forum->kickOutBaddie();
	}



/* ------------------------------ COMMENT HANDLING -----------------------------------*/

/*
 * Insert comment on Question, user action.
 *
 * @return array
 */
	public function commentqAction ($id)
	{
		if ($this->forum->userIsAuthenticated()) {
			$comment = $_POST['comment'];
			$user = $this->forum->getUser();

			$this->commentQs->save([
				'parentID'  => $id,
				'userID'		=> $user->id,
            'name'		=> $user->name,
            'content'	=> $comment,
            'timestamp' => getTime(),
			]);
			
			$this->forum->resetTTL();
			$this->forum->AddFeedback('Din kommentar sparades.');
			$url = $this->url->create('forumdb/id/' . $id . '');
			// header('Refresh: 2; URL='. $url);
			$this->response->redirect($url);
		} else {
			$this->forum->kickOutBaddie();
		}
	}



/*
 * Insert comment on Answer, user action.
 *
 * @return array
 */
	public function commentaAction ($id, $parent)
	{
		if ($this->forum->userIsAuthenticated()) {
			$comment = $_POST['comment'];
			$user = $this->forum->getUser();

			$this->commentAs->save([
				'parentID'  => $id,
				'userID'		=> $user->id,
            'name'		=> $user->name,
            'content'	=> $comment,
            'timestamp' => getTime(),
			]);
			
			$this->forum->resetTTL();
			$this->forum->AddFeedback('Din kommentar sparades.');
			$url = $this->url->create('forumdb/id/' . $parent . '');
			// header('Refresh: 2; URL='. $url);
			$this->response->redirect($url);
		} else {
			$this->forum->kickOutBaddie();
		}
	}



/* ------------------------------ TAG HANDLING -------------------------------------*/

/*
 * Insert tag into table, admin action.
 *
 * @return array
 */
	public function addtagAction() {
		if ($this->forum->userIsAdmin()) {
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
         	$this->forum->resetTTL();
				$this->forum->AddFeedback('Den nya taggen är nu sparad.');
         	$url = $this->url->create('forumdb/addtag');
			   $this->response->redirect($url);

			} else if ($status === false) {
      	// What to do when form could not be processed?
      		$this->forum->resetTTL();
				$this->forum->AddFeedback('Den nya taggen kunde inte skapas.');
				$url = $this->url->create('forumdb/addtag');
			   $this->response->redirect($url);
			}

			//Here starts the rendering phase of the add action
			$this->theme->setTitle("Lägg till tag");

			$tagsHTML = $this->tags->listTags();

	      $this->views->add('kmom03/page1', [
	    		'content' => $this->forum->sidebarGen(),
       		],'sidebar');

			$this->views->add('forumView/add', [
				'content' => $tagsHTML . $form->getHTML(),
				'title' => '<h2>Skapa en ny tag.</h2>',
			]);
		} else {
			$this->forum->kickOutBaddie();
		}
	}



	 /**
     * View all the different tags, public action.
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
	    		'content' => $this->forum->sidebarGen(),
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