<?php @session_start();

if(isset($_GET['heigthJanela'])) {
    $_SESSION['screenInfo'] = [
        'heigthJanela' => $_GET['heigthJanela'],
        'widthJanela' => $_GET['widthJanela'],
        'heightTela' => $_GET['heightTela'],
        'widthTela' => $_GET['widthTela']
    ];
}

 