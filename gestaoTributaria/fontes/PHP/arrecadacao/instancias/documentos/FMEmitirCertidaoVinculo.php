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
  * Página de Formulário para emitir certidão
  * Data de criação : 16/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

  * @ignore

    * $Id: FMEmitirCertidaoVinculo.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11
**/

/*
$Log$
Revision 1.4  2006/09/15 11:50:45  fabio
corrigidas tags de caso de uso

Revision 1.3  2006/09/15 11:08:05  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoTributaria/fontes/PHP/cadastroMonetario/classes/componentes/IPopUpConvenio.class.php';
include_once(CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php");
include_once(CAM_GT_MON_NEGOCIO."RMONCredito.class.php");
include_once(CAM_GT_MON_NEGOCIO."RMONCarteira.class.php");
include_once(CAM_GT_ARR_NEGOCIO."RARRParametroCalculo.class.php" );

//Definicao dos nomes de arquivos
$stPrograma = "EmitirCertidao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

//instanciando classes necessarias
$obRARRGrupo            = new RARRGrupo;
$obRMONCredito          = new RMONCredito;
$obRARRParametroCalculo = new RARRParametroCalculo;
$obRMONCarteira         = new RMONCarteira;

// pegar mascara de credito
$obRARRParametroCalculo->obRARRGrupo->obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRARRParametroCalculo->obRARRGrupo->obRMONCredito->getMascaraCredito();

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST["stAcao"]  );

$obHdnCodModulo = new Hidden;
$obHdnCodModulo->setName  ( "inCodModulo" );
$obHdnCodModulo->setValue ( $_REQUEST["inCodModulo"] );

$obHdnCodBanco  = new Hidden;
$obHdnCodBanco->setName   ( "inCodBanco" );
$obHdnCodBanco->setValue  ( $_REQUEST["inCodBanco"]  );
$obHdnCodBanco->setId     ( "inCodBanco" );

$obHdnEmissao  = new Hidden;
$obHdnEmissao->setName   ( "stTipoEmissao" );
$obHdnEmissao->setValue  ( $_REQUEST['stCtrl']  );
$obHdnEmissao->setId     ( "stTipoEmissao" );

$obBscCredito = new BuscaInner;
$obBscCredito->setRotulo    ( "Credito"        );
$obBscCredito->setTitle     ( "Busca Crédito"   );
$obBscCredito->setId        ( "stCredito"       );
$obBscCredito->obCampoCod->setName      ("inCodCredito"             );
$obBscCredito->obCampoCod->setValue     ( $_REQUEST["inCodCredito"] );
$obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara   ($stMascaraCredito          );
$obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaCredito');");
$obBscCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCredito','stCredito','todos','".Sessao::getId()."','800','550');" );

$obBscGrupoCredito = new BuscaInner;
$obBscGrupoCredito->setRotulo    ( "Grupo de créditos"          );
$obBscGrupoCredito->setTitle     ( "Busca Grupo de créditos"    );
$obBscGrupoCredito->setId        ( "stGrupo"        );
$obBscGrupoCredito->obCampoCod->setName      ("inCodGrupo"      );
$obBscGrupoCredito->obCampoCod->setValue     ( $_REQUEST["inCodGrupo"]      );
$obBscGrupoCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaGrupo');");
$obBscGrupoCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCredito/FLProcurarGrupo.php','frm','inCodGrupo','stGrupo','todos','".Sessao::getId()."','800','350');" );

$obRdbGeral = new Radio;
$obRdbGeral->setRotulo     ( "Tipo de Emissão"       );
$obRdbGeral->setName       ( "stTipoEmissao"         );
$obRdbGeral->setId         ( "stTipoEmissao"         );
$obRdbGeral->setLabel      ( "Geral"                 );
$obRdbGeral->setValue      ( "geral"                 );
$obRdbGeral->setTitle      ( "Tipo de Emissão"       );
$obRdbGeral->setNull       ( false                   );
$obRdbGeral->setChecked    ( false                   );
$obRdbGeral->obEvento->setOnChange("
                                    document.getElementById('stTipoFiltro1').checked = false;\n
                                    document.getElementById('stTipoFiltro2').checked = false;\n
                                    document.getElementById('stTipoFiltro3').checked = false;\n
                                    document.getElementById('spnEmissao').innerHTML  = '';\n
                                  ");

$obRdbParcial = new Radio;
$obRdbParcial->setRotulo     ( "Tipo de Emissão"     );
$obRdbParcial->setName       ( "stTipoEmissao"       );
$obRdbGeral->setId           ( "stTipoEmissao"       );
$obRdbParcial->setLabel      ( "Parcial"             );
$obRdbParcial->setValue      ( "parcial"             );
$obRdbParcial->setTitle      ( "Tipo de Emissão"     );
$obRdbParcial->setNull       ( false                 );
$obRdbParcial->setChecked    ( false                 );
$obRdbParcial->obEvento->setOnChange("
                                    document.getElementById('stTipoFiltro1').checked = false;\n
                                    document.getElementById('stTipoFiltro2').checked = false;\n
                                    document.getElementById('stTipoFiltro3').checked = false;\n
                                    document.getElementById('spnEmissao').innerHTML  = '';\n
                                    ");

$obRdbIndividual = new Radio;
$obRdbIndividual->setRotulo     ( "Tipo de Emissão"  );
$obRdbIndividual->setName       ( "stTipoEmissao"    );
$obRdbGeral->setId              ( "stTipoEmissao"    );
$obRdbIndividual->setLabel      ( "Individual"       );
$obRdbIndividual->setValue      ( "individual"       );
$obRdbIndividual->setTitle      ( "Tipo de Emissão"  );
$obRdbIndividual->setNull       ( false              );
$obRdbIndividual->setChecked    ( false              );
$obRdbIndividual->obEvento->setOnChange("
                                    document.getElementById('stTipoFiltro1').checked = false;\n
                                    document.getElementById('stTipoFiltro2').checked = false;\n
                                    document.getElementById('stTipoFiltro3').checked = false;\n
                                    document.getElementById('spnEmissao').innerHTML  = '';\n
                                       ");

$obRMONCarteira->listarCarteira( $rsCarteira );

$obCmbCarteira = new Select;
$obCmbCarteira->setName               ( "stCarteira"    );
$obCmbCarteira->setRotulo             ( "Carteira"      );
$obCmbCarteira->setCampoId            ( "cod_carteira"  );
$obCmbCarteira->setCampoDesc          ( "num_carteira"  );
$obCmbCarteira->addOption             ( "", "Selecione" );
$obCmbCarteira->preencheCombo         ( $rsCarteira     );

$obRdbCGM = new Radio;
$obRdbCGM->setRotulo     ( "Filtrar por"           );
$obRdbCGM->setName       ( "stTipoFiltro"          );
$obRdbCGM->setId         ( "stTipoFiltro1"         );
$obRdbCGM->setLabel      ( "CGM"                   );
$obRdbCGM->setValue      ( "cgm"                   );
$obRdbCGM->setTitle      ( "Tipo de filtro"        );
$obRdbCGM->setNull       ( false                   );
$obRdbCGM->setChecked    ( false                   );
$obRdbCGM->obEvento->setOnChange("buscaValor('montaSpnEmissao');");

$obRdbImobiliaria = new Radio;
$obRdbImobiliaria->setRotulo     ( "Filtrar por"           );
$obRdbImobiliaria->setName       ( "stTipoFiltro"          );
$obRdbImobiliaria->setId         ( "stTipoFiltro2"         );
$obRdbImobiliaria->setLabel      ( "Inscrição Imobiliária" );
$obRdbImobiliaria->setValue      ( "imobiliaria"           );
$obRdbImobiliaria->setTitle      ( "Tipo de filtro"        );
$obRdbImobiliaria->setNull       ( false                   );
$obRdbImobiliaria->setChecked    ( false                   );
$obRdbImobiliaria->obEvento->setOnChange("buscaValor('montaSpnEmissao');");

$obRdbEconomica = new Radio;
$obRdbEconomica->setRotulo     ( "Filtrar por"           );
$obRdbEconomica->setName       ( "stTipoFiltro"          );
$obRdbEconomica->setId         ( "stTipoFiltro3"         );
$obRdbEconomica->setLabel      ( "Inscrição Econômica"   );
$obRdbEconomica->setValue      ( "economica"             );
$obRdbEconomica->setTitle      ( "Tipo de filtro"        );
$obRdbEconomica->setNull       ( false                   );
$obRdbEconomica->setChecked    ( false                   );
$obRdbEconomica->obEvento->setOnChange("buscaValor('montaSpnEmissao');");

// span para opcoes
$obSpnEmissao = new Span;
$obSpnEmissao->setId( "spnEmissao");
$obSpnEmissao->setValue( "");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "telaPrincipal" );

$obPopUpConvenio = new IpopUpConvenio  ($obForm);
$obPopUpConvenio->obCampoCod->setValue ( $inNumeroConvenio );

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCodModulo );
$obFormulario->addHidden ( $obHdnCodBanco );
$obFormulario->addHidden ( $obHdnEmissao  );
$obFormulario->addTitulo ( "Dados para emissão"  );
if ($_REQUEST['stCtrl'] == "credito") {
    $obFormulario->addComponente( $obBscCredito );
} elseif ($_REQUEST['stCtrl'] == "grupo") {
    $obFormulario->addComponente( $obBscGrupoCredito );
}
//$obFormulario->agrupaComponentes( array ($obRdbGeral, $obRdbParcial, $obRdbIndividual) );
//$obFormulario->addComponente( $obBscConvenio     );
$obFormulario->addComponente( $obPopUpConvenio   );
$obFormulario->addComponente( $obCmbCarteira     );
$obFormulario->agrupaComponentes( array ($obRdbCGM, $obRdbImobiliaria, $obRdbEconomica) );
$obFormulario->addSpan      ( $obSpnEmissao      );
$obFormulario->Ok();
$obFormulario->Show();
