<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

class Ordinaries extends Dictionary
{
    /**
     * Ordinaries constructor.
     */
    public function __construct()
    {
        $this->dictionary = [
            Tokens::FULL_ORDINARY => [
                '(ONE )?ORDINARY( SINISTER)? COLOR'
            ]
        ];
        parent::__construct();
    }

    protected function createRegex(): void
    {
        $tokenMap = [];
        foreach ($this->dictionary as $name => $values) {
            $tokenMap[$name] = implode('|', $values);
        }
        $this->regex = '((' . implode(')|(', array_values($tokenMap)) . '))';
        $this->tokensArray = array_keys($tokenMap);
    }
}
