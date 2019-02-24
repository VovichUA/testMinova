<?php
require_once 'src/Parser.php';
use Src\Parser;

session_start();

try {
    $parser = new Parser($_POST['url']);
    $parser->parse();
} catch (Exception $error) {
    $_SESSION['error'] = $error->getMessage();
    header('Location: /');
}
