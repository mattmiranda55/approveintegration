<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;

class ApproveStripper
{
    /**
     * @var array<string>
     */
    private array $classesToRemove;

    /**
     * @param array<string> $classesToRemove
     */
    public function __construct(array $classesToRemove)
    {
        $this->classesToRemove = $classesToRemove;
    }

    /**
     * Strip elements with specified classes from HTML.
     *
     * @param string $html
     * @return string
     */
    public function strip(string $html): string
    {
        if (empty(trim($html))) {
            return '';
        }

        $dom = new DOMDocument();

        // Suppress warnings for malformed HTML and preserve encoding
        libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="UTF-8">' . $html,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $this->removeElementsByClass($dom);

        $result = $dom->saveHTML();

        // Remove the XML encoding declaration we added
        $result = str_replace('<?xml encoding="UTF-8">', '', $result);

        return trim($result);
    }

    /**
     * Remove all elements that have any of the classes to remove.
     *
     * @param DOMDocument $dom
     * @return void
     */
    private function removeElementsByClass(DOMDocument $dom): void
    {
        $xpath = new DOMXPath($dom);

        foreach ($this->classesToRemove as $class) {
            // XPath to match elements containing the class (handles multiple classes)
            $query = sprintf(
                "//*[contains(concat(' ', normalize-space(@class), ' '), ' %s ')]",
                $class
            );

            $elements = $xpath->query($query);

            if ($elements === false) {
                continue;
            }

            // Collect elements first to avoid modifying DOM while iterating
            $toRemove = [];
            foreach ($elements as $element) {
                $toRemove[] = $element;
            }

            // Remove collected elements
            foreach ($toRemove as $element) {
                $element->parentNode?->removeChild($element);
            }
        }
    }
}
