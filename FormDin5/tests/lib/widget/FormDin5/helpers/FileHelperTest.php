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
class FileHelperTest extends TestCase
{	
	public function testGetNomePastaSistema() {
        $expected = 'appexemplo_v1.0';
		$result = FileHelper::getNomePastaSistema();
		$this->assertSame( $expected , $result );
	}

    public function testGetCaminhoSistema() {
        $result = FileHelper::getCaminhoSistema();
        $this->assertIsString($result);
        $this->assertStringContainsString('appexemplo_v1.0', $result);
    }

    public function testExists_WithValidFile() {
        $filePath = __FILE__;
        $this->assertTrue(FileHelper::exists($filePath));
    }

    public function testExists_WithInvalidFile() {
        $filePath = 'non_existent_file.txt';
        $this->assertFalse(FileHelper::exists($filePath));
    }

    public function testExists_WithEmptyInput() {
        $this->assertFalse(FileHelper::exists(null));
        $this->assertFalse(FileHelper::exists(''));
    }

    public function testMove_Success() {
        $tempDir = __DIR__ . '/temp_test_move';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $from = $tempDir . '/source.txt';
        $to = $tempDir . '/dest_subdir/dest.txt';

        file_put_contents($from, 'test content');

        $result = FileHelper::move($from, $to);
        $this->assertTrue($result);
        $this->assertTrue(file_exists($to));
        $this->assertFalse(file_exists($from));

        // Clean up
        unlink($to);
        rmdir($tempDir . '/dest_subdir');
        rmdir($tempDir);
    }

    public function testMove_SourceDoesNotExistException() {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File not exist:');
        FileHelper::move('non_existent_source.txt', 'dest.txt');
    }

    public function testMove_MkdirFailureException() {
        $from = __DIR__ . '/temp_source_mkdir_fail.txt';
        file_put_contents($from, 'test');
        try {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('Falha ao criar os diretórios:');
            FileHelper::move($from, __DIR__ . '/temp_source_invalid_dir?/dest.txt');
        } finally {
            if (file_exists($from)) {
                unlink($from);
            }
        }
    }
}