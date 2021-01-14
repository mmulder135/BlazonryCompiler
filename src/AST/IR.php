<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\AST;

use BlazonCompiler\Compiler\Language\Tokens;

class IR extends NonTerm
{
    /** @var array<string>  */
    private array $messages;

    /**
     * IR constructor.
     * @param array<int,Node> $nodes
     */
    public function __construct(array $nodes)
    {
        parent::__construct(Tokens::SHIELD, $nodes);
        $this->fixOffsets();
        $this->messages = [];
    }

    /**
     * Match the word(s) starting at offset to token
     *
     * @param string $token
     * @param string $word
     * @param int $offset
     * @param string[] $ignoreTokens
     */
    public function addMatch(string $token, string $word, int $offset, array $ignoreTokens = []): void
    {
        $end = $offset + strlen($word);
        $children = [];
        // get all matched tokens
        foreach ($this->children as $index => $node) {
            if ($offset <= $index) {
                if ($index <= $end) {
                    unset($this->children[$index]);
                    if (!in_array($node->getToken(), $ignoreTokens)) {
                        $children[] = $node;
                    }
                } else {
                    // Past the end of the match
                    break;
                }
            } //else go to next
        }
        $this->children[$offset] = new NonTerm($token, $children);
        ksort($this->children);
    }

    /**
     * Fix the offsets of the nodes,
     * should be done after any editing
     */
    public function fixOffsets(): void
    {
        $offset = 0;
        $new = [];
        foreach ($this->children as $node) {
            $new[$offset] = $node;
            // each token is followed by a space
            $offset += strlen($node->getToken()) + 1;
        }
        $this->children = $new;

        //fix text, get rid of text from deleted nodes
        $this->text = '';
        foreach ($this->children as $child) {
            if ($this->text == '') {
                $this->text = $child->getText();
            } else {
                $this->text .= ' '.$child->getText();
            }
        }
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
                $this->children)
            );
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param string $compiler_step
     * @param string $message
     */
    public function addMessage(string $compiler_step, string $message): void
    {
        $this->messages[] = $compiler_step . ': ' . $message;
    }
}
