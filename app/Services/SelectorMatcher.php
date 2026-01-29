<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use DOMElement;
use Symfony\Component\CssSelector\CssSelectorConverter;

class SelectorMatcher
{
    private HtmlScraper $scraper;
    private CssSelectorConverter $cssConverter;
    private array $selectors;

    public function __construct(?HtmlScraper $scraper = null, ?array $selectors = null)
    {
        $this->scraper = $scraper ?? new HtmlScraper();
        $this->cssConverter = new CssSelectorConverter();
        $this->selectors = config('approve-selectors');
    }

    /**
     * Get the selectors configuration.
     */
    public function getSelectors(): array
    {
        return $this->selectors;
    }

    /**
     * Fetch and analyze a product page for APPROVE integration selectors.
     */
    public function analyzeProductPage(string $url): PageMatchResults
    {
        return $this->analyzePage($url, 'product');
    }

    /**
     * Fetch and analyze a cart page for APPROVE integration selectors.
     */
    public function analyzeCartPage(string $url): PageMatchResults
    {
        return $this->analyzePage($url, 'cart');
    }

    /**
     * Fetch and analyze a gallery/collection page for APPROVE integration selectors.
     */
    public function analyzeGalleryPage(string $url): PageMatchResults
    {
        return $this->analyzePage($url, 'gallery');
    }

    /**
     * Generic page analyzer.
     */
    private function analyzePage(string $url, string $pageType): PageMatchResults
    {
        $result = $this->scraper->getHtml($url);

        if ($result['error'] !== null) {
            return new PageMatchResults(
                url: $url,
                pageType: $pageType,
                success: false,
                error: $result['error']
            );
        }

        $selectorsForType = $this->selectors[$pageType] ?? [];
        $matches = $this->matchSelectorsAgainstHtml($result['html'], $selectorsForType);

        return new PageMatchResults(
            url: $url,
            pageType: $pageType,
            success: true,
            matches: $matches
        );
    }

    /**
     * Match a dictionary of selector categories against HTML content.
     *
     * @param string $html
     * @param array<string, array<string>> $selectorsDict
     * @return array<string, array<SelectorMatch>>
     */
    public function matchSelectorsAgainstHtml(string $html, array $selectorsDict): array
    {
        $dom = $this->parseHtml($html);

        if ($dom === null) {
            return [];
        }

        $xpath = new DOMXPath($dom);
        $results = [];

        foreach ($selectorsDict as $category => $selectors) {
            $categoryMatches = [];

            foreach ($selectors as $selector) {
                $match = $this->matchSelector($xpath, $selector);
                $categoryMatches[] = $match;
            }

            $results[$category] = $categoryMatches;
        }

        return $results;
    }

    /**
     * Parse HTML into a DOMDocument.
     */
    private function parseHtml(string $html): ?DOMDocument
    {
        $dom = new DOMDocument();

        libxml_use_internal_errors(true);
        $success = $dom->loadHTML(
            '<?xml encoding="UTF-8">' . $html,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        return $success ? $dom : null;
    }

    /**
     * Try to match a CSS selector against the DOM using XPath.
     */
    private function matchSelector(DOMXPath $xpath, string $selector): SelectorMatch
    {
        try {
            $xpathQuery = $this->cssToXPath($selector);

            if ($xpathQuery === null) {
                return new SelectorMatch(selector: $selector, matched: false);
            }

            $elements = $xpath->query($xpathQuery);

            if ($elements === false || $elements->length === 0) {
                return new SelectorMatch(selector: $selector, matched: false);
            }

            $firstElement = $elements->item(0);
            $elementText = null;
            $elementTag = null;

            if ($firstElement instanceof DOMElement) {
                $elementTag = $firstElement->tagName;
                $textContent = trim($firstElement->textContent);
                $elementText = mb_substr($textContent, 0, 100);
            }

            return new SelectorMatch(
                selector: $selector,
                matched: true,
                elementTag: $elementTag,
                elementText: $elementText ?: null,
                elementCount: $elements->length
            );
        } catch (\Exception $e) {
            return new SelectorMatch(selector: $selector, matched: false);
        }
    }

    /**
     * Convert a CSS selector to XPath.
     * Uses Symfony's CssSelector for complex selectors, with fallback for custom elements.
     */
    private function cssToXPath(string $selector): ?string
    {
        try {
            // Handle descendant selectors - use the full selector
            return $this->cssConverter->toXPath($selector);
        } catch (\Exception $e) {
            // Fallback for custom elements or invalid selectors
            // Try treating it as a simple tag name
            $selector = trim($selector);

            if (preg_match('/^[a-zA-Z][a-zA-Z0-9-]*$/', $selector)) {
                // It's a simple tag name (possibly a custom element)
                return "//{$selector}";
            }

            return null;
        }
    }

    /**
     * Extract the first matching selector for each category, or null if no match.
     *
     * @param PageMatchResults $results
     * @return array<string, string|null>
     */
    public function getBestMatchPerCategory(PageMatchResults $results): array
    {
        $bestMatches = [];

        foreach ($results->matches as $category => $matchList) {
            $bestMatch = null;

            foreach ($matchList as $match) {
                if ($match->matched) {
                    $bestMatch = $match->selector;
                    break;
                }
            }

            $bestMatches[$category] = $bestMatch;
        }

        return $bestMatches;
    }
}

/**
 * Represents a matched selector with its found element info.
 */
class SelectorMatch
{
    public function __construct(
        public readonly string $selector,
        public readonly bool $matched,
        public readonly ?string $elementTag = null,
        public readonly ?string $elementText = null,
        public readonly int $elementCount = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'selector' => $this->selector,
            'matched' => $this->matched,
            'element_tag' => $this->elementTag,
            'element_text' => $this->elementText,
            'element_count' => $this->elementCount,
        ];
    }
}

/**
 * Results from matching selectors against a page.
 */
class PageMatchResults
{
    /**
     * @param array<string, array<SelectorMatch>> $matches
     */
    public function __construct(
        public readonly string $url,
        public readonly string $pageType,
        public readonly bool $success,
        public readonly ?string $error = null,
        public readonly array $matches = [],
    ) {}

    public function toArray(): array
    {
        $matchesArray = [];

        foreach ($this->matches as $category => $matchList) {
            $matchesArray[$category] = array_map(
                fn(SelectorMatch $m) => $m->toArray(),
                $matchList
            );
        }

        return [
            'url' => $this->url,
            'page_type' => $this->pageType,
            'success' => $this->success,
            'error' => $this->error,
            'matches' => $matchesArray,
        ];
    }
}
