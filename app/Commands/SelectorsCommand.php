<?php

namespace App\Commands;

use App\Services\SelectorMatcher;
use Illuminate\Console\Command;

use function Laravel\Prompts\table;

class SelectorsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'selectors
                            {type? : Page type (product, cart, gallery, mini_cart)}
                            {--json : Output results as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available CSS selectors by page type';

    /**
     * Execute the console command.
     */
    public function handle(SelectorMatcher $matcher): int
    {
        $selectors = $matcher->getSelectors();
        $type = $this->argument('type');

        if ($type) {
            if (!isset($selectors[$type])) {
                $this->error("Unknown page type: {$type}");
                $this->line('Available types: ' . implode(', ', array_keys($selectors)));
                return self::FAILURE;
            }

            $selectors = [$type => $selectors[$type]];
        }

        if ($this->option('json')) {
            $this->line(json_encode($selectors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return self::SUCCESS;
        }

        foreach ($selectors as $pageType => $categories) {
            $this->newLine();
            $this->line("<fg=blue;options=bold>" . strtoupper($pageType) . " SELECTORS</>");
            $this->line(str_repeat('─', 60));

            foreach ($categories as $category => $selectorList) {
                $categoryName = str_replace('_', ' ', ucwords($category, '_'));
                $this->line("<fg=cyan>{$categoryName}:</>");

                foreach ($selectorList as $selector) {
                    $this->line("  • {$selector}");
                }

                $this->newLine();
            }
        }

        return self::SUCCESS;
    }
}
