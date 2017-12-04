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
    * Página de Formulario de Inclusao/Alteracao de MOEDA

    * Data de Criação   : 16/12/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FMManterMoeda.php 60940 2014-11-25 18:03:14Z michel $

    *Casos de uso: uc-05.05.06

*/

/*
$Log$
Revision 1.9  2006/09/15 14:58:03  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONMoeda.class.php" );
include_once ( CAM_GT_MON_COMPONENTES."IPopUpMoeda.class.php" );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterMoeda";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

//Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
/*
$obRMONMoeda = new RMONMoeda;
if ($stAcao == 'alterar') {
    $obRMONMoeda->BuscaRegraDaMoeda ( $rsRecordSet, $_REQUEST["inCodMoeda"] );

    if($rsRecordSet->getNumLinhas()>0){
        $formula = $obRMONMoeda->getCodFuncao() .' - '. $obRMONMoeda->getStrFormula();
        SistemaLegado::executaFrameOculto( 'd.getElementById("stFormula").innerHTML = "'.$formula.'";' );
    
        $stFormulaCompleta = $obRMONMoeda->getCodModulo (). '.'.$obRMONMoeda->getCodBiblioteca ().'.'.$obRMONMoeda->getCodFuncao ();
    }
}
*/

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST["stCtrl"]  );

$obHdnCodMoeda =  new Hidden;
$obHdnCodMoeda->setName   ( "inCodMoeda" );
$obHdnCodMoeda->setValue  ( $_REQUEST["inCodMoeda"]  );

$obLblCodMoeda = new Label ;
$obLblCodMoeda->setRotulo    ( "Código da Moeda" );
$obLblCodMoeda->setName      ( "labelCodMoeda");
$obLblCodMoeda->setValue     ( $_REQUEST["inCodMoeda"] );
$obLblCodMoeda->setTitle     ( "Código da Moeda" );

$obTxtDescSingular = new TextBox ;
$obTxtDescSingular->setRotulo    ( "Descrição no Singular" );
$obTxtDescSingular->setName      ( "stDescSingular");
$obTxtDescSingular->setValue     ( $_REQUEST["stDescSingular"] );
$obTxtDescSingular->setTitle     ( "Descrição da moeda no singular" );
$obTxtDescSingular->setSize      ( 40 );
$obTxtDescSingular->setMaxLength ( 40 );
$obTxtDescSingular->setNull      ( false );

$obTxtDescPlural = new TextBox ;
$obTxtDescPlural->setRotulo    ( "Descrição no Plural" );
$obTxtDescPlural->setName      ( "stDescPlural");
$obTxtDescPlural->setValue     ( $_REQUEST["stDescPlural"] );
$obTxtDescPlural->setTitle     ( "Descrição da moeda no plural" );
$obTxtDescPlural->setSize      ( 40 );
$obTxtDescPlural->setMaxLength ( 40 );
$obTxtDescPlural->setNull      ( false );

$obTxtFracaoSingular = new TextBox ;
$obTxtFracaoSingular->setRotulo    ( "Fração no Singular" );
$obTxtFracaoSingular->setName      ( "stFracaoSingular");
$obTxtFracaoSingular->setValue     ( $_REQUEST["stFracaoSingular"] );
$obTxtFracaoSingular->setTitle     ( "Fração da moeda no singular" );
$obTxtFracaoSingular->setSize      ( 40 );
$obTxtFracaoSingular->setMaxLength ( 40 );
$obTxtFracaoSingular->setNull      ( false );

$obTxtFracaoPlural = new TextBox ;
$obTxtFracaoPlural->setRotulo    ( "Fração no Plural" );
$obTxtFracaoPlural->setName      ( "stFracaoPlural");
$obTxtFracaoPlural->setValue     ( $_REQUEST["stFracaoPlural"] );
$obTxtFracaoPlural->setTitle     ( "Fração da moeda no plural" );
$obTxtFracaoPlural->setSize      ( 40 );
$obTxtFracaoPlural->setMaxLength ( 40 );
$obTxtFracaoPlural->setNull      ( false );

$obTxtSimbolo = new TextBox ;
$obTxtSimbolo->setRotulo    ( "Símbolo da Moeda" );
$obTxtSimbolo->setName      ( "stSimbolo");
$obTxtSimbolo->setValue     ( $_REQUEST["stSimbolo"] );
$obTxtSimbolo->setTitle     ( "Símbolo da Moeda" );
$obTxtSimbolo->setSize      ( 4 );
$obTxtSimbolo->setMaxLength ( 4 );
$obTxtSimbolo->setNull      ( false );

$obDtVigencia  = new Data;
$obDtVigencia->setName               ( "dtVigencia"                    );
$obDtVigencia->setValue              ( $_REQUEST['dtVigencia']           );
$obDtVigencia->setRotulo             ( "Data de Início da Vigência"      );
$obDtVigencia->setTitle              ( "Data em que a moeda entrou em vigor" );
$obDtVigencia->setMaxLength          ( 20                                );
$obDtVigencia->setSize               ( 10                                );
$obDtVigencia->setNull               ( false                             );
$obDtVigencia->obEvento->setOnChange ( "validaData1500( this );"         );

//Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
/*
$obBscFormulaCalculo = new BuscaInner;
$obBscFormulaCalculo->setRotulo     ( "Regra de Conversão" );
$obBscFormulaCalculo->setTitle      ( "Regra de conversão da moeda para sua antecessora"  );
$obBscFormulaCalculo->setId         ( "stFormula"  );
$obBscFormulaCalculo->setNull       ( false );
$obBscFormulaCalculo->obCampoCod->setName   ( "inCodFuncao" );
$obBscFormulaCalculo->obCampoCod->setValue  ( $stFormulaCompleta );
$obBscFormulaCalculo->obCampoCod->obEvento->setOnChange ("buscaValor('buscaFuncao');");
$obBscFormulaCalculo->setFuncaoBusca ( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncao','stFormula','','".Sessao::getId()."&stCodModulo=28&stCodBiblioteca=4&','800','550');" );

//---- COMPONENTES SE FOR ALTERACAO

$obHdnStrFormulaAntiga =  new Hidden;
$obHdnStrFormulaAntiga->setName   ( "stFormulaAntiga" );
$obHdnStrFormulaAntiga->setValue  ( $stFormulaCompleta );
*/

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
//$obForm->setTarget( $pgOcul );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda ( "UC-05.05.06" );
$obFormulario->addTitulo     ( "Dados para Moeda" );

$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );

if ($stAcao == "alterar") {
    $obFormulario->addHidden ($obHdnCodMoeda);
    //Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
    //$obFormulario->addHidden ($obHdnStrFormulaAntiga);
    $obFormulario->addComponente ( $obLblCodMoeda );
}

$obFormulario->addComponente ( $obTxtDescSingular );
$obFormulario->addComponente ( $obTxtDescPlural );
$obFormulario->addComponente ( $obTxtFracaoSingular );
$obFormulario->addComponente ( $obTxtFracaoPlural );
$obFormulario->addComponente ( $obTxtSimbolo );
$obFormulario->addComponente ( $obDtVigencia );

//Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
//$obFormulario->addComponente ( $obBscFormulaCalculo );

if ($stAcao == "incluir") {
    $obFormulario->Ok       ();
} else {
    $obFormulario->Cancelar ();
}

$obFormulario->show();

$stJs .= 'f.stDescSingular.focus();';
sistemaLegado::executaFrameOculto ( $stJs );
