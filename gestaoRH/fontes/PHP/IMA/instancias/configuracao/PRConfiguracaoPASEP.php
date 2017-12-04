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
    * Página de processamento do IMA Configuração - PASEP
    * Data de Criação: 29/05/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.22

    $Id: PRConfiguracaoPASEP.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoPasep.class.php"                        );
include_once( CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php" 										 );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
//Define o nome dos arquivos PHP
$stPrograma = "ConfiguracaoPASEP";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";

switch ($stAcao) {
    case "configurar":
        Sessao::setTrataExcecao(true);
        $obTIMAConfiguracaoPasep = new TIMAConfiguracaoPasep;
        $obTIMAConfiguracaoPasep->recuperaRelacionamento($rsDados);

        if ($_POST['stNumAgenciaTxt']) {
            $obTMONAgencia = new TMONAgencia;
            $stFiltro = " where num_agencia = '".$_POST['stNumAgenciaTxt']."'";
            $obTMONAgencia->recuperaTodos($rsAgencia, $stFiltro);
            $cod_agencia = $rsAgencia->getCampo('cod_agencia');
        }
        if ($_POST["inCodigoEventoPasep"]) {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
            $stFiltro = " WHERE codigo = '".$_POST["inCodigoEventoPasep"]."'";
            $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);
            $inCodEvento = $rsEvento->getCampo("cod_evento");
        }

        $obTIMAConfiguracaoPasep->setDado("num_convenio", $_POST['stCodEmpresa']);
        $obTIMAConfiguracaoPasep->setDado("cod_banco", Sessao::read('BANCO'));
        $obTIMAConfiguracaoPasep->setDado("cod_agencia", $cod_agencia );
        $obTIMAConfiguracaoPasep->setDado("cod_conta_corrente", $_POST['inTxtContaCorrente']);
        $obTIMAConfiguracaoPasep->setDado("cod_evento", $inCodEvento);
        $obTIMAConfiguracaoPasep->setDado("email", $_POST['stEmailContato']);

        if ($rsDados->getNumLinhas() > 0) {
            $obTIMAConfiguracaoPasep->setDado("cod_convenio", $rsDados->getCampo('cod_convenio'));
            $obTIMAConfiguracaoPasep->alteracao();
        } else {
            $obTIMAConfiguracaoPasep->inclusao();
        }

           Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgForm,"Configuração do PASEP concluída com sucesso!","incluir","aviso", Sessao::getId(), "../");
        break;
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
