<?php
header('Content-Type: application/json; charset=utf-8');

require_once 'classes/Pessoas.php';

class Rest
{
    public static function open($requisicao)
    {
        if (isset($requisicao['url'])) {
            $url = explode('/', $requisicao['url']);

            $classe = ucfirst($url[0]);
            array_shift($url);

            $metodo = $url[0];
            switch ($metodo) {
                case 'saldo':
                    $metodo = 'emitirSaldo';
                    break;
                case 'extrato':
                    $metodo = 'emitirExtrato';
                    break;
                case 'credito':
                    $metodo = 'creditar';
                    break;
                case 'debito':
                    $metodo = 'debitar';
                    break;
                case 'transferencia':
                    $metodo = 'transferir';
                    break;
                default:
                    # code...
                    break;
            }
            array_shift($url);

            $cpf = $url[0];
            array_shift($url);

            $parametros = array();
            $parametros = $url;

            try {
                if (class_exists($classe)) {
                    if (method_exists($classe, $metodo)) {
                        $retorno = call_user_func_array(array(new $classe($cpf), $metodo), $parametros);
                        return json_encode(array('status' => 'sucesso', 'dados' => $retorno));
                    } else {
                        return json_encode(array('status' => 'erro', 'dados' => 'Método inexistente!'));
                    }
                } else {
                    return json_encode(array('status' => 'erro', 'dados' => 'Classe inexistente!'));
                }
            } catch (Exception $e) {
                return json_encode(array('status' => 'erro', 'dados' => $e->getMessage()));
            }
        } else {
            return json_encode(array('status' => 'sucesso', 'dados' => 'Bem vindo ao CTRL FIN!'));
        }
    }
}

if (isset($_REQUEST)) {
    echo Rest::open($_REQUEST);
}
