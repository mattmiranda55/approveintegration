<?php

namespace App\Commands;

use App\Services\SelectorMatcher;
use Illuminate\Console\Command;

class AnalyzeGalleryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analyze:gallery
                            {url : The gallery/collection page URL to analyze}
                            {--json : Output results as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze a gallery/collection page for APPROVE integration selectors';

    /**
     * Execute the console command.
     */
    public function handle(SelectorMatcher $matcher): int
    {
        $url = $this->argument('url');

        $this->info('Fetching and analyzing gallery page...');
        $results = $matcher->analyzeGalleryPage($url);

        if (!$results->success) {
            $this->error("Error: {$results->error}");
            return self::FAILURE;
        }

        if ($this->option('json')) {
            $this->line(json_encode([
                'url' => $results->url,
                'page_type' => $results->pageType,
                'success' => $results->success,
                'selectors' => $matcher->getBestMatchPerCategory($results),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->displayResults($results, $matcher);
        }

        return self::SUCCESS;
    }

    private function displayResults($results, SelectorMatcher $matcher): void
    {
        $bestMatches = $matcher->getBestMatchPerCategory($results);

        $this->newLine();
        $this->info('Matched Selectors:');

        foreach ($bestMatches as $category => $selector) {
            $categoryName = str_replace('_', ' ', ucwords($category, '_'));

            if ($selector) {
                $this->line("  <fg=green>✓</> {$categoryName}: <fg=white>{$selector}</>");
            } else {
                $this->line("  <fg=yellow>○</> {$categoryName}: <fg=gray>No match</>");
            }
        }
    }
}
