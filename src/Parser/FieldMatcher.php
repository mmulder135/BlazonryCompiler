<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Parser;

use BlazonCompiler\Compiler\AST\IR;
use BlazonCompiler\Compiler\Language\Tokens;

class FieldMatcher
{
    const COMPILERSTEP = "FieldParser";

    /**
     * Which tokens are needed after the key token is found
     * @var array<string, string[]>
     */
    private const NEEDED = [
        Tokens::PARTITION => [Tokens::COLOR], // Need one more color, total =2
        Tokens::PARTED => [Tokens::PARTITION] // Parted indicates a partition
    ];

    private const CAN_CONTAIN = [
        Tokens::PARTED,
        Tokens::PARTITION,
        Tokens::PARTITION_LINE,
        Tokens::COLOR,
        Tokens::SINISTER,
        Tokens::COMMA,
        Tokens::AND,
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
        $lastIndex = array_key_last($ir->getChildren());
        while (!empty($queue)) {
            if ($offset > $lastIndex) {
                // we did not fill all requirements, no field found
                $ir->addMessage(self::COMPILERSTEP,"Could not find a field declaration, missing tokens: ".implode(', ', $queue));
                return false;
            }
            $node = $ir->getChildren()[$offset];
            $token = $node->getToken();

            if (!in_array($token,self::CAN_CONTAIN)) {
                $ir->addMessage(self::COMPILERSTEP,"Trying to parse field, found {$token} instead");
                return false;
            }
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
        $ir->addMatch(Tokens::FIELD, $word, 0, self::IGNORE);
        $ir->fixOffsets();
        return true;
    }
}
