<?php
Class Cliente{

    private $pdo;

    public function __construct($dbname, $host, $user, $senha){
        try{
            $this->pdo = new PDO("mysql:dbname=".$dbname.";host".$host,$user,$senha);
        }
        catch(PDOException $e){
            echo "Erro com o BD: ".$e->getMessage();
            exite();
        }
        catch(Exception $e){
            echo "Erro Genérico: ".$e->getMessage();
            exite();
        }
    }

    public function buscarDados()
    {
        $resultado = array();
        $comando = $this->pdo->query("SELECT * FROM cliente INNER JOIN cidades ON cliente.id_cidade_fk = cidades.id_cidade ORDER BY Nome");
        $resultado = $comando->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }

    function combobox($selecionado = null) {
        $resultado = array();
        $comando = $this->pdo->query("SELECT * FROM cidades ORDER BY Cidade ASC");
        $comando->execute();

        if($comando->rowCount() > 0){
            while($dados = $comando->fetch(PDO::FETCH_ASSOC)){
                if($selecionado == $dados['id_cidade']){
                    echo "<option value='{$dados['id_cidade']}' selected>{$dados['Cidade']}</option>";
                }else{
                    echo "<option value='{$dados['id_cidade']}'>{$dados['Cidade']}</option>";
                }                
            }
        }
        
    }


    public function cadastrarCliente($nome, $cidade)
    {
        $comando = $this->pdo->prepare("SELECT id FROM cliente WHERE Nome = :n");
        $comando->bindValue(":n", $nome);
        $comando->execute();
        if($comando->rowCount() > 0)//se ja existe no BD
        {
            return false;
        }else{ //não há registro no BD
            $comando = $this->pdo->prepare("INSERT INTO cliente (Nome, id_cidade_fk) VALUES (:n, :c)");
            $comando->bindValue(":n",$nome);
            $comando->bindValue(":c",$cidade);
            $comando->execute();
            return true;
        }
    }


    public function excluirCliente($id)
    {
        $comando = $this->pdo->prepare("DELETE FROM cliente WHERE id_cliente = :id_cliente");
        $comando->bindValue(":id_cliente", $id);
        $comando->execute();
    }
    
    //BUSCAR DADOS DE UMA PESSOA
    public function buscarDadosCliente($id){
        $res = array();
        $comando = $this->pdo->prepare("SELECT * FROM cliente INNER JOIN cidades ON cliente.id_cidade_fk = cidades.id_cidade WHERE id_cliente = :id");
        $comando->bindValue(":id", $id);
        $comando->execute();
        $res = $comando->fetch(PDO::FETCH_ASSOC);
        return $res;
    }

    //ATUALIZAR DADOS NO BANCO DE DADOS
    public function atualizarDados($id, $nome, $cidade)
    {
        $comando = $this->pdo->prepare("UPDATE cliente SET Nome = :n, id_cidade_fk = :c WHERE id_cliente = :id");
        $comando->bindValue(":n", $nome);
        $comando->bindValue(":c", $cidade);
        $comando->bindValue(":id", $id);        
        $comando->execute();
    }

    public function filtrarDados($nome, $cidade){
        $filtro = [ 
            !empty($nome)? 'Nome LIKE "%'.$nome.'%"' : '',
            !empty($cidade)? 'id_cidade_fk = '.$cidade : '',
        ];    
        $filtro = array_filter($filtro);//REMOVE ITEM VAZIO DO ARRAY
        $sql_filtro = (!empty($filtro) ? ' WHERE ' : '') . implode(' AND ', $filtro); 
        //var_dump($sql_filtro);
        $comando = $this->pdo->prepare("SELECT * FROM cliente INNER JOIN cidades ON cliente.id_cidade_fk = cidades.id_cidade {$sql_filtro} ORDER BY Nome");
        $comando->execute();
        $resultado = $comando->fetchAll(PDO::FETCH_ASSOC);
        
        return $resultado;
}
}
?>