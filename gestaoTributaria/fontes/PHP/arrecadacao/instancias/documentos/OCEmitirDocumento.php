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
  * Página de formulário oculto
  * Data de criação : 23/05/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: OCEmitirDocumento.php 63839 2015-10-22 18:08:07Z franver $

  Caso de uso: uc-05.03.11
**/

/*
$Log$
Revision 1.1  2007/10/09 18:48:59  cercato
 Ticket#9281#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNivelAtividade.class.php");
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php");
include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php");
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php" );
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/componentes/MontaGrupoCredito.class.php';

$stCtrl = $_REQUEST['stCtrl'];

$stJs = "";

$obRARRGrupo = new RARRGrupo;
$inCodModulo = $obRARRGrupo->getCodModulo() ;

function BuscarCredito($stParam1, $stParam2)
{
    ;
    $obRegra = new RARRGrupo;

    if ($_REQUEST[$stParam1]) {
        $arDados = explode("/", $_REQUEST[$stParam1]);
        $stMascara = "";
        $obRARRGrupo = new RARRGrupo;
        $obRARRGrupo->RecuperaMascaraGrupoCredito( $stMascara );
        $stMascara .= "/9999";

        if ( strlen($_REQUEST[$stParam1]) < strlen($stMascara) ) {
            $stJs = 'f.'.$stParam1.'.value= "";';
            $stJs .= 'f.'.$stParam1.'.focus();';
            $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Código Grupo/Ano exercício incompleto. (".$_REQUEST[$stParam1].")', 'form','erro','".Sessao::getId()."');";
        } else {
            $obRARRGrupo->setCodGrupo( $arDados[0] );
            $obRARRGrupo->setExercicio( $arDados[1] );

            $obRARRGrupo->listarGrupos( $rsListaGrupo );
            if ( $rsListaGrupo->Eof() ) {
                $stJs = 'f.'.$stParam1.'.value= "";';
                $stJs .= 'f.'.$stParam1.'.focus();';
                $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Código Grupo/Ano exercício inválido. (".$_REQUEST[$stParam1].")', 'form','erro','".Sessao::getId()."');";
            } else {
                $stJs = 'd.getElementById("'.$stParam2.'").innerHTML = "'.$rsListaGrupo->getCampo("descricao").'";';
            }
        }
    } else {
        $stJs = 'f.inCodGrupo.value= "";';
        $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case "montaFiltro":
        $arDados = explode( "§", $_REQUEST["cmbTipoDocumento"] );

        $obRMONCredito = new RMONCredito;
        $obRMONCredito->consultarMascaraCredito();
        $stMascaraCredito = $obRMONCredito->getMascaraCredito();
        $obMontaGrupoCredito = new MontaGrupoCredito;

        $obTxtExercicio = new Exercicio;
        $obTxtExercicio->setName ( 'inExercicio' );
        $obTxtExercicio->setValue ( Sessao::getExercicio() );
        $obTxtExercicio->setNull ( true );

        $obBscCredito = new BuscaInner;
        $obBscCredito->setRotulo    ( "Crédito"        );
        $obBscCredito->setTitle     ( "Busca Crédito"   );
        $obBscCredito->setId        ( "stCredito"       );
        $obBscCredito->obCampoCod->setStyle     ( "width: 80px"   );
        $obBscCredito->obCampoCod->setName      ("inCodCredito"             );
        $obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
        $obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
        $obBscCredito->obCampoCod->setMascara   ($stMascaraCredito          );
        $obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaCredito');");
        $obBscCredito->setFuncaoBusca("abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCredito','stCredito','todos','".Sessao::getId()."','800','550');" );

        $obBscGrupoCredito = new BuscaInner;
        $obBscGrupoCredito->setRotulo    ( "Grupo de Créditos"          );
        $obBscGrupoCredito->setTitle     ( "Busca Grupo de Créditos"    );
        $obBscGrupoCredito->setId        ( "stGrupo"        );
        $obBscGrupoCredito->obCampoCod->setName      ("inCodGrupo"      );
        $obBscGrupoCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaGrupo');");
        $obBscGrupoCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inCodGrupo','stGrupo','todos','".Sessao::getId()."','800','350');" );

        $obBscInscricaoImobiliaria = new IPopUpImovel;
        $obBscInscricaoImobiliaria->obInnerImovel->setNull ( true );

        $obBscInscricaoEconomica = new IPopUpEmpresa;
        $obBscInscricaoEconomica->setNull ( true );
        $obFormulario = new Formulario;

        $obBscContribuinte = new IPopUpCGM( new Form );
        $obBscContribuinte->setNull ( true );
        $obBscContribuinte->setRotulo ( "Contribuinte" );
        $obBscContribuinte->setTitle ( "Informe o número do Contribuinte." );

        if ($arDados[1] == 6) { //certidao pos/neg.
//            $obFormulario->addComponente( $obTxtExercicio );
//            $obMontaGrupoCredito->geraFormulario( $obFormulario, true, true );
//            $obFormulario->addComponente( $obBscCredito );

            $obFormulario->addComponente( $obBscContribuinte );
            $obBscInscricaoImobiliaria->geraFormulario ( $obFormulario );
            $obBscInscricaoEconomica->geraFormulario ( $obFormulario );
            $obFormulario->montaInnerHTML();
            $stJs = "d.getElementById('spnFiltro').innerHTML = '". $obFormulario->getHTML(). "';\n";
        }else
            $stJs = "d.getElementById('spnFiltro').innerHTML = '&nbsp;';\n";

        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "BuscaCodCredito":
        $stJs = BuscarCredito( "inCodGrupo", "stGrupo" );
        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaContribuinteIndividual":
        if ($_REQUEST["inCodContribuinteIndividual"] != "" ||  !empty($_REQUEST["inCodContribuinteIndividual"] )) {
            $obRCGM = new RCGM;
            $obRCGM->setNumCGM ( $_REQUEST["inCodContribuinteIndividual"] );
            $stWhere = " numcgm = ".$obRCGM->getNumCGM();
            $null = "&nbsp;";
            $obRCGM->consultar($rsCgm, $stWhere);
            $inNumLinhas = $rsCgm->getNumLinhas();
            if ($inNumLinhas <= 0) {
                $stJs .= 'f.inCodContribuinteIndividual.value = "";';
                $stJs .= 'f.inCodContribuinteIndividual.focus();';
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$null.'";';
                $stJs .= "alertaAviso('@CGM não encontrado. (".$_REQUEST["inCodContribuinteIndividual"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCgm->getCampo("nom_cgm");
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$stNomCgm.'";';
            }
        }
    break;
    case "buscaCredito":
        $arValores = explode('.',$_REQUEST["inCodCredito"]);
        // array [0]> cod_credito [1]> cod_especie [2]> cod_genero [3]> cod_natureza
        $obRARRGrupo->obRMONCredito->setCodCredito  ($arValores[0]);
        $obRARRGrupo->obRMONCredito->setCodEspecie  ($arValores[1]);
        $obRARRGrupo->obRMONCredito->setCodGenero   ($arValores[2]);
        $obRARRGrupo->obRMONCredito->setCodNatureza ($arValores[3]);
        // VERIFICAR PERMISSAO
        //$obRARRGrupo->obRMONCredito->consultarCreditoPermissao();
        $obRARRGrupo->obRMONCredito->consultarCredito();

        $inCodCredito = $obRARRGrupo->obRMONCredito->getCodCredito();
        $stDescricao = $obRARRGrupo->obRMONCredito->getDescricao() ;

        if ( !empty($stDescricao) ) {
            $stJs .= "d.getElementById('stCredito').innerHTML = '".$stDescricao."';\n";
            if ( $stAcao == 'incluir')
                $stJs .= "d.getElementById('stTipoCalculo').checked = true;\n";
        } else {
            $stJs .= "f.inCodCredito.value ='';\n";
            $stJs .= "f.inCodCredito.focus();\n";
            $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Crédito informado nao existe. (".$_REQUEST["inCodCredito"].")','form','erro','".Sessao::getId()."');";
        }

        $obRARRGrupo->listarCreditos( $rsCreditos );

    break;
    case "buscaGrupo":
        $obRARRGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
        $obRARRGrupo->consultarGrupo();

        $inCodGrupo     = $obRARRGrupo->getCodGrupo () ;
        $stDescricao    = $obRARRGrupo->getDescricao() ;
        $inCodModulo    = $obRARRGrupo->getCodModulo() ;
        $stExercicio    = $obRARRGrupo->getExercicio() ;
        if ( !empty($stDescricao) ) {
            $stJs .= "d.getElementById('stGrupo').innerHTML = '".$stDescricao." / ".$stExercicio."';\n";
            $stJs .= "d.getElementById('spnEmissao').innerHTML = '';\n";
            if ( $stAcao == "emitir")
                $stJs .= "f.inCodModulo.value = '".$inCodModulo."';\n";
            $stJs .= "f.inExercicioGrupo.value = '".$stExercicio."';\n";
            $stJs .= "d.getElementById('stTipoEmissao').checked = false;\n";
            $stJs .= "f.inCodGrupo.focus();\n";
        } else {
            $stJs .= "f.inCodGrupo.value ='';\n";
            $stJs .= "f.inCodGrupo.focus();\n";
            $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Grupo informado nao existe. (".$_REQUEST["inCodGrupo"].")','form','erro','".Sessao::getId()."');";
        }
        break;

    case "buscaConvenio":
        $obRMONConvenio = new RMONConvenio;
        $obRMONConvenio->setNumeroConvenio( $_REQUEST['inNumConvenio'] );
        $obRMONConvenio->listarConvenioBanco( $rsConvenio );
        if ( $rsConvenio->getNumLinhas() > 0 ) {
            $stJs .= "f.inCodBanco.value = ".$rsConvenio->getCampo( "cod_banco" ).";\n";
            $stJs .= "alertaAviso('','form','erro','".Sessao::getId()."');";
        } else {
            $stJs .= "f.inNumConvenio.value ='';\n";
            $stJs .= "f.inNumConvenio.focus();\n";
            $stJs .= "alertaAviso('@Convênio informado nao existe. (".$_REQUEST["inNumConvenio"].")','form','erro','".Sessao::getId()."');";
        }
        break;

    case "Download":
        $inArquivo = $_REQUEST["HdnQual"];
        $content_type = 'application/sxw';
        $arDados = Sessao::read( 'arquivos' );

        $stDocumento = $arDados[$inArquivo]["nome_arquivo_tmp"];
        $download = $arDados[$inArquivo]["nome_arquivo"];

        $download .= ".odt";
        header ("Content-Length: " . filesize( $stDocumento ));
        header("Content-type: $content_type");
        header("Content-Disposition: attachment; filename=\"$download\"");
        readfile( $stDocumento );
        break;

}

if ( $stJs )
    sistemaLegado::executaFrameOculto( $stJs );
