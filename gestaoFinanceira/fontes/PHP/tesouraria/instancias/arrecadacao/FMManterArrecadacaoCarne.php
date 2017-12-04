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
    * Página de Formulário para Arrecadação
    * Data de Criação   : 20/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 25507 $
    $Name$
    $Autor:$
    $Date: 2007-09-17 12:33:10 -0300 (Seg, 17 Set 2007) $

    * Casos de uso: uc-02.04.34

*/

/*
$Log$
Revision 1.14  2007/09/17 15:33:10  luciano
Ticket#9791#

Revision 1.13  2007/07/31 13:59:15  domluc
*** empty log message ***

Revision 1.12  2007/07/30 18:00:10  domluc
Descomentado include do Applet de verificação de terminal

Revision 1.11  2007/07/27 00:18:31  domluc
Adicionadas verificações:
  Se carne ja foi pago
  Se carne tem creditos sem receitas/plano vinculado

Revision 1.10  2007/07/25 16:14:18  domluc
Atualizado Arr por Carne

Revision 1.9  2006/07/05 20:38:50  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_IAPPLETTERMINAL );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"                                       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoCarne";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.Sessao::getExercicio() ) );
$obRTesourariaBoletim->addArrecadacao();
$obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$arFiltro = Sessao::read('filtro');

if ( !count( $arFiltro ) > 0 ) {
    Sessao::write('filtro', $_REQUEST);
} else {
    $_REQUEST = $arFiltro;
}

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodBoletim = new Hidden();
$obHdnCodBoletim->setName( 'inCodBoletim' );
$obHdnCodBoletim->setValue( $inCodBoletim );

$obHdnDtBoletim = new Hidden();
$obHdnDtBoletim->setName( 'stDtBoletim' );
$obHdnDtBoletim->setValue( $stDtBoletim );

$obHdnVlTotal = new Hidden;
$obHdnVlTotal->setName( "nuVlTotal" );
$obHdnVlTotal->setId( "nuVlTotal" );
$obHdnVlTotal->setValue( "" );

$obHdnVlTotalLista = new Hidden;
$obHdnVlTotalLista->setName( "hdnVlTotalLista" );
$obHdnVlTotalLista->setId( "hdnVlTotalLista" );
$obHdnVlTotalLista->setValue( "" );

//$stEval  = " var nuVlTotal = document.frm.nuVlTotalLista.value;                  \n";
//$stEval .= " var nuVlRecebido = document.frm.nuVlRecebido.value;                 \n";
//$stEval .= " nuVlRecebido = nuVlRecebido.replace( new RegExp( '[.]','g' ), '' ); \n";
//$stEval .= " nuVlRecebido = nuVlRecebido.replace( ',', '.' );                    \n";
//$stEval .= " if ( parseFloat( nuVlTotal ) > parseFloat( nuVlRecebido ) ) {        \n";
//$stEval .= "    erro = true;                                                     \n";
//$stEval .= "    mensagem = 'Valor recebido é menor que o valor devido!';         \n";
//$stEval .= " }                                                                   \n";

//$obHdnEval = new HiddenEval();
//$obHdnEval->setName( "stEval" );
//$obHdnEval->setValue( $stEval );

$obApplet = new IAppletTerminal( $obForm );

// Define Objeto Select para Entidade
$obCmbEntidade = new Select();
$obCmbEntidade->setRotulo    ( "Entidade"                 );
$obCmbEntidade->setName      ( "inCodEntidade"            );
$obCmbEntidade->setId        ( "inCodEntidade"            );
$obCmbEntidade->setTitle     ( "Selecione a Entidade."     );
$obCmbEntidade->setCampoId   ( "cod_entidade"             );
$obCmbEntidade->setCampoDesc ( "nom_cgm"                  );
$obCmbEntidade->setNull      ( false                      );
if ( $rsEntidade->getNumLinhas() > 1 ) {
        $obCmbEntidade->addOption            ( "", "Selecione"      );
        $obCmbEntidade->obEvento->setOnChange("montaParametrosGET('buscaBoletim','inCodEntidade');");
} else $jsSL = "montaParametrosGET('buscaBoletim','inCodEntidade');";
$obCmbEntidade->preencheCombo    ( $rsEntidade            );

$obSpanBoletim = new Span;
$obSpanBoletim->setId ( 'spnBoletim' );

// Define Objeto BuscaInner para conta
$obBscConta = new BuscaInner;
$obBscConta->setRotulo ( "*Conta"       );
$obBscConta->setTitle  ( "Informe a Conta Banco que Receberá o Valor Arrecadado." );
$obBscConta->setId     ( "stNomConta"  );
$obBscConta->setValue  ( $stNomConta   );
$obBscConta->setNull   ( true          );
$obBscConta->obCampoCod->setName     ( "inCodPlano" );
$obBscConta->obCampoCod->setSize     ( 10           );
$obBscConta->obCampoCod->setNull     ( true         );
$obBscConta->obCampoCod->setMaxLength( 8            );
$obBscConta->obCampoCod->setValue    ( $inCodPlano  );
$obBscConta->obCampoCod->setAlign    ( "left"       );
$obBscConta->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodPlano','stNomConta','tes_arrec&inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");
$obBscConta->setValoresBusca(CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(),$obForm->getName(),'tes_arrec');

// Define Objeto TextBox para número do carnê
$obTxtCarne = new TextBox();
$obTxtCarne->setRotulo   ( "Carnê"     );
$obTxtCarne->setName     ( "stCarne"   );
$obTxtCarne->setTitle    ( "Digite a Númeração do Carnê ou Passe o Leitor de Código de Barras." );
$obTxtCarne->setValue    ( $stCarne    );
$obTxtCarne->setNull     ( true        );
$obTxtCarne->setMaxLength( 17          );
$obTxtCarne->setSize     ( 20          );
$obTxtCarne->obEvento->setOnChange( "montaParametrosGET( 'montaDados','stCarne,nuVlRecebido');" );

// Define Objeto Span para dados adicionais
$obSpnDados = new Span;
$obSpnDados->setId( "spnDados" );

// Define Objeto TextArea para observações
$obTxtObs = new TextArea;
$obTxtObs->setName   ( "stObservacoes" );
$obTxtObs->setId     ( "stObservacoes" );
$obTxtObs->setValue  ( $stObservacoes  );
$obTxtObs->setRotulo ( "Observações"   );
$obTxtObs->setTitle  ( "Digite a Observação Relativa à este Recebimento." );
$obTxtObs->setNull   ( true            );
$obTxtObs->setRows   ( 2               );
$obTxtObs->setCols   ( 100             );

// Define Objeto Button para  Incluir Item
$obBtnIncluir = new Button;
$obBtnIncluir->setValue( "Incluir Item" );
$obBtnIncluir->obEvento->setOnClick( "incluirItem();" );

// Define Objeto Button para Limpar
$obBtnLimpar = new Button;
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick( "limparItem();" );

// Define Objeto Span Para lista de itens
$obSpan = new Span;
$obSpan->setId( "spnLista" );

// Define Objeto Label para Valor Geral
$obLblVlTotalLista = new Label();
$obLblVlTotalLista->setRotulo( "Total Geral" );
$obLblVlTotalLista->setId    ( "nuVlTotalLista" );

// Define Objeto Numeric para valor recebido
$obTxtVlRecebido = new Numerico;
$obTxtVlRecebido->setName     ( "nuVlRecebido"   );
$obTxtVlRecebido->setId       ( "nuVlRecebido"   );
$obTxtVlRecebido->setValue    ( $nuVlRecebido    );
$obTxtVlRecebido->setRotulo   ( "Valor Recebido" );
$obTxtVlRecebido->setTitle    ( "Informe o Valor Recebido." );
$obTxtVlRecebido->setTitle    ( ""               );
$obTxtVlRecebido->setDecimais ( 2                );
$obTxtVlRecebido->setNegativo ( false            );
$obTxtVlRecebido->setNull     ( false            );
$obTxtVlRecebido->setSize     ( 23               );
$obTxtVlRecebido->setMaxLength( 23               );
$obTxtVlRecebido->setMinValue ( 1                );
$obTxtVlRecebido->obEvento->setOnChange( "montaParametrosGET( 'recalculaTroco','hdnVlTotalLista,nuVlRecebido');" );
$obTxtVlRecebido->obEvento->setOnBlur( "montaParametrosGET( 'recalculaTroco','hdnVlTotalLista,nuVlRecebido');" );

// Define Objeto Label para troco
$obLblVlTroco = new Label;
$obLblVlTroco->setRotulo  ( "Troco"     );
$obLblVlTroco->setId      ( "stVlTroco" );
$obLblVlTroco->setValue   ( $stVlTroco  );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm             );
$obFormulario->addHidden     ( $obHdnAcao          );
$obFormulario->addHidden     ( $obHdnCtrl          );
$obFormulario->addHidden     ( $obHdnCodBoletim    );
$obFormulario->addHidden     ( $obHdnDtBoletim     );
$obFormulario->addHidden     ( $obHdnVlTotal       );
$obFormulario->addHidden     ( $obHdnVlTotalLista  );
$obFormulario->addHidden     ( $obApplet           );
//$obFormulario->addHidden     ( $obHdnEval, true    );
$obFormulario->addComponente ( $obCmbEntidade      );
$obFormulario->addSpan       ( $obSpanBoletim      );
$obFormulario->addComponente ( $obBscConta         );
$obFormulario->addComponente ( $obTxtCarne         );
$obFormulario->addSpan       ( $obSpnDados         );
$obFormulario->addComponente ( $obTxtObs           );
$obFormulario->agrupaComponentes ( array( $obBtnIncluir, $obBtnLimpar ) );
$obFormulario->addSpan       ( $obSpan             );
$obFormulario->addComponente ( $obLblVlTotalLista  );
$obFormulario->addComponente ( $obTxtVlRecebido    );
$obFormulario->addComponente ( $obLblVlTroco       );

$obFormulario->Ok();

$obFormulario->show();
if ($jsSL) SistemaLegado::executaFrameOculto($jsSL);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
