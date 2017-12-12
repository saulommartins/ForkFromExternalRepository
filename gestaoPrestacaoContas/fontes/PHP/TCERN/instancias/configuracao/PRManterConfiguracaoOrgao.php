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
    * Página de Processamento - Parâmetros do Arquivo
    * Data de Criação   : 30/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 25762 $
    $Name$
    $Autor: $
    $Date: 2007-10-02 15:20:03 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTRNConfiguracaoEntidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoOrgao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obErro = new Erro();
$obPersistente = new TTRNConfiguracaoEntidade();

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stAcao = 'incluir';
//echo $stAcao;
foreach ($_POST as $key=>$value) {
    if (strstr($key,"stCodigo")) {
        $arIdentificador = explode('_',$key);
        $inCod = $arIdentificador[1];
        if (trim($value) <> "") {
            $obPersistente->setDado('cod_entidade'  ,$inCod);
            $obPersistente->recuperaPorChave($rsTeste);
            $obPersistente->setDado('valor'         ,$value);
            if($rsTeste->eof())
                $obErro = $obPersistente->inclusao();
            else
                $obErro = $obPersistente->alteracao();
            if( $obErro->ocorreu() )
                break;
        }
    }
}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Dados alterados ", "alterar", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
}

SistemaLegado::LiberaFrames();

?>
