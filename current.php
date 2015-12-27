<?php
require_once ('MysqliDb.php');
$db = new MysqliDb ('localhost', 'root', '', 'golf-cards');
$game = 0;
$mod = 0;
$rounds_completed = 0;
$game_name = "Game Name";
if (isset($_COOKIE["game"]) && !empty($_COOKIE["game"])) {
     $game = $_COOKIE["game"];
}
if (isset($_COOKIE["mod"]) && !empty($_COOKIE["mod"])) {
     $mod = $_COOKIE["mod"];
}
if (isset($_REQUEST["gameName"]) && !empty($_REQUEST["gameName"])) {
    $cols3 = Array ("game_id");
    $db->where("friendly_name", $_REQUEST["gameName"]);
    $result = $db->get ("game_names", null, $cols3);
    // var_dump($result);
    $game =  $result[0]["game_id"];
    $int = 43200;
    setcookie("game",$game,time()+$int);

}
if (isset($_REQUEST["goToNextRound"]) && !empty($_REQUEST["goToNextRound"])) {
    $scoresTable = $game . "scores";

    for ($i=0; $i < $_REQUEST["player_count"]; $i++) { 
        $data = Array ("player_id" => strval($i+1),
                       "round" => $_REQUEST["roundJustCompleted"], 
                       "score" => $_REQUEST["playerIDscore" . strval($i+1)]);
        $id = $db->insert ($scoresTable, $data);
    }
    $data = Array ('max_round' => $_REQUEST["roundJustCompleted"]);
    $db->update ('game_names', $data);
}


if ($game) {
    $nameTable = $game . "names";
    $scoresTable = $game . "scores";

//get players
    $cols = Array ("id","name");
    $players = $db->get ($nameTable, null, $cols);
    $player_num = count($players);

//get total scores
    $cols2 = Array ("player_id","round","score");
    $scores = $db->get ($scoresTable, null, $cols2);
    $totalScores = array();
    for ($i=0; $i < $player_num; $i++) { 
        $cols2 = Array ("score");
        $db->where('player_id', strval($i+1));
        $blee = $db->get ($scoresTable, null, $cols2);
        $blah = array('player' => $i, 'score' => 0);
        foreach ($blee as $key => $value) {
            foreach ($value as $indScore) {
                $blah['score'] += $indScore;
            }
        }
        $totalScores[] = $blah;
    }

    // $db->join($scoresTable . " s", "n.id=s.player_id", "LEFT");
    // $db->where("s.round", 0);
    // $db->orderBy("s.score","desc");
    // $players_and_scores = $db->get ($nameTable . " n", null, "n.name, n.id, s.score");
    // // var_dump($players_and_scores);

    $cols3 = Array ("friendly_name","max_round");
    $db->where("game_id", $game);
    $result = $db->get ("game_names", null, $cols3);
    $game_name =  $result[0]["friendly_name"];
    $rounds_completed = $result[0]["max_round"]; 
}


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
                    <a href="index.html">Homepage</a>
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
                        <?php if($game == 0){ ?>
                        <h1>Join a game!</h1>
                        <form action="current.php" method="post">
                            <input type="text" class="form-control" required name="gameName" placeholder="Enter a game name"><br>
                            <button type="submit" class="btn btn-default">Submit</button>
                        </form>
                        <?php    } elseif (!$mod) { ?>
                        <h1><?php echo $game_name ?></h1>
                        <h3>Scores at Hole <?php echo $rounds_completed?></h3>
                            <table class="table table-striped">
                                <thead>
                                    <tr> 
                                        <th>Name</th>
                                        <th>Rank</th>
                                        <th>Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i=0; $i < $player_num; $i++) { ?>
                                    <tr>
                                        <th scope="row"><?php echo $players[$i]["name"] ?></th> 
                                        <td>1</td>
                                        <td><?php echo $totalScores[$i]["score"] ?></td>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>

                        <?php    } elseif ($mod) { ?>

                        <h1><?php echo $game_name ?></h1>
                        <h2>Hi, scorekeeper</h2>
                        <h3>Scores at Hole <?php echo $rounds_completed?></h3>
                            <table class="table table-striped">
                                <thead>
                                    <tr> 
                                        <th>Name</th>
                                        <th>Rank</th>
                                        <th>Total Score</th>
                                        <?php if($rounds_completed < 18) {?><th>Enter score for hole <?php echo strval($rounds_completed+1)?></th> <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <form class="form-inline" method="post" action="current.php">
                                    <?php for ($i=0; $i < $player_num; $i++) { ?>
                                    <tr>
                                        <th scope="row"><?php echo $players[$i]["name"] ?></th> 
                                        <td>1</td>
                                        <td><?php echo $totalScores[$i]["score"] ?></td>
                                        <?php if($rounds_completed < 18) {?><td style="width: 40%"><input type="number" required class="form-control" name="playerIDscore<?php echo $players[$i]["id"] ?>" placeholder="###"></td><?php } ?>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                            <?php if($rounds_completed < 18) {?>
                            <div class="pull-right"><button type="submit" class="btn btn-success">Submit score for hole <?php echo strval($rounds_completed+1)?> </button>  </div>
                            <?php } else { ?>
                            <div class="pull-right"><button type="submit" class="btn btn-default disabled">You're done! </button>  </div>
                            <?php } ?>
                            <input type="hidden" name="goToNextRound" value="true">
                            <input type="hidden" name="player_count" value="<?php echo $player_num ?>">
                            <input type="hidden" name="roundJustCompleted" value="<?php echo strval($rounds_completed+1)?>">
                            </form>

                        <?php    } ?>

                        <br>
                        <a href="#" class="btn btn-default">Join a different game</a><br>
                        <a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Toggle Menu</a>
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
