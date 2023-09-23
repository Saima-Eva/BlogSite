<?php


namespace app\models;


use app\core\Application;
use app\core\Model;
use app\core\Session;
use app\models\fields\IntegerModelField;
use app\models\fields\StatusModelField;
use app\models\fields\TextModelField;

class SessionModel extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const DB_TABLE = "sessions";


    protected TextModelField $session_key;
    protected StatusModelField $status;
    protected IntegerModelField $user_id;

    public function __construct()
    {
        parent::__construct();
        $this->session_key = new TextModelField($name = "session_key", $verbose = "Session Key");
        $this->status = new StatusModelField($name = 'status', $verbose = "Status");
        $this->user_id = new IntegerModelField($name = 'user_id', $verbose = "User ID");

        $this->session_key->setRequired(true)->setMin(100)->setMax(100);
        $this->status->setStatusList([
            self::STATUS_INACTIVE => "Inactive",
            self::STATUS_ACTIVE => "Active"
        ])->setDefault(self::STATUS_ACTIVE);
    }

    public function getSessionUserID()
    {
        $this->loadSessionID();
        if ($this->isFormValid) {
            $record = $this->db->selectObject(self::DB_TABLE,
                $searchQuery = [$this->session_key, $this->status],
                $columns = [$this->user_id]
            );
            return ($record) ? $record->user_id : null;
        } else {
            return null;
        }
    }

    public function generateSession(UserModel $user)
    {
        $this->loadData([
            "session_key" => $this->session->setSessionKey(),
            "user_id" => $user->id->getValue()
        ]);
        if ($this->isFormValid) {
            $this->db->insertIntoTable(self::DB_TABLE, [$this->session_key, $this->user_id, $this->status]);
            return true;
        } else {
            return false;
        }

    }

    public function logoutSession()
    {
        if ($this->loadSessionID()) {
            $this->status->setValue(self::STATUS_INACTIVE);
            $this->db->updateTable(self::DB_TABLE, [$this->session_key], [$this->status]);
            $this->session->destroySessionKey();
        }
    }

    public function loadSessionID()
    {
        $sessionKey = $this->session->getSessionKey();
        if ($sessionKey) {
            $this->loadData(["session_key" => $sessionKey]);
            return true;
        } else {
            return false;
        }
    }
}
