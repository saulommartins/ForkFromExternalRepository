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
    * Página de
    * Data de criação : 29/06/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    * $Id: FLDiarioBanco.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-02.02.24
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );

include_once 'JSDiarioBanco.js';

//sessao->tipoConta = "banco";

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
$rsRecordset = new RecordSet;

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_CONT_INSTANCIAS."relatorio/OCDiarioBanco.php" );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione a(s) entidade(s)." );
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

// define objeto Periodicidade
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio   (  Sessao::getExercicio() );
$obPeriodo->setNull        (false );
$obPeriodo->setValidaExercicio ( true );
$obPeriodo->setValue           ( 4);

// Define Objeto Busca Inner para Conta a Crédito
$obBscContaInicial = new BuscaInner;
$obBscContaInicial->setRotulo ( "Conta Inicial" );
$obBscContaInicial->setTitle ( "Informe a Conta Inicial." );
$obBscContaInicial->setNulL ( true );
$obBscContaInicial->setId ( "stContaInicial" );
$obBscContaInicial->setValue ( $stContaInicial );
$obBscContaInicial->obCampoCod->setName ( "inCodContaInicial" );
$obBscContaInicial->obCampoCod->setSize ( 10 );
$obBscContaInicial->obCampoCod->setMaxLength( 5 );
$obBscContaInicial->obCampoCod->setValue ( $inCodContaInicial );
$obBscContaInicial->obCampoCod->setAlign ("left");
$obBscContaInicial->setValoresBusca(CAM_GF_CONT_POPUPS."planoConta/OCPlanoConta.php?".Sessao::getId(),$obForm->getName(),"banco");
$obBscContaInicial->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaInicial','stContaInicial','banco','".Sessao::getId()."','800','550');");

// Define Objeto Busca Inner para Conta a Crédito
$obBscContaFinal = new BuscaInner;
$obBscContaFinal->setRotulo ( "Conta Final" );
$obBscContaFinal->setTitle ( "Informe a Conta Final." );
$obBscContaFinal->setNulL ( true );
$obBscContaFinal->setId ( "stContaFinal" );
$obBscContaFinal->setValue ( $stContaFinal );
$obBscContaFinal->obCampoCod->setName ( "inCodContaFinal" );
$obBscContaFinal->obCampoCod->setSize ( 10 );
$obBscContaFinal->obCampoCod->setMaxLength( 5 );
$obBscContaFinal->obCampoCod->setValue ( $inCodContaFinal );
$obBscContaFinal->obCampoCod->setAlign ("left");
$obBscContaFinal->setValoresBusca(CAM_GF_CONT_POPUPS."planoConta/OCPlanoConta.php?".Sessao::getId(),$obForm->getName(),"banco");
$obBscContaFinal->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaFinal','stContaFinal','banco','".Sessao::getId()."','800','550');");

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-02.02.24');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnCaminho );
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades      );
$obFormulario->addComponente( $obPeriodo    );
$obFormulario->addComponente( $obBscContaInicial );
$obFormulario->addComponente( $obBscContaFinal );
$obFormulario->OK();
$obFormulario->show();
