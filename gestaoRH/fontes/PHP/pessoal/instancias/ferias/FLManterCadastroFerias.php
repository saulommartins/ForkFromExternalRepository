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
    * Pï¿½gina de Filtro do Fï¿½rias
    * Data de Criaï¿½ï¿½o: 07/06/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.04.22

    $Id: FLManterCadastroFerias.php 63818 2015-10-19 20:02:07Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCadastroFerias";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$jsOnload = "montaParametrosGET('alterarPost','boConcederFeriasLote,stAcao');";

include_once($pgJS);

Sessao::remove('link');
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obChkApresentarSomenteFerias = new Checkbox;
$obChkApresentarSomenteFerias->setRotulo        ( "Apresentar Somente Férias Vencidas"                                  );
$obChkApresentarSomenteFerias->setName          ( "boApresentarSomenteFerias"                                           );
$obChkApresentarSomenteFerias->setTitle         ( "Selecione se deverão ser listados apenas contratos com férias vencidas."    );
$obChkApresentarSomenteFerias->setValue         ( true                                                                  );

$obChkConcederFeriasLote = new Checkbox;
switch ($stAcao) {
    case "incluir":
        $obChkConcederFeriasLote->setRotulo        ( "Conceder Férias em Lote"                                         );
        break;
    case "consultar":
        $obChkConcederFeriasLote->setRotulo        ( "Consultar Férias em Lote"                                        );
        break;
    case "excluir":
        $obChkConcederFeriasLote->setRotulo        ( "Cancelar Férias em Lote"                                         );
        break;
}
$obChkConcederFeriasLote->setName          ( "boConcederFeriasLote"                                                    );
$obChkConcederFeriasLote->setTitle         ( "Selecione para gerar férias em lote."                                    );
$obChkConcederFeriasLote->setValue         ( true                                                                      );
$obChkConcederFeriasLote->obEvento->setOnChange("if (this.checked == true) {this.value=true;} else {this.value=false;}montaParametrosGET('alterarPost','boConcederFeriasLote,stAcao');"         );

$obSpnConcederFeriasLote = new Span();
$obSpnConcederFeriasLote->setId("spnConcederFeriasLote");

$obHdnConcederFeriasLote = new HiddenEval();
$obHdnConcederFeriasLote->setName("hdnConcederFeriasLote");

$obDtPeriodicidade = new Periodicidade();

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCgmMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setFuncao();
Sessao::write('valida_ativos_cgm', 'true');
if ($stAcao == "consultar") {
    $obIFiltroComponentes->setTodos();
    Sessao::write('valida_ativos_cgm', 'false');
}

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgList                                                               );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obIFiltroComponentes->geraFormulario($obFormulario);
if ($stAcao == "incluir") {
    $obFormulario->addComponente                ( $obChkApresentarSomenteFerias                                         );
}
$obFormulario->addComponente                ( $obChkConcederFeriasLote                                              );
$obFormulario->addSpan($obSpnConcederFeriasLote);
$obFormulario->addHidden($obHdnConcederFeriasLote,true);
if ($stAcao == "consultar") {
    $obFormulario->addComponente                ( $obDtPeriodicidade                                                    );
}
$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
