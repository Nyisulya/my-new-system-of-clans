<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearAllData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:clear {--keep-users : Keep user accounts intact}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all family tree data while keeping system structure intact';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('⚠️  WARNING: This will DELETE ALL family tree data!');
        $this->newLine();

        if (!$this->confirm('Are you absolutely sure you want to continue?')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $this->info('Starting data clearance...');
        $this->newLine();

        try {
            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Clear data in specific order to respect relationships
            $this->clearTable('marriages', '💑 Marriages');
            $this->clearTable('members', '👤 Members');
            $this->clearTable('branches', '🌿 Branches');
            $this->clearTable('families', '🏠 Families');
            $this->clearTable('clans', '🌳 Clans');

            // Optionally clear users
            if (!$this->option('keep-users')) {
                $this->clearTable('users', '👥 Users');
                $this->warn('   Note: You will need to create a new admin user to login');
            } else {
                $this->info('   ✓ Users kept intact');
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $this->newLine();
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->info('✅ All data cleared successfully!');
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->newLine();
            $this->info('Your system is now clean and ready for fresh data entry.');
            $this->newLine();

            if (!$this->option('keep-users')) {
                $this->warn('To create a new admin user, run:');
                $this->warn('   php artisan tinker');
                $this->warn('   Then create a user manually');
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->error('Error clearing data: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Clear a specific table and show progress
     */
    private function clearTable($table, $label)
    {
        try {
            $count = DB::table($table)->count();
            DB::table($table)->truncate();
            $this->info("   ✓ {$label} cleared ({$count} records)");
        } catch (\Exception $e) {
            $this->warn("   ⚠ {$label} - table may not exist");
        }
    }
}
