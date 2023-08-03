<?php

namespace App\SADAIC;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use \DOMDocument;
use \DOMXpath;

class Integration {
    protected $baseURL = '/email/verify';

    public function __construct()
    {
        $this->baseURL = env('SADAIC_WEB', 'https://www.sadaic.org.ar');
    }

    public function login(string $member_id, int $heir, string $password): bool
    {
        $params = [
            "socio"                => $member_id,
            "heredero"             => $heir,
            "clave"                => $password,
            "g-recaptcha-response" => "qkstudio",
            "x"                    => 56,
            "y"                    => 9,
            "capituloid"           => 45,
            "capitulotitulo"       => "Cuenta Corriente",
            "subcapituloid"        => "",
            "subcapitulotitulo"    => "",
            "load"                 => "ctacorriente.login.php"
        ];

        $preparedParams = http_build_query($params);

        // Archivo para guardar copia local de la cookie devuelta port SADAIC
        $fileName = "sadaic/cookies/member_$member_id-$heir.txt";
        Storage::disk('local')->put($fileName, "");
        $fullPath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $fullPath .= $fileName;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => $this->baseURL . "/ctacorriente.login.processor.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => false, // Desactivamos la redirección para capturar el resultado del login
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $preparedParams,
            CURLOPT_HTTPHEADER     => array(
                "Content-Type: application/x-www-form-urlencoded"
            ),
            CURLOPT_COOKIEJAR      => $fullPath // Archivo para almacenar la cookie de SADAIC
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if($response !== "") {
            $message = "";
            switch($response) {
                case "901": // Clave errónea
                    $message = "Socio o clave incorrectos";
                break;
                case "902": // Clave vacia
                    $message = "Debido a la implementación de nuevas medidas de seguridad, se han limpiado todas las claves del sistema. Inicie el trámite de recuperación de clave para solicitur una nueva clave.";
                break;
                case "903": // Socio inactivo
                    $message = "La cuenta del socio se encuentra inactiva";
                break;
                case "904904": // Clave errónea más de 3 veces
                    $response = "904"; // Fix: Por error en el sitio actual de SADAIC, el código 904 se devuelve duplicado
                    $message = "Su cuenta ha sido bloqueada por superar la cantidad de ingresos erróneos.";
                break;
                case "980": // Rechazado
                    $message = "Su solucitud ha sido rechazada por el departamento de Socios de SADAIC.";
                break;
            }

            session([
                "sadaic.member_id"         => $member_id,
                "sadaic.heir"              => $heir,
                "sadaic.loggedIn"          => false,
                "sadaic.loginError"        => intval($response),
                "sadaic.loginErrorMessage" => $message
            ]);

            Storage::disk('local')->delete($fullPath);

            return false;
        }

        session([
            "sadaic.member_id"         => $member_id,
            "sadaic.heir"              => $heir,
            "sadaic.loggedIn"          => true,
            "sadaic.cookieFile"        => $fileName,
            "sadaic.loginError"        => 0,
            "sadaic.loginErrorMessage" => ""
        ]);

        return true;
    }

    public function getLoginError(): array {
        return [
            "error"   => session("sadaic.loginError"),
            "message" => session("sadaic.loginErrorMessage")
        ];
    }

    public function embed(string $url, $selector)
    {
        $loggedIn = session("sadaic.loggedIn", false);
        if (!$loggedIn) {
            return "No conectado a SADAIC";
        }

        // Si la url no tiene barra inicial, se la agregamos antes de unirla
        // con la url base
        if ($url[0] != '/') {
            $url = '/' . $url;
        }

        $url = $this->baseURL . $url;

        $cookiePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $cookiePath .= session("sadaic.cookieFile");

        Log::debug($url);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => false, // Desactivamos la redirección para capturar el resultado del login
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array(
              "Content-Type: application/x-www-form-urlencoded"
            ),
            CURLOPT_COOKIEFILE     => $cookiePath // Archivo para almacenar la cookie de SADAIC
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $output = $this->processResponse($response, $selector);

        return $output;
    }

    public function submit(string $url, array $formData)
    {
        $loggedIn = session("sadaic.loggedIn", false);
        if (!$loggedIn) {
            return response(401);
        }

        // Si la url no tiene barra inicial, se la agregamos antes de unirla con la url base
        if ($url[0] != '/') {
            $url = '/' . $url;
        }
        $url = $this->baseURL . $url;

        $cookiePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $cookiePath .= session("sadaic.cookieFile");

        $preparedParams = http_build_query($formData);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => false, // Desactivamos la redirección para capturar el resultado del login
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $preparedParams,
            CURLOPT_HTTPHEADER     => array(
              "Content-Type: application/x-www-form-urlencoded"
            ),
            CURLOPT_COOKIEFILE     => $cookiePath // Archivo para almacenar la cookie de SADAIC
        ));

        $response = curl_exec($curl);

        // Fix: No todas las respuestas tienen el mismo charset
        $contentType = mb_detect_encoding($response);
        if ($contentType) {
            $response = mb_convert_encoding($response, "UTF-8", $contentType);
        }

        $redirect = curl_getinfo($curl, CURLINFO_REDIRECT_URL);

        curl_close($curl);

        // "Determinamos" si el resultado es HTML y lo procesamos
        if (stripos($response, '<head>') !== false) {
            $response = $this->processResponse($response, ".general-texto");
        }

        return [
            "redirect" => $redirect,
            "response" => $response
        ];
    }

    /**
     * $response    HTML a procesar
     * $target      string | array con el o los selectores de los elementos a recuperar
     *              del HTML. Ejemplos:
     *              - div[2] recupera el tercer div
     *              - .texto recupera el primer elemento con la clase texto
     *              - #form recupera el elemento con la clase form
     * 
     *              Observación: En el caso de los ids, si el documento no es válido (más
     *              de un elemento con el mismo id), la búsqueda retornará no encontrado
     */
    private function processResponse($response, $target)
    {
        // Fix: No todas las respuestas tienen el mismo charset
        $response = mb_convert_encoding($response, "ISO-8859-1", "UTF-8");

        // Generamos un nuevo documento DOM a partir del HTML de la respuesta
        $doc = new DOMDocument();
        $doc->loadHTML($response, LIBXML_NOERROR);

        if (is_string($target)) {
            $target = [$target];
        }

        $xpath = new DOMXpath($doc);

        $output = "";

        foreach($target as $selector) {
            $selector = $this->parseSelector($selector);

            // Recuperamos el elemento que matchee con el selector indicado
            switch($selector['type']) {
                case 'class':
                    // https://stackoverflow.com/a/1390680
                    $results = $xpath->query("//div[contains(concat(' ', normalize-space(@class),' '), ' " . $selector['name'] . " ')]");

                    // Elemento no encontrado
                    if ($results->length == 0) {
                        return response(404);
                    }

                    $node = $results->item($selector['index']); // Recuperamos el DOM Node
                break;
                case 'id':
                    $node = $doc->getElementById($selector['name']); // Recuperamos el DOM Node (DOMElement)

                    // Elemento no encontrado
                    if (!$node) {
                        return response(404);
                    }
                break;
                case 'doc':
                    $node = $doc;
                default:
                    throw new Exception('Selector no soportado: ' . $selector);
            }

            // Corregimos las URLs de las imágenes relativas
            $images = $node->getElementsByTagName("img");
            foreach ($images as $image) {
                $src = $image->getAttribute("src");
                // Si el enlace no es absoluto
                if (substr(0, 4) !== "http") {
                    // Si la url no tiene barra inicial, se la agregamos antes de unirla con la url base
                    if ($src[0] != '/') {
                        $src = '/' . $src;
                    }

                    // Agregamos el dominio
                    $src = $this->baseURL . $src;
                    $image->setAttribute("src", $src);
                }
            }

            // Correción de enlaces internos: a href
            $links = $xpath->query("//a[not(starts-with(@href, 'http'))]");
            foreach ($links as $link) {
                // Anulamos el enlace poniendo # como href 
                $href = $link->getAttribute("href");

                // Omitimos los links utilizados como botones
                if (preg_match('/javascript:/i', $href)) {
                    continue;
                }

                $link->setAttribute("href", "#");
                $link->setAttribute("data-href", $href);

                // Agregamos la clase sadaic-internal-link para poder gestionarla
                // desde Javascript
                $classes = $link->getAttribute("class");
                $classes .= " sadaic-internal-link";
                $link->setAttribute("class", $classes);
            }

            // Corregimos las URLs de los input image
            $inputs = $xpath->query("//input[@type='image']");
            foreach ($inputs as $input) {
                // Recuperamos el valor de src
                $src = $input->getAttribute("src");

                // Si la url no tiene barra inicial, se la agregamos antes de unirla con la url base
                if ($src[0] != '/') {
                    $src = '/' . $src;
                }

                // Agregamos el dominio
                $src = $this->baseURL . $src;

                // Seteamos el valor corregido
                $input->setAttribute("src", $src);
            }

            // Quitar recaptchas
            // https://stackoverflow.com/a/1390680
            $recaptchas = $xpath->query("//div[contains(concat(' ', normalize-space(@class),' '), ' g-recaptcha ')]");
            foreach ($recaptchas as $recaptcha) {
                // Creamos un input hidden para reemplazar el recaptcha
                $hidden = $doc->createElement("input");
                $hidden->setAttribute("type", "hidden");
                $hidden->setAttribute("name", "g-recaptcha-response");
                $hidden->setAttribute("value", "qkstudio");

                $recaptcha->parentNode->replaceChild($hidden, $recaptcha);
            }

            // Captura de los submit de los forms
            $forms = $xpath->query("//form");
            foreach ($forms as $form) {
                // Quitamos el action y el onsubmit de los forms
                $action = $form->getAttribute("action");
                $form->removeAttribute("action");
                $form->setAttribute("data-action", $action);

                // Agregamos la clase sadaic-internal-form para poder gestionarla
                // desde Javascript
                $classes = $form->getAttribute("class");
                $classes .= " sadaic-internal-form";
                $form->setAttribute("class", $classes);
            }

            // Corregimos las URLs de los iframes
            $iframes = $node->getElementsByTagName("iframe");
            foreach ($iframes as $iframe) {
                // Recuperamos el valor de src
                $src = $iframe->getAttribute("src");

                // Si el enlace no es absoluto
                if (substr(0, 4) !== "http") {
                    // Si la url no tiene barra inicial, se la agregamos antes de unirla con la url base
                    if ($src[0] != '/') {
                        $src = '/' . $src;
                    }
                    
                    // Agregamos el dominio
                    $src = $this->baseURL . $src;

                    // Seteamos el nuevo valor
                    $iframe->setAttribute("src", $src);
                }
            }

            // Recuperamos el HTML del nodo
            if ($selector['type'] == 'doc') {
                $output .= $node->saveHTML();
            } else {
                $output .= $node->ownerDocument->saveHTML($node);
            }
        }

        return $output;
    }

    private function parseSelector($selector)
    {
        $output = [];

        switch($selector[0]) {
            case '.':
                $output['type'] = 'class';
            break;
            case '#':
                $output['type'] = 'id';
            break;
            default:
                $output['type'] = 'document';
        }

        $begin = strpos($selector, "[");
        if ($begin !== false) {
            $begin += 1; // Después del [

            $output['index'] = intval(substr(
                $selector,
                $begin,
                strpos($selector, "]") - $begin
            ));
        } else {
            $output['index'] = 0;
            $begin = strlen($selector) + 1;
        }

        if ($output['type'] != 'doc') {
            $output['name'] = substr($selector, 1, $begin - 2);
        } else {
            $output['name'] = substr($selector, 0, $begin - 1);
        }

        return $output;
    }
}