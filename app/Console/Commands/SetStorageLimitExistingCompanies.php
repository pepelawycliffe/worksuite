<?php

namespace App\Console\Commands;

use App\EmployeeDocs;
use App\FileStorage;
use App\LeadFiles;
use App\ProjectFile;
use App\TaskFile;
use App\TicketFile;
use Illuminate\Console\Command;

class SetStorageLimitExistingCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set-existing-companies-storage-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is for set existing companies storage data for max file storage limit.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $taskFiles = TaskFile::all();
        $this->fileStore($taskFiles, 'task-files');

        $leadFiles = LeadFiles::all();
        $this->fileStore($leadFiles, 'lead-files');

        $ticketFiles = TicketFile::all();
        $this->fileStore($ticketFiles, 'ticket-files');

        $projectFiles = ProjectFile::all();
        $this->fileStore($projectFiles, 'project-files');

        $employeeDocsFiles = EmployeeDocs::all();
        $this->fileStore($employeeDocsFiles, 'employee-docs');

    }

    private function fileStore($files, $folder)
    {
        foreach ($files as $file) {
            try {
                $fileStorage = new FileStorage();
                $fileStorage->name = $file->hashname;
                $fileStorage->size = $file->size;
                $fileStorage->path = $folder . '/' . $file->user_id;
                $fileStorage->company_id = $file->company_id;
                $fileStorage->save();

            } catch (\Exception $e) {
                return false;
            }
        }
    }

}
