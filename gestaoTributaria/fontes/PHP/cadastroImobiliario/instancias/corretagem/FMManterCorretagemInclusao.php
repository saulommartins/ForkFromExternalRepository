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
    * Página de Formulário para o cadastro de corretagem
    * Data de Criação   : 25/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FMManterCorretagemInclusao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.13
*/

/*
$Log$
Revision 1.9  2006/09/18 10:30:25  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretor.class.php"    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImobiliaria.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCorretagem";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgFormInc  = "FM".$stPrograma."Inclusao.php";
$pgFormAlt  = "FM".$stPrograma."Alteracao.php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obBoTipoCorretagem = new Hidden;
$obBoTipoCorretagem->setName  ( "boTipoCorretagem" );
$obBoTipoCorretagem->setValue ( $_REQUEST["boTipoCorretagem"] );

$obHdnBusca = new Hidden;
$obHdnBusca->setName  ( "tipoBuscaCreci" );
$obHdnBusca->setValue ( "corretor"       );

//COMPONENTES PARA INCLUSAO
$obTxtRegistroCreci = new TextBox;
$obTxtRegistroCreci->setRotulo    ( "CRECI"                        );
$obTxtRegistroCreci->setName      ( "stRegistroCreci"              );
$obTxtRegistroCreci->setId        ( "stRegistroCreci"              );
$obTxtRegistroCreci->setTitle     ( "Número do registro no CRECI"  );
$obTxtRegistroCreci->setValue     ( $_REQUEST["stRegistroCreci"]   );
$obTxtRegistroCreci->setSize      ( 10                             );
$obTxtRegistroCreci->setMaxLength ( 10                             );
$obTxtRegistroCreci->setNull      ( false                          );
$obTxtRegistroCreci->setValidaCaracteres ( true ,"abcdeghijklmnopqrstuvwxyz1234567890" );
$obTxtRegistroCreci->obEvento->setOnKeyPress( "return validaCRECI(event);" );
$obTxtRegistroCreci->obEvento->setOnChange  ( "buscaDado('validaCreci');"  );

if ($_REQUEST["boTipoCorretagem"] == "imobiliaria") {
    $tipoCGM = "juridica";
    $textCGM = "da imobiliária (pessoa jurídica)";
    $tituloIncluir = "Imobiliária";

    $obBscCreci = new BuscaInner;
    $obBscCreci->setRotulo                ( "Responsável"                                    );
    $obBscCreci->setTitle                 ( "CRECI do corretor responsável pela imobiliária" );
    $obBscCreci->setNull                  ( false                                            );
    $obBscCreci->setId                    ( "stNomeResponsavel"                              );
    $obBscCreci->obCampoCod->setName      ( "stCreciResponsavel"                             );
    $obBscCreci->obCampoCod->setInteiro   ( true                                             );
    $obBscCreci->obCampoCod->setSize      ( 10                                               );
    $obBscCreci->obCampoCod->setMaxLength ( 10                                               );
    $obBscCreci->obCampoCod->setValue     ( $_REQUEST["stCreciResponsavel"]                  );
    $obBscCreci->obCampoCod->obEvento->setOnChange("buscaDado('buscaCreci');"                );
    $obBscCreci->setFuncaoBusca("abrePopUp('".CAM_GT_CIM_POPUPS."corretagem/FLProcurarCorretagem.php','frm','stCreciResponsavel'
                                         ,'stNomeResponsavel','corretor','".Sessao::getId()."','800','550')" );

} elseif ($_REQUEST["boTipoCorretagem"] == "corretor") {
    $tipoCGM = "fisica";
    $textCGM = "do corretor (pessoa física)";
    $tituloIncluir = "Corretor";
}

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo              ( "CGM"                          );
$obBscCGM->setTitle               ( "CGM $textCGM"                 );
$obBscCGM->setNull                ( false                          );
$obBscCGM->setId                  ( "campoInner"                   );
$obBscCGM->obCampoCod->setName    ( "inNumCGM"                     );
$obBscCGM->obCampoCod->setInteiro ( true                           );
$obBscCGM->obCampoCod->setValue   ( $_REQUEST["inNumCGM"]          );
$obBscCGM->obCampoCod->obEvento->setOnChange("buscaCGM('$tipoCGM');" );
$obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM'
                           ,'campoInner','".$tipoCGM."','".Sessao::getId()."','800','550')" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm             );
$obFormulario->setAjuda ( "UC-05.01.13" );
$obFormulario->addHidden         ( $obHdnCtrl          );
$obFormulario->addHidden         ( $obHdnAcao          );
$obFormulario->addHidden         ( $obBoTipoCorretagem );
$obFormulario->addHidden         ( $obHdnBusca         );

$obFormulario->addTitulo ( "Dados para corretagem: ".$tituloIncluir );

$obFormulario->addComponente     ( $obTxtRegistroCreci );
$obFormulario->addComponente     ( $obBscCGM           );
if ($_REQUEST["boTipoCorretagem"] == "imobiliaria") {
    $obFormulario->addComponente ( $obBscCreci         );
}
$obFormulario->Ok();
$obFormulario->setFormFocus( $obTxtRegistroCreci->getId() );
$obFormulario->show ();

SistemaLegado::executaFramePrincipal("d.getElementById('campoInner').innerHTML = '&nbsp;';
                    d.getElementById('stNomeResponsavel').innerHTML = '&nbsp;';");
?>
