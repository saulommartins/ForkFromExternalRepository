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
    * Página de Form do Configuração Eventos Automáticos
    * Data de Criação: 06/11/2015
    * @author Analista:  Dagiane Vieira
    * @author Desenvolvedor: Jean da Silva
    * @ignore
    * Casos de uso: uc-04.05.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php" );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php" );
include_once ( CAM_GRH_FOL_COMPONENTES."IBscEvento.class.php" );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoEventosAutomaticos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get("stAcao");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

$obIBscEvento = new IBscEvento("inCodigoEvento","stEvento");
$obIBscEvento->obBscInnerEvento->setRotulo( "*Evento a lançar" );
$obIBscEvento->setEventoSistema           ( false  );

//Botão para Incluir / Limpar
$obBtnIncluir = new Button;
$obBtnIncluir->setId('btnIncluir');
$obBtnIncluir->setValue('Incluir');
$obBtnIncluir->obEvento->setOnClick("montaParametrosGET('incluirEvento', 'inCodigoEvento,stEvento');");

$obBtnLimpar = new Button;
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->obEvento->setOnClick("executaFuncaoAjax('limparEvento');");

$obSpnLista = new Span;
$obSpnLista->setId('spnLista');
$obSpnLista->setValue($stHTML);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm                                                          );
$obFormulario->addTitulo( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden( $obHdnAcao                                                       );
$obFormulario->addHidden( $obHdnCtrl                                                       );
$obFormulario->addTitulo( "Configuração de Eventos para Lançamento Automático"             );
$obIBscEvento->geraFormulario( $obFormulario                                               );


$obFormulario->agrupaComponentes(array($obBtnIncluir, $obBtnLimpar));
$obFormulario->addSpan($obSpnLista);

$obFormulario->Ok();
$obFormulario->show();

$jsOnLoad = "executaFuncaoAjax('carregaEventos');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
