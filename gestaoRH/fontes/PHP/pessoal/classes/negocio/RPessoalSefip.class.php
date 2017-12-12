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
    * Classe de regra de negócio Pessoal Sefip
    * Data de Criação: 03/02/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Regra

    Caso de uso: uc-04.04.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalSefip.class.php"         );

class RPessoalSefip
{
var $inCodSefip;
/**
    * @access Private
    * @var Integer
*/
var $stDescricao;
/**
    * @access Private
    * @var String
*/
var $stNumSefip;
/**
    * @access Private
    * @var String
*/
var $obTPessoalSefip;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * @access Private
    * @var Boolean
*/
var $boRepetirMensalmente;

/**
    * @access Public
    * @param valor Boolean
*/
function setRepetirMensalmente($valor) { $this->boRepetirMensalmente = $valor; }

/**
    * @access Public
    * @return boolean
*/
function getRepetirMensalmente() { return $this->boRepetirMensalmente; }

/**
    * @access Public
    * @param Object $valor
*/
function setCodSefip($valor) { $this->inCodSefip              = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setDescricao($valor) { $this->stDescricao        = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNumSefip($valor) { $this->stNumSefip        = strtoupper( $valor ); }
/**
    * @access Public
    * @param String $Valor
*/
function setTPessoalSefip($valor) { $this->obTPessoalSefip  = $valor; }
/**
    * @access Public
    * @return Object
*/
function getCodSefip() { return $this->inCodSefip            ; }
/**
    * @access Public
    * @return Integer
*/
function getDescricao() { return $this->stDescricao      ; }
/**
    * @access Public
    * @return String
*/
function getNumSefip() { return strtoupper( $this->stNumSefip)  ; }
/**
    * @access Public
    * @return String
*/
function getTPessoalSefip() { return $this->obTPessoalSefip   ; }
/**
     * Método construtor
     * @access Private
*/
function RPessoalSefip()
{
    $this->setTPessoalSefip ( new TPessoalSefip       );
    $this->obTransacao       = new Transacao;
}

/**
    * Salva dados da Sefip no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalSefip->setDado( "descricao",      $this->getDescricao()          );
        $this->obTPessoalSefip->setDado( "num_sefip",      $this->getNumSefip()           );
        $this->obTPessoalSefip->setDado( "repetir_mensal", $this->getRepetirMensalmente() );

        if ( $this->getCodSefip() ) {
            $this->obTPessoalSefip->setDado("cod_sefip", $this->getCodSefip() );
            $obErro = $this->obTPessoalSefip->alteracao( $boTransacao );
        } else {
            $this->obTPessoalSefip->proximoCod( $inCodSefip , $boTransacao );
            $this->setCodSefip( $inCodSefip );
            $this->obTPessoalSefip->setDado("cod_sefip",$this->getCodSefip() );
            $obErro = $this->obTPessoalSefip->inclusao( $boTransacao );

        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,$this->obTPessoalSefip );

    return $obErro;
}

/***
    * inlcui a Sefip no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function incluirSefip($boTransacao)
{
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        // procurando sefip pelo campo num_sefip por este não pode se repetir
        $obRsSefip = new RecordSet;
        $stFiltro = " where  upper(trim(sefip.num_sefip)) = upper (trim('".$this->getNumSefip()."'))";

        $obErro =  $this->obTPessoalSefip->recuperaTodos ( $obRsSefip, $stFiltro,$stOrder = '',$boTransacao );
        if ( !$obErro->ocorreu()) { //verificar porque o aviso de repetição não aparece
           if ($obRsSefip->getNumLinhas() > 0 ) {
                $obErro->setDescricao ( "O Código Sefip ".$this->getNumSefip() ." já foi cadastrado" );
           } else {
                $obErro = $this->salvar( $boTransacao );
           }
        }
    }

    return $obErro;
}// function incluiSefip($boTransacao = "") {

/*****

    * alterar uma sefip , usando a função salvar
    * acesso publico
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function alterarSefip($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    // verifincando se existe o registro que se quer alterar, só por precaussão
    $obRsSefip = new RecordSet;
    $stFiltro =  " where  trim(upper(sefip.num_sefip)) = trim(upper('".$this->getNumSefip()."'))" ;

    $obErro =  $this->obTPessoalSefip->recuperaTodos ( $obRsSefip, $stFiltro, '' , $boTransacao );
    if ( $obRsSefip->getNumLinhas() > 0 ) {
       $obErro = $this->salvar( $boTransacao );
    } else {
        $obErro->setDescricao( 'o código de sefip digitado não existe');
    }

    return $obErro;

}//function alterarSefip($boTransacao = "") {

/**
    * Exclui dados da Sefip do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalSefip->setDado("cod_sefip", $this->getCodSefip() );
        $obErro = $this->obTPessoalSefip->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalSefip );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = " descricao ", $boTransacao = "")
{
    if( $this->getDescricao() )
        $stFiltro .= " AND upper(descricao) like upper('%".$this->getDescricao()."%') ";

    if( $this->getNumSefip() )
        $stFiltro .= " AND trim(upper(num_sefip)) = trim(upper('".$this->getNumSefip()."'))";

    // retirando o AND do inicio do filtro se ouver um
    $stFiltro = trim ($stFiltro);

    if ( strtoupper(substr($stFiltro,0,4)) == "AND ") {
        $stFiltro = ' WHERE '.substr($stFiltro,4);
    }

    $obErro = $this->obTPessoalSefip->recuperaTodos($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

    /**
        executa um busca usando o método listar e se achar um registro prenche as propriedades da classe com seus respectivos campos
        a busca é feita por código (chave primária) e portando recupera apenas um registro

    */
    public function consultar()
    {
        if ( $this->getCodSefip() ) {
            $rsDados = new RecordSet;
            $obErro = $this->litar($rsDados);
            if ( !$obErro->ocorreu() ) {
                if ( $rsDados->getNumLinhas() == 1 ) {
                   $this->setDescricao          ( $rsDados->getCampo( 'descricao'          ));
                   $this->setNumSefip           ( $rsDados->getCampo( 'num_sefip'          ));
                   $this->setRepetirMensalmente ( $rsDados->getCampo( 'RepetirMensalmente' ));

                }
            }

        }

    }// function consultar

/**
    * exclui uma sefip
    * @access Public
    * @parameter Objeto transação
    * @return Object Objeto Erro

Function excluirSefip( $boTransacao = "") {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalSefip->setDado ('cod_sefip', $this->getCodSefip());
        $obErro = $this->obTPessoalSefip->exclusao( $boTransacao );
    }

    return $obErro;
}

*/

} // fim da class RPessoalSefip;
