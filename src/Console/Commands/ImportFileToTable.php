<?php
namespace EnzanRocket\Foundation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class ImportFileToTable extends Command
{
    protected $signature   = 'rocket:import:file {--include_id} {--columns=} {table} {file_path}';

    protected $name        = 'rocket:import:file';

    protected $description = 'Import Database to CSV/TSV/Excel';

    /** @var \Illuminate\Filesystem\Filesystem */
    protected $files;

    protected $supportFormats = ['csv', 'xlsx'];

    /**
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(
        Filesystem $files
    ) {
        $this->files = $files;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $tableName = $this->argument('table');
        $filePath  = $this->argument('file_path');

        $includeId = $this->option('include_id');

        $columnNames = [];

        $columnInformation = \DB::select('show columns from '.$tableName);
        if (!empty($this->option('columns'))) {
            $columnNames = explode(',', $this->option('columns'));
        } else {
            if ($includeId) {
                $columnNames[] = 'id';
            }
            foreach ($columnInformation as $entity) {
                if ($entity->Field !== 'id') {
                    $columnNames[] = $entity->Field;
                }
            }
        }
        $defaultValues = [];
        foreach ($columnInformation as $entity) {
            if ($entity->Null === 'NO') {
                $type = $entity->Type;
                if (str_starts_with($type, 'varchar') || str_starts_with($type, 'char') || str_starts_with($type, 'text')) {
                    $defaultValues[$entity->Field] = '';
                } else {
                    $defaultValues[$entity->Field] = 0;
                }
            }
        }

        $importer = new class($tableName, $columnNames, $defaultValues) implements ToCollection, WithChunkReading, WithHeadingRow {
            public function __construct(
                private readonly string $tableName,
                private readonly array $columnNames,
                private readonly array $defaultValues
            ) {}

            public function collection(Collection $rows): void
            {
                foreach ($rows as $row) {
                    $data = [];
                    foreach ($this->columnNames as $columnName) {
                        $value = $row[$columnName] ?? null;
                        if (!empty($value)) {
                            $data[$columnName] = $value;
                        } elseif (array_key_exists($columnName, $this->defaultValues)) {
                            $data[$columnName] = $this->defaultValues[$columnName];
                        }
                    }
                    if (!empty($data)) {
                        \DB::table($this->tableName)->insert($data);
                    }
                }
            }

            public function chunkSize(): int
            {
                return 250;
            }
        };

        Excel::import($importer, $filePath);

        return true;
    }
}
