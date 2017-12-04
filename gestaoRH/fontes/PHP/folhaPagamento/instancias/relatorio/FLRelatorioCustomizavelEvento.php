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
    * Filtro de Relatório Configurável de Eventos
    * Data de Criação: 13/04/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-19 15:41:28 -0300 (Qua, 19 Mar 2008) $

    * Casos de uso: uc-04.05.51
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                    );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"  					);
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php"  					);

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioCustomizavelEvento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once $pgJS;

Sessao::write('arContratos',array());
Sessao::write('inQtnEventos',6);

$obRFolhaPagamentoEvento = new RFolhaPagamentoEvento();
$obRFolhaPagamentoEvento->listarEvento($rsEventos);

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                				    );
$obForm->setTarget                              ( "telaPrincipal" );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                  );
$obHdnCtrl->setValue                            ( $stCtrl                                                   );

$obHdnFiltrar =  new HiddenEval;
$obHdnFiltrar->setName                          ( "hdnTipoFiltro"                                           );
$obHdnFiltrar->setValue                         ( ""                                                        );

$stOnChange = "ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroTipoFolha.php?".Sessao::getId()."&inCodConfiguracao='+document.frm.inCodConfiguracao.value+'&inCodMes='+document.frm.inCodMes.value+'&inAno='+document.frm.inAno.value+'&boDesdobramento=false','gerarSpanTipoFolha' );";
$obIFiltroCompetencia = new IFiltroCompetencia(true,'',true);
$obIFiltroCompetencia->obCmbMes->obEvento->setOnChange($stOnChange);
$obIFiltroCompetencia->obTxtAno->obEvento->setOnChange($stOnChange);

$obIFiltroTipoFolha = new IFiltroTipoFolha();
$obIFiltroTipoFolha->setValorPadrao("1");

$obCmbEvento = new SelectMultiplo();
$obCmbEvento->setName                           ( 'inCodEvento'                                             );
$obCmbEvento->setRotulo                         ( "Eventos"                                                 );
$obCmbEvento->setTitle                          ( "Selecione os eventos a serem apresentados no relatório (podem ser selecionados até 6 eventos)." );
$obCmbEvento->SetNomeLista1                     ( 'inCodEventoDisponiveis'                                  );
$obCmbEvento->setCampoId1                       ( '[cod_evento]'                                            );
$obCmbEvento->setCampoDesc1                     ( '[codigo]-[descricao]'                                    );
$obCmbEvento->setStyle1                         ( "width: 300px"                                            );
$obCmbEvento->SetRecord1                        ( $rsEventos                                                );
$obCmbEvento->SetNomeLista2                     ( 'inCodEventoSelecionados'                                 );
$obCmbEvento->setCampoId2                       ( '[cod_evento]'                                            );
$obCmbEvento->setCampoDesc2                     ( '[codigo]-[descricao]'                                    );
$obCmbEvento->setStyle2                         ( "width: 300px"                                            );
$obCmbEvento->SetRecord2                        ( new recordset()                                             );
$obCmbEvento->setNull                           ( false                                                     );
$obCmbEvento->obSelect1->setSize                ( 5                                                         );
$obCmbEvento->obSelect2->setSize                ( 5                                                         );
$stOnClick = "montaParametrosGET('validaQuantidadeEventos', 'inQtnEventos');";
$obCmbEvento->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
$obCmbEvento->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
$obCmbEvento->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
$obCmbEvento->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
$obCmbEvento->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
$obCmbEvento->obSelect1->obEvento->setOnDblClick( $stOnClick );
$obCmbEvento->obSelect2->obEvento->setOnDblClick( $stOnClick );

$obChkApresentarPorEvento1 = new CheckBox();
$obChkApresentarPorEvento1->setName             ( "boValor"                                                 );
$obChkApresentarPorEvento1->setRotulo           ( "Apresentar por Evento"                                   );
$obChkApresentarPorEvento1->setLabel            ( "Valor"                                                   );
$obChkApresentarPorEvento1->setTitle            ( "Selecione o campo a ser apresentado no relatório para cada evento." );
$obChkApresentarPorEvento1->setValue            ( true                                                      );
$obChkApresentarPorEvento1->setNull             ( false                                                     );
$obChkApresentarPorEvento1->setChecked          ( true                                                      );

$obChkApresentarPorEvento2 = new CheckBox();
$obChkApresentarPorEvento2->setName             ( "boQuantidade"                                            );
$obChkApresentarPorEvento2->setRotulo           ( "Apresentar por Evento"                                   );
$obChkApresentarPorEvento2->setLabel            ( "Quantidade"                                              );
$obChkApresentarPorEvento2->setTitle            ( "Selecione o campo a ser apresentado no relatório para cada evento." );
$obChkApresentarPorEvento2->setValue            ( true                                                      );
$obChkApresentarPorEvento2->setNull             ( false                                                     );

$obChkApresentarPorContrato1 = new Radio();
$obChkApresentarPorContrato1->setName           ( "boApresentarPorMatricula"                                );
$obChkApresentarPorContrato1->setRotulo         ( "Apresentar por Matrícula"                                );
$obChkApresentarPorContrato1->setLabel          ( "Lotação"                                                 );
$obChkApresentarPorContrato1->setTitle          ( "Selecione os campos a serem apresentados no relatório para cada contrato." );
$obChkApresentarPorContrato1->setValue          ( "lotacao"                                                 );
$obChkApresentarPorContrato1->obEvento->setOnChange( "montaParametrosGET('verificarEventosSelecionados', 'inCodEventoSelecionados', 'true');"  );
$obChkApresentarPorContrato1->setChecked		( true 					 								    );

$obChkApresentarPorContrato2 = new Radio();
$obChkApresentarPorContrato2->setName           ( "boApresentarPorMatricula"                                 );
$obChkApresentarPorContrato2->setRotulo         ( "Apresentar por Matrícula"                                 );
$obChkApresentarPorContrato2->setLabel          ( "Local"                                                    );
$obChkApresentarPorContrato2->setTitle          ( "Selecione os campos a serem apresentados no relatório para cada contrato." );
$obChkApresentarPorContrato2->setValue          ( "local"                                                    );
$obChkApresentarPorContrato2->obEvento->setOnChange( "montaParametrosGET('verificarEventosSelecionados', 'inCodEventoSelecionados',  'true' );" );

$obChkApresentarPorContrato3 = new Radio();
$obChkApresentarPorContrato3->setName           ( "boApresentarPorMatricula"                                 );
$obChkApresentarPorContrato3->setRotulo         ( "Apresentar por Matrícula"                                 );
$obChkApresentarPorContrato3->setLabel          ( "Cargo/Especialidade"                                      );
$obChkApresentarPorContrato3->setTitle          ( "Selecione os campos a serem apresentados no relatório para cada contrato." );
$obChkApresentarPorContrato3->setValue          ( "cargo"                                                    );
$obChkApresentarPorContrato3->obEvento->setOnChange( "montaParametrosGET('verificarEventosSelecionados', 'inCodEventoSelecionados',  'true' );");

$obChkApresentarPorContrato4 = new Radio();
$obChkApresentarPorContrato4->setName           ( "boApresentarPorMatricula"                                 );
$obChkApresentarPorContrato4->setRotulo         ( "Apresentar por Matrícula"                                 );
$obChkApresentarPorContrato4->setLabel          ( "Função/Especialidade"                                     );
$obChkApresentarPorContrato4->setTitle          ( "Selecione os campos a serem apresentados no relatório para cada contrato." );
$obChkApresentarPorContrato4->setValue          ( "funcao"                                                   );
$obChkApresentarPorContrato4->obEvento->setOnChange( "montaParametrosGET('verificarEventosSelecionados', 'inCodEventoSelecionados', 'true');"          );

$obChkApresentarPorContrato5 = new Radio();
$obChkApresentarPorContrato5->setName           ( "boApresentarPorMatricula"                                 );
$obChkApresentarPorContrato5->setRotulo         ( "Apresentar por Matrícula"                                 );
$obChkApresentarPorContrato5->setLabel          ( "CPF"                                                      );
$obChkApresentarPorContrato5->setTitle          ( "Selecione os campos a serem apresentados no relatório para cada contrato." );
$obChkApresentarPorContrato5->setValue          ( "cpf"                                                      );
$obChkApresentarPorContrato5->obEvento->setOnChange( "montaParametrosGET('verificarEventosSelecionados', 'inCodEventoSelecionados', 'true');"          );

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setRegSubCarEsp();
$obIFiltroComponentes->setRegSubFunEsp();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setPadrao();
$obIFiltroComponentes->setGrupoLotacao();
$obIFiltroComponentes->setGrupoLocal();
$obIFiltroComponentes->setGrupoPadrao();
$obIFiltroComponentes->setGrupoRegSubCarEsp();
$obIFiltroComponentes->setGrupoRegSubFunEsp();
$obIFiltroComponentes->setTodos();

$obRdbAtivo = new Radio();
$obRdbAtivo->setName                            ( "stSituacao"                                              );
$obRdbAtivo->setRotulo                          ( "Situação Servidor"                                       );
$obRdbAtivo->setLabel                           ( "Ativo"                                                   );
$obRdbAtivo->setTitle                           ( "Selecione a(s) Situação(ões)."                           );
$obRdbAtivo->setValue                           ( "A"                                                   );
$obRdbAtivo->setChecked                         ( true                                                      );

$obRdbInativo = new Radio();
$obRdbInativo->setName                          ( "stSituacao"                                              );
$obRdbInativo->setRotulo                        ( "Situação Servidor"                                       );
$obRdbInativo->setLabel                         ( "Aposentado"                                              );
$obRdbInativo->setTitle                         ( "Selecione a(s) Situação(ões)."                           );
$obRdbInativo->setValue                         ( "P"                                                 );

$obRdbRescindido = new Radio();
$obRdbRescindido->setName                       ( "stSituacao"                                              );
$obRdbRescindido->setRotulo                     ( "Situação Servidor"                                       );
$obRdbRescindido->setLabel                      ( "Rescindido"                                              );
$obRdbRescindido->setTitle                      ( "Selecione a(s) Situação(ões)."                           );
$obRdbRescindido->setValue                      ( "R"                                              );

$obRdbPensionista = new Radio();
$obRdbPensionista->setName                      ( "stSituacao"                                              );
$obRdbPensionista->setRotulo                    ( "Situação Servidor"                                       );
$obRdbPensionista->setLabel                     ( "Pensionista"                                             );
$obRdbPensionista->setTitle                     ( "Selecione a(s) Situação(ões)."                           );
$obRdbPensionista->setValue                     ( "E"                                             );

$obRdbTodos = new Radio();
$obRdbTodos->setName                            ( "stSituacao"                                              );
$obRdbTodos->setRotulo                          ( "Situação Servidor"                                       );
$obRdbTodos->setLabel                           ( "Todos"                                                   );
$obRdbTodos->setTitle                           ( "Selecione a(s) Situação(ões)."                           );
$obRdbTodos->setValue                           ( "T"                                             );

$obRdoOrdenacao1 = new Radio();
$obRdoOrdenacao1->setName                       ( "stOrdenacao"                                             );
$obRdoOrdenacao1->setRotulo                     ( "Ordenação das Matrículas"                                );
$obRdoOrdenacao1->setLabel                      ( "Alfabética"                                              );
$obRdoOrdenacao1->setTitle                      ( "Selecione a ordenação dos contratos no relatório."       );
$obRdoOrdenacao1->setValue                      ( "nom_cgm"                                              );
$obRdoOrdenacao1->setChecked                    ( true                                                      );

$obRdoOrdenacao2 = new Radio();
$obRdoOrdenacao2->setName                       ( "stOrdenacao"                                             );
$obRdoOrdenacao2->setRotulo                     ( "Ordenação das Matrículas"                                );
$obRdoOrdenacao2->setLabel                      ( "Numérica"                                                );
$obRdoOrdenacao2->setTitle                      ( "Selecione a ordenação dos contratos no relatório."       );
$obRdoOrdenacao2->setValue                      ( "registro"                                                );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm                          ( $obForm                                                   );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden                        ( $obHdnCtrl                                                );
$obFormulario->addHidden                        ( $obHdnFiltrar                                             );
$obFormulario->addTitulo                        ( "Seleção do Filtro"                                       );
$obIFiltroCompetencia->geraFormulario           ( $obFormulario                                             );
$obIFiltroTipoFolha->geraFormulario		        ( $obFormulario 											);
$obFormulario->addComponente                    ( $obCmbEvento                                              );
$obFormulario->agrupaComponentes                ( array($obChkApresentarPorEvento1, $obChkApresentarPorEvento2)    );
$obFormulario->addComponente                    ( $obChkApresentarPorContrato1                              );
$obFormulario->addComponente                    ( $obChkApresentarPorContrato2                              );
$obFormulario->addComponente                    ( $obChkApresentarPorContrato3                              );
$obFormulario->addComponente                    ( $obChkApresentarPorContrato4                              );
$obFormulario->addComponente                    ( $obChkApresentarPorContrato5                              );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obFormulario->agrupaComponentes                ( array($obRdbAtivo,$obRdbInativo,$obRdbRescindido,$obRdbPensionista,$obRdbTodos ) );
$obFormulario->agrupaComponentes                ( array($obRdoOrdenacao1,$obRdoOrdenacao2)                  );

$obOk  = new Ok(true);
$obOk->obEvento->setOnClick("if ( validaSelectMultiplo() ){ Salvar(); }else{ alertaAviso('Você deve selecionar ao menos um item do tipo de filtro selecionado','n_incluir','erro','<?=Sessao::getId()?>'); }");

$obLimpar  = new Limpar;
$obFormulario->defineBarra( array( $obOk, $obLimpar ) );


$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
