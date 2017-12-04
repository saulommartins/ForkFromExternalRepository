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
    * Página de Listagem de Itens
    * Data de Criação   : 04/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: PRGerarLancamentoCreditoReceber.php 64362 2016-01-26 19:45:10Z michel $

    * Casos de uso: uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoCreditoReceber.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php";


//Define o nome dos arquivos PHP
$stPrograma = "GerarLancamentoCreditoReceber";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";


$rsRecordset = new Recordset;
$obTransacao = new Transacao;

$obTOrcamentoReceita = new TOrcamentoReceita;
$obTOrcamentoReceita->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
$obTOrcamentoReceita->recuperaLancamentosCreditosReceber( $rsLista );

//Inicia nova transação
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

// Limpa registros 2015 Tipo 'I', #23505.
if(Sessao::getExercicio() == '2015'){
    foreach ($rsLista->getElementos() as $registro) {
        $obTContabilidadeLote = new TContabilidadeLote;
        
        $stFiltro = " WHERE lote.cod_entidade = ".$registro["cod_entidade"]."
                       AND lote.exercicio = '".$registro["exercicio"]."'
                       AND lote.dt_lote = '".$registro["exercicio"]."-01-02'
                       AND lote.tipo = 'I'
                       AND lote.nom_lote = 'Previsão de crédito tributário a receber'";
                       
        $obErro = $obTContabilidadeLote->recuperaTodos($rsLote, $stFiltro);
        
        foreach ($rsLote->getElementos() as $lote) {
            $obTContabilidadeLancamento = new TContabilidadeLancamento;
            $obTContabilidadeLancamento->setDado('exercicio', $lote['exercicio']);
            $obTContabilidadeLancamento->setDado('cod_lote', $lote['cod_lote']);
            $obTContabilidadeLancamento->setDado('tipo', 'I');
            $obTContabilidadeLancamento->setDado('cod_entidade', $lote['cod_entidade']);
            $obTContabilidadeLancamento->setDado('cod_historico', 850);
            $obTContabilidadeLancamento->excluiLancamentosAberturaAnteriores($boTransacao);
        }
        
        $obTContabilidadeLote->setDado('exercicio'   , $registro['exercicio']);
        $obTContabilidadeLote->setDado('cod_entidade', $registro['cod_entidade']);
        $obTContabilidadeLote->setDado('tipo'        , 'I');
        $obTContabilidadeLote->setDado('dt_lote'     , $registro['exercicio'].'-01-02');
        $obTContabilidadeLote->setDado('nom_lote'    , 'Previsão de crédito tributário a receber');
        $obErro = $obTContabilidadeLote->excluirLote($boTransacao);
    }
}

// Deleta registros
foreach ($rsLista->getElementos() as $registro) {
    $obTContabilidadeLote = new TContabilidadeLote;
    
    $stFiltro = " WHERE lote.cod_entidade = ".$registro["cod_entidade"]."
                   AND lote.exercicio = '".$registro["exercicio"]."'
                   AND lote.dt_lote = '".$registro["exercicio"]."-01-02'
                   AND lote.tipo = 'M'
                   AND lote.nom_lote = 'Previsão de crédito tributário a receber'";
                   
    $obErro = $obTContabilidadeLote->recuperaTodos($rsLote, $stFiltro);
    
    foreach ($rsLote->getElementos() as $lote) {
        $obTContabilidadeLancamento = new TContabilidadeLancamento;
        $obTContabilidadeLancamento->setDado('exercicio', $lote['exercicio']);
        $obTContabilidadeLancamento->setDado('cod_lote', $lote['cod_lote']);
        $obTContabilidadeLancamento->setDado('tipo', 'M');
        $obTContabilidadeLancamento->setDado('cod_entidade', $lote['cod_entidade']);
        $obTContabilidadeLancamento->setDado('cod_historico', 850);
        $obTContabilidadeLancamento->excluiLancamentosAberturaAnteriores($boTransacao);
    }
    
    $obTContabilidadeLote->setDado('exercicio'   , $registro['exercicio']);
    $obTContabilidadeLote->setDado('cod_entidade', $registro['cod_entidade']);
    $obTContabilidadeLote->setDado('tipo'        , 'M');
    $obTContabilidadeLote->setDado('dt_lote'     , $registro['exercicio'].'-01-02');
    $obTContabilidadeLote->setDado('nom_lote'    , 'Previsão de crédito tributário a receber');
    $obErro = $obTContabilidadeLote->excluirLote($boTransacao);
}

// Insere registros
foreach ($rsLista->getElementos() as $registro) {
    $obTContabilidadeLote = new TContabilidadeLote;
    $obTContabilidadeLote->setDado('exercicio'   , "'".$registro['exercicio']."'");
    $obTContabilidadeLote->setDado('cod_entidade', $registro['cod_entidade']);
    $obTContabilidadeLote->setDado('tipo'    , 'M');
    $obTContabilidadeLote->recuperaUltimoLotePorEntidade($rsRecordset);
    
    $obTContabilidadeLote = new TContabilidadeLote;
    $obTContabilidadeLote->setDado('exercicio'   , $registro['exercicio']);
    $obTContabilidadeLote->setDado('cod_entidade', $registro['cod_entidade']);
    $obTContabilidadeLote->setDado('tipo'        , 'M');
    $obTContabilidadeLote->setDado('cod_lote'    , $rsRecordset->getCampo('cod_lote')+1);
    $obTContabilidadeLote->setDado('dt_lote'     , '02/01/'.$registro['exercicio']);
    $obTContabilidadeLote->setDado('nom_lote'    , 'Previsão de crédito tributário a receber');
    $obErro = $obTContabilidadeLote->inclusao($boTransacao);
    
    $arParam = array(
        'exercicio'           => $registro['exercicio'],
        'cod_plano_deb'       => $registro['cod_plano'],
        'cod_plano_cred'      => $registro['cod_plano_credito'],
        'cod_estrutural_deb'  => $registro['cod_estrutural'],
        'cod_estrutural_cred' => $registro['cod_estrutural_credito'],
        'valor'               => $registro['vl_original'],
        'cod_lote'            => $obTContabilidadeLote->getDado('cod_lote'),
        'cod_entidade'        => $registro['cod_entidade'],
        'cod_historico'       => 850
    );
    
    $obTContabilidadeLancamentoCreditoReceber = new TContabilidadeLancamentoCreditoReceber;
    $obErro = $obTContabilidadeLancamentoCreditoReceber->executaInsereLancamento($arParam, $boTransacao);
}

// Finaliza transação
if(!$obErro->ocorreu()) {
    $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro);
} else {
    SistemaLegado::exibeAviso("Ocorreu um erro ao tentar salvar Lançamentos Contábeis. ","erro","erro");

}

return SistemaLegado::alertaAviso($pgForm, 'Sucesso',"incluir","aviso", Sessao::getId(), "../");
