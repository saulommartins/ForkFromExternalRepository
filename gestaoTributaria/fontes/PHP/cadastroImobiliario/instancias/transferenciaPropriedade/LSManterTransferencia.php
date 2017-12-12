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
    * Página de lista para o cadastro de transferência de proipriedade
    * Data de Criação   : 03/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Vitor Davi Valentini
                             Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: LSManterTransferencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.17
*/

/*
$Log$
Revision 1.8  2006/09/18 10:31:46  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMTransferencia.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GT_CIM_INSTANCIAS."transferenciaPropriedade/";
$obRCIMTransferencia = new RCIMTransferencia;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$link = Sessao::read( 'link' );
$stLink = Sessao::read( 'stLink' );

$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar' : $pgProx = $pgForm; break;
    case 'efetivar': $pgProx = $pgForm; break;
    case 'cancelar': $pgProx = $pgProc; break;
    DEFAULT        : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
if ($_GET["pg"] and  $_GET["pos"]) {
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

//MONTA O FILTRO
if ($_REQUEST["inInscricaoImobiliaria"]) {
    $obRCIMTransferencia->setInscricaoMunicipal( $_REQUEST["inInscricaoImobiliaria"] );
    $stLink .= '&inInscricaoImobiliaria='.$_REQUEST['inInscricaoImobiliaria'];
}
if ($_REQUEST["inNumCGM"]) {
    $obRCIMTransferencia->setNumeroCGM( $_REQUEST["inNumCGM"] );
    $stLink .= '&inNumCGM='.$_REQUEST['inNumCGM'];
}
if ($stAcao == "efetivar" or $stAcao == "alterar" or $stAcao == "cancelar") {
    $obRCIMTransferencia->setEfetivacao( 't' );
}

$stLink .= '&stAcao='.$stAcao;
Sessao::write('link'  , $link);
Sessao::write('stLink', $stLink);
$obRCIMTransferencia->listarTransferencia( $rsListaTransferencia );

$obLista = new Lista;
$obLista->setRecordSet( $rsListaTransferencia );
$obLista->setTitulo("Registros de imóveis para transferência");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inscrição Imobiliária");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Natureza da Transferência" );
$obLista->ultimoCabecalho->setWidth( 52 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "inscricao_mascara" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_natureza] - [descricao]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->addCampo("&inCodigoTransferencia" ,"cod_transferencia"    );
$obLista->ultimaAcao->addCampo("&inInscricaoImobiliaria","inscricao_municipal"  );
$obLista->ultimaAcao->addCampo("&inInscricaoMascara"    ,"inscricao_mascara"    );
$obLista->ultimaAcao->addCampo("&inCodigoNatureza"      ,"cod_natureza"         );
$obLista->ultimaAcao->addCampo("&stCreci"               ,"creci"                );
$obLista->ultimaAcao->addCampo("&stNomeCreci"           ,"nom_cgm"              );
$obLista->ultimaAcao->addCampo("&inProcesso"            ,"cod_processo"         );
$obLista->ultimaAcao->addCampo("&inExercicioProc"       ,"exercicio_proc"       );
$obLista->ultimaAcao->addCampo("&stCodigoMatricula"     ,"mat_registro_imovel"  );
$obLista->ultimaAcao->addCampo("&hdnDataCadastro"       ,"dt_cadastro"          );
$obLista->ultimaAcao->addCampo("&stDescQuestao"         ,"inscricao_mascara"    );
if ($stAcao == "cancelar") {
    $obLista->ultimaAcao->setAcao( "Cancelar" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.17" );
$obFormulario->show();

?>
