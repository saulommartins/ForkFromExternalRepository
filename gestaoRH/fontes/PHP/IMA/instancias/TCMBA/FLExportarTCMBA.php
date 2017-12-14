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
    * Página de Formulário do Exportação Arquivo TCM/BA
    * Data de Criação: 19/03/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30547 $
    $Name$
    $Author: souzadl $
    $Date: 2007-04-19 12:04:12 -0300 (Qui, 19 Abr 2007) $

    * Casos de uso: uc-04.08.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php" );
//include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenio.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ExportarTCMBA";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
//$jsOnload   = "executaFuncaoAjax('processarForm');";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stAcao      = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "oculto"                                                              );

$obCmbFiltrar = new Select;
$obCmbFiltrar->setRotulo                        ( "Opções"                                                             );
$obCmbFiltrar->setTitle                         ( "Selecione o tipo de filtro a ser utilizado para exportação do arquivo TCM/BA."                                         );
$obCmbFiltrar->setName                          ( "stTipoFiltro"                                                        );
$obCmbFiltrar->setValue                         ( "geral"                                                         );
$obCmbFiltrar->setNull(false);
$obCmbFiltrar->setStyle                         ( "width: 200px"                                                        );
$obCmbFiltrar->addOption                        ( "", "Selecione"                                                       );
$obCmbFiltrar->addOption                        ( "lotacao","Lotação"                                                   );
$obCmbFiltrar->addOption                        ( "local","Local"                                                       );
$obCmbFiltrar->addOption                        ( "atributos","Atributos"                                                       );
$obCmbFiltrar->addOption                        ( "geral","Geral"                                                       );
$obCmbFiltrar->obEvento->setOnChange            ( "montaParametrosGET('gerarSpan','stTipoFiltro');"                     );

$obSpnFiltro = new Span;
$obSpnFiltro->setid                             ( "spnFiltro"                                                           );

$obHdnFiltro = new hiddenEval();
$obHdnFiltro->setName("hdnFiltro");

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAExportacaoTCMBA.class.php");
$obTIMAExportacaoTCMBA = new TIMAExportacaoTCMBA();
$obTIMAExportacaoTCMBA->recuperaTodos($rsConfiguracaoTCMBA);
if ($rsConfiguracaoTCMBA->getNumLinhas() == 1) {
    switch ($rsConfiguracaoTCMBA->getCampo("cod_entidade")) {
        case "P":
            $stCodigoEntidade = "P - Prefeitura";
            break;
        case "C":
            $stCodigoEntidade = "C - Câmara";
            break;
        case "D":
            $stCodigoEntidade = "D - Descentralizada";
            break;
    }
    $stNumeroEntidade = $rsConfiguracaoTCMBA->getCampo("num_entidade");
} else {
    $stCodigoEntidade = $stNumeroEntidade = "Configurar informações TCM/BA";
}

$obLblCodigoEntidade = new Label();
$obLblCodigoEntidade->setRotulo("Código da Entidade");
$obLblCodigoEntidade->setId("stCodigoEntidade");
$obLblCodigoEntidade->setValue($stCodigoEntidade);

$obLblNumeroEntidade = new Label();
$obLblNumeroEntidade->setRotulo("Número da Entidade");
$obLblNumeroEntidade->setId("stNumeroEntidade");
$obLblNumeroEntidade->setValue($stNumeroEntidade);

$obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);
$obIFiltroCompetencia->obCmbMes->obEvento->setOnChange("montaParametrosGET('processarCompetencia','inCodMes,inAno');");
$obIFiltroCompetencia->obTxtAno->obEvento->setOnChange($obIFiltroCompetencia->obTxtAno->obEvento->getOnChange()."montaParametrosGET('processarCompetencia','inCodMes,inAno');");

$obSpnCompetencia13 = new Span;
$obSpnCompetencia13->setId                             ( "spnCompetencia13"                                                           );

$obCmbEnvio = new Select;
$obCmbEnvio->setRotulo                        ( "Tipo de Envio"                                             );
$obCmbEnvio->setTitle                         ( "Selecione o tipo de envio do arquivo."                     );
$obCmbEnvio->setName                          ( "inTipoEnvio"                                               );
$obCmbEnvio->setValue                         ( "1"                                                         );
$obCmbEnvio->setNull(false);
$obCmbEnvio->setStyle                         ( "width: 200px"                                              );
$obCmbEnvio->addOption                        ( "", "Selecione"                                             );
$obCmbEnvio->addOption                        ( "1","Inclusão"                                              );
$obCmbEnvio->addOption                        ( "2","Substituição"                                          );

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter','',true);");

$obBtnLimpar = new Button();
$obBtnLimpar->setValue("Limpar");
$obBtnLimpar->obEvento->setOnClick("montaParametrosGET('limparFiltro','stTipoFiltro',true);");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addTitulo                        ( "Seleção do Filtro"                                                   );
$obFormulario->addComponente                    ( $obCmbFiltrar                                                         );
$obFormulario->addSpan($obSpnFiltro);
$obFormulario->addHidden($obHdnFiltro,true);
$obFormulario->addComponente($obLblCodigoEntidade);
$obFormulario->addComponente($obLblNumeroEntidade);
$obIFiltroCompetencia->geraFormulario($obFormulario);
$obFormulario->addSpan($obSpnCompetencia13);
$obFormulario->addComponente($obCmbEnvio);
$obFormulario->defineBarra(array($obBtnOk,$obBtnLimpar));
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
