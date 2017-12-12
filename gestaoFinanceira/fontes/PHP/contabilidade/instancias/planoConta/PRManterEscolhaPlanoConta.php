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
    * Página de Processamento de Escolha do Plano de Contas
    * Data de Criação   : 10/10/2012

    * @author Analista: Tonismar
    * @author Desenvolvedor: Eduardo

    * @ignore
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoContaHistorico.class.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadeSistemaContabil.class.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadeClassificacaoContabil.class.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoConta.class.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoContaAnalitica.class.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoContaGeral.class.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoContaEstrutura.class.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php");

//Define o nome dos arquivos PHP
$stPrograma = 'ManterEscolhaPlanoConta';
$pgFilt    = 'FL'.$stPrograma.'.php';
$pgList    = 'LS'.$stPrograma.'.php';
$pgForm    = 'FM'.$stPrograma.'.php';
$pgProc    = 'PR'.$stPrograma.'.php';
$pgOcul    = 'OC'.$stPrograma.'.php';

$obErro = new Erro;
$obTransacao = new Transacao;
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

$obRContabilidadePlanoContaHistorico = new RContabilidadePlanoContaHistorico;
$obRContabilidadePlanoContaHistorico->setExercicio($_REQUEST['stExercicio']);
$obRContabilidadePlanoContaHistorico->verificaUltimoPlanoEscolhido($rsPlanos, '', $boTransacao);

//Busca o UF do sistema
$inCodUFSistema = trim(SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()));

$obTUF = new TUF;
$stFiltroUF = " WHERE cod_uf = ".$inCodUFSistema;
$obTUF->recuperaTodos( $rsUF, $stFiltroUF, "nom_uf");
while (!$rsUF->eof()) {
    $inStUFSistema = trim($rsUF->getCampo('nom_uf'));
    $rsUF->proximo();
}

// $boIncluir
if ($rsPlanos->getNumLinhas() > 0) {
    //Já tem um plano configurado para este ano, então deve-se alterar o existente
    $boPlanoConfigurado = true;
} else {
    $boPlanoConfigurado = false;
}

if ($_REQUEST['inCodPlano']) {
    $arCodPlano = explode('_', $_REQUEST['inCodPlano']);
    $inCodUF = trim($arCodPlano[0]);
    $inCodPlano = $arCodPlano[1];

    if($inCodUFSistema==$inCodUF||$inCodUF==0){
        if ($boPlanoConfigurado) {
            if ($inCodUF == $rsPlanos->getCampo('cod_uf') && $inCodPlano == $rsPlanos->getCampo('cod_plano') && $request->get('stExercicio') == $rsPlanos->getCampo('exercicio')) {
                $obErro->setDescricao('Plano escolhido já é o Plano atual.');
            }
    
            if ($inCodUF == $rsPlanos->getCampo('cod_uf') && $inCodPlano < $rsPlanos->getCampo('cod_plano') && $request->get('stExercicio') == $rsPlanos->getCampo('exercicio')) {
                $obErro->setDescricao('Não é possível escolher uma versão anterior a atual.');
            }
        }
    }else{
        $obErro->setDescricao('O estado do plano de contas não pode ser alterado. Selecione uma versão do plano de contas do Estado '.$inStUFSistema.' ou da União.');
    }
} else {
    $obErro->setDescricao('Deve ser selecionada uma versão do plano de contas.');
}

$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
$obTAdministracaoConfiguracao->setDado('cod_modulo', 9);
$obTAdministracaoConfiguracao->setDado('exercicio', Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado('parametro', 'masc_plano_contas');
$obTAdministracaoConfiguracao->pegaConfiguracao($stMascara, '', $boTransacao);

if ($stMascara == '') {
    $obErro->setDescricao('Para escolher o plano é necessário que a versão da virada tenha sido aplicada!');
}

if (!$obErro->ocorreu()) {
    $obRContabilidadeSistemaContabil = new RContabilidadeSistemaContabil;
    $obRContabilidadeSistemaContabil->setExercicio($_REQUEST['stExercicio']);
    $obRContabilidadeSistemaContabil->listar($rsSistemaContabil, '', $boTransacao);

    // Verifica se existe Sistema Contábil para o exercicio escolhido
    if ($rsSistemaContabil->getNumLinhas() < 1) {
        //Busca os sistemas contábeis do último ano para replicar para o exercício escolhido
        $obRContabilidadeSistemaContabil->listarUltimoExercicio($rsSistemasContabeis, '', $boTransacao);

        while (!$rsSistemasContabeis->EOF()) {
            $obRContabilidadeSistemaContabil->setCodSistema($rsSistemasContabeis->getCampo('cod_sistema'));
            $obRContabilidadeSistemaContabil->setNomSistema($rsSistemasContabeis->getCampo('nom_sistema'));
            $obErro = $obRContabilidadeSistemaContabil->incluir($boTransacao);

            $rsSistemasContabeis->proximo();
        }
    }

    $obRContabilidadeClassificacaoContabil = new RContabilidadeClassificacaoContabil;
    $obRContabilidadeClassificacaoContabil->setExercicio($_REQUEST['stExercicio']);
    $obRContabilidadeClassificacaoContabil->listar($rsClassificacaoContabil, '', $boTransacao);

    // Verifica se existe Classificação Contábil para o exercicio escolhido
    if ($rsClassificacaoContabil->getNumLinhas() < 1) {
        //Busca as classificações contábeis do último ano para replicar para o exercício escolhido
        $obRContabilidadeClassificacaoContabil->listarUltimoExercicio($rsClassificacoesContabeis, '', $boTransacao);

        while (!$rsClassificacoesContabeis->EOF()) {
            $obRContabilidadeClassificacaoContabil->setCodClassificacao($rsClassificacoesContabeis->getCampo('cod_classificacao'));
            $obRContabilidadeClassificacaoContabil->setNomClassificacao($rsClassificacoesContabeis->getCampo('nom_classificacao'));
            $obErro = $obRContabilidadeClassificacaoContabil->incluir($boTransacao);

            $rsClassificacoesContabeis->proximo();
        }
    }

    $obRContabilidadePlanoContaEstrutura = new RContabilidadePlanoContaEstrutura;
    $obRContabilidadePlanoContaEstrutura->setCodPlano($inCodPlano);
    $obRContabilidadePlanoContaEstrutura->obRAdministracaoUF->setCodigoUF($inCodUF);
    $obRContabilidadePlanoContaEstrutura->setExercicio($_REQUEST['stExercicio']);

    if (!$obErro->ocorreu()) {
        $obErro = $obRContabilidadePlanoContaEstrutura->deletarContasSemMovimentacao($rsContasDeletar, '', $boTransacao);
    }

    if (!$obErro->ocorreu()) {
        $obErro = $obRContabilidadePlanoContaEstrutura->incluirEscolhaPlanoContas($rsContasIncluir, '', $boTransacao);
    }

    if (!$obErro->ocorreu()) {
        $obRContabilidadePlanoContaHistorico->setCodPlano($inCodPlano);
        $obRContabilidadePlanoContaHistorico->obRAdministracaoUF->setCodigoUF($inCodUF);
        $obRContabilidadePlanoContaHistorico->setTimestamp(date('Y-m-d H:i:s'));

        $obErro = $obRContabilidadePlanoContaHistorico->salvar($boTransacao);
    }
}

$stCampos  = "?".Sessao::getId()."&stExercicio=".$_REQUEST['stExercicio']."&inCodPlano=".$inCodPlano."&inCodUF=".$inCodUF;

if (!$obErro->ocorreu()) {
    $obTransacao->commitAndClose();
    SistemaLegado::alertaAviso('PRRelatorioEscolhaPlanoConta.php'.$stCampos, 'Plano de contas escolhido com sucesso', 'incluir', 'aviso', Sessao::getId(), '../');
} else {
    $obTransacao->rollbackAndClose();
    SistemaLegado::alertaAviso($pgForm.'?stAcao=incluir&', urlencode($obErro->getDescricao()), 'n_incluir', 'erro', Sessao::getId(), '../');
}
?>
