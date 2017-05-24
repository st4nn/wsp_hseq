
function iniciarModulo()
{
	$("#lblHeader_NomModulo").text("Evaluación y Aprobación de Viajes");
    $("#lblHeader_NomUsuario").text(Usuario.Nombre);

	$(".datepicker").datetimepicker(
    {
        format: 'YYYY-MM-DD',
        inline: false,
        sideBySide: true,
        locale: 'es'
    });

    $("#frmFormato1").on("submit", function(evento)
    {
        evento.preventDefault();

        $("#tblFormato1_Conductores").tableToJson(function(Conductores)
        {
            $("#tblFormato1_Pasajeros").tableToJson(function(Pasajeros)
            {
                $("#tblFormato1_PuntosDeContacto").tableToJson(function(PuntosDeContacto)
                {
                    $("#frmFormato1").generarDatosEnvio("txtFormato1_", function(datos)
                    {
                        tmpDatos = $.parseJSON(datos);
                        tmpDatos.Pasajeros = Pasajeros;
                        tmpDatos.Conductores = Conductores;
                        tmpDatos.PuntosDeContacto = PuntosDeContacto;

                        datos = JSON.stringify(tmpDatos);

                        $.post('server/php/proyecto/guardarFormato.php', {Usuario : Usuario.id, datos: datos}, function(data, textStatus, xhr) 
                        {
                            if (data.Error == "")
                            {
                                swal({
                                    title: "El formato ha sido guardado",
                                    text: "Desea Iniciar uno nuevo?",
                                    type: "success",
                                    showCancelButton: true,
                                    confirmButtonColor: "#e67e22",
                                    confirmButtonText: "Sí, limpiar el formulario!",
                                    cancelButtonColor : "#9b59b6",
                                    cancelButtonText: "No, voy a revisar éste, muestrame el PDF!",
                                    closeOnConfirm: true,
                                    closeOnCancel: true },
                                function (isConfirm) {
                                    if (isConfirm) 
                                    {
                                        $("#frmFormato1")[0].reset();
                                        $("#txtFormato1_id").val('NULL');
                                        $("#txtFormato1_Formato").val(1);
                                    } else {
                                        var url = "server/formatos/planDeViaje.php?i=" + data.datos;
                                        var win = window.open(url, "_blank", "directories=no, location=no, menubar=no, resizable=yes, scrollbars=yes, statusbar=no, tittlebar=no");
                                        win.focus();
                                        $("#txtFormato1_id").val(data.datos)
                                        $("#txtFormato1_Formato").val(1);
                                        
                                    }
                                });
                            } else
                            {
                                swal({
                                    title: "Error!",
                                    text: data.Error,
                                    type: "error"
                                });
                            }
                        }, 'json');

                    });
                });
            });
        });
        


    });

    $("#txtFormato1_P64").on("change", function()
    {
        $("#tblFormato1_Conductores").tableToJson();
    });

    $(".btnFormato1_AgregarFila").on("click", function(evento)
    {
        evento.preventDefault();
        var tbl = $(this).parent('th').parent('tr').parent('thead').parent('table');

        var tbody = $(tbl).find('tbody');
        var ftr = $(tbody).find('tr');
        ftr = ftr[0];

        var fila = '<tr>' + $(ftr).html() + '<tr>';

        $(tbody).append(fila);

        var ltr = $(tbody).find('tr');
        var ltd = $(ltr).find("td");

        ltr = ltr[ltr.length - 1];
        ltd = ltd[ltd.length - 1];
        var tds = '<button type="button" class="btn palette-Red bg waves-effect btnFormato1_borrarFila"><i class="zmdi zmdi-delete"></i></button>';
        $(ltd).append(tds);
    });

    $(document).delegate('.btnFormato1_borrarFila', 'click', function(evento) {
        evento.preventDefault();
        $(this).parent('td').parent('tr').remove();
    });

    $(".chktFormato1_Factores").on("click", function()
    {

        var objs = $(".chktFormato1_Factores:checked");
        var valores = [0, 0, 0, 0];
        var total = 0;
        $.each(objs, function(index, val) 
        {
            total += parseInt($(val).attr("data-idValor"));
            valores[parseInt($(val).attr("data-Factor"))] += parseInt($(val).attr("data-idValor"));
        });

        $("#lblFormato1_Valoracion_Total").text(total);
        $("#txtFormato1_Valoracion_Total").val(total);

        $.each(valores, function(index, val) 
        {
             if ($("#lblFormato1_Valoracion_F" + index).length > 0)
             {
                $("#lblFormato1_Valoracion_F" + index).text(val);
                $("#txtFormato1_Valoracion_F" + index).val(val);
             }
        });

        $("#cntFormato1_Valoracion_Total").removeClass('bgm-green');
        $("#cntFormato1_Valoracion_Total").removeClass('bgm-yellow');
        $("#cntFormato1_Valoracion_Total").removeClass('bgm-red');
        $("#cntFormato1_Valoracion_Total").removeClass('bgm-black');

        if (total < 12)
        {
            $("#cntFormato1_Valoracion_Total").addClass('bgm-green');
        }
        else
        {
            if (total < 16)
            {
                $("#cntFormato1_Valoracion_Total").addClass('bgm-yellow');    
            } else
            {
                if (total < 25)
                {
                    $("#cntFormato1_Valoracion_Total").addClass('bgm-red');    
                } else
                {
                    $("#cntFormato1_Valoracion_Total").addClass('bgm-black');    
                }
            }
        }
        

    });
	
}