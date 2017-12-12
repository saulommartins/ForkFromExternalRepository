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
    * Classe de regra de negócio para Pessoal  Caso Causa
    * Data de Criação: 05/05/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso :uc-04.04.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCasoCausa.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCasoCausaSubDivisao.class.php");
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalGrupoPeriodo.class.php");
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalPeriodoCaso.class.php");
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalSubDivisao.class.php"     );
include_once ( CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php"     );

class RPessoalCasoCausa
{
/**
    * @access Private
    * @var integer
*/
var $inCodCasoCausa;

/**
    * @access Private
    * @var String
*/
var $stDescricao;

/**
    * @access Private
    * @var boolean;
*/
var $boPagaAvidoPrevio;

/**
    * @access Private
    * @var boolean;
*/
var $boPagaFeriasVencida;

/**
    * @access Private
    * @var String
*/
var $stCodigoSaqueFGTS;

/**
    * @access Private
    * @var float;
*/

var $flMultaFGTS;

/**
    * @access Private
    * @var float
*/
var $flPercContSocial;

/**
    * @access Private
    * @var boolean;
*/
var $boIncFGTSFerias;

/**
    * @access Private
    * @var boolean;
*/
var $boIncFGTSAvisoPrevio;

/**
    * @access Private
    * @var boolean;
*/
var $boIncFGTS13;

/**
    * @access Private
    * @var boolean;
*/
var $boIncIRRFFerias;

/**
    * @access Private
    * @var boolean;
*/
var $boIncIRRFAvisoPrevio;

/**
    * @access Private
    * @var boolean;
*/
var $boIncIRRF13;

/**
    * @access Private
    * @var boolean;
*/
var $boIncPrevFerias;

/**
    * @access Private
    * @var boolean;
*/
var $boIncPrevAvisoPrevio;

/**
    * @access Private
    * @var boolean;
*/
var $boIncPrev13;

/**
    * @access Private
    * @var boolean;
*/
var $boPagaFeriasProporcional;

/**
    * @access Private
    * @var integer;
*/
var $inCodPeriodo;

/**
    * @access Private
    * @var boolean;
*/
var $boIndArt479;

/**
    * @access Private
    * @var Object
*/
var $obTPessoalCasoCausa;
/**
    * @access Private
    * @var Object
*/
var $obTPessoalPeriodoCaso;
/**
    * @access Private
    * @var Object
*/
var $obTPessoalGrupoPeriodo;

/**
    * @access Private
    * @var object
*/
var $roPessoalCausaRescisao;

/**
    * @access Private
    * @var Object
*/
var $obTPessoalCasoCausaSubDivisao;

/**
    * @access Private
    * @var Array de Objetos
*/
var $arRPessoalSubDivisao;

/**
    * @access Private
    * @var Array de Objetos
*/
var $roUltimoPessoalSubDivisao;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodCasoCausa($valor) { $this->inCodCasoCausa = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setPagaAvisoPrevio($valor) { $this->boPagaAvidoPrevio = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setPagaFeriasVencida($valor) { $this->boPagaFeriasVencida = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setPagaFeriasProporcional($valor) { $this->boPagaFeriasProporcional = $valor; }

/**
    * @access Public
    * @param string  $valor
*/
function setCodSaqueFGTS($valor) { $this->stCodigoSaqueFGTS = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setMultaFGTS($valor) { $this->flMultaFGTS = $valor; }

/**
    * @access Public
    * @param float  $valor
*/
function setPercContSocial($valor) { $this->flPercContSocial = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setIncFGTSFerias($valor) { $this->boIncFGTSFerias = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setIncFGTSAvisoPrevio($valor) { $this->boIncFGTSAvisoPrevio = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setIncFGTS13($valor) { $this->boIncFGTS13 = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setIncIRRFFerias($valor) { $this->boIncIRRFFerias = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setIncIRRFAvisoPrevio($valor) { $this->boIncIRRFAvisoPrevio = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setIncIRRF13($valor) { $this->boIncIRRF13 = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setIncPrevFerias($valor) { $this->boIncPrevFerias = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setIncPrevAvisoPrevio($valor) { $this->boIncPrevAvisoPrevio = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setIncPrev13($valor) { $this->boIncPrev13 = $valor; }

/**
    * @access Public
    * @param integer  $valor
*/
function setCodPeriodo($valor) { $this->inCodPeriodo = $valor; }

/**
    * @access Public
    * @param boolean  $valor
*/
function setIndArt479($valor) { $this->boIndArt479 = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalCasoCausa($valor) { $this->obTPessoalCasoCausa      = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalPeriodoCaso($valor) { $this->obTPessoalPeriodoCaso      = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalGrupoPeriodo($valor) { $this->obTPessoalGrupoPeriodo      = $valor  ;}

/**
    * @access Public
    * @param Object $Valor
*/

function setTPessoalCasoCausaSubDivisao($valor) { $this->obTPessoalCasoCausaSubDivisao       = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRPessoalSubDivisao($valor) { $this->obRPessoalSubDivisao       = $valor  ; }

/**
    * @access Public
    * @return Integer
*/
function getCodCasoCausa() { return $this->inCodCasoCausa; }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;   }

/**
    * @access Public
    * @return boolean
*/
function getPagaAvisoPrevio() { return $this->boPagaAvidoPrevio; }

/**
    * @access Public
    * @return boolean
*/
function getPagaFeriasVencida() { return $this->boPagaFeriasVencida; }

/**
    * @access Public
    * @return boolean
*/
function getPagaFeriasProporcional() { return $this->boPagaFeriasProporcional; }

/**
    * @access Public
    * @return string
*/
function getCodSaqueFGTS() { return $this->stCodigoSaqueFGTS; }

/**
    * @access Public
    * @return boolean
*/
function getMultaFGTS() { return $this->flMultaFGTS; }

/**
    * @access Public
    * @return float
*/
function getPercContSocial() { return $this->flPercContSocial ; }

/**
    * @access Public
    * @return boolean
*/
function getIncFGTSFerias() { return $this->boIncFGTSFerias ; }

/**
    * @access Public
    * @return boolean
*/
function getIncFGTSAvisoPrevio() {return  $this->boIncFGTSAvisoPrevio; }

/**
    * @access Public
    * @return boolean
*/
function getIncFGTS13() { return $this->boIncFGTS13; }

/**
    * @access Public
    * @return boolean
*/
function getIncIRRFFerias() {return  $this->boIncIRRFFerias ; }

/**
    * @access Public
    * @return boolean
*/
function getIncIRRFAvisoPrevio() { return $this->boIncIRRFAvisoPrevio; }

/**
    * @access Public
    * @return boolean
*/
function getIncIRRF13() { return $this->boIncIRRF13 ; }

/**
    * @access Public
    * @return boolean
*/
function getIncPrevFerias() {return  $this->boIncPrevFerias; }

/**
    * @access Public
    * @return boolean
*/
function getIncPrevAvisoPrevio() { return $this->boIncPrevAvisoPrevio; }

/**
    * @access Public
    * @return boolean
*/
function getIncPrev13() { return $this->boIncPrev13; }

/**
    * @access Public
    * @return integer
*/
function getCodPeriodo() { return $this->inCodPeriodo ; }

/**
    * @access Public
    * @return boolean
*/
function getIndArt479() { return $this->boIndArt479; }

/**
    * @access Public
    * @return Object
*/
function getTPessoalCasoCausa() { return $this->obTPessoalCasoCausa      ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalPeriodoCaso() { return $this->obTPessoalPeriodoCaso      ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalGrupoPeriodo() { return $this->obTPessoalGrupoPeriodo      ; }

/**
    * @access Public
    * @return Object
*/
function getTPessoalCasoCausaSubDivisao() { return $this->obTPessoalCasoCausaSubDivisao   ; }

/**
    * @access Public
    * @return Object
*/
function getRPessoalSubDivisao() { return $this->obRPessoalSubDivisao            ; }

/**
     * Método construtor
     * @access Private
*/
function RPessoalCasoCausa(&$roPessoalCausaRescisao)
{
    $this->setTPessoalCasoCausa    ( new TPessoalCasoCausa    );
    $this->setTPessoalCasoCausaSubDivisao      (new TPessoalCasoCausaSubDivisao);
    $this->setTPessoalPeriodoCaso      (new TPessoalPeriodoCaso);
    $this->setTPessoalGrupoPeriodo      (new TPessoalGrupoPeriodo);
    $this->obRConfiguracaoPessoal = new RConfiguracaoPessoal;
    $this->obTransacao              = new Transacao;
    $this->roPessoalCausaRescisao   = &$roPessoalCausaRescisao;
    $this->arRPessoalSubDivisao          = array();
}

/**
* Adiciona um array de referencia-objeto
* @access Public
*/
function addPessoalSubDivisao()
{
   $this->arRPessoalSubDivisao[]      =  new RPessoalSubDivisao($this);
   $this->roUltimoPessoalSubDivisao   = &$this->arRPessoalSubDivisao[ count($this->arRPessoalSubDivisao) - 1 ];
}

/**
    * Inclui os dados da Sub-Divisão
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirCaso($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $obErro = $this->obTPessoalCasoCausa->proximoCod ( $this->inCodCasoCausa, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalCasoCausa->setDado  ( "cod_caso_causa"                  , $this->inCodCasoCausa );
        $this->obTPessoalCasoCausa->setDado  ( "cod_causa_rescisao"              , $this->roPessoalCausaRescisao->getCodCausaRescisao());
        $this->obTPessoalCasoCausa->setDado  ( "descricao"                       , $this->stDescricao        );
        $this->obTPessoalCasoCausa->setDado  ( "paga_aviso_previo"               , $this->boPagaAvidoPrevio  );
        $this->obTPessoalCasoCausa->setDado  ( "paga_ferias_vencida"             , $this->boPagaFeriasVencida );
        $this->obTPessoalCasoCausa->setDado  ( "cod_saque_fgts"                  , $this->stCodigoSaqueFGTS );
        $this->obTPessoalCasoCausa->setDado  ( "multa_fgts"                      , $this->flMultaFGTS );
        $this->obTPessoalCasoCausa->setDado  ( "perc_cont_social"                , $this->flPercContSocial );
        $this->obTPessoalCasoCausa->setDado  ( "inc_fgts_ferias"                 , $this->boIncFGTSFerias );
        $this->obTPessoalCasoCausa->setDado  ( "inc_fgts_aviso_previo"           , $this->boIncFGTSAvisoPrevio );
        $this->obTPessoalCasoCausa->setDado  ( "inc_fgts_13"                     , $this->boIncFGTS13 );

        $this->obTPessoalCasoCausa->setDado  ( "inc_irrf_ferias"                 , $this->boIncIRRFFerias );
        $this->obTPessoalCasoCausa->setDado  ( "inc_irrf_aviso_previo"           , $this->boIncIRRFAvisoPrevio );
        $this->obTPessoalCasoCausa->setDado  ( "inc_irrf_13"                     , $this->boIncIRRF13 );

        $this->obTPessoalCasoCausa->setDado  ( "inc_prev_ferias"                 , $this->boIncPrevFerias );
        $this->obTPessoalCasoCausa->setDado  ( "inc_prev_aviso_previo"           , $this->boIncPrevAvisoPrevio );
        $this->obTPessoalCasoCausa->setDado  ( "inc_prev_13"                     , $this->boIncPrev13 );

        $this->obTPessoalCasoCausa->setDado  ( "paga_ferias_proporcional"        , $this->boPagaFeriasProporcional );

        $this->obTPessoalCasoCausa->setDado  ( "cod_periodo"                      , $this->inCodPeriodo );
        $this->obTPessoalCasoCausa->setDado  ( "inden_art_479"                   , $this->boIndArt479 );

        $obErro = $this->obTPessoalCasoCausa->inclusao ( $boTransacao );

        }

    if (!$obErro->ocorreu()) {
       foreach ($this->arRPessoalSubDivisao as $obRPessoalSubDivisao) {
          $this->obTPessoalCasoCausaSubDivisao->setDado('cod_caso_causa'    ,$this->getCodCasoCausa());
          $this->obTPessoalCasoCausaSubDivisao->setDado('cod_sub_divisao'   ,$obRPessoalSubDivisao->getCodSubDivisao());
          $obErro = $this->obTPessoalCasoCausaSubDivisao->inclusao($boTransacao);
      }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCasoCausa );

    return $obErro;
}

/**
    * Exclui os dados do Caso Causa
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirCaso($boTransacao = "")
{
   $boFlagTransacao = false;
   $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
   if (!$obErro->ocorreu()) {
       $this->obTPessoalCasoCausa->setDado ( "cod_caso_causa"    , $this->inCodCasoCausa );
       $tmpComplementoChave =  $this->obTPessoalCasoCausaSubDivisao->getComplementoChave();
       $tmpComplementoCod   =  $this->obTPessoalCasoCausaSubDivisao->getCampoCod();
       $this->obTPessoalCasoCausaSubDivisao->setComplementoChave('');
       $this->obTPessoalCasoCausaSubDivisao->setCampoCod('cod_caso_causa');
       $this->obTPessoalCasoCausaSubDivisao->setDado('cod_caso_causa',$this->getCodCasoCausa());
       $obErro = $this->obTPessoalCasoCausaSubDivisao->exclusao($boTransacao);
       if ($obErro->ocorreu) {
          break;
       }
   }
   $this->obTPessoalCasoCausaSubDivisao->setComplementoChave($tmpComplementoCampo);
   $this->obTPessoalCasoCausaSubDivisao->setCampoCod($tmpComplementoCod);
   if (!$obErro->ocorreu()) {
       $obErro = $this->obTPessoalCasoCausa->exclusao( $boTransacao );
   }
   $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCasoCausa );

   return $obErro;
}

/**
    * Altera os dados do Caso
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarCaso($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalCasoCausa->setDado  ( "cod_caso_causa"                  , $this->getCodCasoCausa() );
        $this->obTPessoalCasoCausa->setDado  ( "cod_causa_rescisao"              , $this->roPessoalCausaRescisao->getCodCausaRescisao());
        $this->obTPessoalCasoCausa->setDado  ( "descricao"                       , $this->stDescricao        );
        $this->obTPessoalCasoCausa->setDado  ( "paga_aviso_previo"               , $this->boPagaAvidoPrevio  );
        $this->obTPessoalCasoCausa->setDado  ( "paga_ferias_vencida"             , $this->boPagaFeriasVencida );
        $this->obTPessoalCasoCausa->setDado  ( "cod_saque_fgts"                  , $this->stCodigoSaqueFGTS );
        $this->obTPessoalCasoCausa->setDado  ( "multa_fgts"                      , $this->flMultaFGTS );
        $this->obTPessoalCasoCausa->setDado  ( "perc_cont_social"                , $this->flPercContSocial );
        $this->obTPessoalCasoCausa->setDado  ( "inc_fgts_ferias"                 , $this->boIncFGTSFerias );
        $this->obTPessoalCasoCausa->setDado  ( "inc_fgts_aviso_previo"           , $this->boIncFGTSAvisoPrevio );
        $this->obTPessoalCasoCausa->setDado  ( "inc_fgts_13"                     , $this->boIncFGTS13 );

        $this->obTPessoalCasoCausa->setDado  ( "inc_irrf_ferias"                 , $this->boIncIRRFFerias );
        $this->obTPessoalCasoCausa->setDado  ( "inc_irrf_aviso_previo"           , $this->boIncIRRFAvisoPrevio );
        $this->obTPessoalCasoCausa->setDado  ( "inc_irrf_13"                     , $this->boIncIRRF13 );

        $this->obTPessoalCasoCausa->setDado  ( "inc_prev_ferias"                 , $this->boIncPrevFerias );
        $this->obTPessoalCasoCausa->setDado  ( "inc_prev_aviso_previo"           , $this->boIncPrevAvisoPrevio );
        $this->obTPessoalCasoCausa->setDado  ( "inc_prev_13"                     , $this->boIncPrev13 );

        $this->obTPessoalCasoCausa->setDado  ( "paga_ferias_proporcional"        , $this->boPagaFeriasProporcional );

        $this->obTPessoalCasoCausa->setDado  ( "cod_periodo"                      , $this->inCodPeriodo );
        $this->obTPessoalCasoCausa->setDado  ( "inden_art_479"                   , $this->boIndArt479 );

        $obErro = $this->obTPessoalCasoCausa->alteracao ( $boTransacao );
        }

    if (!$obErro->ocorreu()) {
       $this->obTPessoalCasoCausaSubDivisao->setDado('cod_caso_causa'    ,$this->getCodCasoCausa());
       $this->obTPessoalCasoCausaSubDivisao->exclusao($boTransacao);
    }
    if (!$obErro->ocorreu()) {
       foreach ($this->arRPessoalSubDivisao as $obRPessoalSubDivisao) {
          $this->obTPessoalCasoCausaSubDivisao->setDado('cod_caso_causa'    ,$this->getCodCasoCausa());
          $this->obTPessoalCasoCausaSubDivisao->setDado('cod_sub_divisao'   ,$obRPessoalSubDivisao->getCodSubDivisao());
          $obErro = $this->obTPessoalCasoCausaSubDivisao->inclusao($boTransacao);
      }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCasoCausa );

    return $obErro;
}

//
    /**
    * Executa um recuperaTodos na classe Persistente PessoalSubDivisao
    * @access Public
    * @param  Object $rsSubDivisao Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listarCaso(&$rsCaso, $stFiltro = "", $boTransacao = "")
    {
        if ($this->roPessoalCausaRescisao->inCodCausaRescisao) {
           if ($this->roPessoalCausaRescisao->getCodCausaRescisao()) {
              $stFiltro .= "  and pcc.cod_causa_rescisao = ".$this->roPessoalCausaRescisao->getCodCausaRescisao()."";
           }
        }
        $obErro = $this->obTPessoalCasoCausa->recuperaRelacionamento( $rsCaso , $stFiltro, "", $boTransacao );

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
    public function listarSubDivisaoSelecionadas(&$rsRecordSet, $stOrder = " descricao ", $boTransacao = "")
    {
      if ( $this->getCodCasoCausa() ) {
          $stFiltro .= " AND pcc.cod_caso_causa  = '".$this->getCodCasoCausa()."' ";
      }
      $obErro = $this->obTPessoalCasoCausaSubDivisao->recuperaRelacionamento($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

      return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente TPessoalPeriodoCaso
    * @access Public
    * @param  Object $rsSubDivisao Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listarPeriodoRescisao(&$rsPeriodoRescisao,$boTransacao = "")
    {
        
        $stEntidade = Sessao::getEntidade();
        if( empty($stEntidade) ){
            $obErro = $this->obRConfiguracaoPessoal->consultar($boTransacao);
            $grupoPeriodo = $this->obRConfiguracaoPessoal->getGrupoPeriodo();
        }else{
            $obErro = $this->obRConfiguracaoPessoal->consultarEntidade($boTransacao);
            $grupoPeriodo = $this->obRConfiguracaoPessoal->getGrupoPeriodo();
        }
        
        if (!empty($grupoPeriodo)) {
            $stFiltro = " WHERE cod_grupo_periodo = ".$this->obRConfiguracaoPessoal->getGrupoPeriodo();
            $obErro = $this->obTPessoalPeriodoCaso->recuperaTodos($rsPeriodoRescisao,$stFiltro,$stOrder,$boTransacao);
        } else {
            $obErro->setDescricao("Não possui código do grupo do período!");
        }

        return $obErro;
    }
}

?>
