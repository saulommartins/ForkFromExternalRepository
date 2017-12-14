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

    * Página de Processamento do Objeto
    * Data de Criação   : 04/07/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis
    * @ignore

    * $Id: PRManterManutencaoParticipante.php 57380 2014-02-28 17:45:35Z diogo.zarpelon $

    * Casos de uso: uc-03.04.07

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(TLIC."TLicitacaoParticipante.class.php");
include(TLIC."TLicitacaoParticipanteConsorcio.class.php");

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterRenunciaRecurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obErro = new Erro;
$boFlagTransacao = false;

$obTransacao = new Transacao;
$obTransacao->begin();
$boTransacao = $obTransacao->getTransacao();
$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

$obTLicitacaoParticipante = new TLicitacaoParticipante;

$arPart = Sessao::read('part');

if ( count($arPart) <= 0) {
    $obErro->setDescricao("Pelo menos um participante deve ser informado!");
}
       
if (!$obErro->ocorreu()) {
    
    $codLicitacao  = Sessao::read('cod_licitacao');
    $codEntidade   = Sessao::read('cod_entidade');
    $codModalidade = Sessao::read('cod_modalidade');
    $stExercicio   = Sessao::read('exercicio');
    
    $arRequest = $_REQUEST;
    $i = 1;
    
    //agora atualiza o campo renuncia_recurso dos participantes
    foreach ($arPart as $partAux) {
        $obTLicitacaoParticipante->setDado('cod_licitacao', $codLicitacao);
        $obTLicitacaoParticipante->setDado('cgm_fornecedor', $partAux['cgmParticipante']);
        $obTLicitacaoParticipante->setDado('cod_modalidade', $codModalidade);
        $obTLicitacaoParticipante->setDado('cod_entidade', $codEntidade);
        $obTLicitacaoParticipante->setDado('numcgm_representante', $partAux['cgmRepLegal']);
        $obTLicitacaoParticipante->setDado('dt_inclusao',addSlashes($partAux['dataInclusao']));
        $obTLicitacaoParticipante->setDado('exercicio',$stExercicio);
        
        if ($arRequest['boPermissao_' . $partAux['cgmParticipante'] . '_' . $i]){
            if ($arRequest['boPermissao_' . $partAux['cgmParticipante'] . '_' . $i] == 'on'){
                $obTLicitacaoParticipante->setDado('renuncia_recurso',true);
            }else {
                $obTLicitacaoParticipante->setDado('renuncia_recurso',false);
            }
        } else {
            $obTLicitacaoParticipante->setDado('renuncia_recurso',false);
        }
        
        $obErro = $obTLicitacaoParticipante->alteracao($boTransacao);
        
        $i++;
    }
}

$obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTLicitacaoParticipante);
    
if (!$obErro->ocorreu()) {
  SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),"Renúncia ao Prazo de Recurso foi concluída!","aviso","aviso", Sessao::getId(), "../");
} else {
  SistemaLegado::exibeAviso(urlencode($obErro),"n_incluir","erro");
}

?>
