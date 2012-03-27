<?php
require_once 'Reflection.php';

class ContactAnnotation extends Annotation {
    public $firstName;
    public $lastName;
    public $email = "noreply@bundaberg.qld.gov.au";
}

/**
 * @Contact("Cameron", "Zemek", email = "cameronz@bundaberg.qld.gov.au")
 */
class Example {
}

$class = new ReflectionAnnotatedClass('Example');
$contact = $class->getAnnotation('Contact');
print_r($contact);
