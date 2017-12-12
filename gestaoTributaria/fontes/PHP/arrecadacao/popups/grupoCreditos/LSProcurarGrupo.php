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
  * Página de lista para popup de grupo credito
  * Data de criação : 03/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: LSProcurarGrupo.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.02
**/

/*
$Log$
Revision 1.10  2006/09/15 11:51:05  fabio
corrigidas tags de caso de uso

Revision 1.9  2006/09/15 10:50:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarGrupo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRegra   = new RARRGrupo;

$stFiltro = "";

$stLink = "&stAcao=".$request->get('stAcao');
$stLink .= "&campoNom=".$request->get('campoNom');
$stLink .= "&campoNum=".$request->get('campoNum');
$stLink .= "&nomForm=".$request->get('nomForm');

// FILTRAGEM

if ( isset($_REQUEST["stCodGrupo"]) ) {
    $obRegra->setCodGrupo( $_REQUEST["stCodGrupo"] );
    $stLink .= "&stCodGrupo=".$_REQUEST["stCodGrupo"];
}
if ( isset($_REQUEST["stDescricao"]) ) {
    $obRegra->setDescricao( $_REQUEST["stDescricao"] );
    $stLink .= "&stDescricao=".$_REQUEST["stDescricao"];
}

if ( isset($_REQUEST["stExercicio"]) ) {
    $obRegra->setExercicio( $_REQUEST["stExercicio"] );
    $stLink .= "&stExercicio=".$_REQUEST["stExercicio"];
}

$stMascara = "";
$obRegra->RecuperaMascaraGrupoCredito( $stMascara );
$inTamanhoMascara = strlen( $stMascara );
$stMascara .= "/9999";

$obRegra->listarGrupos( $rsLista );
$arDados = $rsLista->getElementos();

$inTotal = count( $arDados );
for ($inX=0; $inX<$inTotal; $inX++) {
    $arDados[$inX]["cod_grupo"] = sprintf("%0".$inTamanhoMascara."d", $arDados[$inX]["cod_grupo"]);
}

$rsLista->preenche( $arDados );
$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );

$obLista->setTitulo ( "Registros de Grupos de Créditos" );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Grupo" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Exercicio" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_grupo" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "ano_exercicio" );
$obLista->commitDado();

$obLista->addAcao();

$stAcao = "SELECIONAR";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo("1","cod_grupo");
$obLista->ultimaAcao->addCampo("2","descricao");
$obLista->ultimaAcao->addCampo("3","ano_exercicio");
$obLista->commitAcao();
$obLista->show();

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );

$botoes = array ( $obBtnFiltro );

$obFormulario = new Formulario;
$obFormulario->addHidden($obHdnCampoNom);
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->defineBarra ( $botoes, 'left', '' );
$obFormulario->show();
