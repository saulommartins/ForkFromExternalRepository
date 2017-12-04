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
    * Página de Filtro para Conciliação Bancária
    * Data de Criação   : 06/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    * $Id: FLManterConciliacao.php 63831 2015-10-22 12:51:00Z franver $

    * Casos de uso: uc-02.04.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_NEGOCIO."RTesourariaConciliacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConciliacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

/* Limpa os dados da sessao */
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
Sessao::remove('filtro');
Sessao::remove('filtroAux');

$rsEntidadesDisponiveis  = new recordSet;
$rsEntidadesSelecionadas = new recordSet;

$obRTesourariaConciliacao  = new RTesourariaConciliacao;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

$rsEntidadesDisponiveis = $rsEntidadesSelecionadas = new recordSet;
$stOrdem = " ORDER BY C.nom_cgm";

if ($stAcao == "") {
    $stAcao = "incluir";
}

$obRTesourariaConciliacao->obRContabilidadePlanoBanco->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->obROrcamentoEntidade->listarUsuariosEntidade($rsEntidadesDisponiveis, " ORDER BY cod_entidade");

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ("inCodigoEntidadesSelecionadas");
$obCmbEntidades->setRotulo ( "Entidade" );
$obCmbEntidades->setTitle  ( "Selecione a(s) Entidade(s)." );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidadesDisponiveis->getNumLinhas()==1) {
       $rsEntidadesSelecionadas = $rsEntidadesDisponiveis;
       $rsEntidadesDisponiveis = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodigoEntidadesDisponiveis');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidadesDisponiveis );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodigoEntidadesSelecionadas');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsEntidadesSelecionadas );

$obMes = new Mes;
$obMes->setNull            ( false );
$obMes->setTitle           ( "Informe o mês de movimentação." );
$obMes->setPeriodo         ( true );
$obMes->setExercicio       ( Sessao::getExercicio() );

// Define objeto TextBox para Codigo Plano inicial
$obContaInicial = new TextBox;
$obContaInicial->setName     ( "inCodPlanoInicial" );
$obContaInicial->setRotulo   ( "Conta"          );
$obContaInicial->setTitle    ( "Informe o intervalo de contas." );
$obContaInicial->setInteiro  ( true             );
$obContaInicial->setNull     ( false            );
$obContaInicial->obEvento->setOnBlur( "if(!document.frm.inCodPlanoFinal.value) document.frm.inCodPlanoFinal.value=this.value;" );

// Define Objeto Label
$obLabel = new Label;
$obLabel->setValue( " até " );

// Define objeto TextBox para codigo plano final
$obContaFinal = new TextBox;
$obContaFinal->setName     ( "inCodPlanoFinal"  );
$obContaFinal->setRotulo   ( ""                 );
$obContaFinal->setTitle    ( ''                 );
$obContaFinal->setInteiro  ( true               );
$obContaFinal->setNull     ( false              );

$stMsg = "A data do extrato deve ser no Mês informado!";
// Define objeto Data
$obTxtDtExtrato = new Data;
$obTxtDtExtrato->setName   ( "stDtExtrato"               );
$obTxtDtExtrato->setId     ( "stDtExtrato"               );
$obTxtDtExtrato->setRotulo ( "Data do Extrato"           );
$obTxtDtExtrato->setTitle  ( "Informe a Data do Extrato." );
$obTxtDtExtrato->setNull   ( false                       );
$obTxtDtExtrato->obEvento->setOnChange("if (document.frm.inMes.value!=this.value.substr(3,2)) {this.value='';alertaAviso('".$stMsg."','aviso','','".Sessao::getId()."');}");

// Define objeto Radio para Agrupar
$obRdAgruparT = new Radio();
$obRdAgruparT->setName   ( "boAgrupar" );
$obRdAgruparT->setValue  ( true        );
$obRdAgruparT->setRotulo ( "Agrupar"   );
$obRdAgruparT->setLabel  ( "Sim"       );
$obRdAgruparT->setTitle  ( "Informe se a conciliação deverá ser agrupada." );

$obRdAgruparF = new Radio();
$obRdAgruparF->setName  ( "boAgrupar" );
$obRdAgruparF->setValue ( false       );
$obRdAgruparF->setRotulo( "Agrupar"   );
$obRdAgruparF->setLabel ( "Não"       );
$obRdAgruparF->setChecked( true        );
$obRdAgruparF->setTitle ( "Informe se a conciliação deverá ser agrupada." );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnCtrl );
$obFormulario->addHidden        ( $obHdnAcao );

$obFormulario->addTitulo        ( "Dados para Conciliação Bancária"   );
$obFormulario->addComponente    ( $obCmbEntidades   );
$obFormulario->addComponente    ( $obMes            );
$obFormulario->agrupaComponentes( array( $obContaInicial,$obLabel, $obContaFinal ) );
$obFormulario->addComponente    ( $obTxtDtExtrato   );
$obFormulario->agrupaComponentes( array( $obRdAgruparT, $obRdAgruparF ) );

$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
