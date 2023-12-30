var eventoCarregamento = new Event('fd5GeolocationLoad');
document.dispatchEvent(eventoCarregamento);

function fd5GetLocation(idField) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            fd5ShowPosition(position,idField);
        },fd5ShowPositionError);
    } else{
        let dadosLocalizacao = {
            code: 999
           ,msg :'Geolocalização não é suportada nesse browser.'
        };
       let jsonLocalizacao = JSON.stringify(dadosLocalizacao);
       let fieldJson = document.querySelector('#'+idField+'_json');
       fieldJson.value = jsonLocalizacao;
       console.log(code,msg);
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
    var code= error.code;
    var msg = null;
    switch(error.code) {
        case error.PERMISSION_DENIED:
            msg = "Permissão negada para acessar a localização.";
        break;
        case error.POSITION_UNAVAILABLE:
            msg = "Localização indisponível.";
        break;
        case error.TIMEOUT:
            msg = "Tempo esgotado para obter a localização.";
        break;
        default:
            msg = "Ocorreu um erro desconhecido ao obter a localização.";
        break;
    }
    let dadosLocalizacao = {
        code:code
       ,msg:msg
    };
   let jsonLocalizacao = JSON.stringify(dadosLocalizacao);
   let fieldJson = document.querySelector('#'+idField+'_json');
   fieldJson.value = jsonLocalizacao;
   console.log(code,msg);
}