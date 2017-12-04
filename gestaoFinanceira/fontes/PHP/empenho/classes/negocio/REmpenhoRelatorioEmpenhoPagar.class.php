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
    * Classe de Regra do Relatório de Balancete de Receita
    * Data de Criação   : 18/02/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Rafael Almeida

    * @author Analista: Muriel Preuss
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Regra

    $Revision: 30805 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso : uc-02.03.07
*/

/*
$Log$
Revision 1.8  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php"                 );

/**
    * Classe de Regra de Negócios Empenho Empenhado, Pago ou Liquidado
    * @author Desenvolvedor: Rafael Almeida
*/
class REmpenhoRelatorioEmpenhoPagar extends PersistenteRelatorio
{
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $stDataSituacao;
/**
    * @var Integer
    * @access Private
*/
var $inOrdenacao;
/**
    * @var Integer
    * @access Private
*/
var $stFiltro;

/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataSituacao($valor) { $this->stDataSituacao      = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setOrdenacao($valor) { $this->inOrdenacao        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro           = $valor; }

/**
     * @access Public
     * @return Object
*/
function getCodEntidade() { return $this->inCodEntidade;                }
/**
     * @access Public
     * @return Object
*/
function getDataSituacao() { return $this->stDataSituacao;                }
/**
     * @access Public
     * @return Integer
*/
function getOrdenacao() { return $this->inOrdenacao;                  }
/**
     * @access Public
     * @return Object
*/
function getFiltro() { return $this->stFiltro;                     }

/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioEmpenhoPagar()
{
    $this->obREmpenhoEmpenho                = new REmpenhoEmpenho;
    $this->obRRelatorio                     = new RRelatorio;
    $this->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    include_once( CAM_GF_EMP_MAPEAMENTO."FEmpenhoEmpenhoPagar.class.php"       );
    $obFEmpenhoEmpenhoPagar                 = new FEmpenhoEmpenhoPagar;

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

    $obFEmpenhoEmpenhoPagar->setDado("stFiltro"               ,$this->getFiltro());
    $obFEmpenhoEmpenhoPagar->setDado("stEntidade"             ,$this->getCodEntidade());
    $obFEmpenhoEmpenhoPagar->setDado("exercicio"              ,$this->obREmpenhoEmpenho->getExercicio());
    $obFEmpenhoEmpenhoPagar->setDado("stDataInicial"          ,$this->obREmpenhoEmpenho->getDtEmpenhoInicial());
    $obFEmpenhoEmpenhoPagar->setDado("stDataFinal"            ,$this->obREmpenhoEmpenho->getDtEmpenhoFinal());
    $obFEmpenhoEmpenhoPagar->setDado("stDataSituacao"         ,$this->getDataSituacao());
    $obFEmpenhoEmpenhoPagar->setDado("inCodEmpenhoInicial"    ,$this->obREmpenhoEmpenho->getCodEmpenhoInicial());
    $obFEmpenhoEmpenhoPagar->setDado("inCodEmpenhoFinal"      ,$this->obREmpenhoEmpenho->getCodEmpenhoFinal());
    $obFEmpenhoEmpenhoPagar->setDado("inOrdenacao"            ,$this->getOrdenacao());
    $obFEmpenhoEmpenhoPagar->setDado("inCodFornecedor"        ,$this->obREmpenhoEmpenho->obRCGM->getNumCGM());
    $obFEmpenhoEmpenhoPagar->setDado("inNumOrgao"             ,$this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());

    $obErro = $obFEmpenhoEmpenhoPagar->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount                = 0;
    $inCountInt             = 0;
    $inTotalEmpenhado       = 0;
    $inTotalLiquidado       = 0;
    $inTotalPago            = 0;
    $inTotalAPagar          = 0;
    $inTotalAPagarLiquidado = 0;
    $arRecord               = array();

    while ( !$rsRecordSet->eof() ) {
        //alterações para quebra de linha
        $stNomContaTemp = str_replace( chr(10), "", trim($rsRecordSet->getCampo('credor')) );
        $stNomContaTemp = wordwrap( $stNomContaTemp,30,chr(13) );
        $arNomContaOLD = explode( chr(13), $stNomContaTemp );
        //fim de alterações para quebra de linha

        $arRecord[$inCount]['empenho']          = $rsRecordSet->getCampo('cod_entidade') . " - " . $rsRecordSet->getCampo('cod_empenho') . "/" . $rsRecordSet->getCampo('exercicio');
        $arRecord[$inCount]['data']             = $rsRecordSet->getCampo('dt_emissao');

        $inCount2 = $inCount;
        //alterações para quebra de linha
        foreach ($arNomContaOLD as $stNomContaTemp) {
            $inCountInt++;
            if($inCountInt==1)
                $arRecord[$inCount2]['credor']    = $rsRecordSet->getCampo('cgm') . " - " . $stNomContaTemp;
            else
                $arRecord[$inCount2]['credor']    = $stNomContaTemp;

            $inCount2++;
        }
        $inCountInt = 0;

        $arRecord[$inCount]['empenhado']        = number_format( $rsRecordSet->getCampo('empenhado'),2,',','.');
        $arRecord[$inCount]['liquidado']        = number_format( $rsRecordSet->getCampo('liquidado'),2,',','.');
        $arRecord[$inCount]['pago']             = number_format( $rsRecordSet->getCampo('pago'),2,',','.');
        $arRecord[$inCount]['apagar']           = number_format( $rsRecordSet->getCampo('apagar'),2,',','.');
        $arRecord[$inCount]['apagarliquidado']  = number_format( $rsRecordSet->getCampo('apagarliquidado'),2,',','.');

        $inCount = $inCount2;

        $inTotalEmpenhado           = $inTotalEmpenhado + $rsRecordSet->getCampo('empenhado');
        $inTotalLiquidado           = $inTotalLiquidado + $rsRecordSet->getCampo('liquidado');
        $inTotalPago                = $inTotalPago + $rsRecordSet->getCampo('pago');
        $inTotalAPagar              = $inTotalAPagar + $rsRecordSet->getCampo('apagar');
        $inTotalAPagarLiquidado     = $inTotalAPagarLiquidado + $rsRecordSet->getCampo('apagarliquidado');

        $rsRecordSet->proximo();
    }

    if ($inCount>0) {
    //MONTA TOTALIZADOR GERAL
    $arRecord[$inCount]['nivel']            = 1;
    $arRecord[$inCount]['empenho']          = "";
    $arRecord[$inCount]['data ']            = "";
    $arRecord[$inCount]['credor']           = "";
    $arRecord[$inCount]['empenhado']        = "";
    $arRecord[$inCount]['liquidado']        = "";
    $arRecord[$inCount]['pago']             = "";
    $arRecord[$inCount]['apagar']           = "";
    $arRecord[$inCount]['apagarliquidado']  = "";

    $inCount++;

    //MONTA TOTALIZADOR GERAL
    $arRecord[$inCount]['nivel']            = 2;
    $arRecord[$inCount]['empenho']          = "TOTAL";
    $arRecord[$inCount]['data ']            = "";
    $arRecord[$inCount]['credor']           = "";
    $arRecord[$inCount]['empenhado']        = number_format( $inTotalEmpenhado, 2, ',', '.' );
    $arRecord[$inCount]['liquidado']        = number_format( $inTotalLiquidado, 2, ',', '.' );
    $arRecord[$inCount]['pago']             = number_format( $inTotalPago, 2, ',', '.' );
    $arRecord[$inCount]['apagar']           = number_format( $inTotalAPagar, 2, ',', '.' );
    $arRecord[$inCount]['apagarliquidado']  = number_format( $inTotalAPagarLiquidado, 2, ',', '.' );

    $inCount++;
    }

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
?>
