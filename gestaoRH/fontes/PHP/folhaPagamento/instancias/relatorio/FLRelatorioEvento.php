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
    * Filtro de Emitir Relatório de Evento
    * Data de Criação: 11/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30547 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.05.33
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                              );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                    );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioEvento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgOculF= "OC".$stPrograma."Filtro.php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once($pgJS  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( CAM_FW_POPUPS."relatorio/OCRelatorio.php"                 );
$obForm->setTarget                              ( "oculto"                                                  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                  );
$obHdnCtrl->setValue                            ( $stCtrl                                                   );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName                          ( "stCaminho"                                               );
$obHdnCaminho->setValue                         ( CAM_GRH_FOL_INSTANCIAS."relatorio/OCRelatorioEvento.php"  );

$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

$obTxtCodigoInicial = new TextBox;
$obTxtCodigoInicial->setRotulo                  ( "Código Inicial"                                          );
$obTxtCodigoInicial->setName                    ( "inCodEventoInicial"                                      );
$obTxtCodigoInicial->setValue                   ( $inCodEventoInicial                                       );
$obTxtCodigoInicial->setTitle                   ( "Informe o código do evento para o filtro."               );
$obTxtCodigoInicial->setMascara                 ( $stMascaraEvento                                          );
$obTxtCodigoInicial->setPreencheComZeros        ( "E"                                                       );
$obTxtCodigoInicial->setInteiro                 ( true                                                      );

$obTxtCodigoFinal = new TextBox;
$obTxtCodigoFinal->setRotulo                    ( "Código Final"                                            );
$obTxtCodigoFinal->setName                      ( "inCodEventoFinal"                                        );
$obTxtCodigoFinal->setValue                     ( $inCodEventoFinal                                         );
$obTxtCodigoFinal->setTitle                     ( "Informe o código do evento para o filtro."               );
$obTxtCodigoFinal->setMascara                   ( $stMascaraEvento                                          );
$obTxtCodigoFinal->setPreencheComZeros          ( "E"                                                       );
$obTxtCodigoFinal->setInteiro                   ( true                                                      );

$obChkProvento = new CheckBox;
$obChkProvento->setName                         ( "boProvento"                                              );
$obChkProvento->setValue                        ( "P"                                                       );
$obChkProvento->setLabel                        ( "Proventos"                                               );
$obChkProvento->setRotulo                       ( "Natureza"                                                );
$obChkProvento->setTitle                        ( "Selecione a natureza do evento para o filtro."           );
$obChkProvento->setChecked                      ( false                                                     );

$obChkDesconto = new CheckBox;
$obChkDesconto->setName                         ( "boDesconto"                                              );
$obChkDesconto->setValue                        ( "D"                                                       );
$obChkDesconto->setLabel                        ( "Descontos"                                               );
$obChkDesconto->setRotulo                       ( "Natureza"                                                );
$obChkDesconto->setTitle                        ( "Selecione a natureza do evento para o filtro."           );
$obChkDesconto->setChecked                      ( false                                                     );

$obChkInformativo = new CheckBox;
$obChkInformativo->setName                      ( "boInformativo"                                           );
$obChkInformativo->setValue                     ( "I"                                                       );
$obChkInformativo->setLabel                     ( "Informativo"                                             );
$obChkInformativo->setRotulo                    ( "Natureza"                                                );
$obChkInformativo->setTitle                     ( "Selecione a natureza do evento para o filtro."           );
$obChkInformativo->setChecked                   ( false                                                     );

$obChkBase = new CheckBox;
$obChkBase->setName                             ( "boBase"                                                  );
$obChkBase->setValue                            ( "B"                                                       );
$obChkBase->setLabel                            ( "Base"                                                    );
$obChkBase->setRotulo                           ( "Natureza"                                                );
$obChkBase->setTitle                            ( "Selecione a natureza do evento para o filtro."           );
$obChkBase->setChecked                          ( false                                                     );

$arNatureza = array($obChkProvento,$obChkDesconto,$obChkInformativo,$obChkBase);

$obChkFixo = new CheckBox;
$obChkFixo->setName                             ( "boFixo"                                                  );
$obChkFixo->setValue                            ( "F"                                                       );
$obChkFixo->setLabel                            ( "Fixo"                                                    );
$obChkFixo->setRotulo                           ( "Tipo"                                                    );
$obChkFixo->setTitle                            ( "Selecione o tipo do evento para o filtro."               );
$obChkFixo->setChecked                          ( false                                                     );

$obChkVariavel = new CheckBox;
$obChkVariavel->setName                         ( "boVariavel"                                              );
$obChkVariavel->setValue                        ( "V"                                                       );
$obChkVariavel->setLabel                        ( "Variável"                                                );
$obChkVariavel->setRotulo                       ( "Tipo"                                                    );
$obChkVariavel->setTitle                        ( "Selecione o tipo do evento para o filtro."               );
$obChkVariavel->setChecked                      ( false                                                     );

$arTipo = array($obChkFixo,$obChkVariavel);

$obChkValor = new CheckBox;
$obChkValor->setName                            ( "boValor"                                                 );
$obChkValor->setValue                           ( "V"                                                       );
$obChkValor->setLabel                           ( "Valor"                                                   );
$obChkValor->setRotulo                          ( "Fixado"                                                  );
$obChkValor->setTitle                           ( "Selecione se o evento é fixado por valor e ou quantidade." );
$obChkValor->setChecked                         ( false                                                     );

$obChkQuantidade = new CheckBox;
$obChkQuantidade->setName                       ( "boQuantidade"                                            );
$obChkQuantidade->setValue                      ( "Q"                                                       );
$obChkQuantidade->setLabel                      ( "Quantidade"                                              );
$obChkQuantidade->setRotulo                     ( "Fixado"                                                  );
$obChkQuantidade->setTitle                      ( "Selecione se o evento é fixado por valor e ou quantidade." );
$obChkQuantidade->setChecked                    ( false                                                     );

$arFixado = array($obChkValor,$obChkQuantidade);

$obRFolhaPagamentoEvento = new RFolhaPagamentoEvento();
$obRFolhaPagamentoEvento->addConfiguracaoEvento();
$obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento($rsCaracteristicas);
$obCmbCaracteristica = new Select;
$obCmbCaracteristica->setName                   ( "inCodConfiguracao"                                       );
$obCmbCaracteristica->setValue                  ( $inCodConfiguracao                                        );
$obCmbCaracteristica->setRotulo                 ( "Característica"                                          );
$obCmbCaracteristica->setTitle                  ( "Selecione a característica do evento."                   );
$obCmbCaracteristica->setCampoId                ( "cod_configuracao"                                        );
$obCmbCaracteristica->setCampoDesc              ( "descricao"                                               );
$obCmbCaracteristica->addOption                 ( "", "Selecione"                                           );
$obCmbCaracteristica->preencheCombo             ( $rsCaracteristicas                                        );
$obCmbCaracteristica->setStyle                  ( "width: 250px"                                            );

$obTxtSequencia = new TextBox;
$obTxtSequencia->setRotulo                      ( "Sequência de Cálculo"                                    );
$obTxtSequencia->setName                        ( "inCodSequencia"                                          );
$obTxtSequencia->setValue                       ( $inCodSequencia                                           );
$obTxtSequencia->setTitle                       ( "Selecione a qual sequência de cálculo o evento pertence.");
$obTxtSequencia->setSize                        ( 10                                                        );
$obTxtSequencia->setMaxLength                   ( 8                                                         );
$obTxtSequencia->setInteiro                     ( true                                                      );
$obTxtSequencia->setNull                        ( true                                                      );
//$obTxtSequencia->obEvento->setOnChange          ( "buscaValor('preencherSubDivisao');"                      );

$obRFolhaPagamentoEvento->obRFolhaPagamentoSequencia->listarSequencia( $rsSequencia );

$obCmbSequencia = new Select;
$obCmbSequencia->setName                         ( "stSequencia"                                             );
$obCmbSequencia->setValue                        ( $inCodSequencia                                           );
$obCmbSequencia->setRotulo                       ( "Sequência de Cálculo"                                    );
$obCmbSequencia->setTitle                        ( "Selecione a qual sequência de cálculo o evento pertence.");
$obCmbSequencia->setCampoId                      ( "cod_sequencia"                                           );
$obCmbSequencia->setCampoDesc                    ( "descricao"                                               );
$obCmbSequencia->addOption                       ( "", "Selecione"                                           );
$obCmbSequencia->preencheCombo                   ( $rsSequencia                                              );
$obCmbSequencia->setStyle                        ( "width: 250px"                                            );

$obTxtRegime = new TextBox;
$obTxtRegime->setRotulo                         ( "Regime"                                                  );
$obTxtRegime->setName                           ( "inCodRegime"                                             );
$obTxtRegime->setValue                          ( $inCodRegime                                              );
$obTxtRegime->setTitle                          ( "Selecione o regime para o filtro."                       );
$obTxtRegime->setSize                           ( 10                                                        );
$obTxtRegime->setMaxLength                      ( 8                                                         );
$obTxtRegime->setInteiro                        ( true                                                      );
$obTxtRegime->obEvento->setOnChange             ( "buscaValor('preencherSubDivisao');"                      );

$obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
$obRFolhaPagamentoPeriodoContratoServidor->obRPessoalRegime->listarRegime( $rsRegime );

$obCmbRegime = new Select;
$obCmbRegime->setName                           ( "stRegime"                                                );
$obCmbRegime->setValue                          ( $inCodRegime                                              );
$obCmbRegime->setRotulo                         ( "Regime"                                                  );
$obCmbRegime->setTitle                          ( "Selecione o regime para o filtro."                       );
$obCmbRegime->setCampoId                        ( "[cod_regime]"                                            );
$obCmbRegime->setCampoDesc                      ( "descricao"                                               );
$obCmbRegime->addOption                         ( "", "Selecione"                                           );
$obCmbRegime->preencheCombo                     ( $rsRegime                                                 );
$obCmbRegime->setStyle                          ( "width: 250px"                                            );
$obCmbRegime->obEvento->setOnChange             ( "buscaValor('preencherSubDivisao');"                      );

$obTxtSubDivisao = new TextBox;
$obTxtSubDivisao->setRotulo                     ( "Subdivisão"                                              );
$obTxtSubDivisao->setName                       ( "inCodSubDivisao"                                         );
$obTxtSubDivisao->setValue                      ( $inCodSubDivisao                                          );
$obTxtSubDivisao->setTitle                      ( "Selecione a subdivisão para o filtro."                   );
$obTxtSubDivisao->setSize                       ( 10                                                        );
$obTxtSubDivisao->setMaxLength                  ( 8                                                         );
$obTxtSubDivisao->setInteiro                    ( true                                                      );
$obTxtSubDivisao->obEvento->setOnChange         ( "buscaValor('preencherFuncao');"                          );

$obCmbSubDivisao = new Select;
$obCmbSubDivisao->setName                       ( "stSubDivisao"                                            );
$obCmbSubDivisao->setValue                      ( $inCodSubDivisao                                          );
$obCmbSubDivisao->setRotulo                     ( "Subdivisão"                                              );
$obCmbSubDivisao->setTitle                      ( "Selecione a subdivisão para o filtro."                   );
$obCmbSubDivisao->setCampoId                    ( "[cod_sub_divisao]"                                       );
$obCmbSubDivisao->setCampoDesc                  ( "descricao"                                               );
$obCmbSubDivisao->addOption                     ( "", "Selecione"                                           );
$obCmbSubDivisao->setStyle                      ( "width: 250px"                                            );
$obCmbSubDivisao->obEvento->setOnChange         ( "buscaValor('preencherFuncao');"                          );

$obTxtFuncao = new TextBox;
$obTxtFuncao->setRotulo                         ( "Função"                                                  );
$obTxtFuncao->setName                           ( "inCodCargo"                                              );
$obTxtFuncao->setValue                          ( $inCodCargo                                               );
$obTxtFuncao->setTitle                          ( "Selecione a função para o filtro."                       );
$obTxtFuncao->setSize                           ( 10                                                        );
$obTxtFuncao->setMaxLength                      ( 10                                                        );
$obTxtFuncao->setInteiro                        ( true                                                      );
$obTxtFuncao->obEvento->setOnChange             ( "buscaValor('preencherEspecialidade');"                   );

$obCmbFuncao = new Select;
$obCmbFuncao->setName                           ( "stCargo"                                                 );
$obCmbFuncao->setValue                          ( $inCodCargo                                               );
$obCmbFuncao->setRotulo                         ( "Função"                                                  );
$obCmbFuncao->setTitle                          ( "Selecione a função para o filtro."                       );
$obCmbFuncao->addOption                         ( "", "Selecione"                                           );
$obCmbFuncao->setCampoId                        ( "[cod_cargo]"                                             );
$obCmbFuncao->setCampoDesc                      ( "descricao"                                               );
$obCmbFuncao->setStyle                          ( "width: 250px"                                            );
$obCmbFuncao->obEvento->setOnChange             ( "buscaValor('preencherEspecialidade');"                   );

$obTxtEspecialidade = new TextBox;
$obTxtEspecialidade->setRotulo                  ( "Especialidade"                                           );
$obTxtEspecialidade->setName                    ( "inCodEspecialidade"                                      );
$obTxtEspecialidade->setValue                   ( $inCodEspecialidade                                       );
$obTxtEspecialidade->setTitle                   ( "Selecione a especialidade para o filtro."                );
$obTxtEspecialidade->setSize                    ( 10                                                        );
$obTxtEspecialidade->setMaxLength               ( 10                                                        );
$obTxtEspecialidade->setInteiro                 ( true                                                      );

$obCmbEspecialidade = new Select;
$obCmbEspecialidade->setName                    ( "stEspecialidade"                                         );
$obCmbEspecialidade->setValue                   ( $inCodEspecialidade                                       );
$obCmbEspecialidade->setRotulo                  ( "Especialidade"                                           );
$obCmbEspecialidade->setTitle                   ( "Selecione a especialidade para o filtro."                );
$obCmbEspecialidade->setCampoId                 ( "[cod_especialidade]"                                     );
$obCmbEspecialidade->setCampoDesc               ( "descricao_especialidade"                                 );
$obCmbEspecialidade->addOption                  ( "", "Selecione"                                           );
$obCmbEspecialidade->setStyle                   ( "width: 250px"                                            );

$obChkApresentar = new CheckBox;
$obChkApresentar->setName                       ( "boApresentar"                                            );
$obChkApresentar->setValue                      ( "1"                                                       );
$obChkApresentar->setRotulo                     ( "Apresentar Função/Especi."                               );
$obChkApresentar->setLabel                      ( "Sim"                                                     );
$obChkApresentar->setTitle                      ( "Selecione se o relatório deverá apresentar ou não as funções/especialidades ligadas ao evento." );
$obChkApresentar->setChecked                    ( false                                                     );

$obIFiltroCompetencia = new IFiltroCompetencia;

$obCmbOrdenacao = new Select;
$obCmbOrdenacao->setName                        ( "stOrdenacao"                                             );
$obCmbOrdenacao->setValue                       ( $stOrdenacao                                              );
$obCmbOrdenacao->setRotulo                      ( "Ordenação"                                               );
$obCmbOrdenacao->setTitle                       ( "Selecione a ordenação."                                  );
$obCmbOrdenacao->addOption                      ( "", "Selecione"                                           );
$obCmbOrdenacao->addOption                      ( "codigo","Código do Evento"                               );
$obCmbOrdenacao->addOption                      ( "descricao","Descrição do Evento"                         );
$obCmbOrdenacao->setStyle                       ( "width: 250px"                                            );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                   );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addTitulo                        ( "Parâmetros para Emissão do Relatório"                    );
$obFormulario->addHidden                        ( $obHdnCaminho                                             );
$obFormulario->addHidden                        ( $obHdnCtrl                                                );
$obFormulario->addComponente                    ( $obTxtCodigoInicial                                       );
$obFormulario->addComponente                    ( $obTxtCodigoFinal                                         );
$obFormulario->agrupaComponentes                ( $arNatureza                                               );
$obFormulario->agrupaComponentes                ( $arTipo                                                   );
$obFormulario->agrupaComponentes                ( $arFixado                                                 );
$obFormulario->addComponente                    ( $obCmbCaracteristica                                      );
$obFormulario->addComponenteComposto            ( $obTxtSequencia,$obCmbSequencia                           );
$obFormulario->addTitulo                        ( "Função/Especialidade"                                    );
$obFormulario->addComponenteComposto            ( $obTxtRegime,$obCmbRegime                                 );
$obFormulario->addComponenteComposto            ( $obTxtSubDivisao,$obCmbSubDivisao                         );
$obFormulario->addComponenteComposto            ( $obTxtFuncao,$obCmbFuncao                                 );
$obFormulario->addComponenteComposto            ( $obTxtEspecialidade,$obCmbEspecialidade                   );
$obFormulario->addComponente                    ( $obChkApresentar                                          );
$obIFiltroCompetencia->geraFormulario           ( $obFormulario                                             );
$obFormulario->addComponente                    ( $obCmbOrdenacao                                           );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
