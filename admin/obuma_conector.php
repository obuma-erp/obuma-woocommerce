<?php 
class ObumaConector{
	
	public static function get($url, $access_token){
		// Inicia cURL
        $session = curl_init($url);
        // Indica a cURL que retorne data
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        // Configura cabeceras
        $headers = array(
            'access-token: ' . $access_token,
            'Accept: application/json',
            'Content-Type: application/json'
        );
        curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
        // Ejecuta cURL
        $response = curl_exec($session);
        $code = curl_getinfo($session, CURLINFO_HTTP_CODE);
        curl_close($session);
    	$json = json_decode($response);
    	return $json;
	}

    public static function post( $url, $data, $access_token ){

        
        // json encode data
        $data_string = json_encode($data);
                
        // Inicia cURL
        $session = curl_init($url);

        // Indica a cURL que retorne data
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        // Configura cabeceras
        $headers = array(
            'access-token: ' . $access_token,
            'Accept: application/json',
            'Content-Type: application/json'
        );
        curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

        // Indica que se va ser una petición POST
        curl_setopt($session, CURLOPT_POST, true);

        // Agrega parámetros
        curl_setopt($session, CURLOPT_POSTFIELDS, $data_string);

        // Ejecuta cURL
        $response = curl_exec($session);
        $code = curl_getinfo($session, CURLINFO_HTTP_CODE);

        // Cierra la sesión cURL
        curl_close($session);

        //Esto es sólo para poder visualizar lo que se está retornando
        //---print_r($response);

        $json = json_decode($response);
        
        return $json;

    }

}