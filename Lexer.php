<?php
require_once dirname(__FILE__) . '/Token.php';

class AnnotationLexer {
    private $string;
    private $length; // Length of $string
    private $index = 0;

    public function __construct($string) {
        $this->string = $string;
        $this->length = strlen($string);
    }

    private function error($message) {
        throw new ParseException($message);
    }

    private function skip() {
        $this->index++;
    }

    private function lookAhead($i) {
        $lookAt = $this->index + $i - 1;
        if ($lookAt >= $this->length) {
            return "\n";
        }
        return $this->string[$lookAt];
    }

    private function matchChar($char) {
        if ($this->lookAhead(1) == $char) {
            $this->index++;
        } else {
            $this->error("Excepted '$char' but got ' {$this->lookAhead(1)}'");
        }
    }

    private function isDigit($digit) {
        return ($digit >= '0' && $digit <= '9');
    }

    private function isLetter($letter) {
        return (($letter >= 'a' && $letter <= 'z') ||
                ($letter >= 'A' && $letter <= 'Z'));
    }

    private function isKeywordStart($char) {
        return ($this->isLetter($char) ||
                $char == '_');
    }

    private function isKeyword($char) {
        return ($this->isDigit($char) ||
                $this->isKeywordStart($char));
    }

    private function matchString($string) {
        for ($i = 0, $n = strlen($string); $i < $n; $i++) {
            $this->matchChar($string[$i]);
        }
    }

    private function matchUtil($char) {
        $startIndex = $this->index;
        while (($this->lookAhead(1) != $char) && ($this->index < $this->length)) {
            $this->index++;
        }
        $endIndex = $this->index;
        $length = $endIndex - $startIndex;
        return substr($this->string, $startIndex, $length);
    }

    public function getNextToken() {
        $token = null;
        while ($this->lookAhead(1) == ' ' || $this->lookAhead(1) == '\t') {
            $this->skip(); // ignore whitespace
        }
        switch ($this->lookAhead(1)) {
        case '(':
            $token = $this->matchLParen();
            break;
        case ')':
            $token = $this->matchRParen();
            break;
        case '=':
            if ($this->lookAhead(2) == '>') {
                $token = $this->matchDoubleArrow();
            } else {
                $token = $this->matchEqual();
            }
            break;
        case ',':
            $token = $this->matchComma();
            break;
        case '"': case "'":
            $token = $this->matchLiteral();
            break;
        case '$':
            $token = $this->matchVar();
            break;
        }
        if (!$token) {
            $char = $this->lookAhead(1);
            if ($this->isDigit($char)) {
                $token = $this->matchNum();
            } elseif ($this->isKeywordStart($char)) {
                $token = $this->matchKeyword();
            }
        }
        return $token;
    }

    private function matchComma() {
        $this->matchChar(',');
        return new Token(Token::COMMA, ',');
    }

    private function matchLParen() {
        $this->matchChar('(');
        return new Token(Token::LPAREN, '(');
    }

    private function matchRParen() {
        $this->matchChar(')');
        return new Token(Token::RPAREN, ')');
    }

    private function matchEqual() {
        $this->matchChar('=');
        return new Token(Token::EQUAL, '=');
    }

    private function matchNum() {
        $startIndex = $this->index;
        $decimal = false;
        while ($this->isDigit($this->lookAhead(1)) ||  $this->lookAhead(1) == '.') {
            if ($decimal && $this->lookAhead(1) == '.') {
                error("Unexcepted '.' character");
            } elseif ($this->lookAhead(1) == '.') {
                $decimal = true;
            }
            $this->index++;
        }
        $endIndex = $this->index;
        $length = $endIndex - $startIndex;
        $number = substr($this->string, $startIndex, $length);
        return new Token(Token::NUMBER, $number);
    }

    private function _matchId() {
        $startIndex = $this->index;
        while ($this->isKeyword($this->lookAhead(1))) {
            $this->index++;
        }
        $endIndex = $this->index;
        $length = $endIndex - $startIndex;
        $id = substr($this->string, $startIndex, $length);
        return $id;
    }

    private function matchKeyword() {
        return new Token(Token::KEYWORD, $this->_matchId());
    }

    private function matchVar() {
        $this->matchChar('$');
        return new Token(Token::VARIABLE, $this->_matchId());
    }

    private function matchLiteral() {
        $quoteChar = $this->lookAhead(1);
        if ($quoteChar != '"' && $quoteChar != "'") {
            $this->error("Invalid quote character $quoteChar");
        }
        $this->matchChar($quoteChar);
        $literal = $this->matchUtil($quoteChar);
        $this->matchChar($quoteChar);
        return new Token(Token::STRING_LITERAL, $literal);
    }
}
