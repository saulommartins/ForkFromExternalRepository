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
    * Página de processamento para cancelamento de nota fiscal avulsa
    * Data de Criação   : 24/06/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: $

    * Casos de uso: uc-05.03.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRNotaAvulsaCancelada.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarneDevolucao.class.php" );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "AnularNotaAvulsa";
$pgFilt = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?stAcao=$stAcao";

switch ($stAcao) {
    case "anular":
        $obTARRCarneDevolucao = new TARRCarneDevolucao;
        $obTARRNotaAvulsaCancelada = new TARRNotaAvulsaCancelada;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTARRNotaAvulsaCancelada );

            $obTARRCarneDevolucao->setDado( "numeracao", $_REQUEST["inNumeracao"] );
            $obTARRCarneDevolucao->setDado( "cod_convenio", $_REQUEST["inCodConvenio"] );
            $obTARRCarneDevolucao->setDado( "cod_motivo", 99 );
            $obTARRCarneDevolucao->setDado( "dt_devolucao", date( "d-m-Y" ) );
            $obTARRCarneDevolucao->inclusao();

            $obTARRNotaAvulsaCancelada->setDado( "cod_nota", $_REQUEST["inCodNota"] );
            $obTARRNotaAvulsaCancelada->setDado( "numcgm_usuario", Sessao::read('numCgm') );
            $obTARRNotaAvulsaCancelada->setDado( "observacao", $_REQUEST["stObservacao"] );
            $obTARRNotaAvulsaCancelada->setDado( "dt_cancelamento", date( "d-m-Y" ) );
            $obTARRNotaAvulsaCancelada->inclusao();

        Sessao::encerraExcecao();

        SistemaLegado::alertaAviso( $pgFilt, "Inscrição Econômica: ".$_REQUEST["inInscricaoEconomica"], "incluir", "aviso", Sessao::getId(), "../");
        break;
}
?>
