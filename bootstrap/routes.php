<?php

use Digitalis\Core\Controllers\AccountController;
use Digitalis\Core\Controllers\HomeController;
use Digitalis\Core\Middlewares\AuthenticationMiddleware;
use Digitalis\Core\Models\Lexique;
use Digitalis\Core\Models\Menu\MenuItem;
use Digitalis\Core\Models\Menu\MenuManager;
use Digitalis\Core\Models\Security\CsrfMiddleware;
use Digitalis\Core\Models\SysConst;
use Slim\App;

//$app = new \Slim\App();

$c = $app->getContainer();
$app->group(R_CUR_LANG."/users",function(App $app)use($c){
    $app->get("/profile/{login}", AccountController::class . ':profile')->setName('user.profile');
    $app->get("/changepwd/{login}", AccountController::class . ':changepwd')->setName('user.changepwd');
    $app->post("/changepwd", AccountController::class . ':postChangepwd')->setName('user.pchangepwd')->add(new CsrfMiddleware($c));
})->add(new AuthenticationMiddleware($c));


$app->group(R_CUR_LANG, function (App $app) {

    $app->get('', HomeController::class . ':index')->setName(SysConst::HOME);

    $app->group('/Account', function (App $app) {
        $app->get("/Login", AccountController::class . ':index')->setName(SysConst::R_G_LOGIN);
        $app->post("/Login", AccountController::class . ':login')->setName(SysConst::R_P_LOGIN);
        $app->post("/Logout", AccountController::class . ':logout')->setName(SysConst::R_LOGOUT);
        $app->get("/Firstlogin/{login}", AccountController::class . ':changepwd')->setName('account.changepwd');
        $app->post("/Firstlogin", AccountController::class . ':postChangepwd')->setName('account.pchangepwd');
    });
})->add(new CsrfMiddleware($c));;

MenuManager::initMenu();

$home = new MenuItem(Lexique::GetString(CUR_LANG, 'home'), SysConst::HOME, false, 0, 'home');
MenuManager::add($home);

$resellerRoutefile = $c->baseDir . join(DIRECTORY_SEPARATOR, ['src', $c->reseller->folder, 'route.php']);
if (file_exists($resellerRoutefile)) {
    include $resellerRoutefile;
}