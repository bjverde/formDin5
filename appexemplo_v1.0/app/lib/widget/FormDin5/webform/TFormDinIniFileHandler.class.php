<?php
class TFormDinIniFileHandler {
    private $filePath;
    private $iniData;

    public function __construct($filePath = null){
        if( !empty($filePath) ){
            $this->setfilePath($filePath);
        }
    }

    private function load() {
        if (!file_exists($this->filePath)) {
            throw new Exception("Arquivo INI não encontrado: " . $this->filePath);
        }

        $this->iniData = parse_ini_file($this->filePath, true);
        if ($this->iniData === false) {
            throw new Exception("Falha ao ler o arquivo INI: " . $this->filePath);
        }
    }

    public function setfilePath($filePath){
        $this->filePath = $filePath;
        $this->load();
    }
    public function getfilePath(){
        return $this->filePath;
    }

    /**
     * Recupera o valor de uma chave em uma seção
     *
     * @param string $section 01 - nome da seção
     * @param string $key     02 - nome da chave
     * @return string
     */
    public function getValue($section, $key) {
        if (!isset($this->iniData[$section])) {
            throw new Exception("Seção '$section' não encontrada no arquivo INI.");
        }
        if (!isset($this->iniData[$section][$key])) {
            throw new Exception("Chave '$key' não encontrada na seção '$section'.");
        }
        return $this->iniData[$section][$key];
    }

    /**
     * Recupera o valor boleano de uma chave em uma seção
     *
     * @param string $section 01 - nome da seção
     * @param string $key     02 - nome da chave
     * @return bolean
     */    
    public function getValueWithBolean($section, $key) {
        $valor = $this->getValue($section, $key);
        return $this->testBolean($valor);
    }

    public function setValue($section, $key, $value) {
        if (!isset($this->iniData[$section])) {
            $this->iniData[$section] = [];
        }
        $this->iniData[$section][$key] = $value;
    }

    public function save() {
        $newContent = '';
        foreach ($this->iniData as $section => $data) {
            $newContent .= "[$section]\n";
            foreach ($data as $key => $value) {
                $newContent .= "$key = \"$value\"\n";
            }
            $newContent .= "\n";
        }

        if (file_put_contents($this->filePath, $newContent) === false) {
            throw new Exception("Falha ao escrever no arquivo INI.");
        }
    }

    /**
     * Verifica se o valor do parametro do arquivo tem um valor boleano. Pode ser
     * 1 ou true ou sim ou S ou yes ou Y
     *
     * @param mix|string $valor
     * @return bolean
     */
    public static function testBolean($valor){
        $result = false;
        $valor = strtoupper($valor);
        if( $valor==1 || $valor==true || $valor=='TRUE' || $valor=='SIM' || $valor=='S' || $valor=='YES' || $valor=='Y' ){
            $result = true;
        }
        return $result;
    }
}
