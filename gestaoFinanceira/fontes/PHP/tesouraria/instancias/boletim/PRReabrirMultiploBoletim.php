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
    * Página de Processamento para abertura de multiplos boletins
    * Data de Criação   : 13/10/2006

    * @author Analista: Lucas Teixeira Stephanou
    * @author Desenvolvedor:  Lucas Teixeira Stephanou

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: melo $
    $Date: 2008-03-19 17:25:41 -0300 (Qua, 19 Mar 2008) $

    * Casos de uso: uc-02.04.17 , uc-02.04.25
*/

/*
$Log$
Revision 1.7  2007/08/30 16:05:10  cako
Bug#9982#

Revision 1.6  2007/08/24 20:24:51  cako
Bug#9982#

Revision 1.5  2007/08/23 21:14:08  cako
Bug#9982#

Revision 1.4  2007/02/23 19:16:05  cako
Bug #8395#

Revision 1.3  2007/01/10 16:07:08  cako
Bug #8034#

Revision 1.2  2006/10/23 18:34:58  domluc
Add Caso de Uso Boletim

Revision 1.1  2006/10/23 16:33:08  domluc
Add opção para multiplos boletins

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$pgFilt = "FLReabrirMultiploBoletim.php";
$pgOcul = "OCReabrirMultiploBoletim.php";

SistemaLegado::BloqueiaFrames();

$stNow = date( 'Y-m-d H:i:s.ms' );

$obErro = new Erro();

$obRegra = new RTesourariaBoletim();

if ($stAcao == "reabrir") {
    include_once ( CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php" );
    $obRConfiguracaoConfiguracao = new RConfiguracaoConfiguracao;
    $obRConfiguracaoConfiguracao->setParametro( "virada_GF" );
    $obRConfiguracaoConfiguracao->setExercicio( Sessao::getExercicio() );
    $obRConfiguracaoConfiguracao->setCodModulo( 10 );
    /*
        Alterei o método usado pois, quando é feito a virada, o parametro virada_GF na configuração
        é incluido com valor 'T', e quando é desfeito a virada o valor desse parametro é alterado
        para 'F'. Porém o método verificaParametro não trata do valor do parâmetro e sim se ele
        existe ou não. Neste caso o correto é verificar se, ou existe o parâmetro e, se existe, que
        ele seja com valor 'F'.
    */
    //$obRConfiguracaoConfiguracao->verificaParametro( $boExiste, $boTransacao );
    $obRConfiguracaoConfiguracao->consultar( $boExiste, $boTransacao );
    //if (!$boExiste) {

    if ( $obRConfiguracaoConfiguracao->getValor() == '' || ($obRConfiguracaoConfiguracao->getValor() == 'F')) {
        foreach ($_REQUEST as $campo => $valor) {
            if (!$obErro->ocorreu()) {
                if (substr($campo,0,9) == 'boReabrir') {
                    $inCodBoletim = str_replace('boReabrir_','',$campo);
                    $inCodBoletim = substr($inCodBoletim,0,strpos($inCodBoletim,'_'));

                    $obRegra->setCodBoletim($inCodBoletim );
                    $obRegra->setExercicio(Sessao::getExercicio());
                    $obRegra->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
                    $obErro = $obRegra->listarBoletimFechado( $rsBoletimFechado );

                    if ( !$obErro->ocorreu() && !$rsBoletimFechado->eof() ) {
                        $obRegra->setCodBoletim( $rsBoletimFechado->getCampo('cod_boletim') );
                        $obRegra->setExercicio( $rsBoletimFechado->getCampo('exercicio') );
                        $obRegra->obROrcamentoEntidade->setCodigoEntidade( $rsBoletimFechado->getCampo('cod_entidade') );
                        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampFechamento( $rsBoletimFechado->getCampo('timestamp_fechamento') );
                        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal($_REQUEST["inCodTerminal"]);
                        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal($_REQUEST["stTimestampTerminal"]);
                        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->addUsuarioTerminal();
                        $obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->roUltimoUsuario->obRCGM->setNumCGM($_REQUEST["inCgmUsuario"]);
                        $obRegra->obRTesourariaUsuarioTerminal->setTimestampUsuario($_REQUEST["stTimestampUsuario"]);
                        $obErro = $obRegra->reabrirBoletim( $boTransacao );
                        if(!$obErro->ocorreu())
                            $inCount++;
                    }
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            if($inCount == 1)
                 $stMensagem = "Reabrir Boletim concluído com sucesso! "."(Boletim ".$inCodBoletim."/".Sessao::getExercicio().')';
            else $stMensagem = "Reabrir Boletim concluído com sucesso! ".'('.$inCount." Boletins foram reabertos)";

            SistemaLegado::alertaAviso($pgFilt."?stAcao=".$stAcao."&inCodEntidade=".$_REQUEST['inCodEntidade'],$stMensagem,$stAcao,"aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");
        }
    } else {
        SistemaLegado::exibeAviso("Erro ao Reabrir Boletim! (Já foi efetuado o encerramento do exercício)","n_".$stAcao,"erro");
        SistemaLegado::LiberaFrames();
    }
}

?>
