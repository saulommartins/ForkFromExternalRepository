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
    * Classe de mapeamento da função fn_relatorio_resumo_execucao_restos_pagar
    * Data de Criação: 24/02/2016

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    $Id: FEmpenhoRelatorioResumoExecucaoRP.class.php 65265 2016-05-06 18:40:44Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class FEmpenhoRelatorioResumoExecucaoRP extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    **/
    function __construct()
    {
        parent::Persistente();

        $this->setTabela("empenho.fn_relatorio_resumo_execucao_restos_pagar");

        $this->AddCampo('cod_entidade'      ,'integer',true ,'',false,false);
        $this->AddCampo('exercicio'         ,'varchar',true ,'',false,false);
        $this->AddCampo('dt_inicial'        ,'varchar',true ,'',false,false);
        $this->AddCampo('dt_final'          ,'varchar',true ,'',false,false);
        $this->AddCampo('exercicio_empenho' ,'varchar',false,'',false,false);
        $this->AddCampo('cgm_credor'        ,'integer',false,'',false,false);
        $this->AddCampo('inOrgao'           ,'integer',false,'',false,false);
        $this->AddCampo('inUnidade'         ,'integer',false,'',false,false);
    }

    function montaRecuperaTodos()
    {
        $stSql  = " SELECT rp.exercicio
                         , rp.cod_entidade
                         , sw_cgm.nom_cgm                AS nom_entidade
                         , SUM(rp.empenhado)             AS empenhado
                         , SUM(rp.aliquidar)             AS aliquidar
                         , SUM(rp.liquidadoapagar)       AS liquidadoapagar
                         , SUM(rp.anulado)               AS anulado
                         , SUM(rp.liquidado)             AS liquidado
                         , SUM(rp.pagamento)             AS pagamento
                         , SUM(rp.empenhado_saldo)       AS empenhado_saldo
                         , SUM(rp.aliquidar_saldo)       AS aliquidar_saldo
                         , SUM(rp.liquidadoapagar_saldo) AS liquidadoapagar_saldo

                      FROM empenho.fn_relatorio_resumo_execucao_restos_pagar   ( '".$this->getDado("exercicio")."'
                                                                               , '".$this->getDado("cod_entidade")."'
                                                                               , '".$this->getDado("dt_inicial")."'
                                                                               , '".$this->getDado("dt_final")."'
                                                                               , '".$this->getDado("exercicio_empenho")."'
                                                                               , '".$this->getDado("cgm_credor")."'
                                                                               , ".$this->getDado("inOrgao")."
                                                                               , ".$this->getDado("inUnidade")."
                                                                               ) AS rp
                                                                               ( cod_empenho                INTEGER,
                                                                                 cod_entidade               INTEGER,
                                                                                 exercicio                  VARCHAR,
                                                                                 credor                     VARCHAR,
                                                                                 emissao                    TEXT,
                                                                                 vencimento                 TEXT,
                                                                                 empenhado                  NUMERIC(14,2),
                                                                                 aliquidar                  NUMERIC(14,2),
                                                                                 liquidadoapagar            NUMERIC(14,2),
                                                                                 anulado                    NUMERIC(14,2),
                                                                                 liquidado                  NUMERIC(14,2),
                                                                                 pagamento                  NUMERIC(14,2),
                                                                                 empenhado_saldo            NUMERIC(14,2),
                                                                                 aliquidar_saldo            NUMERIC(14,2),
                                                                                 liquidadoapagar_saldo      NUMERIC(14,2)
                                                                               )

                INNER JOIN orcamento.entidade
                        ON entidade.cod_entidade = rp.cod_entidade
                       AND entidade.exercicio = rp.exercicio

                INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = entidade.numcgm

                  GROUP BY rp.exercicio
                         , rp.cod_entidade
                         , sw_cgm.nom_cgm

                  ORDER BY rp.cod_entidade
                         , rp.exercicio
        ";

        return $stSql;
    }

}
