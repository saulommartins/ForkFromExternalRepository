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
    * Classe de Regra de Almoxarifado
    * Data de Criação   : 28/10/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @package URBEM
    * @subpackage Regra

    $Revision: 12603 $
    $Name$
    $Autor: $
    $Date: 2006-07-13 14:21:32 -0300 (Qui, 13 Jul 2006) $

    * Casos de uso: uc-03.03.01
                    uc-03.03.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                       );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAlmoxarifado.class.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/CGM/classes/negocio/RCGM.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/CGM/classes/negocio/RCGMPessoaFisica.class.php';
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLocalizacaoFisicaItem.class.php"                );

/**
    * Classe de Regra de Almoxarifado
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott
*/
class RAlmoxarifadoAlmoxarifado
{
/**
    * @access Private
    * @var Object
*/
var $obTAlmoxarifadoAlmoxarifado;
/**
    * @access Private
    * @var Integer
*/
var $inCodigo;
/**
    * @access Private
    * @var Object
*/
var $obRCGMAlmoxarifado;
/**
    * @access Private
    * @var Object
*/
var $obRCGMResponsavel;

/**
    * @access Public
    * @return Integer
*/
function setCodigo($inCodigo) { $this->inCodigo = $inCodigo; }

/**
    * @access Public
    * @return Integer
*/
function getCodigo() { return $this->inCodigo; }

/**
     * Método construtor
     * @access Public
*/

function RAlmoxarifadoAlmoxarifado()
{
    $this->obTransacao  = new Transacao ;

    $this->obTAlmoxarifadoAlmoxarifado = new TAlmoxarifadoAlmoxarifado;

    $this->obRCGMAlmoxarifado   = new RCGM();
    $this->obRCGMResponsavel    = new RCGMPessoaFisica();
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    if ($this->inCodigo) {
        $stFiltro  = " and cod_almoxarifado= "  . $this->inCodigo;
    }

    $obErro = $this->obTAlmoxarifadoAlmoxarifado->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Incluir Almoxarifado
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function incluir($boTransacao = "")
{
    $boFlagTransacao            = false;
    $rsRecordSet                = new Recordset();

    $obErro                     = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stFiltro = " where cgm_almoxarifado  = ". $this->obRCGMAlmoxarifado->getNumCGM();
        $this->obTAlmoxarifadoAlmoxarifado->recuperaTodos($rsCGM, $stFiltro);
        if($rsCGM->getNumLinhas()>0)
          $obErro->setDescricao("Já existe um almoxarifado com esse número de CGM.");
    }

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTAlmoxarifadoAlmoxarifado->proximoCod( $this->inCodigo, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->obTAlmoxarifadoAlmoxarifado->setDado( "cod_almoxarifado"   , $this->inCodigo);
            $this->obTAlmoxarifadoAlmoxarifado->setDado( "cgm_responsavel"    , $this->obRCGMResponsavel->getNumCGM() );
            $this->obTAlmoxarifadoAlmoxarifado->setDado( "cgm_almoxarifado"   , $this->obRCGMAlmoxarifado->getNumCGM());
        }

        $obErro = $this->obTAlmoxarifadoAlmoxarifado->inclusao( $boTransacao );

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoAlmoxarifado );
    }

    return $obErro;
}

/**
    * Alterar Almoxarifado
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function alterar($boTransacao = "")
{
    include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAlmoxarifadoLocalizacao.class.php");
    $boFlagTransacao = false;
    $rsRecordSet                = new Recordset();
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $stFiltro = " where cgm_almoxarifado  = ". $this->obRCGMAlmoxarifado->getNumCGM();
        $stFiltro .= " and cod_almoxarifado != ". $this->getCodigo();
        $this->obTAlmoxarifadoAlmoxarifado->recuperaTodos($rsCGM, $stFiltro);
        if($rsCGM->getNumLinhas()>0)
           $obErro->setDescricao("Já existe um almoxarifado com esse número de CGM.");
    }

    if ( !$obErro->ocorreu() ) {
        $this->obTAlmoxarifadoAlmoxarifado->setDado( "cod_almoxarifado"  , $this->inCodigo );
        $this->obTAlmoxarifadoAlmoxarifado->setDado( "cgm_responsavel"   , $this->obRCGMResponsavel->getNumCGM() );
        $this->obTAlmoxarifadoAlmoxarifado->setDado( "cgm_almoxarifado"  , $this->obRCGMAlmoxarifado->getNumCGM() );

        $obErro = $this->obTAlmoxarifadoAlmoxarifado->alteracao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoAlmoxarifado );
    }

    return $obErro;
}

function consultar($boTransacao = "")
{
 include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAlmoxarifadoLocalizacao.class.php");
 $obTAlmoxarifado = new TAlmoxarifadoAlmoxarifado();
 $rsRecordSet     = new RecordSet;
 $obTAlmoxarifado->setDado ('cod_almoxarifado', $this->getCodigo() );
 $obErro = $obTAlmoxarifado->recuperaPorChave( $rsRecordSet, $boTransacao );
 if (!$obErro->ocorreu()) {
    $this->obRCGMAlmoxarifado->setNumCGM($rsRecordSet->getCampo('cgm_almoxarifado'));
    $this->obRCGMAlmoxarifado->consultar(new RecordSet());
 }

 return $obErro;
}

function consultarLocalizacao(&$rsRecordSet,$obTransacao = "")
{
  $obTAlmoxarifadoLocalizacao = new TAlmoxarifadoAlmoxarifadoLocalizacao();
  if ($this->getCodigo()) {
    $stFiltro = " And localizacao_fisica.cod_almoxarifado = ".$this->getCodigo();
  }
  $obErro  = $obTAlmoxarifadoLocalizacao->recuperaLocalizacao($rsRecordSet, $stFiltro, $stOrdem, $obTransacao);

  return $obErro;
}

/**
    * Exclui Almoxarifado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

 function excluir($boTransacao = "")
 {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if (!$obErro->ocorreu()) {

        if (!($obErro->ocorreu())) {

            $this->obTAlmoxarifadoAlmoxarifado->setDado( "cod_almoxarifado"  , $this->inCodigo );
            $this->obTAlmoxarifadoAlmoxarifado->setDado( "cgm_responsavel"   , $this->obRCGMResponsavel->getNumCGM() );
            $this->obTAlmoxarifadoAlmoxarifado->setDado( "cgm_almoxarifado"  , $this->obRCGMAlmoxarifado->getNumCGM() );

            $obErro = $this->obTAlmoxarifadoAlmoxarifado->exclusao( $boTransacao );

        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoAlmoxarifado );
    }

    return $obErro;
 }

 function verificaDadosAlmoxarifado()
 {
     $obTAlmoxarifadoLocalizacaoItem  = new TAlmoxarifadoLocalizacaoFisicaItem;
     $stFiltro = ' WHERE cod_almoxarifado='.$this->inCodigo;
     $obTAlmoxarifadoLocalizacaoItem->recuperaTodos($rsItensLocalizacao,$stFiltro);

     if ($rsItensLocalizacao->getNumlinhas() > 0) {
         return false;
     } else {
         return true;
     }
 }
}
