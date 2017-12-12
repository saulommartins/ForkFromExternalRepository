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
    * Classe de Regra do Relatório de Programação de Pagamentos
    * Data de Criação   : 16/08/2005

    * @author Analista: Muriel Preuss
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Relatorio

    $Revision: 30805 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso : uc-02.03.26
*/

/*
$Log$
Revision 1.10  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                       );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php"                                 );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                                        );
include_once( CAM_FW_PDF."RRelatorio.class.php"                                      );

class REmpenhoRelatorioProgramacaoPagamentos extends PersistenteRelatorio
{
/**
    * @var Varchar
    * @access Private
*/
var $inCodEntidade;

/**
    * @var Varchar
    * @access Private
*/
var $stDtVencimentoInicial;

/**
    * @var Varchar
    * @access Private
*/
var $stDtVencimentoFinal;

/**
    * @var Integer
    * @access Private
*/
var $stFiltro;

/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade      = $valor;                 }
/**
     * @access Public
     * @param Varchar $valor
*/
function setDtVencimentoInicial($valor) { $this->stDtVencimentoInicial = $valor;              }
/**
     * @access Public
     * @param Varchar $valor
*/
function setDtVencimentoFinal($valor) { $this->stDtVencimentoFinal   = $valor;              }
/**
     * @access Public
     * @param Varchar $valor
*/
function setFiltro($valor) { $this->stFiltro           = $valor;                 }

/**
     * @access Public
     * @return Varchar
*/
function getCodEntidade() { return $this->inCodEntidade;                    }
/**
     * @access Public
     * @return Varchar
*/
function getDtVencimentoInicial() { return $this->stDtVencimentoInicial;            }
/**
     * @access Public
     * @return Varchar
*/
function getDtVencimentoFinal() { return $this->stDtVencimentoFinal;              }
/**
     * @access Public
     * @return Varchar
*/
function getFiltro() { return $this->stFiltro;                         }

/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioProgramacaoPagamentos()
{
    $this->obREmpenhoEmpenho                            = new REmpenhoEmpenho;
    $this->obROrcamentoDespesa                                   = new ROrcamentoDespesa;
    $this->obRRelatorio                                 = new RRelatorio;
    $this->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arRecordSet ,&$arRecordSet1,&$arRecordSet2,&$arRecordSet3,&$arRecordSet4, &$arRecordSet5, &$arRecordSet6, &$arRecordSet7, &$arRecordSet8, &$arRecordSet9,$stOrder = "")
{
    include_once( CAM_GF_EMP_MAPEAMENTO."FEmpenhoProgramacaoPagamentosDisponFinanc.class.php"  );
    include_once( CAM_GF_EMP_MAPEAMENTO."FEmpenhoProgramacaoPagamentos.class.php"  );
    $obFEmpenhoProgramacaoPagamentosDisponFinanc = new FEmpenhoProgramacaoPagamentosDisponFinanc;
    $obFEmpenhoProgramacaoPagamentos             = new FEmpenhoProgramacaoPagamentos;

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

    $obFEmpenhoProgramacaoPagamentos->setDado("stFiltro"               ,$this->getFiltro());
    $obFEmpenhoProgramacaoPagamentos->setDado("stEntidade"             ,$this->getCodEntidade());
    $obFEmpenhoProgramacaoPagamentos->setDado("exercicio"              ,$this->obREmpenhoEmpenho->getExercicio());
    $obFEmpenhoProgramacaoPagamentos->setDado("stDataInicial"          ,$this->getDtVencimentoInicial());
    $obFEmpenhoProgramacaoPagamentos->setDado("stDataFinal"            ,$this->getDtVencimentoFinal());
    $obFEmpenhoProgramacaoPagamentos->setDado("inCodDespesa"           ,$this->obROrcamentoDespesa->getCodDespesa());
    $obFEmpenhoProgramacaoPagamentos->setDado("inCodRecurso"           ,$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso());
    $obFEmpenhoProgramacaoPagamentos->setDado("stDestinacaoRecurso"    ,$this->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso());
    $obFEmpenhoProgramacaoPagamentos->setDado("inCodDetalhamento"      ,$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento());
    $obFEmpenhoProgramacaoPagamentos->setDado("inCodFornecedor"        ,$this->obREmpenhoEmpenho->obRCGM->getNumCGM());

    $obErro = $obFEmpenhoProgramacaoPagamentos->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount                = 0;
    $inCount2               = 0;
    $inCount3               = 0;
    $inCount4               = 0;
    $arData                 = array();
    $arRecurso              = array();
    $arRecursoAPagar        = array();
    $arTotalData            = array();
    $arTotalRecurso         = array();
    $arDados                = array();
    $arResumoRecurso        = array();
    $arValor                = array();
    $stDataAtual            = '';
    $stDataAnterior         = '';
    $stRecursoAtual         = '';
    $stRecursoAnterior      = '';
    $nuTotalData            = 0;
    $nuTotalRecurso         = 0;
    $nuTotalPagar           = 0;
    $inCodRecurso           = 0;
    $stNomRecurso           = '';
    $novaData               = 0;

    while (!$rsRecordSet->eof()) {
        $stDataAtual = $rsRecordSet->getCampo('dt_vencimento');
        if ($stDataAtual <> $stDataAnterior) {
            if ($stDataAnterior <> "") {
                $arTotalData[0]['coluna1'] = "Total das Despesas na Data";
                $arTotalData[0]['coluna2'] = $nuTotalData;
                $arRecordSet2[$inCount2] = new RecordSet;
                $arRecordSet2[$inCount2]->preenche($arTotalData);
                $arRecordSet2[$inCount2]->addFormatacao("coluna2","NUMERIC_BR");
                $nuTotalData    = 0;
                $novaData=1;
            }
        }
        $stRecursoAtual = $rsRecordSet->getCampo('cod_recurso') . " - " .strtoupper($rsRecordSet->getCampo('nom_recurso'));
        if (($stRecursoAtual <> $stRecursoAnterior) || ($novaData)) {
            if ($stRecursoAnterior <> "") {

                $arTotalRecurso[0]['coluna1'] = "Total das Despesas Para o Recurso na Data";
                $arTotalRecurso[0]['coluna2'] = $nuTotalRecurso;
                $arRecordSet3[$inCount2][$inCount3] = new RecordSet;
                $arRecordSet3[$inCount2][$inCount3]->preenche($arTotalRecurso);
                $arRecordSet3[$inCount2][$inCount3]->addFormatacao("coluna2","NUMERIC_BR");

                $arRecordSet4[$inCount2][$inCount3] = new RecordSet;
                $arRecordSet4[$inCount2][$inCount3]->preenche($arDados);
                $arRecordSet4[$inCount2][$inCount3]->addFormatacao("coluna3","NUMERIC_BR");

                /* ----------------------------------------------------------------------------
                   MONTA TOTALIZADOR DOS RECURSOS - Se o recurso atual ja foi totalizado
                   em outra data, soma os totais, caso contrato insere novo registro no array */

                $inCodRecurso = substr($stRecursoAnterior,0,strpos  ($stRecursoAnterior,'-')-1);
                $stNomRecurso = substr($stRecursoAnterior,strpos    ($stRecursoAnterior,'-')+2);

                $flag = false;
                foreach ($arResumoRecurso as $inIndice => $arValor) {
                    if ($arValor['coluna1'] == $inCodRecurso) {
                        $arResumoRecurso[$inIndice]['coluna3'] += $nuTotalRecurso;
                        $flag = true;
                    }
                }
                if (!$flag) {
                    $arResumoRecurso[$inCount4]['coluna1'] = $inCodRecurso;
                    $arResumoRecurso[$inCount4]['coluna2'] = $stNomRecurso;
                    $arResumoRecurso[$inCount4]['coluna3'] = $nuTotalRecurso;
                    $inCount4++;
                }
                /* ------------------------------------------------------------------------- */
                $inCount        = 0;
                $nuTotalRecurso = 0;
                $inCount3       ++;
            }
            if ($novaData==1) {
                $inCount2   ++;
                $inCount3   =0;
                $novaData   =0;
            }

            $arRecurso[0]['coluna1'] = "Recurso: ";
            $arRecurso[0]['coluna2'] = $stRecursoAtual;
            $arRecordSet1[$inCount2][$inCount3] = new RecordSet;
            $arRecordSet1[$inCount2][$inCount3]->preenche($arRecurso);
            // -- Este array armazena os recursos a pagar para filtrar somente estes nas disponibilidades
            $arRecursoAPagar[$inCount3] = $stRecursoAtual;
            // -- Fim

            $stRecursoAnterior = $stRecursoAtual;
            $arDados = array();

        }
        $nuTotalData      = bcadd($nuTotalData      ,$rsRecordSet->getCampo('apagar'),2);
        $nuTotalRecurso   = bcadd($nuTotalRecurso   ,$rsRecordSet->getCampo('apagar'),2);
        $nuTotalPagar     = bcadd($nuTotalPagar     ,$rsRecordSet->getCampo('apagar'),2);

        $arDados[$inCount]['coluna1']  = $rsRecordSet->getCampo('cod_entidade')."-".$rsRecordSet->getCampo('cod_empenho')."/".$rsRecordSet->getCampo('exercicio');
        $arDados[$inCount]['coluna2']  = $rsRecordSet->getCampo('cgm')." - ".strtoupper($rsRecordSet->getCampo('credor'));
        $arDados[$inCount]['coluna3']  = $rsRecordSet->getCampo('apagar');

        $inCount++;
        if ($stDataAnterior<>$stDataAtual) {
            $arData[0]['coluna1'] = "Data: ";
            $arData[0]['coluna2'] = $stDataAtual;
            $arRecordSet[$inCount2] = new RecordSet;
            $arRecordSet[$inCount2]->preenche($arData);
        }
        $stDataAnterior = $stDataAtual;
        $rsRecordSet->proximo();
    }
    $arTotalData[0]['coluna1'] = "Total das Despesas na Data";
    $arTotalData[0]['coluna2'] = $nuTotalData;
    $arRecordSet2[$inCount2] = new RecordSet;
    $arRecordSet2[$inCount2]->preenche($arTotalData);
    $arRecordSet2[$inCount2]->addFormatacao("coluna2","NUMERIC_BR");

    $arTotalRecurso[0]['coluna1'] = "Total das Despesas Para o Recurso na Data";
    $arTotalRecurso[0]['coluna2'] = $nuTotalRecurso;
    $arRecordSet3[$inCount2][$inCount3] = new RecordSet;
    $arRecordSet3[$inCount2][$inCount3]->preenche($arTotalRecurso);
    $arRecordSet3[$inCount2][$inCount3]->addFormatacao("coluna2","NUMERIC_BR");

    $inCodRecurso = substr($stRecursoAnterior,0,strpos($stRecursoAnterior,'-')-1);
    $stNomRecurso = substr($stRecursoAnterior,strpos($stRecursoAnterior,'-')+2);

    $flag = false;
    foreach ($arResumoRecurso as $inIndice => $arValor) {
        if ($arValor['coluna1'] == $inCodRecurso) {
            $arResumoRecurso[$inIndice]['coluna3'] += $nuTotalRecurso;
            $flag = true;
        }
    }
    if (!$flag) {
        $arResumoRecurso[$inCount4]['coluna1'] = $inCodRecurso;
        $arResumoRecurso[$inCount4]['coluna2'] = $stNomRecurso;
        $arResumoRecurso[$inCount4]['coluna3'] = $nuTotalRecurso;
    }

    $arRecordSet5 = new RecordSet;
    $arRecordSet5->preenche($arResumoRecurso);
    $arRecordSet5->addFormatacao("coluna3","NUMERIC_BR");

    $arRecordSet3[$inCount2][$inCount3] = new RecordSet;
    $arRecordSet3[$inCount2][$inCount3]->preenche($arTotalRecurso);
    $arRecordSet3[$inCount2][$inCount3]->addFormatacao("coluna2","NUMERIC_BR");

    $arRecordSet4[$inCount2][$inCount3] = new RecordSet;
    $arRecordSet4[$inCount2][$inCount3]->preenche($arDados);
    $arRecordSet4[$inCount2][$inCount3]->addFormatacao("coluna3","NUMERIC_BR");

    $obFEmpenhoProgramacaoPagamentosDisponFinanc->setDado("stEntidade"             ,$this->getCodEntidade());
    $obFEmpenhoProgramacaoPagamentosDisponFinanc->setDado("exercicio"              ,$this->obREmpenhoEmpenho->getExercicio());
    $obFEmpenhoProgramacaoPagamentosDisponFinanc->setDado("stDataInicial"          ,'01/01/'.$this->obREmpenhoEmpenho->getExercicio());
    $obFEmpenhoProgramacaoPagamentosDisponFinanc->setDado("stDataFinal"            ,date( 'd/m/Y'));
    $obFEmpenhoProgramacaoPagamentosDisponFinanc->setDado("inCodRecurso"           ,$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso());
    $obErro = $obFEmpenhoProgramacaoPagamentosDisponFinanc->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $arTotalRecurso     = array();
    $arTotais           = array();
    $arDados            = array();
    $arRecurso          = array();
    $inCount            = 0;
    $inCount2           = 0;
    $nuTotalDispon      = 0;
    $nuTotalRecurso     = 0;
    $stRecursoAtual     = 0;
    $stRecursoAnterior  = "";

    while (!$rsRecordSet->eof()) {
        $stRecursoAtual = $rsRecordSet->getCampo('cod_recurso') . " - " .strtoupper($rsRecordSet->getCampo('nom_recurso'));
        //somente mostra Disponibilidade para o recurso Livre e para os Recursos que tem valor a pagar
        if (in_array($stRecursoAtual,$arRecursoAPagar) or strtoupper($rsRecordSet->getCampo('nom_recurso'))=="LIVRE") {
            if ($stRecursoAtual <> $stRecursoAnterior) {
                if ($stRecursoAnterior <> "") {
                    $arTotalRecurso[0]['coluna1'] = "Total da Disponibilidade Para o Recurso";
                    $arTotalRecurso[0]['coluna2'] = $nuTotalRecurso;
                    $arRecordSet6[$inCount] = new RecordSet;
                    $arRecordSet6[$inCount]->preenche($arTotalRecurso);
                    $arRecordSet6[$inCount]->addFormatacao("coluna2","NUMERIC_BR");

                    $arRecordSet7[$inCount] = new RecordSet;
                    $arRecordSet7[$inCount]->preenche($arDados);
                    $arRecordSet7[$inCount]->addFormatacao("coluna3","NUMERIC_BR");

                    $arDados        = array();
                    $inCount        ++;
                    $inCount2       = 0;
                    $nuTotalRecurso = 0;
                }

                $arRecurso[0]['coluna1'] = "Recurso: ";
                $arRecurso[0]['coluna2'] = $stRecursoAtual;
                $arRecordSet8[$inCount] = new RecordSet;
                $arRecordSet8[$inCount]->preenche($arRecurso);

                $stRecursoAnterior = $stRecursoAtual;
            }
            $nuTotalRecurso   = bcadd($nuTotalRecurso   ,$rsRecordSet->getCampo('vl_saldo_atual'),2);
            $nuTotalDispon    = bcadd($nuTotalDispon    ,$rsRecordSet->getCampo('vl_saldo_atual'),2);

            $arDados[$inCount2]['coluna1']  = $rsRecordSet->getCampo('cod_plano');
            $arDados[$inCount2]['coluna2']  = $rsRecordSet->getCampo('nom_conta');
            $arDados[$inCount2]['coluna3']  = $rsRecordSet->getCampo('vl_saldo_atual');
        }
        $inCount2++;
        $rsRecordSet->proximo();
    }

    $arTotalRecurso[0]['coluna1'] = "Total da Disponibilidade Para o Recurso";
    $arTotalRecurso[0]['coluna2'] = $nuTotalRecurso;
    $arRecordSet6[$inCount] = new RecordSet;
    $arRecordSet6[$inCount]->preenche($arTotalRecurso);
    $arRecordSet6[$inCount]->addFormatacao("coluna2","NUMERIC_BR");

    $arRecordSet7[$inCount] = new RecordSet;
    $arRecordSet7[$inCount]->preenche($arDados);
    $arRecordSet7[$inCount]->addFormatacao("coluna3","NUMERIC_BR");

    $arTotais[0]['coluna1']  = "TOTAL A PAGAR NO PERÍODO";
    $arTotais[0]['coluna2']  = $nuTotalPagar;
    $arTotais[1]['coluna1']  = "TOTAL DISPONIBILIDADE FINANCEIRA NA DATA";
    $arTotais[1]['coluna2']  = $nuTotalDispon;

    $arRecordSet9 = new RecordSet;
    $arRecordSet9->preenche($arTotais);
    $arRecordSet9->addFormatacao("coluna2","NUMERIC_BR");

    if (!$arRecordSet) {
        $arData[0]['coluna1'] = "";
        $arData[0]['coluna2'] = "";
        $arRecordSet[0] = new RecordSet;
        $arRecordSet[0]->preenche($arData);
    }
    if (!$arRecordSet1) {
        $arRecurso[0]['coluna1'] = "";
        $arRecurso[0]['coluna2'] = "";
        $arRecordSet1[0][0] = new RecordSet;
        $arRecordSet1[0][0]->preenche($arRecurso);
    }

    return $obErro;

}

}
?>
