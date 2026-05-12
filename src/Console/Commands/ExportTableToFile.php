<?php
namespace EnzanRocket\Foundation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class ExportTableToFile extends Command
{
    protected $signature   = 'rocket:export:table {--format=} {--include_id} {--columns=} {table} {output_path}';

    protected $name        = 'rocket:export:table';

    protected $description = 'Export Database to CSV/Excel';

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
        $tableName  = $this->argument('table');
        $outputPath = $this->argument('output_path');
        $includeId  = $this->option('include_id');

        $format = strtolower($this->option('format'));
        if (!in_array($format, $this->supportFormats)) {
            $format = 'csv';
        }

        $columnNames = [];

        if (!empty($this->option('columns'))) {
            $columnNames = explode(',', $this->option('columns'));
        } else {
            $columnInformation = \DB::select('show columns from '.$tableName);
            if ($includeId) {
                $columnNames[] = 'id';
            }
            foreach ($columnInformation as $entity) {
                if ($entity->Field !== 'id') {
                    $columnNames[] = $entity->Field;
                }
            }
        }
        $rows = [];

        $count  = \DB::table($tableName)->count();
        $limit  = 1000;
        $offset = 0;
        while ($offset < $count) {
            $entities = \DB::table($tableName)->offset($offset)->limit($limit)->orderBy('id', 'asc')->get();
            foreach ($entities as $entity) {
                $row = [];
                foreach ($columnNames as $columnName) {
                    $row[] = $entity->$columnName;
                }
                $rows[] = $row;
            }
            $offset += $limit;
        }

        $exporter = new class($rows, $tableName, $columnNames) implements FromArray, WithHeadings, WithTitle {
            public function __construct(
                private readonly array $rows,
                private readonly string $sheetTitle,
                private readonly array $columnNames
            ) {}

            public function array(): array
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return $this->columnNames;
            }

            public function title(): string
            {
                return $this->sheetTitle;
            }
        };

        $writerType = $format === 'xlsx' ? ExcelFormat::XLSX : ExcelFormat::CSV;
        $content    = Excel::raw($exporter, $writerType);
        $this->files->put($outputPath, $content);

        return true;
    }
}
