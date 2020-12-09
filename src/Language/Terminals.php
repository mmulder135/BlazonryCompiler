<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

class Terminals extends Dictionary
{
    const METAL = 'METAL';
    const TINCTURE = 'TINCTURE';
    const FUR = 'FUR';
    const PARTITION_LINE = 'PARTITION_LINE';
    const ORDINARY = 'ORDINARY';
    const ONE = "ONE";
//    const STR = 'string';

//    protected string $str = '\w+';

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
            self::ONE => [
                'a','an','one','1'
            ],
//            self::STR => [
//                $this->str
//            ]
        );

        parent::__construct();
    }

    /**
     *
     */
    protected function createRegex(): void
    {
        $tokenMap = array();
        foreach ($this->dictionary as $name => $values) {
            $tokenMap[$name] = implode('|', $values);
        }
        $this->regex = '(\b(' . implode(')\b|\b(', array_values($tokenMap)) . ')\b)';
        $this->tokensArray = array_keys($tokenMap);
    }
}
