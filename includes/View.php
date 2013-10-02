<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Source.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/NavMenu.php');

/**
 * view.php a contains common CVH View elements
 */
  class View {
     
    private $title;
    private $ajax;
    private $NSFW;
    private $unvalidated;
    
    private $navMenu;

    /**
     * Constructor sets the title.
     *
     * @param string $title
     */
    function View($title = NULL) {
        $this->title = $title;
        
        /* AJAX check  */
        $this->ajax = ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? TRUE : FALSE;
        
        /* get session settings */
        session_start();
        $this->unvalidated = (isset($_SESSION['unvalidated'])) ? $_SESSION['unvalidated'] : FALSE;
        $this->NSFW        = (isset($_SESSION['NSFW']))        ? $_SESSION['NSFW']        : FALSE;
        
        /* set navMenu */
        $this->navMenu = new NavMenu(array(
            'Settings' => '/CVH/settings/view.php',
            'View' => array(
                'Answers'   => '/CVH/view/card/answer/all',
                'Questions' => '/CVH/view/card/questions/all',
                'Sources'   => '/CVH/view/sources/all',
            ),
            'Votes' => array(
                'Recent'    => '/CVH/view/vote/recent',
                'Top'       => '/CVH/view/vote/top'
            ),
            'New' => '/CVH/new',
        ));
    }
    
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw LogicException("Attempted to get View property $property which does not exist.");
        }
    }
    
    /**
     * Returns an appropriately formated header for the page.
     *
     * @return string
     */
    public function displayHeader() {
        /* if we are an ajax request we want to abort, and not set a header */
        if ($this->ajax) { return; }
        
        /* if we have a title, we want to add that on to our header */
        if (isset($this->title)) {
            $headerTitle = ' - ' . $this->title;
        } else {
            $headerTitle = '';
        }
        
        $headerArray[] = '<header>';
        $headerArray[] = ($this->NSFW) ? "<hgroup>" : '';
        $headerArray[] = "<h1><a href='/CVH'>Cards vs Humans</a>$headerTitle</h1>";
        $headerArray[] = ($this->NSFW) ? "<h4 class='NSFW'>NSFW</h2></hgroup>" : '';
        $headerArray[] = $this->navMenu->display();
        $headerArray[] = '</header>';
        $header = implode(PHP_EOL, $headerArray);
        
        return $header;
    }
    
    /**
     * Returns an appropriately formated html header
     * 
     * @return string
     */
    public function displayHead() {
        /* if we are an ajax request we want to abort, and not set a header */
        if ($this->ajax) { return; }
        
        $headTitle = '';
        
        if (isset($this->title)) {
            $headTitle = ' - ' . $this->title;
        }
        
        $head .= '<!DOCTYPE html>' . PHP_EOL;
        $head .= '<meta charset="utf-8" />' . PHP_EOL;
        $head .= '<title>Cards vs Humans' . $headTitle . '</title>' . PHP_EOL;
        $head .= '<link rel="stylesheet" href="/CVH/cvh.css" />' . PHP_EOL;
        $head .= '<script src="//code.jquery.com/jquery-latest.js"></script>' . PHP_EOL;
        $head .= '<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.js"></script>' . PHP_EOL;
        $head .= '<script src="/CVH/cvh.js"></script>' . PHP_EOL;
        
        return $head;
    }
    
    /**
     * Returns an appropriatey formated footer for the page.
     *
     * @return string
     */
    public function displayFooter() {
        /* if we are an ajax request we want to abort, and not set a header */
        if ($this->ajax) { return; }
        
        $footer  = "<footer>";
        $footer .= "Madeby: <a rel='Author' href='mailto:maxtmahem@gmail.com'>Austin Stanley</a> - ";
        $footer .= "Last Modified:" . ' ' . date("F d, Y H:i", getlastmod());
        $footer .= "</footer>" . PHP_EOL;
        
        return $footer;
    }
 }
