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

require_once  __DIR__.'/../../mockFormDinArray.php';

use PHPUnit\Framework\TestCase;

/**
 * ServerHelper test case.
 */
class ServerHelperTest extends TestCase
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
     * Testa o método get com atributo existente
     */
    public function testGet_withExistingAttribute()
    {
        $this->setServerData(['HTTP_HOST' => 'www.example.com']);
        $expected = 'www.example.com';
        $result = ServerHelper::get('HTTP_HOST');
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa o método get com atributo inexistente
     */
    public function testGet_withNonExistingAttribute()
    {
        $result = ServerHelper::get('NON_EXISTING_ATTRIBUTE');
        $this->assertNull($result);
    }


    public function testGetCurrentUrl_httpst()
    {
        $this->setServerData([
            'HTTPS' => 'on',
            'SERVER_NAME' => 'www.example.com',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/test/page?param=value'
        ]);

        $expected = 'https://www.example.com/test/page?param=value';
        $result = ServerHelper::getCurrentUrl();
        $this->assertEquals($expected, $result);
    }

    public function testGetCurrentUrl_httpsWithPort()
    {
        $this->setServerData([
            'HTTPS' => 'on',
            'SERVER_NAME' => 'www.example.com',
            'SERVER_PORT' => '443',
            'REQUEST_URI' => '/test/page?param=value'
        ]);

        $expected = 'https://www.example.com:443/test/page?param=value';
        $result = ServerHelper::getCurrentUrl();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa getCurrentUrl com HTTP
     */
    public function testGetCurrentUrl_withHttp()
    {
        $this->setServerData([
            'SERVER_NAME' => 'www.example.com',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/test/page?param=value'
        ]);

        $expected = 'http://www.example.com/test/page?param=value';
        $result = ServerHelper::getCurrentUrl();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa getCurrentUrl com porta customizada
     */
    public function testGetCurrentUrl_withCustomPort()
    {
        $this->setServerData([
            'SERVER_NAME' => 'www.example.com',
            'SERVER_PORT' => '8080',
            'REQUEST_URI' => '/test/page'
        ]);

        $expected = 'http://www.example.com:8080/test/page';
        $result = ServerHelper::getCurrentUrl();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa getCurrentUrl com trim_query_string = true
     */
    public function testGetCurrentUrl_withTrimQueryString()
    {
        $this->setServerData([
            'SERVER_NAME' => 'www.example.com',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/test/page?param=value&other=test'
        ]);

        $expected = 'http://www.example.com/test/page';
        $result = ServerHelper::getCurrentUrl(true);
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa getQueryString
     */
    public function testGetQueryString()
    {
        $this->setServerData([
            'SERVER_NAME' => 'www.example.com',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/test/page?param=value'
        ]);

        $expected = 'http://www.example.com/test/page';
        $result = ServerHelper::getQueryString();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa getHomeUrl com engine.php
     */
    public function testGetHomeUrl_withEnginePHP()
    {
        $this->setServerData([
            'SERVER_NAME' => 'www.example.com',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/app/engine.php/SomeClass'
        ]);

        $expected = 'http://www.example.com/app/';
        $result = ServerHelper::getHomeUrl();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa getHomeUrl com index.php
     */
    public function testGetHomeUrl_withIndexPHP()
    {
        $this->setServerData([
            'SERVER_NAME' => 'www.example.com',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/app/index.php'
        ]);

        $expected = 'http://www.example.com/app/';
        $result = ServerHelper::getHomeUrl();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa getClientIP com HTTP_CLIENT_IP válido
     */
    public function testGetClientIP_withValidClientIP()
    {
        $this->setServerData([
            'HTTP_CLIENT_IP' => '192.168.1.100',
            'HTTP_X_FORWARDED_FOR' => '10.0.0.1',
            'REMOTE_ADDR' => '127.0.0.1'
        ]);

        $expected = '192.168.1.100';
        $result = ServerHelper::getClientIP();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa getClientIP com HTTP_X_FORWARDED_FOR válido
     */
    public function testGetClientIP_withValidForwardedIP()
    {
        $this->setServerData([
            'HTTP_CLIENT_IP' => 'invalid_ip',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.100',
            'REMOTE_ADDR' => '127.0.0.1'
        ]);

        $expected = '192.168.1.100';
        $result = ServerHelper::getClientIP();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa getClientIP com REMOTE_ADDR (fallback)
     */
    public function testGetClientIP_withRemoteAddr()
    {
        $this->setServerData([
            'HTTP_CLIENT_IP' => 'invalid_ip',
            'HTTP_X_FORWARDED_FOR' => 'also_invalid',
            'REMOTE_ADDR' => '192.168.1.100'
        ]);

        $expected = '192.168.1.100';
        $result = ServerHelper::getClientIP();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa getClientIPStringAll
     */
    public function testGetClientIPStringAll()
    {
        $this->setServerData([
            'HTTP_CLIENT_IP' => '192.168.1.100',
            'HTTP_X_FORWARDED_FOR' => '10.0.0.1',
            'REMOTE_ADDR' => '127.0.0.1'
        ]);

        $expected = 'cliente: 192.168.1.100,remote: 127.0.0.1,forwarded: 10.0.0.1';
        $result = ServerHelper::getClientIPStringAll();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa getClientAgent
     */
    public function testGetClientAgent()
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $this->setServerData(['HTTP_USER_AGENT' => $userAgent]);

        $result = ServerHelper::getClientAgent();
        $this->assertEquals($userAgent, $result);
    }

    /**
     * Testa getClientDeviceType com mobile
     */
    public function testGetClientDeviceType_mobile()
    {
        $this->setServerData([
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) mobile Safari'
        ]);

        $expected = ServerHelper::DEVICE_TYPE_MOBILE;
        $result = ServerHelper::getClientDeviceType();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa getClientDeviceType com tablet
     */
    public function testGetClientDeviceType_tablet()
    {
        $this->setServerData([
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (iPad; CPU OS 14_0 like Mac OS X) tablet Safari'
        ]);

        $expected = ServerHelper::DEVICE_TYPE_TABLE;
        $result = ServerHelper::getClientDeviceType();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa getClientDeviceType com desktop
     */
    public function testGetClientDeviceType_desktop()
    {
        $this->setServerData([
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]);

        $expected = ServerHelper::DEVICE_TYPE_DESKTOP;
        $result = ServerHelper::getClientDeviceType();
        $this->assertEquals($expected, $result);
    }

    /**
     * Testa isHTTPS com HTTPS = on
     */
    public function testIsHTTPS_withHttpsOn()
    {
        $this->setServerData(['HTTPS' => 'on']);
        
        $result = ServerHelper::isHTTPS();
        $this->assertTrue($result);
    }

    /**
     * Testa isHTTPS com HTTPS = 1
     */
    public function testIsHTTPS_withHttpsOne()
    {
        $this->setServerData(['HTTPS' => '1']);
        
        $result = ServerHelper::isHTTPS();
        $this->assertTrue($result);
    }

    /**
     * Testa isHTTPS com HTTP_X_FORWARDED_PROTO
     */
    public function testIsHTTPS_withXForwardedProto()
    {
        $this->setServerData([
            'HTTPS' => 'off',
            'HTTP_X_FORWARDED_PROTO' => 'https'
        ]);
        
        $result = ServerHelper::isHTTPS();
        $this->assertTrue($result);
    }

    /**
     * Testa isHTTPS com HTTP_FRONT_END_HTTPS quando testFrontEnd = true
     */
    public function testIsHTTPS_withFrontEndHttps()
    {
        $this->setServerData([
            'HTTPS' => 'off',
            'HTTP_FRONT_END_HTTPS' => 'on'
        ]);
        
        $result = ServerHelper::isHTTPS(true);
        $this->assertTrue($result);
    }

    /**
     * Testa isHTTPS com porta 443 quando testPort = true
     */
    public function testIsHTTPS_withPort443()
    {
        $this->setServerData([
            'HTTPS' => 'off',
            'SERVER_PORT' => '443'
        ]);
        
        $result = ServerHelper::isHTTPS(false, true);
        $this->assertTrue($result);
    }

    /**
     * Testa isHTTPS com todos os parâmetros habilitados e HTTPS válido
     */
    public function testIsHTTPS_withAllParametersEnabled()
    {
        $this->setServerData([
            'HTTPS' => 'off',
            'HTTP_X_FORWARDED_PROTO' => 'http',
            'HTTP_FRONT_END_HTTPS' => 'on',
            'SERVER_PORT' => '80'
        ]);
        
        $result = ServerHelper::isHTTPS(true, true);
        $this->assertTrue($result);
    }

    /**
     * Testa isHTTPS quando não é HTTPS
     */
    public function testIsHTTPS_false()
    {
        $this->setServerData([
            'HTTPS' => 'off',
            'HTTP_X_FORWARDED_PROTO' => 'http',
            'HTTP_FRONT_END_HTTPS' => 'off',
            'SERVER_PORT' => '80'
        ]);
        
        $result = ServerHelper::isHTTPS(true, true);
        $this->assertFalse($result);
    }

    /**
     * Testa isHTTPS sem nenhuma configuração
     */
    public function testIsHTTPS_withNoConfiguration()
    {
        // Remove todas as configurações relacionadas a HTTPS
        unset($_SERVER['HTTPS']);
        unset($_SERVER['HTTP_X_FORWARDED_PROTO']);
        unset($_SERVER['HTTP_FRONT_END_HTTPS']);
        unset($_SERVER['SERVER_PORT']);
        
        $result = ServerHelper::isHTTPS(true, true);
        $this->assertFalse($result);
    }

    /**
     * Testa as constantes da classe
     */
    public function testConstants()
    {
        $this->assertEquals('mobile', ServerHelper::DEVICE_TYPE_MOBILE);
        $this->assertEquals('tablet', ServerHelper::DEVICE_TYPE_TABLE);
        $this->assertEquals('desktop', ServerHelper::DEVICE_TYPE_DESKTOP);
    }
}
?>