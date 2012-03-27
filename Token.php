<?php
class Token {
    const LPAREN = 'LPAREN';
    const RPAREN = 'RPAREN';
    const COMMA = 'COMMA';
    const KEYWORD = 'KEYWORD';
    const EQUAL = 'EQUAL';
    const NUMBER = 'NUMBER';
    const VARIABLE = 'VARIABLE';
    const STRING_LITERAL = 'STRING_LITERAL';

    public $type;
    public $text;

    public function __construct($tokenType, $text) {
        $this->type = $tokenType;
        $this->text = $text;
    }

    public function __toString() {
        return $this->type . ': ' . $this->text;
    }
}
