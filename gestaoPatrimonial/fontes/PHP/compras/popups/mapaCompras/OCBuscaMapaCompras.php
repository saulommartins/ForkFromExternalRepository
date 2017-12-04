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
* Arquivo instância para popup de Objeto
* Data de Criação: 07/03/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Leandro André Zis

* Casos de uso :uc-03.04.07, uc-03.04.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_COM_MAPEAMENTO."TComprasMapa.class.php");
include_once(CAM_GP_COM_MAPEAMENTO."TComprasMapaItemReserva.class.php");

function buscaPopup()
{
    $stCampoCod  = $_REQUEST['stNomCampoCod'];
    $stCampoDesc = $_REQUEST['stIdCampoDesc'];
    $inCodigo    = $_REQUEST[ $stCampoCod ];
    $stExercicio = $_REQUEST['stExercicioMapa'];
    $stJs = null;
    $stMensagem = '';

    if ($inCodigo != "") {
            $arMapa = explode ( '/', $inCodigo );

            $obTComprasMapa = new TComprasMapa();

            if ($stExercicio) {
                    if (!$arMapa[1]) {
                            $arMapa[1] = $stExercicio;
                    } elseif ($arMapa[1] != $stExercicio) {
                            $stMensagem = "Exercício inválido ( ".$arMapa[1] . ") é necessário escolher um mapa do exercício $stExercicio. ";
                    }
            }

            if ($stMensagem == '') {
                    if (!$arMapa[1]) {
                            $arMapa[1] = Sessao::getExercicio();
                            $stJs .= "f.$stCampoCod.value = '" . $arMapa[0] . "/" . $arMapa[1] ."'; \n  ";
                    }

                    $rsMapa = new RecordSet;
                    $inCodLicitacaoMapa = 0;
                    $inHdnCodLicitacao = 0;
                    switch ($_REQUEST['stTipoBusca']) {
                            case 'processoLicitatorio':
                                $stFiltro  = "and mapa.cod_mapa = ".$arMapa[0];
                                $stFiltro .= "and mapa.exercicio = '".$arMapa[1]."'";
                                $boModalidade = false;
                                if ( $_REQUEST['stAcao'] == 'alterar' ){
                                    $arModalidade = explode('-',$_REQUEST['stModalidade']);
                                    $inCodModalidade = trim($arModalidade[0]);
                                    $inHdnCodLicitacao = $_REQUEST['hdnCodLicitacao'];
                                    
                                    $inCodLicitacaoMapa = SistemaLegado::pegaDado('cod_licitacao','licitacao.licitacao'," WHERE exercicio_mapa = '".$arMapa[1]."' AND cod_mapa = ".$arMapa[0]);
                                    $stExercicioLicitacaoMapa = SistemaLegado::pegaDado('exercicio','licitacao.licitacao'," WHERE exercicio_mapa = '".$arMapa[1]."' AND cod_mapa = ".$arMapa[0]);
                                
                                    if ($inCodModalidade == 3 || $inCodModalidade == 6 || $inCodModalidade == 7 ) {
                                        $boModalidade = false;
                                        $stFiltro .= " \n";
                                    } else {
                                        $boModalidade = true;
                                        $stFiltro .= "
                                            AND EXISTS(SELECT mp.exercicio
                                                            , mp.cod_mapa
                                                            , mp.cod_objeto
                                                            , mp.timestamp
                                                            , mp.cod_tipo_licitacao
                                                            , solicitacao.registro_precos
                                                         FROM compras.mapa AS mp
                                                   INNER JOIN compras.mapa_solicitacao
                                                           ON mapa_solicitacao.exercicio = mp.exercicio
                                                          AND mapa_solicitacao.cod_mapa  = mp.cod_mapa
                                                   INNER JOIN compras.solicitacao_homologada
                                                           ON solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                                                          AND solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                                                          AND solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
                                                   INNER JOIN compras.solicitacao
                                                           ON solicitacao.exercicio       = solicitacao_homologada.exercicio
                                                          AND solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
                                                          AND solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
                                                        WHERE mp.cod_mapa = mapa.cod_mapa
                                                          AND mp.exercicio = mapa.exercicio
                                                          AND solicitacao.registro_precos IS FALSE
                                                     GROUP BY mp.exercicio
                                                            , mp.cod_mapa
                                                            , mp.cod_objeto
                                                            , mp.timestamp
                                                            , mp.cod_tipo_licitacao
                                                            , solicitacao.registro_precos )
                                        \n";
                                    }
                                }

                                $obTComprasMapa->recuperaMapaSemReservaProcessoLicitatorio($rsMapa, $stFiltro );
                            break ;
                            default:
                                    $stFiltro  = "where mapa.cod_mapa = ".$arMapa[0];
                                    $stFiltro .= " and mapa.exercicio = '".$arMapa[1]."'";
                                    $obTComprasMapa->recuperaTodos ( $rsMapa, $stFiltro );
                            break;
                    }

                    if ( $rsMapa->getNumLinhas()<=0 OR $inCodLicitacaoMapa !=$inHdnCodLicitacao) {

                            $boMapa = sistemaLegado::pegaDado('cod_mapa','compras.mapa',' where cod_mapa = '.$arMapa[0].' and exercicio = \''.$arMapa[1].'\' ' );

                            $boProcessoLicitatorio = sistemaLegado::pegaDado('cod_licitacao','licitacao.licitacao',' where cod_mapa = '.$arMapa[0].' and exercicio_mapa = \''.$arMapa[1].'\' ');

                            include_once CAM_GP_COM_MAPEAMENTO."TComprasCompraDireta.class.php";
                            $obTComprasCompraDireta = new TComprasCompraDireta;
                            $obTComprasCompraDireta->setDado('cod_mapa'       , $arMapa[0]);
                            $obTComprasCompraDireta->setDado('exercicio_mapa' , $arMapa[1]);
                            $obTComprasCompraDireta->recuperaCompraDiretaPorMapa($rsCompraDiretaAnulada);

                            if($rsCompraDiretaAnulada->getNumLinhas() > 0)
                                $boCompraDireta = true;

                            $obTComprasMapaItemReserva = new TComprasMapaItemReserva();
                            $obTComprasMapaItemReserva->setDado( 'cod_mapa', $arMapa[0] );
                            $obTComprasMapaItemReserva->setDado( 'exercicio', $arMapa[1] );
                            $obTComprasMapaItemReserva->recuperaMapaReserva( $rsMapaReserva );

                            if (!$boMapa) {
                                    $stJs .= "alertaAviso('@Código do Mapa (". $arMapa[0] . '/' . $arMapa[1]  .") não encontrado.', 'form','erro','".Sessao::getId()."');";
                            } elseif ($boProcessoLicitatorio) {
                                if ($boModalidade == true && $_REQUEST['stAcao'] == 'alterar') {
                                    $stJs .= "alertaAviso('@O Mapa (". $arMapa[0] . '/' . $arMapa[1]  ."). A Modalidade desse Processo Licitatório, não é compativel com o mapa de Registro de Preços.', 'form','erro','".Sessao::getId()."');";
                                } else {
                                    $stJs .= "alertaAviso('@O Mapa (". $arMapa[0] . '/' . $arMapa[1]  .") está em Processo Licitatório.', 'form','erro','".Sessao::getId()."');";
                                }
                            } elseif ($boCompraDireta) {
                                    $stJs .= "alertaAviso('@O Mapa (". $arMapa[0] . '/' . $arMapa[1]  .") está vinculado a uma Compra Direta.', 'form','erro','".Sessao::getId()."');";
                            } elseif ( $rsMapaReserva->getNumLinhas() <= 0 ) {
                                    $stJs .= "alertaAviso('@O Mapa (". $arMapa[0] . '/' . $arMapa[1]  .") não possui todas as reservas de saldo.', 'form','erro','".Sessao::getId()."');";
                            }
                            $stJs .= "f.$stCampoCod.value = '';";
                            $stJs .= "f.$stCampoCod.focus();";
                    } else {
                        $stJs .= "f.$stCampoCod.value = '".$arMapa[0] . '/' . $arMapa[1] ."';";
                    }
            } else {
                    $stJs .= "alertaAviso('$stMensagem', 'form','erro','".Sessao::getId()."');";
                    $stJs .= "f.$stCampoCod.value = '';";
                    $stJs .= "f.$stCampoCod.focus();";
            }
    }
    if ($stJs) {
            sistemaLegado::executaFrameOculto( $stJs );
    }
}

switch ($_GET['stCtrl']) {

    case 'buscaPopup':
    default:
        buscaPopup();
    break;

}

?>
