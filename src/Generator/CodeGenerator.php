<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Generator;

use BlazonCompiler\Compiler\AST\NonTerm;
use BlazonCompiler\Compiler\Language\Tokens;
use DOMDocument;
use DOMNode;

class CodeGenerator
{

    public function generate(NonTerm $root): DOMDocument
    {
        return $this->generateField($root);
    }

    public function generateField(NonTerm $field): DOMDocument
    {
//        Field =     Color
//              |   (Party?) Partition (comma?) Color Color
        $children = $field->getChildren();
        $first = $children[0];

        $document = new DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;
        $document->loadXML(SVGParts::BASESHIELD);

        switch ($first->getToken()) {
            case Tokens::COLOR:
                $color = $first->getChildren()[0];
                switch ($color->getToken()) {
                    case Tokens::METAL:
                    case Tokens::TINCTURE:
                        $color = SVGParts::COLORS[$color->getText()];
                        $this->setShieldColor($document, $color);
                        break;
                    case Tokens::FUR:
                        //set shield to white
                        $this->setShieldColor($document, "#ffffff");

                        // put pattern on top
                        $this->addFurPattern($document, $color->getText());
                        break;
                }
                break;
            default:
                break;
        }
        return $document;
    }

    /**
     * @param DOMDocument $document
     * @param string $color
     */
    protected function setShieldColor(DOMDocument $document, string $color): void
    {
        // doc -> svg -> g -> use
        $document->lastChild->lastChild->firstChild->setAttribute("style", "stroke:#000000;fill:".$color);
    }

    /**
     * @param DOMDocument $document
     */
    protected function addFurPattern(DOMDocument $document, string $fur): void
    {
        // doc -> svg -> [defs,g]
        $defs = $document->lastChild->firstChild;
        $g = $document->lastChild->lastChild;

        // Add definition vair shape
        $this->addXML($document, $defs, SVGParts::FURS[$fur]);

        //Add definition mask
        $this->addXML($document, $defs, SVGParts::MASK);
        // Use mask
        $g->setAttribute("mask", "url(#Mask)");

        for ($y = 0; $y <= 660; $y += 120) {
            // make normal row
            for ($x = 0; $x <= 660; $x += 120) {
                $element = $document->createElement("use");
                $element->setAttributeNS("http://www.w3.org/1999/xlink", "xlink:href", "#".$fur);
                $element->setAttribute("transform", "translate({$x},{$y})");
                $g->appendChild($element);
            }
            //make other row
            $y+=120;
            for ($x = -60; $x <= 660; $x += 120) {
                $element = $document->createElement("use");
                $element->setAttributeNS("http://www.w3.org/1999/xlink", "xlink:href", "#".$fur);
                $element->setAttribute("transform", "translate({$x},{$y})");
                $g->appendChild($element);
            }
        }
    }

    protected function addXML(DOMDocument $document, DOMNode $parent, string $xml): void
    {
        $fragment = $document->createDocumentFragment();
        $fragment->appendXML($xml);
        $parent->appendChild($fragment);
    }
}
