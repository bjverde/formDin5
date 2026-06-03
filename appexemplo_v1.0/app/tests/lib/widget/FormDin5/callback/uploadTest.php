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

use PHPUnit\Framework\TestCase;

class uploadTest extends TestCase
{
    private $originalFiles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalFiles = $_FILES;
    }

    protected function tearDown(): void
    {
        $_FILES = $this->originalFiles;
        parent::tearDown();
    }

    private function runUploadScript()
    {
        $code = file_get_contents('upload.class.php');
        
        // Remove <?php opening tag and everything before it (like BOM)
        $code = preg_replace('/^.*?<\?php/s', '', $code);
        
        // Strip the empty class definition to avoid redeclaration errors
        $code = preg_replace('/class\s+upload\s*\{\s*\}/i', '', $code);
        
        // Execute the remaining code
        eval($code);
    }

    public function testUploadWithoutFile()
    {
        $_FILES = [];
        $originalCwd = getcwd();
        chdir('app/lib/widget/FormDin5/callback');

        try {
            ob_start();
            $this->runUploadScript();
            $output = ob_get_clean();
        } finally {
            chdir($originalCwd);
        }

        $this->assertStringContainsString('Erro no envio do arquivo', $output);
    }

    public function testUploadWithFile()
    {
        // Suppress warning from move_uploaded_file in CLI unit tests
        set_error_handler(function($errno, $errstr) {
            if (strpos($errstr, 'move_uploaded_file') !== false) {
                return true;
            }
            return false;
        });

        $tmpName = tempnam(sys_get_temp_dir(), 'upload_test');
        file_put_contents($tmpName, 'test content');

        $_FILES['arquivo'] = [
            'name' => 'test_upload.txt',
            'tmp_name' => $tmpName,
            'error' => UPLOAD_ERR_OK,
            'size' => 12
        ];

        $originalCwd = getcwd();
        chdir('app/lib/widget/FormDin5/callback');

        try {
            ob_start();
            $this->runUploadScript();
            $output = ob_get_clean();
        } finally {
            chdir($originalCwd);
            restore_error_handler();
            if (file_exists($tmpName)) {
                @unlink($tmpName);
            }
            // Clean up the created test file under app/tmp/
            $diretorioDestino = FileHelper::getCaminhoSistema().DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
            $caminhoArquivo = $diretorioDestino . 'test_upload.txt';
            if (file_exists($caminhoArquivo)) {
                @unlink($caminhoArquivo);
            }
        }

        $this->assertStringContainsString('Arquivo recebido e salvo com sucesso', $output);
        $this->assertStringContainsString('test_upload.txt', $output);
    }
}
