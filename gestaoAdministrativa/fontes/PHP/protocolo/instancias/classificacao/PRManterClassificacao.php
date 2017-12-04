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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_PROT_MAPEAMENTO."TClassificacao.class.php";

$stPrograma = "ManterClassificacao";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";

$stAcao              = $request->get('stAcao');
$boGeraManual        = $request->get('boGeraManual');
$inCodClassificacao  = $request->get('inCodClassificacao');
$stNomeClassificacao = $request->get('stNomeClassificacao');

$obErro = new Erro;
$obTClassificacao = new TClassificacao;

switch ($stAcao) {
    
    case "incluir":
        Sessao::setTrataExcecao(true);

        if (empty($stNomeClassificacao))
            $obErro->setDescricao('Informe a descrição da classificação.');
        
        $boGeraCodigo = SistemaLegado::pegaConfiguracao("tipo_numeracao_classificacao_assunto", 5);
        
        if ( empty($boGeraCodigo) || $boGeraCodigo == "xxx" ){
            
            $obErro->setDescricao('Configure o modo de geração do codigo de classificação em Gestão Administrativa :: Protocolo :: Configuração :: Alterar Configuração');
        
        }elseif( $boGeraCodigo == "manual" ){
        
            if ( !empty($inCodClassificacao) ) {
        
                $inValidaCod = SistemaLegado::pegaDado('cod_classificacao', 'sw_classificacao', ' WHERE cod_classificacao = '.$inCodClassificacao);
        
                if ($inCodClassificacao == $inValidaCod) {
                    $obErro->setDescricao('O código informado já está sendo utilizado.');
                } 
        
            }else{
                $obErro->setDescricao('Informe o código da classificação.');
            }
        
        }elseif ( $boGeraCodigo == 'automatico' ) {
            $obTClassificacao->proximoCod($inCodClassificacao);
        }

        if (!$obErro->ocorreu()) {
            $obTClassificacao->setDado('cod_classificacao', $inCodClassificacao);
            $obTClassificacao->setDado('nom_classificacao', $stNomeClassificacao);
            $obErro = $obTClassificacao->inclusao();

            $stMensagem = "Classificação: ".$inCodClassificacao." - ".$stNomeClassificacao;
        }

        Sessao::encerraExcecao();

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgForm."?stAcao=".$stAcao, $stMensagem ,"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;

    case "alterar":
        Sessao::setTrataExcecao(true);

        $stValidaNome = SistemaLegado::pegaDado("nom_classificacao", "sw_classificacao", " WHERE nom_classificacao = '".$stNomeClassificacao."' AND cod_classificacao <> ".$inCodClassificacao);

        if ($stValidaNome == $stNomeClassificacao) {
            $obErro->setDescricao('Essa descrição de classificação já existe.');
        } else {
            $obTClassificacao->setDado('cod_classificacao', $inCodClassificacao);
            $obTClassificacao->setDado('nom_classificacao', $stNomeClassificacao);
            $obTClassificacao->alteracao();

            $stMensagem = "Classificação: ".$inCodClassificacao." - ".$stNomeClassificacao;
        }

        Sessao::encerraExcecao();

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgList."?stAcao=".$stAcao, $stMensagem ,"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;
    
    case "excluir":
        $pgProx = $pgList;
        Sessao::setTrataExcecao(true);

        $boValidaExclusao = SistemaLegado::pegaDado("cod_classificacao", "sw_assunto", " WHERE cod_classificacao = ".$inCodClassificacao);

        if (!empty($boValidaExclusao)) {
            $stMensagem = 'Essa classificação não pode ser excluída por estar sendo utilizada.';
        } else {
            $obTClassificacao->setDado('cod_classificacao', $inCodClassificacao);
            $obTClassificacao->exclusao();

            $stMensagem = "Classificação: ".$inCodClassificacao." - ".$stNomeClassificacao;
        }

        Sessao::encerraExcecao();
        SistemaLegado::alertaAviso($pgList."?stAcao=".$stAcao, $stMensagem, "excluir", "aviso", Sessao::getId(), "../");
    break;
}

?>