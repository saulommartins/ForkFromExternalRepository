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
    * Data de Criação: 08/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: PRManterRetirarVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculo.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaUtilizacao.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaUtilizacaoRetorno.class.php" );
include_once( CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php" );

$stPrograma = "ManterRetirarVeiculo";
$pgFilt   = "FLManterVeiculo.php";
$pgList   = "LSManterVeiculo.php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTFrotaVeiculo = new TFrotaVeiculo();
$obTFrotaUtilizacao = new TFrotaUtilizacao();
$obTFrotaUtilizacaoRetorno = new TFrotaUtilizacaoRetorno();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTFrotaVeiculo );
Sessao::getTransacao()->setMapeamento( $obTFrotaUtilizacao );
Sessao::getTransacao()->setMapeamento( $obTFrotaUtilizacaoRetorno );

switch ($stAcao) {
    case 'retirar' :

        $arDataSaida = explode('/',$_REQUEST['dtSaida']);
        $anoSaida = $arDataSaida[2];
        $anoAtual = Sessao::getExercicio();
        $anoAnterior = $anoAtual-1;
        $erro = "";

        if ( ( $anoSaida < ($anoAnterior) ) || ($anoSaida > $anoAtual) ) {
            $erro = "Somente são permitidas retiradas de veículos para o exercício atual ou para o anterior!";
        }

        if (!$erro) {
            //recupera a ultima kilometragem do sistema
            $stFiltro = "
                WHERE cod_veiculo = ".$_REQUEST['inCodVeiculo']."
             ORDER BY dt_saida DESC, hr_saida DESC
                LIMIT 1
            ";
            $obTFrotaUtilizacaoRetorno->recuperaTodos( $rsVeiculoRetorno, $stFiltro );
            if ( $rsVeiculoRetorno->getCampo('km_retorno') != '' ) {
                $inKmAtual = $rsVeiculoRetorno->getCampo('km_retorno');
                $arDtRetorno = explode('/',$rsVeiculoRetorno->getCampo('dt_retorno'));
                $arHrRetorno = explode(':',$rsVeiculoRetorno->getCampo('hr_retorno'));
                $inTimestamp = mktime($arHrRetorno[0],$arHrRetorno[1],$arHrRetorno[2],$arDtRetorno[1],$arDtRetorno[0],$arDtRetorno[2]);
            } else {
                $obTFrotaVeiculo->recuperaTodos( $rsVeiculo, ' WHERE cod_veiculo = '.$_REQUEST['inCodVeiculo'].' ' );
                $inKmAtual = $rsVeiculo->getCampo('km_inicial');
                $inTimestamp = 0;
            }

            if ( str_replace(',','.',str_replace('.','',$_REQUEST['inKmInicial'])) < $inKmAtual ) {
                $stMensagem = 'A quilometragem de saída nâo pode ser inferior a '.number_format($inKmAtual,1,',','.').'';
            }

            $arDtSaida = explode('/',$_REQUEST['dtSaida']);
            $arHrSaida = explode(':',$_REQUEST['horaSaida']);
            $arHrSaida[2] = date('s');
            if ( $inTimestamp >= mktime($arHrSaida[0],$arHrSaida[1],$arHrSaida[2],$arDtSaida[1],$arDtSaida[0],$arDtSaida[2]) ) {
                $stMensagem = 'A data de retirada do veículo deve ser superior a última retirada';
            }

            //recupera a categoria do veiculo
            $obTFrotaVeiculo->setDado('cod_veiculo', $_REQUEST['inCodVeiculo'] );
            $obTFrotaVeiculo->recuperaPorChave( $rsVeiculoCategoria );

            //recupera o cod_habilidatacao do motorista
            $obTCGMPessoaFisica = new TCGMPessoaFisica();
            $obTCGMPessoaFisica->setDado( 'numcgm', $_REQUEST['inCodMotorista'] );
            $obTCGMPessoaFisica->recuperaPorChave( $rsMotorista );
            if ( $rsVeiculoCategoria->getCampo( 'cod_categoria' ) > $rsMotorista->getCampo('cod_categoria_cnh') ) {
                $stMensagem = 'A categoria CNH do motorista selecionado é incompatível com a categoria exigida pelo veículo';
            }
            if (!$stMensagem) {
                //insere na tabela frota.utilizacao
                $obTFrotaUtilizacao->setDado('cod_veiculo'  , $_REQUEST['inCodVeiculo'] );
                $obTFrotaUtilizacao->setDado('dt_saida'     , $_REQUEST['dtSaida'] );
                $obTFrotaUtilizacao->setDado('hr_saida'     , implode(':',$arHrSaida) );
                $obTFrotaUtilizacao->setDado('cgm_motorista', $_REQUEST['inCodMotorista'] );
                $obTFrotaUtilizacao->setDado('km_saida'     , str_replace(',','.',str_replace('.','',$_REQUEST['inKmInicial'] )));
                $obTFrotaUtilizacao->setDado('destino'      , $_REQUEST['stDestino'] );
                $obTFrotaUtilizacao->inclusao();
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao."&inCodVeiculo=".Sessao::read('codVeiculoFiltro'),'Veículo - '.$_REQUEST['inCodVeiculo'],"incluir","incluir", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
                echo "<script>LiberaFrames(true,true);</script>";
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($erro).'!',"n_incluir","erro");
            echo "<script>LiberaFrames(true,true);</script>";
        }
        break;

    case 'retornar' :
        if (!$_REQUEST['boViradaOdometro']) {
            if ( str_replace(',','.',str_replace('.','',$_REQUEST['inKmRetorno'])) < str_replace(',','.',str_replace('.','',$_REQUEST['inKmInicial']) ) ) {
                $stMensagem = 'A quilometragem de retorno nâo pode ser inferior a quilometragem de retirada';
            }
        }

        $arDtSaida = explode('/',$_REQUEST['dtSaida']);
        $arHrSaida = explode(':',$_REQUEST['horaSaida']);

        $arDtRetorno = explode('/',$_REQUEST['dtRetorno']);
        $arHrRetorno = explode(':',$_REQUEST['horaRetorno']);
        $arHrRetorno[2] = date( 's' );
        if ( mktime($arHrSaida[0],$arHrSaida[1],$arHrSaida[2],$arDtSaida[1],$arDtSaida[0],$arDtSaida[2]) >= mktime($arHrRetorno[0],$arHrRetorno[1],$arHrRetorno[2],$arDtRetorno[1],$arDtRetorno[0],$arDtRetorno[2]) ) {
            $stMensagem = 'A data de retorno deve ser superior a data de retirada';
        }

        if ( mktime($arHrRetorno[0],$arHrRetorno[1],0,$arDtRetorno[1],$arDtRetorno[0],$arDtRetorno[2]) > time() ) {
            $stMensagem = 'A data/hora de retorno não pode ser posterior a data/hora atual';
        }
        
        $nuHoraTrabalhada = '0.00';
        if(isset($_REQUEST['stHoraTrabalhada'])){
            $dataAtual=$arDtRetorno[2].'-'.$arDtRetorno[1].'-'.$arDtRetorno[0]." ".$_REQUEST['horaRetorno'];
            $dataAnterior = $arDtSaida[2].'-'.$arDtSaida[1].'-'.$arDtSaida[0]." ".$_REQUEST['horaSaida'];

            $diffDatas= explode(".",(SistemaLegado::datediff('n' , $dataAnterior, $dataAtual, false))/60);
        
            $min = ($diffDatas[1]) ? ($diffDatas[1]*60) : '00';
            $min = ((substr($min, 2, 2))>60) ? (substr($min, 0, 2)+1) : substr($min, 0, 2);
            $min = round($min / 0.6);
          
            $stHoraTrabalhada = explode(':', $_REQUEST['stHoraTrabalhada']);
       
            if($stHoraTrabalhada[0]>$diffDatas[0]||($diffDatas[0]==$stHoraTrabalhada[0]&&$stHoraTrabalhada[1]>$min)){
                $stMensagem = 'Quantidade de horas trabalhadas deve ser menor ou igual que a diferença entre data/hora de retorno e saída'; 
            }
            
            $nuHoraTrabalhada = intval($stHoraTrabalhada[0]).".".str_pad($stHoraTrabalhada[1], 2 , "0", STR_PAD_LEFT);
        }
        
        //recupera a categoria do veiculo
        $obTFrotaVeiculo->setDado('cod_veiculo', $_REQUEST['inCodVeiculo'] );
        $obTFrotaVeiculo->recuperaPorChave( $rsVeiculoCategoria );

        //recupera o cod_habilidatacao do motorista
        $obTCGMPessoaFisica = new TCGMPessoaFisica();
        $obTCGMPessoaFisica->setDado( 'numcgm', $_REQUEST['inCodMotorista'] );
        $obTCGMPessoaFisica->recuperaPorChave( $rsMotorista );

        if (!$stMensagem) {
            //insere na tabel frota.utilizacao_retorno
            $obTFrotaUtilizacaoRetorno->setDado('cod_veiculo'  , $_REQUEST['inCodVeiculo'] );
            $obTFrotaUtilizacaoRetorno->setDado('dt_saida'     , $_REQUEST['dtSaida'] );
            $obTFrotaUtilizacaoRetorno->setDado('hr_saida'     , $_REQUEST['horaSaida'] );
            $obTFrotaUtilizacaoRetorno->setDado('cgm_motorista', $_REQUEST['inCodMotorista'] );
            $obTFrotaUtilizacaoRetorno->setDado('dt_retorno'   , $_REQUEST['dtRetorno'] );
            $obTFrotaUtilizacaoRetorno->setDado('hr_retorno'   , implode(':',$arHrRetorno) );
            $obTFrotaUtilizacaoRetorno->setDado('km_retorno'   , str_replace(',','.',str_replace('.','',$_REQUEST['inKmRetorno'] )));
            $obTFrotaUtilizacaoRetorno->setDado('observacao'   , $_REQUEST['stObservacao'] );
            $obTFrotaUtilizacaoRetorno->setDado('virada_odometro', ( $_REQUEST['boViradaOdometro'] ? true : false ) );
            $obTFrotaUtilizacaoRetorno->setDado('qtde_horas_trabalhadas', $nuHoraTrabalhada);
            $obTFrotaUtilizacaoRetorno->inclusao();
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao."&inCodVeiculo=".Sessao::read('codVeiculoFiltro'),'Veículo - '.$_REQUEST['inCodVeiculo'],"incluir","incluir", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
            echo "<script>LiberaFrames(true,true);</script>";
        }
        break;

}

Sessao::encerraExcecao();
