<?php

namespace App\API; 

/**
 * Base class for Json RPC
 * based  on https://github.com/datto/php-json-rpc
 */
abstract class JsonRPC
{

    const VERSION = '2.0';

    public function Execute() {



        $request = file_get_contents('php://input');

        if (!is_string($request)) {
            $response = self::parseError();
        } else {
            $json = json_decode($request, true);
            $response = $this->processInput($json);
        }


        if ($response != null) {
            echo json_encode($response, JSON_UNESCAPED_UNICODE );
        } else {
             http_response_code(200);
        }
    }

    
    protected function checkAcess(){
       $api = \App\System::getOptions('api') ;
       $user = null;    
  
        //Bearer
        if($api['atype']==1) {
        
            $jwt ="";
            $headers = apache_request_headers();            
            foreach ($headers as $header => $value) {
                 if($header=="Authorization" ){
                     $jwt = str_replace("Bearer ","",$value) ;
                     $jwt = trim($jwt);
                     break;
                 }
            }     
         
          $key = strlen($api['key']) > 0 ?  $api['key'] : "defkey" ;
                
         
          $decoded = \Firebase\JWT\JWT::decode($jwt, $key, array('HS256'));    
         
          $user = \App\Entity\User::load($decoded->user_id);
      }
      //Basic
      if($api['atype']==2) {
          $user = \App\Helper::login($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']) ;
          
      }
      //без  авторризации
      if($api['atype']==3) {
          $user=\App\Entity\User::getByLogin('admin') ;
      }
      if($user == null){
         throw new  \Exception('Пользователь не  найден',1001);    
      }
      \App\System::setUser($user) ;
  
        
     return  true;
        
    }
    
    
    
    
    
    
    /**
     * Processes the user input, and prepares a response (if necessary).
     *
     * @param string $json
     * Single request object, or an array of request objects, as a JSON string.
     *
     * @return array|null
     * Returns a response object (or an error object) when a query is made.
     * Returns an array of response/error objects when multiple queries are made.
     * Returns null when no response is necessary.
     */
    private function processInput($input) {


        if (!is_array($input)) {
            return self::parseError();
        }

        if (count($input) === 0) {
            return self::requestError();
        }

        if (isset($input[0])) {
            return $this->processBatchRequests($input);
        }

        return $this->processRequest($input);
    }

    /**
     * Processes a batch of user requests, and prepares the response.
     *
     * @param array $input
     * Array of request objects.
     *
     * @return array|null
     * Returns a response/error object when a query is made.
     * Returns an array of response/error objects when multiple queries are made.
     * Returns null when no response is necessary.
     */
    private function processBatchRequests($input) {
        $replies = array();

        foreach ($input as $request) {
            $reply = $this->processRequest($request);

            if ($reply !== null) {
                $replies[] = $reply;
            }
        }

        if (count($replies) === 0) {
            return null;
        }

        return $replies;
    }

    /**
     * Processes an individual request, and prepares the response.
     *
     * @param array $request
     * Single request object to be processed.
     *
     * @return array|null
     * Returns a response object or an error object.
     * Returns null when no response is necessary.
     */
    private function processRequest($request) {
        if (!is_array($request)) {
            return self::requestError();
        }
        
        // The presence of the 'id' key indicates that a response is expected
        $isQuery = array_key_exists('id', $request);

        $id = &$request['id'];

        if (($id !== null) && !is_int($id) && !is_float($id) && !is_string($id)) {
            return self::requestError();
        }

        $version = &$request['jsonrpc'];

        if ($version !== self::VERSION) {
            return self::requestError($id);
        }

        $method = &$request['method'];

        if (!is_string($method)) {
            return self::requestError($id);
        }

        // The 'params' key is optional, but must be non-null when provided
        if (array_key_exists('params', $request)) {
            $arguments = $request['params'];

            if (!is_array($arguments)) {
                return self::requestError($id);
            }
        } else {
            $arguments = array();
        }

        if ($isQuery) {
            return $this->processQuery($id, $method, $arguments);
        }

        $this->processNotification($method, $arguments);
        return null;
    }

    /**
     * Processes a query request and prepares the response.
     *
     * @param mixed $id
     * Client-supplied value that allows the client to associate the server response
     * with the original query.
     *
     * @param string $method
     * String value representing a method to invoke on the server.
     *
     * @param array $arguments
     * Array of arguments that will be passed to the method.
     *
     * @return array
     * Returns a response object or an error object.
     */
    private function processQuery($id, $method, $arguments) {
          
            
        if (method_exists($this, $method) == false) {
            return self::error($id, -32601, "Method '{$method}' not found");
        }
        
        try{
                     
          $result =  @call_user_func_array(array($this, $method), $arguments);
        } catch(\Exception $e){
            return self::error($id, $e->getCode() , $e->getMessage());
        }
        
        if ($result != false) {
            return self::response($id, $result);
        }
    }

    /**
     * Processes a notification. No response is necessary.
     *
     * @param string $method
     * String value representing a method to invoke on the server.
     *
     * @param array $arguments
     * Array of arguments that will be passed to the method.
     */
    private function processNotification($method, $arguments) {
        if (method_exists($this, $method)) {
            @call_user_func_array(array($this, $method), $arguments);
        }
    }

    /**
     * Returns an error object explaining that an error occurred while parsing
     * the JSON text input.
     *
     * @return array
     * Returns an error object.
     */
    private static function parseError() {
        return self::error(null, -32700, 'Parse error');
    }

    /**
     * Returns an error object explaining that the JSON input is not a valid
     * request object.
     *
     * @param mixed $id
     * Client-supplied value that allows the client to associate the server response
     * with the original query.
     *
     * @return array
     * Returns an error object.
     */
    private static function requestError($id = null) {
        return self::error($id, -32600, 'Invalid Request');
    }

    /**
     * Returns a properly-formatted error object.
     *
     * @param mixed $id
     * Client-supplied value that allows the client to associate the server response
     * with the original query.
     *
     * @param int $code
     * Integer value representing the general type of error encountered.
     *
     * @param string $message
     * Concise description of the error (ideally a single sentence).
     *
     * @param null|boolean|integer|float|string|array $data
     * An optional primitive value that contains additional information about
     * the error.
     *
     * @return array
     * Returns an error object.
     */
    protected static function error($id, $code, $message, $data = null) {
        $error = array(
            'code'    => $code,
            'message' => $message
        );

        if ($data !== null) {
            $error['data'] = $data;
        }

        return array(
            'jsonrpc' => self::VERSION,
            'id'      => $id,
            'error'   => $error
        );
    }

    /**
     * Returns a properly-formatted response object.
     *
     * @param mixed $id
     * Client-supplied value that allows the client to associate the server response
     * with the original query.
     *
     * @param mixed $result
     * Return value from the server method, which will now be delivered to the user.
     *
     * @return array
     * Returns a response object.
     */
    private static function response($id, $result) {
        return array(
            'jsonrpc' => self::VERSION,
            'id'      => $id,
            'result'  => $result
        );
    }
    
    
 

}