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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 15/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Regra

    $Revision: 30805 $
    $Name$
    $Autor:$
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.03.11
*/

/*
$Log$
Revision 1.8  2007/08/08 19:45:42  cako
Bug#9819#

Revision 1.7  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO);
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php");
include_once( CAM_FW_PDF."RRelatorio.class.php"           );

/**
    * Classe de Regra de Negócios Razão por Credor
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class REmpenhoRelatorioRazaoCredor extends PersistenteRelatorio
{
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
function getCodEntidade() { return $this->inCodEntidade;                }
/**
     * @access Public
     * @return Object
*/
function getExercicio() { return $this->inExercicio;                  }
/**
     * @access Public
     * @return Object
*/
function REmpenhoRelatorioRazaoCredor()
{
    $this->obREmpenhoEmpenho            = new REmpenhoEmpenho;
    $this->obRRelatorio                  = new RRelatorio;
    $this->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    include_once( CAM_GF_EMP_MAPEAMENTO."FEmpenhoRazaoCredor.class.php" );
    $obFEmpenhoRazaoCredor          = new FEmpenhoRazaoCredor;

    $stFiltro = "";
    if ( $this->getCodEntidade() ) {
        $stEntidade .= $this->getCodEntidade();
    } else {
        $this->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades );
        while ( !$rsEntidades->eof() ) {
            $stEntidade .= $rsEntidades->getCampo( 'cod_entidade' ).",";
            $rsEntidades->proximo();
        }
        $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
        $stEntidade = $stEntidade;
    }

    $obFEmpenhoRazaoCredor->setDado("exercicioEmpenho",$this->getExercicio());
    $obFEmpenhoRazaoCredor->setDado("exercicio", Sessao::getExercicio() );
    $obFEmpenhoRazaoCredor->setDado("stEntidade",$this->getCodEntidade());
    $obFEmpenhoRazaoCredor->setDado("inOrgao", $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());
    $obFEmpenhoRazaoCredor->setDado("inUnidade",$this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade());
    $obFEmpenhoRazaoCredor->setDado("stElementoDespesa",$this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural() );
    $obFEmpenhoRazaoCredor->setDado("inRecurso",$this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso());
    $obFEmpenhoRazaoCredor->setDado("stDestinacaoRecurso",$this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso());
    $obFEmpenhoRazaoCredor->setDado("inCodDetalhamento",$this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento());
    $obFEmpenhoRazaoCredor->setDado("inCGM",$this->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->getNumCGM());
    $obErro = $obFEmpenhoRazaoCredor->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount            = 0;
    $inTotal            = 0;
    $inTotalGeral       = 0;
    $arRecord           = array();
    $dtAtual            = "";
    $mostraData         = true;

    while ( !$rsRecordSet->eof() ) {
        $exercicio = $rsRecordSet->getCampo('exercicio');
        $data = $rsRecordSet->getCampo('data');

        if (($dtAtual <> $data) and $inCount>0) {
           $dtAtual = $data;
           $mostraData=true;

           //MONTA TOTALIZADOR GERAL
           $arRecord[$inCount]['nivel']              = 2;
           $arRecord[$inCount]['data']               = "";
           $arRecord[$inCount]['empenho']            = "";
           $arRecord[$inCount]['despesa']            = "TOTAL DO DIA";
           $arRecord[$inCount]['empenhado']          = number_format( $nuTotalDiaEmpenhado, 2, ',', '.' );
           $arRecord[$inCount]['anulado']            = number_format( $nuTotalDiaAnulado, 2, ',', '.' );
           $arRecord[$inCount]['liquidado']          = number_format( $nuTotalDiaLiquidado, 2, ',', '.' );
           $arRecord[$inCount]['pago']               = number_format( $nuTotalDiaPago, 2, ',', '.' );
           $arRecord[$inCount]['pagar_liquidado']    = number_format($nuTotalDiaLiquidado - $nuTotalDiaPago, 2, ',', '.' );
           $arRecord[$inCount]['pagar']              = number_format(($nuTotalDiaEmpenhado - $nuTotalDiaAnulado) - $nuTotalDiaPago, 2, ',', '.' );

           $inCount++;

           $arRecord[$inCount]['nivel']              = 1;
           $arRecord[$inCount]['data']               = "";
           $arRecord[$inCount]['empenho']            = "";
           $arRecord[$inCount]['despesa']            = "";
           $arRecord[$inCount]['empenhado']          = "";
           $arRecord[$inCount]['anulado']            = "";
           $arRecord[$inCount]['liquidado']          = "";
           $arRecord[$inCount]['pago']               = "";
           $arRecord[$inCount]['pagar_liquidado']    = "";
           $arRecord[$inCount]['pagar']              = "";

           $inCount++;

           $nuTotalDiaEmpenhado   = "";
           $nuTotalDiaAnulado     = "";
           $nuTotalDiaLiquidado   = "";
           $nuTotalDiaPago        = "";

        }

        if (($exercicioAtual <> $exercicio) and $inCount>0) {
            $exercicioAtual = $exercicio;

            //MONTA TOTALIZADOR GERAL
            $arRecord[$inCount]['nivel']              = 2;
            $arRecord[$inCount]['data']               = "";
            $arRecord[$inCount]['empenho']            = "";
            $arRecord[$inCount]['despesa']            = "TOTAL DO EXERCICIO";
            $arRecord[$inCount]['empenhado']          = number_format( $nuTotalEmpenhado, 2, ',', '.' );
            $arRecord[$inCount]['anulado']            = number_format( $nuTotalAnulado, 2, ',', '.' );
            $arRecord[$inCount]['liquidado']          = number_format( $nuTotalLiquidado, 2, ',', '.' );
            $arRecord[$inCount]['pago']               = number_format( $nuTotalPago, 2, ',', '.' );
            $arRecord[$inCount]['pagar_liquidado']    = number_format($nuTotalLiquidado - $nuTotalPago, 2, ',', '.' );
            $arRecord[$inCount]['pagar']              = number_format(($nuTotalEmpenhado - $nuTotalAnulado) - $nuTotalPago, 2, ',', '.' );

            $inCount++;

            $arRecord[$inCount]['nivel']              = 1;
            $arRecord[$inCount]['data']               = "";
            $arRecord[$inCount]['empenho']            = "";
            $arRecord[$inCount]['despesa']            = "";
            $arRecord[$inCount]['empenhado']          = "";
            $arRecord[$inCount]['anulado']            = "";
            $arRecord[$inCount]['liquidado']          = "";
            $arRecord[$inCount]['pago']               = "";
            $arRecord[$inCount]['pagar_liquidado']    = "";
            $arRecord[$inCount]['pagar']              = "";

            $inCount++;

            $arRecord[$inCount]['nivel']              = 1;
            $arRecord[$inCount]['data']               = "";
            $arRecord[$inCount]['empenho']            = "";
            $arRecord[$inCount]['despesa']            = "";
            $arRecord[$inCount]['empenhado']          = "";
            $arRecord[$inCount]['anulado']            = "";
            $arRecord[$inCount]['liquidado']          = "";
            $arRecord[$inCount]['pago']               = "";
            $arRecord[$inCount]['pagar_liquidado']    = "";
            $arRecord[$inCount]['pagar']              = "";

            $inCount++;

            $nuTotalGeralEmpenhado  = $nuTotalGeralEmpenhado + $nuTotalEmpenhado;
            $nuTotalGeralAnulado    = $nuTotalGeralAnulado + $nuTotalAnulado;
            $nuTotalGeralLiquidado  = $nuTotalGeralLiquidado + $nuTotalLiquidado;
            $nuTotalGeralPago       = $nuTotalGeralPago + $nuTotalPago;

            $nuTotalEmpenhado   = 0;
            $nuTotalAnulado     = 0;
            $nuTotalLiquidado   = 0;
            $nuTotalPago        = 0;

        //    $mostra     = true;
        }

        $arRecord[$inCount]['nivel']              = 1;
        if($mostraData)
            $arRecord[$inCount]['data']              = $rsRecordSet->getCampo('data');
        else
            $arRecord[$inCount]['data']              = "";
        $arRecord[$inCount]['empenho']            = $rsRecordSet->getCampo('entidade') . " - " . $rsRecordSet->getCampo('empenho') . "/" . $rsRecordSet->getCampo('exercicio');
        $arRecord[$inCount]['despesa']            = $rsRecordSet->getCampo('despesa');
        $arRecord[$inCount]['empenhado']          = number_format($rsRecordSet->getCampo('empenhado'), 2, ',', '.' );
        $arRecord[$inCount]['anulado']            = number_format($rsRecordSet->getCampo('anulado'), 2, ',', '.' );
        $arRecord[$inCount]['liquidado']          = number_format($rsRecordSet->getCampo('liquidado'), 2, ',', '.' );
        $arRecord[$inCount]['pago']               = number_format($rsRecordSet->getCampo('pago'), 2, ',', '.' );
        $arRecord[$inCount]['pagar_liquidado']    = number_format($rsRecordSet->getCampo('liquidado') - $rsRecordSet->getCampo('pago'), 2, ',', '.' );
        $arRecord[$inCount]['pagar']              = number_format(($rsRecordSet->getCampo('empenhado') - $rsRecordSet->getCampo('anulado')) - $rsRecordSet->getCampo('pago'), 2, ',', '.' );

        $nuTotalEmpenhado   = $nuTotalEmpenhado + $rsRecordSet->getCampo('empenhado');
        $nuTotalAnulado     = $nuTotalAnulado   + $rsRecordSet->getCampo('anulado');
        $nuTotalLiquidado   = $nuTotalLiquidado + $rsRecordSet->getCampo('liquidado');
        $nuTotalPago        = $nuTotalPago      + $rsRecordSet->getCampo('pago');

        $nuTotalDiaEmpenhado   = $nuTotalDiaEmpenhado + $rsRecordSet->getCampo('empenhado');
        $nuTotalDiaAnulado     = $nuTotalDiaAnulado   + $rsRecordSet->getCampo('anulado');
        $nuTotalDiaLiquidado   = $nuTotalDiaLiquidado + $rsRecordSet->getCampo('liquidado');
        $nuTotalDiaPago        = $nuTotalDiaPago      + $rsRecordSet->getCampo('pago');

        if($inCount == 0)
            $dtAtual = $data;

        if($inCount == 0)
            $exercicioAtual = $exercicio;

        $inCount++;
        $mostraData = false;
        $rsRecordSet->proximo();

    }

    if ($inCount>0) {
        //MONTA TOTALIZADOR GERAL
        $arRecord[$inCount]['nivel']              = 2;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = "TOTAL DO EXERCICIO";
        $arRecord[$inCount]['empenhado']          = number_format( $nuTotalEmpenhado, 2, ',', '.' );
        $arRecord[$inCount]['anulado']            = number_format( $nuTotalAnulado, 2, ',', '.' );
        $arRecord[$inCount]['liquidado']          = number_format( $nuTotalLiquidado, 2, ',', '.' );
        $arRecord[$inCount]['pago']               = number_format( $nuTotalPago, 2, ',', '.' );
        $arRecord[$inCount]['pagar_liquidado']    = number_format( $nuTotalLiquidado - $nuTotalPago, 2, ',', '.' );
        $arRecord[$inCount]['pagar']              = number_format(($nuTotalEmpenhado - $nuTotalAnulado) - $nuTotalPago, 2, ',', '.' );

        $nuTotalGeralEmpenhado  = $nuTotalGeralEmpenhado + $nuTotalEmpenhado;
        $nuTotalGeralAnulado    = $nuTotalGeralAnulado + $nuTotalAnulado;
        $nuTotalGeralLiquidado  = $nuTotalGeralLiquidado + $nuTotalLiquidado;
        $nuTotalGeralPago       = $nuTotalGeralPago + $nuTotalPago;

        $inCount++;

        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = "";
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";

        $inCount++;

        //MONTA TOTALIZADOR GERAL
        $arRecord[$inCount]['nivel']              = 2;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = "TOTAL GERAL";
        $arRecord[$inCount]['empenhado']          = number_format( $nuTotalGeralEmpenhado, 2, ',', '.' );
        $arRecord[$inCount]['anulado']            = number_format( $nuTotalGeralAnulado, 2, ',', '.' );
        $arRecord[$inCount]['liquidado']          = number_format( $nuTotalGeralLiquidado, 2, ',', '.' );
        $arRecord[$inCount]['pago']               = number_format( $nuTotalGeralPago, 2, ',', '.' );
        $arRecord[$inCount]['pagar_liquidado']    = number_format( $nuTotalGeralLiquidado - $nuTotalGeralPago, 2, ',', '.' );
        $arRecord[$inCount]['pagar']              = number_format(($nuTotalGeralEmpenhado - $nuTotalGeralAnulado) - $nuTotalGeralPago, 2, ',', '.' );

        $inCount++;
    }

    $arRecord[$inCount]['nivel']              = 1;
    $arRecord[$inCount]['data']               = "";
    $arRecord[$inCount]['empenho']            = "";
    $arRecord[$inCount]['despesa']            = "";
    $arRecord[$inCount]['empenhado']          = "";
    $arRecord[$inCount]['anulado']            = "";
    $arRecord[$inCount]['liquidado']          = "";
    $arRecord[$inCount]['pago']               = "";
    $arRecord[$inCount]['pagar_liquidado']    = "";
    $arRecord[$inCount]['pagar']              = "";

    $inCount++;

    $arRecord[$inCount]['nivel']              = 2;
    $arRecord[$inCount]['data']               = "";
    $arRecord[$inCount]['empenho']            = "";
    $arRecord[$inCount]['despesa']            = "ENTIDADES RELACIONADAS";
    $arRecord[$inCount]['empenhado']          = "";
    $arRecord[$inCount]['anulado']            = "";
    $arRecord[$inCount]['liquidado']          = "";
    $arRecord[$inCount]['pago']               = "";
    $arRecord[$inCount]['pagar_liquidado']    = "";
    $arRecord[$inCount]['pagar']              = "";

//    $this->obREmpenhoEmpenho->obROrcamentoEntidade->setExercicio( $this->getExercicio() );
    $inEntidades = str_replace("'","",$this->getCodEntidade() );
    $arEntidades = explode(",",$inEntidades );

    foreach ($arEntidades as $key => $inCodEntidade) {
        $inCount++;
        $this->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
        $this->obREmpenhoEmpenho->obROrcamentoEntidade->consultarNomes($rsLista);
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = $rsLista->getCampo("entidade");
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";
    }

    if ($this->getExercicio()) {
        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = "";
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";

        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = "EXERCÍCIO";
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";

        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = $this->getExercicio();
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";
    }

    if ($this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()) {
        $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = "";
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";

        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = "ORGÃO";
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";

        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = $rsOrgao->getCampo("nom_orgao");
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";
    }

    if ($this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade()) {
        $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->consultar( $rsCombo, $stFiltro,"", $boTransacao );

        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = "";
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";

        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = "UNIDADE";
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";

        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = $rsCombo->getCampo("nom_unidade");
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";

    }

    if ($this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural()) {
        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = "";
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";

        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = "ELEMENTO DE DESPESA";
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";

        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural();
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";
    }

    if ($this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso()) {
        $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->listar( $rsRecurso );

        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = "";
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";

        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = "RECURSO";
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";

        $inCount++;
        $arRecord[$inCount]['nivel']              = 1;
        $arRecord[$inCount]['data']               = "";
        $arRecord[$inCount]['empenho']            = "";
        $arRecord[$inCount]['despesa']            = $rsRecurso->getCampo("nom_recurso");
        $arRecord[$inCount]['empenhado']          = "";
        $arRecord[$inCount]['anulado']            = "";
        $arRecord[$inCount]['liquidado']          = "";
        $arRecord[$inCount]['pago']               = "";
        $arRecord[$inCount]['pagar_liquidado']    = "";
        $arRecord[$inCount]['pagar']              = "";
    }

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
