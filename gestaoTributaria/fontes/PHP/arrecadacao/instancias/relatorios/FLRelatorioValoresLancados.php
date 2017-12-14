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
    * Página de Filtro para Relatório da Arrecadação
    * Data de Criação   : 05/04/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: FLRelatorioValoresLancados.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioValoresLancados";
$pgFilt     = "FL".$stPrograma.".php";
$pgOcul  		= "OC".$stPrograma.".php";
$pgProc  		= "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

include_once( $pgJS );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $request->get('stCtrl') );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_ARR_INSTANCIAS."relatorios/OCRelatorioValoresLancados.php" );

$obCmbTipo = new Select;
$obCmbTipo->setName      ( "stTipoRelatorio"             );
$obCmbTipo->setRotulo    ( "Tipo de Relatório"           );
$obCmbTipo->setTitle     ( "Selecione o tipo de relatório" );
$obCmbTipo->addOption    ( ""          , "Selecione"     );
$obCmbTipo->addOption    ( "analitico" , "Analítico"     );
$obCmbTipo->addOption    ( "sintetico" , "Sintético"     );
$obCmbTipo->setCampoDesc ( "stTipo"                      );
$obCmbTipo->setNull      ( false                         );
$obCmbTipo->setStyle     ( "width: 200px"                );
$obCmbTipo->obEvento->setOnChange( "montaParametrosGET('montaFiltro','stTipoRelatorio');" );

$obHdnNomeGrupo = new Hidden;
$obHdnNomeGrupo->setName ( "stNomeGrupo" );
$obHdnNomeGrupo->setId ("stNomeGrupo");

$obHdnNomCGM = new Hidden;
$obHdnNomCGM->setName ( "stNaoExiste" );

$obHdnCodigoCredito = new Hidden;
$obHdnCodigoCredito->setName    ('inCodigoCredito');
$obHdnCodigoCredito->setId      ('inCodigoCredito');

$obHdnNomLogradouro = new Hidden;
$obHdnNomLogradouro->setName ( "stNomLogradouro" );
$obHdnNomLogradouro->setValue ( null );

$obHdnNomCondominio = new Hidden;
$obHdnNomCondominio->setName ( "stNomCondominio" );
$obHdnNomCondominio->setValue ( null );

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick( "submeteFiltro()" );

$onBtnLimpar = new Limpar;

$obSpnFiltro = new Span;
$obSpnFiltro->setId ( 'spnFiltro' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnNomeGrupo );
$obFormulario->addHidden( $obHdnNomCGM );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addHidden( $obHdnAcao            );
$obFormulario->addHidden( $obHdnCtrl            );
$obFormulario->addHidden( $obHdnCodigoCredito );
$obFormulario->addHidden( $obHdnNomLogradouro );
$obFormulario->addHidden ( $obHdnNomCondominio );
$obFormulario->addTitulo    ( "Dados para filtro"   );
$obFormulario->addComponente ( $obCmbTipo );
$obFormulario->addSpan       ( $obSpnFiltro );
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );

$obFormulario->show();

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
