<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BlazonryCompiler</title>
    <link rel="stylesheet" href="index.css" type="text/css">
</head>
<header>
    <h1>Welcome to BlazonryCompiler!</h1>
</header>
<body>
<?php

use BlazonCompiler\Compiler\Compiler;

require __DIR__ . '/vendor/autoload.php';
if (isset($_GET['blazon'])) {
    [$xml, $errors] = Compiler::compile($_GET['blazon']);
} else {
    $xml = '';
    $errors = '';
}
?>
<div>
    <p>
        This page has been developed as a showcase of
        <a href="https://github.com/mmulder135/BlazonryCompiler">this</a> project.
        The goal of this project is to build a compiler for blazonry using semi-structured parsing methods.
    </p>
    <p>
        We currently support the colors: Or, Argent, Azure, Purpure, Sable, Vert, and Gules.
        We also support the furs Ermine and Vair.
    </p>
    <p>
        We support the generation of single color fields and field devided per bend or per pale.
        Devisions can also be sinister.
    </p>
</div>
<div>
    <h4>
        Try out the compiler by typing a blazon in the box below.
    </h4>
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="GET">
<!--        <input type="text" size="50" name="blazon" />-->
        <textarea id="blazonInput" name="blazon" rows="4" cols="50"><?php
        if (isset($_GET['blazon'])) {
            echo $_GET['blazon'];
        }
        ?></textarea>
        <br>
        <input type="submit" value="Generate" />
    </form>
</div>
<div id="svg">
    <?php echo $xml; ?>
</div>
<div id="error">
    <h4 style="color: red">
        <?php echo $errors; ?>
    </h4>
</div>
<footer>
    <a href="https://github.com/mmulder135/BlazonryCompiler">GitHub</a>
</footer>
</body>
</html>
