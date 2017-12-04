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

    * Pacote de configuração do TCMBA - Subvenções dos Empenhos
    * Data de Criação   : 25/08/2015
    * @author Analista: Valtair Santos 
    * @author Desenvolvedor: Evandro Melos
    * 
    * $id: $
    
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBASubvencaoEmpenho.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoSubvencoesEmpenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$obErro = new Erro();
$boTransacao = new Transacao();
$boTransacao->abreTransacao($boFlagTransacao,$boTransacao);

$obTTCMBASubvencaoEmpenho = new TTCMBASubvencaoEmpenho();
$obTTCMBASubvencaoEmpenho->setDado('numcgm'              , $request->get('inCGMFornecedor')       );
//Verifica se existe dados no banco para aquele CGM
$obErro = $obTTCMBASubvencaoEmpenho->recuperaSubvencaoEmpenho( $rsSuvncaoEmpenho, "", "", $boTransacao );
//Carrega os outros dados no mapeamento
$obTTCMBASubvencaoEmpenho->setDado('dt_inicio'           , $request->get('stDataInicial')         );
$obTTCMBASubvencaoEmpenho->setDado('dt_termino'          , $request->get('stDataFinal')           );
$obTTCMBASubvencaoEmpenho->setDado('prazo_aplicacao'     , $request->get('inPrazoAplicacao')      );
$obTTCMBASubvencaoEmpenho->setDado('prazo_comprovacao'   , $request->get('inPrazoComprovacao')    );
$obTTCMBASubvencaoEmpenho->setDado('cod_norma_utilidade' , $request->get('inCodNormaReconhecida') );
$obTTCMBASubvencaoEmpenho->setDado('cod_norma_valor'     , $request->get('inCodNormaConcedente')  );
$obTTCMBASubvencaoEmpenho->setDado('cod_banco'           , $request->get('inCodBanco')            );
$obTTCMBASubvencaoEmpenho->setDado('cod_agencia'         , $request->get('inCodAgencia')          );
$obTTCMBASubvencaoEmpenho->setDado('cod_conta_corrente'  , $request->get('stContaCorrente')       );    
//Valida a alteracao ou inclusao
if ( $rsSuvncaoEmpenho->getNumLinhas() > 0) {
    $obErro = $obTTCMBASubvencaoEmpenho->alteracao($boTransacao);
    $stMensagem = "Subvenção do Empenho alterada com sucesso!";
    $stMsg = "alterar";
}else{
    $obErro = $obTTCMBASubvencaoEmpenho->inclusao($boTransacao);
    $stMensagem = "Subvenção do Empenho inclusa com sucesso!";
    $stMsg = "incluir";
}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgFilt,$stMensagem,$stMsg,"aviso",Sessao::getId());
    $boTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTTCMBASubvencaoEmpenho);
}else{    
    SistemaLegado::exibeAviso("Não foi possivel completar a ação!","n_incluir","erro");
}

?>