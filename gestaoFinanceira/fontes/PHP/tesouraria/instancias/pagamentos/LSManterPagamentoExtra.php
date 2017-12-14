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
    * Página de Listagem de Pagamentos Extra a Estornar
    * Data de Criação   : 05/09/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 31732 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.27
*/

/*
$Log$
Revision 1.5  2007/08/08 21:31:54  vitor
Bug#9855#

Revision 1.4  2006/11/22 22:37:30  cleisson
Bug #7582#

Revision 1.3  2006/09/18 11:07:02  cako
implementação do uc-02.04.27

Revision 1.2  2006/09/14 15:04:33  cako
implementação do uc-02.04.27

Revision 1.1  2006/09/14 10:26:26  cako
implementaçao do uc-02.04.27

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPagamentoExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FMManterPagamentoExtraEstorno.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;

$arLink = Sessao::read('link');
if ($_GET["pg"] and  $_GET["pos"]) {
   $stLink .= "&pg=".$_GET["pg"];
   $stLink .= "&pos=".$_GET["pos"];
   $arLink["pg"]  = $_GET["pg"];
   $arLink["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ($arLink) {
    $_REQUEST = $arLink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
}
Sessao::write('link', $arLink);

include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransferencia.class.php" );
$obTTesourariaTransferencia = new TTesourariaTransferencia();
$obTTesourariaTransferencia->setDado('stExercicio',         Sessao::getExercicio()      );
if ($_REQUEST['inCodRecibo'])       $obTTesourariaTransferencia->setDado('inCodRecibo',         $_REQUEST['inCodRecibo']      );
if ($_REQUEST['inCodEntidade'])     $obTTesourariaTransferencia->setDado('inCodEntidade',       $_REQUEST['inCodEntidade']    );
if ($_REQUEST['stDtBoletim'])       $obTTesourariaTransferencia->setDado('stDtBoletim',         $_REQUEST['stDtBoletim']      );
if ($_REQUEST['inCodBoletim'])      $obTTesourariaTransferencia->setDado('inCodBoletim',        $_REQUEST['inCodBoletim']     );
if ($_REQUEST['inCodCredor'])       $obTTesourariaTransferencia->setDado('inCodCredor',         $_REQUEST['inCodCredor']      );
if ($_REQUEST['inCodRecurso'])      $obTTesourariaTransferencia->setDado('inCodRecurso',        $_REQUEST['inCodRecurso']     );
if ($_REQUEST['inCodPlanoDebito'])  $obTTesourariaTransferencia->setDado('inCodPlanoDebito',    $_REQUEST['inCodPlanoDebito'] );
if ($_REQUEST['inCodPlanoCredito']) $obTTesourariaTransferencia->setDado('inCodPlanoCredito',   $_REQUEST['inCodPlanoCredito']);
                                    $obTTesourariaTransferencia->setDado('inCodTipo', 1 ); // tesouraria.tipo_transferencia -> Pagamento Extra

if($_REQUEST['inCodUso'] && $_REQUEST['inCodDestinacao'] && $_REQUEST['inCodEspecificacao'])
    $obTTesourariaTransferencia->setDado('stDestinacaoRecurso', $_REQUEST['inCodUso'].".".$_REQUEST['inCodDestinacao'].".".$_REQUEST['inCodEspecificacao'] );

    $obTTesourariaTransferencia->setDado('inCodDetalhamento', $_REQUEST['inCodDetalhamento'] );

$stFiltro .= " AND (coalesce(t.valor,0.00) - coalesce(te.valor,0.00)) > 0.00 ";

$obTTesourariaTransferencia->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $boTransacao );

$rsLista->addFormatacao( 'valor' , 'NUMERIC_BR' );

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Recibo");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Boletim");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Lote");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Conta Despesa");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Conta a Débito");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Recurso");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_recibo" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_boletim]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_boletim" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_lote]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_plano_debito] - [nom_conta_debito]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_plano_credito] - [nom_conta_credito]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "recurso" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[valor]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->addAcao();

$obLista->ultimaAcao->setAcao( 'Estornar' );

$obLista->ultimaAcao->addCampo( "&inCodRecibo"       , "cod_recibo" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"     , "cod_entidade"      );
$obLista->ultimaAcao->addCampo( "&stNomEntidade"     , "nom_entidade"      );
$obLista->ultimaAcao->addCampo( "&inCodLote"         , "cod_lote"     );
$obLista->ultimaAcao->addCampo( "&inCodPlanoDebito"  , "cod_plano_credito" );
$obLista->ultimaAcao->addCampo( "&stNomContaDebito"  , "nom_conta_credito" );
$obLista->ultimaAcao->addCampo( "&inCodPlanoCredito" , "cod_plano_debito" );
$obLista->ultimaAcao->addCampo( "&stNomContaCredito" , "nom_conta_debito" );
$obLista->ultimaAcao->addCampo( "&inCodRecurso"      , "cod_recurso" );
$obLista->ultimaAcao->addCampo( "&stMascRecurso"      , "masc_recurso_red" );
$obLista->ultimaAcao->addCampo( "&stNomRecurso"      , "nom_recurso" );
$obLista->ultimaAcao->addCampo( "&nuValorPago"       , "valor" );
$obLista->ultimaAcao->addCampo( "&inCodHistorico"    , "cod_historico" );
$obLista->ultimaAcao->addCampo( "&inCodCredor"       , "cod_credor" );
$obLista->ultimaAcao->addCampo( "&stNomCredor"       , "nom_credor" );
$obLista->ultimaAcao->addCampo( "&dtBoletimPagamento", "dt_boletim" );
$obLista->ultimaAcao->addCampo( "&stTipo"            , "tipo" );

$obLista->ultimaAcao->setLink ( $pgForm."?stAcao=".$stAcao."&".Sessao::getId() );

$obLista->commitAcao();
$obLista->show();
?>
