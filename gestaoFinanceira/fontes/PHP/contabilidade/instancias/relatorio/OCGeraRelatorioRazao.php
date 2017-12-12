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
set_time_limit(0);
/**
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 30/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: OCGeraRelatorioRazao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/mapeamento/TAdministracaoConfiguracao.class.php';
include '../../../../../../gestaoRH/fontes/PHP/entidade/classes/mapeamento/TEntidade.class.php';
include '../../../../../../gestaoFinanceira/fontes/PHP/contabilidade/classes/mapeamento/TContabilidadePlanoConta.class.php';

$preview = new PreviewBirt( 2, 9, 3 );
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);

$arEntidade = Sessao::read('arEntidade');
$stDescEntidade = '';
reset($arEntidade->arElementos);
foreach ($arEntidade->arElementos as $arEntidade) {
    reset($_REQUEST['inCodEntidade']);
    foreach ($_REQUEST['inCodEntidade'] as $inCodEntidade) {
        if (in_array($inCodEntidade ,$arEntidade)) {
            $stDescEntidade.= $inCodEntidade."-".$arEntidade['nom_cgm']."<br>";
        }
    }
}

// recupera cod_estrutural de cod_reduzido
if ($_REQUEST['stCodEstruturalInicial'] == '') {
    $_REQUEST['stCodEstruturalInicial'] = '0.0.0.0.0.00.00.00.00.00';
}

if ($_REQUEST['stCodEstruturalFinal'] == '') {
    $_REQUEST['stCodEstruturalFinal'] = '9.9.9.9.9.99.99.99.99.99';
}

if ($_REQUEST['inCodPlanoInicial'] != '') {
    $stFiltro .= " AND pa.cod_plano >= " . $_REQUEST['inCodPlanoInicial'] . " ";
}
if ($_REQUEST['inCodPlanoFinal'] != '') {
    $stFiltro .= " AND pa.cod_plano <= " . $_REQUEST['inCodPlanoFinal'] . " ";
}

$stDtInicialAnterior = date('d/m/Y',mktime(0,0,0,1,1,substr($_REQUEST['stDataInicial'],6,4)));
if ($_REQUEST['stDataInicial'] == "01/01/".Sessao::getExercicio()) {
    $stDtFinalAnterior = date('d/m/Y',mktime(0,0,0,substr($_REQUEST['stDataInicial'],3,2),substr($_REQUEST['stDataInicial'],0,2),substr($_REQUEST['stDataInicial'],6,4)));
} else {
    $stDtFinalAnterior = date('d/m/Y',mktime(0,0,0,substr($_REQUEST['stDataInicial'],3,2),substr($_REQUEST['stDataInicial'],0,2)-1,substr($_REQUEST['stDataInicial'],6,4)));
}
$stEntidades = implode(',', $_REQUEST['inCodEntidade']);

$preview->addParametro('st_entidade'           , $stEntidades);
$preview->addParametro('cod_entidade'          , $stEntidades);
$preview->addParametro('filtro'                , $stFiltro );
$preview->addParametro('cod_estrutural_inicial', $_REQUEST['stCodEstruturalInicial']);
$preview->addParametro('cod_estrutural_final'  , $_REQUEST['stCodEstruturalFinal']);
$preview->addParametro('dt_inicial'            , $_REQUEST['stDataInicial']);
$preview->addParametro('dt_final'              , $_REQUEST['stDataFinal']);
$preview->addParametro('dt_inicial_anterior'   , $stDtInicialAnterior);
$preview->addParametro('dt_final_anterior'     , $stDtFinalAnterior);
$preview->addParametro('bo_movimentacao'       , $_REQUEST['boMovimentacaoConta']);
$preview->addParametro('bo_historico'          , $_REQUEST['boHistoricoCompleto']);
$preview->addParametro('bo_quebra_por_conta'   , $_REQUEST['boQuebraPaginaConta']);
$preview->addParametro('cod_reduzido_inicial'  , $_REQUEST['inCodPlanoInicial']);
$preview->addParametro('cod_reduzido_final'    , $_REQUEST['inCodPlanoFinal']);
$preview->addParametro('entidade_descricao'    , $stDescEntidade);
$preview->addParametro('exercicio'             ,  Sessao::read('exercicio'));

// Exibição do Relatorio Birt
$preview->preview();

?>
