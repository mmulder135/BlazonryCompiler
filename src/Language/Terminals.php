<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

class Terminals extends Dictionary
{
    const METAL = 'metal';
    const TINCTURE = 'tincture';
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
