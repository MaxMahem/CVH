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
    $mysqlLink = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
    if (!$mysqlLink) {
        echo "Failed to connect to MySQL: (" . mysqli_connect_errno() . ") " . mysqli_connect_error();
    }

    /* Fetch Question */
    $questionResult = mysqli_query($mysqlLink, "SELECT * FROM questions ORDER BY RAND() LIMIT 0,1");
    $questions      = mysqli_fetch_assoc($questionResult);

    /* Fetch Answer */
    $answerResult = mysqli_query($mysqlLink, "SELECT * FROM answers ORDER BY RAND() LIMIT 0,1");
    $answers      = mysqli_fetch_assoc($answerResult);
    
    $permURL = "http://" . $_SERVER['HTTP_HOST'] . "/CVH/display/?Q=" . dechex($questions['id']) . "&A=" . dechex($answers['id']);
?>

<body>
    <h1>Cards vs Humans</h1>
    <p class="question"><?php echo $questions['question']; ?></p>
    <p class="answer"><?php   echo $answers['answer']; ?></p>
    <p>'Permanent' URL: <a href="<?php echo $permURL ?>" ><?php echo $permURL ?></a></p>
</body>
</html>