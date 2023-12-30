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