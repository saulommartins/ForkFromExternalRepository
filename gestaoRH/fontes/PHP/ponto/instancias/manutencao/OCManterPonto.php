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
    * Data de Criação: 13/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Rafael Garbin

    * @package URBEM
    * @subpackage

    $Id:
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoConfiguracaoParametrosGerais.class.php"                                );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoJustificativa.class.php"                                               );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoJustificativaHoras.class.php"                                          );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoRelogioPontoJustificativa.class.php"                                   );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoDadosRelogioPonto.class.php"                                           );
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php"                                          );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

$stPrograma = "ManterPonto";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

function montaSpanLoteDiario()
{
    GLOBAL $pgList, $pgForm;
    $obFormulario = new Formulario();
    $stHTML = "";

    if ($_GET["boTipoManutencao"] == "LOTE_DIARIO") {
        $obDataLote = new Data();
        $obDataLote->setName    ( "dtLote"                                           );
        $obDataLote->setValue   ( $dtRequerimentoAposentadoria                       );
        $obDataLote->setRotulo  ( "Dia do Lote"                                      );
        $obDataLote->setTitle   ( "Informe o dia do lote, para manutenção."          );
        $obDataLote->setNull    ( false                                              );
        $obFormulario->addComponente($obDataLote);

        $pgProx = $pgForm;
    } else {
        include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                         );
        $obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
        $obRFolhaPagamentoFolhaSituacao->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);

        $obPeriodo = new Periodo();
        $obPeriodo->setRotulo ( "Período"                                                                         );
        $obPeriodo->setTitle  ( "Informe o período para que o sistema busque as informações do ponto do servidor.");
        $obPeriodo->setNull   ( false                                                                             );

        if ($rsUltimaMovimentacao->getNumLinhas() > 0) {
            $obPeriodo->obDataInicial->setValue($rsUltimaMovimentacao->getCampo('dt_inicial'));
            $obPeriodo->obDataFinal->setValue($rsUltimaMovimentacao->getCampo('dt_final'));
        }

        $obFormulario->addComponente($obPeriodo);

        $pgProx = $pgList;
    }
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stHTML = $obFormulario->getHTML();

    $stJs .= "jQuery('#spnLoteDiario').html('".$stHTML."');                                              \n";
    $stJs .= "jQuery('#hdnTipoManutencao').val('".$obFormulario->obJavaScript->getInnerJavaScript()."'); \n";
    $stJs .= "jQuery('#frm').attr('action', '".$pgProx."');                                              \n";

    return $stJs;
}

function FLProcessaOnLoad()
{
    $stJs .= montaSpanLoteDiario();

    return $stJs;
}

function montaSpanHorasExtras()
{
    $stHTML = "";

    if (trim($_GET["inCodConfiguracao"])!="") {
        $stFiltro = " WHERE configuracao_parametros_gerais.cod_configuracao = ".$_GET["inCodConfiguracao"];
        $stOrdem  = "configuracao_parametros_gerais.descricao";
        $obTPontoConfiguracaoParametrosGerais = new TPontoConfiguracaoParametrosGerais();
        $obTPontoConfiguracaoParametrosGerais->recuperaRelacionamento($rsConfiguracaoParametrosGerais, $stFiltro, $stOrdem);

        if ($rsConfiguracaoParametrosGerais->getCampo("somar_extras") == "t") {

            $stFiltro = " WHERE contrato.cod_contrato = ".$_REQUEST["inCodContrato"];
            $obTPontoDadosRelogioPonto = new TPontoDadosRelogioPonto();
            $obTPontoDadosRelogioPonto->recuperaDadosContratoServidor($rsDadosRelogioPonto, $stFiltro);
            ($rsDadosRelogioPonto->getCampo("autorizar_horas_extras") == "f" ? $boAutorizarHorasExtras=false : $boAutorizarHorasExtras=true);

            $obChkHorasExtras = new CheckBox();
            $obChkHorasExtras->setRotulo ("Autorizar Horas Extras");
            $obChkHorasExtras->setTitle  ("Marque para que o sistema efetue o cálculo das horas extras para o servidor.");
            $obChkHorasExtras->setName   ("boAutorizarHorasExtras");
            $obChkHorasExtras->setId     ("boAutorizarHorasExtras");
            $obChkHorasExtras->setValue  (true);
            if ($boAutorizarHorasExtras) {
                $obChkHorasExtras->setChecked(true);
            }

            $obFormulario = new Formulario();
            $obFormulario->addComponente($obChkHorasExtras);
            $obFormulario->montaInnerHTML();

            $stHTML = $obFormulario->getHTML();
        }
    }
    $stJs .= "jQuery('#spnHorasExtras').html('".$stHTML."');  \n";

    return $stJs;
}

function montaSpanJustificativa()
{
    $stIdsComponentes = "";
    $stOrdem = "descricao";
    $obTPontoJustificativa = new TPontoJustificativa();
    $obTPontoJustificativa->recuperaTodos($rsPontoJustificativa, $stFiltro="", $stOrdem);

    $obHdnInId = new Hidden;
    $obHdnInId->setName( "inId" );
    $obHdnInId->setId( "inId" );
    $obHdnInId->setValue( $_REQUEST["inId"] );

    $obCmbJustificativa = new Select;
    $obCmbJustificativa->setRotulo     ( "Justificativa"                    );
    $obCmbJustificativa->setTitle      ( "Informe a Justificativa."         );
    $obCmbJustificativa->setName       ( "inCodJustificativa"               );
    $obCmbJustificativa->setId         ( "inCodJustificativa"               );
    $obCmbJustificativa->setStyle      ( "width: 300px"                     );
    $obCmbJustificativa->setNull       ( false                              );
    $obCmbJustificativa->setCampoDesc  ( "descricao"                        );
    $obCmbJustificativa->setCampoId    ( "cod_justificativa"                );
    $obCmbJustificativa->addOption     ( "", "Selecione"                    );
    $obCmbJustificativa->preencheCombo ( $rsPontoJustificativa              );
    $obCmbJustificativa->obEvento->setOnChange("montaParametrosGET('carregaDadosJustificativa','inCodJustificativa');");
    $obCmbJustificativa->setNullBarra  ( false                              );
    $stIdsComponentes .= $obCmbJustificativa->getId().",";

    $obTxtObservacao = new TextArea;
    $obTxtObservacao->setRotulo         ( "Observação"                                                      );
    $obTxtObservacao->setName           ( "stObservacao"                                                    );
    $obTxtObservacao->setId             ( "stObservacao"                                                    );
    $obTxtObservacao->setValue          ( ""                                                                );
    $obTxtObservacao->setTitle          ( "Digite as observações relacionadas a justificativa cadastrada."  );
    $obTxtObservacao->setMaxCaracteres  ( 300                                                               );
    $obTxtObservacao->setNull           ( false                                                             );
    $stIdsComponentes .= $obTxtObservacao->getId().",";

    $obPeriodo = new Periodo();
    $obPeriodo->setRotulo ( "Período"                              );
    $obPeriodo->setTitle  ( "Informe o período da justificativa."  );
    $obPeriodo->setNull   ( false                                  );
    $obPeriodo->obDataInicial->setId("stDataInicial");
    $obPeriodo->obDataFinal->setId("stDataFinal");
    $stIdsComponentes .= $obPeriodo->getId().",";

    $obChkAnularHorasFalta = new CheckBox();
    $obChkAnularHorasFalta->setName   ( "boAnularHorasFaltas"          );
    $obChkAnularHorasFalta->setId     ( "boAnularHorasFaltas"          );
    $obChkAnularHorasFalta->setRotulo ( "Anular todas as horas faltas" );
    $obChkAnularHorasFalta->setValue  ( true                           );

    $obSpnAnularHorasFaltas = new Span;
    $obSpnAnularHorasFaltas->setId ( "spnAnularHorasFaltas" );

    $obChkSomenteDiasTrabalho = new CheckBox();
    $obChkSomenteDiasTrabalho->setRotulo ( "Incluir somente dias de trabalho" );
    $obChkSomenteDiasTrabalho->setName   ( "boSomenteDiasTrabalho"            );
    $obChkSomenteDiasTrabalho->setId     ( "boSomenteDiasTrabalho"            );
    $obChkSomenteDiasTrabalho->setValue  ( true                               );

    $obSpnListaJustificativa = new Span;
    $obSpnListaJustificativa->setId ( "spnListaJustificativa" );
    /*
    $obBtnFechar = new Ok();
    $obBtnFechar->setValue ("Fechar");
    $obBtnFechar->setId    ("boFecharJustificativa");
    $obBtnFechar->obEvento->setOnClick("montaParametrosGET('fecharJustificativas');");
*/
    $obBtnAtualizar = new Ok();
    $obBtnAtualizar->setValue ("Atualizar");
    $obBtnAtualizar->setId    ("boAtualizar");
    $obBtnAtualizar->obEvento->setOnClick("jQuery('#okVoltar').val('okManutencaoIndividual'); Salvar();");

    $arCampos = array($obCmbJustificativa, $obTxtObservacao, $obPeriodo);
    $stIdsComponentes = substr($stIdsComponentes,0,strlen($stIdsComponentes)-1);

    $obFormulario = new Formulario();
    $obFormulario->addHidden      ( $obHdnInId                     );
    $obFormulario->addTitulo      ( "Justificativas"               );
    $obFormulario->addComponente  ( $obCmbJustificativa            );
    $obFormulario->addComponente  ( $obTxtObservacao               );
    $obFormulario->addComponente  ( $obPeriodo                     );
    $obFormulario->addComponente  ( $obChkAnularHorasFalta         );
    $obFormulario->addSpan        ( $obSpnAnularHorasFaltas        );
    $obFormulario->addComponente  ( $obChkSomenteDiasTrabalho      );
    $obFormulario->incluirAlterar ( "Justificativa", $arCampos, true, false, $stIdsComponentes);
    $obFormulario->addSpan        ( $obSpnListaJustificativa       );
    $obFormulario->defineBarra    ( array($obBtnAtualizar));
    $obFormulario->montaInnerHTML();
    $stHTML = $obFormulario->getHTML();

    $stJs .= " jQuery('#spnJustificativa').html('".$stHTML."');                                                      \n";
    $stJs .= " jQuery('#btLimparJustificativa').attr('onClick', 'montaParametrosGET(\'montaSpanJustificativa\');' ); \n";
    $stJs .= " jQuery('#btAlterarJustificativa').attr('onClick', 'montaParametrosGET(\'alterarJustificativa\');' );  \n";
    $stJs .= " jQuery('#btIncluirJustificativa').attr('onClick', 'montaParametrosGET(\'incluirJustificativa\');' );  \n";
    $stJs .= " jQuery('#boAnularHorasFaltas').attr('checked', '');                                                   \n";
    $stJs .= " jQuery('#boAnularHorasFaltas').attr('disabled', 'disabled');                                          \n";
    $stJs .= " jQuery('#boSomenteDiasTrabalho').attr('checked', '');                                                 \n";
    $stJs .= " jQuery('#boSomenteDiasTrabalho').attr('disabled', 'disabled');                                        \n";
    $stJs .= " jQuery('#spnAnularHorasFaltas').html('');                                                             \n";
    $stJs .= montaListaJustificativas( Sessao::read("arJustificativas") );

    return $stJs;
}

function carregaDadosJustificativa()
{
    $stJs .= "jQuery('#boAnularHorasFaltas').attr('checked', '');             \n";
    $stJs .= "jQuery('#boAnularHorasFaltas').attr('disabled', 'disabled');    \n";
    $stJs .= "jQuery('#boSomenteDiasTrabalho').attr('checked', '');           \n";
    $stJs .= "jQuery('#boSomenteDiasTrabalho').attr('disabled', 'disabled');  \n";
    $stJs .= "jQuery('#spnAnularHorasFaltas').html('');                       \n";

    if (trim($_GET["inCodJustificativa"]) != "") {
        $stFiltro = " WHERE cod_justificativa = ".$_GET["inCodJustificativa"];
        $obTPontoJustificativa = new TPontoJustificativa();
        $obTPontoJustificativa->recuperaTodos($rsRelogioPontoJustificativa, $stFiltro);

        if (trim($rsRelogioPontoJustificativa->getCampo("anular_faltas")) == "t") {
            $stJs .= "jQuery('#boAnularHorasFaltas').attr('checked', 'checked');  \n";
        }

        if (trim($rsRelogioPontoJustificativa->getCampo("lancar_dias_trabalho")) == "t") {
            $stJs .= "jQuery('#boSomenteDiasTrabalho').attr('checked', 'checked'); \n";
        }

        $stFiltro = " WHERE cod_justificativa = ".$_GET["inCodJustificativa"];
        $obTPontoJustificativaHoras = new TPontoJustificativaHoras();
        $obTPontoJustificativaHoras->recuperaTodos($rsJustificativaHoras, $stFiltro);

        if ($rsJustificativaHoras->getNumLinhas() > 0) {
            $stJs .= "jQuery('#boAnularHorasFaltas').attr('checked', 'checked'); \n";

            $obHrAbonar = new Hora;
            $obHrAbonar->setRotulo     ( "Hora a abonar"    );
            $obHrAbonar->setTitle      ( "Informe o número de horas a abonar, em caso de abono parcial das horas." );
            $obHrAbonar->setName       ( "stHoraAbonar"     );
            $obHrAbonar->setId         ( "stHoraAbonar"     );
            $obHrAbonar->setValue      ( $rsJustificativaHoras->getCampo("horas_abono") );
            $obHrAbonar->setSize       ( 10                 );
            $obHrAbonar->setMaxLength  ( 10                 );

            $obHrFaltasAnular = new Hora;
            $obHrFaltasAnular->setRotulo     ( "Hora de faltas a anular"           );
            $obHrFaltasAnular->setTitle      ( "Informe o número de horas faltas a anular, em caso de anulação parcial de faltas." );
            $obHrFaltasAnular->setName       ( "stHoraFaltasAnular"                );
            $obHrFaltasAnular->setId         ( "stHoraFaltasAnular"                );
            $obHrFaltasAnular->setValue      ( $rsJustificativaHoras->getCampo("horas_falta") );
            $obHrFaltasAnular->setSize       ( 10                                  );
            $obHrFaltasAnular->setMaxLength  ( 10                                  );

            $obFormulario = new Formulario();
            $obFormulario->addComponente  ( $obHrFaltasAnular  );
            $obFormulario->addComponente  ( $obHrAbonar        );
            $obFormulario->montaInnerHTML();

            $stHTML = $obFormulario->getHTML();
            $stJs .= "jQuery('#spnAnularHorasFaltas').html('".$stHTML."');  \n";

        }

        if ($rsRelogioPontoJustificativa->getCampo("lancar_dias_trabalho") == "t") {
            $stJs .= "jQuery('#boSomenteDiasTrabalho').attr('checked' , 'checked');   \n";
            $stJs .= "jQuery('#boSomenteDiasTrabalho').attr('disabled', 'disabled');  \n";
        }
    }

    return $stJs;
}

function validaPeriodo($stDataInicial, $stDataFinal)
{
    list($diaIni, $mesIni, $anoIni) = explode("/", $stDataInicial);
    list($diaFim, $mesFim, $anoFim) = explode("/", $stDataFinal);

    $inDataInicial = (int) $anoIni.$mesIni.$diaIni;
    $inDataFinal   = (int) $anoFim.$mesFim.$diaFim;

    if ($inDataInicial > $inDataFinal) {
        return false;
    }

    return true;
}

function validaJustificativa($origem)
{
    $obErro = new Erro();

    if (trim($_GET["inCodJustificativa"])=="") {
        $obErro->setDescricao($obErro->getDescricao()."@Deve ser selecionado a Justificativa.");
    }

    if (trim($_GET["stObservacao"])=="") {
        $obErro->setDescricao($obErro->getDescricao()."@Deve ser informado a Observação.");
    }

    if (trim($_GET["stDataInicial"])=="") {
        $obErro->setDescricao($obErro->getDescricao()."@Deve ser informado o Período.");
    }

    if (trim($_GET["stDataFinal"])=="") {
        $obErro->setDescricao($obErro->getDescricao()."@Deve ser informado o Período.");
    }

    if (!$obErro->ocorreu()) {
        if (!validaPeriodo($_GET["stDataInicial"], $_GET["stDataFinal"])) {
            $obErro->setDescricao($obErro->getDescricao()."@O Período informado é inválido.");
        }
    }

    // Verifica se existe assentamento gerado para o periodo informado
    if (!$obErro->ocorreu()) {
        $stFiltro  = " AND classificacao_assentamento.cod_tipo = 2  \n";    //Afastamento temporario
        $stFiltro .= " AND assentamento_gerado_contrato_servidor.cod_contrato = ".$_GET["inCodContrato"]." \n";
        $stFiltro .= " AND ( to_date('".$_GET["stDataInicial"]."', 'dd/mm/yyyy') BETWEEN assentamento_gerado.periodo_inicial AND assentamento_gerado.periodo_final \n";
        $stFiltro .= "       OR to_date('".$_GET["stDataFinal"]."', 'dd/mm/yyyy')   BETWEEN assentamento_gerado.periodo_inicial AND assentamento_gerado.periodo_final \n";
        $stFiltro .= "       OR ( assentamento_gerado.periodo_inicial >= to_date('".$_GET["stDataInicial"]."', 'dd/mm/yyyy') AND assentamento_gerado.periodo_final <= to_date('".$_GET["stDataFinal"]."', 'dd/mm/yyyy')) \n";
        $stFiltro .= "     ) \n";

        $obTPessoalAssentamentoGerado = new TPessoalAssentamentoGerado();
        $obTPessoalAssentamentoGerado->recuperaAssentamentoGerado($rsAssentamentoGerado,$stFiltro);

        if ($rsAssentamentoGerado->getNumLinhas() > 0) {
            $obErro->setDescricao($obErro->getDescricao()."@Existem assentamentos gerados para o período informado.");
        }
    }

    //verifica já existe justificativas compreendida no periodo informado
    $inContador = 0;
    $arJustificativas = Sessao::read("arJustificativas");

    if (count($arJustificativas)>0) {
        foreach ($arJustificativas as $chave => $arJustificativa) {

            list($dia, $mes, $ano) = explode("/", $_GET["stDataInicial"]);
            $inPeriodoInicialNovo  = (int) $ano.$mes.$dia;
            list($dia, $mes, $ano) = explode("/", $_GET["stDataFinal"]);
            $inPeriodoFinalNovo    = (int) $ano.$mes.$dia;

            list($dia, $mes, $ano) = explode("/", $arJustificativa["stDataInicial"]);
            $inPeriodoInicial      = (int) $ano.$mes.$dia;
            list($dia, $mes, $ano) = explode("/", $arJustificativa["stDataFinal"]);
            $inPeriodoFinal        = (int) $ano.$mes.$dia;

            if ( ($inPeriodoInicialNovo >= $inPeriodoInicial && $inPeriodoInicialNovo <= $inPeriodoFinal)
                  || ($inPeriodoFinalNovo >= $inPeriodoInicial && $inPeriodoFinalNovo <= $inPeriodoFinal)
                  || ($inPeriodoInicial >= $inPeriodoInicialNovo && $inPeriodoFinal <= $inPeriodoFinalNovo)) {

                if (!(trim($_GET["inId"]) == trim($arJustificativa["inId"]) && trim($origem)=="alterar")) {
                    $obErro->setDescricao($obErro->getDescricao()."@Existem justificativas compreendidas dentro do período informado(".$arJustificativa["stDataInicial"]." a ".$arJustificativa["stDataFinal"].").");
                    break;
                }
            }
        }
    }

    return $obErro;
}

function incluirJustificativa()
{
    $obErro = validaJustificativa("incluir");

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $arJustificativas = Sessao::read("arJustificativas");

        $stFiltro = " WHERE cod_justificativa = ".$_GET["inCodJustificativa"];
        $obTPontoJustificativa = new TPontoJustificativa();
        $obTPontoJustificativa->recuperaTodos($rsPontoJustificativa, $stFiltro);

        $arElementos = array();
        $arElementos["inId"]                = count($arJustificativas) + 1;
        $arElementos["stDataInicial"]       = $_GET["stDataInicial"];
        $arElementos["stDataFinal"]         = $_GET["stDataFinal"];
        $arElementos["stObservacao"]        = $_GET["stObservacao"];
        $arElementos["stHoraAbonar"]        = $_GET["stHoraAbonar"];
        $arElementos["stHoraFaltasAnular"]  = $_GET["stHoraFaltasAnular"];
        $arElementos["inCodJustificativa"]  = $_GET["inCodJustificativa"];
        $arElementos["stJustificativa"]     = $rsPontoJustificativa->getCampo("descricao");

        list($dia, $mes, $ano)              = explode("/", $arElementos["stDataInicial"]);
        $arElementos["data_ordenacao"]      = (int) $ano.$mes.$dia;

        $arJustificativas[] = $arElementos;

        Sessao::write("arJustificativas", $arJustificativas);
        $stJs .= montaListaJustificativas( $arJustificativas );
        $stJs .= montaSpanJustificativa();
    }

    return $stJs;
}

function alterarJustificativa()
{
    $inId = $_REQUEST["inId"];

    $obErro = validaJustificativa("alterar");

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $arJustificativas = Sessao::read('arJustificativas');

        foreach ($arJustificativas as $campo => $valor) {
            if (trim($valor["inId"]) == trim($inId)) {
                $stFiltro = " WHERE cod_justificativa = ".$_GET["inCodJustificativa"];
                $obTPontoJustificativa = new TPontoJustificativa();
                $obTPontoJustificativa->recuperaTodos($rsPontoJustificativa, $stFiltro);

                $arJustificativas[$campo]["stDataInicial"]       = $_GET["stDataInicial"];
                $arJustificativas[$campo]["stDataFinal"]         = $_GET["stDataFinal"];
                $arJustificativas[$campo]["stObservacao"]        = $_GET["stObservacao"];
                $arJustificativas[$campo]["stHoraAbonar"]        = $_GET["stHoraAbonar"];
                $arJustificativas[$campo]["stHoraFaltasAnular"]  = $_GET["stHoraFaltasAnular"];
                $arJustificativas[$campo]["inCodJustificativa"]  = $_GET["inCodJustificativa"];
                $arJustificativas[$campo]["stJustificativa"]     = $rsPontoJustificativa->getCampo("descricao");

                list($dia, $mes, $ano)                           = explode("/", $arJustificativas[$campo]["stDataInicial"]);
                $arJustificativas[$campo]["data_ordenacao"]      = (int) $ano.$mes.$dia;
            }
        }
        Sessao::write('arJustificativas', $arJustificativas);
        $stJs .= montaListaJustificativas( $arFormatosCadastrados );
        $stJs .= montaSpanJustificativa();
    }

    return $stJs;
}

function excluirJustificativa()
{
    $arTMP = array ();
    $id = $_GET["inId"];
    $arJustificativas = Sessao::read("arJustificativas");
    $arJustificativasExcluidos = Sessao::read("arJustificativasExcluidos");
    Sessao::remove("arJustificativas");

    foreach ($arJustificativas as $campo => $valor) {
        if ($valor["inId"] != $id) {
            $arTMP[] = $valor;
        } else {
            $arJustificativasExcluidos[] = $valor;
        }
    }
    Sessao::write("arJustificativas", $arTMP);
    Sessao::write("arJustificativasExcluidos", $arJustificativasExcluidos);
    $stJs = montaListaJustificativas( $arTMP );

    return $stJs;
}

function recuperaJustificativas()
{
    Sessao::remove("arJustificativas");

    $stFiltro = " AND relogio_ponto_justificativa.cod_contrato = ".$_GET["inCodContrato"];
    $stOrderm = " ORDER BY relogio_ponto_justificativa.periodo_inicio, relogio_ponto_justificativa.periodo_termino";
    $obTPontoRelogioPontoJustificativa = new TPontoRelogioPontoJustificativa();
    $obTPontoRelogioPontoJustificativa->recuperaJustificativa($rsRelogioPontoJustificativa, $stFiltro, $stOrderm);

    $arElementos = array();
    while (!$rsRelogioPontoJustificativa->eof()) {
        $arTMP = array();

        $inId                         = count($arElementos) + 1;
        $arTMP["inId"]                = $inId;
        $arTMP["stDataInicial"]       = $rsRelogioPontoJustificativa->getCampo("periodo_inicio");
        $arTMP["stDataFinal"]         = $rsRelogioPontoJustificativa->getCampo("periodo_termino");
        $arTMP["stObservacao"]        = $rsRelogioPontoJustificativa->getCampo("observacao");
        $arTMP["stHoraAbonar"]        = $rsRelogioPontoJustificativa->getCampo("horas_abono");
        $arTMP["stHoraFaltasAnular"]  = $rsRelogioPontoJustificativa->getCampo("horas_falta");
        $arTMP["inCodJustificativa"]  = $rsRelogioPontoJustificativa->getCampo("cod_justificativa");
        $arTMP["inSequencia"]         = $rsRelogioPontoJustificativa->getCampo("sequencia");
        $arTMP["inCodContrato"]       = $rsRelogioPontoJustificativa->getCampo("cod_contrato");
        $arTMP["stTimestamp"]         = $rsRelogioPontoJustificativa->getCampo("timestamp");
        $arTMP["stJustificativa"]     = $rsRelogioPontoJustificativa->getCampo("descricao");

        list($dia, $mes, $ano)       = explode("/", $rsRelogioPontoJustificativa->getCampo("periodo_inicio"));
        $arTMP["data_ordenacao"]     = (int) $ano.$mes.$dia;

        $arElementos[] = $arTMP;

        $rsRelogioPontoJustificativa->proximo();
    }
    Sessao::write("arJustificativas", $arElementos);
}

function montaListaJustificativas($arRecordSet)
{
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );
    $rsRecordSet->ordena("data_ordenacao");

    $stLink .= "&stAcao=".$_REQUEST["stAcao"];

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Justificativas Cadastradas no Período" );
        $obLista->setRecordSet( $rsRecordSet );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Período" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Justificativa" );
        $obLista->ultimoCabecalho->setWidth( 70 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Ação" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[stDataInicial] a [stDataFinal]");
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stJustificativa");
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setLinkId("alterar");
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('carregaJustificativa');");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirJustificativa');");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    $stJs = "jQuery('#spnListaJustificativa').html('".$stHtml."');";

    return $stJs;
}

function carregaJustificativa()
{
    $arJustificativas = Sessao::read("arJustificativas");
    $inId = $_REQUEST["inId"];

    foreach ($arJustificativas as $chave => $dados) {
        if (trim($inId) == trim($dados["inId"])) {
            $stJs .= " jQuery('#inCodJustificativa').val('".$dados["inCodJustificativa"]."');  \n";
            $stJs .= " jQuery('#stObservacao').val('".$dados["stObservacao"]."');              \n";
            $stJs .= " jQuery('#stDataInicial').val('".$dados["stDataInicial"]."');            \n";
            $stJs .= " jQuery('#stDataFinal').val('".$dados["stDataFinal"]."');                \n";
            $stJs .= " jQuery('#inId').val('".$dados["inId"]."');                              \n";

            $_GET["inCodJustificativa"] = $dados["inCodJustificativa"];
            $stJs .= carregaDadosJustificativa();

            #if (!$dados["stHoraFaltasAnular"]) {
                $stJs .= " jQuery('#stHoraAbonar').val('".$dados["stHoraAbonar"]."');               \n";
                $stJs .= " jQuery('#stHoraFaltasAnular').val('".$dados["stHoraFaltasAnular"]."');   \n";
            #}
        }
    }

    $stJs .= " jQuery('#btIncluirJustificativa').attr('disabled', 'disabled');        \n";
    $stJs .= " jQuery('#btAlterarJustificativa').attr('disabled', '');                \n";
    $stJs .= " jQuery('#btAlterarJustificativa').attr('onClick', 'montaParametrosGET(\'alterarJustificativa\');' );";

    return $stJs;
}

function montaListaDadosRelogioPonto()
{
    // Montando Grade com o ponto do periodo
    $obTPontoDadosRelogioPonto = new TPontoDadosRelogioPonto();
    $obTPontoDadosRelogioPonto->setDado("inCodContrato", $_GET["inCodContrato"]);
    $obTPontoDadosRelogioPonto->setDado("stDataInicial", $_GET["stPeriodoInicial"]);
    $obTPontoDadosRelogioPonto->setDado("stDataFinal"  , $_GET["stPeriodoFinal"]);
    $obTPontoDadosRelogioPonto->recuperaDadosRelogioPontoPeriodo($rsDadosRelogioPontoPeriodo);

    $inIndex = 1;
    $arRelogioPontoPeriodo = $rsDadosRelogioPontoPeriodo->getElementos();
    if (is_array($arRelogioPontoPeriodo) && count($arRelogioPontoPeriodo)>0) {
        foreach ($arRelogioPontoPeriodo as $chave => $dados) {
            $arHorario = array();
            $input = "";

            if (trim($dados["horario"])!="") {
                $arHorario = explode("-", $dados["horario"]);
            }

            list($stDia, $stMes, $stAno) = explode("/", $dados["data"]);

            if (count($arHorario)>0) {
                $inContador = 1;
                foreach ($arHorario as $key => $hora) {
                    if (trim($hora)!="") {
                        $input .= "<input tabindex='".$inIndex."' name='*_*".$stDia."_".$stMes."_".$stAno."*_*".$key."' type='text' value='".trim($hora)."' MAXLENGTH='5' size='5'
                                    OnKeyUp='mascaraHora(this, event);' OnBlur='verificaHora(this);' />";
                        $inIndex += 1;
                    }
                    $inContador++;
                }
                $inContador--;

                if ($inContador < 4) {
                     for ($i=$inContador; $i<4; $i++) {
                            $input .= "<input tabindex='".$inIndex."' name='*_*".$stDia."_".$stMes."_".$stAno."*_*".$i."' type='text' value='' MAXLENGTH='5' size='5'
                                        OnKeyUp='mascaraHora(this, event);' OnBlur='verificaHora(this);' />";
                            $inIndex += 1;
                     }
                }
            } else {
                for ($i=1; $i<=4; $i++) {
                        $input .= "<input tabindex='".$inIndex."' name='*_*".$stDia."_".$stMes."_".$stAno."*_*".$i."' type='text' value='' MAXLENGTH='5' size='5'
                                    OnKeyUp='mascaraHora(this, event);' OnBlur='verificaHora(this);' />";
                        $inIndex += 1;
                }
            }
            $arRelogioPontoPeriodo[$chave]["horario"] = $input;
        }
    }

    $rsLista = new RecordSet();
    $rsLista->preenche($arRelogioPontoPeriodo);

    $obLista = new TableTree();
    $obLista->setRecordset($rsLista);
    $obLista->setArquivo(CAM_GRH_PON_INSTANCIAS."manutencao/DTManterPonto.php");
    $obLista->setParametros(array("horario_padrao", "carga_horaria_padrao", "horas_faltas_anuladas", "horas_abonadas"));
    $obLista->setSummary("Registro do Relógio Ponto do Período");

    $obLista->Head->addCabecalho( "Data"          , 10  );
    $obLista->Head->addCabecalho( "Dia"           , 8  );
    $obLista->Head->addCabecalho( "Afastamento/Justificativa" , 10  );
    $obLista->Head->addCabecalho( "Horário"       , 30  );
    $obLista->Head->addCabecalho( "Horas Trab"    , 10  );
    $obLista->Head->addCabecalho( "Horas Falta"   , 10  );
    $obLista->Head->addCabecalho( "Tipo"          , 8  );
    $obLista->Head->addCabecalho( "Origem"        , 8  );

    $obLista->Body->addCampo( "data"          , "C" );
    $obLista->Body->addCampo( "dia"           , "C" );
    $obLista->Body->addCampo( "justificativa_afastamento" , "E" );
    $obLista->Body->addCampo( "horario"       , "C" );
    $obLista->Body->addCampo( "horas_trabalho", "C" );
    $obLista->Body->addCampo( "horas_faltas"  , "C" );
    $obLista->Body->addCampo( "tipo"          , "C" );
    $obLista->Body->addCampo( "origem"        , "E" );

    $obLista->montaHTML(true);
    $stHtml = $obLista->getHtml();

    if (trim($_GET["inCodConfiguracao"])!="") {
        $stJs .= " jQuery('#spnDadosPontoPeriodo').html('".$stHtml."'); \n";
    } else {
        $stJs .= " jQuery('#spnDadosPontoPeriodo').html(''); \n";
    }

    return $stJs;
}

function montaListaDadosRelogioPontoLote()
{
    $inIndex = 1;
    $arRelogioPontoPeriodo = Sessao::read("arContratosLoteDiario");

    if (is_array($arRelogioPontoPeriodo) && count($arRelogioPontoPeriodo)>0) {
        foreach ($arRelogioPontoPeriodo as $chave => $dados) {
            $arHorario = array();
            $input = "";

            if (trim($dados["horario"])!="") {
                $arHorario = explode("-", $dados["horario"]);
            }

            list($stDia, $stMes, $stAno) = explode("/", $dados["dtLote"]);

            if (count($arHorario)>0) {
                $inContador = 1;
                foreach ($arHorario as $key => $hora) {
                    if (trim($hora)!="") {
                        $input .= "<input tabindex='".$inIndex."' name='*_*".$stDia."_".$stMes."_".$stAno."*_*".$key."*_*".$dados["cod_contrato"]."' type='text' value='".trim($hora)."' MAXLENGTH='5' size='5'
                                    OnKeyUp='mascaraHora(this, event);' OnBlur='verificaHora(this);' />";
                        $inIndex += 1;
                    }
                    $inContador++;
                }
                $inContador--;

                if ($inContador < 4) {
                     for ($i=$inContador; $i<4; $i++) {
                        $input .= "<input tabindex='".$inIndex."' name='*_*".$stDia."_".$stMes."_".$stAno."*_*".$i."*_*".$dados["cod_contrato"]."' type='text' value='' MAXLENGTH='5' size='5'
                                    OnKeyUp='mascaraHora(this, event);' OnBlur='verificaHora(this);' />";
                        $inIndex += 1;
                     }
                }
            } else {
                for ($i=1; $i<=4; $i++) {
                        $input .= "<input tabindex='".$inIndex."' name='*_*".$stDia."_".$stMes."_".$stAno."*_*".$i."*_*".$dados["cod_contrato"]."' type='text' value='' MAXLENGTH='5' size='5'
                                    OnKeyUp='mascaraHora(this, event);' OnBlur='verificaHora(this);' />";
                        $inIndex += 1;
                }
            }
            $arRelogioPontoPeriodo[$chave]["horario"] = $input;
        }
    }

    $rsLista = new RecordSet();
    $rsLista->preenche($arRelogioPontoPeriodo);

    $obLista = new TableTree();
    $obLista->setRecordset($rsLista);
    $obLista->setArquivo(CAM_GRH_PON_INSTANCIAS."manutencao/DTManterPonto.php");
    $obLista->setParametros(array("horario_padrao", "carga_horaria_padrao", "justificativa_afastamento", "dtLote"));
    $obLista->setSummary("Registro do Relógio Ponto do Período");

    $obLista->Head->addCabecalho( "Matrícula"     , 10  );
    $obLista->Head->addCabecalho( "Nome"          , 35  );
    $obLista->Head->addCabecalho( "Tipo"          , 10  );
    $obLista->Head->addCabecalho( "Horário"       , 25  );
    $obLista->Head->addCabecalho( "Horas Trab"    , 10  );
    $obLista->Head->addCabecalho( "Horas Falta"   , 10  );

    $obLista->Body->addCampo( "registro"          , "C" );
    $obLista->Body->addCampo( "nom_cgm"           , "E" );
    $obLista->Body->addCampo( "tipo"              , "C" );
    $obLista->Body->addCampo( "horario"           , "E" );
    $obLista->Body->addCampo( "horas_trabalho"    , "C" );
    $obLista->Body->addCampo( "horas_faltas"      , "C" );

    $obLista->montaHTML(true);
    $stHtml = $obLista->getHtml();
    $stJs .= " jQuery('#spnDadosPontoPeriodo').html('".$stHtml."'); \n";

    return $stJs;

}

function FMProcessaOnLoad()
{
    $stJs  = "";

    if (trim($_GET["boTipoManutencao"]) == "INDIVIDUAL") {
        $stJs .= montaSpanHorasExtras();
        $stJs .= montaListaDadosRelogioPonto();
        $stJs .= recuperaJustificativas();
    } else {
        $stJs .= montaListaDadosRelogioPontoLote();
    }

    return $stJs;
}

function fecharJustificativas()
{
    $stJs .= " jQuery('#spnJustificativa').html(''); \n";

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "montaSpanLoteDiario":
        $stJs .= montaSpanLoteDiario();
        break;
    case "FLProcessaOnLoad":
        $stJs .= FLProcessaOnLoad();
        break;
    case "FMProcessaOnLoad":
        $stJs .= FMProcessaOnLoad();
        break;
    case "montaSpanHorasExtras":
        $stJs .= montaSpanHorasExtras();
        break;
    case "montaSpanJustificativa":
        $stJs .= montaSpanJustificativa();
        break;
    case "carregaDadosJustificativa":
        $stJs .= carregaDadosJustificativa();
        break;
    case "incluirJustificativa":
        $stJs .= incluirJustificativa();
        break;
    case "alterarJustificativa":
        $stJs .= alterarJustificativa();
        break;
    case "excluirJustificativa":
        $stJs .= excluirJustificativa();
        break;
    case "carregaJustificativa":
        $stJs .= carregaJustificativa();
        break;
    case "fecharJustificativas":
        $stJs .= fecharJustificativas();
        break;
}

if($stJs)
   echo($stJs);
?>
