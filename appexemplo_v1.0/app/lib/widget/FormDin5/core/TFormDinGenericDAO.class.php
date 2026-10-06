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
        if (empty($database) && empty($tpdo)) {
            throw new InvalidArgumentException('É necessário informar $database ou $tpdo');
        }
        $this->setRepository($repository);
        if (!empty($tpdo)) {
            $this->setTPDOConnection($tpdo);
            $this->setDatabase($tpdo->getDatabase());
        } else {
            $this->setDatabase($database);
            if (!empty($repository)) {
                $this->setTPDOConnection(new TFormDinPdoConnection($database));
            }
        }
    }
    public function getTPDOConnection()
    {
        $this->initTPDOConnection(); //Garante que a conexão PDO seja inicializada sob demanda (lazy initialization)
        return $this->tpdo;
    }
    /**
     * Inicializa a conexão PDO se não estiver inicializada
     * @return void
     */
    private function initTPDOConnection()
    {
        if ($this->tpdo === null && $this->database !== null) {
            $this->setTPDOConnection(new TFormDinPdoConnection($this->database));
        }
    }
    public function setTPDOConnection(TFormDinPdoConnection $tpdo)
    {
        //FormDinHelper::validateObjTypeTPDOConnectionObj($tpdo,__METHOD__,__LINE__);
        $this->tpdo = $tpdo;
        if (!empty($tpdo->getDatabase())) {
            $this->setDatabase($tpdo->getDatabase());
        }
    }

    /**
     * Busca o nome do banco de dados
     *
     * @return string|null
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Seta o nome do banco de dados
     *
     * @param string|null $database
     * @return void
     */
    public function setDatabase(string|null $database)
    {
        $this->database = $database;
        if ($this->tpdo !== null && $database !== null) {
            $this->tpdo->setDatabase($database);
        }
    }

    /**
     * Busca o nome do repository
     *
     * @return string|null
     */
    public function getRepository()
    {
        return $this->repository;
    }
    public function setRepository(string|null $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Busca informações do banco de dados
     *
     * @param string $debugDestino Destino do debug ('tela' ou 'log')
     * @return array
     */
    public function getDatabaseInfo(string $debugDestino = TFormDinPdoConnection::DEBUG_DESTINO_TELA)
    {
        $this->initTPDOConnection(); //Garante que a conexão PDO seja inicializada sob demanda (lazy initialization)    
        return $this->getTPDOConnection()->getDatabaseInfo($debugDestino);
    }

    /**
     * Executa o comando sql recebido retornando o cursor ou verdadeiro o falso
     * se a operação foi bem sucedida.
     *
     * @param string $sql           -1: string sql do comando
     * @param array $arrParams      -2: array com o valores para bind do sql
     * @param bool $showDebugParam  -3: mostra o valor de $sql e $arrParams
     * @param bool $showInfo        -4: chama o getDatabaseInfo
     * @param string $debugDestino  -5: destino do debug ('tela' ou 'log')
     * @return mixed
     */
    public function executeSql($sql, $arrParams = null, bool $showDebugParam = false, bool $showInfo = false, string $debugDestino = 'tela')
    {
        try {
            $this->initTPDOConnection(); //Garante que a conexão PDO seja inicializada sob demanda (lazy initialization)    
            $tpdo = clone $this->getTPDOConnection();
            $result = $tpdo->executeSql($sql, $arrParams, $showDebugParam, $showInfo, $debugDestino);
            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Executa comandos SQL
     *
     * @deprecated Utilize executeSql() em seu lugar.
     * @see TFormDinGenericDAO::executeSql()
     *
     * @param string $sql
     * @param array $values
     * @return mixed|null
     */
    public function execute(string $sql, array $values)
    {
        return $this->executeSql($sql, $values);
    }

    /**
     * Executa comandos SQL e retorna os registros
     *
     * @param string $sql
     * @return array|null Array associativo (chave => valor)
     */
    public function executeSelect(string $sql)
    {
        try {
            $this->initTPDOConnection(); //Garante que a conexão PDO seja inicializada sob demanda (lazy initialization)    
            $tpdo = clone $this->getTPDOConnection();
            $tpdo->setFech(PDO::FETCH_ASSOC);
            $tpdo->setOutputFormat(ArrayHelper::TYPE_PDO);
            $tpdo->setCase(PDO::CASE_NATURAL);
            return $tpdo->executeSql($sql);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }    


    /**
     * Executa comandos SQL e retorna a quantidade de registros
     *
     * @param string $sql
     * @return mixed|null
     */
    public function executeSelectCount(string $sql)
    {
        try {
            $result = $this->executeSelect($sql);
            return ArrayHelper::get($result, 0);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Busca arrays baseado em uma criteria
     *
     * @param TCriteria $criteria
     * @param bool $showDumpLogTela
     * @return array|null Array associativo (chave => valor)
     */
    public function getArrayByCriteria(?TCriteria $criteria = null, bool $showDumpLogTela = false)
    {
        try {
            $this->initTPDOConnection(); //Garante que a conexão PDO seja inicializada sob demanda (lazy initialization)    
            $tpdo = $this->getTPDOConnection();
            $tpdo->setOutputFormat(ArrayHelper::TYPE_PDO);
            return $tpdo->selectByTCriteria($criteria, $this->getRepository(), $showDumpLogTela);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Busca objetos baseados em uma criteria
     *
     * @param TCriteria $criteria
     * @param bool $showDumpLogTela
     * @return array|null Array de objetos (TRecord)
     */
    public function getListObjByCriteria(?TCriteria $criteria = null, bool $showDumpLogTela = false)
    {
        try {
            $this->initTPDOConnection(); //Garante que a conexão PDO seja inicializada sob demanda (lazy initialization)
            $tpdo = $this->getTPDOConnection();
            $tpdo->setOutputFormat(ArrayHelper::TYPE_ADIANTI);
            return $tpdo->selectByTCriteria($criteria, $this->getRepository(), $showDumpLogTela);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Conta registros baseado em uma criteria
     *
     * @param TCriteria|null $criteria
     * @param bool $showDumpLogTela
     * @return int|null
     */
    public function getCountByCriteria(?TCriteria $criteria = null, bool $showDumpLogTela = false)
    {
        try {
            $this->initTPDOConnection(); //Garante que a conexão PDO seja inicializada sob demanda (lazy initialization)
            $tpdo = $this->getTPDOConnection();
            return $tpdo->selectByTCriteriaCount($criteria, $this->getRepository(), $showDumpLogTela);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}//fim classe