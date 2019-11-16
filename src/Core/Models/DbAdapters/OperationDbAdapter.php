<?php
namespace Digitalis\Core\Models\DbAdapters;

use Digitalis\Core\Models\Data;
use Digitalis\Core\Models\SessionManager;
use Digitalis\Core\Models\Client;

/**
 * OperationDbAdapter Gestion des transactions avec le serveur
 *
 * @copyright  2018 IMEDIATIS SARL http://www.imediatis.net
 * @license    Intellectual property rights of IMEDIATIS SARL
 * @version    Release: 1.0
 * @author     Sylvin KAMDEM <sylvin@imediatis.net> (Back-end Developper)
 */
class OperationDbAdapter
{

	public static function getPartners()
	{
		$output = [];
		try {
			$user = SessionManager::getLoggedUser();
			$data = Data::getData('Partner/' . base64_encode($user->getBranchCode()));
			if (isset($data['found']) && $data['found'] && is_array($data['data'])) {
				$output = $data['data'];
			}
		} catch (\Exception $exc) {
			Data::setErrorMessage(Lexique::GetString(CUR_LANG, an_error_occured));
			ErrorHandler::writeLog($exc);
		}
		return $output;
	}

	/**
	 * Permet de récupére un client
	 *
	 * @param string $partner
	 * @param string $numCpt
	 * @return Client
	 */
	public static function getClient($partner, $numCpt)
	{
		$output = null;
		try {
			$data = Data::getData("Client/" . $numCpt . "/" . $partner);
			if (isset($data['found']) && $data['found']) {
				if (!is_null($data['data'])) {
					$output = Client::buildInstance($data['data']);
				} else {
					Data::setErrorMessage($data['message']);
				}
			}
		} catch (\Exception $exc) {
			Data::setErrorMessage(Lexique::GetString(CUR_LANG, an_error_occured));
			ErrorHandler::writeLog($exc);
		}
		return $output;
	}

	public static function getTypePieces()
	{
		$output = [];
		try {
			//$user = SessionManager::getLoggedUser();
			$data = Data::getData('Typepiece');
			if (isset($data['found']) && $data['found'] && is_array($data['data'])) {
				$output = $data['data'];
			}
		} catch (\Exception $exc) {
			Data::setErrorMessage(Lexique::GetString(CUR_LANG, an_error_occured));
			ErrorHandler::writeLog($exc);
		}
		return $output;
	}
}