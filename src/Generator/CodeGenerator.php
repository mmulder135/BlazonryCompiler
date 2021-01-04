<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Generator;

use BlazonCompiler\Compiler\AST\NonTerm;
use BlazonCompiler\Compiler\Language\Tokens;
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
        return $this->generateField($root);
    }

    public function generateField(NonTerm $field): SimpleXMLElement
    {
//        Field =     Color
//              |   (Party?) Partition (comma?) Color Color
        $children = $field->getChildren();
        $first = $children[0];
        $shield = new SimpleXMLElement(dirname(__FILE__, 3).'/images/shield.svg', dataIsURL: true);
        switch ($first->getToken()) {
            case Tokens::COLOR:
                $color = $first->getChildren()[0];
                switch ($color->getToken()) {
                    case Tokens::METAL:
                    case Tokens::TINCTURE:
                        $shield->g->use['style'] = "stroke:#000000;fill:".$this->colors[$color->getText()];
                        break;
                    case Tokens::FUR:
                        //set shield to white
                        $shield->g->use['style'] = "fill:#ffffff;stroke:#000000";
                        // put pattern on top
                        $use = $shield->g->addChild('use');
                        $use->addAttribute("style", "fill:url(#Vair)");
                        $use->addAttribute("id", "FieldPattern");
                        $use->addAttribute("xlink:href", "#Shield1", "http://www.w3.org/1999/xlink");

                        //Add def
                        $pattern = $shield->defs->addChild('pattern');
                        $pattern->addAttribute("id", "Vair");
                        $pattern->addAttribute("height", "240");
                        $pattern->addAttribute("width", "100");
                        $pattern->addAttribute("patternUnits", "userSpaceOnUse");
                        $top = $pattern->addChild('path');
                        $top->addAttribute("transform", "translate(-10,0)");
                        $top->addAttribute("fill", "#0036b6");
                        $top->addAttribute("d", "m62.875.5-31.187 29.95v59.9l-31.188 29.95h124.75l-31.188-29.95v-59.9z");
                        $bottom = $pattern->addChild('path');
                        $bottom->addAttribute("transform", "translate(-60,120)");
                        $bottom->addAttribute("fill", "#0036b6");
                        $bottom->addAttribute("d", "m62.875.5-31.187 29.95v59.9l-31.188 29.95h124.75l-31.188-29.95v-59.9z");
                        break;
                }
                break;
            default:
                break;
        }
        return $shield;
    }
}
