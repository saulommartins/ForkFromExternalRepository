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

    * Data de Criação:

    * @author Analista: Gelson
    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGOROP.class.php 61551 2015-02-04 15:12:44Z evandro $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOROP extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTGOROP()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    //Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
    public function montaRecuperaTodos()
    {
        $stSql = "  SELECT
                            '10' as  tipo_registro
                            , despesa.num_orgao as cod_orgao
                            , despesa.num_unidade as cod_unidade
                            , TRIM(obra.especificacao) as descricao_obra
                            , obra.cod_obra                                  
                            , obra.ano_obra
                            , bem.vl_bem as vl_aquisicao
                            ,(   SELECT vl_atualizado 
                                        FROM patrimonio.fn_depreciacao_acumulada(COALESCE(reavaliacao.cod_bem,bem.cod_bem))
                                        as retorno (
                                            cod_bem             INTEGER
                                            ,vl_acumulado       NUMERIC
                                            ,vl_atualizado      NUMERIC
                                            ,vl_bem             NUMERIC
                                            ,min_competencia    VARCHAR
                                            ,max_competencia    VARCHAR)
                            ) as vl_inc_reavaliacao
                            , CASE WHEN bem_baixado.tipo_baixa = 1 THEN
                                    (   SELECT vl_atualizado 
                                        FROM patrimonio.fn_depreciacao_acumulada(bem_baixado.cod_bem)
                                        as retorno (
                                            cod_bem             INTEGER
                                            ,vl_acumulado       NUMERIC
                                            ,vl_atualizado      NUMERIC
                                            ,vl_bem             NUMERIC
                                            ,min_competencia    VARCHAR
                                            ,max_competencia    VARCHAR)
                                    )
                                ELSE
                                    (   SELECT vl_bem 
                                        FROM patrimonio.fn_depreciacao_acumulada(COALESCE(bem_baixado.cod_bem,bem.cod_bem))
                                        as retorno (
                                            cod_bem             INTEGER
                                            ,vl_acumulado       NUMERIC
                                            ,vl_atualizado      NUMERIC
                                            ,vl_bem             NUMERIC
                                            ,min_competencia    VARCHAR
                                            ,max_competencia    VARCHAR)
                                    )
                            END AS vl_bai_doacao
                            ,(   SELECT vl_atualizado 
                                        FROM patrimonio.fn_depreciacao_acumulada(COALESCE(bem_baixado.cod_bem,bem.cod_bem))
                                        as retorno (
                                            cod_bem             INTEGER
                                            ,vl_acumulado       NUMERIC
                                            ,vl_atualizado      NUMERIC
                                            ,vl_bem             NUMERIC
                                            ,min_competencia    VARCHAR
                                            ,max_competencia    VARCHAR)
                            ) as vl_bai_depreciacao                                    
                            , ( empenho.fn_consultar_valor_empenhado( empenho.exercicio ,empenho.cod_empenho ,empenho.cod_entidade ) -
                            empenho.fn_consultar_valor_empenhado_anulado( empenho.exercicio ,empenho.cod_empenho ,empenho.cod_entidade) )
                            -
                            ( empenho.fn_consultar_valor_liquidado(empenho.exercicio ,empenho.cod_empenho ,empenho.cod_entidade ) -
                              empenho.fn_consultar_valor_liquidado_anulado( empenho.exercicio, empenho.cod_empenho, empenho.cod_entidade )
                            ) AS saldo_atual
                    FROM tcmgo.obra
                    JOIn tcmgo.patrimonio_bem_obra
                         ON patrimonio_bem_obra.cod_obra = obra.cod_obra
                        AND patrimonio_bem_obra.ano_obra = obra.ano_obra
                    
                    JOIN patrimonio.bem
                         ON bem.cod_bem = patrimonio_bem_obra.cod_bem
                    LEFT JOIN patrimonio.reavaliacao
                         ON reavaliacao.cod_bem = bem.cod_bem
                    
                    LEFT JOIN patrimonio.bem_baixado
                         ON bem_baixado.cod_bem = bem.cod_bem                                
                    
                    JOIN tcmgo.obra_empenho
                         on obra.cod_obra = obra_empenho.cod_obra
                        and obra.ano_obra = obra_empenho.ano_obra
                    
                    JOIN empenho.empenho
                         on obra_empenho.cod_empenho  = empenho.cod_empenho
                        and obra_empenho.cod_entidade = empenho.cod_entidade
                        and obra_empenho.exercicio    = empenho.exercicio
                    
                    JOIN empenho.pre_empenho
                         on empenho.exercicio       = pre_empenho.exercicio
                        and empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                    
                    JOIN empenho.pre_empenho_despesa
                         on pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                        and pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                    
                    JOIN orcamento.despesa
                         on despesa.exercicio   = pre_empenho_despesa.exercicio
                        and despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                    
                    WHERE obra_empenho.cod_entidade in ( ". $this->getDado ( 'stEntidades') ." )
                    AND obra.ano_obra = '". $this->getDado( 'exercicio' ) ."'
            ";

        return $stSql;
    }
}
