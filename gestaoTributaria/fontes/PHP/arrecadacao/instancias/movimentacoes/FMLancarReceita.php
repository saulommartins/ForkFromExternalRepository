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
  * Página de Formulario para Lançar Receita
  * Data de criação : 17/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: FMLancarReceita.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.06
**/

/*
$Log$
Revision 1.6  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRAvaliacaoEconomica.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "LancarReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$obRARRAvaliacaoEconomica = new RARRAvaliacaoEconomica;
$obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );

$stAcao = $request->get('stAcao');
//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST["stAcao"] );

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName ( "inNumCGM" );
$obHdnNumCGM->setValue( $_REQUEST['inNumCGM']  );

$obHdnNomCGM = new Hidden;
$obHdnNomCGM->setName ( "stNomCGM" );
$obHdnNomCGM->setValue( $_REQUEST['stNomCGM']  );

$obHdnInscricaoEconomica = new Hidden;
$obHdnInscricaoEconomica->setName ( "inInscricaoEconomica" );
$obHdnInscricaoEconomica->setValue( $_REQUEST['inInscricaoEconomica'] );

$obLblContribuinte = new Label;
$obLblContribuinte->setName  ( "stContribuinte" );
$obLblContribuinte->setRotulo( "Contribuinte"   );
$obLblContribuinte->setValue ( $_REQUEST['inNumCGM']." - ".$_REQUEST['stNomCGM'] );

$obLblInscricao = new Label;
$obLblInscricao->setName  ( "inInscricao" );
$obLblInscricao->setRotulo( "Inscrição Econômica" );
$obLblInscricao->setValue ( $_REQUEST['inInscricaoEconomica'] );

$obTxtFaturamento = new Moeda;
$obTxtFaturamento->setName               ( "flFaturamento"   );
$obTxtFaturamento->setRotulo             ( "Faturamento"     );
$obTxtFaturamento->setTitle              ( "Informe o faturamento da empresa." );
$obTxtFaturamento->setMaxLength          ( 15                );
$obTxtFaturamento->setSize               ( 15                );
$obTxtFaturamento->setValue              ( $_REQUEST["flFaturamento"]    );

$obTxtComplemento = new Moeda;
$obTxtComplemento->setName               ( "flComplemento" );
$obTxtComplemento->setRotulo             ( "Complemento"   );
$obTxtComplemento->setTitle              ( "Informe o valor complementar do faturamento." );
$obTxtComplemento->setMaxLength          ( 15              );
$obTxtComplemento->setSize               ( 15              );
$obTxtComplemento->setValue              ( $_REQUEST["flComplemento"]  );

$obTxtCompetencia = new TextBox;
$obTxtCompetencia->setName               ( "stCompetencia" );
$obTxtCompetencia->setRotulo             ( "Competencia"   );
$obTxtCompetencia->setTitle              ( "Informe a competência do faturamento." );
$obTxtCompetencia->setMaxLength          ( 15              );
$obTxtCompetencia->setSize               ( 15              );
$obTxtCompetencia->setValue              ( $_REQUEST["stCompetencia"]  );

$obDtVencimento = new Data;
$obDtVencimento->setName      ( "dtDataVencimento" );
$obDtVencimento->setTitle     ( "Informe a data de vencimento do faturamento." );
$obDtVencimento->setValue     ( $_REQUEST["dtDataVencimento"]  );
$obDtVencimento->setRotulo    ( "Data de Vencimento" );
$obDtVencimento->setMaxLength ( 20 );
$obDtVencimento->setSize      ( 10 );

$obBscFormulaCalculo = new BuscaInner;
$obBscFormulaCalculo->setRotulo ( "*Fórmula de Cálculo" );
$obBscFormulaCalculo->setTitle  ( "Fórmula que executara o calculo para o transferência de imóvel" );
$obBscFormulaCalculo->setId     ( "stFormula" );
$obBscFormulaCalculo->obCampoCod->setName   ( "inCodigoFormula" );
$obBscFormulaCalculo->obCampoCod->setValue  ( $_REQUEST["inCodigoFormula"]  );
$obBscFormulaCalculo->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncao');");
$obBscFormulaCalculo->setFuncaoBusca( "abrePopUp('../popups/cgm/FLProcurarFuncao.php','frm','inCodigoFormula','stFormula','todos','".Sessao::getId().
"','800','550');" );

$rsAtributos = new RecordSet;

if ( $stAcao == "incluir" && !$obRARRAvaliacaoEconomica->getFaturamento() ) {
    $obRARRAvaliacaoEconomica->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
} else {
    //DEFINICAO DOS ATRIBUTOS
    $arChaveAtributo = array( "inscricao_economica" => $_REQUEST["inInscricaoEconomica"] );

    $obRARRAvaliacaoEconomica->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
    $obRARRAvaliacaoEconomica->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
}

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

$obCheckCarne = new CheckBox;
$obCheckCarne->setName                ( "boCarne"                   );
$obCheckCarne->setValue               ( "1"                         );
$obCheckCarne->setLabel               ( "Emitir Carnê de Cobrança?" );
$obCheckCarne->setTitle               ( "Infome se deve ser emitido carnê de cobrança." );
$obCheckCarne->setNull                ( true                        );
$obCheckCarne->setChecked             ( false                       );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnNumCGM );
$obFormulario->addHidden( $obHdnNomCGM );
$obFormulario->addHidden( $obHdnInscricaoEconomica );
$obFormulario->addTitulo( "Dados para Avaliação de Transferência" );
$obFormulario->addComponente( $obLblContribuinte );
$obFormulario->addComponente( $obLblInscricao );
$obFormulario->addComponente( $obTxtFaturamento );
$obFormulario->addComponente( $obTxtComplemento );
$obFormulario->addComponente( $obTxtCompetencia );
$obFormulario->addComponente( $obDtVencimento );
$obFormulario->addComponente( $obBscFormulaCalculo );
$obMontaAtributos->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obCheckCarne );
$obFormulario->Ok();
$obFormulario->Show();
