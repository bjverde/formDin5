<?php
class TFormDinGenericDAO
{
    private string|null $database = null;
    private string|null $repository = null;
    private TFormDinPdoConnection|null $tpdo = null;

    /**
     * Seta os elmentos basicos para conectar no banco
     *
     * @param string $database   Nome da conexão ou novo do arquivo em /app/config
     * @param string $repository Nome da Classe do tipo Active Record no diretorio /app/model/maindatabase
     * @param object $tpdo objeto do tipo TFormDinPdoConnection
     */
    public function __construct($database = null, $repository = null, $tpdo = null)
    {
        if (empty($tpdo)) {
            $tpdo = new TFormDinPdoConnection($database);
        } else {
            if (empty($database)) {
                $database = $tpdo->getDatabase();
            }
        }
        $this->setDatabase($database);
        $this->setRepository($repository);
        $this->setTPDOConnection($tpdo);
    }
    public function getTPDOConnection()
    {
        return $this->tpdo;
    }
    public function setTPDOConnection(TFormDinPdoConnection $tpdo)
    {
        //FormDinHelper::validateObjTypeTPDOConnectionObj($tpdo,__METHOD__,__LINE__);
        $this->tpdo = $tpdo;
        if (!empty($tpdo->getDatabase())) {
            $this->setDatabase($tpdo->getDatabase());
        }
    }
    public function getDatabase()
    {
        return $this->database;
    }
    public function setDatabase(string|null $database)
    {
        $this->database = $database;
        if ($this->tpdo !== null && $database !== null) {
            $this->tpdo->setDatabase($database);
        }
    }
    public function getRepository()
    {
        return $this->repository;
    }
    public function setRepository(string|null $repository)
    {
        $this->repository = $repository;
    }
    public function getDatabaseInfo()
    {
        $this->getTPDOConnection()->getDatabaseInfo();
    }
    public function executeSelect(string $sql)
    {
        try {
            $tpdo = clone $this->getTPDOConnection();
            $tpdo->setFech(PDO::FETCH_ASSOC);
            $tpdo->setOutputFormat(ArrayHelper::TYPE_PDO);
            $tpdo->setCase(PDO::CASE_NATURAL);
            return $tpdo->executeSql($sql);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function executeSelectCount(string $sql)
    {
        try {
            $result = $this->executeSelect($sql);
            return ArrayHelper::get($result, 0);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function execute(string $sql, array $values)
    {
        try {
            return $this->getTPDOConnection()->executeSql($sql, $values);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getArrayByCriteria(TCriteria $criteria, bool $showDumpLogTela = false)
    {
        try {
            $tpdo = $this->getTPDOConnection();
            return $tpdo->selectByTCriteria($criteria, $this->getRepository(), $showDumpLogTela);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function getListObjByCriteria(TCriteria $criteria, bool $showDumpLogTela = false)
    {
        try {
            $tpdo = $this->getTPDOConnection();
            return $tpdo->selectByTCriteria($criteria, $this->getRepository(), $showDumpLogTela);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}//fim classe