<?php

require_once ('MysqliDb.php');

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
                    <a href="create.php">Create a game</a>
                </li>
                <li>
                    <a href="current.php">Current game</a>
                </li>
                <li>
                    <a href="previous.php">Previous games</a>
                </li>
                <li>
                    <a href="about.html">About</a>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>Previous games</h1>
                        <?php 
                            date_default_timezone_set('UTC');

                            $db = new MysqliDb ('localhost', 'root', '', 'golf-cards');
                            $cols = Array ("friendly_name","max_round","game_id ");
                            $result = $db->get ("game_names", null, $cols); ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr> 
                                        <th>Game Name</th>
                                        <th>Highest round played</th>
                                        <th>Date game was played</th>
                                    </tr>
                                </thead>
                                <tbody>                            
                                <?php for ($i=0; $i < count($result); $i++) { ?>

                                    <tr>
                                        <th scope="row"><?php echo $result[$i]["friendly_name"] ?></th> 
                                        <td><?php echo $result[$i]["max_round"] ?></td>
                                        <td><?php echo date("F j, Y, g:i a",$result[$i]["game_id"]-21600) ?></td>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>


                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    <footer class="footer">
      <div class="container">
        <a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Toggle Menu</a>
      </div>
    </footer>
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
