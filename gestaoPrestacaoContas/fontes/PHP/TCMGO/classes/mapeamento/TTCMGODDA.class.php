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

class TTCMGODDA extends Persistente
{

    public function recuperaArquivoExportacao10(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        
        $stSQL = $this->montaRecuperaArquivoExportacao10($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        
        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    public function montaRecuperaArquivoExportacao10()
    {
        $stSQL = "  SELECT  DISTINCT
                            '10' as tipo_registro
                            ,(SELECT num_orgao 
                                    FROM tcmgo.orgao 
                                    where exercicio = '2015' 
                                    AND uf_crc_contador = 'GO'
                            ) as cod_orgao    
                            ,divida_ativa.cod_inscricao as numr_insc_divida_ativa
                            ,'2' as nome_unidade_gestora
                            , CASE WHEN sw_cgm_pessoa_juridica.numcgm IS NULL THEN
                                        1
                                    ELSE
                                        2
                             END as tipo_devedor
                            , CASE WHEN sw_cgm_pessoa_juridica.cnpj IS NULL THEN
                                        sw_cgm_pessoa_fisica.cpf
                                    ELSE
                                        sw_cgm_pessoa_juridica.cnpj
                             END as cpf_cnpj_devedor        
                            , remove_acentos(sw_cgm.nom_cgm) as nome_devedor
                            , genero_credito.cod_natureza as tipo_divida
                            , TO_CHAR(divida_ativa.dt_inscricao,'ddmmyyyy') as data_inscricao
                            , COALESCE(divida_processo.cod_processo,0) as numr_proc_admin
                            , CASE  WHEN (cobranca_judicial.cod_inscricao IS NOT NULL OR parcelamento.judicial = true)
                                        THEN 1  
                                    WHEN cobranca_judicial.cod_inscricao IS NULL
                                        THEN 3
                                    ELSE
                                        2
                             END AS situacao_cobranca
                            
                            , 0.00 AS vl_original_divida
                            , 0.00 AS vl_ant_principal_atualizado
                            , 0.00 AS vl_ant_juros_atualizado
                            
                            , 0.00 AS vl_saldo_anterior

                            , 0.00 AS vl_insc_princ_atual_periodo
                            , 0.00 AS vl_insc_juros_atual_periodo
                            , 0.00 AS vl_atualiz_monet_principal
                            , 0.00 AS vl_juros_periodo
                            , 0.00 AS vl_abatimento_principal
                            , 0.00 AS vl_abatimento_juros
                            , 0.00 AS vl_baixa_rec_principal
                            , 0.00 AS vl_baixa_rec_juros
                            , 0.00 AS vl_baixa_canc_principal
                            , 0.00 AS vl_baixa_canc_juros
                            , 0.00 AS vl_ajuste_exe_ant_principal
                            , 0.00 AS vl_ajuste_exe_ant_juros
                            , 0.00 AS vl_principal_atualizado
                            , 0.00 AS vl_juros_atualizado
                            , 0.00 AS vl_saldo_atual

                    FROM divida.divida_ativa

                    INNER JOIN divida.divida_cgm
                         ON divida_cgm.exercicio        = divida_ativa.exercicio
                        AND divida_cgm.cod_inscricao    = divida_ativa.cod_inscricao

                    INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = divida_cgm.numcgm
            
                    LEFT JOIN sw_cgm_pessoa_fisica
                        ON sw_cgm_pessoa_fisica.numcgm = divida_cgm.numcgm
            
                    LEFT JOIN sw_cgm_pessoa_juridica
                        ON  sw_cgm_pessoa_juridica.numcgm = divida_cgm.numcgm
            
                    LEFT  JOIN divida.divida_parcelamento
                        ON divida_parcelamento.exercicio        = divida_ativa.exercicio
                        AND divida_parcelamento.cod_inscricao   = divida_ativa.cod_inscricao
            
                    LEFT JOIN divida.parcelamento
                        ON parcelamento.num_parcelamento = divida_parcelamento.num_parcelamento
            
                    LEFT JOIN divida.parcela_origem
                        ON parcela_origem.num_parcelamento  = parcelamento.num_parcelamento
            
                    INNER JOIN monetario.credito
                        ON credito.cod_credito      = parcela_origem.cod_credito
                        AND credito.cod_natureza    = parcela_origem.cod_natureza
                        AND credito.cod_genero      = parcela_origem.cod_genero
                        AND credito.cod_especie     = parcela_origem.cod_especie
            
                    INNER JOIN monetario.especie_credito
                        ON especie_credito.cod_especie      = credito.cod_especie
                        AND especie_credito.cod_genero      = credito.cod_genero
                        AND especie_credito.cod_natureza    = credito.cod_natureza
            
                    INNER JOIN monetario.genero_credito
                        ON genero_credito.cod_natureza  = especie_credito.cod_natureza
                        AND genero_credito.cod_genero   = especie_credito.cod_genero
            
                    LEFT  JOIN divida.divida_processo
                        ON divida_processo.exercicio        = divida_ativa.exercicio
                        AND divida_processo.cod_inscricao   = divida_ativa.cod_inscricao
            
                    LEFT JOIN divida.cobranca_judicial
                        ON cobranca_judicial.cod_inscricao  = divida_ativa.cod_inscricao
                        AND cobranca_judicial.exercicio     = divida_ativa.exercicio
            
                    WHERE divida_ativa.exercicio = '".$this->getDado('exercicio')."'

        ";
        return $stSQL;

    }

}

?>