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
    * Página de Processamento de Plano de Contas
    * Data de Criação   : 04/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: PREncerrarConta.php 60235 2014-10-07 20:38:22Z arthur $

    * Casos de uso: uc-02.02.02
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaEncerrada.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "EncerrarConta";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$obErro = new Erro;
$obTransacao = new Transacao;
$obTContabilidadePlanoContaEncerrada = new TContabilidadePlanoContaEncerrada();

switch ($stAcao) {
    case 'encerrar':
    
    if (!$obErro->ocorreu()) {
        $obTContabilidadePlanoContaEncerrada->setDado('cod_conta'      , $_REQUEST['inCodConta']       );
        $obTContabilidadePlanoContaEncerrada->setDado('exercicio'      , Sessao::getExercicio()        );
        $obTContabilidadePlanoContaEncerrada->setDado('dt_encerramento', $_REQUEST['stDtEncerramento'] );
        $obTContabilidadePlanoContaEncerrada->setDado('motivo'         , $_REQUEST['stMotivo']         );
        $obErro = $obTContabilidadePlanoContaEncerrada->inclusao( $obTransacao );

        $stMensagem = $_REQUEST['stCodClass']." - ".$_REQUEST['stDescricaoConta'];
        if ( !empty($_REQUEST['inCodPlano']) )
            $stMensagem = $_REQUEST['inCodPlano']." - ".$stMensagem;
        
        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgFilt.'?stAcao=encerrar', $stMensagem, 'incluir', 'aviso', Sessao::getId(), '../');
        }else{
            SistemaLegado::alertaAviso($pgForm.'?stAcao=encerrar', $obErro->getDescricao(), 'n_incluir', 'erro', Sessao::getId(), '../');
        }
    } else {
        
    }
    break;

    case 'excluir':
        
    if (!$obErro->ocorreu()) {
        $obTContabilidadePlanoContaEncerrada->setDado('cod_conta'      , $_REQUEST['inCodConta']       );
        $obTContabilidadePlanoContaEncerrada->setDado('exercicio'      , Sessao::getExercicio()        );
        $obErro = $obTContabilidadePlanoContaEncerrada->exclusao( $obTransacao );

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgFilt.'?stAcao=excluir', $_GET['stCodEstrutural'].' - '.$_GET['stNomConta'], 'excluir', 'aviso', Sessao::getId(), '../');
        }else{
            SistemaLegado::alertaAviso($pgForm.'?stAcao=excluir', $obErro->getDescricao(), 'n_incluir', 'erro', Sessao::getId(), '../');
        }
    }
    break;
    
}

?>