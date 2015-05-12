<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 0);
mb_internal_encoding('UTF-8');

function getIv($key){
    $keyLength = mb_strlen($key);
    $ivNeededLength = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CFB);
    $iv = '';

    if($keyLength > $ivNeededLength){
        $iv = substr($key, 0, $ivNeededLength);
    }
    elseif($keyLength < $ivNeededLength){
        do{
            $ivCurrentLength = mb_strlen($iv);
            $iv .= $key;
        }
        while($ivCurrentLength < $ivNeededLength);

        $iv = substr($iv, 0, $ivNeededLength);
    }
    else{
        $iv = $key;
    }

    return $iv;
}

function encryption($text, $key){
    $iv = getIv($key);
    $result =  mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_CBC, $iv);
    return base64_encode($result);
}

function decryption($text, $key){
    $iv = getIv($key);
    $text = base64_decode($text);
    $result = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_CBC, $iv);
    $result = trim($result);
    return $result;
}

if($_GET['method'] == 'encryption'){
    if(empty($_POST['key']) || empty($_POST['text'])){
        $resp = 'Musisz podać tekst oraz klucz.';
    }
    else{
        $encryption_result = encryption($_POST['text'], $_POST['key']);
        $encryption_text = $_POST['text'];
        $encryption_key = $_POST['key'];
    }
}
elseif($_GET['method'] == 'decryption'){
    if(empty($_POST['key']) || empty($_POST['text'])){
        $resp = 'Musisz podać tekst oraz klucz.';
    }
    else{
        $decryption_result = decryption($_POST['text'], $_POST['key']);
        $decryption_text = $_POST['text'];
        $decryption_key = $_POST['key'];
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Ochrona danych - Mateusz Błaszczyk - WEEIA PŁ</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="content">
        <?php
            if(isset($resp)){
                echo '<p class="resp">'.$resp.'</p>';
            }
        ?>
        <h2>Szyfrowanie:</h2>
        <form method="post" action="?method=encryption">
            <input type="text" name="text" placeholder="Tekst jawny" value="<?php echo $encryption_text;?>">
            <input type="text" name="key" placeholder="Klucz" value="<?php echo $encryption_key;?>">
            <button>Szyfruj</button>
            <textarea><?php echo $encryption_result; ?></textarea>
        </form>

        <h2>Deszyfrowanie:</h2>
        <form method="post" action="?method=decryption">
            <input type="text" name="text" placeholder="Tekst zaszyfrowany" value="<?php echo $decryption_text;?>">
            <input type="text" name="key" placeholder="Klucz" value="<?php echo $decryption_key;?>">
            <button>Deszyfruj</button>
            <textarea><?php echo $decryption_result; ?></textarea>
        </form>
    </div>

<div class="copyright">
    <a target="_blank" href="http://webgre.com">Mateusz Błaszczyk</a>
</div>
</body>
</html>