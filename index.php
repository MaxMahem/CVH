<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">  

<head>  
    <meta http-equiv="Content-Type" content="text/html, charset=ISO-8859-1" />
    <meta http-equiv="Pragma" content="no-cache" />   
    <title>Cards vs Humans</title>
    <link rel="stylesheet" type="text/css" href="cvh.css" />
</head>

<?php
    include('../../db-connection.php');
    
    /* TODO maybe add error checking here, I don't like returning this info to the user though */
    $mysqllink = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
    if ($mysqllink->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqllink->connect_errno . ") " . $mysqllink->connect_error;
    }

    /* Fetch Question */
    $questionresult = mysqli_query($mysqllink, "SELECT * FROM questions LIMIT 0,1");
    $questions      = mysqli_fetch_assoc($questionresult);
    $question       = $questions['question'];

    /* Fetch Answer */
    $answerresult = mysqli_query($mysqllink, "SELECT * FROM answers LIMIT 0,1");
    $answers      = mysqli_fetch_assoc($answerresult);
    $answer       = $answers['answer'];
?>

<body>  
    <p class="question"><?php echo $question; ?></p>
    <p class="answer"><?php echo $answer ?></p>
</body>
</html>