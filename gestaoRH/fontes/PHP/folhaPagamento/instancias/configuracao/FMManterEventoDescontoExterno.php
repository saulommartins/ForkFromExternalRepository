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
    * Formulário
    * Data de Criação: 08/08/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Tiago Camargo Finger

    * @ignore

    * Casos de uso: uc-04.05.61
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                                              );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                                       );

$stPrograma = 'ManterEventoDescontoExterno';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//carrega os dados se houver dados
$jsOnload = "executaFuncaoAjax('preencherInnerEventos');";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( $stCtrl  );

###########################################################################################################################
# Componentes Eventos Previdência                                                                                         #
###########################################################################################################################
$obBscEventoBasePrevidencia = new BuscaInner;
$obBscEventoBasePrevidencia->setRotulo                         ( "Evento Informativo Base de Previdência"               );
$obBscEventoBasePrevidencia->setTitle                          ( "Informe o evento informativo para Base de Previdência, dos valores descontados em outras entidades."                                                                              );
$obBscEventoBasePrevidencia->setId                             ( "inCampoInnerEventoBasePrevidencia"                    );
$obBscEventoBasePrevidencia->setValue                          ( ''                                                     );
$obBscEventoBasePrevidencia->obCampoCod->setName               ( "inCodigoEventoBasePrevidencia"                        );
$obBscEventoBasePrevidencia->setNull                           ( false                                                  );
$obBscEventoBasePrevidencia->obCampoCod->setMascara            ( $stMascaraEvento                                       );
$obBscEventoBasePrevidencia->obCampoCod->setPreencheComZeros   ( 'E'                                                    );
$obBscEventoBasePrevidencia->obCampoCod->obEvento->setOnChange ( "executaFuncaoAjax( 'preencherInnerEvento', '&nuCodigoEvento='+document.frm.inCodigoEventoBasePrevidencia.value+'&stNomeCampoEvento=BasePrevidencia' );"                           );
$obBscEventoBasePrevidencia->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_FOL_POPUPS."evento/FLManterEvento.php','frm','inCodigoEventoBasePrevidencia','inCampoInnerEventoBasePrevidencia','','".Sessao::getId()."&stNaturezasAceitas=I&stNatureza=I&boEventoSistema=true','800','550')" );

$obBscEventoDescontoPrevidencia = new BuscaInner;
$obBscEventoDescontoPrevidencia->setRotulo                         ( "Evento Informativo Desconto Previdência"          );
$obBscEventoDescontoPrevidencia->setTitle                          ( "Informe o evento informativo para Desconto de previdência, dos valores descontados em outras entidades"                                                                       );
$obBscEventoDescontoPrevidencia->setId                             ( "inCampoInnerEventoDescontoPrevidencia"            );
$obBscEventoDescontoPrevidencia->setValue                          ( ''                                                 );
$obBscEventoDescontoPrevidencia->obCampoCod->setName               ( "inCodigoEventoDescontoPrevidencia"                );
$obBscEventoDescontoPrevidencia->setNull                           ( false                                              );
$obBscEventoDescontoPrevidencia->obCampoCod->setMascara            ( $stMascaraEvento                                   );
$obBscEventoDescontoPrevidencia->obCampoCod->setPreencheComZeros   ( 'E'                                                );
$obBscEventoDescontoPrevidencia->obCampoCod->obEvento->setOnChange ( "executaFuncaoAjax( 'preencherInnerEvento', '&nuCodigoEvento='+document.frm.inCodigoEventoDescontoPrevidencia.value+'&stNomeCampoEvento=DescontoPrevidencia' );"               );
$obBscEventoDescontoPrevidencia->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_FOL_POPUPS."evento/FLManterEvento.php','frm','inCodigoEventoDescontoPrevidencia','inCampoInnerEventoDescontoPrevidencia','','".Sessao::getId()."&stNaturezasAceitas=I&stNatureza=I&boEventoSistema=true','800','550')" );

###########################################################################################################################
# Componentes Eventos IRRF                                                                                                #
###########################################################################################################################

$obBscEventoBaseIRRF = new BuscaInner;
$obBscEventoBaseIRRF->setRotulo                             ( "Evento Informativo Base de IRRF"                         );
$obBscEventoBaseIRRF->setTitle                              ( "Informe o evento informativo para Base de IRRF, dos valores descontados em outras entidades."                                                                                        );
$obBscEventoBaseIRRF->setId                                 ( "inCampoInnerEventoBaseIRRF"                              );
$obBscEventoBaseIRRF->setValue                              ( ''                                                        );
$obBscEventoBaseIRRF->obCampoCod->setName                   ( "inCodigoEventoBaseIRRF"                                  );
$obBscEventoBaseIRRF->setNull                               ( false                                                     );
$obBscEventoBaseIRRF->obCampoCod->setMascara                ( $stMascaraEvento                                          );
$obBscEventoBaseIRRF->obCampoCod->setPreencheComZeros       ( 'E'                                                       );
$obBscEventoBaseIRRF->obCampoCod->obEvento->setOnChange     ( "executaFuncaoAjax( 'preencherInnerEvento', '&nuCodigoEvento='+document.frm.inCodigoEventoBaseIRRF.value+'&stNomeCampoEvento=BaseIRRF' );"                                            );
$obBscEventoBaseIRRF->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_FOL_POPUPS."evento/FLManterEvento.php','frm','inCodigoEventoBaseIRRF','inCampoInnerEventoBaseIRRF','','".Sessao::getId()."&stNaturezasAceitas=I&stNatureza=I&boEventoSistema=true','800','550')" );

$obBscEventoDescontoIRRF = new BuscaInner;
$obBscEventoDescontoIRRF->setRotulo                         ( "Evento Informativo Desconto IRRF"                        );
$obBscEventoDescontoIRRF->setTitle                          ( "Informe o evento informativo para Desconto de IRRF, dos valores descontados em outras entidades" );
$obBscEventoDescontoIRRF->setId                             ( "inCampoInnerEventoDescontoIRRF"                          );
$obBscEventoDescontoIRRF->setValue                          ( ''                                                        );
$obBscEventoDescontoIRRF->obCampoCod->setName               ( "inCodigoEventoDescontoIRRF"                              );
$obBscEventoDescontoIRRF->setNull                           ( false                                                     );
$obBscEventoDescontoIRRF->obCampoCod->setMascara            ( $stMascaraEvento                                          );
$obBscEventoDescontoIRRF->obCampoCod->setPreencheComZeros   ( 'E'                                                       );
$obBscEventoDescontoIRRF->obCampoCod->obEvento->setOnChange ( "executaFuncaoAjax( 'preencherInnerEvento', '&nuCodigoEvento='+document.frm.inCodigoEventoDescontoIRRF.value+'&stNomeCampoEvento=DescontoIRRF' );"                                    );
$obBscEventoDescontoIRRF->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_FOL_POPUPS."evento/FLManterEvento.php','frm','inCodigoEventoDescontoIRRF','inCampoInnerEventoDescontoIRRF','','".Sessao::getId()."&stNaturezasAceitas=I&stNatureza=I&boEventoSistema=true','800','550')" );

$obFormulario = new Formulario;
$obFormulario->addForm                                          ( $obForm                                               );
$obFormulario->addTitulo                                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"                                                                                                                  );
$obFormulario->addHidden                                        ( $obHdnCtrl                                            );
$obFormulario->addHidden                                        ( $obHdnAcao                                            );
$obFormulario->addTitulo                                        ( "Configuração Desconto Externo"                       );
$obFormulario->addTitulo                                        ( "Eventos Previdência"                                 );
$obFormulario->addComponente                                    ( $obBscEventoBasePrevidencia                           );
$obFormulario->addComponente                                    ( $obBscEventoDescontoPrevidencia                       );
$obFormulario->addTitulo                                        ( "Eventos IRRF"                                        );
$obFormulario->addComponente                                    ( $obBscEventoBaseIRRF                                  );
$obFormulario->addComponente                                    ( $obBscEventoDescontoIRRF                              );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
