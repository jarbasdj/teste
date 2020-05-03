<?php

namespace RestAPI\Models;

use RestAPI\Database\DB;

class Model extends DB
{
    protected function execute($sql, array $types = null, array $params = null)
    {
        $connection = self::getConnection();

        if (!is_null($params) and count($params)) {
            if (count($types) == count($params)) {
                $stmt = $connection->prepare($sql);

                if (!$stmt) {
                    dd("Error: {$connection->errno} - {$connection->error}");
                }

                $_types = implode($types);

                $_params[] = &$_types;

                for ($i = 0; $i < count($types); ++$i) {
                    $_params[] = &$params[$i];
                }

                call_user_func_array([$stmt, 'bind_param'], $_params);

                $exec = $stmt->execute();
                
                if (!$exec) {
                    dd($stmt->error);
                }

                return $stmt;
            } else {
                return false;
            }
        } else {
            return $connection->query($sql);
        }
    }

    protected function getTotalPages(int $databaseCount, int $perPage)
    {
        return  ceil(($databaseCount / $perPage));
    }
}
