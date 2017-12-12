<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos (urbem@cnm.org.br)      *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo  sob *
    * os termos da Licença Pública Geral GNU conforme publicada pela  Free  Software *
    * Foundation; tanto a versão 2 da Licença, como (a seu critério) qualquer versão *
    * posterior.                                                                     *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral  do  GNU  junto  com *
    * este programa; se não, escreva para  a  Free  Software  Foundation,  Inc.,  no *
    * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.               *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de Processamento de Gerar Exercicio Seguinte
    * Data de Criação   : 25/07/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 32625 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoGerarExercicioSeguinte.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDO.class.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPA.class.php';
include_once CAM_GF_LDO_NEGOCIO.'RLDOValidarAcao.class.php';
include_once CAM_GF_LDO_VISAO.'VLDOValidarAcao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'GerarExercicioSeguinte';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';

$obROrcamentoGerarExercicioSeguinte = new ROrcamentoGerarExercicioSeguinte;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$boTransacao = new Transacao();

switch ($stAcao) {
case 'incluir':
    foreach ($_POST as $stNomVar => $stValor) {
        if ($stValor == 'S') {
            $arValoresSelecionado[] = $stNomVar;
        }
    }
    if ($_POST['stReceita'] == 'SS') {
        $arValoresSelecionado[] = 'stReceita';
        $arValoresSelecionado[] = 'stZerarReceita';
    }
    if ($_POST['stDespesa'] == 'SS') {
        $arValoresSelecionado[] = 'stDespesa';
        $arValoresSelecionado[] = 'stZerarDespesa';
    }

    ///Só deverão ser copiadas as despesas das ações que estão validadas para o exercício que está sendo gerado o orçamento. E a LDO deve estar homologada também.
    $stValor = ' { ';
    $boValidaLDO = false;
    if (is_array($arValoresSelecionado)) {
        foreach ($arValoresSelecionado as $stNomValorOriginal) {
            switch ($stNomValorOriginal) {
                //Colocar essa mesma validação, quando forem selecionadas as opções Copiar Despesas com Valores Orçados e Copiar Despesas sem Valores Orçados
                case 'stReceita': 
                    $stNomValor  = 'receita';                     
                break;
                case 'stZerarReceita': 
                    $stNomValor = 'zerar_receita';
                break;
                case 'stDespesa': 
                    $stNomValor  = 'despesa'; 
                    $boValidaLDO = true; 
                break;
                case 'stZerarDespesa': 
                    $stNomValor = 'zerar_despesa';
                break;                
                case 'stMetasArrecadacao': 
                    $stNomValor = 'metas_arrecadacao';
                break;
                case 'stMetasExecucaoDespesa': 
                    $stNomValor = 'metas_execucao_despesa';
                break;
            }
            $stValor .= $stNomValor.', ';
        }
        $stValor = ($stValor) ? substr($stValor,0,strlen($stValor)-2) : ' { ';
    }
    $stValor .= ' } ';
    $obROrcamentoGerarExercicioSeguinte->setExercicio         (Sessao::getExercicio());
    $obROrcamentoGerarExercicioSeguinte->setOpcoesSelecionadas($stValor);
    $stFiltro = "";

    $rsPPA  = new RecordSet;
    $obErro = new Erro;
    $obROrcamentoGerarExercicioSeguinte->verificaPPA($rsPPA);

    if ($rsPPA->getNumLinhas() > 0) {
        //Nessa rotina as ações só são demonstradas para inclusão de despesa, se estiverem Validadas na LDO se a LDO estiver homologada
        if ( $boValidaLDO ) {
            $rsLista = new RecordSet;
            $obTLDO = new TLDO();
            $obTLDO->setDado('exercicio', (Sessao::getExercicio()+1));
            $obTLDO->setDado('homologado', true);
            $obTLDO->recuperaExerciciosLDO($rsExerciciosLDO);
        
            if ($rsExerciciosLDO->getNumLinhas() > -1) {                
                $obModel = new RLDOValidarAcao();
                $obView  = new VLDOValidarAcao($obModel);
                $arParametros['inCodPPA']       = $rsExerciciosLDO->getCampo('cod_ppa');
                $arParametros['slExercicioLDO'] = $rsExerciciosLDO->getCampo('ano');
                $stOrder = ' ORDER BY acao.num_acao';
                $obView->listAcaoDespesa($rsLista, $arParametros, $stOrder);
                
                if ($rsLista->getNumLinhas() > 0) {
                    $stFiltro = " WHERE ppa.cod_ppa = ".$rsExerciciosLDO->getCampo('cod_ppa')."                                    
                                    AND ppa.fn_verifica_homologacao(ppa.cod_ppa)
                                    AND acao_validada.ano::integer = ".$rsExerciciosLDO->getCampo('ano')."
                                ";                    
                    $obErro = $obROrcamentoGerarExercicioSeguinte->incluir($rsRecordSet, $stFiltro, $boTransacao);
                }
            }else{
                $obErro->setDescricao('Não há LDO Homologada para o exercício '.(Sessao::getExercicio()+1).'!');
            }
        }else{
            $obErro = $obROrcamentoGerarExercicioSeguinte->incluir($rsRecordSet, $stFiltro, $boTransacao);
        }
    } else {
        $obErro->setDescricao('Não existe PPA para o exercício a ser gerado!');
    }

    if (!$obErro->ocorreu()) {
        if ($rsRecordSet->getCampo("stretorno") == 't') {
            SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId().'&stAcao='.$stAcao, (Sessao::getExercicio()+1), 'incluir', 'aviso', Sessao::getId(), '../');
        } else {
            SistemaLegado::exibeAviso('Orçamento para Exercício Seguinte já foi gerado!', 'n_incluir', 'erro');
        }
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), 'n_incluir', 'erro');
    }
    SistemaLegado::LiberaFrames();
break;
}
?>