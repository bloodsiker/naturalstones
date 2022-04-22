<?php

namespace ShortcodeBundle\Processor;

/**
 * Interface ProcessorInterface
 */
interface ProcessorInterface
{
    /**
     * Performs data modification before passing it to template.
     *
     * @param array $data Array of matched elements of shortcode pattern
     *
     * @return array
     */
    public function process(array $data);
}
