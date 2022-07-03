<?php

class Reader extends Model{
    private $id;
    private $name;
    private $telephone;
    private $email;
    private $cccd;
    private $rank;

    function __construct($id, $name, $telephone, $email, $cccd, $rank){
        $this->table_name = strtolower(get_class($this));
        $this->id = $id;
        $this->name = $name;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->cccd = $cccd;
        $this->rank = $rank;
    }
    public function findReader($connect){
        $condition = "telephone = '{$this->telephone}'";
        return $this->find($connect, $condition);
    }
}