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

    /** @var array<string,array<string,string|array<int,array<string,string>>>> */
    private array $patterns = [
        'vair' => [
            'name' => 'Vair',
            'height' => '240',
            'width' => '100',
            'paths' => [
                [
                    "transform"=>"translate(-10,5)",
                    "color"=>"#0036b6",
                    "path"=>"m62.875.5-31.187 29.95v59.9l-31.188 29.95h124.75l-31.188-29.95v-59.9z",
                ],
                [
                    "transform"=>"translate(-60,125)",
                    "color"=>"#0036b6",
                    "path"=>"m62.875.5-31.187 29.95v59.9l-31.188 29.95h124.75l-31.188-29.95v-59.9z",
                ],
            ],
        ],
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
        // Ugly way to get the path to shield
        $shield = new SimpleXMLElement(dirname(__FILE__, 3).'/images/shield.svg', dataIsURL: true);
        switch ($first->getToken()) {
            case Tokens::COLOR:
                $color = $first->getChildren()[0];
                switch ($color->getToken()) {
                    case Tokens::METAL:
                    case Tokens::TINCTURE:
                        $color = $this->colors[$color->getText()];
                        $this->addShield($shield, $color);
                        break;
                    case Tokens::FUR:
                        //set shield to white
                        $this->addShield($shield, "#ffffff");

                        // put pattern on top
                        $this->addPattern($shield, $this->patterns[$color->getText()]);
                        break;
                }
                break;
            default:
                break;
        }
        return $shield;
    }

    /**
     * @param SimpleXMLElement $shield
     * @param string $color
     */
    protected function addShield(SimpleXMLElement $shield, string $color): void
    {
        $shield->g->use["style"] = "stroke:#000000;fill:".$color;
    }

    /**
     * @param SimpleXMLElement $shield
     * @param array $pattern
     */
    protected function addPattern(SimpleXMLElement $shield, array $pattern): void
    {
        // Add def for one
        $part = $shield->defs->addChild('path');
        $part->addAttribute("id", "Vair");
        $part->addAttribute("fill", "#0036b6");
        $path = "m62.875.5-31.187 29.95v59.9l-31.188 29.95h124.75l-31.188-29.95v-59.9z";
        $part->addAttribute("d", $path);

        $main = $shield->g;
        for ($y = 0; $y <= 660; $y += 120) {
            // make normal row
            for ($x = 0; $x <= 660; $x += 120) {
                $use = $main->addChild("use");
                $use->addAttribute("xlink:href", "#Vair", "http://www.w3.org/1999/xlink");
                $use->addAttribute("transform", "translate({$x},{$y})");
            }
            //make other row
            $y+=120;
            for ($x = -60; $x <= 660; $x += 120) {
                $use = $main->addChild("use");
                $use->addAttribute("xlink:href", "#Vair", "http://www.w3.org/1999/xlink");
                $use->addAttribute("transform", "translate({$x},{$y})");
            }
        }
    }
}
