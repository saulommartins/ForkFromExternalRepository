<?php

	include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
	include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
	include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeGeral.class.php';

	ini_set("display_errors", 1);
	error_reporting(E_ALL);

	//Define o nome dos arquivos PHP
	$stPrograma = "ProcurarConvenio";
	$pgFilt = "FL".$stPrograma.".php";
	$pgList = "LS".$stPrograma.".php";
	$pgForm = "FM".$stPrograma.".php";
	$pgProc = "PR".$stPrograma.".php";
	$pgOcul = "OC".$stPrograma.".php";
	$pgJS   = "JS".$stPrograma.".js";

	//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
	$stAcao = $request->get('stAcao', 'excluir');

	Sessao::write('link', '');
	Sessao::remove('filtro');

	//DEFINICAO DOS COMPONENTES
	$obHdnAcao = new Hidden;
	$obHdnAcao->setName   ( "stAcao" );
	$obHdnAcao->setValue  ( $stAcao  );

	$obHdnForm = new Hidden;
	$obHdnForm->setName( "nomForm" );
	$obHdnForm->setValue( $request->get('nomForm'));

	$obHdnCampoNum = new Hidden;
	$obHdnCampoNum->setName( "campoNum" );
	$obHdnCampoNum->setValue( $request->get('campoNum'));

	//Define HIDDEN com o o nome do campo texto
	$obHdnCampoNom = new Hidden;
	$obHdnCampoNom->setName( "campoNom" );
	$obHdnCampoNom->setValue( $request->get('campoNom') );

	$obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral;
	$obPeriodicidade               = new Periodicidade;
	$obPeriodicidade->setExercicio ( Sessao::getExercicio());

	$obHdnTipoBusca = new Hidden;
	$obHdnTipoBusca->setName( "stTipoBusca" );

    $arFiltroBuscaConvenio = Sessao::read('arFiltroBuscaConvenio');
    $arFiltroBuscaConvenio = (is_array($arFiltroBuscaConvenio)) ? $arFiltroBuscaConvenio : array();
    //$inCodEntidade = (isset($arFiltroBuscaConvenio['inCodEntidade'])) ? $arFiltroBuscaConvenio['inCodEntidade'] : "";
    $inCodEntidade = $request->get('inCodEntidade');


    $obITextBoxSelectEntidadeGeral->setCodEntidade($inCodEntidade);
    $obITextBoxSelectEntidadeGeral->setLabel(true);
    

    $obTxtExercicio = new TextBox;
    $obTxtExercicio->setName   ('stExercicio');
    $obTxtExercicio->setId     ('stExercicio');
    $obTxtExercicio->setValue  (Sessao::getExercicio());
    $obTxtExercicio->setRotulo ('Exercício');


	$obTxtNumConvenio = new TextBox;
	$obTxtNumConvenio->setRotulo        ( "Número do Convênio"	);
	$obTxtNumConvenio->setTitle         ( "Número do Convênio"	);
	$obTxtNumConvenio->setName          ( "inNumConvenio"		);
	$obTxtNumConvenio->setValue         ( ""					);
	$obTxtNumConvenio->setSize          ( 10					);
	$obTxtNumConvenio->setMaxLength     ( 10					);
	$obTxtNumConvenio->setNull          ( true					);
	$obTxtNumConvenio->setInteiro       ( true					);

	$obDtInicial = new Data;
	$obDtInicial->setName     ( "dtInicial"                      );
	$obDtInicial->setRotulo   ( "Período do Convênio"            );
	$obDtInicial->setTitle    ( 'Informe o período do convênio.' );
	$obDtInicial->setNull     ( true                             );

	$obLabel = new Label;
	$obLabel->setValue( " até " );

	$obDtFinal = new Data;
	$obDtFinal->setName     ( "dtFinal"   );
	$obDtFinal->setRotulo   ( "Período"   );
	$obDtFinal->setTitle    ( ''          );
	$obDtFinal->setNull     ( true        );


	//DEFINICAO DO FORM
	$obForm = new Form;
	$obForm->setAction( $pgList );

	//DEFINICAO DO FORMULARIO
	$obFormulario = new Formulario;
	$obFormulario->addForm       	( $obForm                        );
	$obFormulario->addHidden     	( $obHdnForm                     );
	$obFormulario->addHidden     	( $obHdnCampoNum                 );
	$obFormulario->addHidden     	( $obHdnCampoNom                 );
	$obFormulario->addHidden     	( $obHdnTipoBusca 	             );
	$obFormulario->addTitulo     	( "Dados para filtro"            );
	$obFormulario->addHidden     	( $obHdnAcao                     );
	
    $obFormulario->addComponente 	( $obTxtExercicio	              			 );
	$obFormulario->addComponente 	( $obITextBoxSelectEntidadeGeral 			 );
	$obFormulario->addComponente	( $obTxtNumConvenio							 );
	$obFormulario->agrupaComponentes( array( $obDtInicial,$obLabel, $obDtFinal ) );

	$obFormulario->OK();
	$obFormulario->show();
