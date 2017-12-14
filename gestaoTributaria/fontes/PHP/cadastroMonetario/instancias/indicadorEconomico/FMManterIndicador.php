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
    * Pagina de Formulario de Inclusao/Alteracao de INDICADOR ECONOMICO

    * Data de Criacao: 19/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FMManterIndicador.php 60940 2014-11-25 18:03:14Z michel $

    *Casos de uso: uc-05.05.07

*/

/*
$Log$
Revision 1.7  2006/09/15 14:57:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONIndicadorEconomico.class.php" );

$obRMONIndicador =  new RMONIndicadorEconomico;

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterIndicador";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once ( $pgJs );

//Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
/*
if ($stAcao == 'alterar') {
    //-- BUSCA FORMULA
    $inCodIndicador = $_REQUEST["inCodIndicador"];
    $obRMONIndicador->DevolveFormula ( $rsRecordSet, $inCodIndicador );
    $x = explode ('-', $obRMONIndicador->getDtVigencia());
    $dia = $x[2].'/'.$x[1].'/'.$x[0];
    $formula = $obRMONIndicador->getCodFuncao() .' - '. $obRMONIndicador->getStrFormula();
    SistemaLegado::executaFrameOculto( 'd.getElementById("stFormula").innerHTML = "'.$formula.'";' );
}
*/

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnCodIndicador = new Hidden;
$obHdnCodIndicador->setName  ('inCodIndicador');
$obHdnCodIndicador->setValue ( $_REQUEST['inCodIndicador'] );

//Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
/*
$obHdnDtVigenciaAntes = new Hidden;
$obHdnDtVigenciaAntes->setName  ('dtVigenciaAntes');
$obHdnDtVigenciaAntes->setValue ( $obRMONIndicador->getDtVigencia() );
*/

//------------------------

$obTxtCodIndicador = new TextBox;
$obTxtCodIndicador->setRotulo  ( 'Código');
$obTxtCodIndicador->setTitle   ( 'Código do Indicador');
$obTxtCodIndicador->setName    ( 'inCodIndicador');
$obTxtCodIndicador->setValue   ( $inCodIndicador );
$obTxtCodIndicador->setInteiro ( false );
$obTxtCodIndicador->setSize    ( 10 );
$obTxtCodIndicador->setMaxLength ( 10 );
$obTxtCodIndicador->setNull    ( false );

$obTxtAbreviatura = new TextBox;
$obTxtAbreviatura->setRotulo  ( 'Abreviatura');
$obTxtAbreviatura->setTitle   ( 'Abreviatura ou símbolo utilizado para referenciar o indicador econômico');
$obTxtAbreviatura->setName    ( 'stAbreviatura');
$obTxtAbreviatura->setValue   ( $_REQUEST["stAbreviatura"] );
$obTxtAbreviatura->setInteiro ( false );
$obTxtAbreviatura->setSize    ( 15 );
$obTxtAbreviatura->setMaxLength ( 15 );
$obTxtAbreviatura->setNull    ( false );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo  ( 'Descrição');
$obTxtDescricao->setTitle   ( 'Descrição do Indicador Econômico');
$obTxtDescricao->setName    ( 'stDescricao');
$obTxtDescricao->setValue   ( $_REQUEST["stDescricao"] );
$obTxtDescricao->setInteiro ( false );
$obTxtDescricao->setSize    ( 80 );
$obTxtDescricao->setMaxLength ( 80 );
$obTxtDescricao->setNull    ( false );

$obTxtPrecisao = new TextBox;
$obTxtPrecisao->setRotulo  ( 'Precisão');
$obTxtPrecisao->setTitle   ( 'Precisão utilizada nos valores referentes ao Indicador Econômico');
$obTxtPrecisao->setName    ( 'inPrecisao');
$obTxtPrecisao->setValue   ( $_REQUEST["inPrecisao"] );
$obTxtPrecisao->setInteiro ( true );
$obTxtPrecisao->setSize    ( 10 );
$obTxtPrecisao->setMaxLength ( 10 );
$obTxtPrecisao->setNull    ( false );

$obLblCasasDecimais = new Label;
$obLblCasasDecimais->setName   ( 'Labelx' );
$obLblCasasDecimais->setTitle  ( 'casas decimais' );
$obLblCasasDecimais->setRotulo ( 'Precisão' );
$obLblCasasDecimais->setValue  ( 'casas decimais' );

$obLblCodIndicador = new Label;
$obLblCodIndicador->setName   ( 'LabelCodIndicador' );
$obLblCodIndicador->setTitle  ( 'Código' );
$obLblCodIndicador->setRotulo ( 'Código do Indicador' );
$obLblCodIndicador->setValue  ( $_REQUEST['inCodIndicador'] );

//Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
/*
$obLblCodFuncao = new Label;
$obLblCodFuncao->setName  ( 'LabelCodFuncao' );
$obLblCodFuncao->setTitle ( 'Fórmula de Cálculo' );
$obLblCodFuncao->setRotulo( 'Fórmula de Cálculo' );
$obLblCodFuncao->setValue ($_REQUEST['inCodModulo'].'.'.$_REQUEST['inCodBiblioteca'].'.'.$_REQUEST['inCodFuncao']);

$obBscFormulaCalculo = new BuscaInner;
$obBscFormulaCalculo->setRotulo ( "*Fórmula de Cálculo" );
$obBscFormulaCalculo->setTitle  ( "Fórmula de cálculo utilizada para converter valores utilizando-se o indicador"  );
$obBscFormulaCalculo->setId     ( "stFormula"  );
$obBscFormulaCalculo->obCampoCod->setName   ( "inCodFuncao" );
$obBscFormulaCalculo->obCampoCod->setValue  ( $_REQUEST["inCodFuncao"] );
$obBscFormulaCalculo->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncao');");
$obBscFormulaCalculo->setFuncaoBusca ( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncao','stFormula','','".Sessao::getId()."&stCodModulo=28&stCodBiblioteca=3&','800','550');" );
*/

//--------------------------------
// DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ("oculto" );
//------------------------------------------------------
//MONTA FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda ( "UC-05.05.07" );
$obFormulario->addTitulo ('Dados para o Indicador Econômico');

$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

if ($stAcao == "alterar") {
    //Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
    //$obFormulario->addHidden     ( $obHdnDtVigenciaAntes );
    $obFormulario->addHidden     ( $obHdnCodIndicador );
    $obFormulario->addComponente ( $obLblCodIndicador );
}

$obFormulario->addComponente ( $obTxtDescricao );
$obFormulario->addComponente ( $obTxtAbreviatura );
$obFormulario->agrupaComponentes ( array ($obTxtPrecisao, $obLblCasasDecimais ));

//Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
//$obFormulario->addComponente ( $obBscFormulaCalculo );

if ($stAcao == "incluir") {
    $obFormulario->ok       ();
} else {
    $obFormulario->cancelar ();
}

$obFormulario->show();

$stJs .= 'f.stDescricao.focus();';
sistemaLegado::executaFrameOculto ( $stJs );
