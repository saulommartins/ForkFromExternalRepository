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
    * Página de Filtro para Relatório Anexo III A
    * Data de Criação   : 25/07/2014
    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal
    * @ignore
    *   
    * $Id: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioAnexoIIIA.class.php");

$pgOcul = 'OCRelatorioAnexoIIIA.php';
$pgGera = 'OCGeraRelatorioAnexoIIIA.php';

$stAcao      = $request->get('stAcao');
$boTransacao = new Transacao();
$rsContas    = new RecordSet();
$rsContasSelecionadas = new RecordSet;

$obTTCEMGRelatorioAnexoIIIA = new TTCEMGRelatorioAnexoIIIA();
$obTTCEMGRelatorioAnexoIIIA->recuperaContasRecursoDespesa($rsContas,"","",$boTransacao);

$obForm = new Form();
$obForm->setTarget ( 'telaPrincipal' );
$obForm->setAction($pgGera);

$obHdnAcao = new Hidden();
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

// define objeto Periodicidade
$obPeriodo = new Periodicidade();
$obPeriodo->setExercicio      ( Sessao::getExercicio() );
$obPeriodo->setNull           ( false );
$obPeriodo->setValidaExercicio( true );
$obPeriodo->setValue          ( 4);

$obSelectContas = new SelectMultiplo();
$obSelectContas->setTitle( "Selecione as contas para gerar o relatório." );
$obSelectContas->setName ('inCodContas');
$obSelectContas->setRotulo ( "Contas" );
$obSelectContas->setObrigatorioBarra(true);

// lista de contas disponiveis
$obSelectContas->SetNomeLista1  ('inCodContaDisponiveis');
$obSelectContas->setCampoId1    ('cod_conta'            );
$obSelectContas->setCampoDesc1  ('[cod_plano] - [nom_conta]');
$obSelectContas->SetRecord1     ( $rsContas             );

// lista de contas selecionados
$obSelectContas->SetNomeLista2  ('inCodContaSelecionados'   );
$obSelectContas->setCampoId2    ('cod_conta'                );
$obSelectContas->setCampoDesc2  ('[cod_plano] - [nom_conta]');
$obSelectContas->SetRecord2     ( $rsContasSelecionadas     );

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addTitulo( "Dados para o filtro" );
$obFormulario->addComponente($obPeriodo);
$obFormulario->addComponente($obSelectContas);

$obOk  = new Ok;
$obOk->setId ("Ok");

$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "frm.reset();" );

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';