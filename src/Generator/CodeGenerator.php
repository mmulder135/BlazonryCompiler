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
        $document = new DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;
        $document->loadXML(GeneratorDefinitions::BASESHIELD);

        // generate the field
        if ($root->hasChildToken(Tokens::FIELD)) {
            $this->generateField($root->getChildrenByToken(Tokens::FIELD)[0],$document);
        } else {
            // No field definition, generate gray field
            // TODO: error?
            $color = GeneratorDefinitions::getColor('error');
            $this->setShieldColor($document, $color);
        }

        // generate ordinaries
        // generate charges

        return $document;
    }

    private function generateField(NonTerm $field, DOMDocument $document): void
    {
        // FIELD = PARTITION COLOR COLOR | COLOR
        if ($field->hasChildToken(Tokens::PARTITION)) {
            // PARTITION COLOR COLOR
            $partition = $field->getFirst(Tokens::PARTITION);
            $division = $partition->getFirst(Tokens::DIVISION);
            // DIVISION = 'quarterly' | PER ORDINARY
            if ($division->hasChildToken(Tokens::ORDINARY)) {
                $division = $division->getFirst(Tokens::ORDINARY);
            }
            [$color1, $color2] = $field->getChildrenByToken(Tokens::COLOR);

            $this->setShieldColor($document, GeneratorDefinitions::getColor($color2->getText()));
            $this->addPartition($document, $division->getText(), GeneratorDefinitions::getColor($color1->getText()));
        } else {
            // FIELD = COLOR
            $color = $field->getFirst(Tokens::COLOR);
            $type = $color->getChildren()[0]->getToken();
            switch ($type) {
                case Tokens::METAL:
                case Tokens::TINCTURE:
                    $color = GeneratorDefinitions::getColor($color->getText());
                    $this->setShieldColor($document, $color);
                    break;
                case Tokens::FUR:
                    //set shield to white
                    $this->setShieldColor($document, "#ffffff");

                    // put pattern on top
                    $this->addFurPattern($document, $color->getText());
                    break;
            }
        }
//
//        $children = $field->getChildren();
//        $first = $children[0];
//
//        switch ($first->getToken()) {
//            case Tokens::COLOR:
//                $color = $first->getChildren()[0];
//                switch ($color->getToken()) {
//                    case Tokens::METAL:
//                    case Tokens::TINCTURE:
//                        $color = GeneratorDefinitions::COLORS[$color->getText()];
//                        $this->setShieldColor($document, $color);
//                        break;
//                    case Tokens::FUR:
//                        //set shield to white
//                        $this->setShieldColor($document, "#ffffff");
//
//                        // put pattern on top
//                        $this->addFurPattern($document, $color->getText());
//                        break;
//                }
//                break;
//            case Tokens::PARTITION:
//                // For now just partition color color
//                // children = [partition, color, color]
//                $color1 = $children[1]->getChildren()[0];
//                $color2 = $children[2]->getChildren()[0];
//                $this->setShieldColor($document, GeneratorDefinitions::getColor($color1->getText()));
//                $this->addPartition($document, 'bend', GeneratorDefinitions::getColor($color2->getText()));
//                break;
//            default:
//                break;
//        }
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
     * @param string $fur
     */
    protected function addFurPattern(DOMDocument $document, string $fur): void
    {
        // doc -> svg -> [defs,g]
        $defs = $document->lastChild->firstChild;
        $g = $document->lastChild->lastChild;

        // Add definition vair shape
        $this->addXML($document, $defs, GeneratorDefinitions::getFurDefinition($fur));

        //Add definition mask
        $this->addXML($document, $defs, GeneratorDefinitions::MASK);
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

    protected function addPartition(DOMDocument $document, string $partition, string $color)
    {
        //Create mask
        $mask = GeneratorDefinitions::getPartitionMask($partition);
        if (!$mask) {
            // TODO: proper errors
            throw new \Exception("Can't parse Partition");
        }
        $this->addXML($document, $document->lastChild->firstChild, $mask);

        $part = $document->createElement("use");
        $part->setAttribute("mask", "url(#{$partition})");
        $part->setAttribute("fill", $color);
        $part->setAttributeNS("http://www.w3.org/1999/xlink", "xlink:href", "#Shield1");
        $document->lastChild->lastChild->appendChild($part);
    }
}
