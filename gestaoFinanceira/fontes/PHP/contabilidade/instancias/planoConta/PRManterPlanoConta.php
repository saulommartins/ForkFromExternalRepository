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

    * Página de Processamento de Plano de Contas
    * Data de Criação   : 04/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: PRManterPlanoConta.php 66258 2016-08-03 14:25:21Z evandro $

    * Casos de uso: uc-02.02.02
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php";
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEspecificacaoDestinacaoRecurso.class.php';
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaEncerrada.class.php";
include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterPlanoConta";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;

$stAcao = $request->get('stAcao');

include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );
$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$boDestinacao = $obRConfiguracaoOrcamento->consultarConfiguracaoEspecifica('recurso_destinacao', $boTransacao);

$stFiltroEntidade = " AND entidade.cod_entidade = ".Sessao::getCodEntidade($boTransacao)." AND entidade.exercicio = '".Sessao::getExercicio()."'";
$obTEntidade = new TEntidade;
$obTEntidade->recuperaEntidades($rsEntidades, $stFiltroEntidade, '', $boTransacao);

if($stAcao != 'excluir') {
    if($_REQUEST['stCodClass'] != SistemaLegado::doMask($_REQUEST['stCodClass'])) {
        SistemaLegado::exibeAviso('É obrigatório o preenchimento correto do campo Código de Classificação', 'n_incluir', 'erro');
        exit;
    }
    /* 
    #23315
    Validação ao cadastrar uma nova conta com fonte de recurso o sistema deve verificar se neste grupos
    72112. 72.111, 82.111,82.112,82.113 e 82.114 já existe uma conta com esta mesma fonte de recurso.
    Até porque já existe uma regra estabelecida como uma unica conta por fonte de recurso.
    */

    // Adicionada validação porque campo inCodRecurso não é obrigatório
    if($request->get('inCodRecurso') != '') {
        $obTOrcamentoRecurso = new TOrcamentoRecurso;
        $obTOrcamentoRecurso->setDado('exercicio'     , Sessao::getExercicio() );
        $obTOrcamentoRecurso->setDado('cod_estrutural', $request->get('stCodClass') );
        $obTOrcamentoRecurso->setDado('cod_recurso'   , $request->get('inCodRecurso') );
        $obTOrcamentoRecurso->setDado('cod_conta'     , $request->get('inCodConta'));
        $obErro = $obTOrcamentoRecurso->verificaContaRecurso($rsContaRecurso100, $boTransacao);

        if ($rsContaRecurso100->getNumLinhas() > 0) {
            SistemaLegado::exibeAviso("Já existe uma conta com o recurso ".$request->get('inCodRecurso'). " nesse grupo de contas", 'n_incluir', 'aviso');
            exit;
        }
    }
}

if ( $stAcao == 'incluir' || $stAcao == 'alterar') {
    if ( ($request->get('inCodRecursoContraPartida') != '') && ($request->get('inCodRecurso') == '') ) {
        SistemaLegado::exibeAviso("Atenção: preencher o campo Recurso!", 'aviso', 'aviso');
        exit;
    }
}

switch ($stAcao) {
    case "incluir":
        $obErro = new Erro;
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $obTOrcamentoRecurso = new TOrcamentoRecurso;
        $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
        $obTOrcamentoEspecificacaoDestinacaoRecurso = new TOrcamentoEspecificacaoDestinacaoRecurso;

        if ($_POST['stTipoConta'] == 'A') {
            if ($_REQUEST['stNatSaldo']) {
                $stNatSaldo = $_REQUEST['stNatSaldo'];
            } else {
                SistemaLegado::exibeAviso("Campo Natureza do Saldo é obrigatório.","n_incluir","erro");
                exit;
            }
        }

        if (($_REQUEST['stCodClass'][0] == 3 || $_REQUEST['stCodClass'][0] == 9) && $_REQUEST['stNatSaldo'] == 'C') {
            SistemaLegado::exibeAviso("Contas do Grupo ".$_REQUEST['stCodClass'][0]." só podem ser de natureza 'Devedor'.","n_incluir","erro");
            exit;
        }

        if ($_REQUEST['stCodClass'][0] == 4 && $_REQUEST['stNatSaldo'] == 'D') {
            SistemaLegado::exibeAviso("Contas do Grupo 4 só podem ser de natureza 'Credor'.","n_incluir","erro");
            exit;
        }

        if ($_REQUEST['inCodSistemaContabil']) {
            $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setCodSistema( $_POST['inCodSistemaContabil'] );
            $obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setCodClassificacao( $_POST['inCodClassContabil'] );
            $obRContabilidadePlanoBanco->setCodEstrutural( $_POST['stCodClass'] );
            $obRContabilidadePlanoBanco->setNomConta( $_POST['stDescrConta'] );
            $obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
            $obRContabilidadePlanoBanco->setNatSaldo( $stNatSaldo );
            
            if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16) {
                $obRContabilidadePlanoBanco->setTipoContaCorrenteTCEPE($_POST['inTipoContaCorrenteTCEPE']);
            }
            
            if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 11) {
                $obRContabilidadePlanoBanco->setTipoContaCorrenteTCEMG($_REQUEST['inTipoContaCorrenteTCEMG']);
            }
            
            if ( Sessao::getExercicio() > '2012' ) {
                $stNaturezaSaldo = '';
                switch ($_REQUEST['stNatSaldo']) {
                    case 'D':
                        $stNaturezaSaldo = 'devedor';
                        break;
                    case 'C':
                        $stNaturezaSaldo = 'credor';
                        break;
                    case 'X':
                        $stNaturezaSaldo = 'misto';
                        break;
                }
                $obRContabilidadePlanoBanco->setNaturezaSaldo( $stNaturezaSaldo );
                $obRContabilidadePlanoBanco->setEscrituracao( $_POST['stTipoConta'] == 'A' ? 'analitica' : 'sintetica' );
                $obRContabilidadePlanoBanco->setIndicadorSuperavit( trim($_POST['stIndicadorSuperavit']) );
                $obRContabilidadePlanoBanco->setFuncao( $_POST['stFuncao'] );
                //para o tribunal o cod_classificação é fixo como 4 (Outros)
                $obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setCodClassificacao( 4 );
            }

            if(trim($request->get('inCodRecursoContraPartida')) == '')
                $inCodRecursoContraPartida = 'null';
            else
                $inCodRecursoContraPartida = $request->get('inCodRecursoContraPartida');
            
            if ($_POST['stTipoConta'] == 'A') {
                $obRContabilidadePlanoBanco->setContaAnalitica( true );
                if ( $boDestinacao == 'true' &&  !Sessao::getExercicio() > '2012' ) {
                    if ($_REQUEST['stDestinacaoRecurso'] != '') {
                        $arDestinacaoRecurso = explode('.',$_REQUEST['stDestinacaoRecurso']);

                        $stFiltroBuscaExiste  = ' WHERE exercicio = '.Sessao::getExercicio().' ';
                        $stFiltroBuscaExiste .= '   AND cod_uso = '.$arDestinacaoRecurso[0].' ';
                        $stFiltroBuscaExiste .= '   AND cod_destinacao = '.$arDestinacaoRecurso[1].' ';
                        $stFiltroBuscaExiste .= '   AND cod_especificacao = '.$arDestinacaoRecurso[2].' ';
                        $stFiltroBuscaExiste .= '   AND cod_detalhamento = '.$arDestinacaoRecurso[3].' ';
                        $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltroBuscaExiste, '', $boTransacao);
                        $inCodRecursoExiste = $rsDestinacao->getCampo('cod_recurso');

                        if ($inCodRecursoExiste == '') {
                            $obTOrcamentoRecurso->setDado("exercicio", Sessao::getExercicio() );
                            $obTOrcamentoRecurso->proximoCod( $inCodRecurso, $boTransacao );
                            $obTOrcamentoRecurso->setDado("cod_recurso", $inCodRecurso );
                            $obErro = $obTOrcamentoRecurso->inclusao( $boTransacao );
                            if (!$obErro->ocorreu()) {
                                $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio() );
                                $obTOrcamentoRecursoDestinacao->setDado("cod_recurso", $inCodRecurso          );
                                $obTOrcamentoRecursoDestinacao->setDado("cod_uso", $arDestinacaoRecurso[0]);
                                $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao", $arDestinacaoRecurso[1]);
                                $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2]);
                                $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3]);
                                $obErro = $obTOrcamentoRecursoDestinacao->inclusao($boTransacao);

                                $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida );
                            }

                            if (Sessao::getExercicio() > '2008' && !$obErro->ocorreu()) {
                                $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('exercicio', Sessao::getExercicio());
                                $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                                $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaPorChave($rsEspecificacao, $boTransacao);
                                $stNomEspecificacao = $rsEspecificacao->getCampo('descricao');

                                // Verifica qual o cod_recurso que possui conta contabil vinculada C
                                $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                $obTOrcamentoRecursoDestinacao->setDado("cod_recurso", '');
                                $obTOrcamentoRecursoDestinacao->setDado("cod_uso", '');
                                $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao", '');
                                $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", '');
                                $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                                $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                                $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoC, '', '', $boTransacao);

                                $inCodRecursoBuscaC = $rsContaRecursoC->getCampo('cod_recurso');

                                if ($inCodRecursoBuscaC == '') {
                                    if (!$obErro->ocorreu()) {
                                        $obRContabilidadePlanoBancoC = new RContabilidadePlanoBanco;
                                        $obRContabilidadePlanoBancoC->setCodEstrutural('2.9.3.2.0.00.00.');
                                        $obRContabilidadePlanoBancoC->getProximoEstruturalRecurso($rsProxCod, $boTransacao);
                                        $inProximoCodEstruturalC = $rsProxCod->getCampo('prox_cod_estrutural');
                                        if ($inProximoCodEstruturalC != 99) {
                                            $obRContabilidadePlanoBancoC->obRContabilidadeSistemaContabil->setCodSistema(4);
                                            $obRContabilidadePlanoBancoC->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                            $inProximoCodEstruturalC++;
                                            $inProximoCodEstruturalC = str_pad($inProximoCodEstruturalC, 2, "0", STR_PAD_LEFT);
                                            $stCodEstruturalC = '2.9.3.2.0.00.00.'.$inProximoCodEstruturalC.'.00.00';
                                            $obRContabilidadePlanoBancoC->setCodEstrutural($stCodEstruturalC);
                                            $obRContabilidadePlanoBancoC->setNomConta($stNomEspecificacao);
                                            $obRContabilidadePlanoBancoC->setExercicio(Sessao::getExercicio());
                                            $obRContabilidadePlanoBancoC->setNatSaldo('C');
                                            $obRContabilidadePlanoBancoC->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                            $obRContabilidadePlanoBancoC->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida );
                                            $obRContabilidadePlanoBancoC->setContaAnalitica(true);

                                            $obErro = $obRContabilidadePlanoBancoC->salvar($boTransacao, false);
                                        } else {
                                            SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                                        }
                                    }
                                }

                                // Verifica qual o cod_recurso que possui conta contabil vinculada D
                                $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                                $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'1.9.3.2.0.00.00.%'");
                                $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoD, '', '', $boTransacao);

                                $inCodRecursoBuscaD = $rsContaRecursoD->getCampo('cod_recurso');

                                if ($inCodRecursoBuscaD == '') {
                                    if (!$obErro->ocorreu()) {
                                        $obRContabilidadePlanoBancoD = new RContabilidadePlanoBanco;
                                        $obRContabilidadePlanoBancoD->setCodEstrutural('1.9.3.2.0.00.00.');
                                        $obRContabilidadePlanoBancoD->getProximoEstruturalRecurso($rsProxCodD, $boTransacao);
                                        $inProximoCodEstruturalD = $rsProxCodD->getCampo('prox_cod_estrutural');
                                        if ($inProximoCodEstruturalD != 99) {
                                            $obRContabilidadePlanoBancoD->obRContabilidadeSistemaContabil->setCodSistema(4);
                                            $obRContabilidadePlanoBancoD->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                            $inProximoCodEstruturalD++;
                                            $inProximoCodEstruturalD = str_pad($inProximoCodEstruturalD, 2, "0", STR_PAD_LEFT);
                                            $stCodEstruturalD = '1.9.3.2.0.00.00.'.$inProximoCodEstruturalD.'.00.00';
                                            $obRContabilidadePlanoBancoD->setCodEstrutural($stCodEstruturalD);
                                            $obRContabilidadePlanoBancoD->setNomConta($stNomEspecificacao);
                                            $obRContabilidadePlanoBancoD->setExercicio(Sessao::getExercicio());
                                            $obRContabilidadePlanoBancoD->setNatSaldo('D');
                                            $obRContabilidadePlanoBancoD->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                            $obRContabilidadePlanoBancoD->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida );
                                            $obRContabilidadePlanoBancoD->setContaAnalitica(true);

                                            $obErro = $obRContabilidadePlanoBancoD->salvar($boTransacao);
                                        } else {
                                            SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                                        }
                                    }
                                }
                            }
                        } else {
                            $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecursoExiste);
                            $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida );
                        }
                    }
                } else {
                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso( $_POST['inCodRecurso'] );
                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida );
                }
            }

            if ($_POST['boContaBanco']) {
                $obRContabilidadePlanoBanco->obRMONBanco->setCodBanco( $_POST['inCodBanco'] );
                $obRContabilidadePlanoBanco->obRMONAgencia->setCodAgencia( $_POST['inCodAgencia'] );

                if ($_POST['inCodEntidade'] == '' || $_POST['inCodEntidade'] == null) {
                    $obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade( $_POST['hdnCodEntidade'] );
                } else {
                    $obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade']);
                }
                $obRContabilidadePlanoBanco->setContaCorrente( $_POST['stContaCorrente'] );
                $obRContabilidadePlanoBanco->setCodContaCorrente( $_POST['inContaCorrente'] );
                
                if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16) {
                    $obRContabilidadePlanoBanco->setTipoContaTCEPE($_POST['stTipoContaTCEPE']);
                }
                /*
                if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 11) {
                    $obRContabilidadePlanoBanco->setTipoContaTCEMG($_POST['stTipoContaTCEMG']);
                }
                */
            }
            
            if( !$obErro->ocorreu() ) {
                $obErro = $obRContabilidadePlanoBanco->salvar($boTransacao);
            }

            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

            if( !$obErro->ocorreu() )
                if( $_POST['stTipoConta'] == 'A' )
                    SistemaLegado::alertaAviso($pgForm, $obRContabilidadePlanoBanco->getCodPlano() ." - ". $_POST['stCodClass']." - ".$_POST['stDescrConta'], "incluir", "aviso", Sessao::getId(), "../");
                else
                    SistemaLegado::alertaAviso($pgForm, $_POST['stCodClass']." - ".$_POST['stDescrConta'], "incluir", "aviso", Sessao::getId(), "../");
            else
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

        } else {
            SistemaLegado::exibeAviso("Campo Sistema Contábil é obrigatório.","n_incluir","erro");
        }

    break;
    case "alterar":
        $obErro = new Erro;
        $obTOrcamentoRecurso = new TOrcamentoRecurso;
        $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
        $obTOrcamentoEspecificacaoDestinacaoRecurso = new TOrcamentoEspecificacaoDestinacaoRecurso;

        if ($_POST['stTipoConta'] == 'A') {
            if ($_REQUEST['stNatSaldo']) {
                $stNatSaldo = $_REQUEST['stNatSaldo'];
            } else {
                SistemaLegado::exibeAviso("Campo Natureza do Saldo é obrigatório.","n_incluir","erro");
                exit;
            }
        }

    if (($_REQUEST['stCodClass'][0] == 3 || $_REQUEST['stCodClass'][0] == 9) && $_REQUEST['stNatSaldo'] == 'C') {
            SistemaLegado::exibeAviso("Contas do Grupo ".$_REQUEST['stCodClass'][0]." só podem ser de natureza 'Devedor'.","n_incluir","erro");
            exit;
        }

        if ($_REQUEST['stCodClass'][0] == 4 && $_REQUEST['stNatSaldo'] == 'D') {
            SistemaLegado::exibeAviso("Contas do Grupo 4 só podem ser de natureza 'Credor'.","n_incluir","erro");
            exit;
        }

        if ($_REQUEST['inCodSistemaContabil']) { 
            $obRContabilidadePlanoBanco->setCodConta( $_POST['inCodConta'] );
            $obRContabilidadePlanoBanco->setCodPlano( $_POST['inCodPlano'] );
            $obRContabilidadePlanoBanco->setNatSaldo( $stNatSaldo          );
            $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setCodSistema( $_POST['inCodSistemaContabil'] );
            $obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setCodClassificacao( $_POST['inCodClassContabil'] );
            $obRContabilidadePlanoBanco->setCodEstrutural( $_POST['stCodClass'] );
            $obRContabilidadePlanoBanco->setNomConta( $_POST['stDescrConta'] );
            $obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
            
            if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16) {
                $obRContabilidadePlanoBanco->setTipoContaCorrenteTCEPE($_POST['inTipoContaCorrenteTCEPE']);
            }
            
            if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 11) {
                $obRContabilidadePlanoBanco->setTipoContaCorrenteTCEMG($_POST['inTipoContaCorrenteTCEMG']);
            }
            if ( Sessao::getExercicio() > '2012' ) {
                $stNaturezaSaldo = '';
                switch ($_REQUEST['stNatSaldo']) {
                    case 'D':
                        $stNaturezaSaldo = 'devedor';
                        break;
                    case 'C':
                        $stNaturezaSaldo = 'credor';
                        break;
                    case 'X':
                        $stNaturezaSaldo = 'misto';
                        break;
                }
                $obRContabilidadePlanoBanco->setNaturezaSaldo( $stNaturezaSaldo );
                $obRContabilidadePlanoBanco->setEscrituracao( $_POST['stTipoConta'] == 'A' ? 'analitica' : 'sintetica' );
                $obRContabilidadePlanoBanco->setIndicadorSuperavit( trim($_POST['stIndicadorSuperavit']) );
                $obRContabilidadePlanoBanco->setFuncao( $_POST['stFuncao'] );
            }
            //if( $_POST['inTipoConta'] == 'Analitica' )
            if ($_POST['stTipoConta'] == 'A') {
                $obRContabilidadePlanoBanco->setContaAnalitica( true );
                if ($boDestinacao == 'true' && !Sessao::getExercicio() > '2012' ) {
                    $obErro = new Erro;
                    $arDestinacaoRecurso = explode('.',$_REQUEST['stDestinacaoRecurso']);

                    if (!$_REQUEST['inCodRecurso']) {
                        $stFiltroBuscaExiste  = ' WHERE exercicio = '.Sessao::getExercicio().' ';
                        $stFiltroBuscaExiste .= '   AND cod_uso = '.$arDestinacaoRecurso[0].' ';
                        $stFiltroBuscaExiste .= '   AND cod_destinacao = '.$arDestinacaoRecurso[1].' ';
                        $stFiltroBuscaExiste .= '   AND cod_especificacao = '.$arDestinacaoRecurso[2].' ';
                        $stFiltroBuscaExiste .= '   AND cod_detalhamento = '.$arDestinacaoRecurso[3].' ';
                        $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltroBuscaExiste, '', $boTransacao);
                        $inCodRecursoExiste = $rsDestinacao->getCampo('cod_recurso');

                        if(trim($request->get('inCodRecursoContraPartida')) == '')
                            $inCodRecursoContraPartida = 'null';
                        else
                            $inCodRecursoContraPartida = $request->get('inCodRecursoContraPartida');

                        if ($inCodRecursoExiste == '') {
                            $inCodRecurso = $_REQUEST['inCodRecurso'];
                            
                            
                            $obTOrcamentoRecurso->setDado("exercicio", Sessao::getExercicio() );
                            $obTOrcamentoRecurso->proximoCod( $inCodRecurso );
                            $obTOrcamentoRecurso->setDado("cod_recurso", $inCodRecurso );
                            $obErro = $obTOrcamentoRecurso->inclusao( $boTransacao );

                            if (!$obErro->ocorreu()) {
                                $obTOrcamentoRecursoDestinacao->setDado("exercicio",        Sessao::getExercicio()        );
                                $obTOrcamentoRecursoDestinacao->setDado("cod_recurso",      $inCodRecurso             );
                                $obTOrcamentoRecursoDestinacao->setDado("cod_uso",          $arDestinacaoRecurso[0]   );
                                $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao",   $arDestinacaoRecurso[1]   );
                                $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2]   );
                                $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3]   );
                                $obErro = $obTOrcamentoRecursoDestinacao->inclusao( $boTransacao );

                                $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso( $inCodRecurso );
                                $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida );
                            }

                            if (Sessao::getExercicio() > '2008') {
                                $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('exercicio', Sessao::getExercicio());
                                $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                                $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaPorChave($rsEspecificacao, $boTransacao);
                                $stNomEspecificacao = $rsEspecificacao->getCampo('descricao');

                                // Verifica qual o cod_recurso que possui conta contabil vinculada C
                                $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                $obTOrcamentoRecursoDestinacao->setDado("cod_recurso", '');
                                $obTOrcamentoRecursoDestinacao->setDado("cod_uso", '');
                                $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao", '');
                                $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", '');
                                $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                                $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                                $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoC, '', '', $boTransacao);

                                $inCodRecursoBuscaC = $rsContaRecursoC->getCampo('cod_recurso');

                                if ($inCodRecursoBuscaC == '') {
                                    if (!$obErro->ocorreu()) {
                                        $obRContabilidadePlanoBancoC = new RContabilidadePlanoBanco;
                                        $obRContabilidadePlanoBancoC->setCodEstrutural('2.9.3.2.0.00.00.');
                                        $obRContabilidadePlanoBancoC->getProximoEstruturalRecurso($rsProxCod, $boTransacao);
                                        $inProximoCodEstruturalC = $rsProxCod->getCampo('prox_cod_estrutural');
                                        if ($inProximoCodEstruturalC != 99) {
                                            $obRContabilidadePlanoBancoC->obRContabilidadeSistemaContabil->setCodSistema(4);
                                            $obRContabilidadePlanoBancoC->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                            $inProximoCodEstruturalC++;
                                            $inProximoCodEstruturalC = str_pad($inProximoCodEstruturalC, 2, "0", STR_PAD_LEFT);
                                            $stCodEstruturalC = '2.9.3.2.0.00.00.'.$inProximoCodEstruturalC.'.00.00';
                                            $obRContabilidadePlanoBancoC->setCodEstrutural($stCodEstruturalC);
                                            $obRContabilidadePlanoBancoC->setNomConta($stNomEspecificacao);
                                            $obRContabilidadePlanoBancoC->setExercicio(Sessao::getExercicio());
                                            $obRContabilidadePlanoBancoC->setNatSaldo('C');
                                            $obRContabilidadePlanoBancoC->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                            $obRContabilidadePlanoBancoC->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida );
                                            $obRContabilidadePlanoBancoC->setContaAnalitica(true);

                                            $obErro = $obRContabilidadePlanoBancoC->salvar($boTransacao, false);
                                        } else {
                                            SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                                        }
                                    }
                                }

                                // Verifica qual o cod_recurso que possui conta contabil vinculada D
                                $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                                $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'1.9.3.2.0.00.00.%'");
                                $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoD, '', '', $boTransacao);

                                $inCodRecursoBuscaD = $rsContaRecursoD->getCampo('cod_recurso');

                                if ($inCodRecursoBuscaD == '') {
                                    if (!$obErro->ocorreu()) {
                                        $obRContabilidadePlanoBancoD = new RContabilidadePlanoBanco;
                                        $obRContabilidadePlanoBancoD->setCodEstrutural('1.9.3.2.0.00.00.');
                                        $obRContabilidadePlanoBancoD->getProximoEstruturalRecurso($rsProxCodD, $boTransacao);
                                        $inProximoCodEstruturalD = $rsProxCodD->getCampo('prox_cod_estrutural');
                                        if ($inProximoCodEstruturalD != 99) {
                                            $obRContabilidadePlanoBancoD->obRContabilidadeSistemaContabil->setCodSistema(4);
                                            $obRContabilidadePlanoBancoD->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                            $inProximoCodEstruturalD++;
                                            $inProximoCodEstruturalD = str_pad($inProximoCodEstruturalD, 2, "0", STR_PAD_LEFT);
                                            $stCodEstruturalD = '1.9.3.2.0.00.00.'.$inProximoCodEstruturalD.'.00.00';
                                            $obRContabilidadePlanoBancoD->setCodEstrutural($stCodEstruturalD);
                                            $obRContabilidadePlanoBancoD->setNomConta($stNomEspecificacao);
                                            $obRContabilidadePlanoBancoD->setExercicio(Sessao::getExercicio());
                                            $obRContabilidadePlanoBancoD->setNatSaldo('D');
                                            $obRContabilidadePlanoBancoD->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                            $obRContabilidadePlanoBancoD->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida );
                                            $obRContabilidadePlanoBancoD->setContaAnalitica(true);

                                            $obErro = $obRContabilidadePlanoBancoD->salvar($boTransacao, false);
                                        } else {
                                            SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                                        }
                                    }
                                }
                            }
                        } else {
                            $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecursoExiste);
                            $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecursoContraPartida($inCodRecursoContraPartida);
                        }
                    } else {
                        if(trim($request->get('inCodRecursoContraPartida')) == '')
                            $inCodRecursoContraPartida = 'null';
                        else
                            $inCodRecursoContraPartida = $request->get('inCodRecursoContraPartida');
                        
                        if (Sessao::getExercicio() < '2009') {
                            $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio()        );
                            $obTOrcamentoRecursoDestinacao->setDado("cod_recurso", $_REQUEST['inCodRecurso'] );
                            $obTOrcamentoRecursoDestinacao->setDado("cod_uso", $arDestinacaoRecurso[0]   );
                            $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao", $arDestinacaoRecurso[1]   );
                            $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2] );
                            $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3]   );
                            $obTOrcamentoRecursoDestinacao->alteracao( $boTransacao );
                        } else {
                            $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                            $obTOrcamentoRecursoDestinacao->setDado('cod_recurso', $_REQUEST['inCodRecurso']);
                            $obTOrcamentoRecursoDestinacao->setDado("cod_uso", '');
                            $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao", '');
                            $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", '');
                            $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao", '');
                            $obTOrcamentoRecursoDestinacao->recuperaPorChave($rsEspecificacao, $boTransacao);
                            $inCodEspecificacao = $rsEspecificacao->getCampo('cod_especificacao');

                            if ($inCodEspecificacao != $arDestinacaoRecurso[2]) {
                                $stFiltroBuscaExiste  = ' WHERE exercicio = '.Sessao::getExercicio().' ';
                                $stFiltroBuscaExiste .= '   AND cod_uso = '.$arDestinacaoRecurso[0].' ';
                                $stFiltroBuscaExiste .= '   AND cod_destinacao = '.$arDestinacaoRecurso[1].' ';
                                $stFiltroBuscaExiste .= '   AND cod_especificacao = '.$arDestinacaoRecurso[2].' ';
                                $stFiltroBuscaExiste .= '   AND cod_detalhamento = '.$arDestinacaoRecurso[3].' ';
                                $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltroBuscaExiste, '', $boTransacao);
                                $inCodRecursoExiste = $rsDestinacao->getCampo('cod_recurso');

                                if ($inCodRecursoExiste == '') {
                                    $obTOrcamentoRecurso->setDado("exercicio", Sessao::getExercicio() );
                                    $obTOrcamentoRecurso->proximoCod( $inCodRecurso );
                                    $obTOrcamentoRecurso->setDado("cod_recurso", $inCodRecurso );
                                    $obErro = $obTOrcamentoRecurso->inclusao($boTransacao);
                                    if (!$obErro->ocorreu()) {
                                        $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio() );
                                        $obTOrcamentoRecursoDestinacao->setDado("cod_recurso", $inCodRecurso          );
                                        $obTOrcamentoRecursoDestinacao->setDado("cod_uso", $arDestinacaoRecurso[0]);
                                        $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao", $arDestinacaoRecurso[1]);
                                        $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2]);
                                        $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3]);

                                        $obErro = $obTOrcamentoRecursoDestinacao->inclusao( $boTransacao );

                                        $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso ( $inCodRecurso );
                                        $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida );
                                    }

                                    $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('exercicio', Sessao::getExercicio());
                                    $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                                    $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaPorChave($rsEspecificacao, $boTransacao);
                                    $stNomEspecificacao = $rsEspecificacao->getCampo('descricao');

                                    // Verifica qual o cod_recurso que possui conta contabil vinculada C
                                    $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                    $obTOrcamentoRecursoDestinacao->setDado("cod_recurso", '');
                                    $obTOrcamentoRecursoDestinacao->setDado("cod_uso", '');
                                    $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao", '');
                                    $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", '');
                                    $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                                    $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                                    $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoC, '', '', $boTransacao);

                                    $inCodRecursoBuscaC = $rsContaRecursoC->getCampo('cod_recurso');

                                    if ($inCodRecursoBuscaC == '') {
                                        if (!$obErro->ocorreu()) {
                                            $obRContabilidadePlanoBancoC = new RContabilidadePlanoBanco;
                                            $obRContabilidadePlanoBancoC->setCodEstrutural('2.9.3.2.0.00.00.');
                                            $obRContabilidadePlanoBancoC->getProximoEstruturalRecurso($rsProxCod, $boTransacao);
                                            $inProximoCodEstruturalC = $rsProxCod->getCampo('prox_cod_estrutural');
                                            if ($inProximoCodEstruturalC != 99) {
                                                $obRContabilidadePlanoBancoC->obRContabilidadeSistemaContabil->setCodSistema(4);
                                                $obRContabilidadePlanoBancoC->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                                $inProximoCodEstruturalC++;
                                                $inProximoCodEstruturalC = str_pad($inProximoCodEstruturalC, 2, "0", STR_PAD_LEFT);
                                                $stCodEstruturalC = '2.9.3.2.0.00.00.'.$inProximoCodEstruturalC.'.00.00';
                                                $obRContabilidadePlanoBancoC->setCodEstrutural($stCodEstruturalC);
                                                $obRContabilidadePlanoBancoC->setNomConta($stNomEspecificacao);
                                                $obRContabilidadePlanoBancoC->setExercicio(Sessao::getExercicio());
                                                $obRContabilidadePlanoBancoC->setNatSaldo('C');
                                                $obRContabilidadePlanoBancoC->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                                $obRContabilidadePlanoBancoC->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida );
                                                $obRContabilidadePlanoBancoC->setContaAnalitica(true);

                                                $obErro = $obRContabilidadePlanoBancoC->salvar($boTransacao, false);
                                            } else {
                                                SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                                            }
                                        }
                                    }

                                    // Verifica qual o cod_recurso que possui conta contabil vinculada D
                                    $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                    $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                                    $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'1.9.3.2.0.00.00.%'");
                                    $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoD, '', '', $boTransacao);

                                    $inCodRecursoBuscaD = $rsContaRecursoD->getCampo('cod_recurso');

                                    if ($inCodRecursoBuscaD == '') {
                                        if (!$obErro->ocorreu()) {
                                            $obRContabilidadePlanoBancoD = new RContabilidadePlanoBanco;
                                            $obRContabilidadePlanoBancoD->setCodEstrutural('1.9.3.2.0.00.00.');
                                            $obRContabilidadePlanoBancoD->getProximoEstruturalRecurso($rsProxCodD, $boTransacao);
                                            $inProximoCodEstruturalD = $rsProxCodD->getCampo('prox_cod_estrutural');
                                            if ($inProximoCodEstruturalD != 99) {
                                                $obRContabilidadePlanoBancoD->obRContabilidadeSistemaContabil->setCodSistema(4);
                                                $obRContabilidadePlanoBancoD->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                                $inProximoCodEstruturalD++;
                                                $inProximoCodEstruturalD = str_pad($inProximoCodEstruturalD, 2, "0", STR_PAD_LEFT);
                                                $stCodEstruturalD = '1.9.3.2.0.00.00.'.$inProximoCodEstruturalD.'.00.00';
                                                $obRContabilidadePlanoBancoD->setCodEstrutural($stCodEstruturalD);
                                                $obRContabilidadePlanoBancoD->setNomConta($stNomEspecificacao);
                                                $obRContabilidadePlanoBancoD->setExercicio(Sessao::getExercicio());
                                                $obRContabilidadePlanoBancoD->setNatSaldo('D');
                                                $obRContabilidadePlanoBancoD->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                                $obRContabilidadePlanoBancoD->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida );
                                                $obRContabilidadePlanoBancoD->setContaAnalitica(true);

                                                $obErro = $obRContabilidadePlanoBancoD->salvar($boTransacao, false);
                                            } else {
                                                SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                                            }
                                        }
                                    }
                                } else { // se ja existe o recurso cadastrado, so altera a conta como o novo cod_recurso
                                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecursoExiste);
                                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida );
                                }
                            } else { // se nao alterou a especificacao, altera somente o recurso
                                $stFiltroBuscaExiste  = ' WHERE exercicio = '.Sessao::getExercicio().' ';
                                $stFiltroBuscaExiste .= '   AND cod_uso = '.$arDestinacaoRecurso[0].' ';
                                $stFiltroBuscaExiste .= '   AND cod_destinacao = '.$arDestinacaoRecurso[1].' ';
                                $stFiltroBuscaExiste .= '   AND cod_especificacao = '.$arDestinacaoRecurso[2].' ';
                                $stFiltroBuscaExiste .= '   AND cod_detalhamento = '.$arDestinacaoRecurso[3].' ';
                                $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltroBuscaExiste, '', $boTransacao);
                                $inCodRecursoExiste = $rsDestinacao->getCampo('cod_recurso');

                                if ($inCodRecursoExiste == '') {
                                    $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio()        );
                                    $obTOrcamentoRecursoDestinacao->setDado("cod_recurso", $_REQUEST['inCodRecurso'] );
                                    $obTOrcamentoRecursoDestinacao->setDado("cod_uso", $arDestinacaoRecurso[0]   );
                                    $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao", $arDestinacaoRecurso[1]   );
                                    $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2] );
                                    $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3]   );
                                    $obTOrcamentoRecursoDestinacao->alteracao($boTransacao);

                                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($_REQUEST['inCodRecurso']);
                                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida);
                                } else {
                                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecursoExiste);
                                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecursoContraPartida( $inCodRecursoContraPartida );
                                }
                            }
                        }
                    }
                } else {
                    
                    if(trim($request->get('inCodRecursoContraPartida')) == '')
                        $inCodRecursoContraPartida = 'null';
                    else
                        $inCodRecursoContraPartida = $request->get('inCodRecursoContraPartida');
                    
                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso( $_POST['inCodRecurso'] );
                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecursoContraPartida($inCodRecursoContraPartida);
                }
            }

            if ($_POST['boContaBanco']) {
                $obRContabilidadePlanoBanco->obRMONBanco->setCodBanco( $_POST['inCodBanco'] );
                $obRContabilidadePlanoBanco->obRMONAgencia->setCodAgencia( $_POST['inCodAgencia'] );
    
                if ($_POST['inCodEntidade'] == '' || $_POST['inCodEntidade'] == null) {
                    $obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade( $_POST['hdnCodEntidade'] );
                } else {
                    $obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade']);
                }

                if ($_POST['stContaCorrente'] )
                    $obRContabilidadePlanoBanco->setContaCorrente( $_POST['stContaCorrente'] );
                $obRContabilidadePlanoBanco->setCodContaCorrente( $_POST['inContaCorrente'] );
                
                if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16) {
                    $obRContabilidadePlanoBanco->setTipoContaTCEPE($_POST['stTipoContaTCEPE']);
                }
                /*
                 if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 11) {
                    $obRContabilidadePlanoBanco->setTipoContaTCEMG($_POST['stTipoContaTCEMG']);
                }
                */
            }

            $stFiltro = "&pos=".Sessao::read('pos');
            $stFiltro .= "&pg=".Sessao::read('pg');

            $filtro = Sessao::read('filtro');
            foreach ($filtro as $stCampo => $stValor) {
                if (is_array($stValor)) {
                    foreach ($stValor as $stCampo2 => $stValor2) {
                         $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
                    }
                } else {
                    $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
                }
            }

        if(!$obErro->ocorreu())
            $obErro = $obRContabilidadePlanoBanco->salvar($boTransacao);

        if ( !$obErro->ocorreu() ) {
            $stMensagem = $_POST['stCodClass']." - ".$_POST['stDescrConta'];
            if ( $obRContabilidadePlanoBanco->inCodPlano )
                 $stMensagem = $obRContabilidadePlanoBanco->inCodPlano." - ".$stMensagem;
            SistemaLegado::alertaAviso($pgList."?".$stFiltro, $stMensagem, "alterar", "aviso", Sessao::getId(), "../");
        } else {
             SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
             }
        } else {
            SistemaLegado::exibeAviso("Campo Sistema Contábil é obrigatório.","n_incluir","erro");
        }
        /*if( $inCodSistemaContabil == null)
            SistemaLegado::exibeAviso("Campo Sistema Contábil é obrigatório.","n_incluir","erro");*/
    break;

case 'excluir':
    $obErro = new Erro;
    $obTransacao = new Transacao;
    $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

    require CAM_GF_TES_MAPEAMENTO.'TTesourariaCheque.class.php';

    // Realiza uma pesquisa para verificar se existe algum cheque vinculado a essa conta para poder saber se pode excluir ou não
    $rsCheque = new RecordSet;
    if ($_GET['inCodBanco'] || $_GET['inCodAgencia'] || $_GET['inContaCorrente']) {
        $obTTesourariaCheque = new TTesourariaCheque;
        $obTTesourariaCheque->setDado('cod_banco',          $_GET['inCodBanco']);
        $obTTesourariaCheque->setDado('cod_agencia',        $_GET['inCodAgencia']);
        $obTTesourariaCheque->setDado('cod_conta_corrente', $_GET['inContaCorrente']);
        $obTTesourariaCheque->recuperaPorChave($rsCheque, $boTransacao);
    }

    if (!$obErro->ocorreu()) {
        if ($rsCheque->getNumLinhas() <= 0) {
            $obRContabilidadePlanoBanco->setCodConta($_GET['inCodConta']);
            $obRContabilidadePlanoBanco->setCodPlano($_GET['inCodPlano']);
            $obRContabilidadePlanoBanco->setCodEstrutural($_GET['stCodEstrutural']);
            $obRContabilidadePlanoBanco->setExercicio(Sessao::getExercicio());
            $obErro = $obRContabilidadePlanoBanco->excluir($boTransacao);

            if ($boDestinacao == 'true' && $_REQUEST['inCodRecurso'] && !$obErro->ocorreu()) {
                $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                $obTOrcamentoRecursoDestinacao->setDado('exercicio',   Sessao::getExercicio());
                $obTOrcamentoRecursoDestinacao->setDado('cod_recurso', $_REQUEST['inCodRecurso']);
                $obErro = $obTOrcamentoRecursoDestinacao->exclusao($boTransacao);

                if (!$obErro->ocorreu()) {
                    $obTOrcamentoRecurso = new TOrcamentoRecurso;
                    $obTOrcamentoRecurso->setDado('exercicio', Sessao::getExercicio());
                    $obTOrcamentoRecurso->setDado('cod_recurso', $_REQUEST['inCodRecurso']);
                    $obErro = $obTOrcamentoRecurso->exclusao($boTransacao);
                }
            }

            $stFiltro = '&pos='.Sessao::read('pos');
            $stFiltro .= '&pg='.Sessao::read('pg');
            $filtro = Sessao::read('filtro');
            foreach ($filtro as $stCampo => $stValor) {
                if (is_array($stValor)) {
                    foreach ($stValor as $stCampo2 => $stValor2) {
                         $stFiltro .= '&'.$stCampo2.'='.urlencode($stValor2);
                    }
                } else {
                    $stFiltro .= '&'.$stCampo.'='.urlencode($stValor);
                }
            }
        } else {
            $obErro->setDescricao('Não é possível excluir a conta pois ela possui cheques vinculados a ela.');
        }

        if (!$obErro->ocorreu()) {
            $obTransacao->commitAndClose();
            SistemaLegado::alertaAviso($pgList.'?stAcao=excluir&'.$stFiltro, $_GET['inCodPlano']." - ". $_GET['stCodEstrutural'].' - '.$_GET['stNomConta'], 'excluir', 'aviso', Sessao::getId(), '../');

        } else {
            $obTransacao->rollbackAndClose();
            if (strpos($obErro->getDescricao(), 'fk_')) {
                $obErro->setDescricao('Conta não pode ser excluída porque possui lançamentos.');
            }
            SistemaLegado::alertaAviso($pgList.'?stAcao=excluir&'.$stFiltro, urlencode($obErro->getDescricao()), 'n_excluir', 'erro', Sessao::getId(), '../');
        }

    } else {
        SistemaLegado::exibeAviso('Houve um problema ao excluir a conta.', 'n_incluir', 'erro');
    }

    break;
}
?>
