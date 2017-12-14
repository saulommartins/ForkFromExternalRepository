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
  * Arquivo de instância para busca de Escala
  * Data de Criação: 09/10/2008

  * @author Analista      Dagiane Vieira
  * @author Desenvolvedor Alex Cardoso

  * @package URBEM
  * @subpackage

  $Id: $

  * Casos de uso: uc-04.10.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_PON_MAPEAMENTO."TPontoEscala.class.php"                                       );

//DEFINE O NOME DOS ARQUIVOS PHP
$stPrograma = "ProcurarEscala";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//CONTROLE DE TELA DO LOCAL SELECIONADO
$stFncJavaScript .= " function insereEscala(num,nom) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " jQuery(window.opener.parent.frames['telaPrincipal'].document).find('#".$_REQUEST["campoNom"]."').html(sNom);\n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNom"].".value = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".focus(); \n";
$stFncJavaScript .= " window.close();\n";
$stFncJavaScript .= " }\n";

$obTPontoEscala = new TPontoEscala();
$rsListaEscala  = new RecordSet;

$inCodEscala    = $_REQUEST['inCodEscala'];
$stDescricao    = $_REQUEST['stDescricao'];

if ($_REQUEST['inCodEscala']) {
    $obTPontoEscala->setDado('cod_escala',$inCodEscala);
    $obTPontoEscala->recuperaEscalasAtivas($rsListaEscala);
    $stLink .= "&inCodEscala=".$inCodEscala;
} elseif ($_REQUEST['stDescricao']) {
    $obTPontoEscala->setDado('descricao',$stDescricao);
    $obTPontoEscala->recuperaEscalasAtivas($rsListaEscala);
    $stLink .= "&stDescricao=".$stDescricao;
} else {
    $obTPontoEscala->recuperaEscalasAtivas($rsListaEscala);
}

$stLink .= "&stAcao=".$stAcao."&campoNom=".$_REQUEST["campoNom"]."&campoNum=".$_REQUEST["campoNum"];

//INSTÂNCIA DO OBJETO LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsListaEscala );
$obLista->setTitulo ("Escalas Cadastradas");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

//cod_local e descricao SAO CAMPOS RETORNADOS PELO SQL
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_escala" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereEscala();" );
$obLista->ultimaAcao->addCampo("1","cod_escala"  );
$obLista->ultimaAcao->addCampo("2","descricao"  );
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
