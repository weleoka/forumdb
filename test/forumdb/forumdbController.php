<?php

namespace Weleoka\Forumdb;

/**
 * A testclass for Forumdb Not implemented yet!
 *
 */
class forumdbTest extends \PHPUnit_Framework_TestCase {

    /**
     * Construct the Test dependency object.
     *
     * @param
     *
     */
    public function __construct() {
        $this->el = new \Weleoka\Forumdb\Forum();
    }


    /**
     * Test the Forumdb Constructor.
     *
     * @expectedException Exception
     *
     * @return void
     *
     */
    public function testExample()
    {
        echo "\n testExample:\n";
        $element = new \Weleoka\Forumdb\Forum();
    }
}








