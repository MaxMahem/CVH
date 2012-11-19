<?php

/** displayCard
 * 
 * Returns a properly formated card for display.
 * @todo Enlarge this to a complete class.
 * 
 * @param   array   $card       an array containing the necessary details of the
 *  card. We need id, question/answer, and url.
 * @param   string  $type   	defines the type, either 'question' or 'answer'
 * @param   string  $voteURL    the first part of the URL that should be
 *  used for voting, if desired. The id of the card will be appended to it.
 * @return  string  HTML code for the card.
 * 
 */

function displayCard($card, $type, $voteURL = NULL) {    
    $result .= "<div class='card $type' id='" . $card['id'] . "'>";
    
    /** 
     * @todo Possibly add code for handling voting on questions?
     */
    if ($voteURL != NULL) {
        /* if we recieved a vote URL, embeded the card text inside a voting link */
        $result .= "<a class='answerlink' href='" . $voteURL . strtoupper(dechex($card['id'])) . "'>";
        $result .= $card[$type];
        $result .= "</a>";
    } else {
        /* if we didn't recieve a vote URL, just spit out the card text. */
        $result .= $card[$type];
    }
    
    $result .= '<a href="' . $card['url'] . '" class="source">';

    switch ($card['source']) {
        case 'Cards Against Humanity':
            $result .=  '<img src="/CVH/CAH-Cards-White.svg" alt="Cards Against Humanity" />'; 
            break;
        case 'Cards vs Humans':
            $result .= '<img src="/CVH/CVH_Logo.svg" alt="Cards vs Humans" />';
            break;
    }
    $result .= $card['source'] . '</a>';
    $result .= '</div>' . PHP_EOL;
    
    return $result;
}

?>
