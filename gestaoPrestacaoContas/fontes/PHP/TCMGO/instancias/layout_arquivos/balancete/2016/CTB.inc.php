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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 27/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Vitor Hugo

    * @ignore

    $Id: CTB.inc.php 65220 2016-05-03 21:30:22Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once CAM_GPC_TGO_MAPEAMENTO.Sessao::getExercicio().'/TTGOCTB.class.php';

$arFiltroRelatorio = Sessao::read('filtroRelatorio');
$obTMapeamento = new TTGOCTB();
$obTMapeamento->setDado('dtInicio'   , $arFiltroRelatorio['stDataInicial'] );
$obTMapeamento->setDado('dtFim'      , $arFiltroRelatorio['stDataFinal']   );
$obTMapeamento->setDado('exercicio', Sessao::getExercicio());
$obTMapeamento->setDado('cod_entidade', $stEntidades );
$obTMapeamento->setDado('inMesGeracao', $arFiltroRelatorio['inMes']);

$obTMapeamento->recuperaContasBancarias2014($rsContasBancarias); 
$obTMapeamento->recuperaContasBancariasFonteRecurso2014($rsFonteRecurso); 

$inCount = 0;
//tipo10
foreach ($rsContasBancarias->arElementos as $arContasBancarias) {
    if ($arContasBancarias['saldo_inicial'] < 0) {
        $arContasBancarias['saldo_inicial'] = '-'.str_pad(abs($arContasBancarias['saldo_inicial']),12,'0',STR_PAD_LEFT);
    }
    if ($arContasBancarias['saldo_final'] < 0) {
        $arContasBancarias['saldo_final'] = '-'.str_pad(abs($arContasBancarias['saldo_final']),12,'0',STR_PAD_LEFT);
    }
    
    if ( $arFiltroRelatorio['inMes'] == 1) {
        $arContasBancarias['vl_entradas'] = number_format(($arContasBancarias['vl_entradas'] + $arContasBancarias['saldo_inicial']), 2, '.', '');
        $arContasBancarias['vl_saidas']   = number_format(($arContasBancarias['vl_saidas']   + $arContasBancarias['saldo_inicial']), 2, '.', ''); 
    }

    $arContasBancarias['espacador'] = '';
    $arContasBancarias['numero_registro'] = ++$inCount;
    $stChave = $arContasBancarias['num_orgao'].$arContasBancarias['num_banco'].$arContasBancarias['num_agencia'].$arContasBancarias['num_conta_corrente'].$arContasBancarias['digito'].$arContasBancarias['tipo_conta'];

    $rsBloco = 'rsBloco_'.$inCount;
    unset($$rsBloco);
    $$rsBloco = new RecordSet();
    $$rsBloco->preenche(array($arContasBancarias));

    $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
    $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_banco");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_agencia");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_conta_corrente");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 1 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_inicial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_entradas");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saidas");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_final");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espacador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

    /*TIPO REGISTRO 11 - CONTAS POR FONTE DE RECURSO */
    foreach ($rsFonteRecurso->arElementos as $arFonteRecurso) {
        $stChaveElemento = $arFonteRecurso['num_orgao'].$arFonteRecurso['num_banco'].$arFonteRecurso['num_agencia'].$arFonteRecurso['num_conta_corrente'].$arFonteRecurso['digito'].$arFonteRecurso['tipo_conta'];

        if ($stChave === $stChaveElemento) {
            if ($arFonteRecurso < 0) {
                $arFonteRecurso['saldo_inicial'] = '-'.str_pad(abs($arFonteRecurso['saldo_inicial']),12,'0',STR_PAD_LEFT);
            }
            if ($arFonteRecurso['saldo_final'] < 0) {
                $arFonteRecurso['saldo_final'] = '-'.str_pad(abs($arFonteRecurso['saldo_final']),12,'0',STR_PAD_LEFT);
            }

            $arFonteRecurso['numero_registro'] = ++$inCount;
   
            // Fixando conta 203000 para registro 11
            if ( $arFiltroRelatorio['inMes'] == 1) {
                $arFonteRecursoTMP['tipo_registro'] = $arFonteRecurso['tipo_registro'];
                $arFonteRecursoTMP['num_orgao'] = $arFonteRecurso['num_orgao'];
                $arFonteRecursoTMP['num_unidade'] = $arFonteRecurso['num_unidade'];
                $arFonteRecursoTMP['num_conta_corrente'] = $arFonteRecurso['num_conta_corrente'];
                $arFonteRecursoTMP['num_banco'] = $arFonteRecurso['num_banco'];
                $arFonteRecursoTMP['num_agencia'] = $arFonteRecurso['num_agencia'];
                $arFonteRecursoTMP['digito'] = $arFonteRecurso['digito'];
                $arFonteRecursoTMP['vl_saidas'] = $arFonteRecurso['saldo_inicial'];
                $arFonteRecursoTMP['vl_entradas'] = '0,00';
                $arFonteRecursoTMP['saldo_inicial'] = $arFonteRecurso['saldo_inicial'];
                $arFonteRecursoTMP['saldo_final'] = '0,00';
                $arFonteRecursoTMP['fonte'] = '203000';
                $arFonteRecursoTMP['tipo_conta'] = $arFonteRecurso['tipo_conta'];
               
                $arFonteRecurso['vl_entradas'] = number_format(($arFonteRecurso['vl_entradas'] + $arFonteRecurso['saldo_inicial']), 2, '.', '');
                $arFonteRecurso['saldo_inicial'] = '0,00';
            }
            
            $rsBloco = 'rsBloco_'.$inCount;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arFonteRecurso));

            $obExportador->roUltimoArquivo->addBloco($$rsBloco);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_banco");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_agencia");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_conta_corrente");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 1 );

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fonte");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_inicial");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_entradas");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saidas");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_final");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
        
            if ( $arFiltroRelatorio['inMes'] == 1 ) {
                $arFonteRecursoTMP['numero_registro'] = ++$inCount;
                
                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arFonteRecursoTMP));
            
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_banco");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_agencia");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_conta_corrente");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 1 );
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fonte");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_inicial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_entradas");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saidas");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_final");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
            }
        }
    }
}

/*TIPO REGISTRO 90 - SALDOS BALANCETE FINANCEIRO*/
$inCount++;
$arRegistro = array();
$arRegistro[0][ 'tipo_registro' ] = 90 ;

/// Saldo Exrcicio Anterior Caixa

$arRegistro[0]['vlSaldoExercAntCaixa'] =
         number_format($obTMapeamento->recuperaTotalConta ( " where valor_lancamento.tipo = 'I'
                                                  and plano_conta.cod_estrutural like '1.1.1.1.1".(Sessao::getExercicio()>'2012'?'.01':'')."%'
                                                  and plano_conta.exercicio = '".Sessao::getExercicio()."' "
                                            ),2,'.','');
/// Saldo Exercicio anterior banco exercicio anterior( apenas recursos livres )

$arRegistro[0]['vlSaldoExercAntBanco'] =
         number_format($obTMapeamento->recuperaTotalConta ( " where valor_lancamento.tipo = 'I'
                                                                and plano_conta.cod_estrutural like '1.1.1.1".(Sessao::getExercicio()>'2012'?'.1.06':'.2')."%'
                                                                and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                         " ),2,'.','');

///// Saldo vinculado em conta bancaria exercicio anterior
$arRegistro[0]['vlSaldoExercAntVinculado'] =
        number_format(
                     $obTMapeamento->recuperaTotalConta ( " where valor_lancamento.tipo = 'I'
                                                              and plano_conta.cod_estrutural like '1.1.4%'
                                                              and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                                                   " ),2,'.','');

$arRegistro[0]['vlSaldoMesSegCaixa'] =
        number_format($obTMapeamento->recuperaTotalConta ( " --where valor_lancamento.tipo <> 'I'
                                                    WHERE plano_conta.cod_estrutural like '1.1.1.1.1".(Sessao::getExercicio()>'2012'?'.01':'')."%'
                                                    and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                    and lote.dt_lote  >= to_date( '01/01/".Sessao::getExercicio()."', 'dd/mm/yyyy' )
                                                    and lote.dt_lote  < to_date( '".$arFiltroRelatorio['stDataFinal']."', 'dd/mm/yyyy' ) + 1
                                          " ),2,'.','');

$arRegistro[0]['vlSaldoMesSegBanco'] =
        number_format($obTMapeamento->recuperaTotalConta ( " --where valor_lancamento.tipo <> 'I'
                                                               WHERE plano_conta.cod_estrutural like '1.1.1.1".(Sessao::getExercicio()>'2012'?'.1.06':'.2')."%'
                                                                 and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                                 and lote.dt_lote  >= to_date( '01/01/".Sessao::getExercicio()."', 'dd/mm/yyyy' )
                                                                 and lote.dt_lote  < to_date( '".$arFiltroRelatorio['stDataFinal']."', 'dd/mm/yyyy' ) + 1
                                                      " )
                                            ,2,'.','') ;

// Saldo vinculado em conta bancaria exercicio anterior
$arRegistro[0]['vlSaldoMesSegVinculado'] =
        number_format(
                     $obTMapeamento->recuperaTotalConta ( " --where valor_lancamento.tipo = 'I'
                                                            WHERE plano_conta.cod_estrutural like '1.1.4%'
                                                              and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                              and lote.dt_lote  >= to_date( '01/01/".Sessao::getExercicio()."', 'dd/mm/yyyy' )
                                                              and lote.dt_lote  < to_date( '".$arFiltroRelatorio['stDataFinal']."', 'dd/mm/yyyy' ) + 1
                                                                                   " ),2,'.','');

//recupera orgão e seta unidade para o registro 90
$obTMapeamento->recuperaOrgao($rsCTBOrgao, " WHERE exercicio = '".Sessao::getExercicio()."'");
$arRegistro[0]['num_orgao'] = $rsCTBOrgao->getCampo('num_orgao');
$arRegistro[0]['num_unidade'] = '01';

$arRegistro[0][ 'brancos'        ] = ' ';
$arRegistro[0][ 'numero_registro'] = $inCount;

$rsBloco = 'rsBloco_'.$inCount;
unset($$rsBloco);
$$rsBloco = new RecordSet();
$$rsBloco->preenche($arRegistro);

$obExportador->roUltimoArquivo->addBloco( $$rsBloco );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoExercAntCaixa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoExercAntBanco");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoExercAntVinculado");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoMesSegCaixa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoMesSegBanco");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoMesSegVinculado" );
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

//adicionado tipo 91 para demonstrar apartir de 2013
$inCount++;
$arTMP2 = array();
$arRegistro2 = array();
$arRegistro2b = array();

$obTMapeamento->recuperaRecurso($rsRecurso);

//Registro tipo 91 referente as informações financeiras por recurso
while (!$rsRecurso->eof()) {
    /// Saldo Exrcicio Anterior Caixa
    $obTMapeamento->recuperaTotalContaPorRecurso ( $rsSalExerAntCaixa
                                                 , " where valor_lancamento.tipo = 'I'
                                                       and plano_conta.cod_estrutural like '1.1.1.1.1.01%'
                                                       and recurso.cod_recurso = ".$rsRecurso->getCampo('cod_recurso')."
                                                       and plano_conta.exercicio = '".Sessao::getExercicio()."' "
                                                 );
    $arTMP2['vlSaldoExercAntCaixa'] = number_format((float) $rsSalExerAntCaixa->getCampo('vl_total'),2,'.','');

    /// Saldo Exercicio anterior banco exercicio anterior
    $obTMapeamento->recuperaTotalContaPorRecurso ( $rsSalExerAntBanco
                                                 , " where valor_lancamento.tipo = 'I'
                                                       and plano_conta.cod_estrutural like '1.1.1.1.1.06%'
                                                       and recurso.cod_recurso = ".$rsRecurso->getCampo('cod_recurso')."
                                                       and plano_conta.exercicio = '".Sessao::getExercicio()."' "
                                                 );
    $arTMP2['vlSaldoExercAntBanco'] = number_format((float) $rsSalExerAntBanco->getCampo('vl_total'),2,'.','');

    // Saldo vinculado em conta bancaria exercicio anterior
   $obTMapeamento->recuperaTotalContaPorRecurso ( $rsSalExerAntVinc1
                                                , " where valor_lancamento.tipo = 'I'
                                                      and plano_conta.cod_estrutural like '1.1.4%'
                                                      and recurso.cod_recurso = ".$rsRecurso->getCampo('cod_recurso')."
                                                      and plano_conta.exercicio = '".Sessao::getExercicio()."' "
                                                );

    $arTMP2['vlSaldoExercAntVinculado'] = number_format((float) $rsSalExerAntVinc1->getCampo('vl_total'),2,'.','');

    // Saldo do mês seguinte em caixa
    $obTMapeamento->recuperaTotalContaPorRecurso ( $rsSalMesSegCaixa
                                                 , " --where valor_lancamento.tipo <> 'I'
                                                         and plano_conta.cod_estrutural like '1.1.1.1.1.01%'
                                                         and recurso.cod_recurso = ".$rsRecurso->getCampo('cod_recurso')."
                                                         and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                         and lote.dt_lote  >= to_date( '01/01/".Sessao::getExercicio()."', 'dd/mm/yyyy' )
                                                         and lote.dt_lote  < to_date( '".$arFiltroRelatorio['stDataFinal']."', 'dd/mm/yyyy' ) + 1 "
                                                 );
    $arTMP2['vlSaldoMesSegCaixa'] = number_format((float) $rsSalMesSegCaixa->getCampo('vl_total'),2,'.','');

    // Saldo do mês seguinte no banco
    $obTMapeamento->recuperaTotalContaPorRecurso ( $rsSalMesSegBanco
                                                 , " --where valor_lancamento.tipo <> 'I'
                                                         and plano_conta.cod_estrutural like '1.1.1.1.1.06%'
                                                         and recurso.cod_recurso = ".$rsRecurso->getCampo('cod_recurso')."
                                                         and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                         and lote.dt_lote  >= to_date( '01/01/".Sessao::getExercicio()."', 'dd/mm/yyyy' )
                                                         and lote.dt_lote  < to_date( '".$arFiltroRelatorio['stDataFinal']."', 'dd/mm/yyyy' ) + 1 "
                                                 );
    $arTMP2['vlSaldoMesSegBanco'] = number_format((float) $rsSalMesSegBanco->getCampo('vl_total'),2,'.','');

    // Saldo vinculado em conta bancaria exercicio anterior
    $obTMapeamento->recuperaTotalContaPorRecurso ( $rsSalMesSegVinc1
                                                 , " where plano_conta.cod_estrutural like '1.1.4%'
                                                       and recurso.cod_recurso = ".$rsRecurso->getCampo('cod_recurso')."
                                                       and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                       and lote.dt_lote  >= to_date( '01/01/".Sessao::getExercicio()."', 'dd/mm/yyyy' )
                                                       and lote.dt_lote  < to_date( '".$arFiltroRelatorio['stDataFinal']."', 'dd/mm/yyyy' ) + 1 "
                                                 );
    $arTMP2['vlSaldoMesSegVinculado2'] = number_format((float) $rsSalMesSegVinc1->getCampo('vl_total'),2,'.','');
    $arTMP2['cod_recurso'] = $rsRecurso->getCampo('cod_recurso');
    $arTMP2['num_orgao'] = $rsRecurso->getCampo('num_orgao');
    $arTMP2['num_unidade'] = $rsRecurso->getCampo('num_unidade');
    $arTMP2[ 'tipo_registro' ] = 91 ;
    $arTMP2['numero_registro'] = $inCount;
    $arTMP2['vlSaldoExercAntBancoZerado'] = "0,00";
    $arTMP2['vlSaldoExercAntVinculadoZerado'] = "0,00";
    $arRegistro2[] = $arTMP2;

    $rsRecurso->proximo();
    $inCount++;
}

$rsBloco = 'rsBloco2_'.$inCount;

unset($$rsBloco);
$$rsBloco = new RecordSet();
$$rsBloco->preenche($arRegistro2);

$obExportador->roUltimoArquivo->addBloco( $$rsBloco );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_recurso");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoExercAntCaixa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vlSaldoExercAntBancoZerado'); //vlSaldoExercAntBanco
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vlSaldoExercAntVinculadoZerado'); //vlSaldoExercAntVinculado
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoMesSegCaixa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoMesSegBanco");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoMesSegVinculado2" );
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

//ÚLTIMA LINHA FIXA

$rsBloco = 'rsBloco2linhafixa_'.$inCount;

unset($$rsBloco);
$arTMP3 = $arRegistro2;
$arTMP3[0]['numero_registro'] = $inCount;
$arTMP3[0]['cod_recurso']     = "203000";
$arTMP3[0]['vlSaldoExercAntCaixa'] = "0,00";
$arTMP3[0]['vlSaldoMesSegCaixa'] = "0,00";
$arTMP3[0]['vlSaldoMesSegBanco'] = "0,00";
$arTMP3[0]['vlSaldoMesSegVinculado2'] = "0,00";

$$rsBloco = new RecordSet();
$$rsBloco->preenche($arTMP3);

$obExportador->roUltimoArquivo->addBloco( $$rsBloco );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_recurso");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoExercAntCaixa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vlSaldoExercAntBanco');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vlSaldoExercAntVinculado');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoMesSegCaixa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoMesSegBanco");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlSaldoMesSegVinculado2" );
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$inCount++;

//tipo99

$arTemp[0] = array( 'tipo_registro'=> '99', 'espacador'=> '', 'numero_registro' => $inCount );

$arRecordSet[$stArquivo] = new RecordSet();
$arRecordSet[$stArquivo]->preenche( $arTemp );

$obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espacador");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(88);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
