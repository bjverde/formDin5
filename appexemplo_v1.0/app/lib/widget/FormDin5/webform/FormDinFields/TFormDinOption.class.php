<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * @author Reinaldo A. Barrêto Junior
 * 
 * É uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * ----------------------------------------------------------------------------
 * This file is part of Formdin Framework.
 *
 * Formdin Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License version 3
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License version 3
 * along with this program; if not,  see <http://www.gnu.org/licenses/>
 * or write to the Free Software Foundation, Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA  02110-1301, USA.
 * ----------------------------------------------------------------------------
 * Este arquivo é parte do Framework Formdin.
 *
 * O Framework Formdin é um software livre; você pode redistribuí-lo e/ou
 * modificá-lo dentro dos termos da GNU LGPL versão 3 como publicada pela Fundação
 * do Software Livre (FSF).
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA
 * GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou
 * APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/LGPL em português
 * para maiores detalhes.
 *
 * Você deve ter recebido uma cópia da GNU LGPL versão 3, sob o título
 * "LICENCA.txt", junto com esse programa. Se não, acesse <http://www.gnu.org/licenses/>
 * ou escreva para a Fundação do Software Livre (FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02111-1301, USA.
 */


/**
 * Classe para criação campo do tipo select
 * ------------------------------------------------------------------------
 * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * os parâmetros do metodos foram marcados com:
 * 
 * NOT_IMPLEMENTED = Parâmetro não implementados, talvez funcione em 
 *                   verões futuras do FormDin. Não vai fazer nada
 * DEPRECATED = Parâmetro que não vai funcionar no Adianti e foi mantido
 *              para o impacto sobre as migrações. Vai gerar um Warning
 * FORMDIN5 = Parâmetro novo disponivel apenas na nova versão
 * ------------------------------------------------------------------------
 * 
 * @author Reinaldo A. Barrêto Junior
 */
class TFormDinOption  extends TFormDinGenericField
{
	
	const RADIO = 'radio';
	const CHECK = 'check';
	const SELECT = 'select';
	
	private $arrOptions;
	private $arrValues;
	//private $required;
	private $qtdColunms;
	private $columns;
	private $paddingRight;
	private $multiSelect;
	private $selectSize;
	private $keyField;
	private $displayField;
	private $showMinimal;
	private $nowWrapText;
	private $arrOptionsData;
    
	/**
	 * Método construtor
	 *
	 * $strDisplayColumn = nome da coluna que será utilizada para preencher as opções que serão exibidas para o usuário
	 *
	 * @abstract
	 * @param string $strName          - 1:
	 * @param array $mixOptions        - 2: array no formato "key=>value" ou nome do pacote oracle e da função a ser executada
	 * @param array $arrValues         - 3: array no formato "key=>key" para identificar a(s) opção(ões) selecionada(s)
	 * @param boolean $boolRequired    - 4:
	 * @param integer $intQtdColumns   - 5:
	 * @param integer $intWidth        - 6:
	 * @param integer $intHeight       - 7:
	 * @param integer $intPaddingItems - 8: numero inteiro para definir o espaço vertical entre as colunas de opções
	 * @param boolean $boolMultiSelect - 9:
	 * @param string $strInputType     -10: define o tipo de input a ser gerado. Ex: select, radio ou check
	 * @param string $strKeyField      -11: nome da coluna que será utilizada para preencher os valores das opções
	 * @param string $strDisplayField  -12:
	 * @param boolean $boolNowrapText  -13:
	 * @param string $strDataColumns   -14: informações extras do banco de dados que deverão ser adicionadas na tag option do campo select
	 * @return TOption
	 */
	public function __construct( $strName
	                           , $mixOptions
	                           , $arrValues=null
	                           , $boolRequired=null
                        	   , $intQtdColumns=null
                        	   , $intWidth=null
                        	   , $intHeight=null
                        	   , $intPaddingItems=null
                        	   , $boolMultiSelect=null
                        	   , $strInputType=null
                        	   , $strKeyField=null
                        	   , $strDisplayField=null
                        	   , $boolNowrapText=null
                        	   , $strDataColumns=null 
                        	   )
	{
		parent::__construct( 'div', $strName );
		$this->setValue( $arrValues );
		$this->setRequired( $boolRequired );
		$this->setQtdColumns( $intQtdColumns );
		$this->setPaddingItems( $intPaddingItems );
		$this->setFieldType( ($strInputType == null) ? self::SELECT : $strInputType );
		$this->setMultiSelect( $boolMultiSelect );
		$this->setCss( 'border',  '1px solid #c0c0c0' ); //#176 relacionado com FormDin4.js
		//$this->setClass('fwFieldBoarder');
		$this->setCss( 'display', 'inline' );
		$this->setWidth( $intWidth );
		$this->setHeight( $intHeight );
		$this->setKeyField( $strKeyField );
		$this->setDisplayField( $strDisplayField );
		$this->setOptions( $mixOptions, $strDisplayField, $strKeyField, null, $strDataColumns );
		$this->setNowrapText($boolNowrapText);
		// tratamento para campos selects postados das colunas tipo do TGrid onde os nomes são arrays
		if( $this->getFieldType() == self::SELECT && strpos( $this->getName(), '[' ) !== false ) {
	   	   $name = $this->getName();
		   $arrTemp = explode('[',$name);
		   if( isset($_POST[$arrTemp[0] ] ) )
		   {
		      $expr = '$v=$_POST["'.str_replace( '[', '"][', $name ).';';
		      if( ! preg_match('/\[\]/',$expr ))
		      {
		   		@eval( $expr );
		   		$this->setValue( $v );
		      }
		   }
		}
		//if(isset($_POST[$this->getId()]) && $_POST[$this->getId()] )
		if( isset( $_POST[ $this->getId() ] ) )
		{
			$this->setValue( $_POST[ $this->getId() ] );
		}
	}

	/**
	 * Define um array no formato "key=>value" ou string no formato "S=SIM,N=NAO,..." ou
	 * o nome de um pacoteFunção para recuperar do banco de dados, neste
	 * caso pode ser especificada a coluna chave, a coluna descrição e
	 * searchFields como parametros para a função do pacote oracle.
	 *
	 * Ex: $mixSearchFields="cod_uf=53,num_pessoa=20" ou array('COD_UF'=53,'NUM_PESSOA'=>20)
	 * Ex: $strDataColumns = "cod_uf,sig_uf,cod_regiao"
	 *
	 * @param mixed $mixOptions
	 * @param string $strDisplayField
	 * @param string $strKeyField
	 * @param mixed $mixSearchFields
	 * @param string $strDataColumns
	 */
	public function setOptions( $mixOptions=null, $strDisplayField=null, $strKeyField=null, $mixSearchFields=null, $strDataColumns=null )
	{
		if( isset( $mixOptions ) ) {

			if( !is_null($strDataColumns) && trim( $strDataColumns) != '' ) {
				$arrDataColumns	= explode(',',$strDataColumns);
				$strDataColumns	= ','.$strDataColumns.' ';
			}

			if( is_string( $mixOptions ) ) {
				$where = null;
				$cacheSeconds = null;
				if( preg_match('/\|/',$mixOptions)){
					$mixOptions  	= explode( '|', $mixOptions );
					$mixOptions[1]  = ( isset( $mixOptions[1] ) ? $mixOptions[1] : '' );
					// segundo parametro pode ser o where ou tempo de cache
					$where 		 = is_numeric($mixOptions[1] ) ? '' : $mixOptions[1];
					$cacheSeconds= is_numeric($mixOptions[1])  ? $mixOptions[1] : null;
					$mixOptions  = $mixOptions[0];
				}
				// verificar se passou no formato "S=SIM,N=NAO...."
				if( strpos( $mixOptions, '=' ) || strlen($mixOptions)==1 )
				{

				// tratar opção de 1 caractere. Ex: S,N,1,2...
					if( strlen($mixOptions)==1 ){
						$mixOptions = array( 'N'=>'' );
					} else {
						// tratar formato S=>SIM,N=>NÃO
						$mixOptions = preg_replace('/\=\>/','=',$mixOptions);
						$mixOptions = explode( ',', $mixOptions );
						forEach( $mixOptions as $k=>$v )
						{
							$v = explode( '=', $v );
							$v[ 1 ] = ( isset( $v[ 1 ] ) ) ? $v[ 1 ] : $v[ 0 ];
							$arrTemp[ $v[ 0 ] ] = $v[ 1 ];
						}
						$mixOptions = $arrTemp;
						$arrTemp = null;
					}
				}
				else
				{
					if( function_exists( 'recuperarPacote' ) )
					{
						$packageName 	= $mixOptions;
						$mixOptions = null;
						$searchFields = null;
						if( isset( $mixSearchFields ) )
						{
							if( is_string( $mixSearchFields ) )
							{
								$searchFields = explode( ',', $mixSearchFields );
								if( is_array( $searchFields ) )
								{
									forEach( $searchFields as $k=>$v )
									{
										$v = explode( '=', $v );
										$bvars[ $v[ 0 ] ] = $v[ 1 ];
									}
								}
							}
							else if( is_array( $mixSearchFields ) )
							{
								$bvars = $mixSearchFields;
							}
						}
						// se passou somente o nome da tabela , criar comando select
						if( preg_match( '/\.PK\a?/i', $packageName ) )
						{
							print_r( recuperarPacote( $packageName, $bvars, $mixOptions, $cacheSeconds ) );
						}
						else
						{
							if( $strKeyField && $strDisplayField )
							{
								$sql = "select {$strKeyField},{$strDisplayField}{$strDataColumns} from  {$packageName} order by {$strDisplayField}";
							}
							else
							{
								if( !preg_match( '/' . ESQUEMA . '\./', $packageName ) )
								{
									$packageName = ESQUEMA . '.' . $packageName;
								}
								$sql = "select * from {$packageName}";
							}
							$bvars = null;
							$nrows = 0;
							$mixOptions = null;
							if( $GLOBALS[ 'conexao' ] )
							{
								if( $GLOBALS[ 'conexao' ]->executar_recuperar( $sql, $bvars, $mixOptions, $nrows ) )
								{
									echo 'Erro na execução do sql:' . $sql;
								}
							}
						}
					}
					else
					{
						if( TPDOConnection::getInstance() )
						{
							if( preg_match( '/^select/i', $mixOptions ) > 0 )
							{
								$mixOptions = TPDOConnection::executeSql( $mixOptions );
							}
							else
							{
								if( !is_null( $where ) )
								{
									$where = ' where ' . preg_replace( '/"/', "'", $where );
								}
								else
								{
									$where = '';
								}
								if( $this->getKeyField() && $this->getDisplayField() )
								{
									$sql = "select {$this->getKeyField()},{$this->getDisplayField()}{$strDataColumns} from {$mixOptions} {$where} order by {$this->getDisplayField()}";
								}
								else
								{
									$sql = "select * from {$mixOptions} {$where}";
								}
								$mixOptions = TPDOConnection::executeSql( $sql );
							}
							TPDOConnection::showError();
						}
					}
				}
			}

			$this->arrOptions = null;
			if( is_array( $mixOptions ) )
			{
				// verificar se o array está no formato oracle
				if( key( $mixOptions ) && is_array( $mixOptions[ key( $mixOptions ) ] ) )
				{
					// assumir a primeira e segunda coluna para popular as opções caso não tenha sido informadas
					if( !isset( $strKeyField ) )
					{
						if( !$this->getKeyField() )
						{
							list($strKeyField) = array_keys( $mixOptions );
						}
						else
						{
							$strKeyField = $this->getKeyField();
						}
					}
					if( !isset( $strDisplayField ) )
					{
						if( !$this->getDisplayField() )
						{
							list(, $strDisplayField) = array_keys( $mixOptions );
						}
						else
						{
							$strDisplayField = $this->getDisplayField();
						}
						if( !isset( $strDisplayField ) )
						{
							$strDisplayField = $strKeyField;
						}
					}
					if( $strKeyField && $strDisplayField )
					{
						// reconhecer nome da columa em caixa baixa ou alta
						if( !array_key_exists( $strKeyField, $mixOptions ) )
						{
							$strKeyField = strtoupper( $strKeyField );
							$strDisplayField = strtoupper( $strDisplayField );
						}
						if( !array_key_exists( $strKeyField, $mixOptions ) )
						{
							$strKeyField = strtolower( $strKeyField );
							$strDisplayField = strtolower( $strDisplayField );
						}
						if( is_array( $mixOptions[ $strKeyField ] ) )
						{
							foreach( $mixOptions[ $strKeyField ] as $k=>$v )
							{
								$this->arrOptions[ $v ] = $mixOptions[ $strDisplayField ][ $k ];
								if( isset( $arrDataColumns ) && is_array( $arrDataColumns ) )
								{
									foreach($arrDataColumns as $colName )
									{
										$value='';
										if( isset( $mixOptions[$colName][$k] ) )
										{
											$value = $mixOptions[$colName][$k];
										}
										elseif( isset( $mixOptions[strtoupper($colName) ][$k] ) )
										{
											$value = $mixOptions[strtoupper($colName) ][$k];
										}
										elseif( isset( $mixOptions[strtolower($colName) ][$k] ) )
										{
											$value = $mixOptions[strtolower($colName)][$k];
										}
										$value = $this->specialChars2htmlEntities( $value );
										$value = preg_replace("/\n/",' ',$value);
										$this->arrOptionsData[$v]['data-'.strtolower($colName)] = $value;
									}
								}
							}
						}
					}
				}
				else
				{
					$this->arrOptions = $mixOptions;
				}
			}
		}
		return $this;
	}

	/**
	 * Recupera o array de opções do campo
	 *
	 */
	public function getOptions()
	{
		return $this->arrOptions;
	}


}