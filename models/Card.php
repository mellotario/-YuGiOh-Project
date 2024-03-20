<?php

class Card
{
    public $id;
    public $name;
    public $description;
    public $image_url;
    public $atk;
    public $def;
    public $level;
    public $race;
    public $created_at;
    public $updated_at;

    public function __construct($id, $name, $description, $image_url, $atk, $def, $level, $race, $created_at, $updated_at)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->image_url = $image_url;
        $this->atk = $atk;
        $this->def = $def;
        $this->level = $level;
        $this->race = $race;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // Getter and setter methods for each property
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }


    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
}
