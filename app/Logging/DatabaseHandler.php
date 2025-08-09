<?php

namespace App\Logging;

use Illuminate\Support\Facades\DB;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;

class DatabaseHandler extends AbstractProcessingHandler
{

    protected $connection;
    protected $table;

    public function __construct(int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        $this->connection = config('telescope.storage.database.connection');
        $this->table = 'logs';
        parent::__construct($level, $bubble);
    }

    /**
     * @throws \Throwable
     */
    protected function write(LogRecord $record): void
    {
        DB::connection($this->connection)->table($this->table)->insert([
            'channel' => $record->channel,
            'level' => $record->level->getName(),
            'message' => $record->message,
            'context' => json_encode($record->context ?? []),
            'remote_addr' => request()->ip(),
            'user_id' => auth()->id() ?? null,
            'created_at' => now(),
        ]);
    }

}
