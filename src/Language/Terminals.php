<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

class Terminals extends Dictionary
{
    const METAL = 'metal';
    const TINCTURE = 'tincture';
    const FUR = 'fur';
    const PARTITION_LINE = 'partition line';
    const ORDINARY = 'ordinary';
    const PREPOSITION = "preposition";
    const STR = 'string';

    protected string $str = '\w+';

    public function __construct()
    {
        $this->dictionary = array(
            self::METAL => [
                '[Aa]rgent', '[Oo]r'
            ],
            self::TINCTURE => [
                '[Aa]zure', '[Pp]urpure', '[Ss]able', '[Vv]ert', '[Gg]ules'
            ],
            self::FUR => [
                '[Ee]rmine', '[Vv]air'
            ],
            self::PARTITION_LINE => [
                'engrailed', 'invected', 'embattled', 'indented', 'dancetty', 'wavy', 'nebuly'
            ],
            self::ORDINARY => [
                'bend', 'bar'
            ],
            self::PREPOSITION => [
                'a'
            ],
            self::STR => [
                $this->str
            ]
        );

        parent::__construct();
    }

    public function getStringRegex(): string
    {
        return '(^' . $this->str . ')';
    }
}
