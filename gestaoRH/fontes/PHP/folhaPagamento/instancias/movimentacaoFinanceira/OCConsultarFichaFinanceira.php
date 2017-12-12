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
    * Oculto
    * Data de Criação: 07/02/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.41

    $Id: OCConsultarFichaFinanceira.php 63871 2015-10-27 20:24:26Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO . "RFolhaPagamentoFolhaComplementar.class.php"                           );
include_once ( CAM_GRH_FOL_NEGOCIO . "RFolhaPagamentoPeriodoMovimentacao.class.php"                         );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                    );
include_once ( CAM_GRH_PES_COMPONENTES. "IFiltroContrato.class.php"                                         );
include_once ( CAM_GRH_PES_COMPONENTES. "IFiltroCGMContrato.class.php"                                      );
//include_once ( CAM_GRH_PES_COMPONENTES. "ISelectMultiploLotacao.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarFichaFinanceira";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function processarFiltro($boExecuta=false)
{
    $stJs .= gerarSpan1();
    $stJs .= gerarSpan4();
    $stJs .= mostrarLinkConsulta();
    $stJs .= gerarSpanDesdobramento();
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limpar($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
    $arData = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));
    $inCodMes = $arData[1];
    $inAno    = $arData[2];
    $stJs .= "f.stOpcao[0].checked = true;\n";
    $stJs .= "f.boFiltrarFolhaComplementar.checked = false;\n";
    $stJs .= "f.inAno.value = '".$inAno."';\n";
    $stJs .= "f.inCodMes.options[".$inCodMes."].selected = true;\n";
    $stJs .= limparSpan5();
    $stJs .= processarFiltro();
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function habilitaSpan($boExecuta=false)
{
    if ($_POST['stOpcao'] == 'contrato') {
        $stJs .= gerarSpan1();
    }
    if ($_POST['stOpcao'] == 'cgm_contrato') {
        $stJs .= gerarSpan2();
    }

    if ($_POST['stOpcao'] == 'evento') {
        $stJs .= gerarSpanEvento();
        $stJs .= "d.getElementById('spnSpanDadosInformativos').innerHTML = '';";
    } else {
        $stJs .= gerarSpanDadosInformativos();
        $stJs .= mostrarLinkConsulta();
    }

    $stJs .= "f.boFiltrarFolhaComplementar.checked = false;";
    $stJs .= gerarSpan4();
    $stJs .= limparSpan5();
    $stJs .= gerarSpanBotaoImprimir();
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function habilitaSpan2($boExecuta=false)
{
    $obErro = new erro;
    if (($_POST['inContrato'] == "" ) && (trim($_REQUEST["stOpcao"]) != "evento")) {
       $obErro->setDescricao("Campo Matrícula inválido.");
    }
    if ( !$obErro->ocorreu() ) {
        if (isset($_POST['boFiltrarFolhaComplementar']) and $_POST['boFiltrarFolhaComplementar'] == true) {
            $stJs .= "d.getElementById('spnDesdobramento').innerHTML = '';    \n";
            $stJs .= gerarSpan3(false,$boComplementares);
            $stJs .= _mostrarLinkConsultaComplementar($boComplementares);
        } else {
            $stJs .= gerarSpan4();
            $stJs .= _mostrarLinkConsultaComplementar(false);
        }
        $stJs .= limparSpan5();
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
        $stJs .= "f.boFiltrarFolhaComplementar.checked = false;";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function mostrarLinkConsulta()
{
    $inCodConfiguracao = getCodConfiguracao();

    switch ($inCodConfiguracao) {
        case 1:
            if (isset($_REQUEST["stOpcao"]) and (trim($_REQUEST["stOpcao"])!="evento")) {
                $stJs .= "d.getElementById('SpanlinkSalario').style.display = 'block'; \n";
                $stJs .= "d.getElementById('SpanlinkFerias').style.display = 'none'; \n";
                $stJs .= "d.getElementById('SpanlinkDecimo').style.display = 'none'; \n";
                $stJs .= "d.getElementById('Spanlink').style.display = 'none'; \n";
                $stJs .= "d.getElementById('SpanlinkRescisao').style.display = 'none'; \n";
            }
            $stJs .= gerarSpanDesdobramento();
        break;
        case 2:
            if (isset($_REQUEST["stOpcao"]) and (trim($_REQUEST["stOpcao"])!="evento")) {
                $stJs .= "d.getElementById('SpanlinkSalario').style.display = 'none'; \n";
                $stJs .= "d.getElementById('SpanlinkFerias').style.display = 'block'; \n";
                $stJs .= "d.getElementById('SpanlinkDecimo').style.display = 'none'; \n";
                $stJs .= "d.getElementById('Spanlink').style.display = 'none'; \n";
                $stJs .= "d.getElementById('SpanlinkRescisao').style.display = 'none'; \n";
            }
            $stJs .= gerarSpanDesdobramento();
        break;
        case 3:
            if (isset($_REQUEST["stOpcao"]) and (trim($_REQUEST["stOpcao"])!="evento")) {
                $stJs .= "d.getElementById('SpanlinkSalario').style.display = 'none'; \n";
                $stJs .= "d.getElementById('SpanlinkFerias').style.display = 'none'; \n";
                $stJs .= "d.getElementById('SpanlinkDecimo').style.display = 'block'; \n";
                $stJs .= "d.getElementById('Spanlink').style.display = 'none'; \n";
                $stJs .= "d.getElementById('SpanlinkRescisao').style.display = 'none'; \n";
            }
            $stJs .= gerarSpanDesdobramento();
        break;
        case 4:
            if (isset($_REQUEST["stOpcao"]) and (trim($_REQUEST["stOpcao"])!="evento")) {
                $stJs .= "d.getElementById('SpanlinkSalario').style.display = 'none'; \n";
                $stJs .= "d.getElementById('SpanlinkFerias').style.display = 'none'; \n";
                $stJs .= "d.getElementById('SpanlinkDecimo').style.display = 'none'; \n";
                $stJs .= "d.getElementById('Spanlink').style.display = 'none'; \n";
                $stJs .= "d.getElementById('SpanlinkRescisao').style.display = 'block'; \n";
            }
        break;
        default:
            if (isset($_REQUEST["stOpcao"]) and (trim($_REQUEST["stOpcao"])!="evento")) {
                $stJs .= "d.getElementById('SpanlinkSalario').style.display = 'none'; \n";
                $stJs .= "d.getElementById('SpanlinkFerias').style.display = 'none'; \n";
                $stJs .= "d.getElementById('SpanlinkDecimo').style.display = 'none'; \n";
                $stJs .= "d.getElementById('Spanlink').style.display = 'block'; \n";
                $stJs .= "d.getElementById('SpanlinkRescisao').style.display = 'none'; \n";
            }
            $stJs .= gerarSpanDesdobramento();
        break;
    }
    $stJs .= limparSpan5();

    return $stJs;
}

function mostrarLinkConsultaComplementar($boExecuta=false)
{
    $stJs .= "d.getElementById('SpanlinkSalario').style.display = 'block'; \n";
    if ( (isset($_REQUEST["stOpcao"])) && (trim($_REQUEST["stOpcao"] != "") != "evento") ) {
        $stJs .= "d.getElementById('SpanlinkFerias').style.display = 'none'; \n";
    }
    $stJs .= "d.getElementById('SpanlinkDecimo').style.display = 'none'; \n";
    $stJs .= "d.getElementById('SpanlinkRescisao').style.display = 'none'; \n";
    if ($_POST['inCodComplementar'] != "") {
        $stJs .= "d.getElementById('Spanlink').style.display = 'block'; \n";
    } else {
        $stJs .= "d.getElementById('Spanlink').style.display = 'none'; \n";
    }
    $stJs .= limparSpan5();
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function _mostrarLinkConsultaComplementar($boComplementares)
{
    $inCodConfiguracao = getCodConfiguracao();

    if ($boComplementares == true) {
        if ( (isset($_REQUEST["stOpcao"])) && (trim($_REQUEST["stOpcao"]) != "evento") ) {
            $stJs .= "d.getElementById('SpanlinkSalario').style.display = 'block'; \n";
            $stJs .= "d.getElementById('SpanlinkFerias').style.display = 'none'; \n";
            $stJs .= "d.getElementById('SpanlinkDecimo').style.display = 'none'; \n";
            $stJs .= "d.getElementById('Spanlink').style.display = 'block'; \n";
        }
    } else {
        switch ($inCodConfiguracao) {
            case 1:
                if ( (isset($_REQUEST["stOpcao"])) && (trim($_REQUEST["stOpcao"]) != "evento") ) {
                    $stJs .= "d.getElementById('SpanlinkSalario').style.display = 'block'; \n";
                    $stJs .= "d.getElementById('SpanlinkFerias').style.display = 'none'; \n";
                    $stJs .= "d.getElementById('SpanlinkDecimo').style.display = 'none'; \n";
                    $stJs .= "d.getElementById('SpanlinkRescisao').style.display = 'none'; \n";
                }
            break;
            case 2:
                if ( (isset($_REQUEST["stOpcao"])) && (trim($_REQUEST["stOpcao"]) != "evento") ) {
                    $stJs .= "d.getElementById('SpanlinkSalario').style.display = 'none'; \n";
                    $stJs .= "d.getElementById('SpanlinkFerias').style.display = 'block'; \n";
                    $stJs .= "d.getElementById('SpanlinkDecimo').style.display = 'none'; \n";
                    $stJs .= "d.getElementById('SpanlinkRescisao').style.display = 'none'; \n";
                }
            break;
            case 3:
                if ( (isset($_REQUEST["stOpcao"])) && (trim($_REQUEST["stOpcao"]) != "evento") ) {
                    $stJs .= "d.getElementById('SpanlinkSalario').style.display = 'none'; \n";
                    $stJs .= "d.getElementById('SpanlinkFerias').style.display = 'none'; \n";
                    $stJs .= "d.getElementById('SpanlinkDecimo').style.display = 'block'; \n";
                    $stJs .= "d.getElementById('SpanlinkRescisao').style.display = 'none'; \n";
                }
            break;
            case 4:
                if ( (isset($_REQUEST["stOpcao"])) && (trim($_REQUEST["stOpcao"]) != "evento") ) {
                    $stJs .= "d.getElementById('SpanlinkSalario').style.display = 'none'; \n";
                    $stJs .= "d.getElementById('SpanlinkFerias').style.display = 'none'; \n";
                    $stJs .= "d.getElementById('SpanlinkDecimo').style.display = 'none'; \n";
                    $stJs .= "d.getElementById('SpanlinkRescisao').style.display = 'block'; \n";
                }
            break;
            default:
                if ( (isset($_REQUEST["stOpcao"])) && (trim($_REQUEST["stOpcao"]) != "evento") ) {
                    $stJs .= "d.getElementById('SpanlinkSalario').style.display = 'none'; \n";
                    $stJs .= "d.getElementById('SpanlinkFerias').style.display = 'none'; \n";
                    $stJs .= "d.getElementById('SpanlinkDecimo').style.display = 'none'; \n";
                    $stJs .= "d.getElementById('SpanlinkRescisao').style.display = 'none'; \n";
                }
            break;
        }
        if ( (isset($_REQUEST["stOpcao"])) && (trim($_REQUEST["stOpcao"]) != "evento") ) {
            $stJs .= "d.getElementById('Spanlink').style.display = 'none'; \n";
        }
    }
    $stJs .= limparSpan5();

    return $stJs;
}

function gerarSpan1($boExecuta=false)
{
    $obFormulario      = new Formulario;
    $obIFiltroContrato = new IFiltroContrato("todos");
    $obIFiltroContrato->obIContratoDigitoVerificador->setLinkConsultaServidor(true);
    $obIFiltroContrato->geraFormulario  ( $obFormulario );

    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$obFormulario->getHTML()."';    \n";

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpanEvento()
{
    include_once(CAM_GRH_FOL_COMPONENTES.'IBscEvento.class.php');

    $obIBscEvento = new IBscEvento();
    $obIBscEvento->setTodosEventos(true);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Evento");
    $obIBscEvento->geraFormulario($obFormulario);
    $obFormulario->montaInnerHtml();

    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$obFormulario->getHTML()."';";

    return $stJs;
}

function gerarSpanDadosInformativos()
{

    $obLinDadosInformativos = new Link;
    $obLinDadosInformativos->setRotulo              ( "Dados Informativos"                                  );
    $obLinDadosInformativos->setHref                ( "javascript:abrePopUpRegistrosEventosComplementar();" );
    $obLinDadosInformativos->setValue               ( "<span id='Spanlink' style='display:none'>Consultar Registro de Evento Complementar</span>"           );

    $obLinDadosInformativos2 = new Link;
    $obLinDadosInformativos2->setRotulo             ( "Dados Informativos"                                  );
    $obLinDadosInformativos2->setHref               ( "javascript:abrePopUpRegistrosEventos();"             );
    $obLinDadosInformativos2->setValue              ( "<span id='SpanlinkSalario' style='display:none'>Consultar Registro de Evento</span>"                        );

    $obLinDadosInformativos3 = new Link;
    $obLinDadosInformativos3->setRotulo             ( "Dados Informativos"                                  );
    $obLinDadosInformativos3->setHref               ( "javascript:abrePopUpRegistrosEventosFerias();"       );
    $obLinDadosInformativos3->setValue              ( "<span id='SpanlinkFerias' style='display:none'>Consultar Registro de Evento Férias</span>"           );

    $obLinDadosInformativos4 = new Link;
    $obLinDadosInformativos4->setRotulo             ( "Dados Informativos"                                  );
    $obLinDadosInformativos4->setHref               ( "javascript:abrePopUpRegistrosEventosDecimo();"       );
    $obLinDadosInformativos4->setValue              ( "<span id='SpanlinkDecimo' style='display:none'>Consultar Registro de Evento 13º Salário</span>"           );

    $obLinDadosInformativos5 = new Link;
    $obLinDadosInformativos5->setRotulo             ( "Dados Informativos"                                  );
    $obLinDadosInformativos5->setHref               ( "javascript:abrePopUpRegistrosEventosRescisao();"       );
    $obLinDadosInformativos5->setValue              ( "<span id='SpanlinkRescisao' style='display:none'>Consultar Registro de Evento Rescisão</span>"           );

    $obLinDadosInformativos6 = new Link;
    $obLinDadosInformativos6->setRotulo             ( "Dados Informativos"                                  );
    $obLinDadosInformativos6->setHref               ( "javascript:abrePopUpAssentamentoGerado();"       );
    $obLinDadosInformativos6->setValue              ( "<br><span id='SpanlinkAssentamentoGerado' style='display:block'>Consultar Assentamento Gerado</span>"           );

    $obRdoOrdenacao1 = new Radio;
    $obRdoOrdenacao1->setName                       ( "stOrdenacao"                                         );
    $obRdoOrdenacao1->setTitle                      ( "Selecione a ordenação da consulta."                  );
    $obRdoOrdenacao1->setRotulo                     ( "Ordenação"                                           );
    $obRdoOrdenacao1->setLabel                      ( "Código do Evento"                                    );
    $obRdoOrdenacao1->setValue                      ( "codigo"                                              );
    $obRdoOrdenacao1->setChecked                    ( $stOrdenacao == 'codigo' || !$stOrdenacao             );

    $obRdoOrdenacao2 = new Radio;
    $obRdoOrdenacao2->setName                       ( "stOrdenacao"                                         );
    $obRdoOrdenacao2->setTitle                      ( "Selecione a ordenação da consulta."                  );
    $obRdoOrdenacao2->setRotulo                     ( "Ordenação"                                           );
    $obRdoOrdenacao2->setLabel                      ( "Sequência de Cálculo"                                );
    $obRdoOrdenacao2->setValue                      ( "sequencia"                                           );
    $obRdoOrdenacao2->setChecked                    ( $stOrdenacao == 'sequencia'                           );

    $obFormulario = new Formulario;
    $obFormulario->agrupaComponentes                ( array($obLinDadosInformativos2,$obLinDadosInformativos,$obLinDadosInformativos3,$obLinDadosInformativos4,$obLinDadosInformativos5,$obLinDadosInformativos6));
    $obFormulario->agrupaComponentes                ( array($obRdoOrdenacao1,$obRdoOrdenacao2)              );
    $obFormulario->montaInnerHtml();

    $stJs .= "d.getElementById('spnSpanDadosInformativos').innerHTML = '".$obFormulario->getHTML()."';";

    return $stJs;
}

function gerarSpanBotaoImprimir()
{
    if (trim($_REQUEST["stOpcao"])!="evento") {
        $obBtnImprimir = new Button;
        $obBtnImprimir->setName                         ( "btnImprimir"                                         );
        $obBtnImprimir->setValue                        ( "Imprimir"                                            );
        $obBtnImprimir->setTipo                         ( "button"                                              );
        $obBtnImprimir->obEvento->setOnClick            ( "buscaValor('imprimir');"                           );

        $obFormulario = new Formulario;
        $obFormulario->defineBarra                      ( array($obBtnImprimir)                                 );
        $obFormulario->montaInnerHtml();

        $stJs .= "d.getElementById('spnSpanBotaoImprimir').innerHTML = '".$obFormulario->getHTML()."';";
    } else {
        $stJs .= "d.getElementById('spnSpanBotaoImprimir').innerHTML = '';";
    }

    return $stJs;
}

function buscaEvento($boExecuta=false)
{
    $obErro = new erro;

    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao(Sessao::read('inCodSubDivisao'));
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->setCodCargo( Sessao::read('inCodFuncao') );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( Sessao::read('inCodEspecialidade') );
    $obRFolhaPagamentoPeriodoContratoServidor->addRFolhaPagamentoRegistroEvento();
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setCodigo( str_pad($_POST['inCodigoEvento'],5,"0",STR_PAD_LEFT) );
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setEventoSistema('false');
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->listarEvento($rsEventoEvento);
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->listarEventosConfiguracao( $rsEvento );

    if ( $rsEventoEvento->getNumLinhas() < 0 ) {
        $obErro->setDescricao("O evento informado não existe.");
    }
    if ( $rsEvento->getNumLinhas() < 0 and !$obErro->ocorreu() ) {
        $obErro->setDescricao("O evento informado não possui configuração para a subdivisão/cargo e/ou especialidade do contrato em manutenção.");

    }
    if ( !$obErro->ocorreu() ) {
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setCodEvento($rsEvento->getCampo('cod_evento'));
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setTimestamp($rsEvento->getCampo('timestamp'));
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->addConfiguracaoEvento();
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setCodCaso( $rsEvento->getCampo('cod_caso') );
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao( $rsEvento->getCampo('cod_configuracao') );
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->listarEventosBasePorCaso($rsEventosBase);

        Sessao::write('rsEventosBase',$rsEventosBase);
        Sessao::write('stProcesso',"inclusao");
        Sessao::write('stTipo',$rsEvento->getCampo('tipo'));
        Sessao::write('stFixado',$rsEvento->getCampo('fixado'));
        Sessao::write('nuValor',($rsEvento->getCampo('valor_quantidade') != '') ? number_format($rsEvento->getCampo('valor_quantidade'),2,',','.'): '');
        Sessao::write('stDescricaoEvento',$rsEvento->getCampo('descricao'));
        Sessao::write('stProventosDescontos',$rsEvento->getCampo('proventos_descontos'));
        Sessao::write('boLimiteCalculo',$rsEvento->getCampo('limite_calculo'));

        $obErro = new erro;
        //$stJs .= processarFormInclusao(false,&$obErro);

        if ( !$obErro->ocorreu() ) {
            $rsEvento->addFormatacao("observacao","N_TO_BR");
            $stJs .= "d.getElementById('inCampoInner').innerHTML = '".$rsEvento->getCampo('descricao')."';          \n";
            $stJs .= "f.inCampoInner.value = '".$rsEvento->getCampo('descricao')."';                                \n";
            $stJs .= "f.inCodigoEvento.value = '".$rsEvento->getCampo('codigo')."';                                \n";
        }
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');      \n";;
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpanDesdobramento($boExecuta=false)
{
    if ( getCodConfiguracao() == 3 ) {
        $obRdoAdiantamento = new Radio;
        $obRdoAdiantamento->setName                       ( "stDesdobramento"                                     );
        $obRdoAdiantamento->setTitle                      ( "Selecione o desdobramento."                          );
        $obRdoAdiantamento->setRotulo                     ( "Desdobramento"                                       );
        $obRdoAdiantamento->setLabel                      ( "Adiantamento"                                        );
        $obRdoAdiantamento->setValue                      ( "A"                                                   );
        $obRdoAdiantamento->setChecked                    ( $stDesdobramento == 'A' || !$stDesdobramento          );

        $obRdo13Salario = new Radio;
        $obRdo13Salario->setName                       ( "stDesdobramento"                                        );
        $obRdo13Salario->setTitle                      ( "Selecione o desdobramento."                             );
        $obRdo13Salario->setRotulo                     ( "Desdobramento"                                          );
        $obRdo13Salario->setLabel                      ( "13º Salário"                                            );
        $obRdo13Salario->setValue                      ( "D"                                                      );
        $obRdo13Salario->setChecked                    ( $stDesdobramento == 'D'                                  );

        $obRdoComplemento13Salario = new Radio;
        $obRdoComplemento13Salario->setName                       ( "stDesdobramento"                                        );
        $obRdoComplemento13Salario->setTitle                      ( "Selecione o desdobramento."                             );
        $obRdoComplemento13Salario->setRotulo                     ( "Desdobramento"                                          );
        $obRdoComplemento13Salario->setLabel                      ( "Complemento 13º Salário"                                            );
        $obRdoComplemento13Salario->setValue                      ( "C"                                                      );
        $obRdoComplemento13Salario->setChecked                    ( $stDesdobramento == 'C'                                  );

        $obFormulario = new Formulario;
        $obFormulario->agrupaComponentes(array($obRdoAdiantamento,$obRdo13Salario,$obRdoComplemento13Salario));
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    } else {
        $stHtml = "";
    }
    if ( (isset($_REQUEST["stOpcao"])) && (trim($_REQUEST["stOpcao"] != "") != "evento") ) {
        $stJs .= "d.getElementById('spnDesdobramento').innerHTML = '".$stHtml."';    \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan2($boExecuta=false)
{
    $obIFiltroCGMContrato = new IFiltroCGMContrato(true);
    $obIFiltroCGMContrato->obCmbContrato->setNull(false);

    $obFormulario = new Formulario;
    $obIFiltroCGMContrato->geraFormulario       ( $obFormulario                                             );
    $obFormulario->obJavaScript->montaJavaScript();
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$obFormulario->getHTML()."';    \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan3($boExecuta=false,&$boComplementares)
{
    $obRFolhaPagamentoFolhaComplementar = new RFolhaPagamentoFolhaComplementar(new RFolhaPagamentoPeriodoMovimentacao);
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor(new RFolhaPagamentoPeriodoMovimentacao);
    $stCodMes  = ( strlen($_POST['inCodMes']) == 1 ) ? "0".$_POST['inCodMes'] : $_POST['inCodMes'];
    $stDtFinal = $_POST['inAno']."-".$stCodMes;
    $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->setDtFinal($stDtFinal);
    $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->listarPeriodoMovimentacao($rsUltimaMovimentacao);
    if ( $rsUltimaMovimentacao->getNumLinhas() > 0) {
        if ( (isset($_REQUEST["stOpcao"])) && (trim($_REQUEST["stOpcao"]) == "evento") ) {
            $stFiltro = " AND complementar.cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao');
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementar.class.php");
            $obTFolhaPagamentoComplementar = new TFolhaPagamentoComplementar;
            $obTFolhaPagamentoComplementar->recuperaRelacionamento($rsFolhaComplementar, $stFiltro);
        } else {
            $obRFolhaPagamentoPeriodoContratoServidor->setRegistro($_POST['inContrato']);
            $obRFolhaPagamentoPeriodoContratoServidor->consultarContrato();
            $obRFolhaPagamentoFolhaComplementar->listarFolhaComplementarCalculadaPorContrato($rsFolhaComplementar,$rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'),$obRFolhaPagamentoPeriodoContratoServidor->getCodContrato());
            $rsFolhaComplementar->setUltimoElemento();
            $inCodComplementar = $rsFolhaComplementar->getCampo('cod_complementar');
            if ( $rsFolhaComplementar->getNumLinhas() > 0 ) {
                $boComplementares = true;
            }
        }
    } else {
        $rsFolhaComplementar = new Recordset;
    }
    $rsFolhaComplementar->ordena("cod_complementar","DESC");
    $obCmbFolhaComplementar = new Select;
    $obCmbFolhaComplementar->setRotulo                    ( "Folha Complementar"                                  );
    $obCmbFolhaComplementar->setTitle                     ( "Selecione a folha complementar."                     );
    $obCmbFolhaComplementar->setName                      ( "inCodComplementar"                                   );
    $obCmbFolhaComplementar->setValue                     ( $inCodComplementar                                    );
    $obCmbFolhaComplementar->setStyle                     ( "width: 200px"                                        );
    $obCmbFolhaComplementar->addOption                    ( "", "Selecione"                                       );
    $obCmbFolhaComplementar->setCampoID                   ( "[cod_complementar]"                                  );
    $obCmbFolhaComplementar->setCampoDesc                 ( "[cod_complementar]"                                  );
    $obCmbFolhaComplementar->preencheCombo                ( $rsFolhaComplementar                                  );
    $obCmbFolhaComplementar->obEvento->setOnChange        ( "buscaValor('mostrarLinkConsultaComplementar');"      );

    $obFormulario = new Formulario;
    $obFormulario->addComponente                          ( $obCmbFolhaComplementar                               );
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnSpan3').innerHTML = '".$obFormulario->getHTML()."';    \n";

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function getCodConfiguracao()
{
    if ( isset($_REQUEST['inCodConfiguracao']) ) {
        $inCodConfiguracao = $_REQUEST['inCodConfiguracao'];
    } else {
        switch ($_REQUEST['stTipoCalculo']) {
            case "rescisao":
                $inCodConfiguracao = '4';
                break;
            case "ferias":
                $inCodConfiguracao = '2';
                break;
            case "decimo":
                $inCodConfiguracao = '3';
                break;
            default:
                $inCodConfiguracao = '1';
                break;
        }
    }

    return $inCodConfiguracao;
}

function gerarSpan4($boExecuta=false)
{
    $inCodConfiguracao = getCodConfiguracao();

    $obTxtTipoCalculo= new TextBox;
    $obTxtTipoCalculo->setRotulo                    ( "Tipo de Cálculo"                                     );
    $obTxtTipoCalculo->setTitle                     ( "Selecione o tipo de cálculo."                        );
    $obTxtTipoCalculo->setName                      ( "inCodConfiguracao"                                   );
    $obTxtTipoCalculo->setValue                     ( $inCodConfiguracao                                    );
    $obTxtTipoCalculo->setSize                      ( 6                                                     );
    $obTxtTipoCalculo->setMaxLength                 ( 3                                                     );
    $obTxtTipoCalculo->setNull                      ( false                                                 );
    $obTxtTipoCalculo->setInteiro                   ( true                                                  );
    $obTxtTipoCalculo->obEvento->setOnChange        ( "buscaValor('mostrarLinkConsulta');"                  );

    $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
    $obRFolhaPagamentoEvento->addConfiguracaoEvento();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento($rsConfiguracaoEvento);
    $obCmbTipoCalculo = new Select;
    $obCmbTipoCalculo->setRotulo                    ( "Tipo de Cálculo"                                     );
    $obCmbTipoCalculo->setTitle                     ( "Selecione o tipo de cálculo."                        );
    $obCmbTipoCalculo->setName                      ( "stConfiguracao"                                      );
    $obCmbTipoCalculo->setValue                     ( $inCodConfiguracao                                    );
    $obCmbTipoCalculo->setStyle                     ( "width: 200px"                                        );
    $obCmbTipoCalculo->addOption                    ( "", "Selecione"                                       );
    $obCmbTipoCalculo->setCampoID                   ( "[cod_configuracao]"                                  );
    $obCmbTipoCalculo->setCampoDesc                 ( "[descricao]"                                         );
    $obCmbTipoCalculo->preencheCombo                ( $rsConfiguracaoEvento                                 );
    $obCmbTipoCalculo->setNull                      ( false                                                 );
    $obCmbTipoCalculo->obEvento->setOnChange        ( "buscaValor('mostrarLinkConsulta');"                  );

    $obFormulario = new Formulario;
    $obFormulario->addComponenteComposto            ( $obTxtTipoCalculo,$obCmbTipoCalculo                   );
    $obFormulario->montaInnerHtml();

    $stJs .= "d.getElementById('spnSpan3').innerHTML = '".$obFormulario->getHtml()."';    \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function imprimir($boExecuta=false)
{
    $obErro = new erro;
    if ($_POST['inContrato'] == "") {
        $obErro->setDescricao("Campo Matrícula inválido!()");
    }
    if ( !$obErro->ocorreu() ) {
        $stJs .= "BloqueiaFrames(true,false);";
        $stJs .= "parent.frames[2].Salvar();\n";
//         $stJs .= "f.target = 'oculto';";
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montaListaContratosxEvento()
{
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");

    //Busca a competencia
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes", $_REQUEST['inCodMes']);
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano", $_REQUEST['inAno']);
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

    //Busca o código correto do evento
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEventoCalculado();
    $stFiltro = "where codigo = '".$_REQUEST['inCodigoEvento']."'";
    $obTFolhaPagamentoEvento->recuperaCodigoEventoFichaFinanceira( $rsEvento, $stFiltro);

    /************* Montando busca da listagem de contratos **************/
    $stFiltro = "";
    if ( isset($_POST['inCodComplementar']) and $_POST['inCodComplementar'] ) {
        $stFiltro = " AND registro_evento_complementar.cod_evento = ".$rsEvento->getCampo("cod_evento");
        $stFiltro .= " AND registro_evento_complementar.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
        $stFiltro .= " AND registro_evento_complementar.cod_complementar = ".$_POST['inCodComplementar'];

        $stOrdem .= "  ORDER BY nom_cgm";
        $obTFolhaPagamentoEvento->recuperaContratosFichaFinanceiraComplementarComPensionista( $rsLista, $stFiltro, $stOrdem);

    } else {

        switch ($_POST['inCodConfiguracao']) {
            //Salario
            case 1:
                $stFiltro = " AND registro_evento.cod_evento = ".$rsEvento->getCampo("cod_evento");
                $stFiltro .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
                $stOrdem = "  ORDER BY nom_cgm";
                $obTFolhaPagamentoEvento->recuperaContratosFichaFinanceiraSalarioComPensionista( $rsLista, $stFiltro, $stOrdem);
            break;
            //Ferias
            case 2:
                $stFiltro .= " AND registro_evento_ferias.cod_evento = ".$rsEvento->getCampo("cod_evento");
                $stFiltro .= " AND registro_evento_ferias.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
                $stOrdem = "  ORDER BY nom_cgm";
                $obTFolhaPagamentoEvento->recuperaContratosFichaFinanceiraFeriasComPensionista( $rsLista, $stFiltro, $stOrdem);
            break;
            //Decimo
            case 3:
                $stFiltro .=  " AND registro_evento_decimo.cod_evento = ".$rsEvento->getCampo("cod_evento");
                $stFiltro .= " AND registro_evento_decimo.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
                $stFiltro .= " AND registro_evento_decimo.desdobramento = '".$_REQUEST["stDesdobramento"]."'";
                $stOrdem = "  ORDER BY nom_cgm";
                $obTFolhaPagamentoEvento->recuperaContratosFichaFinanceiraDecimoComPensionista( $rsLista, $stFiltro, $stOrdem);
            break;
            // Rescisão
            case 4:
                $stFiltro .=  " AND registro_evento_rescisao.cod_evento = ".$rsEvento->getCampo("cod_evento");
                $stFiltro .= " AND registro_evento_rescisao.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
                $stOrdem = "  ORDER BY nom_cgm";
                $obTFolhaPagamentoEvento->recuperaContratosFichaFinanceiraRescisaoComPensionista( $rsLista, $stFiltro, $stOrdem);
            break;
        }
    }

    /***********Monta a Lista de contratos****************/
    $obLista = new Table();
    $obLista->setRecordset($rsLista);
    $obLista->setSummary("Lista de Contratos do Evento");

    $obLista->Head->addCabecalho("Matrícula",5);
    $obLista->Head->addCabecalho("Nome",50);
    $obLista->Head->addCabecalho("Desdobramento",20);
    $obLista->Head->addCabecalho("Quantidade",10);
    $obLista->Head->addCabecalho("Valor",15);

    $obLista->Body->addCampo( 'matricula', 'D' );
    $obLista->Body->addCampo( 'nom_cgm', 'E' );
    $obLista->Body->addCampo( 'descricao', 'E' );
    $obLista->Body->addCampo( 'quantidade', 'D' );
    $obLista->Body->addCampo( 'valor', 'D' );

    $obLista->Foot->addSoma ( 'quantidade', 'D' );
    $obLista->Foot->addSoma ( 'valor', 'D' );

    $obLista->Body->addAcao("consultar","executaFuncaoAjax('%s','&inCodContrato=%s&inMatricula=%s&stNomeCGM=%s&inNumCGM=%s&inCodPeriodoMovimentacao=%s&inCodComplementar=".$_REQUEST["inCodComplementar"]."&inCodConfiguracao=".$_REQUEST["inCodConfiguracao"]."&inCodMes=".$_REQUEST["inCodMes"]."&inAno=".$_REQUEST["inAno"]."')",array('processarPopUp','cod_contrato','matricula','nom_cgm','numcgm','cod_periodo_movimentacao','inCodComplementar','inCodConfiguracao','inCodMes','inAno'));

    $obLista->montaHTML(true);
    $stJs = "d.getElementById('spnSpan5').innerHTML = '".$obLista->getHtml()."';  \n";

    return $stJs;
}

function processarPopUp()
{
    $stFiltros = "&inCodContrato=".$_GET["inCodContrato"]."&inRegistro=".$_GET["inMatricula"]."&inCodConfiguracao=".$_GET['inCodConfiguracao']."&nom_cgm=".$_GET["stNomeCGM"]."&numcgm=".$_GET["inNumCGM"]."&inCodPeriodoMovimentacao=".$_GET["inCodPeriodoMovimentacao"]."&inCodComplementar=".$_REQUEST["inCodComplementar"]."&inCodMes=".$_REQUEST["inCodMes"]."&inAno=".$_REQUEST["inAno"]."";
    $stUrlFrame = CAM_GRH_FOL_POPUPS."movimentacaoFinanceira/FRConsultarFichaFinanceira.php?sUrlConsulta=FMConsultarFichaFinanceira.php?".Sessao::getId().$stFiltros;
    $stJs .=  "window.open('".$stUrlFrame."', 'popUpConsultaFichaFinanceira', 'width=800,height=550,resizable=1,scrollbars=1,left=0,top=0');";
    return $stJs;
}

function gerarSpan5($boExecuta=false)
{
    $obErro = new erro;
    $temErro = false;

    if ( (trim($_REQUEST['inCodMes']) == "") or (trim($_REQUEST['inAno']) == "") ) {
        $obErro->setDescricao("Campo Competência inválido!()");
        $temErro = true;
    } else {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
        //Busca a competencia
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes", $_REQUEST['inCodMes']);
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano", $_REQUEST['inAno']);
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsUltimaMovimentacao);

        if (trim($rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'))=="") {
            $obErro->setDescricao("Campo Competência inválido, não foi aberto o período de movimentação!");
            $temErro = true;
        }
    }

    if ($temErro === false || $temErro == "") {
        if ($_REQUEST['stOpcao'] == "evento") {
            if ($_REQUEST['inCodigoEvento'] == "") {
                $obErro->setDescricao("Campo Evento inválido!()");
            } else {
                if ((isset($_POST['boFiltrarFolhaComplementar'])) && ($_POST['boFiltrarFolhaComplementar']==1) ) {
                    if (trim($_REQUEST["inCodComplementar"]) == "") {
                        $obErro->setDescricao("Campo Folha Complementar inválido!");
                    }
                } else {
                    if ($_POST['inCodConfiguracao'] == 3) {
                        if (trim($_REQUEST["stDesdobramento"]) == "") {
                            $obErro->setDescricao("Campo Desdobramento inválido!()");
                        }
                    } elseif (trim($_POST['inCodConfiguracao']) == "") {
                        $obErro->setDescricao("Campo Tipo de Cálculo inválido!()");
                    }
                }
            }
            if ( !$obErro->ocorreu() ) {
                $stJs = montaListaContratosxEvento();
            } else {
                $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
            }
        } else {
            if ($_POST['inContrato'] == "") {
                $obErro->setDescricao("Campo Matrícula inválido!()");
            }
            if ( !$obErro->ocorreu() and $_POST['inCodMes'] == "" or $_POST['inAno'] == "" ) {
                $obErro->setDescricao("Campo Competência inválido!()");
            }
            if ( !$obErro->ocorreu() and isset($_POST['inCodConfiguracao']) and $_POST['inCodConfiguracao'] == "" ) {
                $obErro->setDescricao("Campo Tipo de Cálculo inválido!()");
            }
            if ( !$obErro->ocorreu() and isset($_POST['inCodComplementar']) and $_POST['inCodComplementar'] == "" ) {
                $obErro->setDescricao("Campo Folha Complementar inválido!()");
            }
            if ( !$obErro->ocorreu() ) {
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
                $obTPessoalContrato = new TPessoalContrato();
                $stFiltro = " AND registro = ".$_POST['inContrato'];
                $obTPessoalContrato->recuperaCgmDoRegistro($rsCgm,$stFiltro);

                $inCodConfiguracao = $_POST["inCodConfiguracao"];
                $inCodComplementar = ($_POST["inCodComplementar"]) ? $_POST["inCodComplementar"] : 0;

                if ($_POST['boFiltrarFolhaComplementar'] != "") {
                    $inCodConfiguracao = "0";
                }

                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
                $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
                $obTFolhaPagamentoEventoCalculado->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                $obTFolhaPagamentoEventoCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
                $obTFolhaPagamentoEventoCalculado->setDado("cod_configuracao",$inCodConfiguracao);
                $obTFolhaPagamentoEventoCalculado->setDado("cod_complementar",$inCodComplementar);
                $obTFolhaPagamentoEventoCalculado->setDado("ordem",$_POST["stOrdenacao"]);
                $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculadosFichaFinanceira($rsEventoCalculado);

//                 $stOrdem = "desdobramento_texto,".$_POST["stOrdenacao"];

                switch ($inCodConfiguracao) {
                    case 0:
                        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
                        $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado();
//                         $stFiltro  = " AND registro_evento_complementar.cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
//                         $stFiltro .= " AND registro_evento_complementar.cod_contrato = ".$rsCgm->getCampo("cod_contrato");
//                         $stFiltro .= " AND registro_evento_complementar.cod_complementar = ".$inCodComplementar;
//                         $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosCalculadosFichaFinanceira($rsEventoCalculado,$stFiltro,$stOrdem);

                        $obTFolhaPagamentoEventoComplementarCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
                        $obTFolhaPagamentoEventoComplementarCalculado->setDado("numcgm",$rsCgm->getCampo("numcgm"));
                        $obTFolhaPagamentoEventoComplementarCalculado->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoEventoComplementarCalculado->setDado("natureza",'B');
                        $obTFolhaPagamentoEventoComplementarCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosBase,'',' order by codigo');
                        $obTFolhaPagamentoEventoComplementarCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosBase);

                        $obTFolhaPagamentoEventoComplementarCalculado->setDado("natureza",'D');
                        $obTFolhaPagamentoEventoComplementarCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosDesconto,'',' order by codigo');
                        $obTFolhaPagamentoEventoComplementarCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosDesconto);
                        break;
                    case 1:
                        $obTFolhaPagamentoEventoCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
                        $obTFolhaPagamentoEventoCalculado->setDado("numcgm",$rsCgm->getCampo("numcgm"));
                        $obTFolhaPagamentoEventoCalculado->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoEventoCalculado->setDado("natureza",'B');
                        $obTFolhaPagamentoEventoCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosBase,'',' order by codigo');
                        $obTFolhaPagamentoEventoCalculado->recuperaValoresAcumuladosCalculoSalarioFamilia($rsValoresAcumuladosBaseSalarioFamilia,'',' order by codigo');
                        $obTFolhaPagamentoEventoCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosBase);
                        $obTFolhaPagamentoEventoCalculado->recuperaRotuloValoresAcumuladosCalculoSalarioFamilia($rsRotuloValoresAcumuladosBaseSalarioFamilia);

                        $obTFolhaPagamentoEventoCalculado->setDado("natureza",'D');
                        $obTFolhaPagamentoEventoCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosDesconto,'',' order by codigo');
                        $obTFolhaPagamentoEventoCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosDesconto);
                        break;
                    case 2:
                        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php");
                        $obTFolhaPagamentoEventoFeriasCalculado = new TFolhaPagamentoEventoFeriasCalculado();
//                         $stFiltro  = " AND registro_evento_ferias.cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
//                         $stFiltro .= " AND registro_evento_ferias.cod_contrato = ".$rsCgm->getCampo("cod_contrato");
//                         $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculadosFichaFinanceira($rsEventoCalculado,$stFiltro,$stOrdem);

                        $obTFolhaPagamentoEventoFeriasCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
                        $obTFolhaPagamentoEventoFeriasCalculado->setDado("numcgm",$rsCgm->getCampo("numcgm"));
                        $obTFolhaPagamentoEventoFeriasCalculado->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoEventoFeriasCalculado->setDado("natureza",'B');
                        $obTFolhaPagamentoEventoFeriasCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosBase,'',' order by codigo');
                        $obTFolhaPagamentoEventoFeriasCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosBase);

                        $obTFolhaPagamentoEventoFeriasCalculado->setDado("natureza",'D');
                        $obTFolhaPagamentoEventoFeriasCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosDesconto,'',' order by codigo');
                        $obTFolhaPagamentoEventoFeriasCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosDesconto);
                        break;
                    case 3:
                        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php");
                        $obTFolhaPagamentoEventoDecimoCalculado = new TFolhaPagamentoEventoDecimoCalculado();
//                         $stFiltro  = " AND registro_evento_decimo.cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
//                         $stFiltro .= " AND registro_evento_decimo.cod_contrato = ".$rsCgm->getCampo("cod_contrato");
//                         $stFiltro .= " AND registro_evento_decimo.desdobramento = '".$_POST["stDesdobramento"]."'";
//                         $obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosCalculadosFichaFinanceira($rsEventoCalculado,$stFiltro,$stOrdem);

                        $obTFolhaPagamentoEventoDecimoCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
                        $obTFolhaPagamentoEventoDecimoCalculado->setDado("numcgm",$rsCgm->getCampo("numcgm"));
                        $obTFolhaPagamentoEventoDecimoCalculado->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoEventoDecimoCalculado->setDado("natureza",'B');
                        $obTFolhaPagamentoEventoDecimoCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosBase,'',' order by codigo');
                        $obTFolhaPagamentoEventoDecimoCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosBase);

                        $obTFolhaPagamentoEventoDecimoCalculado->setDado("natureza",'D');
                        $obTFolhaPagamentoEventoDecimoCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosDesconto,'',' order by codigo');
                        $obTFolhaPagamentoEventoDecimoCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosDesconto);
                        break;
                    case 4:
                        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php");
                        $obTFolhaPagamentoEventoRescisaoCalculado = new TFolhaPagamentoEventoRescisaoCalculado();
//                         $stFiltro  = " AND registro_evento_rescisao.cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
//                         $stFiltro .= " AND registro_evento_rescisao.cod_contrato = ".$rsCgm->getCampo("cod_contrato");
//                         $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventosCalculadosFichaFinanceira($rsEventoCalculado,$stFiltro,$stOrdem);

                        $obTFolhaPagamentoEventoRescisaoCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
                        $obTFolhaPagamentoEventoRescisaoCalculado->setDado("numcgm",$rsCgm->getCampo("numcgm"));
                        $obTFolhaPagamentoEventoRescisaoCalculado->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoEventoRescisaoCalculado->setDado("natureza",'B');
                        $obTFolhaPagamentoEventoRescisaoCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosBase,'',' order by codigo');
                        $obTFolhaPagamentoEventoRescisaoCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosBase);

                        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
                        $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
                        $obTFolhaPagamentoEventoCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
                        $obTFolhaPagamentoEventoCalculado->setDado("numcgm",$rsCgm->getCampo("numcgm"));
                        $obTFolhaPagamentoEventoCalculado->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoEventoCalculado->setDado("natureza",'B');
                        $obTFolhaPagamentoEventoCalculado->recuperaValoresAcumuladosCalculoSalarioFamilia($rsValoresAcumuladosBaseSalarioFamilia,'',' order by codigo');
                        $obTFolhaPagamentoEventoCalculado->recuperaRotuloValoresAcumuladosCalculoSalarioFamilia($rsRotuloValoresAcumuladosBaseSalarioFamilia);

                        $obTFolhaPagamentoEventoRescisaoCalculado->setDado("natureza",'D');
                        $obTFolhaPagamentoEventoRescisaoCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosDesconto,'',' order by codigo');
                        $obTFolhaPagamentoEventoRescisaoCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosDesconto);
                        break;
                }

                $rsEventos = processarEventos($rsEventoCalculado,1);
                $inCount1  = $rsEventos->getNumLinhas();
                $stTabela1 .= "<center>";
                //$stTabela1 .= "<a name=\'eventos\'></a>";
                $stTabela1 .= "<table border=0 width=100%>";
                $stTabela1 .= "<tr><td align=right width=10% class=labelcentercabecalho ><font size=-1>Evento</font></td>";
                $stTabela1 .= "<td class=labelcentercabecalho><font size=-1>Descrição</font></td>";
                $stTabela1 .= "<td class=labelcentercabecalho width=10%><font size=-1>Desdobramento</font></td>";
                $stTabela1 .= "<td class=labelcentercabecalho class=labelcentercabecalho align=right width=10%><font size=-1>Quantidade</font></td>";
                $stTabela1 .= "<td class=labelcentercabecalho align=right width=10%><font size=-1>Proventos</font></td>";
                $stTabela1 .= "<td class=labelcentercabecalho align=right width=10%><font size=-1>Descontos</font></td></tr>";
                while ( !$rsEventos->eof() ) {
                    $stTabela1 .= "<tr><td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('codigo')."</td>";
                    $stTabela1 .= "<td  class= fieldfinanceiro ><font size=-1>".$rsEventos->getCampo('descricao')."</font></td>";
                    $stTabela1 .= "<td  class= fieldfinanceiro><font size=-1>".$rsEventos->getCampo('desdobramento_texto')."</font></td>";
                    if ($rsEventos->getCampo('apresenta_parcela') == 't') {
                        $stQuantidadeParc = '/'.$rsEventos->getCampo('quantidade_total_parcela');
                        $stTabela1 .= "<td  class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('quantidade_parcelas').$stQuantidadeParc."</font></td>";
                        $stTabela1 .= "<td  class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('proventos')."</font></td>";
                        $stTabela1 .= "<td  class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('descontos')."</font></td></tr>";

                    } else {
                        $stTabela1 .= "<td  class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('quantidade')."</font></td>";
                        $stTabela1 .= "<td  class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('proventos')."</font></td>";
                        $stTabela1 .= "<td  class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('descontos')."</font></td></tr>";
                    }

                    $rsEventos->proximo();
                }
                $stTabela1 .= "</table>";
                $stTabela1 .= "</center>";

                $rsEventos = processarEventos($rsEventoCalculado,2);
                $inCount2  = $rsEventos->getNumLinhas();
                $stTabela2  = "<center>";
                $stTabela2 .= "<table border=0 width=100%>";
                $stTabela2 .= "<tr><td class=labelcentercabecalho align=right width=10%><font size=-1>Evento</font></td>";
                $stTabela2 .= "<td class=labelcentercabecalho><font size=-1>Descrição</font></td>";
                $stTabela2 .= "<td class=labelcentercabecalho width=10%><font size=-1>Desdobramento</font></td>";
                $stTabela2 .= "<td class=labelcentercabecalho align=right width=10%><font size=-1>Quantidade</font></td>";
                $stTabela2 .= "<td class=labelcentercabecalho align=right width=20%><font size=-1>Valor</font></td></tr>";
                while ( !$rsEventos->eof() ) {
                    $stTabela2 .= "<tr><td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('codigo')."</font></td>";
                    $stTabela2 .= "<td class= fieldfinanceiro ><font size=-1>".$rsEventos->getCampo('descricao')."</font></td>";
                    $stTabela2 .= "<td class= fieldfinanceiro ><font size=-1>".$rsEventos->getCampo('desdobramento_texto')."</font></td>";
                     $stTabela2 .= "<td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('quantidade')."</font></td>";
                    $stTabela2 .= "<td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('descontos')."</font></td></tr>";
                    $rsEventos->proximo();
                }
                $stTabela2 .= "</table>";
                $stTabela2 .= "</center>";

                $rsEventos = processarEventos($rsEventoCalculado,3);
                $inCount3  = $rsEventos->getNumLinhas();
                $stTabela3  = "<center>";
                $stTabela3 .= "<table border=0 width=100%>";
                $stTabela3 .= "<tr><td class=labelcentercabecalho align=right width=10%><font size=-1>Evento</font></td>";
                $stTabela3 .= "<td class=labelcentercabecalho><font size=-1>Descrição</font></td>";
                $stTabela3 .= "<td class=labelcentercabecalho width=10%><font size=-1>Desdobramento</font></td>";
                 $stTabela3 .= "<td class=labelcentercabecalho align=right width=10%><font size=-1>Quantidade</font></td>";
                $stTabela3 .= "<td class=labelcentercabecalho align=right width=20%><font size=-1>Valor</font></td></tr>";
                while ( !$rsEventos->eof() ) {
                    $stTabela3 .= "<tr><td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('codigo')."</font></td>";
                    $stTabela3 .= "<td class= fieldfinanceiro><font size=-1>".$rsEventos->getCampo('descricao')."</font></td>";
                    $stTabela3 .= "<td class= fieldfinanceiro ><font size=-1>".$rsEventos->getCampo('desdobramento_texto')."</font></td>";
                     $stTabela3 .= "<td class= fieldfinanceiro  align=right><font size=-1>".$rsEventos->getCampo('quantidade')."</font></td>";
                    $stTabela3 .= "<td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('descontos')."</font></td></tr>";
                    $rsEventos->proximo();
                }
                $stTabela3 .= "</table>";
                $stTabela3 .= "</center>";

                $rsEventos = processarEventos($rsEventoCalculado,4);
                $stTabela4  = "<center>";
                $stTabela4 .= "<table border=0 width=100%>";

                while ( !$rsEventos->eof() ) {
                    $stTabela4 .= "<tr><td class= fieldfinanceiro><font size=-1>".$rsEventos->getCampo('descricao')."</font></td><td class= fieldfinanceiro align=right width=10%><font size=-1>".$rsEventos->getCampo('proventos')."</font></td><td class= fieldfinanceiro align=right width=10%><font size=-1>".$rsEventos->getCampo('descontos')."</font></td></tr>";
                    $rsEventos->proximo();
                }
                $stTabela4 .= "</table>";
                $stTabela4 .= "</center>";

                $stTabela5  = "<center>";
                $stTabela5 .= "<table border=0 width=100%>";
                $stTabela5 .= "<tr><td align=right width=10% class=labelcentercabecalho><font size=-1>Evento</font></td><td class=labelcentercabecalho><font size=-1>Descrição</font></td><td align=right width=10% class=labelcentercabecalho><font size=-1>Valor</font></td></tr>";
                if ($inCount1 == -1 and $inCount2 == -1 and $inCount3 == -1) {
                    $rsValoresAcumuladosBase = new RecordSet;
                    $rsRotuloValoresAcumuladosBase = new RecordSet;
                }
                while (!$rsValoresAcumuladosBase->eof()) {
                    $stTabela5 .= "<tr><td class= fieldfinanceiro align=right><font size=-1>".$rsValoresAcumuladosBase->getCampo('codigo')."</font></td><td class= fieldfinanceiro><font size=-1>".$rsValoresAcumuladosBase->getCampo('descricao')."</font></td><td class= fieldfinanceiro align=right><font size=-1>".number_format($rsValoresAcumuladosBase->getCampo('valor'),2,',','.')."</font></td></tr>";
                    $rsValoresAcumuladosBase->proximo();
                }
                $stTabela5 .= "</table>";
                $stTabela5 .= "</center>";

                if ($inCodConfiguracao == 1 or $inCodConfiguracao == 4) {
                    $stTabelaSalarioFamilia  = "<center>";
                    $stTabelaSalarioFamilia .= "<table border=0 width=100%>";
                    $stTabelaSalarioFamilia .= "<tr><td class=labelcentercabecalho align=right width=10%><font size=-1>Evento</font></td><td class=labelcentercabecalho width=50%><font size=-1>Descrição</font></td><td align=right width=40% class=labelcentercabecalho><font size=-1>Valor</font></td></tr>";

                    while (!$rsValoresAcumuladosBaseSalarioFamilia->eof()) {
                        $stTabelaSalarioFamilia .= "<tr><td align=right class= fieldfinanceiro><font size=-1>".$rsValoresAcumuladosBaseSalarioFamilia->getCampo('codigo')."</font></td><td class= fieldfinanceiro><font size=-1>".$rsValoresAcumuladosBaseSalarioFamilia->getCampo('descricao')."</font></td><td align=right class= fieldfinanceiro><font size=-1 >".number_format($rsValoresAcumuladosBaseSalarioFamilia->getCampo('valor'),2,',','.')."</font></td></tr>";
                        $rsValoresAcumuladosBaseSalarioFamilia->proximo();
                    }
                    $stTabelaSalarioFamilia .= "</table>";
                    $stTabelaSalarioFamilia .= "</center>";
                }

                $stTabela6  = "<center>";
                $stTabela6 .= "<table border=0 width=100%>";
                $stTabela6 .= "<tr><td class=labelcentercabecalho align=right width=10%><font size=-1>Evento</font></td><td class=labelcentercabecalho><font size=-1>Descrição</font></td><td class=labelcentercabecalho align=right width=10%><font size=-1>Valor</font></td></tr>";
                if ($inCount1 == -1 and $inCount2 == -1 and $inCount3 == -1) {
                    $rsValoresAcumuladosDesconto = new RecordSet;
                    $rsRotuloValoresAcumuladosDesconto = new RecordSet;
                }
                while (!$rsValoresAcumuladosDesconto->eof()) {
                    $stTabela6 .= "<tr><td align=right class= fieldfinanceiro><font size=-1>".$rsValoresAcumuladosDesconto->getCampo('codigo')."</font></td><td class= fieldfinanceiro><font size=-1>".$rsValoresAcumuladosDesconto->getCampo('descricao')."</font></td><td align=right class= fieldfinanceiro><font size=-1>".number_format($rsValoresAcumuladosDesconto->getCampo('valor'),2,',','.')."</font></td></tr>";
                    $rsValoresAcumuladosDesconto->proximo();
                }
                $stTabela6 .= "</table>";
                $stTabela6 .= "</center>";

                $stTabela7  = "<center>";
                $stTabela7 .= "<table border=0 width=100%>";
                $stTabela7 .= "<tr><td class= fieldfinanceiro align=left width=100%><font size=-1>(S)Folha Salário</font></td></tr>";
                $stTabela7 .= "<tr><td class= fieldfinanceiro align=left width=100%><font size=-1>(C)Folha Complementar</font></td></tr>";
                $stTabela7 .= "<tr><td class= fieldfinanceiro align=left width=100%><font size=-1>(F)Folha Férias</font></td></tr>";
                $stTabela7 .= "</table>";
                //$stTabela7 .= "<a href=\'#eventos\'><font size=-1>Topo</font></a>";
                $stTabela7 .= "</center>";

                $obSpnSpan5_1 = new Span;
                $obSpnSpan5_1->setId                            ( "spnSpan5_1"                                  );

                $obSpnSpan5_2 = new Span;
                $obSpnSpan5_2->setId                            ( "spnSpan5_2"                                  );

                $obSpnSpan5_3 = new Span;
                $obSpnSpan5_3->setId                            ( "spnSpan5_3"                                  );

                $obSpnSpan5_4 = new Span;
                $obSpnSpan5_4->setId                            ( "spnSpan5_4"                                  );

                $obSpnSpan5_5 = new Span;
                $obSpnSpan5_5->setId                            ( "spnSpan5_5"                                  );

                $obSpnSpan5_6 = new Span;
                $obSpnSpan5_6->setId                            ( "spnSpan5_6"                                  );

                $obSpnSpan5_7 = new Span;
                $obSpnSpan5_7->setId                            ( "spnSpan5_7"                                  );

                $obSpnSalarioFamilia1 = new Span();
                $obSpnSalarioFamilia1->setId("spnSalarioFamilia");
                $obSpnSalarioFamilia1->setValue($stTabelaSalarioFamilia);

                $obFormulario = new Formulario;
                $obFormulario->addTitulo                        ( "<a name='eventos'></a>Ficha Financeira"      );
                $obFormulario->addTitulo                        ( "Eventos Calculados"                          );
                $obFormulario->addSpan                          ( $obSpnSpan5_1                                 );
                $obFormulario->addTitulo                        ( "Bases de Cálculo"                            );
                $obFormulario->addSpan                          ( $obSpnSpan5_2                                 );
                $obFormulario->addTitulo                        ( "Eventos Informativos"                        );
                $obFormulario->addSpan                          ( $obSpnSpan5_3                                 );
                $obFormulario->addTitulo                        ( "Totais Calculados"                           );
                $obFormulario->addSpan                          ( $obSpnSpan5_4                                 );
                if ($_POST['inCodConfiguracao'] != 3) {
                    $obFormulario->addTitulo                        ( "Valores Acumulados com o Cálculo da Matrícula","center" );
                    $obFormulario->addTitulo                        ( "Matrícula(s): ".$rsRotuloValoresAcumuladosBase->getCampo("rotulo")              ,"center" );
                    $obFormulario->addSpan                          ( $obSpnSpan5_5                                 );
                    if ($_POST['inCodConfiguracao'] == 1 or $_POST['inCodConfiguracao'] == 4) {
                        $obFormulario->addTitulo                        ( "Valores Acumulados para Cálculo do Salário Família","center" );
                        $obFormulario->addTitulo                        ( "Matrícula(s): ".$rsRotuloValoresAcumuladosBaseSalarioFamilia->getCampo("rotulo")              ,"center" );
                        $obFormulario->addSpan                          ( $obSpnSalarioFamilia1                                 );
                    }
                    $obFormulario->addTitulo                        ( "Valores Acumulados até o Cálculo da Matrícula","center" );
                    $obFormulario->addTitulo                        ( "Matrícula(s): ".$rsRotuloValoresAcumuladosDesconto->getCampo("rotulo")          ,"center" );
                    $obFormulario->addSpan                          ( $obSpnSpan5_6                                 );
                    $obFormulario->addSpan                          ( $obSpnSpan5_7                                 );
                }
                $obFormulario->montaInnerHtml();
                $stJs .= "d.getElementById('spnSpan5').innerHTML = '".$obFormulario->getHTML()."';  \n";
                $stJs .= "d.getElementById('spnSpan5_1').innerHTML = '".$stTabela1."';  \n";
                $stJs .= "d.getElementById('spnSpan5_2').innerHTML = '".$stTabela2."';  \n";
                $stJs .= "d.getElementById('spnSpan5_3').innerHTML = '".$stTabela3."';  \n";
                $stJs .= "d.getElementById('spnSpan5_4').innerHTML = '".$stTabela4."';  \n";
                if ($_POST['inCodConfiguracao'] != 3) {
                    $stJs .= "d.getElementById('spnSpan5_5').innerHTML = '".$stTabela5."';  \n";
                    $stJs .= "d.getElementById('spnSpan5_6').innerHTML = '".$stTabela6."';  \n";
                    $stJs .= "d.getElementById('spnSpan5_7').innerHTML = '".$stTabela7."';  \n";
                }
            } else {
                $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
            }
        }
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function limparSpan5()
{
    $stJs .= "d.getElementById('spnSpan5').innerHTML = '';\n";

    return $stJs;
}

function processarEventosDesconto($rsEventos,$inCodConfiguracao,&$stRegistrosDesconto)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementar.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php");
    $obTFolhaPagamentoComplementar          = new TFolhaPagamentoComplementar;
    $obTFolhaPagamentoPeriodoMovimentacao   = new TFolhaPagamentoPeriodoMovimentacao;
    $obTFolhaPagamentoFolhaSituacao         = new TFolhaPagamentoFolhaSituacao;
    $inCodMes = (strlen($_POST['inCodMes']) == 1)? '0'.$_POST['inCodMes'] : $_POST['inCodMes'];
    $stFiltro = " AND to_char(dt_final, 'yyyy-mm') = '".$_POST['inAno']."-".$inCodMes."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltro);
    $arEventos      = array();
    $arSalario      = array();
    $arFerias       = array();
    $arComplementar = array();
    $obTFolhaPagamentoFolhaSituacao->recuperaUltimaFolhaSituacao($rsSituacaoSalario);
    while (!$rsEventos->eof()) {
        $boIncluir = false;
        switch ($inCodConfiguracao) {
            case 1:
                if ( $rsEventos->getCampo("cod_complementar") == 0 ) {
                    if ( $_POST['inContrato'] != $rsEventos->getCampo("registro") ) {
                        $arSalario[] = $rsEventos->getCampo("registro")."(S)";
                        $boIncluir    = true;
                    }
                } elseif ( $rsEventos->getCampo("cod_complementar") == -1 ) {
                    $arFerias[] = $rsEventos->getCampo("registro")."(F)";
                    $boIncluir = true;
                } else {
                    $stFiltro  = " AND complementar_situacao.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND complementar_situacao.cod_complementar = ".$rsEventos->getCampo("cod_complementar");
                    $obTFolhaPagamentoComplementar->recuperaRelacionamento($rsSituacaoComplementar,$stFiltro);
                    if ( $rsSituacaoComplementar->getCampo("situacao") == 'f' ) {
                        $arComplementar[] = $rsEventos->getCampo("registro")."(C".$rsEventos->getCampo("cod_complementar").")";
                        $boIncluir = true;
                    }
                }
                break;
            case 2:
                if ( $rsEventos->getCampo("cod_complementar") == 0 ) {
                    $arSalario[] = $rsEventos->getCampo("registro")."(S)";
                    $boIncluir    = true;
                } elseif ( $rsEventos->getCampo("cod_complementar") == -1 ) {
                    if ( $_POST['inContrato'] != $rsEventos->getCampo("registro") ) {
                        $arFerias[] = $rsEventos->getCampo("registro")."(F)";
                        $boIncluir = true;
                    }
                } else {
                    $stFiltro  = " AND complementar_situacao.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND complementar_situacao.cod_complementar = ".$rsEventos->getCampo("cod_complementar");
                    $obTFolhaPagamentoComplementar->recuperaRelacionamento($rsSituacaoComplementar,$stFiltro);
                    if ( $rsSituacaoComplementar->getCampo("situacao") == 'f' ) {
                        $arComplementar[] = $rsEventos->getCampo("registro")."(C".$rsEventos->getCampo("cod_complementar").")";
                        $boIncluir = true;
                    }
                }
            break;
            default:
                if ( $rsEventos->getCampo("cod_complementar") == 0 ) {
                    if ( $rsSituacaoSalario->getCampo("situacao") == 'f' or $rsSituacaoSalario->getCampo("cod_periodo_movimentacao") != $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao") ) {
                        $arSalario[] = $rsEventos->getCampo("registro")."(S)";
                        $boIncluir = true;
                    }
                } elseif ( $rsEventos->getCampo("cod_complementar") == -1 ) {
                    $arFerias[] = $rsEventos->getCampo("registro")."(F)";
                    $boIncluir = true;
                } else {
                    if ( !($_POST['inContrato'] == $rsEventos->getCampo("registro") and $_POST['inCodComplementar'] == $rsEventos->getCampo("cod_complementar")) ) {
                        $arComplementar[] = $rsEventos->getCampo("registro")."(C".$rsEventos->getCampo("cod_complementar").")";
                        $boIncluir    = true;
                    }
                }
            break;
        }
        if ($boIncluir) {
            $arEventos[$rsEventos->getCampo('codigo')]['valor']     += $rsEventos->getCampo('valor');
            $arEventos[$rsEventos->getCampo('codigo')]['codigo']     = $rsEventos->getCampo('codigo');
            $arEventos[$rsEventos->getCampo('codigo')]['descricao']  = $rsEventos->getCampo('descricao');
        }
        $rsEventos->proximo();
    }
    $arSalario      = array_unique($arSalario);
    $arFerias       = array_unique($arFerias);
    $arComplementar = array_unique($arComplementar);
    foreach ($arSalario as $stTexto) {
        $stRegistrosDesconto .= $stTexto."/";
    }
    foreach ($arFerias as $stTexto) {
        $stRegistrosDesconto .= $stTexto."/";
    }
    foreach ($arComplementar as $stTexto) {
        $stRegistrosDesconto .= $stTexto."/";
    }
    $stRegistrosDesconto = substr($stRegistrosDesconto,0,strlen($stRegistrosDesconto)-1);
    $arRetorno = array();
    foreach ($arEventos as $inCodigoEvento=>$arEvento) {
        $arRetorno[] = $arEvento;
    }
    $rsRetorno = new Recordset;
    $rsRetorno->preenche($arRetorno);

    return $rsRetorno;
}

function processarEventosBase($rsEventos,$inCodConfiguracao,&$stRegistrosBase)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementar.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php");
    $obTFolhaPagamentoComplementar          = new TFolhaPagamentoComplementar;
    $obTFolhaPagamentoPeriodoMovimentacao   = new TFolhaPagamentoPeriodoMovimentacao;
    $obTFolhaPagamentoFolhaSituacao         = new TFolhaPagamentoFolhaSituacao;
    $inCodMes = (strlen($_POST['inCodMes']) == 1)? '0'.$_POST['inCodMes'] : $_POST['inCodMes'];
    $stFiltro = " AND to_char(dt_final, 'yyyy-mm') = '".$_POST['inAno']."-".$inCodMes."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltro);
    $arEventos      = array();
    $arSalario      = array();
    $arFerias       = array();
    $arComplementar = array();
    $obTFolhaPagamentoFolhaSituacao->recuperaUltimaFolhaSituacao($rsSituacaoSalario);
    while (!$rsEventos->eof()) {
        $boIncluir = false;
        switch ($inCodConfiguracao) {
            case 1:
            case 2:
                if ( $rsEventos->getCampo("cod_complementar") == 0 ) {
                    $arSalario[] = $rsEventos->getCampo("registro")."(S)";
                    $boIncluir    = true;
                } elseif ( $rsEventos->getCampo("cod_complementar") == -1 ) {
                    $arFerias[] = $rsEventos->getCampo("registro")."(F)";
                    $boIncluir = true;
                } else {
                    $stFiltro  = " AND complementar_situacao.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND complementar_situacao.cod_complementar = ".$rsEventos->getCampo("cod_complementar");
                    $obTFolhaPagamentoComplementar->recuperaRelacionamento($rsSituacaoComplementar,$stFiltro);
                    if ( $rsSituacaoComplementar->getCampo("situacao") == 'f' ) {
                        $arComplementar[] = $rsEventos->getCampo("registro")."(C".$rsEventos->getCampo("cod_complementar").")";
                        $boIncluir = true;
                    }
                }
            break;
            default:
                if ( $rsEventos->getCampo("cod_complementar") == 0 ) {
                    if ( $rsSituacaoSalario->getCampo("situacao") == 'f' or $rsSituacaoSalario->getCampo("cod_periodo_movimentacao") != $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao") ) {
                        $arSalario[] = $rsEventos->getCampo("registro")."(S)";
                        $boIncluir = true;
                    }
                } elseif ( $rsEventos->getCampo("cod_complementar") == -1 ) {
                    $arFerias[] = $rsEventos->getCampo("registro")."(F)";
                    $boIncluir = true;
                } else {
                    $arComplementar[] = $rsEventos->getCampo("registro")."(C".$rsEventos->getCampo("cod_complementar").")";
                    $boIncluir    = true;
                }
            break;
        }
        if ($boIncluir) {
            $arEventos[$rsEventos->getCampo('codigo')]['valor']     += $rsEventos->getCampo('valor');
            $arEventos[$rsEventos->getCampo('codigo')]['codigo']     = $rsEventos->getCampo('codigo');
            $arEventos[$rsEventos->getCampo('codigo')]['descricao']  = $rsEventos->getCampo('descricao');
        }
        $rsEventos->proximo();
    }
    $arSalario      = array_unique($arSalario);
    $arFerias       = array_unique($arFerias);
    $arComplementar = array_unique($arComplementar);
    foreach ($arSalario as $stTexto) {
        $stRegistrosBase .= $stTexto."/";
    }
    foreach ($arFerias as $stTexto) {
        $stRegistrosBase .= $stTexto."/";
    }
    foreach ($arComplementar as $stTexto) {
        $stRegistrosBase .= $stTexto."/";
    }
    $stRegistrosBase = substr($stRegistrosBase,0,strlen($stRegistrosBase)-1);
    $arRetorno = array();
    foreach ($arEventos as $inCodigoEvento=>$arEvento) {
        $arRetorno[] = $arEvento;
    }
    $rsRetorno = new Recordset;
    $rsRetorno->preenche($arRetorno);

    return $rsRetorno;
}

function processarEventos($rsEventos,$inNatureza)
{
    $arEventos = ( $rsEventos->getNumLinhas() > 0 ) ? $rsEventos->getElementos() : array();
    $arTemp = array();
    switch ($inNatureza) {
        case 1:
            $stNatureza1 = 'P';
            $stNatureza2 = 'D';
        break;
        case 2:
            $stNatureza1 = 'B';
            $stNatureza2 = 'B';
        break;
        case 3:
            $stNatureza1 = 'I';
            $stNatureza2 = 'I';
        break;
        case 4:
            $boTodos = true;
            $nuTotalProventos = 0;
            $nuTotalDescontos = 0;
        break;
    }

    foreach ($arEventos as $arEvento) {
        if( ($arEvento['natureza'] == $stNatureza1 or $arEvento['natureza'] == $stNatureza2)
            and ($arEvento['cod_evento'] != $inCodEvento or $arEvento['desdobramento'] != $stDesdobramento) ){
            if ($arEvento['natureza'] == 'P') {
                $arEvento['proventos'] = $arEvento['valor'];
            } else {
                $arEvento['proventos'] = "0,00";
            }
            if ($arEvento['natureza'] == 'D' or $arEvento['natureza'] == 'B' or $arEvento['natureza'] == 'I') {
                $arEvento['descontos'] = $arEvento['valor'];
            } else {
                $arEvento['descontos'] = "0,00";
            }
            $arTemp[] = $arEvento;
            $stTimestamp        = $arEvento['timestamp_registro'];
            $inCodEvento        = $arEvento['cod_evento'];
            $stDesdobramento    = $arEvento['desdobramento'] ;
        }
        if ($boTodos) {
            if ($arEvento['natureza'] == 'P') {
                $nuTotalProventos += $arEvento['valor'];
            }
            if ($arEvento['natureza'] == 'D') {
                $nuTotalDescontos += $arEvento['valor'];
            }
        }
    }

    if ($boTodos) {
        $arTemp[] = array("descricao"=>"Soma dos Proventos","proventos"=>$nuTotalProventos);
        $arTemp[] = array("descricao"=>"Soma dos Descontos","descontos"=>$nuTotalDescontos);
        $arTemp[] = array("descricao"=>"Líquido","proventos"=>$nuTotalProventos-$nuTotalDescontos);

    }
    $rsEventos = new recordset;
    $rsEventos->preenche($arTemp);
    if (!$boTodos) {
        $rsEventos->addFormatacao("quantidade","NUMERIC_BR");
    }
    $rsEventos->addFormatacao("proventos","NUMERIC_BR");
    $rsEventos->addFormatacao("descontos","NUMERIC_BR");

    return $rsEventos;
}

function preencheCGMContrato($boExecuta=false,$boExtendido=false)
{
    if ($_POST['inContrato']) {
        $obRPessoalServidor = new RPessoalServidor;
        $obRCGMPessoaFisica = new RCGMPessoaFisica;
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->setRegistro($_POST['inContrato']);
        $obRPessoalServidor->roUltimoContratoServidor->listarContratosServidorResumido($rsContratoServidor,$boTransacao);
        if ( $rsContratoServidor->getNumLinhas() > 0 ) {
            $obRPessoalServidor->setCodServidor( $rsContratoServidor->getCampo('cod_servidor') );
            $obRPessoalServidor->consultarServidor($rsServidor,$boTransacao);
            $obRCGMPessoaFisica->setNumCGM($rsServidor->getCampo('numcgm'));
            $obRCGMPessoaFisica->consultarCGM($rsCGM);
            $stNomCGM = $rsCGM->getCampo('numcgm') ." - ". $rsCGM->getCampo('nom_cgm');
        }
        if ( $rsContratoServidor->getNumLinhas() > 0 ) {
            $stJs .= "d.getElementById('inNomCGM').innerHTML = '".addslashes($stNomCGM)."';       \n";
            $stJs .= "f.hdnCGM.value = '".addslashes($stNomCGM)."';                               \n";
            if ($boExtendido) {
                $stJs .= preencheInformacoesFuncao(false,$_POST['inContrato']);
            }
        } else {
            if ($boExtendido) {
                $stJs .= preencheInformacoesFuncao(false,$_POST['inContrato']);
            }
            $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;';                                \n";
            $stJs .= "d.getElementById('inContrato').value = '';                                        \n";
            $stJs .= "alertaAviso('@Matrícula informada não existe. (".$_POST['inContrato'].")','form','erro','".Sessao::getId()."');   \n";

        }
    } else {
        $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;'; \n";
        if ($boExtendido) {
            $stJs .= "d.getElementById('stInformacoesFuncao').innerHTML = '&nbsp;'; \n";
        }
    }
    $stJs .= "f.boFiltrarFolhaComplementar.checked = false; \n";
    $stJs .= gerarSpan4();
    $stJs .= limparSpan5();
    $stJs .= "d.getElementById('Spanlink').style.display = 'none'; \n";
    $stJs .= " LiberaFrames(true, false);";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

$stCtrl = ($_POST["stCtrl"]) ? $_POST["stCtrl"] : $_GET["stCtrl"];
switch ($stCtrl) {
    case "habilitaSpan":
        $stJs.= habilitaSpan();
    break;
    case "habilitaSpan2":
        $stJs.= habilitaSpan2();
    break;
    case "visualizar":
        $stJs.= gerarSpan5();
        $stJs.="window.parent.frames['telaPrincipal'].location.hash ='#eventos'";
    break;
    case "limpar":
        $stJs.= limpar();
    break;
    case "limparSpan5":
        $stJs.= limparSpan5();
    break;
    case "mostrarLinkConsultaComplementar":
        $stJs.= mostrarLinkConsultaComplementar();
    break;
    case "mostrarLinkConsulta":
        $stJs .= mostrarLinkConsulta();
    break;
    case "preencheCGMContrato":
        $stJs .= preencheCGMContrato();
    break;
    case "imprimir":
        $stJs .= imprimir();
    break;
    case "gerarSpanDadosInformativos":
        $stJs .= gerarSpanDadosInformativos();
    break;
    case "buscaEvento":
        $stJs .= buscaEvento();
    break;
    case "processarPopUp":
        $stJs .= processarPopUp();
        echo $stJs;
        $stJs = "";
        break;

}
if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
