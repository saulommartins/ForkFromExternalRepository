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
    * Página de Filtro do Relatório de Pagamento de Estagiários
    * Data de Criação: 26/10/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.07.04

    $Id: FLReciboPagamento.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"                       		);

//Define o nome dos arquivos PHP
$stPrograma = "ReciboPagamento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

Sessao::write('link', '');

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "telaPrincipal" );

Sessao::write("obForm", $obForm);
$obIFiltroCompetencia = new IFiltroCompetencia();

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setCGMCodigoEstagio();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setAtributoEstagiario();
$obIFiltroComponentes->setInstituicaoEnsino();
$obIFiltroComponentes->setInstituicaoIntermediadora();
$obIFiltroComponentes->setGrupoLotacao();
$obIFiltroComponentes->setGrupoLocal();
$obIFiltroComponentes->setDisabledQuebra();

$obChkDuplicar = new CheckBox();
$obChkDuplicar->setRotulo("Emitir Cópia do Recibo");
$obChkDuplicar->setName("boDuplicar");
$obChkDuplicar->setValue("true");
$obChkDuplicar->setTitle("Clique para emitir uma cópia do recido de pagamento.");

$obRdoOrdenacaoAlfabetica = new Radio();
$obRdoOrdenacaoAlfabetica->setRotulo("Ordenação");
$obRdoOrdenacaoAlfabetica->setName("stOrdenacao");
$obRdoOrdenacaoAlfabetica->setValue("alfabetica");
$obRdoOrdenacaoAlfabetica->setLabel("Alfabética");
$obRdoOrdenacaoAlfabetica->setChecked(true);
$obRdoOrdenacaoAlfabetica->setTitle("Selecione o tipo de ordenação: alfabética (nome) ou numérica (código de estágio).");

$obRdoOrdenacaoNumerica = new Radio();
$obRdoOrdenacaoNumerica->setRotulo("Ordenação");
$obRdoOrdenacaoNumerica->setName("stOrdenacao");
$obRdoOrdenacaoNumerica->setValue("numerica");
$obRdoOrdenacaoNumerica->setLabel("Numérica");
$obRdoOrdenacaoNumerica->setTitle("Selecione o tipo de ordenação: alfabética (nome) ou numérica (código de estágio).");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obIFiltroCompetencia->geraFormulario( $obFormulario );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obFormulario->addComponente($obChkDuplicar);
$obFormulario->agrupaComponentes(array($obRdoOrdenacaoAlfabetica,$obRdoOrdenacaoNumerica));
$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
