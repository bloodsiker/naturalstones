<?php

namespace ShortcodeBundle\Processor;

interface ProcessorInterface
{
    /**
     * Performs data modification before passing it to template.
     *
     * @param array<int|string, mixed> $data array of matched elements of shortcode pattern
     *
     * @return array<int|string, mixed>
     */
    public function process(array $data): array;
}