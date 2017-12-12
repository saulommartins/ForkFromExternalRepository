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

    * Página de Formulario de Ajustes Gerais Exportacao - TCE-AL
    * Data de Criação   : 11/07/2006

    * @author Analista: 
    * @author Desenvolvedor: 

    * @ignore

    * $Revision: 57368 $
    * $Name$
    * $Author: diogo.zarpelon $
    * $Date: 2014-02-28 14:23:28 -0300 (Fri, 28 Feb 2014) $
    
    * $id:  

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCEAL_MAPEAMENTO."TTCEALExportacaoConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterParametrosGerais";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get("stAcao","configuracao");

$obTExportacao = new TTCEALExportacaoConfiguracao();

$obErro = new Erro;

$obTransacao = new Transacao;
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

$obTExportacao->setDado("cod_modulo",62);
$obTExportacao->setDado("exercicio",  Sessao::getExercicio());

foreach ($request->getAll() as $chave => $valor) {
    $arRegistro = explode("_",$chave);

    if ($arRegistro[0] == "tceal") {
        $obTExportacao->setDado( "parametro", $arRegistro[0]."_".$arRegistro[1] );
        $obTExportacao->setDado( "cod_entidade", $arRegistro[2] );

        $obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );

        if (!$obErro->ocorreu()) {
            $obTExportacao->setDado( "valor", $valor );
            if ($rsRecordSet->getNumLinhas() > 0) {
                $obErro = $obTExportacao->alteracao( $boTransacao );
            } else {
                $obErro = $obTExportacao->inclusao( $boTransacao );
            }
        }
    }
}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm,"parâmetros atualizados", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}


$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTExportacao );

?>
