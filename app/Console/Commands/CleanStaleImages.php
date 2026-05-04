<?php

namespace App\Console\Commands;

use App\Models\Venue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanStaleImages extends Command
{
    protected $signature = 'images:clean {--dry-run : Show what would be removed without making changes}';
    protected $description = 'Remove stale image paths from venues/suites that no longer exist on disk';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $venues = Venue::whereNotNull('images')->get();
        $totalRemoved = 0;

        foreach ($venues as $venue) {
            $images = $venue->images ?? [];
            if (empty($images)) continue;

            $valid = array_values(array_filter($images, function ($path) {
                return Storage::disk('public')->exists($path)
                    || file_exists(public_path('storage/' . $path));
            }));

            $removed = count($images) - count($valid);
            if ($removed > 0) {
                $this->line("[{$venue->type}] {$venue->name}: removing {$removed} stale image(s)");
                if (!$dryRun) {
                    $venue->update(['images' => $valid]);
                }
                $totalRemoved += $removed;
            }
        }

        $action = $dryRun ? 'Would remove' : 'Removed';
        $this->info("{$action} {$totalRemoved} stale image path(s) total.");
        return 0;
    }
}
