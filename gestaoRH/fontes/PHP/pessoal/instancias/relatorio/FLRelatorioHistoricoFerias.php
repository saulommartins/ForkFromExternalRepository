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
    * Filtro de Relatório de Histórico de Férias
    * Data de Criação: 17/08/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore
    * Casos de uso: uc-04.04.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

$stPrograma = "RelatorioHistoricoFerias";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$jsOnLoad = "montaParametrosGET('montarFiltroOrdenacao', '');";
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgOcul  );
$obForm->setTarget( "oculto" );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRegimeSubDivisao();
$obIFiltroComponentes->setRescisao();
$obIFiltroComponentes->setTodos();
$onChange = $obIFiltroComponentes->obCmbTipoFiltro->obEvento->getOnChange();
$onChange .= " montaParametrosGET('montarFiltroOrdenacao', 'stTipoFiltro');";
$obIFiltroComponentes->obCmbTipoFiltro->obEvento->setOnChange($onChange);

$dtDataLimite = new Data;
$dtDataLimite->setName  ( 'dtDataLimite' );
$dtDataLimite->setValue ( date('d/m/Y')  );
$dtDataLimite->setTitle ( 'Informe a data para o filtro. ' );
$dtDataLimite->setRotulo( 'Data Limite' );
$dtDataLimite->setSize  ( 10 );

$obSpnOrdenacao = new Span();
$obSpnOrdenacao->setId("spnOrdenacao");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia(), "right" );
$obFormulario->addTitulo( 'Seleção do Filtro' );
$obIFiltroComponentes->geraFormulario ($obFormulario);
$obFormulario->addComponente( $dtDataLimite );
$obFormulario->addSpan( $obSpnOrdenacao );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
