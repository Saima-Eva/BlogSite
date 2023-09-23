<?php


namespace app\models;


use app\core\Application;
use app\core\Model;
use app\models\fields\DateTimeModelField;
use app\models\fields\IntegerModelField;
use app\models\fields\StatusModelField;
use app\models\fields\TextModelField;

class PostModel extends Model
{
    const STATUS_UNPUBLISHED = 0;
    const STATUS_PENDING = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_DELETED = 3;
    const DB_TABLE = "posts";
    const PAGE_POST_LIMIT = 10;

    private SessionModel $sessionModel;
    public bool $isUserLoggedIn = false;

    public IntegerModelField $author_id;
    public TextModelField $title;
    public TextModelField $content;
    public DateTimeModelField $created_at;
    public DateTimeModelField $published_at;
    public StatusModelField $status;
    public DateTimeModelField $last_updated_at;
    public UserModel $author;
    public StatusModelField $category;
    public array $postList;
    public array $commentList;
    public int $page = 1;
    public bool $end = false;

    public function __construct()
    {
        parent::__construct();
        $this->author_id = new IntegerModelField("author_id", "Author ID");
        $this->title = new TextModelField("title", "Post Title");
        $this->content = new TextModelField("content", "Post Content");
        $this->status = new StatusModelField("status", "Status");
        $this->created_at = new DateTimeModelField("created_at", "Created At");
        $this->published_at = new DateTimeModelField("published_at", "Published At");
        $this->last_updated_at = new DateTimeModelField("last_updated_at", "Last Updated At");
        $this->category = new StatusModelField("category", "Category");


        $this->title->setMin(10)->setMax(512)->setRequired(true);
        $this->content->setMin(10)->setMax(65535)->setRequired(true);
        $this->status->setStatusList([
            self::STATUS_UNPUBLISHED => "Unpublished",
            self::STATUS_PENDING => "Pending",
            self::STATUS_PUBLISHED => "Published",
            self::STATUS_DELETED => "Deleted",
        ])->setDefault(self::STATUS_UNPUBLISHED);
        $this->category->setStatusList(Application::$app->category->categoryArray);
        $this->category->setRequired(true);

    }

    public function writePost()
    {
        if ($this->isFormValid) {
            $this->db->insertIntoTable(self::DB_TABLE,
                [$this->author_id, $this->title, $this->content, $this->status, $this->category]);
            $this->session->setMessage("info", "Post Added",
                "You have successfully added a new post");
            return true;
        } else {
            return false;
        }
    }

    public function isPostExist($id, $loginRequired = false)
    {
        $data = ["id" => $id];
        $searchFields = [$this->id];
        if ($loginRequired) {
            $data["author_id"] = Application::$app->user->id->getValue();
            array_push($searchFields, $this->author_id);
        }
        $this->loadData($data);

        $record = $this->db->selectObject(self::DB_TABLE,
            $searchFields, [$this->id, $this->title, $this->content, $this->category,
                $this->author_id, $this->status, $this->created_at, $this->published_at]);
        if ($record) {
            $this->setProperties($record);
        }
        $user = new UserModel();
        $this->author = $user->setUserFromID($this->author_id->getValue());
        return ($record) ? true : false;
    }

    public function updatePost()
    {
        $updateFields = [$this->title, $this->content, $this->category];
        if (in_array($this->status->getValue(), [self::STATUS_PENDING, self::STATUS_UNPUBLISHED])) {
            array_push($updateFields, $this->status);
        }
        if ($this->isFormValid) {
            $this->db->updateTable(self::DB_TABLE,
                [$this->id], $updateFields);
            $this->session->setMessage("info", "Post Updated",
                "You have successfully updated your post");
            return true;
        } else {
            return false;
        }
    }

    public function newPostInstance($record)
    {
        $post = new PostModel();
        $post->loadData($record);
        $user = new UserModel();
        $post->author = $user->setUserFromID($post->author_id->getValue());
        return $post;
    }

    public function getAuthorPosts()
    {
        return $this->getPosts(true, false, null, true);
    }

    public function getHomePosts()
    {
        return $this->getPosts(false, true, self::STATUS_PUBLISHED, false,false, true);
    }

    public function getCategoryPosts()
    {
        return $this->getPosts(false, true, PostModel::STATUS_PUBLISHED, false, true, true);
    }

    public function getProfilePosts($currentUser)
    {
        return $this->getPosts(true, true, PostModel::STATUS_PUBLISHED, !$currentUser, false, true);
    }

    public function getPosts($isAuthorPosts = false, $loadContent = false, $postStatus = null, $currentUser = false, $category = false, $pageLimit=false)
    {
        $searchQuery = [];
        $extra = "";
        $columns = [$this->id, $this->author_id, $this->title, $this->created_at, $this->published_at, $this->status, $this->category];
        if ($currentUser) {
            $this->loadData(["author_id" => $this->currentUserID()]);
        }
        if ($isAuthorPosts) {
            array_push($searchQuery, $this->author_id);
        }
        if (!is_null($postStatus)) {
            $this->status->setValue($postStatus);
            array_push($searchQuery, $this->status);
        }
        if ($loadContent) {
            array_push($columns, $this->content);
        }
        if ($category) {
            array_push($searchQuery, $this->category);
        }

        if ($pageLimit){
            $n1 = ($this->page - 1) * self::PAGE_POST_LIMIT;
            $n2 = $this->page * self::PAGE_POST_LIMIT;
            $count = $this->db->selectCount(self::DB_TABLE, $searchQuery);
            $this->end = $n2 >= intval($count->COUNT);
            $extra = $pageLimit ? "LIMIT $n1, ".self::PAGE_POST_LIMIT : "";
        }


        $records = $this->db->selectResult(self::DB_TABLE, $searchQuery, $columns, $extra);
        $this->postList = array_map(fn($r) => $this->newPostInstance($r), $records);
        return $this->postList;
    }

    public function loadComments()
    {
        $comment = new CommentModel();
        $this->commentList = $comment->getPostComments($this);
        return $this->commentList;
    }

    public function updateStatus()
    {
        if ($this->isFormValid) {
            $this->db->updateTable(self::DB_TABLE,
                [$this->id], [$this->status]);
            $this->session->setMessage("info", "Post Status Updated",
                "You have successfully updated post status");

        }
    }
}