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
 * UrlHelper test case.
 */
class UrlHelperTest extends TestCase
{
    /**
     * @var array
     */
    private $originalServer;

    /**
     * Preserva o estado original do $_SERVER antes de cada teste
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->originalServer = $_SERVER;
    }

    /**
     * Restaura o estado original do $_SERVER após cada teste
     */
    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;
        parent::tearDown();
    }

    /**
     * Método auxiliar para configurar dados do $_SERVER
     */
    private function setServerData($data)
    {
        foreach ($data as $key => $value) {
            $_SERVER[$key] = $value;
        }
    }

    /**
     * Testa o método curPageURL
     */
    public function testCurPageURL()
    {
        $this->setServerData([
            'HTTPS' => 'on',
            'SERVER_NAME' => 'www.example.com',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/test/page?param=value'
        ]);

        $expected = 'https://www.example.com/test/page?param=value';
        $result = UrlHelper::curPageURL();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa o método homeUrl que lança erro devido ao método inexistente em ServerHelper
     */
    public function testHomeUrl_throwsError()
    {
        $this->expectException(\Error::class);
        UrlHelper::homeUrl();
    }
}
