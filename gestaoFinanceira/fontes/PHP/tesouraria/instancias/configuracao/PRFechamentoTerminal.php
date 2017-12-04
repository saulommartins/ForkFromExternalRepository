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
    * Data de Criação   : 11/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 32140 $
    $Name$
    $Author: luciano $
    $Date: 2007-08-01 21:26:05 -0300 (Qua, 01 Ago 2007) $

    * Casos de uso: uc-02.04.06
*/

/*
$Log$
Revision 1.21  2007/08/02 00:26:05  luciano
Bug#9774#

Revision 1.20  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaTerminal.class.php" );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"  );
include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaAbertura.class.php"  );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "FechamentoTerminal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stNow = date( 'Y-m-d H:i:s.ms' );

$obErro = new Erro();

$obRegra = new RTesourariaTerminal();
$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );

if ($_REQUEST["stFecharTerminal"]=="I") {

    $obRegra->addUsuarioTerminal();
    if ( Sessao::read('numCgm') < 1 ) {
        list( $_REQUEST['inCodEntidade'], $_REQUEST['inCodTerminal'] ) = explode( '-', $_REQUEST['inCodTerminal'] );
        $obRegra->roUltimoUsuario->obRCGM->setNumCGM( null );
    } else {
        $obRegra->roUltimoUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
    }

    $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );

    $obRegra->setCodTerminal( $_REQUEST['inCodTerminal'] );
    $obRegra->listarSituacaoPorBoletim( $rsTerminal, $obRTesourariaBoletim, 'aberto' );
    $obTTesourariaAbertura = new TTesourariaAbertura();
    $obTTesourariaAbertura->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
    $obTTesourariaAbertura->setDado('cod_terminal',$_REQUEST['inCodTerminal']);
    $obTTesourariaAbertura->recuperaMaxAbertura($rsAbertura);

    while (!$rsAbertura->eof()) {

        $obRTesourariaBoletim->setCodBoletim( $rsAbertura->getCampo( "cod_boletim" ) );

        $obRegra->setCodTerminal        ($rsAbertura->getCampo('cod_terminal'));
        $obRegra->setTimestampTerminal  ($rsAbertura->getCampo('timestamp_terminal'));
        $obRegra->setTimestampAbertura  ($rsAbertura->getCampo('timestamp_abertura'));
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->setNumCGM($rsAbertura->getCampo('cgm_usuario'));
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->setTimestampUsuario($rsAbertura->getCampo('timestamp_usuario'));

        if ($stAcao=='incluir') {
            $obErro = $obRegra->fecharTerminal( $obRTesourariaBoletim, $boTransacao );
        }

        $rsAbertura->proximo();
   }

   $message = $_REQUEST["inCodTerminal"];

} else {

    $message = "Todos os registros foram fechados";

    if($stAcao=='incluir')
        $obErro = $obRegra->fecharTodosTerminais( $boTransacao );
}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm,$message,$stAcao,"aviso", Sessao::getId()."&stAcao=".$stAcao, "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");
}
?>
