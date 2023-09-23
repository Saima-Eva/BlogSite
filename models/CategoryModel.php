<?php


namespace app\models;


use app\core\Model;
use app\models\fields\TextModelField;

class CategoryModel extends Model
{
    public TextModelField $category_key;
    public TextModelField $category_name;
    public array $categoryList;
    public array $categoryArray = [];
    const DB_TABLE = "categories";

    public function __construct()
    {
        parent::__construct();
        $this->category_key = new TextModelField("category_key", "Category Key");
        $this->category_name = new TextModelField("category_name", "Category Name");
        $this->category_key->setRequired(true)->setUniqueMethod([$this, "isUniqueCategory"]);
        $this->category_name->setRequired(true);
    }

    public function getCategoryInstance($record)
    {
        $category = new CategoryModel();
        $category->loadData($record);
        $this->categoryArray[$category->category_key->getValue()] = $category->category_name->getValue();
        return $category;
    }

    public function selectCategories()
    {
        $records = $this->db->selectResult(self::DB_TABLE);
        $this->categoryList = array_map(fn($r) => $this->getCategoryInstance($r), $records);
        return $this->categoryList;
    }

    public function updateCategory()
    {
        $updateFields = [$this->category_key, $this->category_name];

        if ($this->isFormValid) {
            $this->db->updateTable(self::DB_TABLE,
                [$this->id], $updateFields);
            $this->session->setMessage("info", "Post Updated",
                "You have successfully updated category");
            return true;
        } else {
            return false;
        }
    }

    public function isUniqueCategory()
    {
        $record = $this->db->selectObject(self::DB_TABLE,
            [$this->category_key], [$this->category_key]);
        return ($record) ? false : true;
    }

    public function addCategory()
    {
        if ($this->isFormValid && $this->category_key->validateUnique()) {
            $this->db->insertIntoTable(self::DB_TABLE,
                [$this->category_key, $this->category_name]);
            $this->session->setMessage("success", "Category Created",
                "You have successfully created category <b>$this->category_name</b>");
            return true;
        } else {
            return false;
        }

    }

    public function deleteCategory()
    {
        if ($this->isFormValid){
            $this->db->deleteFromTable(self::DB_TABLE,
                [$this->id, $this->category_key]);
            $this->session->setMessage("success", "Category Created",
                "You have successfully deleted category <b>$this->category_name</b>");
            return true;
        } else {
            return false;
        }
    }

}