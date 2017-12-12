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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 12/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    * $Id: FLDemoRecDespExtraOrcamento.php 46609 2012-05-18 13:07:51Z tonismar $

    * Casos de uso: uc-02.02.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );
include_once CAM_GA_ADM_COMPONENTES . 'IMontaAssinaturas.class.php';

$stPrograma = "DemoVariacoesPatrimoniais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::remove('filtroRelatorio');

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
$rsRecordset = new RecordSet;

$obForm = new Form;
$obForm->setAction( CAM_GF_CONT_INSTANCIAS."relatorio/OCGeraRelatorioDemoVariacoesPatrimoniais.php" );
$obForm->setTarget( "telaPrincipal" );

$obHdnValidaData = new HiddenEval;
$obHdnValidaData->setName  ( "hdnValidaData" );
$obHdnValidaData->setValue ( $stValidaData  );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
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

// define objeto Data
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio       (Sessao::getExercicio());
$obPeriodo->setNull            (false);
$obPeriodo->setValidaExercicio (true);
$obPeriodo->setValue           (4);

// define objeto assinatura
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ($obCmbEntidades);

// Define Objeto Radio para opção de exibição da coluna código estrutural
$obRadioEstruturalSim = new Radio();
$obRadioEstruturalSim->setName  ( "stEstrutural" );
$obRadioEstruturalSim->setId    ( "stEstrutural" );
$obRadioEstruturalSim->setLabel ( "Sim" );
$obRadioEstruturalSim->setValue ( "true" );
$obRadioEstruturalSim->setRotulo( "Emitir por estrutural" );

$obRadioEstruturalNao = new Radio();
$obRadioEstruturalNao->setName  ( "stEstrutural" );
$obRadioEstruturalNao->setId    ( "stEstrutural" );
$obRadioEstruturalNao->setLabel ( "Não" );
$obRadioEstruturalNao->setValue ( "false" );
$obRadioEstruturalNao->setChecked(true);

//------------------
// Monta Formulario
//------------------
$obFormulario = new Formulario;
$obFormulario->setAjuda  ('UC-02.02.15');
$obFormulario->addForm   ($obForm);
$obFormulario->addHidden ($obHdnValidaData,true);

$obFormulario->addTitulo ("Dados para Filtro");

$obFormulario->addComponente ($obCmbEntidades);
$obFormulario->addComponente ($obPeriodo);
$obFormulario->agrupaComponentes( array( $obRadioEstruturalSim, $obRadioEstruturalNao) );

$obMontaAssinaturas->geraFormulario ($obFormulario);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>