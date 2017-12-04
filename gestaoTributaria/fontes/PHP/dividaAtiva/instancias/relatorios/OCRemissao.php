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
    * Página de Frame Oculto Relatorio Remissao
    * Data de Criação   : 06/10/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: $

    * Casos de uso: uc-05.04.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
include_once CAM_GT_ARR_COMPONENTES.'MontaGrupoCredito.class.php';
include_once CAM_GT_MON_COMPONENTES.'IPopUpCredito.class.php';

$pgOcul = 'OCRemissao.php';

function montaListaCredito(&$rsLista)
{
    $rsLista->setPrimeiroElemento();
    if (!$rsLista->eof()) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao(false);
        $obLista->setRecordSet($rsLista);

        $obLista->setTitulo ('Lista de Créditos');

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(2);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Exercício');
        $obLista->ultimoCabecalho->setWidth(7);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Código');
        $obLista->ultimoCabecalho->setWidth(13);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Descrição');
        $obLista->ultimoCabecalho->setWidth(80);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(2);
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('stExercicioLista');
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('stCodCredito');
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('stCreditoDescricao');
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao('EXCLUIR');
        $obLista->ultimaAcao->setFuncao(true);
        $obLista->ultimaAcao->addCampo('1', 'stCodCredito');
        $obLista->ultimaAcao->addCampo('2', 'stExercicioLista');
        $obLista->ultimaAcao->setLink("javascript:excluirCredito();");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace("\n","",$stHTML);
        $stHTML = str_replace("  ","",$stHTML);
        $stHTML = str_replace("'","\\'",$stHTML);
    } else {
        $stHTML = '&nbsp;';
    }

    $stJs = "d.getElementById('spnLista').innerHTML = '".$stHTML."';";

    return $stJs;
}

function montaListaGrupoCredito(&$rsLista)
{
    $rsLista->setPrimeiroElemento();
    if (!$rsLista->eof()) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao(false);
        $obLista->setRecordSet($rsLista);

        $obLista->setTitulo('Lista de Grupos de Créditos');

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(2);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Código');
        $obLista->ultimoCabecalho->setWidth(20);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Descrição');
        $obLista->ultimoCabecalho->setWidth(80);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(2);
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('stCodGrupo');
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('stGrupoDescricao');
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao('EXCLUIR');
        $obLista->ultimaAcao->setFuncao(true);
        $obLista->ultimaAcao->addCampo('1', 'stCodGrupo');
        $obLista->ultimaAcao->setLink('javascript:excluirGrupoCredito();');
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace("\n","",$stHTML);
        $stHTML = str_replace("  ","",$stHTML);
        $stHTML = str_replace("'","\\'",$stHTML);
    } else {
        $stHTML = '&nbsp;';
    }

    $stJs = "d.getElementById('spnLista').innerHTML = '".$stHTML."';";

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case 'ExcluirCredito':
        if ($_REQUEST['inIndice1']) {
            $arListaCreditoSessao = Sessao::read('arLista');
            $arListaCreditoTMP = array();
            $inTotalDados = count($arListaCreditoSessao);
            for ($inX=0; $inX<$inTotalDados; $inX++) {
                if ($arListaCreditoSessao[$inX]['stExercicioLista'].$arListaCreditoSessao[$inX]['stCodCredito'] != $_GET['stExercicioLista'].$_GET['inIndice1']) {
                    $arListaCreditoTMP[] = $arListaCreditoSessao[$inX];
                }
            }

            Sessao::write('arLista', $arListaCreditoTMP);

            $rsListaCredito = new RecordSet;
            $rsListaCredito->preenche($arListaCreditoTMP);

            $stJs = montaListaCredito($rsListaCredito);
            sistemaLegado::executaFrameOculto($stJs);
        }
        break;

    case "ExcluirGrupoCredito":
        if ($_REQUEST["inIndice1"]) {
            $arListaGrupoCreditoSessao = Sessao::read( "arLista" );
            $arListaGrupoCreditoTMP = array();
            $inTotalDados = count( $arListaGrupoCreditoSessao );
            for ($inX=0; $inX<$inTotalDados; $inX++) {
                if ($arListaGrupoCreditoSessao[$inX]["stCodGrupo"] != $_GET["inIndice1"]) {
                    $arListaGrupoCreditoTMP[] = $arListaGrupoCreditoSessao[$inX];
                }
            }

            Sessao::write('arLista', $arListaGrupoCreditoTMP );

            $rsListaGrupoCredito = new RecordSet;
            $rsListaGrupoCredito->preenche( $arListaGrupoCreditoTMP );

            $stJs = montaListaGrupoCredito( $rsListaGrupoCredito );
            sistemaLegado::executaFrameOculto( $stJs );
        }
        break;

    case 'limparCredito':
        $stJs .= "jq('#inCodCredito').val('');";
        $stJs .= "jq('#stCredito').html('');";
        $stJs .= "jq('#stExercicioLista').val('');";

        echo $stJs;

        break;

    case 'limparGrupoCredito':
        $stJs .= "jq('#inCodGrupo').val('');";
        $stJs .= "jq('#stGrupo').html('');";

        echo $stJs;

        break;

    case 'IncluirCredito':
        if ($_GET['inCodCredito']) {
            $arListaSessao = Sessao::read('arLista');
            $boIncluir = true;
            $inTotalDados = count($arListaSessao);
            for ($inX=0; $inX < $inTotalDados; $inX++) {
                if ($arListaSessao[$inX]['stExercicioLista'].$arListaSessao[$inX]['stCodCredito'] == $_GET['stExercicioLista'].$_GET['inCodCredito']) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $obRMONCredito = new RMONCredito;
                $arDados = explode('.', $_GET['inCodCredito']);
                $obRMONCredito->setCodCredito($arDados[0]);
                $obRMONCredito->setCodEspecie($arDados[1]);
                $obRMONCredito->setCodGenero($arDados[2]);
                $obRMONCredito->setCodNatureza($arDados[3]);
                $obRMONCredito->listarCreditos($rsCreditos);

                $arListaSessao[$inTotalDados]['stCodCredito'] = $_GET['inCodCredito'];
                $arListaSessao[$inTotalDados]['stExercicioLista']  = $_GET['stExercicioLista'];
                $arListaSessao[$inTotalDados]['stCreditoDescricao'] = $rsCreditos->getCampo('descricao_credito');

                Sessao::write('arLista', $arListaSessao);

                $rsListaCredito = new RecordSet;
                $rsListaCredito->preenche($arListaSessao);

                $stJs = montaListaCredito($rsListaCredito);
                $stJs .= "f.inCodCredito.value = '';";
                $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;'";
            } else {
                $stJs = "alertaAviso('@Crédito já está na lista. (".$_GET['stExercicioLista'].' / '.$_GET["inCodCredito"].")','form','erro','".Sessao::getId()."');";
                $stJs .= "f.inCodCredito.value = '';";
                $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;'";
            }

            echo $stJs;
        }

        break;

    case "IncluirGrupoCredito":
        if ($_GET["inCodGrupo"]) {
            $arListaSessao = Sessao::read('arLista');
            $boIncluir = true;
            $inTotalDados = count($arListaSessao);
            for ($inX=0; $inX<$inTotalDados; $inX++) {
                if ($arListaSessao[$inX]["stCodGrupo"] == $_GET["inCodGrupo"]) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $arDados = explode('/', $_GET['inCodGrupo']);
                $obRARRGrupo = new RARRGrupo;
                $obRARRGrupo->setCodGrupo( $arDados[0] );
                $obRARRGrupo->setExercicio( $arDados[1] );
                $obRARRGrupo->consultarGrupo();
                $arListaSessao[$inTotalDados]['stCodGrupo'] = $_GET['inCodGrupo'];
                $arListaSessao[$inTotalDados]['stGrupoDescricao'] = $obRARRGrupo->getDescricao();

                Sessao::write('arLista', $arListaSessao);

                $rsListaGrupoCredito = new RecordSet;
                $rsListaGrupoCredito->preenche($arListaSessao);

                $stJs = montaListaGrupoCredito($rsListaGrupoCredito);
                $stJs .= "f.inCodGrupo.value = '';";
                $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;'";
            } else {
                $stJs = "alertaAviso('@Grupo de crédito já está na lista. (".$_GET["inCodGrupo"].")','form','erro','".Sessao::getId()."');";
                $stJs .= "f.inCodGrupo.value = '';";
                $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;'";
            }

            echo $stJs;
        }
        break;

    case 'montaCredito':
        $obIPopUpCredito = new IPopUpCredito;
        $obIPopUpCredito->setRotulo('Crédito');
        $obIPopUpCredito->setTitle ('Informe o código de crédito.');
        $obIPopUpCredito->setNull  (true);

        $obExercicio = new Exercicio;
        $obExercicio->setRotulo('Exercicio');
        $obExercicio->setName  ('stExercicioLista');
        $obExercicio->setId    ('stExercicioLista');

        $obBtnIncluirCredito = new Button;
        $obBtnIncluirCredito->setName             ('btnIncluirCredito');
        $obBtnIncluirCredito->setValue            ('Incluir');
        $obBtnIncluirCredito->setTipo             ('button');
        $obBtnIncluirCredito->setDisabled         (false);
        $obBtnIncluirCredito->obEvento->setOnClick("montaParametrosGET('IncluirCredito');");

        $obBtnLimparCredito = new Button;
        $obBtnLimparCredito->setName             ('btnLimparCredito');
        $obBtnLimparCredito->setValue            ('Limpar');
        $obBtnLimparCredito->setTipo             ('button');
        $obBtnLimparCredito->setDisabled         (false );
        $obBtnLimparCredito->obEvento->setOnClick("montaParametrosGET('limparCredito');");

        $botoesCredito = array ($obBtnIncluirCredito, $obBtnLimparCredito);

        $obFormulario = new Formulario;
        $obFormulario->addTitulo('Crédito');
        $obIPopUpCredito->geraFormulario($obFormulario, true, true);
        $obFormulario->addComponente($obExercicio);
        $obFormulario->defineBarra($botoesCredito, 'left', '');

        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $stJs  = "d.getElementById('spnCredito').innerHTML = '".$stHtml."';";
        $stJs .= "d.getElementById('spnLista').innerHTML = '';";
        Sessao::write('arLista', array());

        echo $stJs;
        break;

    case 'montaGrupoCredito':
        $obIPopUpGrupoCredito = new MontaGrupoCredito;
        $obIPopUpGrupoCredito->setRotulo('Grupo de Crédito');
        $obIPopUpGrupoCredito->setTitulo('Grupos de créditos alvo da remissão.');

        $obBtnIncluirGrupoCredito = new Button;
        $obBtnIncluirGrupoCredito->setName             ('btnIncluirGrupoCredito');
        $obBtnIncluirGrupoCredito->setValue            ('Incluir');
        $obBtnIncluirGrupoCredito->setTipo             ('button');
        $obBtnIncluirGrupoCredito->setDisabled         (false);
        $obBtnIncluirGrupoCredito->obEvento->setOnClick("montaParametrosGET('IncluirGrupoCredito', 'inCodGrupo', true);");

        $obBtnLimparGrupoCredito = new Button;
        $obBtnLimparGrupoCredito->setName             ('btnLimparGrupoCredito');
        $obBtnLimparGrupoCredito->setValue            ('Limpar');
        $obBtnLimparGrupoCredito->setTipo             ('button');
        $obBtnLimparGrupoCredito->setDisabled         (false);
        $obBtnLimparGrupoCredito->obEvento->setOnClick("montaParametrosGET('limparGrupoCredito');");

        $botoesGrupoCredito = array ($obBtnIncluirGrupoCredito, $obBtnLimparGrupoCredito);

        $obFormulario = new Formulario;
        $obFormulario->addTitulo('Grupos de Crédito');
        $obIPopUpGrupoCredito->geraFormulario($obFormulario, true, true);
        $obFormulario->defineBarra($botoesGrupoCredito, 'left', '');

        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $stJs  = "d.getElementById('spnCredito').innerHTML = '".$stHtml."';";
        $stJs .= "d.getElementById('spnLista').innerHTML = '';";
        Sessao::write('arLista', array());

        echo $stJs;
        break;

}
