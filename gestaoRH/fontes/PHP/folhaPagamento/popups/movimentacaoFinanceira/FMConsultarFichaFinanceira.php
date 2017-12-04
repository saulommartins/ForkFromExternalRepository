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
    * Data de Criação: 28/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 32866 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-02-06 12:41:12 -0200 (Qua, 06 Fev 2008) $

    * Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                          );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                              );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarFichaFinanceira";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get("stAcao");
foreach ($_REQUEST as $key => $valor) {
    $link[$key] = $valor;
}
Sessao::write("link",$link);

$obRFolhaPagamentoFolhaSituacao           = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$jsOnload   = "executaFuncaoAjax('gerarSpan1');";

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                              );
$obHdnAcao->setValue                            ( $stAcao                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                              );
$obHdnCtrl->setValue                            ( $request->get('stStrl')                               );

$obLblContrato= new Label;
$obLblContrato->setRotulo                       ( "Matrícula"                                           );
$obLblContrato->setName                         ( "inContrato"                                          );
$obLblContrato->setValue                        ( $request->get("inRegistro")                           );

$obLblPeriodo = new Label;
$obLblPeriodo->setId                            ( "stPeriodo" );
$obLblPeriodo->setValue                         ( $request->get('stPeriodo')  );
$obLblPeriodo->setRotulo                        ( "Período de Movimentação" );

$obLblComplementar= new Label;
$obLblComplementar->setRotulo                       ( "Complementar"                                    );
$obLblComplementar->setName                         ( "inCodComplementar"                               );
$obLblComplementar->setValue                        ( $request->get("inCodComplementar")                );

$obLblCGM= new Label;
$obLblCGM->setRotulo                            ( "CGM"                                                 );
$obLblCGM->setName                              ( "stCgm"                                               );
$obLblCGM->setValue                             ( $request->get("numcgm") .' - '. $request->get("nom_cgm")  );

$obSpnSpan1 = new Span;
$obSpnSpan1->setId                              ( "SpnSpan1"                                            );

$obBtnOk = new Ok;

$obBtnCancelar = new Button;
$obBtnCancelar->setName                         ( 'fechar'                                              );
$obBtnCancelar->setValue                        ( 'Fechar'                                              );
$obBtnCancelar->obEvento->setOnClick            ( "window.parent.window.close();"                       );

$obBtnImprimir = new Ok;
$obBtnImprimir->setName                    ( "btnImprimir"      );
$obBtnImprimir->setValue                   ( "Imprimir"         );
$obBtnImprimir->setTipo                    ( "button"           );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgForm                                               );
$obForm->setTarget                              ( "telaPrincipal"                                       );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addTitulo                        ( "Dados da Matrícula Servidor"                         );
$obFormulario->addHidden                        ( $obHdnAcao                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                            );
if ($_REQUEST['inCodComplementar']) {
   $obFormulario->addComponente                 ( $obLblComplementar                                    );
}
$obFormulario->addComponente                    ( $obLblContrato                                        );
$obFormulario->addComponente                    ( $obLblCGM                                             );
$obFormulario->addSpan                          ( $obSpnSpan1                                           );
$obFormulario->defineBarra                      ( array($obBtnCancelar),"" ,""                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
