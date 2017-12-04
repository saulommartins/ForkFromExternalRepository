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
  * Página de Processamento de Configuração de Termos de Parceria/Subvenção/OSCIP
  * Data de Criação: 21/10/2015

  * @author Analista: 
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: PRManterConfiguracaoParcSubvOSCIP.php 64410 2016-02-18 12:13:33Z lisiane $
  * $Revision: 64410 $
  * $Author: lisiane $
  * $Date: 2016-02-18 10:13:33 -0200 (Thu, 18 Feb 2016) $
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATermoParceria.class.php';
require_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATermoParceriaDotacao.class.php';
require_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATermoParceriaProrrogacao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoParcSubvOSCIP";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obErro = new Erro();
$obTransacao = new Transacao();
$boFlagTransacao = false;
$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

if( $request->get('stAcao') == 'configurar' ){
    $rsTermoParcerias = new RecordSet();
    $rsTermoParceriaDotacao = new RecordSet();
    $stExercicioProcesso = ($request->get('stExercicioProcesso') == "") ? $request->get('hdnExercicioProcesso') : $request->get('stExercicioProcesso');
    $arDotacoes = Sessao::read("arDotacoes");

    $obTTCMBATermoParceriaDotacao = new TTCMBATermoParceriaDotacao();

    if(count($arDotacoes) > 0 && !$obErro->ocorreu()){
        /**
           Verifica se houve alteração na chave do numero de processo.
           Ocorrendo alteração, remove a dotação do termo de parceria assim como o termo parceria para inserir sua alteração
           Isso ocorre APENAS quando o numero de processo muda
        */

        $arTermoParcerias = Sessao::read('arTermoParcerias');
        
        if (is_array($arTermoParcerias))  {
            foreach ($arTermoParcerias as $dados) {
                if ($dados['nro_processo'] == $request->get('stHdnNumeroProcessoAnterior')) {
                    if ( trim($request->get('stHdnNumeroProcessoAnterior')) != trim($request->get('stNumeroProcesso')) ) {
                        $stNumeroProcesso = trim($request->get('stHdnNumeroProcessoAnterior'));
                    } else {
                        $stNumeroProcesso = trim($request->get('stNumeroProcesso'));
                    }
    
                    $obTTCMBATermoParceriaDotacao->setDado('exercicio'   , $stExercicioProcesso );
                    $obTTCMBATermoParceriaDotacao->setDado('cod_entidade', $request->get('inCodEntidade') );
                    $obTTCMBATermoParceriaDotacao->setDado('nro_processo', $stNumeroProcesso );
                    $obErro = $obTTCMBATermoParceriaDotacao->recuperaPorChave($rsTermoParceriaDotacao,$boTransacao);
    
                    if (!$obErro->ocorreu() && $rsTermoParceriaDotacao->getNumLinhas() > 0) {
                        $obErro = $obTTCMBATermoParceriaDotacao->exclusao($boTransacao);            
                    }
    
                    if ( !$obErro->ocorreu() ) {
                        $obTTCMBATermoParceria = new TTCMBATermoParceria();
                        $obTTCMBATermoParceria->setDado('exercicio'    , $stExercicioProcesso );
                        $obTTCMBATermoParceria->setDado('cod_entidade' , $request->get('inCodEntidade') );
                        $obTTCMBATermoParceria->setDado('nro_processo' , $stNumeroProcesso );
                        $obErro = $obTTCMBATermoParceria->recuperaPorChave($rsTermoParceriaAnterior,$boTransacao);
    
                        if (!$obErro->ocorreu() && $rsTermoParceriaAnterior->getNumLinhas() > 0) {
                            $obErro = $obTTCMBATermoParceria->exclusao($boTransacao);
                        }
                    }
                }
            }
        }
    } else {
        $obErro->setDescricao("É necessário preencher ao menos uma Dotação, para o Termo de Parceria.");
    }

    if( !$obErro->ocorreu() ){

        // Insere os novos dados ou altera existentes

        $obTTCMBATermoParceria = new TTCMBATermoParceria();   
        $obTTCMBATermoParceria->setDado('exercicio'           , $stExercicioProcesso);
        $obTTCMBATermoParceria->setDado('cod_entidade'        , $request->get('inCodEntidade'));
        $obTTCMBATermoParceria->setDado('nro_processo'        , trim($request->get('stNumeroProcesso')));
        $obTTCMBATermoParceria->setDado('dt_assinatura'       , $request->get('stDtAssinatura'));
        $obTTCMBATermoParceria->setDado('dt_publicacao'       , $request->get('stDtPublicacao'));
        $obTTCMBATermoParceria->setDado('imprensa_oficial'    , $request->get('stImprensaOficial'));
        $obTTCMBATermoParceria->setDado('dt_inicio'           , $request->get('stDtInicioTermo'));
        $obTTCMBATermoParceria->setDado('dt_termino'          , $request->get('stDtTerminoTermo'));
        $obTTCMBATermoParceria->setDado('numcgm'              , $request->get('inCGMParceria'));
        $obTTCMBATermoParceria->setDado('processo_licitatorio', $request->get('stProcessoLicitatorio'));
        $obTTCMBATermoParceria->setDado('processo_dispensa'   , $request->get('stProcessoDispensa'));
        $obTTCMBATermoParceria->setDado('objeto'              , $request->get('txtObjeto'));
        $obTTCMBATermoParceria->setDado('nro_processo_mj'     , $request->get('stProcessoMJ'));
        $obTTCMBATermoParceria->setDado('dt_processo_mj'      , $request->get('dtProcessoMJ'));
        $obTTCMBATermoParceria->setDado('dt_publicacao_mj'    , $request->get('dtPublicacaoMJ'));
        $obTTCMBATermoParceria->setDado('vl_parceiro_publico' , $request->get('vlParceiroPublico'));
        $obTTCMBATermoParceria->setDado('vl_termo_parceria'   , $request->get('vlParceiroOSCIP'));
        $obErro = $obTTCMBATermoParceria->recuperaPorChave($rsTermoParcerias,$boTransacao);

        if( $rsTermoParcerias->getNumLinhas() < 0 && !$obErro->ocorreu() ){
            $obErro = $obTTCMBATermoParceria->inclusao($boTransacao);        
        } else {
            $obErro = $obTTCMBATermoParceria->alteracao($boTransacao);    
        }
    }

    if(count($arDotacoes) > 0 && !$obErro->ocorreu()){

        // INCLUINDO TODAS AS DOTAÇÕES DO TERMO DE PARCERIA

        foreach($arDotacoes AS $arDotacao){
            $obTTCMBATermoParceriaDotacao->setDado('exercicio'        , $stExercicioProcesso );
            $obTTCMBATermoParceriaDotacao->setDado('cod_entidade'     , $request->get('inCodEntidade') );
            $obTTCMBATermoParceriaDotacao->setDado('nro_processo'     , $request->get('stNumeroProcesso') );
            $obTTCMBATermoParceriaDotacao->setDado('exercicio_despesa', $arDotacao['exercicio_despesa']);
            $obTTCMBATermoParceriaDotacao->setDado('cod_despesa'      , $arDotacao['cod_despesa']);
            $obErro = $obTTCMBATermoParceriaDotacao->inclusao($boTransacao);

            if($obErro->ocorreu()) {
                break;
            }
        }
    }

    if (!$obErro->ocorreu()) {
        SistemaLegado::alertaAviso($pgForm."?stAcao=".$request->get('stAcao')."&inCodEntidade=".$request->get('inCodEntidade'), $request->get('stNumeroProcesso').'/'.$stExercicioProcesso, 'incluir', "aviso", Sessao::getId(), "../");
        Sessao::remove("arTermoParcerias");
        Sessao::remove("arDotacoes");
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTTCMBATermoParceria);
    } else {
        SistemaLegado::LiberaFrames(true,true);
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }

} else if($request->get('stAcao') == 'excluirTermoParceria') {
    $arTermoParcerias = Sessao::read("arTermoParcerias");
    $stNroProcesso = "";
    if(is_array($arTermoParcerias)) {
        foreach($arTermoParcerias AS $arTermoParceria){
            if($arTermoParceria["inId"] == $request->get('inId')){
                
                $stNroProcesso = trim($arTermoParceria['nro_processo']).'/'.$arTermoParceria['exercicio'];
                
                $obTTCMBATermoParceriaProrrogacao = new TTCMBATermoParceriaProrrogacao();
                $obTTCMBATermoParceriaProrrogacao->setDado('exercicio'   , $arTermoParceria['exercicio']);
                $obTTCMBATermoParceriaProrrogacao->setDado('cod_entidade', $arTermoParceria['cod_entidade']);
                $obTTCMBATermoParceriaProrrogacao->setDado('nro_processo', trim($arTermoParceria['nro_processo']));
                $obErro = $obTTCMBATermoParceriaProrrogacao->recuperaPorChave($rsTermoParceriaProrrogacao, $boTransacao);
                
                if(!$obErro->ocorreu() && $rsTermoParceriaProrrogacao->getNumLinhas() > 0){
                    $obErro->setDescricao("Esse Termo de Parceria já possui Prorrogações cadastradas.");
                }
                
                if(!$obErro->ocorreu()){
                    $obTTCMBATermoParceriaDotacao = new TTCMBATermoParceriaDotacao();
                    $obTTCMBATermoParceriaDotacao->setDado('exercicio'   , $arTermoParceria['exercicio']);
                    $obTTCMBATermoParceriaDotacao->setDado('cod_entidade', $arTermoParceria['cod_entidade']);
                    $obTTCMBATermoParceriaDotacao->setDado('nro_processo', trim($arTermoParceria['nro_processo']));
                    $obErro = $obTTCMBATermoParceriaDotacao->recuperaPorChave($rsTermoParceriaDotacao, $boTransacao);
                }
                
                if(!$obErro->ocorreu() && $rsTermoParceriaDotacao->getNumLinhas() > 0){
                    $obErro = $obTTCMBATermoParceriaDotacao->exclusao($boTransacao);
                }
                
                if(!$obErro->ocorreu()){
                    $obTTCMBATermoParceria = new TTCMBATermoParceria();
                    $obTTCMBATermoParceria->setDado('exercicio'   , $arTermoParceria['exercicio']);
                    $obTTCMBATermoParceria->setDado('cod_entidade', $arTermoParceria['cod_entidade']);
                    $obTTCMBATermoParceria->setDado('nro_processo', trim($arTermoParceria['nro_processo']));
                    $obErro = $obTTCMBATermoParceria->recuperaPorChave($rsTermoParceria, $boTransacao);
                }
                
                if(!$obErro->ocorreu() && $rsTermoParceria->getNumLinhas() > 0){
                    $obErro = $obTTCMBATermoParceria->exclusao($boTransacao);
                }
            }
        }
    }
    if(!$obErro->ocorreu()){
        SistemaLegado::alertaAviso($pgForm."?stAcao=".$request->get('stAcao')."&inCodEntidade=".$request->get('inCodEntidade'), "Exclusao do Termo de Parceria '".$stNroProcesso."'! ", 'excluir', "aviso", Sessao::getId(), "../");
        Sessao::remove("arTermoParcerias");
        Sessao::remove("arDotacoes");
        
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTTCMBATermoParceria);
    
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","sn");
    }
}

?>