<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected array $configVisual = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->configVisual = $this->cargarConfigVisual();
    }

    private function cargarConfigVisual(): array
    {
        try {
            $model = model('App\Models\ConfiguracionVisualModel');
            return $model->Obtener();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
