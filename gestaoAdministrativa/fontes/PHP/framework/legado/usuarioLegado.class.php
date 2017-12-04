<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

include_once 'cgmLegado.class.php'; //Insere a classe cgm que é usada como herança, a classe usuário é uma subclasse de cgm

class usuarioLegado extends cgmLegado
{
    //Variáveis da tabela usuario:
    public $numCgm, $codOrgao, $codUnidade, $codDepto, $codSetor, $setor, $dtCadastro, $username, $status;
    //Variáveis da Tabela cgm_pessoa_fisica:
    public $rg, $cpf;
    //Variáveis da tabela cad_geral_municipio:
    public $cgm, $digVerificador, $nomCgm, $endereco, $bairro, $cep;
    public $endCorresp, $bairroCorresp, $cepCorresp;
    public $foneResidencial, $foneComercial, $foneCelular, $email, $emailAdic;
    public $comboSetores;
    public $comboUnidades;
    public $comboDepartamentos;
    public $comboOrgaos;
    public $comboFuncoes;
/***************************************************************************
Método Construtor
/**************************************************************************/
    public function usuarioLegado()
    {
        $this->numCgm = "";
        $this->codOrgao = "";
        $this->codUnidade = "";
        $this->codDepto = "";
        $this->codSetor = "";
        $this->setor = "";
        $this->dtCadastro = "";
        $this->username = "";
        $this->status = "";
        $this->rg = "";
        $this->cpf = "";
        $this->cgm = "";
        $this->digVerificador = "";
        $this->nomCgm = "";
        $this->endereco = "";
        $this->bairro = "";
        $this->cep = "";
        $this->endCorresp = "";
        $this->bairroCorresp = "";
        $this->cepCorresp = "";
        $this->foneResidencial = "";
        $this->foneComercial = "";
        $this->foneCelular = "";
        $this->email = "";
        $this->comboSetores = "";
        $this->comboUnidades = "";
        $this->comboDepartamentos = "";
        $this->comboOrgaos = "";
        $this->comboFuncoes = "";
    }// Fim método construtor

/***************************************************************************
Inclui um novo usuário no sistema
Entra com o cgm, função, setor, username, senha e status
Insere estes dados na tabela usuario, em seguida atualiza a senha criptografada
/**************************************************************************/
    public function incluiUsuario($cgm,$codMasSetor,$usuario,$senha,$status,$exercicio)
    {
        $anoEx = pegaConfiguracao("ano_exercicio");
        //$this->pegaSetor($setor);
        $vet = preg_split( "/[^a-zA-Z0-9]/",$codMasSetor);
            $codOrgao = $vet[0];
            $codUnidade = $vet[1];
            $codDpto = $vet[2];
            $codSetor = $vet[3];
            $exercicio = $vet[4];
        $sSQL = "Insert into administracao.usuario (
                numcgm,cod_orgao,cod_unidade,cod_departamento,cod_setor,
                ano_exercicio,username,password,status)
                Values ('".$cgm."','".$codOrgao."',
                '".$codUnidade."','".$codDpto."',
                '".$codSetor."','".$exercicio."','".$usuario."','".$senha."','".$status."')";
        $conectaBD = new databaseLegado ;
        $conectaBD->abreBD();
        if ($conectaBD->executaSql($sSQL)) {
            $ok = true;
        } else {
            $ok = false;
        }
        $conectaBD->fechaBD();
        //Criptografa a senha do usuário
        $modSenha = new sessao;
        $modSenha->setaVariaveis($usuario,$senha,$exercicio);
        $modSenha->insereSenha();

        return $ok;
    }// Fim function incluiUsuario

/***************************************************************************
Entra com o cgm do usuário e um array contendo as impressoras que este
terá acesso
/**************************************************************************/
    public function incluiUsuarioImpressora($cgm,$impressoras,$padrao)
    {
        //Deleta os acessos atuais para depois inserir os novos
        $sSQL = "Delete From administracao.usuario_impressora
                Where numcgm = '$cgm' ";
        $conectaBD = new databaseLegado ;
        $conectaBD->abreBD();
        $conectaBD->executaSql($sSQL);
        $conectaBD->fechaBD();

        //Insere as impressoras a que o usuário terá acesso
        $sSQL = "";

            if (count($impressoras) > 0) {
                foreach ($impressoras as $value) {
                    if ($padrao == $value) {
                        $sSQL .= "Insert Into administracao.usuario_impressora values('".$cgm."','".$value."', 't'); ";
                    } else {
                        $sSQL .= "Insert Into administracao.usuario_impressora values('".$cgm."','".$value."', 'f'); ";
                    }
                }
            }

        if ($sSQL != "") {
            $conectaBD = new databaseLegado ;
            $conectaBD->abreBD();
            if ($conectaBD->executaSql($sSQL)) {
                $ok = true;
            } else {
                $ok = false;
            }
            $conectaBD->fechaBD();
        }

        return $ok;
    }// Fim function incluiUsuarioImpressora

/***************************************************************************
Entra com o cgm do usuário e uma impressora
Retorna true se o usuário tiver acesso à impressora
/**************************************************************************/
    //Verifica se o usuário tem acesso a uma impressora
    public function verificaUsuarioImpressora($cgm,$impressora)
    {
        $sSQL = "Select numcgm, cod_impressora, impressora_padrao
                From administracao.usuario_impressora
                Where numcgm = '$cgm'
                And cod_impressora = '$impressora' ";
        //echo "$sSQL"."<br>";
        $conectaBD = new databaseLegado ;
        $conectaBD->abreBD();
        $conectaBD->abreSelecao($sSQL);
        $conectaBD->vaiPrimeiro();
            if ($conectaBD->numeroDeLinhas > 0) { // O usuário tem acesso a impressora
                $ok = true;
            } else { // O usuário tem acesso a impressora
                $ok = false;
            }
        $conectaBD->limpaSelecao();
        $conectaBD->fechaBD();

        return $ok;
    }// Fim function verificaUsuarioImpressora

/***************************************************************************
Verifica se um cgm já tem um usuário cadastrado no sistema passando apenas
um parâmetro de consulta.
Entra com o campo a ser verificado o valor que o campo deve conter
/**************************************************************************/
    public function verificaUsuario($campo,$param)
    {
        $sSQL = "Select numcgm, username
                From administracao.usuario
                Where $campo = '$param' ";
        //echo "<br>$sSQL<br>";
        $conectaBD = new databaseLegado ;
        $conectaBD->abreBD();
        $conectaBD->abreSelecao($sSQL);
        $conectaBD->vaiPrimeiro();
            if ($conectaBD->numeroDeLinhas > 0) { // Já existe cadastro na tabela usuario
                $this->username = $conectaBD->pegaCampo("username");
                $ok = true;
            } else { // Não existe este usuário ou cgm na tabela usuario
                $ok = false;
            }
        $conectaBD->limpaSelecao();
        $conectaBD->fechaBD();

        return $ok;
    }// Fim function verificaUsuario

/***************************************************************************
Verifica se um username pertence ao cgm especificado
Entra com o cgm e o username
/**************************************************************************/
    public function verificaUsuarioCgm($cgm,$usuario)
    {
        $sSQL = "Select numcgm, username From administracao.usuario
                Where numcgm = '$cgm' And username = '$usuario' ";
        //echo "<br>$sSQL<br>";
        $conectaBD = new databaseLegado ;
        $conectaBD->abreBD();
        $conectaBD->abreSelecao($sSQL);
        $conectaBD->vaiPrimeiro();
            if ($conectaBD->numeroDeLinhas > 0) { // O usuário pertence ao cgm informado
                $ok = true;
            } else { // O usuário pertence a outro cgm informado
                $ok = false;
            }
        $conectaBD->limpaSelecao();
        $conectaBD->fechaBD();

        return $ok;
    }// Fim function verificaUsuarioCgm

/***************************************************************************
Verifica se o username já existe e se pertence a um cgm
/**************************************************************************/
    public function verificaUsername($cgm,$usuario)
    {
        if ($this->verificaUsuario("username",$usuario)) { //Verifica se o nome de usuário já existe
            if ($this->verificaUsuarioCgm($cgm,$usuario)) { //Verifica se o username que esta sendo cadastrado pertence a este cgm
                $ok = true;
            } else {
                $ok = false;
            }
        } else {
            $ok = true;
        }

        return $ok;
    }// Fim da function verificaUsername

/***************************************************************************
Atualiza os dados de um usuário já cadastrado
Entra com o cgm, função, setor, username, senha e status
Atualiza estes dados na tabela usuario, em seguida atualiza a senha criptografada
/**************************************************************************/
    public function atualizaUsuario($cgm,$codMasSetor,$usuario,$senha,$status,$exercicio)
    {
        $vet = preg_split( "/[^a-zA-Z0-9]/",$codMasSetor);
            $codOrgao   = $vet[0];
            $codUnidade = $vet[1];
            $codDpto    = $vet[2];
            $codSetor   = $vet[3];
        $sSQL = "Update administracao.usuario
                Set cod_setor='$codSetor',
                cod_departamento='$codDpto', cod_unidade='$codUnidade',
                cod_orgao='$codOrgao', ano_exercicio='$exercicio',
                username='$usuario', status='$status'
                Where numcgm = '$cgm' ";

        $conectaBD = new databaseLegado ;
        $conectaBD->abreBD();
        $conectaBD->executaSql($sSQL);
        $conectaBD->fechaBD();

        //Criptografa a senha do usuário
        $modSenha = new sessao;
        $modSenha->setaVariaveis($usuario,$senha,$exercicio);
        $ok=true;
        if ($senha) {                                                    // colocado o teste para caso
            if ($modSenha->insereSenha()) {            // a senha esteja vazia nao seja inserida no BD
                $ok = true;
            } else {
                $ok = false;
            }
        }

        return $ok;
    }// Fim function atualizaUsuario

/***************************************************************************
Altera a senha de um usuário
Entra com o username, a senha atual e a nova senha
Primeiro verifica se a senha atual está correta, em seguida altera-a
/**************************************************************************/
    public function alteraSenha($usuario,$senhaAtual,$senhaNova,$exercicio)
    {
        $modSenha = new sessao;
        $modSenha->setaVariaveis($usuario,$senhaAtual,$exercicio);
            //Faz a autenticacao para verificar a senha atual
            if ($modSenha->autenticaUsuario()) {
                $modSenha->setaVariaveis($usuario,$senhaNova,$exercicio);
                //Insere a nova senha
                if ($modSenha->insereSenha()) {
                    $ok = true;
                } else {
                    $ok = false;
                }
            } else {
                $ok = false;
            }

            return $ok;
    }// Fim function alteraSenha

/***************************************************************************
Altera a senha de um usuário pelo usuario siam
Entra com o username e nova senha
/**************************************************************************/
    public function alteraSenhaPeloSiam($usuario,$senha)
    {
        $dbConfig = new databaseLegado ;
        $dbConfig->abreBd();
        $senha = crypt($senha);   //Função que criptografa a senha
        $update = "update administracao.usuario set password = '$senha' where username = '$usuario'"; //insere a nova senha na base de dados
        if ($dbConfig->executaSql($update)) {
            $ok = true;
        } else {
            $ok = false;
        }
        $dbConfig->fechaBd();

        return $ok;
    }// Fim function alteraSenha

/***************************************************************************
Entra com o cgm e retorna todos os dados relativos ao usuário gravando
nas respectivas variáveis da classe
/**************************************************************************/
    public function pegaDadosUsuario($numCgm)
    {
        $this->pegaDadosCgm($numCgm);
        if ($this->verificaUsuario("numcgm",$numCgm)) {
            $sql = "
                    SELECT  U.numcgm
                         ,  orgao_descricao.cod_orgao
                         ,  U.username
                         ,  U.status

                            -- MANTER COMPATIBILIDADE
                         ,  orgao_descricao.descricao as nom_setor

                      FROM  administracao.usuario as U
                INNER JOIN  organograma.orgao_descricao
                        ON  orgao_descricao.cod_orgao = U.cod_orgao
                     WHERE  numcgm = '$numCgm'";
            //echo $sql;
            $conectaBD = new databaseLegado ;
            $conectaBD->abreBD();
            $conectaBD->abreSelecao($sql);
            $conectaBD->vaiPrimeiro();
                if (!$conectaBD->eof()) {
                    $this->vetCgm['username'] = $conectaBD->pegaCampo("username");
                    $this->vetCgm['codOrgao'] = $conectaBD->pegaCampo("cod_orgao");
                    $this->vetCgm['nomSetor'] = $conectaBD->pegaCampo("nom_setor");
                    #$this->vetCgm[setor]    = $conectaBD->pegaCampo("cod_orgao").".".$conectaBD->pegaCampo("cod_unidade").".".$conectaBD->pegaCampo("cod_departamento").".".$conectaBD->pegaCampo("cod_setor");
                    $this->vetCgm['setor']    = $conectaBD->pegaCampo("cod_orgao")." - ".$conectaBD->pegaCampo("nom_setor");
                    $this->vetCgm['status']   = $conectaBD->pegaCampo("status");
                    if ($conectaBD->pegaCampo("status") == 'A') {
                            $this->vetCgm['status'] = 'Ativo';
                        } elseif ($conectaBD->pegaCampo("status") == 'I') {
                            $this->vetCgm['status'] = 'Inativo';
                        } else {
                            $this->vetCgm['status'] = 'Desconhecido';
                        }
                }
            $conectaBD->limpaSelecao();
            $conectaBD->fechaBD();
        }

        return $this->vetCgm;
    }//Fim function pegaDadosUsuario

/***************************************************************************
Entra com o cgm e retorna todos os dados relativos ao usuário gravando
nas respectivas variáveis da classe
/**************************************************************************/
    public function pegaDados($cgm) {//Função desatualizada -- EXCLUIR
        $sSQL = "Select C.numcgm, C.nom_cgm,
                C.tipo_logradouro, C.logradouro, C.numero, C.complemento, C.bairro, C.cep,
                C.tipo_logradouro_corresp, C.logradouro_corresp, C.numero_corresp,
                C.complemento_corresp, C.bairro_corresp, C.cep_corresp,
                C.fone_residencial, C.fone_comercial, C.fone_celular,
                C.e_mail, C.e_mail_adcional, C.dt_cadastro, C.cod_responsavel,
                P.rg, P.cpf
                From sw_cgm As C, sw_cgm_pessoa_fisica As P
                where C.numcgm = P.numcgm
                And C.numcgm = '$cgm' ";
        $conectaBD = new databaseLegado ;
        $conectaBD->abreBD();
        $conectaBD->abreSelecao($sSQL);
        $conectaBD->vaiPrimeiro();
            if (!$conectaBD->eof()) {
                $this->numCgm = $conectaBD->pegaCampo("numcgm");
                $this->nomCgm = $conectaBD->pegaCampo("nom_cgm");
                $this->endereco = $conectaBD->pegaCampo("tipo_logradouro")." ".$conectaBD->pegaCampo("logradouro")." ".$conectaBD->pegaCampo("numero")." ".$conectaBD->pegaCampo("complemento");
                $this->bairro = $conectaBD->pegaCampo("bairro");
                $this->cep = $conectaBD->pegaCampo("cep");
                $this->endCorresp = $conectaBD->pegaCampo("tipo_logradouro_corresp")." ".$conectaBD->pegaCampo("logradouro_corresp")." ".$conectaBD->pegaCampo("numero_corresp")." ".$conectaBD->pegaCampo("complemento_corresp");
                $this->bairroCorresp = $conectaBD->pegaCampo("bairro_corresp");
                $this->cepCorresp = $conectaBD->pegaCampo("cep_corresp");
                $this->foneResidencial = $conectaBD->pegaCampo("fone_residencial");
                $this->foneComercial = $conectaBD->pegaCampo("fone_comercial");
                $this->foneCelular = $conectaBD->pegaCampo("fone_celular");
                $this->email = $conectaBD->pegaCampo("e_mail");
                $this->emailAdic = $conectaBD->pegaCampo("e_mail_adcional");
                $this->rg = $conectaBD->pegaCampo("rg");
                $this->cpf = $conectaBD->pegaCampo("cpf");
            }
        $conectaBD->limpaSelecao();
        $conectaBD->fechaBD();
        //Busca os dados dede usuário se este cgm já for usuário
        if ($this->verificaUsuario("numcgm",$this->numCgm)) {
            $sSQL = "Select U.numcgm, U.cod_setor,
                    U.username, U.status, S.nom_setor
                    From administracao.usuario as U, administracao.setor as S
                    Where numcgm = '$this->numCgm'
                    And U.cod_setor = S.cod_setor ";
            $conectaBD = new databaseLegado ;
            $conectaBD->abreBD();
            $conectaBD->abreSelecao($sSQL);
            $conectaBD->vaiPrimeiro();
                if (!$conectaBD->eof()) {
                    $this->username = $conectaBD->pegaCampo("username");
                    $this->codSetor = $conectaBD->pegaCampo("cod_setor");
                    $this->setor = $conectaBD->pegaCampo("nom_setor");
                        if ($conectaBD->pegaCampo("status") == 'A') {
                            $this->status = 'Ativo';
                        } elseif ($conectaBD->pegaCampo("status") == 'I') {
                            $this->status = 'Inativo';
                        } else {
                            $this->status = 'Desconhecido';
                        }
                }
            $conectaBD->limpaSelecao();
            $conectaBD->fechaBD();
        }
    }// Fim function pegaDados

/**************************************************************************/
/**** Gera o Combo com os setores para seleção                          ***/
/**************************************************************************/
    public function listaComboSetores()
    {
        $sSQL = "SELECT cod_setor, nom_setor FROM administracao.setor ORDER by nom_setor";
        $dbEmp = new databaseLegado ;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboSetores = "";
        $this->comboSetores .= "<select name=comboSet style='width: 200px;'>\n<option value=xxx SELECTED>Todos</option>\n";
        while (!$dbEmp->eof()) {
            $codSetor  = trim($dbEmp->pegaCampo("cod_setor"));
            $nomSetor  = trim($dbEmp->pegaCampo("nom_setor"));
            $dbEmp->vaiProximo();
            $this->comboSetores .= "<option value=".$codSetor.">".$nomSetor."</option>\n";
    }
        $this->comboSetores .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    }
/**************************************************************************/
/**** Mostra o na tela o Combo por módulos gerado                       ***/
/**************************************************************************/
    public function mostraComboSetores()
    {
        echo $this->comboSetores;
    }
/**************************************************************************/
/**** Gera o Combo com as unidades para seleção                         ***/
/**************************************************************************/
    public function listaComboUnidades()
    {
        $sSQL = "SELECT cod_unidade, nom_unidade FROM administracao.unidade ORDER by nom_unidade";
        $dbEmp = new databaseLegado ;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboUnidades = "";
        $this->comboUnidades .= "<select name=comboUni style='width: 200px;'>\n<option value=xxx SELECTED>Todas</option>\n";
        while (!$dbEmp->eof()) {
            $codUnidade  = trim($dbEmp->pegaCampo("cod_unidade"));
            $nomUnidade  = trim($dbEmp->pegaCampo("nom_unidade"));
            $dbEmp->vaiProximo();
            $this->comboUnidades .= "<option value=".$codUnidade.">".$nomUnidade."</option>\n";
    }
        $this->comboUnidades .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    }
/**************************************************************************/
/**** Mostra o na tela o Combo por unidades gerado                       ***/
/**************************************************************************/
    public function mostraComboUnidades()
    {
        echo $this->comboUnidades;
    }
/**************************************************************************/
/**** Gera o Combo com as departamentos para seleção                         ***/
/**************************************************************************/
    public function listaComboDepartamentos()
    {
        $sSQL = "SELECT cod_departamento, nom_departamento FROM administracao.departamento ORDER by nom_departamento";
        $dbEmp = new databaseLegado ;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboDepartamentos = "";
        $this->comboDepartamentos .= "<select name=comboDep style='width: 200px;'>\n<option value=xxx SELECTED>Todos</option>\n";
        while (!$dbEmp->eof()) {
            $codDepartamento  = trim($dbEmp->pegaCampo("cod_departamento"));
            $nomDepartamento  = trim($dbEmp->pegaCampo("nom_departamento"));
            $dbEmp->vaiProximo();
            $this->comboDepartamentos .= "<option value=".$codDepartamento.">".$nomDepartamento."</option>\n";
    }
        $this->comboDepartamentos .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    }
/**************************************************************************/
/**** Mostra o na tela o Combo por departamentos gerado                       ***/
/**************************************************************************/
    public function mostraComboDepartamentos()
    {
        echo $this->comboDepartamentos;
    }
/**************************************************************************/
/**** Gera o Combo com as orgaos para seleção                         ***/
/**************************************************************************/
    public function listaComboOrgaos()
    {
        $sSQL = "SELECT cod_orgao, nom_orgao FROM administracao.orgao ORDER by nom_orgao";
        $dbEmp = new databaseLegado ;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboOrgaos = "";
        $this->comboOrgaos .= "<select name=comboOrg style='width: 200px;'>\n<option value=xxx SELECTED>Todos</option>\n";
        while (!$dbEmp->eof()) {
            $codOrgao  = trim($dbEmp->pegaCampo("cod_orgao"));
            $nomOrgao  = trim($dbEmp->pegaCampo("nom_orgao"));
            $dbEmp->vaiProximo();
            $this->comboOrgaos .= "<option value=".$codOrgao.">".$nomOrgao."</option>\n";
    }
        $this->comboOrgaos .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    }
/**************************************************************************/
/**** Mostra o na tela o Combo por orgaos gerado                       ***/
/**************************************************************************/
    public function mostraComboOrgaos()
    {
        echo $this->comboOrgaos;
    }
/**************************************************************************/
/**** Gera o Combo com os Funções para seleção                          ***/
/**************************************************************************/
    public function listaComboFuncoes()
    {
        $sSQL = "SELECT cod_funcao, nom_funcao FROM sw_funcao ORDER by nom_funcao";
        $dbEmp = new databaseLegado ;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboModulo = "";
        $this->comboFun .= "<select name=comboFun style='width: 200px;'>\n<option value=xxx SELECTED>Todas</option>\n";
        while (!$dbEmp->eof()) {
            $codFuncao  = trim($dbEmp->pegaCampo("cod_funcao"));
            $nomFuncao  = trim($dbEmp->pegaCampo("nom_funcao"));
            $dbEmp->vaiProximo();
            $this->comboFun .= "<option value=".$codFuncao.">".$nomFuncao."</option>\n";
    }
        $this->comboFun .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    }
/**************************************************************************/
/**** Mostra o na tela o Combo por módulos gerado                       ***/
/**************************************************************************/
    public function mostraComboFuncoes()
    {
        echo $this->comboFun;
    }// fim da funcionalidade

} //Fim da classe usuario
?>
