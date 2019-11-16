<?php
namespace Digitalis\Core\Models\ViewModels;

/**
 * JournalViewModel Modèle de vue de traitement du journal
 *
 * @copyright  2018 IMEDIATIS SARL http://www.imediatis.net
 * @license    Intellectual property rights of IMEDIATIS SARL
 * @version    Release: 1.0
 * @author     Sylvin KAMDEM <sylvin@imediatis.net> (Back-end Developper)
 */
class JournalViewModel implements \JsonSerializable
{

	/**
	 * Date de début de la période
	 *
	 * @IME\Required{"errMsg":"Ce champ est obligatoire"}
	 * @IME\DataType{"type":"date","errMsg":"Valeur invalide pour ce champ"}
	 * @var \DateTime
	 */
	public $tb_jrnl_ddeb;

	/**
	 * Date de fin de période
	 *
	 * @IME\Required{"errMsg":"Ce champ est obligatoire"}
	 * @IME\DataType{"type":"date","errMsg":"Valeur invalide pour ce champ"}
	 * @var \DateTime
	 */
	public $tb_jrnl_dfin;

	/**
	 * Partenaire 
	 *
	 * @IME\DataType{"nullable":true}
	 * @var string
	 */
	public $ld_jrnl_partner;

	/**
	 * Numéro de compte du client
	 *
	 * @IME\DataType{"nullable":true}
	 * @IME\Length{"max":20,"errMsg":"Ce champ n'accepte pas plus de 20 caractères"}
	 * @var string
	 */
	public $tb_jrnl_account;

	public function __construct()
	{
		$this->tb_jrnl_ddeb = new \DateTime();
		$this->tb_jrnl_dfin = new \DateTime();
	}

	public function toArray()
	{
		return [
			'startDate' => ($this->tb_jrnl_ddeb instanceof \DateTime) ? $this->tb_jrnl_ddeb->format('Y-m-d') : $this->tb_jrnl_ddeb,
			'endDate' => ($this->tb_jrnl_dfin instanceof \DateTime) ? $this->tb_jrnl_dfin->format('Y-m-d') : $this->tb_jrnl_dfin,
			'partner' => $this->ld_jrnl_partner,
			'accountNum' => $this->tb_jrnl_account
		];
	}

	public function jsonSerialize()
	{
		return $this->toArray();
	}
}