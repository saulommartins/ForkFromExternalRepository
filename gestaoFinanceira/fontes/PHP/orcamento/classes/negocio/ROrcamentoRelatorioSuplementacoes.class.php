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
    * Classe de Regra de Negócios para relatorio de Suplementacoes
    * Data de Criação: 04/07/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 30824 $
    $Name$
    $Author: cako $
    $Date: 2007-08-16 11:29:41 -0300 (Qui, 16 Ago 2007) $

    * Casos de uso: uc-02.01.25
*/

/*
$Log$
Revision 1.14  2007/08/16 14:29:41  cako
Bug#9935#

Revision 1.13  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO  		);
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacao.class.php" 		);
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"       			);
include_once( CAM_FW_PDF."RRelatorio.class.php"                  		);

class ROrcamentoRelatorioSuplementacoes extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obTOrcamentoSuplementacao;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inCodNorma;
/**
    * @var Integer
    * @access Private
*/
var $inCodDespesa;
/**
    * @var String
    * @acess Private
*/
var $stSituacao;
/**
    * @var String
    * @acess Private
*/
var $stTipoRelatorio;
/**
    * @var String
    * @acess Private
*/
var $inCodTipoSuplementacao;
/**
    * @var Integer
    * @access Private
*/
var $inExercicio;
/**
    * @var String
    * @access Private
*/
var $stFiltro;
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
     * @access Public
     * @param Object $valor
*/
function setTOrcamentoSuplementacao($valor) { $this->obTOrcamentoSuplementacao    = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade		    = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setSituacao($valor) { $this->stSituacao 					= $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade            	= $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodNorma($valor) { $this->inCodNorma		            = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodDespesa($valor) { $this->inCodDespesa            		= $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setExercicio($valor) { $this->inExercicio              	= $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro                 	= $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicial($valor) { $this->stDataInicial              	= $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal               	= $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTipoSuplementacao($valor) { $this->inCodTipoSuplementacao       = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTipoRelatorio($valor) { $this->stTipoRelatorio              = $valor; }
/**
     * @access Public
     * @return Object
*/
function getTOrcamentoSuplementacao() { return $this->obTOrcamentoSuplementacao;}
/**
     * @access Public
     * @return Object
*/
function getTROrcamentoEntidade() { return $this->obROrcamentoEntidade;			}
/**
     * @access Public
     * @return Object
*/
function getSituacao() { return $this->stSituacao; 				}
/**
     * @access Public
     * @return Object
*/
function getCodEntidade() { return $this->inCodEntidade;            }
/**
     * @access Public
     * @return Object
*/
function getCodNorma() { return $this->inCodNorma;   	        }
/**
     * @access Public
     * @return Object
*/
function getCodDespesa() { return $this->inCodDespesa;	            }
/**
     * @access Public
     * @return Object
*/

function getExercicio() { return $this->inExercicio;              }
/**
     * @access Public
     * @param Object $valor
*/
function getFiltro() { return $this->stFiltro;                 }
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
     * @param Object $valor
*/
function getTipoSuplementacao() { return $this->inCodTipoSuplementacao;   }
/**
     * @access Public
     * @param Object $valor
*/
function getTipoRelatorio() { return $this->stTipoRelatorio;   		}

/**
    * Método Construtor
    * @access Private
*/

function ROrcamentoRelatorioSuplementacoes()
{
    $this->setTOrcamentoSuplementacao       ( new TOrcamentoSuplementacao		);
    $this->setROrcamentoEntidade	        ( new ROrcamentoEntidade			);
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arRecordSet,&$arRecordSet2,&$arRecordSet3, &$arRecordSet4, $stOrder = "")
{
    /**
     * Saida 0.000,00
    */
    function NroBR($ent)
    {
        $aux1 = explode(',',$ent);
        $aux2="";
        $count=count($aux1)-1;
        if ($count>0) {
            for ($i=0; $i<$count; $i++) {
                $aux2=$aux1[$i];
            }

            $ent=str_replace(".","",$aux2);
            $ent=number_format ( $ent, 0, ",", ".");
            $ent=$ent.",".substr( $aux1[$count],0,2);
        } else {
            $ent=number_format ( $ent, 2, ",", ".");
        }

        return $ent;
    }

    $stFiltro 		    = "";
    $stOrder 		    = "";
    $inCount                = '0';
    $inCount2               = '0';
    $inCount3               = '0';
    $inCount4               = '0';
    $arRecord               = array();
    $arRecord2              = array();
    $arRecord3              = array();
    $arRecord4              = array();
    $arRecordSet            = array( new RecordSet );
    $arRecordSet2           = array( new RecordSet );
    $arRecordSet3           = array( new RecordSet );
    $arRecordSet4           = array( new RecordSet );
    $rsEntidades	    = new RecordSet;
    $entidadeAnterior 	    = "";
    $entidadeAtual	    = "";
    $leiDecretoAnterior     = "";
    $leiDecretoAtual	    = "";
    $situacaoAtual          = "";
    $situacaoAnterior       = "";
    $tipoSuplementacaoAtual = "";
    $tipoSuplementacaoAnterior = "";
    $dataAtual              = "";
    $dataAnterior           = "";
    $dotacaoAtual           = "";
    $dotacaoAnterior        = "";
    $totalReduzido          = '0.00' ;
    $totalSuplementado      = '0.00' ;
    $totalAnulada	    = '0.00' ;
    $totalValida	    = '0.00' ;
    $total		    = '0.00' ;
    $imprimeTotal	    = '0' ;

    $this->obTOrcamentoSuplementacao->setDado("exercicio"	,$this->getExercicio()			);
    $this->obTOrcamentoSuplementacao->setDado("cod_entidade"    ,$this->getCodEntidade()		);
    $this->obTOrcamentoSuplementacao->setDado("cod_despesa"	,$this->getCodDespesa()			);
    $this->obTOrcamentoSuplementacao->setDado("cod_norma"	,$this->getCodNorma()			);
    $this->obTOrcamentoSuplementacao->setDado("dt_inicial"	,$this->getDataInicial()		);
    $this->obTOrcamentoSuplementacao->setDado("dt_final"	,$this->getDataFinal()			);
    $this->obTOrcamentoSuplementacao->setDado("situacao"	,$this->getSituacao()			);
    $this->obTOrcamentoSuplementacao->setDado("cod_tipo"	,$this->getTipoSuplementacao()	        );
    $this->obTOrcamentoSuplementacao->setDado("relatorio"	,$this->getTipoRelatorio()		);
    $obErro = $this->obTOrcamentoSuplementacao->recuperaRelatorioSuplementacoes( $rsRecordSet, $stFiltro, $stOrder );

    if ( !$obErro->ocorreu() ) {
            switch ($this->getTipoRelatorio()) {
                CASE 'entidade':
                        while (!$rsRecordSet->eof()) {
                $entidadeAtual = $rsRecordSet->getCampo('entidade');

                if ($entidadeAtual <> $entidadeAnterior) {

                    if ($totalValida <> 0) {
                                                $arRecord[$inCount]['coluna1']  = "";
                        $arRecord[$inCount]['coluna2']  = "";
                        $arRecord[$inCount]['coluna3']  = "";
                        $arRecord[$inCount]['coluna4']  = "";
                        $arRecord[$inCount]['coluna5']  = "";
                        $arRecord[$inCount]['coluna6']  = "";
                        $inCount++;
                        $arRecord[$inCount]['coluna1']  = "";
                        $arRecord[$inCount]['coluna2']  = "";
                        $arRecord[$inCount]['coluna3']  = "";
                        $arRecord[$inCount]['coluna4']  = "Total: ";
                        $arRecord[$inCount]['coluna5']  = NroBR ( $totalValida );
                        $arRecord[$inCount]['coluna6']  = "";
                        $inCount++;
                                                $arRecord[$inCount]['coluna1']  = "";
                        $arRecord[$inCount]['coluna2']  = "";
                        $arRecord[$inCount]['coluna3']  = "";
                        $arRecord[$inCount]['coluna4']  = "";
                        $arRecord[$inCount]['coluna5']  = "";
                        $arRecord[$inCount]['coluna6']  = "";
                        $inCount++;

                        $totalValida	= 0;
                    }

                                        $this->obROrcamentoEntidade->setExercicio(Sessao::getExercicio());
                    $this->obROrcamentoEntidade->setCodigoEntidade($entidadeAtual);
                    $this->obROrcamentoEntidade->consultarNomes($rsEntidades);
                    $arRecord2[0]['coluna1']  = "Entidade:                        ";
                                        $arRecord2[0]['coluna2']  = $this->obROrcamentoEntidade->getNomeEntidade();
                    $arRecord2[0]['coluna3']  = "";
                    $arRecord2[0]['coluna4']  = "";
                    $arRecord2[0]['coluna5']  = "";
                    $arRecord2[0]['coluna6']  = "";
                                        $arRecordSet2[$inCount3] = new RecordSet;
                    $arRecordSet2[$inCount3]->preenche( $arRecord2 );
                                        $inCount3++;

                    if ($entidadeAnterior <>"") {
                        $arRecord[$inCount]['coluna1']  = "";
                        $arRecord[$inCount]['coluna2']  = "";
                        $arRecord[$inCount]['coluna3']  = "";
                        $arRecord[$inCount]['coluna4']  = "";
                        $arRecord[$inCount]['coluna5']  = "";
                        $arRecord[$inCount]['coluna6']  = "";
                        $inCount++;
                        $arRecord[$inCount]['coluna1']  = "Total Geral da Entidade: ";
                        $arRecord[$inCount]['coluna2']  = "";
                        $arRecord[$inCount]['coluna3']  = "";
                        $arRecord[$inCount]['coluna4']  = "";
                        $arRecord[$inCount]['coluna5']  = NroBR ( $total );
                        $arRecord[$inCount]['coluna6']  = "";

                                            $arRecordSet[$inCount2] = new RecordSet;
                                            $arRecordSet[$inCount2]->preenche( $arRecord );

                                            $inCount2	++;
                                            $inCount 	= 0;
                                            $total		= 0;
                                            $arRecord   = array();
                                        }
                }

                if ($rsRecordSet->getCampo('situacao')=='Anulada') {
                    $totalAnulada = bcadd($totalAnulada,$rsRecordSet->getCampo('valor'),2);
                    $imprimeTotal=1;
                }
                if ($rsRecordSet->getCampo('situacao')=='Válida') {
                    $totalValida  = bcadd($totalValida ,$rsRecordSet->getCampo('valor'),2);
                }
                if ( $rsRecordSet->getCampo('situacao')=='Válida' && $imprimeTotal==1) {
                    $arRecord[$inCount]['coluna1']  = "";
                    $arRecord[$inCount]['coluna2']  = "";
                    $arRecord[$inCount]['coluna3']  = "";
                    $arRecord[$inCount]['coluna4']  = "";
                    $arRecord[$inCount]['coluna5']  = "";
                    $arRecord[$inCount]['coluna6']  = "";
                    $inCount++;
                    $arRecord[$inCount]['coluna1']  = "";
                    $arRecord[$inCount]['coluna2']  = "";
                    $arRecord[$inCount]['coluna3']  = "";
                    $arRecord[$inCount]['coluna4']  = "Total: ";
                    $arRecord[$inCount]['coluna5']  = NroBR ( $totalAnulada);
                    $arRecord[$inCount]['coluna6']  = "";
                                        $inCount++;
                                        $arRecord[$inCount]['coluna1']  = "";
                    $arRecord[$inCount]['coluna2']  = "";
                    $arRecord[$inCount]['coluna3']  = "";
                    $arRecord[$inCount]['coluna4']  = "";
                    $arRecord[$inCount]['coluna5']  = "";
                    $arRecord[$inCount]['coluna6']  = "";
                    $inCount++;
                    $imprimeTotal=0;
                    $totalAnulada=0;
                }

                $total = bcadd($total,$rsRecordSet->getCampo('valor'),2);

                $arRecord[$inCount]['coluna1']  = $rsRecordSet->getCampo('data');
                $arRecord[$inCount]['coluna2']  = $rsRecordSet->getCampo('fundamentacao');
                $arRecord[$inCount]['coluna3']  = $rsRecordSet->getCampo('fonte');
                $arRecord[$inCount]['coluna4']  = $rsRecordSet->getCampo('tipo_suplementacao');
                $arRecord[$inCount]['coluna5']  = NroBR ( $rsRecordSet->getCampo('valor') );
                $arRecord[$inCount]['coluna6']  = $rsRecordSet->getCampo('situacao');
                $inCount++;

                $entidadeAnterior 	= $entidadeAtual;
                $rsRecordSet->proximo();
            }

            if ($totalValida <> 0) {
                $arRecord[$inCount]['coluna1']  = "";
                $arRecord[$inCount]['coluna2']  = "";
                $arRecord[$inCount]['coluna3']  = "";
                $arRecord[$inCount]['coluna4']  = "";
                $arRecord[$inCount]['coluna5']  = "";
                $arRecord[$inCount]['coluna6']  = "";
                $inCount++;
                $arRecord[$inCount]['coluna1']  = "";
                $arRecord[$inCount]['coluna2']  = "";
                $arRecord[$inCount]['coluna3']  = "";
                $arRecord[$inCount]['coluna4']  = "Total: ";
                $arRecord[$inCount]['coluna5']  = NroBR ( $totalValida );
                $arRecord[$inCount]['coluna6']  = "";
                $inCount++;
                $totalValida		= 0;
            }
                        if ($totalValida == 0 && $totalAnulada <>0) {
                $arRecord[$inCount]['coluna1']  = "";
                $arRecord[$inCount]['coluna2']  = "";
                $arRecord[$inCount]['coluna3']  = "";
                $arRecord[$inCount]['coluna4']  = "";
                $arRecord[$inCount]['coluna5']  = "";
                $arRecord[$inCount]['coluna6']  = "";
                $inCount++;
                $arRecord[$inCount]['coluna1']  = "";
                $arRecord[$inCount]['coluna2']  = "";
                $arRecord[$inCount]['coluna3']  = "";
                $arRecord[$inCount]['coluna4']  = "Total: ";
                $arRecord[$inCount]['coluna5']  = NroBR ( $totalAnulada );
                $arRecord[$inCount]['coluna6']  = "";
                $inCount++;
                $totalAnulada		= 0;
            }

            if ($entidadeAtual) {
                $arRecord[$inCount]['coluna1']  = "";
                $arRecord[$inCount]['coluna2']  = "";
                $arRecord[$inCount]['coluna3']  = "";
                $arRecord[$inCount]['coluna4']  = "";
                $arRecord[$inCount]['coluna5']  = "";
                $arRecord[$inCount]['coluna6']  = "";
                $inCount++;
                $arRecord[$inCount]['coluna1']  = "Total Geral da Entidade: ";
                $arRecord[$inCount]['coluna2']  = "";
                $arRecord[$inCount]['coluna3']  = "";
                $arRecord[$inCount]['coluna4']  = "";
                $arRecord[$inCount]['coluna5']  = NroBR ( $total );
                $arRecord[$inCount]['coluna6']  = "";

                $arRecordSet[$inCount2] = new RecordSet;
                $arRecordSet[$inCount2]->preenche( $arRecord );
            }
        break;

        CASE 'lei_decreto':
                $totalDecreto       = '0';
                $totalGeral         = '0';
                $totalGeralAnulado  = '0';

                while (!$rsRecordSet->eof()) {
                    $leiDecretoAtual        = $rsRecordSet->getCampo('fundamentacao');
                    $tipoSuplementacaoAtual = $rsRecordSet->getCampo('tipo_suplementacao');
                    $dataAtual              = $rsRecordSet->getCampo('data');
                    $situacaoAtual          = $rsRecordSet->getCampo('situacao');

                    $atuais     = $leiDecretoAtual.$tipoSuplementacaoAtual.$dataAtual.$situacaoAtual;
                    $anteriores = $leiDecretoAnterior.$tipoSuplementacaoAnterior.$dataAnterior.$situacaoAnterior;

                    if ( ($atuais <> $anteriores) && ($anteriores <> "") ) {
            $arRecord4[0]['coluna1']  = "";
            $arRecord4[0]['coluna2']  = "";
            $arRecord4[0]['coluna3']  = "Total:";
            $arRecord4[0]['coluna4']  = $total;
            $arRecord4[0]['coluna5']  = "";

                        if ($leiDecretoAtual <> $leiDecretoAnterior && $leiDecretoAnterior <> "" && $totalDecreto<>'0') {
                            if ($total<>$totalDecreto) {
                    $arRecord4[1]['coluna1']  = "";
                $arRecord4[1]['coluna2']  = "";
                $arRecord4[1]['coluna3']  = "Total Geral do Decreto:";
                $arRecord4[1]['coluna4']  = $totalDecreto;
                $arRecord4[1]['coluna5']  = "";
                            }
                            $totalDecreto='0';
                        }

                            $arRecordSet[$inCount2] = new RecordSet;
                            $arRecordSet[$inCount2]->preenche( $arRecord );

                            $arRecordSet4[$inCount2] = new RecordSet;
                            $arRecordSet4[$inCount2]->preenche( $arRecord4 );
                            $arRecordSet4[$inCount2]->addFormatacao("coluna4","NUMERIC_BR");

                            $inCount2++;
                            $inCount 		= 0;
                            $total		= 0;
                            $arRecord           = array();
                            $arRecord2          = array();
                            $arRecord3          = array();
                            $arRecord4          = array();
                            $inCount3           = 0;
                            $inCount4           = 0;

            }

                    $arRecord[$inCount]['coluna1']  = 'Data: ';
                    $arRecord[$inCount]['coluna2']  = $rsRecordSet->getCampo('data');
                    $arRecord[$inCount]['coluna3']  = 'Fundamentação: ';
                    $arRecord[$inCount]['coluna4']  = $rsRecordSet->getCampo('fundamentacao');
                    $arRecord[$inCount]['coluna5']  = 'Tipo Suplementação:';
                    $arRecord[$inCount]['coluna6']  = $rsRecordSet->getCampo('tipo_suplementacao');
                    $arRecord[$inCount]['coluna7']  = 'Situação: ';
                    $arRecord[$inCount]['coluna8']  = $rsRecordSet->getCampo('situacao');

                    $this->obTOrcamentoSuplementacao->setDado("cod_norma"   ,$rsRecordSet->getCampo('cod_norma'));
                    $this->obTOrcamentoSuplementacao->setDado("cod_tipo"    ,$rsRecordSet->getCampo('cod_tipo'));
                    $this->obTOrcamentoSuplementacao->setDado("data"	    ,$rsRecordSet->getCampo('data'));
                    $this->obTOrcamentoSuplementacao->setDado("situacao"    ,$rsRecordSet->getCampo('situacao'));
                    $this->obTOrcamentoSuplementacao->setDado("cod_entidade",$rsRecordSet->getCampo('cod_entidade'));
                    $obErro = $this->obTOrcamentoSuplementacao->recuperaDotacoesPorDecreto( $rsRecordSet2, $stFiltro, $stOrder );

                    while (!$rsRecordSet2->eof()) {
                        if ($rsRecordSet2->getCampo('tipo')=='reducao') {
                            $arRecord2[$inCount3]['coluna1']  = '';
                            $arRecord2[$inCount3]['coluna2']  = $rsRecordSet2->getCampo('dotacao_formatada');
                            $arRecord2[$inCount3]['coluna3']  = $rsRecordSet2->getCampo('fonte');
                    $arRecord2[$inCount3]['coluna4']  = $rsRecordSet2->getCampo('descricao');
                    $arRecord2[$inCount3]['coluna5']  = $rsRecordSet2->getCampo('valor');
                            $inCount3++;
                        } elseif ($rsRecordSet2->getCampo('tipo')=='suplementacao') {
                            $total = bcadd($total,$rsRecordSet2->getCampo('valor'),2);

                            $arRecord3[$inCount4]['coluna1']  = '';
                            $arRecord3[$inCount4]['coluna2']  = $rsRecordSet2->getCampo('dotacao_formatada');
                            $arRecord3[$inCount4]['coluna3']  = $rsRecordSet2->getCampo('fonte');
                    $arRecord3[$inCount4]['coluna4']  = $rsRecordSet2->getCampo('descricao');
                    $arRecord3[$inCount4]['coluna5']  = $rsRecordSet2->getCampo('valor');
                            $inCount4++;
                        }
                        $rsRecordSet2->proximo();
                    }

                    $totalDecreto   = bcadd($totalDecreto,$total,2);

                    if ($rsRecordSet->getCampo('situacao')=='Válida') {
                        $totalGeral = bcadd($totalGeral,$total,2);
                    } else {
                        $totalGeralAnulado = bcadd($totalGeralAnulado,$total,2);
                    }

                    $arRecordSet2[$inCount2] = new RecordSet;
                    $arRecordSet2[$inCount2]->preenche($arRecord2);
                    $arRecordSet2[$inCount2]->addFormatacao("coluna5","NUMERIC_BR");

                    $arRecordSet3[$inCount2] = new RecordSet;
                    $arRecordSet3[$inCount2]->preenche($arRecord3);
                    $arRecordSet3[$inCount2]->addFormatacao("coluna5","NUMERIC_BR");

                    $inCount++;
                    $leiDecretoAnterior 	= $leiDecretoAtual;
                    $tipoSuplementacaoAnterior  = $tipoSuplementacaoAtual;
                    $dataAnterior               = $dataAtual;
                    $situacaoAnterior           = $situacaoAtual;

                    $rsRecordSet->proximo();
        }

                $inCount1=0;

                if ($leiDecretoAtual) {
                    $inCount++;
                    $arRecord4[$inCount1]['coluna1']  = "";
                    $arRecord4[$inCount1]['coluna2']  = "";
                    $arRecord4[$inCount1]['coluna3']  = "Total:";
                    $arRecord4[$inCount1]['coluna4']  = $total;
                    $arRecord4[$inCount1]['coluna5']  = "";
                    $inCount1++;

                    if ($total<>$totalDecreto) {
                $arRecord4[$inCount1]['coluna1']  = "";
            $arRecord4[$inCount1]['coluna2']  = "";
            $arRecord4[$inCount1]['coluna3']  = "Total Geral do Decreto:";
            $arRecord4[$inCount1]['coluna4']  = $totalDecreto;
            $arRecord4[$inCount1]['coluna5']  = "";
                        $inCount1++;
                    }

                    if ($totalDecreto<>$totalGeral && $totalGeral<>'0') {
                        $arRecord4[$inCount1]['coluna1']  = "";
            $arRecord4[$inCount1]['coluna2']  = "";
            $arRecord4[$inCount1]['coluna3']  = "Total dos Decretos Válidos:";
            $arRecord4[$inCount1]['coluna4']  = $totalGeral;
            $arRecord4[$inCount1]['coluna5']  = "";
                        $inCount1++;
                    }

                    if ($totalDecreto<>$totalGeralAnulado && $totalGeralAnulado<>'0') {
                        $arRecord4[$inCount1]['coluna1']  = "";
            $arRecord4[$inCount1]['coluna2']  = "";
            $arRecord4[$inCount1]['coluna3']  = "Total dos Decretos Anulados:";
            $arRecord4[$inCount1]['coluna4']  = $totalGeralAnulado;
            $arRecord4[$inCount1]['coluna5']  = "";
                    }

                    $arRecordSet[$inCount2] = new RecordSet;
                    $arRecordSet[$inCount2]->preenche( $arRecord );

                    $arRecordSet4[$inCount2] = new RecordSet;
                    $arRecordSet4[$inCount2]->preenche( $arRecord4 );
                    $arRecordSet4[$inCount2]->addFormatacao("coluna4","NUMERIC_BR");
                }
        break;

        CASE 'data':
            $totalSuplementado = 0;
            $totalReduzido     = 0;
            while (!$rsRecordSet->eof()) {
                $valor = $rsRecordSet->getCampo('valor');
                if ($valor > 0) {
                    $totalSuplementado = bcadd($totalSuplementado,$valor,2);
                } else {
                    $totalReduzido = bcadd($totalReduzido,$valor,2);
                }

                $arRecord[$inCount]['coluna1']  = $rsRecordSet->getCampo('data');
                $arRecord[$inCount]['coluna2']  = $rsRecordSet->getCampo('fundamentacao');
                $arRecord[$inCount]['coluna3']  = $rsRecordSet->getCampo('fonte');
                $arRecord[$inCount]['coluna4']  = $rsRecordSet->getCampo('tipo_suplementacao');
                $arRecord[$inCount]['coluna5']  = $rsRecordSet->getCampo('dotacao');
                $arRecord[$inCount]['coluna6']  = NroBR ( $rsRecordSet->getCampo('valor') );
                $inCount++;
                $rsRecordSet->proximo();
            }
            $arRecord2[0]['coluna1']  = "";
            $arRecord2[0]['coluna2']  = "";
            $arRecord2[0]['coluna3']  = "";
            $arRecord2[0]['coluna4']  = "";
            $arRecord2[0]['coluna5']  = "";
            $arRecord2[0]['coluna6']  = "";

            $arRecord2[1]['coluna1']  = "Total Suplementado no Período: ";
            $arRecord2[1]['coluna2']  = "";
            $arRecord2[1]['coluna3']  = "";
            $arRecord2[1]['coluna4']  = "";
            $arRecord2[1]['coluna5']  = "";
            $arRecord2[1]['coluna6']  = NroBR ( $totalSuplementado );

            $arRecord2[2]['coluna1']  = "";
            $arRecord2[2]['coluna2']  = "";
            $arRecord2[2]['coluna3']  = "";
            $arRecord2[2]['coluna4']  = "";
            $arRecord2[2]['coluna5']  = "";
            $arRecord2[2]['coluna6']  = "";

            $arRecord2[3]['coluna1']  = "Total Reduzido no Período: ";
            $arRecord2[3]['coluna2']  = "";
            $arRecord2[3]['coluna3']  = "";
            $arRecord2[3]['coluna4']  = "";
            $arRecord2[3]['coluna5']  = "";
            $arRecord2[3]['coluna6']  = NroBR ( $totalReduzido );

            $arRecord2[4]['coluna1']  = "";
            $arRecord2[4]['coluna2']  = "";
            $arRecord2[4]['coluna3']  = "";
            $arRecord2[4]['coluna4']  = "";
            $arRecord2[4]['coluna5']  = "";
            $arRecord2[4]['coluna6']  = "";

            $arRecordSet = new RecordSet;
             $arRecordSet->preenche( $arRecord );

            $arRecordSet2 = new RecordSet;
             $arRecordSet2->preenche( $arRecord2 );
        break;

        CASE 'dotacao':
            while (!$rsRecordSet->eof()) {
                $dotacaoAtual = $rsRecordSet->getCampo('dotacao');
                if ($dotacaoAtual <> $dotacaoAnterior) {
                    if ($dotacaoAnterior <> "") {

                        $arRecord3[0]['coluna1']  = "Total da Dotação: ";
                        $arRecord3[0]['coluna2']  = $totalReduzido;
                        $arRecord3[0]['coluna3']  = $totalSuplementado;

                        $arRecordSet3[$inCount2] = new RecordSet;
                        $arRecordSet3[$inCount2]->preenche( $arRecord3);
                        $arRecordSet3[$inCount2]->addFormatacao("coluna2","NUMERIC_BR");
                        $arRecordSet3[$inCount2]->addFormatacao("coluna3","NUMERIC_BR");

                        $arRecordSet[$inCount2] = new RecordSet;
                        $arRecordSet[$inCount2]->preenche( $arRecord);
                        $arRecordSet[$inCount2]->addFormatacao("coluna5","NUMERIC_BR");
                        $arRecordSet[$inCount2]->addFormatacao("coluna6","NUMERIC_BR");

                        $inCount 	        = 0;
                        $inCount2++;
                        $totalReduzido      = '0.00';
                        $totalSuplementado  = '0.00';
                        $arRecord   = array();
                    }
                    $arRecord2[0]['coluna1']  = "Dotação:            ";
                    $arRecord2[0]['coluna2']  = $dotacaoAtual;
                    $arRecord2[0]['coluna3']  = "";
                    $arRecord2[0]['coluna4']  = "";
                    $arRecordSet2[$inCount2] = new RecordSet;
                    $arRecordSet2[$inCount2]->preenche( $arRecord2 );
                }
                $totalReduzido      = bcadd($totalReduzido,$rsRecordSet->getCampo('valor_reduzido'),2);
                $totalSuplementado  = bcadd($totalSuplementado,$rsRecordSet->getCampo('valor_suplementado'),2);

                $arRecord[$inCount]['coluna1']  = $rsRecordSet->getCampo('data');
                $arRecord[$inCount]['coluna2']  = $rsRecordSet->getCampo('fundamentacao');
                $arRecord[$inCount]['coluna3']  = $rsRecordSet->getCampo('fonte');
                $arRecord[$inCount]['coluna4']  = $rsRecordSet->getCampo('tipo_suplementacao');
                $arRecord[$inCount]['coluna5']  = $rsRecordSet->getCampo('valor_reduzido');
                $arRecord[$inCount]['coluna6']  = $rsRecordSet->getCampo('valor_suplementado');
                $inCount++;

                $dotacaoAnterior 	= $dotacaoAtual;
                $rsRecordSet->proximo();

            }
            if ($dotacaoAtual) {
                $arRecord3[0]['coluna1']  = "Total da Dotação: ";
                $arRecord3[0]['coluna2']  = $totalReduzido;
                $arRecord3[0]['coluna3']  = $totalSuplementado;

                $arRecordSet[$inCount2] = new RecordSet;
                $arRecordSet[$inCount2]->preenche( $arRecord);
                $arRecordSet[$inCount2]->addFormatacao("coluna5","NUMERIC_BR");
                $arRecordSet[$inCount2]->addFormatacao("coluna6","NUMERIC_BR");

                $arRecordSet3[$inCount2] = new RecordSet;
                $arRecordSet3[$inCount2]->preenche( $arRecord3);
                $arRecordSet3[$inCount2]->addFormatacao("coluna2","NUMERIC_BR");
                $arRecordSet3[$inCount2]->addFormatacao("coluna3","NUMERIC_BR");
            }
        break;

        CASE 'anuladas':
            while (!$rsRecordSet->eof()) {
                $stMotivoTemp = str_replace( chr(10), "", $rsRecordSet->getCampo('motivo') );
                $stMotivoTemp = wordwrap( $stMotivoTemp,70,chr(13) );
                $arMotivoOLD = explode( chr(13), $stMotivoTemp );
                if ($rsRecordSet->getCorrente() == 1) {
                    $inCountQuebra = $inCount;
                }
                $leiDecretoAtual = $rsRecordSet->getCampo('fundamentacao');
                if ($leiDecretoAtual <> $leiDecretoAnterior && $leiDecretoAnterior <> "") {

                    $arRecord4[0]['coluna1']  = "";
                    $arRecord4[0]['coluna2']  = "";
                    $arRecord4[0]['coluna3']  = "";
                    $arRecord4[0]['coluna4']  = "Total Decreto: ";
                    $arRecord4[0]['coluna5']  = $total;

                    $arRecordSet[$inCount2] = new RecordSet;
                    $arRecordSet[$inCount2]->preenche( $arRecord );

                    $arRecordSet4[$inCount2] = new RecordSet;
                    $arRecordSet4[$inCount2]->preenche( $arRecord4 );
                    $arRecordSet4[$inCount2]->addFormatacao("coluna5","NUMERIC_BR");

                    $inCount2			++;
                    $inCount 			= 0;
                    $total				= 0;
                    $arRecord           = array();
                    $arRecord2          = array();
                    $arRecord3          = array();
                    $inCount3           = 0;
                    $inCount4           = 0;

                }
                $arRecord[$inCount]['coluna1']  = $rsRecordSet->getCampo('data');
                $arRecord[$inCount]['coluna2']  = $rsRecordSet->getCampo('data_anulacao');
                $arRecord[$inCount]['coluna3']  = $rsRecordSet->getCampo('fundamentacao');
                $arRecord[$inCount]['coluna4']  = $rsRecordSet->getCampo('tipo_suplementacao');
                foreach ($arMotivoOLD as $stMotivoTemp) {
                    $arRecord[$inCountQuebra]['coluna5'] = $stMotivoTemp;
                $inCountQuebra++;
                }

                $this->obTOrcamentoSuplementacao->setDado("situacao"    ,"Anulada");
                $this->obTOrcamentoSuplementacao->setDado("cod_norma"	,$rsRecordSet->getCampo('cod_norma'));
                $this->obTOrcamentoSuplementacao->setDado("data"	    ,$rsRecordSet->getCampo('data'));

                $obErro = $this->obTOrcamentoSuplementacao->recuperaDotacoesPorDecreto( $rsRecordSet2, $stFiltro, $stOrder );

                while (!$rsRecordSet2->eof()) {
                    if ($rsRecordSet2->getCampo('tipo')=='reducao') {
                        $arRecord2[$inCount3]['coluna1']  = '';
                        $arRecord2[$inCount3]['coluna2']  = $rsRecordSet2->getCampo('dotacao_formatada');
                        $arRecord2[$inCount3]['coluna3']  = $rsRecordSet2->getCampo('fonte');
                        $arRecord2[$inCount3]['coluna4']  = $rsRecordSet2->getCampo('descricao');
                        $arRecord2[$inCount3]['coluna5']  = $rsRecordSet2->getCampo('valor');
                        $inCount3++;
                    } elseif ($rsRecordSet2->getCampo('tipo')=='suplementacao') {
                        $total = bcadd($total,$rsRecordSet2->getCampo('valor'),2);

                        $arRecord3[$inCount4]['coluna1']  = '';
                        $arRecord3[$inCount4]['coluna2']  = $rsRecordSet2->getCampo('dotacao_formatada');
                        $arRecord3[$inCount4]['coluna3']  = $rsRecordSet2->getCampo('fonte');
                        $arRecord3[$inCount4]['coluna4']  = $rsRecordSet2->getCampo('descricao');
                        $arRecord3[$inCount4]['coluna5']  = $rsRecordSet2->getCampo('valor');
                        $inCount4++;
                    }
                    $rsRecordSet2->proximo();
                }
                $arRecordSet2[$inCount2] = new RecordSet;
                $arRecordSet2[$inCount2]->preenche($arRecord2);
                $arRecordSet2[$inCount2]->addFormatacao("coluna5","NUMERIC_BR");

                $arRecordSet3[$inCount2] = new RecordSet;
                $arRecordSet3[$inCount2]->preenche($arRecord3);
                $arRecordSet3[$inCount2]->addFormatacao("coluna5","NUMERIC_BR");

                $inCount = $inCountQuebra-1;
                $inCountQuebra=0;
                $inCount++;
                $leiDecretoAnterior 	= $leiDecretoAtual;
                $rsRecordSet->proximo();
            }

            if ($leiDecretoAtual) {
                $inCount++;
                $arRecord4[0]['coluna1']  = "";
                $arRecord4[0]['coluna2']  = "";
                $arRecord4[0]['coluna3']  = "";
                $arRecord4[0]['coluna4']  = "Total Decreto: ";
                $arRecord4[0]['coluna5']  = $total;

                $arRecordSet[$inCount2] = new RecordSet;
                $arRecordSet[$inCount2]->preenche( $arRecord );

                $arRecordSet4[$inCount2] = new RecordSet;
                $arRecordSet4[$inCount2]->preenche( $arRecord4 );
                $arRecordSet4[$inCount2]->addFormatacao("coluna5","NUMERIC_BR");

            }
        break;

        CASE 'resumo':

            while (!$rsRecordSet->eof()) {
                $valor    = $rsRecordSet->getCampo('valor_su');
                $valor_re = $rsRecordSet->getCampo('valor_re');
                $total    = bcadd($total,$valor,2);
                $total_re = bcadd($total_re,$valor_re,2);

                if ($rsRecordSet->getCampo('valor_su') != 0.00) {
                    $arRecord[$inCount]['coluna1']  = $rsRecordSet->getCampo('tipo_suplementacao');
                    $arRecord[$inCount]['coluna2']  = $rsRecordSet->getCampo('valor_su');
                }
                $inCount++;
                $rsRecordSet->proximo();
            }
            $arRecord2[0]['coluna1']  = "Sub-total: ";
            $arRecord2[0]['coluna2']  = $total;

            $arRecord3[0]['coluna1'] = "Reduções Orçamentárias no Período:";
            $arRecord3[0]['coluna2'] =  $total_re;

            $arRecord4[0]['coluna1'] = "Acréscimo Orçamentário:";
            $arRecord4[0]['coluna2'] = $total - $total_re;

            $arRecordSet = new RecordSet;
             $arRecordSet->preenche( $arRecord );
            $arRecordSet->addFormatacao("coluna2","NUMERIC_BR");

            $arRecordSet2 = new RecordSet;
             $arRecordSet2->preenche( $arRecord2 );
            $arRecordSet2->addFormatacao("coluna2","NUMERIC_BR");

            $arRecordSet3 = new RecordSet;
            $arRecordSet3->preenche( $arRecord3 );
            $arRecordSet3->addFormatacao("coluna2","NUMERIC_BR");

            $arRecordSet4 = new RecordSet;
            $arRecordSet4->preenche( $arRecord4 );
            $arRecordSet4->addFormatacao("coluna2","NUMERIC_BR");

        break;

    }
}
}
}
