<?php
class Annotation {
    private $annotationName;
    private $annotationString;

    public function __construct($name, $string, $attributes) {
        $this->annotationName = $name;
        $this->annotationString = $string;

        // Set fields from attributes
        $vars = get_object_vars($this);
        unset($vars['annotationName']);
        unset($vars['annotationString']);
        $keys = array_keys($vars);
        foreach ($attributes as $k => $v) {
            if (is_int($k)) {
                if ($k < count($keys)) {
                    $k = $keys[$k];
                    $this->{$k} = $v;
                } elseif (is_array($this->value)) {
                    $this->value[] = $v;
                } elseif (isset($this->value)) {
                    $this->value = array($this->value, $v);
                } else {
                    $this->value = $v;
                }
            } else {
                $this->{$k} = $v;
            }
        }
    }

    public function getName() {
        return $this->annotationName;
    }

    public function getAnnotation() {
        return $this->annotationString;
    }

    public function __toString() {
        return '@' . $this->annotationName . $this->annotationString;
    }

    static public function getAnnotationClasses() {
        $annotationClasses = array();
        foreach (get_declared_classes() as $className) {
            if (is_subclass_of($className, 'Annotation')) {
                $annotationClasses[] = $className;
            }
        }
        return $annotationClasses;
    }
}
