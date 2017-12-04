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
    * Página de filtro de Responsável Técnico
    * Data de Criação   : 15/07/2004

    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.01
*/

/*
$Log$
Revision 1.8  2007/05/21 18:57:56  melo
Bug #9229#

Revision 1.7  2006/07/17 16:55:43  andre.almeida
Bug #6175#

Revision 1.6  2006/07/14 16:27:50  leandro.zis
Bug #6177#

Revision 1.5  2006/07/05 20:42:46  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_CGM_NEGOCIO."RResponsavelTecnico.class.php"   );

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "ResponsavelTecnico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

/**
    * Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
*/
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

/**
    * Instância o OBJETO da regra de negócios RResponsavelTecnico
*/
$obRResponsavel = new RResponsavelTecnico;

//************************************************/
// Define componentes do FORMULARIO
//***********************************************/
/**
    * Instância o formulário
*/
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

/**
    * Define o OBJETO da ação stAcao
*/
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

/**
    * Define o OBJETO da controle
*/
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define OBJETO HIDDEN para armazenar o CODIGO DA PROFISSÃO
$obHdnCodigoProfissao = new Hidden;
$obHdnCodigoProfissao->setName( "inCodProfissao" );
$obHdnCodigoProfissao->setValue( $obRResponsavel->getCodigoProfissao() );

/**
    * Define o OBJETO TextBox para código da profissão
*/
$obTxtProfissao = new TextBox;
$obTxtProfissao->setName      ( "inCodigoProfissao" );
$obTxtProfissao->setValue     ( $inCodigoProfissao );
$obTxtProfissao->setRotulo    ( "Profissão" );
$obTxtProfissao->setTitle     ( "Selecione a profissão." );
$obTxtProfissao->setSize      ( "11" );
$obTxtProfissao->setMaxLength ( "10" );
$obTxtProfissao->setInteiro   ( true );

/**
    * Define o OBJETO Select para profissão
*/
$obRResponsavel->obRProfissao->listarProfissaoContabil( $rsProfissao );
$obCmbProfissao = new Select;
$obCmbProfissao->setName       ( 'stNomeProfissao' );
$obCmbProfissao->setNull       ( true );
$obCmbProfissao->addOption     ( "", "Selecione" );
$obCmbProfissao->setCampoId    ( 'cod_profissao' );
$obCmbProfissao->setCampoDesc  ( 'nom_profissao' );
$obCmbProfissao->setValue      ( $inCodigoProfissao );
$obCmbProfissao->preencheCombo ( $rsProfissao );

/**
    * Define o OBJETO TEXTBOX para código da profissão
*/
$obTxtRegistro = new TextBox;
$obTxtRegistro->setName      ( "inCodigoRegistro" );
$obTxtRegistro->setValue     ( $inCodigoRegistro );
$obTxtRegistro->setRotulo    ( "Número de Registro" );
$obTxtRegistro->setTitle     ( "Informe o número de registro." );
$obTxtRegistro->setSize      ( "11" );
$obTxtRegistro->setMaxLength ( "10" );
$obTxtRegistro->setNull      ( true );
$obTxtRegistro->setInteiro   ( true );

/**
    * Define os OBJETOs para BUSCAR e EXIBIR Profissional
*/
$obBscProfissional = new BuscaInner;
$obBscProfissional->setRotulo( "Profissional" );
$obBscProfissional->setTitle( "Informe o código do profissional." );
$obBscProfissional->setNull( true );
$obBscProfissional->setId( "nom_cgm" );
$obBscProfissional->obCampoCod->setName("inNumCGM");
$obBscProfissional->obCampoCod->setValue( $inNumCGM );
$obBscProfissional->obCampoCod->obEvento->setOnBlur("buscaCGM_Filtro();");
$obBscProfissional->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','nom_cgm','geral','".Sessao::getId()."','800','550')" );

/**
    * Define o OBJETO TextBox para código UF
*/
$obTxtUF = new TextBox;
$obTxtUF->setName      ( "inCodigoUF" );
$obTxtUF->setInteiro   ( true );
$obTxtUF->setRotulo    ( "UF" );
$obTxtUF->setTitle     ( "Selecione a UF." );
$obTxtUF->setSize      ( "4" );
$obTxtUF->setMaxLength ( "2" );
$obTxtUF->setValue     ( $obRResponsavel->getCodigoUf() );
$obTxtUF->setNull      ( true );

/**
    * Define o OBJETO Select para UF
*/
$obRResponsavel->listarUF( $rsUF );
$obCmbUF = new Select;
$obCmbUF->setName       ( 'stNomeUF' );
$obCmbUF->setNull       ( true );
$obCmbUF->addOption     ( "", "Selecione" );
$obCmbUF->setCampoId    ( 'cod_uf' );
$obCmbUF->setCampoDesc  ( 'nom_uf' );
$obCmbUF->setValue      ( $inCodigoUF );
$obCmbUF->preencheCombo ( $rsUF );

/**
    * Define o OBJETO para controle da paginação
*/
$obHdnPg = new Hidden;
$obHdnPg->setName  ( "pg" );
$obHdnPg->setValue ( $_GET["pg"] );

/**
    * Define o OBJETO para controle da paginação
*/
$obHdnPos = new Hidden;
$obHdnPos->setName  ( "pos" );
$obHdnPos->setValue ( $_GET["pos"] );

/**
    * Criacão do formuário
*/
$obFormulario = new Formulario;
$obFormulario->setAjuda               ( "UC-02.01.01"                    );
$obFormulario->addForm                ( $obForm                          );
$obFormulario->addHidden              ( $obHdnPg                         );
$obFormulario->addHidden              ( $obHdnPos                        );
$obFormulario->addHidden              ( $obHdnAcao                       );
$obFormulario->addHidden              ( $obHdnCtrl                       );
$obFormulario->addHidden              ( $obHdnCodigoProfissao            );
$obFormulario->addTitulo              ( "Dados para Responsável Técnico" );

$obFormulario->addComponenteComposto  ( $obTxtProfissao,$obCmbProfissao  );
$obFormulario->addComponente          ( $obBscProfissional               );
$obFormulario->addComponente          ( $obTxtRegistro                   );
$obFormulario->addComponenteComposto  ( $obTxtUF,$obCmbUF                );

$obFormulario->OK();
$obFormulario->show();
?>
