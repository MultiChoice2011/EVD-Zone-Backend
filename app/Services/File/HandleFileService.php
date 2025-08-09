<?php

namespace App\Services\File;

use App\Enums\File\DirectoryName;
use App\Enums\File\TableColumnMap;
use App\Helpers\FileUpload;
use App\Repositories\Admin\NotificationRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandleFileService
{
    use FileUpload, ApiResponseAble;

    public function __construct(
    )
    {}

    public function destroyFile(array $data): JsonResponse
    {
        try {
            DB::beginTransaction();

            $fileName = pathinfo($data['url'], PATHINFO_FILENAME);

            $publicId = $data['folder_name'] . '/' . $fileName;

            $this->removeFile($publicId);

            $folderName = basename($data['folder_name']);
            $tables = DirectoryName::getTableNames($folderName);

            $this->removeFromDB($tables, $data['url']);

            DB::commit();
            return $this->ApiSuccessResponse(null, 'Deleted successfully...!');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    private function removeFromDB(array $tables, string $url): void
    {
        foreach ($tables as $table) {
            $columnName = TableColumnMap::getColumnName($table);

            if ($columnName) {
                DB::table($table)
                    ->where([
                        [$columnName, '=', $url],
                    ])
                    ->update([
                        $columnName => null
                    ]);
            } else {
                return;
            }
        }
    }

}
