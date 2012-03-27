<?php
class TokenBuffer {
    private $tokenQueue;
    private $lexer;

    public function __construct($lexer, $size) {
        $this->lexer = $lexer;

        // init queue
        $this->tokenQueue = array();
        for ($i = 0; $i < $size; $i++) {
            $token = $lexer->getNextToken();
            if ($token == null) {
                break;
            }
            $this->tokenQueue[] = $token;
        }
    }

    public function isEmpty() {
        return count($this->tokenQueue) == 0;
    }

    public function size() {
        return count($this->tokenQueue);
    }

    public function getToken($i = 0) {
        return $this->tokenQueue[$i];
    }

    /**
     * Read the next token from the lexer
     */
    public function readToken() {
        if ($this->isEmpty()) {
            return null;
        }
        $token = array_shift($this->tokenQueue);

        // Add another token to the queue
        $newToken = $this->lexer->getNextToken();
        if ($newToken != null) {
            $this->tokenQueue[] = $newToken;
        }

        return $token;
    }
}

