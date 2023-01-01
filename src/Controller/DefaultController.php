<?php

namespace App\Controller;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends BaseController
{
    #[Route('/', name: 'default', methods: ['GET'])]
    public function index(
        #[Autowire('%kernel.project_dir%')] string $projectDir,
        #[Autowire('%env(APP_ENV)%')] string $appEnv,
    ): Response {
        return $this->render(
            'default/index.html.twig',
            [
                'controller_name' => 'DefaultController',
                'php_version' => PHP_VERSION,
                'symfony_version' => Kernel::VERSION,
                'project_dir' => $projectDir,
                'app_env' => $appEnv,
            ]
        );
    }
}
