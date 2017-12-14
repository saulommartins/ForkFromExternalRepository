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
    * Data de Criação   : 25/09/2004
    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor: $
    $Date: 2006-12-06 19:40:31 -0200 (Qua, 06 Dez 2006) $

    * Casos de uso: uc-02.01.33
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';

$stExercicio = $_REQUEST['stExercicio'];

$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
$obTAdministracaoConfiguracao->setDado('exercicio', $stExercicio);
$obTAdministracaoConfiguracao->setDado('modulo', 8);
if ($stExercicio < '2014') {
    $obTAdministracaoConfiguracao->pegaConfiguracao($inPeriodo, 'unidade_medida_metas');
} else {
    $obTAdministracaoConfiguracao->pegaConfiguracao($inPeriodo, 'unidade_medida_metas_despesa');
}
if ($inPeriodo == '') {
    SistemaLegado::alertaAviso('FLMetasExecucaoDespesa.php?'.Sessao::getId().'&stAcao='.$_REQUEST['stAcao'], 'Não há Período de Apuração das Metas configurado para o exercício de  '.$stExercicio.'!','','aviso', Sessao::getId(), '../');
}

$preview = new PreviewBirt(2,8,6);
$preview->setTitulo('Metas de Execução da Despesa');
$preview->setVersaoBirt('2.5.0');

$arOrgaoUnidade = explode('.', $_REQUEST['stDotacaoOrcamentaria']);

$preview->addParametro('exercicio'      , $stExercicio);
$preview->addParametro('cod_entidade'   , implode(',', $_REQUEST['inCodEntidade']));
$preview->addParametro('cod_recurso'    , $_REQUEST['inCodRecurso']);
$preview->addParametro('cod_orgao'      , $arOrgaoUnidade[0]);
$preview->addParametro('cod_unidade'    , $arOrgaoUnidade[1]);
$preview->addParametro('cod_dotacao_ini', $_REQUEST['inCodDotacaoInicial']);
$preview->addParametro('cod_dotacao_fim', $_REQUEST['inCodDotacaoFinal']);
$preview->addParametro('tipo_relatorio' , $_REQUEST['boDemonstrarSintéticas']);
$preview->addParametro('periodo'        , $inPeriodo);

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
