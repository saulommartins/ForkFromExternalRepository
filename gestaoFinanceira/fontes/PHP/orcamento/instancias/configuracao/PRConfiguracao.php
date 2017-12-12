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
    * Interface de processamento da Configuração do Orçamento
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    $Id: PRConfiguracao.php 64548 2016-03-11 18:28:10Z evandro $

    * Casos de uso: uc-02.01.01
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );
/*
* Define o nome dos arquivos PHP
*/
$stPrograma = "Configuracao";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$boTransacao = "";
$obErro = new Erro;
$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());

// Classificação de receita
$stMascara = $request->get("stMascReceita");
$arMascara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);

$arIdProjeto = explode(",",$request->get("inDigitoIDProjeto"));
$arIdAtividade = explode(",",$request->get("inDigitoIDAtividade"));
$arIdNaoOrcamentarios = explode(",",$request->get("inDigitoIDNaoOrcamentarios"));
$arIdEspecial = explode(",",$request->get('inDigitoIDEspecial'));
$boErro = false;

if (array_intersect($arIdProjeto, $arIdAtividade)) {
    $boErro = true;
}

if (array_intersect($arIdProjeto, $arIdNaoOrcamentarios)) {
    $boErro = true;
}

if (array_intersect($arIdProjeto, $arIdEspecial)) {
    $boErro = true;
}

if (array_intersect($arIdAtividade, $arIdNaoOrcamentarios)) {
    $boErro = true;
}

if (array_intersect($arIdAtividade, $arIdEspecial)) {
    $boErro = true;
}

if (array_intersect($arIdNaoOrcamentarios, $arIdEspecial)) {
    $boErro = true;
}

if ($boErro) {
    $obErro->setDescricao('Os valores não podem se repetir entre os Dígitos de Identificação');
}

if (!$obErro->ocorreu()) {
    /* Receitas 'normais' */
    $obRConfiguracaoOrcamento->setDedutora( false );
    $obRConfiguracaoOrcamento->recuperaMaxPosicaoReceita( $rsMaxPosicaoReceita, $boTransacao );

    $inMaxPosicaoReceita = $rsMaxPosicaoReceita->getCampo('max_posicao');
    $inTotalPosicao = count($arMascara);

    // Deleta posicao
    if ($inTotalPosicao < $inMaxPosicaoReceita) {
        for ($inMaxPosicaoReceita; $inMaxPosicaoReceita > $inTotalPosicao; $inMaxPosicaoReceita--) {
            // Deleta máscara antes de inserir a nova.
            $obRConfiguracaoOrcamento->setPosicao( $inMaxPosicaoReceita );
            $obRConfiguracaoOrcamento->setTipoReceita ( '0' );
            if(!$obErro->ocorreu())
                $obErro = $obRConfiguracaoOrcamento->deletarMascaraReceita( $boTransacao );
            if($obErro->ocorreu()) $obErro->setDescricao("Existem classificações de Receita que utilizam a mascara '$stMascara'");
        }
    }

    // Inclui ou altera mascara
    if (!$obErro->ocorreu()) {
        foreach ($arMascara as $inKey => $inValor) {
            if ( $inKey < $rsMaxPosicaoReceita->getCampo('max_posicao') ) {
                $obRConfiguracaoOrcamento->setPosicao            ( ($inKey + 1) );
                $obRConfiguracaoOrcamento->setMascPosicaoReceita ( $inValor );
                $obRConfiguracaoOrcamento->setTipoReceita        ( '0' );
                if(!$obErro->ocorreu())
                    $obErro = $obRConfiguracaoOrcamento->alterarReceita( $boTransacao );
            } else {
                $obRConfiguracaoOrcamento->setPosicao            ( ($inKey + 1) );
                $obRConfiguracaoOrcamento->setMascPosicaoReceita ( $inValor );
                $obRConfiguracaoOrcamento->setTipoReceita        ( '0' );
                if(!$obErro->ocorreu())
                    $obErro = $obRConfiguracaoOrcamento->salvarReceita( $boTransacao );
            }
        }
    }

    /* Receitas Dedutoras */
    $stMascaraDedutora = $request->get("stMascReceitaDedutora");
    $arMascaraDedutora = preg_split( "/[^a-zA-Z0-9]/", $stMascaraDedutora);

    $obRConfiguracaoOrcamento->setDedutora(true);
    $obRConfiguracaoOrcamento->recuperaMaxPosicaoReceita( $rsMaxPosicaoReceitaDedutora, $boTransacao );

    $inMaxPosicaoReceitaDedutora = $rsMaxPosicaoReceitaDedutora->getCampo('max_posicao');
    $inTotalPosicao = count($arMascaraDedutora);

    // Deleta posicao
    if ($inTotalPosicao < $inMaxPosicaoReceitaDedutora) {
        for ($inMaxPosicaoReceitaDedutora; $inMaxPosicaoReceitaDedutora > $inTotalPosicao; $inMaxPosicaoReceitaDedutora--) {
            // Deleta máscara antes de inserir a nova.
            $obRConfiguracaoOrcamento->setPosicao( $inMaxPosicaoReceitaDedutora );
            $obRConfiguracaoOrcamento->setTipoReceita ( '1' );
            if(!$obErro->ocorreu())
                $obErro = $obRConfiguracaoOrcamento->deletarMascaraReceita( $boTransacao );
            if($obErro->ocorreu()) $obErro->setDescricao("Existem classificações de Receita Dedutora que utilizam a máscara '$stMascaraDedutora'");
        }
    }

    // Inclui ou altera mascara
    if (!$obErro->ocorreu()) {
        foreach ($arMascaraDedutora as $inKey => $inValor) {
            if ( $inKey < $rsMaxPosicaoReceitaDedutora->getCampo('max_posicao') ) {
                $obRConfiguracaoOrcamento->setPosicao            ( ($inKey + 1) );
                $obRConfiguracaoOrcamento->setMascPosicaoReceita ( $inValor );
                $obRConfiguracaoOrcamento->setTipoReceita        ( '1' );
                if(!$obErro->ocorreu())
                    $obErro = $obRConfiguracaoOrcamento->alterarReceita( $boTransacao );
            } else {
                $obRConfiguracaoOrcamento->setPosicao            ( ($inKey + 1) );
                $obRConfiguracaoOrcamento->setMascPosicaoReceita ( $inValor );
                $obRConfiguracaoOrcamento->setTipoReceita        ( '1' );
                if(!$obErro->ocorreu())
                    $obErro = $obRConfiguracaoOrcamento->salvarReceita( $boTransacao );
            }
        }
    }

    $obRConfiguracaoOrcamento->setDedutora( false );

    // Rúbrica de despesa
    $stMascRubricaDespesa = $request->get("stMascPosicaoDespesa");
    $arMascRubricaDespesa = preg_split( "/[^a-zA-Z0-9]/", $stMascRubricaDespesa);

    $obRConfiguracaoOrcamento->recuperaMaxPosicaoDespesa( $rsMaxPosicaoDespesa, $boTransacao );

    $inMaxPosicaoDespesa   = $rsMaxPosicaoDespesa->getCampo('max_posicao');
    $inTotalPosicaoDespesa = count($arMascRubricaDespesa);

    // Deleta posicao
    if ($inTotalPosicaoDespesa < $inMaxPosicaoDespesa) {
        for ($inMaxPosicaoDespesa; $inMaxPosicaoDespesa > $inTotalPosicaoDespesa; $inMaxPosicaoDespesa--) {
            // Deleta máscara antes de inserir a nova.
            $obRConfiguracaoOrcamento->setPosicaoRubrica( $inMaxPosicaoDespesa );
            if(!$obErro->ocorreu())
                $obErro = $obRConfiguracaoOrcamento->deletarMascaraDespesa( $boTransacao );
            if($obErro->ocorreu()) $obErro->setDescricao ( "Existem classificações de Despesa que utilizam a máscara '$stMascRubricaDespesa'");
        }
    }

    // Inclui ou altera mascara
    if (!$obErro->ocorreu()) {
        foreach ($arMascRubricaDespesa as $inKey => $inValor) {
            if ( $inKey < $rsMaxPosicaoDespesa->getCampo('max_posicao') ) {
                $obRConfiguracaoOrcamento->setPosicaoRubrica           ( ($inKey + 1) );
                $obRConfiguracaoOrcamento->setMascClassificacaoReceita ( $inValor );
                if(!$obErro->ocorreu())
                    $obErro = $obRConfiguracaoOrcamento->alterarRubrica( $boTransacao );
            } else {
                $obRConfiguracaoOrcamento->setPosicaoRubrica           ( ($inKey + 1) );
                $obRConfiguracaoOrcamento->setMascClassificacaoReceita ( $inValor );
                if(!$obErro->ocorreu())
                    $obErro = $obRConfiguracaoOrcamento->salvarRubrica( $boTransacao );
            }
        }
    }
    $obRConfiguracaoOrcamento->setCodModulo                      ( $request->get("inCodModulo")               );
    $obRConfiguracaoOrcamento->setCodEntidadePrefeitura          ( $request->get("inCodEntidadePrefeitura")   );
    $obRConfiguracaoOrcamento->setCodEntidadeCamara              ( $request->get("inCodEntidadeCamara")	      );
    $obRConfiguracaoOrcamento->setCodEntidadeRPPS                ( $request->get("inCodEntidadeRPPS")	      );
    $obRConfiguracaoOrcamento->setCodEntidadeConsorcio           ( $request->get("inCodEntidadeConsorcio")    );
    $obRConfiguracaoOrcamento->setFormaExecucaoOrcamento         ( $request->get("inCodFormaExecucao")        );
    $obRConfiguracaoOrcamento->setNumPAOPosicaoDigitoID          ( $request->get("inPosicaoDigitoID")         );
    $obRConfiguracaoOrcamento->setNumPAODigitosIDProjeto         ( $request->get("inDigitoIDProjeto")         );
    $obRConfiguracaoOrcamento->setNumPAODigitosIDAtividade       ( $request->get("inDigitoIDAtividade")       );
    $obRConfiguracaoOrcamento->setNumPAODigitosIDOperEspeciais   ( $request->get("inDigitoIDEspecial")        );
    $obRConfiguracaoOrcamento->setMascDespesa                    ( $request->get("stMascDespesa")             );
    $obRConfiguracaoOrcamento->setNumPAODigitosIDNaoOrcamentarios( $request->get("inDigitoIDNaoOrcamentarios"));

    if($request->get('stMascRecurso'))
        $obRConfiguracaoOrcamento->setMascRecurso( $request->get("stMascRecurso") );
    if($request->get('stMascDestinacaoRecurso'))
        $obRConfiguracaoOrcamento->setMascDestinacaoRecurso( $request->get("stMascDestinacaoRecurso") );

    $obRConfiguracaoOrcamento->setDestinacaoRecurso( $request->get("boDestinacao") );

    if (Sessao::getExercicio() < '2014') {
        $obRConfiguracaoOrcamento->setUnidadeMedidaMetas       ( $request->get("inCodApuracaoMetas")        );
    } else {
        $obRConfiguracaoOrcamento->setUnidadeMedidaMetasReceita( $request->get("inCodApuracaoMetasReceita") );
    $obRConfiguracaoOrcamento->setUnidadeMedidaMetasDespesa( $request->get("inCodApuracaoMetasDespesa") );
    }
    $obRConfiguracaoOrcamento->setCodContador                ( $request->get("inCodContador")         );
    $obRConfiguracaoOrcamento->setCodTecContabil             ( $request->get("inCodTecContabil")      );
    $obRConfiguracaoOrcamento->setMascClassDespesa           ( $request->get("stMascPosicaoDespesa")  );
    $obRConfiguracaoOrcamento->setMascClassReceita           ( $request->get("stMascReceita")         );
    $obRConfiguracaoOrcamento->setMascClassReceitaDedutora   ( $request->get("stMascReceitaDedutora") );
    $obRConfiguracaoOrcamento->setLimiteSuplementacaoDecreto ( $request->get("nuLimiteSuplementacaoDecreto") );

    if ( Sessao::getExercicio() > 2015 && SistemaLegado::isTCEMG($boTransacao) ) {
        $obRConfiguracaoOrcamento->setSuplementacaoRigidaRecurso ( ($request->get("stSuplementacaoRegidaRecurso") == 'S') ? 'sim':'nao' );
    }

    if(!$obErro->ocorreu())
        $obErro = $obRConfiguracaoOrcamento->salvaConfiguracao( $boTransacao );

    if (!$obErro->ocorreu()) {
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&acao=".$request->get('acao'),"Configuração Orçamento ","alterar","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
    }
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
}

?>
