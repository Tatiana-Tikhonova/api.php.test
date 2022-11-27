<?php

namespace App;

use App\MyException;
use App\Models\Model;

class Controller
{
    public $reqBody;

    public function __construct(string $endpoint, string $method, string $id, array $req = [])
    {
        if ((bool)$req) {
            if ('string' === gettype($req['body'])) {
                $this->reqBody = \json_decode($req['body'], true);
            } else {
                $this->reqBody = $req['body'];
            }
        }
        $res = '';

        switch ($method) {
            case 'GET':
                if ($id) {
                    $res =  Model::getOne($endpoint, $id);
                } else {
                    $res =  Model::getAll($endpoint);
                }
                if (false !== (bool)$res) {
                    echo \json_encode($res);
                } else {
                    throw new MyException('Not found');
                }

                break;
            case 'POST':
                switch ($req['query']) {
                    case 'search':
                        $res =  Model::search($endpoint, $this->reqBody);
                        break;
                    case 'create':
                        if ('users' == $endpoint) {
                            $create = $this->createUser($endpoint);
                        } else {
                            $create = Model::insert($endpoint, $this->reqBody);
                        }
                        if ($create) {
                            $res = Model::getLastHandled($endpoint);
                        }
                        break;
                    default:
                        throw new MyException('Not found');
                }

                if (false !== (bool)$res) {
                    echo \json_encode($res);
                } else {
                    throw new MyException('Not found');
                }
                break;
            case 'PUT':
                if ('update' != $req['query']) {
                    throw new MyException('Not found');
                    die;
                }
                $id = $this->reqBody['id'];
                $is_exists = Model::getOne($endpoint, $id);

                if ($is_exists) {
                    $update = Model::update($endpoint, $req['body']);
                    if ($update) {
                        $res = Model::getOne($endpoint, $id);
                    }
                } else {
                    throw new MyException('Not found');
                }
                echo \json_encode($res);
                break;
            case 'DELETE':
                $del = Model::delete($endpoint, $id);
                if ($del) {
                    $res = [
                        'object' => $endpoint,
                        'id' => $id,
                        'deleted' => true
                    ];
                }
                echo \json_encode($res);
                break;
            default:
                throw new MyException('Not found');
        }
    }

    public function createUser($endpoint)
    {
        $email = $this->reqBody['email'];
        $params = [
            'field' => 'email',
            'value' => $email
        ];
        $search =  Model::search($endpoint, $params);
        if (false === $search) {
            $res = Model::insert($endpoint, $this->reqBody);
        } else {
            throw new MyException('User already exists');
        }

        return $res;
    }
}
