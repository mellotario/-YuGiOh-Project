<?php

class Card {
    private $id;
    private $name;
    private $description;
    private $category_id;

    public function __construct($id, $name, $description, $category_id) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->category_id = $category_id;
    }

    // Getter and setter methods for each property
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCategoryId() {
        return $this->category_id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setCategoryId($category_id) {
        $this->category_id = $category_id;
    }
}
