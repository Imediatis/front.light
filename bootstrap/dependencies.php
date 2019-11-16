<?php

use Digitalis\Core\Models\Data;
use Digitalis\Core\Models\EnvironmentManager as EnvMngr;
use Digitalis\Core\Models\MailWorker;
use Digitalis\Core\Models\Reseller;
use Digitalis\Core\Models\SessionManager;
use Digitalis\Core\Models\SysConst;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

//$app = new Slim\App();

$container = $app->getContainer();

SessionManager::set(SysConst::T_CORE_SHARE_VIEW_F, SysConst::CORE_SHARED_VIEW_F);

$container['debug'] = function () {
    return EnvMngr::isDebug();
};

$container['baseUrl'] = function ($c) {
    return $c->request->getUri()->getScheme() . '://' . $c->request->getUri()->getHost() . (!is_null($c->request->getUri()->getPort()) ? ':' . $c->request->getUri()->getPort() : '') . '/';
};

$container['baseDir'] = function () {
    return realpath(__DIR__ . join(DIRECTORY_SEPARATOR, [DIRECTORY_SEPARATOR, '..'])) . DIRECTORY_SEPARATOR;
};

$container['reseller'] = function ($c) {
    $sreseller =  SessionManager::getReseller();
    $reseller = !is_null($sreseller) ? $sreseller : new Reseller(EnvMngr::getResellerFile());
    return $reseller;
};

$container['ipAddress'] = function ($c) {
    $clientIp = MailWorker::getIpAddress();
    SessionManager::set(SysConst::ORIGINAL_CLIENT_IP,$clientIp);
    return $clientIp;
};

//
// INTEGRATION DE TWIG
//
$container['view'] = function ($c) {
    $path = [$c['baseDir'] . 'src'];
    $view = new Twig($path, [
        'cache' => EnvMngr::isProduction() ? $c['baseDir'] . join(DIRECTORY_SEPARATOR, ['tmp', 'cache']) : false,
        'debug' => EnvMngr::isDebug()
    ]);

    if (EnvMngr::isDebug()) {
        $view->addExtension(new Twig_Extension_Debug());
    }

    //Instanciate a add twig specific extenssion
    $basepath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new TwigExtension($c['router'], $basepath));

    return $view;
};
//
//CETTE injection doit être fonction du client
//
$container['mailer'] = function () {
    $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mailer->CharSet = 'UTF-8';
    return $mailer;
};

//
//RECUPERATION ET SAUVEGARDE EN SESSION DU SYSTEME D'EXPLOITATION DU CLIENT
//
SessionManager::set(SysConst::CLIENT_OS, Data::cgetOS($container->request->getServerParam("HTTP_USER_AGENT")));
//
//RECUPERATION DE LA ROUTE DEMANDE
//
SessionManager::set(SysConst::R_ROUTE, $container->request->getUri()->getPath());
