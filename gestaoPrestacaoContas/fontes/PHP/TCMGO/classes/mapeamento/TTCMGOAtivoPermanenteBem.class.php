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
    * Extensão da Classe de mapeamento
    * Data de Criação: 18/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCMGOAtivoPermanenteBem.class.php 62348 2015-04-28 13:31:16Z jean $

    * Casos de uso: uc-06.04.00
*/

include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeBalancoFinanceiro.class.php" );

class TTCMGOAtivoPermanenteBem  extends TContabilidadeBalancoFinanceiro
{
    public function TTCMGOAtivoPermanenteBem()
    {
        parent::TContabilidadeBalancoFinanceiro();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function montaRecuperaTodos()
    {
        $stDataIni = '01/01/'.$this->getDado( 'exercicio' );
        $stDataFim = '31/12/'.$this->getDado( 'exercicio' );
        $stSql = "  SELECT * 
                    FROM (
                        SELECT  DISTINCT
                                bem.cod_bem
                                , '10' AS tipo_registro
                                , bem_comprado.num_orgao
                                , bem_comprado.num_unidade
                                , TO_CHAR(dt_aquisicao, 'yyyy') AS exercicio_aquisicao
                                , CASE  WHEN bem.cod_natureza = 1 THEN '01'
                                        WHEN bem.cod_natureza = 2 THEN '02'
                                        WHEN bem.cod_natureza = 3 THEN '03'
                                        WHEN bem.cod_natureza = 4 THEN '03'
                                END AS tipo_bem
                                , CASE WHEN bem.cod_natureza = 1 
                                    THEN
                                        bem_veiculo.tipo_bem_movel
                                    ELSE 
                                        bem_veiculo.tipo_veiculo::VARCHAR
                                END AS tipo_bem_movel 
                                , CASE WHEN bem.cod_natureza = 1 AND bem_veiculo.tipo_bem_movel != '05' AND bem_veiculo.tipo_bem_movel != '99' 
                                    THEN
                                        bem_veiculo.cod_combustivel
                                    ELSE 
                                        '00'
                                END AS tipo_combustivel
                                , bem.descricao AS desc_bem
                                , bem.num_placa AS tombamento
                                , 1 AS qtde
                                , COALESCE(bem.vl_bem, 0.00) AS vl_aquisicao_saldo_anterior
                                , bem_comprado_empenho.cod_empenho AS nro_empenho
                                , bem_comprado_empenho.exercicio   AS ano_empenho
                                , COALESCE(bem.vl_bem, 0.00) AS vl_aquisicao
                                , 0.00 AS vl_doacao
                                , COALESCE(max_reavaliacao.vl_reavaliacao, 0.00) AS vl_reavaliacao
                                , 0.00 AS vl_baixa_alienacao
                                , 0.00 AS vl_baixa_doacao
                                , 0.00 AS vl_baixa_depreciacao
                                , COALESCE(( SELECT vl_atualizado 
                                    FROM patrimonio.fn_depreciacao_acumulada(bem.cod_bem) 
                                    AS ( cod_bem INTEGER
                                       , vl_acumulado NUMERIC(14,2)
                                       , vl_atualizado NUMERIC(14,2)
                                       , vl_bem NUMERIC(14,2)
                                       , min_competencia VARCHAR
                                       , max_competencia VARCHAR
                                      )
                                ),0.00) AS vl_baixa_depreciacao
                                , COALESCE(( SELECT vl_atualizado 
                                    FROM patrimonio.fn_depreciacao_acumulada(bem.cod_bem) 
                                    AS ( cod_bem INTEGER
                                       , vl_acumulado NUMERIC(14,2)
                                       , vl_atualizado NUMERIC(14,2)
                                       , vl_bem NUMERIC(14,2)
                                       , min_competencia VARCHAR
                                       , max_competencia VARCHAR
                                    )
                                ),0.00) AS vl_saldo_atual
                                       
                        FROM patrimonio.bem
                        
                        LEFT JOIN patrimonio.bem_comprado
                            ON bem_comprado.cod_bem = bem.cod_bem
                        
                        LEFT JOIN patrimonio.bem_comprado_empenho
                            ON bem_comprado_empenho.cod_bem = bem.cod_bem
                        
                        LEFT JOIN ( SELECT  vl_reavaliacao
                                            , cod_bem
                                            , MAX(cod_reavaliacao)
                                    FROM patrimonio.reavaliacao
                                    GROUP BY cod_bem,vl_reavaliacao
                        ) AS max_reavaliacao
                            ON max_reavaliacao.cod_bem = bem.cod_bem
                        
                        LEFT JOIN ( SELECT  historico_bem.cod_bem
                                            , historico_bem.cod_local
                                            , historico_bem.cod_situacao
                                            , historico_bem.cod_orgao
                                            , historico_bem.descricao
                                            , historico_bem.timestamp
                                    FROM patrimonio.historico_bem
                                    INNER JOIN (SELECT cod_bem
                                                       , MAX(timestamp) AS timestamp
                                                FROM  patrimonio.historico_bem
                                                GROUP BY  cod_bem
                                    ) AS historico_bem_max
                                        ON historico_bem.cod_bem = historico_bem_max.cod_bem
                                        AND historico_bem.timestamp = historico_bem_max.timestamp
                        )AS historico_bem
                            ON  historico_bem.cod_bem = bem.cod_bem
                            
                        LEFT JOIN ( SELECT  apolice_bem.cod_bem
                                            , apolice_bem.cod_apolice
                                            , apolice_bem.timestamp
                                    FROM patrimonio.apolice_bem
                                    INNER JOIN (SELECT cod_bem
                                                        , MAX(timestamp) AS timestamp
                                                FROM patrimonio.apolice_bem
                                                GROUP BY cod_bem
                                    ) AS apolice_bem_max
                                        ON apolice_bem_max.cod_bem = apolice_bem.cod_bem
                                        AND apolice_bem_max.timestamp = apolice_bem.timestamp
                        ) AS apolice_bem
                            ON apolice_bem.cod_bem = bem.cod_bem
                            
                        LEFT JOIN patrimonio.apolice
                            ON apolice.cod_apolice = apolice_bem.cod_apolice
                            
                        LEFT JOIN patrimonio.situacao_bem
                            ON situacao_bem.cod_situacao = historico_bem.cod_situacao
                            
                        LEFT JOIN ( SELECT  bem_responsavel.cod_bem
                                            , bem_responsavel.numcgm
                                            , bem_responsavel.dt_inicio
                                            , sw_cgm.nom_cgm
                                    FROM patrimonio.bem_responsavel
                                    INNER JOIN sw_cgm
                                        ON sw_cgm.numcgm = bem_responsavel.numcgm
                                    INNER JOIN( SELECT  cod_bem
                                                        , MAX(dt_inicio) AS dt_inicio
                                                        , MAX(timestamp) AS timestamp
                                                FROM patrimonio.bem_responsavel
                                                GROUP BY cod_bem
                                    ) AS bem_responsavel_max
                                        ON bem_responsavel_max.cod_bem = bem_responsavel.cod_bem
                                        AND bem_responsavel_max.timestamp = bem_responsavel.timestamp
                        ) AS bem_responsavel
                            ON bem_responsavel.cod_bem = bem.cod_bem
                            
                        LEFT JOIN sw_cgm AS fornecedor
                            ON fornecedor.numcgm = bem.numcgm
                        
                        LEFT JOIN ( SELECT  bem_marca.cod_bem
                                            , bem_marca.cod_marca
                                            , marca.descricao
                                    FROM patrimonio.bem_marca
                                    INNER JOIN almoxarifado.marca
                                        ON bem_marca.cod_marca = marca.cod_marca
                        ) AS bem_marca
                            ON bem.cod_bem = bem_marca.cod_bem
                        
                        INNER JOIN orcamento.unidade
                             ON bem_comprado.num_orgao   = unidade.num_orgao
                            AND bem_comprado.num_unidade = unidade.num_unidade
                            AND bem_comprado.exercicio   = unidade.exercicio
                            
                        INNER JOIN tcmgo.orgao
                             ON orgao.num_orgao = unidade.num_orgao
                            AND orgao.exercicio = unidade.exercicio
                        
                        /*INNER JOIN tcmgo.unidade_responsavel
                             ON unidade_responsavel.exercicio       = unidade.exercicio
                            AND unidade_responsavel.num_unidade     = unidade.num_unidade
                            AND unidade_responsavel.num_orgao       = unidade.num_orgao*/

                        LEFT JOIN ( SELECT  b.cod_bem
                                            , veiculo.cod_veiculo
                                            , lpad(cod_tipo_tcm::varchar, 2, '0') AS tipo_bem_movel
                                            , lpad(cod_combustivel::varchar, 2, '0') AS cod_combustivel
                                            , tipo_veiculo.cod_tipo as tipo_veiculo
                                    FROM tcmgo.tipo_veiculo_vinculo
                                    INNER JOIN frota.tipo_veiculo
                                        ON tipo_veiculo.cod_tipo = tipo_veiculo_vinculo.cod_tipo			
                                    INNER JOIN frota.veiculo
                                        ON veiculo.cod_tipo_veiculo = tipo_veiculo.cod_tipo	
                                    INNER JOIN( SELECT  MAX(timestamp) as timestamp
                                                        , cod_veiculo
                                                FROM frota.veiculo_propriedade
                                                GROUP BY cod_veiculo
                                    ) AS veiculo_propriedade
                                        ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                                    INNER JOIN frota.proprio
                                        ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo			
                                        AND proprio.timestamp = veiculo_propriedade.timestamp
                                    INNER JOIN patrimonio.bem b
                                        ON b.cod_bem = proprio.cod_bem
                             
                                    LEFT JOIN(  SELECT  combustivel.cod_combustivel
                                                        , combustivel.descricao
                                                        , veiculo.cod_veiculo
                                                FROM tcmgo.combustivel
                                                INNER JOIN tcmgo.combustivel_vinculo
                                                    ON combustivel_vinculo.cod_tipo 	  = combustivel.cod_tipo
                                                    AND combustivel_vinculo.cod_combustivel = combustivel.cod_combustivel
                                                INNER JOIN frota.item
                                                    ON item.cod_item = combustivel_vinculo.cod_item
                                                INNER JOIN frota.combustivel_item
                                                    ON item.cod_item = item.cod_item
                                                INNER JOIN frota.combustivel AS f_combustivel
                                                    ON f_combustivel.cod_combustivel = combustivel_item.cod_combustivel
                                                INNER JOIN frota.veiculo_combustivel
                                                    ON veiculo_combustivel.cod_combustivel = f_combustivel.cod_combustivel           
                                                INNER JOIN frota.veiculo
                                                    ON veiculo.cod_veiculo = veiculo_combustivel.cod_veiculo                         
                                    ) AS combustivel
                                        ON veiculo.cod_veiculo = combustivel.cod_veiculo
                        ) AS bem_veiculo
                            ON bem_veiculo.cod_bem = bem.cod_bem
                        --WHERE EXTRACT(YEAR FROM bem.dt_aquisicao)  = ".$this->getDado( 'exercicio' )."    
                    ) AS resultado
            ";
        return $stSql;
    }

}
