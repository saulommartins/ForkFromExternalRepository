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
    * Pagina de processamento para Empenho - Ordem de Pagamento
    * Data de Criação   : 17/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.05, uc-02.03.23
*/

/*
$Log$
Revision 1.6  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoPagamentoLiquidacao.class.php" );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$stLink = "&pg=".Sessao::read('pg')."&pos=".Sessao::read('pos')."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma      = "GerarPagamento";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
//include_once( $pgJs );

$obREmpenhoPagamentoLiquidacao = new REmpenhoPagamentoLiquidacao;

$stAcao = "pagar";

switch ($stAcao) {
    case "pagar":
        $obREmpenhoPagamentoLiquidacao->setDataInicial( $_REQUEST["stDataInicial"] ) ;
        $obREmpenhoPagamentoLiquidacao->setDataFinal  ( $_REQUEST["stDataFinal"] ) ;

        $obErro = $obREmpenhoPagamentoLiquidacao->pagar();

        if ( !$obErro->ocorreu() ) {
            if ($obREmpenhoPagamentoLiquidacao->boLogErros) {
                SistemaLegado::alertaAviso($pgFilt."?stAcao=pagar","Processamento concluído com erros. <a href='../../tmp/".$obREmpenhoPagamentoLiquidacao->getNomLogErros()."' target='blank'>Verifique o log.</a>","pagar","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($pgFilt."?stAcao=pagar","Processamento concluído.","pagar","aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"pagar","erro");
        }
    break;
}

?>
