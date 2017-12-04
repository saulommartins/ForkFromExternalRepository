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
    * Formulário de lista Vale-Tranporte Servidor
    * Data de Criação: 24/10/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30922 $
    $Name$
    $Author: souzadl $
    $Date: 2006-09-26 07:00:14 -0300 (Ter, 26 Set 2006) $

    * Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_BEN_NEGOCIO."RBeneficioContratoServidorConcessaoValeTransporte.class.php"        );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConcessaoValeTransporte";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);
include_once ($pgOcul);

$stCaminho   = CAM_GRH_BEN_INSTANCIAS."valeTransporteServidor/";

$obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$stLink .= "&stAcao=".$stAcao;

$arSessaoLink = Sessao::read('link');
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $arSessaoLink["pg"]  = $_GET["pg"];
    $arSessaoLink["pos"] = $_GET["pos"];
}
if ( is_array($arSessaoLink) ) {
    $_REQUEST = $arSessaoLink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arSessaoLink[$key] = $valor;
    }
}
Sessao::write('link', $arSessaoLink);

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'consultar'  : $pgProx = $pgForm;  break;
    case 'alterar'    : $pgProx = $pgForm;  break;
    case 'excluir'    : $pgProx = $pgProc;  break;
    DEFAULT           : $pgProx = $pgForm;
}
if ($_REQUEST['stConcessao'] == 'grupo') {
    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
    if ($_REQUEST['inCodMes'] != 0) {
        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes($_REQUEST['inCodMes']);
    }
    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio($_REQUEST['inAno']);
    if ($_REQUEST['inCodGrupo']) {
        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo( $_REQUEST['inCodGrupo'] );
    }
    if ($_REQUEST['stDescricaoGrupo']) {
        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setDescricao( $_REQUEST['stDescricaoGrupo'] );
    }
    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->listarGruposCadastrados( $rsLista );

    if ($_REQUEST['boAgrupar']) {
        $rsLista = processaConcessoesCadastradasPorGrupo( $rsLista, $_REQUEST['boAgrupar'] );
    }
    $obLista = new Lista;
    $obLista->setTitulo( "Grupos Cadastrados" );
    $obLista->setRecordSet( $rsLista );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Descricao");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();

    if ($stAcao == 'alterar') {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Vigência - Vale_Transporte");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
    } else {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Vigência");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        if (!$_REQUEST['boAgrupar']) {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Vale_Transporte");
            $obLista->ultimoCabecalho->setWidth( 30 );
            $obLista->commitCabecalho();
        } else {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Mês");
            $obLista->ultimoCabecalho->setWidth( 10 );
            $obLista->commitCabecalho();
        }
    }

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "grupo" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    if ($stAcao == 'alterar') {
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[vale_transporte]" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
    } else {
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vigencia" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        if (!$_REQUEST['boAgrupar']) {
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "vale_transporte" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
        } else {
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "mes" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
        }
    }

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( $stAcao );
    if ($stAcao == 'alterar') {
        $obLista->ultimaAcao->addCampo("&inCodGrupo"         , "cod_grupo"     );
        $obLista->ultimaAcao->addCampo("&stGrupo"            , "grupo"         );
        $obLista->ultimaAcao->addCampo("&inCodMes"           , "cod_mes"       );
        $obLista->ultimaAcao->addCampo("&inExercicio"        , "exercicio"     );
    } else {
        $obLista->ultimaAcao->addCampo("&inCodConcessao"     , "cod_concessao"  );
        $obLista->ultimaAcao->addCampo("&inCodMes"           , "cod_mes"       );
        $obLista->ultimaAcao->addCampo("&inExercicio"        , "exercicio"     );
        $obLista->ultimaAcao->addCampo("&inCodGrupo"          , "cod_grupo"      );
        $obLista->ultimaAcao->addCampo("&inCodContrato"       , "cod_contrato"   );
        $obLista->ultimaAcao->addCampo("&boAgrupar"           , "boAgrupar"      );
    }
    if ($stAcao == "excluir") {
        $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"[grupo]/[mes]");
        $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink.$stLinkPagina );
    } else {
        $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink.$stLinkPagina );
    }
    $obLista->commitAcao();
} elseif ($_REQUEST['stConcessao'] == 'vale-transporte') {
    if ($_REQUEST['stConcessaoVT'] == 'contrato') {
        $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
        if ($_REQUEST['inCodMes'] != 0) {
            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes($_REQUEST['inCodMes']);
        }
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRBeneficioValeTransporte->setCodValeTransporte( $_REQUEST['inCodValeTransporte'] );
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->listarValesTransportesCadastrados( $rsLista );
    } else {
        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
        if ($_REQUEST['inCodMes'] != 0) {
            $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes($_REQUEST['inCodMes']);
        }
        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->obRBeneficioValeTransporte->setCodValeTransporte( $_REQUEST['inCodValeTransporte'] );
        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->listarGruposCadastrados( $rsLista );
    }
    $obLista = new Lista;
    $obLista->setTitulo( "Vales-Transportes Cadastrados" );
    $obLista->setRecordSet( $rsLista );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Vale-Transporte");
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Mês");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Vigência");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    if ($_REQUEST['stConcessaoVT'] == 'grupo') {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Grupo");
        $obLista->ultimoCabecalho->setWidth( 35 );
        $obLista->commitCabecalho();
    }

    if ($_REQUEST['stConcessaoVT'] == 'contrato') {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Registro");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
    }

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "vale_transporte" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "mes" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "vigencia" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    if ($_REQUEST['stConcessaoVT'] == 'grupo') {
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "grupo" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
    }

    if ($_REQUEST['stConcessaoVT'] == 'contrato') {
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "registro" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
    }

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodValeTransporte"      , "cod_vale_transporte"  );
    $obLista->ultimaAcao->addCampo("inExercicio"               , "exercicio"            );
    //if ($_REQUEST['inCodMes'] != 0) {
       $obLista->ultimaAcao->addCampo("inCodMes"               , "cod_mes"              );
    //}
    if ($_REQUEST['stConcessaoVT'] == 'contrato') {
        $obLista->ultimaAcao->addCampo("inRegistro"            , "registro"             );
    }
    if ($_REQUEST['stConcessaoVT'] == 'grupo') {
        $arSessaoLink = Sessao::read('link');
        $arSessaoLink['stConcessao'] = 'grupo';
        Sessao::write('link', $arSessaoLink);
        $obLista->ultimaAcao->addCampo("&stGrupo"             , "grupo"                 );
        $obLista->ultimaAcao->addCampo("inCodGrupo"           , "cod_grupo"             );
    }
    if ($stAcao == "excluir") {
        $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink.$stLinkPagina );
    } else {
        $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink.$stLinkPagina );
    }
    $obLista->commitAcao();
} elseif ($_REQUEST['stConcessao'] == 'contrato' or $_REQUEST['stConcessao'] == 'cgm_contrato') {
    $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
    if ($_REQUEST['inCodMes'] != "") {
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes($_REQUEST['inCodMes']);
    }
    if ($_REQUEST['inAno']) {
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio($_REQUEST['inAno']);
    }
    if ($_REQUEST['inRegistro']) {
        $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->setRegistro( $_REQUEST['inRegistro'] );
    }
    if ($_REQUEST['inNumCGM']) {
        $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->roPessoalServidor->obRCGMPessoaFisica->setNumCGM( $_REQUEST['inNumCGM'] );
    }

    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->listarConcessoesCadastradasPorContrato( $rsLista );
    $rsLista = processaConcessoesCadastradasPorContrato( $rsLista ,$_REQUEST['boAgrupar']);
    $obLista = new Lista;
    $obLista->setTitulo( "Concessões Cadastradas por Matrícula" );
    $obLista->setRecordSet( $rsLista );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Mês");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    if (!$_REQUEST['boAgrupar']) {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Grupo");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Vigência - Vale-Transporte");
        $obLista->ultimoCabecalho->setWidth( 55 );
        $obLista->commitCabecalho();
    }

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "registro" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[numcgm] / [nom_cgm]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "mes" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    if (!$_REQUEST['boAgrupar']) {
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "grupo" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vale_transporte" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
    }

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( $stAcao );
    if ($stAcao == 'alterar') {
        $obLista->ultimaAcao->addCampo("&inCodContrato"       , "cod_contrato"  );
        $obLista->ultimaAcao->addCampo("&inRegistro"          , "registro"      );
        $obLista->ultimaAcao->addCampo("&inCodMes"            , "cod_mes"       );
        $obLista->ultimaAcao->addCampo("&inExercicio"         , "exercicio"     );
        $obLista->ultimaAcao->addCampo("&inCodGrupo"          , "cod_grupo"     );
        $obLista->ultimaAcao->addCampo("&boUtilizarGrupo"     , "bo_grupo"      );
        $obLista->ultimaAcao->addCampo("&numcgm"              , "numcgm"        );
        $obLista->ultimaAcao->addCampo("&nom_cgm"             , "nom_cgm"       );
    } else {
        $obLista->ultimaAcao->addCampo("&inCodConcessao"      , "cod_concessao" );
        $obLista->ultimaAcao->addCampo("&inCodMes"            , "cod_mes"       );
        $obLista->ultimaAcao->addCampo("&inExercicio"         , "exercicio"     );
        $obLista->ultimaAcao->addCampo("&inCodContrato"       , "cod_contrato"  );
        if ($_REQUEST['boAgrupar']) {
            $obLista->ultimaAcao->addCampo("&boAgrupar"       , "boAgrupar"     );
        }
        $obLista->ultimaAcao->addCampo("inCodGrupo"           , "cod_grupo"     );
    }
    if ($stAcao == "excluir") {
        $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"stDescQuestao");
        $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink.$stLinkPagina );
    } else {
        $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink.$stLinkPagina );
    }
    $obLista->commitAcao();
}

$obLista->show();
?>
