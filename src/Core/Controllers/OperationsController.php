<?php
namespace Digitalis\Core\Controllers;

use Digitalis\Core\Controllers\Controller;
use Digitalis\Core\Models\Data;
use Digitalis\Core\Models\DbAdapters\OperationDbAdapter;
use Digitalis\Core\Models\Lexique;
use Digitalis\Core\Models\Security\LoggedUser;
use Digitalis\Core\Models\SessionManager;
use Digitalis\Core\Models\SysConst;
use Digitalis\Core\Models\ViewModels\JournalViewModel;
use Digitalis\Core\Models\ViewModels\WithdrawViewModel;
use Imediatis\EntityAnnotation\ModelState;
use Imediatis\EntityAnnotation\Security\InputValidator;
use Slim\Http\Request;
use Slim\Http\Response;
use Digitalis\Core\Models\ApiResponse;
use Digitalis\Core\Models\DbAdapters\UserDbAdapter;
use Digitalis\Core\Models\JsonResponse;

/**
 * OperationsController Gestionnaire des acion des utilisateurs
 * @copyright  2018 IMEDIATIS SARL http://www.imediatis.net
 * @license    Intellectual property rights of IMEDIATIS SARL
 * @version    Release: 1.0
 * @author     Sylvin KAMDEM <sylvin@imediatis.net> (Back-end Developper)
 */
class OperationsController extends Controller
{
	public function __construct($container)
	{
		parent::__construct($container);
		parent::setCurrentController(__class__);
	}

	/***********************************OUVERTURE FERMETURE AGENCE CAISSE**************************************/

	public function opencloseBranch(Request $request, Response $response)
	{
		$loggedUser = SessionManager::getLoggedUser();
		$this->title(Lexique::GetString(CUR_LANG, $loggedUser->branchIsOpened ? 'branch-closing' : 'branch-opening'));

		return $this->render($response, 'open-close-branch', true);
	}

	public function popencloseBranch(Request $request, Response $response)
	{
		$loggedUser = SessionManager::getLoggedUser();
		$this->title(Lexique::GetString(CUR_LANG, $loggedUser->branchIsOpened ? 'branch-closing' : 'branch-opening'));

		$data = Data::sendData( sprintf('branches/%s/branchopenclose', base64_encode($loggedUser->branchCode)), json_encode(['login' => base64_encode($loggedUser->getLogin()), 'status' => $loggedUser->branchIsOpened ? 0 : 1]));

		if (isset($data['updated']) && $data['updated']) {
			SessionManager::set(SysConst::FLASH, Lexique::GetString(CUR_LANG, $data['message']));
		} else {
			$msg = isset($data['message']) ? $data['message'] : 'unable-to-open-the-agency';
			SessionManager::set(SysConst::FLASH, Lexique::GetString(CUR_LANG, $msg), DANGER);
		}
		return $this->redirect($response, 'operation.opencloseBranch');
	}

	public function opencloseBox(Request $request, Response $response)
	{
		$loggedUser = SessionManager::getLoggedUser();

		$this->title(Lexique::GetString(CUR_LANG, $loggedUser->boxIsOpened ? 'box-closing' : 'box-opening'));

		return $this->render($response, 'open-close-box', true);
	}

	public function popencloseBox(Request $request, Response $response)
	{
		$loggedUser = SessionManager::getLoggedUser();
		$this->title(Lexique::GetString(CUR_LANG, $loggedUser->boxIsOpened ? 'box-closing' : 'box-opening'));

		$data = Data::sendData(sprintf('boxes/%s/boxopenclose', base64_encode($loggedUser->boxCode)), json_encode(['login' => base64_encode($loggedUser->getLogin()), 'status' => $loggedUser->boxIsOpened ? 0 : 1]));

		if (isset($data['updated']) && $data['updated']) {
			SessionManager::set(SysConst::FLASH, Lexique::GetString(CUR_LANG, $data['message']));
			if (isset($data['data']) && is_array($data['data'])) {
				SessionManager::updateLoggedUser(LoggedUser::buildInstance($data['data']));
			}
		} else {
			$msg = isset($data['message']) ? $data['message'] : 'unable-to-open-the-box-';
			SessionManager::set(SysConst::FLASH, Lexique::GetString(CUR_LANG, $msg), DANGER);
		}

		return $this->redirect($response, 'operation.opencloseBox');
	}
	/*****************************************FIN*************************/
	/**********************************OPERATIONS*********************************/

	public function cashWithdrawal(Request $request, Response $response)
	{
		$this->title(Lexique::GetString(CUR_LANG, 'cash-withdrawal'));

		return $this->render(
			$response,
			'cashwithdrawal',
			true,
			[
				'partners' => OperationDbAdapter::getPartners(),
				'typePieces' => OperationDbAdapter::getTypePieces()
			]
		);
	}

	public function pcashWithdrawal(Request $request, Response $response)
	{
		$this->title(Lexique::GetString(CUR_LANG, 'cash-withdrawal'));
		$model = new WithdrawViewModel();
		$model = InputValidator::BuildModelFromRequest($model, $request);
		if (ModelState::isValid()) { }

		return $this->render(
			$response,
			'cashwithdrawal',
			true,
			[
				SysConst::MODEL => $model->toArray(),
				SysConst::MODEL_ERRORS => ModelState::getErrors(),
				'partners' => OperationDbAdapter::getPartners(),
				'typePieces' => OperationDbAdapter::getTypePieces()
			]
		);
	}

	public function journal(Request $request, Response $response)
	{
		$this->title(Lexique::GetString(CUR_LANG, 'journal-of-transactions'));
		$model = new JournalViewModel();
		$transactions = [];
		$total = 0;

		return $this->render($response, 'journal', true, [SysConst::MODEL => $model->toArray(), 'partners' => OperationDbAdapter::getPartners(), 'transactions' => $transactions, 'total' => $total]);
	}

	public function postjournal(Request $request, Response $response)
	{
		$this->title(Lexique::GetString(CUR_LANG, 'journal-of-transactions'));
		$model = new JournalViewModel();
		$model = InputValidator::BuildModelFromRequest($model, $request);
		$transactions = [];
		$total = 0;

		return $this->render($response, 'journal', true, [SysConst::MODEL => $model->toArray(), 'partners' => OperationDbAdapter::getPartners(), 'transactions' => $transactions, 'total' => $total]);
	}

	public function getClient(Request $request, Response $response)
	{
		$output = new JsonResponse();
		InputValidator::InitSlimRequest($request);

		$partner = InputValidator::getString('partner');
		$numCpt = InputValidator::getString('numCpt');

		$client = OperationDbAdapter::getClient($partner, $numCpt);
		$output->data =  !is_null($client) ? $client->toArray() : null;
		$output->isSuccess = !is_null($client);
		$output->message = Lexique::GetString(CUR_LANG, !is_null($client) ? 'customer-identified' : 'unable-to-identify-this-c');

		return $this->renderJson($response, $output->asArray());
	}
}