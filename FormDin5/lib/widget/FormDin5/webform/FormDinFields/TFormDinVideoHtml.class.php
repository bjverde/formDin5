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

class TFormDinVideoHtml extends TFormDinGenericField
{
    protected $adiantiObj;
    private $class = array();
    

    /**
     * Video HTML 
     *
     * @param string  $id          - 1: ID do campo
     * @param string  $label       - 2: Label do campo, usado para validações
     * @param string  $strValue    - 4: Valor inicial
     * @param boolean $controls    - 5: Default TRUE  = habilita o controler sobre o vídeo, FALSE desativa o controler
     * @param boolean $autoplay    - 6: Default FALSE = habilita o autoplay, FALSE não iniciar o vídeo automaticamente
     * @param boolean $loop        - 7: Default FALSE = habilita o video em loop, FALSE não fica em loop
     * @return TLabel
     */
    public function __construct(string $id
                               ,string $label
                               ,string $strValue
                               ,bool $controls
                               ,bool $autoplay
                               ,bool $loop
                               )
    {

        $showMediaSource = new TElement('source');
        $showMediaSource->src = $strValue;
        $showMediaSource->type = 'video/mp4';

        $adiantiObj = new TElement('video');
        $adiantiObj->class = 'videomidiatv';
        $adiantiObj->setProperty('autoplay', 'true');
        $adiantiObj->setProperty('loop', 'true');
        $adiantiObj->setProperty('muted', 'true');
        $adiantiObj->setProperty('controls', 'true');
        $adiantiObj->add($showMediaSource);
        $adiantiObj->add('Your browser does not support HTML video.');

        parent::__construct($adiantiObj,$id,$label,null,null,null);
        $this->autoplay($autoplay);
        $this->controls($controls);
        $this->loop($loop);
    }

    private function setProperty($property, $valeu)
    {
        $valeu = empty($valeu)?true:$valeu;
        if($valeu){
            $this->getAdiantiObj()->setProperty($property, $valeu);
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