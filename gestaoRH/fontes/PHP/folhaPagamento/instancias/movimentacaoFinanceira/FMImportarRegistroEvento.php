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
* Página de Formulario de Registrar/Importar Evento
* Data de Criação: 30/05/2006

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Id: FMImportarRegistroEvento.php 65248 2016-05-04 19:08:25Z evandro $

* Casos de uso: uc-04.05.49
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php";

//Define a função do arquivo, ex: incluir ou alterar
$stAcao = $request->get('stAcao', 'incluir');

//Define o nome dos arquivos PHP
$stPrograma = "ImportarRegistroEvento";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once $pgJs;

//Utilizado para trazer o título da competência.
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obRFolhaPagamentoFolhaSituacao->consultarFolha();
$stSituacaoFolha = $obRFolhaPagamentoFolhaSituacao->getSituacao();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );
$obForm->setEncType( "multipart/form-data" );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval" );
$obHdnEval->setValue ( "" );

$obLblSituacao = new Label;
$obLblSituacao->setRotulo ( 'Situação'   );
$obLblSituacao->setName   ( 'stSituacao' );
$obLblSituacao->setId     ( 'stSituacao' );
$obLblSituacao->setValue  ( "A folha salário está fechada. Para efetuar o registro de eventos é necessário reabri-lá." );

//Define objetos RADIO para armazenar o TIPO dos Itens
$obRdbLoteEvento = new Radio;
$obRdbLoteEvento->setRotulo  ( "Opções"           );
$obRdbLoteEvento->setLabel   ( "Lote de Eventos"  );
$obRdbLoteEvento->setName    ( "stOpcao"          );
$obRdbLoteEvento->setId      ( "stLoteEvento"     );
$obRdbLoteEvento->setValue   ( "lote_evento"      );
$obRdbLoteEvento->setChecked ( true               );
$obRdbLoteEvento->setNull    ( false              );
$obRdbLoteEvento->obEvento->setOnChange("montaParametrosGET('gerarSpanOpcoes','stOpcao');");

$obRdbLoteMatricula = new Radio;
$obRdbLoteMatricula->setRotulo  ( "Opções"              );
$obRdbLoteMatricula->setLabel   ( "Lote de Matrículas"  );
$obRdbLoteMatricula->setName    ( "stOpcao"             );
$obRdbLoteMatricula->setId      ( "stLoteMatricula"     );
$obRdbLoteMatricula->setValue   ( "lote_matricula"      );
$obRdbLoteMatricula->setChecked ( false                 );
$obRdbLoteMatricula->setNull    ( false                 );
$obRdbLoteMatricula->obEvento->setOnChange("montaParametrosGET('gerarSpanOpcoes','stOpcao',true);");

$obRdbOpcaoImportar = new Radio;
$obRdbOpcaoImportar->setRotulo  ( "Opções"          );
$obRdbOpcaoImportar->setLabel   ( "Importar"        );
$obRdbOpcaoImportar->setName    ( "stOpcao"         );
$obRdbOpcaoImportar->setId      ( "stOpcaoImportar" );
$obRdbOpcaoImportar->setValue   ( "importar"        );
$obRdbOpcaoImportar->setChecked ( false             );
$obRdbOpcaoImportar->setNull    ( false             );
$obRdbOpcaoImportar->obEvento->setOnChange("montaParametrosGET('gerarSpanOpcoes','stOpcao',true);");

$obSpnOpcao = new Span();
$obSpnOpcao->setId("spnOpcao");

$obBtnOk = new Ok(true);

$arBotoes = array($obBtnOk);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden ( $obHdnEval, true );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia(), "right" );
if ($stSituacaoFolha != "Aberto") {
    $obFormulario->addComponente ( $obLblSituacao );
} else {
    $jsOnload   = "executaFuncaoAjax( 'gerarSpanLoteEvento' );";
    $obFormulario->agrupaComponentes( array( $obRdbLoteEvento,$obRdbLoteMatricula , $obRdbOpcaoImportar ) );
    $obFormulario->addSpan( $obSpnOpcao );
    $obFormulario->defineBarra($arBotoes);
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
