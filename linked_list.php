<?php

class Node {
    public $data;
    public $next;

    public function __construct($data) {
        $this->data = $data;
        $this->next = null;
    }
}

class LinkedList {
    public $head = null;

    public function add($data) {
        $newNode = new Node($data);

        if ($this->head == null) {
            $this->head = $newNode;
            return;
        }

        $current = $this->head;

        while ($current->next != null) {
            $current = $current->next;
        }

        $current->next = $newNode;
    }
}


?>