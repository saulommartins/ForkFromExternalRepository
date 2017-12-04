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
/*
 * Classe de mapeamento da tabela tceam.elenco_contas_tce
 *
 * @package SW2
 * @subpackage Mapeamento
 * @version $Id$
 * @author eduardo.schitz@cnm.org.br
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEAMVinculoElencoPlanoContas extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     * @author eduardo.schitz@cnm.org.br
     */
    public function TTCEAMVinculoElencoPlanoContas()
    {
        parent::Persistente();
        $this->setTabela('tceam.vinculo_elenco_plano_contas');

        $this->setComplementoChave('cod_plano, exercicio_plano');

        $this->AddCampo('cod_plano'       , 'integer', true, ''  , true , true);
        $this->AddCampo('exercicio_plano' , 'varchar', true, '4' , true , true);
        $this->AddCampo('cod_elenco'      , 'integer', true, ''  , false, true);
        $this->AddCampo('exercicio_elenco', 'varchar', true, '4' , false, true);
    }

    public function recuperaVinculoPlanoContas(&$rsRecordSet, $stCondicao = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaVinculoPlanoContas().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaVinculoPlanoContas()
    {
        $stSql = "
            SELECT contas.cod_estrutural
                 , contas.cod_plano
                 , contas.nom_conta
                 , contas.exercicio
                 , SUM(contas.vl_credito) AS vl_credito
                 , SUM(contas.vl_debito) AS vl_debito
                 , vinculo_elenco_plano_contas.cod_elenco
              FROM (
                SELECT conta_credito.nom_conta
                     , conta_credito.cod_estrutural
                     , conta_credito.cod_plano
                     , conta_credito.exercicio
                     , ABS(valor_lancamento.vl_lancamento) AS vl_credito
                     , 0 AS vl_debito
                  FROM contabilidade.lancamento
                  JOIN contabilidade.valor_lancamento
                    ON valor_lancamento.cod_lote     = lancamento.cod_lote
                   AND valor_lancamento.tipo         = lancamento.tipo
                   AND valor_lancamento.sequencia    = lancamento.sequencia
                   AND valor_lancamento.exercicio    = lancamento.exercicio
                   AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                  JOIN contabilidade.lote
                    ON lote.cod_lote      = lancamento.cod_lote
                   AND lote.exercicio     = lancamento.exercicio
                   AND lote.tipo          = lancamento.tipo
                   AND lote.cod_entidade  = lancamento.cod_entidade
                  JOIN orcamento.entidade
                    ON entidade.cod_entidade  = lancamento.cod_entidade
                   AND entidade.exercicio     = lancamento.exercicio
                  JOIN sw_cgm
                    ON sw_cgm.numcgm = entidade.numcgm

                  JOIN ( SELECT conta_credito.cod_lote
                              , conta_credito.tipo
                              , conta_credito.sequencia
                              , conta_credito.exercicio
                              , conta_credito.tipo_valor
                              , conta_credito.cod_entidade
                              , plano_analitica.cod_plano
                              , plano_conta.cod_estrutural
                              , plano_conta.nom_conta
                           FROM contabilidade.plano_analitica
                           JOIN contabilidade.conta_credito
                             ON conta_credito.cod_plano    = plano_analitica.cod_plano
                            AND conta_credito.exercicio    = plano_analitica.exercicio
                           JOIN contabilidade.plano_conta
                             ON plano_conta.cod_conta    = plano_analitica.cod_conta
                            AND plano_conta.exercicio    = plano_analitica.exercicio
                          WHERE plano_analitica.exercicio = '".$this->getDado('exercicio')."'
                     ) AS  conta_credito
                    ON conta_credito.cod_lote     = valor_lancamento.cod_lote
                   AND conta_credito.sequencia    = valor_lancamento.sequencia
                   AND conta_credito.tipo_valor   = valor_lancamento.tipo_valor
                   AND conta_credito.tipo         = valor_lancamento.tipo
                   AND conta_credito.exercicio    = valor_lancamento.exercicio
                   AND conta_credito.cod_entidade = valor_lancamento.cod_entidade

                 WHERE lancamento.exercicio = '".$this->getDado('exercicio')."'
                   AND to_char(lote.dt_lote,'mm') = '".$this->getDado('mes')."'
           --        AND lancamento.tipo <> 'I'

            UNION

                SELECT conta_debito.nom_conta
                     , conta_debito.cod_estrutural
                     , conta_debito.cod_plano
                     , conta_debito.exercicio
                     , 0 AS vl_credito
                     , ABS(valor_lancamento.vl_lancamento) AS vl_debito
                  FROM contabilidade.lancamento
                  JOIN contabilidade.valor_lancamento
                    ON valor_lancamento.cod_lote     = lancamento.cod_lote
                   AND valor_lancamento.tipo         = lancamento.tipo
                   AND valor_lancamento.sequencia    = lancamento.sequencia
                   AND valor_lancamento.exercicio    = lancamento.exercicio
                   AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                  JOIN contabilidade.lote
                    ON lote.cod_lote      = lancamento.cod_lote
                   AND lote.exercicio     = lancamento.exercicio
                   AND lote.tipo          = lancamento.tipo
                   AND lote.cod_entidade  = lancamento.cod_entidade
                  JOIN orcamento.entidade
                    ON entidade.cod_entidade  = lancamento.cod_entidade
                   AND entidade.exercicio     = lancamento.exercicio
                  JOIN sw_cgm
                    ON sw_cgm.numcgm = entidade.numcgm

                  JOIN ( SELECT conta_debito.cod_lote
                              , conta_debito.tipo
                              , conta_debito.sequencia
                              , conta_debito.exercicio
                              , conta_debito.tipo_valor
                              , conta_debito.cod_entidade
                              , plano_analitica.cod_plano
                              , plano_conta.cod_estrutural
                              , plano_conta.nom_conta
                           FROM contabilidade.plano_analitica
                           JOIN contabilidade.conta_debito
                             ON conta_debito.cod_plano    = plano_analitica.cod_plano
                            AND conta_debito.exercicio    = plano_analitica.exercicio
                           JOIN contabilidade.plano_conta
                             ON plano_conta.cod_conta    = plano_analitica.cod_conta
                            AND plano_conta.exercicio    = plano_analitica.exercicio
                          WHERE plano_analitica.exercicio = '".$this->getDado('exercicio')."'
                     ) AS  conta_debito
                    ON conta_debito.cod_lote     = valor_lancamento.cod_lote
                   AND conta_debito.sequencia    = valor_lancamento.sequencia
                   AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                   AND conta_debito.tipo         = valor_lancamento.tipo
                   AND conta_debito.exercicio    = valor_lancamento.exercicio
                   AND conta_debito.cod_entidade = valor_lancamento.cod_entidade

                 WHERE lancamento.exercicio = '".$this->getDado('exercicio')."'
                   AND to_char(lote.dt_lote,'mm') = '".$this->getDado('mes')."'
              --     AND lancamento.tipo <> 'I'

                   UNION

                    SELECT plano_conta.nom_conta
                    , plano_conta.cod_estrutural
                    , plano_analitica.cod_plano
                    , plano_analitica.exercicio
                    , 0 AS vl_credito
                    , 0 AS vl_debito
                    FROM contabilidade.plano_analitica
                    JOIN contabilidade.plano_conta
                    ON plano_conta.cod_conta = plano_analitica.cod_conta
                    AND plano_conta.exercicio = plano_analitica.exercicio
                    JOIN contabilidade.plano_banco
                    ON plano_banco.cod_plano = plano_analitica.cod_plano
                    AND plano_banco.exercicio = plano_analitica.exercicio
                    WHERE plano_analitica.exercicio = '".$this->getDado('exercicio')."'
            ) AS contas
    LEFT JOIN tceam.vinculo_elenco_plano_contas
           ON vinculo_elenco_plano_contas.cod_plano       = contas.cod_plano
          AND vinculo_elenco_plano_contas.exercicio_plano = contas.exercicio
        WHERE vl_credito > 0 OR vl_debito > 0 OR (vl_credito = 0 AND vl_debito = 0)
     GROUP BY contas.cod_estrutural
            , contas.cod_plano
            , contas.nom_conta
            , contas.exercicio
            , vinculo_elenco_plano_contas.cod_elenco
     ORDER BY contas.cod_estrutural";

        return $stSql;
    }

}
