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
    * Classe de mapeamento da tabela tcers.receita_carac_peculiar_receita
    * Data de Criação: 16/02/2009

    * @author Analista: Tonismar R. Bernardo
    * @author Desenvolvedor: André Machado

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CLA_PERSISTENTE);

class TTCERSLivroDiario extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCERSLivroDiario()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.plano_conta');

        $this->AddCampo('exercicio','varchar',true,'4',true,true);
        $this->AddCampo('dtInicial', 'varchar', true, '10', false, false);
        $this->AddCampo('dtFinal', 'varchar', true, '10', false, false);
    }

    public function montaRecuperaTodos()
    {
        $stSql = " SELECT * FROM (
SELECT
*, 'D' as tipo_conta1
                    FROM
                         contabilidade.plano_conta            as pc
                        ,contabilidade.plano_analitica        as pa
                        ,contabilidade.conta_debito           as cd
                        ,contabilidade.valor_lancamento       as vl
                        ,contabilidade.lancamento             as la

            LEFT JOIN contabilidade.historico_contabil as chc
              ON chc.exercicio = la.exercicio
             AND chc.cod_historico = la.cod_historico
            LEFT JOIN contabilidade.lancamento_empenho     as le
              ON le.exercicio = la.exercicio
             AND le.cod_entidade = la.cod_entidade
             AND le.tipo = la.tipo
             AND le.cod_lote = la.cod_lote
             AND le.sequencia = la.sequencia
            LEFT JOIN contabilidade.liquidacao as cl
              ON le.exercicio = cl.exercicio
                     AND le.cod_lote = cl.cod_lote
             AND le.tipo = cl.tipo
             AND le.sequencia = cl.sequencia
             AND le.cod_entidade = cl.cod_entidade
            LEFT JOIN empenho.nota_liquidacao as enl
              ON cl.exercicio = enl.exercicio
             AND cl.cod_entidade = enl.cod_entidade
             AND cl.cod_nota = enl.cod_nota
            LEFT JOIN empenho.empenho as ee
              ON ee.exercicio = enl.exercicio
             AND ee.cod_entidade = enl.cod_entidade
             AND ee.cod_empenho = enl.cod_empenho

                        ,contabilidade.lote                   as lo
                        ,contabilidade.sistema_contabil       as sc

                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cd.cod_plano
                    AND     pa.exercicio    = cd.exercicio
                    AND     cd.cod_lote     = vl.cod_lote
                    AND     cd.tipo         = vl.tipo
                    AND     cd.sequencia    = vl.sequencia
                    AND     cd.exercicio    = vl.exercicio
                    AND     cd.tipo_valor   = vl.tipo_valor
                    AND     cd.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio = '".$this->getDado('exercicio')."'
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio
            AND     lo.dt_lote between to_date('".$this->getDado('dtInicial')."','dd/mm/yyyy') AND to_date('".$this->getDado('dtFinal')."','dd/mm/yyyy')
--                    ORDER BY pc.cod_estrutural


UNION
SELECT *, 'C' as tipo_conta1
                    FROM
                         contabilidade.plano_conta       as pc
                        ,contabilidade.plano_analitica   as pa
                        ,contabilidade.conta_credito     as cc
                        ,contabilidade.valor_lancamento  as vl
                        ,contabilidade.lancamento        as la

            LEFT JOIN contabilidade.historico_contabil as chc
              ON chc.exercicio = la.exercicio
             AND chc.cod_historico = la.cod_historico
            LEFT JOIN contabilidade.lancamento_empenho     as le
              ON le.exercicio = la.exercicio
             AND le.cod_entidade = la.cod_entidade
             AND le.tipo = la.tipo
             AND le.cod_lote = la.cod_lote
             AND le.sequencia = la.sequencia
            LEFT JOIN contabilidade.liquidacao as cl
              ON le.exercicio = cl.exercicio
                     AND le.cod_lote = cl.cod_lote
             AND le.tipo = cl.tipo
             AND le.sequencia = cl.sequencia
             AND le.cod_entidade = cl.cod_entidade
            LEFT JOIN empenho.nota_liquidacao as enl
              ON cl.exercicio = enl.exercicio
             AND cl.cod_entidade = enl.cod_entidade
             AND cl.cod_nota = enl.cod_nota
            LEFT JOIN empenho.empenho as ee
              ON ee.exercicio = enl.exercicio
             AND ee.cod_entidade = enl.cod_entidade
             AND ee.cod_empenho = enl.cod_empenho

                        ,contabilidade.lote              as lo
                        ,contabilidade.sistema_contabil  as sc
                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cc.cod_plano
                    AND     pa.exercicio    = cc.exercicio
                    AND     cc.cod_lote     = vl.cod_lote
                    AND     cc.tipo         = vl.tipo
                    AND     cc.sequencia    = vl.sequencia
                    AND     cc.exercicio    = vl.exercicio
                    AND     cc.tipo_valor   = vl.tipo_valor
                    AND     cc.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio = '".$this->getDado('exercicio')."'
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio
                    AND     lo.dt_lote between to_date('".$this->getDado('dtInicial')."','dd/mm/yyyy') AND to_date('".$this->getDado('dtFinal')."','dd/mm/yyyy')
--                    ORDER BY pc.cod_estrutural
) as tbl
 ";

        return $stSql;
    }
}
