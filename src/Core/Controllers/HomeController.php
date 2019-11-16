<?php
namespace Digitalis\Core\Controllers;

use Digitalis\Core\Controllers\Controller;
use Digitalis\Core\Models\Lexique;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * HomeController Gestionnaire des action des utilisateurs
 * @copyright  2018 IMEDIATIS SARL http://www.imediatis.net
 * @license    Intellectual property rights of IMEDIATIS SARL
 * @version    Release: 1.0
 * @author     Sylvin KAMDEM <sylvin@imediatis.net> (Back-end Developper)
 */
class HomeController extends Controller
{
	public function __construct($container)
	{
		parent::__construct($container);
		parent::setCurrentController(__class__);
	}

	public function index(Request $request, Response $response)
	{
		$this->title(Lexique::GetString(CUR_LANG, 'index'));

		return $this->render($response, 'index', true);
	}
}
