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
    * Interface de Alteração das configurações do Cadastro Econômico
    * Data de Criação   : 15/07/2004

    * @author Analista: Jorge Ribbar
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php"   );
include_once( CAM_GA_ADM_NEGOCIO."RAdministracaoUF.class.php"         );

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "ResponsavelTecnico";
$pgForm = "FM".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js" ;

include_once($pgJS);

$obRResponsavel = new RCEMResponsavelTecnico;
$obRAdministracaoUF = new RUF();
$obRCGM = new RCGM();

$stAcao = $_GET['stAcao'] ? $_GET['stAcao'] : $_POST['stAcao'];
if ( empty($stAcao) ) {
    $stAcao = "incluir";
}

if ($stAcao == "alterar") {
    $obRResponsavel->setNumCgm                        ( $_GET["inCodigoCGM"]       );
    $obRResponsavel->setCodigoProfissao               ( $_GET["inCodigoProfissao"] );
    $obRResponsavel->obRProfissao->setCodigoProfissao ( $_GET["inCodigoProfissao"] );

    $js2 = "buscaConselho();";
    SistemaLegado::executaFramePrincipal($js2);

    $obRResponsavel->consultarResponsavelTecnico();
    $obRResponsavel->obRProfissao->consultarProfissao();
    $obRCGM->setNumCGM($obRResponsavel->getNumCgm());
    $obRCGM->consultar( $rsCGM );

    $stNomCGM      = $obRCGM->getNomCGM();
    $inNumCGM      = $obRResponsavel->getNumCgm();
    $inNumRegistro = $obRResponsavel->getNumRegistro();

    $obLblProfissao = new Label;
    $obLblProfissao->setName ( "inCodigoProfissao" );
    $obLblProfissao->setValue( $obRResponsavel->obRProfissao->getCodigoProfissao()." - ".$obRResponsavel->obRProfissao->getNomeProfissao() );
    $obLblProfissao->setRotulo( "Profissão" );

    $obLblProfissional = new Label;
    $obLblProfissional->setName ( "inNumCGM" );
    $obLblProfissional->setValue( $inNumCGM." - ".$stNomCGM );
    $obLblProfissional->setRotulo( "Profissional" );

    $obLblClasse = new Label;
    $obLblClasse->setName ( "stClasse" );
    $obLblClasse->setValue( $obRResponsavel->obRProfissao->obRConselho->getNomeConselho() );
    $obLblClasse->setRotulo( "Classe de Conselho" );

    $stNomeRegistro = $obRResponsavel->obRProfissao->obRConselho->getNomeRegistro();

    $obTxtRegistro = new TextBox;
    $obTxtRegistro->setName      ( "inCodigoRegistro" );
    $obTxtRegistro->setValue     ( $obRResponsavel->getNumRegistro() );
    $obTxtRegistro->setRotulo    ( $stNomeRegistro );
    $obTxtRegistro->setSize      ( "11" );
    $obTxtRegistro->setMaxLength ( "10" );
    $obTxtRegistro->setInteiro   ( true );
    $obTxtRegistro->setNull      ( false );
}

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" ); //oculto - telaPrincipal

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

if ($stAcao == 'alterar') {
    $obHdnSequencia = new Hidden();
    $obHdnSequencia->setName ( "inSequencia" );
    $obHdnSequencia->setValue( $obRResponsavel->getSequencia() );
}

$obHdnCodProfissao = new Hidden;
$obHdnCodProfissao->setName( "inCodProfissao" );
$obHdnCodProfissao->setValue( $obRResponsavel->obRProfissao->getCodigoProfissao() );

$obHdnCodRegistro = new Hidden;
$obHdnCodRegistro->setName( "inNumRegistro" );
$obHdnCodRegistro->setValue( $obRResponsavel->getNumRegistro() );

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName( "inNumCGM" );
$obHdnNumCGM->setValue( $obRResponsavel->getNumCgm() );

$obHdnNomCGM = new Hidden;
$obHdnNomCGM->setName ( "stNomCGM" );
$obHdnNomCGM->setValue( $obRResponsavel->getNomCGM() );

$obTxtProfissao = new TextBox;
$obTxtProfissao->setName      ( "inCodigoProfissao" );
$obTxtProfissao->setValue     ( $obRResponsavel->getCodigoProfissao() );
$obTxtProfissao->setRotulo    ( "Profissão" );
$obTxtProfissao->setTitle     ( "Selecione a profissão." );
$obTxtProfissao->setSize      ( "11" );
$obTxtProfissao->setMaxLength ( "10" );
$obTxtProfissao->setNull      ( false );
$obTxtProfissao->setInteiro   ( true );
$obTxtProfissao->obEvento->setOnChange( "buscaConselho();" );

$obRResponsavel->obRProfissao->listarProfissaoContabil( $rsProfissao );

if ($stAcao == "incluir") {
    if ($_GET["inCodigoProfissao"]) {
        $inCodigoProfissao = $_GET["inCodigoProfissao"];
    }

    if ($inCodigoProfissao) {
        $obRResponsavel->setCodigoProfissao( $inCodigoProfissao );
        $js = "buscaConselho();";
        SistemaLegado::executaFramePrincipal($js);
    }
}

if ($stAcao == "incluir") {
    $obBscProfissional = new BuscaInner;
    $obBscProfissional->setRotulo( "Profissional" );
    $obBscProfissional->setTitle( "Informe o código do profissional." );
    $obBscProfissional->setNull( false );
    $obBscProfissional->setId( "nom_cgm" );
    $obBscProfissional->obCampoCod->setName("inNumCGM");
    $obBscProfissional->obCampoCod->setValue( $inNumCGM );
    $obBscProfissional->obCampoCod->obEvento->setOnBlur("buscaCGM();");
    $obBscProfissional->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','nom_cgm','geral','".Sessao::getId()."','800','550')" );

    $obCmbProfissao = new Select;
    $obCmbProfissao->setName       ( 'stNomeProfissao' );
    $obCmbProfissao->setNull       ( false );
    $obCmbProfissao->addOption     ( "", "Selecione" );
    $obCmbProfissao->setCampoId    ( 'cod_profissao' );
    $obCmbProfissao->setCampoDesc  ( 'nom_profissao' );
    $obCmbProfissao->setValue      ( $obRResponsavel->getCodigoProfissao() );
    $obCmbProfissao->preencheCombo ( $rsProfissao );
    $obCmbProfissao->obEvento->setOnChange( "buscaConselho();" );
}

$obSpanConselho = new Span;
$obSpanConselho->setId( "dadosConselho" );

$obSpanRegistro = new Span;
$obSpanRegistro->setId( "dadosRegistro" );

$obRAdministracaoUF->listarUF( $rsUF );
$obTxtUF = new TextBox;
$obTxtUF->setName      ( "inCodigoUF" );
$obTxtUF->setInteiro   ( true );
$obTxtUF->setRotulo    ( "UF" );
$obTxtUF->setTitle     ( "Selecione a UF." );
$obTxtUF->setSize      ( "4" );
$obTxtUF->setMaxLength ( "2" );
$obTxtUF->setValue     ( $obRResponsavel->getCodigoUf() );
$obTxtUF->setNull      ( false );

$obCmbUF = new Select;
$obCmbUF->setName       ( 'stNomeUF' );
$obCmbUF->setValue      ( $obRResponsavel->getCodigoUf() );
$obCmbUF->setNull       ( false );
$obCmbUF->addOption     ( "", "Selecione" );
$obCmbUF->setCampoId    ( 'cod_uf' );
$obCmbUF->setCampoDesc  ( 'nom_uf' );
$obCmbUF->preencheCombo ( $rsUF );

$obHdnPg = new Hidden;
$obHdnPg->setName  ( "pg" );
$obHdnPg->setValue ( $_GET["pg"] );

$obHdnPos = new Hidden;
$obHdnPos->setName  ( "pos" );
$obHdnPos->setValue ( $_GET["pos"] );

$obFormulario = new Formulario;
$obFormulario->setAjuda                  ( "UC-02.01.01"                    );
$obFormulario->addForm                   ( $obForm                          );
$obFormulario->addHidden                 ( $obHdnPg                         );
$obFormulario->addHidden                 ( $obHdnPos                        );
$obFormulario->addHidden                 ( $obHdnAcao                       );
$obFormulario->addHidden                 ( $obHdnCtrl                       );
$obFormulario->addHidden                 ( $obHdnNomCGM                     );
$obFormulario->addHidden                 ( $obHdnCodProfissao               );
$obFormulario->addHidden                 ( $obHdnCodRegistro                );
$obFormulario->addTitulo                 ( "Dados para Responsável Técnico" );
if ($stAcao == "incluir") {
    $obFormulario->addComponenteComposto ( $obTxtProfissao, $obCmbProfissao );
    $obFormulario->addComponente         ( $obBscProfissional               );
    $obFormulario->addSpan               ( $obSpanConselho                  );
    $obFormulario->addSpan               ( $obSpanRegistro                  );
} else {
    $obFormulario->addComponente         ( $obLblProfissao                  );
    $obFormulario->addComponente         ( $obLblProfissional               );
    $obFormulario->addComponente         ( $obLblClasse                     );
    $obFormulario->addComponente         ( $obTxtRegistro                   );
    $obFormulario->addHidden             ( $obHdnNumCGM                     );
    $obFormulario->addHidden             ( $obHdnSequencia                  );
}

$obFormulario->addComponenteComposto     ( $obTxtUF,$obCmbUF                );

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar();
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
