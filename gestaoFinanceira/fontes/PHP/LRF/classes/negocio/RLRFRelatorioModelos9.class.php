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
    * Classe de Regra do Relatório de Modelos 9
    * Data de Criação   : 25/05/2005

    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-07-25 14:47:02 -0300 (Ter, 25 Jul 2006) $

    * Casos de uso :uc-02.05.10
*/

/*
$Log$
Revision 1.8  2006/07/25 17:47:02  cako
Bug #6642#

Revision 1.7  2006/07/05 20:44:40  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO    );
include_once( CAM_GF_LRF_MAPEAMENTO."FLRFModelosExecutivo.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"          );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Negócios Modelos Executivo
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class RLRFRelatorioModelos9 extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFLRFModelosExecutivo;
/*
    * @var Object
    * @access Private
*/
var $obROrcamentoEntidade;
/**
    * @var Object
    * @access Private
*/
var $inCodModelo;
/**
    * @var Object
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inExercicio;
/**
    * @var String
    * @access Private
*/
var $stDataInicial;
/**
    * @var String
    * @access Private
*/
var $stDataFinal;
/**
    * @var String
    * @access Private
*/
var $stFiltro;
/**
    * @var String
    * @access Private
*/
var $stTipoValorDespesa;

/**
    * @var Integer
    * @access Private
*/
function setFLRFModelosExecutivo($valor) { $this->obFLRFModelosExecutivo  = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodModelo($valor) { $this->inCodModelo      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setExercicio($valor) { $this->inExercicio        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicial($valor) { $this->stDataInicial              = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal               = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro           = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTipoValorDespesa($valor) { $this->stTipoValorDespesa           = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getFLRFModelosExecutivo() { return $this->obFLRFModelosExecutivo;   }
/*
    * @access Public
    * @return Object
*/
function getROrcamentoEntidade() { return $this->obROrcamentoEntidade  ;        }
/**
     * @access Public
     * @param Object $valor
*/
function getCodModelo() { return $this->inCodModelo;                     }
/**
     * @access Public
     * @param Object $valor
*/
function getCodEntidade() { return $this->inCodEntidade;                 }
/**
     * @access Public
     * @return Object
*/
function getExercicio() { return $this->inExercicio;                   }
/**
     * @access Public
     * @param Object $valor
*/
function getDataInicial() { return $this->stDataInicial;            }
/**
     * @access Public
     * @param Object $valor
*/
function getDataFinal() { return $this->stDataFinal;              }
/**
     * @access Public
     * @return Object
*/
function getFiltro() { return $this->stFiltro;                      }
/**
     * @access Public
     * @return Object
*/
function getTipoValorDespesa() { return $this->stTipoValorDespesa;           }

/**
     * @access Public
     * @return Object
*/
function RLRFRelatorioModelos9()
{
    $sessao = $_SESSION ['sessao'];
    $this->setFLRFModelosExecutivo       ( new FLRFModelosExecutivo         );
    $this->obROrcamentoEntidade          = new ROrcamentoEntidade;
    $this->obRRelatorio                  = new RRelatorio;
    $this->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsModelo1 ,&$rsModelo2,&$rsModelo4, &$rsModelo5, &$rsModelo6,$stOrder = "")
{
    $stFiltro = "";
    if ( $this->getCodEntidade() ) {
        $stEntidade .= $this->getCodEntidade();
    } else {
        $this->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades );
        while ( !$rsEntidades->eof() ) {
            $stEntidade .= $rsEntidades->getCampo( 'cod_entidade' ).",";
            $rsEntidades->proximo();
        }
        $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
        $stEntidade = $stEntidade;
    }

//#############################CALCULO DO MODELO (1)##########################################################

    $vlAjustadoModelo1   = 0 ;
    $flReceitasCorrentes = 0 ;
    $flDeducoes          = 0 ;

    $this->obFLRFModelosExecutivo->setDado("inCodModelo",1);
    $this->obFLRFModelosExecutivo->setDado("stDataInicial",$this->getDataInicial());
    $this->obFLRFModelosExecutivo->setDado("stDataFinal",$this->getDataFinal());
    $this->obFLRFModelosExecutivo->setDado("exercicio",$this->getExercicio());
    $this->obFLRFModelosExecutivo->setDado("stEntidade",$this->getCodEntidade());
    $this->obFLRFModelosExecutivo->setDado("stFiltro",$this->getFiltro());
    $this->obFLRFModelosExecutivo->setDado("stTipoValorDespesa",null);
    $obErro = $this->obFLRFModelosExecutivo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    while (!$rsRecordSet->eof()) {
      if ($rsRecordSet->getCorrente() == 1) {
        if ( $rsRecordSet->getCampo('redutora') == 't' ) {
            $flReceitasCorrentes = bcsub($flReceitasCorrentes,$rsRecordSet->getCampo('vl_ajustado'),4);
        } else {
            $flReceitasCorrentes = bcadd($flReceitasCorrentes,$rsRecordSet->getCampo('vl_ajustado'),4);
        }
      } else {
        if ( $rsRecordSet->getCampo('redutora') == 't' ) {
            $flDeducoes          = bcsub($flDeducoes,$rsRecordSet->getCampo('vl_ajustado'),4);
        } else {
            $flDeducoes          = bcadd($flDeducoes,$rsRecordSet->getCampo('vl_ajustado'),4);
        }
      }
      $rsRecordSet->proximo();
    }
    //$vlAjustadoModelo1 = bcsub($flReceitasCorrentes,$flDeducoes,4);
    $vlAjustadoModelo1 = bcsub($flReceitasCorrentes,$flDeducoes,4);

    $arModelo1[0]['coluna1']    = "   "."Arrecadadas no mês de referência e nos onze anteriores(12 meses)";
    $arModelo1[0]['coluna2']    = number_format($vlAjustadoModelo1, 2, ',', '.' );;

    $rsModelo1         = new RecordSet;
    $rsModelo1->preenche( $arModelo1 );

//###########################################################################################################

//#############################CALCULO DO MODELO (2)##########################################################
    $flGarantiaValores    = 0;
    $vlAjustadoModelo2    = 0;
    $flPorcentagemModelo2 = 0;

    $this->obFLRFModelosExecutivo->setDado("inCodModelo",2);
    $this->obFLRFModelosExecutivo->setDado("stDataInicial",$this->getDataInicial());
    $this->obFLRFModelosExecutivo->setDado("stDataFinal",$this->getDataFinal());
    $this->obFLRFModelosExecutivo->setDado("exercicio",$this->getExercicio());
    $this->obFLRFModelosExecutivo->setDado("stEntidade",$this->getCodEntidade());
    $this->obFLRFModelosExecutivo->setDado("stFiltro",$this->getFiltro());
    $this->obFLRFModelosExecutivo->setDado("stTipoValorDespesa",$this->getTipoValorDespesa());
    $obErro = $this->obFLRFModelosExecutivo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    while (!$rsRecordSet->eof()) {
      if ( $rsRecordSet->getCampo('redutora') == 't' ) {
        $flDespesaPessoal = bcsub($flDespesaPessoal,$rsRecordSet->getCampo('vl_ajustado'),4);
      } else {
        $flDespesaPessoal = bcadd($flDespesaPessoal,$rsRecordSet->getCampo('vl_ajustado'),4);
      }
      $rsRecordSet->proximo();
    }
    $vlAjustadoModelo2 = $flDespesaPessoal;

    $arModelo2[0]['coluna1']    = "   "."Total das Garantias";
    $arModelo2[0]['coluna2']    = number_format($vlAjustadoModelo2,2,',','.');
    $flPorcentagemModelo2       = number_format(bcdiv(bcmul(100,$vlAjustadoModelo2,4),$vlAjustadoModelo1,4), 2, ',', '.' );
    $arModelo2[0]['coluna3']    = $flPorcentagemModelo2."%";

    $arModelo2[1]['coluna1']    = "   "."Limite para emissão de Alerta - LRF, Inciso II do § 1º do art.59";
    $arModelo2[1]['coluna2']    = '';
    $arModelo2[1]['coluna3']    = "48,60%";

    $arModelo2[2]['coluna1']    = "   "."Limite Prudencial - LRF,Parágrafo Único do art. 22";
    $arModelo2[2]['coluna2']    = '';
    $arModelo2[2]['coluna3']    = "51,30%";

    $arModelo2[3]['coluna1']    = "   "."Limite Legal - LRF, alínea b do Inciso III do art. 20";
    $arModelo2[3]['coluna2']    = '';
    $arModelo2[3]['coluna3']    = "54,00%";

    $rsModelo2        = new RecordSet;
    $rsModelo2->preenche( $arModelo2 );

###########################################################################################################

//#############################CALCULO DO MODELO (4)##########################################################
    $flDividaConsolidada  = 0;
    $flDeducoes1          = 0;
    $flDeducoes2          = 0;
    $vlAjustadoModelo4    = 0;
    $flPorcentagemModelo4 = 0;

    $this->obFLRFModelosExecutivo->setDado("inCodModelo",4);
    $this->obFLRFModelosExecutivo->setDado("stDataInicial",$this->getDataInicial());
    $this->obFLRFModelosExecutivo->setDado("stDataFinal",$this->getDataFinal());
    $this->obFLRFModelosExecutivo->setDado("exercicio",$this->getExercicio());
    $this->obFLRFModelosExecutivo->setDado("stEntidade",$this->getCodEntidade());
    $this->obFLRFModelosExecutivo->setDado("stFiltro",$this->getFiltro());
    $this->obFLRFModelosExecutivo->setDado("stTipoValorDespesa",null);
    $obErro = $this->obFLRFModelosExecutivo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    while (!$rsRecordSet->eof()) {
        if ($rsRecordSet->getCorrente() < 5) {
        //if ($rsRecordSet->getCampo('nom_quadro') == 'Dívida Consolidada ou Fundada') {
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                $flDividaConsolidada = bcsub($flDividaConsolidada,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                $flDividaConsolidada = bcadd($flDividaConsolidada,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
        } elseif ($rsRecordSet->getCorrente() < 13) {
            //if ($rsRecordSet->getCampo('nom_quadro') == 'Deduções') {
                if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                    $flDeducoes1 = bcsub($flDeducoes1,$rsRecordSet->getCampo('vl_ajustado'),4);
                } else {
                    $flDeducoes1 = bcadd($flDeducoes1,$rsRecordSet->getCampo('vl_ajustado'),4);
                }
        } else {
            if ($rsRecordSet->getCorrente() < 18) {
                if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                    $flDeducoes2 = bcsub($flDeducoes2,$rsRecordSet->getCampo('vl_ajustado'),4);
                } else {
                    $flDeducoes2 = bcadd($flDeducoes2,$rsRecordSet->getCampo('vl_ajustado'),4);
                }
            }
        }
      $rsRecordSet->proximo();
    }

//    $vlAjustadoModelo4 = number_format(($flDividaConsolidada)  - ($flDeducoes1 - $flDeducoes2), 2, ',', '.' );

    $vlAjustadoModelo4 = bcsub($flDividaConsolidada,bcsub($flDeducoes1,$flDeducoes2,4),4);

    $arModelo4[0]['coluna1']    = "   "."Dívida Consolidada Líquida";
    $arModelo4[0]['coluna2']    = number_format($vlAjustadoModelo4, 2, ',', '.' );
    $flPorcentagemModelo4       = number_format(bcdiv(bcmul(100,$vlAjustadoModelo4,4),$vlAjustadoModelo1,4), 2, ',', '.' );
    $arModelo4[0]['coluna3']    = $flPorcentagemModelo4."%";

    $arModelo4[1]['coluna1']    = "   "."Limite para emissão de alerta - LRF, Inciso II do § 1º do art.59";
    $arModelo4[1]['coluna2']    = '';
    $arModelo4[1]['coluna3']    = "108%";

    $arModelo4[2]['coluna1']    = "   "."Limite Legal - Resolução do Senado Federal nº 40/2001,Inciso II do art. 3º";
    $arModelo4[2]['coluna2']    = '';
    $arModelo4[2]['coluna3']    = "120%";

    $rsModelo4         = new RecordSet;
    $rsModelo4->preenche( $arModelo4 );

//###########################################################################################################

//#############################CALCULO DO MODELO (5)##########################################################
    $flGarantiaValores    = 0;
    $vlAjustadoModelo5    = 0;
    $flPorcentagemModelo5 = 0;

    $this->obFLRFModelosExecutivo->setDado("inCodModelo",5);
    $this->obFLRFModelosExecutivo->setDado("stDataInicial",$this->getDataInicial());
    $this->obFLRFModelosExecutivo->setDado("stDataFinal",$this->getDataFinal());
    $this->obFLRFModelosExecutivo->setDado("exercicio",$this->getExercicio());
    $this->obFLRFModelosExecutivo->setDado("stEntidade",$this->getCodEntidade());
    $this->obFLRFModelosExecutivo->setDado("stFiltro",$this->getFiltro());
    $this->obFLRFModelosExecutivo->setDado("stTipoValorDespesa",null);
    $obErro = $this->obFLRFModelosExecutivo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    while (!$rsRecordSet->eof()) {
      $flGarantiaValores = bcadd($flGarantiaValores,$rsRecordSet->getCampo('vl_ajustado'),4);
      $rsRecordSet->proximo();
    }
    $vlAjustadoModelo5 = $flGarantiaValores;

    $arModelo5[0]['coluna1']    = "   "."Total das Garantias";
    $arModelo5[0]['coluna2']    = number_format($vlAjustadoModelo5, 2, ',', '.' );
    $flPorcentagemModelo5       = number_format(bcdiv(bcmul(100,$vlAjustadoModelo5,4),$vlAjustadoModelo1,4), 2, ',', '.' );
    $arModelo5[0]['coluna3']    = $flPorcentagemModelo5."%";

    $arModelo5[1]['coluna1']    = "   "."Limite para emissão de alerta s/ Limite Legal - LRF, Inciso II do § 1º do art.59";
    $arModelo5[1]['coluna2']    = '';
    $arModelo5[1]['coluna3']    = "19,80%";

    $arModelo5[2]['coluna1']    = "   "."Limite Legal - Resolução do Senado Federal nº 43/2001,art,9º,caput do art. 3º";
    $arModelo5[2]['coluna2']    = '';
    $arModelo5[2]['coluna3']    = "22,00%";

    $arModelo5[3]['coluna1']    = "   "."Limite para emissão de alerta s/ Limite Legal Ampliado - LRF, Inciso III do § 1º do art.59";
    $arModelo5[3]['coluna2']    = '';
    $arModelo5[3]['coluna3']    = "28,80%";

    $arModelo5[4]['coluna1']    = "   "."Limite Legal Ampliado - Resolução do Senado Federal nº 43/2001,Parágrafo único do art. 9º";
    $arModelo5[4]['coluna2']    = '';
    $arModelo5[4]['coluna3']    = "32,00%";

    $rsModelo5        = new RecordSet;
    $rsModelo5->preenche( $arModelo5 );

//###########################################################################################################

//#############################CALCULO DO MODELO (6)##########################################################
    $flOperacoesCredito       = 0;
    $flAntecipacao            = 0;
    $vlAjustadoModelo6        = 0;
    $flPorcentagemCredito     = 0;
    $flPorcentagemAntecipacao = 0;

    $this->obFLRFModelosExecutivo->setDado("inCodModelo",6);
    $this->obFLRFModelosExecutivo->setDado("stDataInicial",$this->getDataInicial());
    $this->obFLRFModelosExecutivo->setDado("stDataFinal",$this->getDataFinal());
    $this->obFLRFModelosExecutivo->setDado("exercicio",$this->getExercicio());
    $this->obFLRFModelosExecutivo->setDado("stEntidade",$this->getCodEntidade());
    $this->obFLRFModelosExecutivo->setDado("stFiltro",$this->getFiltro());
    $this->obFLRFModelosExecutivo->setDado("stTipoValorDespesa",null);
    $obErro = $this->obFLRFModelosExecutivo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    while (!$rsRecordSet->eof()) {
      if ($rsRecordSet->getCorrente() < 5) {
         if ( $rsRecordSet->getCampo('redutora') == 't' ) {
             $flOperacoesCredito = bcsub($flOperacoesCredito,$rsRecordSet->getCampo('vl_ajustado'),4);
         } else {
             $flOperacoesCredito = bcadd($flOperacoesCredito,$rsRecordSet->getCampo('vl_ajustado'),4);
         }
      } else {
        $flAntecipacao = bcadd($flAntecipacao,$rsRecordSet->getCampo('vl_ajustado'),4);
      }
      $rsRecordSet->proximo();
    }

    $arModelo6[0]['coluna1']    = "   "."Operações de Crédito Internas e Externas";
    $arModelo6[0]['coluna2']    = number_format($flOperacoesCredito, 2, ',', '.' );
    $flPorcentagemCredito       = number_format(bcdiv(bcmul(100,$flOperacoesCredito,4),$vlAjustadoModelo1,4), 2, ',', '.' );
    $arModelo6[0]['coluna3']    = $flPorcentagemCredito."%";

    $arModelo6[1]['coluna1']    = "   "."Limite para emissão de alerta s/ Limite Legal - LRF, Inciso II do § 1º do art.59";
    $arModelo6[1]['coluna2']    = '';
    $arModelo6[1]['coluna3']    = "14,40%";

    $arModelo6[2]['coluna1']    = "   "."Limite Legal -Operação de Crédito Internas e Externas - Resolução do Senado Federal nº 43/2001,art,7º";
    $arModelo6[2]['coluna2']    = '';
    $arModelo6[2]['coluna3']    = "16,00%";

    $arModelo6[3]['coluna1']    = "   "."Operações de Crédito p/ Antecipação de Receita - ARO";
    $arModelo6[3]['coluna2']    = number_format($flAntecipacao, 2, ',', '.' );
    $flPorcentagemAntecipacao   = number_format(bcdiv(bcmul(100,$flAntecipacao,4),$vlAjustadoModelo1,4), 2, ',', '.' );
    $arModelo6[3]['coluna3']    = $flPorcentagemAntecipacao;

    $arModelo6[4]['coluna1']    = "   "."Limite para emissão de alerta s/ Limite Legal -ARO- Resolução do Senado Federal nº 43/2001,art. 10";
    $arModelo6[4]['coluna2']    = '';
    $arModelo6[4]['coluna3']    = "6,30%";

    $arModelo6[5]['coluna1']    = "   "."Limite Legal -ARO -Resolução do Senado Federal nº 43/2001,art. 10";
    $arModelo6[5]['coluna2']    = '';
    $arModelo6[5]['coluna3']    = "16,00%";

    $rsModelo6        = new RecordSet;
    $rsModelo6->preenche( $arModelo6 );

//###########################################################################################################
    return $obErro;
}

}
