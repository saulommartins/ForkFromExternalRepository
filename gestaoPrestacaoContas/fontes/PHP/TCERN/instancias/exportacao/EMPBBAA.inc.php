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

    * Data de Criação   : 05/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 25713 $
    $Name$
    $Author: diego $
    $Date: 2007-10-01 01:43:00 -0300 (Seg, 01 Out 2007) $

    * Casos de uso: uc-06.06.00
*/

    include_once( CAM_GPC_TCERN_MAPEAMENTO."TTRNEmpenho.class.php" );
    $obTMapeamento = new TTRNEmpenho();
    $obTMapeamento->setDado('inBimestre'    , $inBimestre );
    $obTMapeamento->setDado('inCodEntidade' , $inCodEntidade );
    $obTMapeamento->setDado('stDataInicial' , $stDataInicial );
    $obTMapeamento->setDado('stDataFinal'   , $stDataFinal );

    $obTMapeamento->recuperaDadosEmpenho($rsArquivo);
    $obTMapeamento->recuperaDadosPagamento($rsDados);
    $obTMapeamento->recuperaDadosAnulacoes($rsAnulacoes);
    $obTMapeamento->recuperaDadosAnulacoesPagamento($rsPagamento);

    foreach ($rsArquivo->arElementos as $arArquivo) {
        $rsBloco = 'rsBloco_'.$inCount;
    unset($$rsBloco);
    $$rsBloco = new RecordSet();
    $$rsBloco->preenche(array($arArquivo));
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]1"); //Reservado
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('[]'.Sessao::getExercicio()); //bimestre 1
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('[]'.$inBimestre); //bimestre 2
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERIC_ZEROS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]0"); //processo
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_empenho");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]"); //processo licitacao
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("estrutural");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_recurso");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("pf_pj");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_cnpj"); //correto
        //$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]08161341000150"); //fixo (teste)
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_cgm");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_empenho");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_empenhado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fundef");
        //$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]00"); //fundef
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("royalties");
        //$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]00"); //royalties
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        //$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("classificacao_institucional");
        //$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        //$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        //$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("classificacao_funcional");
        //$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        //$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_pao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(48);
    }

    //$arRecordSet = null;
    //$obTMapeamento->recuperaDadosPagamento($arRecordSet[$stArquivo]);
//$obTMapeamento->debug();
//die();

    foreach ($rsDados->arElementos as $arDados) {
        $rsBloco = 'rsBloco_'.$inCount;
    unset($$rsBloco);
    $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arDados));
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]2"); //Reservado
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('[]'.Sessao::getExercicio()); //bimestre 1
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('[]'.$inBimestre); //bimestre 2
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERIC_ZEROS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ordem_bancaria"); //numero OB Cheque [verificar]
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_pagamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_serie"); // nota fiscal
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_nota"); // nota fiscal
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_nota"); // nota fiscal
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_validacao"); //nota_fiscal
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_pago");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_cnpj");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente"); //correto
        //$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]112345"); //fixo (teste)
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("modelo"); // Modelo Nota Fiscal
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
    }

    //$arRecordSet = null;
    //$obTMapeamento->recuperaDadosAnulacoes($arRecordSet[$stArquivo]);

    foreach ($rsAnulacoes->arElementos as $arAnulacoes) {
        $rsBloco = 'rsBloco_'.$inCount;
    unset($$rsBloco);
    $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arAnulacoes));
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]3"); //Reservado
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('[]'.Sessao::getExercicio()); //bimestre 1
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('[]'.$inBimestre); //bimestre 2
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERIC_ZEROS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade"); //num_anulacao (nota_anulacao)
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERIC_ZEROS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho"); //nota_anulacao
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_anulado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("motivo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_anulacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(164);
    }

    //$arRecordSet = null;
    //$obTMapeamento->recuperaDadosAnulacoesPagamento($arRecordSet[$stArquivo]);
    $contOP = 0;
    foreach ($rsPagamento->arElementos as $arPagamento/* => $key*/) {
    $contOP++;
    $arPagamento['cont_op'] = $contOP;

        $rsBloco = 'rsBloco_'.$inCount;
    unset($$rsBloco);
    $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arPagamento));
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]4"); //Reservado
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('[]'.Sessao::getExercicio()); //bimestre 1
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('[]'.$inBimestre); //bimestre 2
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ordem_bancaria"); //numero OB Cheque [verificar]
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade"); //num_anulacao (nota_anulacao)
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERIC_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cont_op"); //num_anulacao (nota_anulacao)
    //$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ordem_pagamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERIC_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_anulado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_anulacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERIC_ZEROS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(184);
    }
