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
    * Página de Oculto do Consultar Registro de Evento de Férias
    * Data de Criação: 23/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30547 $
    $Name$
    $Author: souzadl $
    $Date: 2006-09-08 07:05:52 -0300 (Sex, 08 Set 2006) $

    * Casos de uso: uc-04.05.53
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarRegistroEventoDecimo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function processarForm()
{
    $stJs .= gerarSpanEventosCadastrados();

    return $stJs;
}

function gerarSpanEventosCadastrados()
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoDecimo.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
    $inMes = (strlen($_GET['inCodMes']) == 1 ) ? '0'.$_GET['inCodMes'] : $_GET['inCodMes'];
    $dtPeriodoMovimentacao = $inMes."/".$_GET['inAno'];
    $stFiltro = " AND to_char(FPM.dt_final, 'mm/yyyy') = '".$dtPeriodoMovimentacao."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltro);
    if ( $rsPeriodoMovimentacao->getNumLinhas() > 0 ) {
        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " WHERE registro = ".$_GET["inRegistro"];
        $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
        $obTFolhaPagamentoRegistroEventoDecimo = new TFolhaPagamentoRegistroEventoDecimo;
        $stFiltro  = " AND cod_contrato = ".$rsContrato->getCampo("cod_contrato");
        $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltro .= " AND natureza != 'B'";
        $stOrdem  = " descricao";
        $obTFolhaPagamentoRegistroEventoDecimo->recuperaRelacionamento($rsEventos,$stFiltro,$stOrdem);
        if ( $rsEventos->getNumLinhas() > 0 ) {
            $rsEventos->addFormatacao("valor","NUMERIC_BR");
            $rsEventos->addFormatacao("quantidade","NUMERIC_BR");

            //retira a formatacao do campo 'quantidade' para que possa imprimir na forma quantidade/total_de_parcelas
            while (!$rsEventos->eof()) {
                $arRegistro = $rsEventos->getObjeto();
                if (array_key_exists('parcela', $arRegistro) and $arRegistro['parcela'] != '') {
                    $arRegistro['quantidade'] = number_format($rsEventos->getCampo('quantidade')).'/'.$rsEventos->getCampo('parcela');
                }
                $arEventos[] = $arRegistro;
                $rsEventos->proximo();
            }
            $rsEventos->addFormatacao("quantidade","");
            $rsEventos->preenche($arEventos);

            $obLista = new Lista;
            $obLista->setTitulo("Eventos Cadastrados");
            $obLista->setRecordSet( $rsEventos );
            $obLista->setMostraPaginacao( false );

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Código");
            $obLista->ultimoCabecalho->setWidth( 10 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Evento");
            $obLista->ultimoCabecalho->setWidth( 30 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Desdobramento");
            $obLista->ultimoCabecalho->setWidth( 10 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Valor");
            $obLista->ultimoCabecalho->setWidth( 10 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Quantidade");
            $obLista->ultimoCabecalho->setWidth( 10 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Automático");
            $obLista->ultimoCabecalho->setWidth( 10 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("DIREITA");
            $obLista->ultimoDado->setCampo( "codigo" );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("ESQUERDA");
            $obLista->ultimoDado->setCampo( "descricao" );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("ESQUERDA");
            $obLista->ultimoDado->setCampo( "desdobramento_texto" );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("DIREITA");
            $obLista->ultimoDado->setCampo( "valor" );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("DIREITA");
            $obLista->ultimoDado->setCampo( "quantidade" );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("CENTRO");
            $obLista->ultimoDado->setCampo( "automatico" );
            $obLista->commitDado();

            $obLista->montaHTML();
            $stHtml = $obLista->getHTML();
            $stHtml = str_replace("\n","",$stHtml);
            $stHtml = str_replace("  ","",$stHtml);
            $stHtml = str_replace("'","\\'",$stHtml);

            $stJs .= "d.getElementById('spnEventosCadastrados').innerHTML = '".$stHtml."';   \n";
            $stJs .= gerarSpanEventosBase($arEventos,$rsContrato->getCampo("cod_contrato"),$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
        }
    }

    return $stJs;
}

function gerarSpanEventosBase($arEventos,$inCodContrato,$inCodPeriodoMovimentacao)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoBase.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $rsEventosBase = new Recordset;
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
    foreach ($arEventos as $arEvento) {
        $stCodEventos .= $arEvento['cod_evento'].",";
    }
    if ($stCodEventos != "") {
        $stCodEventos = substr($stCodEventos,0,strlen($stCodEventos)-1);
        $stFiltro  = " AND evento_base.cod_evento IN ($stCodEventos)";
        $stFiltro .= " AND evento_base.cod_configuracao = 3";
        $stFiltro .= " AND registro_evento_decimo.cod_contrato = $inCodContrato";
        $stFiltro .= " AND registro_evento_decimo.cod_periodo_movimentacao = $inCodPeriodoMovimentacao";

        $obTFolhaPagamentoEventoBase = new TFolhaPagamentoEventoBase;
        $obTFolhaPagamentoEventoBase->recuperaEventoBaseDesdobramentoDecimo($rsEventosBase,$stFiltro);
    }

    if ( $rsEventosBase->getNumLinhas() > 0 ) {
        $arEventosBase = $rsEventosBase->getElementos();

        foreach ($arEventosBase as $inIndex=>$arEventoBase) {
            $arEventoBase['descricao']  = trim($arEventoBase['descricao']);
            $arEventoBase['desdobramento_texto'] = trim($arEventoBase['desdobramento_texto']);
            $arEventoBase['valor']      = '0,00';
            $arEventoBase['quantidade'] = '0,00';
            $arEventoBase['automatico'] = 'Sim';
            $arEventosBase[$inIndex]    = $arEventoBase;
        }
        $rsEventosBase = new Recordset;
        $rsEventosBase->preenche($arEventosBase);

        $obLista = new Lista;
        $obLista->setTitulo("Base de Cálculo");
        $obLista->setRecordSet( $rsEventosBase );
        $obLista->setMostraPaginacao( false );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Código");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Evento");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Desdobramento");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Quantidade");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Automático");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "codigo_base" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "descricao_base" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "desdobramento_texto" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "valor" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "quantidade" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "automatico" );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);

        $stJs .= "d.getElementById('spnEventosBase').innerHTML = '".$stHtml."';   \n";
    } else {
        $stJs .= "d.getElementById('spnEventosBase').innerHTML = '';   \n";
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "processarForm":
        $stJs .= processarForm();
    break;
}

if ($stJs) {
   echo $stJs;
}

?>
