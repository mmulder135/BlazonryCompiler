<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Generator;

use BlazonCompiler\Compiler\AST\Node;
use BlazonCompiler\Compiler\AST\NonTerm;
use BlazonCompiler\Compiler\Language\Tokens;
use DOMDocument;
use DOMElement;
use DOMNode;
use Exception;

class CodeGenerator
{

    public function generateWithEdge(NonTerm $root): DOMDocument
    {
        $document = $this->generate($root);
        $edge = $document->createElement("use");
        $edge->setAttributeNS("http://www.w3.org/1999/xlink", "xlink:href", "#Shield1");
        $edge->setAttribute("fill", "none");
        $edge->setAttribute("stroke-width", "2");
        $edge->setAttribute("stroke", "#000000");
        $document->lastChild->appendChild($edge);
        return $document;
    }

    public function generate(NonTerm $root): DOMDocument
    {
        $document = new DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;
        $document->loadXML(GeneratorDefinitions::BASESHIELD);

        // generate the field
        if ($root->hasChildToken(Tokens::FIELD)) {
            $this->generateField($root->getChildrenByToken(Tokens::FIELD)[0], $document);
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
            // PARTITION = 'quarterly' | PER ORDINARY
            if ($partition->hasChildToken(Tokens::ORDINARY)) {
                $partition = $partition->getFirst(Tokens::ORDINARY);
            }
            [$color1, $color2] = $field->getChildrenByToken(Tokens::COLOR);

            $this->setColor($document, $color2);
            $sinister = $field->hasChildToken(Tokens::SINISTER);
            $this->addPartition($document, $partition->getText(), $color1, $sinister);
        } else {
            // FIELD = COLOR
            $color = $field->getFirst(Tokens::COLOR);
            $this->setColor($document, $color);
        }
    }

    protected function setColor(DOMDocument $document, Node $colorNode): void
    {
        $type = $colorNode->getChildren()[0]->getToken();
        switch ($type) {
            case Tokens::METAL:
            case Tokens::TINCTURE:
                $color = GeneratorDefinitions::getColor($colorNode->getText());
                $this->setShieldColor($document, $color);
                break;
            case Tokens::FUR:
                //set shield to white
                $this->setShieldColor($document, "#ffffff");

                // put pattern on top
                $this->addFurPattern($document, $colorNode->getText());
                break;
            default:
                // TODO: add error
                $color = GeneratorDefinitions::getColor('error');
                $this->setShieldColor($document, $color);
        }
    }

    /**
     * @param DOMDocument $document
     * @param string $color
     */
    protected function setShieldColor(DOMDocument $document, string $color): void
    {
        // doc -> svg -> g -> use
        $document->lastChild->lastChild->firstChild->setAttribute("fill", $color);
    }

    /**
     * @param DOMDocument $document
     * @param string $fur
     * @param string|null $mask
     */
    protected function addFurPattern(DOMDocument $document, string $fur, ?string $maskName = null): void
    {
        // doc -> svg -> [defs,g]
        $defs = $document->lastChild->firstChild;
        $g = $document->lastChild->lastChild;

        // Add definition fur shape
        $this->addXML($document, $defs, GeneratorDefinitions::getFurDefinition($fur));

        // Create group to place pattern in
        $patternGroup = $document->createElement("g");
        $patternGroup->setAttribute("id", "PatternGroup");
        $g->appendChild($patternGroup);

        //Add definition mask
        $this->addXML($document, $defs, GeneratorDefinitions::MASK);
        // Add mask to general group
        $g->setAttribute("mask", "url(#Mask)");
        if ($maskName) {
            // add partition mask to patterngroup
            $patternGroup->setAttribute("mask", "url(#{$maskName})");
        }

        for ($y = 0; $y <= 660; $y += 120) {
            // make normal row
            for ($x = 0; $x <= 660; $x += 120) {
                $patternGroup->appendChild($this->createFurUseStatement($document, $fur, $x, $y));
            }
            //make other row
            $y+=120;
            for ($x = -60; $x <= 660; $x += 120) {
                $patternGroup->appendChild($this->createFurUseStatement($document, $fur, $x, $y));
            }
        }
    }

    /**
     * @param DOMDocument $document
     * @param string $fur
     * @param int $x
     * @param int $y
     * @return DOMElement
     */
    protected function createFurUseStatement(DOMDocument $document, string $fur, int $x, int $y): DOMElement
    {
        $element = $document->createElement("use");
        $element->setAttributeNS("http://www.w3.org/1999/xlink", "xlink:href", "#".$fur);
        $element->setAttribute("transform", "translate({$x},{$y})");
        return $element;
    }

    protected function addXML(DOMDocument $document, DOMNode $parent, string $xml): void
    {
        $fragment = $document->createDocumentFragment();
        $fragment->appendXML($xml);
        $parent->appendChild($fragment);
    }

    /**
     * @param DOMDocument $document
     * @param string $partition
     * @param NonTerm $colorNode
     * @param bool|null $sinister
     * @throws Exception
     */
    protected function addPartition(DOMDocument $document,
        string $partition,
        NonTerm $colorNode,
        bool $sinister = false)
    {
        //Create mask
        $points = GeneratorDefinitions::getPartitionMaskPoints($partition);
        if (!$points) {
            // TODO: proper errors
            throw new Exception("Can't generate Partition");
        }
//        $this->addXML($document, $document->lastChild->firstChild, $mask);
        $poly = $document->createElement("polygon");
        $poly->setAttribute("points", $points);
        $poly->setAttribute("fill", "white");
        if ($sinister && GeneratorDefinitions::canBeSinister($partition)) {
            $poly->setAttribute("transform",GeneratorDefinitions::SINISTERTRANSFORM);
        }
        $mask = $document->createElement("mask");
        $mask->appendChild($poly);
        $mask->setAttribute("id", $partition);
        // defs -> append mask
        $document->lastChild->firstChild->appendChild($mask);

        $type = $colorNode->getChildren()[0]->getToken();
        switch ($type) {
            case Tokens::METAL:
            case Tokens::TINCTURE:
                $color = GeneratorDefinitions::getColor($colorNode->getText());
                $this->addColorUseStatement($document, $partition, $color);
                break;
            case Tokens::FUR:
                //set shield to white
                $color = "#ffffff";
                $this->addColorUseStatement($document, $partition, $color);

                // put pattern on top
                $this->addFurPattern($document, $colorNode->getText(), $partition);
                break;
            default:
                // TODO: add error
                $color = GeneratorDefinitions::getColor('error');
                $this->addColorUseStatement($document, $partition, $color);
        }
    }


    protected function addColorUseStatement(DOMDocument $document, string $maskName, string $color): void
    {
        $part = $document->createElement("use");
        $part->setAttribute("mask", "url(#{$maskName})");
        $part->setAttribute("fill", $color);
        $part->setAttributeNS("http://www.w3.org/1999/xlink", "xlink:href", "#Shield1");
        $document->lastChild->lastChild->appendChild($part);
        // dco -> svg -> g -> append
    }
}
