<?php

namespace App\Commands;

use App\Services\SelectorMatcher;
use App\Services\PageMatchResults;
use Illuminate\Console\Command;

use function Laravel\Prompts\error;
use function Laravel\Prompts\table;

class AnalyzeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analyze
                            {--product= : Product page URL to analyze}
                            {--cart= : Cart page URL to analyze}
                            {--gallery= : Gallery/collection page URL to analyze}
                            {--json : Output results as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze eCommerce pages for APPROVE integration selectors';

    /**
     * Execute the console command.
     */
    public function handle(SelectorMatcher $matcher): int
    {
        $productUrl = $this->option('product');
        $cartUrl = $this->option('cart');
        $galleryUrl = $this->option('gallery');
        $jsonOutput = $this->option('json');

        if (!$productUrl && !$cartUrl && !$galleryUrl) {
            error('Please provide at least one URL to analyze.');
            $this->line('');
            $this->line('Usage:');
            $this->line('  php application analyze --product=URL');
            $this->line('  php application analyze --cart=URL');
            $this->line('  php application analyze --gallery=URL');
            $this->line('  php application analyze --product=URL --cart=URL --gallery=URL');
            return self::FAILURE;
        }

        $results = [];

        if ($productUrl) {
            $results['product'] = $matcher->analyzeProductPage($productUrl);
        }

        if ($cartUrl) {
            $results['cart'] = $matcher->analyzeCartPage($cartUrl);
        }

        if ($galleryUrl) {
            $results['gallery'] = $matcher->analyzeGalleryPage($galleryUrl);
        }

        if ($jsonOutput) {
            $this->outputJson($results, $matcher);
        } else {
            $this->outputFormatted($results, $matcher);
        }

        return self::SUCCESS;
    }

    /**
     * Output results as JSON.
     */
    private function outputJson(array $results, SelectorMatcher $matcher): void
    {
        $output = [];

        foreach ($results as $type => $pageResults) {
            $output[$type] = [
                'url' => $pageResults->url,
                'page_type' => $pageResults->pageType,
                'success' => $pageResults->success,
                'error' => $pageResults->error,
                'selectors' => $pageResults->success
                    ? $matcher->getBestMatchPerCategory($pageResults)
                    : null,
            ];
        }

        $this->line(json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Output results in formatted table view.
     */
    private function outputFormatted(array $results, SelectorMatcher $matcher): void
    {
        foreach ($results as $type => $pageResults) {
            $this->newLine();
            $this->line("<fg=blue>═══════════════════════════════════════════════════════════</>");
            $this->line("<fg=white;options=bold>" . strtoupper($type) . " PAGE</>");
            $this->line("<fg=gray>URL:</> {$pageResults->url}");
            $this->line("<fg=blue>═══════════════════════════════════════════════════════════</>");

            if (!$pageResults->success) {
                error("Error: {$pageResults->error}");
                continue;
            }

            $this->displayMatchResults($pageResults, $matcher);
        }
    }

    /**
     * Display match results for a page.
     */
    private function displayMatchResults(PageMatchResults $results, SelectorMatcher $matcher): void
    {
        $bestMatches = $matcher->getBestMatchPerCategory($results);

        $tableData = [];

        foreach ($results->matches as $category => $matchList) {
            $categoryName = str_replace('_', ' ', ucwords($category, '_'));
            $bestSelector = $bestMatches[$category];

            if ($bestSelector) {
                // Find the match details
                $matchDetails = null;
                foreach ($matchList as $match) {
                    if ($match->selector === $bestSelector) {
                        $matchDetails = $match;
                        break;
                    }
                }

                $countStr = $matchDetails && $matchDetails->elementCount > 1
                    ? " ({$matchDetails->elementCount})"
                    : '';

                $textPreview = '';
                if ($matchDetails && $matchDetails->elementText) {
                    $text = strlen($matchDetails->elementText) > 40
                        ? substr($matchDetails->elementText, 0, 40) . '...'
                        : $matchDetails->elementText;
                    $textPreview = $text;
                }

                $tableData[] = [
                    "<fg=green>✓</>",
                    $categoryName,
                    $bestSelector . $countStr,
                    $textPreview,
                ];
            } else {
                $tableData[] = [
                    "<fg=yellow>○</>",
                    $categoryName,
                    '<fg=gray>No match</>',
                    '',
                ];
            }
        }

        table(
            headers: ['', 'Category', 'Selector', 'Preview'],
            rows: $tableData
        );
    }
}
