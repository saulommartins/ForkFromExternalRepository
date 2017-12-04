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

    * Página de Processamento para Configuracao Contas Bancarias TCEMG
    * Data de Criação   : 14/02/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @ignore
    *
    * $Id: PRManterConfiguracaoREGLIC.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * $Revision: $
    * $Author: $
    * $Date: $
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGContaBancaria.class.php");
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoREGLIC.class.php");
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTipoRegistroPreco.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoREGLIC";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obErro = new Erro;
$obTransacao = new Transacao;
$obTransacao->begin();
$boTransacao = $obTransacao->getTransacao();

/*
 * Rotina de Inclusao
 */
###
      
$obTCEMGConfiguracaoREGLIC = new TTCEMGConfiguracaoREGLIC();
$obTCEMGConfiguracaoREGLIC->setDado    ('exercicio', Sessao::getExercicio() );
$obTCEMGConfiguracaoREGLIC->setDado('cod_entidade', $_POST[ 'inCodEntidade' ] );
$obErro = $obTCEMGConfiguracaoREGLIC->recuperaPorChave( $rsRecordSet, $boTransacao );
if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
    $obTCEMGConfiguracaoREGLIC->setDado('regulamento_art_47', $_REQUEST['inRegulamentoArt47'] );
    $obTCEMGConfiguracaoREGLIC->setDado('cod_norma', $_REQUEST['inCodNorma']);
    $obTCEMGConfiguracaoREGLIC->setDado('reg_exclusiva', $_REQUEST['inRegExclusiva']);
    $obTCEMGConfiguracaoREGLIC->setDado('artigo_reg_exclusiva', $_REQUEST['stArtigoRegExclusiva']);
    $obTCEMGConfiguracaoREGLIC->setDado('valor_limite_reg_exclusiva', str_replace(',','.',$_REQUEST['vlLimiteRegExclusiva']));
    $obTCEMGConfiguracaoREGLIC->setDado('proc_sub_contratacao', $_REQUEST['inProcSubContratacao']);
    $obTCEMGConfiguracaoREGLIC->setDado('artigo_proc_sub_contratacao', $_REQUEST['stArtigoProcSubContratacao']);
    $obTCEMGConfiguracaoREGLIC->setDado('percentual_sub_contratacao', str_replace(',','.',$_REQUEST['flPercentualSubContratacao']));
    $obTCEMGConfiguracaoREGLIC->setDado('criterio_empenho_pagamento', $_REQUEST['inCriteriosEmpenhoPagamento']);
    $obTCEMGConfiguracaoREGLIC->setDado('artigo_empenho_pagamento', $_REQUEST['stArtigoEmpenhoPagamento']);
    $obTCEMGConfiguracaoREGLIC->setDado('estabeleceu_perc_contratacao', $_REQUEST['inEstabeleceuPercContratacao']);
    $obTCEMGConfiguracaoREGLIC->setDado('artigo_perc_contratacao', $_REQUEST['stArtigoPercContratacao']);
    $obTCEMGConfiguracaoREGLIC->setDado('percentual_contratacao', str_replace(',','.',$_REQUEST['flPercentualContratacao']));
    $obErro = $obTCEMGConfiguracaoREGLIC->alteracao($boTransacao );
} else {
    $obTCEMGConfiguracaoREGLIC->setDado('exercicio', Sessao::getExercicio());
    $obTCEMGConfiguracaoREGLIC->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
    $obTCEMGConfiguracaoREGLIC->setDado('regulamento_art_47', $_REQUEST['inRegulamentoArt47'] );
    $obTCEMGConfiguracaoREGLIC->setDado('cod_norma', $_REQUEST['inCodNorma']);
    $obTCEMGConfiguracaoREGLIC->setDado('reg_exclusiva', $_REQUEST['inRegExclusiva']);
    $obTCEMGConfiguracaoREGLIC->setDado('artigo_reg_exclusiva', $_REQUEST['stArtigoRegExclusiva']);
    $obTCEMGConfiguracaoREGLIC->setDado('valor_limite_reg_exclusiva', $_REQUEST['vlLimiteRegExclusiva']);
    $obTCEMGConfiguracaoREGLIC->setDado('proc_sub_contratacao', $_REQUEST['inProcSubContratacao']);
    $obTCEMGConfiguracaoREGLIC->setDado('artigo_proc_sub_contratacao', $_REQUEST['stArtigoProcSubContratacao']);
    $obTCEMGConfiguracaoREGLIC->setDado('percentual_sub_contratacao', $_REQUEST['flPercentualSubContratacao']);
    $obTCEMGConfiguracaoREGLIC->setDado('criterio_empenho_pagamento', $_REQUEST['inCriteriosEmpenhoPagamento']);
    $obTCEMGConfiguracaoREGLIC->setDado('artigo_empenho_pagamento', $_REQUEST['stArtigoEmpenhoPagamento']);
    $obTCEMGConfiguracaoREGLIC->setDado('estabeleceu_perc_contratacao', $_REQUEST['inEstabeleceuPercContratacao']);
    $obTCEMGConfiguracaoREGLIC->setDado('artigo_perc_contratacao', $_REQUEST['stArtigoPercContratacao']);
    $obTCEMGConfiguracaoREGLIC->setDado('percentual_contratacao', $_REQUEST['flPercentualContratacao']);
    $obErro = $obTCEMGConfiguracaoREGLIC->inclusao($boTransacao );
}

$obTTCEMGTipoRegistroPreco = new TTCEMGTipoRegistroPreco();
$obTTCEMGTipoRegistroPreco->setDado    ('exercicio', Sessao::getExercicio() );
$obTTCEMGTipoRegistroPreco->setDado('cod_entidade', $_POST[ 'inCodEntidade' ] );
$obTTCEMGTipoRegistroPreco->recuperaTipoRegistroPreco($rsNormas);
$cont= 1;
foreach ($rsNormas->arElementos as $arNorma) {

    $obTTCEMGTipoRegistroPreco = new TTCEMGTipoRegistroPreco();
    $obTTCEMGTipoRegistroPreco->setDado    ('exercicio', Sessao::getExercicio() );
    $obTTCEMGTipoRegistroPreco->setDado('cod_entidade', $_POST[ 'inCodEntidade' ] );
    $obTTCEMGTipoRegistroPreco->setDado('cod_norma', $arNorma[ 'cod_norma' ] );
    $obErro = $obTTCEMGTipoRegistroPreco->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
        $obTTCEMGTipoRegistroPreco->setDado("cod_tipo_decreto", ($_REQUEST['inCodTipoDecreto_'.$cont]?$_REQUEST['inCodTipoDecreto_'.$cont]:'null'));
        $obErro = $obTTCEMGTipoRegistroPreco->alteracao( $boTransacao );
    } else {
        $obTTCEMGTipoRegistroPreco->setDado("cod_tipo_decreto",($_REQUEST['inCodTipoDecreto_'.$cont]?$_REQUEST['inCodTipoDecreto_'.$cont]:'null'));
        $obErro = $obTTCEMGTipoRegistroPreco->inclusao( $boTransacao );
    }
    $cont++;
}



if (!$obErro->ocorreu()) {
    $obErro = $obTransacao->commitAndClose();
} else {
    $obTransacao->rollbackAndClose();
}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgFilt ,"Configuração atualizada", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}
