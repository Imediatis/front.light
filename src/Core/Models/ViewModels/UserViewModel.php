<?php
namespace Digitalis\Core\Models\ViewModels;

/**
 * UserViewModel Modèle pour la gestion des utilisateurs
 *
 * @copyright  2018 IMEDIATIS SARL http://www.imediatis.net
 * @license    Intellectual property rights of IMEDIATIS SARL
 * @version    Release: 1.0
 * @author     Sylvin KAMDEM<sylvin@imediatis.net> (Back-end Developper)
 */
class UserViewModel implements \JsonSerializable
{
	/**
	 * Login de l'utilisateur
	 *
	 * @IME\Required{"errMsg":"Ce champ est obligatoire"}
	 * @IME\Length{"max":30,"errMsg":"Ce champ n'admet pas plus de 30 caractères"}
	 * @var string
	 */
	public $login_usr;

	/**
	 * Fonction de l'utilisateur
	 *
	 * @IME\Length{"max":20,"errMsg":"Ce champ n'admet pas plus de 20 caractères"}
	 * @var string
	 */
	public $pwd_usr;

	/**
	 * Code de la caisse à laquelle l'opérateur est connecté
	 *
	 * @IME\Required{"errMsg":"Ce champ est obligatoire"}
	 * @IME\Length{"max":12,"errMsg":"Ce champ n'admet pas plus de 12 caractères"}
	 * @var string
	 */
	public $boxKey;

	public function toArray()
	{
		return [
			'login' => $this->login_usr,
			'pwd' => $this->pwd_usr,
			'boxKey' => $this->boxKey
		];
	}

	public function jsonSerialize()
	{
		return $this->toArray();
	}
}