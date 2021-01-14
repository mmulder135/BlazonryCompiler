<?php
declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use BlazonCompiler\Compiler\Generator\CodeGenerator;
use BlazonCompiler\Compiler\Parser\Parser;

if ($argc == 1) {
    ?>
    Usage:
    php <?php echo $argv[0]; ?> "a string of blazon"
<?php
} else {
    $shield = Parser::parse($argv[1]);
    print_r($shield);
    echo "----------\n";
    $g = new CodeGenerator();
    $xml = $g->generateWithEdge($shield)->saveXML();
    file_put_contents("test.svg", $xml);
    echo "Parse result can be viewed in test.svg";
}

