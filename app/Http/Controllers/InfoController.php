<?php

namespace App\Http\Controllers;

use App\DTO\ServerInfoDTO;
use App\DTO\ClientInfoDTO;
use App\DTO\DatabaseInfoDTO;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function serverInfo()
    {
        $dto = new ServerInfoDTO(
            phpVersion: phpversion(),
            phpSapi: php_sapi_name(),
            os: PHP_OS_FAMILY
        );

        return response()->json($dto->toArray());
    }

    public function clientInfo(Request $request)
    {
        $dto = new ClientInfoDTO(
            ipAddress: $request->ip(),
            userAgent: $request->userAgent() ?? 'unknown'
        );

        return response()->json($dto->toArray());
    }

    public function databaseInfo()
    {
        $connection = DB::connection();
        $pdo = $connection->getPdo();

        $driver = $connection->getDriverName();
        $database = $connection->getDatabaseName();
        $serverVersion = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);

        $dto = new DatabaseInfoDTO(
            driver: $driver,
            database: $database,
            serverVersion: $serverVersion
        );

        return response()->json($dto->toArray());
    }
}
