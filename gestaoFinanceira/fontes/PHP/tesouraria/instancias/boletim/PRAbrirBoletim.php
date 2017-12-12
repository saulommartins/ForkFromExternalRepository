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
    * Página de Processamento de Configuração do módulo Tesouraria
    * Data de Criação   : 31/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-03-22 10:28:31 -0300 (Qui, 22 Mar 2007) $

    * Casos de uso: uc-02.04.25
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php";
include_once CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php";

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "AbrirBoletim";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obErro = new Erro();
$obRegra = new RTesourariaBoletim();

if ($stAcao == "incluir") {
    $arDataBoletim = preg_split("/\//", $_REQUEST['stDtBoletim']);
    
    if ( $arDataBoletim[2] != Sessao::getExercicio()) {
        SistemaLegado::exibeAviso("A data do boletim deve ser dentro do exercício atual: ".Sessao::getExercicio(),"n_".$stAcao,"erro");
    }
    
    $obRConfiguracaoConfiguracao = new RConfiguracaoConfiguracao;
    $obRConfiguracaoConfiguracao->setParametro( "virada_GF" );
    $obRConfiguracaoConfiguracao->setExercicio( Sessao::getExercicio() );
    $obRConfiguracaoConfiguracao->setCodModulo( 10 );
    $obRConfiguracaoConfiguracao->consultar();
    $stVirada = $obRConfiguracaoConfiguracao->getValor();
    
    if ( strtolower($stVirada) != 't' or $stVirada != 1 ) {
        $obRegra->setExercicio(Sessao::getExercicio());
        $obRegra->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obRegra->setDataBoletim( $_REQUEST['stDtBoletim'] );
        $obRegra->listar( $rsBoletim );

        if ($rsBoletim->getNumLinhas() > 0) {
            SistemaLegado::exibeAviso("Já existe um boletim para a data ".$_REQUEST["stDtBoletim"]."! (Boletim ".$rsBoletim->getCampo("cod_boletim")."/".$rsBoletim->getCampo("exercicio").")","n_".$stAcao,"erro");
        } else {
            list($dia2,$mes2,$ano2) = preg_split("/\//",$_REQUEST["stDtBoletim"]);

            if ( ("$ano2$mes2$dia2" <= date("Ymd")) ) {

                $obRegra->setDataBoletim( false );
                $obRegra->listar( $rsBoletim, "", " dt_boletim DESC");

                list($ano3,$mes3,$dia3) = preg_split("/-/",$rsBoletim->getCampo("dt_boletim"));
                $stUltimoBoletim = $rsBoletim->getCampo('cod_boletim');

                if ($ano3.$mes3.$dia3 < $ano2.$mes2.$dia2) {
                    $obRegra->setCodBoletim($_REQUEST["inCodBoletim"]);
                    $obRegra->setDataBoletim($_REQUEST["stDtBoletim"]);
                    $obRegra->obRTesourariaUsuarioTerminal->setTimestampUsuario($_REQUEST["stTimestampUsuario"]);
                    $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal($_REQUEST["inCodTerminal"]);
                    $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal($_REQUEST["stTimestampTerminal"]);
                    $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->addUsuarioTerminal();
                    $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->roUltimoUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));
                    $obErro = $obRegra->incluir( $boTransacao );

                    if ( !$obErro->ocorreu() )
                        SistemaLegado::alertaAviso($pgForm,"Boletim ".$_REQUEST["inCodBoletim"] . "/" . Sessao::getExercicio(), $stAcao,"aviso", Sessao::getId()."&stAcao=".$stAcao, "../");
                    else
                        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");
                } else {
                    SistemaLegado::exibeAviso("A data do boletim deve ser posterior à do ultimo existente (Boletim ".$stUltimoBoletim." - ".$dia3."/".$mes3."/".$ano3.")","n_".$stAcao,"erro");
                }
            } else {
                SistemaLegado::exibeAviso("A data do boletim deve ser igual ou menor que a data atual.","n_".$stAcao,"erro");
            }
        }
    } else {
        SistemaLegado::exibeAviso("Já foi efetuado o encerramento do exercício","n_".$stAcao,"erro");
    }

}

?>