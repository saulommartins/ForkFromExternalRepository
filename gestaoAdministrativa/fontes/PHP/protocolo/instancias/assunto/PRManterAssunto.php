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
* Arquivo de instância para manutenção de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 16315 $
$Name$
$Author: cassiano $
$Date: 2006-10-03 13:02:25 -0300 (Ter, 03 Out 2006) $

Casos de uso: uc-01.06.95
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_PROT_MAPEAMENTO."TPROClassificacao.class.php");
include_once(CAM_GA_PROT_MAPEAMENTO."TPROAssunto.class.php");
include_once(CAM_GA_PROT_MAPEAMENTO."TPROAssuntoAtributo.class.php");
include_once(CAM_GA_PROT_MAPEAMENTO."TPRODocumento.class.php");
include_once(CAM_GA_PROT_MAPEAMENTO."TPRODocumentoAssunto.class.php");
include_once(CAM_GA_PROT_MAPEAMENTO."TPROAssuntoAcao.class.php");

$stPrograma = "ManterAssunto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTPROClassificacao = new TPROClassificacao();
$obTPROAssunto 	     = new TPROAssunto();
$obTPROAssuntoAcao   = new TPROAssuntoAcao();
$obErro              = new Erro;
$obTPROAssunto->obTPROClassificacao = &$obTPROClassificacao;

$inCodigoClassificacao = $_POST['inCmbCodigoClassificacao'] ? $_POST['inCmbCodigoClassificacao'] : $_POST['inCodigoClassificacao'];

$obTPROClassificacao->setDado('cod_classificacao',$inCodigoClassificacao);
$obTPROAssunto->setDado('nom_assunto_validacao', str_replace("'", "''",$_POST['stDescricao']));
$obTPROAssunto->setDado('nom_assunto', str_replace("\'", "\\\'\'",$_POST['stDescricao']));
$obTPROAssunto->setDado('confidencial',$_POST['boConfidencial']);

$arDocumentos = array();
$arAtributos  = array();

if (is_array($_POST['inDocumento']) ) {
    foreach ($_POST['inDocumento'] as $inIndice => $inCodigoDocumento) {
        $arDocumentos[$inIndice] = new TPRODocumentoAssunto();
        $arDocumentos[$inIndice]->obTPROAssunto = &$obTPROAssunto;
        $arDocumentos[$inIndice]->setDado('cod_documento', $inCodigoDocumento);
    }
}

if (is_array($_POST['inAtributo'])) {
    foreach ($_POST['inAtributo'] as $inIndice => $inCodigoAtributo) {
        $arAtributos[$inIndice] = new TPROAssuntoAtributo();
        $arAtributos[$inIndice]->obTPROAssunto = &$obTPROAssunto;
        $arAtributos[$inIndice]->setDado('cod_atributo', $inCodigoAtributo);
    }
}

switch ($_REQUEST['stAcao']) {
    case "incluir":
        Sessao::setTrataExcecao(true);

        $boGeraCodigo = SistemaLegado::pegaConfiguracao("tipo_numeracao_classificacao_assunto", 5);

        if (!empty($boGeraCodigo) && $boGeraCodigo == 'automatico') {
            $obTPROAssunto->proximoCod($inCodigoAssunto);
        } else {
            $inCodigoAssunto = $_POST['inCodigoAssunto'];
            $inValidaCod = SistemaLegado::pegaDado('cod_assunto', 'sw_assunto', ' WHERE cod_assunto = '.$inCodigoAssunto.' AND cod_classificacao = '.$inCodigoClassificacao );

            if ($inCodigoAssunto == $inValidaCod) {
                $obErro->setDescricao('O código informado já está sendo utilizado.');
            }
        }

        if (!$obErro->ocorreu()) {

            $obTPROAssunto->setDado('cod_assunto',$inCodigoAssunto);
            $obTPROAssunto->inclusao();

            foreach ($arDocumentos as $obDocumento) {
                $obDocumento->inclusao();
            }
    
            foreach ($arAtributos as $obAtributo) {
                $obAtributo->inclusao();
            }

            //pega os dados da sessao
            $arAcaoSessao = Sessao::read('acaoSessao');
            $obTPROAssuntoAcao->obTPROAssunto = &$obTPROAssunto;
                if ( is_array($arAcaoSessao) ) {
                    foreach ($arAcaoSessao as $arAcao) {
                             $obTPROAssuntoAcao->setDado('cod_acao',$arAcao['cod_acao']);
                             $obTPROAssuntoAcao->inclusao();
                    }
                }
            $stMensagem = "Assunto: ".$inCodigoAssunto." - ".$_POST['stDescricao'];
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
        $inCodigoAssunto = $_POST['inCodigoAssunto'];
        $inCodigoClassificacao = $_POST['inCodigoClassificacao'];
        $obTPROAssunto->setDado('cod_assunto',$inCodigoAssunto);
        $obTPROAssunto->alteracao();
        //TRATA A ALTERAÇÃO DOS DOCUMENTOS
        $obDocumento = new TPRODocumentoAssunto();
        $stChave = $obDocumento->getComplementoChave();
        $obDocumento->setComplementoChave('cod_assunto,cod_classificacao');
        $obDocumento->setDado('cod_assunto', $inCodigoAssunto);
        $obDocumento->setDado('cod_classificacao', $inCodigoClassificacao);
        $obDocumento->exclusao();
        foreach ($arDocumentos as $obDocumento) {
            $obDocumento->inclusao();
        }
        //TRATA A ALTERAÇÃO DOS ATRIBUTOS
        $obAtributo = new TPROAssuntoAtributo();
        $obAtributo->setDado('cod_classificacao',$inCodigoClassificacao);
        $obAtributo->setDado('cod_assunto', 	 $inCodigoAssunto);

                 //pega os dados da sessao
                 $arAtributosSessao = Sessao::read('atributos');
        foreach ($arAtributos as $obAtributo) {
            if ( !isset($arAtributosSessao[$obAtributo->getDado('cod_atributo')]) ) {
                $obAtributo->inclusao();
            } else {
                unset( $arAtributosSessao[$obAtributo->getDado('cod_atributo')] );
            }
        }
        if (is_array( $arAtributosSessao ) ) {
            foreach ($arAtributosSessao as $inCodigoAtributo => $boValor) {
                $obAtributo->setDado('cod_atributo', $inCodigoAtributo);
                $obAtributo->exclusao();
            }
        }

        $obTPROAssuntoAcao->obTPROAssunto = &$obTPROAssunto;
        $stComplementoChave = $obTPROAssuntoAcao->getComplementoChave();
        $obTPROAssuntoAcao->setComplementoChave('cod_assunto,cod_classificacao');
        $obTPROAssuntoAcao->exclusao();
                 //pega os dados da sessao
                  $arAcaoSessao = Sessao::read('acaoSessao');
        if ( is_array($arAcaoSessao) ) {
                        foreach ($arAcaoSessao as $arAcao) {
                             $obTPROAssuntoAcao->setDado('cod_acao',$arAcao['cod_acao']);
                             $obTPROAssuntoAcao->inclusao();
                        }
        }
        $stMensagem = "Assunto: ".$inCodigoAssunto." - ".$_POST['stDescricao'];
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,$stMensagem ,"alterar","aviso", Sessao::getId(), "../");
    break;
    case "excluir":
        $pgProx = $pgList;
        Sessao::setTrataExcecao(true);
        Sessao::getExcecao()->setLocal('tp');
        Sessao::getTransacao()->setMapeamento( $obTPROAssunto );
        $inCodigoClassificacao = $_REQUEST['inCodigoClassificacao'];
        $inCodigoAssunto = $_REQUEST['inCodigoAssunto'];

        $obTPROAssunto->setDado('cod_assunto',$inCodigoAssunto);
        $obTPROAssunto->obTPROClassificacao->setDado('cod_classificacao', $inCodigoClassificacao);

        $stFiltro  = ' WHERE cod_classificacao ='.$inCodigoClassificacao.' AND ';
        $stFiltro .= 'cod_assunto ='.$inCodigoAssunto;

        //LISTA OS ATRIBUTOS DINAMICOS DO ASSUNTO
        $obTPROAssuntoAtributo = new TPROAssuntoAtributo();
        $obTPROAssuntoAtributo->setDado('cod_classificacao', $inCodigoClassificacao );
        $obTPROAssuntoAtributo->setDado('cod_assunto', $inCodigoAssunto );
        $obTPROAssuntoAtributo->recuperaTodos($rsAssuntoAtributo,$stFiltro);

        //EXCLUI UM A UM OS ATRIBUTOS, FOI FEITO ASSIM PARA PODER RETORNAR AO USUÁRIO
        //QUAL ATRIBUTO PODE DAR ERRO NA EXCLUSÃO
        while (!$rsAssuntoAtributo->eof()) {
            $obTPROAssuntoAtributo->setDado('cod_atributo', $rsAssuntoAtributo->getCampo('cod_atributo') );
            $obTPROAssuntoAtributo->exclusao();
            $rsAssuntoAtributo->proximo();
        }

        //EXCLUI OS DOCUMENTOS ASSOCIADOS AO ASSUNTO
        $obDocumento = new TPRODocumentoAssunto();
        $stChave = $obDocumento->getComplementoChave();
        $obDocumento->setComplementoChave('cod_assunto,cod_classificacao');
        $obDocumento->setDado('cod_assunto', $inCodigoAssunto);
        $obDocumento->setDado('cod_classificacao', $inCodigoClassificacao);
        $obDocumento->exclusao();

        //EXCLUI O RELACIONAMENTO
        $obTPROAssuntoAcao->obTPROAssunto = &$obTPROAssunto;
        $stComplementoChave = $obTPROAssuntoAcao->getComplementoChave();
        $obTPROAssuntoAcao->setComplementoChave('cod_assunto,cod_classificacao');
        $obTPROAssuntoAcao->exclusao();

        //EXCLUI O ANDAMENTO PADRAO
                include_once(CAM_GA_PROT_MAPEAMENTO."TPROAndamentoPadrao.class.php");
                $obAndamentoPadrao = new TPROAndamentoPadrao();
                $obAndamentoPadrao->setComplementoChave('cod_classificacao, cod_assunto');
                $obAndamentoPadrao->setDado('cod_assunto', $inCodigoAssunto);
        $obAndamentoPadrao->setDado('cod_classificacao', $inCodigoClassificacao);
        $obAndamentoPadrao->exclusao();

        //EXCLUI O ASSUNTO
        $obTPROAssunto->exclusao();
        $stMensagem = 'Assunto: '.$inCodigoAssunto.' - '.$_REQUEST['stDescQuestao'];

        Sessao::encerraExcecao();
                sistemaLegado::alertaAviso($pgList,$stMensagem,"excluir","aviso", Sessao::getId(), "../");
                break;
}
?>
