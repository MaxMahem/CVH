<?php
/**
 * view.php a contains common CVH View elements
 */
 
 class View {
     
    private $title;
    private $ajax;
    
    /**
     * Constructor sets the title.
     *
     * @param string $title
     */
    function View($title = NULL) {
        $this->title = $title;
        
        /* AJAX check  */
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->ajax = TRUE;
        } else {
            $this->ajax = FALSE;
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
        
        $header  = '<header>';
        $header .= "<h1><a href='/CVH'>Cards vs Humans</a>$headerTitle</h1>";
        $header .= self::displayNav();
        $header .= '</header>' . PHP_EOL;
        
        return $header;
    }
    
    public function displayNav() {
        $nav .= "<nav>";
        $nav .= "<ul>";
        $nav .= "   <li><a href='/CVH/settings/view.php'>Settings</a>";
        $nav .= "   <li>View";
        $nav .= "       <ul>";
        $nav .= "           <li><a href='/CVH/view/answer/all'>Answers</a>";
        $nav .= "           <li><a href='/CVH/view/question/all'>Questions</a>";
        $nav .= "           <li><a href='/CVH/view/source/all'>Sources</a>";
        $nav .= "       </ul>";
        $nav .= "   </li>";
        $nav .= "   <li><a href='/CVH/suggest'>New</a>";
        $nav .= "</ul>";
        $nav .= "</nav>";
        
        return $nav;   
    }
    
    /**
     * Returns an appropriately formated html header
     * 
     * @return string
     */
     Function displayHead() {
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
    Function displayFooter() {
        $footer  = '<footer id="footer">';
        $footer .= 'Madeby: <a href="mailto:maxtmahem@gmail.com">Austin Stanley</a> - Last Modified:' . ' ' . date("F d, Y H:i", getlastmod());
        $footer .= '</footer>' . PHP_EOL;
        
        return $footer;
    }
 }