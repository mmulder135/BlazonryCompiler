<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Checker;

use BlazonCompiler\Compiler\AST\IR;
use BlazonCompiler\Compiler\Language\Tokens;

class StaticChecker
{
    const COMPILERSTEP = "Heraldry Checker";

    /**
     * Check the intermediate representation for color-mistakes
     * @param IR $root
     */
    public static function checkIR(IR $root):void
    {
        // Check if no metal on metal or tincture on tincture
        if ($root->hasChildToken(Tokens::FIELD)) {
            $field = $root->getFirst(Tokens::FIELD);
            if ($field->hasChildToken(Tokens::PARTITION)) {
                [$color1, $color2] = $field->getChildrenByToken(Tokens::COLOR);
                $type1 = $color1->getChildren()[0]->getToken();
                $type2 = $color2->getChildren()[0]->getToken();
                // tincture + metal -> everything allowed
                // tincture + tincture / metal + metal -> judge as one color
                if ($type1 == $type2) {
                    $type = $type1;
                } elseif ($type1 == Tokens::FUR) {
                    $type = $type2;
                } elseif ($type2 == Tokens::FUR) {
                    $type = $type1;
                } else {
                    // not the same and neither is fur, metal + tincture
                    return;
                }

            } else {
                // get if field is tincture or metal
                $color = $field->getFirst(Tokens::COLOR);
                $type = $color->getChildren()[0]->getToken();
            }
            if ($type == Tokens::FUR) {
                // Anything can be placed on a fur
                return;
            }
            $ordinaries = $root->getChildrenByToken(Tokens::FULL_ORDINARY);
            foreach ($ordinaries as $ordinary) {
                // check if type is the same
                if ($ordinary->getFirst(Tokens::COLOR)->getChildren()[0]->getToken() == $type) {
                    $root->addMessage(
                        self::COMPILERSTEP,
                        "Ordinary '{$ordinary->getText()}' and field are both of type {$type}"
                    );
                }
            }
        }
    }
}
