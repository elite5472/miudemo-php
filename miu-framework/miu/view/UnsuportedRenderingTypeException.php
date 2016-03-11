<?php
namespace \miu\View;

/**
 * Thrown when an IRenderer is told to render as something it doesn't support.
 * 
 * For instance, telling an HTMLView to render as 'xml' will throw this exception.
 * 
 * @author Guillermo Borges
 */
class UnsuportedRenderingTypeException extends \Exception { }
?>