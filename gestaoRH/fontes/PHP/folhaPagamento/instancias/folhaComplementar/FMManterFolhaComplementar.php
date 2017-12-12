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
    * Formulário de Abrir/Fechar Folha Complementar
    * Data de Criação: 13/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.05.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                              );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

$link = Sessao::read("link");
$stPrograma = "ManterFolhaComplementar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$link["pg"]."&pos=".$link["pos"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao,$boTransacao);
if ( $rsUltimaMovimentacao->getNumLinhas() > 0 ) {
    $obRFolhaPagamentoPeriodoMovimentacao->obRFolhaPagamentoFolhaSituacao->consultarFolha();
    $obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

    $dtInicial = $rsUltimaMovimentacao->getCampo("dt_inicial");
    $dtFinal   = $rsUltimaMovimentacao->getCampo("dt_final");
    $stSituacao= $obRFolhaPagamentoPeriodoMovimentacao->obRFolhaPagamentoFolhaSituacao->getSituacao()." em ".$obRFolhaPagamentoPeriodoMovimentacao->obRFolhaPagamentoFolhaSituacao->getDataHora();

    $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao($rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'));
    $obRFolhaPagamentoPeriodoMovimentacao->obRFolhaPagamentoFolhaSituacao->listarFolha($rsFolha,$boTransacao);

    Sessao::write('inCodPeriodoMovimentacao',$rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'));
    Sessao::write('boFolhaSalarioReaberta',( $rsFolha->getCampo('count') > 2 ) ? true : false);
    Sessao::write('stSituacaoFolhaSalario',$rsFolha->getCampo('situacao'));
    Sessao::write('stTimestampFolhaSalarioFechada',$rsFolha->getCampo('timestamp_fechado'));

    include_once($pgJs);
    include_once($pgOcul);
    
    $stAcao = $request->get("stAcao");

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName                             ( "stAcao"                                                  );
    $obHdnAcao->setValue                            ( $stAcao                                                   );

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName                             ( "stCtrl"                                                  );
    $obHdnCtrl->setValue                            ( ""                                                        );

    $obHdnCodComplementar = new Hidden;
    $obHdnCodComplementar->setName                  ( "inCodComplementar"                                       );
    $obHdnCodComplementar->setValue                 ( ""                                                        );

    $obHdnCodPeriodoMovimentacao = new Hidden;
    $obHdnCodPeriodoMovimentacao->setName           ( "inCodPeriodoMovimentacao"                                );
    $obHdnCodPeriodoMovimentacao->setValue          ( $rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'));

    $obLblDtInicial = new Label;
    $obLblDtInicial->setName                        ( "dtInicial"                                               );
    $obLblDtInicial->setValue                       ( $dtInicial                                                );
    $obLblDtInicial->setRotulo                      ( "Data Inicial"                                            );

    $obLblDtFinal = new Label;
    $obLblDtFinal->setName                          ( "dtFinal"                                                 );
    $obLblDtFinal->setValue                         ( $dtFinal                                                  );
    $obLblDtFinal->setRotulo                        ( "Data Final"                                              );

    $obLblSituacao = new Label;
    $obLblSituacao->setRotulo                       ( "Situação Atual"                                          );
    $obLblSituacao->setValue                        ( $stSituacao                                               );

    $obSpan1 = new Span;
    $obSpan1->setId                                 ( "spnSpan1"                                                );

    $obSpan2 = new Span;
    $obSpan2->setId                                 ( "spnSpan2"                                                );

    $obSpan3 = new Span;
    $obSpan3->setId                                 ( "spnSpan3"                                                );

    //DEFINICAO DOS COMPONENTES
    $obForm = new Form;
    $obForm->setAction                              ( $pgProc                                                   );
    $obForm->setTarget                              ( "oculto"                                                  );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addForm                          ( $obForm                                                   );
    $obFormulario->addHidden                        ( $obHdnAcao                                                );
    $obFormulario->addHidden                        ( $obHdnCtrl                                                );
    $obFormulario->addHidden                        ( $obHdnCodComplementar                                     );
    $obFormulario->addHidden                        ( $obHdnCodPeriodoMovimentacao                              );
    $obFormulario->addTitulo                    ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"   );
    $obFormulario->addTitulo                        ( "Periodo de Movimentação em Aberto"                       );
    $obFormulario->addComponente                    ( $obLblDtInicial                                           );
    $obFormulario->addComponente                    ( $obLblDtFinal                                             );
    $obFormulario->addTitulo                        ( "Folha Salário"                                           );
    $obFormulario->addComponente                    ( $obLblSituacao                                            );
    $obFormulario->addSpan                          ( $obSpan1                                                  );
    $obFormulario->addSpan                          ( $obSpan2                                                  );
    $obFormulario->addSpan                          ( $obSpan3                                                  );
    $obFormulario->show();

    processarForm(true);
    
} else {
    $stMensagem = "Não foi encontrado nenhum período de movimentação aberto. Para abrir uma complementar é necessário que o período esteja aberto.";
    $obLblMensagem = new Label;
    $obLblMensagem->setName                         ( "stMensagem"                                              );
    $obLblMensagem->setValue                        ( $stMensagem                                               );
    $obLblMensagem->setRotulo                       ( "Situação"                                                );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addComponente                    ( $obLblMensagem                                            );
    $obFormulario->show();
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
