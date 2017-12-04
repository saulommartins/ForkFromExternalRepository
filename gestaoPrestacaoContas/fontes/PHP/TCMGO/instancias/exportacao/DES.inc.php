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
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    $Revision: 62759 $
    $Name$
    $Author: jean $
    $Date: 2015-06-16 15:00:15 -0300 (Tue, 16 Jun 2015) $
    
    $Id: DES.inc.php 62759 2015-06-16 18:00:15Z jean $

    * Casos de uso: uc-06.04.00
*/
    include_once( CAM_GPC_TGO_MAPEAMENTO."TTGODES.class.php" );
    $obTMapeamento = new TTGODES();
    $obTMapeamento->setDado('exercicio'  , Sessao::getExercicio() );
    $obTMapeamento->setDado('cod_entidade',$stEntidades );

    //registro 10
    $obTMapeamento->recuperaDespesa( $rsDespesa,"","",$boTransacao );
    //registro 11
    $obTMapeamento->recuperaDespesaElemento( $rsDSPElemento,"","",$boTransacao );
    //registro 12
    $obTMapeamento->recuperaDespesaRecurso( $rsDSPRecurso,"","",$boTransacao );
    //registro 13    
    $obTMapeamento->recuperaDespesaRecursoDetalhamento( $rsDSPRecursoDetalhamento,"","",$boTransacao );
    

    $inCount = 0;

    //REGISTRO 10
    $stChave10 = '';
    $stChaveAuxiliar10 = '';
    foreach ($rsDespesa->getElementos() as $arRegistro10) {
        $arRegistro10['numero_sequencial'] = ++$inCount;
        $stChaveAuxiliar10 = $arRegistro10['cod_programa']
                            .$arRegistro10['num_orgao']
                            .$arRegistro10['num_unidade']
                            .$arRegistro10['cod_funcao']
                            .$arRegistro10['cod_subfuncao']
                            .$arRegistro10['cod_natureza']
                            .$arRegistro10['numero_pao'];

        if ( $stChaveAuxiliar10 != $stChave10 ) {
            $stChave10 = $arRegistro10['cod_programa']
                    .$arRegistro10['num_orgao']
                    .$arRegistro10['num_unidade']
                    .$arRegistro10['cod_funcao']
                    .$arRegistro10['cod_subfuncao']
                    .$arRegistro10['cod_natureza']
                    .$arRegistro10['numero_pao'];

            $rsBloco = 'rsBloco_'.$inCount;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arRegistro10));
            
            $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
            $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_natureza");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_pao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_pao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(200);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_orcado");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_suplementado");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_reduzido");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_credito_especial");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_credito_extra");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_limitacao_empenho");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_resersao_limitacao_empenho");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_correcao_orcamento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulacao_realocacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_transposicao_recurso");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_autorizado");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(39);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);        

            //REGISTRO 11
            $stChave11 = '';
            foreach ($rsDSPElemento->getElementos() as $arRegistro11) {
                $stChave11Aux = $arRegistro11['cod_programa']
                                .$arRegistro11['num_orgao']
                                .$arRegistro11['num_unidade']
                                .$arRegistro11['cod_funcao']
                                .$arRegistro11['cod_subfuncao']
                                .$arRegistro11['cod_natureza']
                                .$arRegistro11['numero_pao'];

                //Verifica se registro 11 bate com chave do registro 10
                if ($stChave10 == $stChave11Aux) {
                    //Chave única do registro 11
                    $stChave11Aux = $stChave11Aux.$arRegistro11['elemento_despesa'];
                    if ($stChave11 != $stChave11Aux) {
                        $stChave11 = $stChave11Aux;

                        $arRegistro11['numero_sequencial'] = ++$inCount;

                        $rsBloco = 'rsBloco_'.$inCount;
                        unset($$rsBloco);
                        $$rsBloco = new RecordSet();
                        $$rsBloco->preenche(array($arRegistro11));

                        $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                        $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');
        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_natureza");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_pao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elemento_despesa");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_orcado");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_suplementado");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_reduzido");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_credito_especial");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_credito_extra");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_limitacao_empenho");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_reversao_limitacao_empenho");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_correcao_orcamento");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulacao_realocacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_transposicao_recurso");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_autorizado");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_empenhado");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_liquidado");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_pago");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(194);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                        //REGISTRO 12
                        $stChave12 = '';
                        foreach ($rsDSPRecurso->getElementos() as $arRegistro12) {
                            $stChave12Aux = $arRegistro12['cod_programa']
                                            .$arRegistro12['num_orgao']
                                            .$arRegistro12['num_unidade']
                                            .$arRegistro12['cod_funcao']
                                            .$arRegistro12['cod_subfuncao']
                                            .$arRegistro12['cod_natureza']
                                            .$arRegistro12['numero_pao']
                                            .$arRegistro12['elemento_despesa'];
                    
                            //Verifica se registro 12 bate com chave do registro 11
                            if ($stChave11 == $stChave12Aux) {
                                //Chave única do registro 12
                                if ($stChave12 != $stChave12Aux) {
                                    $stChave12 = $stChave12Aux;
                        
                                    $arRegistro12['numero_sequencial'] = ++$inCount;

                                    $rsBloco = 'rsBloco_'.$inCount;
                                    unset($$rsBloco);
                                    $$rsBloco = new RecordSet();
                                    $$rsBloco->preenche(array($arRegistro12));
        
                                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                                    $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');
    
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
            
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                                
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                                
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                                
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                                
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
                                
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_natureza");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                                
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_pao");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
                                
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elemento_despesa");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                                        
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
                                        
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_orcado");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                        
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_suplementado");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                        
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_reduzido");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                        
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_credito_especial");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                        
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_credito_extra");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_limitacao_empenho");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                    
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_reversao_limitacao_empenho");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                    
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_correcao_orcamento");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                    
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulacao_realocacao");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                    
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_transposicao_recurso");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_autorizado");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                    
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_empenhado");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                    
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_liquidado");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                    
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_pago");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espacador");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(191);
                                    
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                                    //REGISTRO 13
                                    $stChave13 = '';
                                        foreach ($rsDSPRecursoDetalhamento->getElementos() as $arRegistro13) {
                                            $stChave13Aux = $arRegistro13['cod_programa']
                                                            .$arRegistro13['num_orgao']
                                                            .$arRegistro13['num_unidade']
                                                            .$arRegistro13['cod_funcao']
                                                            .$arRegistro13['cod_subfuncao']
                                                            .$arRegistro13['cod_natureza']
                                                            .$arRegistro13['numero_pao']
                                                            .$arRegistro13['elemento_despesa'];
                                            //Verifica se registro 13 bate com chave do registro 12
                                            if ($stChave12 == $stChave13Aux) {
                                                //Chave única do registro 13
                                                if ($stChave13 != $stChave13Aux) {
                                                    $stChave13 = $stChave13Aux;
                                            
                                                    $arRegistro13['numero_sequencial'] = ++$inCount;
        
                                                    $rsBloco = 'rsBloco_'.$inCount;
                                                    unset($$rsBloco);
                                                    $$rsBloco = new RecordSet();
                                                    $$rsBloco->preenche(array($arRegistro13));
                
                                                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                                                    $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');
        
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_natureza");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_pao");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elemento_despesa");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("det_fonte_recurso");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_empenhado");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_liquidado");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_pago");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espacador");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(331);
                                                
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                                                }//if chave registro 13
                                            }//if registro 13
                                        }//foreach registro 13
                                }//if chave registro 12
                            }//if registro 12    
                        }//foreach registro 12
                    }//if chave registro 11
                }//if registro 11
            }//foreach registro 11
        }//if chave registro 10
    }//foreach registro 10

    $arTemp[0] = array( 'tipo_registro'=> '99', 'espacador'=> '', 'numero_sequencial' => ++$inCount );

    $rsRegistro99 = new RecordSet();
    $rsRegistro99->preenche( $arTemp );

    $obExportador->roUltimoArquivo->addBloco($rsRegistro99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espacador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(399);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);



?>
