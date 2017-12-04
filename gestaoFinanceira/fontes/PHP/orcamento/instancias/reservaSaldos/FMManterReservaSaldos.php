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
    * Formulario para Orçamento - Reserva de Saldos
    * Data de Criação   : 03/05/2005

    * @author Analista: Diego Barbosa Victória
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Id: FMManterReservaSaldos.php 64392 2016-02-10 16:06:09Z michel $

    * Casos de uso: uc-02.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_INCLUDE."validaGF.inc.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoReservaSaldos.class.php";

//Define o nome dos arquivos PHP
$stPrograma      = "ManterReservaSaldos";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao', 'incluir');

// DEFINE OBJETOS DAS CLASSES
$obROrcamentoReservaSaldos = new ROrcamentoReservaSaldos;
$obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoEntidade->setExercicio( Sessao::getExercicio()    );
$obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm')  );
$obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

if (date('Y') > Sessao::getExercicio() && Sessao::read('data_reserva_saldo_GF')) {
    $arDataReserva = explode('-', Sessao::read('data_reserva_saldo_GF'));
    $dtDataReserva = $arDataReserva[2].'/'.$arDataReserva[1].'/'.$arDataReserva[0];
} else {
    $dtDataReserva = date('d/m')."/".Sessao::getExercicio();
}
$dtDataValidade = "31/12/".Sessao::getExercicio();

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $request->get('stCtrl') );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $request->get('stAcao') );

//Define o Hidden para armazenar o cod despesa antes de abrir a popup para alterar o mesmo
$obHdnCodDespesa = new Hidden;
$obHdnCodDespesa->setName   ( "inCodDespesaAnterior" );
$obHdnCodDespesa->setId     ( "inCodDespesaAnterior" );
$obHdnCodDespesa->setValue  ( "" );

// DEFINE OBJETOS DO FORMULARIO - INCLUIR

$obTxtCodigoEntidade = new TextBox;
$obTxtCodigoEntidade->setName        ( "inCodigoEntidade"             );
$obTxtCodigoEntidade->setId          ( "inCodigoEntidade"             );
if ($rsEntidade->getNumLinhas()==1)
    $obTxtCodigoEntidade->setValue   ( $rsEntidade->getCampo("cod_entidade") );
else
    $obTxtCodigoEntidade->setValue   ( $inCodigoEntidade              );
$obTxtCodigoEntidade->setRotulo      ( "Entidade"                     );
$obTxtCodigoEntidade->setTitle       ( "Selecione a entidade."        );
$obTxtCodigoEntidade->obEvento->setOnChange( "limparCampos();"        );
$obTxtCodigoEntidade->setInteiro     ( true                           );
$obTxtCodigoEntidade->setNull        ( false                          );

// Define Objeto Select para Nome da Entidade
$obCmbNomeEntidade = new Select;
$obCmbNomeEntidade->setName          ( "stNomeEntidade"               );
$obCmbNomeEntidade->setId            ( "stNomeEntidade"               );
$obCmbNomeEntidade->setValue         ( $inCodigoEntidade              );
// Caso o usuário tenha permissão para mais de uma entidade, exibe o selecionar.
// Se tiver apenas uma, evita o addOption forçando a primeira e única opção ser selecionada.
if ($rsEntidade->getNumLinhas()>1) {
    $obCmbNomeEntidade->addOption        ( "", "Selecione"                );
    $obCmbNomeEntidade->obEvento->setOnChange( "limparCampos();"          );
}
$obCmbNomeEntidade->setCampoId       ( "cod_entidade"                 );
$obCmbNomeEntidade->setCampoDesc     ( "nom_cgm"                      );
$obCmbNomeEntidade->setStyle         ( "width: 520"                   );
$obCmbNomeEntidade->preencheCombo    ( $rsEntidade                    );
$obCmbNomeEntidade->setNull          ( false                          );

$obDtDataReserva = new Data;
$obDtDataReserva->setName          ( "dtDataReserva"    );
$obDtDataReserva->setRotulo        ( "Data da Reserva"  );
$obDtDataReserva->setTitle         ( "Informe a data da reserva." );
$obDtDataReserva->setValue         ( $dtDataReserva     );
$obDtDataReserva->setMaxLength     ( 20                 );
$obDtDataReserva->setSize          ( 10                 );
$obDtDataReserva->setNull          ( false              );
$obDtDataReserva->obEvento->setOnChange("buscaDado('buscaDespesa');");

// Define Objeto BuscaInner para Despesa
$obBscDespesa = new BuscaInner;
$obBscDespesa->setRotulo ( "Dotação Orçamentária"   );
$obBscDespesa->setTitle  ( "Informe a dotação orçamentária." );
$obBscDespesa->setNulL   ( false                    );
$obBscDespesa->setId     ( "stNomDespesa"           );
$obBscDespesa->setValue  ( $stNomDespesa            );
$obBscDespesa->obCampoCod->setName ( "inCodDespesa" );
$obBscDespesa->obCampoCod->setSize ( 10 );
$obBscDespesa->obCampoCod->setMaxLength( 5 );
$obBscDespesa->obCampoCod->setValue ( $inCodDespesa );
$obBscDespesa->obCampoCod->setAlign ("left");
$obBscDespesa->obCampoCod->obEvento->setOnBlur("if (document.frm.inCodigoEntidade.value!='') {if (this.value!=document.frm.inCodDespesaAnterior.value) {document.frm.inCodDespesaAnterior.value=this.value;BloqueiaFrames(true,false);buscaDado('buscaDespesa');}}else this.value='';");
$obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/FLDespesa2.php','frm','inCodDespesa','stNomDespesa','autorizacaoEmpenho&inCodEntidade='+document.frm.inCodigoEntidade.value,'".Sessao::getId()."','800','550');");

// Define Objeto Span Para saldo anterior
$obSpanSaldo = new Span;
$obSpanSaldo->setId( "spnSaldoDotacao" );

$obDtDataValidade = new Data;
$obDtDataValidade->setName          ( "dtDataValidade"              );
$obDtDataValidade->setRotulo        ( "Data da Validade"            );
$obDtDataValidade->setTitle         ( "Informe a data de validade." );
$obDtDataValidade->setValue         ( $dtDataValidade               );
$obDtDataValidade->setMaxLength     ( 20                            );
$obDtDataValidade->setSize          ( 10                            );
$obDtDataValidade->setNull          ( false                         );

$obTxtValor = new Moeda;
$obTxtValor->setName             ( "flValor"                     );
$obTxtValor->setId               ( "flValor"                     );
$obTxtValor->setRotulo           ( "Valor"                       );
$obTxtValor->setTitle            ( "Informe o valor."            );
$obTxtValor->setValue            ( $request->get('flValorPagar') );
$obTxtValor->setSize             ( 12                            );
$obTxtValor->setMaxLength        ( 18                            );
$obTxtValor->setFloat            ( true                          );
$obTxtValor->setDecimais         ( 2                             );
$obTxtValor->setNull             ( false                         );

$obTxtMotivo = new TextArea;
$obTxtMotivo->setName             ( "stMotivo"          );
$obTxtMotivo->setRotulo           ( "Motivo"            );
$obTxtMotivo->setTitle            ( "Informe o motivo." );
$obTxtMotivo->setValue            ( $stMotivo           );
$obTxtMotivo->setMaxCaracteres    ( 160                 );
$obTxtMotivo->setNull             ( true                );

$obOk = new Ok;
$obOk->obEvento->setOnClick( "Salvar();if ( validaValor() ) {BloqueiaFrames(true,false);}" );
$obLimpar = new Limpar();
$obLimpar->obEvento->setOnClick( "limparTodos();" );

if ($request->get('stAcao') == "incluir") {
    $js .= "document.frm.inCodigoEntidade.focus();";
    SistemaLegado::executaFramePrincipal($js);
}

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm              );
$obFormulario->addHidden     ( $obHdnCtrl           );
$obFormulario->addHidden     ( $obHdnAcao           );
$obFormulario->addHidden     ( $obHdnCodDespesa     );
$obFormulario->setAjuda ( 'UC-02.01.08' );

if ($request->get('stAcao') == "incluir") {
    $obFormulario->addTitulo     ( "Dados para Reserva de Saldos" );
    $obFormulario->addComponenteComposto( $obTxtCodigoEntidade , $obCmbNomeEntidade );
    $obFormulario->addComponente ( $obDtDataReserva       );
    $obFormulario->addComponente ( $obBscDespesa          );
    $obFormulario->addSpan       ( $obSpanSaldo           );
    $obFormulario->addComponente ( $obDtDataValidade      );
    $obFormulario->addComponente ( $obTxtValor            );
    $obFormulario->addComponente ( $obTxtMotivo           );
    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );
}
$obFormulario->show();
include_once( $pgJs );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
