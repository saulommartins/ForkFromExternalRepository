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
    * Pagina de Formulario de Inclusao/Alteracao da FORMULA DO INDICADOR ECONOMICO

    * Data de Criacao: 19/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FMManterFormulaIndicador.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.07

*/

/*
$Log$
Revision 1.9  2006/09/15 14:57:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONIndicadorEconomico.class.php" );
//include_once ( CAM_GT_MON_MAPEAMENTO."TMONAcrescimo.class.php" );

$obRMONIndicador =  new RMONIndicadorEconomico;

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterIndicador";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LSManterIndicador.php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PRManterFormulaIndicador.php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once ( $pgJs );

if ($stAcao == 'formula') {
    //-- BUSCA FORMULA
    $inCodIndicador = $_REQUEST["inCodIndicador"];
    $obRMONIndicador->DevolveFormula ( $rsRecordSet, $inCodIndicador );

    $x = explode ('-', $obRMONIndicador->getDtVigencia());
    $dia = $x[2].'/'.$x[1].'/'.$x[0];
    $formula = $obRMONIndicador->getCodFuncao() .' - '. $obRMONIndicador->getStrFormula();
    SistemaLegado::executaFrameOculto( 'd.getElementById("stFormula").innerHTML = "'.$formula.'";' );

    //-- BUSCA VALOR
    $obRMONIndicador->DevolveValor ( $rsRecordSet, $inCodIndicador );

}

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue ( $stCtrl );

$obHdnCodIndicador = new Hidden;
$obHdnCodIndicador->setName  ('inCodIndicador');
$obHdnCodIndicador->setValue ( $_REQUEST['inCodIndicador'] );

$obHdnDescricao = new Hidden;
$obHdnDescricao->setName  ('stDescricao');
$obHdnDescricao->setValue ( $_REQUEST['stDescricao'] );

$obHdnValor = new Hidden;
$obHdnValor->setName  ('inValor');
$obHdnValor->setValue ( $obRMONIndicador->getValor() );

$obHdnDtVigenciaAntes = new Hidden;
$obHdnDtVigenciaAntes->setName  ('dtVigenciaAntes');
$obHdnDtVigenciaAntes->setValue ( $dia );

//------------------------

$obTxtValor = new Moeda;
$obTxtValor->setRotulo  ( 'Valor');
$obTxtValor->setTitle   ( 'Valor referente ao Indicador Econômico');
$obTxtValor->setName    ( 'inValor');
$obTxtValor->setValue   ( $obRMONIndicador->getValor() );
$obTxtValor->setInteiro ( true );
$obTxtValor->setSize    ( 10 );
$obTxtValor->setMaxLength ( 10 );
$obTxtValor->setNull    ( false );

$obDtVigencia  = new Data;
$obDtVigencia->setName               ( "dtVigencia"                    );
$obDtVigencia->setValue              ( $dia                            );
$obDtVigencia->setRotulo             ( "Data de Vigência"              );
$obDtVigencia->setTitle              ( "Data em que a fórmula de cálculo passa a ser válida" );
$obDtVigencia->setMaxLength          ( 20                              );
$obDtVigencia->setSize               ( 10                              );
$obDtVigencia->setNull               ( false                           );
$obDtVigencia->obEvento->setOnChange ( "validaData1500( this );"       );

$obLblCasasDecimais = new Label;
$obLblCasasDecimais->setName   ( 'Labelx' );
$obLblCasasDecimais->setTitle  ( 'casas decimais' );
$obLblCasasDecimais->setRotulo ( 'Precisão' );
$obLblCasasDecimais->setValue  ( 'casas decimais' );

$obLblCodIndicador = new Label;
$obLblCodIndicador->setName   ( 'LabelCodIndicador' );
$obLblCodIndicador->setTitle  ( 'Código do Indicador Econômico' );
$obLblCodIndicador->setRotulo ( 'Código' );
$obLblCodIndicador->setValue  ( $_REQUEST['inCodIndicador'] );

$obLblDescricao = new Label;
$obLblDescricao->setName   ( 'LabelDescricao' );
$obLblDescricao->setTitle  ( 'Descrição do Indicador Econômico' );
$obLblDescricao->setRotulo ( 'Descrição' );
$obLblDescricao->setValue  ( $_REQUEST['stDescricao'] );

$obLblAbreviatura = new Label;
$obLblAbreviatura->setName   ( 'LabelAbreviatura' );
$obLblAbreviatura->setTitle  ( 'Abreviatura ou símbolo utilizado para referenciar o indicador econômico' );
$obLblAbreviatura->setRotulo ( 'Abreviatura' );
$obLblAbreviatura->setValue  ( $_REQUEST['stAbreviatura'] );

$obLblPrecisao = new Label;
$obLblPrecisao->setName   ( 'LabelPrecisao' );
$obLblPrecisao->setTitle  ( 'Precisão utilizada nos valores referentes ao Indicador Econômico' );
$obLblPrecisao->setRotulo ( 'Precisão' );
$obLblPrecisao->setValue  ( $_REQUEST['inPrecisao'] );

//---- INNER PARA ALTERACAO
$obBscFormulaCalculo2 = new BuscaInner;
$obBscFormulaCalculo2->setRotulo ( "*Fórmula de Cálculo" );
$obBscFormulaCalculo2->setTitle  ( "Fórmula de cálculo utilizada para converter valores utilizando-se o indicador"  );
$obBscFormulaCalculo2->setId     ( "stFormula"  );
$obBscFormulaCalculo2->obCampoCod->setName   ( "inCodFuncao" );
$obBscFormulaCalculo2->obCampoCod->setValue  ( $obRMONIndicador->getCodModulo ().'.'.$obRMONIndicador->getCodBiblioteca ().'.'.$obRMONIndicador->getCodFuncao () );
$obBscFormulaCalculo2->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncao');");
$obBscFormulaCalculo2->setFuncaoBusca ( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncao','stFormula','','".Sessao::getId()."&stCodModulo=28&stCodBiblioteca=3&','800','550');" );

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

if ($stAcao == "formula") {
$obFormulario->addHidden     ( $obHdnDtVigenciaAntes );
$obFormulario->addHidden     ( $obHdnCodIndicador );
$obFormulario->addComponente ( $obLblCodIndicador );
$obFormulario->addHidden     ( $obHdnDescricao );
$obFormulario->addHidden     ( $obHdnValor );
}

$obFormulario->addComponente ( $obLblDescricao );
$obFormulario->addComponente ( $obLblAbreviatura );
$obFormulario->agrupaComponentes ( array ($obLblPrecisao, $obLblCasasDecimais ));

$obFormulario->addComponente ( $obBscFormulaCalculo2 );
$obFormulario->addComponente ( $obDtVigencia );

if ($stAcao == "incluir") {
    $obFormulario->ok       ();
} else {
    $obFormulario->cancelar ();
}

$obFormulario->show();

$stJs .= 'f.inCodFuncao.focus();';
sistemaLegado::executaFrameOculto ( $stJs );
