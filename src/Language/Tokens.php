<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

final class Tokens
{
    /** Terminal Tokens */
    // Terminals
    const METAL = 'METAL';
    const TINCTURE = 'TINCTURE';
    const FUR = 'FUR';
    const PARTITION_LINE = 'PARTITION_LINE';
    const ORDINARY = 'ORDINARY';
    const ONE = "ONE";
    const PER = 'PER';
    const DIVISION = 'DIVISION';
    const SINISTER = 'SINISTER';
    const PARTED = 'PARTED';
    const AND = 'AND';

    // Separators
    const WS = 'WS';
    const COMMA = 'COMMA';

    //Unrecognized tokens
    const STR = 'STR';

    /** NonTerminal tokens */
    const COLOR = 'COLOR';
    const PARTITION = 'PARTITION';
    const FIELD = 'FIELD';
}
