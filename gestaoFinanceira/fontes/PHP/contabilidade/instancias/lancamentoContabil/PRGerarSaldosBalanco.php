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
    * Página de Processamento para Gerar Saldos de Balanço
    * Data de Criação   : 21/12/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.31
*/

/*
$Log$
Revision 1.2  2006/07/05 20:50:57  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "GerarSaldosBalanco";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$rsContas = $rsSaldo = new recordSet();
$obErro = new Erro;

$obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stAcao = 'incluir';

switch ($stAcao) {
    case "incluir":

        $obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setExercicio    ( Sessao::getExercicio());
        $obErro = $obRContabilidadeLancamentoValor->gerarSaldosBalanco();
        if ( !$obErro->ocorreu() ) {
            if ($obRContabilidadeLancamentoValor->boLogErros) {
                SistemaLegado::alertaAviso($pgFilt."?stAcao=incluir","Geração de Saldos de Balanço Concluida com erros. <a href='".CAM_GF_CONTABILIDADE."/tmp/".$obRContabilidadeLancamentoValor->getNomLogErros()."' target='blank'>Verifique o log.</a>","pagar","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($pgFilt."?stAcao=incluir","Geração de Saldos de Balanço Concluida.","pagar","aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}
?>
