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

        $this->tags = new \Weleoka\Forumdb\Tag();
        $this->tags->setDI($this->di);
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
				'title' => 'Visar frågan: ',
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

			$this->views->add('comments/add', [
				'content' =>$form->getHTML(),
				'title' => 'Skapa en ny kommentar',
			]);
    		} else {
    			$url = $this->url->create('');

				$this->views->add('me/page', [
        			'content' => '<i class="fa fa-square-o"></i><a href="' . $url . '/users/login/' . $id . '"> Logga in</a> för att skriva inlägg',
    			]);
    		}
	}


    /**
     * View all comments.
     *
     * @return void
     */
	public function viewAction($tag = null, $redirect = null)
	{
    	  $all = $this->questions->query()
            ->where('tag = "' . $tag . '"')
            ->execute();
    	  $array = object_to_array($all);
		  $this->theme->setTitle("Alla Frågor");
        $this->views->add('comments/comments', [
            'comments' => $array,
            'tag'      => $tag,
            'redirect' => $redirect,
            'title'	  => 'Alla frågor under taggen ' . $tag . '.',
        ]);

        $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen($tag),
        ],'sidebar');
	}



    /**
     * Add a question.
     *
     * @return void
     */
	public function addAction($tag = null, $question = null)
	{
		$tags = $this->tags->getTags();
       $form = $this->form;
				$form = $form->create([], [
					'title' => [
						'type'        => 'text',
						'label'       => 'Fråga',
						'required'    => true,
						'placeholder' => 'Frågans titel.',
						'validation'  => ['not_empty'],
					],
					'content' => [
						'type'        => 'textarea',
						'label'       => 'Kommentar',
						'required'    => true,
						'placeholder' => 'Kommentar',
						'validation'  => ['not_empty'],
					],
				  'tag' => [
    					'type' => 'select',
    					'label' => 'Forum tags:',
    					'options' => $tags,
  					],
					'submit' => [
						'type'      => 'submit',
						'class'		=> 'bigButton',
						'callback'  => function($form) use ($tag){
						$now = date_create()->format('Y-m-d H:i:s'); // returns local time
						$user = $this->forum->getUser();
					//	$now = gmdate('Y-m-d H:i:s'); // returns UTC

						$this->questions->save([
								'title'		=> $form->Value('title'),
								'userID'		=> $_SESSION['user']['id'],
                        'name'		=> $_SESSION['user']['name'],
                        'content'	=> $form->Value('content'),
                        'email'		=> $_SESSION['user']['email'],
                        'timestamp' => $now,
                        'tag'			=> $form->Value('tag'),
						]);
						return true;
					}
				],
			]);

			// Check the status of the form
			$status = $form->check();

			if ($status === true) {
         // What to do if the form was submitted?
				$this->forum->AddFeedback('Kommentaren har sparats.');
         	$url = $this->url->create('' . $tab . '');
			   $this->response->redirect($url);

			} else if ($status === false) {
      	// What to do when form could not be processed?
				$this->forum->AddFeedback('Kommentaren kunde inte sparas.');
				$url = $this->url->create('forumdb/add/' . $tab . '');
			   $this->response->redirect($url);
			}

			//Here starts the rendering phase of the add action
			$this->theme->setTitle("Lägg till kommentar");

	      $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen($tag),
       		],'sidebar');

			$this->views->add('comments/add', [
				'content' =>$form->getHTML(),
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
				$this->forum->AddFeedback("Kommentarens ändringar sparades.");
         	$url = $this->url->create('forumdb/view/' . $tab . '');
			   $this->response->redirect($url);

			} else if ($status === false) {
      	// What to do when form could not be processed?
				$this->forum->AddFeedback("Kommentaren kunde inte sparas till databasen.");
				$url = $this->url->create('forumdb/edit/' . $tab . '');
			   $this->response->redirect($url);
			}
			//Here starts the rendering phase of the add action
			$this->theme->setTitle("Lägg till kommentar");

	      $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen($tab),
       		],'sidebar');

			$this->views->add('comments/edit', [
				'content' =>$form->getHTML(),
				'title' => 'Redigera kommentar',
			]);
	}



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
 	 	// $comment = $this->comments->find($id);
 	   $one = $this->forum->find($id);
 	   $tab = $one->tab;

    	$res = $this->forum->delete($id);

 	 	$feedback = "Posten är nu permanent borttagen.";

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
	public function sidebarGen($tag = null)
	{
	  $user = new \Weleoka\Users\User();
	  $url = $this->url->create('');
     $sidebar = '<p><i class="fa fa-plus">    </i> <a href="' . $url . '/forumdb/add/' . $tag . '"> Ny kommentar</a></p>
                 <p><i class="fa fa-list-ol"></i><a href="' . $url . '/forumdb/view/' . $tag . '"> Alla</a></p>';
     if ($user->isAdmin()) {
     		$sidebar .= '--- admin ---';
     		$sidebar .= '<p><i class="fa fa-refresh"></i><a href="' . $url . '/forumdb/addtag"> Lägg till tag</a></p>';
     		$sidebar .= '<p><i class="fa fa-refresh"></i><a href="' . $url . '/setupComments"> Nolställ DB</a></p>';
	  }
	  return $sidebar;
	}



/*
 * Insert tag into table.
 *
 * @return array
 */
	public function addtagAction() {
		$form = $this->form;
				$form = $form->create([], [
					'tag' => [
						'type'        => 'text',
						'label'       => 'Ny tag: ',
						'required'    => true,
						'placeholder' => 'Ny tag',
						'validation'  => ['not_empty'],
					],
					'submit' => [
						'type'      => 'submit',
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
	    		'content' => $tagsHTML,
       		],'featured-1');

	      $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen(),
       		],'sidebar');

			$this->views->add('comments/add', [
				'content' =>$form->getHTML(),
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

}