<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Generator;

use BlazonCompiler\Compiler\AST\NonTerm;
use SimpleXMLElement;

class CodeGenerator
{
    /** @var array<string,string> */
    private array $colors = [
        'or' => '#e3d800',
        'argent' => '#fafafa',
        'azure' => '#0036b6',
        'purpure' => '#b6008f',
        'sable' => '#0a0a0a',
        'vert' => '#2ab600',
        'gules' => '#b60000'
    ];

    public function generate(NonTerm $root): SimpleXMLElement
    {
        $word = $root->getText();
        $path = dirname(__FILE__, 3).'/images/shield.svg';
        $svg = simplexml_load_file($path);
        $svg->path['fill'] = $this->colors[$word];
        return $svg;
    }

}
