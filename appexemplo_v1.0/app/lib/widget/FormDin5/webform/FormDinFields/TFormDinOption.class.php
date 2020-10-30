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
	 * @param object $objAdiantiField  -01: Objeto de campo do Adianti
	 * @param string $id               -02: ID do campo
	 * @param string  $strLabel        -03: Label do campo
	 * @param boolean $boolRequired    -04: Obrigatorio. Default FALSE = não obrigatori, TRUE = obrigatorio
	 * @param array $mixOptions        -05: array no formato "key=>value" ou nome do pacote oracle e da função a ser executada
	 * @param array $arrValues         -06: array no formato "key=>key" para identificar a(s) opção(ões) selecionada(s)
	 * @param boolean $boolRequired    -07:
	 * @param integer $intQtdColumns   -09:
	 * @param integer $intWidth        -10:
	 * @param integer $intHeight       -09:
	 * @param integer $intPaddingItems -10: numero inteiro para definir o espaço vertical entre as colunas de opções
	 * @param boolean $boolMultiSelect -10: Default FALSE = SingleSelect, TRUE = MultiSelect
	 * @param string $strInputType     -11: define o tipo de input a ser gerado. Ex: select, radio ou check
	 * @param string $strKeyField      -12: Nome da coluna que será utilizada para preencher os valores das opções
	 * @param string $strDisplayField  -13: Nome da coluna que será utilizada para preencher as opções que serão exibidas para o usuário
	 * @param boolean $boolNowrapText  -14:
	 * @param string $strDataColumns   -15: informações extras do banco de dados que deverão ser adicionadas na tag option do campo select
	 * @return TOption
	 */
	public function __construct( $adiantiObj
							   , $strName
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
		parent::__construct($adiantiObj,$id,$label,$boolRequired,$value,null);
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
				$mixOptions = ArrayHelper::convertString2Array($mixOptions);
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