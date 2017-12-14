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
    * 
    * Data de Criação   : 26/09/2014

    * @author Analista:
    * @author Desenvolvedor:  Lisiane Morais
    * @ignore

    $Id: OCManterOrcamento.php 60602 2014-11-03 14:49:54Z michel $

    *Casos de uso: uc-03.05.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConvenios";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

switch ($request->get('stCtrl')) {

    case 'buscaDados':
        $ano_vigencia   = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 63 AND parametro = 'tcepe_ano_vigencia'");
        $dtAprovacaoLOA = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 63 AND parametro = 'tcepe_data_aprovacao_LOA'");
        $dtAprovacaoLDO = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 63 AND parametro = 'tcepe_data_aprovacao_LDO'");
        $dtAprovacaoPPA = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 63 AND parametro = 'tcepe_data_aprovacao_PPA'");
        $inCodLeiLOA    = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 63 AND parametro = 'tcepe_lei_orcamentaria_LOA'");
        $inCodLeiLDO    = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 63 AND parametro = 'tcepe_lei_orcamentaria_LDO'");
        $inCodLeiPPA    = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 63 AND parametro = 'tcepe_lei_orcamentaria_PPA'");
        
        if ( $ano_vigencia ){
            $stJs =  "jq('#ano_vigencia').val('".$ano_vigencia."');\n";
        }else {
            $stJs .= "jq('#ano_vigencia').val('');\n";
        }
        
        if ( $dtAprovacaoLOA ){
            $stJs .=  "jq('#dtAprovacaoLOA').val('".$dtAprovacaoLOA."');\n";
        }else {
            $stJs .= "jq('#dtAprovacaoLOA').val('');\n";
        }
        
        if ( $dtAprovacaoLDO ){
            $stJs .=  "jq('#dtAprovacaoLDO').val('".$dtAprovacaoLDO."');\n";
        }else {
            $stJs .= "jq('#dtAprovacaoLDO').val('');\n";
        }
        
        if ( $dtAprovacaoPPA ){
            $stJs .=  "jq('#dtAprovacaoPPA').val('".$dtAprovacaoPPA."');\n";
        }else {
            $stJs .= "jq('#dtAprovacaoPPA').val('');\n";
        }
        
        $stJs .= buscaLei('inCodLeiLOA', 'stNomeLeiLOA', $inCodLeiLOA);
        $stJs .= buscaLei('inCodLeiLDO', 'stNomeLeiLDO', $inCodLeiLDO);
        $stJs .= buscaLei('inCodLeiPPA', 'stNomeLeiPPA', $inCodLeiPPA);

        echo $stJs;
        
    break;

    case 'buscaLeiLOA':
        buscaLei('inCodLeiLOA', 'stNomeLeiLOA', $_REQUEST['inCodLeiLOA']);
    break;

    case 'buscaLeiLDO':
        buscaLei('inCodLeiLDO', 'stNomeLeiLDO', $_REQUEST['inCodLeiLDO']);
    break;

    case 'buscaLeiPPA':
        buscaLei('inCodLeiPPA', 'stNomeLeiPPA', $_REQUEST['inCodLeiPPA']);
    break;
}

function buscaLei($nomCodLei, $nomNomeLei, $inCodLei){
    $stJs  = "jq('#".$nomCodLei."').val('');\n";
    $stJs .= "jq('#".$nomNomeLei."').html('&nbsp;');";
    if($inCodLei){
        $obTNorma = new TNorma;
        $stFiltro = ' WHERE N.cod_norma='.$inCodLei.' ';
        $obTNorma->recuperaNormasDecreto($rsLei, $stFiltro);
        
        if($rsLei->getNumLinhas()>0){
            $stLei  = $rsLei->getCampo('nom_tipo_norma').' '.$rsLei->getCampo('num_norma_exercicio').' - '.$rsLei->getCampo('nom_norma');
            $stJs  = "jq('#".$nomCodLei."').val('".$inCodLei."');\n";
            $stJs .= "jq('#".$nomNomeLei."').html('".$stLei."');";
        }else
            $stJs .= "alertaAviso('@Código informado não existe. (".$inCodLei.")','form','erro','".Sessao::getId()."');";
    }
    
    echo $stJs;
}
