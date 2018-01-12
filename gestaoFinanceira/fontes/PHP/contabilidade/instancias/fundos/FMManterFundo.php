<?php

	include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
	include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
	include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoEntidade.class.php';

	//Define o nome dos arquivos PHP
	$stPrograma = "ManterFundo";
	$pgFilt = "FL".$stPrograma.".php";
	$pgList = "LS".$stPrograma.".php";
	$pgForm = "FM".$stPrograma.".php";
	$pgProc = "PR".$stPrograma.".php";
	$pgOcul = "OC".$stPrograma.".php?exercicio=" . Sessao::getExercicio();
	$pgJS   = "JS".$stPrograma.".js";

	//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
	$stAcao = $request->get('stAcao', 'incluir');

	//*****************************************************//
	// Define COMPONENTES DO FORMULARIO
	//*****************************************************//
	//Instancia o formulário
	$obForm = new Form;
	$obForm->setAction( $pgProc );
	$obForm->setTarget( "oculto" );

	//Define o objeto da ação stAcao
	$obHdnAcao = new Hidden;
	$obHdnAcao->setName ( "stAcao" );
	$obHdnAcao->setValue( "" );

	//Define o objeto de controle
	$obHdnCtrl = new Hidden;
	$obHdnCtrl->setName ( "stCtrl" );
	$obHdnCtrl->setValue( "" );


	$rsEntidade = new RecordSet;
	$obREntidade = new ROrcamentoEntidade();
	$obREntidade->listar($rsEntidade);

	$obCmbEntidade = new Select;
	$obCmbEntidade->setRotulo        ( "Entidade" );
	$obCmbEntidade->setName          ( "inCodEntidade" );
	$obCmbEntidade->setStyle         ( "width: 500px");
	$obCmbEntidade->setCampoID       ( "cod_entidade" );
	$obCmbEntidade->setCampoDesc     ( "nom_cgm" );
	$obCmbEntidade->addOption        ( "", "Selecione" );
	$obCmbEntidade->setNull          ( false );
	$obCmbEntidade->preencheCombo    ( $rsEntidade );
	$obCmbEntidade->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."&value='+this.value,'recuperarOrgaosPorEntidade')");

	$obCmbOrgao = new Select;
	$obCmbOrgao->setRotulo        ( "Órgão orçamentário" );
	$obCmbOrgao->setName          ( "inCodOrgao" );
	$obCmbOrgao->setStyle         ( "width: 500px");
	$obCmbOrgao->addOption        ( "", "Selecione" );
	$obCmbOrgao->setNull          ( false );
	$obCmbOrgao->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."&value='+this.value,'recuperarUnidadesPorOrgao')");

	$obCmbUnidade = new Select;
	$obCmbUnidade->setRotulo        ( "Unidade orçamentária" );
	$obCmbUnidade->setName          ( "inCodUnidade" );
	$obCmbUnidade->setStyle         ( "width: 500px");
	$obCmbUnidade->addOption        ( "", "Selecione" );

	// Define Objeto TextBox para Codigo do Fundo
	$obTxtCodFundo = new TextBox;
	$obTxtCodFundo->setName   ( "inCodFundo" );
	$obTxtCodFundo->setId     ( "inCodFundo" );
	$obTxtCodFundo->setRotulo ( "Código do Fundo" );
	$obTxtCodFundo->setTitle  ( "Informe o Nro do Fundo" );
	$obTxtCodFundo->setInteiro( true );
	$obTxtCodFundo->setStyle  ( "width: 150px" );
	$obTxtCodFundo->setNull   ( false );
	$obTxtCodFundo->setMaxLength( 8 );


	// Define Objeto TextBox para Descricao do Fundo
	$obTxtNomFundo = new TextBox;
	$obTxtNomFundo->setName     ( "stDescricaoFundo" );
	$obTxtNomFundo->setId       ( "stDescricaoFundo" );
	$obTxtNomFundo->setRotulo   ( "Descrição do fundo" );
	$obTxtNomFundo->setTitle    ( "Informe o Nome do Fundo" );
	$obTxtNomFundo->setNull     ( false );
	$obTxtNomFundo->setSize     ( 80 );
	$obTxtNomFundo->setMaxLength( 120 );


	// Define Objeto TextBox para o CNPJ do Fundo
	$obTxtCnpjFundo = new TextBox;
	$obTxtCnpjFundo->setName     ( "stCnpjFundo" );
	$obTxtCnpjFundo->setId       ( "stCnpjFundo" );
	$obTxtCnpjFundo->setRotulo   ( "CNPJ do fundo" );
	$obTxtCnpjFundo->setTitle    ( "Informe o CNPJ do Fundo" );
	$obTxtCnpjFundo->setStyle    ( "width: 150px" );
	$obTxtCnpjFundo->setNull     ( true );
	$obTxtCnpjFundo->setMaxLength( 18 );
	$obTxtCnpjFundo->obEvento->setOnKeyUp("mascaraCNPJ(this, event);");

	$obCmbObjTipoContabilidade = new Select;
	$obCmbObjTipoContabilidade->setRotulo   ("Contabilidade centralizada");
	$obCmbObjTipoContabilidade->setName     ("inTipoContabilidade");
	$obCmbObjTipoContabilidade->setId       ( "inTipoContabilidade"    );
	$obCmbObjTipoContabilidade->setValue    ("");
	$obCmbObjTipoContabilidade->setStyle    ("width: 150px"     );
	$obCmbObjTipoContabilidade->setCampoID  ("tipo_contabilidade");
	$obCmbObjTipoContabilidade->setNull     (false);
	$obCmbObjTipoContabilidade->addOption   ("1", "Sim");
	$obCmbObjTipoContabilidade->addOption   ("2", "Não");

	$obCmbObjTipoPlano = new Select;
	$obCmbObjTipoPlano->setRotulo   ("Plano");
	$obCmbObjTipoPlano->setName     ("inPlano");
	$obCmbObjTipoPlano->setId       ( "inPlano" );
	$obCmbObjTipoPlano->setValue    ("");
	$obCmbObjTipoPlano->setStyle    ("width: 150px" );
	$obCmbObjTipoPlano->setCampoID  ("plano");
	$obCmbObjTipoPlano->setNull     (false);
	$obCmbObjTipoPlano->addOption   ("1", "Plano Previdenciário");
	$obCmbObjTipoPlano->addOption   ("2", "Plano Financeiro");

	//****************************************//
	// Monta FORMULARIO
	//****************************************//
	$obFormulario = new Formulario;
	$obFormulario->addForm( $obForm );
	$obFormulario->addTitulo( "Dados para Fundo Municipal" );

	$obFormulario->addHidden( $obHdnCtrl );
	$obFormulario->addHidden( $obHdnAcao );

	$obFormulario->addComponente( $obCmbEntidade );
	$obFormulario->addComponente( $obCmbOrgao );
	$obFormulario->addComponente( $obCmbUnidade );

	$obFormulario->addComponente( $obTxtCodFundo );
	$obFormulario->addComponente( $obTxtNomFundo );
	$obFormulario->addComponente( $obTxtCnpjFundo );

	$obFormulario->addComponente( $obCmbObjTipoContabilidade );
	$obFormulario->addComponente( $obCmbObjTipoPlano );


	$obBtnOk = new Ok;
	$obBtnCancelar = new Button;
	$obBtnCancelar->setValue( "Limpar" );
	$obBtnCancelar->obEvento->setOnClick( "Limpar();" );

	$obFormulario->defineBarra( array($obBtnOk, $obBtnCancelar) );

	$obFormulario->show();

	include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
