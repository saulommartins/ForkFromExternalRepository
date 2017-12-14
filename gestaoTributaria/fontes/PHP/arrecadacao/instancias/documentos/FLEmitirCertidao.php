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
  * Página de Filtro de Emissão de Certidão
  * Data de criação : 16/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

    * $Id: FLEmitirCertidao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11
**/

/*
$Log$
Revision 1.5  2006/09/15 11:50:45  fabio
corrigidas tags de caso de uso

Revision 1.4  2006/09/15 11:08:05  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php");
include_once( CAM_GT_CEM_NEGOCIO."RCEMNivelAtividade.class.php");

//Definicao dos nomes de arquivos
$stPrograma = "EmitirCertidao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php"; $pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "link", "" );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

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
$obBscGrupoCredito->obCampoCod->setValue     ( $_REQUEST["inCodGrupo"] );
$obBscGrupoCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaGrupo');");
$obBscGrupoCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCredito/FLProcurarGrupo.php','frm','inCodGrupo','stGrupo','todos','".Sessao::getId()."','800','350');" );

$obBscContribuinte = new BuscaInnerIntervalo;
$obBscContribuinte->setRotulo           ( "Contribuinte"    );
//$obBscContribuinte->setTitle            ( "Valor Inicial para Codigo do Contribuinte");
$obBscContribuinte->obLabelIntervalo->setValue ( "até"          );
$obBscContribuinte->obCampoCod->setName     ("inCodContribuinteInicial"  );
$obBscContribuinte->obCampoCod->setValue        ( $_REQUEST["inCodContribuinteInicio"]  );
$obBscContribuinte->obCampoCod->obEvento->setOnChange("buscaValor('buscaContribuinteInicio');");     $obBscContribuinte->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteInicial','stNaoExiste','','".Sessao::getId()."','800','450');" ));
$obBscContribuinte->obCampoCod2->setName        ("inCodContribuinteFinal"  );
$obBscContribuinte->obCampoCod2->setValue       ( $_REQUEST["inCodContribuinteFinal"]  );     $obBscContribuinte->obCampoCod2->obEvento->setOnChange("buscaValor('buscaContribuinteFinal');");     $obBscContribuinte->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteFinal','stNaoExiste','','".Sessao::getId()."','800','450');" ));

$obBscInscricaoImobiliaria = new BuscaInnerIntervalo;
$obBscInscricaoImobiliaria->setRotulo           ( "Inscrição Imobiliária"   );
//$obBscInscricaoImobiliaria->setTitle            ( "Intervalo de Valores para Inscrição Imobiliária");
$obBscInscricaoImobiliaria->obLabelIntervalo->setValue ( "até"          );
$obBscInscricaoImobiliaria->obCampoCod->setName     ("inNumInscricaoImobiliariaInicial"  );
$obBscInscricaoImobiliaria->obCampoCod->setValue        ( $_REQUEST["inNumInscricaoImobiliariaInicial"]  );
$obBscInscricaoImobiliaria->obCampoCod->obEvento->setOnChange("buscaValor('buscaIImobiliariaInicio');");     $obBscInscricaoImobiliaria->setFuncaoBusca      ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaInicial','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));
$obBscInscricaoImobiliaria->obCampoCod2->setName        ( "inNumInscricaoImobiliariaFinal" );
$obBscInscricaoImobiliaria->obCampoCod2->setValue       ( $_REQUEST["inNumInscricaoImobiliariaFinal"]  );     $obBscInscricaoImobiliaria->obCampoCod2->obEvento->setOnChange("buscaValor('buscaIImobiliariaFinal');");     $obBscInscricaoImobiliaria->setFuncaoBusca2     ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaFinal','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));

$obTxtCertidao = new Textbox;
$obTxtCertidao->setName   ( "inCodDocumento" );
$obTxtCertidao->setRotulo ( "Certidão"       );
$obTxtCertidao->setValue  ( $_REQUEST["inCodDocumento"]  );

/* consulta mascara*/
$obRCIMNivel = new RCIMNivel;
$obRCIMNivel->mascaraNivelVigenciaAtual($stMascara);
$obTxtLocalizacaoInicial = new Textbox;
$obTxtLocalizacaoInicial->setName   ( "inCodLocalizacaoInicial" );
//$obTxtLocalizacaoInicial->setTitle  ( "Localização"             );
$obTxtLocalizacaoInicial->setRotulo ( "Localização"             );
$obTxtLocalizacaoInicial->setMaxLength( strlen($stMascara)      );
$obTxtLocalizacaoInicial->setMinLength( strlen($stMascara)      );
$obTxtLocalizacaoInicial->obEvento->setOnKeyUp("mascaraDinamico(&quot;".$stMascara."&quot;,this,event)");
$obTxtLocalizacaoInicial->setValue  ( $_REQUEST["inCodLocalizacaoInicial"]  );

$obLabelIntervalo = new Label;
$obLabelIntervalo->setValue ( "&nbsp;até&nbsp;" );

$obTxtLocalizacaoFinal = new Textbox;
$obTxtLocalizacaoFinal->setName     ( "inCodLocalizacaoFinal"   );
//$obTxtLocalizacaoFinal->setTitle    ( "Localização"             );
$obTxtLocalizacaoFinal->setRotulo   ( "Localização"             );
$obTxtLocalizacaoFinal->setMaxLength( strlen($stMascara)        );
$obTxtLocalizacaoFinal->setMinLength( strlen($stMascara)        );
$obTxtLocalizacaoFinal->obEvento->setOnKeyUp("mascaraDinamico(&quot;".$stMascara."&quot;,this,event)");
$obTxtLocalizacaoFinal->setValue    ( $_REQUEST["inCodLocalizacaoFinal"]    );

$obBscInscricaoEconomica = new BuscaInnerIntervalo;
$obBscInscricaoEconomica->setRotulo         ( "*Inscrição Econômica"    );
//$obBscInscricaoEconomica->setTitle          ( "Intervalo de Valores para Inscrição Econômica");
$obBscInscricaoEconomica->obLabelIntervalo->setValue ( "até"            );
$obBscInscricaoEconomica->obCampoCod->setName       ("inNumInscricaoEconomicaInicial"  );
$obBscInscricaoEconomica->obCampoCod->setValue      ( $_REQUEST["inNumInscricaoEconomicaInicial"]  );
$obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange("buscaValor('buscaIEconomicaInicio');");     $obBscInscricaoEconomica->setFuncaoBusca("abrePopUp(&quot;".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php&quot;,&quot;frm&quot;,&quot;inNumInscricaoEconomicaInicial&quot;,&quot;stCampo&quot;,&quot;todos&quot;,&quot;".Sessao::getId()."&quot;,&quot;800&quot;,&quot;550&quot;);");
$obBscInscricaoEconomica->obCampoCod2->setName          ( "inNumInscricaoEconomicaFinal" );
$obBscInscricaoEconomica->obCampoCod2->setValue         ( $_REQUEST["inNumInscricaoEconomicaFinal"]  );
$obBscInscricaoEconomica->obCampoCod2->obEvento->setOnChange("buscaValor('buscaIEconomicaFinal');");
$obBscInscricaoEconomica->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inNumInscricaoEconomicaInicial','stCampo','todos','".Sessao::getId()."','800','550');"));

// consulta mascara
$obRCEMNivelAtividade = new RCEMNivelAtividade;
$obRCEMNivelAtividade->geraMascara( $stMascara );
$obLabelIntervaloAtvididade = new Label;
$obLabelIntervaloAtvididade->setValue ( "&nbsp;até&nbsp;" );

$obTxtAtividadeInicial = new Textbox;
$obTxtAtividadeInicial->setName     ( "inCodAtividadeInicial"   );
//$obTxtAtividadeInicial->setTitle    ( "Atividade"               );
$obTxtAtividadeInicial->setRotulo   ( "Atividade"               );
$obTxtAtividadeInicial->setValue    ( $_REQUEST["inCodAtividadeInicial"]    );
$obTxtAtividadeInicial->setMaxLength( strlen($stMascara)        );
$obTxtAtividadeInicial->setMinLength( strlen($stMascara)        );
$obTxtAtividadeInicial->obEvento->setOnKeyUp("mascaraDinamico(&quot;".$stMascara."&quot;,this,event)");

$obTxtAtividadeFinal = new Textbox;
$obTxtAtividadeFinal->setName   ( "inCodAtividadeFinal"     );
//$obTxtAtividadeFinal->setTitle  ( "Atividade"               );
$obTxtAtividadeFinal->setRotulo ( "Atividade"               );
$obTxtAtividadeFinal->setValue  ( $_REQUEST["inCodAtividadeFinal"]      );
$obTxtAtividadeFinal->setMaxLength( strlen($stMascara)      );
$obTxtAtividadeFinal->setMinLength( strlen($stMascara)      );
$obTxtAtividadeFinal->obEvento->setOnKeyUp("mascaraDinamico(&quot;".$stMascara."&quot;,this,event)");

$obLblGeral = new Label;
$obLblGeral->setName ( "stGeral" );
$obLblGeral->setValue( "&nbsp;a&nbsp;" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
$obForm->setTarget( "telaPrincipal" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Dados para o filtro"  );
//$obFormulario->addComponente( $obBscGrupoCredito );
//$obFormulario->addComponente( $obBscCredito      );
$obFormulario->addComponente( $obTxtCertidao );
$obFormulario->addComponente( $obBscContribuinte );
$obFormulario->addComponente( $obBscInscricaoImobiliaria );
$obFormulario->agrupaComponentes( array($obTxtLocalizacaoInicial, $obLblGeral, $obTxtLocalizacaoFinal) );
$obFormulario->addComponente( $obBscInscricaoEconomica   );
$obFormulario->agrupaComponentes( array($obTxtAtividadeInicial, $obLblGeral, $obTxtAtividadeFinal) );
$obFormulario->Ok();
$obFormulario->Show();
