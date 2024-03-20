<?php

class Deck {
    private $id;
    private $name;
    private $user_id;

    public function __construct($id, $name, $user_id) {
        $this->id = $id;
        $this->name = $name;
        $this->user_id = $user_id;
    }

    // Getter and setter methods for each property
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }
}
