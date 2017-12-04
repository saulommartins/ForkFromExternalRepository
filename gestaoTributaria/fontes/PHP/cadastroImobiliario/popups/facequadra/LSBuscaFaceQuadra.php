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
    * Página de para o cadastro de face de quadra
    * Data de Criação   : 13/08/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @ignore

    * $Id: LSBuscaFaceQuadra.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.07
*/

/*
$Log$
Revision 1.4  2006/09/15 15:04:05  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_MAPEAMENTO."TLoteCIM.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "BuscaLote";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";

// Funca JS para preencher o campo do Processo
$stFncJavaScript .= " function Insere(cod_lote) {                  \n";
$stFncJavaScript .= "     var descricao                           \n";
$stFncJavaScript .= "     window.opener.parent.frames['telaPrincipal'].document.frm.".$_POST['campoNum'].".value = cod_lote; \n";
$stFncJavaScript .= "     window.close();                         \n";
$stFncJavaScript .= " }                                           \n";

$obTLoteCIM = new TLoteCIM;
$stFiltro = " WHERE cod_lote IS NOT NULL";
$stLink   = "";

// pesquisa pelo numero do Lote
if ($_POST["inCodLote"]) {
    $stFiltro .= " AND cod_lote = ".$_POST["inCodLote"];
    $stLink .= "&inCodLote=".$_POST["inCodLote"];
}

// busca pela localizacao do Lote
if ($_POST["stMascLocalizacao"] and $_POST["inQuantNiveis"]) {
    $inQuantNiveis    = $_POST["inQuantNiveis"] - 1;
    $inCodNivel       = $inQuantNiveis;
    $arCodLocalizacao = explode( ".",$_POST["stMascLocalizacao"] );
    $inCodPai         = $arCodLocalizacao[$inQuantNiveis-2];
    $inCodLocalizacao = $arCodLocalizacao[$inQuantNiveis-1];

    $stFiltro .= " AND cod_pai         = '".$inCodPai."'";
    $stFiltro .= " AND cod_localizacao = '".$inCodLocalizacao."'";
    $stFiltro .= " AND cod_nivel       = ".$inCodNivel;
}

$obTLoteCIM->recuperaMascara($rsLote, $stFiltro, " ");

$stLink .= "&stAcao=".$stAcao;

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLote );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->ultimoCabecalho->addConteudo("Lote ");
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo("masclote" );
$obLista->commitDado();

$obLista->addAcao();

$stAcao = "SELECIONAR";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo("1","masclote");
//$obLista->ultimaAcao->addCampo("2",$_POST["stMascLocalizacao"]);
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

?>
