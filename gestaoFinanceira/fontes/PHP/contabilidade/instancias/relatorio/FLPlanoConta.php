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
    * Página de Filtro para relatorico de Contas
    * Data de Criação   : 25/11/2004

    * @author Analista Jorge B. Ribarr
    * @author Desenvolvedor Anderson R. M. Buzo

    * @ignore

    * $Id: FLPlanoConta.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php"          );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeSistemaContabil.class.php"     );

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
$obRContabilidadeSistemaContabil     = new RContabilidadeSistemaContabil;

$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
$rsRecordset = new RecordSet;
$obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->listarGrupos( $rsCodGrupo );
$obRContabilidadeSistemaContabil->setExercicio( Sessao::getExercicio() );
$obRContabilidadeSistemaContabil->listarSistemaContaAnalitica( $rsSistema );

$obRContabilidadePlanoBanco->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$stOrdem = "ORDER BY cod_entidade";
$obRContabilidadePlanoBanco->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

$obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );

Sessao::write('rsPlanoConta', "");
//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_CONT_INSTANCIAS."relatorio/OCPlanoConta.php" );

//Define o objeto SelectMultiplo para armazenar os Grupos
$obCmbGrupo = new SelectMultiplo();
$obCmbGrupo->setName   ('inCodGrupo');
$obCmbGrupo->setRotulo ( "Grupos" );
$obCmbGrupo->setTitle  ( "" );
$obCmbGrupo->setNull   ( false );
// lista de atributos disponiveis
$obCmbGrupo->SetNomeLista1 ('inCodGrupoDisponivel');
$obCmbGrupo->setCampoId1   ( 'cod_grupo' );
$obCmbGrupo->setCampoDesc1 ( '[cod_grupo] - [nom_conta]' );
$obCmbGrupo->SetRecord1    ( $rsCodGrupo );
// lista de atributos selecionados
$obCmbGrupo->SetNomeLista2 ('inCodGrupo');
$obCmbGrupo->setCampoId2   ('cod_entidade');
$obCmbGrupo->setCampoDesc2 ('nom_cgm');
$obCmbGrupo->SetRecord2    ( $rsRecordset );

//Define o objeto SelectMultiplo para armazenar os Grupos
$obCmbSistema = new SelectMultiplo();
$obCmbSistema->setName   ('inCodSistema');
$obCmbSistema->setRotulo ( "Sistemas" );
$obCmbSistema->setTitle  ( "" );
$obCmbSistema->setNull   ( true  );
// lista de atributos disponiveis
$obCmbSistema->SetNomeLista1 ('inCodSistemaDisponivel');
$obCmbSistema->setCampoId1   ( 'cod_sistema' );
$obCmbSistema->setCampoDesc1 ( '[cod_sistema] - [nom_sistema]' );
$obCmbSistema->SetRecord1    ( $rsSistema );
// lista de atributos selecionados
$obCmbSistema->SetNomeLista2 ('inCodSistema');
$obCmbSistema->setCampoId2   ('cod_sistema');
$obCmbSistema->setCampoDesc2 ('nom_cgm');
$obCmbSistema->SetRecord2    ( $rsRecordset );

// Define SELECT multiplo para codigo da entidade
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "" );
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

// Define Objeto Textbox para Codigo de Classificação inicial
$obTxtCodEstruturalInicial = new TextBox;
$obTxtCodEstruturalInicial->setName      ( "stCodEstruturalInicial"   );
$obTxtCodEstruturalInicial->setId        ( "stCodEstruturalInicial"   );
$obTxtCodEstruturalInicial->setRotulo    ( "Código Estrutural" );
$obTxtCodEstruturalInicial->setTitle     ( "Informe o código de classificação da conta" );
$obTxtCodEstruturalInicial->setMascara   ( $stMascara );
$obTxtCodEstruturalInicial->setPreencheComZeros ( true );

// Define Objeto Label
$obLblCodEstrutural = new Label;
$obLblCodEstrutural->setValue(" até  ");

// Define Objeto Textbox para Codigo de Classificação final
$obTxtCodEstruturalFinal = new TextBox;
$obTxtCodEstruturalFinal->setName      ( "stCodEstruturalFinal"   );
$obTxtCodEstruturalFinal->setId        ( "stCodEstruturalFinal"   );
$obTxtCodEstruturalFinal->setRotulo    ( "Código Estrutural" );
$obTxtCodEstruturalFinal->setTitle     ( "Informe o código de classificação da conta" );
$obTxtCodEstruturalFinal->setMascara   ( $stMascara );
$obTxtCodEstruturalFinal->setPreencheComZeros ( true );

// define objeto TextBox para codigo reduzido inicial
$obTxtCodPlanoInicial = new TextBox;
$obTxtCodPlanoInicial->setName   ( "inCodPlanoInicial" );
$obTxtCodPlanoInicial->setRotulo ( "Código Reduzido" );

// Define Objeto Label
$obLblCodPlano = new Label;
$obLblCodPlano->setValue(" até  ");

// define objeto TextBox para codigo reduzido final
$obTxtCodPlanoFinal = new TextBox;
$obTxtCodPlanoFinal->setName   ( "inCodPlanoFinal" );
$obTxtCodPlanoFinal->setRotulo ( "Código Reduzido" );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda('UC-02.02.19');
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addTitulo( "Dados para Filtro"        );
$obFormulario->addComponente( $obCmbGrupo            );
$obFormulario->addComponente( $obCmbSistema          );
$obFormulario->addComponente( $obCmbEntidades     );
$obFormulario->agrupaComponentes( array( $obTxtCodEstruturalInicial, $obLblCodEstrutural ,$obTxtCodEstruturalFinal) );
$obFormulario->agrupaComponentes( array( $obTxtCodPlanoInicial, $obLblCodPlano ,$obTxtCodPlanoFinal ) );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
