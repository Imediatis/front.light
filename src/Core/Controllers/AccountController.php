<?php
namespace Digitalis\Core\Controllers;

use Digitalis\Core\Controllers\Controller;
use Digitalis\Core\Models\Data;
use Digitalis\Core\Models\DbAdapters\UserDbAdapter;
use Digitalis\Core\Models\Lexique;
use Digitalis\Core\Models\Security\ChangePwdViewModel;
use Digitalis\Core\Models\SessionManager;
use Digitalis\Core\Models\SysConst;
use Digitalis\Core\Models\ViewModels\UserViewModel;
use Imediatis\EntityAnnotation\ModelState;
use Imediatis\EntityAnnotation\Security\InputValidator;
use Slim\Http\Body;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * AccountController Description of AccountController here
 * @copyright  2018 IMEDIATIS SARL http://www.imediatis.net
 * @license    Intellectual property rights of IMEDIATIS SARL
 * @version    Release: 1.0
 * @author     Sylvin KAMDEM<sylvin@imediatis.net> (Back-end Developper)
 */
class AccountController extends Controller
{
    public function __construct($container)
    {
        parent::__construct($container);
        parent::setCurrentController(__class__);
    }

    /**
     * Appelle le formulaire de connexion à l'application
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function index(Request $request, Response $response)
    {
        $this->title('Login');
        $loggedUser = SessionManager::getLoggedUser();
        if ($loggedUser) {
            return $this->redirect($response, SysConst::HOME, 301);
        }
        //TODO : implémenter les instructions pour vider les éléments de session ici
        return $this->render($response, "login", true);
    }

    /**
     * Authentifie un utilisateur
     *
     * @param Request $resquest
     * @param Response $response
     * @return void
     */
    public function login(Request $request, Response $response)
    {
        $model = new UserViewModel();
        $model = InputValidator::BuildModelFromRequest($model, $request);
        if (ModelState::isValid()) {
            $loggeduser = UserDbAdapter::loggUser($model);
            if ($loggeduser) {
                if ($loggeduser->status == 2) {
                    return $this->redirect($response, "account.changepwd", 301, ['login' => base64_encode($loggeduser->getLogin())]);
                } elseif ($loggeduser->status == 0) { 
                    SessionManager::set(SysConst::FLASH,Lexique::GetString(CUR_LANG,'user-account-locked'),DANGER);
                }else{
                    SessionManager::set(SysConst::AUTH_USER, serialize($loggeduser));
                    UserDbAdapter::setLastAction();
                    return $this->redirect($response, SysConst::HOME, 301);
                }
            } else {
                SessionManager::set(SysConst::FLASH, Lexique::GetString(CUR_LANG, 'user-name-or-password-inc'), DANGER);
            }
        } else {
            SessionManager::set(SysConst::MODEL_ERRORS, ModelState::getErrors());
        }
        return $this->render($response, 'login', true, [SysConst::MODEL_ERRORS => ModelState::getErrors()]);
    }

    /**
     * Déconnexion de l'utilisateur
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function logout(Request $request, Response $response)
    {
        $loggeduser = SessionManager::getLoggedUser();
        if ($loggeduser) {
            UserDbAdapter::setLastLogout($loggeduser->login);
        }
        SessionManager::remove(SysConst::AUTH_USER);
        session_destroy();
        return $this->redirect($response, SysConst::R_G_LOGIN);
    }

    public function checkAccount(Request $request, Response $response)
    {
        // $body = new Body(fopen('php://temp', 'r+'));
        // $out = new JsonResponse();
        // $out->message = "Session Active";
        // $out->isSuccess = true;
        // $body->write(json_encode($out, JSON_PRETTY_PRINT));
        // return $response->withStatus(200)
        //     ->withHeader('Content-type', 'application/json')
        //     ->withBody($body);
    }


    public function changepwd(Request $request, Response $response)
    {
        $this->title(Lexique::GetString(CUR_LANG, 'update'));
        $login = base64_decode($request->getAttribute('login'));
        $model = new ChangePwdViewModel($login);
        return $this->render($response, 'changepwd', true, [SysConst::MODEL => $model->toArray()]);
    }

    public function postChangepwd(Request $request, Response $response)
    {
        $model = new ChangePwdViewModel();
        $model = InputValidator::BuildModelFromRequest($model, $request);
        $login = $model->tb_usr_login;

        $model->validate();

        if (ModelState::isValid()) { //TODO IMPLEMENTER LE CONTROLE DE COMPLEXITE DU MOT DE PASSE
            if (UserDbAdapter::changePwd($model)) {
                SessionManager::set(SysConst::FLASH, Lexique::GetString(CUR_LANG, 'your-password-has-been-su'));
                return $this->render($response, 'changepwd', true, ['logginSucces' => true]);
            } else {
                SessionManager::set(SysConst::FLASH, Data::getErrorMessage(), DANGER);
            }
        } else {
            SessionManager::set(SysConst::MODEL_ERRORS, ModelState::getErrors());
        }
        return $this->redirect($response, 'account.changepwd', 301, ['login' => base64_encode($login)]);
    }

    public function resetPwd(Request $request, Response $response)
    {
        // $login = base64_decode(InputValidator::getString('login'));
        // $output = new JsonResponse();
        // if (UserDbAdapter::resetPwd($login)) {
        //     $output->message = Lexique::GetString(CUR_LANG, operation_success);
        // } else {
        //     $output->isSuccess = false;
        //     $output->message = Data::getErrorMessage();
        // }
        // return $this->renderJson($response, $output->asArray());
    }
}
