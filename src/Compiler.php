<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler;

use BlazonCompiler\Compiler\Checker\StaticChecker;
use BlazonCompiler\Compiler\Generator\CodeGenerator;
use BlazonCompiler\Compiler\Parser\Parser;

class Compiler
{
    /**
     * Compiles blazon to an svg string, returns [xml, errors].
     * @param string $blazon
     * @return string[]
     */
    public static function compile(string $blazon): array
    {
        $ir = Parser::parse($blazon);
        StaticChecker::checkIR($ir);
        $generator = new CodeGenerator();
        $doc = $generator->generateWithEdge($ir);
        $xml =(string) $doc->saveXML();
        $errors = implode(",\n", $ir->getMessages());
        return [$xml,$errors];
    }
}
