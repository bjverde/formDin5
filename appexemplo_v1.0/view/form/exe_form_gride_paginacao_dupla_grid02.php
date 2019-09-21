<?php
/*
 * Formdin Framework
 * Copyright (C) 2012 Ministério do Planejamento
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


if (isset($_REQUEST['ajax'])  && $_REQUEST['ajax']) {
    $res = TPDOConnection::executeSql("select * from tb_municipio");

    $gride = new TGrid( 'gd02'                        // id do gride
                       ,'Municipios'
                    );
    $gride->addKeyField( 'COD_MUNICIPIO' ); // chave primaria
    $gride->setData( $res ); // array de dados
    $gride->setMaxRows( 17 );
    $gride->setUrl( 'view/form/exe_form_gride_paginacao_dupla_grid02.php' );

    $gride->addRowNumColumn();
    $gride->addColumn('COD_MUNICIPIO', 'Id');
    $gride->addColumn('COD_UF', 'Id UF');
    $gride->addColumn('NOM_MUNICIPIO', 'Municipio');

    $gride->enableDefaultButtons(false);
    $gride->setExportExcel(false);
    $gride->show();
    die();
}