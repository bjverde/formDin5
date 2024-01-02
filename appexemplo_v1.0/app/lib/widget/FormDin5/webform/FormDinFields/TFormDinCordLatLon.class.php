<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * @author Reinaldo A. Barrêto Junior
 * 
 * É uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * @author Luís Eugênio Barbosa do FormDin 4
 * 
 * Adianti Framework é uma criação Adianti Solutions Ltd
 * @author Pablo Dall'Oglio
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

class TFormDinCordLatLon extends TFormDinGenericField
{
    protected $adiantiObj;
    private $class = array();
    private $showFields = null;

    /**
     * Pegar informações geolocalização do navegador
     *
     * @param string  $idField         -01: ID do campo
     * @param string  $label           -02: Label do campo, usado para validações
     * @param boolean $boolRequired    -03: Campo obrigatório ou não. Default FALSE = não obrigatório, TRUE = obrigatório
     * @param boolean $showFields      -04: TRUE (Default) or FALSE, Show fields latitude and longitude
     * @param boolean $showAltitude    -05: TRUE (Default) or FALSE, Show field  altitude
     * @param boolean $fieldsReadOnly  -06: TRUE (Default) or FALSE, Field read only
     * @param boolean $fielAllJson     -07: TRUE (Default) or FALSE, Cria um campo oculta que vai receber um JSON com todos os atributos
     * @return TElement
     */
    public function __construct(string $idField
                               ,string $label
                               ,$boolRequired = false
                               ,$showFields = true
                               ,$showAltitude = true
                               ,$fieldsReadOnly= true
                               ,$fielAllJson = true
                               )
    {
        $fd5HiddenJson  = new TFormDinHiddenField($idField.'_json',null,$boolRequired);
        $adObjHiddenJson= $fd5HiddenJson->getAdiantiObj();        

        $fd5Lat = new TFormDinNumericField($idField.'_lat','Latitude',18,$boolRequired,16,false,null,-90,90,false,null,null,null,null,null,null,true,null,'.');
        $adiantiObjLat = $fd5Lat->getAdiantiObj();

        $fd5Lon = new TFormDinNumericField($idField.'_lon','Longitude',18,$boolRequired,16,false,null,-90,90,false,null,null,null,null,null,null,true,null,'.');
        $adiantiObjLon = $fd5Lon->getAdiantiObj();

        $fd5Alt = new TFormDinNumericField($idField.'_alt','Altitude',18,$boolRequired,16,false,null,-90,90,false,null,null,null,null,null,null,true,null,'.');
        $adiantiObjAlt = $fd5Alt->getAdiantiObj();

        $scriptJsGeo = new TElement('script');
        $scriptJsGeo->setProperty('src', 'app/lib/widget/FormDin5/javascript/FormDin5GeoLocation.js?appver='.FormDinHelper::version());

        $btnGeo = new TButton('btnScreenshot');
        $btnGeo->class = 'btn btn-primary btn-sm';
        $btnGeo->setLabel('Informar Geolocalização');
        $btnGeo->setImage('fas:map-marker');
        $btnGeo->addFunction("fd5GetLocation('".$idField."')");        

        $idDivGeo = $idField.'_videodiv';
        $divGeo = new TElement('div');
        $divGeo->class = 'fd5DivVideo';
        $divGeo->setProperty('id',$idDivGeo);
        $divGeo->add($btnGeo);
        $divGeo->add($adObjHiddenJson);
        $divGeo->add($adiantiObjLat);
        $divGeo->add($adiantiObjLon);
        $divGeo->add($adiantiObjAlt);
        $divGeo->add($scriptJsGeo);

        $adiantiObj = $divGeo;
        parent::__construct($adiantiObj,$idDivGeo,$label,false,null,null);
        return $this->getAdiantiObj();
    }

    private function setProperty($property, $valeu)
    {
        if($valeu==true){
            $this->getAdiantiObj()->setProperty($property, 'true');
        }
    }
    public function loop($valeu)
    {
        $this->setProperty('loop', $valeu);
    }    
    public function controls($valeu)
    {
        $this->setProperty('controls', $valeu);
    }
    public function muted($valeu)
    {
        $this->setProperty('muted', $valeu);
    }
    public function autoplay($valeu)
    {
        $this->setProperty('autoplay', $valeu);
        $this->setProperty('muted', $valeu);
    }
}