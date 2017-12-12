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
    * Classe de regra de negócio Pessoal Causa Rescisao
    * Data de Criação: 03/05/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCasoCausa.class.php"              );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalMovimentoSefipSaida.class.php"    );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisao.class.php"     );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCasoCausa.class.php"         );
include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"                         );

class RPessoalCausaRescisao
{
/**
    * @access Private
    * @var Integer
*/
var $inCodCausaRescisao;

/**
    * @access Private
    * @var Integer
*/
var $inNumCausa;

/**
    * @access Private
    * @var String
*/
var $stDescricao;

/**
    * @access Private
    * @var Object
*/
var $obTPessoalCausaRescisao;

/**
    * @access Private
    * @var Object
*/
var $obTPessoalCasoCausa;

/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * @access Private
    * @var Object
*/
var $arRPessoalCasoCausa;

/**
    * @access Private
    * @var Object
*/
var $roUltimoPessoalCasoCausa;

/**
    * @access Private
    * @var Object
*/
var $obRPessoalMovimentoSefipSaida;

/**
* @var Objeto
* @access Private
*/
var $obRFuncao;

/**
    * @access Public
    * @param Object $valor
*/

function setCodCausaRescisao($valor) { $this->inCodCausaRescisao              = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setNumCausa($valor) { $this->inNumCausa        = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDescricao($valor) { $this->stDescricao        = $valor; }
/**
    * @access Public
    * @param String $Valor
*/

function setTPessoalCausaRescisao($valor) { $this->obTPessoalCausaRescisao       = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalCasoCausa($valor) { $this->obTPessoalCasoCausa       = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setRPessoalMovimentoSefipSaida($valor) { $this->obRPessoalMovimentoSefipSaida   = $valor  ; }

/**
* @access Public
* @param Object $valor
*/
function setRFuncao($valor) { $this->obRFuncao                    = $valor; }

/**
    * @access Public
    * @return Object
*/
function getCodCausaRescisao() { return $this->inCodCausaRescisao            ; }
/**
    * @access Public
    * @return Integer
*/
function getNumCausa() { return $this->inNumCausa      ; }
/**
    * @access Public
    * @return Numeric
*/
function getDescricao() { return $this->stDescricao      ; }
/**
    * @access Public
    * @return String
*/

function getTPessoalCausaRescisao() { return $this->obTPessoalCausaRescisao   ; }

/**
    * @access Public
    * @return Object
*/
function getTPessoalCasoCausa() { return $this->obTPessoalCasoCausa   ; }

/**
    * @access Public
    * @return Object
*/
function getRPessoalMovimentoSefipSaida() { return $this->obRPessoalMovimentoSefipSaida       ; }

/**
* @access Public
* @param Object $valor
*/
function getRFuncao() { return $this->obRFuncao;                               }

/**
     * Método construtor
     * @access Private
*/
function RPessoalCausaRescisao()
{
    $this->setTPessoalCausaRescisao      (new TPessoalCausaRescisao);
    $this->setTPessoalCasoCausa          (new TPessoalCasoCausa);
    $this->obTransacao                   = new Transacao;
    $this->arRPessoalCasoCausa           = array();
    $this->obRPessoalMovimentoSefipSaida = new RPessoalMovimentoSefipSaida;
    $this->setRFuncao                    ( new RFuncao           );
}

/**
* Adiciona um array de referencia-objeto
* @access Public
*/
function addPessoalCasoCausa()
{
   $this->arRPessoalCasoCausa[]      =  new RPessoalCasoCausa($this);
   $this->roUltimoPessoalCasoCausa   = &$this->arRPessoalCasoCausa[ count($this->arRPessoalCasoCausa) - 1 ];
}

/**
    * Inclui dados da Causa rescisao no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirCausa($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro =  $this->obTPessoalCausaRescisao->proximoCod( $inCodCausaRescisao , $boTransacao );
        $this->setCodCausaRescisao($inCodCausaRescisao);
        if ( !$obErro->ocorreu() ) {
             $this->obTPessoalCausaRescisao->setDado("cod_causa_rescisao"      , $this->getCodCausaRescisao() );
             $this->obTPessoalCausaRescisao->setDado("descricao"               , $this->getDescricao() );
             $this->obTPessoalCausaRescisao->setDado("num_causa"               , $this->getNumCausa() );
             $this->obTPessoalCausaRescisao->setDado("cod_sefip_saida"         , $this->obRPessoalMovimentoSefipSaida->getCodSefip());
             $obErro = $this->obTPessoalCausaRescisao->inclusao( $boTransacao );
        }
    }
    if (!$obErro->ocorreu()) {
        foreach ($this->arRPessoalCasoCausa  as $obRPessoalCasoCausa) {
               $obErro =  $obRPessoalCasoCausa->incluirCaso($boTransacao);
               if ($obErro->ocorreu()) {
                  break;
               }
           }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCausaRescisao);

    return $obErro;
    }

}

/**
    * altera dados da Causa rescisao no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarCausa($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !$obErro->ocorreu() ) {
             $this->obTPessoalCausaRescisao->setDado("cod_causa_rescisao"      , $this->getCodCausaRescisao() );
             $this->obTPessoalCausaRescisao->setDado("descricao"               , $this->getDescricao() );
             $this->obTPessoalCausaRescisao->setDado("num_causa"               , $this->getNumCausa() );
             $this->obTPessoalCausaRescisao->setDado("cod_sefip_saida"         , $this->obRPessoalMovimentoSefipSaida->getCodSefip());
             $obErro = $this->obTPessoalCausaRescisao->alteracao( $boTransacao );
        }
    }
//    if (!$obErro->ocorreu()) {
//        $obErro = $this->excluirAssociacao($boTransacao);
//        if ($obErro->ocorreu()) {
//            break;
//        }
//    }

    if (!$obErro->ocorreu()) {
        foreach ($this->arRPessoalCasoCausa  as $obRPessoalCasoCausa) {
            $obErro =  $obRPessoalCasoCausa->alterarCaso($boTransacao);
            if ($obErro->ocorreu()) {
                break;
            }
           }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCausaRescisao);

    return $obErro;
    }

}

/**
    * Exclui os dados do Caso Causa pelo cod da rescisao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirAssociacao($boTransacao = "")
{
   $boFlagTransacao = false;
   $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
   if (!$obErro->ocorreu()) {
       $this->roUltimoPessoalCasoCausa->listarCaso($rsCaso,$stFiltro="",$boTransacao);
       $tmpComplementoChave =  $this->roUltimoPessoalCasoCausa->obTPessoalCasoCausaSubDivisao->getComplementoChave();
       $tmpComplementoCod   =  $this->roUltimoPessoalCasoCausa->obTPessoalCasoCausaSubDivisao->getCampoCod();
       $this->roUltimoPessoalCasoCausa->obTPessoalCasoCausaSubDivisao->setComplementoChave('');
       $this->roUltimoPessoalCasoCausa->obTPessoalCasoCausaSubDivisao->setCampoCod('cod_caso_causa');
       while (!$rsCaso->eof()) {
           $this->roUltimoPessoalCasoCausa->obTPessoalCasoCausaSubDivisao->setDado('cod_caso_causa',$rsCaso->getCampo('cod_caso_causa'));
           $obErro = $this->roUltimoPessoalCasoCausa->obTPessoalCasoCausaSubDivisao->exclusao($boTransacao);
           if ($obErro->ocorreu) {
              break;
           }
           $rsCaso->proximo();
       }
      $this->roUltimoPessoalCasoCausa->obTPessoalCasoCausaSubDivisao->setComplementoChave($tmpComplementoCampo);
      $this->roUltimoPessoalCasoCausa->obTPessoalCasoCausaSubDivisao->setCampoCod($tmpComplementoCod);
      if (!$obErro->ocorreu()) {
        $tmpCampo = $this->obTPessoalCasoCausa->getCampoCod();
        $this->obTPessoalCasoCausa->setCampoCod('cod_causa_rescisao');
        $this->obTPessoalCasoCausa->setDado    ( "cod_causa_rescisao" , $this->getCodCausaRescisao() );
        $obErro = $this->obTPessoalCasoCausa->exclusao( $boTransacao );
        $this->obTPessoalCasoCausa->setCampoCod($tmpCampo);
      }
   }
   $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCasoCausa );

   return $obErro;
}

/**
    * Exclui os dados da Causa Rescisao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirCausa($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    include_once(CAM_GRH_PES_NEGOCIO."RPessoalRescisaoContrato.class.php");
    $obRPessoalRescisaoContrato = new RPessoalRescisaoContrato;
    $obRPessoalRescisaoContrato->listarRescisaoContratoRescindidos( $rsRescisaoContrato , $boTransacao );

    //Monta array com os cod_caso_causa utilizados em rescisao de contrato
    while ( !$rsRescisaoContrato->eof() ) {
        $arCasoCausaRescisaoContrato[] = $rsRescisaoContrato->getCampo('cod_caso_causa');
        $rsRescisaoContrato->proximo();
    }

    if (!$obErro->ocorreu()) {
        $this->addPessoalCasoCausa();
        $this->roUltimoPessoalCasoCausa->listarCaso($rsCaso,$stFiltro="",$boTransacao);
        while (!$rsCaso->eof() && !$obErro->ocorreu()) {
            //Verifica se o caso nao foi usado em rescisao de contrato
            if ( (!is_array( $arCasoCausaRescisaoContrato)) or (!in_array($rsCaso->getCampo('cod_caso_causa'),$arCasoCausaRescisaoContrato))) {
                $this->roUltimoPessoalCasoCausa->setCodCasoCausa($rsCaso->getCampo('cod_caso_causa'));
                $obErro = $this->roUltimoPessoalCasoCausa->excluirCaso($boTransacao);
            } else {
                $obErro->setDescricao("O caso de causa ".$rsCaso->getCampo('cod_caso_causa')." está sendo utilizado numa rescisão de contrato.");
            }
            $rsCaso->proximo();
        }
    }
    if (!$obErro->ocorreu()) {

        ////verificando se existem assentamentos usando esta rescisao
         include_once( CAM_GRH_PES_MAPEAMENTO . 'TPessoalAssentamentoCausaRescisao.class.php' );
        $obTPessoalAssentamentoCausa = new TPessoalAssentamentoCausaRescisao;
        $stFiltro = 'and pacr.cod_causa_rescisao ='. $this->getCodCausaRescisao();
        $obTPessoalAssentamentoCausa->recuperaRelacionamento( $rsAssentamento, $stFiltro, '', $boTransacao );

        if ( $rsAssentamento->getNumLinhas() > 0 ) {
            $obErro->setDescricao("A causa de rescisão está sendo utilizado no assentamento " . $rsAssentamento->getCampo('cod_assentamento') );
        } else {
            $this->obTPessoalCausaRescisao->setDado('cod_causa_rescisao',$this->getCodCausaRescisao());
            $obErro = $this->obTPessoalCausaRescisao->exclusao($boTransacao);
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCasoCausa );

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
function listarCausa(&$rsRecordSet, $stOrder = " descricao ", $boTransacao = "")
{
    if( $this->getNumCausa() )
        $stFiltro .= " AND num_causa = '".$this->getNumCausa()."' ";
    if( $this->getDescricao() )
        $stFiltro .= " AND descricao ILIKE '%".$this->getDescricao()."%' ";
    if($stFiltro)
        $stFiltro = " WHERE cod_causa_rescisao IS NOT NULL ".$stFiltro;
    $obErro = $this->obTPessoalCausaRescisao->recuperaTodos($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
