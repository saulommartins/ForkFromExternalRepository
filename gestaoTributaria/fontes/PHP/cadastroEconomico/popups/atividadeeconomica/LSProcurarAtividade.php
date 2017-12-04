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
    * Página de Listagem de Atividades
    * Data de Criação   : 25/11/2004

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: LSProcurarAtividade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-05.02.07, uc-03.04.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarAtividade";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

include_once $pgJS;

$link = Sessao::read( "link" );
if ( isset($_REQUEST["campoNom"] )) {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
} else {
    $_REQUEST = $link;
}

Sessao::write( "link", $link );

$stFncJavaScript .= " function insereAtividade(num,nom,cod) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " var sCod;                  \n";
$stFncJavaScript .= " sCod = cod;                \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".inCodigoAtividade.value = sCod; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoFoco"].".focus(); \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$obRCEMAtividade = new RCEMAtividade;

$stFiltro = "";
$stLink = "";

$stLink .= "&stAcao=".$stAcao;

//MONTA O FILTRO
if ($_REQUEST["stValorComposto"]) {
    //RETIRA O PONTO FINAL DO VALOR COMPOSTO CASO EXISTA
    $obRCEMAtividade->setValorComposto( $_REQUEST["stValorComposto"] );
}
if ($_REQUEST["stNomeAtividade"]) {
    $obRCEMAtividade->setNomeAtividade( $_REQUEST["stNomeAtividade"] );
}
if ($_REQUEST["inCodigoNivel"]) {
    $obRCEMAtividade->setCodigoNivel( $_REQUEST["inCodigoNivel"] );
}

if ( Sessao::read( "CodigoVigencia" ) ) {
    $obRCEMAtividade->setCodigoVigencia( Sessao::read( "CodigoVigencia" ) );
}

$obRCEMAtividade->listarAtividade( $rsListaAtividade );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsListaAtividade );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nível" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor_composto" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_atividade" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_nivel" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( "selecionar" );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink  ( "JavaScript:insereAtividade();" );
$obLista->ultimaAcao->addCampo ("&stValorComposto",     "valor_composto"   );
$obLista->ultimaAcao->addCampo ("&stNomeAtividade",     "nom_atividade"    );
$obLista->ultimaAcao->addCampo ("&inCodigoAtividade",   "cod_atividade"    );

$obLista->commitAcao();
$obLista->show();

$stFiltro = '&nomForm='.$_REQUEST['nomForm'].'&campoNum='.$_REQUEST['campoNum'].'&campoNom='.$_REQUEST['campoNom'].'&campoFoco='.$_REQUEST['campoFoco'];

$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar('".$stFiltro."');" );
$obBtnFiltro->setDisabled          ( false        );

$botoes = array ( $obBtnFiltro );

$obFormulario = new Formulario;
$obFormulario->defineBarra ( $botoes, 'left', '' );
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
