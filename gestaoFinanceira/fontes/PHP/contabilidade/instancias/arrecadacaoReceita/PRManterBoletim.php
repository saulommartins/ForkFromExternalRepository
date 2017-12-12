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
    * Página de Processamento de Evento
    * Data de Criação   : 13/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    * $Id: PRManterBoletim.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.17

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceitaBoletim.class.php";

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterBoletim";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra  = new RContabilidadeLancamentoReceitaBoletim;

$stFiltro = "&pos=".Sessao::read('pos');
$stFiltro .= "&pg=".Sessao::read('pg');
$stFiltro .= "&paginando=".Sessao::read('paginando');
$filtro = Sessao::read('filtro');
if ($filtro) {
    foreach ($filtro as $stCampo => $stValor) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

switch ($stAcao) {

    case "excluir":
        $obRegra->setDataInicial    ( $_REQUEST['dtLote'] );
        $obRegra->setDataFinal      ( $_REQUEST['dtLote'] );
        $obRegra->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio(Sessao::getExercicio());
        $obRegra->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote($_REQUEST['inNumeroBoletim']);
        $obRegra->obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade ($_REQUEST['inCodEntidade']);
        $obErro = $obRegra->excluirBoletim();
        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgList.$stFiltro,"Data do Boletim: ".$obRegra->getDataInicial(),"excluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::alertaAviso($pgList,urlencode($obRegra->getDataInicial()),"n_excluir","erro", Sessao::getId(), "../");
    break;
}

?>
