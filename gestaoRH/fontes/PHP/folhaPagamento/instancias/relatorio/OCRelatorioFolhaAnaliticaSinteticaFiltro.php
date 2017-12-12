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
    * Oculto de Relatório da Folha Analítica/Sintética
    * Data de Criação: 21/03/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-19 15:41:28 -0300 (Qua, 19 Mar 2008) $

    * Casos de uso: uc-04.05.50
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaComplementar.class.php"                         );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php"                                    );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                      );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploRegSubCarEsp.class.php"                          );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                               );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                 );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploBanco.class.php"                                 );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPrevidencia.class.php"                               );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioFolhaAnaliticaSintetica";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function gerarSpan1($boExecuta=false)
{
    if ($_POST['boFiltrarFolhaComplementar']) {
        $stDtFinal = $_POST['inAno']."-".str_pad($_POST['inCodMes'], 2, "0", STR_PAD_LEFT);
        $obRFolhaPagamentoFolhaComplementar = new RFolhaPagamentoFolhaComplementar(new RFolhaPagamentoPeriodoMovimentacao);
        $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->setDtFinal( $stDtFinal );
        $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->listarPeriodoMovimentacao($rsMovimentacao);
        if ( $rsMovimentacao->getNumLinhas() > 0 ) {
            $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao($rsMovimentacao->getCampo('cod_periodo_movimentacao'));
            $obRFolhaPagamentoFolhaComplementar->listarFolhaComplementar($rsFolhaComplementar);
        } else {
            $rsFolhaComplementar = new Recordset;
        }
        $obCmbFolhaComplementar = new Select;
        $obCmbFolhaComplementar->setRotulo                  ( "Folha Complementar"                          );
        $obCmbFolhaComplementar->setTitle                   ( "Selecione a folha complementar."             );
        $obCmbFolhaComplementar->setName                    ( "inCodComplementar"                           );
        $obCmbFolhaComplementar->setValue                   ( $inCodComplementar                            );
        $obCmbFolhaComplementar->setStyle                   ( "width: 200px"                                );
        $obCmbFolhaComplementar->addOption                  ( "", "Selecione"                               );
        $obCmbFolhaComplementar->setCampoID                 ( "[cod_complementar]"                          );
        $obCmbFolhaComplementar->setCampoDesc               ( "[cod_complementar]"                          );
        $obCmbFolhaComplementar->preencheCombo              ( $rsFolhaComplementar                          );

        $obFormulario = new Formulario;
        $obFormulario->addComponente                        ( $obCmbFolhaComplementar                       );
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    } else {
        $inCodConfiguracao  = ($inCodConfiguracao) ? $inCodConfiguracao : 1;

        $obTxtTipoCalculo= new TextBox;
        $obTxtTipoCalculo->setRotulo                        ( "Tipo de Cálculo"                             );
        $obTxtTipoCalculo->setTitle                         ( "Selecione o tipo de cálculo."                );
        $obTxtTipoCalculo->setName                          ( "inCodConfiguracao"                           );
        $obTxtTipoCalculo->setValue                         ( $inCodConfiguracao                            );
        $obTxtTipoCalculo->setSize                          ( 6                                             );
        $obTxtTipoCalculo->setMaxLength                     ( 3                                             );
        $obTxtTipoCalculo->setInteiro                       ( true                                          );

        $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
        $obRFolhaPagamentoEvento->addConfiguracaoEvento();
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento($rsConfiguracaoEvento);
        $obCmbTipoCalculo = new Select;
        $obCmbTipoCalculo->setRotulo                        ( "Tipo de Cálculo"                             );
        $obCmbTipoCalculo->setTitle                         ( "Selecione o tipo de cálculo."                );
        $obCmbTipoCalculo->setName                          ( "stConfiguracao"                              );
        $obCmbTipoCalculo->setValue                         ( $inCodConfiguracao                            );
        $obCmbTipoCalculo->setStyle                         ( "width: 200px"                                );
        //$obCmbTipoCalculo->addOption                        ( "", "Selecione"                               );
        $obCmbTipoCalculo->setCampoID                       ( "[cod_configuracao]"                          );
        $obCmbTipoCalculo->setCampoDesc                     ( "[descricao]"                                 );
        $obCmbTipoCalculo->preencheCombo                    ( $rsConfiguracaoEvento                         );

        $obFormulario = new Formulario;
        $obFormulario->addComponenteComposto                ( $obTxtTipoCalculo,$obCmbTipoCalculo           );
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spSpan1').innerHTML = '$stHtml';";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan2($boExecuta=false)
{
    if ( $_POST['stTipoFiltro'] == 'contrato' or !ISSET($_POST['stTipoFiltro']) ) {
        $obIFiltroContrato = new IFiltroContrato("todos");

        $obFormulario = new Formulario;
        $obIFiltroContrato->geraFormulario                      ( $obFormulario                                 );
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    } else {
        $stHtml = "";
    }

    $stJs .= "d.getElementById('spSpan2').innerHTML = '$stHtml';";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan3($boExecuta=false)
{
    if ($_POST['stTipoFiltro'] == 'cgm_contrato') {
        $obIFiltroCGMContrato = new IFiltroCGMContrato(true);

        $obFormulario = new Formulario;
        $obIFiltroCGMContrato->geraFormulario                   ( $obFormulario                                 );
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    } else {
        $stHtml = "";
    }

    $stJs .= "d.getElementById('spSpan2').innerHTML = '$stHtml';";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan4($boExecuta=false)
{
    if ( $_POST['stTipoFiltro'] == 'cgm_contrato' OR $_POST['stTipoFiltro'] == 'contrato' OR !ISSET($_POST['stTipoFiltro']) ) {
        $obBtnIncluir = new Button;
        $obBtnIncluir->setName                          ( "btnIncluir"                                      );
        $obBtnIncluir->setValue                         ( "Incluir"                                         );
        $obBtnIncluir->obEvento->setOnClick             ( "buscaValor('incluirContrato');"                  );

        $obBtnLimpar = new Button;
        $obBtnLimpar->setName                           ( "btnLimpar"                                       );
        $obBtnLimpar->setValue                          ( "Limpar"                                          );
        $obBtnLimpar->obEvento->setOnClick              ( "buscaValor('limparContrato');"                   );

        $obFormulario = new Formulario;
        $obFormulario->defineBarra                      ( array($obBtnIncluir,$obBtnLimpar),'',''           );
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    } else {
        $stHtml = "";
    }

    $stJs .= "d.getElementById('spSpan4').innerHTML = '$stHtml';";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan5($boExecuta=false)
{
    if ( count(Sessao::read('arContratos')) ) {
        $rsContratos = new recordset;
        $rsContratos->preenche(Sessao::read('arContratos'));
        $rsContratos->addFormatacao("cgm","HTML");
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Matrículas para o Filtro" );
        $obLista->setRecordSet( $rsContratos );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Matrícula" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "CGM" );
        $obLista->ultimoCabecalho->setWidth( 45 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "contrato" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "cgm" );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "Excluir" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "Javascript:excluirContrato();" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    } else {
        $stHtml = "";
    }

    $stJs .= "d.getElementById('spSpan5').innerHTML = '$stHtml';";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan7($boExecuta=false)
{
    if ($_POST['stTipoFiltro'] == 'geral') {
        $obChkFiltrarRegSubCarEsp = new CheckBox;
        $obChkFiltrarRegSubCarEsp->setName         ( "boFiltrarRegSubCarEsp"                                );
        $obChkFiltrarRegSubCarEsp->setRotulo       ( "Filtrar por Regime/Subdivisão/ Cargo/Especialidade"   );
        $obChkFiltrarRegSubCarEsp->setTitle        ( "Informe se deseja filtrar por regime/subdivisão/cargo/especialidade.");
        $obChkFiltrarRegSubCarEsp->setValue        ( true                                                   );
        $obChkFiltrarRegSubCarEsp->obEvento->setOnChange( "buscaValor('gerarSpan8');"                       );

        $obSpnSpan8 = new Span;
        $obSpnSpan8->setId                         ( "spSpan8"                                              );

        $obChkFiltrarRegSubFunEsp = new CheckBox;
        $obChkFiltrarRegSubFunEsp->setName         ( "boFiltrarRegSubFunEsp"                                );
        $obChkFiltrarRegSubFunEsp->setRotulo       ( "Filtrar por Regime/Subdivisão/ Função/Especialidade"  );
        $obChkFiltrarRegSubFunEsp->setTitle        ( "Informe se deseja filtrar por regime/subdivisão/função/especialidade.");
        $obChkFiltrarRegSubFunEsp->setValue        ( true                                                   );
        $obChkFiltrarRegSubFunEsp->obEvento->setOnChange( "buscaValor('gerarSpan9');"                       );

        $obSpnSpan9 = new Span;
        $obSpnSpan9->setId                         ( "spSpan9"                                              );

        $obChkFiltrarPorPadrao = new CheckBox;
        $obChkFiltrarPorPadrao->setName             ( "boFiltrarPorPadrao"                                  );
        $obChkFiltrarPorPadrao->setRotulo           ( "Filtrar por Padrão"                                  );
        $obChkFiltrarPorPadrao->setTitle            ( "Informe se deseja filtrar por padrão."               );
        $obChkFiltrarPorPadrao->setValue            ( true                                                  );
        $obChkFiltrarPorPadrao->obEvento->setOnChange( "buscaValor('gerarSpan10');"                         );

        $obSpnSpan10 = new Span;
        $obSpnSpan10->setId                         ( "spSpan10"                                            );

        $obChkFiltrarPorLotacao = new CheckBox;
        $obChkFiltrarPorLotacao->setName            ( "boFiltrarPorLotacao"                                 );
        $obChkFiltrarPorLotacao->setRotulo          ( "Filtrar por Lotação"                                 );
        $obChkFiltrarPorLotacao->setTitle           ( "Informe se deseja filtrar por lotação."              );
        $obChkFiltrarPorLotacao->setValue           ( true                                                  );
        $obChkFiltrarPorLotacao->obEvento->setOnChange( "buscaValor('gerarSpan11');"                        );

        $obSpnSpan11 = new Span;
        $obSpnSpan11->setId                         ( "spSpan11"                                            );

        $obChkFiltrarPorLocal = new CheckBox;
        $obChkFiltrarPorLocal->setName              ( "boFiltrarPorLocal"                                   );
        $obChkFiltrarPorLocal->setRotulo            ( "Filtrar por Local"                                   );
        $obChkFiltrarPorLocal->setTitle             ( "Informe se deseja filtrar por local."                );
        $obChkFiltrarPorLocal->setValue             ( true                                                  );
        $obChkFiltrarPorLocal->obEvento->setOnChange( "buscaValor('gerarSpan12');"                          );

        $obSpnSpan12 = new Span;
        $obSpnSpan12->setId                         ( "spSpan12"                                            );

        $obChkFiltrarPorBanco = new CheckBox;
        $obChkFiltrarPorBanco->setName              ( "boFiltrarPorBanco"                                   );
        $obChkFiltrarPorBanco->setRotulo            ( "Filtrar por Banco"                                   );
        $obChkFiltrarPorBanco->setTitle             ( "Informe se deseja filtrar por Banco."                );
        $obChkFiltrarPorBanco->setValue             ( true                                                  );
        $obChkFiltrarPorBanco->obEvento->setOnChange( "buscaValor('gerarSpan13');"                          );

        $obSpnSpan13 = new Span;
        $obSpnSpan13->setId                         ( "spSpan13"                                            );

        $obChkFiltrarPorPrevidencia = new CheckBox;
        $obChkFiltrarPorPrevidencia->setName              ( "boFiltrarPorPrevidencia"                       );
        $obChkFiltrarPorPrevidencia->setRotulo            ( "Filtrar por Previdência"                       );
        $obChkFiltrarPorPrevidencia->setTitle             ( "Informe se deseja filtrar por Previdência."    );
        $obChkFiltrarPorPrevidencia->setValue             ( true                                            );
        $obChkFiltrarPorPrevidencia->obEvento->setOnChange( "buscaValor('gerarSpan14');"                    );

        $obSpnSpan14 = new Span;
        $obSpnSpan14->setId                               ( "spSpan14"                                      );

        $obChkAtivo = new CheckBox;
        $obChkAtivo->setName                        ( "boAtivo"                                             );
        $obChkAtivo->setRotulo                      ( "Situação Servidor"                                   );
        $obChkAtivo->setLabel                       ( "Ativo"                                               );
        $obChkAtivo->setTitle                       ( "Selecione a(s) Situação(ões)."                       );
        $obChkAtivo->setValue                       ( true                                                  );

        $obChkRescindido = new CheckBox;
        $obChkRescindido->setName                   ( "boRescindido"                                        );
        $obChkRescindido->setRotulo                 ( "Situação Servidor"                                   );
        $obChkRescindido->setLabel                  ( "Rescindido"                                          );
        $obChkRescindido->setTitle                  ( "Selecione a(s) Situação(ões)."                       );
        $obChkRescindido->setValue                  ( true                                                  );

        $obChkInativo = new CheckBox;
        $obChkInativo->setName                      ( "boInativo"                                           );
        $obChkInativo->setRotulo                    ( "Situação Servidor"                                   );
        $obChkInativo->setLabel                     ( "Aposentado"                                          );
        $obChkInativo->setTitle                     ( "Selecione a(s) Situação(ões)."                       );
        $obChkInativo->setValue                     ( true                                                  );

        $obChkPensionista = new CheckBox;
        $obChkPensionista->setName                  ( "boPensionista"                                       );
        $obChkPensionista->setRotulo                ( "Situação Servidor"                                   );
        $obChkPensionista->setLabel                 ( "Pensionista"                                         );
        $obChkPensionista->setTitle                 ( "Selecione a(s) Situação(ões)."                       );
        $obChkPensionista->setValue                 ( true                                                  );

        switch ($_POST['stFolha']) {
            case 'analítica':
                $arCampos = array('Banco'               =>'Banco',
                                  'Lotacao'             =>'Lotação',
                                  'Local'               =>'Local',
                                  'RegimedoCargo'       =>'Regime do Cargo',
                                  'SubdivisaodoCargo'   =>'Subdivisão do Cargo',
                                  'Cargo'               =>'Cargo',
                                  'EspecialidadedoCargo'=>'Especialidade do Cargo',
                                  'RegimedaFuncao'      =>'Regime da Função',
                                  'SubdivisaodaFuncao'  =>'Subdivisão da Função',
                                  'Funcao'              =>'Função',
                                  'EspecialidadedaFuncao'=>'Especialidade da Função',
                                  'Situacao'            =>'Situação',
                                  'Cgm'                 =>'CGM');
            break;
            case "analítica_resumida":
                $arCampos = array('Banco'               =>'Banco',
                                  'Lotacao'             =>'Lotação',
                                  'Local'               =>'Local',
                                  'RegimedaFuncao'      =>'Regime da Função',
                                  'Funcao'              =>'Função',
                                  'Cgm'                 =>'CGM');

            break;
            case "sintética":
                $arCampos = array('Banco'               =>'Banco',
                                  'Lotacao'             =>'Lotação',
                                  'Cgm'                 =>'CGM');
            break;
        }

        $inCount = 0;
        foreach ($arCampos as $stName=>$stLabel) {
            $stNomeCampo = "obChk".$stName;
            $$stNomeCampo = new CheckBox;
            $$stNomeCampo->setName                  ( "arrayOrdenacao[bo".$stName."]"                            );
            $$stNomeCampo->setId                    ( "obChk".$inCount                                           );
            $$stNomeCampo->setStyle                 ( "" );
            $$stNomeCampo->setRotulo                ( "Ordenação/Agrupar Dados"                             );
            $$stNomeCampo->setLabel                 ( $stLabel                                              );
            $$stNomeCampo->setTitle                 ( "Selecionando uma das opções será habilitada a opção para ordenação da informação no relatório."                       );
            $$stNomeCampo->setValue                 ( true                                                  );

            $stNomeCampo = "obCmbNumAlf".$stName;
            $$stNomeCampo = new Select;
            $$stNomeCampo->setRotulo                ( "Ordenação/Agrupar Dados"                             );
            $$stNomeCampo->setTitle                 ( "Selecionando uma das opções será habilitada a opção para ordenação da informação no relatório."                       );
            $$stNomeCampo->setName                  ( "stAlfNum".$stName                                    );
            $$stNomeCampo->setId                    ( "stAlfNum".$inCount                                   );
            $$stNomeCampo->setStyle                 ( "width: 100px" );
            $$stNomeCampo->addOption                ( "numerica","Numérica"                                 );
            $$stNomeCampo->addOption                ( "alfabetica","Alfabética"                             );
            $inCount++;
        }

        $obChkEmitirTotais = new CheckBox;
        $obChkEmitirTotais->setName                 ( "boEmitirTotais"                                      );
        $obChkEmitirTotais->setRotulo               ( "Emitir Totais por Agrupamento"                       );
        $obChkEmitirTotais->setTitle                ( "Selecionando a opção o relatório apresentará os totais ao final de cada agrupamento." );
        $obChkEmitirTotais->setValue                ( true                                                  );

        $obChkEmitirRelatorio = new CheckBox;
        $obChkEmitirRelatorio->setName              ( "boEmitirRelatorio"                                   );
        $obChkEmitirRelatorio->setRotulo            ( "Emitir Somente Relatório de Totais"                  );
        $obChkEmitirRelatorio->setTitle             ( "Selecionando a opção o relatório apresentará somente os totais dos agrupamentos." );
        $obChkEmitirRelatorio->setValue             ( true                                                  );

        $obFormulario = new Formulario;
        $obFormulario->addComponente                ( $obChkFiltrarRegSubCarEsp                             );
        $obFormulario->addSpan                      ( $obSpnSpan8                                           );
        $obFormulario->addComponente                ( $obChkFiltrarRegSubFunEsp                             );
        $obFormulario->addSpan                      ( $obSpnSpan9                                           );
        $obFormulario->addComponente                ( $obChkFiltrarPorPadrao                                );
        $obFormulario->addSpan                      ( $obSpnSpan10                                          );
        $obFormulario->addComponente                ( $obChkFiltrarPorLotacao                               );
        $obFormulario->addSpan                      ( $obSpnSpan11                                          );
        $obFormulario->addComponente                ( $obChkFiltrarPorLocal                                 );
        $obFormulario->addSpan                      ( $obSpnSpan12                                          );
        $obFormulario->addComponente                ( $obChkFiltrarPorBanco                                 );
        $obFormulario->addSpan                      ( $obSpnSpan13                                          );
        $obFormulario->addComponente                ( $obChkFiltrarPorPrevidencia                           );
        $obFormulario->addSpan                      ( $obSpnSpan14                                          );

        $obFormulario->agrupaComponentes            ( array($obChkAtivo,$obChkRescindido,$obChkInativo,$obChkPensionista)    );

        $obFormulario->abreLinha();
        $obFormulario->addRotulo( "", "Ordenação/Agrupar Dados", count( $arCampos ) );

        $inCount = 0;
        foreach ($arCampos as $stName=>$stLabel) {

            $obImgDesce = new Img();
            $obImgDesce->setTitle("Desce");
            $obImgDesce->obEvento->setOnClick(" trocaOrdenacao(".$inCount.", ".($inCount+1)."); ");
            $obImgDesce->setCaminho(CAM_FW_IMAGENS."botao_expandir15px.png");
            $obImgDesce->setStyle("float:left; margin-right:3px; margin-top:2px; width:16px; height:15px;");
            $obImgDesce->montaHtml();

            $obImgSobe = new Img();
            $obImgSobe->setTitle("Sobe");
            $obImgSobe->obEvento->setOnClick(" trocaOrdenacao(".$inCount.", ".($inCount-1)."); ");
            $obImgSobe->setCaminho(CAM_FW_IMAGENS."botao_retrair15px.png");
            $obImgSobe->setStyle("float:left; margin-right:3px; margin-top:2px; width:16px; height:15px;");
            $obImgSobe->montaHtml();

            $stNomeCampoCheck  = "obChk".$stName;
            $$stNomeCampoCheck->montaHtml();

            $stNomeCampoCombo  = "obCmbNumAlf".$stName;
            $$stNomeCampoCombo->montaHtml();

            $stNomeCampoSpan = "obSpan".$stName;
            $$stNomeCampoSpan = new Label;
            $$stNomeCampoSpan->setValue( $$stNomeCampoCombo->getHtml() . $$stNomeCampoCheck->getHtml() );
            $$stNomeCampoSpan->setId   ( "span".($inCount)  );

            if ($inCount == 0) {
                $obImgSobe->setCaminho(CAM_FW_IMAGENS."ftv2blank.gif");
                $obImgSobe->obEvento->setOnClick("");
            } elseif ($inCount == sizeof($arCampos)-2) {
                $obImgDesce->setCaminho(CAM_FW_IMAGENS."ftv2blank.gif");
                $obImgDesce->obEvento->setOnClick("");
            } elseif ($stName == "Cgm") {
                $obImgSobe->setCaminho(CAM_FW_IMAGENS."ftv2blank.gif");
                $obImgSobe->obEvento->setOnClick("");

                $obImgDesce->setCaminho(CAM_FW_IMAGENS."ftv2blank.gif");
                $obImgDesce->obEvento->setOnClick("");
            }

            $obFormulario->addCampo( $obImgDesce, true, false );
            $obFormulario->ultimaLinha->ultimaCelula->setNoWRap( true );
            $obFormulario->addCampo( $obImgSobe, false, false );
            $obFormulario->addCampo( $$stNomeCampoSpan, false, true );

            $obFormulario->fechaLinha();
            if ( $inCount++ < count( $arCampos ) ) {
                $obFormulario->abreLinha();
            }
        }

        $obFormulario->addComponente                ( $obChkEmitirTotais                                    );
        $obFormulario->addComponente                ( $obChkEmitirRelatorio                                 );
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    } else {
        $stHtml = "";
    }

    $stJs .= "d.getElementById('spSpan2').innerHTML = '$stHtml';";

    //Zera Conteudo para Spans com conteudo JS a executar
    //bem como das Spans com conteudo JS de Selects Multiplos
    $stJs .= "f.hdnSpans.value = '';";
    for ($i=8;$i<14;$i++) {
        $stJs .= "if(f.hdnSpans".$i.") f.hdnSpans".$i.".value = '';";
    }

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan8($boExecuta=false)
{
    if ( isset($_POST['boFiltrarRegSubCarEsp']) ) {
        $obISelectMultiploRegSubCarEsp = new ISelectMultiploRegSubCarEsp;
        $obFormulario = new Formulario;
        $obISelectMultiploRegSubCarEsp->geraFormulario( $obFormulario );
        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    } else {
        $stHtml = "";
        $stEval = "";
    }

    $stJs .= "d.getElementById('spSpan8').innerHTML = '$stHtml';        \n";
    $stJs .= "f.hdnSpans8.value                     = '".$stEval."';   \n";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan9($boExecuta=false)
{
    if ( isset($_POST['boFiltrarRegSubFunEsp']) ) {
        $obISelectMultiploRegSubFunEsp = new ISelectMultiploRegSubCarEsp(true);
        $obFormulario = new Formulario;
        $obISelectMultiploRegSubFunEsp->geraFormulario( $obFormulario );
        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    } else {
        $stHtml = "";
        $stEval = "";
    }

    $stJs .= "d.getElementById('spSpan9').innerHTML = '$stHtml';";
    $stJs .= "f.hdnSpans9.value                     = '".$stEval."';   \n";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan10($boExecuta=false)
{
    if ( isset($_POST['boFiltrarPorPadrao']) ) {
        $obFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
        $obFolhaPagamentoPadrao->listarPadrao($rsPadrao);
        $obCmbPadrao = new SelectMultiplo();
        $obCmbPadrao->setName              ( 'inCodPadrao'                                                 );
        $obCmbPadrao->setRotulo            ( "Padrão"                                                      );
        $obCmbPadrao->setTitle             ( "Selecione o(s) padrão(ões)."                                 );
        $obCmbPadrao->SetNomeLista1        ( 'inCodPadraoDisponiveis'                                      );
        $obCmbPadrao->setCampoId1          ( '[cod_padrao]'                                                );
        $obCmbPadrao->setCampoDesc1        ( '[descricao]'                                                 );
        $obCmbPadrao->setStyle1            ( "width: 300px"                                                );
        $obCmbPadrao->SetRecord1           ( $rsPadrao                                                     );
        $obCmbPadrao->SetNomeLista2        ( 'inCodPadraoSelecionados'                                     );
        $obCmbPadrao->setCampoId2          ( '[cod_Padrao]'                                                );
        $obCmbPadrao->setCampoDesc2        ( '[descricao]'                                                 );
        $obCmbPadrao->setStyle2            ( "width: 300px"                                                );
        $obCmbPadrao->SetRecord2           ( new recordset                                                 );

        $obFormulario = new Formulario;
        $obFormulario->addComponente( $obCmbPadrao );
        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    } else {
        $stHtml = "";
        $stEval = "";
    }

    $stJs .= "d.getElementById('spSpan10').innerHTML = '$stHtml';";
    $stJs .= "f.hdnSpans10.value                     = '".$stEval."';   \n";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan11($boExecuta=false)
{
    if ( isset($_POST['boFiltrarPorLotacao']) ) {
        $obISelectMultiploLotacao = new ISelectMultiploLotacao();
        $obFormulario = new Formulario;
        $obFormulario->addComponente($obISelectMultiploLotacao);
        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
        
        $obCheckSubNivelLotacao = new CheckBox();
        $obCheckSubNivelLotacao->setRotulo ('Subníveis da lotação');
        $obCheckSubNivelLotacao->setTitle  ('Selecionar para que sejam incluídos os subníveis dos orgãos das lotações relacionadas.');
        $obCheckSubNivelLotacao->setId     ('boSubNivelLotacao');
        $obCheckSubNivelLotacao->setName   ('boSubNivelLotacao');
        $obCheckSubNivelLotacao->setValue  (true);

        $obFormulario->addComponente($obCheckSubNivelLotacao);
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
        $stJsExtra = atualizarLotacao();
        $stJsExtra = str_replace("jQuery", "jq_", $stJsExtra);
    } else {
        $stHtml = "";
        $stEval = "";
    }

    $stJs .= "d.getElementById('spSpan11').innerHTML = '$stHtml';";
    $stJs .= $stJsExtra;
    $stJs .= "f.hdnSpans11.value = '".$stEval."';\n";

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan12($boExecuta=false)
{
    if ( isset($_POST['boFiltrarPorLocal']) ) {
        $obISelectMultiploLocal = new ISelectMultiploLocal();
        $obFormulario = new Formulario;
        $obFormulario->addComponente($obISelectMultiploLocal);
        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    } else {
        $stHtml = "";
        $stEval = "";
    }

    $stJs .= "d.getElementById('spSpan12').innerHTML = '$stHtml';";
    $stJs .= "f.hdnSpans12.value                     = '".$stEval."';   \n";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan13($boExecuta=false)
{
    if ( isset($_POST['boFiltrarPorBanco']) ) {
        $obISelectMultiploBanco = new ISelectMultiploBanco(false);

        $obFormulario = new Formulario;
        $obFormulario->addComponente($obISelectMultiploBanco);
        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    } else {
        $stHtml = "";
        $stEval = "";
    }

    $stJs .= "d.getElementById('spSpan13').innerHTML = '$stHtml';";
    $stJs .= "f.hdnSpans13.value                     = '".$stEval."';\n";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan14($boExecuta=false)
{
    if ( isset($_POST['boFiltrarPorPrevidencia']) ) {
        $rsPrevidencia = new Recordset;
        $obRFolhaPagamentoPrevidencia   = new RFolhaPagamentoPrevidencia;
        $obRFolhaPagamentoPrevidencia->listarPrevidenciasOficiais($rsPrevidencia);

        $obCmbPrevidencia = new SelectMultiplo;
        $obCmbPrevidencia->SetName           ( 'inCodPrevidencia'                );
        $obCmbPrevidencia->SetRotulo         ( "Previdência"                     );
        $obCmbPrevidencia->setTitle          ( "Selecione o(as) Previdência(as).");
        $obCmbPrevidencia->SetNomeLista1     ( "inCodPrevidenciaDisponivel"      );
        $obCmbPrevidencia->SetRotulo         ( "Previdência"                     );
        $obCmbPrevidencia->setCampoId1       ( "[cod_previdencia]"               );
        $obCmbPrevidencia->setCampoDesc1     ( "[descricao]"                     );
        $obCmbPrevidencia->Setrecord1        ( $rsPrevidencia                    );
        $obCmbPrevidencia->SetNomeLista2     ( "inCodPrevidenciaSelecionados"    );
        $obCmbPrevidencia->setCampoId2       ( "[cod_previdencia]"               );
        $obCmbPrevidencia->setCampoDesc2     ( "[descricao]"                     );
        $obCmbPrevidencia->Setrecord2        (  new RecordSet                    );

        $obFormulario = new Formulario;
        $obFormulario->addComponente( $obCmbPrevidencia );
        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    } else {
        $stHtml = "";
        $stEval = "";
    }

    $stJs .= "d.getElementById('spSpan14').innerHTML = '$stHtml';";
    $stJs .= "f.hdnSpans14.value                     = '".$stEval."';   \n";

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpanAtributos()
{
    include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
    $obRPessoalServidor = new RPessoalServidor();
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

    $obCmbAtributo = new Select();
    $obCmbAtributo->setRotulo("Atributo Dinâmico");
    $obCmbAtributo->setName("inCodAtributo");
    $obCmbAtributo->setTitle("Selecione o atributo dinâmico para filtro.");
    $obCmbAtributo->setNull(false);
    $obCmbAtributo->setCampoDesc("nom_atributo");
    $obCmbAtributo->setCampoId("cod_atributo");
    $obCmbAtributo->addOption("","Selecione");
    $obCmbAtributo->preencheCombo($rsAtributos);
    $obCmbAtributo->obEvento->setOnChange("buscaValor('gerarSpanAtributosDinamicos');");

    $obSpnAtributo = new Span();
    $obSpnAtributo->setId("spnAtributo");

    $obChkAtributoDinamico = new CheckBox;
    $obChkAtributoDinamico->setName                  ( "boAtributoDinamico"                                          );
    $obChkAtributoDinamico->setStyle                 ( "" );
    $obChkAtributoDinamico->setRotulo                ( "Ordenar/Agrupar Dados"                             );
    $obChkAtributoDinamico->setTitle                 ( "Selecionando está opção será habilitada a opção para ordenar da informação no relatório."                       );
    $obChkAtributoDinamico->setValue                 ( true                                                  );

    $obChkEmitirTotais = new CheckBox;
    $obChkEmitirTotais->setName                 ( "boEmitirTotais"                                      );
    $obChkEmitirTotais->setRotulo               ( "Emitir Totais por Agrupamento"                       );
    $obChkEmitirTotais->setTitle                ( "Selecionando a opção o relatório apresentará os totais ao final de cada agrupamento." );
    $obChkEmitirTotais->setValue                ( true                                                  );

    $obChkEmitirRelatorio = new CheckBox;
    $obChkEmitirRelatorio->setName              ( "boEmitirRelatorio"                                   );
    $obChkEmitirRelatorio->setRotulo            ( "Emitir Somente Relatório de Totais"                  );
    $obChkEmitirRelatorio->setTitle             ( "Selecionando a opção o relatório apresentará somente os totais dos agrupamentos." );
    $obChkEmitirRelatorio->setValue             ( true                                                  );

    $obFormulario = new Formulario();
    $obFormulario->addComponente($obCmbAtributo);
    $obFormulario->addSpan($obSpnAtributo);
    $obFormulario->addComponente($obChkAtributoDinamico);
    $obFormulario->addComponente($obChkEmitirTotais);
    $obFormulario->addComponente($obChkEmitirRelatorio);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stJs .= "d.getElementById('spSpan2').innerHTML = '".$obFormulario->getHTML()."';\n";
    $stJs .= "f.hdnSpans.value = '$stEval';                 \n";

    return $stJs;
}

function gerarSpanAtributosDinamicos()
{
    if ($_REQUEST['inCodAtributo'] != "") {
        include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
        $obRPessoalServidor = new RPessoalServidor();
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->setChavePersistenteValores( array('cod_atributo'=>$_REQUEST['inCodAtributo']) );
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributo_"  );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );

        $obHdnCodCadastro = new hidden();
        $obHdnCodCadastro->setName("inCodCadastro");
        $obHdnCodCadastro->setValue($rsAtributos->getCampo("cod_cadastro"));

        $obFormulario = new Formulario();
        $obFormulario->addHidden($obHdnCodCadastro);
        $obMontaAtributos->geraFormulario( $obFormulario );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $Js = $obFormulario->getInnerJavaScript();
    }
    $stJs .= "d.getElementById('spnAtributo').innerHTML = '$stHtml';   \n";
    $stJs .= "f.hdnSpans.value = f.hdnSpans.value + '$Js';                 \n";

    return $stJs;
}

function gerarSpan($boExecuta=false)
{
    switch ($_POST['stTipoFiltro']) {
        case 'contrato':
            $stJs.= gerarSpan2();
        break;
        case 'cgm_contrato':
            $stJs.= gerarSpan3();
        break;
        case 'atributo':
            $stJs.= gerarSpanAtributos();
        break;
        case 'geral':
            $stJs.= gerarSpan7();
        break;
    }
    $stJs.= gerarSpan4();

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function processarFiltro($boExecuta=false)
{
    $stJs.= "f.stTipoFiltro.options[1].selected = true;\n";
    $stJs.= gerarSpan1();
    $stJs.= gerarSpan2();
    $stJs.= gerarSpan4();
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function incluirContrato($boExecuta=false)
{
    $obErro = new erro;

    if (!$_POST['inContrato']) {
        $obErro->setDescricao( 'Escolha um contrato!');
    }

    if (!$obErro->ocorreu() ) {
        foreach ( Sessao::read('arContratos') as $arContrato ) {
            if ($arContrato['contrato'] == $_POST['inContrato']) {
                $obErro->setDescricao("O contrato ".$_POST['inContrato']." já foi incluído para o filtro.");
            }
        }
    }
    if ( !$obErro->ocorreu() ) {

        Sessao::write('stTipoFiltro',( ISSET($_POST['stTipoFiltro']) ) ?  $_POST['stTipoFiltro'] : Sessao::read('stTipoFiltro'));
        switch ( Sessao::read('stTipoFiltro') ) {
            case "contrato":
                $stCGM = stripslashes($_POST['hdnCGM']);
                $stJs.= limparContrato();
            break;
            case "cgm_contrato":
                $stCGM = stripslashes($_POST['inNumCGM']." - ".$_POST['inCampoInner']);
            break;
        }
        $arContratos = Sessao::read("arContratos");
        $arContrato['inId']     = count($arContratos);
        $arContrato['contrato'] = $_POST['inContrato'];
        $arContrato['cgm']      = $stCGM;
        $arContratos[] = $arContrato;
        Sessao::write('arContratos',$arContratos);
        $stJs.= gerarSpan5();
        $stJs.= "f.stTipoFiltro.disabled = true;    \n";
        if ($_POST['stTipoFiltro'] != "") {
            $stJs.= "f.hdnTipoFiltro.value = '".$_POST['stTipoFiltro']."';\n";
        }
    } else {
        $stJs.= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    //$stJs.= limparContrato();
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function excluirContrato($boExecuta=false)
{
    $arTemp = array();
    $arContratos = Sessao::read("arContratos");
    foreach ($arContratos as $arContrato) {
        if ($arContrato['inId'] != $_GET['inId']) {
            $arTemp[] = $arContrato;
        }
    }
    Sessao::write('arContratos',$arTemp);
    $stJs.= gerarSpan5();
    if ( count(Sessao::read('arContratos')) == 0 ) {
        $stJs.= "f.stTipoFiltro.disabled = false;    \n";
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function limparContrato($boExecuta=false)
{
    $stTipoFiltro = ( isset($_POST['stTipoFiltro']) ) ? $_POST['stTipoFiltro'] : Sessao::read('stTipoFiltro');
    switch ($stTipoFiltro) {
        case "contrato":
            $stJs.= "f.inContrato.value = '';                           \n";
            $stJs.= "d.getElementById('inNomCGM').innerHTML = '&nbsp;'; \n";
        break;
        case "cgm_contrato":
            $stJs.= "limpaSelect(f.inContrato,0);                       \n";
            $stJs.= "f.inContrato[0] = new Option('Selecione','','selected');\n";
            $stJs.= "f.inNumCGM.value = '';                             \n";
            $stJs.= "d.getElementById('inCampoInner').innerHTML = '&nbsp;'; \n";
        break;
    }

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function limparGeral($boExecuta=false)
{
    $stJs .= "f.stTipoFiltro.disabled = false;              \n";
    $stJs .= "d.getElementById('spSpan5').innerHTML = '';   \n";
    $stJs .= limparContrato();
    Sessao::write('arContratos',"");
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function submeter()
{
    $obErro = new Erro();

    if ( ($_POST["stTipoFiltro"] == "contrato" or $_POST["stTipoFiltro"] == "cgm_contrato") and count(Sessao::read("arContratos")) == 0 ) {
        $obErro->setDescricao("Deve haver pelo menos um contrato na lista.");
    }

    ///////// COMPETENCIA SELECIONADA
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $stCompetencia  = (  $_POST["inCodMes"] < 10 ) ? "0".$_POST["inCodMes"] : $_POST["inCodMes"];
    $stCompetencia .= $_POST["inAno"];
    $stFiltroCompetencia = " WHERE to_char(dt_final,'mmyyyy') = '".$stCompetencia."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltroCompetencia);

    if ($rsPeriodoMovimentacao->getNumLinhas() < 1) {
        $obErro->setDescricao("Não existe período de competência para o mês e ano selecionados.");
    }

    if ( $obErro->ocorreu() ) {
        $stJs = "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    } else {
        $stJs .= "parent.frames[2].Salvar();\n";
    }

    return $stJs;
}

function atualizarLotacao()
{
    include_once(CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php");
    include_once(CAM_GRH_PES_COMPONENTES."ISelectAnoCompetencia.class.php");
    include_once(CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");

    $stJs 					 = "";
    $arFiltroCompetencia 	 = Sessao::read("arFiltroCompetencia");
    $arFiltroAnoCompetencia  = Sessao::read("arFiltroAnoCompetencia");
    $arSelectMultiploLotacao = Sessao::read("arSelectMultiploLotacao");

    if (is_array($arFiltroCompetencia) && count($arFiltroCompetencia) > 0) {
        foreach ($arFiltroCompetencia as $obFiltroCompetencia) {
            if (trim($obFiltroCompetencia->getCodigoPeriodoMovimentacao()) != "") {
                $obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
                $obFPessoalOrganogramaVigentePorTimestamp->setDado("cod_periodo_movimentacao",$obFiltroCompetencia->getCodigoPeriodoMovimentacao());
                $obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);

                $inCodOrganograma = $rsOrganogramaVigente->getCampo("cod_organograma");
                $stDataFinal      = $rsOrganogramaVigente->getCampo("dt_final");

                if (is_array($arSelectMultiploLotacao) && count($arSelectMultiploLotacao) > 0) {
                    foreach ($arSelectMultiploLotacao as $obSelectMultiploLotacao) {
                        $stJs .= $obSelectMultiploLotacao->atualizarLotacao($stDataFinal, $inCodOrganograma);
                    }
                }
            }
        }
    }

    if (is_array($arFiltroAnoCompetencia) && count($arFiltroAnoCompetencia) > 0) {
        foreach ($arFiltroAnoCompetencia as $obFiltroAnoCompetencia) {
            if (trim($obFiltroAnoCompetencia->getCodigoPeriodoMovimentacao()) != "") {
                $obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
                $obFPessoalOrganogramaVigentePorTimestamp->setDado("cod_periodo_movimentacao",$obFiltroAnoCompetencia->getCodigoPeriodoMovimentacao());
                $obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);

                $inCodOrganograma = $rsOrganogramaVigente->getCampo("cod_organograma");
                $stDataFinal      = $rsOrganogramaVigente->getCampo("dt_final");

                if (is_array($arSelectMultiploLotacao) && count($arSelectMultiploLotacao) > 0) {
                    foreach ($arSelectMultiploLotacao as $obSelectMultiploLotacao) {
                        $stJs .= $obSelectMultiploLotacao->atualizarLotacao($stDataFinal, $inCodOrganograma);
                    }
                }
            }
        }
    }

    return $stJs;
}

switch ($_POST["stCtrl"]) {
    case "gerarSpan1":
        $stJs.= gerarSpan1();
    break;
    case "gerarSpan8":
        $stJs.= gerarSpan8();
    break;
    case "gerarSpan9":
        $stJs.= gerarSpan9();
    break;
    case "gerarSpan10":
        $stJs.= gerarSpan10();
    break;
    case "gerarSpan11":
        $stJs.= gerarSpan11();
    break;
    case "gerarSpan12":
        $stJs.= gerarSpan12();
    break;
    case "gerarSpan13":
        $stJs.= gerarSpan13();
    break;
    case "gerarSpan14":
        $stJs.= gerarSpan14();
    break;
    case "gerarSpan":
        $stJs.= gerarSpan();
    break;
    case "processarFiltro":
        $stJs.= processarFiltro();
    break;
    case "incluirContrato":
        $stJs.= incluirContrato();
    break;
    case "excluirContrato":
        $stJs.= excluirContrato();
    break;
    case "limparContrato":
        $stJs.= limparContrato();
    break;
    case "limparGeral":
        $stJs.= limparGeral();
    break;
    case "gerarSpanAtributosDinamicos":
        $stJs .= gerarSpanAtributosDinamicos();
    break;
    case "submeter":
        $stJs .= submeter();
    break;
}

if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
