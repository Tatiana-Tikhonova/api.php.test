<?php

namespace App\Models;

use App\Db;

class Model
{
    public static function getAll($endpoint)
    {
        $db = Db::unique();
        $sql = 'SELECT * FROM ' . $endpoint;
        return $db->query($sql, []);
    }


    public static function getOne($endpoint, $id)
    {
        $db = Db::unique();
        $sql = 'SELECT * FROM ' . $endpoint . ' WHERE id=:id';
        $res = $db->query($sql, [':id' => $id]);
        if ((bool)$res) {
            return $res;
        } else {
            $err = ErrorHandler::unique();
            $errors = [
                "message" => "Not found",
                "field" => $id
            ];
            return $err->handler(404, $errors);
        }
    }

    public static function search($endpoint, $req)
    {

        $field = $req['field'];
        $value = $req['value'];
        $db = Db::unique();
        $sql = 'SELECT * FROM ' . $endpoint . ' WHERE ' . $field . '=:' . $field;
        $res = $db->query($sql, [':' . $field => $value]);

        // if (!!$res) {
        //     $err = ErrorHandler::unique();
        //     $errors = [
        //         "message" => "Not found",
        //         "field" => $field,
        //         "value" => $value
        //     ];
        //     $res = $err->handler(404, $errors);
        // }

        // var_dump($res);
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

        if ($res) {
            $res = self::getOne($endpoint, $db->getLastId());
        }
        return $res;
    }

    // public function update()
    // {
    //     if ($this->isNew()) {
    //         return;
    //     }
    //     $id = $this->id;
    //     $values = [];
    //     $setParams = '';
    //     foreach ($this as $key => $value) {
    //         if ('id' == $key) {
    //             continue;
    //         }
    //         $setParams .= $key . '=:' . $key . ', ';
    //         $values[':' . $key] = $value;
    //     }
    //     $setParams = trim($setParams, ', ');
    //     $sql = 'UPDATE ' . static::TABLE . ' SET ' . $setParams . '  WHERE id=' . $id;

    //     $db = Db::unique();
    //     $ret = $db->execute($sql, $values);
    //     return $ret;
    // }

    // public function save()
    // {
    //     if (!$this->isNew()) {
    //         $ret = $this->update();
    //         echo 'update';
    //     } else {
    //         $ret = $this->insert();
    //         echo 'insert';
    //     }
    //     return $ret;
    // }
    // public static function delete($id)
    // {
    //     $sql = 'DELETE FROM ' . static::TABLE . ' WHERE id=' . $id;
    //     $db = Db::unique();
    //     $db->query($sql, [], static::class);
    //     return 'Deleted!';
    // }
}
