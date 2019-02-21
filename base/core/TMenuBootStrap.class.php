<?php
/*
 * FormDin 5 Framework
 * Created by Reinaldo A. Barrêto Jr em 2019
 * Based on FormDin 4 de Luís Eugênio Barbosa
 * https://github.com/bjverde/formDin5
 *
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

class TMenuBootStrap {

    public function __construct() {
    }
    
	public function getNavBarButton()
	{
        $button = new TElement('button');
        $button->setClass('navbar-toggler');
        $button->setAttribute('type','button');
        $button->setAttribute('data-toggle','collapse');
        $button->setAttribute('data-target','#navbarsExampleDefault');
        $button->setAttribute('aria-controls','navbarsExampleDefault');
        $button->setAttribute('aria-expanded','false');
        $button->setAttribute(' aria-label','Toggle navigation');
        return $button;
    }

    public function getNavBar()
	{
        $navBar = new TElement('nav');
        $navBar->setClass('sticky-top navbar navbar-expand-md navbar-dark bg-dark');
        $navBar->add( $this->getNavBarButton() );
        return $navBar;
    }

	public function getMenuBootStrap($menuFile, $print=true)
	{
        if ( !file_exists($menuFile) ) {
            throw new InvalidArgumentException (TMesage::MENU_FILE_FAIL);
        }
        //ob_start();
        //include $menuFile;
        //$result = ob_get_clean();

        $result = '<menu>
        <item id="1" text="Campos" img="user916.gif">
            <item id="11" text="Campo Texto">
               <tooltip>Declaração de texto</tooltip>
                <item id="11.1" text="Campo Texto Simples">
                    <item id="11.1.1" text="Campo Texto">
                       <userdata name="url">view/fields/exe_TextField.php</userdata>
                       <userdata name="jsonParams">{"p1":"parametro_1","p2":"parametro_2"}</userdata>
                    </item>
                    <item id="11.1.2" text="Entrada com Máscara">
                       <userdata name="url">view/fields/exe_maskField.php</userdata>
                    </item>
                    <item id="11.2.3" text="Campo Memo">
                       <userdata name="url">view/fields/exe_TMemo.php</userdata>
                    </item>
                </item>
                <item id="11.2" text="Campo Texto Richo">
                    <item id="11.2.1" text="Campo Memo com tinyMCE">
                       <userdata name="url">view/fields/exe_Ttinymce.php</userdata>
                    </item>
                    <item id="11.2.2" text="Campo Editor com CkEditor">
                       <userdata name="url">view/fields/exe_TTextEditor.php</userdata>
                    </item>
                </item>
                <item id="11.3" text="Campo Texto funções">
                    <item id="11.3.1" text="Autocompletar">
                       <userdata name="url">view/fields/exe_autocomplete.php</userdata>
                    </item>
                    <item id="11.3.2" text="Autocompletar II">
                       <userdata name="url">view/fields/exe_autocomplete2.php</userdata>
                    </item>
                    <item id="11.3.3" text="Consulta On-line">
                       <userdata name="url">view/fields/exe_onlinesearch.php</userdata>
                    </item>
                    <item id="11.3.4" text="Consulta On-line I (ERRO)">
                       <userdata name="url">view/fields/exe_onlinesearch1.php</userdata>
                    </item>
                    <item id="11.3.5" text="Autocompletar 3 + Consulta On-line">
                       <userdata name="url">view/fields/exe_autocomplete3.php</userdata>
                    </item>
                </item>
            </item>
        
            <item id="12" text="Campo HTML">
                <item id="12.1" text="Campo HTML">
                   <userdata name="url">view/fields/exe_HtmlField.php</userdata>
                </item>
                <item id="12.2" text="Campo HTML com iFrame">
                   <userdata name="url">modulos/iframe_phpinfo/ambiente_phpinfo.php</userdata>
                </item>
            </item>
            
            <item id="13" text="Campo Coord GMS">
                <item id="13.1" text="Campo Coord GMS">
                   <userdata name="url">view/fields/exe_CoordGmsField.php</userdata>
                </item>
                <item id="13.2" text="Campo Coord GMS 02">
                   <userdata name="url">view/fields/exe_CoordGmsField02.php</userdata>
                </item>
            </item>
            <item id="14" text="Campo Select">
                <item id="14.1" text="Campo Select - Simples">
                   <userdata name="url">view/fields/exe_SelectField_01.php</userdata>
                </item>
                <item id="14.2" text="Campo Select - Combinados">
                   <userdata name="url">view/fields/exe_SelectField_02.php</userdata>
                </item>
            </item>
            <item id="15" text="Campo Radio">
               <userdata name="url">view/fields/exe_RadioField.php</userdata>
            </item>
            <item id="16" text="Campo Check" img="../../base/imagens/iconCheckAll.gif">
               <userdata name="url">view/fields/exe_CheckField.php</userdata>
            </item>
            <item id="17" text="Campo Arquivo ou Blob">
                <item id="171" text="Campo Blob">
                <item id="1171" text="Campo Blob Salvo no Banco">
                   <userdata name="url">view/fields/exe_fwShowBlob.php</userdata>
                </item>
                <item id="1172" text="Campo Blob Salvo no Disco">
                   <userdata name="url">view/fields/exe_fwShowBlobDisco.php</userdata>
                </item>
                </item>
                <item id="172" text="Campo Arquivo simples">
                <item id="1721" text="Assincrono">
                   <userdata name="url">view/fields/exe_FileAsync.php</userdata>
                </item>
                <item id="1722" text="Normal">
                   <userdata name="url">view/fields/exe_TFile.php</userdata>
                </item>
                <item id="1723" text="TAssincrono">
                   <userdata name="url">view/fields/exe_TFileAsync.php</userdata>
                </item>
                </item>
                <item id="173" text="Cadastro Arquivo Postgres">
                   <tooltip>Exemplo de Upload de imagem que mostra o arquivo antes de finalizar</tooltip>
                   <userdata name="url">pdo/exe_pdo_4.php</userdata>
                </item>
            </item>
            <item id="18" text="Campo Numérico">
               <userdata name="url">view/fields/exe_NumberField.php</userdata>
            </item>
            <item id="19" text="Campo Brasil" img="../../base/imagens/flag_brazil.png">
                <item id="191" text="Campo CEP">
                   <userdata name="url">view/fields/exe_CepField.php</userdata>
                </item>
                <item id="192" text="Campo Telefone">
                   <userdata name="url">view/fields/exe_FoneField.php</userdata>
                </item>
                <item id="193" text="Campo Cpf/Cnpj">
                   <userdata name="url">view/fields/exe_campo_cpf_cnpj.php</userdata>
                </item>
            </item>
            <item id="110" text="Campos Data e hora">
                <item id="1101" text="Campo Data">
                   <userdata name="url">view/fields/exe_DateField.php</userdata>
                </item>
                <item id="1102" text="Campo Hora">
                   <userdata name="url">view/fields/exe_campo_hora.php</userdata>
                </item>
                <item id="1104" text="Campo Agenda">
                   <userdata name="url">view/fields/exe_TCalendar.php</userdata>
                </item>
            </item>
            <item id="111" text="Campo Select Diretorio/Pasta">
               <userdata name="url">view/fields/exe_OpenDirField.php</userdata>
            </item>
            <item id="115" text="Campo Senha" img="../../base/imagens/lock16.gif">
               <userdata name="url">view/fields/exe_TPasswordField.php</userdata>
            </item>
            <item id="117" text="Campo Captcha">
               <userdata name="url">view/fields/exe_TCaptchaField.php</userdata>
            </item>
            <item id="119" text="Campo Cor">
               <userdata name="url">view/fields/exe_TColorPicker.php</userdata>
            </item>
            <item id="120" text="Tecla de Atalho">
               <userdata name="url">view/fields/exe_Shortcut.php</userdata>
            </item>
            <item id="121" text="Campo Link">
               <userdata name="url">view/fields/exe_field_link.php</userdata>
            </item>
            <item id="122" text="Redirect">
               <userdata name="url">exe_redirect.inc</userdata>
            </item>
            <item id="123" text="TZip">
               <userdata name="url">exe_TZip.php</userdata>
            </item>
            <item id="124" text="E-mail" img="../../base/imagens/email.png">
               <userdata name="url">view/fields/exe_TEmail.php</userdata>
            </item>
        </item>
        </menu>';

        $xmlMenu = new DOMDocument();
        $xmlMenu->loadXml($result);

        var_dump($xmlMenu);

        /*
        foreach($xmlMenu->getElementsByTagName('item') as $item) {
            echo $item->getAttribute('item'), "\n";
            echo "+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+\n";
        }
        */

        $navBar = $this->getNavBar();
        return $navBar;
	}
}
?>