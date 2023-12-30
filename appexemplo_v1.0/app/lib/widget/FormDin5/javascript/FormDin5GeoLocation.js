var eventoCarregamento = new Event('fd5GeolocationLoad');
document.dispatchEvent(eventoCarregamento);

function fd5GetLocation(idField) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            fd5ShowPosition(position,idField);
        },fd5ShowPositionError);
    } else{
        console.log('Geolocalização não é suportada nesse browser.');
    }
}
function fd5ShowPosition(position,idField) {

    let latitude =position.coords.latitude;
    let longitude=position.coords.longitude;
    let altitude =position.coords.altitude;
    var dadosLocalizacao = {
         latitude: latitude
        ,longitude:longitude
        ,altitude: altitude
        ,accuracy: position.coords.accuracy
        ,altitudeAccuracy: position.coords.altitudeAccuracy
        ,heading:position.coords.heading
        ,speed:  position.coords.speed
        ,timestamp: position.timestamp
    };
    let jsonLocalizacao = JSON.stringify(dadosLocalizacao);
    let fieldJson = document.querySelector('#'+idField+'_json');
    fieldJson.value = jsonLocalizacao;    

    let fieldLat = document.querySelector('#'+idField+'_lat');
    fieldLat.value = latitude;

    let fieldLon = document.querySelector('#'+idField+'_lon');
    fieldLon.value = longitude;

    let fieldAlt = document.querySelector('#'+idField+'_alt');
    fieldAlt.value = altitude;
}
function fd5ShowPositionError(error) {
    console.log('Erro ao obter localização:', error);
}