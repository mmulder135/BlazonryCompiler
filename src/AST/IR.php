<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\AST;

class IR
{
    /** @var array<int, Node> */
    private array $nodes;

    /**
     * IR constructor.
     * @param array<int,Term> $nodes
     */
    public function __construct(array $nodes)
    {
        $this->nodes = $nodes;
        $this->fixOffsets();
    }

    /**
     * Match the word(s) starting at offset to token
     *
     * @param string $token
     * @param string $word
     * @param int $offset
     */
    public function addMatch(string $token, string $word, int $offset): void
    {
        $end = $offset + strlen($word);
        $children = [];
        // get all matched tokens
        foreach ($this->nodes as $index => $node) {
            if ($offset <= $index) {
                if ($index <= $end) {
                    unset($this->nodes[$index]);
                    $children[] = $node;
                } else {
                    // Past the end of the match
                    break;
                }
            } //else go to next
        }
        $this->nodes[$offset] = new NonTerm($token, $children);
        ksort($this->nodes);
    }

    /**
     * Removes a node from the IR. DESTRUCTIVE
     * @param int $offset
     */
    public function removeNode(int $offset): void
    {
        unset($this->nodes[$offset]);
    }

    public function fixOffsets(): void
    {
        $offset = 0;
        $new = [];
        foreach ($this->nodes as $node) {
            $new[$offset] = $node;
            // each token is followed by a space
            $offset += strlen($node->getToken()) + 1;
        }
        $this->nodes = $new;
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return
            implode(
                ' ',
                array_map(function ($node) {
                    return $node->getToken();
                },
                $this->nodes)
            );
    }

    /**
     * @return array<int,Node>
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }
}
