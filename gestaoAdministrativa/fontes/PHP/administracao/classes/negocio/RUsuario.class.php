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
    * Classe de negócio Usuario
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-01.03.93

    $Id: RUsuario.class.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RSetor.class.php"    );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"    );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php" );
/**
    * Classe de Regra de Negócio Usuario
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class RUsuario
{
/**#@+
    * @var Object
    * @access Private
*/
var $obTUsuario;
var $obRSetor;
var $obRCGM;
/**#@-*/
/**#@+
    * @var String
    * @access Private
*/
var $stUsername;
var $stPassword;
var $stStatus;
var $dtCadastro;
var $inCodOrgao;
/**#@-*/

/**#@+
     * @access Public
     * @param Object $valor
*/
function setTUsuario($valor) { $this->obTUsuario       = $valor; }
function setRSetor($valor) { $this->obRSetor         = $valor; }
function setRCGM($valor) { $this->obRCGM           = $valor; }
/**#@-*/
/**#@+
     * @access Public
     * @param String $valor
*/
function setUsername($valor) { $this->stUsername       = $valor; }
function setPassword($valor) { $this->stPassword       = sha1($valor); }
function setStatus($valor) { $this->stStatus         = $valor; }
function setCadastro($valor) { $this->dtCadastro       = $valor; }
function setCodOrgao($valor) { $this->inCodOrgao       = $valor; }
/**#@-*/

/**#@+
     * @access Public
     * @return Object
*/
function getTUsuario() { return $this->obTUsuario       ; }
function getRSetor() { return $this->obRSetor         ; }
function getRCGM() { return $this->obRCGM           ; }
/**#@-*/
/**#@+
     * @access Public
     * @return String
*/
function getUsername() { return $this->stUsername; }
function getPassword() { return $this->stPassword; }
function getStatus() { return $this->stStatus;   }
function getCadastro() { return $this->dtCadastro; }
function getCodOrgao() { return $this->inCodOrgao; }
/**#@-*/

/**
    * Método Construtor
    * @access Private
*/
function RUsuario()
{
    $this->setTUsuario  ( new TUsuario );
    $this->setRSetor    ( new RSetor   );
    $this->setRCGM      ( new RCGM     );
    $this->obRSetor->setExercicio( Sessao::getExercicio()   );
    $this->obRCGM->setNumCGM     ( Sessao::read('numCgm')      );
}
/**
    * Executa um recuperaTodos na classe Persistente Usuario
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "", $boTransacao = "")
{
    $obErro = $this->obTUsuario->recuperaTodos( $rsLista, '', $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Executa um recuperaPorChave na classe Persistente Usuario
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar(&$rsLista, $boTransacao = "")
{
    $stFiltro = "";

    if ($this->obRCGM->getNumCGM()) {
        $stFiltro .= " AND usuario.numcgm = ".$this->obRCGM->getNumCGM()." ";
    }

    $obErro = $this->obTUsuario->recuperaRelacionamento( $rsLista, $stFiltro, '', $boTransacao );

    if (!$obErro->ocorreu() and !$rsLista->eof()) {
        $this->setCodOrgao  ( $rsLista->getCampo('cod_orgao')   );
        $this->setUsername  ( $rsLista->getCampo('username')    );
        $this->setPassword  ( $rsLista->getCampo('password')    );
        $this->setStatus    ( $rsLista->getCampo('status')      );
        $this->setCadastro  ( $rsLista->getCampo('dt_cadastro') );

        $obErro = $this->obRCGM->consultar( $rsCGM , $boTransacao );
    }

    return $obErro;
}

/**
    * Método base para os métodos de lista
    * @access Private
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stFiltro Filtro especifico de cada método
    * @param  String $stOrdem Ordenação dos registros
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarGenerico(&$rsLista, $stFiltro = '', $stOrdem = '', $boTransacao = '')
{
    $obErro = $this->obTUsuario->recuperaTodos( $rsLista, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os usuários em função do username
    * @access Private
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPorUsername(&$rsLista, $boTransacao = "")
{
    $stFiltro = " WHERE UPPER( username ) = UPPER('".$this->getUsername()."') ";
    $stOrdem = " ORDER BY username ";
    $obErro = $this->listarGenerico( $rsLista, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarUsuarioCGM(&$rsRecordSet, $inCNPJ = '', $inCPF = '', $inRG = '', $boTransacao = '')
{
    if ($inCNPJ) {
        $stFiltro .= " AND PJ.cnpj = '".$inCNPJ."'";
    }
    if ($inCPF) {
        $stFiltro .= " AND PF.cpf = '".$inCPF."' ";
    }
    if ($inRG) {
        $stFiltro.= " AND PF.rg = '".$inRG."' ";
    }
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " AND CGM.numcgm = ".$this->obRCGM->getNumCGM()." ";
    }
    if ( $this->obRCGM->getNomCGM() ) {
        $stFiltro .= "AND lower(CGM.nom_cgm) like lower('";
        $stFiltro .= str_replace("'","\'",$this->obRCGM->getNomCGM())."')||'%' ";
    }

    if ( $this->getUsername() ) {
        $stFiltro .= " AND lower(U.username) like lower('";
        $stFiltro .= str_replace("'","\'",$this->getUsername())."')||'%' ";
    }
    $stOrdem = ' order by CGM.nom_cgm ';
    $obErro = $this->obRCGM->obTCGM->recuperaRelacionamentoSinteticoComUsuario( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function consultarUsuario(&$rsLista, $boTransacao = "")
{
    global $request;
    $stFiltro = "";

    if ($this->obRCGM->inNumCGM != '') {
        $stFiltro .= " usuario.numcgm = ".$this->obRCGM->getNumCGM()." AND ";
    }
    if ( $request->get('stUsername') != '' ) {
        $stFiltro .= " lower(usuario.username) like lower('";
        $stFiltro .= str_replace("'","\'",$_REQUEST['stUsername'])."')||'%' AND ";
    }
    if ($this->obRCGM->stNomCGM != '') {
        $stFiltro .= " lower(sw_cgm.nom_cgm) like lower('";
        $stFiltro .= str_replace("'","\'",$this->obRCGM->getNomCGM())."')||'%' AND ";
    }
    $stFiltro = ($stFiltro)? " AND ".substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = " ORDER BY sw_cgm.nom_cgm ";
    $obErro = $this->obTUsuario->recuperaUsuario( $rsLista, $stFiltro,$stOrder,$boTransacao );

    return $obErro;
}

/**
    * Verifica se o usuário já foi cadastrado
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificarCadastroUsuario($boTransacao = "")
{
    $stFiltro  = " WHERE \n";
    $stFiltro .= "     USERNAME = '".$this->getUsername()."' \n";
    $obErro = $this->obTUsuario->recuperaTodos( $rsUsuario, $stFiltro, '', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $rsUsuario->eof() ) {
            $obErro->setDescricao( 'Login ou Senha Incorretos!' );
        } else {
            $stSenhaEncriptada = crypt( $this->getPassword(), $rsUsuario->getCampo('password') );
            $stFiltro .= "     AND password = '".$stSenhaEncriptada."' \n";
            $obErro = $this->obTUsuario->recuperaTodos( $rsUsuario, $stFiltro, '', $boTransacao );
            if ( !$obErro->ocorreu() and $rsUsuario->eof() ) {
                $obErro->setDescricao( 'Login ou Senha Incorretos!' );
            } else {
                $this->obTUsuario->setDado( 'numcgm', $rsUsuario->getCampo('numcgm') );
            }
        }
    }

    return $obErro;
}

function incluirUsuario($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->listarPorUsername( $rsUsername, $boTransacao );

        if ( !$obErro->ocorreu() && $rsUsername->eof() ) {
            $obErro = $this->consultar( $rsValidaCGM, $boTransacao );

            if ( !$obErro->ocorreu() && $rsValidaCGM->eof() ) {
                $this->obTUsuario->setDado('numcgm'      , $this->obRCGM->getNumCGM() );
                $this->obTUsuario->setDado('cod_orgao'   , $this->getCodOrgao()       );
                $this->obTUsuario->setDado('dt_cadastro' , date('d/m/Y', time())      );
                $this->obTUsuario->setDado('username'    , $this->getUsername()       );
                $this->obTUsuario->setDado('status'      , $this->getStatus()         );
                $this->obTUsuario->setDado('password'    , crypt($this->getPassword(), $this->getUsername()) );

                $obErro = $this->obTUsuario->inclusao($boTransacao);
            } else {
                $obErro->setDescricao( "O usuário ".$this->getUsername()." já está cadastrado com o CGM informado!" );
            }
        } else {
            $obErro->setDescricao( "O usuário ".$this->getUsername()." já existe, por favor escolha outro username!" );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->getTUsuario() );

    $obErro->stDescricao = str_replace("role","usuário",$obErro->stDescricao);

    return $obErro;
}

function alterarUsuario($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stFiltro .= " AND usuario.numcgm = ".$this->obRCGM->getNumCGM()." ";
        $obErro = $this->obTUsuario->recuperaUsuario( $rsLista, $stFiltro,'',$boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTUsuario->setDado('numcgm'      , $this->obRCGM->getNumCGM()       );
            $this->obTUsuario->setDado('cod_orgao'   , $this->getCodOrgao()             );
            $this->obTUsuario->setDado('status'      , $this->getStatus()               );
            $this->obTUsuario->setDado('username'    , $this->getUsername()             );
            $this->obTUsuario->setDado('dt_cadastro' , $rsLista->getCampo('dt_cadastro'));
            $this->obTUsuario->setDado('password'    , $rsLista->getCampo('password')   );

            $obErro = $this->obTUsuario->alteracao( $boTransacao );
        }
    }

    # Caso for o usuário logado, atualiza a sessão com o novo cod_orgao escolhido.
    if (Sessao::read('numCgm') == $this->obRCGM->getNumCGM()) {
        Sessao::write('codOrgao', $this->getCodOrgao(), true);
    }

    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->getTUsuario() );

    return $obErro;
}

function alterarSenha($stSenhaAtual = '', $boTransacao = "")
{
    $obErro = $this->listarPorUsername( $rsLista, $boTransacao );

    if ( !$obErro->ocorreu() && !$rsLista->eof() ) {
        $obConexao = new Conexao();
        if ( Sessao::read('numCgm') != 0 ) {
            $stSenhaSessao = Sessao::getPassword();
            Sessao::setPassword($stSenhaAtual);
            $obErro = $obConexao->abreConexao();
            if ( $obErro->ocorreu() ) {
                $obErro->setDescricao( "Usuário ou senha inválidos!" );
            } else {
                $obConexao->fechaConexao();
            }
        }
        if ( !$obErro->ocorreu() ) {

            $stSQL = "UPDATE administracao.usuario set password = '". crypt($this->getPassword(), $this->getUsername())."' WHERE username = '".$this->getUsername()."'";
            $obErro = $obConexao->executaSQL($rsRecordSet, $stSQL);

            if ( !$obErro->ocorreu() && Sessao::read('numCgm') != 0 ) {
                Sessao::setPassword($this->getPassword());
            }
        }
    } else {
        $obErro->setDescricao( 'Usuário não cadastrado!' );
    }

    return $obErro;
}

function atualizarCadastroUsuario($boTransacao = "")
{
    $stUsernameSessao = Sessao::getUsername();
    $stPasswordSessao = Sessao::getPassword();
    Sessao::setUsername( 'urbem' );
    Sessao::setPassword( 'Pha0eive' );
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->verificarCadastroUsuario( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $stSQL = " ALTER USER \"sw.".$this->getUsername()."\" PASSWORD '".$this->getPassword()."' ";
            $obConexao = new Conexao;
            $obErro = $obConexao->executaDML( $stSQL, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obTUsuario->recuperaPorChave( $rsUsuario, $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTUsuario->setDado( 'cod_orgao'   , $rsUsuario->getCampo('cod_orgao')   );
                    $this->obTUsuario->setDado( 'dt_cadastro' , $rsUsuario->getCampo('dt_cadastro') );
                    $this->obTUsuario->setDado( 'username'    , $rsUsuario->getCampo('username')    );
                    $this->obTUsuario->setDado( 'password'    , ''                                  );

                    $obErro = $this->obTUsuario->alteracao( $boTransacao );
                }
            }
        }

        Sessao::setUsername( $stUsernameSessao );
        Sessao::setPassword( $stPasswordSessao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

}
