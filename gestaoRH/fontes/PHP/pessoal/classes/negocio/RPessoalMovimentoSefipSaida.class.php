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
    * Classe de regra de negócio PessoalMovimentoSefipSaida
    * Data de Criação: 04/05/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso :uc-04.04.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalMovimentoSefip.class.php"                               );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalMovSefipSaida.class.php"                             );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalMovimentoSefipRetorno.class.php"                        );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCategoria.class.php"                                    );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCategoriaMovimento.class.php"                           );

class RPessoalMovimentoSefipSaida extends RPessoalSefip
{
/**
    * @access Private
    * @var Integer
*/
var $inCodMovimentoSefipSaida;

/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * @access Private
    * @var Object
*/
var $obTMovSefipSaida;

/**
    * @access Private
    * @var Object
*/
var $obRSefipRetorno;

/**
    * @access Private
    * @var Object

    referencia a ultima posição da array de categorias
*/
var $roCategoriaMovimento;

/**
    * @access Private
    * @var Object
    esta propriedade vai conter o array de categorias, todas as posições serão do tipo RPessoalCategoriaMovimento
*/

var $arCategoriaMovimento = array();

/**
    *@access Private
    *var Object
*/

var $roRCategoriaMovimento;

/**
    * @access Public
    * @param String $stINdicativo
*/

function addCategoria($stIndicativo, $inCodCategoria)
{
    // objeto da classe CategoriaMovimento

    $this->arCategoriaMovimento[] = new RPessoalCategoriaMovimento  ($this);

    $this->roCategoriaMovimento   = $this->arCategoriaMovimento  [count($this->arCategoriaMovimento )-1];
    $this->roCategoriaMovimento->setIndicativo  ( $stIndicativo );

    $this->roCategoriaMovimento->obRORPessoalCategoria->setCodCategoria($inCodCategoria);

}//function addCategoria()

/**
    * @access Public
    * @param Integer $valor
*/
function setCodMovimentoSefipSaida($valor) { $this->inCodMovimentoSefipSaida    = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setTMovSefipSaida($valor) { $this->obTPessoalMovSefipSaida = $valor  ; }

/**
    * @access Public
    * @param Object $Valor
*/
function setRSefipRetorno($valor) { $this->obRSefipRetorno = $valor  ; }

/**
    * @access Public
    * @return Object
*/
function getTMovSefipSaida() { return $this->obTPessoalMovSefipSaida;              }

/**
    * @access Public
    * @return Object
*/
function getRSefipRetorno() { return $this->obRSefipRetorno;      }

/**
     * Método construtor
     * @access Private
*/
function RPessoalMovimentoSefipSaida()
{
    parent::RPessoalSefip();
    $this->setTMovSefipSaida  ( new TPessoalMovSefipSaida         );
    $this->setRSefipRetorno   ( new RPessoalMovimentoSefipRetorno );
    $this->obTransacao         = new Transacao;
}

/**
    * Executa um recuperaLista na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarMovSefipSaida(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = '';

    if ( $this->getCodSefip()           ) { $stFiltro .= " and sefip.cod_sefip  =      " . $this->getCodSefip ()               ; }
    if ( $this->getDescricao()          ) { $stFiltro .= " and upper (sefip.descricao) like  upper('" .trim( $this->getDescricao())         . "%')"; }
    if ( $this->getNumSefip()           ) { $stFiltro .= " and upper (sefip.num_sefip) like  upper('" .trim( $this->getNumSefip() )         . "%')"; }
    if ( $this->getRepetirMensalmente() ) { $stFiltro .= " and sefip.repetir_mensal = '" . $this->getRepetirMensalmente(). "'" ; }

    if ( strtoupper(substr($stFiltro,0,5)) == ' AND ') {
        $stFiltro = ' WHERE '.substr($stFiltro,4);
    }

    $stOrder = ' sefip.descricao ';

    $obErro = $this->obTPessoalMovSefipSaida->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaLista na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarMovSefipSaidaSemRetorno(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
       $obErro = $this->obTPessoalMovSefipSaida->recuperaSefipSaidaSemRetorno ( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

       return $obErro;
}

/**
    * Executa inlclusão de Sefip de saida
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @return Object Objeto Erro
    * data: 13/02/2005
*/
function incluirMovimentoSefipSaida($boTransacao = '')
{
    $boFlagTransacao = false;
    $obErro = new Erro;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    /// validações     ( ver possibilidade de botar isso em uma função )
    // verificando se a sefip tem retorno
    if ( $this->obRSefipRetorno->getCodSefip() ) {
        //verificando se a sefip de retorno existe
        $obErro = $this->obRSefipRetorno->consultar($boTransacao);
    } elseif ( count( $this->arCategoriaMovimento ) == 0 ) {
        // se chegou aqui não tem retorno e tb não tem categorias
        $obErro->setDescricao ( 'Escolha uma sefip para retorno ou categorias para recolhimento do FGTS!' );
    }

    if ( !$obErro->ocorreu() ) {

        // incluindo na tabela pessoal.sefip
        $obErro = $this->incluirSefip ( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            //incluindo na tabela pessoal.mov_sefip_saida
            $this->obTPessoalMovSefipSaida->setDado("cod_sefip_saida", $this->getCodSefip() );
            $obErro = $this->obTPessoalMovSefipSaida->inclusao( $boTransacao );

            if ( !$obErro->ocorreu() ) {
                    if ( count( $this->arCategoriaMovimento ) != 0 ) {
                        // fazendo as ligações com a tabela de Categoria-Movimento
                        foreach ($this->arCategoriaMovimento  as $obCategoria) {
                             //$obCategoria->obRORPessoalMovimentoSefipSaida->setCodSefip($this->getCodSefip());
                             $obErro = $obCategoria->incluirCategoriaMovimento($boTransacao);
                             //$arTrans[] = $boTransacao;
                             if ( $obErro->ocorreu() ) { break; }
                        }
                    } else {
                        // fazendo a ligação com a tabela de retorno
                        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalMovSefipSaidaMovSefipRetorno.class.php" );
                        $obTMovSaidaMovRetorno = new TPessoalMovSefipSaidaMovSefipRetorno;
                        $obTMovSaidaMovRetorno->setDado  ( "cod_sefip_saida"   , $this->getCodSefip() );
                        $obTMovSaidaMovRetorno->setDado  ( "cod_sefip_retorno" , $this->obRSefipRetorno->getCodSefip() );
                        $obErro = $obTMovSaidaMovRetorno->inclusao ( $boTransacao );
                    }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalMovSefipSaida );

    return $obErro;
}

/***
    * Excuta exclusão de uma sefip da saida
    * @access public
    * @autor: Bruce Cruz de Sena
    @return ObErro
*/

function excluirMovimentoSefipSaida()
{
    include_once ( CAM_GRH_PES_MAPEAMENTO. 'TPessoalMovSefipSaida.class.php' );

    $obTSefipSaida = new TPessoalMovSefipSaida;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao ( $boFlagTransacao,  $boTransacao ) ;

    if ( !$obErro->ocorreu() ) {

        $this->listarMovSefipSaida($rsSefip);

        if ( !$obErro->ocorreu() ) {
              if ( $rsSefip->getCampo ('cod_sefip_retorno')) {

                    // excluindo ligação com a tabela mov_sefip_retorno
                    include_once ( CAM_GRH_PES_MAPEAMENTO . 'TPessoalMovSefipSaidaMovSefipRetorno.class.php' );
                    $obTMovSaidaMovRetorno = new TPessoalMovSefipSaidaMovSefipRetorno;
                    $obTMovSaidaMovRetorno->setDado  ( "cod_sefip_saida"   , $this->getCodSefip () );
                    $obErro = $obTMovSaidaMovRetorno->exclusao ( $boTransacao );

              } else {

                    // excluindo ligação com categorias  ( se a sefip não tem retorno eça é obrigada a ter ao menos uma categoria
                    $obErro = $this->excluirCategorias( $boTransacao );
              }
              if ( !$obErro->ocorreu() ) {

                  // exlcuindo a sefip de saida
                  $obTSefipSaida->setDado ('cod_sefip_saida', $this->getCodSefip () );
                  $obErro = $obTSefipSaida->exclusao( $boTransacao  );

              }

              if (!$obErro->ocorreu()) {
                     // chamando exclusão da SuperClasse
                     $obErro =$this->excluir($boTransacao );

              }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao,$boTransacao, $obErro, $obTPessoalMovimentoSefipRetorno );

    return $obErro;

} //function excluirMovimentoSefipSaida() {

/**
    * excluir todas as categorias da sefip atual
    @para obTransacao
    @ return obErro;
*/

function excluirCategorias($boTransacao = '')
{
     include_once ( CAM_GRH_PES_MAPEAMENTO . 'TPessoalCategoriaMovimento.class.php');

     $otCategorias = new TPessoalCategoriaMovimento;
     $obErro = $otCategorias->excluirPorMovimento  ( $this->getCodSefip(),  $boTransacao );

     return $obErro;

}// function excluirCategorias($boTransacao = '') {

/*****

    * alterar uma sefip de saida , esta estendendo o metodo super classe (RPessoalSefip)
    * acesso publico
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function alterarSefip($boTransacao = "")
{
    // iniciando transação
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao ( $boFlagTransacao,  $boTransacao ) ;

    $obErro = $this->listarMovSefipSaida($rsSefip, $boTransacao);

    if ( !$obErro->ocorreu() ) {
       if ( $rsSefip->getCampo ('cod_sefip_retorno')) {

           // excluindo ligação com a tabela mov_sefip_retorno';
           include_once ( CAM_GRH_PES_MAPEAMENTO . 'TPessoalMovSefipSaidaMovSefipRetorno.class.php' );
           $obTMovSaidaMovRetorno = new TPessoalMovSefipSaidaMovSefipRetorno;

           $obTMovSaidaMovRetorno->setDado  ( "cod_sefip_saida"   , $this->getCodSefip () );

           $obErro = $obTMovSaidaMovRetorno->exclusao ( $boTransacao );
       } else {
            $obErro = $this->excluirCategorias( $boTransacao );
       }
    }


    // verificando se a sefip tem retorno
    if ( $this->obRSefipRetorno->getCodSefip() ) {
        // fazendo a ligação com a tabela de retorno
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalMovSefipSaidaMovSefipRetorno.class.php" );
        $obTMovSaidaMovRetorno = new TPessoalMovSefipSaidaMovSefipRetorno;
        $obTMovSaidaMovRetorno->setDado  ( "cod_sefip_saida"   , $this->getCodSefip() );
        $obTMovSaidaMovRetorno->setDado  ( "cod_sefip_retorno" , $this->obRSefipRetorno->getCodSefip() );
        $obTMovSaidaMovRetorno->inclusao ( $boTransacao );
    } else {
          // fazendo as ligações com a tabela de Categoria-Movimento
          foreach ($this->arCategoriaMovimento  as $obCategoria) {
                $obErro = $obCategoria->incluirCategoriaMovimento($boTransacao);
                if ( $obErro->ocorreu() ) { break; }
          }
    }
    if ( !$obErro->ocorreu()) {
        // chamando método da super classe
        $obErro = parent::alterarSefip ( $boTransacao );
    }
    // fechando transação

    $this->obTransacao->fechaTransacao( $boFlagTransacao,$boTransacao, $obErro, $obTPessoalMovimentoSefipRetorno );

    return $obErro;

}//function alterarSefip($boTransacao = "") {


/****
        listar acategorias de movimento da sefip atual


*/

function listarCategoriaMovimento(&$rsRecordSet , $boTransacao = '')
{
    $stFiltro = '';

    if ( $this->getCodSefip()           ) { $stFiltro .= " where pmsc.cod_sefip_saida  =      " . $this->getCodSefip ()               ; }

    $obErro = $this->obTPessoalMovSefipSaida->recuperaSefipCategoria ( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;




}//function listarCategoriaMovimento(&$obLIsta , $boTransacao = '') {

}
