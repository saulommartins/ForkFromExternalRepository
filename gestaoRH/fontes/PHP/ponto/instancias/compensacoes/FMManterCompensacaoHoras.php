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
/*
    * Formulário para cadastro de compensações de horas
    * Data de Criação   : 03/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

$stPrograma = "ManterCompensacaoHoras";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//
$stAcao = $request->get('stAcao');
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             	( "stAcao"                                                              );
$obHdnAcao->setValue                            	( $stAcao                                                   );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             	( "stCtrl"                                                              );
$obHdnCtrl->setValue                            	( $stCtrl                                                               );

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

if ($stAcao == "incluir") {
    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatricula();
    $obIFiltroComponentes->setCGMMatricula();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setRegSubFunEsp();
    $obIFiltroComponentes->setGeral(false);
} else {
    $obHdnFalta = new Hidden;
    $obHdnFalta->setName("dtFaltaAntiga");
    $obHdnFalta->setValue($_GET["dt_falta"]);

    $obHdnCompensacao = new Hidden;
    $obHdnCompensacao->setName("dtCompensacaoAntiga");
    $obHdnCompensacao->setValue($_GET["dt_compensacao"]);

    $obHdnCodigo = new Hidden;
    $obHdnCodigo->setName("inCodigo");
    $obHdnCodigo->setValue($_GET["codigo"]);

    $obHdnFiltro = new Hidden;
    $obHdnFiltro->setName("stTipoFiltro");
    $obHdnFiltro->setValue($_GET["stTipoFiltro"]);

    $obLblFiltro = new Label;
    $obLblFiltro->setId("lblFiltro");
    switch ($_GET["stTipoFiltro"]) {
        case "contrato":
        case "cgm_contrato":
            $obLblFiltro->setRotulo("Matrícula");
            break;
        case "lotacao":
            $obLblFiltro->setRotulo("Lotação");
            break;
        case "local":
            $obLblFiltro->setRotulo("Local");
            break;
        case "reg_sub_fun_esp":
            $obLblFiltro->setRotulo("Regime/Subdivisão/Função");
            break;
    }
    $obLblFiltro->setValue($_GET["descricao"]);

    $dtFalta = $_GET["dt_falta"];
    $dtCompensacao = $_GET["dt_compensacao"];

    $stLocation = $pgList."?".Sessao::getId()."&stAcao=".$stAcao;
}

include_once(CAM_GRH_PON_COMPONENTES."DataCalendario.class.php");
$obDtFalta = new DataCalendario();
$obDtFalta->setRotulo("Dia da Falta");
$obDtFalta->setName("dtFalta");
$obDtFalta->setId("dtFalta");
$obDtFalta->setTitle("Informe o dia da falta para compensação.");
$obDtFalta->setNull(false);
$obDtFalta->setValue($dtFalta);
$obDtFalta->montaFuncaoBusca();

$obDtCompensacao = new DataCalendario();
$obDtCompensacao->setRotulo("Dia da Compensação");
$obDtCompensacao->setName("dtCompensacao");
$obDtCompensacao->setId("dtCompensacao");
$obDtCompensacao->setTitle("Informe o dia da compensação.");
$obDtCompensacao->setNull(false);
$obDtCompensacao->setValue($dtCompensacao);
$obDtCompensacao->montaFuncaoBusca();

//**************************************************************************************************************************//
//Define FORMULARIO
//**************************************************************************************************************************//
$obFormulario = new Formulario;
$obFormulario->addHidden                        	( $obHdnAcao                                                            );
$obFormulario->addHidden                        	( $obHdnCtrl                                                            );
$obFormulario->addTitulo 							( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right" 	);
$obFormulario->addForm								( $obForm 																);
if ($stAcao == "incluir") {
    $obIFiltroComponentes->geraFormulario($obFormulario);
} else {
    $obFormulario->addHidden($obHdnFalta);
    $obFormulario->addHidden($obHdnCompensacao);
    $obFormulario->addHidden($obHdnFiltro);
    $obFormulario->addHidden($obHdnCodigo);
    $obFormulario->addComponente($obLblFiltro);
}
$obFormulario->addTitulo("Dados da Compensação de Horas");
$obFormulario->addComponente($obDtFalta);
$obFormulario->addComponente($obDtCompensacao);
if ($stAcao == "incluir") {
    $obFormulario->ok();
} else {
    $obFormulario->cancelar($stLocation);
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
