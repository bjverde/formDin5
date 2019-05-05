<?php
/*
 * FormDin 5 Framework
 * Created by Reinaldo A. Barrêto Jr in 2019
 * Based on the FormDin 4 of Luís Eugênio Barbosa
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
if(!defined('EOL')){ define('EOL',"\n"); }

$path =  __DIR__.'/../../../core/';
require_once $path.'webform/TElement.class.php';
require_once $path.'webform/TMenuBootStrap.class.php';

use PHPUnit\Framework\TestCase;

/**
 * THtmlPage test case.
 */
class TMenuBootStrapTest extends TestCase
{
	
	/**
	 *
	 * @var THtmlPage
	 */
    private $tMenuBootStrap;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();				
		$this->tMenuBootStrap = new TMenuBootStrap(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
	    $this->tMenuBootStrap = null;		
		parent::tearDown ();
	}
		
	public function testGetNavBarId_ok() {
		$expected = 'navbarCollapse';
		$result =  $this->tMenuBootStrap->getNavBarId();
		$this->assertEquals( $expected , $result);
	}
	
	public function testSetMenuIconsPath_ok() {
	    $expected = '/img/icon';
	    $this->tMenuBootStrap->setMenuIconsPath('/img/icon');
	    $result = $this->tMenuBootStrap->getMenuIconsPath();
	    $this->assertEquals( $expected , $result);
	}
	
	public function testGetNavBrand_ok() {
	    $result = $this->tMenuBootStrap->getNavBrand();
	    $resultClassCss = $result->getClass();
	    $resultAttribute = $result->getAttribute('href');
	    $this->assertEquals( 'navbar-brand' , $resultClassCss);
	    $this->assertEquals( 'index.php', $resultAttribute);
	    $this->assertInstanceOf(TElement::class,$result);
	}
	
	public function testGetMenuIcon_ok() {
	    $this->tMenuBootStrap->setMenuIconsPath('/img/icon/');
	    $result = $this->tMenuBootStrap->getMenuIcon('kkk.png');
	    $resultClassCss = $result->getClass();
	    $resultAttribute = $result->getAttribute('src');
	    $resultHtml = $result->show(false);
	    $this->assertEquals( '<img class="menuIcon"  src="/img/icon/kkk.png" >'.EOL, $resultHtml);
	    $this->assertEquals( 'menuIcon', $resultClassCss);
	    $this->assertEquals( '/img/icon/kkk.png', $resultAttribute);
	    $this->assertInstanceOf(TElement::class,$result);
	}
}

