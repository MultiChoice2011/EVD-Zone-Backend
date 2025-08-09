<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UniqueIgnoreSoftDeleted implements ValidationRule
{
    protected $mainTable;
    protected $translationTable;
    protected $column;
    protected $ignoreId;
    protected $foreignKey;

    public function __construct($mainTable, $translationTable, $column, $foreignKey, $ignoreId = null)
    {
        $this->mainTable = $mainTable;
        $this->translationTable = $translationTable;
        $this->column = $column;
        $this->ignoreId = $ignoreId;
        $this->foreignKey = $foreignKey;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::table($this->translationTable)
            ->join($this->mainTable, $this->translationTable . '.' . $this->foreignKey, '=', $this->mainTable . '.id')
            ->where($this->translationTable . '.' . $this->column, $value)
            ->whereNull($this->mainTable . '.deleted_at');

        if ($this->ignoreId) {
            $query->where($this->translationTable . '.' . $this->foreignKey , '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail(trans('validation.unique'));
        }
    }
}
