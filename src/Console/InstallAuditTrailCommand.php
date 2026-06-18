<?php

namespace NathanDeBarros\AuditTrail\Console;

use Illuminate\Console\Command;

class InstallAuditTrailCommand extends Command
{
    protected $signature = 'audit-trail:install {--force : Overwrite already published files}';

    protected $description = 'Publish the Laravel Audit Trail Starter configuration, migration, and assets.';

    public function handle(): int
    {
        $force = (bool) $this->option('force');

        $this->components->info('Publishing Laravel Audit Trail Starter files...');

        $this->call('vendor:publish', [
            '--tag' => 'audit-trail-config',
            '--force' => $force,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'audit-trail-migrations',
            '--force' => $force,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'audit-trail-assets',
            '--force' => $force,
        ]);

        $this->newLine();
        $this->components->info('Audit Trail installed successfully.');
        $this->line('Next steps:');
        $this->line('1. Review config/audit-trail.php');
        $this->line('2. Run php artisan migrate');
        $this->line('3. Visit /' . config('audit-trail.route_prefix', 'audit-trail'));

        return self::SUCCESS;
    }
}
