<?php
/**
* Página de filtros para montar relatório de CGM
* Data de Criação: 04/07/2013

* Copyright CNM - Confederação Nacional de Municípios

* @author Analista      : Eduardo Schitz
* @author Desenvolvedor : Franver Sarmento de MOraes

$Id: $

* @package URBEM
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_FW_LEGADO."funcoesLegado.lib.php");
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php");
include_once(CAM_GA_CSE_MAPEAMENTO."TPais.class.php");
include_once(CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioCgm";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

// Definição do form
$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setEncType('multipart/form-data');

$stValorComposto = $request->get('stValorComposto');

$obHdnValorComposto = new Hidden;
$obHdnValorComposto->setName ( "stValorComposto" );
$obHdnValorComposto->setValue( $stValorComposto  );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue($request->get('stCtrl'));

// Campo qu epossui os três tipos de CGM
$obCheTipoFisica = new CheckBox();
$obCheTipoFisica->setId	   ('inTipoFis');
$obCheTipoFisica->setName  ('inTipoFis');
$obCheTipoFisica->setValue (1);
$obCheTipoFisica->setRotulo('Pessoa Física');

$obCheTipoJuridica = new CheckBox();
$obCheTipoJuridica->setId     ('inTipoJur');
$obCheTipoJuridica->setName   ('inTipoJur');
$obCheTipoJuridica->setValue  (2);
$obCheTipoJuridica->setRotulo ('Pessoa Jurídica');

$obCheTipoCgmInterno = new CheckBox();
$obCheTipoCgmInterno->setId	('inTipoInt');
$obCheTipoCgmInterno->setName	('inTipoInt');
$obCheTipoCgmInterno->setValue	(4);
$obCheTipoCgmInterno->setRotulo ('CGM Interno');

// Campo de Data Inicio
$obDatPeriodoInicio = new Data();
$obDatPeriodoInicio->setId	 ('dtDataIni');
$obDatPeriodoInicio->setName	 ('dtDataIni');
$obDatPeriodoInicio->setRotulo	 ('Periodo');
$obDatPeriodoInicio->setNullBarra(false);

// Separador das datas
$obLblData = new Label();
$obLblData->setValue("&nbsp;à&nbsp;");

// Campo de Data Final
$obDatPeriodoFinal = new Data();
$obDatPeriodoFinal->setId	('dtDataFim');
$obDatPeriodoFinal->setName	('dtDataFim');
$obDatPeriodoFinal->setRotulo	('Periodo');
$obDatPeriodoFinal->setNullBarra(false);

// Agrupa os compodentes de data num array()
$arDataPeriodo = array($obDatPeriodoInicio, $obLblData, $obDatPeriodoFinal);

// Lista todos os paises que possuem CGM cadastrada
$obTPais = new TPais();
$obTPais->mostraTodosPaisesCgm($rsPais);

// Campo de país
$obSlPais = new Select();
$obSlPais->setId		('inCodPais');
$obSlPais->setName		('inCodPais');
$obSlPais->setRotulo		('País');
$obSlPais->setTitle		('Selecione o País que deseja usar para o relátorio.');
$obSlPais->addOption		('','Selecione um País');
$obSlPais->setCampoId		('cod_pais');
$obSlPais->setCampoDesc		('nom_pais');
$obSlPais->preencheCombo	($rsPais);
$obSlPais->setValue		($request->get('inCodPais'));
$obSlPais->obEvento->setOnChange("preencheUf('')");

// Campo de estado
$obSlEstado = new Select();
$obSlEstado->setId		   ('inCodEstadoUf');
$obSlEstado->setName		   ('inCodEstadoUf');
$obSlEstado->setRotulo		   ('Estado/UF');
$obSlEstado->setTitle		   ('Selecione o estado que deseja usar para o relátorio.');
$obSlEstado->addOption		   ('','Selecione');
$obSlEstado->setValue		   ($request->get('inCodEstadoUf'));
$obSlEstado->obEvento->setOnChange ( "preencheMunicipio('')" );

// Campo de Cidade
$obSlCidade = new Select();
$obSlCidade->setId	('inCodMunicipio');
$obSlCidade->setName	('inCodMunicipio');
$obSlCidade->setRotulo	('Município');
$obSlCidade->setTitle	('Selecione a cidade que deseja usar para o relátorio.');
$obSlCidade->addOption	('','Selecione');
$obSlCidade->setValue	($request->get('inCodMunicipio'));

// Lista de atividades das CGM's
$obBscAtividade = new BuscaInner;
$obBscAtividade->setRotulo              ( "Atividade"                     );
$obBscAtividade->setTitle               ( "Atividade Econômica"           );
$obBscAtividade->setId                  ( "campoInner"                    );
$obBscAtividade->obCampoCod->setName    ( "inCodigoAtividade"             );
$obBscAtividade->obCampoCod->setInteiro ( false                           );
$obBscAtividade->obCampoCod->obEvento->setOnChange ( "montaParametrosGET( 'preencheInnerAtividade', 'inCodigoAtividade' );"          );
$obBscAtividade->obCampoCod->obEvento->setOnBlur   ( "montaParametrosGET( 'buscaAtividade', 'inCodigoAtividade, stValorComposto' );" );
$stBusca  = "abrePopUp('".CAM_GT_CEM_POPUPS."atividadeeconomica/FLProcurarAtividade.php','frm','stValorComposto','campoInner',''";
$stBusca .= " ,'".Sessao::getId()."&campoNom=campoInner&campoNum=stValorComposto&campoFoco=inCodigoAtividade','800','550')";
$obBscAtividade->setFuncaoBusca        ( $stBusca                         );


// Ordem da lista
$obSlOrderBy = new Select();
$obSlOrderBy->setId	('stOrderBy');
$obSlOrderBy->setName	('stOrderBy');
$obSlOrderBy->setRotulo	('Ordenar por');
$obSlOrderBy->setTitle	('Selecione o campo que deseja usar para ordernar a lista.');
$obSlOrderBy->addOption	('numcgm','CGM');
$obSlOrderBy->addOption	('nom_cgm','Nome');

// Mostrar endereço.
$obRadEnderecoSim = new Radio();
$obRadEnderecoSim->setId	('stEndereco');
$obRadEnderecoSim->setName	('stEndereco');
$obRadEnderecoSim->setValue	('S');
$obRadEnderecoSim->setRotulo	('Mostrar Endereço no Relatório');
$obRadEnderecoSim->setLabel	('Sim');

$obRadEnderecoNao = new Radio();
$obRadEnderecoNao->setId	('stEndereco');
$obRadEnderecoNao->setName	('stEndereco');
$obRadEnderecoNao->setValue	('N');
$obRadEnderecoNao->setRotulo	('Mostrar Endereço no Relatório');
$obRadEnderecoNao->setLabel	('Não');
$obRadEnderecoNao->setChecked	(true);

// Agrupa os Radios num array()
$arRadEndereco = array($obRadEnderecoSim, $obRadEnderecoNao);


// Define botoes de ação.
// Botao de OK
$obBtnOK = new Ok(true);
// Botao de Limpar
$obBtnLimpar = new Button;
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->obEvento->setOnClick('Limpar(true);');

$arBotoes = array($obBtnOK, $obBtnLimpar);

// Definição do Formulário
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);

$obFormulario->addHidden                    ($obHdnCtrl			);
$obFormulario->addHidden                    ($obHdnValorComposto	);
$obFormulario->addTitulo                    ("Dados para filtro"        );
$obFormulario->addComponente                ($obCheTipoFisica		);
$obFormulario->addComponente                ($obCheTipoJuridica		);
$obFormulario->addComponente                ($obCheTipoCgmInterno	);
$obFormulario->agrupaComponentes            ($arDataPeriodo		);
$obFormulario->addComponente                ($obSlPais			);
$obFormulario->addComponente                ($obSlEstado		);
$obFormulario->addComponente                ($obSlCidade		);
$obFormulario->addComponente                ($obBscAtividade		);
$obFormulario->addComponente                ($obSlOrderBy		);
$obFormulario->agrupaComponentes            ($arRadEndereco		);
$obFormulario->defineBarra                  ($arBotoes			);
$obFormulario->show();
