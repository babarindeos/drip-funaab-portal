<?php

namespace App\Console\Commands;

use App\Models\ProposalApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MoveDocuments extends Command
{
    protected $signature = 'drip:move-documents {--dry-run : Show what would be moved without changing anything}';

    protected $description = 'Move already-uploaded proposal documents to the disk set in DRIP_DOCUMENTS_DISK';

    public function handle(): int
    {
        $target = config('drip.documents_disk');
        $dry_run = (bool) $this->option('dry-run');

        if ($target === 'public') {
            $this->info("DRIP_DOCUMENTS_DISK is 'public', so there is nothing to move. Set it to 'local' (and run config:clear) to make documents private.");
            return 0;
        }

        $moved = 0;
        $already_there = 0;
        $missing = 0;
        $failed = 0;

        foreach (ProposalApplication::cursor() as $application) {
            foreach (['proposal_title_file', 'proposal_file', 'college_review'] as $column) {
                $path = $application->{$column};

                if (empty($path)) {
                    continue;
                }

                if (Storage::disk($target)->exists($path)) {
                    $already_there++;
                    continue;
                }

                if (! Storage::disk('public')->exists($path)) {
                    $missing++;
                    $this->warn("Not found anywhere: {$path} (application {$application->uuid})");
                    continue;
                }

                if ($dry_run) {
                    $this->line("Would move: {$path}");
                    $moved++;
                    continue;
                }

                // copy first, and only delete the original once the copy is confirmed
                $stream = Storage::disk('public')->readStream($path);
                Storage::disk($target)->writeStream($path, $stream);

                if (is_resource($stream)) {
                    fclose($stream);
                }

                if (Storage::disk($target)->exists($path)) {
                    Storage::disk('public')->delete($path);
                    $moved++;
                    $this->line("Moved: {$path}");
                } else {
                    $failed++;
                    $this->error("Could not copy {$path} - left where it was.");
                }
            }
        }

        $verb = $dry_run ? 'would be moved' : 'moved';
        $this->newLine();
        $this->info("Done. {$moved} {$verb}, {$already_there} already on '{$target}', {$missing} not found, {$failed} failed.");

        return $failed > 0 ? 1 : 0;
    }
}
