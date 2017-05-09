<?php

/**
 * Class Db
 */
class Db
{
    /**
     * @var mysqli
     */
    private $mysqli;

    public function __construct()
    {
        $this->connect();
    }

    public function getMysqli() :mysqli
    {
        return $this->mysqli;
    }

    /**
     * @return mysqli
     */
    public function connect() :mysqli
    {
        if ($this->mysqli === null) {
            //Connexion MySQL
            if ($_SERVER['HTTP_HOST'] === 'localhost') {
                $mysqli = new mysqli(
                    "localhost",
                    "root",
                    "nicolasnobre",
                    "kelVoiture"
                );
            } else {
                $mysqli = new mysqli(
                    "nicolasnobre.com.mysql",
                    "nicolasnobre_co",
                    "Pj8XTqg6",
                    "nicolasnobre_co"
                );
            }

            if ($mysqli->connect_errno) {
                echo "Echec lors de la connexion à MySQL : ( {$mysqli->connect_errno} )  {$mysqli->connect_error}";
                exit;
            }

            $this->mysqli = $mysqli;
        }

        return $this->mysqli;
    }
}