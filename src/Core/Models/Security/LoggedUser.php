<?php
namespace Digitalis\Core\Models\Security;

/**
 * LoggedUser Utilisateur connecté au système
 *
 * @copyright  2018 IMEDIATIS SARL http://www.imediatis.net
 * @license    Intellectual property rights of IMEDIATIS SARL
 * @version    Release: 1.0
 * @author     Sylvin KAMDEM<sylvin@imediatis.net> (Back-end Developper)
 */
class LoggedUser implements \Serializable
{
    /**
     * Login de l'utilisateur connecté
     *
     * @var string
     */
    private $login;

    /**
     * Nom de l'utilisateur
     *
     * @var string
     */
    private $lastName;

    /**
     * Prénom de l'utilisateur
     *
     * @var string
     */
    private $firstName;

    /**
     * avatar de l'utilisateur
     *
     * @var string
     */
    private $avatar;

    /**
     * Profile de l'utilisateur
     *
     * @var string
     */
    private $profile;

    /**
     * Titre de la personne
     *
     * @var string
     */
    private $function;

    /**
     * Code de la caisse
     * 
     * @var string
     */
    private $boxCode;

    /**
     * Statut de la caisse
     *
     * @var boolean
     */
    public $boxIsOpened;

    /**
     * Code de l'agence
     * 
     * @var string
     */
    private $branchCode;

    /**
     * Nom de l'agence
     *
     * @var string
     */
    private $branchName;

    /**
     * Détermine si l'agence est ouverte ou pas
     *
     * @var boolean
     */
    public $branchIsOpened;

    /**
     * Statut de l'opérateur
     *
     * @var integer
     */
    private $status;


    /**
     * Clé secrette de la caisse
     *
     * @var string
     */
    private $boxKey;

    /**
     * Clé secrette de l'agence
     *
     * @var string
     */
    private $branchKey;

    /**
     * token de l'utilisateur connecté
     *
     * @var string
     */
    private $token;

    public function __construct($login = null, $lastName = null, $firstName = null, $profile = null, $function = null, $avatar = null)
    {
        $this->login = $login;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->function = $function;
        $this->profile = $profile;
        $this->avatar = is_null($avatar) ? "user.svg" : $avatar;
    }

    /**
     * retourne le nom de l'agence
     *
     * @return string
     */
    public function getBranchName()
    {
        return $this->branchName;
    }

    /**
     * Nom de l'agence
     *
     * @param string $branchName
     * @return void
     */
    public function setBranchName($branchName)
    {
        $this->branchName = $branchName;
    }

    /**
     * Retourne la valeur de $branchCode
     *
     * @return string
     */
    public function getBranchCode()
    {
        return $this->branchCode;
    }

    /**
     * Définit la valeur de $branchCode
     *
     * @param string $branchCode
     */
    public function setBranchCode($branchCode)
    {
        $this->branchCode = $branchCode;
    }
    /**
     * Retourne la valeur de $boxCode
     *
     * @return string
     */
    public function getBoxCode()
    {
        return $this->boxCode;
    }

    /**
     * Définit la valeur de $boxCode
     *
     * @param string $boxCode
     */
    public function setBoxCode($boxCode)
    {
        $this->boxCode = $boxCode;
    }

    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Retourne la valeur de firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Retourne la valeur de lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Retourne la valeur de function
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Retourne la valeur de profile
     *
     * @return string
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Retourne la valeur de avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function serialize()
    {
        return serialize(array(
            $this->login,
            $this->lastName,
            $this->firstName,
            $this->avatar,
            $this->profile,
            $this->function,
            $this->boxCode,
            $this->boxIsOpened,
            $this->branchCode,
            $this->branchIsOpened,
            $this->branchName,
            $this->status,
            $this->boxKey,
            $this->branchKey,
            $this->token
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->login,
            $this->lastName,
            $this->firstName,
            $this->avatar,
            $this->profile,
            $this->function,
            $this->boxCode,
            $this->boxIsOpened,
            $this->branchCode,
            $this->branchIsOpened,
            $this->branchName,
            $this->status,
            $this->boxKey,
            $this->branchKey,
            $this->token
        ) = unserialize($serialized);
    }

    public function forTwig()
    {
        return array(
            'login' => $this->login,
            'lastName' => $this->lastName,
            'firstName' => $this->firstName,
            'fullName' => trim($this->firstName . ' ' . $this->lastName),
            'avatar' => $this->avatar,
            'profile' => $this->profile,
            'function' => $this->function,
            'boxCode' => $this->boxCode,
            'boxIsOpened' => $this->boxIsOpened,
            'branchCode' => $this->branchCode,
            'branchIsOpened' => $this->branchIsOpened,
            'branchName' => $this->branchName,
            'status' => $this->status,
            'boxKey' => $this->boxKey,
            'branchKey' => $this->branchKey,
            'token'=>$this->token
        );
    }

    /**
     * Permet de construire une instance de LoggedUser à partir d'un tableau associatif
     *
     * @param array $ruser
     * @return LoggedUser
     */
    public static function buildInstance(array $ruser)
    {
        if (isset($ruser['login'])) {
            $loggeduser = new LoggedUser();
            $loggeduser->login = isset($ruser['login']) ? $ruser['login'] : null;
            $loggeduser->lastName = isset($ruser['lastName']) ? $ruser['lastName'] : null;
            $loggeduser->firstName = isset($ruser['firstName']) ? $ruser['firstName'] : null;
            $loggeduser->avatar = isset($ruser['avatar']) ? $ruser['avatar'] : null;
            $loggeduser->profile = isset($ruser['profile']) ? $ruser['profile'] : null;
            $loggeduser->function = isset($ruser['function']) ? $ruser['function'] : null;
            $loggeduser->boxCode = isset($ruser['boxCode']) ? $ruser['boxCode'] : null;
            $loggeduser->branchCode = isset($ruser['branchCode']) ? $ruser['branchCode'] : null;
            $loggeduser->branchName = isset($ruser['branchName']) ? $ruser['branchName'] : null;
            $loggeduser->status = isset($ruser['status']) ? $ruser['status'] : 1;
            $loggeduser->boxIsOpened = isset($ruser['boxIsOpened']) ? $ruser['boxIsOpened'] : false;
            $loggeduser->branchIsOpened = isset($ruser['branchIsOpened']) ? $ruser['branchIsOpened'] : false;
            $loggeduser->boxKey = isset($ruser['boxKey']) ? $ruser['boxKey'] : null;
            $loggeduser->branchKey = isset($ruser['branchKey']) ? $ruser['branchKey'] : null;
            $loggeduser->token = isset($ruser['token']) ? $ruser['token'] : null;
            return $loggeduser;
        }
        return null;
    }
}