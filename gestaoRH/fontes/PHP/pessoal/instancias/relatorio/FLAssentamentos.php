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
    * Arquivo de Filtro
    * Data de Criação: 21/02/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.04.51

    $Id: FLAssentamentos.php 59612 2014-09-02 12:00:51Z gelson $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );

//Define o nome dos arquivos PHP
$stPrograma = "Assentamentos";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

Sessao::remove('link');
Sessao::remove("arContratos");

$stAcao = $request->get('stAcao');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction    ( $pgProc  );
$obForm->setTarget("oculto");

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRegSubFunEsp();
$obIFiltroComponentes->setAtributoServidor();
$obIFiltroComponentes->setRescisao();
$obIFiltroComponentes->setTodos();

include_once(CAM_GRH_PES_COMPONENTES."IFiltroAssentamentoMultiplo.class.php");
$obIFiltroAssentamentoMultiplo = new IFiltroAssentamentoMultiplo();
$obIFiltroAssentamentoMultiplo->obISelectMultiploClassificacaoAssentamento->setNull(false);
$obIFiltroAssentamentoMultiplo->obISelectMultiploAssentamento->setNull(false);

$obDtPeriodicidade = new Periodicidade();

$rdAgrupaContrato = new Radio;
$rdAgrupaContrato->setRotulo ( "Agrupar por:" );
$rdAgrupaContrato->setName   ( "boAgrupamento" );
$rdAgrupaContrato->setValue  ( "C" );
$rdAgrupaContrato->setTitle  ( "Informe a forma de agrupamento para a emissão do relatório." );
$rdAgrupaContrato->setLabel  ( "Contrato" );
$rdAgrupaContrato->setChecked( ($boAgrupamento=='C' || $boAgrupamento=='') );
$rdAgrupaContrato->setNull   ( false );

$rdAgrupaAssentamento = new Radio;
$rdAgrupaAssentamento->setRotulo ( "Agrupar por:" );
$rdAgrupaAssentamento->setName   ( "boAgrupamento" );
$rdAgrupaAssentamento->setValue  ( "A" );
$rdAgrupaAssentamento->setTitle  ( "Informe a forma de agrupamento para a emissão do relatório." );
$rdAgrupaAssentamento->setLabel  ( "Classificação/Assentamento" );
$rdAgrupaAssentamento->setChecked( $boAgrupamento=='A' );
$rdAgrupaAssentamento->setNull   ( false );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addForm   ( $obForm                                           );
$obFormulario->addHidden ( $obHdnAcao                                        );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obIFiltroAssentamentoMultiplo->geraFormulario($obFormulario);
$obFormulario->addComponente($obDtPeriodicidade);
$obFormulario->addComponenteComposto($rdAgrupaContrato, $rdAgrupaAssentamento);
$obFormulario->OK(true);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
