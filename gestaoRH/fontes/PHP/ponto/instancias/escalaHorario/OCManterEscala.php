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
    * Página de Oculto para Manter Escala
    * Data de Criação: 02/10/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

    * Casos de uso: uc-04.10.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscala.class.php"                                    );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscalaTurno.class.php"                               );

$stAcao = $request->get('stAcao');
$stCtrl = $_REQUEST['stCtrl'];

$stPrograma = "ManterEscala";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

function submeter()
{
    global $stAcao;
    $obErro = new Erro();
    $arTurnos = ( is_array(Sessao::read('arTurnos')) ) ? Sessao::read('arTurnos') : array();

    if (sizeof($arTurnos) == 0) {
        $obErro->setDescricao("Deve haver pelo menos um turno na lista de Turnos Cadastrados!");
    }

    if (!$obErro->ocorreu()) {
        $stFiltro = " AND descricao ilike '".$_REQUEST['stDescricao']."'";

        $obTPontoEscala = new TPontoEscala();
        $obTPontoEscala->recuperaEscalasAtivas($rsEscala, $stFiltro);

        if ($rsEscala->getNumLinhas() > 0) {
            if ($stAcao == 'incluir' || ($stAcao == 'alterar' && $rsEscala->getCampo('cod_escala') != $_REQUEST['inCodEscala'])) {
                $obErro->setDescricao("Já existe uma Escala no sistema com a mesma descrição (Escala ".
                                      $rsEscala->getCampo('cod_escala')." - ".$rsEscala->getCampo('descricao').")");
            }
        }
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    } else {
        $stJs .= "f.submit(); //BloqueiaFrames(true,false);\n";
    }

    return $stJs;
}

function preencherTurnos($inCodEscala, $boBloqueiaEdicao)
{
    $rsDiaria = new RecordSet();
    $stFiltroEscalaTurno = " AND escala_turno.cod_escala = ".$inCodEscala;
    $obTPontoEscalaTurno = new TPontoEscalaTurno();
    $obTPontoEscalaTurno->recuperaTurnosAtivos($rsEscalaTurno, $stFiltroEscalaTurno, " ORDER BY escala_turno.dt_turno ASC ");

    $arSessaoTurno = array();
    if ($rsEscalaTurno->getNumLinhas() > 0) {
        $inCountTurno=1;
        while (!$rsEscalaTurno->eof()) {

            $arTurno                             = array();
            $arTurno['inId']                     = $inCountTurno;
            $arTurno['dtTurno']                  = $rsEscalaTurno->getCampo('dt_turno');
            $arTurno['stHoraEntrada1']           = substr($rsEscalaTurno->getCampo('hora_entrada_1'), 0, 5);
            $arTurno['stHoraEntrada2']           = substr($rsEscalaTurno->getCampo('hora_entrada_2'), 0, 5);
            $arTurno['stHoraSaida1']             = substr($rsEscalaTurno->getCampo('hora_saida_1'), 0, 5);
            $arTurno['stHoraSaida2']             = substr($rsEscalaTurno->getCampo('hora_saida_2'), 0, 5);
            $arTurno['stTipoDia']                = $rsEscalaTurno->getCampo('tipo');
            $arTurno['stDescTipoDia']            = ($rsEscalaTurno->getCampo('tipo')=='T')?"Trabalho":"Folga";

            $stOrdenacao = explode("/",$rsEscalaTurno->getCampo('dt_turno'));
            $arTurno['stOrdenacao']              = $stOrdenacao[2].$stOrdenacao[1].$stOrdenacao[0];

            $arSessaoTurno[] = $arTurno;

            $rsEscalaTurno->proximo();
            $inCountTurno++;
        }//
    }//
    Sessao::write('arTurnos', $arSessaoTurno);
    $stJs .= montaListaTurnos($arSessaoTurno, false, $boBloqueiaEdicao);

    return $stJs;
}

function montaSpanIncluirAlterarTurno()
{
    $arTurnos = ( is_array(Sessao::read('arTurnos')) ) ? Sessao::read('arTurnos') : array();
    $arComponentes = array();

    if (sizeof($arTurnos)>0) {
        $arDtUltimoTurno        = explode("/", $arTurnos[sizeof($arTurnos)-1]['dtTurno']);
        $inTimestampUltimoTurno = mktime(0, 0, 0, $arDtUltimoTurno[1], $arDtUltimoTurno[0], $arDtUltimoTurno[2]) + 86400;//1 dia
        $dtInicio = $dtFim = date('d/m/Y', $inTimestampUltimoTurno);
    } else {
        $dtInicio = $dtFim = date("d/m/Y");
    }

    $obDtInicio = new Data();
    $obDtInicio->setRotulo("Início");
    $obDtInicio->setTitle("Informe a data de início da escala");
    $obDtInicio->setName('dtInicio');
    $obDtInicio->setId('dtInicio');
    $obDtInicio->setNull(false);
    $obDtInicio->setNullBarra(false);
    $obDtInicio->setValue($dtInicio);
    $arComponentes[] = $obDtInicio;

    $obDtFim = new Data();
    $obDtFim->setRotulo("Fim");
    $obDtFim->setTitle("Informe a data final da escala. Para incluir um dia na escala, a data início e fim devem permacener iguais");
    $obDtFim->setName('dtFim');
    $obDtFim->setId('dtFim');
    $obDtFim->setNull(false);
    $obDtFim->setNullBarra(false);
    $obDtFim->setValue($dtFim);
    $arComponentes[] = $obDtFim;

    $obHoraEntrada1 = new Hora();
    $obHoraEntrada1->setRotulo("Hora Entrada1");
    $obHoraEntrada1->setTitle("Informe a hora da primeira entrada do dia");
    $obHoraEntrada1->setName("stHoraEntrada1");
    $obHoraEntrada1->setId("stHoraEntrada1");
    $obHoraEntrada1->setNull(false);
    $obHoraEntrada1->setNullBarra(false);
    $arComponentes[] = $obHoraEntrada1;

    $obHoraSaida1 = new Hora();
    $obHoraSaida1->setRotulo("Hora Saida1");
    $obHoraSaida1->setTitle("Informe a hora da primeira saida do dia");
    $obHoraSaida1->setName("stHoraSaida1");
    $obHoraSaida1->setId("stHoraSaida1");
    $obHoraSaida1->setNull(false);
    $obHoraSaida1->setNullBarra(false);
    $arComponentes[] = $obHoraSaida1;

    $obHoraEntrada2 = new Hora();
    $obHoraEntrada2->setRotulo("Hora Entrada2");
    $obHoraEntrada2->setTitle("Informe a hora da segunda entrada do dia, caso exista");
    $obHoraEntrada2->setName("stHoraEntrada2");
    $obHoraEntrada2->setId("stHoraEntrada2");
    $obHoraEntrada2->setNullBarra(true);
    $arComponentes[] = $obHoraEntrada2;

    $obHoraSaida2 = new Hora();
    $obHoraSaida2->setRotulo("Hora Saida2");
    $obHoraSaida2->setTitle("Informe a hora da segunda saida do dia, caso exista");
    $obHoraSaida2->setName("stHoraSaida2");
    $obHoraSaida2->setId("stHoraSaida2");
    $obHoraSaida2->setNullBarra(true);
    $arComponentes[] = $obHoraSaida2;

    $obTipoDiaTrabalho = new Radio();
    $obTipoDiaTrabalho->setRotulo('Tipo de Dia');
    $obTipoDiaTrabalho->setTitle('Informe como deve considerar o(s) dia(s) na escala: como dia normal de trabalho ou folga');
    $obTipoDiaTrabalho->setName('stTipoDia');
    $obTipoDiaTrabalho->setId('stTipoDiaTrabalho');
    $obTipoDiaTrabalho->setValue('T');
    $obTipoDiaTrabalho->setLabel("Trabalho");
    $obTipoDiaTrabalho->setNull(false);
    $obTipoDiaTrabalho->setNullBarra(false);
    $obTipoDiaTrabalho->setChecked(true);
    $arComponentes[] = $obTipoDiaTrabalho;

    $obTipoDiaFolga = new Radio();
    $obTipoDiaFolga->setName('stTipoDia');
    $obTipoDiaFolga->setId('stTipoDiaFolga');
    $obTipoDiaFolga->setValue('F');
    $obTipoDiaFolga->setLabel('Folga');
    $arComponentes[] = $obTipoDiaFolga;

    $obHdnInId = new hidden();
    $obHdnInId->setName("inId");
    $obHdnInId->setId("inId");
    $arComponentes[] = $obHdnInId;

    $obFormulario = new Formulario;
    $obFormulario->addTitulo			( "Programação dos Turnos"      );
    $obFormulario->addHidden            ( $obHdnInId                    );
    $obFormulario->addComponente        ( $obDtInicio                   );
    $obFormulario->addComponente        ( $obDtFim                      );
    $obFormulario->addComponente        ( $obHoraEntrada1               );
    $obFormulario->addComponente        ( $obHoraSaida1                 );
    $obFormulario->addComponente        ( $obHoraEntrada2               );
    $obFormulario->addComponente        ( $obHoraSaida2                 );
    $obFormulario->agrupaComponentes    ( array($obTipoDiaTrabalho, $obTipoDiaFolga) );
    $obFormulario->IncluirAlterar       ( "Turno", $arComponentes, false );

    $stJs .= str_replace("\n","",$obFormulario->getInnerJavascriptBarra());

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stHtml = str_replace("\n","",$stHtml);

    $stJs .= "jQuery('#spnIncluirAlterarTurno').html('".$stHtml."');\n";

    return $stJs;
}

function montaListaTurnos($arTurnos, $boOrdernar = true, $boBloqueiaEdicao = false)
{
    global $pgOcul;
    $rsTurnos = new Recordset;
    $rsTurnos->preenche($arTurnos);

    if ($rsTurnos->getNumLinhas() > 0) {

        if ($boOrdernar) {
            $rsTurnos->ordena('stOrdenacao');
            $rsTurnos->setPrimeiroElemento();
            while (!$rsTurnos->eof()) {
                $rsTurnos->setCampo('inId', $rsTurnos->getCorrente());
                $rsTurnos->proximo();
            }
            $rsTurnos->setPrimeiroElemento();
            Sessao::write('arTurnos', $rsTurnos->getElementos());
        }

        $obLista = new Lista;
        $obLista->setTitulo("Turnos Cadastrados");
        $obLista->setRecordSet($rsTurnos);
        $obLista->setMostraPaginacao( false );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Data");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Entrada1");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Saida1");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Entrada2");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Saida2");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Tipo");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        if ($boBloqueiaEdicao == false) {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 2 );
            $obLista->commitCabecalho();
        }

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "[dtTurno]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "[stHoraEntrada1]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "[stHoraSaida1]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "[stHoraEntrada2]&nbsp;" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "[stHoraSaida2]&nbsp;" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "[stDescTipoDia]" );
        $obLista->commitDado();

        if ($boBloqueiaEdicao == false) {
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "ALTERAR" );
            $obLista->ultimaAcao->setFuncaoAjax( true );
            $obLista->ultimaAcao->setLink( "JavaScript: executaFuncaoAjax('preencherAlteraTurno');");
            $obLista->ultimaAcao->addCampo("1","inId");
            $obLista->commitAcao();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "EXCLUIR" );
            $obLista->ultimaAcao->setFuncaoAjax( true );
            $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirTurno');");
            $obLista->ultimaAcao->addCampo("1","inId");
            $obLista->commitAcao();
        }

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);

        $stJs .= "jQuery('#boProjetarTurnos').attr('disabled', false);\n";
    } else {
        $stJs .= "jQuery('#boProjetarTurnos').attr('disabled', true);\n";
    }

    $stJs .= "jQuery('#inValidaTurnos').val(".$rsTurnos->getNumLinhas().");\n";
    $stJs .= "jQuery('#spnTurnos').html('".$stHtml."');\n";

    return $stJs;
}

function validaTurno()
{
    $arTurnos = ( is_array(Sessao::read('arTurnos')) ) ? Sessao::read('arTurnos') : array();

    $obErro = new erro;

    if ($_REQUEST['dtInicio'] != $_REQUEST['dtFim'] && !SistemaLegado::comparaDatas($_REQUEST['dtFim'],$_REQUEST['dtInicio']) ) {
        $obErro->setDescricao("O campo Fim, deve ser maior ou igual ao campo Início");
    }

    if (!$obErro->ocorreu()) {
        $stHoraEntrada1 = str_replace(":", "", $_REQUEST['stHoraEntrada1'])*1;
        $stHoraEntrada2 = str_replace(":", "", $_REQUEST['stHoraEntrada2'])*1;
        $stHoraSaida1   = str_replace(":", "", $_REQUEST['stHoraSaida1'])*1;
        $stHoraSaida2   = str_replace(":", "", $_REQUEST['stHoraSaida2'])*1;
        
        if ($stHoraEntrada1 > 2359) {
            $obErro->setDescricao("O campo Hora Entrada1, deve estar entre 00:00 e 23:59.");
        } elseif ($stHoraSaida1 > 2359) {
            $obErro->setDescricao("O campo Hora Saída1, deve estar entre 00:00 e 23:59.");
        }

        if (!$obErro->ocorreu()) {
            if ($stHoraEntrada2 != "") {
                if ($stHoraEntrada2 > 2359) {
                    $obErro->setDescricao("O campo Hora Entrada2, deve estar entre 00:00 e 23:59.");
                } elseif ($stHoraEntrada2 <= $stHoraSaida1) {
                    $obErro->setDescricao("O campo Hora Entrada2, deve ser maior que o campo Hora Saída1");
                } elseif ($stHoraSaida2 != "" && $stHoraSaida2 > 2359) {
                    $obErro->setDescricao("O campo Hora Saída2, deve estar entre 00:00 e 23:59.");
                }
            }
        }
    }

    if (!$obErro->ocorreu()) {
        foreach ($arTurnos as $arTurno) {
            if ($_REQUEST['dtInicio'] == $arTurno['dtTurno'] && $_REQUEST['inId'] != $arTurno['inId']) {
                $obErro->setDescricao('Campo Início possui data de turno já cadastrado ('.$arTurno['dtTurno'].')');
                break;
            } elseif ($_REQUEST['dtFim'] == $arTurno['dtTurno'] && $_REQUEST['inId'] != $arTurno['inId']) {
                $obErro->setDescricao('Campo Fim possui data de turno já cadastrado ('.$arTurno['dtTurno'].')');
                break;
            } elseif ( dataContida($_REQUEST['dtInicio'], $_REQUEST['dtFim'], $arTurno['dtTurno']) && $_REQUEST['inId'] != $arTurno['inId']) {
                $obErro->setDescricao('O período selecionado nos campos Início e Fim, conflita com data de turno já cadastrado ('.$arTurno['dtTurno'].')');
                break;
            }
        }
    }

    return $obErro;
}

function incluirTurno($boValidar = true)
{
    $arTurnos = ( is_array(Sessao::read('arTurnos')) ) ? Sessao::read('arTurnos') : array();

    $obErro = new Erro();
    if($boValidar)
        $obErro = validaTurno();

    if ( !$obErro->ocorreu() ) {

        $inSegundosDia = 86400;

        $arInicio          = explode("/", $_REQUEST['dtInicio']);
        $inTimestampInicio = mktime(0, 0, 0, $arInicio[1], $arInicio[0], $arInicio[2]);
        $inTimestampTurno  = $inTimestampInicio;

        $arFim          = explode("/", $_REQUEST['dtFim']);
        $inTimestampFim = mktime(0, 0, 0, $arFim[1], $arFim[0], $arFim[2]);

        $inNumeroTurnosIncluir = ceil(($inTimestampFim - $inTimestampInicio) / $inSegundosDia);

        for ($inCountTurnos = 0; $inCountTurnos <= $inNumeroTurnosIncluir; $inCountTurnos++) {
            $arTurno                             = array();
            //$arTurno['inId']                     = count($arTurnos)+$inCountTurnos+1;
            $arTurno['dtTurno']                  = date('d/m/Y', $inTimestampTurno);
            $arTurno['stHoraEntrada1']           = $_REQUEST['stHoraEntrada1'];
            $arTurno['stHoraEntrada2']           = $_REQUEST['stHoraEntrada2'];
            $arTurno['stHoraSaida1']             = $_REQUEST['stHoraSaida1'];
            $arTurno['stTipoDia']                = $_REQUEST['stTipoDia'];
            $arTurno['stDescTipoDia']            = ($_REQUEST['stTipoDia']=='T')?"Trabalho":"Folga";

            if($arTurno['stHoraEntrada2'] != "")
                $arTurno['stHoraSaida2']         = $_REQUEST['stHoraSaida2'];

            $arOrdenacao = explode("/",$arTurno['dtTurno']);
            $arTurno['stOrdenacao']              = $arOrdenacao[2].$arOrdenacao[1].$arOrdenacao[0];
            $arTurnos[]                          = $arTurno;

            $inTimestampTurno += $inSegundosDia;
        }//

        $stJs .= montaListaTurnos($arTurnos);
        $stJs .= montaSpanIncluirAlterarTurno();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function alterarTurno()
{
    $obErro = validaTurno();

    if ( !$obErro->ocorreu() ) {
        $arTurnos = ( is_array(Sessao::read('arTurnos')) ? Sessao::read('arTurnos') : array());
        $arSessaoTurnos = array();
        foreach ($arTurnos as $arTurno) {
            if ($arTurno['inId'] != $_REQUEST['inId']) {
                $arSessaoTurnos[] = $arTurno;
            }
        }
        Sessao::write('arTurnos', $arSessaoTurnos);
        $stJs .= incluirTurno(false);
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function incluirProjecao()
{
    $obErro = validaProjecao();

    if (!$obErro->ocorreu()) {
        $arTurnos = ( is_array(Sessao::read('arTurnos')) ) ? Sessao::read('arTurnos') : array();

        $arPrimeiroTurno           = explode("/", $arTurnos[0]['dtTurno']);
        $inTimestampPrimeiroTurno  = mktime(0, 0, 0, $arPrimeiroTurno[1], $arPrimeiroTurno[0], $arPrimeiroTurno[2]);

        $arUltimoTurno             = explode("/", $arTurnos[sizeof($arTurnos)-1]['dtTurno']);
        $inTimestampUltimoTurno    = mktime(0, 0, 0, $arUltimoTurno[1], $arUltimoTurno[0], $arUltimoTurno[2]);

        $arInicioProjecao          = explode("/", $_REQUEST['dtInicioProjecao']);
        $inTimestampInicioProjecao = mktime(0, 0, 0, $arInicioProjecao[1], $arInicioProjecao[0], $arInicioProjecao[2]);

        $inDiferencaTurnos = $inDiferencaTurnosInicial = $inTimestampInicioProjecao - $inTimestampPrimeiroTurno;

        $inDiferencaPrimeiroUltimoTurno = $inTimestampUltimoTurno-$inTimestampPrimeiroTurno;

        $arTurnosProjecao = array();

        $inCountPeriodos = 1;
        do {
            foreach ($arTurnos as $arTurno) {
                $arDtTurno        = explode("/", $arTurno['dtTurno']);
                $inTimestampTurno = mktime(0, 0, 0, $arDtTurno[1], $arDtTurno[0], $arDtTurno[2]);
                $dtTurnoProjecao  = date('d/m/Y', $inTimestampTurno+$inDiferencaTurnos);

                if (SistemaLegado::comparaDatas($dtTurnoProjecao,$_REQUEST['dtFimProjecao'])) {
                    break 1;
                }

                $arTurnoProjecao = $arTurno;
                $arTurnoProjecao['dtTurno'] = $dtTurnoProjecao;

                $arOrdenacao = explode("/",$arTurnoProjecao['dtTurno']);
                $arTurnoProjecao['stOrdenacao'] = $arOrdenacao[2].$arOrdenacao[1].$arOrdenacao[0];

                $arTurnosProjecao[] = $arTurnoProjecao;
            }

            $inCountPeriodos += 1;
            $inDiferencaTurnos = $inDiferencaTurnosInicial*$inCountPeriodos;

        } while (!SistemaLegado::comparaDatas($dtTurnoProjecao,$_REQUEST['dtFimProjecao']));

        $arTurnos = array_merge($arTurnos, $arTurnosProjecao);

        Sessao::write('arTurnos', $arTurnos);
        $stJs .= montaSpanProjetarTurnosDatas('false');
        $stJs .= montaListaTurnos($arTurnos);
        $stJs .= montaSpanIncluirAlterarTurno();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function validaProjecao()
{
    $obErro = new erro;
    $arTurnos = ( is_array(Sessao::read('arTurnos')) ) ? Sessao::read('arTurnos') : array();
    $arUltimoTurno = $arTurnos[sizeof($arTurnos)-1];

    if ( !SistemaLegado::comparaDatas($_REQUEST['dtInicioProjecao'],$arUltimoTurno['dtTurno']) ) {
        $obErro->setDescricao("O campo Início da Projeção, deve ser maior que a data do último turno da lista");
    } elseif ( !SistemaLegado::comparaDatas($_REQUEST['dtFimProjecao'],$_REQUEST['dtInicioProjecao']) ) {
        $obErro->setDescricao("O campo Fim da Projeção, deve ser maior ao Início da Projeção");
    }

    if (!$obErro->ocorreu()) {
        foreach ($arTurnos as $arTurno) {
            if ($_REQUEST['dtInicioProjecao'] == $arTurno['dtTurno']) {
                $obErro->setDescricao('Campo Início da Projeção possui data de turno já cadastrado ('.$arTurno['dtTurno'].')');
                break;
            } elseif ($_REQUEST['dtFimProjecao'] == $arTurno['dtTurno']) {
                $obErro->setDescricao('Campo Fim da Projeção possui data de turno já cadastrado ('.$arTurno['dtTurno'].')');
                break;
            } elseif ( dataContida($_REQUEST['dtInicioProjecao'], $_REQUEST['dtFimProjecao'], $arTurno['dtTurno']) ) {
                $obErro->setDescricao('O período selecionado nos campos Início da Projeção e Fim da Projeção, conflita com data de turno já cadastrado ('.$arTurno['dtTurno'].')');
                break;
            }
        }
    }

    return $obErro;
}

function preencherAlteraTurno()
{
    $arSessaoTurnos = Sessao::read('arTurnos');

    if (is_array($arSessaoTurnos)) {
        foreach ($arSessaoTurnos as $arTurno) {

            if ($arTurno['inId'] == $_REQUEST['inId']) {

                $stJs .= montaSpanIncluirAlterarTurno();

                $stJs .= "limpaFormularioTurno();";

                $stJs .= "jQuery('#inId').val('".$arTurno['inId']."');";

                $stJs .= "jQuery('#dtInicio').val('".$arTurno['dtTurno']."');";
                $stJs .= "jQuery('#dtFim').val('".$arTurno['dtTurno']."');";

                $stJs .= "jQuery('#stHoraEntrada1').val('".$arTurno['stHoraEntrada1']."');";
                $stJs .= "jQuery('#stHoraEntrada2').val('".$arTurno['stHoraEntrada2']."');";

                $stJs .= "jQuery('#stHoraSaida1').val('".$arTurno['stHoraSaida1']."');";
                $stJs .= "jQuery('#stHoraSaida2').val('".$arTurno['stHoraSaida2']."');";

                if ($arTurno['stTipoDia'] == 'T') {
                    $stJs .= "jQuery('#stTipoDiaTrabalho').attr('checked', true);";
                } else {
                    $stJs .= "jQuery('#stTipoDiaFolga').attr('checked', true);";
                }

                $stJs .= "jQuery('#btAlterarTurno').attr('disabled', false);";
                $stJs .= "jQuery('#btIncluirTurno').attr('disabled', true);";

                break;
            }
        }//

        return $stJs;
    }
}

function excluirTurno()
{
    $obErro = new Erro();
    $arTurnos = ( is_array(Sessao::read('arTurnos')) ? Sessao::read('arTurnos') : array());
    $arSessaoTurnos = array();
    foreach ($arTurnos as $arTurno) {
        if ($arTurno['inId'] != $_REQUEST['inId']) {
            $arSessaoTurnos[] = $arTurno;
        }
    }

    if (!$obErro->ocorreu()) {
        Sessao::write('arTurnos', $arSessaoTurnos);
        $stJs .= montaSpanIncluirAlterarTurno();
        $stJs .= montaListaTurnos($arSessaoTurnos);
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function montaSpanProjetarTurnosDatas($boProjetarTurnos)
{
    if ($boProjetarTurnos == 'true') {
        $arTurnos         = ( is_array(Sessao::read('arTurnos')) ? Sessao::read('arTurnos') : array());

        if (sizeof($arTurnos) > 0) {
            $dtUltimoTurno    = $arTurnos[sizeof($arTurnos)-1]['dtTurno'];
            $arUltimoTurno          = explode("/", $dtUltimoTurno);
            $inTimestampUltimoTurno = mktime(0, 0, 0, $arUltimoTurno[1], $arUltimoTurno[0], $arUltimoTurno[2]);
            $dtUltimoTurno          = date('d/m/Y', $inTimestampUltimoTurno+86400);
        }

        $obDtInicioProjecao = new Data();
        $obDtInicioProjecao->setRotulo("Início da Projeção");
        $obDtInicioProjecao->setTitle("Informe a data inicial da projeção (deve ser posterior à última cadastrada nos turnos)");
        $obDtInicioProjecao->setName('dtInicioProjecao');
        $obDtInicioProjecao->setId('dtInicioProjecao');
        $obDtInicioProjecao->setNull(false);
        $obDtInicioProjecao->setNullBarra(false);
        $obDtInicioProjecao->setValue($dtUltimoTurno);

        $obDtFimProjecao = new Data();
        $obDtFimProjecao->setRotulo("Fim da Projeção");
        $obDtFimProjecao->setTitle("Informe a data final da projeção");
        $obDtFimProjecao->setName('dtFimProjecao');
        $obDtFimProjecao->setId('dtFimProjecao');
        $obDtFimProjecao->setNull(false);
        $obDtFimProjecao->setNullBarra(false);
        $obDtFimProjecao->setValue($dtUltimoTurno);

        $obFormulario = new Formulario;
        $obFormulario->addComponente( $obDtInicioProjecao );
        $obFormulario->addComponente( $obDtFimProjecao );
        $obFormulario->Incluir("Projecao", array($obDtInicioProjecao,$obDtFimProjecao), false);

        $stJs .= str_replace("\n","",$obFormulario->getInnerJavascriptBarra());

        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stJs .= "jQuery('#spnProjetarTurnosDatas').html('".$stHtml."');\n";
    } else {
        $stJs .= montaSpanIncluirAlterarTurno();
        $stJs .= "jQuery('#boProjetarTurnos').attr('checked', false);\n";
        $stJs .= "jQuery('#spnProjetarTurnosDatas').html('');\n";
    }

    return $stJs;
}

function limparFormulario()
{
    Sessao::write('arTurnos', array());
    $stJs .= montaSpanIncluirAlterarTurno();
    $stJs .= montaListaTurnos(array());

    return $stJs;
}

function dataContida($dtInicioPeriodo, $dtFimPeriodo, $dtComparacao)
{
    if (SistemaLegado::comparaDatas($dtComparacao, $dtInicioPeriodo) && SistemaLegado::comparaDatas($dtFimPeriodo, $dtComparacao)) {
        return true;
    }

    return false;
}

switch ($stCtrl) {
    case "incluirProjecao":
        $stJs = incluirProjecao();
        break;
    case "preencherTurnos":
        $stJs = preencherTurnos($_REQUEST['inCodEscala'], $_REQUEST['boBloqueiaEdicao']);
        break;
    case "incluirTurno":
        $stJs = incluirTurno();
        break;
    case "alterarTurno":
        $stJs = alterarTurno();
        break;
    case "excluirTurno":
        $stJs = excluirTurno();
        break;
    case "montaSpanProjetarTurnosDatas":
        $stJs = montaSpanProjetarTurnosDatas($_REQUEST['boProjetarTurnos']);
        break;
    case "montaSpanIncluirAlterarTurno":
        $stJs = montaSpanIncluirAlterarTurno();
        break;
    case "preencherAlteraTurno":
        $stJs = preencherAlteraTurno();
        break;
    case "submeter":
        $stJs = submeter();
        break;
    case "limparFormulario":
        $stJs = limparFormulario();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
