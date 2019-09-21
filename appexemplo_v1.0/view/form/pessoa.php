<?php
/**
 * System generated by SysGen (System Generator with Formdin Framework) 
 * Download SysGen: https://github.com/bjverde/sysgen
 * Download Formdin Framework: https://github.com/bjverde/formDin
 * 
 * SysGen  Version: 1.3.1-alpha
 * FormDin Version: 4.4.3-alpha
 * 
 * System xx created in: 2019-04-11 00:32:52
 */

defined('APLICATIVO') or die();

$primaryKey = 'IDPESSOA';
$frm = new TForm('Cadastro de Pessoas',800,950);
$frm->setFlat(true);
$frm->setMaximize(true);


$html1 = 'Esse form foi criado com o <a href="https://github.com/bjverde/sysgen">Sysgen 1.3.0</a></i>.
          <br>
          <br>Utiliza a tabela PESSOA do banco de dados bdApoio.s3db ( sqlite )  ';

$frm->addHiddenField( 'BUSCAR' ); //Campo oculto para buscas
$frm->addHiddenField( $primaryKey );   // coluna chave da tabela
$frm->addHtmlField('html1', $html1, null, null, null, null)->setCss('border', '1px solid #ffeb3b')->setCss('background-color', '#ffffcc')->setCss('margin-bottom', '10px');
$frm->addTextField('NOM_PESSOA', 'Nome',50,true,50);
$frm->addCpfCnpjField('CPF_CNPJ', 'CPF/CNPJ',true);
$listTP = array('PF'=>'Pessoa Física','PJ'=>'Pessoa Juridica');
$frm->addSelectField('TP_PESSOA', 'Tipo de Pessoa', true, $listTP);

$frm->addButton('Buscar', null, 'btnBuscar', 'buscar()', null, true, false);
$frm->addButton('Salvar', null, 'Salvar', null, null, false, false);
$frm->addButton('Limpar', null, 'Limpar', null, null, false, false);


$acao = isset($acao) ? $acao : null;
switch( $acao ) {
    //--------------------------------------------------------------------------------
    case 'Limpar':
        $frm->clearFields();
    break;
    case 'Salvar':
        try{
            if ( $frm->validate() ) {
                $vo = new PessoaVO();
                $frm->setVo( $vo );
                $class = new Pessoa();
                $resultado = $class->save( $vo );
                if($resultado==1) {
                    $frm->setMessage('Registro gravado com sucesso!!!');
                    $frm->clearFields();
                }else{
                    $frm->setMessage($resultado);
                }
            }
        }
        catch (DomainException $e) {
            $frm->setMessage( $e->getMessage() );
        }
        catch (Exception $e) {
            MessageHelper::logRecord($e);
            $frm->setMessage( $e->getMessage() );
        }
    break;
    //--------------------------------------------------------------------------------
    case 'gd_excluir':
        try{
            $id = $frm->get( $primaryKey ) ;
            $class = new Pessoa();
            $resultado = $class->delete( $id );
            if($resultado==1) {
                $frm->setMessage('Registro excluido com sucesso!!!');
                $frm->clearFields();
            }else{
                $frm->clearFields();
                $frm->setMessage($resultado);
            }
        }
        catch (DomainException $e) {
            $frm->setMessage( $e->getMessage() );
        }
        catch (Exception $e) {
            MessageHelper::logRecord($e);
            $frm->setMessage( $e->getMessage() );
        }
    break;
}


function getWhereGridParameters(&$frm)
{
    $retorno = null;
    if($frm->get('BUSCAR') == 1 ){
        $retorno = array(
                'IDPESSOA'=>$frm->get('IDPESSOA')
                ,'NOM_PESSOA'=>$frm->get('NOM_PESSOA')
                ,'CPF_CNPJ'=>$frm->get('CPF_CNPJ')
                ,'TP_PESSOA'=>$frm->get('TP_PESSOA')
        );
    }
    return $retorno;
}

if( isset( $_REQUEST['ajax'] )  && $_REQUEST['ajax'] ) {
    $maxRows = ROWS_PER_PAGE;
    $whereGrid = getWhereGridParameters($frm);
    $dados = Pessoa::selectAll( $primaryKey, $whereGrid );
    $mixUpdateFields = $primaryKey.'|'.$primaryKey
                    .',NOM_PESSOA|NOM_PESSOA'
                    .',CPF_CNPJ|CPF_CNPJ'
                    .',TP_PESSOA|TP_PESSOA'
                    ;
    $gride = new TGrid( 'gd'                        // id do gride
    				   ,'Gride with SQL Pagination' // titulo do gride
    				   );
    $gride->addKeyField( $primaryKey ); // chave primaria
    $gride->setData( $dados ); // array de dados
    $gride->setMaxRows( $maxRows );
    $gride->setUpdateFields($mixUpdateFields);
    $gride->setUrl( 'view/form/pessoa.php' );

    $gride->addColumn($primaryKey,'id');
    $gride->addColumn('NOM_PESSOA','Nome');
    $gride->addColumn('CPF_CNPJ','CPF/CNPJ');
    $gride->addColumn('TP_PESSOA','Tipo');


    $gride->show();
    die();
}

$frm->addHtmlField('gride');
$frm->addJavascript('init()');
$frm->show();

?>
<script>
function init() {
    var Parameters = {"BUSCAR":""
                    ,"IDPESSOA":""
                    ,"NOM_PESSOA":""
                    ,"CPF_CNPJ":""
                    ,"TP_PESSOA":""
                    };
    fwGetGrid('view/form/pessoa.php','gride',Parameters,true);
}
function buscar() {
    jQuery("#BUSCAR").val(1);
    init();
}
</script>