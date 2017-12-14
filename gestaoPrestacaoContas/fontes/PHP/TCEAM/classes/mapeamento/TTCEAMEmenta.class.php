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

class TTCEAMEmenta extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMEmenta()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function montaRecuperaTodos()
    {
        $stSql  = " SELECT  LPAD(norma.num_norma,4,'') || TO_CHAR(norma.dt_assinatura,'yyyy') as num_norma \n";
        $stSql .= "        ,CASE WHEN norma.cod_tipo_norma = 0                                            \n";
        $stSql .= "              THEN 9                                                                   \n";
        $stSql .= "              WHEN norma.cod_tipo_norma = 1                                            \n";
        $stSql .= "              THEN 5                                                                   \n";
        $stSql .= "              WHEN norma.cod_tipo_norma = 2                                            \n";
        $stSql .= "              THEN 1                                                                   \n";
        $stSql .= "              WHEN norma.cod_tipo_norma = 3                                            \n";
        $stSql .= "              THEN 9                                                                   \n";
        $stSql .= "              WHEN norma.cod_tipo_norma = 4                                            \n";
        $stSql .= "              THEN 2                                                                   \n";
        $stSql .= "              ELSE 9                                                                   \n";
        $stSql .= "         END AS cod_tipo_norma                                                         \n";
        $stSql .= "        ,CASE WHEN suplementacao.cod_tipo = 1                                          \n";
        $stSql .= "              THEN 16                                                                  \n";
        $stSql .= "              WHEN suplementacao.cod_tipo = 2                                          \n";
        $stSql .= "              THEN 12                                                                  \n";
        $stSql .= "              WHEN suplementacao.cod_tipo = 4                                          \n";
        $stSql .= "              THEN 15                                                                  \n";
        $stSql .= "              WHEN suplementacao.cod_tipo = 5                                          \n";
        $stSql .= "              THEN 13                                                                  \n";
        $stSql .= "              WHEN suplementacao.cod_tipo = 6                                          \n";
        $stSql .= "              THEN 53                                                                  \n";
        $stSql .= "              WHEN suplementacao.cod_tipo = 7                                          \n";
        $stSql .= "              THEN 51                                                                  \n";
        $stSql .= "              WHEN suplementacao.cod_tipo = 9                                          \n";
        $stSql .= "              THEN 54                                                                  \n";
        $stSql .= "              WHEN suplementacao.cod_tipo = 10                                         \n";
        $stSql .= "              THEN 52                                                                  \n";
        $stSql .= "              WHEN suplementacao.cod_tipo = 11                                         \n";
        $stSql .= "              THEN 55                                                                  \n";
        $stSql .= "              ELSE 99                                                                  \n";
        $stSql .= "         END AS cod_tipo                                                               \n";
        $stSql .= "       , ''::varchar as cod_unidade_descentralizada                                                               \n";
        $stSql .= "   FROM normas.norma                                                                   \n";
        $stSql .= "   JOIN normas.tipo_norma                                                              \n";
        $stSql .= "     ON norma.cod_tipo_norma = tipo_norma.cod_tipo_norma                               \n";
        $stSql .= "   JOIN orcamento.suplementacao                                                        \n";
        $stSql .= "     ON suplementacao.cod_norma = norma.cod_norma                                      \n";
        $stSql .= "   JOIN orcamento.suplementacao_suplementada                                           \n";
        $stSql .= "     ON suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao \n";
        $stSql .= "    AND suplementacao_suplementada.exercicio = suplementacao.exercicio                 \n";
        $stSql .= "   JOIN orcamento.despesa                                                              \n";
        $stSql .= "     ON despesa.cod_despesa = suplementacao_suplementada.cod_despesa                   \n";
        $stSql .= "    AND despesa.exercicio = suplementacao_suplementada.exercicio                       \n";
        $stSql .= "  WHERE norma.exercicio = '".$this->getDado('exercicio')."'                            \n";
        $stSql .= "    AND to_char(norma.dt_publicacao,'mm') = '".$this->getDado('inMes')."'              \n";
        $stSql .= "    AND despesa.cod_entidade in (".$this->getDado('entidades').")                      \n";
        $stSql .= "    AND NOT EXISTS ( SELECT  1                                                                           \n";
        $stSql .= "                                   FROM  orcamento.suplementacao_anulada                     \n";
        $stSql .= "                                WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio                \n";
        $stSql .= "                                    AND  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao           \n";
        $stSql .= "                              )                                                                                          \n";
        $stSql .= "    AND NOT EXISTS ( SELECT  1                                                                           \n";
        $stSql .= "                                    FROM  orcamento.suplementacao_anulada                        \n";
        $stSql .= "                                  WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio                           \n";
        $stSql .= "                                      AND  suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao        \n";
        $stSql .= "                              )            \n";
        $stSql .= "GROUP BY norma.num_norma, norma.cod_tipo_norma, suplementacao.cod_tipo, norma.dt_assinatura \n";

        if ($this->getDado('boIncorporar')) {
                $stSql .= " UNION
                SELECT  LPAD(norma.num_norma,4,'') || TO_CHAR(norma.dt_assinatura,'yyyy') as num_norma
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
                         END AS cod_tipo_norma
                        ,CASE WHEN suplementacao.cod_tipo = 1
                              THEN 16
                              WHEN suplementacao.cod_tipo = 2
                              THEN 12
                              WHEN suplementacao.cod_tipo = 4
                              THEN 15
                              WHEN suplementacao.cod_tipo = 5
                              THEN 13
                              WHEN suplementacao.cod_tipo = 6
                              THEN 53
                              WHEN suplementacao.cod_tipo = 7
                              THEN 51
                              WHEN suplementacao.cod_tipo = 9
                              THEN 54
                              WHEN suplementacao.cod_tipo = 10
                              THEN 52
                              WHEN suplementacao.cod_tipo = 11
                              THEN 55
                              ELSE 99
                         END AS cod_tipo
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
