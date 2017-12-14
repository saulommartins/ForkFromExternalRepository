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
    * Data de Criação   : 07/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Evandro Melos
    $Id: TTCEPELiquidacao.class.php 60377 2014-10-16 16:02:38Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPELiquidacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPELiquidacao()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = "  SELECT
                                    
                             TO_CHAR(empenho.dt_empenho,'yyyy') AS ano_empenho                                  
                            , LPAD(LPAD(despesa.num_orgao::VARCHAR,2,'0') || LPAD(despesa.num_unidade::VARCHAR,2,'0'),5,'0') AS cod_unidade                                  
                            , empenho.cod_empenho AS num_empenho
                            , nota_liquidacao.cod_nota AS num_liquidacao
                            , TO_CHAR(nota_liquidacao.dt_liquidacao,'ddmmyyyy') AS dt_liquidacao                                  
                            , REPLACE(SUM(nota_liquidacao_item.vl_total)::varchar,'.',',') AS vl_liquidacao
                            , documento.cod_tipo
                            , documento.nro_documento
                            , documento.serie                   
                            , sw_uf.sigla_uf
                            , TRIM(nota_liquidacao.observacao) as historico
                            , codigo_fonte_recurso.cod_fonte AS cod_font_recursos
                    FROM empenho.pre_empenho
                     
                    JOIN empenho.empenho
                         ON empenho.exercicio = pre_empenho.exercicio
                        AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                            
                    JOIN empenho.nota_liquidacao
                         ON nota_liquidacao.exercicio_empenho = empenho.exercicio
                        AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                        AND nota_liquidacao.cod_empenho = empenho.cod_empenho
                            
                    JOIN empenho.nota_liquidacao_item
                         ON nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                        AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                        AND nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
                            
                    JOIN (  SELECT despesa.*
                                   , conta_despesa.cod_estrutural
                                   , pre_empenho_despesa.cod_pre_empenho
                                   , recurso.cod_fonte                                       
                            FROM empenho.pre_empenho_despesa
                            JOIN orcamento.despesa
                                 ON despesa.exercicio = pre_empenho_despesa.exercicio
                                AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                            JOIN orcamento.conta_despesa
                                 ON conta_despesa.exercicio = despesa.exercicio
                                AND conta_despesa.cod_conta = despesa.cod_conta
                            JOIN orcamento.recurso
                                 ON despesa.cod_recurso = recurso.cod_recurso
                                AND despesa.exercicio   = recurso.exercicio                                   
                    )AS despesa
                         ON despesa.exercicio = pre_empenho.exercicio
                        AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    LEFT JOIN tcepe.codigo_fonte_recurso
                         ON codigo_fonte_recurso.cod_recurso = despesa.cod_recurso
                        AND codigo_fonte_recurso.exercicio  = despesa.exercicio

                    LEFT JOIN tcepe.documento
                         ON documento.exercicio    = nota_liquidacao.exercicio
                        AND documento.cod_entidade = nota_liquidacao.cod_entidade
                        AND documento.cod_nota     = nota_liquidacao.cod_nota
                    
                    LEFT JOIN sw_uf
                         ON sw_uf.cod_uf = documento.cod_uf

                    WHERE nota_liquidacao.exercicio = '".$this->getDado('exercicio')."'
                    AND TO_CHAR(nota_liquidacao.dt_liquidacao,'mmyyyy') = '".$this->getDado('mes').$this->getDado('exercicio')."'                              
                    AND nota_liquidacao.cod_entidade IN (".$this->getDado('cod_entidade').")
                    AND empenho.exercicio = '".$this->getDado('exercicio')."'

                    GROUP BY  empenho.cod_entidade
                            , nota_liquidacao.cod_nota
                            , nota_liquidacao.exercicio_empenho
                            , empenho.cod_empenho                               
                            , despesa.num_unidade
                            , despesa.num_orgao
                            , empenho.dt_empenho
                            , nota_liquidacao.dt_liquidacao   
                            , documento.cod_tipo
                            , documento.nro_documento
                            , documento.serie   
                            , sw_uf.sigla_uf
                            , nota_liquidacao.observacao
                            , codigo_fonte_recurso.cod_fonte 

                    ORDER BY cod_unidade, num_empenho


        ";
        
        return $stSql;
    }
}

?>