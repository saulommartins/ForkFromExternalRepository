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

    * @author Tonismar R. Bernardo

    * @date: 21/03/2011

    * @ignore
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEAMMovfun extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMMovfun()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function montaRecuperaTodos()
    {
        $stSql = "  SELECT  LPAD(norma.num_norma::varchar,4,'0') || TO_CHAR(norma.dt_assinatura,'yyyy') as num_norma                   \n";
        $stSql .= "                        ,CASE WHEN norma.cod_tipo_norma = 0                                                                              \n";
        $stSql .= "                              THEN 9                                                                                                                           \n";
        $stSql .= "                              WHEN norma.cod_tipo_norma = 1                                                                                  \n";
        $stSql .= "                              THEN 5                                                                                                                           \n";
        $stSql .= "                              WHEN norma.cod_tipo_norma = 2                                                                                   \n";
        $stSql .= "                              THEN 1                                                                                                                              \n";
        $stSql .= "                              WHEN norma.cod_tipo_norma = 3                                                                                   \n";
        $stSql .= "                              THEN 9                                                                                                                              \n";
        $stSql .= "                              WHEN norma.cod_tipo_norma = 4                                                                                   \n";
        $stSql .= "                              THEN 2                                                                                                                             \n";
        $stSql .= "                              ELSE 9                                                                                                                              \n";
        $stSql .= "                         END AS cod_tipo_fundamento                                                                                               \n";
        $stSql .= "                       , to_char(norma.dt_assinatura, 'dd/mm/yyyy')as data_fundamento                                         \n";
        $stSql .= "                       , ''::varchar as numero_data_leis_autorizadas                                                                          \n";
        $stSql .= "                       , ''::varchar as numero_diario_oficial                                                                                         \n";
        $stSql .= "                       , ''::varchar as cod_unidade_descentralizada                                                                           \n";
        $stSql .= "                   FROM normas.norma                                                                                                                  \n";
        $stSql .= "                   JOIN normas.tipo_norma                                                                                                             \n";
        $stSql .= "                     ON norma.cod_tipo_norma = tipo_norma.cod_tipo_norma                                                          \n";
        $stSql .= "                   JOIN orcamento.suplementacao                                                                                                   \n";
        $stSql .= "                     ON suplementacao.cod_norma = norma.cod_norma                                                                 \n";
        $stSql .= "                   JOIN orcamento.suplementacao_suplementada                                                                          \n";
        $stSql .= "                     ON suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao        \n";
        $stSql .= "                    AND suplementacao_suplementada.exercicio = suplementacao.exercicio                                \n";
        $stSql .= "                   JOIN orcamento.despesa                                                                                                             \n";
        $stSql .= "                     ON despesa.cod_despesa = suplementacao_suplementada.cod_despesa                              \n";
        $stSql .= "                    AND despesa.exercicio = suplementacao_suplementada.exercicio                                          \n";
        $stSql .= "                  WHERE norma.exercicio = '".$this->getDado('exercicio')."'                                                                                                  \n";
        $stSql .= "                    AND to_char(norma.dt_publicacao,'mm') = '".$this->getDado('inMes')."'                                                                          \n";
        $stSql .= "                    AND despesa.cod_entidade in (".$this->getDado('entidades').")                                                                                               \n";
        $stSql .= "                    AND NOT EXISTS ( SELECT  1                                                                                                        \n";
        $stSql .= "                                       FROM  orcamento.suplementacao_anulada                                                              \n";
        $stSql .= "                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio                      \n";
        $stSql .= "                                        AND  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao            \n";
        $stSql .= "                                   )                                                                                                                                    \n";
        $stSql .= "                    AND NOT EXISTS ( SELECT  1                                                                                                         \n";
        $stSql .= "                                       FROM  orcamento.suplementacao_anulada                                                              \n";
        $stSql .= "                                      WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio                    \n";
        $stSql .= "                                        AND  suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao           \n";
        $stSql .= "                                   )                                                                                                                                      \n";
        $stSql .= "                GROUP BY norma.num_norma, norma.cod_tipo_norma, suplementacao.cod_tipo, norma.dt_assinatura                   \n";

        if ($this->getDado('boIncorporar')) {
                $stSql .= " UNION
                SELECT  LPAD(norma.num_norma::varchar,4,'0') || TO_CHAR(norma.dt_assinatura,'yyyy') as num_norma
                        ,CASE WHEN norma.cod_tipo_norma = 0
                              THEN 9
                              WHEN norma.cod_tipo_norma = 1
                              THEN 5
                              WHEN norma.cod_tipo_norma = 2
                              THEN 1
                              WHEN norma.cod_tipo_norma = 3
                              THEN 9
                              WHEN norma.cod_tipo_norma = 4
                              THEN 2
                              ELSE 9
                         END AS cod_tipo_fundamento
                       , to_char(norma.dt_assinatura, 'dd/mm/yyyy')as data_fundamento
                       , ''::varchar as numero_data_leis_autorizadas
                       , ''::varchar as numero_diario_oficial
                       , ''::varchar as cod_unidade_descentralizada
                   FROM normas.norma
                   JOIN normas.tipo_norma
                     ON norma.cod_tipo_norma = tipo_norma.cod_tipo_norma
                   JOIN orcamento.suplementacao
                     ON suplementacao.cod_norma = norma.cod_norma
                   JOIN orcamento.suplementacao_suplementada
                     ON suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                    AND suplementacao_suplementada.exercicio = suplementacao.exercicio
                   JOIN orcamento.despesa
                     ON despesa.cod_despesa = suplementacao_suplementada.cod_despesa
                    AND despesa.exercicio = suplementacao_suplementada.exercicio
                  WHERE norma.exercicio = '".$this->getDado('exercicio')."'
                    AND despesa.cod_entidade in (".$this->getDado('stCodEntidadesIncorporar').")
                    AND NOT EXISTS ( SELECT  1
                                       FROM  orcamento.suplementacao_anulada
                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                        AND  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                   )
                    AND NOT EXISTS ( SELECT  1
                                       FROM  orcamento.suplementacao_anulada
                                      WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                        AND  suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                   )
                GROUP BY norma.num_norma, norma.cod_tipo_norma, suplementacao.cod_tipo, norma.dt_assinatura ";
        }

        return $stSql;
    }
}
