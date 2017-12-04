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
    $Date: 2007-06-04 19:18:28 -0300 (Seg, 04 Jun 2007) $

    * Casos de uso: uc-02.02.05
*/

/*
$Log$
Revision 1.6  2007/06/04 22:17:07  cako
Bug #9349#

Revision 1.5  2006/07/07 13:26:22  jose.eduardo
Bug #6383#

Revision 1.4  2006/07/05 20:50:39  cleisson
Adicionada tag Log aos arquivos

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceita.class.php"        );
include( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceitaBoletim.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ArrecadarReceita";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgProx    = $pgForm;

$obRContabilidadeLancamentoReceita = new RContabilidadeLancamentoReceita;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
$obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $_POST['inCodLote'] );
$obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote( $_POST['stNomLote'] );
$obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( $_POST['stDtLote'] );
$obRContabilidadeLancamentoReceita->setContaDebito( $_POST['inCodContaDebito'] );
$obRContabilidadeLancamentoReceita->setContaCredito( $_POST['inCodContaCredito'] );
$obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( $_POST['inCodHistorico'] );
$obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->setBoComplemento( $_POST['boComplemento'] );
$obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->setComplemento( $_POST['stComplemento'] );
$obRContabilidadeLancamentoReceita->obROrcamentoReceita->setCodReceita( $_POST['inCodReceita'] );
$nuValor = str_replace('.','',$_POST['nuValor']);
$nuValor = str_replace(',','.',$nuValor);

if($nuValor < 0.00) $nuValor = $nuValor * -1;

$obRContabilidadeLancamentoReceita->setValor(  $nuValor );
$obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );

switch ($stAcao) {
    case "anular":
        //$pgProx = $pgList;
        $obRContabilidadeLancamentoReceita->setEstorno ('true');
        $obErro = $obRContabilidadeLancamentoReceita->alterar();
        if ( !$obErro->ocorreu() ) {
            $pgProx .= "?".Sessao::getId();
            $pgProx .= "&inCodEntidade=".$_POST['inCodEntidade'];
            $pgProx .= "&inCodLote=".$_POST['inCodLote'];
            $pgProx .= "&stNomLote=".$_POST['stNomLote'];
            $pgProx .= "&stDtLote=".$_POST['stDtLote'];
            $inSequencia = $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->getSequencia();
            SistemaLegado::alertaAviso($pgForm.'?stAcao='.$stAcao, $_POST['inCodLote']." - ".$inSequencia, "alterar", "aviso", Sessao::getId(), "../");
        } else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    break;
    case "incluir":
        $obRContabilidadeLancamentoReceitaAux = new RContabilidadeLancamentoReceita;
        $obRContabilidadeLancamentoReceitaAux->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
        $obRContabilidadeLancamentoReceitaAux->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
        $obRContabilidadeLancamentoReceitaAux->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( 'A' );
        $obRContabilidadeLancamentoReceitaAux->obRContabilidadeLancamento->obRContabilidadeLote->listar( $rsLote, 'cod_lote DESC LIMIT 1' );

        if ( $rsLote->getCampo('cod_lote') == $_POST['inCodLote'] ) {
            if ( $rsLote->getCampo("dt_lote") != $_POST['stDtLote'] ) {
                $inCodLote = $rsLote->getCampo('cod_lote')+1;
                SistemaLegado::executaFrameOculto("f.inCodLote.value = '".$inCodLote."';");
                SistemaLegado::exibeAviso("'O lote ".$rsLote->getCampo('cod_lote')." foi utilizado em outra data. Foi selecionado o lote disponível ".$inCodLote."!");
            } else {
                $obErro = $obRContabilidadeLancamentoReceita->incluir();
                if ( !$obErro->ocorreu() ) {
                    $pgProx .= "?".Sessao::getId();
                    $pgProx .= "&inCodEntidade=".$_POST['inCodEntidade'];
                    $pgProx .= "&inCodLote=".$_POST['inCodLote'];
                    $pgProx .= "&stNomLote=".$_POST['stNomLote'];
                    $pgProx .= "&stDtLote=".$_POST['stDtLote'];
                    $inSequencia = $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->getSequencia();
                    SistemaLegado::alertaAviso($pgForm, $_POST['inCodLote']." - ".$inSequencia, "incluir", "aviso", Sessao::getId(), "../");
                } else
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } else {
            $obErro = $obRContabilidadeLancamentoReceita->incluir();
            if ( !$obErro->ocorreu() ) {
                $pgProx .= "?".Sessao::getId();
                $pgProx .= "&inCodEntidade=".$_POST['inCodEntidade'];
                $pgProx .= "&inCodLote=".$_POST['inCodLote'];
                $pgProx .= "&stNomLote=".$_POST['stNomLote'];
                $pgProx .= "&stDtLote=".$_POST['stDtLote'];
                $inSequencia = $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->getSequencia();
                SistemaLegado::alertaAviso($pgForm, $_POST['inCodLote']." - ".$inSequencia, "incluir", "aviso", Sessao::getId(), "../");
            } else
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "excluir":
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->setSequencia( $_GET['inSequencia'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $_GET['inCodLote'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( $_GET['inCodHistorico'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_GET['inCodEntidade'] );
        $obRContabilidadeLancamentoValor->setTipoValor( $_GET['stTipoValor'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( $_GET['stTipo'] );

        $obErro = $obRContabilidadeLancamentoValor->excluir();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso( $pgList."?stAcao=excluir&".$stFiltro, $_GET['inCodLote'] .' - '.$_GET['inSequencia'],"excluir","aviso",Sessao::getId(),"../");
        } else {
            SistemaLegado::alertaAviso( $pgList."?stAcao=excluir&".$stFiltro, urlencode($obErro->getDescricao()), "n_excluir","erro",Sessao::getId(),"../" );
        }
    // */
    break;
}
?>
