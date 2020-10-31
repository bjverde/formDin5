<?php
class mockFormDinArray
{
    
    /**
     *
     * @var TGrid
     */
    private $tGrid;
    private $dataGrid;
    
    
    public function incluirPessoa($dadosPessoa, $id, $nome, $tipo, $cpf, $cnpj,$dtnascimento){
        $dadosPessoa['IDPESSOA'][]=$id;
        $dadosPessoa['NMPESSOA'][]=$nome;
        $dadosPessoa['TPPESSOA'][]=$tipo;
        $dadosPessoa['NMCPF'][]=$cpf;
        $dadosPessoa['NMCNPJ'][]=$cnpj;
        $dadosPessoa['DTNASCIMENTO'][]=$dtnascimento;
        return $dadosPessoa;
    }
    
    public function generateTable(){
        $dadosPessoa = array();
        $dadosPessoa = $this->incluirPessoa($dadosPessoa, 1, 'Joao Silva'   , 'F', '12345678909', null            ,'1990-05-01');
        $dadosPessoa = $this->incluirPessoa($dadosPessoa, 2, 'Maria Laranja', 'F', '52798074002', null            ,'1980-12-10');
        $dadosPessoa = $this->incluirPessoa($dadosPessoa, 3, 'Dell'         , 'J', null         , '72381189000110','1984-04-01');
        $dadosPessoa = $this->incluirPessoa($dadosPessoa, 4, 'Microsoft'    , 'J', null         , '72381189000110','1975-05-01');
        
        return $dadosPessoa;
    }
    
    public function incluirPessoaPDO($id, $nome, $tipo, $cpf, $cnpj,$dtnascimento){
        $dadosPessoa=array();
        $dadosPessoa['IDPESSOA']=$id;
        $dadosPessoa['NMPESSOA']=$nome;
        $dadosPessoa['TPPESSOA']=$tipo;
        $dadosPessoa['NMCPF']=$cpf;
        $dadosPessoa['NMCNPJ']=$cnpj;
        $dadosPessoa['DTNASCIMENTO']=$dtnascimento;
        return $dadosPessoa;
    }
    
    public function generateTablePessoaPDO(){
        $dadosPessoa = array();
        $dadosPessoa[] = $this->incluirPessoaPDO(1, 'Joao Silva'   , 'F', '12345678909', null            ,'1990-05-01');
        $dadosPessoa[] = $this->incluirPessoaPDO(2, 'Maria Laranja', 'F', '52798074002', null            ,'1980-12-10');
        $dadosPessoa[] = $this->incluirPessoaPDO(3, 'Dell'         , 'J', null         , '72381189000110','1984-04-01');
        $dadosPessoa[] = $this->incluirPessoaPDO(4, 'Microsoft'    , 'J', null         , '72381189000110','1975-05-01');
        
        return $dadosPessoa;
    }

    public function incluirPessoaAdianti($id, $nome, $tipo, $cpf, $cnpj, $dtnascimento){
        $dadosPessoa = new StdClass;
        $dadosPessoa->IDPESSOA = $id;
        $dadosPessoa->NMPESSOA = $nome;
        $dadosPessoa->TPPESSOA = $tipo;
        $dadosPessoa->NMCPF    = $cpf;
        $dadosPessoa->NMCNPJ   = $cnpj;
        $dadosPessoa->DTNASCIMENTO = $dtnascimento;
        return $dadosPessoa;
    }

    public function generateTablePessoaAdianti(){
        $dadosPessoa = array();
        $dadosPessoa[] = $this->incluirPessoaAdianti(1, 'Joao Silva'   , 'F', '12345678909', null            ,'1990-05-01');
        $dadosPessoa[] = $this->incluirPessoaAdianti(2, 'Maria Laranja', 'F', '52798074002', null            ,'1980-12-10');
        $dadosPessoa[] = $this->incluirPessoaAdianti(3, 'Dell'         , 'J', null         , '72381189000110','1984-04-01');
        $dadosPessoa[] = $this->incluirPessoaAdianti(4, 'Microsoft'    , 'J', null         , '72381189000110','1975-05-01');
        
        return $dadosPessoa;
    }


    public function incluirPessoaAdianti_MixCase($id, $nome, $tipo, $cpf, $cnpj, $dtnascimento){
        $dadosPessoa = new StdClass;
        $dadosPessoa->IDPESSOA = $id;
        $dadosPessoa->nmpessoa = $nome;
        $dadosPessoa->TPPESSOA = $tipo;
        $dadosPessoa->nmcpf    = $cpf;
        $dadosPessoa->NMCNPJ   = $cnpj;
        $dadosPessoa->dtnascimento = $dtnascimento;
        return $dadosPessoa;
    }

    public function generateTablePessoaAdianti_MixCase(){
        $dadosPessoa = array();
        $dadosPessoa[] = $this->incluirPessoaAdianti_MixCase(1, 'Joao Silva'   , 'F', '12345678909', null            ,'1990-05-01');
        $dadosPessoa[] = $this->incluirPessoaAdianti_MixCase(2, 'Maria Laranja', 'F', '52798074002', null            ,'1980-12-10');
        $dadosPessoa[] = $this->incluirPessoaAdianti_MixCase(3, 'Dell'         , 'J', null         , '72381189000110','1984-04-01');
        $dadosPessoa[] = $this->incluirPessoaAdianti_MixCase(4, 'Microsoft'    , 'J', null         , '72381189000110','1975-05-01');
        
        return $dadosPessoa;
    }
}