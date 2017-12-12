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
    * Página de Processamento dos Parâmetros do Arquivo de Relacionamento de Tipo de Alteracao
    * Data de Criação   : 12/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.13
*/

/*
$Log$
Revision 1.8  2006/07/05 20:46:20  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EXP_NEGOCIO."RExportacaoTCERJRelTipoAlteracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterRelacaoTipoAlteracao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Percorre os resultados do formulario adicionando os tipos de alteracao que possuem o campo "Tipo Alteracao ExportacaoTCE" informado
$obRegra = new RExportacaoTCERJRelTipoAlteracao();
$cont=0;

foreach ($_POST as $key=>$value) {
    if ( strstr( $key , "inCodigoTipoAlteracao" ) ) {
        if ($value<>"") {
            $arTipoAlteracao = explode( "_" , $key );
            if($arTipoAlteracao[3]=='') $arTipoAlteracao[3]='S';
            $obRegra->addTipoAlteracao();
            $obRegra->roUltimoTipoAlteracao->setCodigoTipoAlteracao( $value );
            $obRegra->roUltimoTipoAlteracao->setExercicio          ( $arTipoAlteracao[1] );
            $obRegra->roUltimoTipoAlteracao->setCodigoTipo         ( $arTipoAlteracao[2] );
            $obRegra->roUltimoTipoAlteracao->setTipo               ( $arTipoAlteracao[3] );
            $cont++;
        }
    }
}

$obErro = $obRegra->salvarRelacionamento() ;
if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " Tipos de Alteração incluídos/alterados ", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

?>
