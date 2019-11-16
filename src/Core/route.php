<?php

use Digitalis\Core\Controllers\OperationsController;
use Digitalis\Core\Models\Lexique;
use Digitalis\Core\Models\Menu\MenuItem;
use Digitalis\Core\Models\Menu\MenuManager;
use Digitalis\Core\Models\Security\CsrfMiddleware;
use Digitalis\Core\Models\SessionManager;
use Slim\App;

//$app = new \Slim\App();

$c = $app->getContainer();

$app->group(R_CUR_LANG, function (App $app) { })->add(new CsrfMiddleware($c));

//
//Pour ne ne pas contrôler le Requestforgery token pour le data table
//mettre uniquement les liens vers les datata table dans cette zone
//
$app->group(R_CUR_LANG, function (App $app) {
	$app->group('/Operations', function (App $app) {
		$app->get('/open-close-branch', OperationsController::class . ':opencloseBranch')->setName('operation.opencloseBranch');
		$app->post('/open-close-branch', OperationsController::class . ':popencloseBranch');

		$app->get('/open-close-box', OperationsController::class . ':opencloseBox')->setName('operation.opencloseBox');
		$app->post('/open-close-box', OperationsController::class . ':popencloseBox');

		$app->get('/CashWidrawal', OperationsController::class . ':cashWithdrawal')->setName('operation.cashWithdrawal');
		$app->post('/CashWidrawal', OperationsController::class . ':pcashWithdrawal');
		$app->post('/Client', OperationsController::class . ':getClient')->setName('operation.getClient');

		$app->get('/Journal', OperationsController::class . ':journal')->setName('operation.journal');
		$app->post('/Journal', OperationsController::class . ':postjournal');
	});
})->add(new CsrfMiddleware($c));

$moperations = new MenuItem(Lexique::GetString(CUR_LANG, 'operations'), null, true, 4, 'users');
$loggedUser = SessionManager::getLoggedUser();
if ($loggedUser && $loggedUser->boxIsOpened) {
	$moperations->addChildren(new MenuItem(Lexique::GetString(CUR_LANG, 'cash-withdrawal'), 'operation.cashWithdrawal', false, 1, 'share-square-o'))
		->addChildren(new MenuItem(Lexique::GetString(CUR_LANG, 'journal'), 'operation.journal', false, 2, 'newspaper-o'));
}

MenuManager::add($moperations);