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
    * Página de Processamento dos Parâmetros do Arquivo de Relacionamento das Receitas
    * Data de Criação   : 12/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.09
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:12  hboaventura
Ticket#10234#

Revision 1.6  2006/07/05 20:46:20  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCERJ_NEGOCIO."RExportacaoTCERJRelReceita.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterRelacaoReceita";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Percorre os resultados do formulaáio adicionando as contas que possuem o campo "Receita ExportacaoTCE" informado
$obRegra = new RExportacaoTCERJRelReceita();
$cont=0;

foreach ($_POST as $key=>$value) {
    if ( strstr( $key , "inEstruturalTce" ) ) {
        if ($value<>"") {
            $arContas = explode( "_" , $key );
            $boLancamento = 'boLancamento_'.$arContas[3];
            if($$boLancamento=="t") $boLancamento = "True";
            if($$boLancamento=="f") $boLancamento = "False";
            $obRegra->addConta();
            $obRegra->roUltimaConta->setEstruturalExportacaoTCE   ( $value );
            $obRegra->roUltimaConta->setExercicio       ( $arContas[1] );
            $obRegra->roUltimaConta->setCodigoConta     ( $arContas[2] );
            $obRegra->roUltimaConta->setLancamento      ( $boLancamento );
            $cont++;
        }
    }
}

$obErro = $obRegra->salvarRelacionamento() ;
if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " Contas incluídas/alteradas ", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

?>
