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
    * Data de Criação   : 15/02/2005

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Id: FLBalanceteReceita.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.21
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_ORC_NEGOCIO . 'ROrcamentoEntidade.class.php';
include CAM_GF_ORC_NEGOCIO . 'ROrcamentoConfiguracao.class.php';
include CAM_GF_ORC_NEGOCIO . 'ROrcamentoReceita.class.php';
include CAM_GF_ORC_COMPONENTES . 'IIntervaloPopUpEstruturalReceita.class.php';
include CAM_GF_ORC_COMPONENTES . 'IIntervaloPopUpReceita.class.php';
include CAM_GF_ORC_COMPONENTES . 'IMontaRecursoDestinacao.class.php';
include CAM_GA_ADM_COMPONENTES . 'IMontaAssinaturas.class.php';

include 'JSBalanceteReceita.js';

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
$rsRecordset = new RecordSet;

$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->consultarConfiguracao();

$obROrcamentoReceita = new ROrcamentoReceita;
$obROrcamentoReceita->obROrcamentoClassificacaoReceita->setExercicio(Sessao::getExercicio());
$mascClassificacao = $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaMascara();

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_ORC_INSTANCIAS."relatorio/OCBalanceteReceita.php" );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValidaExercicio( true              );
$obPeriodicidade->setNull           ( false             );
$obPeriodicidade->setValue          ( 4                 );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades para filtro." );
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

$obIntervaloReceita = new IIntervaloPopUpReceita();
$obIntervaloReceita->setRotulo('Código Reduzido');

$obIntervaloEstrutural = new IIntervaloPopUpEstruturalReceita();
$obIntervaloEstrutural->setRotulo('Código Estrutural');

$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

$obSimNaoResumoRecurso = new SimNao();
$obSimNaoResumoRecurso->setRotulo("Totalizar por Recurso");
$obSimNaoResumoRecurso->setTitle("Demonstrar o resumo por recurso.");
$obSimNaoResumoRecurso->setName('radResumoRecurso');
$obSimNaoResumoRecurso->setId('radResumoRecurso');
$obSimNaoResumoRecurso->setNull(true);
$obSimNaoResumoRecurso->setChecked('Não');

if (isset($js)) {
       SistemaLegado::executaFramePrincipal($js);
}

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);

$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-02.01.21');
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnCaminho);
$obFormulario->addTitulo("Dados para Filtro");
$obFormulario->addComponente($obCmbEntidades);
$obFormulario->addComponente($obPeriodicidade);
$obFormulario->addComponente($obIntervaloReceita);
$obFormulario->addComponente($obIntervaloEstrutural);
$obIMontaRecursoDestinacao->geraFormulario($obFormulario);
$obFormulario->addComponente($obSimNaoResumoRecurso);

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario($obFormulario);

$obFormulario->OK();
$obFormulario->show();

?>
