<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Checker;

use BlazonCompiler\Compiler\AST\IR;
use BlazonCompiler\Compiler\Language\Tokens;

class StaticChecker
{
    const COMPILERSTEP = "Heraldry Checker";

    public static function checkIR(IR $root):void
    {
        // Check if no metal on metal or tincture on tincture
        if ($root->hasChildToken(Tokens::FIELD)) {
            $field = $root->getFirst(Tokens::FIELD);
            // get if field is tincture or metal
            $color = $field->getFirst(Tokens::COLOR);
            $type = $color->getChildren()[0]->getToken();
            $ordinaries = $root->getChildrenByToken(Tokens::FULL_ORDINARY);
            foreach ($ordinaries as $ordinary) {
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
