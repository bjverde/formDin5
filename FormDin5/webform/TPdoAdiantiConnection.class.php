<?php
/**
 * Classe que faz varias transformações de data e hora
 *
 * @author reinaldo.junior
 */
class TPdoAdiantiConnection
{

    private $database = null;
    private $fech = null;

    public function __construct($database,$fech = null)
    {
        $this->setDatabase($database);
        $this->setFech($fech);
    }

    public function setDatabase($database)
    {
        if( empty($database) ){
            throw new InvalidArgumentException('Database Not Object .class:');
        }
        $this->database = $database;
    }
    public function getDatabase()
    {
        return $this->database;
    }

    public function setFech($fech)
    {
        if(empty($fech)){
            $fech = PDO::FETCH_ASSOC;
        }
        $this->fech = $fech;
    }
    public function getFech()
    {
        return $this->fech;
    }

    public function executeSql($sql, $values = null)
    {
        try {
            $database = $this->getDatabase();
            $fech     = $this->getFech();
            
            TTransaction::open($database); // abre uma transação
            $conn = TTransaction::get();   // obtém a conexão  
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $fech);
            $stmt = $conn->query($sql);    // realiza a consulta
            $result = $stmt->fetchall();
            TTransaction::close();         // fecha a transação.
            return $result;
        }
        catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public static function getArrayKeyValue($colunaChave,$colunaValor,$list)
    {
        $result = array();
        foreach ($list as $row) {
            $result[$row[$colunaChave]]=$row[$colunaValor];
        }
        return $result;
    }

    public function getArrayKeyValueBySql($colunaChave,$colunaValor,$sql, $values = null)
    {
        $resultList = $this->executeSql($sql, $values = null);
        $result = self::getArrayKeyValue($colunaChave,$colunaValor,$resultList);
        return $result;
    }

    public function selectByTCriteria(TCriteria $criteria, $repositoryName)
    {
        try {
            $database = $this->getDatabase();
            TTransaction::open($database); // abre uma transação
            $repository = new TRepository($repositoryName);
            $collections = $repository->load($criteria);
            TTransaction::close();         // fecha a transação.
            return $collections;
        }
        catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function selectCountByTCriteria(TCriteria $criteria, $repositoryName)
    {
        try {
            $database = $this->getDatabase();
            TTransaction::open($database); // abre uma transação
            $repository = new TRepository($repositoryName);
            $count = $repository->count($criteria);
            TTransaction::close();         // fecha a transação.
            return $count;
        }
        catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
