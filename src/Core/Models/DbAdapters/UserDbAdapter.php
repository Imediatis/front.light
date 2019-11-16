<?php
namespace Digitalis\Core\Models\DbAdapters;

use Digitalis\Core\Handlers\ErrorHandler;
use Digitalis\Core\Models\Data;
use Digitalis\Core\Models\Lexique;
use Digitalis\Core\Models\Security\ChangePwdViewModel;
use Digitalis\Core\Models\Security\LoggedUser;
use Digitalis\Core\Models\SessionManager;
use Digitalis\Core\Models\ViewModels\UserViewModel;

/**
 * UserDbAdapter Gestionnaire des utilisateurs avec le système
 *
 * @copyright  2018 IMEDIATIS SARL http://www.imediatis.net
 * @license    Intellectual property rights of IMEDIATIS SARL
 * @version    Release: 1.0
 * @author     Sylvin KAMDEM <sylvin@imediatis.net> (Back-end Developper)
 */
class UserDbAdapter
{
	/**
	 * Permet de connecter un utilisateur au système central
	 *
	 * @param UserViewModel $user
	 * @return LoggedUser
	 */
	public static function loggUser($user)
	{
		try {
			$data = Data::sendData('operators/login', json_encode($user));
			
			if (isset($data['found']) && $data['found'] && is_array($data['data'])) {
				return LoggedUser::buildInstance($data['data']);
			}
		} catch (\Exception $exc) {
			Data::setErrorMessage(Lexique::GetString(CUR_LANG, an_error_occured));
			ErrorHandler::writeLog($exc);
		}
		return null;
	}

	/**
	 * Permet de savoir si l'utilisateur se connecte une seconde fois
	 *
	 * @param string $login
	 * @return LoggedUser
	 */
	public static function checkLogin($token)
	{
		try {
			$response = Data::getData('operators/checklogin/' . $token);
			if (isset($response['found']) && $response['found']) {
				return LoggedUser::buildInstance($response['data']);
			}
		} catch (\Exception $exc) {
			Data::setErrorMessage(Lexique::GetString(CUR_LANG, an_error_occured));
			ErrorHandler::writeLog($exc);
		}
		return null;
	}

	/**
	 * Définit les paramètres lié à la dernière déconnexion
	 *
	 * @param string $login
	 * @return void
	 */
	public static function setLastLogout($login)
	{
		try {
			Data::sendData(sprintf('operators/%s/lastlogout', base64_encode($login)),null,"PUT");
		} catch (\Exception $exc) {
			Data::setErrorMessage(Lexique::GetString(CUR_LANG, an_error_occured));
			ErrorHandler::writeLog($exc);
		}
	}

	/**
	 * Permet de définir les paramètres de la dernière connexion
	 *
	 * @param string $login
	 * @return void
	 */
	public static function setLastLogin($login)
	{
		try {
			Data::sendData(sprintf('operators/%s/lastlogin', base64_encode($login)), null, "PUT");
		} catch (\Exception $exc) {
			Data::setErrorMessage(Lexique::GetString(CUR_LANG, an_error_occured));
			ErrorHandler::writeLog($exc);
		}
	}

	/**
	 * Permet de définir la date de la dernière action
	 *
	 * @return boolean
	 */
	public static function setLastAction()
	{
		try {
			$logged = SessionManager::getLoggedUser();
			$login = !is_null($logged) ? $logged->getLogin() : null;
			if ($login) {
				Data::sendData(sprintf('operators/%s/lastaction', base64_encode($login)), null, "PUT");
				return true;
			}
		} catch (\Exception $exc) {
			Data::setErrorMessage(Lexique::GetString(CUR_LANG, an_error_occured));
			ErrorHandler::writeLog($exc);
		}
		return false;
	}

	/**
	 * Permet de récupérer un utilisateur en foction de son loggin
	 *
	 * @param string $login
	 * @return LoggedUser
	 */
	public static function getByLogin($login)
	{
		return self::checkLogin($login);
	}

	/**
	 * change le mot de passe actuel de l'opérateur
	 *
	 * @param ChangePwdViewModel $newData
	 * @return boolean
	 */
	public static function changePwd($newData)
	{
		try {
			$data = Data::sendData(sprintf('operators/%s/lastaction', base64_encode($newData->tb_usr_login)), json_encode($newData));
			if (isset($data['saved']) && $data['saved']) {
				return $data['data'];
			} else {
				if (isset($data['message'])) {
					Data::setErrorMessage($data['message']);
				}
			}
		} catch (\Exception $exc) {
			Data::setErrorMessage(Lexique::GetString(CUR_LANG, an_error_occured));
			ErrorHandler::writeLog($exc);
		}
		return false;
	}


}
