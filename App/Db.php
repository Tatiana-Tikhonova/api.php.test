<?php

namespace App;

use PDO;
use App\MyException;
use App\Models\ErrorHandler;

class Db
{
    use \App\SingletonTrait;
    protected $dbh;
    public $conf;

    protected function __construct()

    {
        $this->conf = json_decode(file_get_contents(dirname(__DIR__) . '/db-config.json'), true);
        $this->err = ErrorHandler::unique();

        try {
            $this->dbh = new PDO(
                'mysql:host=' . $this->conf['host'] . ';dbname=' . $this->conf['dbname'],
                $this->conf['user'],
                $this->conf['password']
            );
        } catch (\PDOException $e) {
            $error = ["message" => $e->getMessage()];
            return $this->err->handler(404, $error);
        }
    }


    public function execute(string $sql, array $params = []): bool
    {
        $sth = $this->dbh->prepare($sql);
        $res = $sth->execute($params);
        return $res;
    }
    public function getLastId()
    {
        return $this->dbh->lastInsertId();
    }

    public function query($sql, $params, $class = 'stdClass')
    {
        try {
            $sth = $this->dbh->prepare($sql);
            $req = $sth->execute($params);

            if (false !== $req) {
                $response = $sth->fetchAll(\PDO::FETCH_CLASS, $class);
                if ($response) {
                    $res = $response;
                }else{
                    $error = [
                        "message" => "Not found"
                    ];
                    $res = $this->err->handler(404, $error);
                }
            } else {
                $error = [
                    "message" => "Not found"
                ];
                $res = $this->err->handler(404, $error);
            }
            return $res;
        } catch (\PDOException $e) {
            $error = [
                "message" => "Not found",
                "field" => str_replace(':', '', array_key_first($params))
            ];
            return $this->err->handler(404, $error);
        }
    }
}
