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
            $this->database = $tpdo->getDatabase();
        } elseif (!empty($this->database)) {
            $tpdo->setDatabase($this->database);
        }
    }

    /**
     * Busca o nome do banco de dados
     *
     * @return string|null
     */
    public function getDatabase(): string|null
    {
        if ($this->tpdo !== null && !empty($this->tpdo->getDatabase())) {
            $this->database = $this->tpdo->getDatabase();
        }
        return $this->database;
    }

    /**
     * Seta o nome do banco de dados
     *
     * @param string|null $database
     * @return void
     */
    public function setDatabase(string|null $database): void
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
            throw new Exception($e->getMessage(), (int) $e->getCode(), $e);
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
            throw new Exception($e->getMessage(), (int) $e->getCode(), $e);
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
            throw new Exception($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Transforma uma lista de registros em um array associativo chave => valor
     *
     * @param string $colunaChave Nome da coluna chave
     * @param string $colunaValor Nome da coluna valor
     * @param array|null $list Lista de registros
     * @param int $typeCase PDO::CASE_NATURAL, PDO::CASE_UPPER, PDO::CASE_LOWER
     * @return array
     */
    public static function getArrayKeyValue(string $colunaChave, string $colunaValor, ?array $list, int $typeCase = PDO::CASE_NATURAL): array
    {
        if (empty($list)) {
            return [];
        }
        return ArrayHelper::convertArray2PhpKeyValue($list, $colunaChave, $colunaValor, $typeCase);
    }

    /**
     * Executa comandos SQL e retorna um array associativo chave => valor
     *
     * @param string $colunaChave Nome da coluna chave
     * @param string $colunaValor Nome da coluna valor
     * @param string $sql Comando SQL
     * @param array|null $values Valores para bind
     * @return array
     */
    public function getArrayKeyValueBySql(string $colunaChave, string $colunaValor, string $sql, ?array $values = null): array
    {
        $resultList = $this->executeSql($sql, $values);
        $case = $this->getTPDOConnection()?->getCase() ?? PDO::CASE_NATURAL;
        return self::getArrayKeyValue($colunaChave, $colunaValor, $resultList, $case);
    }

    /**
     * Busca arrays baseado em uma criteria
     *
     * @param TCriteria|null $criteria
     * @param bool $showDumpLogTela
     * @return array|null Array associativo (chave => valor)
     */
    public function getArrayByCriteria(?TCriteria $criteria = null, bool $showDumpLogTela = false)
    {
        try {
            $this->initTPDOConnection(); //Garante que a conexão PDO seja inicializada sob demanda (lazy initialization)    
            $tpdo = $this->getTPDOConnection();
            $tpdo->openTransaction($showDumpLogTela);
            $repository = new TRepository($this->getRepository());
            $collections = $repository->load($criteria);
            $result = ArrayHelper::convertArray2OutputFormat($collections, ArrayHelper::TYPE_PDO, $tpdo->getCase());
            TTransaction::close();
            return $result;
        } catch (Exception $e) {
            TTransaction::rollback();
            throw new Exception($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Busca objetos baseados em uma criteria
     *
     * @param TCriteria|null $criteria
     * @param bool $showDumpLogTela
     * @return array|null Array de objetos (TRecord)
     */
    public function getListObjByCriteria(?TCriteria $criteria = null, bool $showDumpLogTela = false)
    {
        try {
            $this->initTPDOConnection(); //Garante que a conexão PDO seja inicializada sob demanda (lazy initialization)
            $tpdo = $this->getTPDOConnection();
            $tpdo->openTransaction($showDumpLogTela);
            $repository = new TRepository($this->getRepository());
            $collections = $repository->load($criteria);
            $result = ArrayHelper::convertArray2OutputFormat($collections, ArrayHelper::TYPE_ADIANTI, $tpdo->getCase());
            TTransaction::close();
            return $result;
        } catch (Exception $e) {
            TTransaction::rollback();
            throw new Exception($e->getMessage(), (int) $e->getCode(), $e);
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
            $tpdo->openTransaction($showDumpLogTela);
            $repository = new TRepository($this->getRepository());
            $count = $repository->count($criteria);
            TTransaction::close();
            return $count;
        } catch (Exception $e) {
            TTransaction::rollback();
            throw new Exception($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}//fim classe