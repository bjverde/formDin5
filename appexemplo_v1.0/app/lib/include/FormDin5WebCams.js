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
 * Este programa é distribuí1do na esperança que possa ser útil, mas SEM NENHUMA
 * GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou
 * APLICAÇÃO EM PARTICULAR. Veja a Licen?a Pública Geral GNU/LGPL em portugu?s
 * para maiores detalhes.
 *
 * Você deve ter recebido uma cópia da GNU LGPL versão 3, sob o título
 * "LICENCA.txt", junto com esse programa. Se não, acesse <http://www.gnu.org/licenses/>
 * ou escreva para a Fundação do Software Livre (FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02111-1301, USA.
 */


function fd5VideoSpec() {
    var video = document.querySelector('video');
    const constraints = {
        video: {
          width: {
            min: 1280,
            ideal: 1920,
            max: 2560,
          },
          height: {
            min: 720,
            ideal: 1080,
            max: 1440,
          },
        },
      };
    return constraints;
}


// stop video stream
function fd5VideoStop() {
    specs = fd5VideoSpec();
    var videoStream = navigator.mediaDevices.getUserMedia(specs);
    if (videoStream) {
        videoStream.getTracks().forEach((track) => {
        track.stop();
      });
    }
}

function fd5VideoStart()
{
    if ( !"mediaDevices" in navigator ||
         !"getUserMedia" in navigator.mediaDevices ) {
        alert("Camera API is not available in your browser");
        return;
    }
    //fd5VideoStop();
	var video = document.querySelector('video');

	navigator.mediaDevices.getUserMedia({video:true})
	.then(stream => {
		video.srcObject = stream;
		video.play();
	})
	.catch(error => {
		console.log(error);
	});	
}

function fd5WebCamCampiturar()
{
	var video = document.querySelector('video');
    var canvas = document.querySelector('canvas');
    canvas.height = video.videoHeight;
    canvas.width  = video.videoWidth;
    var context   = canvas.getContext('2d');
    context.drawImage(video, 0, 0);


    var link = document.createElement('a');
    link.download = 'foto.png';
    link.href = canvas.toDataURL();
    link.textContent = 'Clique para baixar a imagem';
    document.body.appendChild(link);	
}

function fd5VideoSaveTmpAdianti(){
    document.getElementById("carregando").innerHTML="Salvando, aguarde...";
    var file = document.getElementById("base64image").src;
    var formdata = new FormData();
    formdata.append("base64image", file);
    var ajax = new XMLHttpRequest();
    ajax.addEventListener("load", function(event) { upload_completo(event);}, false);
    ajax.open("POST", "upload.php");
    ajax.send(formdata);
}

function fd5VideoCaminhoSite(){
    let pathname = window.location.pathname;
    let partes   = pathname.split('index.php');
    return partes[0];
}