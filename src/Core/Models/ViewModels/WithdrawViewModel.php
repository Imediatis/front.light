<?php
namespace Digitalis\Core\Models\ViewModels;

use Digitalis\Core\Models\SessionManager;

/**
 * WithdrawViewModel 
 *
 * @copyright  2018 IMEDIATIS SARL http://www.imediatis.net
 * @license    Intellectual property rights of IMEDIATIS SARL
 * @version    Release: 1.0
 * @author     Sylvin KAMDEM <sylvin@imediatis.net> (Back-end Developper)
 */
class WithdrawViewModel implements \JsonSerializable
{
	/**
	 * Partenaire auprès de qui l'opération est faite
	 *
	 * @IME\Required{"errMsg":"Ce champ est obligatoire"}
	 * @var string
	 */
	public $LD_trans_partner;

	/**
	 * Numéro de compte du client
	 *
	 * @IME\Required{"errMsg":"Ce champ est obligatoire"}
	 * @IME\Length{"max":20,"errMsg":"Ce champ n'admet pas plus de 20 caractères"}
	 * @var string
	 */
	public $tb_trans_account;

	/**
	 * Nom et prénom du client
	 *
	 * @IME\Required{"errMsg":"Ce champ est obligatoire"}
	 * @IME\Length{"max":50,"errMsg":"Ce champ n'admet pas plus de 50 caractères"}
	 * @var string
	 */
	public $tb_trans_customer;

	/**
	 * Montant de la transaction
	 *
	 * @IME\Required{"errMsg":"Ce champ est obligatoire"}
	 * @IME\DataType{"type":"integer","errMsg":"Valeur invalide pour ce champ"}
	 * @var integer
	 */
	public $tb_trans_amount;

	/**
	 * Type de pièce d'identité
	 *
	 * @IME\Required{"errMsg":"Ce champ est obligatoire"}
	 * @IME\Length{"max":10,"errMsg":"Ce champ n'admet pas plus de 10 caractères"}
	 * @var string
	 */
	public $ld_trans_doc_type;

	/**
	 * Numéro de la pièce d'identité
	 *
	 * @IME\Required{"errMsg":"Ce champ est obligatoire"}
	 * @IME\Length{"max":20,"errMsg":"Ce champ n'admet pas plus de 20 caractères"}
	 * @var string
	 */
	public $tb_trans_doc_num;

	/**
	 * Lieu de délivrance de la pièce d'identité
	 *
	 * @IME\Length{"max":30,"errMsg":"Ce champ n'admet pas plus de 30 caractères"}
	 * @IME\DataType{"nullable":true}
	 * @var string
	 */
	public $tb_trans_doc_place;

	/**
	 * Date de délivrance de la pièce d'identité
	 *
	 * @IME\Required{"errMsg":"Ce champ est obligatoire"}
	 * @IME\DataType{"type":"date","errMsg":"Valeur invalide pour ce champ"}
	 * @var \DateTime
	 */
	public $tb_trans_doc_date;

	public function toArray()
	{
		$user = SessionManager::getLoggedUser();
		return [
			'partner' => $this->LD_trans_partner,
			'accountNum' => $this->tb_trans_account,
			'amount' => $this->tb_trans_amount,
			'customer' => $this->tb_trans_customer,
			'tPiece' => $this->ld_trans_doc_type,
			'docNum' => $this->tb_trans_doc_num,
			'issuePlace' => $this->tb_trans_doc_place,
			'issueDate' => ($this->tb_trans_doc_date instanceof \DateTime) ? $this->tb_trans_doc_date->format('Y-m-d') : $this->tb_trans_doc_date,
			'transDate' => (new \DateTime())->format(DATE_ISO8601),
			'caisse' => $user->getBoxCode(),
			'operator' => $user->getLogin()
		];
	}

	public function jsonSerialize()
	{
		return $this->toArray();
	}
}