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
    * Página de processamento para o cadastro de vigências
    * Data de Criação   : 28/03/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: PRManterVigencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.02
*/

/*
$Log$
Revision 1.4  2006/09/18 10:30:39  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php");

;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterVigencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RCIMNivel;
$obErro  = new Erro;

/* Função para data no formato ingles*/

function dtBr2dtEn($dtData)
{
    $dia = substr($dtData,0,2);
    $mes = substr($dtData,3,2);
    $ano = substr($dtData,6,4);
    $dtData = $ano."-".$mes."-".$dia;

    return $dtData;
}

$rsDataUltimaVigencia = new Recordset;
$stDataInicio = $_REQUEST['dtDataInicio'];
$obRegra->consultarDataUltimaVigencia($rsDataUltimaVigencia);
$stUltDataVigencia = $rsDataUltimaVigencia->getCampo( "dtinicio" );

/* Converter datas para formato americano */

// Data Digitada
$stAno = substr($stDataInicio,6,4);
$stMes = substr($stDataInicio,3,2);
$stDia = substr($stDataInicio,0,2);
$stDataInicioA = $stAno."-".$stMes."-".$stDia;
// Ultima data do Banco de Dados
$stAno = substr($stUltDataVigencia,6,4);
$stMes = substr($stUltDataVigencia,3,2);
$stDia = substr($stUltDataVigencia,0,2);
$stUltDataVigenciaA = $stAno."-".$stMes."-".$stDia;
/* Fim da Conversao*/

switch ($stAcao) {
    case "incluir":
        $obRegra->setInicioVigencia( $_REQUEST['dtDataInicio'] );
        $obRegra->consultarDataUltimaVigencia($rsUltimaData);
        //if ($stDataInicioA>$stUltDataVigenciaA) {
        if ( dtBr2dtEn($rsUltimaData->getCampo("dtinicio")) < dtBr2dtEn($_REQUEST['dtDataInicio'])) {
            $obRegra->setInicioVigencia( $stDataInicio );
            $obErro = $obRegra->incluirVigencia();
            if( !$obErro->ocorreu() )
                SistemaLegado::alertaAviso($pgForm,"Vigência: ".$obRegra->getCodigoVigencia().' - '.$obRegra->getInicioVigencia(),"incluir","aviso", Sessao::getId(), "../");
            else
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        } else {
            SistemaLegado::exibeAviso("Data de Início deve ser posterior à ".$rsUltimaData->getCampo("dtinicio").". Data: ".$_REQUEST['dtDataInicio'],"n_incluir","aviso");
            SistemaLegado::executaFrameOculto("f.dtDataInicio.value='';f.dtDataInicio.focus();");
        }
    break;
    case "excluir":
        $obRegra->setCodigoVigencia( $_REQUEST['inCodigoVigencia'] );
        $obRegra->setInicioVigencia( $_REQUEST['dtDataInicio'] );
        /* Vigencia nao pode estar vinculada a nenhum nivel */

        $obRegra->listarNiveis($rsNiveisDaVigencia);
        if ($rsNiveisDaVigencia->getNumLinhas()>0) {
            SistemaLegado::alertaAviso($pgList,"Vigência vinculada a um ou mais níveis. Código da Vigência: ".$_REQUEST['inCodigoVigencia'],"n_excluir","erro");
        } else { // se nao existir niveis para determinada exigencia, exclui!
            $obErro = $obRegra->excluirVigencia();
                if( !$obErro->ocorreu() )
                    SistemaLegado::alertaAviso($pgList,"Vigência: ".$obRegra->getCodigoVigencia().' - '.$obRegra->getInicioVigencia(),"excluir","aviso", Sessao::getId(), "../");
                else
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;
    case "alterar":
        $obRegra->setCodigoVigencia( $_REQUEST['inCodigoVigencia'] );
        $obRegra->setInicioVigencia( $_REQUEST['dtDataInicio'] );
        $obRegra->consultarDataUltimaVigencia($rsUltimaData);
        $obRegra->listarNiveis( $rsNivel );

        if ( $rsNivel->getNumLinhas() < 1 ) {
            $obErro = $obRegra->alterarVigencia();
            if( !$obErro->ocorreu() )
                SistemaLegado::alertaAviso($pgList,"Vigência: ".$obRegra->getCodigoVigencia().' - '.$obRegra->getInicioVigencia(),"alterar","aviso", Sessao::getId(), "../");
            else
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        } else {
            SistemaLegado::exibeAviso("Vigência ".$_REQUEST['inCodigoVigencia']." possui ao menos um nível cadastrado. Não pode ser alterada!","n_incluir","aviso");
        }
    break;

}
