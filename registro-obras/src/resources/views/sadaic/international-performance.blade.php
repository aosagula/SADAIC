@extends('member.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center" id="embed-header">
        <div class="col col-12">
            <h2>{{ $title }}</h2>
        </div>
    </div>
</div>
<div id="embed-content" class="container">
    <div class="row">
        <div class="col-12">
            {!! $content !!}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.onload = function() {
    $("#btnAbrirFormulario").on("click", function () {
        $("#texto_introduccion").css("display", "none");
        $("#fomrulario").css("display", "block");
    });

    $("#btnEnviar").on("click", function () {
        var msg = "";
        if ($("#nombreInterprete").val() == "") msg += "Por favor completa el nombre del interprete\n";
        if ($("#nombreDeclarante").val() == "") msg += "Por favor completa el nombre del declarante\n";
        //if($("#numeroSocio").val()=="") msg+="Por favor completa el número de socio\n";
        if ($("#telefono").val() == "") msg += "Por favor completa el numero de teléfono\n";
        if ($("#email").val() == "") {
            msg += "Por favor completa el email\n";
        } else {
            if (!emailIsValid($("#email").val())) {
                msg += "El email tiene un formato incorrecto.\n";
            }
        }

        if ($("#file").val() != "") {
            var ext = $('#file').val().split('.').pop().toLowerCase();
            if ($.inArray(ext, ['doc', 'docx', 'pdf', 'gif', 'png', 'jpg', 'jpeg']) == -1) {
                msg += "El archivo (3) debe ser de formato DOC, DOCX, PDF, PNG, JPG, JPEG, BMP o GIF\n";
            } else {
                var file_size = $('#file')[0].files[0].size;
                if (file_size > 1536000) {
                    msg += "El archivo (2) debe tener un peso maximo de 1.5MB.\n";
                }
            }
        }

        if ($("#file_2").val() != "") {
            var ext = $('#file_2').val().split('.').pop().toLowerCase();
            if ($.inArray(ext, ['doc', 'docx', 'pdf', 'gif', 'png', 'jpg', 'jpeg']) == -1) {
                msg += "El archivo (3) debe ser de formato DOC, DOCX, PDF, PNG, JPG, JPEG, BMP o GIF\n";
            } else {
                var file_size = $('#file_2')[0].files[0].size;
                if (file_size > 1536000) {
                    msg += "El archivo (2) debe tener un peso maximo de 1.5MB.\n";
                }
            }
        }

        if ($("#file_3").val() != "") {
            var ext = $('#file_3').val().split('.').pop().toLowerCase();
            if ($.inArray(ext, ['doc', 'docx', 'pdf', 'gif', 'png', 'jpg', 'jpeg']) == -1) {
                msg += "El archivo (3) debe ser de formato DOC, DOCX, PDF, PNG, JPG, JPEG, BMP o GIF\n";
            } else {
                var file_size = $('#file_3')[0].files[0].size;
                if (file_size > 1536000) {
                    msg += "El archivo (3) debe tener un peso maximo de 1.5MB.\n";
                }
            }
        }

        if ($("#file_4").val() != "") {
            var ext = $('#file_4').val().split('.').pop().toLowerCase();
            if ($.inArray(ext, ['doc', 'docx', 'pdf', 'gif', 'png', 'jpg', 'jpeg']) == -1) {
                msg += "El archivo (4) debe ser de formato DOC, DOCX, PDF, PNG, JPG, JPEG, BMP o GIF\n";
            } else {
                var file_size = $('#file_4')[0].files[0].size;
                if (file_size > 1536000) {
                    msg += "El archivo (4) debe tener un peso maximo de 1.5MB.\n";
                }
            }
        }


        var cant = parseInt($("#hidCantFec").val());
        for (i = 0; i < cant; i++) {
            fecha = $("#txtFechaSadaic_" + i).val();
            evento = $("#txtEvento_" + i).val();
            direccion = $("#txtDireccion_" + i).val();
            ciudad = $("#txtCiudad_" + i).val();
            pais = $("#txtPais_" + i).val();
            if (fecha == "") msg += "Debe completar el campo fecha de la fila " + (i + 1) + ".\n";
            if (evento == "") msg += "Debe completar el campo evento de la fila " + (i + 1) + ".\n";
            if (direccion == "") msg += "Debe completar el campo direccion de la fila " + (i + 1) + ".\n";
            if (ciudad == "") msg += "Debe completar el campo ciudad de la fila " + (i + 1) + ".\n";
            if (pais == "") msg += "Debe completar el campo pais de la fila " + (i + 1) + ".\n";

        }

        var cant = parseInt($("#hidCantCom").val());
        for (i = 0; i < cant; i++) {
            titulo = $("#txtTituloObra_" + i).val();
            compositor = $("#txtCompositor_" + i).val();
            if (titulo == "") msg += "Debe completar el campo titulo de la fila " + (i + 1) + ", de compositores.\n";
            if (compositor == "") msg += "Debe completar el campo compositor de la fila " + (i + 1) + ", de compositores.\n";
        }

        if (msg != "") {
            alert(msg);
        } else {
            $("#btnEnviar").css("display", "none");
            $("#txt_mensaje").empty();
            $("#txt_mensaje").append("Enviando email ... ");
            $("#txt_mensaje").css("display", "block");


            var formData = new FormData();
            formData.append('nombreInterprete', $("#nombreInterprete").val());
            formData.append('nombreDeclarante', $("#nombreDeclarante").val());
            formData.append('numeroSocio', $("#numeroSocio").val());
            formData.append('telefono', $("#telefono").val());
            formData.append('email', $("#email").val());
            ////formData.append('g-recaptcha-response', grecaptcha.getResponse());
            formData.append('g-recaptcha-response', grecaptcha.getResponse(1));
            // Attach file
            formData.append('image', $('input[type=file]')[0].files[0]);
            formData.append('image_2', $('input[type=file]')[1].files[0]);
            formData.append('image_3', $('input[type=file]')[2].files[0]);
            formData.append('image_4', $('input[type=file]')[3].files[0]);


            var cant = parseInt($("#hidCantFec").val());
            formData.append('hidCantFec', cant);
            for (i = 0; i < cant; i++) {
                formData.append('txtFecha_' + i, $("#txtFechaSadaic_" + i).val());
                formData.append('txtEvento_' + i, $("#txtEvento_" + i).val());
                formData.append('txtDireccion_' + i, $("#txtDireccion_" + i).val());
                formData.append('txtCiudad_' + i, $("#txtCiudad_" + i).val());
                formData.append('txtPais_' + i, $("#txtPais_" + i).val());
                formData.append('txtNombre_' + i, $("#txtNombre_" + i).val());
            }

            var cant = parseInt($("#hidCantCom").val());
            formData.append('hidCantCom', cant);
            for (i = 0; i < cant; i++) {
                formData.append('txtTituloObra_' + i, $("#txtTituloObra_" + i).val());
                formData.append('txtCompositor_' + i, $("#txtCompositor_" + i).val());
            }

            $.ajax({
                url: "ctacorriente.formulario.exterior.processor.php",
                type: 'post',
                contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                processData: false, // NEEDED, DON'T OMIT THIS
                dataType: 'json',
                data: formData,
                success: function (data) {
                    if (data.ok == 1) {
                        $("#txt_mensaje").empty();
                        $("#txt_mensaje").append("<span style='color: green;font-weight: 600;font-size: 13px;'>Los datos fueron enviados correctamente, recibirá un email con la información de su Nro. Tramite.</span>");

                        $("#btnEnviar").css("display", "none");
                    } else {
                        $("#txt_mensaje").empty();
                        $("#txt_mensaje").append("<span style='color: red;font-weight: 600;font-size: 12px;'>" + data.error + "</span>");

                        $("#btnEnviar").css("display", "block");
                    }
                }
            });

        }
    });
}

var opcionesPaises = '<option value="" id="">Elegir opción</option>\n\
<option value="Afganistán" id="AF">Afganistán</option>\n\
<option value="Albania" id="AL">Albania</option>\n\
<option value="Alemania" id="DE">Alemania</option>\n\
<option value="Andorra" id="AD">Andorra</option>\n\
<option value="Angola" id="AO">Angola</option>\n\
<option value="Anguila" id="AI">Anguila</option>\n\
<option value="Antártida" id="AQ">Antártida</option>\n\
<option value="Antigua y Barbuda" id="AG">Antigua y Barbuda</option>\n\
<option value="Antillas holandesas" id="AN">Antillas holandesas</option>\n\
<option value="Arabia Saudí" id="SA">Arabia Saudí</option>\n\
<option value="Argelia" id="DZ">Argelia</option>\n\
<option value="Argentina" id="AR">Argentina</option>\n\
<option value="Armenia" id="AM">Armenia</option>\n\
<option value="Aruba" id="AW">Aruba</option>\n\
<option value="Australia" id="AU">Australia</option>\n\
<option value="Austria" id="AT">Austria</option>\n\
<option value="Azerbaiyán" id="AZ">Azerbaiyán</option>\n\
<option value="Bahamas" id="BS">Bahamas</option>\n\
<option value="Bahrein" id="BH">Bahrein</option>\n\
<option value="Bangladesh" id="BD">Bangladesh</option>\n\
<option value="Barbados" id="BB">Barbados</option>\n\
<option value="Bélgica" id="BE">Bélgica</option>\n\
<option value="Belice" id="BZ">Belice</option>\n\
<option value="Benín" id="BJ">Benín</option>\n\
<option value="Bermudas" id="BM">Bermudas</option>\n\
<option value="Bhután" id="BT">Bhután</option>\n\
<option value="Bielorrusia" id="BY">Bielorrusia</option>\n\
<option value="Birmania" id="MM">Birmania</option>\n\
<option value="Bolivia" id="BO">Bolivia</option>\n\
<option value="Bosnia y Herzegovina" id="BA">Bosnia y Herzegovina</option>\n\
<option value="Botsuana" id="BW">Botsuana</option>\n\
<option value="Brasil" id="BR">Brasil</option>\n\
<option value="Brunei" id="BN">Brunei</option>\n\
<option value="Bulgaria" id="BG">Bulgaria</option>\n\
<option value="Burkina Faso" id="BF">Burkina Faso</option>\n\
<option value="Burundi" id="BI">Burundi</option>\n\
<option value="Cabo Verde" id="CV">Cabo Verde</option>\n\
<option value="Camboya" id="KH">Camboya</option>\n\
<option value="Camerún" id="CM">Camerún</option>\n\
<option value="Canadá" id="CA">Canadá</option>\n\
<option value="Chad" id="TD">Chad</option>\n\
<option value="Chile" id="CL">Chile</option>\n\
<option value="China" id="CN">China</option>\n\
<option value="Chipre" id="CY">Chipre</option>\n\
<option value="Ciudad estado del Vaticano" id="VA">Ciudad estado del Vaticano</option>\n\
<option value="Colombia" id="CO">Colombia</option>\n\
<option value="Comores" id="KM">Comores</option>\n\
<option value="Congo" id="CG">Congo</option>\n\
<option value="Corea" id="KR">Corea</option>\n\
<option value="Corea del Norte" id="KP">Corea del Norte</option>\n\
<option value="Costa del Marfíl" id="CI">Costa del Marfíl</option>\n\
<option value="Costa Rica" id="CR">Costa Rica</option>\n\
<option value="Croacia" id="HR">Croacia</option>\n\
<option value="Cuba" id="CU">Cuba</option>\n\
<option value="Dinamarca" id="DK">Dinamarca</option>\n\
<option value="Djibouri" id="DJ">Djibouri</option>\n\
<option value="Dominica" id="DM">Dominica</option>\n\
<option value="Ecuador" id="EC">Ecuador</option>\n\
<option value="Egipto" id="EG">Egipto</option>\n\
<option value="El Salvador" id="SV">El Salvador</option>\n\
<option value="Emiratos Arabes Unidos" id="AE">Emiratos Arabes Unidos</option>\n\
<option value="Eritrea" id="ER">Eritrea</option>\n\
<option value="Eslovaquia" id="SK">Eslovaquia</option>\n\
<option value="Eslovenia" id="SI">Eslovenia</option>\n\
<option value="España" id="ES">España</option>\n\
<option value="Estados Unidos" id="US">Estados Unidos</option>\n\
<option value="Estonia" id="EE">Estonia</option>\n\
<option value="c" id="ET">Etiopía</option>\n\
<option value="Ex-República Yugoslava de Macedonia" id="MK">Ex-República Yugoslava de Macedonia</option>\n\
<option value="Filipinas" id="PH">Filipinas</option>\n\
<option value="Finlandia" id="FI">Finlandia</option>\n\
<option value="Francia" id="FR">Francia</option>\n\
<option value="Gabón" id="GA">Gabón</option>\n\
<option value="Gambia" id="GM">Gambia</option>\n\
<option value="Georgia" id="GE">Georgia</option>\n\
<option value="Georgia del Sur y las islas Sandwich del Sur" id="GS">Georgia del Sur y las islas Sandwich del Sur</option>\n\
<option value="Ghana" id="GH">Ghana</option>\n\
<option value="Gibraltar" id="GI">Gibraltar</option>\n\
<option value="Granada" id="GD">Granada</option>\n\
<option value="Grecia" id="GR">Grecia</option>\n\
<option value="Groenlandia" id="GL">Groenlandia</option>\n\
<option value="Guadalupe" id="GP">Guadalupe</option>\n\
<option value="Guam" id="GU">Guam</option>\n\
<option value="Guatemala" id="GT">Guatemala</option>\n\
<option value="Guayana" id="GY">Guayana</option>\n\
<option value="Guayana francesa" id="GF">Guayana francesa</option>\n\
<option value="Guinea" id="GN">Guinea</option>\n\
<option value="Guinea Ecuatorial" id="GQ">Guinea Ecuatorial</option>\n\
<option value="Guinea-Bissau" id="GW">Guinea-Bissau</option>\n\
<option value="Haití" id="HT">Haití</option>\n\
<option value="Holanda" id="NL">Holanda</option>\n\
<option value="Honduras" id="HN">Honduras</option>\n\
<option value="Hong Kong R. A. E" id="HK">Hong Kong R. A. E</option>\n\
<option value="Hungría" id="HU">Hungría</option>\n\
<option value="India" id="IN">India</option>\n\
<option value="Indonesia" id="ID">Indonesia</option>\n\
<option value="Irak" id="IQ">Irak</option>\n\
<option value="Irán" id="IR">Irán</option>\n\
<option value="Irlanda" id="IE">Irlanda</option>\n\
<option value="Isla Bouvet" id="BV">Isla Bouvet</option>\n\
<option value="Isla Christmas" id="CX">Isla Christmas</option>\n\
<option value="Isla Heard e Islas McDonald" id="HM">Isla Heard e Islas McDonald</option>\n\
<option value="Islandia" id="IS">Islandia</option>\n\
<option value="Islas Caimán" id="KY">Islas Caimán</option>\n\
<option value="Islas Cook" id="CK">Islas Cook</option>\n\
<option value="Islas de Cocos o Keeling" id="CC">Islas de Cocos o Keeling</option>\n\
<option value="Islas Faroe" id="FO">Islas Faroe</option>\n\
<option value="Islas Fiyi" id="FJ">Islas Fiyi</option>\n\
<option value="Islas Malvinas Islas Falkland" id="FK">Islas Malvinas Islas Falkland</option>\n\
<option value="Islas Marianas del norte" id="MP">Islas Marianas del norte</option>\n\
<option value="Islas Marshall" id="MH">Islas Marshall</option>\n\
<option value="Islas menores de Estados Unidos" id="UM">Islas menores de Estados Unidos</option>\n\
<option value="Islas Palau" id="PW">Islas Palau</option>\n\
<option value="Islas Salomón" d="SB">Islas Salomón</option>\n\
<option value="Islas Tokelau" id="TK">Islas Tokelau</option>\n\
<option value="Islas Turks y Caicos" id="TC">Islas Turks y Caicos</option>\n\
<option value="Islas Vírgenes EE.UU." id="VI">Islas Vírgenes EE.UU.</option>\n\
<option value="Islas Vírgenes Reino Unido" id="VG">Islas Vírgenes Reino Unido</option>\n\
<option value="Israel" id="IL">Israel</option>\n\
<option value="Italia" id="IT">Italia</option>\n\
<option value="Jamaica" id="JM">Jamaica</option>\n\
<option value="Japón" id="JP">Japón</option>\n\
<option value="Jordania" id="JO">Jordania</option>\n\
<option value="Kazajistán" id="KZ">Kazajistán</option>\n\
<option value="Kenia" id="KE">Kenia</option>\n\
<option value="Kirguizistán" id="KG">Kirguizistán</option>\n\
<option value="Kiribati" id="KI">Kiribati</option>\n\
<option value="Kuwait" id="KW">Kuwait</option>\n\
<option value="Laos" id="LA">Laos</option>\n\
<option value="Lesoto" id="LS">Lesoto</option>\n\
<option value="Letonia" id="LV">Letonia</option>\n\
<option value="Líbano" id="LB">Líbano</option>\n\
<option value="Liberia" id="LR">Liberia</option>\n\
<option value="Libia" id="LY">Libia</option>\n\
<option value="Liechtenstein" id="LI">Liechtenstein</option>\n\
<option value="Lituania" id="LT">Lituania</option>\n\
<option value="Luxemburgo" id="LU">Luxemburgo</option>\n\
<option value="Macao R. A. E" id="MO">Macao R. A. E</option>\n\
<option value="Madagascar" id="MG">Madagascar</option>\n\
<option value="Malasia" id="MY">Malasia</option>\n\
<option value="Malawi" id="MW">Malawi</option>\n\
<option value="Maldivas" id="MV">Maldivas</option>\n\
<option value="Malí" id="ML">Malí</option>\n\
<option value="Malta" id="MT">Malta</option>\n\
<option value="Marruecos" id="MA">Marruecos</option>\n\
<option value="Martinica" id="MQ">Martinica</option>\n\
<option value="Mauricio" id="MU">Mauricio</option>\n\
<option value="Mauritania" id="MR">Mauritania</option>\n\
<option value="Mayotte" id="YT">Mayotte</option>\n\
<option value="México" id="MX">México</option>\n\
<option value="Micronesia" id="FM">Micronesia</option>\n\
<option value="Moldavia" id="MD">Moldavia</option>\n\
<option value="Mónaco" id="MC">Mónaco</option>\n\
<option value="Mongolia" id="MN">Mongolia</option>\n\
<option value="Montserrat" id="MS">Montserrat</option>\n\
<option value="Mozambique" id="MZ">Mozambique</option>\n\
<option value="Namibia" id="NA">Namibia</option>\n\
<option value="Nauru" id="NR">Nauru</option>\n\
<option value="Nepal" id="NP">Nepal</option>\n\
<option value="Nicaragua" id="NI">Nicaragua</option>\n\
<option value="Níger" id="NE">Níger</option>\n\
<option value="Nigeria" id="NG">Nigeria</option>\n\
<option value="Niue" id="NU">Niue</option>\n\
<option value="Norfolk" id="NF">Norfolk</option>\n\
<option value="Noruega" id="NO">Noruega</option>\n\
<option value="Nueva Caledonia" id="NC">Nueva Caledonia</option>\n\
<option value="Nueva Zelanda" id="NZ">Nueva Zelanda</option>\n\
<option value="Omán" id="OM">Omán</option>\n\
<option value="Panamá" id="PA">Panamá</option>\n\
<option value="Papua Nueva Guinea" id="PG">Papua Nueva Guinea</option>\n\
<option value="Paquistán" id="PK">Paquistán</option>\n\
<option value="Paraguay" id="PY">Paraguay</option>\n\
<option value="Perú" id="PE">Perú</option>\n\
<option value="Pitcairn" id="PN">Pitcairn</option>\n\
<option value="Polinesia francesa" id="PF">Polinesia francesa</option>\n\
<option value="Polonia" id="PL">Polonia</option>\n\
<option value="Portugal" id="PT">Portugal</option>\n\
<option value="Puerto Rico" id="PR">Puerto Rico</option>\n\
<option value="Qatar" id="QA">Qatar</option>\n\
<option value="Reino Unido" id="UK">Reino Unido</option>\n\
<option value="República Centroafricana" id="CF">República Centroafricana</option>\n\
<option value="República Checa" id="CZ">República Checa</option>\n\
<option value="República de Sudáfrica" id="ZA">República de Sudáfrica</option>\n\
<option value="República Democrática del Congo Zaire" id="CD">República Democrática del Congo Zaire</option>\n\
<option value="República Dominicana" id="DO">República Dominicana</option>\n\
<option value="Reunión" id="RE">Reunión</option>\n\
<option value="Ruanda" id="RW">Ruanda</option>\n\
<option value="Rumania" id="RO">Rumania</option>\n\
<option value="Rusia" id="RU">Rusia</option>\n\
<option value="Samoa" id="WS">Samoa</option>\n\
<option value="Samoa occidental" id="AS">Samoa occidental</option>\n\
<option value="San Kitts y Nevis" id="KN">San Kitts y Nevis</option>\n\
<option value="San Marino" id="SM">San Marino</option>\n\
<option value="San Pierre y Miquelon" id="PM">San Pierre y Miquelon</option>\n\
<option value="San Vicente e Islas Granadinas" id="VC">San Vicente e Islas Granadinas</option>\n\
<option value="Santa Helena" id="SH">Santa Helena</option>\n\
<option value="Santa Lucía" id="LC">Santa Lucía</option>\n\
<option value="Santo Tomé y Príncipe" id="ST">Santo Tomé y Príncipe</option>\n\
<option value="Senegal" id="SN">Senegal</option>\n\
<option value="Serbia y Montenegro" id="YU">Serbia y Montenegro</option>\n\
<option value="Sychelles" id="SC">Seychelles</option>\n\
<option value="Sierra Leona" id="SL">Sierra Leona</option>\n\
<option value="Singapur" id="SG">Singapur</option>\n\
<option value="Siria" id="SY">Siria</option>\n\
<option value="Somalia" id="SO">Somalia</option>\n\
<option value="Sri Lanka" id="LK">Sri Lanka</option>\n\
<option value="Suazilandia" id="SZ">Suazilandia</option>\n\
<option value="Sudán" id="SD">Sudán</option>\n\
<option value="Suecia" id="SE">Suecia</option>\n\
<option value="Suiza" id="CH">Suiza</option>\n\
<option value="Surinam" id="SR">Surinam</option>\n\
<option value="Svalbard" id="SJ">Svalbard</option>\n\
<option value="Tailandia" id="TH">Tailandia</option>\n\
<option value="Taiwán" id="TW">Taiwán</option>\n\
<option value="Tanzania" id="TZ">Tanzania</option>\n\
<option value="Tayikistán" id="TJ">Tayikistán</option>\n\
<option value="Territorios británicos del océano Indico" id="IO">Territorios británicos del océano Indico</option>\n\
<option value="Territorios franceses del sur" id="TF">Territorios franceses del sur</option>\n\
<option value="Timor Oriental" id="TP">Timor Oriental</option>\n\
<option value="Togo" id="TG">Togo</option>\n\
<option value="Tonga" id="TO">Tonga</option>\n\
<option value="Trinidad y Tobago" id="TT">Trinidad y Tobago</option>\n\
<option value="Túnez" id="TN">Túnez</option>\n\
<option value="Turkmenistán" id="TM">Turkmenistán</option>\n\
<option value="Turquía" id="TR">Turquía</option>\n\
<option value="Tuvalu" id="TV">Tuvalu</option>\n\
<option value="Ucrania" id="UA">Ucrania</option>\n\
<option value="Uganda" id="UG">Uganda</option>\n\
<option value="Uruguay" id="UY">Uruguay</option>\n\
<option value="Uzbekistán" id="UZ">Uzbekistán</option>\n\
<option value="Vanuatu" id="VU">Vanuatu</option>\n\
<option value="Venezuela" id="VE">Venezuela</option>\n\
<option value="Vietnam" id="VN">Vietnam</option>\n\
<option value="Wallis y Futuna" id="WF">Wallis y Futuna</option>\n\
<option value="Yemen" id="YE">Yemen</option>\n\
<option value="Zambia" id="ZM">Zambia</option>\n\
<option value="Zimbabue" id="ZW">Zimbabue</option>';


function agregarFecha() {
    var i = $("#hidCantFec").val();

    var html = "";
    html += "<tr id='fecha_" + i + "'>\n\
  <td><input autocomplete='off' type='text' name='txtFechaSadaic_" + i + "' id='txtFechaSadaic_" + i + "' style='width:80px'></td>\n\
  <td><input type='text' name='txtEvento_" + i + "' id='txtEvento_" + i + "' style='width:110px'></td>\n\
  <td><input type='text' name='txtDireccion_" + i + "' id='txtDireccion_" + i + "' style='width:110px'></td>\n\
  <td><select name='txtPais_" + i + "' id='txtPais_" + i + "' style='width:110px'>" + opcionesPaises + "</select></td>\n\
  <td><input type='text' name='txtCiudad_" + i + "' id='txtCiudad_" + i + "' style='width:110px'></td>\n\
  <td><input type='text' name='txtNombre_" + i + "' id='txtNombre_" + i + "' style='width:100px'></td>\n\
  <td><a href='Javascript:eliminarFecha(" + i + ");' style='text-decoration:none; color: #dc2103; font-weight:600'>[x]</a></td>\n\
  </tr>";
    $("#fechas").append(html);
    $("#hidCantFec").val(parseInt($("#hidCantFec").val()) + 1);

    $("#txtFechaSadaic_" + i).datepicker({
        format: 'dd-mm-yyyy',
        closeText: 'Cerrar',
        prevText: '<Ant',
        nextText: 'Sig>',
        currentText: 'Hoy',
        months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        days: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        daysShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
        daysMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
        weekHeader: 'Sm',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    });
}

function eliminarFecha(id) {
    $("#fecha_" + id).remove();
}



function agregarCompositor() {
    var i = $("#hidCantCom").val();

    var html = "";
    html += "<tr id='compositor_" + i + "'>\n\
  <td style='text-align: center;'><input type='text' name='txtTituloObra_" + i + "' id='txtTituloObra_" + i + "' style='width:300px;text-align:center'></td>\n\
  <td style='text-align: center;'><input type='text' name='txtCompositor_" + i + "' id='txtCompositor_" + i + "' style='width:300px;text-align:center'></td>\n\
  <td style='text-align: center;'><a href='Javascript:eliminarCompositor(" + i + ");' style='text-decoration:none; color: #dc2103; font-weight:600'>[x]</a></td>\n\
  </tr>;"

    $("#compositores").append(html);
    $("#hidCantCom").val(parseInt($("#hidCantCom").val()) + 1);
}

function eliminarCompositor(id) {
    $("#compositor_" + id).remove();
}

function emailIsValid(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
}

function isNumber(evt) {
    var iKeyCode = (evt.which) ? evt.which : evt.keyCode
    if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
        return false;

    return true;
}
</script>
<script src="{{ asset('js/sadaic.js') }}" defer></script>
@endpush

@push('styles')
<style>
#embed-header h2 {
    border-bottom: solid 1px rgb(208, 215, 219);
    padding-bottom: 8px;
}
</style>
@endpush