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
    * Pagina de Processamento de Comissao de Avaliacao
    * Data de Criacao   : 14/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * $Id: PRRecurso.php 64337 2016-01-15 18:38:47Z michel $

    * Casos de uso: uc-02.01.05
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_ORC_NEGOCIO.'ROrcamentoRecurso.class.php';
include CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoBanco.class.php';
include CAM_GF_PPA_NEGOCIO . 'RPPAManterPPA.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "Recurso";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obROrcamentoRecurso  = new ROrcamentoRecurso;
$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
//$obTransacao = new Transacao;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obErro = new Erro();

//recupera os exercicios do ppa para propagar o recurso pelos exercicios do ppa
$obRPPAManterPPA = new RPPAManterPPA();
$obRPPAManterPPA->stExercicio = Sessao::getExercicio();
$obRPPAManterPPA->listByExercicio($rsRecordSet);

$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
$obTAdministracaoConfiguracao->setDado('cod_modulo', 9);
$obTAdministracaoConfiguracao->setDado('exercicio', Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado('parametro', 'masc_plano_contas');
$obTAdministracaoConfiguracao->pegaConfiguracao($stMascara, '');

$stExercicio      = (int) Sessao::getExercicio();
$stExercicioFinal = (int) $rsRecordSet->getCampo('ano_final');

switch ($stAcao) {
    case "incluir":
        //faz o insert para cada ano até o ano_final do ppa
        
        $boFlagTransacao = false;
        $obErro = $obROrcamentoRecurso->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        
        if ( !$obErro->ocorreu() ) {
            $stExercicio = (int) $rsRecordSet->getCampo('ano_inicio');
            for ($stExercicio; $stExercicio <= $stExercicioFinal; $stExercicio++) {
                if (!$obErro->ocorreu()) {
                    $obROrcamentoRecurso->setCodRecurso     ($_POST['inCodRecurso']);
                    $obROrcamentoRecurso->setNome           ($_POST['stNome']);
                    $obROrcamentoRecurso->setFinalidade     ($_POST['stFinalidade']);
                    $obROrcamentoRecurso->setTipo           ($_POST['stTipo']);
                    $obROrcamentoRecurso->setCodFonteRecurso($_POST['inCodFonteRecurso']);
                    $obROrcamentoRecurso->setCodigoTC       ($_POST['inCodigoTC']);
                    $obROrcamentoRecurso->setExercicio      ($stExercicio);
                    $obROrcamentoRecurso->setTipoEsfera     ($_POST['inTipoEsfera']);
                    $obErro = $obROrcamentoRecurso->incluir($boTransacao, $boFlagTransacao);
                }
            }
        }
        
        if (!$obErro->ocorreu() && $request->get('inCodTCE') != '') {
            include_once ( CAM_GF_ORC_MAPEAMENTO."TTCEPECodigoFonteRecurso.class.php" );
            $obTTCEPECodigoFonteRecurso = new TTCEPECodigoFonteRecurso;
            $obTTCEPECodigoFonteRecurso->setDado ('cod_fonte', $request->get('inCodTCE'));
            $obTTCEPECodigoFonteRecurso->setDado ('exercicio', Sessao::getExercicio());
            $obTTCEPECodigoFonteRecurso->setDado ('cod_recurso', $request->get('inCodRecurso'));
            $obErro = $obTTCEPECodigoFonteRecurso->inclusao($boTransacao);
        }
        
        $obROrcamentoRecurso->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obROrcamentoRecurso );
        
        //não cria contas contábeis apartir de 2014 e antes de 2009
        if (Sessao::getExercicio() > '2008' && Sessao::getExercicio() <= '2013') {
            if (!$obErro->ocorreu()) {
                
                if ( Sessao::getExercicio() > '2012' ) {
                    $obRContabilidadePlanoBanco->setCodEstrutural('7.2.1.1.1.');
                } else {
                    $obRContabilidadePlanoBanco->setCodEstrutural('1.9.3.2.0.00.00.');
                }
                $obRContabilidadePlanoBanco->getProximoEstruturalRecurso($rsProxCod);
                $inProximoCodEstruturalD = $rsProxCod->getCampo('prox_cod_estrutural');
                
                if ($inProximoCodEstruturalD != 99) {
                    $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setCodSistema(4);
                    $obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                    $inProximoCodEstruturalD++;
                    $inProximoCodEstruturalD = str_pad($inProximoCodEstruturalD, 2, "0", STR_PAD_LEFT);
                    if ( Sessao::getExercicio() > '2012' ) {
                        $stCodEstruturalD = '7.2.1.1.1.'.$inProximoCodEstruturalD.'.00';
                    } else {
                        $stCodEstruturalD = '1.9.3.2.0.00.00.'.$inProximoCodEstruturalD.'.00.00';
                    }
                    $stCodEstruturalD = SistemaLegado::doMask($stCodEstruturalD, $stMascara);
                    
                    $obRContabilidadePlanoBanco->setCodEstrutural($stCodEstruturalD);
                    $obRContabilidadePlanoBanco->setNomConta($_REQUEST['stNome']);
                    $obRContabilidadePlanoBanco->setExercicio(Sessao::getExercicio());
                    $obRContabilidadePlanoBanco->setNatSaldo('D');
                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($_REQUEST['inCodRecurso']);
                    $obRContabilidadePlanoBanco->setContaAnalitica(true);
                    
                    $obErro = $obRContabilidadePlanoBanco->salvar($boTransacao);
                } else {
                    SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                }
            }
            
            if (!$obErro->ocorreu()) {
                $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
                
                if ( Sessao::getExercicio() > '2012' ) {
                    $obRContabilidadePlanoBanco->setCodEstrutural('8.2.1.1.1.');
                } else {
                    $obRContabilidadePlanoBanco->setCodEstrutural('2.9.3.2.0.00.00.');
                }
                $obRContabilidadePlanoBanco->getProximoEstruturalRecurso($rsProxCod);
                $inProximoCodEstruturalC = $rsProxCod->getCampo('prox_cod_estrutural');
                
                if ($inProximoCodEstruturalC != 99) {
                    $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setCodSistema(4);
                    $obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                    $inProximoCodEstruturalC++;
                    $inProximoCodEstruturalC = str_pad($inProximoCodEstruturalC, 2, "0", STR_PAD_LEFT);
                    if ( Sessao::getExercicio() > '2012' ) {
                        $stCodEstruturalC = '8.2.1.1.1.'.$inProximoCodEstruturalC.'.00';
                    } else {
                        $stCodEstruturalC = '2.9.3.2.0.00.00.'.$inProximoCodEstruturalC.'.00.00';
                    }
                    $stCodEstruturalC = SistemaLegado::doMask($stCodEstruturalC, $stMascara);
                    $obRContabilidadePlanoBanco->setCodEstrutural($stCodEstruturalC);
                    $obRContabilidadePlanoBanco->setNomConta($_REQUEST['stNome']);
                    $obRContabilidadePlanoBanco->setExercicio(Sessao::getExercicio());
                    $obRContabilidadePlanoBanco->setNatSaldo('C');
                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($_REQUEST['inCodRecurso']);
                    $obRContabilidadePlanoBanco->setContaAnalitica(true);
                    
                    $obErro = $obRContabilidadePlanoBanco->salvar($boTransacao);
                    
                    if (!$obErro->ocorreu() && Sessao::getExercicio() > '2012') {
                        $obRContabilidadePlanoBanco->setCodEstrutural('8.2.1.1.2.');
                        
                        $obRContabilidadePlanoBanco->getProximoEstruturalRecurso($rsProxCod);
                        $inProximoCodEstruturalC = $rsProxCod->getCampo('prox_cod_estrutural');
                        
                        if ($inProximoCodEstruturalC != 99) {
                            $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setCodSistema(4);
                            $obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                            $obRContabilidadePlanoBanco->setCodPlano('');
                            $obRContabilidadePlanoBanco->setCodConta('');
                            $inProximoCodEstruturalC++;
                            $inProximoCodEstruturalC = str_pad($inProximoCodEstruturalC, 2, "0", STR_PAD_LEFT);
                            $stCodEstruturalC = '8.2.1.1.2.'.$inProximoCodEstruturalC.'.00';
                            $stCodEstruturalC = SistemaLegado::doMask($stCodEstruturalC, $stMascara);
                            $obRContabilidadePlanoBanco->setCodEstrutural($stCodEstruturalC);
                        } else {
                            SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                        }
                        
                        $obErro = $obRContabilidadePlanoBanco->salvar($boTransacao);
                        
                        if (!$obErro->ocorreu()) {
                            $obRContabilidadePlanoBanco->setCodEstrutural('8.2.1.1.3.');
                            
                            $obRContabilidadePlanoBanco->getProximoEstruturalRecurso($rsProxCod);
                            $inProximoCodEstruturalC = $rsProxCod->getCampo('prox_cod_estrutural');
                            if ($inProximoCodEstruturalC != 99) {
                                $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setCodSistema(4);
                                $obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                $obRContabilidadePlanoBanco->setCodPlano('');
                                $obRContabilidadePlanoBanco->setCodConta('');
                                $inProximoCodEstruturalC++;
                                $inProximoCodEstruturalC = str_pad($inProximoCodEstruturalC, 2, "0", STR_PAD_LEFT);
                                $stCodEstruturalC = '8.2.1.1.3.'.$inProximoCodEstruturalC.'.00';
                                $stCodEstruturalC = SistemaLegado::doMask($stCodEstruturalC, $stMascara);
                                $obRContabilidadePlanoBanco->setCodEstrutural($stCodEstruturalC);
                            } else {
                                SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                            }
                            
                            $obErro = $obRContabilidadePlanoBanco->salvar($boTransacao);
                        }
                        
                        if (!$obErro->ocorreu()) {
                            $obRContabilidadePlanoBanco->setCodEstrutural('8.2.1.1.4.');
                            
                            $obRContabilidadePlanoBanco->getProximoEstruturalRecurso($rsProxCod);
                            $inProximoCodEstruturalC = $rsProxCod->getCampo('prox_cod_estrutural');
                            if ($inProximoCodEstruturalC != 99) {
                                $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setCodSistema(4);
                                $obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                $obRContabilidadePlanoBanco->setCodPlano('');
                                $obRContabilidadePlanoBanco->setCodConta('');
                                $inProximoCodEstruturalC++;
                                $inProximoCodEstruturalC = str_pad($inProximoCodEstruturalC, 2, "0", STR_PAD_LEFT);
                                $stCodEstruturalC = '8.2.1.1.4.'.$inProximoCodEstruturalC.'.00';
                                $stCodEstruturalC = SistemaLegado::doMask($stCodEstruturalC, $stMascara);
                                $obRContabilidadePlanoBanco->setCodEstrutural($stCodEstruturalC);
                            } else {
                                SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                            }
                            
                            $obErro = $obRContabilidadePlanoBanco->salvar($boTransacao);
                        }
                    }
                } else {
                    SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                }
            }
        }
        
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Recurso: ".$_POST['stNome'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );
        $obErro = new Erro;
        $obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
        $obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
        $obRConfiguracaoOrcamento->consultarConfiguracao();
        $boDestinacao = $obRConfiguracaoOrcamento->getDestinacaoRecurso();
        
        if ($boDestinacao == 'true') {
            Sessao::getTransacao()->setMapeamento( $obTOrcamentoRecursoDestinacao );
            
         /* Mantem os dados do Recurso e insere os dados da Destinacao.
            Caso nao exista ainda uma destinacao definida para o recurso 'direto', insere esta destinacao
            para todos exercicios que o recurso 'direto' existe.  */
            include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php" );
            $arDestinacaoRecurso = explode('.',$_REQUEST['stDestinacaoRecurso']);
            $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
            $obTOrcamentoRecursoDestinacao->setDado('cod_recurso', $_REQUEST['inCodRecurso'] );
            $obTOrcamentoRecursoDestinacao->setDado('exercicio'  , Sessao::getExercicio()        );
            $obErro = $obTOrcamentoRecursoDestinacao->recuperaPorChave( $rsRecurso, $boTransacao );
            
            // Se ja existe Destinacao, altera pra todos exercicios
            if (!$obErro->ocorreu() && $rsRecurso->getNumLinhas() > 0) {
                $obTOrcamentoRecursoDestinacao->setDado("cod_recurso",      $_REQUEST['inCodRecurso']);
                $obTOrcamentoRecursoDestinacao->setDado("cod_uso",          $arDestinacaoRecurso[0]);
                $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao",   $arDestinacaoRecurso[1]);
                $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2]);
                $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3]);
                for ($stExercicio; $stExercicio <= $stExercicioFinal; $stExercicio++) {
                    if (!$obErro->ocorreu()) {
                        $obTOrcamentoRecursoDestinacao->setDado("exercicio", $stExercicio);
                        $obErro = $obTOrcamentoRecursoDestinacao->alteracao($boTransacao);
                    }
                }
            } elseif (!$obErro->ocorreu()) { // Senao, inclui a destinacao montada para todos exercicios do recurso.
                $obTOrcamentoRecursoDestinacao->setDado("cod_recurso",      $_REQUEST['inCodRecurso'] );
                $obTOrcamentoRecursoDestinacao->setDado("cod_uso",          $arDestinacaoRecurso[0] );
                $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao",   $arDestinacaoRecurso[1] );
                $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2] );
                $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3] );
                
                $obROrcamentoRecurso->setCodRecurso ( $_REQUEST['inCodRecurso'] );
                $obROrcamentoRecurso->setExercicio  ( Sessao::getExercicio() );
                $obErro = $obROrcamentoRecurso->listarRecursoDireto( $rsDireto, '', $boTransacao );
                
                while (!$obErro->ocorreu() && !$rsDireto->eof() ) {
                    $obTOrcamentoRecursoDestinacao->setDado("exercicio", $rsDireto->getCampo('exercicio') );
                    $obErro = $obTOrcamentoRecursoDestinacao->inclusao( $boTransacao );
                    $rsDireto->proximo();
                }
            }
            
        } else {
            $boFlagTransacao = false;
            $obErro = $obROrcamentoRecurso->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );            
            
            if (!$obErro->ocorreu()) {
                //faz o update para cada ano até o ano_final do ppa
                for ($stExercicio; $stExercicio <= $stExercicioFinal; $stExercicio++) {
                    if (!$obErro->ocorreu()) {
                        $obROrcamentoRecurso->setCodRecurso     ($_POST['inCodRecurso']);
                        $obROrcamentoRecurso->setExercicio      ($stExercicio);
                        $obROrcamentoRecurso->setNome           ($_POST['stNome']);
                        $obROrcamentoRecurso->setFinalidade     ($_POST['stFinalidade']);
                        $obROrcamentoRecurso->setTipo           ($_POST['stTipo']);
                        $obROrcamentoRecurso->setCodFonteRecurso($_POST['inCodFonteRecurso']);
                        $obROrcamentoRecurso->setCodigoTC       ($_POST['inCodigoTC']);
                        $obROrcamentoRecurso->setTipoEsfera     ($_POST['inTipoEsfera']);
                        $obErro = $obROrcamentoRecurso->alterar ($boTransacao);
                    }
                }
                
                if (!$obErro->ocorreu() && $request->get('inCodTCE') != '') {
                    include_once ( CAM_GF_ORC_MAPEAMENTO."TTCEPECodigoFonteRecurso.class.php" );
                    $obTTCEPECodigoFonteRecurso = new TTCEPECodigoFonteRecurso;
                    
                    $obTTCEPECodigoFonteRecurso->setDado ('cod_recurso', $request->get('inCodRecurso'));
                    $obTTCEPECodigoFonteRecurso->setDado ('exercicio'  , $request->get('stExercicio'));
                    $obTTCEPECodigoFonteRecurso->recuperaPorChave($rsCodigoFonteRecurso, $boTransacao);
                    
                    $obTTCEPECodigoFonteRecurso->setDado ('cod_fonte', $request->get('inCodTCE'));
                    $obTTCEPECodigoFonteRecurso->setDado ('exercicio', Sessao::getExercicio());
                    $obTTCEPECodigoFonteRecurso->setDado ('cod_recurso', $request->get('inCodRecurso'));
                    
                    if ($rsCodigoFonteRecurso->getNumLinhas() < 1){
                        $obErro = $obTTCEPECodigoFonteRecurso->inclusao($boTransacao);
                    } else {
                        $obErro = $obTTCEPECodigoFonteRecurso->alteracao($boTransacao);
                    }
                }
            }
            $obROrcamentoRecurso->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obROrcamentoRecurso );
        }
        
        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');
        foreach ($arFiltro as $stCampo => $stValor) {
            $stFiltro .= $stCampo."=". $stValor ."&";
        }
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];
        
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".$stFiltro,"Recurso: ".$_POST['stNome'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
        
    break;

    case "excluir":
        $boFlagTransacao = false;
        $obErro = $obROrcamentoRecurso->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $obROrcamentoRecurso->setCodRecurso( $_GET['inCodRecurso'] );
        
        //faz o update para cada ano até o ano_final do ppa
        for ($stExercicio; $stExercicio <= $stExercicioFinal; $stExercicio++) {
            if (!$obErro->ocorreu()) {
                $obROrcamentoRecurso->setExercicio($stExercicio);
                $obErro = $obROrcamentoRecurso->excluir($boTransacao);
            }
        }
        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');
    
        foreach ($arFiltro as $stCampo => $stValor) {
            foreach($arFiltro[$stCampo] as $stCampo2 => $value){
                $stFiltro .= $stCampo2."=".urlencode( $value )."&";
            }
        }
        
        $obROrcamentoRecurso->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obROrcamentoRecurso );
        
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir&".$stFiltro,"Recurso: ".$_GET['stDescricao'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir&".$stFiltro,urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;

}

?>
