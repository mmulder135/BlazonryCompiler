<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

class Terminals extends Dictionary
{
    public function __construct()
    {
        $this->dictionary = [
            Tokens::METAL => [
                'argent', 'or'
            ],
            Tokens::TINCTURE => [
                'azure', 'purpure', 'sable', 'vert', 'gules'
            ],
            Tokens::FUR => [
                'ermine', 'vair'
            ],
            Tokens::PARTITION_LINE => [
                'engrailed', 'invected', 'embattled', 'indented', 'dancetty', 'wavy', 'nebuly'
            ],
            Tokens::ORDINARY => [
                'fess', 'bend', 'pale', 'chevron', 'cross', 'bar'
            ],
            Tokens::DIVISION => [
                'quarterly'
            ],
            Tokens::PARTED => [
                'party'
            ],
            Tokens::PER => [
                'per',
            ],
            Tokens::AND => [
                'and',
            ],
            Tokens::ONE => [
                'a','an','one','1'
            ],
        ];

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
