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
* Página filtro para Relatório de Totais por Fornecedor
* Data de Criação   : 21/11/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @ignore

$Revision: 30547 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioValeTransporte.class.php"                                );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                              );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioTotaisPorFornecedor";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OCFiltro".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgProc = CAM_FW_POPUPS."relatorio/OCRelatorio.php";

include_once ($pgJS);

$rsVazio = new RecordSet;
$obRBeneficioValeTransporte = new RBeneficioValeTransporte;
$obRBeneficioValeTransporte->obRBeneficioFornecedorValeTransporte->listarFornecedorValeTransporteRelatorio($rsFornecedorDisponiveis);

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setValue( ""     );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GRH_BEN_INSTANCIAS."relatorio/OCRelatorioTotaisPorFornecedor.php" );

$obCmbFornecedor = new SelectMultiplo();
$obCmbFornecedor->setName         ( 'fornecedor'                                                                  );
$obCmbFornecedor->setRotulo       ( "Fornecedor"                                                                  );
$obCmbFornecedor->setTitle        ( "Selecione o(s) fornecedor(es) para o filtro."                                );
$obCmbFornecedor->setNull         ( false                                                                         );
$obCmbFornecedor->SetNomeLista1   ( 'FornecedorDisponiveis'                                                       );
$obCmbFornecedor->setCampoId1     ( 'numcgm'                                                                      );
$obCmbFornecedor->setCampoDesc1   ( '[numcgm] - [nom_cgm]'                                                        );
$obCmbFornecedor->setStyle1       ( "width: 300px"                                                                );
$obCmbFornecedor->SetRecord1      ( $rsFornecedorDisponiveis                                                      );
$obCmbFornecedor->SetNomeLista2   ( 'FornecedorSelecionados'                                                      );
$obCmbFornecedor->setCampoId2     ( 'numcgm'                                                                      );
$obCmbFornecedor->setCampoDesc2   ( '[numcgm] - [nom_cgm]'                                                        );
$obCmbFornecedor->setStyle2       ( "width: 300px"                                                                );
$obCmbFornecedor->SetRecord2      ( $rsVazio                                                                      );
$stOnClick = "selecionaFornecedor(true);buscaValor('preencheItinerario');selecionaFornecedor(false);";
$obCmbFornecedor->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
$obCmbFornecedor->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
$obCmbFornecedor->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
$obCmbFornecedor->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
$obCmbFornecedor->obSelect1->obEvento->setOnDblClick( $stOnClick );
$obCmbFornecedor->obSelect2->obEvento->setOnDblClick( $stOnClick );

$obCmbItinerario = new SelectMultiplo();
$obCmbItinerario->setName         ( 'itinerario'                                                                  );
$obCmbItinerario->setRotulo       ( "Itinerario"                                                                  );
$obCmbItinerario->setTitle        ( "Selecione o itinerário para o filtro."                                       );
$obCmbItinerario->setNull         ( false                                                                         );
$obCmbItinerario->SetNomeLista1   ( 'ItinerarioDisponiveis'                                                       );
$obCmbItinerario->setCampoId1     ( 'vale_transporte_cod_vale_transporte'                                         );
$obCmbItinerario->setCampoDesc1   ( '[municipio_origem]/[municipio_destino]'                                      );
$obCmbItinerario->setStyle1       ( "width: 300px"                                                                );
$obCmbItinerario->SetRecord1      ( $rsVazio                                                                      );
$obCmbItinerario->SetNomeLista2   ( 'ItinerarioSelecionados'                                                      );
$obCmbItinerario->setCampoId2     ( 'vale_transporte_cod_vale_transporte'                                         );
$obCmbItinerario->setCampoDesc2   ( '[municipio_origem]/[municipio_destino]'                                      );
$obCmbItinerario->setStyle2       ( "width: 300px"                                                                );
$obCmbItinerario->SetRecord2      ( $rsVazio                                                                      );

$obChkAgruparPorLotacao = new CheckBox;
$obChkAgruparPorLotacao->setName     ( "stAgruparPorLotacao"                                                         );
$obChkAgruparPorLotacao->setId       ( "stAgruparPorLotacao"                                                         );
$obChkAgruparPorLotacao->setTitle    ( "Selecione o agrupamento de registros por lotação e ou por local."            );
$obChkAgruparPorLotacao->setRotulo   ( "Agrupar"                                                                     );
$obChkAgruparPorLotacao->setLabel    ( "Lotação"                                                                     );
$obChkAgruparPorLotacao->setValue    ( "lotacao"                                                                     );
$obChkAgruparPorLotacao->setNull     ( true                                                                          );
$obChkAgruparPorLotacao->setChecked  ( false                                                                         );

$obChkAgruparPorLocal = new CheckBox;
$obChkAgruparPorLocal->setName       ( "stAgruparPorLocal"                                                           );
$obChkAgruparPorLocal->setId         ( "stAgruparPorLocal"                                                           );
$obChkAgruparPorLocal->setTitle      ( "Selecione o agrupamento de registros por lotação e ou por local."            );
$obChkAgruparPorLocal->setRotulo     ( "Agrupar"                                                                     );
$obChkAgruparPorLocal->setLabel      ( "Local"                                                                       );
$obChkAgruparPorLocal->setValue      ( "local"                                                                       );
$obChkAgruparPorLocal->setNull       ( true                                                                          );
$obChkAgruparPorLocal->setChecked    ( false                                                                         );

$obTxtVigencia = new Data;
$obTxtVigencia->setName           ( "dtVigencia"                                                                  );
$obTxtVigencia->setNull           ( false                                                                         );
$obTxtVigencia->setRotulo         ( "Vigência"                                                                    );
$obTxtVigencia->setTitle          ( "Informe vigência para o filtro"                                              );

//FORM
$obFormulario = new Formulario;

$obFormulario->addForm               ( $obForm                  );
$obFormulario->addHidden             ( $obHdnCtrl               );
$obFormulario->addHidden             ( $obHdnCaminho            );

$obFormulario->addTitulo             ( "Parâmetros para Emissão do Relatório"  );
$obFormulario->addComponente         ( $obCmbFornecedor                        );
$obFormulario->addComponente         ( $obCmbItinerario                        );
$obFormulario->addComponente         ( new ISelectMultiploLotacao              );
$obFormulario->addComponente         ( new ISelectMultiploLocal                );
$obFormulario->addComponenteComposto ( $obChkAgruparPorLotacao,$obChkAgruparPorLocal );
$obFormulario->addComponente         ( $obTxtVigencia                          );

$obFormulario->OK();
$obFormulario->show();
