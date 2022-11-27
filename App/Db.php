<?php

namespace App;

use PDO;
use App\MyException;

class Db
{
    use \App\SingletonTrait;
    protected $dbh;
    protected $conf;

    protected function __construct()
    {
        $this->conf = json_decode(file_get_contents(dirname(__DIR__) . '/db-config.json'), true);
        try {
            $this->dbh = new PDO(
                'mysql:host=' . $this->conf['host'] . ';dbname=' . $this->conf['dbname'],
                $this->conf['user'],
                $this->conf['password']
            );
        } catch (\PDOException $e) {
            throw new MyException($e->getMessage());
        }
    }

    public function execute(string $sql, array $params = []): bool
    {
        try {
            $sth = $this->dbh->prepare($sql);
            $res = $sth->execute($params);
            return $res;
        } catch (\PDOException $e) {
            throw new MyException('Not found');
        }
    }

    public function getLastId()
    {
        return $this->dbh->lastInsertId();
    }

    public function getAll($sql, $params, $class = 'stdClass')
    {
        try {
            $sth = $this->dbh->prepare($sql);
            $req = $sth->execute($params);
            if (false !== $req) {
                return $sth->fetchAll(\PDO::FETCH_CLASS, $class);
            }
            return [];
        } catch (\PDOException $e) {
            throw new MyException('Not found');
        }
    }

    public function getOne($sql, $params)
    {
        try {
            $sth = $this->dbh->prepare($sql);
            $req = $sth->execute($params);

            if (false !== $req) {
                return $sth->fetch(\PDO::FETCH_ASSOC);
            }
            return [];
        } catch (\PDOException $e) {
            throw new MyException('Not found');
        }
    }
}
