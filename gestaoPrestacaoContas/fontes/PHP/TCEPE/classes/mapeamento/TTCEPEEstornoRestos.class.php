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
    $Id: TTCEPEEstornoRestos.class.php 60621 2014-11-04 15:15:34Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEEstornoRestos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPEEstornoRestos()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = "
                   SELECT retorno.exercicio AS ano_empenho
                        , LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS unidade_orcamentaria
                        , retorno.empenho AS num_empenho
                        , 1 AS num_estorno
                        , REPLACE(retorno.data,'/','') AS data_estorno
                        , retorno.valor AS valor_estorno
                        , empenho_anulado.motivo
                     FROM empenho.fn_empenho_restos_pagar_anulado_liquidado_estornoliquidacao(  ''
                                                                                               ,''
                                                                                               ,'".$this->getDado('dt_inicial')."'
                                                                                               ,'".$this->getDado('dt_final')."'
                                                                                               ,'".$this->getDado('cod_entidade')."'
                                                                                               ,''
                                                                                               ,''
                                                                                               ,''
                                                                                               ,''
                                                                                               ,''
                                                                                               ,''
                                                                                               ,'1' --Situação = Anulado
                                                                                               ,''
                                                                                               ,''  
                                                                                             )
                                                                                   AS retorno( entidade            integer,
                                                                                               empenho             integer,
                                                                                               exercicio           char(4),
                                                                                               cgm                 integer,
                                                                                               razao_social        varchar,
                                                                                               cod_nota            integer,
                                                                                               valor               numeric,
                                                                                               data                text                                           
                                                                                             )
                     JOIN empenho.empenho
                       ON empenho.exercicio             = retorno.exercicio
                      AND empenho.cod_entidade          = retorno.entidade
                      AND empenho.cod_empenho           = retorno.empenho 
                     JOIN empenho.empenho_anulado
                       ON empenho_anulado.exercicio     = empenho.exercicio
                      AND empenho_anulado.cod_entidade  = empenho.cod_entidade
                      AND empenho_anulado.cod_empenho   = empenho.cod_empenho
                     JOIN ( SELECT pre_empenho.exercicio
                                 , pre_empenho.cod_pre_empenho
                                 , CASE WHEN ( pre_empenho.implantado = true ) THEN
                                            restos_pre_empenho.num_orgao
                                        ELSE 
                                            despesa.num_orgao
                                   END AS num_orgao
                                 , CASE WHEN ( pre_empenho.implantado = true ) THEN 
                                            restos_pre_empenho.num_unidade
                                        ELSE 
                                            despesa.num_unidade
                                   END AS num_unidade
                                 , codigo_fonte_recurso.cod_fonte
                              FROM empenho.pre_empenho
                         LEFT JOIN empenho.restos_pre_empenho
                                ON restos_pre_empenho.exercicio         = pre_empenho.exercicio
                               AND restos_pre_empenho.cod_pre_empenho   = pre_empenho.cod_pre_empenho
                         LEFT JOIN empenho.pre_empenho_despesa
                                ON pre_empenho_despesa.exercicio        = pre_empenho.exercicio
                               AND pre_empenho_despesa.cod_pre_empenho  = pre_empenho.cod_pre_empenho
                         LEFT JOIN orcamento.despesa
                                ON despesa.exercicio                    = pre_empenho_despesa.exercicio
                               AND despesa.cod_despesa                  = pre_empenho_despesa.cod_despesa
                         LEFT JOIN orcamento.recurso
                                ON recurso.exercicio                    = despesa.exercicio
                               AND recurso.cod_recurso                  = despesa.cod_recurso
                         LEFT JOIN tcepe.codigo_fonte_recurso
                                ON codigo_fonte_recurso.cod_recurso     = recurso.cod_recurso
                               AND codigo_fonte_recurso.exercicio       = recurso.exercicio
                          ) AS despesa
                       ON despesa.exercicio             = empenho.exercicio
                      AND despesa.cod_pre_empenho       = empenho.cod_pre_empenho
        ";
        return $stSql;
    }
}

?>