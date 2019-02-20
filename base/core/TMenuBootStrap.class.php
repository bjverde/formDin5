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

	public function getMenuBootStrap($print=true)
	{
        $navBar = $this->getNavBar();
        return $navBar;
	}
}
?>