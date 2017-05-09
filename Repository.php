<?php

include_once 'Db.php';

/**
 * Class Repository
 */
class Repository
{

    /**
     * @var mysqli
     */
    private $mysqli;

    /**
     * Repository constructor.
     */
    public function __construct()
    {
        $this->mysqli = (new Db)->getMysqli();
    }

    /**
     * @return bool|mysqli_result
     */
    public function getAllSearch()
    {
        return $this->mysqli->query("SELECT * FROM kelVoiture_search");
    }

    public function updateDateSearch($idSearch)
    {
        return $this->mysqli->query(
            "UPDATE kelVoiture_search SET last_check = " . date('Ymd') ." WHERE id = $idSearch"
        );
    }

    /**
     * @param array $values
     */
    public function insertHistory(array $values)
    {
        if ($values['idAnnonce'] < 1) {
            $values['idAnnonce'] = 0;
        }
        if ($values['idSearch'] < 1) {
            $values['idSearch'] = 0;
        }
        if (empty($values['brand'])) {
            $values['brand'] = '';
        }
        if (empty($values['model'])) {
            $values['model'] = '';
        }
        if (empty($values['version'])) {
            $values['version'] = '';
        }
        if (empty($values['seller'])) {
            $values['seller'] = '';
        }
        if ($values['dept'] < 1) {
            $values['dept'] = 0;
        }
        if ($values['year'] < 1) {
            $values['year'] = 0;
        }
        if ($values['km'] < 1) {
            $values['km'] = 0;
        }
        if ($values['price'] < 1) {
            $values['price'] = 0;
        }

        $date = date('Ymd');

        $rqt = $this->mysqli
            ->prepare('
                INSERT IGNORE INTO kelVoiture_history 
                    (id_annonce, brand, model, version, seller_type, department, `year`, km, price, `date`, 
                    datetime)
                VALUES 
                    (?,?,?,?,?,?,?,?,?,?,CURRENT_TIMESTAMP)
            ');

        $rqt->bind_param(
            'issssiiiii',
            $values['idAnnonce'],
            $values['brand'],
            $values['model'],
            $values['version'],
            $values['seller'],
            $values['dept'],
            $values['year'],
            $values['km'],
            $values['price'],
            $date
        );

        $rqt->execute();

        $idHistory = $rqt->insert_id;
        if ($idHistory === 0) {
            $idHistory = (int) $this
                ->mysqli
                ->query(
                    "SELECT * FROM kelVoiture_history WHERE id_annonce = {$values['idAnnonce']} AND `date` = $date"
                )
                ->fetch_object()
                ->id;
        }

        $rqt = $this->mysqli
            ->prepare('INSERT INTO kelVoiture_history_search (id_search, id_history) VALUES (?, ?)');
        $rqt->bind_param(
            'ii',
            $_SESSION['idCurrentSearch'],
            $idHistory
        );

        $rqt->execute();
    }
}