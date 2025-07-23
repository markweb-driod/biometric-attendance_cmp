<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncStudentsToClasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-students-to-classes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing students to classes...');
        $count = 0;
        foreach (\App\Models\Classroom::all() as $class) {
            $students = \App\Models\Student::where('academic_level', $class->level)
                ->where('department', 'Computer Science')
                ->pluck('id');
            $class->students()->sync($students);
            $this->info("Class '{$class->class_name}' (Level: {$class->level}) synced with " . count($students) . " students.");
            $count++;
        }
        $this->info("Sync complete. {$count} classes processed.");
        return 0;
    }
}
