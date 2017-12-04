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
    * Lista de visualização de resultado de calculo de contrato
    * Data de Criação: 07/12/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2008-01-09 16:03:59 -0200 (Qua, 09 Jan 2008) $

    * Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php");
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php");

SistemaLegado::LiberaFrames();

$jsOnload = '';
if ($request->get('stCtrl') == 'gerarSpanErroCalculo' ) {
    $jsOnload = "jQuery('#stOpcaoErro').attr('checked', true); executaFuncaoAjax('gerarSpanErroCalculo','stOpcao=erro');";
}

$stAcao = $request->get("stAcao");
$link = Sessao::read("link");
if ( $request->get("pg") and  $request->get("pos") ) {
    $stLink = "&pg=".$request->get("pg")."&pos=".$request->get("pos");
    $link["pg"]  = $request->get("pg");
    $link["pos"] = $request->get("pos");
    Sessao::write("link",$link);
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalculoSalario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgProcImpressao = "PR".$stPrograma."Impressao.php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$jsOnload = "executaFuncaoAjax('gerarSpanSucessoErro');".$jsOnload;

include_once($pgJS);

//DEFINICAO DOS COMPONENTES
$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                );
$obHdnCtrl->setValue                            ( $request->get('stCtrl') );

//Define objeto SPAN
$obSpnSpan1 = new Span;
$obSpnSpan1->setId                              ( "spnSpan1"                                            );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                              );
$obHdnAcao->setValue                            ( $stAcao                                               );

$obRdoCalculados = new Radio;
$obRdoCalculados->setName                       ( "stOpcao"                                             );
$obRdoCalculados->setId                         ( "stOpcaoCalculados"                                   );
$obRdoCalculados->setRotulo                     ( "Matrículas"                                          );
$obRdoCalculados->setLabel                      ( "Calculados com sucesso"                              );
$obRdoCalculados->setValue                      ( "calculados"                                          );
$obRdoCalculados->obEvento->setOnChange         ( "executaFuncaoAjax('gerarSpanSucessoCalculo',stOpcao);"   );
$obRdoCalculados->setChecked                    ( true );

$obRdoErro = new Radio;
$obRdoErro->setName                             ( "stOpcao"                                             );
$obRdoErro->setId                               ( "stOpcaoErro"                                         );
$obRdoErro->setRotulo                           ( "Matrículas"                                          );
$obRdoErro->setLabel                            ( "Erro no cálculo"                                     );
$obRdoErro->setValue                            ( "erro"                                                );
$obRdoErro->obEvento->setOnChange               ( "executaFuncaoAjax('gerarSpanErroCalculo',stOpcao);"  );

$obLblContratos = new Label;
$obLblContratos->setName                         ( "inQuantContratos"                                    );
$obLblContratos->setRotulo                       ( "Matrículas Calculadas"                                );
$obLblContratos->setValue                        ( Sessao::read("inContratosCalculados")                   );

$obLblContratosSucesso = new Label;
$obLblContratosSucesso->setId                         ( "inQuantContratosSucesso"                                    );
$obLblContratosSucesso->setRotulo                       ( "Matrículas Calculadas com Sucesso"                         );

$obLblContratosErro = new Label;
$obLblContratosErro->setId                         ( "inQuantContratosErro"                                    );
$obLblContratosErro->setRotulo                       ( "Matrículas Calculadas com Erro"                                );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                          ( $pgProcImpressao             );
$obForm->setTarget                          ( "telaPrincipal"                                              );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                               );
$obFormulario->addHidden                    ( $obHdnAcao                                            );
$obFormulario->addHidden                    ( $obHdnCtrl                                            );
$obFormulario->addTitulo                    ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->agrupaComponentes            ( array($obRdoCalculados,$obRdoErro)                    );
$obFormulario->addComponente                ( $obLblContratos                                       );
$obFormulario->addComponente                ( $obLblContratosSucesso                                       );
$obFormulario->addComponente                ( $obLblContratosErro                                       );
$obFormulario->addSpan                          ( $obSpnSpan1                                           );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
