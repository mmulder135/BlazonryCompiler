<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

class FieldMatches extends Dictionary
{

    /**
     * FieldMatches constructor.
     */
    public function __construct()
    {
        $this->dictionary = [
            Tokens::FIELD => [
                [Tokens::COLOR],
                [Tokens::PARTITION, Tokens::COLOR, Tokens::COLOR]
            ],
        ];
        parent::__construct();
    }

    protected function createRegex(): void
    {
        $tokenMap = [];
        foreach ($this->dictionary as $name => $values) {
            $options = [];
            foreach ($values as $entry) {
                $options[] = implode(' ', $entry);
            }
            $tokenMap[$name] = implode('|', $options);
        }
        $this->regex = '((' . implode(')|(', array_values($tokenMap)) . '))';
        $this->tokensArray = array_keys($tokenMap);
    }
}
