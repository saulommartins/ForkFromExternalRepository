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
    * Página de Formulário para configuração
    * Data de Criação   : 16/04/2007

    * @author Henrique Boaventura

    * @ignore
    *
    * $Id: FMManterOrgao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");
include_once(CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );
include_once(TTGO."TTGOTipoOrgao.class.php");
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php" );
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php" );

$stPrograma = "ManterOrgao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include( $pgJs );

$stAcao = $request->get('stAcao');

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;
Sessao::write('arGestor', array());

if (isset($inCodigo)) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTOrcamentoOrgao = new TOrcamentoOrgao();
$obTOrcamentoOrgao->setDado('exercicio', Sessao::getExercicio());
$obTOrcamentoOrgao->recuperaOrgaosOrganograma( $rsOrgao);
$obCmbOrgao = new Select();
$obCmbOrgao->setRotulo( 'Órgão' );
$obCmbOrgao->setTitle( 'Selecione o Órgão' );
$obCmbOrgao->setName( 'inOrgao' );
$obCmbOrgao->setId( 'inOrgao' );
$obCmbOrgao->addOption( '', 'Selecione' );
$obCmbOrgao->setCampoId( 'num_orgao' );
$obCmbOrgao->setCampoDesc( 'nom_orgao' );
$obCmbOrgao->preencheCombo( $rsOrgao );
$obCmbOrgao->obEvento->setOnChange( "montaParametrosGET( 'preencheDados', 'inOrgao' );");

$obTipoOrgao = new TTGOTipoOrgao();
$obTipoOrgao->recuperaTodos( $rsTipoOrgao );

$obCmbTipoOrgao = new Select();
$obCmbTipoOrgao->setRotulo( 'Tipo do Órgão' );
$obCmbTipoOrgao->setTitle( 'Selecione o tipo do órgão' );
$obCmbTipoOrgao->setName( 'inTipoOrgao' );
$obCmbTipoOrgao->setId( 'inTipoOrgao' );
$obCmbTipoOrgao->addOption( '','Selecione' );
$obCmbTipoOrgao->setCampoId( 'cod_tipo' );
$obCmbTipoOrgao->setCampoDesc( 'descricao' );
$obCmbTipoOrgao->preencheCombo( $rsTipoOrgao );
$obCmbTipoOrgao->setNull( false );

$obCGMOrgao = new IPopUpCGMVinculado( $obForm );
$obCGMOrgao->setTabelaVinculo   ( 'sw_cgm_pessoa_juridica' );
$obCGMOrgao->setCampoVinculo    ( 'numcgm' );
$obCGMOrgao->setNomeVinculo     ( 'CGM Órgão' );
$obCGMOrgao->setRotulo          ( 'CGM do Órgão' );
$obCGMOrgao->setName            ( 'stNomCGMOrgao' );
$obCGMOrgao->setId              ( 'stNomCGMOrgao' );
$obCGMOrgao->obCampoCod->setName( 'inCGMOrgao' );
$obCGMOrgao->obCampoCod->setId  ( 'inCGMOrgao' );
$obCGMOrgao->setNull            ( false );

$obCGMContador = new IPopUpCGMVinculado( $obForm );
$obCGMContador->setTabelaVinculo        ( 'sw_cgm_pessoa_fisica'   );
$obCGMContador->setCampoVinculo         ( 'numcgm'                 );
$obCGMContador->setNomeVinculo          ( 'Contador'               );
$obCGMContador->setRotulo               ( 'Responsável Técnico'    );
$obCGMContador->setName                 ( 'stNomContador'          );
$obCGMContador->setId                   ( 'stNomContador'          );
$obCGMContador->obCampoCod->setName     ( 'inCGMContador'          );
$obCGMContador->obCampoCod->setId       ( 'inCGMContador'          );
$obCGMContador->setNull                 ( false                    );

$obTxtCRCContador = new TextBox();
$obTxtCRCContador->setRotulo( 'CRC' );
$obTxtCRCContador->setName( 'stCRCContador' );
$obTxtCRCContador->setId( 'stCRCContador' );
$obTxtCRCContador->setMaxLength( 11 );
$obTxtCRCContador->setNull( false );

$obTUF = new TUF();
$stFiltro = " WHERE cod_pais = 1 ";
$stOrder = " sigla_uf ASC ";
$obTUF->recuperaTodos( $rsUF, $stFiltro, $stOrder );

$obCmbUFContador = new Select;
$obCmbUFContador->setName       ( "stSiglaUF" );
$obCmbUFContador->setId         ( "stSiglaUF" );
$obCmbUFContador->setRotulo     ( "UF CRC" );
$obCmbUFContador->setTitle      ( "Selecione o estado do CRC." );
$obCmbUFContador->setNull       ( true  );
$obCmbUFContador->setCampoId    ( "[sigla_uf]" );
$obCmbUFContador->setCampoDesc  ( "[sigla_uf]" );
$obCmbUFContador->addOption     ( "", "Selecione" );
$obCmbUFContador->preencheCombo ( $rsUF );
$obCmbUFContador->setNull( false );

$obCGMResponsavelInterno = new IPopUpCGMVinculado( $obForm );
$obCGMResponsavelInterno->setTabelaVinculo        ( 'sw_cgm_pessoa_fisica'   );
$obCGMResponsavelInterno->setCampoVinculo         ( 'numcgm'                 );
$obCGMResponsavelInterno->setNomeVinculo          ( 'Controle Interno'       );
$obCGMResponsavelInterno->setRotulo               ( 'Controle Interno'       );
$obCGMResponsavelInterno->setName                 ( 'stNomReponsavelInterno' );
$obCGMResponsavelInterno->setId                   ( 'stNomReponsavelInterno' );
$obCGMResponsavelInterno->obCampoCod->setName     ( 'inCGMReponsavelInterno' );
$obCGMResponsavelInterno->obCampoCod->setId       ( 'inCGMReponsavelInterno' );
$obCGMResponsavelInterno->setNull 				  ( false 					 );

$obCGMRepresentante = new IPopUpCGMVinculado( $obForm );
$obCGMRepresentante->setTabelaVinculo    ( 'sw_cgm_pessoa_fisica'   );
$obCGMRepresentante->setCampoVinculo     ( 'numcgm'                 );
$obCGMRepresentante->setNomeVinculo      ( 'Representante'          );
$obCGMRepresentante->setRotulo           ( 'Representante'          );
$obCGMRepresentante->setName             ( 'stNomRepresentante' );
$obCGMRepresentante->setId               ( 'stNomRepresentante' );
$obCGMRepresentante->obCampoCod->setName ( 'inCGMRepresentante' );
$obCGMRepresentante->obCampoCod->setId   ( 'inCGMRepresentante' );
$obCGMRepresentante->setNull 			 ( true 					 );

$obCGMGestor = new IPopUpCGMVinculado( $obForm );
$obCGMGestor->setTabelaVinculo( 'sw_cgm_pessoa_fisica' );
$obCGMGestor->setCampoVinculo ( 'numcgm' );
$obCGMGestor->setNomeVinculo  ( 'Gestor' );
$obCGMGestor->setRotulo       ( 'Gestor' );
$obCGMGestor->setName         ( 'stNomCGMGestor' );
$obCGMGestor->setId           ( 'stNomCGMGestor' );
$obCGMGestor->obCampoCod->setName( 'inCGMGestor' );
$obCGMGestor->obCampoCod->setId  ( 'inCGMGestor' );
$obCGMGestor->setNull ( true );
$obCGMGestor->setObrigatorioBarra (true );

$obTxtCargoGestor = new TextBox();
$obTxtCargoGestor->setRotulo( 'Cargo' );
$obTxtCargoGestor->setName( 'stCargoGestor' );
$obTxtCargoGestor->setId( 'stCargoGestor' );
$obTxtCargoGestor->setSize( 50 );
$obTxtCargoGestor->setMaxLength( 50 );
$obTxtCargoGestor->setNull( true );

$obHdnId = new Hidden();
$obHdnId->setId( 'hdnId' );
$obHdnId->setName( 'hdnId' );

$obDtInicio = new Data();
$obDtInicio->setName( 'dtInicio' );
$obDtInicio->setId( 'dtInicio' );
$obDtInicio->setRotulo( 'Data de Início' );
$obDtInicio->setObrigatorioBarra( true );

$obDtTermino = new Data();
$obDtTermino->setName( 'dtTermino' );
$obDtTermino->setId( 'dtTermino' );
$obDtTermino->setRotulo( 'Data de Término' );
$obDtTermino->setObrigatorioBarra( true );

$obBtOk = new Button();
$obBtOk->setValue( 'Incluir' );
$obBtOk->setId( 'btIncluir' );
$obBtOk->obEvento->setOnCLick( "montaParametrosGET( 'incluiGestor', 'inCGMGestor,stNomCGMGestor,stCargoGestor,dtInicio,dtTermino' );" );

$obBtLimpar = new Button();
$obBtLimpar->setValue( 'Limpar' );
$obBtLimpar->obEvento->setOnClick( "limparGestor();");

$obSpnGestor = new Span();
$obSpnGestor->setId( 'spnGestor' );

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm  ($obForm);

$obFormulario->addHidden ($obHdnAcao);
$obFormulario->addHidden ($obHdnCtrl);
$obFormulario->addTitulo ( "Configuração do Órgão" );
$obFormulario->addComponente( $obCmbOrgao );
$obFormulario->addComponente( $obCmbTipoOrgao );
$obFormulario->addComponente( $obCGMOrgao );
$obFormulario->addComponente( $obCGMContador );
$obFormulario->addComponente( $obTxtCRCContador );
$obFormulario->addComponente( $obCmbUFContador );
$obFormulario->addComponente( $obCGMResponsavelInterno );
$obFormulario->addComponente( $obCGMRepresentante );
$obFormulario->addTitulo( 'Dados do Gestor' );
$obFormulario->addComponente( $obCGMGestor );
$obFormulario->addComponente( $obTxtCargoGestor );
$obFormulario->addComponente( $obDtInicio );
$obFormulario->addComponente( $obDtTermino );
$obFormulario->addHidden( $obHdnId );
$obFormulario->defineBarra( array( $obBtOk, $obBtLimpar ) );
$obFormulario->addSpan( $obSpnGestor );
$obFormulario->OK      ();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
