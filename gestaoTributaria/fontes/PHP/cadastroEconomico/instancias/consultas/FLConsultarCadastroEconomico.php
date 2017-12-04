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
    * Página de Filtro para Consulta de Cadastro Economico
    * Data de Criação: 16/09/2005

    * @author  Marcelo B. Paulino

    * @ignore

    * $Id: FLConsultarCadastroEconomico.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.21
*/

/*
$Log$
Revision 1.14  2007/04/03 16:02:42  rodrigo
Bug #8950#

Revision 1.13  2007/02/14 17:58:26  rodrigo
#5874#

Revision 1.12  2006/09/15 14:32:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"       );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"     );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicenca.class.php"               );
include_once ( CAM_GT_CEM_COMPONENTES."ITextLicenca.class.php"          );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarCadastroEconomico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
include_once( $pgJS );

Sessao::remove( "link" );
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$arConfiguracao = array();
$obRCEMLicenca = new RCEMLicenca;
$obRCEMLicenca->recuperaConfiguracao( $arConfiguracao , $sessao );

$obHdnTipoLicenca = new Hidden;
$obHdnTipoLicenca->setName  ('stTipoLicenca');
$obHdnTipoLicenca->setValue ( $arConfiguracao['numero_licenca'] );

$obRCEMLicenca->obRCEMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCEMLicenca->obRCEMConfiguracao->getMascaraInscricao();

// CONSULTA CONFIGURACAO DO MODULO ECONOMICO
$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setCodigoModulo( 14 );
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCEMConfiguracao->getMascaraInscricao();

$obMontaAtividade = new MontaAtividade;
$obMontaAtividade->setCadastroAtividade( false );
$obMontaAtividade->setTitle("Atividade exercida pela Inscrição Econômica.");

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $_REQUEST["stCtrl"] );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo( "CGM" );
$obBscCGM->setId( "stNomCGM" );
$obBscCGM->obCampoCod->setName("inNumCGM");
$obBscCGM->obCampoCod->setValue( $_REQUEST["inNumCGM"] );
$obBscCGM->setTitle("CGM referente à Inscrição Econômica.");
$obBscCGM->obCampoCod->obEvento->setOnChange("buscaValor('buscaCGM');");
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','stNomCGM','geral','".Sessao::getId()."','800','550');" );

//INSCRICAO ECONOMICA
$obBscInscricaoEconomica = new BuscaInner;
$obBscInscricaoEconomica->setRotulo               ( "Inscrição Econômica" );
$obBscInscricaoEconomica->setTitle                ( "Pessoa física ou jurídica cadastrada como inscrição econômica.");
$obBscInscricaoEconomica->setId                   ( "stInscricaoEconomica"      );
$obBscInscricaoEconomica->obCampoCod->setName     ( "inInscricaoEconomica"      );
$obBscInscricaoEconomica->obCampoCod->setSize     ( strlen($stMascaraInscricao ));
$obBscInscricaoEconomica->obCampoCod->setMaxLength( strlen($stMascaraInscricao ));
$obBscInscricaoEconomica->obCampoCod->setMascara  ( $stMascaraInscricao         );
$obBscInscricaoEconomica->setFuncaoBusca          ( "abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inInscricaoEconomica','stInscricaoEconomica','todos','".Sessao::getId()."','800','550');" );
$obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange( "buscaValor('buscaInscricao');" );

$obTxtCNPJ = new CNPJ;
$obTxtCNPJ->setRotulo( "CNPJ" );
$obTxtCNPJ->setName( "stCNPJ" );
$obTxtCNPJ->setTitle("CNPJ do CGM referente à Inscrição Econômica.");

$obTxtCPF = new CPF;
$obTxtCPF->setRotulo( "CPF" );
$obTxtCPF->setName( "stCPF" );
$obTxtCPF->setTitle("CPF do CGM referente à Inscrição Econômica.");

$obTxtNomeRazao = new TextBox;
$obTxtNomeRazao->setRotulo   ( "Nome / Razão Social"   );
$obTxtNomeRazao->setName     ( "stNomeRazaoSocial"     );
$obTxtNomeRazao->setSize     ( "80"    );
$obTxtNomeRazao->setMaxLength( "80"    );
$obTxtNomeRazao->setTitle("Nome do CGM referente à Inscrição Econômica.");

$obBscSocio = new BuscaInner;
$obBscSocio->setRotulo           ( "Sócio"        );
$obBscSocio->setId               ( "stNomeSocio"  );
$obBscSocio->setTitle("Sócio da Inscrição Econômica.");
$obBscSocio->obCampoCod->setName ("inCodigoSocio" );
$obBscSocio->obCampoCod->setValue( $_REQUEST["inCodigoSocio"] );
$obBscSocio->setFuncaoBusca      ( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodigoSocio','stNomeSocio','todos','".Sessao::getId()."','800','550');" );
$obBscSocio->obCampoCod->obEvento->setOnChange( "buscaValor('buscaSocio');" );

$obBscDomicilio = new BuscaInner;
$obBscDomicilio->setRotulo           ( "Domicílio Fiscal"  );
$obBscDomicilio->setId               ( "stEndereco"        );
$obBscDomicilio->obCampoCod->setName ( "inCodigoDomicilio" );
$obBscDomicilio->obCampoCod->setValue( $_REQUEST["inCodigoDomicilio"]  );
$obBscDomicilio->setTitle            ( "Imóvel ocupado para exercício das atividades econômicas" );
$obBscDomicilio->setFuncaoBusca      ( "abrePopUp('".CAM_GT_CEM_POPUPS."domicilioFiscal/FLProcurarDomicilioFiscal.php','frm','inCodigoDomicilio','stEndereco','todos','".Sessao::getId()."','800','550');");
//javascript: abrePopUp('../../../../../../gestaoTributaria/fontes/PHP/cadastroEconomico/popups/domicilioFiscal/FLProcurarDomicilioFiscal.php','frm','inCodigoDomicilio','stEndereco','todos','PHPSESSID=8395f02e21f1ed6434518ca9bd5634e6&iURLRandomica=20060911115045.110','800','550');;

$obBscDomicilio->obCampoCod->obEvento->setOnChange("buscaValor('buscaDomicilio');");

$obBscNatureza = new BuscaInner;
$obBscNatureza->setRotulo               ( "Natureza Jurídica" );
$obBscNatureza->setId                   ( "stNomeNatureza"    );
$obBscNatureza->setTitle("Natureza Jurídica da Inscrição Econômica.");
$obBscNatureza->obCampoCod->setName     ("inCodigoNatureza"   );
$obBscNatureza->obCampoCod->setMascara  ("999-9"   );
$obBscNatureza->obCampoCod->setValue( $_REQUEST["inCodigoNatureza"]   );
$obBscNatureza->setFuncaoBusca      ( "abrePopUp('".CAM_GT_CEM_POPUPS."naturezajuridica/FLProcurarNaturezaJuridica.php','frm','inCodigoNatureza','stNomeNatureza','todos','".Sessao::getId()."','800','550');" );
$obBscNatureza->obCampoCod->obEvento->setOnChange( "buscaValor('buscaNatureza');" );

$obBtnOK     = new OK;
$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick( "Limpar();" );

$obForm = new Form;
$obForm->setAction( $pgList         );
$obForm->setTarget( 'telaPrincipal' );

// DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm    );
$obFormulario->setAjuda      ( "UC-05.02.21");
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnTipoLicenca         );

$obFormulario->addTitulo    ( "Dados para Filtro" );

$obFormulario->addComponente ( $obBscInscricaoEconomica );
$obFormulario->addComponente ( $obTxtCNPJ               );
$obFormulario->addComponente ( $obTxtCPF                );
$obFormulario->addComponente ( $obTxtNomeRazao          );
$obFormulario->addComponente ( $obBscCGM );

$obMontaAtividade->geraFormulario( $obFormulario       );
$obFormulario->addComponente( $obBscSocio              );
$obFormulario->addComponente( $obBscDomicilio          );
$obFormulario->addComponente( $obBscNatureza           );

$obTxtLicenca = new ITextLicenca;
$obTxtLicenca->geraFormulario ( $obFormulario );
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );

$obFormulario->show();
