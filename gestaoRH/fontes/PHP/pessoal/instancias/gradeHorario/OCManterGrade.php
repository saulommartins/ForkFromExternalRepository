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
* Definir grande de horário
* Data de Criação: 12/09/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30860 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.04.41
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalDiasTurno.class.php"                                 );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalFaixaTurno.class.php"                                );

$stPrograma = "ManterGrade";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

function comparaHorarios($stHora1,$stHora2)
{
    $arHora1    = explode(":",$stHora1);
    $inHora1    = $arHora1[0];
    $inMinuto1  = $arHora1[1];
    $inHora1   .= $inMinuto1 ;
    $arHora2    = explode(":",$stHora2);
    $inHora2    = $arHora2[0];
    $inMinuto2  = $arHora2[1];
    $inHora2   .= $inMinuto2;
    if ($inHora2 <= $inHora1) {
        return true;
    } else {
        return false;
    }
}

function validaHorario($stHorario)
{
    $boRetorno = true;

    list($stHora, $stMinutos) = explode(":", $stHorario);

    if ((int) $stHora > 24 || (int) $stMinutos>59) {
        $boRetorno = false;
    }

    return $boRetorno;
}

function incluirGrade($boExecuta=false)
{
    $obErro = new erro;
    $arGrade = Sessao::read('Grade');
    $arDiasSelecionados = montaDiasSelecionados();

    if (count($arDiasSelecionados)==0) {
        $obErro->setDescricao("Campo Dia Semana deve ser informado!");
    }

    if (!validaHorario($_GET['stHoraEntrada1'])) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Entrada1 é uma hora inválida!");
    }

    if (!validaHorario($_GET['stHoraSaida1'])) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Saída1 é uma hora inválida!");
    }

    if ( comparaHorarios($_GET['stHoraEntrada1'],$_GET['stHoraSaida1']) ) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Entrada1 deve ser inferior a Hora Saída1!");
    }

    if (trim($_GET['stHoraEntrada2']) != "" || trim($_GET['stHoraSaida2']) != "") {
        if (!validaHorario($_GET['stHoraEntrada2'])) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Entrada2 é uma hora inválida!");
        }

        if (!validaHorario($_GET['stHoraSaida2'])) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Saída2 é uma hora inválida!");
        }

        if ( comparaHorarios($_GET['stHoraEntrada2'],$_GET['stHoraSaida2']) ) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Entrada2 deve ser inferior a Hora Saída2!");
        }

        if ( comparaHorarios($_GET['stHoraSaida1'],$_GET['stHoraEntrada2']) ) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Saída1 deve ser inferior a Hora Entrada2!");
        }
    }

    if (count($arGrade)>0) {
        foreach ($arDiasSelecionados as $inCodDia) {
            foreach ($arGrade as $chave => $valor) {
                if ($valor["inDiaSemana"] == $inCodDia) {

                    $stFiltro = " WHERE cod_dia = ".$inCodDia;
                    $obTPessoalDiasTurno = new TPessoalDiasTurno();
                    $obTPessoalDiasTurno->recuperaTodos($rsDiasTurno, $stFiltro);

                    $obErro->setDescricao($obErro->getDescricao()."@O dia informado já esta na lista de turnos(".trim($rsDiasTurno->getCampo("nom_dia")).")!");
                }
            } // Foreach dos elementos da Lista
        } // Foreach dos dias selecionados
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        if (count($arDiasSelecionados)>0) {
            foreach ($arDiasSelecionados as $inCodDia) {
                $stFiltro = " WHERE cod_dia = ".$inCodDia;
                $obTPessoalDiasTurno = new TPessoalDiasTurno();
                $obTPessoalDiasTurno->recuperaTodos($rsDiasTurno, $stFiltro);

                $arElementos['inId']           = count($arGrade) + 1;
                $arElementos['stNomDia']       = trim($rsDiasTurno->getCampo('nom_dia'));
                $arElementos['inDiaSemana']    = $inCodDia;
                $arElementos['stHoraEntrada1'] = $_GET['stHoraEntrada1'];
                $arElementos['stHoraSaida1']   = $_GET['stHoraSaida1'  ];
                $arElementos['stHoraEntrada2'] = $_GET['stHoraEntrada2'];
                $arElementos['stHoraSaida2']   = $_GET['stHoraSaida2'  ];
                $arGrade[]                     = $arElementos;
            }
        }
        Sessao::write('Grade', $arGrade);
        $stJs .= listarGrade( $arGrade );
        $stJs .= " limpaFormularioGrade(); ";
    }

    return $stJs;
}

function alterarGrade()
{
    $obErro = new erro;
    $arGrade = Sessao::read('Grade');
    $arDiasSelecionados = montaDiasSelecionados();

    if (count($arDiasSelecionados)==0) {
        $obErro->setDescricao("Campo Dia Semana deve ser informado!");
    }

    if (!validaHorario($_GET['stHoraEntrada1'])) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Entrada1 é uma hora inválida!");
    }

    if (!validaHorario($_GET['stHoraSaida1'])) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Saída1 é uma hora inválida!");
    }

    if ( comparaHorarios($_GET['stHoraEntrada1'],$_GET['stHoraSaida1']) ) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Entrada1 deve ser inferior a Hora Saída1!");
    }

    if (trim($_GET['stHoraEntrada2']) != "" || trim($_GET['stHoraSaida2']) != "") {
        if (!validaHorario($_GET['stHoraEntrada2'])) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Entrada2 é uma hora inválida!");
        }

        if (!validaHorario($_GET['stHoraSaida2'])) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Saída2 é uma hora inválida!");
        }

        if ( comparaHorarios($_GET['stHoraEntrada2'],$_GET['stHoraSaida2']) ) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Entrada2 deve ser inferior a Hora Saída2!");
        }

        if ( comparaHorarios($_GET['stHoraSaida1'],$_GET['stHoraEntrada2']) ) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Hora Saída1 deve ser inferior a Hora Entrada2!");
        }
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        if (count($arDiasSelecionados)>0) {
            foreach ($arDiasSelecionados as $inCodDia) {
                foreach ($arGrade as $chave => $dadosGrade) {
                    if ($inCodDia == $dadosGrade["inDiaSemana"]) {
                        $arGrade[$chave]['stHoraEntrada1'] = $_GET['stHoraEntrada1'];
                        $arGrade[$chave]['stHoraSaida1']   = $_GET['stHoraSaida1'  ];
                        $arGrade[$chave]['stHoraEntrada2'] = $_GET['stHoraEntrada2'];
                        $arGrade[$chave]['stHoraSaida2']   = $_GET['stHoraSaida2'  ];
                    }
                }
            }
        }
        Sessao::write('Grade', $arGrade);
        $stJs .= listarGrade( $arGrade );
        $stJs .= " limpaFormularioGrade(); ";
        $stJs .= desbloqueiaDiasSemana();
    }

    return $stJs;
}

function montaGrade()
{
    $arGrade = Sessao::read('Grade');

    $stJs .= listarGrade( $arGrade );
    $stJs .= " limpaFormularioGrade(); ";

    return $stJs;
}

function excluirGrade()
{
    $arTMP = array ();
    $id = $_REQUEST["inId"];
    $arGrade = Sessao::read("Grade");
    Sessao::remove("Grade");

    foreach ($arGrade as $campo => $valor) {
        if ($valor["inId"] != $id) {
            $arTMP[] = $valor;
        }
    }
    Sessao::write("Grade", $arTMP);
    $stJs = listarGrade( $arTMP );

    return $stJs;
}

function carregaDiaSemanaGrade()
{
    $arDiasSelecionados = montaDiasSelecionados();
    $arGrade  = Sessao::read("Grade");
    $inCodDia = $_GET["inDiaSemana"];

    $stJs .= desmarcaDiasSemana();
    foreach ($arDiasSelecionados as $inCodDia) {
        foreach ($arGrade as $chave => $dadosGrade) {
            if ($inCodDia == $dadosGrade["inDiaSemana"]) {
                $stJs .= " jQuery('#stHoraEntrada1').val('".$dadosGrade['stHoraEntrada1']."');  ";
                $stJs .= " jQuery('#stHoraEntrada2').val('".$dadosGrade['stHoraEntrada2']."');";
                $stJs .= " jQuery('#stHoraSaida1').val('".$dadosGrade['stHoraSaida1']."');      ";
                $stJs .= " jQuery('#stHoraSaida2').val('".$dadosGrade['stHoraSaida2']."');    ";
            }
        }
    }
    $stJs .= " jQuery('#inDiaSemana-".$inCodDia."').attr('checked', 'checked');               ";
    $stJs .= " jQuery('#inDiaSemana-".$inCodDia."').attr('disabled', '');                     ";
    $stJs .= " f.btIncluirGrade.disabled = true;                                              ";
    $stJs .= " f.btAlterarGrade.disabled = false;                                             ";
    $stJs.= "  f.btLimparGrade.setAttribute('onclick', \"montaParametrosGET('desbloqueiaDiasSemana')\");";

    return $stJs;
}

function desmarcaDiasSemana()
{
    $obTPessoalDiasTurno = new TPessoalDiasTurno();
    $obTPessoalDiasTurno->recuperaTodos($rsDiasTurno);

    while (!$rsDiasTurno->eof()) {
        $stJs .= " jQuery('#inDiaSemana-".$rsDiasTurno->getCampo('cod_dia')."').attr('checked', '');";
        $stJs .= " jQuery('#inDiaSemana-".$rsDiasTurno->getCampo('cod_dia')."').attr('disabled', 'disabled');";
        $rsDiasTurno->proximo();
    }

    return $stJs;
}

function desbloqueiaDiasSemana()
{
    $obTPessoalDiasTurno = new TPessoalDiasTurno();
    $obTPessoalDiasTurno->recuperaTodos($rsDiasTurno);

    while (!$rsDiasTurno->eof()) {
        $stJs .= " jQuery('#inDiaSemana-".$rsDiasTurno->getCampo('cod_dia')."').attr('checked', '');";
        $stJs .= " jQuery('#inDiaSemana-".$rsDiasTurno->getCampo('cod_dia')."').attr('disabled', '');";
        $rsDiasTurno->proximo();
    }

    return $stJs;
}

function listarGrade($arRecordSet)
{
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );
    $rsRecordSet->ordena('inDiaSemana');
    $stLink .= "&stAcao=".$_REQUEST["stAcao"]."&stDescricao=".$_GET["stDescricao"];

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Turnos Cadastrados" );
        $obLista->setRecordSet( $rsRecordSet );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Dia" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Entrada1" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Saída1" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Entrada2" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Saída2" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stNomDia");
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stHoraEntrada1");
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stHoraSaida1" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stHoraEntrada2");
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stHoraSaida2" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setLinkId("alterar");
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('carregaDiaSemanaGrade');");
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->ultimaAcao->addCampo("2","inDiaSemana");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirGrade');");
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    $stJs = "d.getElementById('spnListaHorario').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaDiasSelecionados()
{
    $arDiasSelecionados = array();
    if (count($_REQUEST)>0) {
        foreach ($_REQUEST as $chave => $valor) {
            $pos = strpos($chave, "inDiaSemana");
            if ($pos === false) {
                //faz nada
            } else {
                $arDiasSelecionados[] = $valor;
            }
        }
    }

    return $arDiasSelecionados;
}

switch (trim($_REQUEST["stCtrl"])) {
    case "incluirGrade":
        $stJs .= incluirGrade();
        break;
    case "alterarGrade":
        $stJs .= alterarGrade();
        break;
    case "excluirGrade":
        $stJs .= excluirGrade();
        break;
    case "carregaDiaSemanaGrade":
        $stJs .= carregaDiaSemanaGrade();
        break;
    case "montaGrade":
        $stJs .= montaGrade();
        break;
    case "desbloqueiaDiasSemana":
        $stJs .= desbloqueiaDiasSemana();
        $stJs .= " limpaFormularioGrade(); ";
        break;
}

if($stJs)
   echo($stJs);
?>
