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
   /*
    * Página de Processamento de Consideração - Execução Variação
    * Data de Criação: 03/09/2013

    * @author Analista:
    * @author Desenvolvedor: Grace Mungunda Waka

    * @ignore

    * Casos de uso:

    $Id:

    $Id:$
    */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGExecucaoVariacao.class.php" );

$stPrograma = "ConsideracaoExecucaoVariacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );

$obTTCEMGConsideracao = new TTCEMGExecucaoVariacao();

$obErro = new Erro;
$obTransacao = new Transacao;
$obTransacao->begin();
$boTransacao = $obTransacao->getTransacao();

    $obTTCEMGConsideracao->setDado("cod_mes",$_REQUEST['inMes'] );
    $obTTCEMGConsideracao->setDado("exercicio", Sessao::getExercicio());
    $obTTCEMGConsideracao->recuperaRelacionamento( $rsConsideracao );

    if (!$rsConsideracao->eof()) {
        $obTTCEMGConsideracao->setDado( 'cons_adm_dir'      , $_REQUEST['stAdmDireta']       );
        $obTTCEMGConsideracao->setDado( 'cons_aut'          , $_REQUEST['stConsAut']         );
        $obTTCEMGConsideracao->setDado( 'cons_fund'         , $_REQUEST['stFund']            );
        $obTTCEMGConsideracao->setDado( 'cons_empe_est_dep' , $_REQUEST['stEmpEstDep']       );
        $obTTCEMGConsideracao->setDado( 'cons_dem_ent'      , $_REQUEST['stDemaisEntidades'] );
        $obTTCEMGConsideracao->alteracao();
    } else {
        $obTTCEMGConsideracao->setDado("cod_mes",$_REQUEST['inMes'] );
        $obTTCEMGConsideracao->setDado("exercicio", Sessao::getExercicio());
        $obTTCEMGConsideracao->setDado( 'cons_adm_dir'      , $_REQUEST['stAdmDireta']       );
        $obTTCEMGConsideracao->setDado( 'cons_aut'          , $_REQUEST['stConsAut']         );
        $obTTCEMGConsideracao->setDado( 'cons_fund'         , $_REQUEST['stFund']            );
        $obTTCEMGConsideracao->setDado( 'cons_empe_est_dep' , $_REQUEST['stEmpEstDep']       );
        $obTTCEMGConsideracao->setDado( 'cons_dem_ent'      , $_REQUEST['stDemaisEntidades'] );
        $obTTCEMGConsideracao->inclusao();
    }

SistemaLegado::alertaAviso($pgFilt, "Consideracao", "incluir", "aviso", '', "../");
Sessao::encerraExcecao();
?>
