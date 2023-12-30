var eventoCarregamento = new Event('fd5GeolocationLoad');
document.dispatchEvent(eventoCarregamento);

function fd5GetLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(fd5ShowPosition,fd5ShowPositionError);
    } else{
        console.log('Geolocalização não é suportada nesse browser.');
    }
}
function fd5ShowPosition(position) {
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
function fd5ShowPositionError(error) {
    console.log('Erro ao obter localização:', error);
}