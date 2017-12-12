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
    * Página de Formulario de desdobramento Receita
    * Data de Criação   : 10/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    $Id: FMManterDesdobramentoReceita.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeDesdobramentoReceita.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterDesdobramentoReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once $pgJS;
include_once $pgOcul;

$arReceitasSecundarias = Sessao::read('arReceitasSecundarias');
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "desdobrar";
}

$obROrcamentoReceita = new ROrcamentoReceita;
$obROrcamentoReceita->setCodReceita( $_REQUEST['inCodReceita'] );
$obROrcamentoReceita->setExercicio( Sessao::getExercicio() );
$obROrcamentoReceita->listar( $rsReceita );

//VOLTA PARA PÁGINA DE FILTRO CASO O CODIGO DA RECEITA SEJA INVÁLIDO
if ( $rsReceita->eof() ) {
    SistemaLegado::alertaAviso($pgFilt,"Valor inválido. (".$_REQUEST['inCodReceita'].")" ,"cc","aviso", Sessao::getId(), "../");
}

$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obROrcamentoEntidade->setCodigoEntidade( $rsReceita->getCampo("cod_entidade") );
$obROrcamentoEntidade->consultar( $rs );

$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->setCodModulo( 8 );
$obROrcamentoConfiguracao->consultarConfiguracao();
$stMascaraRecurso = $obROrcamentoConfiguracao->getMascRecurso();

$stPercentualAtualizado = 100;
if ( $_REQUEST["inCodReceita"] and count( $arReceitasSecundarias ) == 0 ) {
    $obRContabilidadeDesdobramentoReceita = new RContabilidadeDesdobramentoReceita( $obROrcamentoReceita );
    $obRContabilidadeDesdobramentoReceita->consultar();
    $obErro = $obRContabilidadeDesdobramentoReceita->geraListaReceitaSecundaria( $arReceitasSecundarias );
    foreach ($arReceitasSecundarias as  $arDesdobramentoReceita) {
        $nuPercentual = str_replace( ".", "", $arDesdobramentoReceita["percentual"] );
        $nuPercentual = str_replace( ",", ".", $nuPercentual );
        $stPercentualAtualizado -= $nuPercentual;
    }
    Sessao::write('arReceitasSecundarias', $arReceitasSecundarias);
}

if ( is_array( $arReceitasSecundarias ) ) {
    $rsLista = new RecordSet;
    $rsLista->preenche( $arReceitasSecundarias );
    $rsLista->addStrPad( "cod_recurso", strlen($stMascaraRecurso) );
    $stJs .= montaListaReceitaSecundaria( $rsLista );
    SistemaLegado::executaFramePrincipal($stJs);
}

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodigoReceitaPrincipal = new Hidden;
$obHdnCodigoReceitaPrincipal->setName  ( "inCodigoReceitaPrincipal" );
$obHdnCodigoReceitaPrincipal->setValue ( $_REQUEST['inCodReceita'] );

$obHdnCodigoEntidadePrincipal = new Hidden;
$obHdnCodigoEntidadePrincipal->setName  ( "inCodigoEntidadePrincipal" );
$obHdnCodigoEntidadePrincipal->setValue ( $obROrcamentoEntidade->getCodigoEntidade() );

$obHdnMascaraRecurso = new Hidden;
$obHdnMascaraRecurso->setName  ( "stMascaraRecurso" );
$obHdnMascaraRecurso->setValue ( $stMascaraRecurso );

$obLblEntidade = new Label;
$obLblEntidade->setRotulo ( "Entidade" );
$obLblEntidade->setName   ( "stEntidade" );
$obLblEntidade->setValue  ( $obROrcamentoEntidade->getCodigoEntidade()." - ".$obROrcamentoEntidade->obRCGM->getNomCGM() );

$obLblReceitaPrincipal = new Label;
$obLblReceitaPrincipal->setRotulo ( "Receita Principal" );
$obLblReceitaPrincipal->setName   ( "stReceitaPrincipal" );
$stValueReceitaPrincipal  = $rsReceita->getcampo( "cod_receita" )." - ";
$stValueReceitaPrincipal .= $rsReceita->getcampo( "mascara_classificacao" )." - ";
$stValueReceitaPrincipal .= $rsReceita->getcampo( "descricao" );
$obLblReceitaPrincipal->setValue  ( $stValueReceitaPrincipal );

$obLblRecurso = new Label;
$obLblRecurso->setRotulo ( "Recurso" );
$obLblRecurso->setName   ( "stRecurso" );
$stValueRecurso  = str_pad( $rsReceita->getcampo( "cod_recurso" ), strlen( $stMascaraRecurso ),"0", STR_PAD_LEFT )." - ";
$stValueRecurso .= $rsReceita->getcampo( "nom_recurso" );
$obLblRecurso->setValue  ( $stValueRecurso );

$obLblPercentualAtualizado = new Label;
$obLblPercentualAtualizado->setName  ( "stPercentualAtualizado" );
$obLblPercentualAtualizado->setRotulo( "Percentual" );
$obLblPercentualAtualizado->setValue (  number_format( $stPercentualAtualizado, 2, ",", ".")."%" );
$obLblPercentualAtualizado->setID    ( "stPercentualAtualizado" );

$obHdnPercentualAtualizado = new Hidden;
$obHdnPercentualAtualizado->setName ( "stPercentualAtualizado" );
$obHdnPercentualAtualizado->setValue ( $stPercentualAtualizado );

// Define Objeto BuscaInner para Receita
$obBscReceita = new BuscaInner;
$obBscReceita->setRotulo ( "*Receita Secundária" );
$obBscReceita->setTitle ( "Informe uma receita orçamentária" );
$obBscReceita->setId ( "stNomReceita" );
$obBscReceita->obCampoCod->setName ( "inCodReceita" );
$obBscReceita->obCampoCod->setSize ( 10 );
$obBscReceita->obCampoCod->setMaxLength( 5 );
$obBscReceita->obCampoCod->setAlign ("left");
$obBscReceita->obCampoCod->obEvento->setOnChange("buscaDado('buscaReceita');");
$obBscReceita->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."receita/LSReceita.php','frm','inCodReceita','stNomReceita','','".Sessao::getId()."','800','550');");

$obNumPercentual = new Numerico;
$obNumPercentual->setRotulo    ( "*Percentual" );
$obNumPercentual->setName      ( "flPercentual" );
$obNumPercentual->setTitle     ( "Informe o percentual" );
$obNumPercentual->setNegativo  ( false );
$obNumPercentual->setMaxValue  ( 100.00 );
$obNumPercentual->setSize      ( 5 );
$obNumPercentual->setMaxLength ( 5 );

$obLblPercentual = new Label;
$obLblPercentual->setValue ( "%" );

$obBtnIncluir = new Button;
$obBtnIncluir->setValue ( "Incluir" );
$obBtnIncluir->obEvento->setOnClick( "incluirReceitaSecundaria();" );

$obBtnLimpar = new Button;
$obBtnLimpar->setValue( "Limpar" );
//$obBtnLimpar->obEvento->setOnClick( "buscaDado('limparReceitaSecundaria');" );
$obBtnLimpar->obEvento->setOnClick( "limparReceitaSecundaria();" );

$obSpnReceitaSecundaria = new Span;
$obSpnReceitaSecundaria->setId( "spnReceitaSecundaria" );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados para Desdobramento de Receita" );
$obFormulario->setAjuda             ('UC-02.02.01');
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCodigoReceitaPrincipal  );
$obFormulario->addHidden    ( $obHdnCodigoEntidadePrincipal );
$obFormulario->addHidden    ( $obHdnPercentualAtualizado    );
$obFormulario->addHidden    ( $obHdnMascaraRecurso          );
$obFormulario->addComponente ( $obLblEntidade );
$obFormulario->addComponente ( $obLblReceitaPrincipal );
$obFormulario->addComponente ( $obLblRecurso );
$obFormulario->addComponente ( $obLblPercentualAtualizado );
$obFormulario->addComponente ( $obBscReceita );
$obFormulario->agrupaComponentes ( array( $obNumPercentual , $obLblPercentual ) );
$obFormulario->defineBarra   ( array( $obBtnIncluir, $obBtnLimpar ) );
$obFormulario->addSpan       ( $obSpnReceitaSecundaria );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
