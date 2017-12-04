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
    * Classe de Regra do Relatório de Modelos 14
    * Data de Criação   : 25/05/2005

    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-07-25 14:16:31 -0300 (Ter, 25 Jul 2006) $

    * Casos de uso: uc-02.05.10
*/

/*
$Log$
Revision 1.8  2006/07/25 17:16:31  cako
Bug #6642#

Revision 1.7  2006/07/05 20:44:40  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO    );
include_once( CAM_GF_LRF_MAPEAMENTO."FLRFModelosExecutivo.class.php"   );
include_once( CAM_GF_LRF_MAPEAMENTO."FLRFModelosLegislativo.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"          );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );
include_once (CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php"   );

/**
    * Classe de Regra de Negócios Modelos Legislativo
    * @author Desenvolvedor: Marcelo B. Paulino
*/
class RLRFRelatorioModelos14 extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFLRFModelosExecutivo;
/**
    * @var Object
    * @access Private
*/
var $obFLRFModelosLegislativo;
/**
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
function setFLRFModelosExecutivo($valor) { $this->obFLRFModelosExecutivo = $valor;   }
/**
    * @var Integer
    * @access Private
*/
function setFLRFModelosLegislativo($valor) { $this->obFLRFModelosLegislativo = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade = $valor;     }
/**
     * @access Public
     * @param Object $valor
*/
function setCodModelo($valor) { $this->inCodModelo = $valor;              }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade = $valor;            }
/**
     * @access Public
     * @param Object $valor
*/
function setExercicio($valor) { $this->inExercicio = $valor;              }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicial($valor) { $this->stDataInicial = $valor;            }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal = $valor;              }
/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro = $valor;                 }
/**
     * @access Public
     * @param Object $valor
*/
function setTipoValorDespesa($valor) { $this->stTipoValorDespesa  = $valor;      }

/**
     * @access Public
     * @param Object $valor
*/
function getFLRFModelosExecutivo() { return $this->obFLRFModelosExecutivo;  }
/**
     * @access Public
     * @param Object $valor
*/
function getFLRFModelosLegislativo() { return $this->obFLRFModelosLegislativo;}
/**
    * @access Public
    * @return Object
*/
function getROrcamentoEntidade() { return $this->obROrcamentoEntidade;    }
/**
     * @access Public
     * @param Object $valor
*/
function getCodModelo() { return $this->inCodModelo;             }
/**
     * @access Public
     * @param Object $valor
*/
function getCodEntidade() { return $this->inCodEntidade;           }
/**
     * @access Public
     * @return Object
*/
function getExercicio() { return $this->inExercicio;             }
/**
     * @access Public
     * @param Object $valor
*/
function getDataInicial() { return $this->stDataInicial;           }
/**
     * @access Public
     * @param Object $valor
*/
function getDataFinal() { return $this->stDataFinal;             }
/**
     * @access Public
     * @return Object
*/
function getFiltro() { return $this->stFiltro;                }
/**
     * @access Public
     * @return Object
*/
function getTipoValorDespesa() { return $this->stTipoValorDespesa;      }

/**
     * @access Public
     * @return Object
*/
function RLRFRelatorioModelos14()
{
    $sessao = $_SESSION ['sessao'];
    $this->setFLRFModelosExecutivo  ( new FLRFModelosExecutivo   );
    $this->setFLRFModelosLegislativo( new FLRFModelosLegislativo );
    $this->obROrcamentoEntidade = new ROrcamentoEntidade;
    $this->obRRelatorio         = new RRelatorio;
    $this->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsModelo1 , &$rsModelo10 , $stOrder = "")
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

//###################################### CALCULO DO MODELO(1) ######################################

    $vlAjustadoModelo1   = 0 ;
    $flReceitasCorrentes = 0 ;
    $flDeducoes          = 0 ;

    $obRConfiguracaoConfiguracao            =   new RConfiguracaoConfiguracao() ;
    $obRConfiguracaoConfiguracao->setCodModulo  ( 8                         );
    $obRConfiguracaoConfiguracao->setExercicio  ( $this->getExercicio()     );
    $obRConfiguracaoConfiguracao->setParametro  ( "cod_entidade_prefeitura" );
    $obRConfiguracaoConfiguracao->consultar     ( $boTransacao              );

    $this->obFLRFModelosExecutivo->setDado( "inCodModelo"        , 1                            );
    $this->obFLRFModelosExecutivo->setDado( "stDataInicial"      , $this->getDataInicial()      );
    $this->obFLRFModelosExecutivo->setDado( "stDataFinal"        , $this->getDataFinal()        );
    $this->obFLRFModelosExecutivo->setDado( "exercicio"          , $this->getExercicio()        );
    $this->obFLRFModelosExecutivo->setDado( "stEntidade"         , $obRConfiguracaoConfiguracao->getValor() );
    $this->obFLRFModelosExecutivo->setDado( "stFiltro"           , $this->getFiltro()           );
    $this->obFLRFModelosExecutivo->setDado( "stTipoValorDespesa" , null );
    $obErro = $this->obFLRFModelosExecutivo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

//     while ( !$rsRecordSet->eof() ) {
//         if ( $rsRecordSet->getCorrente() == 1 ) {
//             if ( $rsRecordSet->getCampo('redutora') == 't' ) {
//                 $flReceitasCorrentes = $rsRecordSet->getCampo( 'vl_ajustado' );
//                 $flVlAjustado1       = bcsub($flVlAjustado1,$rsRecordSet->getCampo('vl_ajustado'),4);
//             } else {
//                 $flVlAjustado1       = bcadd($flVlAjustado1,$rsRecordSet->getCampo('vl_ajustado'),4);
//             }
//         } else {
//             $flDeducoes = bcadd($flDeducoes,$rsRecordSet->getCampo('vl_ajustado'),4);
//         }
//         $rsRecordSet->proximo();
//     }
//     $vlAjustadoModelo1 = bcsub($flReceitasCorrentes,$flDeducoes,4);

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
    $vlAjustadoModelo1 = bcsub($flReceitasCorrentes,$flDeducoes,4);

    $arModelo1[0]['coluna1']    = "   "."Arrecadadas no mês de referência e nos onze anteriores(12 meses)";
    $arModelo1[0]['coluna2']    = number_format($vlAjustadoModelo1, 2, ',', '.' );;

    $rsModelo1 = new RecordSet;
    $rsModelo1->preenche( $arModelo1 );

//##################################################################################################

//###################################### CALCULO DO MODELO(10) #####################################

    $flGarantiaValores     = 0;
    $vlAjustadoModelo10    = 0;
    $flPorcentagemModelo10 = 0;

    $obRConfiguracaoConfiguracao            =   new RConfiguracaoConfiguracao() ;
    $obRConfiguracaoConfiguracao->setCodModulo  ( 8                         );
    $obRConfiguracaoConfiguracao->setExercicio  ( $this->getExercicio()     );
    $obRConfiguracaoConfiguracao->setParametro  ( "cod_entidade_camara" );
    $obRConfiguracaoConfiguracao->consultar     ( $boTransacao              );

    $this->obFLRFModelosLegislativo->setDado( "inCodModelo"        , 10                           );
    $this->obFLRFModelosLegislativo->setDado( "stDataInicial"      , $this->getDataInicial()      );
    $this->obFLRFModelosLegislativo->setDado( "stDataFinal"        , $this->getDataFinal()        );
    $this->obFLRFModelosLegislativo->setDado( "exercicio"          , $this->getExercicio()        );
    $this->obFLRFModelosLegislativo->setDado( "stEntidade"         , $obRConfiguracaoConfiguracao->getValor() );
    $this->obFLRFModelosLegislativo->setDado( "stFiltro"           , $this->getFiltro()           );
    $this->obFLRFModelosLegislativo->setDado( "stTipoValorDespesa" , $this->getTipoValorDespesa() );
    $obErro = $this->obFLRFModelosLegislativo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    while (!$rsRecordSet->eof()) {
        if ( $rsRecordSet->getCampo('redutora') == 't' ) {
            $flVlAjustado = bcsub($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
        } else {
            $flVlAjustado = bcadd($rsRecordSet->getCampo('vl_ajustado'),$flVlAjustado,4);
        }
//       $flDespesaPessoal = bcadd( $flDespesaPessoal ,$rsRecordSet->getCampo('vl_ajustado'), 4 );
      $rsRecordSet->proximo();
    }
    $vlAjustadoModelo10 = $flVlAjustado;

    $arModelo10[0]['coluna1'] = "   "."Total da Despesa Líquida c/Pessoal nos 12 últimos meses";
    $arModelo10[0]['coluna2'] = number_format( $vlAjustadoModelo10, 2, ',', '.' );
    if($vlAjustadoModelo10 > 0 and $vlAjustadoModelo1 > 0)
        $flPorcentagemModelo10    = number_format( bcdiv( bcmul( 100, $vlAjustadoModelo10, 4), $vlAjustadoModelo1, 4), 2, ',', '.' );
    else
        $flPorcentagemModelo10 = "0,0";
    $arModelo10[0]['coluna3'] = $flPorcentagemModelo10."%";

    $arModelo10[1]['coluna1'] = "   "."Limite para emissão de Alerta - LRF, Inciso II do § 1º do art.59";
    $arModelo10[1]['coluna2'] = '';
    $arModelo10[1]['coluna3'] = "5,4%";

    $arModelo10[2]['coluna1'] = "   "."Limite Prudencial - LRF,Parágrafo Único do art. 22";
    $arModelo10[2]['coluna2'] = '';
    $arModelo10[2]['coluna3'] = "5,7%";

    $arModelo10[3]['coluna1'] = "   "."Limite Legal - LRF, alínea a do Inciso III do art. 20";
    $arModelo10[3]['coluna2'] = '';
    $arModelo10[3]['coluna3'] = "6,0%";

    $rsModelo10 = new RecordSet;
    $rsModelo10->preenche( $arModelo10 );

//##################################################################################################
    return $obErro;
}

}
