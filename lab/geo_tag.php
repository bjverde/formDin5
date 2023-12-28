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
    <p id="demo">Clique no botão para obter sua localização:</p>
    <button onclick="getLocation()">Clique aqui</button>
    <div id="info"></div>
    <div id="mapholder"></div>
    <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script>
var x=document.getElementById("demo");

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition,showError);
    } else{
        console.log('Geolocalização não é suportada nesse browser.');
    }
}
function showPosition(position) {
    console.log(position);
    let lat=position.coords.latitude;
    let lon=position.coords.longitude;
    console.log('lat',lat);
    console.log('lon',lon);
}
function showError(error) {
    console.log(error);
}
</script>
</body>
</html>