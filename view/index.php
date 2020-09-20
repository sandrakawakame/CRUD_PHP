<?php 
    include "cabecalho.php";
    include "footer.php";
    require_once "../model/classe-cliente.php";
    $c = new Cliente("desafio_crud", "host=localhost","root","");
?>

<body>
    <?php
    //PEGANDO O ID PASSADO VIA GET
    if(isset($_GET['id_excluir'])) //SE EXISTE ATRIBUTO ID
    {
        $id_cliente = addslashes($_GET['id_excluir']);
        $c->excluirCliente($id_cliente); //ENVIA O ID PARA A FUNÇÃO
    }

    if(isset($_GET['id_update']))//EDITAR
    {
        $id_up = addslashes($_GET['id_update']);
        $res = $c->buscarDadosCliente($id_up);

    }

    
    if(isset($_POST['filtrar']) && $_POST['filtrar'] == 'Filtrar'){//FILTRAR
        $nome = addslashes(($_POST['nome']));
        $cidade = addslashes(($_POST['cidade']));
        if(!empty($nome) || !empty($cidade)){
            $res = $c->filtrarDados($nome, $cidade);
            //var_dump($res);
            
        }
    }
?>

    <section id="form">
        <div class="container">
            <br /><br /><br />
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-default">

                        <div class="row">

                            <div class="col-sm-12 col-md-12"></div>
                            <div class="col-sm-12 col-md-12">

                                <?php
                                if(isset($_POST['nome']) && empty($_POST['filtrar']))
                                //VERIFICA SE CLICOU NO BOTÃO CADASTRAR OU EDITAR
                                {
                                    
                                    if(isset($_GET['id_update']) && !empty($_GET['id_update']))//EDITAR
                                    {
                                        $id_upd = addslashes($_GET['id_update']);
                                        $nome = addslashes(($_POST['nome']));
                                        $cidade = addslashes(($_POST['cidade']));
                                        if(!empty($nome) && !empty($cidade)){
                                            $c->atualizarDados($id_upd, $nome, $cidade);   
                                            header("location: index.php");
                                        }else{                                
                                            ?>
                                <script>
                                alert("O campo não pode ser vazio!")
                                </script>
                                <?php
                                        }
                                    }else //CADASTRAR
                                    {
                                        $nome_cadastro = addslashes($_POST['nome']);
                                        $cidade_cadastro = addslashes($_POST['cidade']);
                                        if(!empty($nome_cadastro) && !empty($cidade_cadastro)){
                                            if($c->cadastrarCliente($nome_cadastro, $cidade_cadastro)){
                                            ?>
                                <script>
                                alert("Cadastrado com sucesso!")
                                </script>
                                <?php
                                            }else{
                                            ?>
                                <script>
                                alert("Preencha todos os campos")
                                </script>
                                <?php
                                        }
                                    }
                                }
                            }

                            ?>
                                <form class="form-inline" method="POST">
                                    <div id="top" class="panel-heading">
                                        <h4><b> CADASTRO CLIENTES</b> </h4>
                                    </div>
                                    <br>
                                    <input id="nomeCliente" name="nome"
                                        value="<?php if(isset($res['Nome'])){echo $res['Nome'];}?>" type="text"
                                        class="form-control" placeholder="Nome">
                                    <select class="form-control show-tick" id="id_cidade" name="cidade"
                                        style="width: 25rem;">
                                        <option value="">Cidade</option>
                                        <?php
                                            $valor = $c->combobox($res['id_cidade_fk']);                                
                                        ?>
                                    </select>
                                    <input id="cadastrar" class="btn btn-info" type="submit"
                                        value="<?php if(isset($res)){echo "Atualizar";}else{echo "Cadastrar";}?>"
                                        style="width: 15rem;margin:10px;"></input>
                                    <input id="filtrar" name="filtrar" class="btn btn-primary" type="submit"
                                        value="Filtrar" style="width: 15rem;margin:10px"></input>
                                </form>
                            </div>
                        </div>
                        <br /><br />
                    </div>
                </div>
            </div>
        </div>
        <!-- #FIM# Filtros da lista -->
    </section>

    <section class="content" id="tabela">
        <div class="container">
            <br /><br /><br />
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel-body">
                        <table class="table" id="tabela">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" class="col-sm-2">Nome</th>
                                    <th scope="col" class="col-sm-2">Cidade</th>
                                    <th scope="col" class="col-sm-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                
                                $dados =  empty($_POST['filtrar']) ? $c->buscarDados() : $c->filtrarDados($nome, $cidade);
                                    if(count($dados)>0){ //se tem registro no BD
                                        for($i=0; $i < count($dados); $i++){
                                            echo "<tr>";
                                            foreach($dados[$i] as $key => $value){
                                                if($key != "id_cliente" && $key!= "id_cidade_fk" && $key != "id_cidade"){                                                   
                                                    echo "<td>". $value ."</td>";
                                                }
                                            }     
                                ?>
                                <td>
                                    <a id="editar"
                                        href="index.php?id_update=<?php echo $dados[$i]['id_cliente']?>">Editar</a>
                                    <a id="excluir"
                                        href="index.php?id_excluir=<?php echo $dados[$i]['id_cliente'];?>">Excluir</a>
                                    <!--PASSANDO O ID VIA GET-->
                                </td>
                                </tr>
                                <?php
                                        }                            
                                    }
                                    else{ //BD vazio
                                        ?>
                                <script>
                                alert("Não há registro no Banco de Dados")
                                </script>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>