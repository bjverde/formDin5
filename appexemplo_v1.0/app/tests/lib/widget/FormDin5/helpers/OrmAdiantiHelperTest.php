<?php
/*
 * Formdin Framework
 * Copyright (C) 2012 Ministério do Planejamento
 * Criado por Luís Eugênio Barbosa
 * Essa versão é um Fork https://github.com/bjverde/formDin
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

require_once  __DIR__.'/../../mockFormDinArray.php';

use PHPUnit\Framework\TestCase;

/**
 * paginationSQLHelper test case.
 */
class OrmAdiantiHelperTest extends TestCase
{	

	public function testParam_null_false() {
	    $expected = false;
        $param = null;
        $result = OrmAdiantiHelper::valueTest($param);
        $this->assertEquals( $expected , $result);
	}
	public function testParam_empyt_false() {
	    $expected = false;
        $param = '';
        $result = OrmAdiantiHelper::valueTest($param);
        $this->assertEquals( $expected , $result);
	}
	public function testParam_empytArray_false() {
	    $expected = false;
        $param = array();
        $result = OrmAdiantiHelper::valueTest($param);
        $this->assertEquals( $expected , $result);
	}
	public function testParam_obj_false() {
	    $expected = false;
        $param = new StdClass;
        $result = OrmAdiantiHelper::valueTest($param);
        $this->assertEquals( $expected , $result);
	}
    public function testParam_string_true() {
	    $expected = true;
        $param = 'ana';
        $result = OrmAdiantiHelper::valueTest($param);
        $this->assertEquals( $expected , $result);
	}
    public function testParam_stringNumeric_true() {
	    $expected = true;
        $param = '10';
        $result = OrmAdiantiHelper::valueTest($param);
        $this->assertEquals( $expected , $result);
	}
    public function testParam_numeric_true() {
	    $expected = true;
        $param = 10;
        $result = OrmAdiantiHelper::valueTest($param);
        $this->assertEquals( $expected , $result);
	}
    public function testParam_array_true() {
	    $expected = true;
        $param = array(1=>'ana');
        $result = OrmAdiantiHelper::valueTest($param);
        $this->assertEquals( $expected , $result);
	}
    //--------------------------------------------------------------------------------
    public function testAddFilter_like() {
        $data = new stdClass();
        $data->nome = 'Maria';

	    $expected = array();
        $expected[] = new TFilter('nome','like',$data->nome);

        $filters= array();
        $result = OrmAdiantiHelper::addFilter($filters,'nome','like',$data,'nome');
        $this->assertEquals( $expected , $result);
	}

    public function testAddFilter_like_array() {
        $param = array();
        $param['nome'] = 'Maria';

        $data = new stdClass();
        $data->nome = 'Maria';

	    $expected = array();
        $expected[] = new TFilter('nome','like',$data->nome);

        $filters= array();
        $result = OrmAdiantiHelper::addFilter($filters,'nome','like',null,null,$param,'nome');
        $this->assertEquals( $expected , $result);
	}

    public function testAddFilter_equal() {
        $data = new stdClass();
        $data->nome = 'Maria';

	    $expected = array();
        $expected[] = new TFilter('nome','=',$data->nome);

        $filters= array();
        $result = OrmAdiantiHelper::addFilter($filters,'nome','=',$data,'nome');
        $this->assertEquals( $expected , $result);
	}

    public function testAddFilter_equal_array() {
        $param = array();
        $param['nome'] = 'Maria';

        $data = new stdClass();
        $data->nome = 'Maria';

	    $expected = array();
        $expected[] = new TFilter('nome','=',$data->nome);

        $filters= array();
        $result = OrmAdiantiHelper::addFilter($filters,'nome','=',null,null,$param,'nome');
        $this->assertEquals( $expected , $result);
	}
        
    public function testAddFilter_notEqual() {
        $data = new stdClass();
        $data->nome = 'Maria';

	    $expected = array();
        $expected[] = new TFilter('nome','!=',$data->nome);

        $filters= array();
        $result = OrmAdiantiHelper::addFilter($filters,'nome','!=',$data,'nome');
        $this->assertEquals( $expected , $result);
	}
    public function testAddFilter_In() {
        $data = new stdClass();
        $data->nome = 'Maria';

	    $expected = array();
        $expected[] = new TFilter('nome','in',$data->nome);

        $filters= array();
        $result = OrmAdiantiHelper::addFilter($filters,'nome','in',$data,'nome');
        $this->assertEquals( $expected , $result);
	}
    //--------------------------------------------------------------------------------
    public function testAddFilterTCriteria_like() {
        $data = new stdClass();
        $data->nome = 'Maria';

	    $expected = new TCriteria;
        $expected->add(new TFilter('nome','like',$data->nome));

        $criteria= new TCriteria;
        $result  = OrmAdiantiHelper::addFilterTCriteria($criteria,'nome','like',$data,'nome');
        $this->assertEquals( $expected , $result);
	}

    public function testAddFilterTCriteria_like_array() {
        $param = array();
        $param['nome'] = 'Maria';

        $data = new stdClass();
        $data->nome = 'Maria';

	    $expected = new TCriteria;
        $expected->add(new TFilter('nome','like',$data->nome));
        $expected->add(new TFilter('nome2','like',$data->nome));

        $criteria= new TCriteria;
        $criteria = OrmAdiantiHelper::addFilterTCriteria($criteria,'nome','like',null,null,$param,'nome');
        $result = OrmAdiantiHelper::addFilterTCriteria($criteria,'nome2','like',null,null,$param,'nome');
        $this->assertEquals( $expected , $result);
	}
}