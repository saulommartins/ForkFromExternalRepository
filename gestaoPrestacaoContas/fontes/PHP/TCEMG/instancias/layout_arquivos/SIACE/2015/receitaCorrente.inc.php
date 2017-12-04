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
/*
 * Arquivo de geracao do arquivo receitaCorrente TCM/MG
 * Data de Criação   : 29/01/2009

 * @author Analista      Tonismar Régis Bernardo
 * @author Desenvolvedor André Machado

 * @package URBEM
 * @subpackage

 * @ignore

 $Id: receitaCorrente.inc.php 63423 2015-08-26 20:27:31Z jean $
*/

include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/FTCEMGReceitaCorrente.class.php';

$arFiltros = Sessao::read('filtroRelatorio');

$obFTCEMGReceitaCorrente = New FTCEMGReceitaCorrente();
foreach ($arDatasInicialFinal as $stDatas) {
    $obFTCEMGReceitaCorrente->setDado('stExercicio'         , Sessao::getExercicio());
    $obFTCEMGReceitaCorrente->setDado('stCodEntidades'      , implode(',', $arFiltros['inCodEntidadeSelecionado']));
    $obFTCEMGReceitaCorrente->setDado('dt_inicial'          , $stDatas['stDtInicial'] );
    $obFTCEMGReceitaCorrente->setDado('dt_final'            , $stDatas['stDtFinal'] );  
   
    $obFTCEMGReceitaCorrente->recuperaTodos($rsReceitaCorrente);

    $arDados = array();
    $arRealizada = array();
        
    list($inDia, $inMes, $inAno) = explode('/',$stDatas['stDtInicial']);
        
    $arDados['mes']     = $inMes;
    $arRealizada['mes'] = $inMes;
        
    while ( !$rsReceitaCorrente->eof()) {
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,3) == '1.2') {
            $arDados['recContrib']     = number_format($arDados['recContrib'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['recContrib'] = number_format($arRealizada['recContrib'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,3) == '1.5') {
            $arDados['recIndust']     = number_format($arDados['recIndust'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['recIndust'] = number_format($arRealizada['recIndust'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,3) == '1.4') {
            $arDados['recAgropec']     = number_format($arDados['recAgropec'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['recAgropec'] = number_format($arRealizada['recAgropec'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,3) == '1.6') {
            $arDados['recServ']     = number_format($arDados['recServ'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['recServ'] = number_format($arRealizada['recServ'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,3) == '1.9') {
            $arDados['outrasRecCor']     = number_format($arDados['outrasRecCor'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['outrasRecCor'] = number_format($arRealizada['outrasRecCor'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,5) == '9.1.1') {
            $arDados['deducoesExcFundeb']     = number_format($arDados['deducoesExcFundeb'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['deducoesExcFundeb'] = number_format($arRealizada['deducoesExcFundeb'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,5) == '1.1.2') {
            $arDados['tribTaxas']     = number_format($arDados['tribTaxas'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['tribTaxas'] = number_format($arRealizada['tribTaxas'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,5) == '1.1.3') {
            $arDados['tribContMelhoria']     = number_format($arDados['tribContMelhoria'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['tribContMelhoria'] = number_format($arRealizada['tribContMelhoria'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,10) == '1.1.1.2.02') {
            $arDados['recIPTU']     = number_format($arDados['recIPTU'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['recIPTU'] = number_format($arRealizada['recIPTU'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,10) == '1.1.1.3.05') {
            $arDados['recISSQN']     = number_format($arDados['recISSQN'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['recISSQN'] = number_format($arRealizada['recISSQN'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,10) == '1.1.1.2.08') {
            $arDados['recITBI']     = number_format($arDados['recITBI'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['recITBI'] = number_format($arRealizada['recITBI'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,10) == '1.1.1.2.04') {
            $arDados['transfIRRF']     = number_format($arDados['transfIRRF'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['transfIRRF'] = number_format($arRealizada['transfIRRF'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,7) == '1.3.2.5') {
            $arDados['recAplic']     = number_format($arDados['recAplic'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['recAplic'] = number_format($arRealizada['recAplic'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( (substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,3) == '1.3') || (substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,7) == '1.3.2.5')) {
            $valorOutrasRecursosPrevisto   = ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.3.0.0.00.00.00.00.00') ? $rsReceitaCorrente->getCampo('valor_previsto') :  $valorOutrasRecursosPrevisto - $rsReceitaCorrente->getCampo('valor_previsto');
            $valorOutrasRecursosArrecadado = ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.3.0.0.00.00.00.00.00') ? $rsReceitaCorrente->getCampo('arrecadado_periodo') :  $valorOutrasRecursosArrecadado - $rsReceitaCorrente->getCampo('arrecadado_periodo');
            
            $arDados['outrasRec']     = number_format($valorOutrasRecursosPrevisto, 2, '.', '');
            $arRealizada['outrasRec'] = number_format($valorOutrasRecursosArrecadado, 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,13) == '1.7.2.1.01.02') {
            $arDados['cotaParteFPM']     = number_format($arDados['cotaParteFPM'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['cotaParteFPM'] = number_format($arRealizada['cotaParteFPM'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,13) == '1.7.2.2.01.01') {
            $arDados['cotaParteICMS']     = number_format($arDados['cotaParteICMS'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['cotaParteICMS'] = number_format($arRealizada['cotaParteICMS'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,13) == '1.7.2.2.01.04') {
            $arDados['cotaParteIPI']     = number_format($arDados['cotaParteIPI'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['cotaParteIPI'] = number_format($arRealizada['cotaParteIPI'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,13) == '1.7.2.2.01.02') {
            $arDados['cotaParteIPVA']     = number_format($arDados['cotaParteIPVA'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['cotaParteIPVA'] = number_format($arRealizada['cotaParteIPVA'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,16) == '1.7.2.1.01.02.06') {
            $arDados['transfFUNDEB']     = number_format($arDados['transfFUNDEB'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['transfFUNDEB'] = number_format($arRealizada['transfFUNDEB'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,5) == '1.7.6') {
            $arDados['convenios']     = number_format($arDados['convenios'] + $rsReceitaCorrente->getCampo('valor_previsto'), 2, '.', '');
            $arRealizada['convenios'] = number_format($arRealizada['convenios'] + $rsReceitaCorrente->getCampo('arrecadado_periodo'), 2, '.', '');
        }
        if ((substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 3)  == '1.7'             ) ||
            (substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 13) == '1.7.2.1.01.02'   ) ||
            (substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 13) == '1.7.2.2.01.01'   ) ||
            (substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 13) == '1.7.2.2.01.04'   ) ||
            (substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 13) == '1.7.2.2.01.02'   ) ||
            (substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 16) == '1.7.2.1.01.02.06') ||
            (substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 5)  == '1.7.6')          ) {
        
            $valorOutrasTransferenciasPrevisto   = ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.7.0.0.00.00.00.00.00') ? $rsReceitaCorrente->getCampo('valor_previsto') :  $valorOutrasTransferenciasPrevisto - $rsReceitaCorrente->getCampo('valor_previsto');
            $valorOutrasTransferenciasArrecadado = ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.7.0.0.00.00.00.00.00') ? $rsReceitaCorrente->getCampo('arrecadado_periodo') :  $valorOutrasTransferenciasArrecadado - $rsReceitaCorrente->getCampo('arrecadado_periodo');
            
            $arDados['outrasTransf']     = number_format($valorOutrasTransferenciasPrevisto, 2, '.', '');
            $arRealizada['outrasTransf'] = number_format($valorOutrasTransferenciasArrecadado, 2, '.', '');
        }

        $rsReceitaCorrente->proximo();
    }

    $rsTemp = new RecordSet;
    if ( $inMes == '01' || (integer)$inMes == 1 ) {
        $arRealizada['cod_tipo'] = '01';
        $arFinal[] = $arRealizada;
    }
        
    // Atribui o valor para previsão anual inicial, previsão anual atualizada e prevista, os 3 primeiros tipos, que possuem o valor de campo da consulta, de acordo com o campo valor_previsto .
    for ($inContador = 2; $inContador < 4 ; $inContador++) {
        $arDados['cod_tipo'] = '0'.$inContador;
        $arFinal[] = $arDados;
    }

        
    // Adiciona por último no array os valores do tipo 4, realizada, que possui valores da consulta do campo arrecadado_periodo.
    $arRealizada['cod_tipo'] = '04';
    $arFinal[] = $arRealizada;
    
    $rsTemp->preenche($arFinal);

    $arFinal = '';
    
    $obExportador->roUltimoArquivo->addBloco($rsTemp);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recContrib');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recIndust');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recAgropec');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recServ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('outrasRecCor');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('tribTaxas');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('tribContMelhoria');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recAplic');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('outrasRec');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recIPTU');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recISSQN');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recITBI');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cotaParteFPM');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('transfIRRF');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cotaParteICMS');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cotaParteIPVA');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cotaParteIPI');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('transfFUNDEB');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('convenios');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('outrasTransf');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('deducoesExcFundeb');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';'); 
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_tipo');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
}
?>