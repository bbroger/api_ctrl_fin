<?php
require_once("Conexao.php");

class Pessoas extends Conexao
{
    public function __construct($cpf)
    {
        $conn = parent::get_instance();
        $sql = "SELECT * FROM tb_pessoas WHERE cpf = :cpf LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":cpf", $cpf);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            foreach ($stmt as $row_pessoa) {
                $this->cpf = $row_pessoa['cpf'];
                $this->saldo = $row_pessoa['saldo'];
            }
        } else {
            throw new Exception("Nenhuma pessoa encontrada com esse CPF");
        }
    }

    public function emitirSaldo()
    {
        $saldo = $this->saldo;
        $this->registrarMovimentacao($this->cpf, "Emitir Saldo", "", "", "Emitido");
        return $saldo;
    }

    public function emitirExtrato()
    {
        $conn = parent::get_instance();
        $sql = "SELECT * FROM tb_movimentacao WHERE pessoa_cpf = :pessoa_cpf";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":pessoa_cpf", $this->cpf);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $this->registrarMovimentacao($this->cpf, "Emitir Extrato", "", "", "Emitido");
            return $stmt->fetchAll();
        } else {
            throw new Exception("Nenhuma movimentação encontrada");
        }
    }

    public function debitar($valor)
    {
        if (($this->saldo - $valor) >= 0 && $valor > 0) {
            $this->saldo -= $valor;
            $conn = parent::get_instance();
            $sql = "UPDATE tb_pessoas SET saldo = :saldo WHERE cpf = :cpf";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":saldo", $this->saldo);
            $stmt->bindParam(":cpf", $this->cpf);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $this->registrarMovimentacao($this->cpf, "Debitar", "", $valor);
                return true;
            } else {
                throw new Exception("Saldo insuficiente para efetuar do débito");
            }
        } else {
            throw new Exception("Saldo insuficiente para efetuar o debito.");
        }
    }

    public function creditar($valor)
    {
        if (isset($valor) && $valor > 0) {
            $this->saldo += $valor;
            $conn = parent::get_instance();
            $sql = "UPDATE tb_pessoas SET saldo = :saldo WHERE cpf = :cpf";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":saldo", $this->saldo);
            $stmt->bindParam(":cpf", $this->cpf);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $this->registrarMovimentacao($this->cpf, "Creditar", "", $valor);
                return true;
            } else {
                throw new Exception("Favor informar um valor válido.");
            }
        } else {
            throw new Exception("Favor informar um valor válido.");
        }
    }

    public function transferir($valor, $beneficiario)
    {
        try {
            $pessoas = new Pessoas($beneficiario);
            if ($this->debitar($valor, true)) {
                if ($pessoas->creditar($valor)) {
                    $this->registrarMovimentacao($this->cpf, "Transferir", $beneficiario, $valor);
                    return true;
                }
            }
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function registrarMovimentacao($pessoa, $tipo, $beneficiario = null, $valor = null)
    {
        switch ($tipo) {
            case 'Emitir Saldo';
            case 'Emitir Extrato';
                $conn = parent::get_instance();
                $queryInsert = " INSERT INTO tb_movimentacao (pessoa_cpf, tipo, data_movimentacao) 
                    VALUES (:pessoa_cpf, :tipo, :situacao, NOW()); ";
                $stmt = $conn->prepare($queryInsert);
                $stmt->bindParam(':pessoa_cpf', $pessoa);
                $stmt->bindParam(':tipo', $tipo);
                $stmt->execute();
                break;
            case 'Debitar';
            case 'Creditar';
                $conn = parent::get_instance();
                $queryInsert = " INSERT INTO tb_movimentacao (pessoa_cpf, tipo, valor, data_movimentacao) 
                    VALUES (:pessoa_cpf, :tipo, :valor, NOW()); ";
                $stmt = $conn->prepare($queryInsert);
                $stmt->bindParam(':pessoa_cpf', $pessoa);
                $stmt->bindParam(':tipo', $tipo);
                $stmt->bindParam(':valor', $valor);
                $stmt->execute();
                break;
            case 'Transferir';
                $conn = parent::get_instance();
                $queryInsert = " INSERT INTO tb_movimentacao (pessoa_cpf, tipo, beneficiario, valor, data_movimentacao) 
                    VALUES (:pessoa_cpf, :tipo, :beneficiario, :valor, NOW()); ";
                $stmt = $conn->prepare($queryInsert);
                $stmt->bindParam(':pessoa_cpf', $pessoa);
                $stmt->bindParam(':tipo', $tipo);
                $stmt->bindParam(':beneficiario', $beneficiario);
                $stmt->bindParam(':valor', $valor);
                $stmt->execute();
                break;
            default:
                # code...
                break;
        }
    }
}
