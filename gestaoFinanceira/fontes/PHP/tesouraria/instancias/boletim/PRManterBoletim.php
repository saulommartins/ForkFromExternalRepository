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

    $Revision: 31732 $
    $Name$
    $Author: eduardoschitz $
    $Date: 2008-03-26 11:31:58 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.04.17 , uc-02.04.25
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_NEGOCIO.'RTesourariaBoletim.class.php';

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterBoletim";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stNow = date( 'Y-m-d H:i:s.ms' );
$obErro = new Erro();

SistemaLegado::BloqueiaFrames();

if ($stAcao == "incluir") {
    if ($request->get('boMultiBoletim')) {
        
        include_once( CAM_GF_TES_NEGOCIO."RTesourariaTerminal.class.php" );
        
        foreach ($_REQUEST as $campo => $valor) {
            if (!$obErro->ocorreu()) {
                if (substr($campo,0,8) == 'boFechar') {
                    $inCodBoletim = str_replace('boFechar_','',$campo);
                    $inCodBoletim = substr($inCodBoletim,0,strpos($inCodBoletim,'_'));

                    $obRegra = new RTesourariaBoletim();
                    $obRegra->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
                    $obRegra->setCodBoletim( $inCodBoletim );
                    $obRegra->setExercicio( Sessao::getExercicio() );
                    $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->addUsuarioTerminal();
                    $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->roUltimoUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));

                    $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->addUsuarioTerminal();
                    $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->listarSituacaoPorBoletim( $rsLista, $obRegra, 'aberto');
                    if (!$rsLista->eof()) { //  Caso o terminal de caixa não esteja fechado ainda
                        $obRegra->obRTesourariaUsuarioTerminal->setTimestampUsuario($rsLista->getCampo('timestamp_usuario') );

                        $obRTesourariaTerminal = new RTesourariaTerminal;
                        $obRTesourariaTerminal->setCodTerminal        ($rsLista->getCampo('cod_terminal'));
                        $obRTesourariaTerminal->setTimestampTerminal  ($rsLista->getCampo('timestamp_terminal'));
                        $obRTesourariaTerminal->setTimestampAbertura  ($rsLista->getCampo('timestamp_abertura'));
                        $obRTesourariaTerminal->fecharTerminal( $obRegra, $boTransacao );

                        $obRegra->setCodBoletim($inCodBoletim); // Fecha o boletim com os dados do terminal de caixa de quem o abriu.
                        $obRegra->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
                        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal($rsLista->getCampo('cod_terminal') );
                        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal($rsLista->getCampo('timestamp_terminal') );
                        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->addUsuarioTerminal();
                        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->roUltimoUsuario->obRCGM->setNumCGM($rsLista->getCampo('cgm_usuario') );
                    } else { // Se já estiver fechado o terminal de caixa que o boletim foi aberto, fecha o boletim com os dados
                             // de quem esta fechando o boletim (usuario e terminal local)
                        $obRegra->setCodBoletim($inCodBoletim);
                        $obRegra->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
                        $obRegra->obRTesourariaUsuarioTerminal->setTimestampUsuario($_REQUEST['stTimestampUsuario'] );
                        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal($_REQUEST['inCodTerminal'] );
                        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal($_REQUEST['stTimestampTerminal'] );
                        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->addUsuarioTerminal();
                        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->roUltimoUsuario->obRCGM->setNumCGM(Sessao::read('numCgm') );
                    }

                    $obErro = $obRegra->fecharBoletim( $boTransacao );
                    if(!$obErro->ocorreu())
                        $inCount++;
                    unset($obRegra);
                    unset($obRTesourariaTerminal);
                }
            }
        }
        if (!$obErro->ocorreu()) {
            $stBoletins = substr($stBoletins,0,strlen($stBoletins)-1);
            SistemaLegado::alertaAviso($pgForm,$inCount > 1 ? $inCount." Boletins foram fechados": "$inCodBoletim/".Sessao::getExercicio(),$stAcao,"aviso", Sessao::getId()."&stAcao=".$stAcao, "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");
            SistemaLegado::LiberaFrames();
        }
    } else {
        $obRegra = new RTesourariaBoletim();
        $obRegra->setCodBoletim($_REQUEST["inCodBoletim"]);
        $obRegra->setExercicio($_REQUEST["stExercicio"]);
        $obRegra->obROrcamentoEntidade->setCodigoEntidade($_REQUEST["inCodEntidade"]);
        $obRegra->obRTesourariaUsuarioTerminal->setTimestampUsuario($_REQUEST["stTimestampUsuario"]);
        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal($_REQUEST["inCodTerminal"]);
        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal($_REQUEST["stTimestampTerminal"]);
        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->addUsuarioTerminal();
        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->roUltimoUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));

        $obErro = $obRegra->fecharBoletim( $boTransacao );

        if ( !$obErro->ocorreu() ) {
             SistemaLegado::alertaAviso($pgForm,$_REQUEST["inCodBoletim"] . "/" . $_REQUEST["stExercicio"],$stAcao,"aviso", Sessao::getId()."&stAcao=".$stAcao, "../");
} else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");
            SistemaLegado::LiberaFrames();
        }
    }

} elseif ($stAcao == "alterar") {
    include_once ( CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php" );
    $obRConfiguracaoConfiguracao = new RConfiguracaoConfiguracao;
    $obRConfiguracaoConfiguracao->setParametro( "virada_GF" );
    $obRConfiguracaoConfiguracao->setExercicio( Sessao::getExercicio() );
    $obRConfiguracaoConfiguracao->setCodModulo( 10 );
    $obRConfiguracaoConfiguracao->verificaParametro( $boExiste, $boTransacao );
    if (!$boExiste) {
        $obRegra = new RTesourariaBoletim();
        $obRegra->setCodBoletim($_REQUEST["inCodBoletim"]);
        $obRegra->setExercicio($_REQUEST["stExercicio"]);
        $obRegra->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampFechamento($_REQUEST["stTimestampFechamento"]);
        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal($_REQUEST["inCodTerminal"]);
        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal($_REQUEST["stTimestampTerminal"]);
        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->addUsuarioTerminal();
        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->roUltimoUsuario->obRCGM->setNumCGM($_REQUEST["inCgmUsuario"]);
        $obRegra->obRTesourariaUsuarioTerminal->setTimestampUsuario($_REQUEST["stTimestampUsuario"]);

        $obErro = $obRegra->reabrirBoletim( $boTransacao );

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgFilt,"Boletim ".$_REQUEST["inCodBoletim"] . "/" . $_REQUEST["stExercicio"],$stAcao,"aviso", Sessao::getId()."&stAcao=".$stAcao, "../");
        else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");
            SistemaLegado::LiberaFrames();
        }
    } else {
        SistemaLegado::alertaAviso($pgFilt,"Já foi efetuado o encerramento do exercício","n_".$stAcao,"erro", Sessao::getId()."&stAcao=".$stAcao, "../");
    }

}

?>