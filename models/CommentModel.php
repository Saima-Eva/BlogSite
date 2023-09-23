<?php


namespace app\models;


use app\core\Model;
use app\models\fields\DateTimeModelField;
use app\models\fields\IntegerModelField;
use app\models\fields\TextModelField;

class CommentModel extends Model
{
    const STATUS_UNPUBLISHED = 0;
    const STATUS_PENDING = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_DELETED = 4;
    const DB_TABLE = "comments";

    private SessionModel $sessionModel;
    public bool $isUserLoggedIn = false;

    public IntegerModelField $author_id;
    public IntegerModelField $post_id;
    public TextModelField $content;
    public DateTimeModelField $created_at;
    public IntegerModelField $status;
    public DateTimeModelField $last_updated_at;
    public UserModel $author;
    public PostModel $post;
    public array $commentList;

    public function __construct()
    {
        parent::__construct();
        $this->author_id = new IntegerModelField("author_id", "Author ID");
        $this->post_id = new IntegerModelField("post_id", "Post ID");
        $this->content = new TextModelField("content", "Comment Content");
        $this->status = new IntegerModelField("status", "Status");
        $this->created_at = new DateTimeModelField("created_at", "Created At");
        $this->last_updated_at = new DateTimeModelField("last_updated_at", "Last Updated At");

        $this->post_id->setRequired(true);
        $this->content->setMin(10)->setMax(65535)->setRequired(true);
        $this->status->setMax(self::STATUS_DELETED)->setDefault(self::STATUS_PUBLISHED);
        $this->post = new PostModel();
    }

    public function writeComment()
    {
        if ($this->isFormValid) {
            $this->db->insertIntoTable(self::DB_TABLE,
                [$this->author_id, $this->post_id, $this->content, $this->status]);
            $this->session->setMessage("info", "Comment Posted",
                "You have successfully posted a comment");

            return true;
        } else {
            return false;
        }
    }

    public function isPostExist($id, $loginRequired = false)
    {
        return $this->post->isPostExist($this->post_id->getValue());
    }

    public function updateComment()
    {
        if ($this->isFormValid) {
            $this->db->updateTable(self::DB_TABLE,
                [$this->id], [$this->content]);
        }
    }

    public function newCommentInstance($record)
    {
        $comment = new CommentModel();
        $comment->loadData($record);
        $user = new UserModel();
        $comment->author = $user->setUserFromID($comment->author_id);
        return $comment;
    }

    public function getPostComments($post)
    {
        $this->loadData(["post_id" => $post->id->getValue()]);
        $records = $this->db->selectResult(self::DB_TABLE,
            [$this->post_id], [$this->id, $this->author_id, $this->post_id, $this->content, $this->created_at, $this->status]);
        $this->commentList = array_map(fn($r) => $this->newCommentInstance($r), $records);
        return $this->commentList;
    }

    public function deleteComment()
    {
        $this->db->deleteFromTable(self::DB_TABLE, [$this->id, $this->author_id]);
        $this->session->setMessage("info", "Comment Deleted",
            "You comment successfully deleted ");
    }
}