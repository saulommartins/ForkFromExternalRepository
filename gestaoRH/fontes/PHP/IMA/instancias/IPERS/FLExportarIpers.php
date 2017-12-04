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
    * Página de Formulário do Exportação Arquivo Ipers
    * Data de Criação: 25/06/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: FLExportarIpers.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.08.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php"                                     );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoIpe.class.php"                        );

//Define o nome dos arquivos PHP
$stPrograma = "ExportarIpers";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
include_once($pgJS);
$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stAcao      = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);

$obTFolhaPagamentoConfiguracaoIpe = new TFolhaPagamentoConfiguracaoIpe();
$obTFolhaPagamentoConfiguracaoIpe->setDado('cod_periodo_movimentacao', $rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'));
$obTFolhaPagamentoConfiguracaoIpe->recuperaTodosVigencia($rsConfiguracaoIpe, '', ' ORDER BY cod_configuracao DESC LIMIT 1 ');

if ( $rsConfiguracaoIpe->getNumLinhas() > 0 ) {

    $jsOnload   = "montaParametrosGET('gerarSpan','stSituacao');";

    //DEFINICAO DOS COMPONENTES
    $obHdnAcao =  new Hidden;
    $obHdnAcao->setName                             ( "stAcao"                                                              );
    $obHdnAcao->setValue                            ( $stAcao                                                               );

    $obHdnCtrl =  new Hidden;
    $obHdnCtrl->setName                             ( "stCtrl"                                                              );
    $obHdnCtrl->setValue                            ( $stCtrl                                                               );

    $obHdnTipoFiltroExtra = new hiddenEval();
    $obHdnTipoFiltroExtra->setName("hdnTipoFiltroExtra");
    $obHdnTipoFiltroExtra->setValue("eval(document.frm.hdnTipoFiltro.value);");

    //DEFINICAO DO FORM
    $obForm = new Form;
    $obForm->setAction                              ( $pgProc                                                               );
    $obForm->setTarget                              ( "oculto"                                                              );

    $obBtnOk = new Ok();
    $obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter','',true);");

    $obBtnLimpar = new Limpar();
    $obBtnLimpar->obEvento->setOnClick("executaFuncaoAjax('limparForm');");

    $obComboTipoEmissao = new Select;
    $obComboTipoEmissao->setRotulo                         ( "Tipo de Emissão"                                  );
    $obComboTipoEmissao->setTitle                          ( "Selecione o cadastro para filtro."                );
    $obComboTipoEmissao->setName                           ( "inCodTipoEmissao"                                 );
    $obComboTipoEmissao->setValue                          ( "1"                                                );
    $obComboTipoEmissao->setStyle                          ( "width: 200px"                                     );
    $obComboTipoEmissao->addOption                         ( "", "Selecione"                                    );
    $obComboTipoEmissao->addOption                         ( "1", "Manutenção"                                  );
    $obComboTipoEmissao->addOption                         ( "2", "Acerto de Manutenção"                        );
    $obComboTipoEmissao->addOption                         ( "3", "Inclusão"                                    );
    $obComboTipoEmissao->addOption                         ( "4", "Acerto de Inclusão"                          );
    $obComboTipoEmissao->setNull                           ( false                                              );

    $obComboSituacao = new Select;
    $obComboSituacao->setRotulo                         ( "Cadastro"                                            );
    $obComboSituacao->setTitle                          ( "Selecione o cadastro para filtro."                   );
    $obComboSituacao->setName                           ( "stSituacao"                                          );
    $obComboSituacao->setValue                          ( "todos"                                               );
    $obComboSituacao->setStyle                          ( "width: 200px"                                        );
    $obComboSituacao->addOption                         ( "", "Selecione"                                       );
    $obComboSituacao->addOption                         ( "ativos", "Ativos"                                    );
    $obComboSituacao->addOption                         ( "aposentados", "Aposentados"                          );
    $obComboSituacao->addOption                         ( "pensionistas", "Pensionistas"                        );
    $obComboSituacao->addOption                         ( "rescindidos", "Rescindidos"                          );
    $obComboSituacao->addOption                         ( "todos", "Todos"                                      );
    $obComboSituacao->setNull                           ( false                                                 );
    $obComboSituacao->obEvento->setOnChange("montaParametrosGET('gerarSpan','stSituacao');");

    $obRdnJuntarCalculoSim = new Radio();
    $obRdnJuntarCalculoSim->setName     ('stJuntarCalculo');
    $obRdnJuntarCalculoSim->setValue    ('sim');
    $obRdnJuntarCalculoSim->setRotulo   ('Juntar todos tipos de cálculo');
    $obRdnJuntarCalculoSim->setLabel    ('Sim');
    $obRdnJuntarCalculoSim->obEvento->setOnChange("javascript: juntarCalculo('sim');");

    $obRdnJuntarCalculoNao = new Radio();
    $obRdnJuntarCalculoNao->setName     ('stJuntarCalculo');
    $obRdnJuntarCalculoNao->setValue    ('nao');
    $obRdnJuntarCalculoNao->setRotulo   ('Juntar todos tipos de cálculo');
    $obRdnJuntarCalculoNao->setLabel    ('Não');
    $obRdnJuntarCalculoNao->setChecked  (true);
    $obRdnJuntarCalculoNao->obEvento->setOnChange("javascript: juntarCalculo('nao');");

    $obSpnCadastro = new Span();
    $obSpnCadastro->setId("spnCadastro");

    $obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);

    $obIFiltroTipoFolha = new IFiltroTipoFolha();
    $obIFiltroTipoFolha->setMostraDesdobramento(true,"D");
    $obIFiltroTipoFolha->setValorPadrao(1);

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario();
    $obFormulario->addForm                          ( $obForm                                                               );
    $obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
    $obFormulario->addHidden                        ( $obHdnAcao                                                            );
    $obFormulario->addHidden                        ( $obHdnCtrl                                                            );
    $obFormulario->addHidden                        ( $obHdnTipoFiltroExtra,true											);
    $obFormulario->addTitulo( "Dados de Emissão do Arquivo" ,"left");
    $obFormulario->addComponente($obComboTipoEmissao);
    $obFormulario->addComponente($obComboSituacao);
    $obFormulario->addSpan($obSpnCadastro);
    $obIFiltroCompetencia->geraFormulario($obFormulario);
    $obFormulario->agrupaComponentes(array($obRdnJuntarCalculoSim,$obRdnJuntarCalculoNao));
    $obIFiltroTipoFolha->geraFormulario($obFormulario);
    $obFormulario->defineBarra( array($obBtnOk,$obBtnLimpar) );
    $obFormulario->show();

} else {
    $obLblMensagem = new Label();
    $obLblMensagem->setRotulo("Mensagem");

    if ($rsConfiguracaoIpe->getNumLinhas() > 0) {
        $obLblMensagem->setValue("A vigência existente para a configuração do IPERS é superior à data fim da competência atual, por favor revise a configuração.");
    } else {
        $obLblMensagem->setValue("A configuração do IPERS não foi realizada, essa configuração é necessária para a geração do arquivo.");
    }

    $obFormulario = new Formulario;
    $obFormulario->addTitulo( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
    $obFormulario->addComponente($obLblMensagem);
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
