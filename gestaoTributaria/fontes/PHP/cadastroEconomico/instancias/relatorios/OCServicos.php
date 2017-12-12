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
    * Frame Oculto para popup logradouro
    * Data de Criação   : 02/05/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    * $Id: OCServicos.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMServico.class.php" );

// INSTANCIA OBJETO
$arFiltroSessao = Sessao::read( "filtroRelatorio" );
// SETA ATRIBUTOS DA REGRA QUE IRA GERAR O FILTRO DO RELATORIO
$stOrder = " ORDER BY ";
switch ($arFiltroSessao["stOrder"]) {
    case 'codigo':
        $stOrder .= " servico.cod_estrutural ";
        break;

    case 'descricao':
        $stOrder .= " servico.nom_servico ";
        break;

    default:
        $stOrder .= " servico.cod_estrutural ";
        break;
}

$stFiltro = "";
if ($arFiltroSessao["stNomServico"]) {
    $stFiltro .= " servico.nom_servico LIKE '%".$arFiltroSessao["stNomServico"]."%' AND ";
}

if ($arFiltroSessao["inCodInicio"] && $arFiltroSessao["inCodTermino"]) {
    $stFiltro .= " servico.cod_estrutural BETWEEN '".$arFiltroSessao["inCodInicio"]."' AND '".$arFiltroSessao["inCodTermino"]."' AND ";
}else
    if ($arFiltroSessao["inCodInicio"]) {
        $stFiltro .= " servico.cod_estrutural = '".$arFiltroSessao["inCodInicio"]."' AND ";
    }else
        if ($arFiltroSessao["inCodTermino"]) {
            $stFiltro .= " servico.cod_estrutural = '".$arFiltroSessao["inCodTermino"]."' AND ";
        }

if ($arFiltroSessao["inCodInicioVigencia"] && $arFiltroSessao["inCodTerminoVigencia"]) {
    $arDataInicio = explode( "/", $arFiltroSessao["inCodInicioVigencia"] );
    $arDataFim = explode( "/", $arFiltroSessao["inCodTerminoVigencia"] );
    $stFiltro .= " vigencia_servico.dt_inicio BETWEEN '".$arDataInicio[2]."-".$arDataInicio[1]."-".$arDataInicio[0]."' AND '".$arDataFim[2]."-".$arDataFim[1]."-".$arDataFim[0]."' AND ";
}else
    if ($arFiltroSessao["inCodInicioVigencia"]) {
        $arDataInicio = explode( "/", $arFiltroSessao["inCodInicioVigencia"] );
        $stFiltro .= " vigencia_servico.dt_inicio = '".$arDataInicio[2]."-".$arDataInicio[1]."-".$arDataInicio[0]."' AND ";
    }else
        if ($arFiltroSessao["inCodTerminoVigencia"]) {
            $arDataFim = explode( "/", $arFiltroSessao["inCodTerminoVigencia"] );
            $stFiltro .= " vigencia_servico.dt_inicio = '".$arDataFim[2]."-".$arDataFim[1]."-".$arDataFim[0]."' AND ";
        }

if ($stFiltro) {
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
}

$obTCEMServico = new TCEMServico;
// GERA RELATORIO ATRAVES DO FILTRO SETADO
$obTCEMServico->listaServicosRelatorio( $rsServicos, $stFiltro, $stOrder );
$rsServicos->addFormatacao( "aliquota", "NUMERIC_BR" );
Sessao::write( "lista_servicos", $rsServicos );

$obRRelatorio = new RRelatorio;
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioServicos.php" );
?>
