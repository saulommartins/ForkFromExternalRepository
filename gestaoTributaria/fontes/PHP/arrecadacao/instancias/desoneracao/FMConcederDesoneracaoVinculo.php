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
  * Página Vínculo de Formulário para Conceder Desoneração
  * Data de criação : 03/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

    * $Id: FMConcederDesoneracaoVinculo.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.04
**/

/*
$Log$
Revision 1.8  2006/10/02 09:11:43  domluc
#6973#

Revision 1.7  2006/09/15 11:50:40  fabio
corrigidas tags de caso de uso

Revision 1.6  2006/09/15 11:04:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDesoneracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FMConcederDesoneracao.php";
$pgFormVinculo = "FMConcederDesoneracaoVinculo.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

if ($_REQUEST['boConcessao'] == "contribuinte") {
//$obRARRDesoneracao = new RARRDesoneracao;
$stAcao = $_REQUEST[ "stAcao" ];
$stCtrl = $_REQUEST[ "stCtrl" ];
//DEFINIÇÃO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( 'stAcao' );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $stCtrl  );

$obHdnConcessao = new Hidden;
$obHdnConcessao->setName ( 'tipoConcessao' );
$obHdnConcessao->setValue( $_REQUEST['boConcessao']  );

$obBscDesoneracao = new BuscaInner;
$obBscDesoneracao->setTitle            ( "Desoneração a ser concedida." );
$obBscDesoneracao->setRotulo           ( "Desoneração"     );
$obBscDesoneracao->setId               ( "stDesoneracao"   );
$obBscDesoneracao->obCampoCod->setName ( "inCodigoDesoneracao" );
$obBscDesoneracao->obCampoCod->setValue( $_REQUEST["inCodigoDesoneracao"]  );
$obBscDesoneracao->setNull ( false             );
$obBscDesoneracao->obCampoCod->setSize (  9                );
$obBscDesoneracao->obCampoCod->obEvento->setOnChange( "buscaValor('montaAtributos');" );
$obBscDesoneracao->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."desoneracao/FLProcurarDesoneracao.php','frm','inCodigoDesoneracao','stDesoneracao','','".Sessao::getId()."','800','550');");

$obBscCGM = new BuscaInner;
$obBscCGM->setTitle ( "CGM do contribuinte." );
$obBscCGM->setRotulo( "CGM" );
$obBscCGM->setId( "stNomCGM" );
$obBscCGM->obCampoCod->setName("inNumCGM");
$obBscCGM->obCampoCod->setValue( $_REQUEST["inNumCGM"] );
$obBscCGM->setNull ( false     );
$obBscCGM->obCampoCod->obEvento->setOnChange("buscaValor('buscaCGM');");
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','stNomCGM','geral','".Sessao::getId()."','800','550');" );

$obSpnTipoInscricao = new Span;
$obSpnTipoInscricao->setID("spnTipoInscricao");

$obRdbInscricaoImobiliaria = new Radio;
$obRdbInscricaoImobiliaria->setRotulo   ( "Vincular Desoneração à:" );
$obRdbInscricaoImobiliaria->setName     ( "boTipoInscricao" );
$obRdbInscricaoImobiliaria->setValue    ( "II" );
$obRdbInscricaoImobiliaria->setLabel    ( "Inscrição Imobiliária" );
$obRdbInscricaoImobiliaria->setNull     ( true );
$obRdbInscricaoImobiliaria->setChecked  ( false );
$obRdbInscricaoImobiliaria->setTitle    ( "Define o tipo de vinculo da desoneração" );
$obRdbInscricaoImobiliaria->obEvento->setOnChange ( "buscaValor('TipoInscricao');" );

$obRdbInscricaoEconomio = new Radio;
$obRdbInscricaoEconomio->setRotulo       ( "Vincular Desoneração à:" );
$obRdbInscricaoEconomio->setName         ( "boTipoInscricao" );
$obRdbInscricaoEconomio->setValue        ( "IE" );
$obRdbInscricaoEconomio->setLabel        ( "Inscrição Econômica" );
$obRdbInscricaoEconomio->setNull         ( true );
$obRdbInscricaoEconomio->setChecked      ( false );
$obRdbInscricaoEconomio->setTitle        ( "Define o tipo de vinculo da desoneração" );
$obRdbInscricaoEconomio->obEvento->setOnChange ( "buscaValor('TipoInscricao');");

$obSpanAtributos = new Span;
$obSpanAtributos->setId ( 'spnAtributos' );

//DEFINICAO DO FORMULARIO
$obForm = new Form;
$obForm->setAction            ( $pgProc  );
$obForm->setTarget            ( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm      );
$obFormulario->addHidden      ( $obHdnAcao   );
$obFormulario->addHidden      ( $obHdnCtrl   );
$obFormulario->addHidden      ( $obHdnConcessao  );
$obFormulario->addTitulo      ( "Dados para Concessão por Contribuinte" );
$obFormulario->addComponente  ( $obBscDesoneracao );
$obFormulario->addComponente  ( $obBscCGM );
$obFormulario->addComponenteComposto ( $obRdbInscricaoImobiliaria, $obRdbInscricaoEconomio );
$obFormulario->addSpan               ( $obSpnTipoInscricao );
$obFormulario->addSpan               ( $obSpanAtributos    );
$obFormulario->Ok();
$obFormulario->Show();
} else {

//DEFINIÇÃO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( 'stAcao' );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $stCtrl  );

$obHdnConcessao = new Hidden;
$obHdnConcessao->setName ( 'tipoConcessao' );
$obHdnConcessao->setValue( $_REQUEST['boConcessao']  );

$obBscDesoneracao = new BuscaInner;
$obBscDesoneracao->setTitle            ( "Desoneração a ser concedida." );
$obBscDesoneracao->setRotulo           ( "Desoneração"     );
$obBscDesoneracao->setId               ( "stDesoneracao"   );
$obBscDesoneracao->obCampoCod->setName ( "inCodigoDesoneracao" );
$obBscDesoneracao->obCampoCod->setValue( $_REQUEST["inCodigoDesoneracao"]  );
$obBscDesoneracao->setNull ( false             );
$obBscDesoneracao->obCampoCod->setSize (  9                );
$obBscDesoneracao->obCampoCod->obEvento->setOnChange( "buscaValor('montaAtributos');" );
$obBscDesoneracao->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."desoneracao/FLProcurarDesoneracao.php','frm','inCodigoDesoneracao','stDesoneracao','','".Sessao::getId()."','800','550');");

$stMascaraCalculo = "99.99.999";

$obBscCalculo = new BuscaInner;
$obBscCalculo->setNull             ( false             );
$obBscCalculo->setTitle            ( "Função para identificar os contribuintes desonerados." );
$obBscCalculo->setRotulo           ( "Regra de Concessão" );
$obBscCalculo->setId               ( "stFormula"  );
$obBscCalculo->obCampoCod->setName ( "inCodigoFormula" );
$obBscCalculo->obCampoCod->setValue( $_REQUEST["inCodigoFormula"]  );
$obBscCalculo->obCampoCod->setInteiro ( true );
$obBscCalculo->obCampoCod->setNull ( false             );
$obBscCalculo->obCampoCod->setSize (  9                );
$obBscCalculo->obCampoCod->obEvento->setOnChange( "buscaValor('buscaFuncao');" );
//$obBscCalculo->setFuncaoBusca      (  "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodigoFormula','stFormula','todos','".Sessao::getId()."','800','550');" );
$obBscCalculo->setFuncaoBusca      (  "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php?".Sessao::getId()."&stCodModulo=25&stCodBiblioteca=2&','frm','inCodigoFormula','stFormula','todos','".Sessao::getId()."','800','550');" );
$obBscCalculo->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraCalculo."', this, event);");
$obBscCalculo->obCampoCod->setMinLength ( strlen($stMascaraCalculo) );

$obRdbInscricaoImobiliaria = new Radio;
$obRdbInscricaoImobiliaria->setRotulo   ( "Vincular Desoneração à:" );
$obRdbInscricaoImobiliaria->setName     ( "boTipoInscricao" );
$obRdbInscricaoImobiliaria->setValue    ( "II" );
$obRdbInscricaoImobiliaria->setLabel    ( "Inscrição Imobiliária" );
$obRdbInscricaoImobiliaria->setNull     ( true );
$obRdbInscricaoImobiliaria->setChecked  ( false );
$obRdbInscricaoImobiliaria->setTitle    ( "Define o tipo de vinculo da desoneração" );

$obRdbInscricaoEconomio = new Radio;
$obRdbInscricaoEconomio->setRotulo       ( "Vincular Desoneração à:" );
$obRdbInscricaoEconomio->setName         ( "boTipoInscricao" );
$obRdbInscricaoEconomio->setValue        ( "IE" );
$obRdbInscricaoEconomio->setLabel        ( "Inscrição Econômica" );
$obRdbInscricaoEconomio->setNull         ( true );
$obRdbInscricaoEconomio->setChecked      ( false );
$obRdbInscricaoEconomio->setTitle        ( "Define o tipo de vinculo da desoneração" );

$obBtnOk = new Ok();
$obBtnOk->setId( 'Ok' );
$obBtnOk->obEvento->setOnCLick('ExecutarDesoneracao();');

$obBtnLimpar = new Button();
$obBtnLimpar->setName( "stLimpar" );
$obBtnLimpar->setValue( 'Limpar' );
$obBtnLimpar->obEvento->setOnClick("buscaValor('limparConcessaoGrupo');");

//DEFINICAO DO FORMULARIO
$obForm = new Form;
$obForm->setAction            ( $pgProc  );
$obForm->setTarget            ( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm      );
$obFormulario->addHidden      ( $obHdnAcao   );
$obFormulario->addHidden      ( $obHdnCtrl   );
$obFormulario->addHidden      ( $obHdnConcessao  );
$obFormulario->addTitulo      ( "Dados para Concessão por Grupo" );
$obFormulario->addComponente  ( $obBscDesoneracao );
$obFormulario->addComponente  ( $obBscCalculo );
$obFormulario->addComponenteComposto ( $obRdbInscricaoImobiliaria, $obRdbInscricaoEconomio );
//$obFormulario->Ok();
$obFormulario->defineBarra( array($obBtnOk, $obBtnLimpar) );
$obFormulario->Show();

}
