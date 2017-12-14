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
    * Filtro para procura de matrículas do componente IFiltroComponente
    * Data de Criação: 14/11/2007

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.00.00

    $Id: LSMatriculaFiltroComponente.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "MatriculaFiltroComponente";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript .= " function insereMatricula(num) {  \n";
$stFncJavaScript .= " var sNum;                        \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".focus(); \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$obTPessoalContrato = new TPessoalContrato();
$stFiltro = "";
$stLink   = "&campoNum=".$_REQUEST["campoNum"];
$stLink  .= "&boRescindido=".$_REQUEST['boRescindido'];
$stLink  .= "&stTipo=".$_REQUEST["stTipo"];
$stLink  .= "&campoNom=".$_REQUEST["campoNom"];

$stLink .= "&stAcao=".$stAcao;
$rsLista = new RecordSet;
$stOrdem = " nom_cgm";

$stNome = $_REQUEST["campoNom"];

if ($_REQUEST["campoNom"]) {
    $stFiltro = " AND nom_cgm ilike upper('".trim($stNome)."%') ";
}

//Define qual listagem deverá ser feita
switch ($_REQUEST["stTipo"]) {
    case "contrato":
        $stFiltro .= " AND NOT EXISTS (SELECT 1 FROM pessoal.contrato_servidor_caso_causa WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato)";
        break;
    case "contrato_aposentado":
        $stFiltro .= " AND EXISTS (SELECT 1 FROM pessoal.aposentadoria WHERE aposentadoria.cod_contrato = contrato.cod_contrato";
        $stFiltro .= "               AND NOT EXISTS (SELECT 1 FROM pessoal.aposentadoria_excluida WHERE aposentadoria.cod_contrato = aposentadoria_excluida.cod_contrato))";
        break;
    case "contrato_rescisao":
        $stFiltro .= " AND EXISTS (SELECT 1 FROM pessoal.contrato_servidor_caso_causa WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato)";
        break;
}

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
$obTPessoalContrato = new TPessoalContrato();
$obTPessoalContrato->recuperaCgmDoRegistro($rsLista,$stFiltro);

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Matrícula");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CGM / Nome");
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Situação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "registro" );
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[situacao]" );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDado();
$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereMatricula();" );
$obLista->ultimaAcao->addCampo("1","registro");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;

$obBtnCancelar = new Button;
$obBtnCancelar->setName                 ( 'cancelar'                                        );
$obBtnCancelar->setValue                ( 'Cancelar'                                        );
$obBtnCancelar->obEvento->setOnClick    ( "window.close();"                                 );

$obBtnFiltro = new Button;
$obBtnFiltro->setName                   ( 'filtro'                                          );
$obBtnFiltro->setValue                  ( 'Filtro'                                          );
$obBtnFiltro->obEvento->setOnClick      ( "Cancelar('".$pgFilt.$stLink."','telaPrincipal');");

$obFormulario->defineBarra              ( array( $obBtnCancelar,$obBtnFiltro ) , '', ''     );
$obFormulario->obJavaScript->addFuncao  ( $stFncJavaScript                                  );
$obFormulario->show();
