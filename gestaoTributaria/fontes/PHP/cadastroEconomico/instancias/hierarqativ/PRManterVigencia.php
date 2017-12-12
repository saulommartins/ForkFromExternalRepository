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
    * Página de Processamento de Vigencia
    * Data de Criação   : 17/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo
    * @ignore

    * $Id: PRManterVigencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.06

*/

/*
$Log$
Revision 1.4  2006/09/15 14:32:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GT_CEM_NEGOCIO."RCEMNivelAtividade.class.php");

;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterVigencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RCEMNivelAtividade;
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

switch ($stAcao) {

    case "incluir":
        $obRegra->setInicioVigencia( $_REQUEST['dtDataInicio'] );
        $obRegra->consultarDataUltimaVigencia($rsUltimaData);

        if ( dtBr2dtEn($rsUltimaData->getCampo("dtinicio")) < dtBr2dtEn($_REQUEST['dtDataInicio'])) {
            $obErro = $obRegra->incluirVigencia();
            if( !$obErro->ocorreu() )
                sistemaLegado::alertaAviso($pgForm,"Vigência: ".$obRegra->getCodigoVigencia().'-'.$obRegra->getInicioVigencia(),"incluir","aviso", Sessao::getId(), "../");
            else
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        } else {
            sistemaLegado::exibeAviso("Data deve ser maior que a ultima cadastrada: Data:".$_REQUEST['dtDataInicio'],"n_incluir","aviso");
            sistemaLegado::executaFrameOculto("f.dtDataInicio.value='';f.dtDataInicio.focus();");
        }
    break;
    case "excluir":
        $obRegra->setCodigoVigencia( $_REQUEST['inCodigoVigencia'] );
        $obRegra->setInicioVigencia( $_REQUEST['dtDataInicio'] );
        /* COnsultar se a niveis para vigencia */

        $obRegra->listarNiveis($rsNiveisVigencia);
        if ( $rsNiveisVigencia->getNumLinhas() < 1) {
            $obErro = $obRegra->excluirVigencia();
            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgList,"Vigência: ".$obRegra->getCodigoVigencia().'-'.$obRegra->getInicioVigencia(),"excluir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
            }
        } else {
       sistemaLegado::alertaAviso($pgList,"Vigência possui niveis atrelados a ela! (Vigência:".$obRegra->getCodigoVigencia().")","erro","erro", Sessao::getId(), "../");
      }
     break;
    case "alterar":
        $obRegra->setCodigoVigencia( $_REQUEST['inCodigoVigencia'] );
        $obRegra->setInicioVigencia( $_REQUEST['dtDataInicio'] );
        $obRegra->consultarDataUltimaVigencia($rsUltimaData);

        if ( dtBr2dtEn($rsUltimaData->getCampo("dtinicio")) < dtBr2dtEn($_REQUEST['dtDataInicio'])) {
           $obErro = $obRegra->alterarVigencia();

            if( !$obErro->ocorreu() )
                sistemaLegado::alertaAviso($pgList,"Vigência: ".$obRegra->getCodigoVigencia().'-'.$obRegra->getInicioVigencia(),"alterar","aviso", Sessao::getId(), "../");
            else
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        } else {
            sistemaLegado::exibeAviso("Data deve ser maior que a ultima cadastrada: Data:".$_REQUEST['dtDataInicio'],"n_incluir","aviso");
            sistemaLegado::executaFrameOculto("f.dtDataInicio.value='';f.dtDataInicio.focus();");
        }

    break;

}
