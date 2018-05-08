$(document).ready(function(){
    console.log('Iniciando o script...');

    $('form').on('submit', function(e){
        e.preventDefault();

        var $form = $(this),
            $input = $form.find('input'),
            id_table = $input.val();

        if(id_table.trim() == ''){
            swal("Oops! =(", "Digite um valor válido.");
            return;
        }

        $.ajax({
            method: "POST",
            url: $form.attr('action'),
            data: { id_table: id_table }
        })
          .done(function(response) {
                if(response.status == false){
                    swal("Mesa: "+id_table, "NÃO encontramos o pagamento desta mesa! Verifique a situação da mesma no caixa.", "error");
                }else{
                    if (response.mesa.caixa == 1 && response.mesa.bematech != 1)
                        swal("Mesa: " + id_table, "Pagamento realizado no caixa.", "success")
                    else if(response.mesa.caixa == 1 && response.mesa.bematech == 1)
						swal("Mesa: " + id_table, "Comanda liberada.", "success")
					else
                        swal("Mesa: " + id_table, "Pagamento realizado através do Fast2Pay.", "success");

                    var tr = $('#linha-'+id_table).remove();
                    $input.val('');
                    $input.focus();
              }
          });

    });
});