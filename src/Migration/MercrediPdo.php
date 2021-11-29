<?php


namespace AcMarche\Mercredi\Migration;

use PDO;
use Symfony\Component\Dotenv\Dotenv;

class MercrediPdo
{
    private ?PDO $bdd = null;

    public function connect()
    {
        new Dotenv();
        $dsn = 'mysql:host=localhost;dbname=mercredi';
        $username = $_ENV['MERCREDI_USER'];
        $password = $_ENV['MERCREDI_PASSWORD'];
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );

        $this->bdd = new PDO($dsn, $username, $password, $options);
    }

    public function getAll(string $table)
    {
        $sql = 'SELECT * FROM ' . $table;
        $query = $this->execQuery($sql);

        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getAllWhere(string $table, string $where, bool $one)
    {
        $sql = 'SELECT * FROM ' . $table . ' WHERE ' . $where;
        $query = $this->execQuery($sql);
        if ($one) {
            return $query->fetch(PDO::FETCH_OBJ);
        }

        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function execQuery($sql)
    {
        if (!$this->bdd) {
            $this->connect();
        }
        // var_dump($sql);
        $query = $this->bdd->query($sql);
        $error = $this->bdd->errorInfo();
        if ($error[0] != '0000') {
            //    var_dump($error[2]);
            // mail('jf@marche.be', 'duobac error sql', $error[2]);

            throw new \Exception($error[2]);
        }

        return $query;
    }
}
