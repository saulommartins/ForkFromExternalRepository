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
* Página de Formulario para Periodo de Movimentação
* Data de Criação: 24/10/2005
Conselho

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30547 $
$Name$
$Author: tiago $
$Date: 2007-06-29 10:28:31 -0300 (Sex, 29 Jun 2007) $

* Casos de uso: uc-04.05.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php" ;
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaComplementar.class.php";

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue($stStrl);

//Define o nome dos arquivos PHP
$stAcao     = $request->get('stAcao');
$stPrograma = "ManterPeriodoMovimentacao";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$pgJS       = "OC".$stPrograma.".php";

$obErro = new Erro();
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

if ($stAcao != "mensagemincluir") {
    $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
    $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao( $rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao') );
    $obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( $obRFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoFolhaSituacao->consultarFolha();    
    if (($obRFolhaPagamentoFolhaSituacao->getSituacao() == 'Aberto') and ($stAcao != "mensagemincluir") ) {
        $obErro->setDescricao("&nbsp;&nbsp;- Folha salário aberta. Feche a folha salário para abrir um novo período de movimentação;<br>");
    }
    $obRFolhaPagamentoFolhaComplementar = new RFolhaPagamentoFolhaComplementar( $obRFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoFolhaComplementar->listarFolhaComplementar($rsFolhaComplementar);
    if (($rsFolhaComplementar->getCampo('situacao') == "a") and ($stAcao != "mensagemincluir")) {
        $obErro->setDescricao($obErro->getDescricao()."&nbsp;&nbsp;- Folha complementar aberta. Feche a folha complementar para abrir um novo período de movimentação;<br>");
    }
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php");
    $obTFolhaPagamentoLogErroCalculo = new TFolhaPagamentoLogErroCalculo();
    $obTFolhaPagamentoLogErroCalculo->recuperaTodos($rsErroCalculo);    
    if ($rsErroCalculo->getNumLinhas() > 0) {
        $obErro->setDescricao($obErro->getDescricao()."&nbsp;&nbsp;- Folha salário com erros, resolva os erros ocorridos para poder abrir um novo período de movimentação;<br>");
    }
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoComplementar.class.php");
    $obTFolhaPagamentoLogErroCalculoComplementar = new TFolhaPagamentoLogErroCalculoComplementar();
    $obTFolhaPagamentoLogErroCalculoComplementar->recuperaTodos($rsErroCalculoComplementar);    
    if ($rsErroCalculoComplementar->getNumLinhas() > 0) {
        $obErro->setDescricao($obErro->getDescricao()."&nbsp;&nbsp;- Folha complementar com erros, resolva os erros ocorridos para poder abrir um novo período de movimentação;<br>");
    }
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoFerias.class.php");
    $obTFolhaPagamentoLogErroCalculoFerias = new TFolhaPagamentoLogErroCalculoFerias();
    $obTFolhaPagamentoLogErroCalculoFerias->recuperaTodos($rsErroCalculoFerias);
    if ($rsErroCalculoFerias->getNumLinhas() > 0) {
        $obErro->setDescricao($obErro->getDescricao()."&nbsp;&nbsp;- Folha férias com erros, resolva os erros ocorridos para poder abrir um novo período de movimentação;<br>");
    }
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoDecimo.class.php");
    $obTFolhaPagamentoLogErroCalculoDecimo = new TFolhaPagamentoLogErroCalculoDecimo();
    $obTFolhaPagamentoLogErroCalculoDecimo->recuperaTodos($rsErroCalculoDecimo);    
    if ($rsErroCalculoDecimo->getNumLinhas() > 0) {
        $obErro->setDescricao($obErro->getDescricao()."&nbsp;&nbsp;- Folha décimo com erros, resolva os erros ocorridos para poder abrir um novo período de movimentação;<br>");
    }
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoRescisao.class.php");
    $obTFolhaPagamentoLogErroCalculoRescisao = new TFolhaPagamentoLogErroCalculoRescisao();
    $obTFolhaPagamentoLogErroCalculoRescisao->recuperaTodos($rsErroCalculoRescisao);
    if ($rsErroCalculoRescisao->getNumLinhas() > 0) {
        $obErro->setDescricao($obErro->getDescricao()."&nbsp;&nbsp;- Folha rescisão com erros, resolva os erros ocorridos para poder abrir um novo período de movimentação;<br>");
    }
}

if ($obErro->ocorreu()) {

    $obLblObs = new Label;
    $obLblObs->setRotulo        ( "Observação"          );
    $obLblObs->setValue         ( "Não é possível abrir um novo período de movimentação pois existe(m) o(s) seguinte(s) problema(s):<br>".$obErro->getDescricao() );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obLblObs );

} else {
    include_once ($pgJS);

    $stAcao = $request->get("stAcao");

    $obRPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obRPeriodoMovimentacao->listarUltimaMovimentacao($rsLista);

    if ($rsLista->getNumLinhas() > 0) {
        $stDataInicialAnterior = $rsLista->getCampo('dt_inicial');
        $stDataFinalAnterior   = $rsLista->getCampo('dt_final');

        //Pega a data final anterior e soma + um dia.
        $arDataInicial = explode("/", $stDataFinalAnterior);
        $stNovaDataInicial     = date( 'd/m/Y', mktime(0, 0, 0, $arDataInicial[1]  , $arDataInicial[0]+1, $arDataInicial[2]) );

        if ( date( 'd', mktime(0, 0, 0, $arDataInicial[1]  , $arDataInicial[0]+1, $arDataInicial[2]) ) == "01" ) {
            //Pega o último dia do mes da data inicial
            $arDataFinal = explode("/", $stNovaDataInicial);
            $inDia = date( 't', mktime(0, 0, 0, $arDataInicial[1]  , $arDataInicial[0]+1, $arDataInicial[2]) );;
            $stNovaDataFinal = date( 'd/m/Y', mktime(0, 0, 0, $arDataFinal[1]  , $inDia, $arDataFinal[2]) );
        } else {
            //Pega a data final anterior e soma + um mes.
            $arDataFinal = explode("/", $stDataFinalAnterior);
            $stNovaDataFinal     = date( 'd/m/Y', mktime(0, 0, 0, $arDataFinal[1]+1  , $arDataFinal[0], $arDataFinal[2]) );
        }

        //Faz o validaData com o valor do LABEL "Dara Inicial".
        $stJsValidaData = "validaData('$stNovaDataInicial');";
    } else {
        //Faz o validaData com o valor digitado no CAMPO "Data Inicial".
        $stJsValidaData = 'validaData(document.frm.stNovaDataInicial.value);';
    }

    //****************************************//
    //Define COMPONENTES DO FORMULARIO
    //****************************************//
    //Instancia o formulário
    $obForm = new Form;
    $obForm->setAction ( $pgProc );
    $obForm->setTarget ( "oculto" );

    //Define o objeto da ação stAcao
    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ( "stAcao" );
    $obHdnAcao->setValue ( $stAcao );

    $obHdnCtrl =  new Hidden;
    $obHdnCtrl->setName  ( "stCtrl" );
    $obHdnCtrl->setValue ( $stStrl  );

    //Define a nova data inicial quando existe o label.
    $obHdnNovaDataInicial = new Hidden;
    $obHdnNovaDataInicial->setName  ( "hdnNovaDataInicial" );
    $obHdnNovaDataInicial->setValue ( $stNovaDataInicial );

    //Define objeto LABEL para armazenar a DATA INICIAL do Periodo Anterior
    $obLblDataInicialAnterior = new Label;
    $obLblDataInicialAnterior->setRotulo ( 'Data Inicial'           );
    $obLblDataInicialAnterior->setName   ( 'lblDataInicialAnterior' );
    $obLblDataInicialAnterior->setId     ( 'lblDataInicialAnterior' );
    $obLblDataInicialAnterior->setValue  ( $stDataInicialAnterior   );

    //Define objeto LABEL para armazenar a DATA FINAL do Periodo Anterior
    $obLblDataFinalAnterior = new Label;
    $obLblDataFinalAnterior->setRotulo ( 'Data Final'           );
    $obLblDataFinalAnterior->setName   ( 'lblDataFinalAnterior' );
    $obLblDataFinalAnterior->setId     ( 'lblDataFinalAnterior' );
    $obLblDataFinalAnterior->setValue  ( $stDataFinalAnterior   );

    //Define objeto LABEL para armazenar a DATA INICIAL do Novo Periodo de Movimentação (Usado quando já existe algum periodo)
    $obLblNovaDataInicial = new Label;
    $obLblNovaDataInicial->setRotulo ( 'Data Inicial'           );
    $obLblNovaDataInicial->setName   ( 'lblNovaDataInicial' );
    $obLblNovaDataInicial->setId     ( 'lblNovaDataInicial' );
    $obLblNovaDataInicial->setValue  ( $stNovaDataInicial   );

    //Define objeto DATA para armazenar a DATA INICIAL do Novo Periodo de Movimentação (Usado quando NÃO existe NENHUM periodo)
    $obDtNovaDataInicial = new Data;
    $obDtNovaDataInicial->setRotulo ( "Data Inicial"      );
    $obDtNovaDataInicial->setTitle  ( "Informe a data inicial referente à movimentação da folha." );
    $obDtNovaDataInicial->setName   ( "stNovaDataInicial" );
    $obDtNovaDataInicial->setId     ( "stNovaDataInicial" );
    $obDtNovaDataInicial->setValue  ( $stNovaDataInicial  );
    $obDtNovaDataInicial->setNull   ( false               );

    //Define objeto DATA para armazenar a DATA FINAL do Novo Periodo de Movimentação
    $obDtNovaDataFinal = new Data;
    $obDtNovaDataFinal->setRotulo ( "Data Final"          );
    $obDtNovaDataFinal->setTitle  ( "Informe a data final referente à movimentação da folha." );
    $obDtNovaDataFinal->setName   ( "stNovaDataFinal"     );
    $obDtNovaDataFinal->setId     ( "stNovaDataFinal"     );
    $obDtNovaDataFinal->setValue  ( $stNovaDataFinal    );
    $obDtNovaDataFinal->setNull   ( false                 );
    $obDtNovaDataFinal->obEvento->setOnChange ( $stJsValidaData );

    $obBntOk = new ok();
    $obBntOk->obEvento->setOnClick( "buscaValorFiltro('submeter');" );

    $obBtnLimpar = new Limpar;

    //Define objeto LABEL para armazenar a observação
    $obLblObs = new Label;
    $obLblObs->setRotulo        ( "Observação"          );
    if ($stAcao == 'mensagemincluir') {
        $obLblObs->setValue         ( "Um novo período foi aberto com sucesso, toda e qualquer operação efetuada no sistema a partir deste momento estará diretamente ligada ao novo período vigente. <br/> Efetuado cálculo da folha com sucesso." );
    } else {
        $obLblObs->setValue         ( "O período atual foi fechado e todos os dados referentes ao período excluído foram removidos do sistema, o período anterior foi reaberto com sucesso, toda e qualquer operação efetuada no sistema a partir deste momento estará diretamente ligada ao novo período vigente." );
    }

    //****************************************//
    //Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm   ( $obForm );
    $obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
    $obFormulario->addHidden ( $obHdnAcao );
    $obFormulario->addHidden ( $obHdnCtrl );
    if ($rsLista->getNumLinhas() > 0) {
        if ($stAcao == 'incluir') {
            $obFormulario->addTitulo     ( "Período Anterior" );
        } else {
            $obFormulario->addTitulo     ( "Período Atual Vigente" );
        }
        $obFormulario->addComponente ( $obLblDataInicialAnterior   );
        $obFormulario->addComponente ( $obLblDataFinalAnterior     );
        $obFormulario->addHidden ( $obHdnNovaDataInicial );
    }

    if ($stAcao == 'incluir') {
        $obFormulario->addTitulo     ( "Novo Período de Movimentação" );
        if ($rsLista->getNumLinhas() > 0) {
            $obFormulario->addComponente ( $obLblNovaDataInicial );
        } else {
            $obFormulario->addComponente ( $obDtNovaDataInicial  );
        }

        $obFormulario->addComponente ( $obDtNovaDataFinal );
        $obFormulario->defineBarra(array( $obBntOk, $obBtnLimpar ));

        if ($rsLista->getNumLinhas() > 0) {
            $obFormulario->setFormFocus($obDtNovaDataFinal->getId() );
        } else {
            $obFormulario->setFormFocus($obDtNovaDataInicial->getId() );
        }
    } else {
        $obFormulario->addComponente( $obLblObs );

    }
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';