<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

class ShortMatches extends Dictionary
{
    public function __construct()
    {
        $this->dictionary = [
            Tokens::COLOR => [
                [Tokens::METAL],
                [Tokens::TINCTURE],
                [Tokens::FUR]
            ],
            Tokens::PARTITION => [
                [Tokens::DIVISION],
                [Tokens::PER, Tokens::ORDINARY],
                [Tokens::PER, Tokens::ORDINARY, Tokens::SINISTER],
            ]
        ];
        parent::__construct();
    }
    /**
     * @inheritDoc
     */
    protected function createRegex(): void
    {
        $tokenMap = [];
        foreach ($this->dictionary as $name => $values) {
            // values = [[metal],[tincture]]
            // entry = [metal] | entry = [per, ordinary]
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
