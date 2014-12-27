$(document).ready(function(){
	$.ajax({
		url: "index.php/pre_saisir",
		dataType: "json",
		success: function(data){
                        $('#type_cli').val(data.ID_CLIENT);
                        $('#civilite').val(data.CIVILITE);
                        $('#nom').val(data.NOM);
                        $('#prenom').val(data.PRENOM);
                        $('#email').val(data.MAIL);
                        $('#societe').val(data.SOCIETE);
                        $('#adresse').val(data.ADRESSE);
                        $('#tmobile').val(data.TELEPHONE1);
                        $('#tfixe').val(data.TELEPHONE2);
                        $('#faxe').val(data.FAXE);
		},
		error: function(e){
			console.log(e.message);
		}
	})
})