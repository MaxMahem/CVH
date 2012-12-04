<?php
/**
 * view.php a contains common CVH View elements
 */
 
 class View {
     
    private $title;
    
    /**
     * Constructor sets the title.
     *
     * @param string $title
     */
    function View($title = NULL) {
        $this->title = $title;
    }
    
    /**
     * Returns an appropriately formated header for the page.
     *
     * @return string
     */
    Function displayHeader() {
        $headerTitle = '';
        
        if (isset($this->title)) {
            $headerTitle = ' - ' . $this->title;
        }
        
        $header  = '<header id="header">';
        $header .= '<h1><a href="/CVH">Cards vs Humans</a>' . $headerTitle . '</h1>';
        $header .= '</header>' . PHP_EOL;
        
        return $header;
    }
    
    /**
     * Returns an appropriately formated html header
     * 
     * @return string
     */
     Function displayHead() {
        $headTitle = '';
        
        if (isset($this->title)) {
            $headTitle = ' - ' . $this->title;
        }
        
        $head  = '<head>';
        $head .= '<meta charset="utf-8" />';
        $head .= '<title>Cards vs Humans' . $headTitle . '</title>';
        $head .= '<link rel="stylesheet" type="text/css" href="/CVH/cvh.css" />';
        $head .= '</head>' . PHP_EOL;
        
        return $head;
    }
    
    /**
     * Returns an appropriatey formated footer for the page.
     *
     * @return string
     */
    Function displayFooter() {
        $footer  = '<footer id="footer">';
        $footer .= 'Madeby: <a href="mailto:maxtmahem@gmail.com">Austin Stanley</a>';
        $footer .= '</footer>' . PHP_EOL;
        
        return $footer;
    }
 }