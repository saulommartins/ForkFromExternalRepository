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
    * Data de Criação: 29/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: PRManterManutencao.php 62009 2015-03-24 18:16:33Z evandro $

    * Casos de uso: uc-03.02.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaManutencao.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaManutencaoEmpenho.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaManutencaoAnulacao.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaManutencaoItem.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaEfetivacao.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaAutorizacao.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculo.class.php" );

$stPrograma = "ManterManutencao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTFrotaManutencao         = new TFrotaManutencao();
$obTFrotaManutencaoEmpenho  = new TFrotaManutencaoEmpenho();
$obTFrotaManutencaoAnulacao = new TFrotaManutencaoAnulacao();
$obTFrotaManutencaoItem     = new TFrotaManutencaoItem();
$obTFrotaEfetivacao         = new TFrotaEfetivacao();
$obTFrotaAutorizacao        = new TFrotaAutorizacao();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTFrotaManutencao );
Sessao::getTransacao()->setMapeamento( $obTFrotaManutencaoEmpenho );
Sessao::getTransacao()->setMapeamento( $obTFrotaManutencaoAnulacao );
Sessao::getTransacao()->setMapeamento( $obTFrotaManutencaoItem );
Sessao::getTransacao()->setMapeamento( $obTFrotaEfetivacao );

//valida capacidade do tanque
if (count(Sessao::read('arItensAutorizacao')) > 0) {
    foreach ( Sessao::read('arItensAutorizacao') AS $arTemp ) {
        $obTFrotaVeiculo = new TFrotaVeiculo();
        $obTFrotaVeiculo->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
        $obTFrotaVeiculo->recuperaVeiculoAnalitico( $rsVeiculo );
        $inQuantidade = (int) $arTemp['quantidade'];
        if ($inQuantidade > $rsVeiculo->getCampo('capacidade_tanque')) {
            $stMensagem = 'Capacidade do tanque não informada ou acima do limite '.$rsVeiculo->getCampo('capacidade_tanque').'';
        }
    }
}

switch ($stAcao) {
    case 'incluir' :
        if ( count( Sessao::read('arItensAutorizacao') ) <= 0 ) {
            $stMensagem = 'É necessário pelo menos um item para a manutenção';
        }
        if ($_REQUEST['inCodAutorizacao'] != '') {
            $arAutorizacao = explode('/',$_REQUEST['inCodAutorizacao'] );
            $obTFrotaAutorizacao->setDado( 'cod_autorizacao', $arAutorizacao[0] );
            $obTFrotaAutorizacao->setDado( 'exercicio', $arAutorizacao[1] );
            $obTFrotaAutorizacao->recuperaRelacionamento( $rsAutorizacao );
            if ( implode(array_reverse(explode('/',$rsAutorizacao->getCampo('dt_autorizacao')))) > implode(array_reverse(explode('/',$_REQUEST['dtManutencao']))) ) {
                $stMensagem = 'A data de manutenção deve ser igual ou posterior a de autorização de abastecimento('.$rsAutorizacao->getCampo('dt_autorizacao').')';
            }
        }

        foreach ( Sessao::read('arItensAutorizacao') AS $arTemp ) {
            if ( ($arTemp['quantidade'] <= 0 ) ) {
                $stMensagem = 'O item '.$arTemp['cod_item'].' está sem quantidade';
            }
        }

        if ($_REQUEST['inQuilometragem'] =="") {
            $stMensagem = 'Verifique se o veiculo selecionado é valido!';
        }

        if (!$stMensagem) {
            //inclui na table frota.manutencao
            $obTFrotaManutencao->setDado( 'exercicio',Sessao::getExercicio() );
            $obTFrotaManutencao->proximoCod( $inCodManutencao );
            $obTFrotaManutencao->setDado( 'cod_manutencao', $inCodManutencao );
            $obTFrotaManutencao->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
            $obTFrotaManutencao->setDado( 'dt_manutencao', $_REQUEST['dtManutencao'] );
            $obTFrotaManutencao->setDado( 'km', $_REQUEST['inQuilometragem'] );
            $obTFrotaManutencao->setDado( 'observacao', $_REQUEST['stObservacao'] );
            $obTFrotaManutencao->inclusao();

            if ($_REQUEST['inCodigoEmpenho'] != '' AND $_REQUEST['stExercicioEmpenho'] != '' AND $_REQUEST['inCodEntidade'] != '') {
                //inclui na table frota.manutencao_empenho
                $obTFrotaManutencaoEmpenho->setDado( 'exercicio', Sessao::getExercicio() );
                $obTFrotaManutencaoEmpenho->setDado( 'cod_manutencao', $inCodManutencao );
                $obTFrotaManutencaoEmpenho->setDado( 'cod_empenho', $_REQUEST['inCodigoEmpenho'] );
                $obTFrotaManutencaoEmpenho->setDado( 'cod_entidade', $_REQUEST['inCodEntidade'] );
                $obTFrotaManutencaoEmpenho->setDado( 'exercicio_empenho', $_REQUEST['stExercicioEmpenho'] );
                $obTFrotaManutencaoEmpenho->inclusao();
            }
            foreach ( Sessao::read('arItensAutorizacao') AS $arTemp ) {
                $obTFrotaManutencaoItem->setDado( 'cod_manutencao', $inCodManutencao );
                $obTFrotaManutencaoItem->setDado( 'cod_item', $arTemp['cod_item'] );
                $obTFrotaManutencaoItem->setDado( 'exercicio', Sessao::getExercicio() );
                $obTFrotaManutencaoItem->setDado( 'quantidade', $arTemp['quantidade'] );
                $obTFrotaManutencaoItem->setDado( 'valor', $arTemp['valor'] );
                $obTFrotaManutencaoItem->inclusao();

                //se for um combustivel, insere na efetuacao tambem
                if ($arTemp['combustivel']) {
                    $obTFrotaEfetivacao->setDado( 'cod_autorizacao', $arAutorizacao[0] );
                    $obTFrotaEfetivacao->setDado( 'cod_manutencao', $inCodManutencao );
                    $obTFrotaEfetivacao->setDado( 'exercicio_autorizacao', $arAutorizacao[1] );
                    $obTFrotaEfetivacao->setDado( 'exercicio_manutencao', Sessao::getExercicio() );
                    $obTFrotaEfetivacao->inclusao();
                }
            }
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,'Manutenção - '.$inCodManutencao.'/'.Sessao::getExercicio(),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::LiberaFrames(true,true);
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }
        break;
    case 'alterar' :
        $arManutencao = explode('/',$_REQUEST['inCodManutencao'] );

        if ( count( Sessao::read('arItensAutorizacao') ) <= 0 ) {
            $stMensagem = 'É necessário pelo menos um item para a manutenção';
        }
        if ($_REQUEST['inCodAutorizacao'] != '') {
            $arAutorizacao = explode('/',$_REQUEST['inCodAutorizacao'] );
            $obTFrotaAutorizacao->setDado( 'cod_autorizacao', $arAutorizacao[0] );
            $obTFrotaAutorizacao->setDado( 'exercicio', $arAutorizacao[1] );
            $obTFrotaAutorizacao->recuperaRelacionamento( $rsAutorizacao );
            if ( implode(array_reverse(explode('/',$rsAutorizacao->getCampo('dt_autorizacao')))) > implode(array_reverse(explode('/',$_REQUEST['dtManutencao']))) ) {
                $stMensagem = 'A data de manutenção deve ser igual ou posterior a de autorização de abastecimento('.$rsAutorizacao->getCampo('dt_autorizacao').')';
            }
        }
        foreach ( Sessao::read('arItensAutorizacao') AS $arTemp ) {

            if ( ($arTemp['valor'] <= 0) OR ($arTemp['quantidade'] <= 0 ) ) {
                $stMensagem = 'O item '.$arTemp['cod_item'].' está sem valor ou sem quantidade';
            }

            if ( ($arTemp['tipo'] == 'Combustível' ) && ($_REQUEST['inCodAutorizacao'] == '') ) {                
                $stMensagem = "Quando for tipo 'Combustível' deve existir um Código de Autorização, na qual está vazio";
            }
        }

        if (!$stMensagem) {
            //altera a table frota.manutencao
            $obTFrotaManutencao->setDado( 'exercicio'       , $arManutencao[1] );
            $obTFrotaManutencao->setDado( 'cod_manutencao'  , $arManutencao[0] );
            $obTFrotaManutencao->setDado( 'cod_veiculo'     , $_REQUEST['inCodVeiculo'] );
            $obTFrotaManutencao->setDado( 'dt_manutencao'   , $_REQUEST['dtManutencao'] );
            $obTFrotaManutencao->setDado( 'km'              , $_REQUEST['inQuilometragem'] );
            $obTFrotaManutencao->setDado( 'observacao'      , $_REQUEST['stObservacao'] );
            $obTFrotaManutencao->alteracao();

            if ($_REQUEST['inCodigoEmpenho'] != '' AND $_REQUEST['stExercicioEmpenho'] != '' AND $_REQUEST['inCodEntidade'] != '') {
                $obTFrotaManutencaoEmpenho->setDado( 'exercicio'      , $arManutencao[1] );
                $obTFrotaManutencaoEmpenho->setDado( 'cod_manutencao' , $arManutencao[0] );
                $obTFrotaManutencaoEmpenho->recuperaPorChave( $rsManutencaoEmpenho );
                    if ( $rsManutencaoEmpenho->getNumLinhas() > 0) { 
                       //altera a table frota.manutencao_empenho
                       $obTFrotaManutencaoEmpenho->setDado( 'cod_empenho'       , $_REQUEST['inCodigoEmpenho'] );
                       $obTFrotaManutencaoEmpenho->setDado( 'cod_entidade'      , $_REQUEST['inCodEntidade'] );
                       $obTFrotaManutencaoEmpenho->setDado( 'exercicio_empenho' , $_REQUEST['stExercicioEmpenho'] );
                       $obTFrotaManutencaoEmpenho->alteracao();
                    } else {
                       $obTFrotaManutencaoEmpenho->setDado( 'cod_empenho'       , $_REQUEST['inCodigoEmpenho'] );
                       $obTFrotaManutencaoEmpenho->setDado( 'cod_entidade'      , $_REQUEST['inCodEntidade'] );
                       $obTFrotaManutencaoEmpenho->setDado( 'exercicio_empenho' , $_REQUEST['stExercicioEmpenho'] );
                       $obTFrotaManutencaoEmpenho->inclusao();
                    }
            } else {
               $obTFrotaManutencaoEmpenho->exclusao();
            } 
            
            //deleta todos os registros da table frota.manutencao_item
            $obTFrotaManutencaoItem->setDado( 'cod_manutencao' , $arManutencao[0] );
            $obTFrotaManutencaoItem->setDado( 'exercicio'      , $arManutencao[1] );
            $obTFrotaManutencaoItem->exclusao();

            //recupera da table frota.efetivacao
            $obTFrotaEfetivacao->recuperaTodos( $rsEfetivacao, " WHERE cod_manutencao = ".$arManutencao[0]." AND exercicio_manutencao = '".$arManutencao[1]."' ");

            //deleta os registros da table frota.efetivacao
            while ( !$rsEfetivacao->eof() ) {
                $obTFrotaEfetivacao->setDado( 'cod_manutencao'          , $arManutencao[0] );
                $obTFrotaEfetivacao->setDado( 'exercicio_manutencao'    , $arManutencao[1] );
                $obTFrotaEfetivacao->setDado( 'cod_autorizacao'         , $rsEfetivacao->getCampo('cod_autorizacao') );
                $obTFrotaEfetivacao->setDado( 'exercicio_autorizacao'   , $rsEfetivacao->getCampo('exercicio_autorizacao') );
                $obTFrotaEfetivacao->exclusao();
                $rsEfetivacao->proximo();
            }

            foreach ( Sessao::read('arItensAutorizacao') AS $arTemp ) {
                $obTFrotaManutencaoItem->setDado( 'cod_manutencao'  , $arManutencao[0] );
                $obTFrotaManutencaoItem->setDado( 'cod_item'        , $arTemp['cod_item'] );
                $obTFrotaManutencaoItem->setDado( 'exercicio'       , $arManutencao[1] );
                $obTFrotaManutencaoItem->setDado( 'quantidade'      , $arTemp['quantidade'] );
                $obTFrotaManutencaoItem->setDado( 'valor'           , $arTemp['valor'] );
                $obTFrotaManutencaoItem->inclusao();

                //se for um combustivel, insere na efetuacao tambem
                if ( $arTemp['tipo'] == 'Combustível' ) {
                    if ( $_REQUEST['inCodAutorizacao'] != '' ) {
                        $obTFrotaEfetivacao->setDado( 'cod_autorizacao'         , $arAutorizacao[0] );
                        $obTFrotaEfetivacao->setDado( 'cod_manutencao'          , $arManutencao[0] );
                        $obTFrotaEfetivacao->setDado( 'exercicio_autorizacao'   , $arAutorizacao[1] );
                        $obTFrotaEfetivacao->setDado( 'exercicio_manutencao'    , $arManutencao[1] );
                        $obTFrotaEfetivacao->inclusao();    
                    }else{                                        
                        $stMensagem = "Quando for tipo 'Combustível' deve existir um Código de Autorização, na qual está vazio";
                        break;
                    }
                }
            }
            //Validando qualquer erro durante os inserts e updates
            if (!$stMensagem) {
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Manutenção - '.$arManutencao[0].'/'.$arManutencao[1],"alterar","aviso", Sessao::getId(), "../");
            }else{
                SistemaLegado::LiberaFrames(true,true);
                SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
            }
        } else {
            SistemaLegado::LiberaFrames(true,true);
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }
        break;
    case 'anular' :
        //inclui na table frota.manutencao_anulacao
        $arManutencao = explode('/',$_REQUEST['inCodManutencao']);
        $arAutorizacao = explode('/',$_REQUEST['inCodAutorizacao']);
        $obTFrotaManutencaoAnulacao->setDado( 'cod_manutencao', $arManutencao[0] );
        $obTFrotaManutencaoAnulacao->setDado( 'exercicio', $arManutencao[1] );
        $obTFrotaManutencaoAnulacao->setDado( 'observacao', $_REQUEST['stObservacaoAnulacao'] );
        $obTFrotaManutencaoAnulacao->inclusao();

        if ($_REQUEST['inCodAutorizacao'] != '') {
            $obTFrotaEfetivacao->setDado( 'cod_autorizacao', $arAutorizacao[0] );
            $obTFrotaEfetivacao->setDado( 'exercicio_autorizacao', $arAutorizacao[1]);
            $obTFrotaEfetivacao->setDado( 'cod_manutencao', $arManutencao[0] );
            $obTFrotaEfetivacao->setDado( 'exercicio_manutencao', $arManutencao[1] );
            $obTFrotaEfetivacao->exclusao();
        }
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Manutenção - '.$arManutencao[0].'/'.$arManutencao[1],"anular","aviso", Sessao::getId(), "../");

        break;
}

Sessao::encerraExcecao();
