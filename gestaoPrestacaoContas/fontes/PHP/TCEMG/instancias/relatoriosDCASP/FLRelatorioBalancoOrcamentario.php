<?php

    ini_set("display_errors", 1);
    error_reporting(E_ALL);

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioDCASPBalancoOrcamentario.class.php");

    $pgOcul = 'OCRelatorioBalancoOrcamentario.php';
    $pgGera = 'OCGeraRelatorioBalancoOrcamentario.php';

    $stAcao      = $request->get('stAcao');
    $boTransacao = new Transacao();
    // $rsContas    = new RecordSet();
    $rsContasSelecionadas = new RecordSet;

    // $obTTCEMGRelatorioDCASPBalancoOrcamentario = new TTCEMGRelatorioDCASPBalancoOrcamentario();
    // $obTTCEMGRelatorioDCASPBalancoOrcamentario->recuperaContasRecursoDespesa($rsContas,"","",$boTransacao);

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

/*
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
*/
    $obFormulario = new Formulario();
    $obFormulario->addForm($obForm);
    $obFormulario->addHidden( $obHdnAcao );

    $obFormulario->addTitulo( "Dados para o filtro" );
    $obFormulario->addComponente($obPeriodo);
    // $obFormulario->addComponente($obSelectContas);

    $obOk  = new Ok;
    $obOk->setId ("Ok");

    $obLimpar = new Button;
    $obLimpar->setValue( "Limpar" );
    $obLimpar->obEvento->setOnClick( "frm.reset();" );

    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );

    $obFormulario->show();

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';