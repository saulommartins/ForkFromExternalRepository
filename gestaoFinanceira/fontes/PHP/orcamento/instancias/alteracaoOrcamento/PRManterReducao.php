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
    * Página de Processamento de Suplementacao
    * Data de Criação   : 18/02/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 30813 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.07
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterReducao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

$obROrcamentoSuplementacao = new ROrcamentoSuplementacao;

$stAcao = $request->get('stAcao');

//valida a utilização da rotina de encerramento do mês contábil
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

$arDtAutorizacao = explode('/', $request->get('stData'));
if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
    SistemaLegado::exibeAviso(urlencode("Mês do Crédito encerrado!"),"n_incluir","erro");
    exit;
}

/*
#23438
Através desta configuração, quando o usuario realizar algum credito suplementar por redução, deverá levar em consideração os recursos das dotações reduzidas e suplementadas, ou seja,
Quando reduzir uma dotação da fonte 148 somente poderá suplementar dotações da fonte 148.
A única excessão será com as fontes 100, 101 e 102, que poderão ser reduzidas e suplementadas entre si, e as fontes 118 e 119 que também poderão ser reduzidas e suplementadas entre si.
*/
$stConfiguracao = SistemaLegado::pegaConfiguracao('suplementacao_rigida_recurso', 8, Sessao::getExercicio(), $boTransacao);
if ( $stConfiguracao == 'sim' ) {
    $arRecursos          = Sessao::read('arRecursos');
    $arRecursosRedutoras = Sessao::read('arRecursosRedutoras');
    //Retirando recursos que nao possuem valor
    foreach ($arRecursos as $key => $value) {
        if ( $value['valor_recurso'] <= 0 ) {
            unset($arRecursos[$key]);
        }
    }
    foreach ($arRecursosRedutoras as $key => $value) {
        if ( $value['valor_recurso'] <= 0 ) {
            unset($arRecursosRedutoras[$key]);
        }
    }

    //Verificando se o que foi suplementado esta no array de redutoras 
    //exceto recursos 100 101 102 118 119 os valoresde devem ser agrupados.
    foreach ($arRecursos as $key => $value) {
        switch ($value['cod_recurso']) {
            case 100:
            case 101:
            case 102:
            case 118:
            case 119:
                $nuValorTotalRecursoUnidas = $nuValorTotalRecursoUnidas+$value['valor_recurso'];
            break;
            
            default:
                foreach ($arRecursosRedutoras as $key2 => $value2) {
                    if ( $value['cod_recurso'] == $value2['cod_recurso']) {
                        if ( $value['valor_recurso'] != $value2['valor_recurso'] ) {
                            SistemaLegado::exibeAviso("O Valores Totais do Recurso: ".$value['nom_recurso']." devem ser iguais para Suplementa e Redutora.", 'aviso', 'aviso');
                            exit();
                        }
                    }
                }                
            break;
        }
    }
    //Verificando se o que foi redutoras esta no array de suplementado
    //exceto recursos 100 101 102 118 119 os valoresde devem ser agrupados.
    foreach ($arRecursosRedutoras as $key => $value) {
        switch ($value['cod_recurso']) {
            case 100:
            case 101:
            case 102:
            case 118:
            case 119:
                $nuValorTotalRecursoRedutoraUnidas = $nuValorTotalRecursoRedutoraUnidas+$value['valor_recurso'];
            break;
            
            default:
                foreach ($arRecursos as $key2 => $value2) {
                    if ( $value['cod_recurso'] == $value2['cod_recurso']) {
                        if ( $value['valor_recurso'] != $value2['valor_recurso'] ) {
                            SistemaLegado::exibeAviso("O Valores Totais do Recurso: '".$value['nom_recurso']."' devem ser iguais para Suplementa e Redutora.", 'aviso', 'aviso');
                            exit();
                        }
                    }
                }                
            break;
        }
    }
    //Validando o valor entre os recursos que devem ser agrupados
    //recursos 100 101 102 118 119 os valoresde devem ser agrupados.
    if ( $nuValorTotalRecursoRedutoraUnidas != $nuValorTotalRecursoUnidas ) {        
        SistemaLegado::exibeAviso("A fonte de recurso das dotações suplementadas devem ser iguais as dotações redutoras!", 'aviso', 'aviso');
        exit();
    }
}

switch ($stAcao) {

    case "Suplementa":
    case "Especial":
        $obErro = new Erro;

        $nuVlTotal = str_replace( '.' , '' , $request->get('nuVlTotal') );
        $nuVlTotal = str_replace( ',' ,'.' , $nuVlTotal          );

        $obROrcamentoSuplementacao->setExercicio         ( Sessao::getExercicio()  );
        $obROrcamentoSuplementacao->setCodTipo           ( $request->get('inCodTipo')  );
        $obROrcamentoSuplementacao->obRNorma->setCodNorma( $request->get('inCodNorma') );
        $obROrcamentoSuplementacao->setVlTotal           ( $nuVlTotal              );
        $obROrcamentoSuplementacao->setDecreto           ( $stDecreto              );
        $obROrcamentoSuplementacao->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );
        $obROrcamentoSuplementacao->setCredSuplementar   ( 'Reducao'               );
        $obROrcamentoSuplementacao->setMotivo            ( $request->get('stMotivo') );
        $obROrcamentoSuplementacao->setDtLancamento      ( $request->get('stData')   );

        $arSuplementada = Sessao::read('arSuplementada');
        $inCount = count( $arSuplementada );
        if ($inCount) {
            foreach ($arSuplementada as $arDespesaSuplementar) {
                $obROrcamentoSuplementacao->addDespesaSuplementada();
                $obROrcamentoSuplementacao->roUltimoDespesaSuplementada->setCodDespesa   ( $arDespesaSuplementar['cod_reduzido']);
                $obROrcamentoSuplementacao->roUltimoDespesaSuplementada->setValorOriginal( $arDespesaSuplementar['vl_valor']    );
            }
        }

        $arRedutoras = Sessao::read('arRedutoras');
        $inCount = count( $arRedutoras );
        if ($inCount) {
            foreach ($arRedutoras as $arDespesaReducao) {
                $obROrcamentoSuplementacao->addDespesaReducao();
                $obROrcamentoSuplementacao->roUltimoDespesaReducao->setCodDespesa   ( $arDespesaReducao['cod_reduzido']);
                $obROrcamentoSuplementacao->roUltimoDespesaReducao->setValorOriginal( $arDespesaReducao['vl_valor']    );
            }
        } else {
            $obErro->setDescricao( "É necessário cadastrar pelo menos uma Redução" );
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obROrcamentoSuplementacao->incluir();

            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=".$stAcao, $obROrcamentoSuplementacao->getDecreto() , "incluir", "aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }
    break;
}

?>