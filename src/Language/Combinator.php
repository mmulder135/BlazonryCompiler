<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

class Combinator extends Dictionary
{

    public function __construct()
    {
        $this->dictionary = [];
        parent::__construct();
    }

    public function addDictionary(Dictionary $dictionary): void
    {
        // Remove last bracket
        $subSelf = substr($this->regex, 0, -1);
        // Remove first bracket
        $subNew = substr($dictionary->getRegex(), 1);
        if (strlen($subSelf) > 1) {
            // Combine into new regex
            $this->regex = $subSelf.'|'.$subNew;
        } else {
            $this->regex = $subSelf.$subNew;
        }
        // Add new tokens to tokensArray
        $newTokens = $dictionary->getTokens();
        foreach ($newTokens as $token) {
            $this->tokensArray[] = $token;
        }
//        array_push($this->tokensArray, $dictionary->getTokens());
    }

    protected function createRegex(): void
    {
        $this->regex = '()';
        $this->tokensArray = [];
    }
}
