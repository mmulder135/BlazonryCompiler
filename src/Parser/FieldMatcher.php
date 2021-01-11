<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Parser;

use BlazonCompiler\Compiler\AST\IR;
use BlazonCompiler\Compiler\Language\Tokens;

class FieldMatcher
{

    /**
     * Which tokens are needed after the key token is found
     * @var array<string, string[]>
     */
    private const NEEDED = [
        Tokens::PARTITION => [Tokens::COLOR], // Need one more color, total =2
        Tokens::PARTED => [Tokens::PARTITION] // Parted indicates a partition
    ];

    /**
     * Tokens that are not needed in the result
     * @var string[]
     */
    private const IGNORE = [
        Tokens::COMMA, Tokens::AND, Tokens::PARTED
    ];

    public static function parseField(IR $ir): bool
    {
        $offset = 0;
        $word = '';
        $queue = [Tokens::COLOR]; // at least one color is always needed
        $lastIndex = array_key_last($ir->getNodes());
        while (!empty($queue)) {
            if ($offset > $lastIndex) {
                // we did not fill all requirements, no field found
                // TODO: add error somewhere
                return false;
            }
            $node = $ir->getNodes()[$offset];
            $token = $node->getToken();

            $word ? $word .= ' '.$token : $word = $token;

            // Remove token from queue if it is there
            $i = array_search($token, $queue);
            if ($i !== false) {
                unset($queue[$i]);
            }

            $offset += strlen($token) + 1;

            if (array_key_exists($token, self::NEEDED)) {
                foreach (self::NEEDED[$token] as $need) {
                    $queue[] = $need;
                }
            }
        }
        // TODO: remove comma's and linking words at the end?
        $ir->addMatch(Tokens::FIELD, $word, 0, self::IGNORE);
        return true;
    }
}
