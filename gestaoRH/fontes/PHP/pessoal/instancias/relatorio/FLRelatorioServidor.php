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
* Página filtro para relatório de Servidor
* Data de Criação   : 15/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30566 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.04.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma    = "RelatorioServidor";
$pgFilt        = "FL".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma."Filtro.php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GRH_PES_INSTANCIAS."relatorio/OCRelatorioServidor.php" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obChkAtivos = new Radio();
$obChkAtivos->setName("stSituacao");
$obChkAtivos->setRotulo("Cadastro");
$obChkAtivos->setLabel("Ativos");
$obChkAtivos->setValue("ativos");
$obChkAtivos->setNull(false);
$obChkAtivos->setChecked(true);
$obChkAtivos->setTitle("Selecione o tipo de cadastro para emissão.");
$obChkAtivos->obEvento->setOnChange("buscaValor('gerarSpanAtivosAposentados');");

$obChkAposentados = new Radio();
$obChkAposentados->setName("stSituacao");
$obChkAposentados->setLabel("Aposentados");
$obChkAposentados->setValue("aposentados");
$obChkAposentados->obEvento->setOnChange("buscaValor('gerarSpanAtivosAposentados');");

$obChkPensionistas = new Radio();
$obChkPensionistas->setName("stSituacao");
$obChkPensionistas->setLabel("Pensionistas");
$obChkPensionistas->setValue("pensionistas");
$obChkPensionistas->obEvento->setOnChange("buscaValor('gerarSpanPensionistas');");

$obChkRescindidos = new Radio();
$obChkRescindidos->setName("stSituacao");
$obChkRescindidos->setLabel("Rescindidos");
$obChkRescindidos->setValue("rescindidos");
$obChkRescindidos->obEvento->setOnChange("buscaValor('gerarSpanAtivosAposentados');");

$obChkTodos = new Radio();
$obChkTodos->setName("stSituacao");
$obChkTodos->setLabel("Todos");
$obChkTodos->setValue("todos");
$obChkTodos->obEvento->setOnChange("buscaValor('gerarSpanAtivosAposentados');");

$arChkCadastro = array($obChkAtivos,$obChkRescindidos,$obChkAposentados,$obChkPensionistas,$obChkTodos);

$obSpnCadastro = new Span();
$obSpnCadastro->setId("spnCadastro");

$obCheckIdentificacao = new CheckBox;
$obCheckIdentificacao->setName    ( "boIdentificacao" );
$obCheckIdentificacao->setId      ( "boIdentificacao" );
$obCheckIdentificacao->setValue   ( "1"               );
$obCheckIdentificacao->setRotulo  ( "Informações da Identificação");
$obCheckIdentificacao->setChecked ( false             );

$obCheckFoto = new CheckBox;
$obCheckFoto->setName    ( "boFoto" );
$obCheckFoto->setValue   ( "1"      );
$obCheckFoto->setRotulo  ( "Foto"   );
$obCheckFoto->setChecked ( false    );

$obCheckDocumentacao = new CheckBox;
$obCheckDocumentacao->setName    ( "boDocumentacao" );
$obCheckDocumentacao->setId      ( "boDocumentacao" );
$obCheckDocumentacao->setValue   ( "1"              );
$obCheckDocumentacao->setRotulo  ( "Informações da Documentação");
$obCheckDocumentacao->setChecked ( false            );

$obCheckContratuais = new CheckBox;
$obCheckContratuais->setName    ( "boContratuais" );
$obCheckContratuais->setId      ( "boContratuais" );
$obCheckContratuais->setValue   ( "1"             );
$obCheckContratuais->setRotulo  ( "Informações Contratuais" );
$obCheckContratuais->setChecked ( false           );

$obCheckSalariais = new CheckBox;
$obCheckSalariais->setName    ( "boSalariais" );
$obCheckSalariais->setId      ( "boSalariais" );
$obCheckSalariais->setValue   ( "1"           );
$obCheckSalariais->setRotulo  ( "Informações Salariais" );
$obCheckSalariais->setChecked ( false         );

$obCheckBancarias = new CheckBox;
$obCheckBancarias->setName    ( "boBancarias" );
$obCheckBancarias->setId      ( "boBancarias" );
$obCheckBancarias->setValue   ( "1"           );
$obCheckBancarias->setRotulo  ( "Informações Bancárias" );
$obCheckBancarias->setChecked ( false         );

$obCheckLotacao = new CheckBox;
$obCheckLotacao->setName    ( "boLotacao" );
$obCheckLotacao->setId      ( "boLotacao" );
$obCheckLotacao->setValue   ( "1"         );
$obCheckLotacao->setRotulo  ( "Informações da Lotação" );
$obCheckLotacao->setChecked ( false       );

$obCheckPrevidencia = new CheckBox;
$obCheckPrevidencia->setName    ( "boPrevidencia"   );
$obCheckPrevidencia->setId      ( "boPrevidencia"   );
$obCheckPrevidencia->setValue   ( "1"               );
$obCheckPrevidencia->setRotulo  ( "Informações da Previdência" );
$obCheckPrevidencia->setChecked ( false             );

$obCheckFerias = new CheckBox;
$obCheckFerias->setName    ( "boFerias" );
$obCheckFerias->setId      ( "boFerias" );
$obCheckFerias->setValue   ( "1"        );
$obCheckFerias->setRotulo  ( "Informações de Férias" );
$obCheckFerias->setChecked ( false      );

$obCheckAtributos = new CheckBox;
$obCheckAtributos->setName    ( "boAtributos"         );
$obCheckAtributos->setId      ( "boAtributos"         );
$obCheckAtributos->setValue   ( "1"                   );
$obCheckAtributos->setRotulo  ( "Informações dos Atributos Dinâmicos" );
$obCheckAtributos->setChecked ( false                 );

$obCheckDependentes = new CheckBox;
$obCheckDependentes->setName    ( "boDependentes" );
$obCheckDependentes->setId      ( "boDependentes" );
$obCheckDependentes->setValue   ( "1"             );
$obCheckDependentes->setRotulo  ( "Informações dos Dependentes" );
$obCheckDependentes->setChecked ( false           );

$obCheckAssentamentos = new CheckBox;
$obCheckAssentamentos->setName    ( "boAssentamentos" );
$obCheckAssentamentos->setId      ( "boAssentamentos" );
$obCheckAssentamentos->setValue   ( "1"               );
$obCheckAssentamentos->setRotulo  ( "Informações dos Assentamentos" );
$obCheckAssentamentos->setChecked ( false             );

$obChkTodos = new Checkbox;
$obChkTodos->setName                        ( "boTodos" );
$obChkTodos->setId                          ( "boTodos" );
//$obChkTodos->setValue                       ( 1 );
$obChkTodos->setRotulo                      ( "Selecionar Todas" );
$obChkTodos->obEvento->setOnChange          ( "selecionarTodos(this.value);" );
$obChkTodos->montaHTML();
    
//FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                       );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );

$obFormulario->addHidden            ( $obHdnCtrl                    );
$obFormulario->addHidden            ( $obHdnAcao                    );
$obFormulario->addHidden            ( $obHdnCaminho                 );

$obFormulario->addTitulo            ( "Parâmetros para Emissão"     );
$obFormulario->agrupaComponentes($arChkCadastro);

$obFormulario->addSpan              ( $obSpnCadastro );

$obFormulario->addTitulo            ( "Opções de Visualização"      );
$obFormulario->addComponente        ( $obCheckIdentificacao  );
$obFormulario->addComponente        ( $obCheckFoto  );
$obFormulario->addComponente        ( $obCheckDocumentacao  );
$obFormulario->addComponente        ( $obCheckContratuais  );
$obFormulario->addComponente        ( $obCheckSalariais  );
$obFormulario->addComponente        ( $obCheckBancarias  );
$obFormulario->addComponente        ( $obCheckLotacao  );
$obFormulario->addComponente        ( $obCheckPrevidencia  );
$obFormulario->addComponente        ( $obCheckFerias  );
$obFormulario->addComponente        ( $obCheckAtributos  );
$obFormulario->addComponente        ( $obCheckDependentes  );
$obFormulario->addComponente        ( $obCheckAssentamentos  );
$obFormulario->addComponente        ( $obChkTodos  );

$obBtnClean = new Button;
$obBtnClean->setName                ( "btnClean"                    );
$obBtnClean->setValue               ( "Limpar"                      );
$obBtnClean->setTipo                ( "button"                      );
$obBtnClean->obEvento->setOnClick   ( "limpaForm();"    );
$obBtnClean->setDisabled            ( false                         );

$obBtnOK = new Ok;
$obBtnOK->obEvento->setOnClick ( "eval(jQuery('#hdnTipoFiltro').val()); if (validaVisualizacoes()) { Salvar(); }" );
$botoesForm = array ( $obBtnOK , $obBtnClean );

$obFormulario->defineBarra($botoesForm);

$obFormulario->show();

$stJs = "buscaValor('gerarSpanAtivosAposentados');";

sistemaLegado::executaFrameOculto( $stJs );
