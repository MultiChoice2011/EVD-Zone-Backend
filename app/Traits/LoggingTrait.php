<?php
namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

trait LoggingTrait
{
    /**
     * Get the migration connection name.
     */
    public function getConnection(): ?string
    {
        return config('telescope.storage.database.connection');
    }

    public function logException(Exception $exception): string
    {
        $logId = Uuid::uuid4()->toString();
        $guard = getActiveGuard();
        $authUserId = Auth::guard($guard)?->user()?->id ?? null;

        $db = DB::connection($this->getConnection());
        $db->table('log_activity')->insert([
            'id' => $logId,
            'guard' => $guard,
            'method' => request()->method(),
            'auth_user_id' => $authUserId,
            'host' => request()->getHost(),
            'ip_address' => request()->ip(),
            'url' => request()->fullUrl(),
            'status' => $exception->getCode(),
            'headers' => json_encode(request()->headers->all()),
            'payload' => json_encode(request()->all()),
            'response' => null,
            'exception' => json_encode([
                "code"          => $exception->getCode(),
                "message"       => $exception->getMessage(),
                "file"          => $exception->getFile(),
                "line"          => $exception->getLine(),
                "trace"         => $exception->getTrace()
            ]),
            'model' => '',
            'model_id' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (App::environment('local')) {
            return $exception->getMessage();
        }

        return "Technical Error, Please contact support team with this code " . $logId;
    }
}
