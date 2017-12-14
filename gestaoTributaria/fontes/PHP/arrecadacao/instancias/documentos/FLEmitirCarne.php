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
  * Página de Filtro de Emissão de Carnês
  * Data de criação : 11/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

    * $Id: FLEmitirCarne.php 61735 2015-02-27 18:27:50Z evandro $

  Caso de uso: uc-05.03.11
**/

/*
$Log$
Revision 1.21  2006/12/13 16:08:59  cercato
correcao para a popup de contribuinte fechar apos selecionar.

Revision 1.20  2006/11/28 10:27:24  cercato
bug #7675#

Revision 1.19  2006/09/15 11:50:45  fabio
corrigidas tags de caso de uso

Revision 1.18  2006/09/15 11:08:05  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php");
include_once( CAM_GT_CEM_NEGOCIO."RCEMNivelAtividade.class.php");
include_once( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/componentes/MontaGrupoCredito.class.php';

//Definicao dos nomes de arquivos
$stPrograma = "EmitirCarne";
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
    $stAcao = "reemitir";
}

Sessao::write( "link", "" );

$obRMONCredito = new RMONCredito;
$obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRMONCredito->getMascaraCredito();
$obMontaGrupoCredito = new MontaGrupoCredito;

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obHdnExercicioGrupo  = new Hidden;
$obHdnExercicioGrupo->setName   ( "inExercicioGrupo" );
$obHdnExercicioGrupo->setValue  ( isset($_REQUEST["inExercicioGrupo"]  ));

$obTxtExercicio = new Exercicio;
$obTxtExercicio->setName ('inExercicio');
$obTxtExercicio->setValue(Sessao::getExercicio());

$obHdnCampoNome  = new Hidden;
$obHdnCampoNome->setName   ( "stNome" );

$obTxtNumeracaoAnterior = new TextBox;
$obTxtNumeracaoAnterior->setId('inNumAnterior');
$obTxtNumeracaoAnterior->setName('inNumAnterior');
$obTxtNumeracaoAnterior->setRotulo('Numeração Anterior');
$obTxtNumeracaoAnterior->setTitle('Numeração Anterior do Carnê que deseja Emitir <hr /> <i>Somente Numeros Inteiros <br /> Ex: 432101002040 </i>');
$obTxtNumeracaoAnterior->setMaxLength(20);
$obTxtNumeracaoAnterior->setSize(30);
$obTxtNumeracaoAnterior->setInteiro(true);
$obTxtNumeracaoAnterior->obEvento->setOnChange("montaParametrosGET( ?verificaNumeracao?, ?inNumAnterior? );");

$obBscCredito = new BuscaInner;
$obBscCredito->setRotulo    ( "Crédito"        );
$obBscCredito->setTitle     ( "Busca Crédito"   );
$obBscCredito->setId        ( "stCredito"       );
$obBscCredito->obCampoCod->setStyle     ( "width: 80px"   );
$obBscCredito->obCampoCod->setName      ("inCodCredito"             );
if (isset($inCodCredito)) {
    $obBscCredito->obCampoCod->setValue     ( isset($inCodCredito)             );
}
$obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara   ($stMascaraCredito          );
$obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaCredito');");
$obBscCredito->setFuncaoBusca("abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCredito','stCredito','todos','".Sessao::getId()."','800','550');" );

$obBscGrupoCredito = new BuscaInner;
$obBscGrupoCredito->setRotulo                ( "Grupo de Créditos"          );
$obBscGrupoCredito->setTitle                 ( "Busca Grupo de Créditos"    );
$obBscGrupoCredito->setId                    ( "stGrupo"        );
$obBscGrupoCredito->obCampoCod->setName      ( "inCodGrupo"      );
if (isset($inCodGrupo)) {
    $obBscGrupoCredito->obCampoCod->setValue ( $inCodGrupo       );
}
$obBscGrupoCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaGrupo');");
$obBscGrupoCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inCodGrupo','stGrupo','todos','".Sessao::getId()."','800','350');" );

$obBscContribuinte = new BuscaInnerIntervalo;
$obBscContribuinte->setRotulo                           ( "Contribuinte"    );
$obBscContribuinte->obLabelIntervalo->setValue          ( "até"          );
$obBscContribuinte->obCampoCod->setName                 ( "inCodContribuinteInicial"  );
if (isset($inCodContribuinteInicio))
    $obBscContribuinte->obCampoCod->setValue            ( $inCodContribuinteInicio );
$obBscContribuinte->obCampoCod->obEvento->setOnChange   ( "buscaValor('buscaContribuinteInicio');");
$obBscContribuinte->setFuncaoBusca                      ( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteInicial','stNome','','".Sessao::getId()."','800','450');" ));
$obBscContribuinte->obCampoCod2->setName                ( "inCodContribuinteFinal"  );
if (isset($inCodContribuinteFinal)) 
    $obBscContribuinte->obCampoCod2->setValue           ( $inCodContribuinteFinal );
$obBscContribuinte->obCampoCod2->obEvento->setOnChange  ("buscaValor('buscaContribuinteFinal');");
$obBscContribuinte->setFuncaoBusca2                     ( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteFinal','stNome','','".Sessao::getId()."','800','450');" ));

$obBscInscricaoImobiliaria = new BuscaInnerIntervalo;
$obBscInscricaoImobiliaria->setRotulo                           ( "Inscrição Imobiliária"   );
$obBscInscricaoImobiliaria->obLabelIntervalo->setValue          ( "até"          );
$obBscInscricaoImobiliaria->obCampoCod->setName                 ("inNumInscricaoImobiliariaInicial"  );
$obBscInscricaoImobiliaria->obCampoCod->setMaxLength            ( 8 );
if (isset($inNumInscricaoImobiliariaInicial) )
    $obBscInscricaoImobiliaria->obCampoCod->setValue            ( $inNumInscricaoImobiliariaInicial  );
$obBscInscricaoImobiliaria->obCampoCod->obEvento->setOnChange   ("buscaValor('buscaIImobiliariaInicio');");
$obBscInscricaoImobiliaria->setFuncaoBusca                      ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaInicial','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));
$obBscInscricaoImobiliaria->obCampoCod2->setName                ( "inNumInscricaoImobiliariaFinal" );
$obBscInscricaoImobiliaria->obCampoCod2->setMaxLength           ( 8 );
if (isset($inNumInscricaoImobiliariaFinal ) )
    $obBscInscricaoImobiliaria->obCampoCod2->setValue           ( $inNumInscricaoImobiliariaFinal  );
$obBscInscricaoImobiliaria->obCampoCod2->obEvento->setOnChange  ("buscaValor('buscaIImobiliariaFinal');");
$obBscInscricaoImobiliaria->setFuncaoBusca2                     ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaFinal','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));

$obBscInscricaoEconomica = new BuscaInnerIntervalo;
$obBscInscricaoEconomica->setRotulo                         ( "Inscrição Econômica"    );
$obBscInscricaoEconomica->obLabelIntervalo->setValue        ( "até"            );
$obBscInscricaoEconomica->obCampoCod->setName               ("inNumInscricaoEconomicaInicial"  );
$obBscInscricaoEconomica->obCampoCod->setMaxLength          ( 8 );
if ( isset($inNumInscricaoEconomicaInicial) )
    $obBscInscricaoEconomica->obCampoCod->setValue          ( $inNumInscricaoEconomicaInicial  );
$obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange ("buscaValor('buscaIEconomicaInicio');");
$obBscInscricaoEconomica->setFuncaoBusca                    ("abrePopUp(&quot;".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php&quot;,&quot;frm&quot;,&quot;inNumInscricaoEconomicaInicial&quot;,&quot;stCampo&quot;,&quot;todos&quot;,&quot;".Sessao::getId()."&quot;,&quot;800&quot;,&quot;550&quot;);");
$obBscInscricaoEconomica->obCampoCod2->setName              ( "inNumInscricaoEconomicaFinal" );
$obBscInscricaoEconomica->obCampoCod2->setMaxLength         ( 8 );
if ( isset($inNumInscricaoEconomicaFinal) )
    $obBscInscricaoEconomica->obCampoCod2->setValue         ( $inNumInscricaoEconomicaFinal  );
$obBscInscricaoEconomica->obCampoCod2->obEvento->setOnChange("buscaValor('buscaIEconomicaFinal');");
$obBscInscricaoEconomica->setFuncaoBusca2                   ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inNumInscricaoEconomicaFinal','stCampo','todos','".Sessao::getId()."','800','550');"));

$obRCIMNivel = new RCIMNivel;
$obRCIMNivel->mascaraNivelVigenciaAtual($stMascara);

$obBscLocalizacao = new BuscaInnerIntervalo;
$obBscLocalizacao->setRotulo                            ( "Localização"    );
$obBscLocalizacao->obLabelIntervalo->setValue           ( "até"            );
$obBscLocalizacao->obCampoCod->setId                    ( "stLocal1"        );
$obBscLocalizacao->obCampoCod->setName                  ("inCodLocalizacaoInicial"  );
if ( isset($inCodLocalizacaoInicial) ) 
    $obBscInscricaoEconomica->obCampoCod->setValue      ( $inCodLocalizacaoInicial );
$obBscLocalizacao->obCampoCod->setMaxLength             ( strlen($stMascara)      );
$obBscLocalizacao->obCampoCod->obEvento->setOnKeyUp     ("mascaraDinamico(&quot;".$stMascara."&quot;,this,event)");
$obBscLocalizacao->obCampoCod->obEvento->setOnChange    ("buscaValor('buscaLocalizacaoInicio');");
$obBscLocalizacao->setFuncaoBusca                       ("abrePopUp(&quot;".CAM_GT_CIM_POPUPS."localizacao/FLBuscaLocalizacao.php&quot;,&quot;&quotfrm&quot;,&quot;inCodLocalizacaoInicial&quot;,&quot;stLocal1&quot;,&quot;todos&quot;,&quot;".Sessao::getId()."&quot;,&quot;800&quot;,&quot;550&quot;);");
$obBscLocalizacao->obCampoCod2->setId                   ( "stLocal2"        );
$obBscLocalizacao->obCampoCod2->setName                 ( "inCodLocalizacaoFinal" );
if ( isset($inCodLocalizacaoFinal) )
    $obBscInscricaoEconomica->obCampoCod->setValue      ( $inCodLocalizacaoFinal);
$obBscLocalizacao->obCampoCod2->setMaxLength            ( strlen($stMascara)      );
$obBscLocalizacao->obCampoCod2->obEvento->setOnKeyUp    ("mascaraDinamico(&quot;".$stMascara."&quot;,this,event)");
$obBscLocalizacao->obCampoCod2->obEvento->setOnChange   ("buscaValor('buscaLocalizacaoFim');");
$obBscLocalizacao->setFuncaoBusca2                      ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."localizacao/FLBuscaLocalizacao.php','frm','inCodLocalizacaoFinal','stLocal2','todos','".Sessao::getId()."','800','550');"));

$obRdbTipoParcelasUnica = new Radio;
$obRdbTipoParcelasUnica->setRotulo   ( "Tipo de Parcelas" );
$obRdbTipoParcelasUnica->setTitle    ( "Informe o tipo parcela a ser filtrado." );
$obRdbTipoParcelasUnica->setName     ( "stTipoParcela" );
$obRdbTipoParcelasUnica->setLabel    ( "Únicas" );
$obRdbTipoParcelasUnica->setValue    ( "unicas" );
$obRdbTipoParcelasUnica->setNull     ( false );

$obRdbTipoParcelasNormais = new Radio;
$obRdbTipoParcelasNormais->setRotulo   ( "Tipo de Parcelas" );
$obRdbTipoParcelasNormais->setTitle    ( "Informe o tipo parcela a ser filtrado." );
$obRdbTipoParcelasNormais->setName     ( "stTipoParcela" );
$obRdbTipoParcelasNormais->setLabel    ( "Normais" );
$obRdbTipoParcelasNormais->setValue    ( "normais" );
$obRdbTipoParcelasNormais->setNull     ( false );

$obRdbTipoParcelasAmbas = new Radio;
$obRdbTipoParcelasAmbas->setRotulo   ( "Tipo de Parcelas" );
$obRdbTipoParcelasAmbas->setTitle    ( "Informe o tipo parcela a ser filtrado." );
$obRdbTipoParcelasAmbas->setName     ( "stTipoParcela" );
$obRdbTipoParcelasAmbas->setLabel    ( "Ambas" );
$obRdbTipoParcelasAmbas->setValue    ( "ambas" );
$obRdbTipoParcelasAmbas->setNull     ( false );
$obRdbTipoParcelasAmbas->setChecked  ( true );

$obHdnEmissao =  new Hidden;
$obHdnEmissao->setName   ( "emissao_carnes" );
$obHdnEmissao->setValue  ( "local" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
$obForm->setTarget( "telaPrincipal" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnEmissao );
$obFormulario->addHidden ( $obHdnExercicioGrupo );
$obFormulario->addHidden ( $obHdnCampoNome );
$obFormulario->addTitulo( "Dados para o Filtro"  );
$obFormulario->addComponente( $obTxtExercicio    );
$obFormulario->addComponente( $obTxtNumeracaoAnterior );

$obFormulario->agrupaComponentes( array( $obRdbTipoParcelasUnica, $obRdbTipoParcelasNormais, $obRdbTipoParcelasAmbas ) );

$obMontaGrupoCredito->geraFormulario( $obFormulario, true, true );

$obFormulario->addComponente( $obBscCredito      );
$obFormulario->addComponente( $obBscContribuinte );
$obFormulario->addComponente( $obBscInscricaoEconomica );
$obFormulario->addComponente( $obBscInscricaoImobiliaria );
$obFormulario->addComponente( $obBscLocalizacao );

$obFormulario->Ok();
$obFormulario->setFormFocus($obTxtExercicio->getId());
$obFormulario->Show();
