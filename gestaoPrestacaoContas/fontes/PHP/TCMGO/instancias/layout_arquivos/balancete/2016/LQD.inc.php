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

    * Data de Criação   : 18/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Id: LQD.inc.php 65190 2016-04-29 19:36:51Z michel $

    * Casos de uso: uc-06.04.00
*/

    include_once CAM_GPC_TGO_MAPEAMENTO.Sessao::getExercicio()."/TTGOLQD.class.php";

    $arFiltroRelatorio = Sessao::read('filtroRelatorio');
    $obTMapeamento = new TTGOLQD();
    $obTMapeamento->setDado('dtInicio'   , $arFiltroRelatorio['stDataInicial'] );
    $obTMapeamento->setDado('dtFim'   	 , $arFiltroRelatorio['stDataFinal']   );
    $obTMapeamento->setDado('exercicio'  , Sessao::getExercicio() );
    $obTMapeamento->setDado('cod_entidade',$stEntidades );
    $obTMapeamento->recuperaLiquidacaoDespesa( $rsArquivo );
    $obTMapeamento->recuperaLiquidacaoDespesaFR( $rsLiquidacaoDespesaFR );
    $obTMapeamento->recuperaDocumentosFiscais( $rsDocumentosFiscais );

    $i = 0;

    foreach ($rsArquivo->arElementos as $arArquivo) {
        $stChave =  $arArquivo['codprograma'].
                    $arArquivo['codorgao'].
                    $arArquivo['numunidade'].
                    $arArquivo['codfuncao'].
                    $arArquivo['codsubfuncao'].
                    $arArquivo['codnatureza'].
                    $arArquivo['numpao'].
                    $arArquivo['elementodespesa'].
                    $arArquivo['subelementodespesa'].
                    $arArquivo['cod_empenho'].
                    $arArquivo['numeroliquidacao'];

        $arArquivo['numero_sequencial'] = ++$i;

        $rsBloco = 'rsBloco_'.$i;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arArquivo));

        $obExportador->roUltimoArquivo->addBloco( $$rsBloco );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codprograma");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numunidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfuncao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codsubfuncao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codnatureza");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numpao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elementodespesa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelementodespesa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dotacaoresto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(21);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtempenho");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numeroliquidacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtliquidacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipoliquidacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_liquidado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_contador");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_resp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("especificacaoliquidacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(200);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        //REGISTRO TIPO 11 -- LIQUIDACAO DESPESA POR FONTE RECURSO
        foreach ($rsLiquidacaoDespesaFR->arElementos as $arLiquidacaoDespesaFR) {
            $stChaveElemento11 = $arLiquidacaoDespesaFR['codprograma'].
                                 $arLiquidacaoDespesaFR['codorgao'].
                                 $arLiquidacaoDespesaFR['numunidade'].
                                 $arLiquidacaoDespesaFR['codfuncao'].
                                 $arLiquidacaoDespesaFR['codsubfuncao'].
                                 $arLiquidacaoDespesaFR['codnatureza'].
                                 $arLiquidacaoDespesaFR['numpao'].
                                 $arLiquidacaoDespesaFR['elementodespesa'].
                                 $arLiquidacaoDespesaFR['subelementodespesa'].
                                 $arLiquidacaoDespesaFR['cod_empenho'].
                                 $arLiquidacaoDespesaFR['numeroliquidacao']
                                 ;

            if ($stChave === $stChaveElemento11) {

                $arLiquidacaoDespesaFR['numero_sequencial'] = ++$i;

                $rsBloco = 'rsBloco_'.$i;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arLiquidacaoDespesaFR));

                $obExportador->roUltimoArquivo->addBloco( $$rsBloco );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codprograma");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numunidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfuncao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codsubfuncao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codnatureza");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numpao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elementodespesa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelementodespesa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dotacaoresto");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(21);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtempenho");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numeroliquidacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtliquidacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfonterecurso");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vldespfr");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(256);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
            }
        }

        //REGISTRO TIPO 12 -- DETALHAMENTO DOS DOCUMENTOS FISCAIS

            foreach ($rsDocumentosFiscais->arElementos as $arDocumentosFiscais) {
                $stChaveElemento12 =    $arDocumentosFiscais['codprograma'].
                                        $arDocumentosFiscais['codorgao'].
                                        $arDocumentosFiscais['numunidade'].
                                        $arDocumentosFiscais['codfuncao'].
                                        $arDocumentosFiscais['codsubfuncao'].
                                        $arDocumentosFiscais['codnatureza'].
                                        $arDocumentosFiscais['numpao'].
                                        $arDocumentosFiscais['elementodespesa'].
                                        $arDocumentosFiscais['subelementodespesa'].
                                        $arDocumentosFiscais['cod_empenho'].
                                        $arDocumentosFiscais['numeroliquidacao']
                                        ;
                                        
                if ($stChave === $stChaveElemento12) {
                    
                    $arDocumentosFiscais['numero_sequencial'] = ++$i;
                    
                    $rsBloco = 'rsBloco_'.$i;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arDocumentosFiscais));

                    $obExportador->roUltimoArquivo->addBloco( $$rsBloco );

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2)           ;

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codprograma");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numunidade");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfuncao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codsubfuncao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codnatureza");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numpao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elementodespesa");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelementodespesa");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dotacaoresto");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(21);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtempenho");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numeroliquidacao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtliquidacao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_docfiscal");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_docfiscal");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("serie_docfiscal");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_docfiscal");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("chaveacesso");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(44);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_docvltotal");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_docassoc");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_cpf");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_credor");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_inscest");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_inscmun");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep_municipio");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf_credor");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_credor");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(72);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                }
            }
    }

    $arTemp[0] = array( 'tipo_registro'=> '99', 'espacador'=> '', 'numero_sequencial' => $i+1 );

    $arRecordSet[$stArquivo] = new RecordSet();
    $arRecordSet[$stArquivo]->preenche( $arTemp );

    $obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espacador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(349);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
