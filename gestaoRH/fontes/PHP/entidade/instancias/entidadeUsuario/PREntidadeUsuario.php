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
    * Página de Formulário do Estagiário
    * Data de Criação: 14/06/2007

    * @author Analista: Diego Lemos de Souza
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30674 $
    $Name$
    $Author: souzadl $
    $Date: 2007-09-26 14:25:27 -0300 (Qua, 26 Set 2007) $

    * Casos de uso: uc-04.00.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "EntidadeUsuario";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao(true);

$inCodEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura",8,Sessao::getExercicio());
$inCodEntidade = $_POST["inCodEntidade"];

include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
$obTEntidade = new TEntidade();
$stFiltro  = " AND entidade.exercicio = '".Sessao::getExercicio()."'";
$stFiltro .= " AND entidade.cod_entidade = ".$inCodEntidade;
$stFiltro .= " AND usuario_entidade.numcgm = ".Sessao::read('numCgm');
$obTEntidade->recuperaEntidadesUsuarios($rsEntidades,$stFiltro);

$arSchemasRH = array();
$obTEntidade->recuperaSchemasRH($rsSchemasRH);
while (!$rsSchemasRH->eof()) {
    $arSchemasRH[] = $rsSchemasRH->getCampo("schema_nome");
    $rsSchemasRH->proximo();
}
Sessao::write('arSchemasRH',$arSchemasRH,true);

if ($inCodEntidadePrefeitura == $inCodEntidade) {
    $stMensagem = "A entidade ".$rsEntidades->getCampo("nom_cgm")." foi selecionada para trabalho.";
    Sessao::setEntidade("");
} else {
    include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
    $obTEntidade = new TEntidade();
    $stFiltro = " WHERE nspname = 'pessoal_".$inCodEntidade."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquemas,$stFiltro);
    //Usuários normais
    if ($rsEsquemas->getNumLinhas() == -1) {
        $stMensagem = "A entidade ".$rsEntidades->getCampo("nom_cgm")." não possui uma estrutura de banco de dados criada, entre em contato com o administrador do sistema e solicite a criação dessa estrutura no seguinte caminho: Gestão Administrativa :: Administração :: Sistema :: Incluir Entidade em Gestão RH.";
        Sessao::write("boEntidade",false);
    } else {
        $stMensagem = "A entidade ".$rsEntidades->getCampo("nom_cgm")." foi selecionada para trabalho.";
        Sessao::setEntidade($inCodEntidade);
    }
}

Sessao::encerraExcecao();
SistemaLegado::LiberaFrames(true,false);
sistemaLegado::exibeAviso(urlencode($stMensagem),'','');
$stLink = CAM_FW_INSTANCIAS."index/menu.php?".Sessao::getId()."&nivel=1&cod_gestao_pass=".$_POST["inCodGestao"]."&stTitulo=".$_POST["stTitulo"]."&stVersao=".$_POST["stVersao"];
sistemaLegado::executaFrameOculto("parent.frames['telaMenu'].location.replace('".$stLink."');");

?>
