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
    * Página de filtro para o cadastro de corretagem
    * Data de Criação   : 25/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FLManterCorretagem.php 59612 2014-09-02 12:00:51Z gelson $

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
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretagem.class.php" );

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

Sessao::remove('filtro');
Sessao::remove('link');

$stAcao = $request->get('stAcao');
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

if ($stAcao == "alterar") {
    $_REQUEST['boTipoCorretagem'] = '';
    $_REQUEST['tipoBuscaCreci']   = '';
}

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST['stCtrl']  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao']  );

$obHdnBusca = new Hidden;
$obHdnBusca->setName  ( "tipoBuscaCreci" );
$obHdnBusca->setValue ( $_REQUEST['tipoBuscaCreci']    );

$obHdnTipoCGM = new Hidden;
$obHdnTipoCGM->setName  ( "tipoCGM"  );
$obHdnTipoCGM->setValue ( "juridica" );

// DEFINE OBJETOS DO FILTRO ATIVIDADE/INSCRICAO
$obRadioImobiliaria = new Radio;
$obRadioImobiliaria->setName      ( "boTipoCorretagem"             );
$obRadioImobiliaria->setId        ( "boTipoCorretagem"             );
$obRadioImobiliaria->setRotulo    ( "Tipo de Corretagem"           );
$obRadioImobiliaria->setValue     ( "imobiliaria"                  );
$obRadioImobiliaria->setLabel     ( "Imobiliária"                  );
$obRadioImobiliaria->setNull      ( false                          );
$obRadioImobiliaria->setChecked   ( !$_REQUEST['boTipoCorretagem'] );
$obRadioImobiliaria->obEvento->setOnChange( "buscaDado('atualizaFiltro');" );

$obRadioCorretor= new Radio;
$obRadioCorretor->setName         ( "boTipoCorretagem"             );
$obRadioCorretor->setId           ( "boTipoCorretagem"             );
$obRadioCorretor->setValue        ( "corretor"                     );
$obRadioCorretor->setLabel        ( "Corretor"                     );
$obRadioCorretor->setNull         ( false                          );
$obRadioCorretor->setChecked      ( $_REQUEST['boTipoCorretagem']  );
$obRadioCorretor->obEvento->setOnChange( "buscaDado('atualizaFiltro');" );

$obBscCreci = new BuscaInner;
$obBscCreci->setRotulo                ( "CRECI"                                          );
$obBscCreci->setTitle                 ( "Número do registro no CRECI"                    );
$obBscCreci->setNull                  ( true                                             );
$obBscCreci->setId                    ( "stNomeResponsavel"                              );
$obBscCreci->obCampoCod->setName      ( "stCreciResponsavel"                             );
$obBscCreci->obCampoCod->setId        ( "stCreciResponsavel"                             );
$obBscCreci->obCampoCod->setInteiro   ( false                                            );
$obBscCreci->obCampoCod->setSize      ( 10                                               );
$obBscCreci->obCampoCod->setMaxLength ( 10                                               );
$obBscCreci->obCampoCod->setValue     ( $_REQUEST["stCreciResponsavel"]                  );
$obBscCreci->obCampoCod->obEvento->setOnChange("buscaDado('buscaCreci');"                );
$obBscCreci->obCampoCod->obEvento->setOnKeyPress( "return validaCRECI(event);"           );
$obBscCreci->setFuncaoBusca("abrePopUp('../../popups/corretagem/FLProcurarCorretagem.php','frm','stCreciResponsavel'
                             ,'stNomeResponsavel','".$_REQUEST['tipoBuscaCreci']."','".Sessao::getId()."','800','550')" );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo              ( "CGM"                          );
$obBscCGM->setTitle               ( "CGM da imobiliária (pessoa jurídica)" );
$obBscCGM->setNull                ( true                           );
$obBscCGM->setId                  ( "campoInner"                   );
$obBscCGM->obCampoCod->setName    ( "inNumCGM"                     );
$obBscCGM->obCampoCod->setValue   ( $_REQUEST["inNumCGM"]          );
$obBscCGM->obCampoCod->obEvento->setOnChange("buscaCGM('juridica');" );
$obBscCGM->setFuncaoBusca("abrePopUp('../../popups/cgm/FLProcurarCgm.php','frm','inNumCGM','campoInner','juridica','".Sessao::getId()."','800','550')" );

$obSpanCGM = new Span;
$obSpanCGM->setId("spnCGM");

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimpar"       );
$obBtnLimpar->setValue             ( "Limpar"          );
$obBtnLimpar->obEvento->setOnClick ( "Limpar();" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgList         );
$obForm->setTarget ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                   ( $obForm                               );
$obFormulario->setAjuda ( "UC-05.01.13" );
$obFormulario->addHidden                 ( $obHdnCtrl                            );
$obFormulario->addHidden                 ( $obHdnAcao                            );
$obFormulario->addHidden                 ( $obHdnBusca                           );

$obFormulario->addTitulo                 ( "Dados para Filtro"                   );
if ($stAcao == "alterar") {
    $_REQUEST['boTipoCorretagem'] = '';
    $_REQUEST['tipoBuscaCreci']   = '';
    $obFormulario->addHidden             ( $obHdnTipoCGM                         );
    $obFormulario->addComponente         ( $obBscCreci                           );
    $obFormulario->addComponente         ( $obBscCGM                             );
} elseif ($stAcao == "excluir") {
    $obFormulario->addComponenteComposto ( $obRadioImobiliaria, $obRadioCorretor );
    $obFormulario->addSpan               ( $obSpanCGM                            );
}
$obFormulario->Ok();
if ($stAcao == "excluir") {
    SistemaLegado::executaFramePrincipal("buscaDado('atualizaFiltro');");
    $obFormulario->setFormFocus( $obRadioImobiliaria->getId() );
} elseif ($stAcao == "alterar") {

    SistemaLegado::executaFramePrincipal("buscaDado('atualizaFiltro');");
    $obFormulario->setFormFocus( $obBscCreci->obCampoCod->getId() );
}
$obFormulario->show();
?>
