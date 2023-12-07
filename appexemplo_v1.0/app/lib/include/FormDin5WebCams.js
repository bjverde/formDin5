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

function fd5VideoStop() {
    specs = fd5VideoSpec();
    var videoStream = navigator.mediaDevices.getUserMedia(specs);
    if (videoStream) {
        videoStream.getTracks().forEach((track) => {
        track.stop();
      });
    }
}

function fd5VideoStart(){
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

function fd5VideoCampiturar(id){
  try {
    let nameFile = 'image' + Math.floor((Math.random() * 1000000) + 1) + '.png';
    let hiddenField = document.querySelector('#'+id);

    var video  = document.querySelector('#'+id+'_video');
    var canvas = document.querySelector('#'+id+'_videoCanvas');
    var context= canvas.getContext('2d');

    video.style.display = 'none';
    canvas.height = video.videoHeight;
    canvas.width  = video.videoWidth;
    context.drawImage(video, 0, 0);

    hiddenField.value = nameFile;
    fd5VideoSaveTmpAdianti(id,nameFile,canvas);
  }
  catch (e) {
      __adianti_error('Error', e);
  }
}

function fd5VideoSaveTmpAdianti(id,nameFile,canvas){
  try {
    let pathSite = fd5VideoCaminhoSite();
    pathSite = pathSite+'app/lib/widget/FormDin5/callback/upload.class.php';

    let dataURL = canvas.toDataURL();
    let file = dataUrltoFile(dataURL,nameFile);
    let formdata = new FormData();
    formdata.append(id, nameFile);
    formdata.append('arquivo', file);

    let ajax = new XMLHttpRequest();
    ajax.open("POST", pathSite,true);
    //ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    ajax.send(formdata);

    // Tratando a resposta da requisição (opcional)
    ajax.onreadystatechange = function() {
      if (ajax.readyState === XMLHttpRequest.DONE) {
          if (ajax.status === 200) {
              // A requisição foi processada com sucesso
              console.log('Imagem enviada para o PHP com sucesso!');
          } else {
              // Ocorreu um erro durante o envio
              console.error('Erro ao enviar a imagem para o PHP');
          }
      }
    };
    //ajax.addEventListener("load", function(event) { upload_completo(event);}, false);
    //ajax.send(formdata);
  }
  catch (e) {
      __adianti_error('Error', e);
  }
}

function fd5VideoCaminhoSite(){
  let pathname = window.location.pathname;
  let partes   = pathname.split('index.php');
  return partes[0];
}

function upload_completo(event) {
  console.log(event);
}

function dataUrltoFile(dataURL,nameFile) {
  let byteString = atob(dataURL.split(',')[1]);
  let mimeType = dataURL.split(',')[0].split(':')[1].split(';')[0];
  let n = byteString.length;
  let u8arr = new Uint8Array(n);
  while (n--) {
      u8arr[n] = byteString.charCodeAt(n);
  }
  let name = nameFile;
  return new File([u8arr], name, {type:mimeType});
}


/*
onReadyStateChange = function(evt)
    {
        var status = null;
        
        try {
            status = evt.target.status;
        }
        catch(e) {
            return;
        }
        
        if (status == '200' && evt.target.readyState == '4' && evt.target.responseText) {
            try {
                var response = JSON.parse( evt.target.responseText );
                
                if ( response.type == 'error' ) {
                    __adianti_error('Error', response.msg);
                }
            }
            catch (e) {
                __adianti_error('Error', e);
            }
        }
    };
*/