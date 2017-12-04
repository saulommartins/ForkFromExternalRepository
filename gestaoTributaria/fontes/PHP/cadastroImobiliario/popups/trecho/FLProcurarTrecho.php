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
    * Página de filtro para o cadastro de trecho
    * Data de Criação   : 21/10/2004

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: FLProcurarTrecho.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.06
*/

/*
$Log$
Revision 1.6  2007/05/22 13:39:58  fabio
Bug #9280#

Revision 1.5  2006/09/15 15:04:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

/*
    O botão que realizar a inclusão do trecho selecionado
    por esta Pop-Up em uma lista deve, OBRIGATORIAMENTE,
    se chamar "btnIncluirTrecho".
*/

session_regenerate_id();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php");
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

//Define valores para sessao
//session_regenerate_id();
//Sessao::setId( "PHPSESSID=".session_id());
//Sessao::geraURLRandomica();
Sessao::write('acao'  ,  "721");
Sessao::write('modulo',   "12");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarTrecho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FMManterFaceQuadra.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

//Instancia objetos
$obRCIMLogradouro 	= new RCIMLogradouro;

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->listaDadosMunicipio( $arConfiguracao );

$inCodigoUF = $_REQUEST["inCodigoUF"] ? $_REQUEST["inCodigoUF"] : $arConfiguracao["cod_uf"];
$inCodigoMunicipio = $_REQUEST["inCodigoMunicipio"] ? $_REQUEST["inCodigoMunicipio"] : $arConfiguracao["cod_municipio"];

//RecordSets
$rsUFs     		= new RecordSet;
$rsMunicipios  	= new RecordSet;

$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Preenche RecordSet
$obRCIMLogradouro->listarUF( $rsUFs );

// Definição dos objetos para o formuário
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obIFrame = new IFrame;
$obIFrame->setName("oculto");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("0");

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "telaMensagem");
$obIFrame2->setWidth  ( "100%"        );
$obIFrame2->setHeight ( "50"          );

$obTxtNome = new TextBox;
$obTxtNome->setRotulo         ( "Nome"             );
$obTxtNome->setName           ( "stNomeLogradouro" );
$obTxtNome->setValue          ( $stNomeLogradouro  );
$obTxtNome->setSize           ( 80                 );
$obTxtNome->setMaxLength      ( 80                 );
$obTxtNome->setNull           ( true               );

$obTxtCodUF = new TextBox;
$obTxtCodUF->setRotulo             ( "Estado"                );
$obTxtCodUF->setName               ( "inCodigoUF"            );
$obTxtCodUF->setValue              ( $inCodigoUF             );
$obTxtCodUF->setSize               ( 8                       );
$obTxtCodUF->setMaxLength          ( 8                       );
$obTxtCodUF->setNull               ( false                   );
$obTxtCodUF->obEvento->setOnChange ( "preencheMunicipio('')" );

$obCmbUF = new Select;
$obCmbUF->setName                  ( "cmbUF"                 );
$obCmbUF->addOption                ( "", "Selecione"         );
$obCmbUF->setCampoId               ( "[cod_uf]"              );
$obCmbUF->setCampoDesc             ( "nom_uf"                );
$obCmbUF->preencheCombo            ( $rsUFs                  );
$obCmbUF->setValue                 ( $inCodigoUF             );
$obCmbUF->setNull                  ( false                   );
$obCmbUF->setStyle				   ( "width: 220px"		     );
$obCmbUF->obEvento->setOnChange    ( "preencheMunicipio('')" );

if ($inCodigoUF) {
    $obRCIMLogradouro->setCodigoUF( $inCodigoUF );
    $obRCIMLogradouro->listarMunicipios( $rsMunicipios );
}

$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setRotulo      ( "Município"        );
$obTxtCodMunicipio->setName        ( "inCodigoMunicipio");
$obTxtCodMunicipio->setValue       ( $inCodigoMunicipio );
$obTxtCodMunicipio->setSize        ( 8                  );
$obTxtCodMunicipio->setMaxLength   ( 8                  );
$obTxtCodMunicipio->setNull        ( false              );

$obCmbMunicipio = new Select;
$obCmbMunicipio->setName           ( "cmbMunicipio"     );
$obCmbMunicipio->addOption         ( "", "Selecione"    );
$obCmbMunicipio->setCampoId        ( "[cod_municipio]"  );
$obCmbMunicipio->setCampoDesc      ( "nom_municipio"    );
$obCmbMunicipio->setValue          ( $inCodigoMunicipio );
$obCmbMunicipio->preencheCombo     ( $rsMunicipios      );
$obCmbMunicipio->setNull           ( false              );
$obCmbMunicipio->setStyle		   ( "width: 220px"	    );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction				   	( $pgList );

$obFormulario = new Formulario;
$obFormulario->addForm				 ( $obForm 				  );
$obFormulario->addHidden			 ( $obHdnAcao			  );
$obFormulario->addHidden			 ( $obHdnCtrl			  );
$obFormulario->addTitulo			 ( "Dados para filtro" 	  );
$obFormulario->addComponente		 ( $obTxtNome			  );
$obFormulario->addComponenteComposto ( $obTxtCodUF , $obCmbUF );
$obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio);

$obFormulario->OK	();
$obFormulario->show	();
$obIFrame->show();
$obIFrame2->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
