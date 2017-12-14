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
 * Arquivo instância para popup de Penalidade
 * Data de Criação: 11/08/2008

 * @author Analista      : Heleno Menezes da Silva
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: LSPenalidade.php 64421 2016-02-19 12:14:17Z fabio $

 * Casos de uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_FIS_MAPEAMENTO . "TFISPenalidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Penalidade";

$pgFilt = "FL" . $stPrograma . ".php?" . Sessao::getId();
$pgForm = "FM" . $stPrograma . ".php";
$pgProc = "PR" . $stPrograma . ".php";
$pgOcul = "OC" . $stPrograma . ".php";

$stJS .= "function inserePenalidade(num, prm) {\n";
$stJS .= "    window.opener.parent.frames['telaPrincipal'].document.frm.inCodPenalidade.value = num;\n";
$stJS .= "    window.opener.parent.frames['telaPrincipal'].document.getElementById('stPenalidade').innerHTML = prm;\n";
$stJS .= "    window.opener.parent.frames['telaPrincipal'].document.frm.inCodPenalidade.focus();\n";
$stJS .= "    window.close();\n";
$stJS .= "}\n";

$stFiltro = "";
$stLink   = "&campoNum=".$_REQUEST["campoNum"];
$stLink  .= "&boRescindido=".$_REQUEST['boRescindido'];
$stLink  .= "&stTipo=".$_REQUEST["stTipo"];
$stLink  .= "&tipoBusca=".$_REQUEST["tipoBusca"];
$stLink  .= "&stAcao=".$stAcao;

$stNome = $_REQUEST["campoNom"];

$obPenalidade = new TFISPenalidade();
$rsPenalidade = new RecordSet();

if ($_REQUEST["campoNom"]) {
    $stFiltro .= " nom_penalidade ILIKE UPPER( '%" . trim( $stNome ). "%' ) ";
}

if ($_REQUEST["tipoBusca"]) {
    if ( $stFiltro )
        $stFiltro .= " AND ";

    $stFiltro .= " penalidade.cod_tipo_penalidade = ".$_REQUEST["tipoBusca"];
}

$obPenalidade->recuperaListaPenalidades( $rsPenalidade, $stFiltro );

$obLista = new Lista();
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsPenalidade );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 85 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_penalidade" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_penalidade" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:inserePenalidade();" );
$obLista->ultimaAcao->addCampo( "1", "cod_penalidade" );
$obLista->ultimaAcao->addCampo( "2", "nom_penalidade" );
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario();

$obBtnCancelar = new Button();
$obBtnCancelar->setName( 'cancelar' );
$obBtnCancelar->setValue( 'Cancelar' );
$obBtnCancelar->obEvento->setOnClick( "window.close();" );

$obBtnFiltro = new Button();
$obBtnFiltro->setName( 'filtro' );
$obBtnFiltro->setValue( 'Filtro' );
$obBtnFiltro->obEvento->setOnClick( "Cancelar('".$pgFilt.$stLink."','telaPrincipal');" );

$obFormulario->defineBarra( array( $obBtnCancelar,$obBtnFiltro ) , '', '' );
$obFormulario->obJavaScript->addFuncao( $stJS );

$obFormulario->show();

?>
