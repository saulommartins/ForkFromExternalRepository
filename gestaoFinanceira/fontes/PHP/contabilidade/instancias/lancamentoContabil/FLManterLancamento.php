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
    * Página de Lista de Lancamento
    * Data de Criação   : 17/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: FLManterLancamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterLancamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$rsRecordset = new RecordSet;
$stOrdem = "ORDER BY cod_entidade";
$obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

Sessao::write('filtro', array());
Sessao::write('pg', '');
Sessao::write('pos', '');
Sessao::write('paginando', false);

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

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Define o objeto TEXT
$obTxtCodLoteInicial = new TextBox;
$obTxtCodLoteInicial->setName     ( "inCodLoteInicial" );
$obTxtCodLoteInicial->setValue    ( $inCodLoteInicial );
$obTxtCodLoteInicial->setRotulo   ( "Lote" );
$obTxtCodLoteInicial->setInteiro  ( true );
$obTxtCodLoteInicial->setSize     ( 10 );
$obTxtCodLoteInicial->setMaxLength( 10 );
$obTxtCodLoteInicial->setNull     ( true );
$obTxtCodLoteInicial->setTitle    ( 'Informe um código' );

//Define o objeto TEXT
$obTxtCodLoteFinal = new TextBox;
$obTxtCodLoteFinal->setName     ( "inCodLoteFinal" );
$obTxtCodLoteFinal->setValue    ( $inCodLoteFinal );
$obTxtCodLoteFinal->setRotulo   ( "Lote" );
$obTxtCodLoteFinal->setInteiro  ( true );
$obTxtCodLoteFinal->setSize     ( 10 );
$obTxtCodLoteFinal->setMaxLength( 10 );
$obTxtCodLoteFinal->setNull     ( true );
$obTxtCodLoteFinal->setTitle    ( 'Informe um código' );

// define objeto TextBox para nome do lote
$obTxtNomLote = new TextBox;
$obTxtNomLote->setName   ( "stNomLote" );
$obTxtNomLote->setRotulo ( "Nome do Lote" );
$obTxtNomLote->setTitle ( "Informe o nome do lote" );
$obTxtNomLote->setSize     ( 80 );
$obTxtNomLote->setMaxLength( 80 );

// define objeto Data
$obTxtDtInicio = new Data;
$obTxtDtInicio->setName     ( "stDtInicio" );
$obTxtDtInicio->setValue    ( $stDtInicio  );
$obTxtDtInicio->setRotulo   ( "Período" );
$obTxtDtInicio->setNull     ( true );
$obTxtDtInicio->setTitle    ( 'Informe um período' );

// define objeto Label
$obLblPeriodo = new Label;
$obLblPeriodo->setValue( " até " );

// define objeto Data
$obTxtDtTermino = new Data;
$obTxtDtTermino->setName     ( "stDtTermino" );
$obTxtDtTermino->setValue    ( $stDtTermino  );
$obTxtDtTermino->setRotulo   ( "Período" );
$obTxtDtTermino->setNull     ( true );
$obTxtDtTermino->setTitle    ( 'Informe um período' );

// Define SELECT multiplo para codigo da entidade
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione a(s) entidade(s)" );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidades );
// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda('UC-02.02.04');
$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );

$obFormulario->addTitulo( "Dados para Filtro"        );
$obFormulario->addComponente( $obCmbEntidades );
$obFormulario->agrupaComponentes( array($obTxtCodLoteInicial, $obLblPeriodo, $obTxtCodLoteFinal) );
$obFormulario->addComponente( $obTxtNomLote     );
$obFormulario->agrupaComponentes( array($obTxtDtInicio, $obLblPeriodo, $obTxtDtTermino) );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
