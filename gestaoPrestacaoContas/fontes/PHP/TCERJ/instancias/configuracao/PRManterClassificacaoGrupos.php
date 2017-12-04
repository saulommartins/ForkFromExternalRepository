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
    * Página de Processamento de Configuração para Relatórios MODELOS
    * Data de Criação   : 22/05/2006

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 59612 $
    $Name$
    $Autor:$
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.02.02

*/

/*
$Log$
Revision 1.7  2006/07/06 13:52:24  diego
Retirada tag de log com erro.

Revision 1.6  2006/07/06 12:42:06  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TCRJ."TCRJClassificacaoGrupoPatrimonio.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterClassificacaoGrupos";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );

$obTClassificacaoGrupoPatrimonio = new TCRJClassificacaoGrupoPatrimonio();
Sessao::getTransacao()->setMapeamento( $obTClassificacaoGrupoPatrimonio );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {

    default:
       if ($_REQUEST['stSiglaClassificacao'])
           $obTClassificacaoGrupoPatrimonio->recuperaTodos($rsClassificacaoGrupoPatrimonio," WHERE sigla = '".$_REQUEST['stSiglaClassificacao']."' " );
        if ($rsClassificacaoGrupoPatrimonio->eof()) {
            if ($_REQUEST['inCodNatureza'] && $_REQUEST['inCodGrupo'] && $_REQUEST['stSiglaClassificacao']) {
                $obTClassificacaoGrupoPatrimonio->setDado("cod_natureza" ,$_REQUEST['inCodNatureza']);
                $obTClassificacaoGrupoPatrimonio->setDado("cod_grupo"    ,$_REQUEST['inCodGrupo']);
                $obTClassificacaoGrupoPatrimonio->setDado("sigla"        ,$_REQUEST['stSiglaClassificacao']);
                $obTClassificacaoGrupoPatrimonio->recuperaPorChave($rsClassificacaoGrupoDados);
                $rsClassificacaoGrupoDados->eof() ? $obTClassificacaoGrupoPatrimonio->inclusao() : $obTClassificacaoGrupoPatrimonio->alteracao();
            }

        }
        SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=$stAcao", "incluir", "aviso", Sessao::getId(), "../");
    break;
}

Sessao::encerraExcecao();

?>
