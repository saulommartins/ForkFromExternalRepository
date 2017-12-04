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
* Arquivo de instância para manutenção de atributos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3347 $
$Name$
$Author: pablo $
$Date: 2005-12-05 11:05:04 -0200 (Seg, 05 Dez 2005) $

Casos de uso: uc-01.03.96
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_REGRA."RCadastroDinamico.class.php");
include_once '../../../bibliotecas/mascaras.lib.php';

$stAtributo_1 = 'valor1';
$stAtributo_2 = 'valor2';
/*
for () {
    $obRLicitacao->obRValorDinamico->addDados( array(
                                                     "cod_atributo"    =>"1"
                                                    ,"cod_cadastro"    =>"9"
                                                    ,"exercicio"       =>"2004"
                                                    ,"cod_licitacao"   =>"3"
                                                    ,"valor"           =>"3434343"
                                                    ) );

}

function addDados( $arParametros = array() )
{
}
*/

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtributo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php?".Sessao::getId();
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRRegra = new RCadastroDinamico;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRRegra->setCodCadastro($_POST['inCodCadastro']);
if( is_array($_POST['inCodAtributosSelecionados']) )
    foreach ($_POST['inCodAtributosSelecionados'] as $inCodAtributo) {
        $obRRegra->addAtributosDinamicos( $inCodAtributo );
    }
$obErro = $obRRegra->salvar();

if (!$obErro->ocorreu()) {
    $pgForm .= "acao=$acao&modulo=$modulo";
    alertaAviso($pgForm,"Atributos do cadastro ".$_POST['inCodCadastro'],"incluir","aviso", Sessao::getId(), "../");
} else {
    exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

?>
