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
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

set_time_limit(0);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_FRAMEWORK."/request/Request.class.php" );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$request = new Request($_REQUEST);
$stAcao = $request->get('stAcao');
$acao = ($request->get('acao')!='') ? "&acao=".$request->get('acao'): '';
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

echo "\n";
$stFiltroRelatorio = Sessao::read('filtroRelatorio');
if ( $request->get("boCtrl") == "true"  ) {
    $stArquivoPrincipal = "BarraProgresso.php?".Sessao::getId().$acao;
    $stArquivoOculto = $stFiltroRelatorio["stCaminho"]."?".Sessao::getId();
    if ($_REQUEST["stFilaImpressao"]) {
        $stFiltroRelatorio["stFilaImpressao"] = $_REQUEST["stFilaImpressao"];
    }
    if ($_REQUEST["inNumCopias"]) {
        $stFiltroRelatorio["inNumCopias"] = $_REQUEST["inNumCopias"];
    } else {
        $stFiltroRelatorio["inNumCopias"] = 1;
    }
} else {
    $stArquivoPrincipal = "FMImpressora.php?".Sessao::getId().$acao;
    $stArquivoOculto = "";
}
Sessao::write('filtroRelatorio',$stFiltroRelatorio);

?>
<frameset rows="100%,0%">
    <frame name="telaPrincipalRelatorio" marginwidth="0" marginheight="0" src="<?=$stArquivoPrincipal;?>">
    <frame name="ocultoRelatorio" marginwidth="0" marginheight="0" src="<?=$stArquivoOculto;?>">
</frameset>
