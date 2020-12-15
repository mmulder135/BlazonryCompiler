<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

class Separators extends Dictionary
{
    const WS = ':WS:';
    const COMMA = ':COMMA:';

    public function __construct()
    {
        $this->dictionary = [
            self::WS => [
                '\s+'
            ],
            self::COMMA => [
                ','
            ]
        ];
        parent::__construct();
    }

    protected function createRegex(): void
    {
        $tokenMap = array();
        foreach ($this->dictionary as $name => $values) {
            $tokenMap[$name] = implode('|', $values);
        }
        $this->regex = '((' . implode(')|(', array_values($tokenMap)) . '))';
        $this->tokensArray = array_keys($tokenMap);
    }
}
