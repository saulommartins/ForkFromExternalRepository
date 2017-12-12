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
    * Página de Formulário do Exportação PASEP
    * Data de Criação: 29/05/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.23

    $Id: FMExportarPASEP.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

$arArquivoDownload = Sessao::read('arArquivosDownload');

$stPrograma = "ExportarPASEP";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgProcRel  = "PRRelatorioPASEP.php";
$pgDown     = "DW".$stPrograma.".php?arq=".$arArquivoDownload[0]['stLink']."&label=".$arArquivoDownload[0]['stNomeArquivo'];
$pgJS       = "JS".$stPrograma.".js";

include_once($pgJS);

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao       = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$arSessaoFiltroRelatorio = Sessao::read('filtroRelatorio');
$stAcao = $request->get('stAcao');
$stMensagem = "";

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnEtapaProcessamento =  new Hidden;
$obHdnEtapaProcessamento->setId    ( "inEtapaProcessamento" );
$obHdnEtapaProcessamento->setName  ( "inEtapaProcessamento" );
$obHdnEtapaProcessamento->setValue ( $arSessaoFiltroRelatorio["inEtapaProcessamento"] );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgDown );
$obForm->setTarget ( "telaPrincipal" );

$stTabela1 .= "<table border=0 width=100%>";

switch ($arSessaoFiltroRelatorio["inEtapaProcessamento"]) {
    case 1:
        $stTitulo   = "Exportar Arquivo PASEP FPS900";
        break;
    case 2:
        $stTitulo   = "Importar Arquivo PASEP FPS909";
        break;
    case 3:
        $stTitulo = "Importar Arquivo PASEP FPS910";
        $stFolhaPagamentoPasepAnterior = Sessao::read("stFolhaPagamentoPasepAnterior");
        $stFolhaPagamentoPasepAtual    = Sessao::read("stFolhaPagamentoPasepAtual");

        // Caso seja o primeiro lançamento
        if (trim($stFolhaPagamentoPasepAnterior) == "") {
            $stMensagem = "É necessário calcular a Folha ".($stFolhaPagamentoPasepAtual == 1 ? "Salário" : "Complementar").".";
        } else {
            if (trim($stFolhaPagamentoPasepAnterior) == trim($stFolhaPagamentoPasepAtual)) {
                $stMensagem = "Os lançamentos do PASEP na Folha ".($stFolhaPagamentoPasepAtual == 1 ? "Salário" : "Complementar")." foram deletados. É necessário recalcular.";
            } else {
                $stMensagem = "Os lançamentos do PASEP na Folha ".($stFolhaPagamentoPasepAnterior == 1 ? "Salário" : "Complementar")." foram deletados. É necessário recalcular as folhas Salário e Complementar.";
            }
        }
        break;
    case 4:
        $stTitulo   = "Exportar Arquivo PASEP FPS950";
        break;
    case 5:
        $stTitulo   = "Importar Arquivo PASEP FPS959";
        break;
    case 6:
        $stTitulo   = "Importar Arquivo PASEP FPS952";
        break;

}
$stTabela1 .= "<tr><td align=center colspan=2 width=100%><font size=3><b>".$stTitulo."</b></font></td></tr>";
$stTabela1 .= "</table>";
$stTabela1 .= "</center>";

$spnResumo = new Span();
$spnResumo->setValue($stTabela1);

$obHdnMensagem =  new Hidden;
$obHdnMensagem->setId    ( "stMensagemPasep" );
$obHdnMensagem->setName  ( "stMensagemPasep" );
$obHdnMensagem->setValue ( $stMensagem );

$obHdnTitulo =  new Hidden;
$obHdnTitulo->setId    ( "stTituloPasep" );
$obHdnTitulo->setName  ( "stTituloPasep" );
$obHdnTitulo->setValue ( $stTitulo );

$obFormulario = new Formulario();
$obFormulario->addForm                          ( $obForm                    );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"   );
$obFormulario->addHidden                        ( $obHdnAcao                 );
$obFormulario->addHidden                        ( $obHdnCtrl                 );
$obFormulario->addHidden                        ( $obHdnEtapaProcessamento   );
$obFormulario->addHidden                        ( $obHdnMensagem             );
$obFormulario->addHidden                        ( $obHdnTitulo               );
$obFormulario->addSpan($spnResumo);

switch ($arSessaoFiltroRelatorio["inEtapaProcessamento"]) {
    case 1:
    case 4:
        $obBtnImprimir = new Ok();
        $obBtnImprimir->setValue("Gerar Relatório");
        $obBtnImprimir->setStyle("width:110px;");
        $obBtnImprimir->obEvento->setOnClick("abrePopUp('".CAM_GRH_IMA_INSTANCIAS."pasep/PRRelatorioPASEP.php','frm','','','','".Sessao::getId()."','800','550')");

        $obBtnDownload = new Button();
        $obBtnDownload->setValue("Download");
        $obBtnDownload->obEvento->setOnClick("download()");

        $obFormulario->defineBarra(array($obBtnImprimir,$obBtnDownload),"center","");
        break;
    case 2:
    case 6:
    case 5:
        $obBtnImprimir = new Ok();
        $obBtnImprimir->setValue("Gerar Relatório de Erros");
        $obBtnImprimir->setStyle("width:150px;");
        $obBtnImprimir->obEvento->setOnClick("abrePopUp('".CAM_GRH_IMA_INSTANCIAS."pasep/PRRelatorioPASEP.php','frm','','','','".Sessao::getId()."','800','550')");

    $obBtnImprimir2 = new Ok();
        $obBtnImprimir2->setValue("Gerar Relatório de Conferência");
        $obBtnImprimir2->setStyle("width:190px;");
        $obBtnImprimir2->obEvento->setOnClick("abrePopUp('".CAM_GRH_IMA_INSTANCIAS."pasep/PRRelatorioPASEP.php','frm','','','','".Sessao::getId()."&stRelatorio=conferencia','800','550')");

        $obFormulario->defineBarra(array($obBtnImprimir, $obBtnImprimir2),"center","");
        break;
    case 3:
        $obBtnImprimir = new Ok();
        $obBtnImprimir->setValue("Gerar Relatório de Erros");
        $obBtnImprimir->setStyle("width:150px;");
        $obBtnImprimir->obEvento->setOnClick("abrePopUp('".CAM_GRH_IMA_INSTANCIAS."pasep/PRRelatorioPASEP.php','frm','','','','".Sessao::getId()."&stRelatorio=erro','800','550')");

        $obBtnImprimir2 = new Ok();
        $obBtnImprimir2->setValue("Gerar Relatório de Conferência");
        $obBtnImprimir2->setStyle("width:190px;");
        $obBtnImprimir2->obEvento->setOnClick("abrePopUp('".CAM_GRH_IMA_INSTANCIAS."pasep/PRRelatorioPASEP.php','frm','','','','".Sessao::getId()."&stRelatorio=conferencia','800','550')");

        $obFormulario->defineBarra(array($obBtnImprimir,$obBtnImprimir2),"center","");
        break;

}
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
