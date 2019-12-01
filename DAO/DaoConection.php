<?php
namespace Dao;

use PDO;

class DaoConection {
    public $db;
    public function __construct()
    {
        if(ENVIRONMENT == 'development') {
            
            $config['dbname'] = 'devstagram';
            $config['host'] = 'localhost';
            $config['dbuser'] = 'root';
            $config['dbpass'] = '';
            $config['jwt_secret_key'] = 'abC123';
        } else {
            
            $config['dbname'] = 'devstagram';
            $config['host'] = 'localhost';
            $config['dbuser'] = 'root';
            $config['dbpass'] = 'root';
        }

        //global $db;
        try {
            $this->db = new \PDO("mysql:dbname=".$config['dbname'].";host=".$config['host'], $config['dbuser'], $config['dbpass']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $e) {
            echo "ERRO: ".$e->getMessage();
            exit;
        }
    }
}