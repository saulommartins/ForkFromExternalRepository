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
    * Classe de Regra do Relatório de Resumo de Despesa
    * Data de Criação   : 05/12/2005

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Regra

    $Revision: 31914 $
    $Name$
    $Autor: $
    $Date: 2008-03-11 11:03:10 -0300 (Ter, 11 Mar 2008) $

    * Casos de uso: uc-02.04.16
*/

/*
$Log$
Revision 1.10  2007/09/03 13:09:43  vitor
Ticket#10014#

Revision 1.9  2006/12/05 21:10:55  cleisson
Bug #7551#

Revision 1.8  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO   );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"             );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Negócios Transferencias Bancarias
    * @author Desenvolvedor: Jose Eduardo Porto
*/
class RTesourariaRelatorioResumoDespesa extends PersistenteRelatorio
{
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stEntidade;
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
    * @var Integer
    * @access Private
*/
var $inDespesaInicial;
/**
    * @var Integer
    * @access Private
*/
var $inDespesaFinal;
/**
    * @var Integer
    * @access Private
*/
var $inContaBancoInicial;
/**
    * @var Integer
    * @access Private
*/
var $inContaBancoFinal;
/**
    * @var String
    * @access Private
*/
var $stTipoRelatorio;
/**
    * @var Integer
    * @access Private
*/
var $inCodRecurso;

var $stDestinacaoRecurso;
var $inCodDetalhamento;

/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setEntidade($valor) { $this->stEntidade           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataInicial($valor) { $this->stDataInicial      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataFinal($valor) { $this->stDataFinal      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setFiltro($valor) { $this->stFiltro      = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setDespesaInicial($valor) { $this->inDespesaInicial= $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setDespesaFinal($valor) { $this->inDespesaFinal= $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setContaBancoInicial($valor) { $this->inContaBancoInicial= $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setContaBancoFinal($valor) { $this->inContaBancoFinal= $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setTipoRelatorio($valor) { $this->stTipoRelatorio      = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodRecurso($valor) { $this->inCodRecurso= $valor; }
function setDestinacaoRecurso($valor) { $this->stDestinacaoRecurso = $valor; }
function setCodDetalhamento($valor) { $this->inCodDetalhamento = $valor; }

/*
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                      }
/*
    * @access Public
    * @return String
*/
function getEntidade() { return $this->stEntidade;                      }
/*
    * @access Public
    * @return String
*/
function getDataInicial() { return $this->stDataInicial;                      }
/*
    * @access Public
    * @return String
*/
function getDataFinal() { return $this->stDataFinal;                      }
/*
    * @access Public
    * @return String
*/
function getFiltro() { return $this->stFiltro;                      }
/*and pc.cod_estrutural <>
    * @access Public
    * @return Integer
*/
function getDespesaInicial() { return $this->inDespesaInicial;   }
/*
    * @access Public
    * @return Integer
*/
function getDespesaFinal() { return $this->inDespesaFinal;                      }
/*
    * @access Public
    * @return Integer
*/
function getContaBancoInicial() { return $this->inContaBancoInicial;   }
/*
    * @access Public
    * @return Integer
*/
function getContaBancoFinal() { return $this->inContaBancoFinal;                      }
/*
    * @access Public
    * @return String
*/
function getTipoRelatorio() { return $this->stTipoRelatorio;                      }
/*
    * @access Public
    * @return Integer
*/
function getCodRecurso() { return $this->inCodRecurso;                      }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaRelatorioResumoDespesa()
{
    $this->obRTesourariaBoletim            = new RTesourariaBoletim;
    $this->obRRelatorio                    = new RRelatorio;
    $this->obRTesourariaBoletim->addArrecadacao();
    $this->obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );

}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    include_once( CAM_GF_TES_MAPEAMENTO."FTesourariaResumoDespesa.class.php");
    $obFTesourariaResumoDespesa = new FTesourariaResumoDespesa;

    $obFTesourariaResumoDespesa->setDado("stEntidade"           ,$this->getEntidade());
    $obFTesourariaResumoDespesa->setDado("stExercicio"          ,$this->getExercicio());
    $obFTesourariaResumoDespesa->setDado("stDataInicial"        ,$this->getDataInicial());
    $obFTesourariaResumoDespesa->setDado("stDataFinal"          ,$this->getDataFinal());
    $obFTesourariaResumoDespesa->setDado("stTipoRelatorio"      ,$this->getTipoRelatorio());
    $obFTesourariaResumoDespesa->setDado("inDespesaInicial"     ,$this->getDespesaInicial());
    $obFTesourariaResumoDespesa->setDado("inDespesaFinal"       ,$this->getDespesaFinal());
    $obFTesourariaResumoDespesa->setDado("inContaBancoInicial"  ,$this->getContaBancoInicial());
    $obFTesourariaResumoDespesa->setDado("inContaBancoFinal"    ,$this->getContaBancoFinal());
    $obFTesourariaResumoDespesa->setDado("inCodRecurso"         ,$this->getCodRecurso());
    $obFTesourariaResumoDespesa->setDado("stDestinacaoRecurso"  ,$this->stDestinacaoRecurso );
    $obFTesourariaResumoDespesa->setDado("inCodDetalhamento"    ,$this->inCodDetalhamento );
    $obFTesourariaResumoDespesa->setDado("inNumCgm"             ,$this->obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->getNumCGM());
    $obFTesourariaResumoDespesa->setDado("boUtilizaEstruturalTCE", 'false' );

    if (Sessao::getExercicio() > '2012') {
        $obFTesourariaResumoDespesa->setDado("boUtilizaEstruturalTCE", 'true' );
    }

    $obErro = $obFTesourariaResumoDespesa->recuperaTodos( $rsResumoDespesa, $stFiltro, "ORDER BY complemento ASC" );

    $arResumoDespesa = array();

    if ($rsResumoDespesa->getNumLinhas() > -1) {

        switch ($this->getTipoRelatorio()) {
            case "B":
                    $label_despesa  = "Banco:";
                    $nome_campo     = "Despesas";
                    $label_subTotal = "Total Geral das Despesas para este Banco";
                break;
            case "R":
                    $label_despesa  = "Recurso:";
                    $nome_campo     = "Código";
                    $label_subTotal = "Total Geral das Despesas para este Recurso";
                break;
            case "E":
                    $label_despesa  = "Entidade:";
                    $nome_campo     = "Código";
                    $label_subTotal = "Total Geral das Despesas por Entidade";
                break;
            default:
                    $nome_campo     = "Despesa";
                    $label_subTotal = "Total Geral das Despesas";
        }
        $inCount = 0;
        $subTotalPago = 0;
        $subTotalEstornado = 0;
        $totalGeralPago = 0;
        $totalGeralEstornado = 0;
        $complemento = $rsResumoDespesa->getCampo("complemento");

        while (!$rsResumoDespesa->eof()) {
            $arResumoDespesa[$inCount]["despesa"]      = $label_despesa;
            $arResumoDespesa[$inCount]["descricao"]    = $rsResumoDespesa->getCampo("complemento");
            $arResumoDespesa[$inCount]["tipo_despesa"] = "";
            $arResumoDespesa[$inCount]["pago"]         = "";
            $arResumoDespesa[$inCount]["estornado"]    = "";
            $arResumoDespesa[$inCount]["total"]        = "";

            $arResumoDespesa[$inCount+1]["despesa"]      = $nome_campo;
            $arResumoDespesa[$inCount+1]["descricao"]    = "Descrição";
            $arResumoDespesa[$inCount+1]["tipo_despesa"] = "Tipo";
            $arResumoDespesa[$inCount+1]["pago"]         = "Vlr. Pago";
            $arResumoDespesa[$inCount+1]["estornado"]    = "Vlr. Estorn.";
            $arResumoDespesa[$inCount+1]["total"]        = "Total";

            $inCount += 2;
            if ($complemento != $rsResumoDespesa->getCampo("complemento")) {
                $complemento = $rsResumoDespesa->getCampo("complemento");
            }

            $subTotalPago = 0;
            $subTotalEstornado = 0;
            $totalGeralPago = 0;
            $totalGeralEstornado = 0;

            $subTotalPagoE = 0;
            $subTotalEstornadoE = 0;
            $totalGeralPagoE = 0;
            $totalGeralEstornadoE = 0;

            while ($complemento == $rsResumoDespesa->getCampo("complemento")) {
                if ($rsResumoDespesa->getCampo('tipo_despesa') == "O") {
                    $arResumoDespesa[$inCount]["despesa"]      = $rsResumoDespesa->getCampo("despesa");
                    $arResumoDespesa[$inCount]["tipo_despesa"]      = $rsResumoDespesa->getCampo("tipo_despesa");
                    $arResumoDespesa[$inCount]["pago"]         = number_format($rsResumoDespesa->getCampo("pago"),"2",",",".");
                    $arResumoDespesa[$inCount]["estornado"]    = number_format($rsResumoDespesa->getCampo("estornado"),"2",",",".");
                    $total = $rsResumoDespesa->getCampo("pago") - $rsResumoDespesa->getCampo("estornado");
                    $arResumoDespesa[$inCount]["total"]        = number_format($total,2,',','.');

                    $stDescricao = str_replace( chr(10) , "", $rsResumoDespesa->getCampo("descricao") );
                    $stDescricao = wordwrap( $stDescricao , 40, chr(13) );
                    $arDescricao = explode( chr(13), $stDescricao );
                    foreach ($arDescricao as $stDescricao) {
                        $arResumoDespesa[$inCount]["descricao"]  = $stDescricao;
                        $inCount++;
                    }

                    $subTotalPago += $rsResumoDespesa->getCampo("pago");
                    $subTotalEstornado += $rsResumoDespesa->getCampo("estornado");
                    $totalGeralPago += $rsResumoDespesa->getCampo("pago");
                    $totalGeralEstornado += $rsResumoDespesa->getCampo("estornado");
                }
                if ($rsResumoDespesa->getCampo('tipo_despesa') == "E") {
                    $arResumoDespesa[$inCount]["despesa"]      = $rsResumoDespesa->getCampo("despesa");
                    $arResumoDespesa[$inCount]["tipo_despesa"] = $rsResumoDespesa->getCampo("tipo_despesa");
                    $arResumoDespesa[$inCount]["pago"]         = number_format($rsResumoDespesa->getCampo("pago"),"2",",",".");
                    $arResumoDespesa[$inCount]["estornado"]    = number_format($rsResumoDespesa->getCampo("estornado"),"2",",",".");
                    $total = $rsResumoDespesa->getCampo("pago") - $rsResumoDespesa->getCampo("estornado");
                    $arResumoDespesa[$inCount]["total"]        = number_format($total,2,',','.');

                    $stDescricao = str_replace( chr(10) , "", $rsResumoDespesa->getCampo("descricao") );
                    $stDescricao = wordwrap( $stDescricao , 40, chr(13) );
                    $arDescricao = explode( chr(13), $stDescricao );

                    foreach ($arDescricao as $stDescricao) {
                        $arResumoDespesa[$inCount]["descricao"]  = $stDescricao;
                        $inCount++;
                    }

                    $subTotalPagoE += $rsResumoDespesa->getCampo("pago");
                    $subTotalEstornadoE += $rsResumoDespesa->getCampo("estornado");
                    $totalGeralPagoE += $rsResumoDespesa->getCampo("pago");
                    $totalGeralEstornadoE += $rsResumoDespesa->getCampo("estornado");
                }
                $rsResumoDespesa->proximo();
            }

            $subTotalDespesaPago      =  $subTotalPago + $subTotalPagoE;
            $subTotalDespesaEstornado =  $subTotalPagoEstornado + $subTotalPagoEstornadoE;
            $subTotalDespesaGeral     =  $subTotalDespesaPago - $subTotalDespesaEstornado;

            $arResumoDespesa[$inCount]["despesa"]        = "";
            $arResumoDespesa[$inCount]["descricao"]      = "";
            $arResumoDespesa[$inCount]["tipo_despesa"]   = "";
            $arResumoDespesa[$inCount]["pago"]           = "";
            $arResumoDespesa[$inCount]["estornado"]      = "";
            $arResumoDespesa[$inCount]["total"]          = "";
            $inCount++;

            $arResumoDespesa[$inCount]["despesa"]        = "";
            $arResumoDespesa[$inCount]["descricao"]      = "Total das Despesas Orçamentárias ";
            $arResumoDespesa[$inCount]["tipo_despesa"]   = "";
            $arResumoDespesa[$inCount]["pago"]           = number_format($subTotalPago,"2",",",".");
            $arResumoDespesa[$inCount]["estornado"]      = number_format($subTotalEstornado,"2",",",".");
            $arResumoDespesa[$inCount]["total"]          = number_format($subTotalPago - $subTotalEstornado,"2",",",".");
            $inCount++;

            $arResumoDespesa[$inCount]["despesa"]        = "";
            $arResumoDespesa[$inCount]["descricao"]      = "Total das Despesas Extra-Orçamentárias ";
            $arResumoDespesa[$inCount]["tipo_despesa"]   = "";
            $arResumoDespesa[$inCount]["pago"]           = number_format($subTotalPagoE,"2",",",".");
            $arResumoDespesa[$inCount]["estornado"]      = number_format($subTotalEstornadoE,"2",",",".");
            $arResumoDespesa[$inCount]["total"]          = number_format($subTotalPagoE - $subTotalEstornadoE,"2",",",".");
            $inCount++;

            $arResumoDespesa[$inCount]["despesa"]        = "";
            $arResumoDespesa[$inCount]["descricao"]      = 'Total das Despesas';
            $arResumoDespesa[$inCount]["tipo_despesa"]   = "";
            $arResumoDespesa[$inCount]["pago"]           = number_format($subTotalPago + $subTotalPagoE,"2",",",".");
            $arResumoDespesa[$inCount]["estornado"]      = number_format($subTotalEstornado + $subTotalEstornadoE,"2",",",".");
            $arResumoDespesa[$inCount]["total"]          = number_format(($subTotalPago + $subTotalPagoE) - ( $subTotalEstornado + $subTotalEstornadoE) ,"2",",",".");

            $totalGeralOrcamentario += $subTotalPago - $subTotalEstornado;
            $totalEstornadoOrcamentario += $subTotalEstornado;
            $totalPagoOrcamentario += $subTotalPago;

            $totalGeralExtra += $subTotalPagoE - $subTotalEstornadoE;
            $totalEstornadoExtra += $subTotalEstornadoE;
            $totalPagoExtra += $subTotalPagoE;

            $totalGeral += ($subTotalPago + $subTotalPagoE) - ( $subTotalEstornado + $subTotalEstornadoE);
            $totalEstornado += $subTotalEstornado + $subTotalEstornadoE;
            $totalPago += $subTotalPago + $subTotalPagoE;

            $inCount++;

            $arResumoDespesa[$inCount]["despesa"]        = "";
            $arResumoDespesa[$inCount]["descricao"]      = "";
            $arResumoDespesa[$inCount]["tipo_despesa"]   = "";
            $arResumoDespesa[$inCount]["pago"]           = "";
            $arResumoDespesa[$inCount]["estornado"]      = "";
            $arResumoDespesa[$inCount]["total"]          = "";
            $inCount++;
        }

        $arResumoDespesa[$inCount]["despesa"]      = "";
        $arResumoDespesa[$inCount]["tipo_despesa"] = "";
        $arResumoDespesa[$inCount]["descricao"]    = "";
        $arResumoDespesa[$inCount]["pago"]         = "";
        $arResumoDespesa[$inCount]["estornado"]    = "";
        $arResumoDespesa[$inCount]["total"]        = "";
        $inCount++;

        $arResumoDespesa[$inCount]["despesa"]      = "";
        $arResumoDespesa[$inCount]["tipo_despesa"] = "";
        $arResumoDespesa[$inCount]["descricao"]    = "Total Geral das Despesas Orçamentárias";
        $arResumoDespesa[$inCount]["pago"]         = number_format($totalPagoOrcamentario,"2",",",".");
        $arResumoDespesa[$inCount]["estornado"]    = number_format($totalEstornadoOrcamentario,"2",",",".");
        $arResumoDespesa[$inCount]["total"]        = number_format($totalGeralOrcamentario,"2",",",".");
        $inCount++;

        $arResumoDespesa[$inCount]["despesa"]      = "";
        $arResumoDespesa[$inCount]["tipo_despesa"] = "";
        $arResumoDespesa[$inCount]["descricao"]    = "Total Geral das Despesas Extra-Orçamentárias";
        $arResumoDespesa[$inCount]["pago"]         = number_format($totalPagoExtra,"2",",",".");
        $arResumoDespesa[$inCount]["estornado"]    = number_format($totalEstornadoExtra,"2",",",".");
        $arResumoDespesa[$inCount]["total"]        = number_format($totalGeralExtra,"2",",",".");

        $arResumoDespesa[$inCount+1]["despesa"]      = "";
        $arResumoDespesa[$inCount+1]["descricao"]    = "Total Geral das Despesas";
        $arResumoDespesa[$inCount+1]["tipo_despesa"] = "";
        $arResumoDespesa[$inCount+1]["pago"]        = number_format($totalPago,"2",",",".");
        $arResumoDespesa[$inCount+1]["estornado"]   = number_format($totalEstornado,"2",",",".");
        $arResumoDespesa[$inCount+1]["total"]       = number_format($totalGeral,"2",",",".");
    }

    $obErro = $obFTesourariaResumoDespesa->recuperaBoletimDespesa( $rsBoletimDespesa, $stFiltro, $stOrder );
    $rsResumoDespesa  = new RecordSet;
    $rsResumoDespesa->preenche($arResumoDespesa);
    $rsRecordSet = array( $rsResumoDespesa, $rsBoletimDespesa );

    return $obErro;
}

}
