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

    * Página de Processamento
    * Data de Criação   : 25/02/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @ignore
    *
    * $Id: PRManterConfiguracaoConvenioConta.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * $Revision: 59612 $
    * $Author: gelson $
    * $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConvenioPlanoBanco.class.php");


//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoConvenioConta";
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

$obTCEMGContaConvenio = new TTCEMGConvenioPlanoBanco();
$obTCEMGContaConvenio->setDado    ('exercicio', Sessao::getExercicio() );
$obTCEMGContaConvenio->setDado    ('cod_entidade', $_POST[ 'inCodEntidade' ] );
$obTCEMGContaConvenio->recuperaPlanoContaAnalitica( $rsConvenios ) ;

$cont= 1;
foreach ($rsConvenios->arElementos as $arConvenio) {
    
    $obTCEMGContaConvenio = new TTCEMGConvenioPlanoBanco();
    $obTCEMGContaConvenio->setDado("cod_entidade", $_POST['inCodEntidade'] );
    $obTCEMGContaConvenio->setDado("cod_plano", $arConvenio["cod_plano"]);
    $obTCEMGContaConvenio->setDado("exercicio", $arConvenio["exercicio"] );
    $obTCEMGContaConvenio->setDado("num_convenio", $arConvenio["num_convenio"]);
    $obTCEMGContaConvenio->setDado("dt_assinatura", $arConvenio["dt_assinatura"]);
    $obErro = $obTCEMGContaConvenio->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
        $obTCEMGContaConvenio->setDado("num_convenio",($_REQUEST['inNumConvenio_'.$cont]?$_REQUEST['inNumConvenio_'.$cont]:'null') );
        $obTCEMGContaConvenio->setDado("dt_assinatura", $_REQUEST['dtAssinatura_'.$cont]);
        $obErro = $obTCEMGContaConvenio->alteracao( $boTransacao );
    } else {
        if ($_REQUEST['inNumConvenio_'.$cont] != "" &&  $_REQUEST['dtAssinatura_'.$cont] != "") {
            $obTCEMGContaConvenio->setDado("num_convenio", $_REQUEST['inNumConvenio_'.$cont]);
            $obTCEMGContaConvenio->setDado("dt_assinatura", $_REQUEST['dtAssinatura_'.$cont]);
            $obErro = $obTCEMGContaConvenio->inclusao( $boTransacao );
        }
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
