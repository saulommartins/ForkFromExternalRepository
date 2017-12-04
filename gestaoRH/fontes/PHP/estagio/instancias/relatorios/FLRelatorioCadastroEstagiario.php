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
    * Página de Filtro do Relatório de Cadastro de Estagiários
    * Data de Criação : 07/02/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Alexandre Melo

    * @ignore

    $Revision: 32866 $
    $Name$
    $Autor: $
    $Date: 2007-04-02 09:37:23 -0300 (Seg, 02 Abr 2007) $

    * Casos de uso: uc-04.07.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"                                   );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioCadastroEstagiario";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

// $jsOnload = "executaFuncaoAjax('gerarSpanCodigoEstagiario');";
//
// Sessao::write('link', '');

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao=new Hidden;
$obHdnAcao -> setName                             ( "stAcao" );
$obHdnAcao -> setValue                            ( $stAcao  );

$obHdnCtrl=new Hidden;
$obHdnCtrl -> setName                             ( "stCtrl" );
$obHdnCtrl -> setValue                            ( $stCtrl  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "telaPrincipal" );

Sessao::write('obForm', $obForm);
$obIFiltroCompetencia = new IFiltroCompetencia();

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setCGMCodigoEstagio();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setInstituicaoEnsino();
$obIFiltroComponentes->setInstituicaoIntermediadora();

$stHintContrato = 'Selecione uma das opções: Em Vigor: apenas em vigor na competência, por Data Fim do Estágio: para emitir por período de data fim ou Todos;';
$stRotuloContrato = 'Contratos';
$stNomeContrato = 'stContrato';

$obContratoEmVigor = new Radio();
$obContratoEmVigor->setRotulo($stRotuloContrato);
$obContratoEmVigor->setTitle($stHintContrato);
$obContratoEmVigor->setName($stNomeContrato);
$obContratoEmVigor->setId($stNomeContrato."EmVigor");
$obContratoEmVigor->setValue("emVigor");
$obContratoEmVigor->setLabel("Em vigor");
$obContratoEmVigor->setChecked(true);
$obContratoEmVigor->obEvento->setOnChange("montaParametrosGET('preencherSpanContrato','".$stNomeContrato."');");

$obContratoDtFimEstagio = new Radio();
$obContratoDtFimEstagio->setRotulo($stRotuloContrato);
$obContratoDtFimEstagio->setTitle($stHintContrato);
$obContratoDtFimEstagio->setName($stNomeContrato);
$obContratoDtFimEstagio->setId($stNomeContrato."DtFimEstagio");
$obContratoDtFimEstagio->setValue("dtFimEstagio");
$obContratoDtFimEstagio->setLabel("por Data Fim do Estágio");
$obContratoDtFimEstagio->obEvento->setOnChange("montaParametrosGET('preencherSpanContrato','".$stNomeContrato."');");

$obContratoTodos = new Radio();
$obContratoTodos->setRotulo($stRotuloContrato);
$obContratoTodos->setTitle($stHintContrato);
$obContratoTodos->setName($stNomeContrato);
$obContratoTodos->setId($stNomeContrato."Todos");
$obContratoTodos->setValue("todos");
$obContratoTodos->setLabel("Todos");
$obContratoTodos->obEvento->setOnChange("montaParametrosGET('preencherSpanContrato','".$stNomeContrato."');");

$obContrato = new Span();
$obContrato->setId("spnContrato");

$obHdnContrato = new Hiddeneval();
$obHdnContrato->setName("hdnContrato");
$obHdnContrato->setId("hdnContrato");

//DEFINICAO DO FORMULARIO
$obFormulario=new Formulario;
$obFormulario->addForm           ( $obForm );
$obFormulario->addTitulo         ( $obRFolhaPagamentoFolhaSituacao -> consultarCompetencia() ,"right" );
$obFormulario->addHidden         ( $obHdnAcao );
$obFormulario->addHidden         ( $obHdnCtrl );

$obIFiltroCompetencia->geraFormulario( $obFormulario );
$obIFiltroComponentes->geraFormulario( $obFormulario );

$obFormulario->agrupaComponentes(array($obContratoEmVigor,$obContratoDtFimEstagio,$obContratoTodos));
$obFormulario->addSpan($obContrato);
$obFormulario->addHidden($obHdnContrato,true);
$obFormulario->addHidden($obHdnCtrl);

$obFormulario->ok();
$obFormulario -> show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
