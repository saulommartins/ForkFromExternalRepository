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
    * Página de Listagem de Terminais
    * Data de Criação   : 03/11/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-02-23 17:17:31 -0200 (Sex, 23 Fev 2007) $

    * Casos de uso: uc-02.04.17  , uc-02.04.25
*/

/*
$Log$
Revision 1.13  2007/02/23 19:16:05  cako
Bug #8395#

Revision 1.12  2006/10/23 18:34:58  domluc
Add Caso de Uso Boletim

Revision 1.11  2006/07/05 20:39:03  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBoletim";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRTesourariaBoletim = new RTesourariaBoletim;

$rsLista = new RecordSet();

$obRTesourariaBoletim->setCodBoletim( $_REQUEST["inCodBoletim"] );
$obRTesourariaBoletim->setDataBoletim( $_REQUEST["stDtBoletim"] );
$obRTesourariaBoletim->setExercicio ( Sessao::getExercicio() );
$obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
$obRTesourariaBoletim->listarBoletimFechado($rsLista, "ORDER BY cod_boletim" );

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->ultimoCabecalho->addConteudo("Boletim");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data do Boletim");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data do Fechamento");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Usuário");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_boletim" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "data_boletim" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_fechamento" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cgm_usuario] - [nom_cgm]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "Reabrir");
$obLista->ultimaAcao->addCampo( "&inCodBoletim"         , "cod_boletim"          );
$obLista->ultimaAcao->addCampo( "stExercicio"           , "exercicio"            );
$obLista->ultimaAcao->addCampo( "inCodEntidade"         , "cod_entidade"         );
$obLista->ultimaAcao->addCampo( "stTimestampFechamento" , "timestamp_fechamento" );
$stLink  = $pgProc."?stAcao=".$_REQUEST["stAcao"]."&".Sessao::getId()."&inCodTerminal=".$_REQUEST['inCodTerminal']."&stTimestampTerminal=";
$stLink .= $_REQUEST['stTimestampTerminal']."&inCgmUsuario=".Sessao::read('numCgm')."&stTimestampUsuario=".$_REQUEST['stTimestampUsuario'];
$obLista->ultimaAcao->setLink( $stLink );
$obLista->commitAcao();

$obLista->show();

if ( $rsLista->eof() ) {
    SistemaLegado::exibeAviso("Verifique se há Boletins abertos ou liberados para contabilidade!","","");
}

?>
