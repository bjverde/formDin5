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


use PHPUnit\Framework\TestCase;

class Pessoa {
    public string|null $nome;
    public int|null $idade;
    public string|null $email;

    public function __construct(string|null $nome=null, int|null $idade=null,string|null $email=null){
        $this->nome = $nome;
        $this->idade = $idade;
        $this->email = $email;
    }
    public function __toString(): string{
        return "Nome: {$this->nome}, Idade: {$this->idade}, Email: {$this->email}";
    }
}

class Cachorro {
    public string|null $nome;
    public int|null $idade;
    public string|null $raca;

    public function __construct(string $nome,int $idade,string $raca){
        $this->nome = $nome;
        $this->idade = $idade;
        $this->raca = $raca;
    }
    public function __toString(): string{
        return "Nome: {$this->nome}, Idade: {$this->idade}, Raça: {$this->raca}";
    }
}


/**
 * GetHelper test case.
 */
class ObjectHelperTest extends TestCase
{
    public function testTransferirAtributoSeExistir_objIguais() {
        $origem  = new Pessoa('João', 30, 'joao@example.com');
        $destino = new Pessoa();
        $retorno = ObjectHelper::transferirAtributoSeExistir($origem, $destino, 'nome');
        $this->assertEquals($origem->nome, $retorno->nome, 'O atributo nome deve ser transferido corretamente.');
        $retorno = ObjectHelper::transferirAtributoSeExistir($origem, $destino, 'email');
        $this->assertEquals($origem->email, $retorno->email, 'O atributo email deve ser transferido corretamente.' );
        $this->assertNull($retorno->idade, 'O atributo idade deve ser nulo.');
    }
}

