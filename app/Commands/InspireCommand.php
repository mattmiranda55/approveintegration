<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class InspireCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'approve:inspire';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->comment(Inspiring::quote());
    }
}
