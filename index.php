<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">  

<head>
    <meta charset="utf-8" />
    <title>Cards vs Humans</title>
    <link rel="stylesheet" type="text/css" href="cvh.css" />
</head>

<?php
    /* the db-connection file is assumed to define DBHOST, DBUSER, DBPASS, and DBNAME 
     * with their appropriate values, and should be located outside of the webroort
     * which we assume is one level up */
    include('../../db-connection.php');
    
    /* TODO maybe add more error checking here, I don't like returning this info to the user though */
    $mysqlLink = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
    if (!$mysqlLink) {
        echo "Failed to connect to MySQL: (" . mysqli_connect_errno() . ") " . mysqli_connect_error();
    }

    /* Fetch Question - Only return one answer, so */
    $questionResult = mysqli_query($mysqlLink, "SELECT questions.id, questions.question, sources.source, sources.url " .  
                                               "FROM questions INNER JOIN sources ON questions.source_id = sources.id " . 
                                               "WHERE questions.number_of_answers = 1 " .
                                               "ORDER BY RAND() LIMIT 0,1");
    $questions      = mysqli_fetch_assoc($questionResult);

    /* Fetch Answer - Currently we return 3 answers */
    $answerResult   = mysqli_query($mysqlLink,"SELECT answers.id,    answers.answer,     sources.source, sources.url " . 
                                              "FROM answers    INNER JOIN sources ON answers.source_id   = sources.id " .
                                              "WHERE answers.NSFW = 0 " .
                                              "ORDER BY RAND() LIMIT 0,3");
    /* itterate though the results, put them into an array */
    while ($row = mysqli_fetch_assoc($answerResult)) {
        $answers[] = $row;
    }

    $permURL = "http://" . $_SERVER['HTTP_HOST'] . "/CVH/display/" .  strtoupper(dechex($questions['id'])) . "-";
    $voteURL = "/CVH/vote/" .  strtoupper(dechex($questions['id'])) . "-";
?>

<body>
    <div id="header">
        <h1><a href="/CVH">Cards vs Humans</a></h1>
    </div>
	
    <div class="card question">
        <p><?php echo $questions['question']; ?></p>
        <a href="<?php echo $questions['url']; ?>" class="source">
<?php 
                if ($questions['source'] == 'Cards Against Humanity') {
                    echo '<img src="CAH-Cards-White.svg" alt="Cards Against Humanity" />'; 
                }
                echo $questions['source'] . PHP_EOL;
?>
        </a>
    </div>
    
    <div class="instructions">
        Pick the card you like the best!
    </div>
    
    <div class="clear"></div>
    
<?php foreach ($answers as $answer) { ?>
    <div class="card answer">
        <a class="answerlink" href="<?php echo $voteURL . strtoupper(dechex($answer['id'])); ?>">
            <?php echo $answer['answer']; ?>
        </a>
        <a href="<?php echo $answer['url']; ?>" class="source">
<?php 
                if ($answer['source'] == 'Cards Against Humanity') {
                    echo '<img src="CAH-Cards-White.svg" alt="Cards Against Humanity" />';
                }
                echo $answer['source'] . PHP_EOL;
?>
        </a>
    </div>
<?php } ?>
    
    <div class="card answer bad">
        <a class="answerlink" href="/CVH">THESE ALL SUCK!</a>
    </div>
    <div class="clear"></div>

<?php foreach ($answers as $answer) { ?>
    <div class="permalink">
        <p><a href="<?php echo $permURL . strtoupper(dechex($answer['id'])); ?>" >Permalink</a></p>
    </div>
<?php } ?>
</body>
</html>