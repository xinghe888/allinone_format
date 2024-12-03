<?php

namespace Http;

use Core\LogManager;
use Core\OutputManager;

class HttpController
{
    private $logger;
    private $outputManager;

    public function __construct()
    {
        $this->logger = new LogManager();
        $this->outputManager = new OutputManager();
    }

    public function m3u($format, $params)
    {
        try {
            if (!ob_get_level()) {
                ob_start();
            }
            ob_clean();

            $content = $this->outputManager->getM3uContent($format, $params);

            if (!headers_sent()) {
                header('Content-Type: text/plain; charset=utf-8');
            }

            echo $content;
        } catch (\Exception $e) {
            $this->logger->error('M3U output failed: ' . $e->getMessage());
            if (!headers_sent()) {
                http_response_code(500);
            }
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function txt($format, $params)
    {
        try {
            if (!ob_get_level()) {
                ob_start();
            }
            ob_clean();

            $content = $this->outputManager->getTxtContent($format, $params);

            if (!headers_sent()) {
                header('Content-Type: text/plain; charset=utf-8');
            }

            echo $content;
        } catch (\Exception $e) {
            $this->logger->error('TXT output failed: ' . $e->getMessage());
            if (!headers_sent()) {
                http_response_code(500);
            }
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function jump()
    {
        try {
            if (!isset($_GET['url'])) {
                throw new \Exception('Missing url parameter');
            }

            $url = $_GET['url'];

            // 如果存在 $ 符号，只保留前面的部分
            if (($pos = strpos($url, '$')) !== false) {
                $url = substr($url, 0, $pos);
            }

            // URL 解码
            $url = urldecode($url);

            // 记录跳转日志
            $this->logger->info('Jump to: ' . $url);

            // 执行跳转
            header('Location: ' . $url);
            exit;
        } catch (\Exception $e) {
            $this->logger->error('Jump failed: ' . $e->getMessage());
            if (!headers_sent()) {
                http_response_code(400);
            }
            echo 'Error: ' . $e->getMessage();
        }
    }
}