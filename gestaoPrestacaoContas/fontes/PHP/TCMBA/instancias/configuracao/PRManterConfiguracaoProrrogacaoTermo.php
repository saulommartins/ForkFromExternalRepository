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
  * Página de Processamento de Configuração de Prorrogação de Termo Parceria/Subvenção/OSCIP
  * Data de Criação: 21/10/2015

  * @author Analista: 
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: PRManterConfiguracaoProrrogacaoTermo.php 63828 2015-10-21 20:04:39Z franver $
  * $Revision: 63828 $
  * $Author: franver $
  * $Date: 2015-10-21 18:04:39 -0200 (Wed, 21 Oct 2015) $
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATermoParceriaProrrogacao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoProrrogacaoTermo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obErro = new Erro();
$obTransacao = new Transacao();
$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

if($request->get('stAcao') == 'configurar'){
    $arProrrogacoes = Sessao::read("arProrrogacoes");

    if(!is_array($arProrrogacoes)) {
        $arProrrogacoes = array();
    }
    
    if(count($arProrrogacoes) > 0 && !$obErro->ocorreu()){
        /**
         * REMOVE TODAS AS PRORROGAÇÔES DO TERMO DE PARCERIA
         */
        $obTTCMBATermoParceriaProrrogacao = new TTCMBATermoParceriaProrrogacao();
        $obTTCMBATermoParceriaProrrogacao->setDado('exercicio'   , $request->get('stExercicioProcesso'));
        $obTTCMBATermoParceriaProrrogacao->setDado('cod_entidade', $request->get('inCodEntidade'));
        $obTTCMBATermoParceriaProrrogacao->setDado('nro_processo', trim($request->get('stNumeroProcesso')));
        $obErro = $obTTCMBATermoParceriaProrrogacao->recuperaPorChave($rsTermoParceriaProrrogacao, $boTransacao);
        
        if(!$obErro->ocorreu() && $rsTermoParceriaProrrogacao->getNumLinhas() > 0){
            $obErro = $obTTCMBATermoParceriaProrrogacao->exclusao($boTransacao);
        }
    
        /**
         * INCLUINDO TODAS AS PRORROGAÇÔES DO TERMO DE PARCERIA
         */
        foreach($arProrrogacoes AS $arProrrogacao){
            $obTTCMBATermoParceriaProrrogacao->setDado('nro_termo_aditivo'     , $arProrrogacao['numeroTermoAditivo']);
            $obTTCMBATermoParceriaProrrogacao->setDado('exercicio_aditivo'     , $arProrrogacao['exercicioTermoAditivo']);
            $obTTCMBATermoParceriaProrrogacao->setDado('dt_prorrogacao'        , $arProrrogacao['dataProrrogacao']);
            $obTTCMBATermoParceriaProrrogacao->setDado('dt_publicacao'         , $arProrrogacao['dataPublicacao']);
            $obTTCMBATermoParceriaProrrogacao->setDado('imprensa_oficial'      , $arProrrogacao['imprensaOficial']);
            $obTTCMBATermoParceriaProrrogacao->setDado('indicador_adimplemento', $arProrrogacao['boIndicadorAdimplemento']);
            $obTTCMBATermoParceriaProrrogacao->setDado('dt_inicio'             , $arProrrogacao['dataInicio']);
            $obTTCMBATermoParceriaProrrogacao->setDado('dt_termino'            , $arProrrogacao['dataTermino']);
            $obTTCMBATermoParceriaProrrogacao->setDado('vl_prorrogacao'        , $arProrrogacao['valorProrrogacao']);
            $obErro = $obTTCMBATermoParceriaProrrogacao->inclusao($boTransacao);

            if($obErro->ocorreu())
                break;
        }
    }
    
    if(!$obErro->ocorreu()){
        SistemaLegado::alertaAviso($pgFilt."?stAcao=".$request->get('stAcao')."&inCodEntidade=".$request->get('inCodEntidade'), $request->get('stNumeroProcesso').'/'.$request->get('stExercicioProcesso'), 'incluir', "aviso", Sessao::getId(), "../");
        Sessao::remove("arProrrogacoes");
        
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTTCMBATermoParceria);
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }

}
?>