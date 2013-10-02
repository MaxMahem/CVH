<?php

/**
 * A class for building HTML5 nav menus.
 *
 * @author MaxMahem
 */

class NavMenu {
    
    private $menuArray;
    
    const UL = 'ul';
    const OL = 'ol';
    
    /** NavMenu($menuArray)
     * Constructor needs menu to build array
     * 
     * @param array $menuArray a nested array for building the menu.
     */
    function NavMenu($menuArray) {
        /* validate input */
        if (!is_array($menuArray)) {
            throw new InvalidArgumentException("Invalid argument thrown to new NavMenu(). $menuArray given, array expected");
        }
        
        $this->menuArray = $menuArray;
    }
    
    /** display()
     * Returns an appropriately formated nav bar
     * 
     * @param  string $type        type of menu tree, NavMenu::UL or NavMenu:OL
     * @param  string $currentPage current page in Menu.
     * @return string the navbar.
     */
    public function display($type = self::UL, $currentPage = NULL) {
        /* validate input */
        if (($type != self::UL) && ($type != self::OL)) {
            throw new InvalidArgumentException("Invalid type: $type passed to new NavMenu->display, NavMenu::UL or NavMenu::OL expected.");
        }
        
        $navArray[] = "<nav>";
        $navArray[] = "<h2>Site Navigation</h2>";
        $navArray[] = self::makeMenuTree($this->menuArray, $type, $currentPage);
        $navArray[] = "</nav>";
        
        $nav = implode(PHP_EOL, $navArray);
        
        return $nav;
    }
    
    /** makeMenuTree($menuArray)
     * Generates a ul tree from an array.
     * 
     * @param  array  $menuArray  nested array to be turned into a tree
     * @param  string $type       type of menu tree, NavMenu::UL or NavMenu:OL
     * @param  string currentPage current page in Menu.
     * @return string             markedup tree.
     */
    private static function makeMenuTree($menuArray, $type = self::UL, $currentPage = NULL) {
        /* validate input */
        if (!is_array($menuArray)) {
            throw new InvalidArgumentException("Invalid menuArray: $menuArray passed to NavMenu::makeMenuTree, array expected.");
        }
        if (($type !== self::UL) && ($type !== self::OL)) {
            throw new InvalidArgumentException("Invalid type: $type passed to new NavMenu::makeMenuTree, NavMenu::UL or NavMenu::OL expected.");
        }
        
        /* open list */
        $outputArray[] = "<$type>";
        
        foreach ($menuArray as $label => $data) {
            /* chec for array */
            if (is_array($data)) {
                /* if we get an array, print the label and recurse */
                $outputArray[] = "<li>$label";
                $outputArray[] = self::makeMenuTree($data, $type);
                $outputArray[] = "</li>";
            } else {
                /* not nested, check if current page and print label */
                $li = ($label === $currentPage) ? "<li class='current'>" : "<li>";
                $outputArray[] = "$li<a href=$data>$label</a></li>";
            }
        }
        
        /* close list */
        $outputArray[] = "</$type>";
        
        $output = implode(PHP_EOL, $outputArray);
        return $output;
    }    
}

?>