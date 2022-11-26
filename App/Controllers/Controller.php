<?php

namespace App\Controllers;

use App\Models\ErrorHandler;
use App\Models\Model;
use App\Models\User;

class Controller
{
    public $method;
    public $model;
    public $endpoint;
    public $reqBody;

    public function __construct(string $endpoint, string $method, string $id, array $req)
    {
        $this->err = ErrorHandler::unique();
        $this->reqBody = \json_decode($req['body'], true);
        switch ($method) {
            case 'GET':
                if ($id) {
                    $res =  Model::getOne($endpoint, $id);
                } else {
                    $res =  Model::getAll($endpoint);
                }
                echo \json_encode($res);
                break;
            case 'POST':
                switch ($req['query']) {
                    case 'search':

                        $res =  Model::search($endpoint, $this->reqBody);
                        break;
                    case 'create':
                        if ('users' == $endpoint) {
                            $params = [
                                'field' => 'email',
                                'value' => $this->reqBody['email']
                            ];
                            $search =  Model::search($endpoint, $params);
                            var_dump($search);
                            if (array_key_exists('error', $search) && 'Not found' == $search['error']['message']) {
                                $res = Model::insert($endpoint, $this->reqBody);
                            } else {
                            }
                        } else {
                            $res = Model::insert($endpoint, $this->reqBody);
                        }

                        break;
                    default:
                        $error = [
                            "message" => "Incorrect query value",
                            "query" => $req['query']
                        ];
                        $res = $this->err->handler(405, $error);
                }

                // echo \json_encode($res);
                break;
            case 'PUT':

                break;
            case 'DELETE':

                break;

            default:
                $error = ["message" => "Incorrect HTTP method"];
                echo \json_encode($this->err->handler(405, $error));
        }
    }
}
