<?php

namespace App\Models;

use App\Db;

class Model
{
    public static function getAll($endpoint)
    {
        $db = Db::unique();
        $sql = 'SELECT * FROM ' . $endpoint;
        return $db->getAll($sql, []);
    }


    public static function getOne($endpoint, $id)
    {
        $db = Db::unique();
        $sql = 'SELECT * FROM ' . $endpoint . ' WHERE id=:id';
        $res = $db->getOne($sql, [':id' => $id]);

        return $res;
    }

    public static function getLastHandled($endpoint)
    {
        $db = Db::unique();
        $id = $db->getLastId();
        $sql = 'SELECT * FROM ' . $endpoint . ' WHERE id=:id';
        $res = $db->getOne($sql, [':id' => $id]);

        return $res;
    }

    public static function search($endpoint, $req)
    {
        $field = $req['field'];
        $value = $req['value'];
        $db = Db::unique();
        $sql = 'SELECT * FROM ' . $endpoint . ' WHERE ' . $field . '=:' . $field;
        $res = $db->getOne($sql, [':' . $field => $value]);
        return $res;
    }

    public static function insert($endpoint, $req)
    {
        $columns = [];
        $values = [];
        foreach ($req as $key => $value) {
            $columns[] = $key;
            $values[':' . $key] = $value;
        }

        $sql = 'INSERT INTO ' . $endpoint  . ' (' . implode(',', $columns) . ') 
         VALUES (' . implode(',', array_keys($values)) . ')';

        $db = Db::unique();
        $res = $db->execute($sql, $values);
        return $res;
    }

    public static function update($endpoint, $req)
    {
        $id = $req['id'];
        $values = [];
        $setParams = '';
        foreach ($req as $key => $value) {
            if ('id' == $key) {
                continue;
            }
            $setParams .= $key . '=:' . $key . ', ';
            $values[':' . $key] = $value;
        }
        $setParams = trim($setParams, ', ');
        $sql = 'UPDATE ' . $endpoint . ' SET ' . $setParams . '  WHERE id=' . $id;

        $db = Db::unique();
        $ret = $db->execute($sql, $values);
        return $ret;
    }

    public static function delete($endpoint, $id)
    {
        $sql = 'DELETE FROM ' . $endpoint . ' WHERE id=' . $id;
        $db = Db::unique();
        $ret = $db->execute($sql, []);
        // $db->getAll($sql, [], static::class);
        // return 'Deleted!';
        return $ret;
    }
}
