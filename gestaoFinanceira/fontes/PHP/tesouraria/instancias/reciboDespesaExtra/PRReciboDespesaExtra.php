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
    * Pagina de processamento de Recibo de Despesa Extra
    * Data de Criação   : 28/08/2006

    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Id: PRReciboDespesaExtra.php 61703 2015-02-26 14:35:46Z arthur $

    * Casos de uso: uc-02.04.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_MAPEAMENTO.'TTesourariaReciboExtra.class.php';
include_once CAM_GF_TES_MAPEAMENTO.'TTesourariaReciboExtraBanco.class.php';
include_once CAM_GF_TES_MAPEAMENTO.'TTesourariaReciboExtraCredor.class.php';
include_once CAM_GF_TES_MAPEAMENTO.'TTesourariaReciboExtraRecurso.class.php';
include_once CAM_FW_HTML.'MontaOrgaoUnidade.class.php';

$stAcao = $request->get('stAcao');
//Define o nome dos arquivos PHP
$stPrograma = "ReciboDespesaExtra";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php?" .Sessao::getId()."&stAcao=$stAcao";
$pgForm       = "FM".$stPrograma.".php?" .Sessao::getId()."&stAcao=$stAcao";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

$obTReciboExtra = new TTesourariaReciboExtra;

SistemaLegado::BloqueiaFrames(true,true);

switch ($stAcao) {

    case 'incluir':

        $obTReciboExtra->setDado ('cod_entidade'      , $_POST['inCodEntidade']       );

        ///// Validações

        /// Data de emissao deve ser maior ou igual a do ultimo recibo e menor ou igual a data atual

        /////pegando a data do ultimo recibo de Receita
        $obTReciboExtra->setDado ('tipo_recibo','D');
        $obTReciboExtra->setDado ('exercicio',Sessao::getExercicio());
        $obTReciboExtra->recuperaUltimaDataRecibo( $rsDataRecibo );

        $stUltimaData = substr($rsDataRecibo->getCampo( 'data' ), 0, 10 );
        $stUltimaData = explode (  '-', $stUltimaData );
        $stUltimaData = $stUltimaData[2].'/'.$stUltimaData[1].'/'.$stUltimaData[0];

        if ($stUltimaData == '//') {
            $stUltimaData = '01/01/'.Sessao::getExercicio();
        }

        $obErro = new Erro;

        if ( sistemaLegado::comparaDatas ($stUltimaData,  $_POST['dtDataEmissao'])) {
            $obErro->setDescricao( 'A data de emissão do recibo deve ser maior ou igual a última data cadastrada.' );
            SistemaLegado::executaFrameOculto( "window.parent.frames['telaPrincipal'].document.frm.Ok.disabled = false;" );
        }

        if ( !$obErro->ocorreu() ) {
            if ( sistemaLegado::comparaDatas ($_POST['dtDataEmissao'], date('d/m/Y') )) {
                $obErro->setDescricao( 'A data de emissão do recibo deve ser menor ou igual a data atual.' );
                SistemaLegado::executaFrameOculto( "window.parent.frames['telaPrincipal'].document.frm.Ok.disabled = false;" );
            }
        }

        if ( !$obErro->ocorreu() ) {
            if ( str_replace(',','.',str_replace('.','',$_POST['txtValor'])) <= 0.00 ) {
                $obErro->setDescricao('O valor deve ser maior do que zero.');
                SistemaLegado::executaFrameOculto( "window.parent.frames['telaPrincipal'].document.frm.Ok.disabled = false;" );
            }
        }

        if ( !$obErro->ocorreu() ) {
            Sessao::setTrataExcecao(true);
            Sessao::getTransacao()->setMapeamento( $obTReciboExtra );

            /////// Gerando o proximo codigo de registro
            /////////////descobrindo como gerar o codigo
            include_once ( CAM_GF_EMP_MAPEAMENTO.'TEmpenhoConfiguracao.class.php' );
            $obTEmpenhoConfiguracao = new TEmpenhoConfiguracao;
            $obTEmpenhoConfiguracao->setDado( 'parametro', 'numero_empenho' );
            $obTEmpenhoConfiguracao->consultar ();
            $obTReciboExtra->proximoCod( $inCodigoRecibo, $obTEmpenhoConfiguracao->getDado( 'valor' ) );

            $obTReciboExtra->setDado ('cod_recibo_extra'  , $inCodigoRecibo                );
            $obTReciboExtra->setDado ('exercicio'         , Sessao::getExercicio()             );
            $obTReciboExtra->setDado ('timestamp_usuario' , $_POST['stTimestampUsuario']   );
            $obTReciboExtra->setDado ('cgm_usuario'       , Sessao::read('numCgm')                );
            $obTReciboExtra->setDado ('timestamp_terminal', $_POST['stTimestampTerminal']  );
            $obTReciboExtra->setDado ('cod_terminal'      , $_POST['inCodTerminal']        );
            $obTReciboExtra->setDado ('cod_plano'         , $_POST['inCodContaDespesa']    );
            $obTReciboExtra->setDado ('historico'         , $_POST['txtHistorico']         );
            $obTReciboExtra->setDado ('timestamp'         , substr($_POST['dtDataEmissao'], 6, 4).'-'.
                                                            substr($_POST['dtDataEmissao'], 3, 2).'-'.
                                                            substr( $_POST['dtDataEmissao'],0,2). date( ' H:i:s.ms' ) );

            $obTReciboExtra->setDado ('valor'             , $_POST['txtValor']             );
            $obTReciboExtra->setDado ('tipo_recibo'       , 'D'                            );
            $obTReciboExtra->inclusao();

            ////// ligação com conta banco
            if ($_POST['inCodContaBanco']) {
                $obTReciboExtrabanco = new TTesourariaReciboExtraBanco ;
                $obTReciboExtrabanco->obTTesourariaReciboExtra = &$obTReciboExtra;
                $obTReciboExtrabanco->setDado( 'cod_plano', $_POST['inCodContaBanco'] );
                $obTReciboExtrabanco->inclusao();
            }

            ////// ligação com CGM do credor
            if ($_POST['inCodCredor']) {
                $obCredor = new TTesourariaReciboExtraCredor;
                $obCredor->obTTesourariaReciboExtra = & $obTReciboExtra;
                $obCredor->setDado ( 'numcgm', $_POST['inCodCredor'] );
                $obCredor->inclusao();
            }

            //// ligação con recurso
            include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );
            $obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
            $obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
            $obRConfiguracaoOrcamento->consultarConfiguracao();
            $boDestinacao = $obRConfiguracaoOrcamento->getDestinacaoRecurso();
            if ($boDestinacao == 'true') {
                if ($_REQUEST['stDestinacaoRecurso']) {
                    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php" );
                    $obTOrcamentoRecurso = new TOrcamentoRecurso;
                    $obTOrcamentoRecurso->setDado("exercicio", Sessao::getExercicio() );
                    $obTOrcamentoRecurso->proximoCod( $inCodRecurso );
                    $obTOrcamentoRecurso->setDado("cod_recurso", $inCodRecurso );
                    $obErro = $obTOrcamentoRecurso->inclusao( $boTransacao );
                    if (!$obErro->ocorreu()) {
                        $arDestinacaoRecurso = explode('.',$_REQUEST['stDestinacaoRecurso']);

                        include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php" );
                        $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                        $obTOrcamentoRecursoDestinacao->setDado("exercicio",        Sessao::getExercicio()      );
                        $obTOrcamentoRecursoDestinacao->setDado("cod_recurso",      $inCodRecurso           );
                        $obTOrcamentoRecursoDestinacao->setDado("cod_uso",          $arDestinacaoRecurso[0] );
                        $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao",   $arDestinacaoRecurso[1] );
                        $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2] );
                        $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3] );
                        $obErro = $obTOrcamentoRecursoDestinacao->inclusao( $boTransacao );

                        $obRecurso = new TTesourariaReciboExtraRecurso;
                        $obRecurso->obTTesourariaReciboExtra = $obTReciboExtra;
                        $obRecurso->setDado ( 'cod_recurso',  $inCodRecurso ) ;
                        $obRecurso->inclusao();
                    }
                }
            } else {
                if ( ( $_POST['inCodRecurso'] ) and ( !$obErro->ocorreu() ) ) {
                    $obRecurso = new TTesourariaReciboExtraRecurso;
                    $obRecurso->obTTesourariaReciboExtra = $obTReciboExtra;
                    $obRecurso->setDado ( 'cod_recurso',  $_POST['inCodRecurso'] ) ;
                    $obRecurso->inclusao();
                }
            }

            /* Salvar assinaturas configuráveis se houverem */
            $arAssinaturas = Sessao::read('assinaturas');
            if ( isset($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0 ) {

                include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaReciboExtraAssinatura.class.php" );
                $arAssinatura = $arAssinaturas['selecionadas'];

                $obTTesReciboExtraAssinatura = new TTesourariaReciboExtraAssinatura;
                $obTTesReciboExtraAssinatura->setDado( 'exercicio', $obTReciboExtra->getDado('exercicio') );
                $obTTesReciboExtraAssinatura->setDado( 'cod_entidade', $obTReciboExtra->getDado('cod_entidade') );
                $obTTesReciboExtraAssinatura->setDado( 'cod_recibo_extra', $obTReciboExtra->getDado('cod_recibo_extra') );
                $obTTesReciboExtraAssinatura->setDado( 'tipo_recibo', 'D' );
                $arPapel = $obTTesReciboExtraAssinatura->arrayPapel();

                foreach ($arAssinatura as $arAssina) {
                    if (count($arAssinatura) < 2) {
                    $stPapel = (isset($arAssina['papel'])) ? $arAssina['papel'] : 0;
                    $inNumAssina = (isset($arPapel[$stPapel])) ? $arPapel[$stPapel] : 1;
                    $obTTesReciboExtraAssinatura->setDado( 'num_assinatura', $inNumAssina );
                    $obTTesReciboExtraAssinatura->setDado( 'numcgm', $arAssina['inCGM'] );
                    $obTTesReciboExtraAssinatura->setDado( 'cargo', $arAssina['stCargo'] );
                    $obErro = $obTTesReciboExtraAssinatura->inclusao( $boTransacao );
                    } else {
                    $stPapel = (isset($arAssina['papel'])) ? $arAssina['papel'] : 0;
                    $inNumAssina = (isset($arPapel[$stPapel])) ? $arPapel[$stPapel] : 1;
                    $obTTesReciboExtraAssinatura->setDado( 'num_assinatura', $inNumAssina );
                    $obTTesReciboExtraAssinatura->setDado( 'numcgm', $arAssina['inCGM'] );
                    $obTTesReciboExtraAssinatura->setDado( 'cargo', $arAssina['stCargo'] );
                    $obTTesReciboExtraAssinatura->inclusao( $boTransacao );
                    //$obErro = $obTTesReciboExtraAssinatura->inclusao( $boTransacao );
                    //$obErro->setDescricao("Só é permitido uma assinatura!");
                    }
                }
                unset($obTTesReciboExtraAssinatura);
                // Limpa Sessao->assinaturas
                Sessao::remove('assinaturas');

            }

            Sessao::encerraExcecao();

            if ( $obErro->ocorreu() ) {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                SistemaLegado::executaFrameOculto( "window.parent.frames['telaPrincipal'].document.frm.Ok.disabled = false;" );
            } else {
                sistemaLegado::alertaAviso($pgForm,"Recibo de Despesa Extra ".$inCodigoRecibo . "/" . Sessao::getExercicio(), $stAcao,"aviso", Sessao::getId(), "../");
                $stCaminho = CAM_GF_TES_INSTANCIAS . 'reciboDespesaExtra/OCRelatorioReciboDespesaExtra.php';
                $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&cod_recibo_extra=".$inCodigoRecibo."&inCodEntidade=".$_POST['inCodEntidade'];

                // o post foi passado pra sessão para poder ser usado no arquivo de geração do relatório OCRelatorioReciboDespesaExtra

                Sessao::write('post', $_POST);
                Sessao::write('exercicio', Sessao::getExercicio());
                Sessao::write('numeroRecibo', $inCodigoRecibo);

                SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
            }

        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            SistemaLegado::executaFrameOculto( "window.parent.frames['telaPrincipal'].document.frm.Ok.disabled = false;" );
        }
    break;

    case 'imprimir':
        /// gerando o recibo impresso

        $stFiltro .= 'where recibo_extra.cod_recibo_extra  = '.$_GET['inCodRecibo'];
        $stFiltro .= "  and recibo_extra.exercicio         = '".$_GET['stExercicio']."'";
        $stFiltro .= '  and recibo_extra.cod_entidade      = '.$_GET['inCodEntidade'];
        $stFiltro .= "  and recibo_extra.tipo_recibo       = 'D'";

        $obTReciboExtra->setDado('cod_recibo_extra', $_GET['inCodRecibo'] );
        $obTReciboExtra->setDado('cod_entidade', $_GET['inCodEntidade'] );
        $obTReciboExtra->setDado('exercicio', $_GET['stExercicio'] );
        $obTReciboExtra->setDado('tipo_recibo', 'D' );

        $obTReciboExtra->recuperaRelacionamento ( $rsReciboExtra, $stFiltro );

        $arDados = array();
        $arDados['exercicio']         = $rsReciboExtra->getCampo('exercicio');
        $arDados['inCodEntidade']     = $rsReciboExtra->getCampo('cod_entidade');
        $arDados['dtDataEmissao']     = $rsReciboExtra->getCampo('timestamp');
        $arDados['txtValor']          = number_format($rsReciboExtra->getCampo('valor'),'2',',','.');
        $arDados['inCodCredor']       = $rsReciboExtra->getCampo('cod_credor');
        $arDados['stNomCredor']       = $rsReciboExtra->getCampo('nom_cgm_credor');
        $arDados['inCodContaDespesa'] = $rsReciboExtra->getCampo('cod_plano_despesa');
        $arDados['inCodContaBanco']   = $rsReciboExtra->getCampo('cod_plano_banco');
        $arDados['txtHistorico']      = $rsReciboExtra->getCampo('historico');
        $arDados['inCodRecurso']      = $rsReciboExtra->getCampo('cod_recurso');
        $arDados['stDescricaoRecurso']= $rsReciboExtra->getCampo('nom_recurso');
        $arDados['numeroRecibo']      = $rsReciboExtra->getCampo('cod_recibo_extra');
        Sessao::write('post',$arDados);

        $stCaminho = CAM_GF_TES_INSTANCIAS . 'reciboDespesaExtra/OCRelatorioReciboDespesaExtra.php';
        $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&cod_recibo_extra=".$inCodigoRecibo."&inCodEntidade=".$_GET['inCodEntidade'];
        SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );

    break;

    case 'excluir':
        include_once ( CAM_GF_TES_MAPEAMENTO . 'TTesourariaReciboExtraTransferencia.class.php' );
    include_once ( CAM_GF_TES_MAPEAMENTO . 'TTesourariaTransferenciaEstornada.class.php' );
    $obTTransfEstornada = new TTesourariaTransferenciaEstornada;

        $obTReciboExtraTransf = new TTesourariaReciboExtraTransferencia;
        $obTReciboExtraTransf->setDado ('cod_recibo_extra' ,  $_GET['inCodRecibo']   );
        $obTReciboExtraTransf->setDado ('exercicio'        ,  $_GET['stExercicio']   );
        $obTReciboExtraTransf->setDado ('cod_entidade'     ,  $_GET['inCodEntidade'] );
        $obTReciboExtraTransf->setDado ('tipo_recibo'      ,  $_GET['stTipoRecibo']  );
        $obTReciboExtraTransf->consultar();

    if ($obTReciboExtraTransf->getDado('cod_entidade')) {
        $stFiltroEstornada = " WHERE cod_entidade = ".$obTReciboExtraTransf->getDado('cod_entidade').
                 " AND exercicio = '".$obTReciboExtraTransf->getDado('exercicio').
                 "' AND cod_lote = ".$obTReciboExtraTransf->getDado('cod_lote').
                 " AND tipo = '".$obTReciboExtraTransf->getDado('tipo')."'";

        $obTTransfEstornada->recuperaTodos($rsTransfEstornada, $stFiltroEstornada);
    }

        if ( !$obTReciboExtraTransf->getDado('cod_lote') || $rsTransfEstornada->getCampo('cod_lote')) {
            include_once ( CAM_GF_TES_MAPEAMENTO . 'TTesourariaReciboExtraAnulacao.class.php' );
            $obReciboAnulacao = new TTesourariaReciboExtraAnulacao;
            $obReciboAnulacao->setDado ('cod_recibo_extra' ,  $_GET['inCodRecibo']   );
            $obReciboAnulacao->setDado ('exercicio'        ,  $_GET['stExercicio']   );
            $obReciboAnulacao->setDado ('cod_entidade'     ,  $_GET['inCodEntidade'] );
            $obReciboAnulacao->setDado ('tipo_recibo'      ,  $_GET['stTipoRecibo']  );
            $obErro = $obReciboAnulacao->inclusao();
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","Recibo de despesa anulado.","alterar","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir", $obErro->getDescricao() ,"alterar","aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir",'Já foi feita uma transferência para este recibo.' ,"n_incluir","erro", Sessao::getId(), "../");
        }

    break;

}

SistemaLegado::LiberaFrames(true,true);

?>