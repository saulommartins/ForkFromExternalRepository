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
    * Arquivo instância para popup de Fiscal

    * Data de Criação   : 19/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Bruno Ferreira
    * @ignore

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_FIS_MAPEAMENTO."TFISFiscal.class.php"                                     );

//Define o nome dos arquivos PHP
$stPrograma = "Fiscal";

$pgFilt = "FL".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stJS.= "function insereFiscalizacao(num,prm) {                                                                       \n";
$stJS.= " window.opener.parent.frames['telaPrincipal'].document.frm.inFiscal.value = num;                  \n";
$stJS.= " window.opener.parent.frames['telaPrincipal'].document.getElementById('stFiscal').innerHTML = prm;\n";
$stJS.= " window.opener.parent.frames['telaPrincipal'].document.frm.inFiscal.focus();                      \n";
$stJS.= " window.close();                                                                                            \n";
$stJS.= "}                                                                                                           \n";

$stFiltro = "";
$stLink   = "&campoNum=".$_REQUEST["campoNum"];
$stLink  .= "&boRescindido=".$_REQUEST['boRescindido'];
$stLink  .= "&stTipo=".$_REQUEST["stTipo"];

$stLink .= "&stAcao=".$stAcao;

$stNome = $_REQUEST["campoNom"];

$obFiscal = new TFISFiscal();
$rsFiscal = new RecordSet();

if ($_REQUEST["campoNom"]) { $stFiltro .= " WHERE gl.nom_cgm ILIKE UPPER( '%".trim($stNome)."%' ) "; }
$obFiscal->recuperaListaFiscal( $rsFiscal, $stFiltro,null,false );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsFiscal );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição");
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "codigo" );
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nome" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( $stAcao                            );
$obLista->ultimaAcao->setFuncao( true                               );
$obLista->ultimaAcao->setLink  ( "JavaScript:insereFiscalizacao();" );
$obLista->ultimaAcao->addCampo ( "1","codigo"                     );
$obLista->ultimaAcao->addCampo ( "2","nome"                    );
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;

$obBtnCancelar = new Button;
$obBtnCancelar->setName               ( 'cancelar'                                         );
$obBtnCancelar->setValue              ( 'Cancelar'                                         );
$obBtnCancelar->obEvento->setOnClick  ( "window.close();"                                  );

$obBtnFiltro = new Button;
$obBtnFiltro->setName                 ( 'filtro'                                           );
$obBtnFiltro->setValue                ( 'Filtro'                                           );
$obBtnFiltro->obEvento->setOnClick    ( "Cancelar('".$pgFilt.$stLink."','telaPrincipal');" );

$obFormulario->defineBarra            ( array( $obBtnCancelar,$obBtnFiltro ) , '', ''      );
$obFormulario->obJavaScript->addFuncao( $stJS                                              );
$obFormulario->show();

?>
