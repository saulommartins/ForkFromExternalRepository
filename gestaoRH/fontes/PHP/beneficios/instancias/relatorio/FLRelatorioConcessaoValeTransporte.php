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
* Página de filtro para relatório de concessão de vale-transporte
* Data de Criação: 07/11/2005

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2006-09-26 07:00:14 -0300 (Ter, 26 Set 2006) $

* Casos de uso: uc-04.06.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                              );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                );
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php"                                      );
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php"                                      );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioConcessaoValeTransporte";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OCFiltro".$stPrograma.".php";
$pgProc = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

//Define o código de validação dos campos dentro do spam
$obHdnEval = new HiddenEval;
$obHdnEval->setName ( "stOpcaoEval" );
$obHdnEval->setValue( "" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GRH_BEN_INSTANCIAS."relatorio/OCRelatorioConcessaoValeTransporte.php" );

//Define objeto RADIO para definir as opções de filtro
$obRdoOpcoesContrato = new Radio;
$obRdoOpcoesContrato->setName    ( "stRdoOpcoes" );
$obRdoOpcoesContrato->setId      ( "stRdoOpcoesContrato" );
$obRdoOpcoesContrato->setRotulo  ( "Opções"   );
$obRdoOpcoesContrato->setLabel   ( "Matrícula" );
$obRdoOpcoesContrato->setTitle   ( "Informe o filtro para gerar o relatório da concessão de vale-transporte" );
$obRdoOpcoesContrato->setValue   ( "contrato" );
$obRdoOpcoesContrato->setNull    ( false      );
$obRdoOpcoesContrato->setChecked ( true       );
$obRdoOpcoesContrato->obEvento->setOnChange ( "buscaValor('geraSpan');" );

$obRdoOpcoesCGMContratro = new Radio;
$obRdoOpcoesCGMContratro->setName    ( "stRdoOpcoes"    );
$obRdoOpcoesCGMContratro->setId      ( "stRdoOpcoesCgm" );
$obRdoOpcoesCGMContratro->setRotulo  ( "Opções"         );
$obRdoOpcoesCGMContratro->setLabel   ( "CGM/Matrícula"   );
$obRdoOpcoesCGMContratro->setValue   ( "cgm"            );
$obRdoOpcoesCGMContratro->setNull    ( false            );
$obRdoOpcoesCGMContratro->setChecked ( false            );
$obRdoOpcoesCGMContratro->obEvento->setOnChange ( "buscaValor('geraSpan');" );

$obRdoOpcoesGrupo = new Radio;
$obRdoOpcoesGrupo->setName    ( "stRdoOpcoes" );
$obRdoOpcoesGrupo->setId      ( "stRdoOpcoesGrupo" );
$obRdoOpcoesGrupo->setRotulo  ( "Opções" );
$obRdoOpcoesGrupo->setLabel   ( "Grupo"  );
$obRdoOpcoesGrupo->setValue   ( "grupo"  );
$obRdoOpcoesGrupo->setNull    ( false    );
$obRdoOpcoesGrupo->setChecked ( false    );
$obRdoOpcoesGrupo->obEvento->setOnChange ( "buscaValor('geraSpan');" );

//SPAM Filtros
$obSpnFiltro = new Span;
$obSpnFiltro->setId ( "spnFiltro" );

//Define os SELECTMULTIPLO para Lotação
$obCmbLotacao = new ISelectMultiploLotacao();

//Define os SELECTMULTIPLO para Local
$obCmbLocal   = new ISelectMultiploLocal();

//Define a PERIODICIDADE
$obDtPeriodicidade = new Periodicidade();
$obDtPeriodicidade->setExercicio ( Sessao::getExercicio() );
$obDtPeriodicidade->setNull      ( false              );
$obDtPeriodicidade->setId        ( "stPeriodicidade"  );

//Monta FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm );
$obFormulario->addTitulo         ( "Parâmetros para Emissão do Relatório" );
$obFormulario->addHidden         ( $obHdnCtrl );
$obFormulario->addHidden         ( $obHdnCaminho );
$obFormulario->agrupaComponentes ( array ( $obRdoOpcoesContrato, $obRdoOpcoesCGMContratro, $obRdoOpcoesGrupo ) );
$obFormulario->addHidden         ( $obHdnEval, true );
$obFormulario->addSpan           ( $obSpnFiltro );
$obFormulario->addComponente     ( $obCmbLotacao );
$obFormulario->addComponente     ( $obCmbLocal );
$obFormulario->addComponente     ( $obDtPeriodicidade );

$obBtnClean = new Button;
$obBtnClean->setName                    ( "btnClean"     );
$obBtnClean->setValue                   ( "Limpar"       );
$obBtnClean->setTipo                    ( "button"       );
$obBtnClean->obEvento->setOnClick       ( "limpaForm();" );
$obBtnClean->setDisabled                ( false          );

$obBtnOK = new Ok;
$botoesForm     = array ( $obBtnOK , $obBtnClean );
$obFormulario->defineBarra($botoesForm);

$obFormulario->setFormFocus($obRdoOpcoesContrato->getId() );

$stJs .= "buscaValor('geraSpan'); \n";
SistemaLegado::executaFramePrincipal($stJs);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
