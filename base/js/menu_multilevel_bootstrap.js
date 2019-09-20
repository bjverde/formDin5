/*
 * FormDin 5 Framework
 * Created by Reinaldo A. Barrêto Jr in 2019
 * Based on the FormDin 4 of Luiz Eugênio
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

jQuery(document).ready(function () {

	jQuery('.navbar .dropdown-item').on('click', function (e) {
        var $el = jQuery(this).children('.dropdown-toggle');
        var $parent = $el.offsetParent(".dropdown-menu");
        jQuery(this).parent("li").toggleClass('open');

        if (!$parent.parent().hasClass('navbar-nav')) {
            if ($parent.hasClass('show')) {
                $parent.removeClass('show');
                $el.next().removeClass('show');
                $el.next().css({"top": -999, "left": -999});
            } else {
                $parent.parent().find('.show').removeClass('show');
                $parent.addClass('show');
                $el.next().addClass('show');
                var $offsetTop = null;
                if($el.length > 0){
                    var $offsetTop = $el[0].offsetTop;
                }
                $el.next().css({"top": $offsetTop, "left": $parent.outerWidth() - 4});
            }
            e.preventDefault();
            e.stopPropagation();
        }
    });

	jQuery('.navbar .dropdown').on('hidden.bs.dropdown', function () {
		jQuery(this).find('li.dropdown').removeClass('show open');
		jQuery(this).find('ul.dropdown-menu').removeClass('show open');
    });

});

function showItemMenu(e){
    url=e.dataset.url;
    jQuery('#app_footer_company').text(url);
}

function changeItemMenu(e){
    url=e.dataset.url;
    jQuery('#app_footer_module').text(url);
    //jQuery("#app_iframe").attr("src", url);
    app_load_module(url,null,null);
}