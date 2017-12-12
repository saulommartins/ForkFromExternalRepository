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
    * Página de Processamento de Implantação de saldo da Tesouraria
    * Data de Criação   : 20/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31194 $
    $Name$
    $Author: grasiele $
    $Date: 2008-03-20 12:09:41 -0300 (Qui, 20 Mar 2008) $

    * Casos de uso: uc-02.04.22
*/

/*
$Log$
Revision 1.6  2007/07/26 23:38:40  cako
Bug#9478#

Revision 1.5  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaSaldoTesouraria.class.php"                              );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ImplantarSaldo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stNow = date( 'Y-m-d H:i:s.ms' );

$obErro = new Erro();

$obRegra = new RTesourariaSaldoTesouraria();

if ($stAcao == "incluir") {

    $obRegra->obRContabilidadePlanoBanco->setCodPlano ( $_REQUEST["inCodConta"] );
    $obRegra->obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio()        );
    $obRegra->obRContabilidadePlanoBanco->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRegra->obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade ( $_REQUEST["inCodigoEntidade"]);
    $obRegra->setVlSaldo( $_REQUEST['nuValor'] );

    $obErro = $obRegra->salvar(false);

    if ( !$obErro->ocorreu() )
        SistemaLegado::alertaAviso($pgForm."?inCodEntidade=".$_REQUEST["inCodigoEntidade"],$_REQUEST["inCodConta"] . "/" .Sessao::getExercicio(),$stAcao,"aviso", Sessao::getId()."&stAcao=".$stAcao, "../");
    else
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");

}

?>
