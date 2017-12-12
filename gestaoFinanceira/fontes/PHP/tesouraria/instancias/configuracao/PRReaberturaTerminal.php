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
    * Data de Criação   : 27/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 32140 $
    $Name$
    $Author: luciano $
    $Date: 2007-08-01 21:47:52 -0300 (Qua, 01 Ago 2007) $

    * Casos de uso: uc-02.04.06
*/

/*
$Log$
Revision 1.9  2007/08/02 00:42:09  luciano
Bug#9774#

Revision 1.8  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaTerminal.class.php" );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"  );
include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaFechamento.class.php"  );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ReaberturaTerminal";
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

if ($_REQUEST["stReabrirTerminal"]=="I") {

    $obRegra->addUsuarioTerminal();
    if ( Sessao::read('numCgm') < 1 ) {
        list( $_REQUEST['inCodEntidade'], $_REQUEST['inCodTerminal'] ) = explode( '-', $_REQUEST['inCodTerminal'] );
        $obRegra->roUltimoUsuario->obRCGM->setNumCGM( null );
    } else {
        $obRegra->roUltimoUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));
    }

    $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );

    $obRegra->setCodTerminal( $_REQUEST['inCodTerminal'] );
    $obRegra->listarSituacaoPorBoletim( $rsTerminal, $obRTesourariaBoletim, 'fechado' );

    $obTTesourariaFechamento = new TTesourariaFechamento();
    $obTTesourariaFechamento->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
    $obTTesourariaFechamento->setDado('cod_terminal',$_REQUEST['inCodTerminal']);
//    $stFiltro = " WHERE cod_entidade = ".$_REQUEST['inCodEntidade']." AND ";
//    $stFiltro.= "       cod_terminal = ".$_REQUEST['inCodTerminal']." AND ";
    if ( Sessao::read('numCgm') > 0 ) {
//        $stFiltro.= " cgm_usuario = ".Sessao::read('numCgm')." AND ";
    }
    $obTTesourariaFechamento->recuperaMaxFechamento($rsFechamento);

    while (!$rsFechamento->eof()) {

        $obRTesourariaBoletim->setCodBoletim( $rsFechamento->getCampo( "cod_boletim" ) );
        $obRegra->setCodTerminal        ( $_REQUEST["inCodTerminal"] );
        $obRegra->setTimestampTerminal  ( $rsFechamento->getCampo('timestamp_terminal') );
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->setNumCGM($rsFechamento->getCampo('cgm_usuario'));
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->setTimestampUsuario($rsFechamento->getCampo('timestamp_usuario'));

        if ($stAcao=='incluir') {
            $obErro = $obRegra->abrirTerminal( $obRTesourariaBoletim, $boTransacao );
        }

        $rsFechamento->proximo();
    }

    $message = $_REQUEST["inCodTerminal"];

} else {

    $message = "Todos os terminais foram reabertos";
    if($stAcao=='incluir')
        $obErro = $obRegra->abrirTodosTerminais( $boTransacao );
}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm,$message,$stAcao,"aviso", Sessao::getId()."&stAcao=".$stAcao, "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");
}

?>
