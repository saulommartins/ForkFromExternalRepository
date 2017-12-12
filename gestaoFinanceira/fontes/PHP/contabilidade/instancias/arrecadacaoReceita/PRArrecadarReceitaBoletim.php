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
    * Página de Processamento de Arrecadação de Receita
    * Data de Criação   : 21/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.05
*/

/*
$Log$
Revision 1.4  2006/07/05 20:50:39  cleisson
Adicionada tag Log aos arquivos

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceitaBoletim.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ArrecadarReceitaBoletim";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgProx    = $pgForm;

$stLink = "&stAcao=".$_REQUEST["stAcao"];

switch ($stAcao) {
    case "gerar":
        set_time_limit(0);
        $obRContabilidadeLancamentoReceitaBoletim = new RContabilidadeLancamentoReceitaBoletim;
        $obRContabilidadeLancamentoReceitaBoletim->setDataInicial ( $_REQUEST["stDtInicio"] );
        $obRContabilidadeLancamentoReceitaBoletim->setDataFinal   ( $_REQUEST["stDtTermino"] );
        $obErro = $obRContabilidadeLancamentoReceitaBoletim->importaReceitasSam();
        $stMensagem  = "( Período: ".$_REQUEST["stDtInicio"]." - ".$_REQUEST["stDtTermino"]." ";
        $stMensagem .= " / Tempo de processamento: ".$obRContabilidadeLancamentoReceitaBoletim->getTempoImportacao().") ";
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso( $pgFilt."?".$stLink, "Processo concluído!".$stMensagem ,"gerar","aviso",Sessao::getId(),"../");
        } else {
            SistemaLegado::alertaAviso( $pgFilt."?".$stLink, "Processo concluído com erros!".$stMensagem." <a href='".CAM_GF_CONTABILIDADE."tmp/".$obRContabilidadeLancamentoReceitaBoletim->getNomLogErros()."' target='blank'>Verifique o log.</a>","gerar","aviso", Sessao::getId(), "../");

//            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}
?>
