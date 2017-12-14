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
    * Página de Filtro para Parcelamento de Créditos
    * Data de Criação   : 20/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: FLParcelarCreditos.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.20
*/

/*
$Log$
Revision 1.2  2006/09/15 11:16:00  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );
include_once(CAM_GT_MON_NEGOCIO."RMONCredito.class.php");
include_once(CAM_GT_ARR_NEGOCIO."RARRParametroCalculo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ParcelarCreditos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );
//include_once(CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php");
//include_once(CAM_GT_MON_NEGOCIO."RMONCarteira.class.php");

//instanciando classes necessarias
$obRARRGrupo            = new RARRGrupo;
$obRMONCredito          = new RMONCredito;
$obRARRParametroCalculo = new RARRParametroCalculo;
//$obRMONCarteira         = new RMONCarteira;

// CONSULTA CONFIGURACAO DO MODULO IMOBILIARIO
$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCIMConfiguracao->getMascaraIM();

// CONSULTA CONFIGURACAO DO MODULO ECONOMICO
$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setCodigoModulo( 14 );
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();
$stMascaraInscricaoEconomico = $obRCEMConfiguracao->getMascaraInscricao();

// pegar mascara de credito
$obRARRParametroCalculo->obRARRGrupo->obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRARRParametroCalculo->obRARRGrupo->obRMONCredito->getMascaraCredito();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgForm );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $_REQUEST["stAcao"] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $_REQUEST["stCtrl"] );

$obHdnNomeContribuinte = new Hidden;
$obHdnNomeContribuinte->setName ( "stNomeContribuinte" );
$obHdnNomeContribuinte->setId ( "stNomeContribuinte" );
$obHdnNomeContribuinte->setValue( $_REQUEST["stNomeContribuinte"] );

//BUSCA DE CONTRIBUINTE
$obBscContribuinte = new BuscaInner;
$obBscContribuinte->setId               ( "stContribuinte"          );
$obBscContribuinte->setRotulo        ( "Contribuinte"            );
$obBscContribuinte->setTitle            ( "Codigo do Contribuinte"  );
$obBscContribuinte->setNull             ( false                    );
$obBscContribuinte->obCampoCod->setName        ("inCodContribuinte"    );
$obBscContribuinte->obCampoCod->setValue        ( $_REQUEST["inCodContribuinte"]    );
$obBscContribuinte->obCampoCod->obEvento->setOnChange("buscaValor('buscaContribuinte');");
$obBscContribuinte->obCampoCod->obEvento->setOnKeyPress("iniciaAutoComplete(event,this);");
$obBscContribuinte->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinte','stContribuinte','','".Sessao::getId()."','800','450');" );

//INSCRICAO IMOBILIARIA
$obBscInscricaoMunicipal = new BuscaInner;
$obBscInscricaoMunicipal->setNull                       ( true                               );
$obBscInscricaoMunicipal->setRotulo                   ( "Inscrição Imobiliária"            );
$obBscInscricaoMunicipal->setId                          ( "stInscricaoInscricaoImobiliaria"  );
$obBscInscricaoMunicipal->obCampoCod->setName           ( "inInscricaoImobiliaria"           );
$obBscInscricaoMunicipal->obCampoCod->setMaxLength   ( strlen($stMascaraInscricao)        );
$obBscInscricaoMunicipal->obCampoCod->setMascara      ( $stMascaraInscricao                );
$obBscInscricaoMunicipal->obCampoCod->setInteiro         ( false                              );
$obBscInscricaoMunicipal->obCampoCod->obEvento->setOnChange("buscaValor('procuraImovel');");
$obBscInscricaoMunicipal->setFuncaoBusca( "abrePopUp( '".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php', 'frm', 'inInscricaoImobiliaria', 'stInscricaoInscricaoImobiliaria', 'todos', '".Sessao::getId()."', '800', '550' );" );

//INSCRICAO ECONOMICA
$obBscInscricaoEconomica = new BuscaInner;
$obBscInscricaoEconomica->setId                   ( "stInscricaoEconomica"  );
$obBscInscricaoEconomica->setRotulo               ( "Inscrição Econômica"   );
$obBscInscricaoEconomica->setTitle                ( "Pessoa física ou jurídica cadastrada como inscrição econômica");
$obBscInscricaoEconomica->obCampoCod->setName     ( "inInscricaoEconomica"      );
$obBscInscricaoEconomica->obCampoCod->setMaxLength( strlen($stMascaraInscricaoEconomico ));
$obBscInscricaoEconomica->obCampoCod->setMascara  ( $stMascaraInscricao         );
$obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange("buscaValor('buscaIE');");
$obBscInscricaoEconomica->setFuncaoBusca          ( "abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inInscricaoEconomica','stInscricaoEconomica','todos','".Sessao::getId()."','800','550');" );

// campos para Creditos
$obBscCredito = new BuscaInner;
$obBscCredito->setRotulo( "Crédito" );
$obBscCredito->setTitle( "Busca Crédito" );
$obBscCredito->setId( "stCredito" );
$obBscCredito->obCampoCod->setName("inCodCredito");
$obBscCredito->obCampoCod->setValue( $inCodCredito );
$obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara   ($stMascaraCredito          );
$obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaCredito');");
$obBscCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCredito','stCredito','todos','".Sessao::getId()."','800','550');" );

// campos para Grupos de Creditos
$obBscGrupoCredito = new BuscaInner;
$obBscGrupoCredito->setRotulo    ( "Grupo de créditos"          );
$obBscGrupoCredito->setTitle     ( "Busca Grupo de créditos"    );
$obBscGrupoCredito->setId        ( "stGrupo"        );
$obBscGrupoCredito->obCampoCod->setName      ("inCodGrupo"      );
$obBscGrupoCredito->obCampoCod->setValue     ( $inCodGrupo      );
$obBscGrupoCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaGrupo');");
$obBscGrupoCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCredito/FLProcurarGrupo.php','frm','inCodGrupo','stGrupo','todos','".Sessao::getId()."','800','350');" );

$obHdnCodigoCredito = new Hidden;
$obHdnCodigoCredito->setName    ('inCodigoCredito');
$obHdnCodigoCredito->setId          ('inCodigoCredito');
$obHdnCodigoCredito->setValue    ( $inCodigoCredito );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao            );
$obFormulario->addHidden( $obHdnCtrl            );
$obFormulario->addHidden( $obHdnCodigoCredito );
$obFormulario->addHidden( $obHdnNomeContribuinte );
$obFormulario->addTitulo    ( "Dados para filtro"   );
$obFormulario->addComponente ( $obBscGrupoCredito );
$obFormulario->addComponente ($obBscCredito);
$obFormulario->addComponente( $obBscContribuinte       );
$obFormulario->addComponente( $obBscInscricaoEconomica );
$obFormulario->addComponente( $obBscInscricaoMunicipal );

$obFormulario->ok();
$obFormulario->show();

$stJs .= 'f.inCodContribuinte.focus();';
sistemaLegado::executaFrameOculto ( $stJs );
?>
