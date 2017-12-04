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
    * Popup de busca do PAO
    * Data de Criação: 11/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 31000 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-17 11:49:55 -0300 (Ter, 17 Jul 2007) $

    * Casos de uso: uc-02.01.03
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stFiltro = "";
$stLink   = "";

$arFiltro = Sessao::read('filtroPopUp');
if ( is_array($arFiltro) ) {
    $_REQUEST = $arFiltro;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arFiltro[$key] = $valor;
    }
    Sessao::write('filtroPopUp',$arFiltro);
}

//Definição do filtro de acordo com os valores informados no FL
if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$_REQUEST['campoNom'];
}
if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}
if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}
if ($_REQUEST["inExercicio"]) {
    $stLink .= '&inExercicio='.$_REQUEST['inExercicio'];
}

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarPAO";
$pgFilt = "FL".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'].$stLink."&".Sessao::getId();
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript .= " function inserePAO(num,nom,nuPAO,sDotacao) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " if ( window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."') ) { \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; } \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNom"].".value = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".inHdnNumPAO.value = nuPAO; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".stHdnDotacao.value = sDotacao; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus(); \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$inExercicio = trim($_REQUEST['inExercicio']);
if ($inExercicio == "") {
    $inExercicio = Sessao::getExercicio();
}

include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoProjetoAtividade.class.php");
$obTOrcamentoProjetoAtividade = new TOrcamentoProjetoAtividade();
$stFiltro = " WHERE pao.exercicio = '".$inExercicio."'";
if ($_REQUEST["stNumPAO"]) {
    $stFiltro .= " AND acao.num_acao = ".$_REQUEST["stNumPAO"];
    $stLink .= "&stNumPAO=".$_REQUEST["stNumPAO"];
}
if ($_REQUEST["stNomPAO"]) {
    $stFiltro .= " AND pao.nom_pao ilike '%".$_REQUEST["stNomPAO"]."%'";
    $stLink .= "&stNomPAO=".$_REQUEST["stNomPAO"];
}

$stOrderBy = "
          ORDER BY acao.num_acao
                 , dotacao
";

$obTOrcamentoProjetoAtividade->recuperaPorNumPAODotacao($rsPAO,$stFiltro,$stOrderBy);

//faz busca dos CGM's utilizando o filtro setado
$stLink .= "&stAcao=".$stAcao;

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsPAO );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "dotacao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "titulo" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:inserePAO();" );
$obLista->ultimaAcao->addCampo("1","num_acao");
$obLista->ultimaAcao->addCampo("2","titulo");
$obLista->ultimaAcao->addCampo("3","num_pao");
$obLista->ultimaAcao->addCampo("4","dotacao");
$obLista->commitAcao();
$obLista->show();

$obBtnVoltar = new Voltar();
$obBtnVoltar->obEvento->setOnClick("Cancelar('".$pgFilt."','telaPrincipal');");

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->defineBarra(array($obBtnVoltar));
$obFormulario->show();
?>
