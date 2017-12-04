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
    * Página de Oculto para Manter Vinculo de Escala
    * Data de Criação: 10/10/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscala.class.php"                                    );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscalaTurno.class.php"                               );

$stAcao = $request->get('stAcao');
$stCtrl = $_REQUEST['stCtrl'];

$stPrograma = "ManterVinculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//Proveniente de OCManterEscala.php
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

//Proveniente de OCManterEscala.php
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

    }

    $stJs .= "jQuery('#spnTurnos').html('".$stHtml."');\n";

    return $stJs;
}

switch ($stCtrl) {
    case "preencherTurnos":
        $stJs = preencherTurnos($_REQUEST['inCodEscala'], true);
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
