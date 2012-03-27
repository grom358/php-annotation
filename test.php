<?php
require_once 'Reflection.php';

/**
 * Example of allowed annotations
 *
 * @persist
 * @author Cameron Zemek
 * @SingleValue("hello world")
 * @MultiValue("hello", "world")
 * @KeywordValue(type = "text", label = "Hello")
 * @PositionalAndKeyword("text", label = "Hello")
 * @Complex("hello", 3,
 *     (
 *     fruit = "apple",
 *     veggie = "pumpkin",
 *     age = 20,
 *     say = ("hello", who = "world")
 *     )
 *  )
 */
class Example {
    /**
     * @dbType TEXT
     */
    public $description;
}

$reflectClass = new ReflectionAnnotatedClass('Example');
print_r($reflectClass->getAnnotations());
