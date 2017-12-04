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
 * Data de Criaï¿½ï¿½o: 26/11/2007

 * @author Analista: Gelson W. Gonï¿½alves
 * @author Desenvolvedor: Henrique Boaventura

 * $Id: PRManterAutorizacao.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-03.02.13
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaAutorizacao.class.php";
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculo.class.php';

$stPrograma = "ManterAutorizacao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";
$pgGera   = "OCGeraAutorizacao.php";

$stAcao = $request->get('stAcao');

$obTFrotaAutorizacao = new TFrotaAutorizacao();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTFrotaAutorizacao );

switch ($stAcao) {

    case 'incluir':

        $inQuantidade = $_REQUEST['inQuantidade'] == '' ? 0 : $_REQUEST['inQuantidade'];
        $inValor      = $_REQUEST['inValor'] 	  == '' ? 0 : $_REQUEST['inValor'];

        $inQuantidade = (int) str_replace(array('.',','),'', $inQuantidade);
        $inValor 	  = (int) str_replace(array('.',','),'', $inValor);

        //se não for completar o tanque, verifica
        if ($_REQUEST['boCompletar'] != 1) {
            if (($inQuantidade == 0) && ($inValor == 0) && $_REQUEST['boCompletar'] == false) {
                $stMensagem = 'Digite a Quantidade ou Valor Total';
            }

            $obTFrotaVeiculo = new TFrotaVeiculo();
            $obTFrotaVeiculo->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
            $obTFrotaVeiculo->recuperaVeiculoAnalitico( $rsVeiculo );

            $inQuantidadeArredondado = (int) $_REQUEST['inQuantidade'];

            if ($rsVeiculo->getCampo('capacidade_tanque') < $inQuantidadeArredondado) {
                $stMensagem = 'Capacidade do tanque não informada ou acima do limite '.$rsVeiculo->getCampo('capacidade_tanque').' ';
            }
        }

        // Data da Autorização deve ser do exercício atual. Caso contrário exibir mensagem: A Data da Autorização deve ser do Exercício atual.
        if ( substr($request->get('stDtAutorizacao'), -4) != Sessao::getExercicio() ) {
            $stMensagem = "A Data da Autorização deve ser do Exercício atual";
        }
        
        // Data da Autorização deve ser igual ou maior que a anterior, caso esta não seja a primeira do exercício (exemplo: a anterior foi a autorização 8 em 05/01
        // , a próxima será 9 com data igual ou posterior a 05/01. Caso seja a primeira autorização do exercício, permite qualquer data igual ou menor a data atual). 
        // Caso contrário exibir mensagem: A Data da Autorização deve igual ou superior a dd/mm/aaaa 
        // (No caso dd/mm/aaaa é a data da última autorização de abastecimento lançada no exercício);
        $obTFrotaAutorizacao->setDado('exercicio', Sessao::getExercicio());
        $stFiltro = " AND timestamp = (SELECT MAX(timestamp) from frota.autorizacao) AND exercicio = '".Sessao::getExercicio()."'";
        $obTFrotaAutorizacao->recuperaRelacionamento($rsDtAutorizacaoMax,$stFiltro,"");
        
        // Data da Autorização não pode ser superior a data atual. Caso contrário exibir mensagem: A Data da Autorização não pode ser superior a data atual.        
        if ( SistemaLegado::comparaDatas($request->get('stDtAutorizacao'), date('d/m/Y')) ) {
            $stMensagem = "A Data da Autorização não pode ser superior a data atual";            
        }

        if ($rsDtAutorizacaoMax->getNumLinhas() > 0) {
            if ( SistemaLegado::comparaDatas($rsDtAutorizacaoMax->getCampo('dt_autorizacao'),$request->get('stDtAutorizacao')))
                $stMensagem = "A Data da Autorização deve ser igual ou superior a ".$rsDtAutorizacaoMax->getCampo('dt_autorizacao'); 
        }
      
        if (!$stMensagem) {
            //recupera o proximo cod
            $obTFrotaAutorizacao->proximoCod( $inCodAutorizacao );
            $stDtAutorizacao = SistemaLegado::dataToSql($request->get('stDtAutorizacao')) . " 00:00:00.000";
            
            //insere na table frota.autorizacao
            $obTFrotaAutorizacao->setDado('cod_autorizacao'      , $inCodAutorizacao              );
            $obTFrotaAutorizacao->setDado('exercicio'            , Sessao::getExercicio()         );
            $obTFrotaAutorizacao->setDado('cod_item'             , $_REQUEST['slCombustivel']     );
            $obTFrotaAutorizacao->setDado('cgm_resp_autorizacao' , $_REQUEST['inCodAutorizador']  );
            $obTFrotaAutorizacao->setDado('cgm_motorista'        , $_REQUEST['inCodMotorista']    );
            $obTFrotaAutorizacao->setDado('cgm_fornecedor'       , $_REQUEST['inCodAbastecedora'] );
            $obTFrotaAutorizacao->setDado('cod_veiculo'          , $_REQUEST['inCodVeiculo']      );
            $obTFrotaAutorizacao->setDado('timestamp'            , $stDtAutorizacao               );

            if (isset($_REQUEST['inValor'])) {
                $obTFrotaAutorizacao->setDado('valor', number_format(str_replace(',','.',str_replace('.','',$_REQUEST['inValor'])),2,'.','') );
            } else {
                $obTFrotaAutorizacao->setDado('valor', 0);
            }

            if (isset($_REQUEST['inQuantidade'])) {
                $obTFrotaAutorizacao->setDado('quantidade', number_format(str_replace(',','.',str_replace('.','',$_REQUEST['inQuantidade'])),4,'.','') );
            } else {
                $obTFrotaAutorizacao->setDado('quantidade', 0);
            }

            $obTFrotaAutorizacao->setDado('observacao', $_REQUEST['stComentario'] );
            $obTFrotaAutorizacao->inclusao();

            SistemaLegado::alertaAviso($pgGera."?".Sessao::getId()."&stAcao=".$stAcao."&inCodAutorizacao=".$inCodAutorizacao."&stExercicio=".Sessao::getExercicio()."&boVias=".$_REQUEST['boVias']."&inTipo=".( ($_REQUEST['inQuantidade'] == '') ? 1 : 0 ),'Autorização '.$inCodAutorizacao.'/'.Sessao::getExercicio(),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }
    break;

    case 'alterar' :
        //se não for completar o tanque, verifica
        if ($_REQUEST['boCompletar'] != 1) {
            if ($_REQUEST['inValor'] == '') {
                $stMensagem = 'Preencha o campo valor';
            } elseif ($_REQUEST['inQuantidade'] == '') {
                $stMensagem = 'Preenha o campo quantidade';
            }

            $inQuantidade = $_REQUEST['inQuantidade'] == '' ? 0 : $_REQUEST['inQuantidade'];
            $inValor 	  = $_REQUEST['inValor'] 	  == '' ? 0 : $_REQUEST['inValor'];

            $inQuantidade = (int) str_replace(array('.',','),'', $inQuantidade);
            $inValor 	  = (int) str_replace(array('.',','),'', $inValor);

            if (($inQuantidade == 0) && ($inValor == 0)) {
                $stMensagem = 'Digite a Quantidade ou Valor Total';
            }

            $obTFrotaVeiculo = new TFrotaVeiculo();
            $obTFrotaVeiculo->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
            $obTFrotaVeiculo->recuperaVeiculoAnalitico( $rsVeiculo );

            $inQuantidadeArredondado = (int) $_REQUEST['inQuantidade'];

            if ($rsVeiculo->getCampo('capacidade_tanque') < $inQuantidadeArredondado) {
                $stMensagem = 'A capacidade máxima do tanque é de '.$rsVeiculo->getCampo('capacidade_tanque').' litros';
            }
        }

        // Data da Autorização deve ser do exercício atual. Caso contrário exibir mensagem: A Data da Autorização deve ser do Exercício atual.
        if ( substr($request->get('stDtAutorizacao'), -4) < Sessao::getExercicio() ) {
            $stMensagem = "A Data da Autorização deve ser do Exercício atual";
        }

        // Data da Autorização não pode ser superior a data atual. Caso contrário exibir mensagem: A Data da Autorização não pode ser superior a data atual.        
        if ( SistemaLegado::comparaDatas($request->get('stDtAutorizacao'), date('d/m/Y')) ) {
            $stMensagem = "A Data da Autorização não pode ser superior a data atual";            
        }

        // - A data da autorização informada deve pertencer ao intervalo de datas onde: 
        // data_informada >= data_autorizacao_anterior "E" data_informada <= data_autorizacao_posterior(caso exista posterior)        
        $arAutorizacao   = explode('/',$_REQUEST['inCodAutorizacao'] );
        $obTFrotaAutorizacao->setDado('exercicio', Sessao::getExercicio());

        //Busca dados da autorizacao que esta sendo alterada
        $stFiltro = " AND cod_autorizacao = ".$arAutorizacao[0]." AND exercicio = '".$arAutorizacao[1]."'";
        $obTFrotaAutorizacao->recuperaRelacionamento($rsAutorizacao,$stFiltro,"");
        
        //Busca dados do PROXIMO cod_autorizacao referente ao que esta sendo alterado, se existir
        $stFiltro = " AND cod_autorizacao = ".($arAutorizacao[0]+1)." AND exercicio = '".$arAutorizacao[1]."'";
        $obTFrotaAutorizacao->recuperaRelacionamento($rsProximoAutorizacao,$stFiltro,"");        
        
        //Busca dados do ANTERIOR cod_autorizacao referente ao que esta sendo alterado, se existir
        $stFiltro = " AND cod_autorizacao = ".($arAutorizacao[0]-1)." AND exercicio = '".$arAutorizacao[1]."'";
        $obTFrotaAutorizacao->recuperaRelacionamento($rsAnteriorAutorizacao,$stFiltro,"");        

        // Caso a ordem cronológica seja afetada, exibir a mensagem: 
        // - Se existir cod_autorização anterior e posterior:
        if ( ($rsAnteriorAutorizacao->getNumLinhas() >= 1) && ($rsProximoAutorizacao->getNumLinhas() >= 1)  ) {
            // Mensagem: Data inválida, a data da Autorização de Abastecimento pertencer ao intervalo: dd/mm/aaaa_anterior e dd/mm/aaaa_posterior.        
            if ( 
                ($request->get('stDtAutorizacao') < $rsAnteriorAutorizacao->getCampo('dt_autorizacao'))
                    ||
                ($request->get('stDtAutorizacao') > $rsProximoAutorizacao->getCampo('dt_autorizacao')) 
            ){
                $stMensagem = "Data inválida, a data da Autorização de Abastecimento deve pertencer ao intervalo: 
                                ".$rsAnteriorAutorizacao->getCampo('dt_autorizacao')." e ".$rsProximoAutorizacao->getCampo('dt_autorizacao')."";   
            }    
            // Se existir cod_autorização anterior e não existir posterior:
        }elseif( $rsAnteriorAutorizacao->getNumLinhas() >= 1 ){            
            // Mensagem: A data da Autorização de Abastecimento deve igual ou superior a dd/mm/aaaa.
            if ( $request->get('stDtAutorizacao') < $rsAnteriorAutorizacao->getCampo('dt_autorizacao') ){  
                $stMensagem = "A data da Autorização de Abastecimento deve ser igual ou superior a ".$rsAnteriorAutorizacao->getCampo('dt_autorizacao')."";
            }    
        }   

        if (!$stMensagem) {
            $stDtAutorizacao = SistemaLegado::dataToSql($request->get('stDtAutorizacao')) . " 00:00:00.000";

            //insere na table frota.autorizacao
            $obTFrotaAutorizacao->setDado('cod_autorizacao'      , $arAutorizacao[0]              );
            $obTFrotaAutorizacao->setDado('exercicio'            , $arAutorizacao[1]              );
            $obTFrotaAutorizacao->setDado('cod_item'             , $_REQUEST['slCombustivel']     );
            $obTFrotaAutorizacao->setDado('cgm_resp_autorizacao' , $_REQUEST['inCodAutorizador']  );
            $obTFrotaAutorizacao->setDado('cgm_fornecedor'       , $_REQUEST['inCodAbastecedora'] );
            $obTFrotaAutorizacao->setDado('cgm_motorista'        , $_REQUEST['inCodMotorista']    );
            $obTFrotaAutorizacao->setDado('cod_veiculo'          , $_REQUEST['inCodVeiculo']      );
            $obTFrotaAutorizacao->setDado('timestamp'            , $stDtAutorizacao               );

            if (isset($_REQUEST['inValor'])) {
                $obTFrotaAutorizacao->setDado('valor', number_format(str_replace(',','.',str_replace('.','',$_REQUEST['inValor'])),2,'.','') );
            } else {
                $obTFrotaAutorizacao->setDado('valor', 0);
            }

            if (isset($_REQUEST['inQuantidade'])) {
                $obTFrotaAutorizacao->setDado('quantidade', number_format(str_replace(',','.',str_replace('.','',$_REQUEST['inQuantidade'])),4,'.','') );
            } else {
                $obTFrotaAutorizacao->setDado('quantidade', 0);
            }

            $obTFrotaAutorizacao->setDado('observacao', $_REQUEST['stComentario'] );
            $obTFrotaAutorizacao->alteracao();

            SistemaLegado::alertaAviso($pgGera."?".Sessao::getId()."&stAcao=".$stAcao."&inCodAutorizacao=".$_REQUEST["inCodAutorizacao"]."&boVias=".$_REQUEST['boVias']."&stExercicio=".Sessao::getExercicio()."&inTipo=".( ($_REQUEST['inQuantidade'] == '') ? 1 : 0 ),'Autorização - '.$_REQUEST["inCodAutorizacao"],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
    }
    break;

    case 'reemitir' :
        SistemaLegado::alertaAviso($pgGera."?".Sessao::getId()."&stAcao=".$stAcao."&inCodAutorizacao=".$_REQUEST['inCodAutorizacao']."&boVias=".$_REQUEST['boVias']."&stExercicio=".$_REQUEST['stExercicio'],'Autorização - '.$_REQUEST['inCodAutorizacao'],"incluir","aviso", Sessao::getId(), "../");
    break;

    case 'excluir' :
        //exclui da table frota.autorizacao
        $obTFrotaAutorizacao->setDado( 'cod_autorizacao', $_REQUEST['inCodAutorizacao'] );
        $obTFrotaAutorizacao->setDado( 'exercicio', $_REQUEST['stExercicio'] );
        $obTFrotaAutorizacao->exclusao();

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Autorização: '.$_REQUEST['inCodAutorizacao'],"excluir","aviso", Sessao::getId(), "../");
    break;
}

Sessao::encerraExcecao();

?>
