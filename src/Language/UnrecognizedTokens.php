<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

class UnrecognizedTokens extends Dictionary
{

    /**
     * UnrecognizedTokens constructor.
     */
    public function __construct()
    {
        $this->dictionary = [
            Tokens::STR => [
                '\w+'
            ]
        ];
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function createRegex(): void
    {
        $this->regex = "((" . $this->dictionary[Tokens::STR][0] . "))";
        $this->tokensArray = [Tokens::STR];
    }
}
