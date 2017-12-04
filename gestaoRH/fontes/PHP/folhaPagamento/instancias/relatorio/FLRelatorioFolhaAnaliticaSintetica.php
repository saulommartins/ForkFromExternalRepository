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
    * Filtro de Relatório da Folha Analítica/Sintética
    * Data de Criação: 21/03/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: alex $
    $Date: 2008-03-05 11:37:33 -0300 (Qua, 05 Mar 2008) $

    * Casos de uso: uc-04.05.50
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                              );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                          );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioFolhaAnaliticaSintetica";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgOculF= "OC".$stPrograma."Filtro.php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::write('arContratos',array());
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once($pgJS   );
include_once($pgOculF);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( CAM_FW_POPUPS."relatorio/OCRelatorio.php"                 );
$obForm->setTarget                              ( "oculto"                                                  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                  );
$obHdnCtrl->setValue                            ( $stCtrl                                                   );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName                          ( "stCaminho"                                               );
//$obHdnCaminho->setValue                         ( CAM_GRH_FOL_INSTANCIAS."relatorio/OCRelatorioFolhaAnaliticaSintetica.php"  );
$obHdnCaminho->setValue                         ( CAM_GRH_FOL_INSTANCIAS."relatorio/".$pgProc);

$obHdnSpans =  new HiddenEval;
$obHdnSpans->setName                            ( "hdnSpans"                                                );
$obHdnSpans->setValue                           ( ""                                                        );

//Adiciona Spans que receberão conteudo JS a serem executados para selectsMultiplos
//eg. selecionaTodos(componenteFormulario)
//Spans : 8 RegimeFuncao
//        9 RegimeCargo
//       10 Padrão
//       11 Lotacao
//       12 Local
//       13 Bancos

for ($i=8;$i<15;$i++) {
    ${"obHdnSpans".$i} =  new HiddenEval;
    ${"obHdnSpans".$i}->setName                            ( "hdnSpans$i"                                       );
    ${"obHdnSpans".$i}->setValue                           ( ""                                                 );
}

$obHdnFiltrar =  new HiddenEval;
$obHdnFiltrar->setName                          ( "hdnTipoFiltro"                                           );
$obHdnFiltrar->setValue                         ( ""                                                        );

$obRdoFolha1 = new Radio;
$obRdoFolha1->setChecked                        ( true                                                      );
$obRdoFolha1->setLabel                          ( "Analítica Resumida"                                      );
$obRdoFolha1->setName                           ( "stFolha"                                                 );
$obRdoFolha1->setRotulo                         ( "Folha"                                                   );
$obRdoFolha1->setTitle                          ( "Selecione tipo de relatório a ser impresso."             );
$obRdoFolha1->setValue                          ( "analítica_resumida"                                      );
$obRdoFolha1->obEvento->setOnchange             ( "buscaValor('gerarSpan');"                                );

$obRdoFolha2 = new Radio;
$obRdoFolha2->setLabel                          ( "Analítica"                                               );
$obRdoFolha2->setName                           ( "stFolha"                                                 );
$obRdoFolha2->setRotulo                         ( "Folha"                                                   );
$obRdoFolha2->setTitle                          ( "Selecione tipo de relatório a ser impresso."             );
$obRdoFolha2->setValue                          ( "analítica"                                               );
$obRdoFolha2->obEvento->setOnchange             ( "buscaValor('gerarSpan');"                                );

$obRdoFolha3 = new Radio;
$obRdoFolha3->setLabel                          ( "Sintética"                                               );
$obRdoFolha3->setName                           ( "stFolha"                                                 );
$obRdoFolha3->setRotulo                         ( "Folha"                                                   );
$obRdoFolha3->setTitle                          ( "Selecione tipo de retório a ser impresso."               );
$obRdoFolha3->setValue                          ( "sintética"                                               );
$obRdoFolha3->obEvento->setOnchange             ( "buscaValor('gerarSpan');"                                );

$obIFiltroCompetencia = new IFiltroCompetencia(true, "", true);
$obIFiltroCompetencia->obCmbMes->setNull        ( false                                                     );
$obIFiltroCompetencia->obCmbMes->obEvento->setOnChange( $obIFiltroCompetencia->obCmbMes->obEvento->getOnChange()."buscaValor('gerarSpan1');");
$obIFiltroCompetencia->obTxtAno->setNull        ( false                                                     );
$obIFiltroCompetencia->obTxtAno->obEvento->setOnChange( $obIFiltroCompetencia->obTxtAno->obEvento->getOnChange()."buscaValor('gerarSpan1');");

$obChkFiltrarFolhaComplementar = new CheckBox;
$obChkFiltrarFolhaComplementar->setName         ( "boFiltrarFolhaComplementar"                              );
$obChkFiltrarFolhaComplementar->setRotulo       ( "Filtrar por Folha Complementar"                          );
$obChkFiltrarFolhaComplementar->setTitle        ( "Informe se deseja ou não filtrar por complementar."      );
$obChkFiltrarFolhaComplementar->setValue        ( true                                                      );
$obChkFiltrarFolhaComplementar->obEvento->setOnChange( "buscaValor('gerarSpan1');"                          );

$obSpnSpan1 = new Span;
$obSpnSpan1->setId                              ( "spSpan1"                                                 );

$obCmbFiltar = new Select;
$obCmbFiltar->setRotulo                         ( "Filtrar"                                                 );
$obCmbFiltar->setTitle                          ( "Selecione o tipo de filtro."                             );
$obCmbFiltar->setName                           ( "stTipoFiltro"                                            );
$obCmbFiltar->setValue                          ( $stTipoFiltro                                             );
$obCmbFiltar->setStyle                          ( "width: 200px"                                            );
$obCmbFiltar->addOption                         ( "", "Selecione"                                           );
$obCmbFiltar->addOption                         ( "contrato","Matrícula"                                    );
$obCmbFiltar->addOption                         ( "cgm_contrato","CGM/Matrícula"                            );
$obCmbFiltar->addOption                         ( "atributo","Atributo Dinâmico"                            );
$obCmbFiltar->addOption                         ( "geral","Geral"                                           );
$obCmbFiltar->obEvento->setOnChange             ( "buscaValor('gerarSpan');"                                );

$obSpnSpan2 = new Span;
$obSpnSpan2->setId                              ( "spSpan2"                                                 );

$obSpnSpan4 = new Span;
$obSpnSpan4->setId                              ( "spSpan4"                                                 );

$obSpnSpan5 = new Span;
$obSpnSpan5->setId                              ( "spSpan5"                                                 );

$obSpnSpan6 = new Span;
$obSpnSpan6->setId                              ( "spSpan6"                                                 );

$obBtnOk = new Ok;
$obBtnOk->obEvento->setOnClick("buscaValor('submeter');");

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimparGeral"                                          );
$obBtnLimpar->setValue                          ( "Limpar"                                                  );
$obBtnLimpar->obEvento->setOnClick              ( "buscaValor('limparGeral');"                              );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                   );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden                        ( $obHdnCaminho                                             );
$obFormulario->addHidden                        ( $obHdnCtrl                                                );
$obFormulario->addHidden                        ( $obHdnSpans,true                                          );

//Adiciona Spans para selectsMultiplos (ver definicao acima)
for ($i=8;$i<15;$i++) {
    $obFormulario->addHidden                    ( ${"obHdnSpans".$i},true                                   );
}

$obFormulario->addHidden                        ( $obHdnFiltrar                                             );
$obFormulario->addTitulo                        ( "Seleção do Filtro"                                       );
$obFormulario->agrupaComponentes                ( array($obRdoFolha1,$obRdoFolha2,$obRdoFolha3)             );
$obIFiltroCompetencia->geraFormulario           ( $obFormulario                                             );
$obFormulario->addComponente                    ( $obChkFiltrarFolhaComplementar                            );
$obFormulario->addSpan                          ( $obSpnSpan1                                               );
$obFormulario->addComponente                    ( $obCmbFiltar                                              );
$obFormulario->addSpan                          ( $obSpnSpan2                                               );
$obFormulario->addSpan                          ( $obSpnSpan4                                               );
$obFormulario->addSpan                          ( $obSpnSpan5                                               );
$obFormulario->addSpan                          ( $obSpnSpan6                                               );

$obRdoOrdenacao1 = new Radio;
$obRdoOrdenacao1->setChecked            ( true                                                      );
$obRdoOrdenacao1->setLabel              ( "Alfabética"                                              );
$obRdoOrdenacao1->setName               ( "stOrdenacao"                                             );
$obRdoOrdenacao1->setRotulo             ( "Ordenação"                                               );
$obRdoOrdenacao1->setTitle              ( "Informe o tipo de ordenação dos dados para os contratos.");
$obRdoOrdenacao1->setValue              ( "alfabetica"                                              );

$obRdoOrdenacao2 = new Radio;
$obRdoOrdenacao2->setLabel              ( "Numérica"                                                );
$obRdoOrdenacao2->setName               ( "stOrdenacao"                                             );
$obRdoOrdenacao2->setRotulo             ( "Ordenação"                                               );
$obRdoOrdenacao2->setTitle              ( "Informe o tipo de ordenação dos dados para os contratos.");
$obRdoOrdenacao2->setValue              ( "numérica"                                                );

$obRdoOrdenacaoEventos1 = new Radio;
$obRdoOrdenacaoEventos1->setName        ( "stOrdenacaoEventos"                                      );
$obRdoOrdenacaoEventos1->setRotulo      ( "Ordenação dos Eventos"                                   );
$obRdoOrdenacaoEventos1->setLabel       ( "Código do Evento"                                        );
$obRdoOrdenacaoEventos1->setTitle       ( "Selecione a ordenação dos eventos."                      );
$obRdoOrdenacaoEventos1->setValue       ( "codigo"                                                  );
$obRdoOrdenacaoEventos1->setChecked     ( true                                                      );

$obRdoOrdenacaoEventos2 = new Radio;
$obRdoOrdenacaoEventos2->setName        ( "stOrdenacaoEventos"                                      );
$obRdoOrdenacaoEventos2->setRotulo      ( "Ordenação dos Eventos"                                   );
$obRdoOrdenacaoEventos2->setLabel       ( "Sequência de Cálculo"                                    );
$obRdoOrdenacaoEventos2->setTitle       ( "Selecione a ordenação dos eventos."                      );
$obRdoOrdenacaoEventos2->setValue       ( "sequencia"                                               );
$obRdoOrdenacaoEventos2->setChecked     ( false                                                     );
$obFormulario->agrupaComponentes        ( array($obRdoOrdenacao1,$obRdoOrdenacao2)                  );
$obFormulario->agrupaComponentes        ( array($obRdoOrdenacaoEventos1,$obRdoOrdenacaoEventos2));

$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                              );
$obFormulario->show();

processarFiltro(true);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
