<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 * @link     https://github.com/wayhood/hyperf-action
 */
namespace Wayhood\HyperfAction\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;
use Wayhood\HyperfAction\Collector\ActionCollector;
use Wayhood\HyperfAction\Collector\CategoryCollector;
use Wayhood\HyperfAction\Collector\DescriptionCollector;
use Wayhood\HyperfAction\Collector\RequestParamCollector;

/**
 * @Command
 */
class GeneratorPostmanCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('gen:postman');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Generate postman collection and environment file for app action.');
    }

    public function handle()
    {
        $dir = BASE_PATH . '/postman';
        $this->makeDirectory($dir);

        $appName = env('APP_NAME', 'app-server');
        $appPort = env('PORT', '9501');
        $this->generateCollectionFile($appName, $dir);
        $this->generateEnvironmentFile($appName, $appPort, $dir);
    }

    protected function makeDirectory($path)
    {
        @mkdir($path, 0777, true);
    }

    private function generateCollectionFile($appName, $dir)
    {
        $json = [];
        $json['info'] = [
            'name' => $appName,
            'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
        ];

        $actions = ActionCollector::result();

        $category = CategoryCollector::list();
        $description = DescriptionCollector::list();
        $request = RequestParamCollector::list();
        $categoryName = array_keys(array_flip($category));
        $categoryItems = [];
        foreach ($categoryName as $name) {
            $categoryItems[$name] = ['name' => $name];
        }
        $items = [];
        foreach ($category as $key => $value) {
            $mapping = $actions[$key];
            if (! isset($items[$value])) {
                $items[$value] = [];
            }

            if (isset($description[$key])) {
                $requestParams = [];
                if (isset($request[$key])) {
                    $requestParams = $request[$key];
                }

                $items[$value][] = [
                    'name' => $description[$key] . ' ' . $mapping,
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            [
                                'key' => 'Authorization',
                                'value' => '{{' . $appName . '_token}}',
                                'type' => 'text',
                            ],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => $this->getRequestParams($requestParams, $mapping),
                            'options' => [
                                'raw' => [
                                    'language' => 'json',
                                ],
                            ],
                        ],
                        'url' => [
                            'raw' => '{{' . $appName . '_host}}',
                            'host' => ['{{' . $appName . '_host}}'],
                        ],
                        'response' => [],
                    ],
                ];
            }
        }

        $jsonItem = [];
        foreach ($categoryItems as $key => $value) {
            $jsonItem[] = [
                'name' => $key,
                'item' => $items[$key],
            ];
        }

        $json['item'] = $jsonItem;

        $filename = $appName . '_postman_collection.json';
        file_put_contents($dir . '/' . $filename, json_encode($json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    private function generateEnvironmentFile($appName, $port, $dir)
    {
        $json = [
            'name' => $appName,
            'values' => [
                [
                    'key' => $appName . '_host',
                    'value' => 'http://localhost:' . $port,
                    'type' => 'default',
                    'enable' => true,
                ],
                [
                    'key' => '' . $appName . '_token',
                    'value' => '',
                    'type' => 'default',
                    'enable' => true,
                ],
            ],
        ];

        $filename = $appName . '_postman_environment.json';
        file_put_contents($dir . '/' . $filename, json_encode($json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    private function getRequestParams($requestParams, $mapping)
    {
        $req = [
            'dispatch' => $mapping,
        ];

        $params = [];
        foreach ($requestParams as $requestParam) {
            if ($requestParam->type == 'string') {
                $params[$requestParam->name] = strval($requestParam->example);
            } elseif ($requestParam->type == 'int') {
                $params[$requestParam->name] = intval($requestParam->example);
            } elseif ($requestParam->type == 'float') {
                $params[$requestParam->name] = floatval($requestParam->example);
            } elseif ($requestParam->type == 'bool') {
                $params[$requestParam->name] = boolval($requestParam->example);
            } elseif ($requestParam->type == 'array') {
                $example = $requestParam->example;
                if ($requestParam->base64 == true) {
                    $example = base64_decode($example);
                }
                $example = @json_decode($example, true);
                if (! is_array($example)) {
                    $example = [];
                }
                $params[$requestParam->name] = $example;
            } elseif ($requestParam->type == 'object') {
                $example = $requestParam->example;
                if ($requestParam->base64 == true) {
                    $example = base64_decode($example);
                }
                $example = @json_decode($example, true);
                if (! is_array($example)) {
                    $example = new \stdClass();
                }
                $params[$requestParam->name] = $example;
            } else {
                $params[$requestParam->name] = $requestParam->example;
            }
        }
        if (count($params) == 0) {
            $params = new \stdClass();
        }
        $req['params'] = $params;
        $request['request'] = $req;
        return json_encode($request, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
