<?php
require_once ('MysqliDb.php');

$num_players = 0;
$player_names = array();
$game_name = "default";
if (isset($_REQUEST["player_number"]) && !empty($_REQUEST["player_number"])) {
    $num_players = $_REQUEST["player_number"];
}
if (isset($_REQUEST["game_name"]) && !empty($_REQUEST["game_name"])) {
    $game_name = $_REQUEST["game_name"];
}
if (isset($_REQUEST["player1"]) && !empty($_REQUEST["player1"])) {
    for ($i=0; $i < $num_players; $i++) { 
        $tmp = "player" . strval($i+1);
        $player_names[] = $_REQUEST[$tmp];
    }
}
$done = false;
if (isset($_REQUEST["done"]) && !empty($_REQUEST["done"])) {
    $done = true;
}


$db = new MysqliDb ('localhost', 'root', '', 'golf-cards');
$time = time();
$name = $time . "names";
$scores = $time . "scores";
$q = "CREATE TABLE $name (id INT(9) UNSIGNED PRIMARY KEY AUTO_INCREMENT, name VARCHAR(30) NOT NULL)";
$w = "CREATE TABLE $scores (id INT(9) UNSIGNED PRIMARY KEY AUTO_INCREMENT, player_id int(30) NOT NULL, round int(30) NOT NULL, score int(30) NOT NULL)";
if ($done) { //!empty($num_players) && count($player_names) == $num_players
    $tmp = $db->rawQuery($q);
    $tmp = $db->rawQuery($w);
    $data = Array ("game_id" => $time, "friendly_name" => $game_name);
    $id = $db->insert ("game_names", $data);

    for ($i=0; $i < $num_players; $i++) { 
        $data = Array ("name" => $player_names[$i]);
        $id = $db->insert ($name, $data);
    }
    for ($i=0; $i < $num_players; $i++) { 
        $data = Array ("player_id" => strval($i+1), 
                       "round" => 0, 
                       "score" => 0);
        $id = $db->insert ($scores, $data);
    }
    $int = 43200;
    setcookie("game",$time,time()+$int);
    setcookie("mod","true",time()+$int);

    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'current.php';
    header("Location: http://$host$uri/$extra");}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Golf Card Game Instructions and Helper">
    <meta name="author" content="Adam Starbuck">

    <title>Golf Card Game Score Keeper</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/simple-sidebar.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="./">
                        Golf Score Keeper
                    </a>
                </li>
                <li>
                    <a href="./">Homepage</a>
                </li>
                <li>
                    <a href="current.php">Current game</a>
                </li>
                <li>
                    <a href="http://www.pagat.com/draw/golf.html">Instructions</a>
                </li>
                <li>
                    <a href="previous.php">Previous games</a>
                </li>
                <li>
                    <a href="about.html">About</a>
                </li>
                <li>
                    <a href="stats.php">Stats</a>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>Golf Card Game Score Keeper and Instructions</h1>
                        <?php if(!isset($_REQUEST["value2"])){ ?>
                            <p>How many players do you have?</p>
                            <form action="create.php" method="post">
                                <select class="form-control" name="player_number" required>
                                  <option>1</option <?php if($num_players == 1) echo 'selected="selected"'?> >
                                  <option>2</option <?php if($num_players == 2) echo 'selected="selected"'?> >
                                  <option>3</option <?php if($num_players == 3) echo 'selected="selected"'?> >
                                  <option>4</option <?php if($num_players == 4) echo 'selected="selected"'?> >
                                  <option>5</option <?php if($num_players == 5) echo 'selected="selected"'?> >
                                  <option>6</option <?php if($num_players == 6) echo 'selected="selected"'?> >
                                  <option>7</option <?php if($num_players == 7) echo 'selected="selected"'?> >
                                  <option>8</option <?php if($num_players == 8) echo 'selected="selected"'?> >
                                  <option>9</option <?php if($num_players == 9) echo 'selected="selected"'?> >
                                  <option>10</option <?php if($num_players == 10) echo 'selected="selected"'?> >
                                </select>
                                <input type="hidden" name="value2" value="true">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </form>
                        <?php
                        }?>


                            <?php if(isset($_REQUEST["value2"])){ ?>
                            <p>Okay, now enter their names in the order you want them in!</p>
                            <form action="create.php" method="post">
                                <input type="hidden" name="player_number" value="<?php echo $num_players ?>">
                                <input type="hidden" name="done" value="true">
                                <?php for ($i=0; $i < $num_players; $i++) { 
                                    $nameaaaaa = "player" . strval($i+1);
                                    // var_dump($nameaaaaa);
                                ?>
                                    <input type="text" required class="form-control" placeholder="Player <?php echo $i+1 ?> name" name="<?php echo $nameaaaaa ?>">
                                <?php
                                }?>
                                <br>
                                <label for="game_name">Enter the friendly game name</label>
                                <input type="text" class="form-control" name="game_name" placeholder="Game name">
                                <br>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </form>
                            <?php
                            }?>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>

</body>

</html>
