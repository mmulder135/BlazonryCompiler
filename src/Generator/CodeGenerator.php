<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Generator;

use BlazonCompiler\Compiler\AST\IR;
use BlazonCompiler\Compiler\AST\Node;
use BlazonCompiler\Compiler\AST\NonTerm;
use BlazonCompiler\Compiler\Language\Tokens;
use DOMDocument;
use DOMElement;
use DOMNode;
use Exception;

class CodeGenerator
{
    const COMPILERSTEP = "Generator";
    private IR $shield;
    private DOMDocument $document;

    public function generateWithEdge(IR $shield): DOMDocument
    {
        // Generate shield normally
        $this->generate($shield);

        // Add outer edge
        $edge = $this->document->createElement("use");
        $edge->setAttributeNS("http://www.w3.org/1999/xlink", "xlink:href", "#Shield1");
        $edge->setAttribute("fill", "none");
        $edge->setAttribute("stroke-width", "2");
        $edge->setAttribute("stroke", "#000000");
        $this->document->lastChild->appendChild($edge);

        return $this->document;
    }

    public function generate(IR $shield): DOMDocument
    {
        $this->shield = $shield;

        $this->document = new DOMDocument();
        $this->document->preserveWhiteSpace = false;
        $this->document->formatOutput = true;
        $this->document->loadXML(GeneratorDefinitions::BASESHIELD);

        // generate the field
        if ($this->shield->hasChildToken(Tokens::FIELD)) {
            $this->generateField($this->shield->getFirst(Tokens::FIELD));
        } else {
            // No field definition, generate gray field
            $this->shield->addMessage(self::COMPILERSTEP,"Could not generate field, generating gray shield");
            $color = GeneratorDefinitions::getColor('error');
            $this->setShieldColor($color);
        }

        $ordinaries = $this->shield->getChildrenByToken(Tokens::FULL_ORDINARY);
        foreach ($ordinaries as $ordinary) {
            $this->generateOrdinary($ordinary);
        }

        // generate ordinaries
        // generate charges

        return $this->document;
    }

    /**
     * @param Node $field
     */
    private function generateField(Node $field): void
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

            $this->setColor($color2);
            $sinister = $field->hasChildToken(Tokens::SINISTER);
            $this->addPartition($partition->getText(), $color1, $sinister);
        } else {
            // FIELD = COLOR
            $color = $field->getFirst(Tokens::COLOR);
            $this->setColor($color);
        }
    }

    protected function setColor(Node $colorNode): void
    {
        $type = $colorNode->getChildren()[0]->getToken();
        switch ($type) {
            case Tokens::METAL:
            case Tokens::TINCTURE:
                $color = GeneratorDefinitions::getColor($colorNode->getText());
                if (!$color) {
                    $this->shield->addMessage(self::COMPILERSTEP,"Unknown color {$colorNode->getText()}");
                }
                $this->setShieldColor($color);
                break;
            case Tokens::FUR:
                //set shield to white
                $this->setShieldColor("#ffffff");

                // put pattern on top
                $this->addFurPattern($colorNode->getText());
                break;
            default:
                $this->shield->addMessage(self::COMPILERSTEP,"Could not generate field, generating gray shield");
                $color = GeneratorDefinitions::getColor('error');
                $this->setShieldColor($color);
        }
    }

    /**
     * @param string $color
     */
    protected function setShieldColor(string $color): void
    {
        // doc -> svg -> g -> use
        $this->document->lastChild->lastChild->firstChild->setAttribute("fill", $color);
    }

    /**
     * @param string $furName
     * @param string|null $patternName
     */
    protected function addFurPattern(string $furName, ?string $patternName = null): void
    {
        // doc -> svg -> [defs,g]
        $defs = $this->document?->lastChild?->firstChild;
        $g = $this->document?->lastChild?->lastChild;

        // Add definition fur shape
        $fur = GeneratorDefinitions::getFurDefinition($furName);
        if (!$fur) {
            $this->shield->addMessage(self::COMPILERSTEP,"Unknown fur:".$furName);
            return;
        }
        $this->addXML($defs, $fur);

        // Create group to place pattern in
        $patternGroup = $this->document->createElement("g");
        $patternGroup->setAttribute("id", "PatternGroup");
        $g->appendChild($patternGroup);

        //Add definition mask
        if (!$this->checkMaskExists("Mask")) {
            $this->addXML($defs, GeneratorDefinitions::MASK);
        }
        // Add mask to general group
        $g->setAttribute("mask", "url(#Mask)");
        if ($patternName) {
            // add partition mask to patterngroup
            $patternGroup->setAttribute("mask", "url(#{$patternName})");
        }

        for ($y = 0; $y <= 660; $y += 120) {
            // make normal row
            for ($x = 0; $x <= 660; $x += 120) {
                $patternGroup->appendChild($this->createFurUseStatement($furName, $x, $y));
            }
            //make other row
            $y+=120;
            for ($x = -60; $x <= 660; $x += 120) {
                $patternGroup->appendChild($this->createFurUseStatement($furName, $x, $y));
            }
        }
    }

    /**
     * @param string $fur
     * @param int $x
     * @param int $y
     * @return DOMElement
     */
    protected function createFurUseStatement(string $fur, int $x, int $y): DOMElement
    {
        $element = $this->document->createElement("use");
        $element->setAttributeNS("http://www.w3.org/1999/xlink", "xlink:href", "#".$fur);
        $element->setAttribute("transform", "translate({$x},{$y})");
        return $element;
    }

    protected function addXML(DOMNode $parent, string $xml): void
    {
        $fragment = $this->document->createDocumentFragment();
        $fragment->appendXML($xml);
        $parent->appendChild($fragment);
    }

    /**
     * @param string $partition
     * @param NonTerm $colorNode
     * @param bool $sinister
     * @throws Exception
     */
    protected function addPartition(
        string $partition,
        NonTerm $colorNode,
        bool $sinister = false
    ):void {
        //Create mask
        if (!$this->checkMaskExists($partition)) {
            $points = GeneratorDefinitions::getPartitionMaskPoints($partition);
            if (!$points) {
                $this->shield->addMessage(self::COMPILERSTEP,"Can't generate partition: ".$partition);
                return;
//                throw new Exception("Can't generate Partition");
            }
    //        $this->addXML($document, $document->lastChild->firstChild, $mask);
            $poly = $this->document->createElement("polygon");
            $poly->setAttribute("points", $points);
            $poly->setAttribute("fill", "white");
            if ($sinister) {
                if (GeneratorDefinitions::canBeSinister($partition)) {
                    $poly->setAttribute("transform", GeneratorDefinitions::SINISTERTRANSFORM);
                } else {
                    $this->shield->addMessage(self::COMPILERSTEP,"Cannot apply sinister to ".$partition);
                }
            }
            $mask = $this->document->createElement("mask");
            $mask->appendChild($poly);
            $mask->setAttribute("id", $partition);
            // defs -> append mask
            $this->document->lastChild->firstChild->appendChild($mask);
        }

        $type = $colorNode->getChildren()[0]->getToken();
        switch ($type) {
            case Tokens::METAL:
            case Tokens::TINCTURE:
                $color = GeneratorDefinitions::getColor($colorNode->getText());
                if (!$color) {
                    $this->shield->addMessage(self::COMPILERSTEP,"Unknown color {$colorNode->getText()}");
                }
                $this->addColorUseStatement($partition, $color);
                break;
            case Tokens::FUR:
                //set shield to white
                $color = "#ffffff";
                $this->addColorUseStatement($partition, $color);

                // put pattern on top
                $this->addFurPattern($colorNode->getText(), $partition);
                break;
            default:
                $this->shield->addMessage(self::COMPILERSTEP,
                    "Could not generate color {$colorNode->getText()} of type {$type}, generating it as gray"
                );
                $color = GeneratorDefinitions::getColor('error');
                $this->addColorUseStatement($partition, $color);
        }
    }


    protected function addColorUseStatement(string $maskName, string $color): void
    {
        $part = $this->document->createElement("use");
        $part->setAttribute("mask", "url(#{$maskName})");
        $part->setAttribute("fill", $color);
        $part->setAttributeNS("http://www.w3.org/1999/xlink", "xlink:href", "#Shield1");
        $this->document->lastChild->lastChild->appendChild($part);
        // dco -> svg -> g -> append
    }

    protected function generateOrdinary(NonTerm $ordinary): void
    {
        $sinister = $ordinary->hasChildToken(Tokens::SINISTER);
        $shape = $ordinary->getFirst(Tokens::ORDINARY)->getText();
        $colorName = $ordinary->getFirst(Tokens::COLOR)->getText();

        $color = GeneratorDefinitions::getColor($colorName);
        if (!$color) {
            $this->shield->addMessage(self::COMPILERSTEP,"Can't use color {$colorName} here");
            $color = GeneratorDefinitions::getColor('error');
        }

        $points = GeneratorDefinitions::getOrdinaryPoints($shape);
        if (!$points ) {
            $this->shield->addMessage(self::COMPILERSTEP,"Can't generate ordinary {$ordinary->getText()}");
            return;
        }

        // Add mask if it doesn't exist
        if (!$this->checkMaskExists("Mask")) {
            $this->addXML($this->document->lastChild->firstChild, GeneratorDefinitions::MASK);
        }

        // Add ordinary shape
        $poly = $this->document->createElement("polygon");
        $poly->setAttribute("mask", "url(#Mask)");
        $poly->setAttribute("points", $points);
        $poly->setAttribute("fill", $color);
        if ($sinister) {
            if (GeneratorDefinitions::canBeSinister($shape)) {
                $poly->setAttribute("transform", GeneratorDefinitions::SINISTERTRANSFORM);
            } else {
                $this->shield->addMessage(self::COMPILERSTEP,"Cannot apply sinister to ".$shape);
            }
        }

        $this->document->lastChild?->lastChild?->appendChild($poly);
    }

    private function checkMaskExists(string $id): bool
    {
        $masks = $this->document->getElementsByTagName("mask");
        $length = $masks->length;
        for ($i = 0; $i < $length; $i++) {
            $mask = $masks->item($i);
            // if id = id return true
            $name = $mask->getAttribute("id");
            if ($name == $id) {
                return true;
            }
        }
        return false;
    }
}
