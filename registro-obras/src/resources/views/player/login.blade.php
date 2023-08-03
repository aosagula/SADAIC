<script>
const formData = new FormData();
formData.append("interpreteid", "{{$player_id}}");
formData.append("clave", "{{$password}}");
formData.append("g-recaptcha-response", "qkstudio");
formData.append("x", "39");
formData.append("y", "14");
formData.append("capituloid", "45");
formData.append("capitulotitulo", "Cuenta Corriente");
formData.append("subcapituloid", "");
formData.append("subcapitulotitulo", "");
formData.append("load", "ctacorriente.login.php");

const request = new XMLHttpRequest();
request.open("POST", "https://www.sadaic.org.ar/ctacorriente.ida.login.processor.php");
request.onload = () => {
  window.location = request.responseURL;
};
request.send(formData);
</script>