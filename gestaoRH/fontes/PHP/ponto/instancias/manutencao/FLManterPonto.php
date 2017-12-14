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
    * Formulário
    * Data de Criação: 13/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Rafael Garbin

    * @package URBEM
    * @subpackage

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                                  );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                                        );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"                                              );

$stPrograma = "ManterPonto";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

Sessao::write("link", "");
Sessao::write("arJustificativas", array());

$jsOnload = "montaParametrosGET('FLProcessaOnLoad','boTipoManutencao');";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $_REQUEST["stAcao"] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obRadioTipoManutencaoIndividual = new Radio();
$obRadioTipoManutencaoIndividual->setName   ( "boTipoManutencao" );
$obRadioTipoManutencaoIndividual->setRotulo ( "Tipo Manutenção" );
$obRadioTipoManutencaoIndividual->setTitle  ( "Informe o tipo de manutenção do relógio ponto: individual ou por lote diário, conforme filtro." );
$obRadioTipoManutencaoIndividual->setLabel  ( "Individual" );
$obRadioTipoManutencaoIndividual->setValue  ( "INDIVIDUAL" );
$obRadioTipoManutencaoIndividual->setId     ( "boTipoManutencaoIndividual" );
$obRadioTipoManutencaoIndividual->setChecked ( true );
$obRadioTipoManutencaoIndividual->obEvento->setOnChange( "montaParametrosGET('montaSpanLoteDiario','boTipoManutencao');" );

$obRadioTipoManutencaoLoteDiario = new Radio();
$obRadioTipoManutencaoLoteDiario->setName   ( "boTipoManutencao" );
$obRadioTipoManutencaoLoteDiario->setRotulo ( "Tipo Manutenção" );
$obRadioTipoManutencaoLoteDiario->setTitle  ( "Informe o tipo de manutenção do relógio ponto: individual ou por lote diário, conforme filtro." );
$obRadioTipoManutencaoLoteDiario->setLabel  ( "Lote Diário" );
$obRadioTipoManutencaoLoteDiario->setValue  ( "LOTE_DIARIO" );
$obRadioTipoManutencaoLoteDiario->setId     ( "boTipoManutencaoLoteDiario" );
$obRadioTipoManutencaoLoteDiario->obEvento->setOnChange( "montaParametrosGET('montaSpanLoteDiario','boTipoManutencao');" );

$obHdnTipoManutencao = new hiddenEval();
$obHdnTipoManutencao->setId("hdnTipoManutencao");
$obHdnTipoManutencao->setName("hdnTipoManutencao");

$obSpnLoteDiario = new Span;
$obSpnLoteDiario->setId ( "spnLoteDiario" );

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRegimeSubDivisaoFuncao();
$obIFiltroComponentes->setGeral(false);
$obIFiltroComponentes->setFiltroPadrao("contrato");

$obRadioOrdenacaoNome = new Radio();
$obRadioOrdenacaoNome->setName   ( "boOrdenacao" );
$obRadioOrdenacaoNome->setRotulo ( "Ordenação" );
$obRadioOrdenacaoNome->setTitle  ( "Selecione o tipo de ordenação: nome ou de código de matrícula." );
$obRadioOrdenacaoNome->setLabel  ( "Nome" );
$obRadioOrdenacaoNome->setValue  ( "NOME" );
$obRadioOrdenacaoNome->setId     ( "boOrdenacaoNome" );
$obRadioOrdenacaoNome->setChecked ( true );

$obRadioOrdenacaoMatricula = new Radio();
$obRadioOrdenacaoMatricula->setName   ( "boOrdenacao" );
$obRadioOrdenacaoMatricula->setRotulo ( "Ordenação" );
$obRadioOrdenacaoMatricula->setTitle  ( "Selecione o tipo de ordenação: nome ou de código de matrícula." );
$obRadioOrdenacaoMatricula->setLabel  ( "Matrícula" );
$obRadioOrdenacaoMatricula->setValue  ( "MATRICULA" );
$obRadioOrdenacaoMatricula->setId     ( "boOrdenacaoMatricula" );

$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm                                                                  );
$obFormulario->addTitulo         ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"         );
$obFormulario->addHidden         ( $obHdnCtrl                                                               );
$obFormulario->addHidden         ( $obHdnAcao                                                               );
$obFormulario->addHidden         ( $obHdnTipoManutencao, true                                               );
$obFormulario->addTitulo         ( "Manutenção do Ponto"                                                    );
$obFormulario->agrupaComponentes ( array($obRadioTipoManutencaoIndividual, $obRadioTipoManutencaoLoteDiario));
$obFormulario->addSpan           ( $obSpnLoteDiario                                                         );
$obFormulario->addTitulo         ( "Seleção do Filtro"                                                      );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obIFiltroComponentes->getOnload($jsOnload);
$obFormulario->agrupaComponentes ( array($obRadioOrdenacaoNome, $obRadioOrdenacaoMatricula)                 );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
