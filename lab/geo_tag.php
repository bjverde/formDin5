<?php
//http://www.linhadecodigo.com.br/artigo/3653/usando-geolocalizacao-com-html5.aspx
//https://blog.mxcursos.com/aprenda-geolocalizacao-com-html5/
//https://www.blogson.com.br/capturar-a-localizacao-do-visitante-de-seu-site-com-html-5/
//https://diveintohtml5.com.br/geolocation.html
//https://pt.stackoverflow.com/questions/52227/como-obter-localiza%C3%A7%C3%A3o-atual-do-usu%C3%A1rio-atrav%C3%A9s-da-api-do-google-maps
//https://developer.mozilla.org/pt-BR/docs/Using_geolocation
//https://www.devmedia.com.br/html5-geolocation-dica/28546
?>


<!DOCTYPE html>
<html>
<body>
    <h1>Exemplo de Geolocalização</h1>
<script>

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition,showError);
    } else{
        console.log('Geolocalização não é suportada nesse browser.');
    }
}
function showPosition(position) {
    console.log(position);
    let latitude=position.coords.latitude;
    let longitude=position.coords.longitude;
    var dadosLocalizacao = {
         latitude: position.coords.latitude
        ,longitude: position.coords.longitude
        ,altitude: position.coords.altitude
        ,accuracy: position.coords.accuracy
        ,altitudeAccuracy: position.coords.altitudeAccuracy
        ,heading: position.coords.heading
        ,speed: position.coords.speed
        ,timestamp: position.timestamp
    };
    var jsonLocalizacao = JSON.stringify(dadosLocalizacao);
    console.log('Localização em formato JSON:', jsonLocalizacao);
}
function showError(error) {
    console.log('Erro ao obter localização:', erro);
}

// Atribuir a função diretamente a window.onload
window.onload = getLocation;
</script>
</body>
</html>