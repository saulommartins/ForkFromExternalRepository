<?php

    // ini_set("display_errors", 1);
    // error_reporting(E_ALL);

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );
    include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioBalancoFinanceiro.class.php");

    $pgOcul = 'OCRelatorioBalancoFinanceiro.php';
    $pgGera = 'OCGeraRelatorioBalancoFinanceiro.php';

    $stAcao = $request->get('stAcao');
    $boTransacao = new Transacao();

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

    /* ComboBox dos entidades */
    $obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario();

    $obFormulario = new Formulario();
    $obFormulario->addForm($obForm);
    $obFormulario->addHidden( $obHdnAcao );

    $obFormulario->addTitulo( "Dados para o filtro" );
    $obFormulario->addComponente($obISelectMultiploEntidadeUsuario);
    $obFormulario->addComponente($obPeriodo);

    $obOk  = new Ok;
    $obOk->setId ("Ok");

    $obLimpar = new Button;
    $obLimpar->setValue( "Limpar" );
    $obLimpar->obEvento->setOnClick( "frm.reset();" );

    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );

    $obFormulario->show();

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';