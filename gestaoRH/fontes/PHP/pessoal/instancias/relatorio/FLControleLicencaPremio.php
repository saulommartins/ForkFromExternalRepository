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
    * Arquivo de Filtro de Controle de Licença Prêmio
    * Data de Criação: 17/10/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: FLControleLicencaPremio.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.04.18
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );

//Define o nome dos arquivos PHP
$stPrograma = "ControleLicencaPremio";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

Sessao::remove('link');
Sessao::remove('arContratos');
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                                 ( "stAcao"                                          );
$obHdnAcao->setValue                                ( $stAcao                                           );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                                  ( $pgProc                                           );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$oIFiltroComponentes = new IFiltroComponentes();
$oIFiltroComponentes->setMatricula();
$oIFiltroComponentes->setCGMMatricula();
$oIFiltroComponentes->setLotacao();
$oIFiltroComponentes->setLocal();
$oIFiltroComponentes->setRegimeSubDivisaoFuncao();
$oIFiltroComponentes->setAtributoServidor();
$oIFiltroComponentes->setGrupoLotacao();
$oIFiltroComponentes->setGrupoLocal();
$oIFiltroComponentes->setGrupoRegimeSubDivisaoFuncao();
$oIFiltroComponentes->setGrupoAtributoServidor();

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalClassificacaoAssentamento.class.php");
$obTPessoalClassificacaoAssentamento = new TPessoalClassificacaoAssentamento();
$obTPessoalClassificacaoAssentamento->recuperaClassificacaoAssentamentoLicencaPremio( $rsClassificacao );

//Define objeto TEXTBOX para informar o CODIGO da classificação
$obTxtCodClassificao = new TextBox;
$obTxtCodClassificao->setRotulo                 ( "Classificação Assentamento"                                                  );
$obTxtCodClassificao->setTitle                  ( "Selecione a classificação do assentamento da licença prêmio."                        );
$obTxtCodClassificao->setName                   ( "inCodClassificacaoTxt"                                           );
$obTxtCodClassificao->setId                     ( "inCodClassificacaoTxt"                                           );
$obTxtCodClassificao->setSize                   ( 10                                                                );
$obTxtCodClassificao->setMaxLength              ( 10                                                                );
$obTxtCodClassificao->setInteiro                ( true                                                              );
$obTxtCodClassificao->setNull(false);
$obTxtCodClassificao->obEvento->setOnChange     ( "montaParametrosGET('preencherAssentamento','inCodClassificacao');"                            );

//Define objeto SELECT para listar a DESCRIÇÂO da classificação
$obCmbClassificao = new Select;
$obCmbClassificao->setRotulo                    ( "Classificação Assentamento"                                                  );
$obCmbClassificao->setTitle                     ( "Selecione a classificação do assentamento da licença prêmio."                        );
$obCmbClassificao->setName                      ( "inCodClassificacao"                                              );
$obCmbClassificao->setId                        ( "inCodClassificacao"                                              );
$obCmbClassificao->setStyle                     ( "width: 200px"                                                    );
$obCmbClassificao->addOption                    ( "", "Selecione"                                                   );
$obCmbClassificao->setCampoID                   ( "cod_classificacao"                                               );
$obCmbClassificao->setCampoDesc                 ( "descricao"                                                       );
$obCmbClassificao->preencheCombo                ( $rsClassificacao                                                  );
$obCmbClassificao->setNull(false);
$obCmbClassificao->obEvento->setOnChange        ( "montaParametrosGET('preencherAssentamento','inCodClassificacao');"                            );

//Define objeto TEXTBOX para informar o CODIGO da classificação
$obTxtCodAssentamento = new TextBox;
$obTxtCodAssentamento->setRotulo                ( "Assentamento"                                                   );
$obTxtCodAssentamento->setTitle                 ( "Selecione assentamento de licença prêmio previamente cadastrado e configuracao."                            );
$obTxtCodAssentamento->setName                  ( "inCodAssentamentoTxt"                                            );
$obTxtCodAssentamento->setId                    ( "inCodAssentamentoTxt"                                            );
$obTxtCodAssentamento->setSize                  ( 10                                                                );
$obTxtCodAssentamento->setMaxLength             ( 10                                                                );
$obTxtCodAssentamento->setInteiro               ( true                                                              );
$obTxtCodAssentamento->setNull(false);

//Define objeto SELECT para listar a DESCRIÇÂO do motivo
$obCmbAssentamento = new Select;
$obCmbAssentamento->setRotulo                   ( "Assentamento"                                                   );
$obCmbAssentamento->setTitle                    ( "Selecione assentamento de licença prêmio previamente cadastrado e configuracao."                            );
$obCmbAssentamento->setName                     ( "inCodAssentamento"                                               );
$obCmbAssentamento->setId                       ( "inCodAssentamento"                                               );
$obCmbAssentamento->setStyle                    ( "width: 200px"                                                    );
$obCmbAssentamento->addOption                   ( "", "Selecione"                                                   );
$obCmbAssentamento->setNull(false);

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

$obDtFinalLeitura = new Data();
$obDtFinalLeitura->setRotulo("Data Final de Leitura");
$obDtFinalLeitura->setName("dtFinalLeitura");
$obDtFinalLeitura->setTitle("Informe a data final para leitura do tempo.");
$obDtFinalLeitura->setValue($rsPeriodoMovimentacao->getCampo("dt_final"));
$obDtFinalLeitura->setNull(false);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo                            ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addForm                              ( $obForm                                           );
$obFormulario->addHidden                            ( $obHdnAcao                                        );
$oIFiltroComponentes->geraFormulario($obFormulario);
$obFormulario->addComponenteComposto($obTxtCodClassificao,$obCmbClassificao);
$obFormulario->addComponenteComposto($obTxtCodAssentamento,$obCmbAssentamento);
$obFormulario->addComponente($obDtFinalLeitura);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
