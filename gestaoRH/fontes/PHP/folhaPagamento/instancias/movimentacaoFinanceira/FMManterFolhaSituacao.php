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
* Página de Formulario para Fechar/Reabrir Competência
* Data de Criação: 11/01/2006

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30547 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.05.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterFolhaSituacao";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);
//include_once($pgOcul);

    $obHdnCtrl =  new Hidden;
    $obHdnCtrl->setName                             ( "stCtrl"                                              );
    $obHdnCtrl->setValue                            ( $stStrl                                               );

//**************************************************************//
// Busca informações e define variaveis
//  que são usadas pela página para montar formulário
//**************************************************************//
//Busca as informações da última movimentação
$obRPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$obRPeriodoMovimentacao->listarUltimaMovimentacao( $rsUltimaMovimentacao, $boTransacao );

//Busca as informações da última situação da Folha de Pagamento (folha_situacao)
$obRPeriodoMovimentacao->obRFolhaPagamentoFolhaSituacao->consultarFolha( $boTransacao );

//Define o mes da competencia atual, que é o mes da data final do periodo de movimentação
$arMes = explode( "/" , $rsUltimaMovimentacao->getCampo("dt_final") );

//Array com a descrição dos meses.
$arDescMes   = array ("Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril"  , "Maio"    , "Junho",
                  "Julho"  , "Agosto"   , "Setembro"    , "Outubro", "Novembro", "Dezembro");

//Define uma variavel booleana pra saber se a situação está aberta.
if ( $obRPeriodoMovimentacao->obRFolhaPagamentoFolhaSituacao->getSituacao() == "Aberto" ) {
    $boSituacaoAberta = true;
} else {
    $boSituacaoAberta = false;
}

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                              );
$obHdnCtrl->setValue                            ( $stStrl                                               );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//Define o objeto OK para fechar a folha
$obFechar = new Ok;
$obFechar->setRotulo            ( ""          );
$obFechar->setName              ( "btnFechar" );
$obFechar->setValue             ( "Fechar"    );
$obFechar->setDisabled          ( !$boSituacaoAberta );
//$obFechar->obEvento->setOnClick ("montaParametrosGET('submeter','',true);");
//$obFechar->obEvento->setOnClick ("buscaValor('submeter');");
$obFechar->obEvento->setOnClick                  ( "buscaValorFiltro('submeter');"                       );

//Define o objeto OK para reabrir a folha
$obReabrir = new Ok;
$obReabrir->setName              ( "btnAbrir"        );
if ($obRPeriodoMovimentacao->obRFolhaPagamentoFolhaSituacao->getDataHora() == "") {
    $obReabrir->setValue             ( "Abrir"         );
} else {
    $obReabrir->setValue             ( "Reabrir"         );
}
$obReabrir->setDisabled          ( $boSituacaoAberta );
//$obReabrir->obEvento->setOnClick ("montaParametrosGET('submeter','',true);");
//$obReabrir->obEvento->setOnClick ("buscaValor('submeter');");
$obReabrir->obEvento->setOnClick                  ( "buscaValorFiltro('submeter');"                       );

//Define o objeto da ação stAcao
//Se a situação estiver aberta, a ação será fechar
//caso estiver fechada a ação será reabrir
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
if ($boSituacaoAberta) {
    $obHdnAcao->setValue( "fechar" );
    $obFechar->setId("BOTAO");
} else {
    $obHdnAcao->setValue( "abrir" );
    $obReabrir->setId("BOTAO");
}

$lblSituacao = new Label;
$lblSituacao->setRotulo ( 'Situação'      );
$lblSituacao->setName   ( 'stlblSituacao' );
$lblSituacao->setId     ( 'stlblSituacao' );
$lblSituacao->setValue  ( 'Não foi encontrado nenhum período de movimentação aberto. Para fechar ou reabrir uma folha salário é necessário que o período esteja aberto.' );

//Define objeto LABEL para mostrar a "Data Inicial" do "Período de Movimentação em Aberto"
$lblDataInicial = new Label;
$lblDataInicial->setRotulo ( 'Data Inicial'     );
$lblDataInicial->setName   ( 'stlblDataInicial' );
$lblDataInicial->setId     ( 'stlblDataInicial' );
$lblDataInicial->setValue  ( $rsUltimaMovimentacao->getCampo("dt_inicial") );

//Define objeto LABEL para mostrar a "Data Final" do "Período de Movimentação em Aberto"
$lblDataFinal = new Label;
$lblDataFinal->setRotulo ( 'Data Final'     );
$lblDataFinal->setName   ( 'stlblDataFinal' );
$lblDataFinal->setId     ( 'stlblDataFinal' );
$lblDataFinal->setValue  ( $rsUltimaMovimentacao->getCampo("dt_final") );

//Define objeto LABEL para mostrar a "Competência Atual" da "Competência"
$lblCompetenciaAtual = new Label;
$lblCompetenciaAtual->setRotulo ( 'Competência Atual'     );
$lblCompetenciaAtual->setName   ( 'stlblCompetenciaAtual' );
$lblCompetenciaAtual->setId     ( 'stlblCompetenciaAtual' );
$lblCompetenciaAtual->setValue  ( $arDescMes[ $arMes[1] - 1 ] );

//Define objeto LABEL para mostrar a "Última Situação" da "Competência"
$lblSituacaoAtual = new Label;
$lblSituacaoAtual->setRotulo ( 'Situação Atual'      );
$lblSituacaoAtual->setName   ( 'stlblUltimaSituacao' );
$lblSituacaoAtual->setId     ( 'stlblUltimaSituacao' );
if ($obRPeriodoMovimentacao->obRFolhaPagamentoFolhaSituacao->getDataHora() != "") {
    $lblSituacaoAtual->setValue  ( $obRPeriodoMovimentacao->obRFolhaPagamentoFolhaSituacao->getSituacao()." em ".$obRPeriodoMovimentacao->obRFolhaPagamentoFolhaSituacao->getDataHora() );
}

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
if ( $rsUltimaMovimentacao->getNumLinhas() > 0 ) {
    $obFormulario->addHidden             ( $obHdnAcao                                         );
    $obFormulario->addHidden             ( $obHdnCtrl                                         );
    $obFormulario->addTitulo             ( "Período de Movimentação em Aberto"                );
    $obFormulario->addComponente         ( $lblDataInicial                                    );
    $obFormulario->addComponente         ( $lblDataFinal                                      );
    $obFormulario->addTitulo             ( "Folha Salário"                                    );
    $obFormulario->addComponente         ( $lblCompetenciaAtual                               );
    $obFormulario->addComponente         ( $lblSituacaoAtual                                  );
    $obFormulario->addComponenteComposto ( $obFechar , $obReabrir );
} else {
    $obFormulario->addComponente         ( $lblSituacao                                       );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
