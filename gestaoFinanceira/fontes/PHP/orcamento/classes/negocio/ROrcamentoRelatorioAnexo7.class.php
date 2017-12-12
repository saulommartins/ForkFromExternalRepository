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
    * Classe de regra para Anexo7
    * Data de Criação   : 27/09/2004

    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Gustavo Tourinho

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.14
*/

/*
$Log$
Revision 1.8  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                                                                 );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioDoacaoFuncionalProgramatica.class.php"          );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioDoacaoFuncionalProgramaticaBalanco.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                                         );

class ROrcamentoRelatorioAnexo7 extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFOrcamentoSomatorioDoacaoFuncionalProgramatica;
var $stFiltro;
var $inExercicio;
var $stDataInicial;
var $stDataFinal;
var $stSituacao;
var $stTipoRelatorio;
var $obREntidade;
var $stEntidades;

/**
     * @access Public
     * @param Object $valor
*/
function setFOrcamentoSomatorioDoacaoFuncionalProgramatica($valor) { $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica  = $valor; }
function setFiltro($valor) { $this->stFiltro                      = $valor; }
function setExercicio($valor) { $this->inExercicio                   = $valor; }
function setDataInicial($valor) { $this->stDataInicial                 = $valor; }
function setDataFinal($valor) { $this->stDataFinal                   = $valor; }
function setSituacao($valor) { $this->stSituacao                    = $valor; }
function setTipoRelatorio($valor) { $this->stTipoRelatorio               = $valor; }
function setREntidade($valor) { $this->obREntidade                   = $valor; }
function setEntidades($valor) { $this->stEntidades                   = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getFOrcamentoSomatorioDoacaoFuncionalProgramatica() { return $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica; }
function getFiltro() { return $this->stFiltro                    ; }
function getExercicio() { return $this->inExercicio                 ; }
function getDataInicial() { return $this->stDataInicial               ; }
function getDataFinal() { return $this->stDataFinal                 ; }
function getSituacao() { return $this->stSituacao                  ; }
function getTipoRelatorio() { return $this->stTipoRelatorio             ; }
function getREntidade() { return $this->obREntidade                 ; }
function getEntidades() { return $this->stEntidades                 ; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioAnexo7()
{
    $this->obREntidade                  = new ROrcamentoEntidade;
    $this->obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm')         );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet, $stOrder = "")
{
    if ($this->stTipoRelatorio=="orcamento") {
        $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica =new FOrcamentoSomatorioDoacaoFuncionalProgramatica;
    } else {
        $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica = new FOrcamentoSomatorioDoacaoFuncionalProgramaticaBalanco;
    }
    if ($this->stTipoRelatorio=="orcamento") {
        $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica->setDado ("exercicio", $this->inExercicio       );
        $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica->setDado ("stFiltro", $this->stFiltro           );
    } else {
        $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica->setDado ("exercicio", $this->inExercicio       );
        $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica->setDado ("stFiltro", $this->stFiltro           );
        $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica->setDado ("dataInicial", $this->stDataInicial   );
        $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica->setDado ("dataFinal", $this->stDataFinal       );
        $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica->setDado ("stEntidades", $this->stEntidades     );
        $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica->setDado ("stSituacao", $this->stSituacao       );
    }

    $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica->setDado ("exercicio", $this->inExercicio);
    $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica->setDado ("stFiltro", $this->stFiltro);

    $obErro = $this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica->recuperaTodos( $rsRecordSet, "", "" );
    //$this->obFOrcamentoSomatorioDoacaoFuncionalProgramatica->Debug();

    $inCount = 0;
    $inTotalProjeto   = 0;
    $inTotalAtividade = 0;
    $inTotalOperacao  = 0;
    $inTotal          = 0;

    $stTitulo  = "Anexo 7 - Programa de Trabalho do Governo Demonstrativo de Funções, Subfunções Programas e Subprogramas por Projetos e Atividades ";

    $arLinha[$inCount]['titulo'] = $stTitulo;

    $rsRecordSetNovo = new RecordSet;
    $rsRecordSetNovo->preenche( $arLinha );
    $arRecordSet[0] = $rsRecordSetNovo;

   while ( !$rsRecordSet->eof() ) {

        if ($rsRecordSet->getCampo("nivel") == 1 ) {
            $inTotalProjeto   = $inTotalProjeto + $rsRecordSet->getCampo("vl_projeto");
            $inTotalAtividade = $inTotalAtividade + $rsRecordSet->getCampo("vl_atividade");
            $inTotalOperacao  = $inTotalOperacao + $rsRecordSet->getCampo("vl_operacao");
            $inTotal          = $inTotal + $rsRecordSet->getCampo("vl_total");
        }

        if ( !$rsRecordSet->getCampo('vl_projeto') ) {
            $inValorProjeto = "";
        } else {
            $inValorProjeto = number_format ($rsRecordSet->getCampo('vl_projeto'),2,",",".");
        }

        if ( !$rsRecordSet->getCampo('vl_atividade') ) {
            $inValorAtividade = "";
        } else {
            $inValorAtividade = number_format ($rsRecordSet->getCampo('vl_atividade'),2,",",".");
        }

        if ( !$rsRecordSet->getCampo('vl_operacao') ) {
            $inValorOperacao = "";
        } else {
            $inValorOperacao = number_format ($rsRecordSet->getCampo('vl_operacao'),2,",",".");
        }
        if ( !$rsRecordSet->getCampo('vl_total') ) {
            $inValorTotal = "";
        } else {
            $inValorTotal = number_format ($rsRecordSet->getCampo('vl_total'),2,",",".");
        }
        $arOrcamento[$inCount]['nivel']        = $rsRecordSet->getCampo("nivel");
        $arOrcamento[$inCount]['alinhamento']  = $rsRecordSet->getCampo('alinhamento');
        $arOrcamento[$inCount]['dotacao']      = $rsRecordSet->getCampo('dotacao');
        $arOrcamento[$inCount]['descricao']    = $rsRecordSet->getCampo('descricao');
        $arOrcamento[$inCount]['vl_projeto']   = $inValorProjeto;
        $arOrcamento[$inCount]['vl_atividade'] = $inValorAtividade;
        $arOrcamento[$inCount]['vl_operacao']  = $inValorOperacao;
        $arOrcamento[$inCount]['vl_total']     = $inValorTotal;

        $inCount++;
        $rsRecordSet->proximo();
    }

    $inIndice = count ($arOrcamento);
    $arOrcamento[$inIndice]["alinhamento"]  = 8;
    $arOrcamento[$inIndice]["descricao"]    = "Total ...";
    $arOrcamento[$inIndice]['vl_projeto']   = number_format ($inTotalProjeto  ,2,",",".");
    $arOrcamento[$inIndice]['vl_atividade'] = number_format ($inTotalAtividade,2,",",".");
    $arOrcamento[$inIndice]['vl_operacao']  = number_format ($inTotalOperacao ,2,",",".");
    $arOrcamento[$inIndice]['vl_total']     = number_format ($inTotal         ,2,",",".");

    $inIndice++;
    $arOrcamento[$inIndice]['nivel']         = 1;
    $arOrcamento[$inIndice]["alinhamento"]   = 1;
    $arOrcamento[$inIndice]["descricao"]     = "";

    $inIndice++;
    $arOrcamento[$inIndice]['nivel']            = 1;
    $arOrcamento[$inIndice]["alinhamento"]      = 1;
    $arOrcamento[$inIndice]["descricao"]        = "ENTIDADES RELACIONADAS";

    $stEntidade = substr(trim($this->getFiltro()),strpos($this->getFiltro(),"("));
    $stEntidade = substr($stEntidade,0,strlen($stEntidade)-1);

    $inEntidades = str_replace("'","",$stEntidade);
    $arEntidades = explode(",",$inEntidades );

    foreach ($arEntidades as $key => $inCodEntidade) {
        $inIndice++;
        $this->obREntidade->setCodigoEntidade( $inCodEntidade );
        $this->obREntidade->consultarNomes($rsLista);
        $arOrcamento[$inIndice]['nivel']        = 1;
        $arOrcamento[$inIndice]["alinhamento"]  = 1;
        $arOrcamento[$inIndice]["descricao"]    = "- ".$rsLista->getCampo("entidade");;
    }

    $inIndice++;
    $arOrcamento[$inIndice]['nivel']            = 1;
    $arOrcamento[$inIndice]["alinhamento"]      = 1;
    $arOrcamento[$inIndice]["descricao"]        = "";

    $rsRecordSetNovo = new RecordSet;
    $rsRecordSetNovo->preenche( $arOrcamento );
    $arRecordSet[1] = $rsRecordSetNovo;

    $rsRecordSet = $arRecordSet;

    return $obErro;
}
}
