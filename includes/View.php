<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Source.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');

/**
 * view.php a contains common CVH View elements
 */
  class View {
     
    private $title;
    private $ajax;
    private $NSFW;
    private $unvalidated;

    /**
     * Constructor sets the title.
     *
     * @param string $title
     */
    function View($title = NULL) {
        $this->title = $title;
        
        /* AJAX check  */
        $this->ajax = ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? TRUE : FALSE;
        
        /* get Cookie settings */
        $NSFWCookie        = filter_input(INPUT_COOKIE, 'NSFW');
        $unvalidatedCookie = filter_input(INPUT_COOKIE, 'Unvalidated');
        
        $this->unvalidated = ($unvalidatedCookie) ? TRUE : FALSE;               
        $this->NSFW        = ($NSFWCookie)        ? TRUE : FALSE;
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
        $headerTitle = '';
        
        if (isset($this->title)) {
            $headerTitle = ' - ' . $this->title;
        }
        
        $header  = '<header>' . PHP_EOL;
        $header .= ($this->NSFW) ? "<hgroup>"  . PHP_EOL : '';
        $header .= "<h1><a href='/CVH'>Cards vs Humans</a>$headerTitle</h1>" . PHP_EOL;
        $header .= ($this->NSFW) ? "<h4 class='NSFW'>NSFW</h2></hgroup>"  . PHP_EOL : '';
        $header .= self::displayNav() . PHP_EOL;
        $header .= '</header>' . PHP_EOL;
        
        return $header;
    }
    
    public function displayNav() {
        $nav .= "<nav>" . PHP_EOL;
        $nav .= "<h2>Site Navigation</h2>" . PHP_EOL;
        $nav .= "<ul>" . PHP_EOL;
        $nav .= "<li><a href='/CVH/settings/view.php'>Settings</a>" . PHP_EOL;
        $nav .= "<li>View" . PHP_EOL;
        $nav .= "<ul>" . PHP_EOL;
        $nav .= "<li><a href='/CVH/view/card/answer/all'>Answers</a>" . PHP_EOL;
        $nav .= "<li><a href='/CVH/view/card/question/all'>Questions</a>" . PHP_EOL;
        $nav .= "<li><a href='/CVH/view/source/all'>Sources</a>" . PHP_EOL;
        $nav .= "</ul>" . PHP_EOL;
        $nav .= "</li>" . PHP_EOL;
        $nav .= "<li><a href='/CVH/suggest'>New</a>" . PHP_EOL;
        $nav .= "</ul>" . PHP_EOL;
        $nav .= "</nav>" . PHP_EOL;
        
        return $nav;   
    }
    
    /**
     * Returns an appropriately formated html header
     * 
     * @return string
     */
    public function displayHead() {
        /* if we are an ajax request we want to abort, and not set a header */
        if ($this->ajax) { die(); }
        
        $headTitle = '';
        
        if (isset($this->title)) {
            $headTitle = ' - ' . $this->title;
        }
        
        $head .= '<!DOCTYPE html>' . PHP_EOL;
        $head .= '<meta charset="utf-8" />' . PHP_EOL;
        $head .= '<title>Cards vs Humans' . $headTitle . '</title>' . PHP_EOL;
        $head .= '<link rel="stylesheet" href="/CVH/cvh.css" />' . PHP_EOL;
        
        return $head;
    }
    
    /**
     * Returns an appropriatey formated footer for the page.
     *
     * @return string
     */
    public function displayFooter() {
        $footer  = "<footer>";
        $footer .= "Madeby: <a rel='Author' href='mailto:maxtmahem@gmail.com'>Austin Stanley</a> - ";
        $footer .= "Last Modified:" . ' ' . date("F d, Y H:i", getlastmod());
        $footer .= "</footer>" . PHP_EOL;
        
        return $footer;
    }
    

 }