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
    * 
    * Data de Criação   : 01/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Evandro Melos
    $Id: TTCEPENormaAtualizacao.class.php 60474 2014-10-23 16:59:30Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPENormaAtualizacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPENormaAtualizacao()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = "  SELECT
                         suplementacao.exercicio
                        , norma.num_norma || suplementacao.exercicio AS numero
                        , atributo_norma_valor.valor || suplementacao.exercicio AS nro_lei
                        , TO_CHAR(suplementacao.dt_suplementacao,'ddmmyyyy') AS dt_lei_alteracao
                        , vinculo_tipo_norma.cod_tipo as tipo_norma
                    
                    FROM orcamento.suplementacao
                    
                    LEFT JOIN orcamento.suplementacao_suplementada
                         ON suplementacao_suplementada.exercicio = suplementacao.exercicio
                        AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                     
                    LEFT JOIN orcamento.suplementacao_reducao
                         ON suplementacao_reducao.exercicio = suplementacao.exercicio
                        AND suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
                     
                    JOIN orcamento.despesa
                         ON despesa.exercicio = suplementacao_suplementada.exercicio
                        AND despesa.cod_despesa = suplementacao_suplementada.cod_despesa
                     
                    JOIN normas.norma
                         ON norma.cod_norma = suplementacao.cod_norma
                    
                    JOIN normas.tipo_norma
                         ON tipo_norma.cod_tipo_norma = norma.cod_tipo_norma

                    LEFT JOIN normas.atributo_norma_valor
                         ON atributo_norma_valor.cod_norma = norma.cod_norma

                    LEFT JOIN normas.atributo_tipo_norma
                         ON atributo_tipo_norma.cod_modulo     = atributo_norma_valor.cod_modulo
                        AND atributo_tipo_norma.cod_cadastro   = atributo_norma_valor.cod_cadastro
                        AND atributo_tipo_norma.cod_atributo   = atributo_norma_valor.cod_atributo
                        AND atributo_tipo_norma.cod_tipo_norma = atributo_norma_valor.cod_tipo_norma

                    JOIN administracao.atributo_dinamico
                         ON atributo_dinamico.cod_modulo   = atributo_tipo_norma.cod_modulo
                        AND atributo_dinamico.cod_cadastro = atributo_tipo_norma.cod_cadastro
                        AND atributo_dinamico.cod_atributo = atributo_tipo_norma.cod_atributo          
                        AND atributo_dinamico.nom_atributo ilike '%N_mero da Lei%'
                    
                    LEFT JOIN tcepe.vinculo_tipo_norma
                         ON vinculo_tipo_norma.cod_tipo_norma = tipo_norma.cod_tipo_norma

                    WHERE suplementacao.exercicio = '".$this->getDado('exercicio')."'
                        AND despesa.cod_entidade IN (".$this->getDado('cod_entidade').")
                        AND suplementacao.dt_suplementacao BETWEEN TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy')
                        AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                        AND NOT EXISTS
                        (
                           SELECT 1
                             FROM orcamento.suplementacao_anulada
                            WHERE suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                               OR suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                              AND suplementacao_anulada.exercicio         = suplementacao.exercicio
                        )
                      
                    GROUP BY  suplementacao.exercicio
                            , norma.num_norma         
                            , nro_lei
                            , dt_lei_alteracao
                            , vinculo_tipo_norma.cod_tipo_norma 
                    ORDER BY dt_lei_alteracao
                            , nro_lei
        ";
        
        return $stSql;
    }
}

?>