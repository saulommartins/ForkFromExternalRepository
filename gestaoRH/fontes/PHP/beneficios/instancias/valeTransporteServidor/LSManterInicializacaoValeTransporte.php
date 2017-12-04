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
    * Formulário de lista Inicializacao de Vale-Tranporte Servidor
    * Data de Criação: 07/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Eduardo Antunez

    * @ignore

    $Revision: 30931 $
    $Name$
    $Author: souzadl $
    $Date: 2006-09-26 07:00:14 -0300 (Ter, 26 Set 2006) $

    * Casos de uso: uc-04.06.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioContratoServidorConcessaoValeTransporte.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInicializacaoValeTransporte";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJS   = "JSManterConcessaoValeTransporte.js";

include_once ($pgJS);

$stCaminho   = CAM_GRH_BEN_INSTANCIAS."valeTransporteServidor/";

$stLocation = $pgForm."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'];

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$stLink .= "stAcao=".$stAcao;

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

if ($stAcao == 'excluir') {
    $obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
    Sessao::write('stInicializacao', $_POST['stInicializacao']);
    switch ($_POST['stInicializacao']) {
        case "contrato":
        case "cgm":
            $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->setRegistro($_POST['inContrato']);
            $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->consultarContrato();
            $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes($_POST['inMes']);
            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio($_POST['inAno']);
            $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->listarConcessaoValeTransporteInicializados($rsContrato);
            Sessao::write('arContratosInicializados', $rsContrato->getElementos());
        break;
        case "grupo":
            $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo($_POST['inCodGrupo']);
            $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
            $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes($_POST['inMes']);
            $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio($_POST['inAno']);
            $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->listarConcessaoValeTransporteInicializados($rsGrupo);
            Sessao::write('arGruposInicializados', $rsGrupo->getElementos());
        break;
        case "geral":
            $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes($_POST['inMes']);
            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio($_POST['inAno']);
            $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->listarConcessaoValeTransporteInicializadosGeral($rsContrato,$rsGrupo);
            Sessao::write('arContratosInicializados', $rsContrato->getElementos());
            Sessao::write('arGruposInicializados', $rsGrupo->getElementos());
        break;
    }
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

//Define o objeto RADIO para filtro
$obRdoConcessaoContrato = new Radio;
$obRdoConcessaoContrato->setName                ( "stConcessao"                                                 );
$obRdoConcessaoContrato->setId                  ( "stConcessao"                                                 );
$obRdoConcessaoContrato->setTitle               ( ""                                                            );
$obRdoConcessaoContrato->setRotulo              ( "Concessão"                                                   );
$obRdoConcessaoContrato->setLabel               ( "Matrícula"                                                    );
$obRdoConcessaoContrato->setValue               ( "contrato"                                                    );
$obRdoConcessaoContrato->setNull                ( false                                                         );
$obRdoConcessaoContrato->obEvento->setOnChange  ( "buscaValor('geraSpanLista');"                                );

$obRdoConcessaoGrupo = new Radio;
$obRdoConcessaoGrupo->setName                   ( "stConcessao"                                                 );
$obRdoConcessaoGrupo->setId                     ( "stConcessao"                                                 );
$obRdoConcessaoGrupo->setTitle                  ( ""                                                            );
$obRdoConcessaoGrupo->setRotulo                 ( "Concessão"                                                   );
$obRdoConcessaoGrupo->setLabel                  ( "Grupo"                                                       );
$obRdoConcessaoGrupo->setValue                  ( "grupo"                                                       );
$obRdoConcessaoGrupo->setNull                   ( false                                                         );
$obRdoConcessaoGrupo->obEvento->setOnChange     ( "buscaValor('geraSpanLista');"                                );

if (Sessao::read('arContratosInicializados')) {
    $obRdoConcessaoContrato->setChecked         ( true                                                          );
} elseif (Sessao::read('arGruposInicializados')) {
    $obRdoConcessaoGrupo->setChecked            ( true                                                          );
} else {
    $obRdoConcessaoContrato->setChecked         ( true                                                          );
}

$obSpanConcessao = new Span;
$obSpanConcessao->setId                         ( "spnConcessao"                                                );

$obCancelar = new Button;
$obCancelar->setName  ( "CancelarButton" );
$obCancelar->setValue ( "Cancelar" );
$obCancelar->setStyle ( "width: 80px" );
$obCancelar->obEvento->setOnClick("Cancelar('".$stLocation."');");

$obExcluir = new Button;
$obExcluir->setName  ( "Excluir" );
$obExcluir->setValue ( "Excluir" );
$obExcluir->setStyle ( "width: 80px" );
$stLink = Sessao::getId().'&'.$stLink."*_*stDescQuestao=";
$stLink = str_replace( '&', '*_*', $stLink );
$obExcluir->obEvento->setOnClick("alertaQuestao('".$stCaminho.$pgProx."?".$stLink."','sn_excluir','".Sessao::getId()."');");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnCtrl                                                );
$obFormulario->addHidden        ( $obHdnAcao                                                );
if ($stAcao == 'excluir')
    $obFormulario->addTitulo    ( "Concessões Inicializadas à Excluir"                      );
else
    $obFormulario->addTitulo    ( "Concessões Inicializadas"                                );
$obFormulario->addComponenteComposto( $obRdoConcessaoContrato , $obRdoConcessaoGrupo        );
$obFormulario->addSpan          ( $obSpanConcessao                                          );

if ($stAcao == 'excluir')
    $obFormulario->defineBarra  ( array($obExcluir,$obCancelar) );

$stJs .= "buscaValor('geraSpanLista'); \n";
SistemaLegado::executaFramePrincipal($stJs);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
