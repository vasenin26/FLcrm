<?php

class Customers_model extends Model {

    protected $table = 'customers';
    protected $fields = Array(
        'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
        'createdon' => 'TIMESTAMP',
        'count' => 'INT',
        'dsc' => 'MEDIUMTEXT'
    );
}
