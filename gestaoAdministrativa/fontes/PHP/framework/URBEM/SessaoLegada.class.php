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

class SessaoLegada
{
    /*** Declaração de variaveis ***/
    public $id;
    public $username;
    public $password;
    public $numCgm;
    public $nomCgm;
    public $nomSetor;
    public $codSetor;
    public $codDpto;
    public $codUnidade;
    public $codOrgao;
    public $anoExercicio;//SETOR DO USUARIO
    public $acao;
    public $complementoacao;
    public $transf;
    public $transf2;
    public $transf3;
    public $transf4;
    public $transf5;
    public $transf6;
    public $assinaturas;
    public $relatorio;
    public $exercicio;//LOGIN
    public $raiz;
    public $acaoMaisAcessos;
    public $mensagens;
    public $filtro;
    public $link;
    public $modulo;

    /*** Método construtor ***/
    //function sessao() {
    public function SessaoLegada()
    {
        $this->username = "";
        $this->password = "";
        $this->numCgm = "";
        $this->nomCgm = "";
        $this->nomSetor = "";
        $this->codSetor = "";
        $this->codDpto = "";
        $this->codUnidade = "";
        $this->codOrgao = "";
        $this->anoExercicio = "";
        $this->acao = "";
        $this->transf = "";
        $this->transf2 = "";
        $this->transf3 = "";
        $this->transf4 = "";
        $this->transf5 = "";
        $this->transf6 = "";
        $this->assinaturas = array('disponiveis'=>array(), 'selecionadas'=>array(), 'papeis'=>array());
        $this->relatorio = "";
        $this->exercicio = "";
        $this->raiz = "";
        $this->acaoMaisAcessos = "";
        $this->filtro = "";
        $this->link = "";
    }

    /*** Método que realiza a autenticação de usuários ***/
    public function autenticaUsuario()
    {
        $dbConfig = new dataBase;
        $dbConfig->abreBd();
        $select =   "select parametro,valor
                        from administracao.configuracao WHERE parametro = 'status'";
        $dbConfig->abreSelecao($select);
        $status = $dbConfig->pegaCampo("valor");
        $dbConfig->limpaSelecao();

        //if (($status == 'I') && ($this->username != 'admin')) {
        if (($status == 'I') && ($this->numCgm != 0 )) {
            $dbConfig->fechaBd();

            return false;
        } else {
            $select =   "select password
            from administracao.usuario
            where username = '$this->username'";    //Seleciona a senha do usuário para conferência
            $dbConfig->abreSelecao($select);
            if (!$dbConfig->eof()) {
                $salt = $dbConfig->pegaCampo("password");           //Gera o $salt
                $this->password = crypt($this->password, $salt);    //Criptografa a Senha para Conferência
                $select =   "select numcgm
                from administracao.usuario
                where username = '$this->username'
                and password = '$this->password'
                and status = 'A' or numcgm = 0";                  //Faz a conferência dos Valores
                $dbConfig->limpaSelecao();
                $dbConfig->abreSelecao($select);
                $dbConfig->fechaBd();
                if (!$dbConfig->eof()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $dbConfig->fechaBd();

                return false;
            }
        }
    }

    /*** Método de Inserção de Senha ***/
    public function insereSenha()
    {
        $dbConfig = new dataBase;
        $dbConfig->abreBd();
        $this->password = crypt($this->password);   //Função que criptografa a senha
        $update = "update administracao.usuario set password = '$this->password' where username = '$this->username'"; //insere a nova senha na base de dados
        if ($dbConfig->executaSql($update)) {
            $ok = true;
        } else {
            $ok = false;
        }
        $dbConfig->fechaBd();

        return $ok;
    }

    /*** Método de Atualização de Senha ***/
    public function atualizaSenha()
    {
        $this->autenticaUsuario();
        $this->insereSenha();
    }

    /*** Método Seta Variáveis ***/
    public function setaVariaveis($usuario, $senha, $exercicio)
    {
        $this->username = $usuario;
        $this->password = $senha;
        $this->exercicio = $exercicio;
    }

    /*** Método de Inicialização de Sessão ***/
    public function inicializaSessao()
    {
        $dbConfig = new dataBase;
        $dbConfig->abreBd();
        $select =   "select u.numcgm, c.nom_cgm, s.nom_setor, u.cod_setor,
        u.cod_departamento, u.cod_unidade, u.cod_orgao, s.ano_exercicio
        from administracao.usuario as u, sw_cgm as c, administracao.setor as s
        where u.username = '$this->username'
        and u.numcgm = c.numcgm
        and u.cod_setor = s.cod_setor
        and u.cod_departamento = s.cod_departamento
        and u.cod_unidade = s.cod_unidade
        and u.cod_orgao = s.cod_orgao "; //Faz o select dos dados necessários para a sessão
        $dbConfig->abreSelecao($select);
        if (!$dbConfig->eof()) {
            $this->numCgm = $dbConfig->pegaCampo("numcgm");
            $this->nomCgm = $dbConfig->pegaCampo("nom_cgm");
            $this->nomSetor = $dbConfig->pegaCampo("nom_setor");
            $this->codSetor = $dbConfig->pegaCampo("cod_setor");
            $this->codDpto = $dbConfig->pegaCampo("cod_departamento");
            $this->codUnidade = $dbConfig->pegaCampo("cod_unidade");
            $this->codOrgao = $dbConfig->pegaCampo("cod_orgao");
            $this->anoExercicio = $dbConfig->pegaCampo("ano_exercicio");
            $dbConfig->limpaSelecao();

            return true;
        } else

        return false;
        $dbConfig->fechaBd();
    }

    /*** Método de Validação da Sessão ***/
    public function validaSessao()
    {
        //session_start(); //inicializa a sessão
        $this->id = "PHPSESSID=".session_id();

        if(isset($_SESSION['sessao']))
        return true;
        else
        return false;
    }

    /*** Método de Destruição de Sessão ***/
    public function destroiSessao()
    {
        unset($_SESSION['sessao']); //Destrói as variáveis da sessão
        session_destroy(); //Destrói a sessão
    }

    /*** Método de Geração do número randômico - Jorge***/
    public static function geraURLRandomica()
    {
        $aux = explode("&",Sessao::getId());
        $sAux = $aux[0];
        list($sMilisec, $sSec) = explode(" ",microtime());
        $sMilisec = substr($sMilisec,1,4);
        if (isset($bSemPontos)) {
            $sMilisec = substr($sMilisec,1);
        }
        $stAgora = date("His",time()).$sMilisec;
        $sRnd = "&iURLRandomica=".date("Y-m-d",time()).$stAgora;
        $sAux = $sAux.preg_replace ("/-/", "", $sRnd);
        //$this->id = $sAux;
        Sessao::setId($sAux);
    }
}
?>
