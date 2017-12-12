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
* Página relatório de Servidor
* Data de Criação   : 15/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30860 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.04.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );
include_once( CAM_GRH_PES_NEGOCIO."RRelatorioServidor.class.php"      );

$obRRelatorio           = new RRelatorio;
$obRRelatorioServidor   = new RRelatorioServidor;
$rsRecordset            = new Recordset;

$arFiltro = Sessao::read('filtroRelatorio');

switch ($arFiltro["stTipoFiltro"]) {
    case "contrato":
    case "contrato_todos":
    case "contrato_rescisao":
    case "contrato_aposentado":
    case "cgm_contrato":
    case "cgm_contrato_todos":
    case "cgm_contrato_rescisao":
    case "cgm_contrato_aposentado":
        $stFiltroContratos = " AND contrato.cod_contrato IN (";
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stFiltroContratos .= $arContrato["cod_contrato"].",";
        }
        $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";
    break;
    case "contrato_pensionista":
    case "cgm_contrato_pensionista":
        $stFiltroContratos = " AND contrato.cod_contrato IN (";
        foreach (Sessao::read("arPensionistas") as $arContrato) {
            $stFiltroContratos .= $arContrato["cod_contrato"].",";
        }
        $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";
    break;
    case "lotacao":
        $stOrdem = "";
        $virgula = "";

        if ($boAgrupar) {
            $stOrdem .= "orgao";
            $virgula = ", ";
        }
        if ($stOrdenacao == "alfabetica") {
            $stOrdem .= $virgula."descricao_lotacao,nom_cgm";
        } else {
            $stOrdem .= $virgula."orgao,registro";
        }
        $stFiltroContratos = " AND vw_orgao_nivel.cod_orgao IN (";
        foreach ($arFiltro['inCodLotacaoSelecionados'] as $inCodOrgao) {
            $stFiltroContratos .= $inCodOrgao.",";
        }
        $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";
    break;
    case "local":
        $stOrdem = "";
        $virgula = "";

        if ($boAgrupar) {
            $stOrdem .= "local";
            $virgula = ", ";
        }

        if ($stOrdenacao == "alfabetica") {
            $stOrdem .= $virgula."descricao_local,nom_cgm";
        } else {
            $stOrdem .= $virgula."cod_local,registro";
        }
        $stFiltroContratos = " AND contrato_servidor_local.cod_local IN (";
        foreach ($arFiltro['inCodLocalSelecionados'] as $inCodLocal) {
            $stFiltroContratos .= $inCodLocal.",";
        }
        $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";
    break;
    case "atributo_servidor":
        $inCodAtributo = $arFiltro["inCodAtributo"];
        $inCodCadastro = $arFiltro["inCodCadastro"];
        $stFiltroContratos = " AND atributo_dinamico.cod_atributo IN (";
        if ( is_array($inCodAtributo) ) {
            foreach ( $inCodAtributo as $cod_atributo) {
                $stFiltroContratos .= $cod_atributo.",";
            }    
            $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";            
        }else{
            $stFiltroContratos .= $inCodAtributo.")";
        }
        $stFiltroContratos .= " AND atributo_dinamico.cod_cadastro IN (";
        if ( is_array($inCodCadastro) ) {
            foreach ($inCodCadastro as $cod_cadastro) {
                $stFiltroContratos .= $cod_cadastro.",";
            }
            $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";
        }else{
            $stFiltroContratos .= $inCodCadastro.")";
        }
        $valorAtributo = $arFiltro["Atributo_".$inCodAtributo."_".$inCodCadastro.""];

        if ($valorAtributo != '') {
            $stFiltroContratos .=" AND atributo_contrato_servidor_valor.valor = '".$valorAtributo."' ";
        }

   break;
}

switch ($arFiltro['stSituacao']){
    case 'ativos':
        $stFiltroContratos .=" AND situacao = 'A' ";
    break;
    
    case 'rescindidos':
        $stFiltroContratos .=" AND situacao = 'R' ";
    break;

    case 'aposentados':
        $stFiltroContratos .=" AND situacao = 'P' ";
    break;

    case 'pensionistas':
        $stFiltroContratos .=" AND situacao = 'E' ";
    break;
}

if ($stFiltroContratos != '') {
    $stFiltroContratos = " WHERE ".substr($stFiltroContratos,5);
}

$obRRelatorioServidor->geraRecordSet( $rsRecordset, $stFiltroContratos, $stOrder );

Sessao::write('rsServidores', $rsRecordset);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioServidor.php" );
?>
