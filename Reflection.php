<?php
/*
 * @author <a href="cameronz@bundaberg.qld.gov.au">Cameron Zemek</a>
 */
require_once 'Parser.php';

$GLOBALS['_Annotation_Parser'] = new AnnotationParser();

function Annotation_build($reflector) {
    global $_Annotation_Parser;
    $comment = $reflector->getDocComment();
    return $_Annotation_Parser->parse($comment);
}

class ReflectionAnnotatedClass extends ReflectionClass {
    private $annotations;

    public function __construct($className) {
        parent::__construct($className);
        $this->annotations = Annotation_build($this);
    }

    public function hasAnnotation($annotation) {
        return isset($this->annotations[$annotation]);
    }

    public function getAnnotation($annotation) {
        return $this->annotations[$annotation];
    }

    public function getAnnotations() {
        return $this->annotations;
    }

    public function getConstructor() {
        return new ReflectionAnnotatedMethod(parent::getConstructor());
    }

    protected function createReflectionMethod($method) {
        return new ReflectionAnnotatedMethod($this->getName(), $method->getName());
    }

    public function getMethod($name) {
        return $this->createReflectionMethod(parent::getMethod($name));
    }

    public function getMethods() {
        $methods = array();
        foreach (parent::getMethods() as $method) {
            $methods[] = $this->createReflectionMethod($method);
        }
        return $methods;
    }

    protected function createReflectionProperty($property) {
        return new ReflectionAnnotatedProperty($this->getName(), $property->getName());
    }

    public function getProperty($name) {
        return $this->createReflectionProperty(parent::getProperty($name));
    }

    public function getProperties() {
        $properties = array();
        foreach (parent::getProperties() as $property) {
            $properties[] = $this->createReflectionProperty($property);
        }
        return $properties;
    }

    protected function createReflectionClass($class) {
        return new ReflectionAnnotatedClass($class->getName());
    }

    public function getInterfaces() {
        $interfaces = array();
        foreach (parent::getInterfaces() as $interface) {
            $interfaces[] = $this->createReflectionClass($interface);
        }
        return $interfaces;
    }

    public function getParentClass() {
        return $this->createReflectionClass(parent::getParentClass());
    }
}

class ReflectionAnnotatedMethod extends ReflectionMethod {
    private $annotations;

    public function __construct($class, $methodName) {
        parent::__construct($class, $methodName);
        $this->annotations = Annotation_build($this);
    }

    public function hasAnnotation($annotation) {
        return isset($this->annotations[$annotation]);
    }

    public function getAnnotation($annotation) {
        return $this->annotations[$annotation];
    }

    public function getAnnotations() {
        return $this->annotations;
    }

    public function getDeclaringClass() {
        $class = parent::getDeclaringClass();
        return new ReflectionAnnotatedClass($class->getName());
    }

    public function getParameters() {
        $parameters = array();
        foreach (parent::getParameters() as $parameter) {
            $function = array($this->getDeclaringClass()->getName(), $this->getName());
            $parameters[] = new ReflectionAnnotatedParameter($function, $parameter->getName());
        }
        return $parameters;
    }
}

class ReflectionAnnotatedProperty extends ReflectionProperty {
    private $annotations;

    public function __construct($class, $propertyName) {
        parent::__construct($class, $propertyName);
        $this->annotations = Annotation_build($this);
    }

    public function hasAnnotation($annotation) {
        return isset($this->annotations[$annotation]);
    }

    public function getAnnotation($annotation) {
        return $this->annotations[$annotation];
    }

    public function getAnnotations() {
        return $this->annotations;
    }

    public function getDeclaringClass() {
        $class = parent::getDeclaringClass();
        return new ReflectionAnnotatedClass($class->getName());
    }
}

class ReflectionAnnotatedFunction extends ReflectionFunction {
    private $annotations;

    public function __construct($functionName) {
        parent::__construct($functionName);
        $this->annotations = Annotation_build($this);
    }

    public function hasAnnotation($annotation) {
        return isset($this->annotations[$annotation]);
    }

    public function getAnnotation($annotation) {
        return $this->annotations[$annotation];
    }

    public function getAnnotations() {
        return $this->annotations;
    }

    public function getParameters() {
        $parameters = array();
        foreach (parent::getParameters() as $parameter) {
            $parameters[] = new ReflectionAnnotatedParameter($this->getName(), $parameter->getName());
        }
        return $parameters;
    }
}

class ReflectionAnnotatedParameter extends ReflectionParameter {
    public function getDeclaringClass() {
        $class = parent::getDeclaringClass();
        return new ReflectionAnnotatedClass($class->getName());
    }

    public function getClass() {
        $class = parent::getClass();
        return new ReflectionAnnotatedClass($class->getName());
    }
}
