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
    * Página de Filtro do Consultar Registro de Evento na Complementar
    * Data de Criação: 14/02/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 30547 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-03-31 11:26:27 -0300 (Seg, 31 Mar 2008) $

    * Casos de uso: uc-04.05.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                  );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaComplementar.class.php"                        );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarRegistroEventoComplementar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( "../../popups/folhaComplementar/".$pgList );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl" );
$obHdnCtrl->setValue                            ( $stCtrl  );

$obRdoContrato = new Radio;
$obRdoContrato->setName                         ( "boOpcao"                         );
$obRdoContrato->setId                           ( "boOpcao"                         );
$obRdoContrato->setTitle                        ( "Selecione a opção para o filtro" );
$obRdoContrato->setRotulo                       ( "Opções"                          );
$obRdoContrato->setLabel                        ( "Matrícula"                        );
$obRdoContrato->setValue                        ( "contrato"                        );
$obRdoContrato->setChecked                      ( true                              );
$obRdoContrato->obEvento->setOnChange           ( "buscaValor('montaSpanContrato')" );

$obRdoCgmContrato = new Radio;
$obRdoCgmContrato->setName                      ( "boOpcao"                            );
$obRdoCgmContrato->setId                        ( "boOpcao"                            );
$obRdoCgmContrato->setTitle                     ( "Selecione a opção para o filtro"    );
$obRdoCgmContrato->setRotulo                    ( "Opções"                             );
$obRdoCgmContrato->setLabel                     ( "CGM/Matrícula"                       );
$obRdoCgmContrato->setValue                     ( "cgm_contrato"                       );
$obRdoCgmContrato->obEvento->setOnChange        ( "buscaValor('montaSpanCGMContrato')" );

$obRdoEvento = new Radio;
$obRdoEvento->setName                           ( "boOpcao"                      );
$obRdoEvento->setId                             ( "boOpcao"                      );
$obRdoEvento->setTitle                          ( "Selecione a opção para o filtro"    );
$obRdoEvento->setRotulo                         ( "Opções"                             );
$obRdoEvento->setLabel                          ( "Evento"                       );
$obRdoEvento->setValue                          ( "evento"                       );
$obRdoEvento->obEvento->setOnChange             ( "buscaValor('montaSpanEvento')" );

$obSpanFiltro = new Span;
$obSpanFiltro->setId                            ( "spnFiltro" );
$obSpanFiltro->setValue                         ( ""          );

$obHdnFiltro = new HiddenEval;
$obHdnFiltro->setName                           ( "hdnFiltro" );
$obHdnFiltro->setValue                          ( ""          );

$obIFiltroCompetencia = new IFiltroCompetencia;
$obIFiltroCompetencia->obCmbMes->obEvento->setOnChange("buscaValor('preencheComplementar');");
$obIFiltroCompetencia->obTxtAno->obEvento->setOnChange("buscaValor('preencheComplementar');");

$obCmbComplementar = new Select;
$obCmbComplementar->setRotulo     ( "Complementar"       );
$obCmbComplementar->setTitle      ( "Selecione a folha complementar" );
$obCmbComplementar->setName       ( "inCodComplementar"  );
$obCmbComplementar->setId         ( "inCodComplementar"  );
$obCmbComplementar->setValue      ( $inCodComplementar   );
$obCmbComplementar->addOption     ( "", "Selecione"      );
$obCmbComplementar->setNull       ( false                );

$obSpnBotao = new Span;
$obSpnBotao->setId                              ( "spnSpanBotao"  );

$obSpnLista = new Span;
$obSpnLista->setId                              ( "spnSpanLista"  );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                 );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addTitulo                        ( "Parâmetros para Emissão do Relatório"  );
$obFormulario->addHidden                        ( $obHdnCtrl                              );
$obFormulario->agrupaComponentes                ( array($obRdoContrato,$obRdoCgmContrato,$obRdoEvento) );
$obFormulario->addSpan                          ( $obSpanFiltro                           );
$obFormulario->addHidden                        ( $obHdnFiltro,true                       );
$obIFiltroCompetencia->geraFormulario           ( $obFormulario                           );
$obFormulario->addComponente                    ( $obCmbComplementar                      );
$obFormulario->addSpan                          ( $obSpnBotao                                           );
$obFormulario->addSpan                          ( $obSpnLista                                           );

$obFormulario->show();

sistemaLegado::executaFrameOculto("buscaValor('carregaValoresIniciais');");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
