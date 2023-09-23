<?php


namespace app\models;


use app\core\Application;
use app\core\Model;
use app\models\fields\DateTimeModelField;
use app\models\fields\EmailModelField;
use app\models\fields\IntegerModelField;
use app\models\fields\PasswordModelField;
use app\models\fields\StatusModelField;
use app\models\fields\TextModelField;

class UserModel extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;
    const ROLE_USER = 0;
    const ROLE_ADMIN = 1;
    const DB_TABLE = "users";

    private SessionModel $sessionModel;
    public bool $isUserLoggedIn = false;

    public TextModelField $firstname;
    public TextModelField $lastname;
    public EmailModelField $email;
    public PasswordModelField $password;
    public PasswordModelField $confirmPassword;
    public StatusModelField $status;
    public StatusModelField $role;
    public DateTimeModelField $created_at;
    public array $userList = [];

    public function __construct()
    {
        parent::__construct();
        $this->sessionModel = new SessionModel();

        $this->firstname = new TextModelField($name = "firstname", $verbose = "First Name");
        $this->lastname = new TextModelField($name = "lastname", $verbose = "Last Name");
        $this->email = new EmailModelField($name = "email", $verbose = "Email");
        $this->password = new PasswordModelField($name = "password", $verbose = "Password");
        $this->confirmPassword = new PasswordModelField($name = "confirmPassword", $verbose = "Confirm Password");
        $this->status = new StatusModelField($name = 'status', $verbose = "Status");
        $this->role = new StatusModelField($name = 'role', $verbose = "User Role");
        $this->created_at = new DateTimeModelField($name = 'created_at', $verbose = "Member Since");

        $this->firstname->setRequired(true)->setMin(2)->setMax(50);;
        $this->lastname->setRequired(true)->setMin(2)->setMax(50);
        $this->email->setRequired(true);
        $this->email->setUniqueMethod([$this, "isNewUser"]);
        $this->password->setRequired(true);
        $this->confirmPassword->setRequired(true);
        $this->status->setStatusList([
            self::STATUS_INACTIVE => "Inactive",
            self::STATUS_ACTIVE => "Active",
            self::STATUS_DELETED => "Deleted"
        ])->setDefault(self::STATUS_ACTIVE);

        $this->role->setStatusList([
            self::ROLE_USER => "User",
            self::ROLE_ADMIN => "Admin"
        ])->setDefault(self::ROLE_USER);
        return $this;
    }

    public function register()
    {
        if ($this->isFormValid && $this->email->validateUnique()) {
            $fields = [$this->firstname, $this->lastname, $this->email, $this->status, $this->password];
            $this->db->insertIntoTable(self::DB_TABLE, $fields);
            $this->session->setMessage("success", "Registration successful",
                "You have successfully registered to " . SITE_NAME);
            return true;
        } else {
            return false;
        }
    }

    public function login()
    {
        if ($this->isFormValid) {
            $user = $this->db->selectObject(self::DB_TABLE, $searchQuery = [$this->email, $this->status],
                $columns = [$this->email, $this->password]);
            if ($user) {
                if ($this->password->verify($user->password)) {
                    $this->setUser();
                } else {
                    $this->password->addErrorMessage("Password didn't match");
                    return false;
                }
            } else {
                $this->email->addErrorMessage("No user with email <b>$this->email</b> was found");
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function logout()
    {
        $this->sessionModel->logoutSession();
        Application::$app->user = new UserModel();
        $this->session->setMessage("success", "Logout Successful",
            "You have successfully logged out from " . SITE_NAME);

    }

    public function formValidation()
    {
        if ($this->confirmPassword->getDbValue()) {
            $validation = $this->confirmPassword->match($this->password);
            if (!$validation) {
                $this->isFormValid = false;
                $this->addErrors($this->confirmPassword);
            }
        }
    }

    private function setUser()
    {
        $record = $this->db->selectObject(self::DB_TABLE,
            $searchQuery = [$this->email],
            $columns = [$this->id, $this->firstname, $this->lastname, $this->status, $this->role]
        );
        $this->setProperties($record);
        $this->sessionModel->generateSession($this);
        $this->isUserLoggedIn = true;
        $this->session->setMessage("success", "Login Successful",
            "Welcome <b>{$this->getFullName()}</b>!!! You have successfully logged in to " . SITE_NAME);

    }

    public function verifyUser()
    {
        $user_id = $this->sessionModel->getSessionUserID();
        if ($user_id) {
            $this->setUserFromID($user_id);
            $this->isUserLoggedIn = true;
            return $this->isUserLoggedIn;
        } else {
            $this->isUserLoggedIn = false;
            return false;
        }
    }

    public function setUserFromID($user_id)
    {
        $this->loadData(["id" => $user_id]);
        $record = $this->db->selectObject(self::DB_TABLE,
            $searchQuery = [$this->id],
            $columns = [$this->email, $this->firstname, $this->lastname, $this->status, $this->role]
        );
        $this->setProperties($record);
        return $this;
    }

    public function isUserExist()
    {
        return $this->isValidUser($this->email);
    }

    public function isValidUser($searchField)
    {
        $record = $this->db->selectObject(self::DB_TABLE, [$searchField], [$this->email]);
        return ($record) ? true : false;
    }


    public function isNewUser()
    {
        return !$this->isUserExist();
    }

    public function getFullName(): string
    {
        return (strlen($this->firstname->getValue()) > 0) ? "$this->firstname $this->lastname" : "Anonymous";
    }

    public function isCurrentUser()
    {
        return $this->id->getValue() == Application::$app->user->id->getValue();
    }

    public function isAdminUser()
    {
        return $this->role->getValue() == self::ROLE_ADMIN;
    }

    public function getUserInstance($record)
    {
        $user = new UserModel();
        $user->setProperties($record);
        return $user;
    }

    public function getUsers()
    {
        $records = $this->db->selectResult(self::DB_TABLE, null,
            [$this->id, $this->firstname, $this->lastname, $this->status, $this->role, $this->created_at]);

        $this->userList = array_map(fn($r) => $this->getUserInstance($r), $records);
        return $this->userList;
    }

    public function updateUser()
    {
        $updateFields = [$this->status, $this->role];

        if ($this->isFormValid) {
            $this->db->updateTable(self::DB_TABLE,
                [$this->id], $updateFields);
            $this->session->setMessage("info", "Post Updated",
                "You have successfully updated user status and role");
            return true;
        } else {
            return false;
        }
    }
}