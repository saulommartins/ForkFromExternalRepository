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
    * Arquivo de filtro de busca de CGM de Instituição/Entidade
    * Data de Criação: 03/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    $Revision: 30843 $
    $Name$
    $Author: souzadl $
    $Date: 2006-10-30 13:04:04 -0300 (Seg, 30 Out 2006) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCgm";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$stFncJavaScript .= " function insereCGM(num,nom,cnpj,endereco,bairro,cidade,telefone) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " d = window.opener.parent.frames['telaPrincipal'].document;                \n";
$stFncJavaScript .= " if ( d.getElementById('".$_REQUEST["campoNom"]."') ) { d.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; } \n";
$stFncJavaScript .= " d.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum; \n";
//if ($inner!=0) {
//    $stFncJavaScript .= " d.".$_REQUEST["nomForm"].".Hdn".$_REQUEST["campoNum"].".value = sNum; \n";
//}
$stFncJavaScript .= "d.".$_REQUEST["nomForm"].".stNomCGM.value = nom;\n";
if ( Sessao::read('boDadosExtra') ) {
    $stFncJavaScript .= "d.getElementById('stCNPJ').innerHTML = cnpj;\n";
    $stFncJavaScript .= "d.getElementById('stEndereco').innerHTML = endereco;\n";
    $stFncJavaScript .= "d.getElementById('stBairro').innerHTML = bairro;\n";
    $stFncJavaScript .= "d.getElementById('stCidade').innerHTML = cidade;\n";
    $stFncJavaScript .= "d.getElementById('stTelefone').innerHTML = telefone;\n";
}
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$obMascara = new Mascara;
$stFiltro = "";
$stLink   = "";

//Definição do filtro de acordo com os valores informados
$stLink .= "&stTipoPessoa=".$_REQUEST["stTipoPessoa"];

if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$_REQUEST['campoNom'];
}
if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}
if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}

if ($_REQUEST["stTipoBusca"] == "usuario") {
    $stFiltro .= " AND sw_cgm.numcgm IN (select numcgm from administracao.usuario where status='A') ";
    $stLink .= '&stTipoBusca='.$_REQUEST['stTipoBusca'];
} else {
    $stFiltro .= " AND sw_cgm_pessoa_juridica.numcgm is not null ";
}

if ($_REQUEST["stNomeCgm"]) {
    $stFiltro .= " AND lower(nom_cgm) like lower('".$_REQUEST['stHdnNomeCgm']."') ";
    $stLink   .= "&stNomeCgm=".$_REQUEST["stNomeCgm"]."&stHdnNomeCgm=".$_REQUEST["stHdnNomeCgm"];
}
if ($_REQUEST["stCNPJ"]) {
    $inCNPJ = $_REQUEST["stCNPJ"];
    $obMascara->desmascaraDado( $inCNPJ );
    $stFiltro .= " AND sw_cgm_pessoa_juridica.cnpj = '".$inCNPJ."' ";
    $stLink   .= "&stCNPJ=".$_REQUEST["stCNPJ"];
}
if ($_REQUEST["stNomeFantasia"]) {
    $stFiltro .= " AND  lower(sw_cgm_pessoa_juridica.nom_fantasia) like lower('".$_REQUEST["stHdnNomeFantasia"]."') ";
    $stLink   .= "&stNomeFantasia=".$_REQUEST["stNomeFantasia"]."&stHdnNomeFantasia=".$_REQUEST["stHdnNomeFantasia"];
}
//faz busca dos CGM's utilizando o filtro setado
$stLink .= "&stAcao=".$stAcao;
$rsLista = new RecordSet;

if (!$_POST['boFiltro']) {
    include_once( CAM_GA_CGM_MAPEAMENTO."TCGMPessoaJuridica.class.php"       );
    $obTCGMPessoaJuridica = new TCGMPessoaJuridica();
    $obTCGMPessoaJuridica->recuperaDadosPessoaJuridica($rsLista, $stFiltro, " ORDER BY sw_cgm.nom_cgm" );
} else {
    if ($_POST['boInstituicao']) {
        include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioInstituicaoEnsino.class.php");
        $obTEstagioInstituicaoEnsino = new TEstagioInstituicaoEnsino();
        $obTEstagioInstituicaoEnsino->recuperaRelacionamento($rsLista,$stFiltro," sw_cgm.nom_cgm");
    } else {
        include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioEntidadeIntermediadora.class.php");
        $obTEstagioEntidadeIntermediadora = new TEstagioEntidadeIntermediadora();
        $obTEstagioEntidadeIntermediadora->recuperaEntidadesIntermediarias($rsLista,$stFiltro," sw_cgm.nom_cgm");
    }
}

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
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
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();
//$obLista->addDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereCGM();" );
$obLista->ultimaAcao->addCampo("1","numcgm");
$obLista->ultimaAcao->addCampo("2","nom_cgm");
$obLista->ultimaAcao->addCampo("3","cnpj");
$obLista->ultimaAcao->addCampo("4","endereco");
$obLista->ultimaAcao->addCampo("5","bairro");
$obLista->ultimaAcao->addCampo("6","cidade");
$obLista->ultimaAcao->addCampo("7","fone_comercial");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
