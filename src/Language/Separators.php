<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

class Separators extends Dictionary
{
    /**
     * Separators constructor.
     */
    public function __construct()
    {
        $this->dictionary = [
            Tokens::COMMA => [
                ',',';'
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
