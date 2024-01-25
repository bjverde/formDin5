//-------------------------------------------------------------------------------------
function fwCheckNumCharRickEditor(e,max)
{
	try {
        var obj = fwGetObj(e.id+'_counter');
		var texto = e.value.trim();
		var tamanho = texto.length;
		obj.style.color='#000000';
		if( tamanho > max ){
			fwRemoverCaractere(e,13);
			texto = e.value.trim();
			tamanho = texto.length;
			if( tamanho > max ){
				obj.style.color='red';
				alert('Limite de '+max+' caracteres atingido!');
				texto = texto.substr(0,max);
				e.value=texto;
				var dif = (tamanho-e.value.length);
				if( dif > 1 ){
					alert( 'Foram removidos '+dif+' caracteres do final do texto.')
				}
			}
		}
		obj.innerHTML='caracteres:'+e.value.length+"/"+max;
	} catch(e)	{}
}
//--------------------------------------------------------------------------------------