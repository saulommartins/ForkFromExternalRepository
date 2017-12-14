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
    * Filtro
    * Data de Criação: 13/12/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30547 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-19 15:41:28 -0300 (Qua, 19 Mar 2008) $

    * Casos de uso: uc-04.05.35
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                    );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"					);
include_once (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"      );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioAuditoriaFolha";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                   );
$obForm->setTarget                              ( "telaPrincipal"                                           );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                  );
$obHdnCtrl->setValue                            ( $stCtrl                                                   );

$obChkProvento = new CheckBox;
$obChkProvento->setName                         ( "boProvento"                                              );
$obChkProvento->setValue                        ( "P"                                                       );
$obChkProvento->setLabel                        ( "Proventos"                                               );
$obChkProvento->setRotulo                       ( "Eventos a analisar"                                      );
$obChkProvento->setTitle                        ( "Selecione a natureza do evento para o filtro."           );
$obChkProvento->setChecked                      ( false                                                     );

$obChkDesconto = new CheckBox;
$obChkDesconto->setName                         ( "boDesconto"                                              );
$obChkDesconto->setValue                        ( "D"                                                       );
$obChkDesconto->setLabel                        ( "Descontos"                                               );
$obChkDesconto->setRotulo                       ( "Eventos a analisar"                                      );
$obChkDesconto->setTitle                        ( "Selecione a natureza do evento para o filtro."           );
$obChkDesconto->setChecked                      ( false                                                     );

$obChkInformativo = new CheckBox;
$obChkInformativo->setName                      ( "boInformativo"                                           );
$obChkInformativo->setValue                     ( "I"                                                       );
$obChkInformativo->setLabel                     ( "Informativo"                                             );
$obChkInformativo->setRotulo                    ( "Eventos a analisar"                                      );
$obChkInformativo->setTitle                     ( "Selecione a natureza do evento para o filtro."           );
$obChkInformativo->setChecked                   ( false                                                     );

$obChkBase = new CheckBox;
$obChkBase->setName                             ( "boBase"                                                  );
$obChkBase->setValue                            ( "B"                                                       );
$obChkBase->setLabel                            ( "Base"                                                    );
$obChkBase->setRotulo                           ( "Eventos a analisar"                                      );
$obChkBase->setTitle                            ( "Selecione a natureza do evento para o filtro."           );
$obChkBase->setChecked                          ( false                                                     );

$arNatureza = array($obChkProvento,$obChkDesconto,$obChkInformativo,$obChkBase);

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setGrupoLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setGrupoLocal();
$obIFiltroComponentes->setEventoMultiplo();
$obIFiltroComponentes->setTodos();

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimpar"                                                 );
$obBtnLimpar->setValue                          ( "Limpar"                                                    );
$obBtnLimpar->setTipo                           ( "button"                                                    );
$obBtnLimpar->obEvento->setOnClick              ( "montaParametrosPOST('limparFormulario','', true);"          );

$obBtnOk = new OK;
$obBtnOk->obEvento->setOnClick                  ( "montaParametrosPOST('OK','',true);"                         );

$obRdoTipoCalculo = new Radio;
$obRdoTipoCalculo->setName                      ( "stEmitir"                                  		      );
$obRdoTipoCalculo->setTitle                     ( "Informe se prefere emitir relatório de um cálculo ou de todos os tipos de cálculo. Exemplo: salário, férias, décimo terceiro, rescisão." );
$obRdoTipoCalculo->setRotulo                    ( "Emitir"                               		      );
$obRdoTipoCalculo->setLabel                     ( "por Tipo de Cálculo"                                       );
$obRdoTipoCalculo->setValue                     ( "tipo_calculo"                                              );
$obRdoTipoCalculo->setChecked                   ( $stEmitir == 'tipo_calculo' || !$stEmitir		      );
$obRdoTipoCalculo->obEvento->setOnChange	    ("montaParametrosGET('gerarSpanFiltroFolha','stEmitir');" );

$obRdoTodasOcorrencias = new Radio;
$obRdoTodasOcorrencias->setName                  ( "stEmitir"                                  	              );
$obRdoTodasOcorrencias->setTitle                 ( "Informe se prefere emitir relatório de um cálculo ou de todos os tipos de cálculo. Exemplo: salário, férias, décimo terceiro, rescisão."  );
$obRdoTodasOcorrencias->setRotulo                ( "Emitir"                               		      );
$obRdoTodasOcorrencias->setLabel                 ( "Todas Ocorrências de Cálculo"                             );
$obRdoTodasOcorrencias->setValue                 ( "todas_ocorrencias"                                        );
$obRdoTodasOcorrencias->setChecked               ( $stEmitir == 'todas_ocorrencias'			      );
$obRdoTodasOcorrencias->obEvento->setOnChange("montaParametrosGET('gerarSpanFiltroFolha','stEmitir');"	      );

$competenciaMesAtual = pegaDado("dt_inicial","folhapagamento.periodo_movimentacao","order by 1 DESC LIMIT 1");
$competenciaMesAtual = SistemaLegado::dataToBr($competenciaMesAtual);
//metodo para subtrair ou somar dias meses ou anos apartir de uma dada passada por parametro
$competenciaMesAnterior = SistemaLegado::somaOuSubtraiData($competenciaMesAtual,false,1,'month');

$obIFiltroCompetenciaInicial = new IFiltroCompetencia(true,$competenciaMesAnterior,true);
$obIFiltroCompetenciaInicial->setRotulo("Competência de Comparação");
$obIFiltroCompetenciaInicial->setDisabledSession(true);

$obIFiltroCompetenciaFinal = new IFiltroCompetencia(true,$competenciaMesAtual,true);
$obIFiltroCompetenciaFinal->setRotulo("Competência de Análise");
$obIFiltroCompetenciaFinal->setComplemento("Final");

$obRdoOrdenacao1 = new Radio();
$obRdoOrdenacao1->setName                       ( "stOrdenacao"                                               );
$obRdoOrdenacao1->setRotulo                     ( "Ordenação das Matrículas"                                  );
$obRdoOrdenacao1->setLabel                      ( "Alfabética"                                                );
$obRdoOrdenacao1->setTitle                      ( "Selecione a ordenação dos contratos no relatório."         );
$obRdoOrdenacao1->setValue                      ( "nom_cgm"                                                   );
$obRdoOrdenacao1->setChecked                    ( true                                                        );

$obRdoOrdenacao2 = new Radio();
$obRdoOrdenacao2->setName                       ( "stOrdenacao"                                               );
$obRdoOrdenacao2->setRotulo                     ( "Ordenação das Matrículas"                                  );
$obRdoOrdenacao2->setLabel                      ( "Numérica"                                                  );
$obRdoOrdenacao2->setTitle                      ( "Selecione a ordenação dos contratos no relatório."         );
$obRdoOrdenacao2->setValue                      ( "registro"                                                  );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                      ( $obForm                                               	      );
$obFormulario->addTitulo                    ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right");
$obFormulario->addTitulo                    ( "Parâmetros para a Consulta"                		      );
$obIFiltroCompetenciaInicial->geraFormulario( $obFormulario                                                   );
$obIFiltroCompetenciaFinal->geraFormulario  ( $obFormulario                                                   );
$obFormulario->agrupaComponentes            ( $arNatureza                                                     );
$obIFiltroComponentes->geraFormulario       ( $obFormulario						      );
$obFormulario->agrupaComponentes            ( array($obRdoOrdenacao1,$obRdoOrdenacao2)                        );
$obFormulario->defineBarra                  ( array($obBtnOk,$obBtnLimpar)                          	      );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
