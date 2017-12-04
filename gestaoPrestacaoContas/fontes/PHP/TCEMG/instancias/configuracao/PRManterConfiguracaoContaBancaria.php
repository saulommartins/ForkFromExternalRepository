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
    * $Id: PRManterConfiguracaoContaBancaria.php 59842 2014-09-15 19:23:06Z lisiane $
    *
    * $Revision: $
    * $Author: $
    * $Date: $
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGContaBancaria.class.php");
//Define o nome dos arquivos PHP

$stPrograma = "ManterConfiguracaoContaBancaria";
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
$stOrdem = Sessao::read('stOrdem');
$obTCEMGContaBancaria = new TTCEMGContaBancaria;
$obTCEMGContaBancaria->setDado    ('exercicio', Sessao::getExercicio() );
$obTCEMGContaBancaria->setDado('cod_entidade', $_POST[ 'inCodEntidade' ] );
$obTCEMGContaBancaria->recuperaPlanoContaAnalitica( $rsContas, "", $stOrdem ) ;
$cont= 1;

foreach ($rsContas->arElementos as $arContas) {
    $obTCEMGContaBancaria = new TTCEMGContaBancaria;
    $obTCEMGContaBancaria->setDado("cod_entidade", $_POST['inCodEntidade'] );
    $obTCEMGContaBancaria->setDado("sequencia", $cont);
    $obTCEMGContaBancaria->setDado("cod_conta", $arContas["cod_conta"]);
    $obTCEMGContaBancaria->setDado("exercicio", $arContas["exercicio"] );
    $obTCEMGContaBancaria->setDado("cod_tipo_aplicacao", $arContas["cod_tipo_aplicacao"]);
    $obErro = $obTCEMGContaBancaria->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
        $obTCEMGContaBancaria->setDado("cod_tipo_aplicacao", ($_REQUEST['inCodTipoAplicacao_'.$cont]?$_REQUEST['inCodTipoAplicacao_'.$cont]:'null'));
        $obTCEMGContaBancaria->setDado("cod_ctb_anterior", ($_REQUEST['inCodCTBAnterior_'.$cont]?$_REQUEST['inCodCTBAnterior_'.$cont]:'null'));
        $obErro = $obTCEMGContaBancaria->alteracao( $boTransacao );
    } else {
        $obTCEMGContaBancaria->setDado("cod_tipo_aplicacao",($_REQUEST['inCodTipoAplicacao_'.$cont]?$_REQUEST['inCodTipoAplicacao_'.$cont]:'null'));
        $obTCEMGContaBancaria->setDado("cod_ctb_anterior", ($_REQUEST['inCodCTBAnterior_'.$cont]?$_REQUEST['inCodCTBAnterior_'.$cont]:'null'));
        $obErro = $obTCEMGContaBancaria->inclusao( $boTransacao );
    }
    $cont++;
}
//

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
