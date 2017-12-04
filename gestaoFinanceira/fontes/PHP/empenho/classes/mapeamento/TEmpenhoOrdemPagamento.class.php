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
    * Classe de mapeamento da tabela EMPENHO.ORDEM_PAGAMENTO
    * Data de Criação: 30/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TEmpenhoOrdemPagamento.class.php 66620 2016-10-07 18:17:07Z franver $

    * Casos de uso: uc-02.03.12,uc-02.03.16,uc-02.03.05,uc-02.04.05,uc-02.03.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  EMPENHO.ORDEM_PAGAMENTO
  * Data de Criação: 30/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoOrdemPagamento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TEmpenhoOrdemPagamento()
    {
        parent::Persistente();
        $this->setTabela('empenho.ordem_pagamento');

        $this->setCampoCod('cod_ordem');
        $this->setComplementoChave('exercicio,cod_entidade');

        $this->AddCampo('cod_ordem','integer',true,'',true,false);
        $this->AddCampo('exercicio','char',true,'04',true,false);
        $this->AddCampo('cod_entidade','integer',true,'',true,false);
        $this->AddCampo('observacao','varchar',true,'600',false,false);
        $this->AddCampo('dt_emissao','date',true,'',false,false);
        $this->AddCampo('dt_vencimento','date',true,'',false,false);
        $this->AddCampo('tipo','varchar',true,'01',false,false);

    }

    /**
        * Monta a cláusula SQL
        * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
        * @access Public
        * @return String String contendo o SQL
    */

    public function montaRecuperaRelacionamento()
    {
        $stSql  = "
              select *
                from (
                      select cod_ordem
                           , exercicio
                           , cod_entidade
                           , dt_emissao
                           , dt_vencimento
                           , observacao
                           , entidade
                           , cod_recurso
                           , masc_recurso_red
                           , cod_detalhamento
                           , cgm_beneficiario
                           , implantado
                           , beneficiario
                           , coalesce(sum(coalesce(num_exercicio_empenho,0.00)),0.00) as num_exercicio_empenho
                           , exercicio_empenho
                           , '' as dt_estorno
                           , coalesce(sum(coalesce(valor_pagamento,0.00)),0.00) as valor_pagamento
                           , coalesce(sum(coalesce(valor_anulada,0.00)),0.00) as valor_anulada
                           , coalesce(sum(coalesce(saldo_pagamento,0.00)),0.00) as saldo_pagamento
                           , nota_empenho
                           , vl_nota
                           , vl_nota_anulacoes
                           , vl_nota_original
                           , CASE WHEN (sum(coalesce(saldo_pagamento,0.00))) < coalesce(vl_nota,0.00)
                                   and (coalesce(vl_nota,0.00) > 0.00 )
                                  then 'A Pagar'
                                  WHEN (coalesce(sum(coalesce(saldo_pagamento,0.00)),0.00) = coalesce(vl_nota,0.00))
                                   and (coalesce(vl_nota,0.00) > 0.00 )
                                  then 'Paga'
                                  WHEN (coalesce(vl_nota,0.00) = 0.00 )
                                  then 'Anulada'
                              end as situacao
                           , CASE WHEN coalesce(sum(coalesce(valor_anulada,0.00)),0.00) > 0.00
                                  then 'Sim'
                                  else 'Não'
                              end as pagamento_estornado
                        from (
                              select op.cod_ordem
                                   , op.exercicio
                                   , op.cod_entidade
                                   , to_char( op.dt_emissao   , 'dd/mm/yyyy' ) as dt_emissao
                                   , to_char( op.dt_vencimento, 'dd/mm/yyyy' ) as dt_vencimento
                                   , op.observacao
                                   , cgm.nom_cgm as entidade
                                   , 1 as num_exercicio_empenho
                                   , em.exercicio as exercicio_empenho
                                   , rec.masc_recurso_red
                                   , rec.cod_recurso
                                   , rec.cod_detalhamento
                                   , pe.cgm_beneficiario
                                   , pe.implantado
                                   , cgm_pe.nom_cgm as beneficiario
                                   , coalesce(nota_liq_paga.vl_pago    , 0.00) as valor_pagamento
                                   , coalesce(nota_liq_paga.vl_anulado, 0.00 ) as valor_anulada
                                   , coalesce(nota_liq_paga.saldo_pagamento, 0.00) as saldo_pagamento
                                   , nota_liq_paga.cod_nota
                                   , empenho.retorna_notas_empenhos(op.exercicio,op.cod_ordem, op.cod_entidade) as nota_empenho
                                   , coalesce(sum(coalesce(tot_op.total_op,0.00)),0.00) as vl_nota
                                   , coalesce(sum(coalesce(tot_op.anulacoes_op,0.00)),0.00) as vl_nota_anulacoes
                                   , coalesce(sum(coalesce(tot_op.vl_original_op,0.00)),0.00) as vl_nota_original
                                from empenho.ordem_pagamento as op
                           left join empenho.ordem_pagamento_anulada as opa
                                  on op.cod_ordem    = opa.cod_ordem
                                 and op.exercicio    = opa.exercicio
                                 and op.cod_entidade = opa.cod_entidade
        ";
        if ( $this->getDado( 'exercicio_op') ) {
            $stSql .= "         and op.exercicio = '".$this->getDado('exercicio_op')."'                                  \n";
        }
        $stSql .= "
                                join empenho.pagamento_liquidacao as pl
                                  on op.cod_ordem    = pl.cod_ordem
                                 and op.exercicio    = pl.exercicio
                                 and op.cod_entidade = pl.cod_entidade
        ";
        if ( $this->getDado( 'exercicio_op') ) {
            $stSql .= "         and op.exercicio = '".$this->getDado('exercicio_op')."'                                  \n";
        }
        $stSql .= "
                                join (
                                      select coalesce(sum(coalesce(pl.vl_pagamento,0.00)),0.00) - coalesce(opla.vl_anulado,0.00) as total_op
                                           , coalesce(opla.vl_anulado,0.00) as anulacoes_op
                                           , coalesce(sum(coalesce(pl.vl_pagamento,0.00)),0.00) as vl_original_op
                                           , pl.cod_ordem
                                           , pl.cod_entidade
                                           , pl.exercicio
                                        from empenho.pagamento_liquidacao as pl
                                   left join ( 
                                              select opla.cod_ordem
                                                   , opla.cod_entidade
                                                   , opla.exercicio
                                                   , opla.exercicio_liquidacao
                                                   , opla.cod_nota
                                                   , coalesce(sum(coalesce(opla.vl_anulado,0.00)), 0.00 ) as vl_anulado
                                                from empenho.ordem_pagamento_liquidacao_anulada as opla
                                            group by opla.cod_ordem
                                                   , opla.cod_entidade
                                                   , opla.exercicio
                                                   , opla.cod_nota
                                                   , opla.exercicio_liquidacao
                                             ) as opla
                                          on opla.cod_ordem    = pl.cod_ordem
                                         and opla.cod_entidade = pl.cod_entidade
                                         and opla.exercicio    = pl.exercicio
                                         and opla.exercicio_liquidacao = pl.exercicio_liquidacao
                                         and opla.cod_nota     = pl.cod_nota
                
                                       where pl.cod_ordem is not null
        ";
        if ( $this->getDado( 'exercicio_op') ) {
            $stSql .= "              and pl.exercicio = '".$this->getDado('exercicio_op')."'                                  \n";
        }
        $stSql .= "
                                    group by pl.cod_ordem
                                           , pl.cod_entidade
                                           , pl.exercicio
                                           , opla.vl_anulado
                                     ) as tot_op
                                  on tot_op.cod_ordem    = pl.cod_ordem
                                 and tot_op.exercicio    = pl.exercicio
                                 and tot_op.cod_entidade = pl.cod_entidade
                
                                join empenho.nota_liquidacao as nl
                                  on pl.cod_nota              = nl.cod_nota
                                 and pl.cod_entidade          = nl.cod_entidade
                                 and pl.exercicio_liquidacao  = nl.exercicio
        ";
        if ( $this->getDado('cod_nota') ) {
            $stSql .= "                                           and pl.cod_nota  between ". $this->getDado('cod_nota') ."                                \n";
        }
        if ( $this->getDado('cod_nota_inicial') ) {
            $stSql .= "                                           and pl.cod_nota >= ". $this->getDado('cod_nota_inicial') ."                              \n";
        }
        if ( $this->getDado('cod_nota_final') ) {
            $stSql .= "                                           and pl.cod_nota <= ". $this->getDado('cod_nota_final') ."                                \n";
        }
        $stSql .= "
                
                           left join (
                                      select nlp.cod_entidade
                                           , nlp.cod_nota
                                           , plnlp.cod_ordem
                                           , plnlp.exercicio
                                           , nlp.exercicio as exercicio_liquidacao
                                           , coalesce(sum(coalesce(nlp.vl_pago ,0.00)),0.00) as vl_pago
                                           , coalesce(sum(coalesce(nlpa.vl_anulado ,0.00)),0.00) as vl_anulado
                                           , coalesce(sum(coalesce(nlp.vl_pago,0.00) - coalesce(nlpa.vl_anulado,0.00)),0.00) as saldo_pagamento
                                        from empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp
                                           , empenho.nota_liquidacao_paga as nlp
                                   left join (
                                              SELECT exercicio
                                                   , cod_nota
                                                   , cod_entidade
                                                   , timestamp
                                                   , coalesce(sum(coalesce(nlpa.vl_anulado,0.00)),0.00) as vl_anulado
                                                FROM empenho.nota_liquidacao_paga_anulada as nlpa
                                            GROUP BY exercicio
                                                   , cod_nota
                                                   , cod_entidade
                                                   , timestamp
                                             ) as nlpa
                                          on nlp.exercicio    = nlpa.exercicio
                                         and nlp.cod_nota     = nlpa.cod_nota
                                         and nlp.cod_entidade = nlpa.cod_entidade
                                         and nlp.timestamp    = nlpa.timestamp
                
                                       where nlp.cod_entidade = plnlp.cod_entidade
                                         and nlp.cod_nota     = plnlp.cod_nota
                                         and nlp.exercicio    = plnlp.exercicio_liquidacao
                                         and nlp.timestamp    = plnlp.timestamp
        ";
        if ( $this->getDado( 'exercicio_op') ) {
            $stSql .= "                        and plnlp.exercicio = '".$this->getDado('exercicio_op')."'                                  \n";
        }
        $stSql .= "
                                    group by nlp.cod_entidade
                                           , nlp.cod_nota
                                           , nlp.exercicio
                                           , nlpa.vl_anulado
                                           , plnlp.cod_ordem
                                           , plnlp.exercicio
                                     ) as nota_liq_paga
                                  on pl.cod_nota     = nota_liq_paga.cod_nota
                                 and pl.cod_entidade = nota_liq_paga.cod_entidade
                                 and pl.exercicio    = nota_liq_paga.exercicio
                                 and pl.cod_ordem    = nota_liq_paga.cod_ordem
                                 and pl.exercicio_liquidacao = nota_liq_paga.exercicio_liquidacao
                
                                join empenho.empenho as em
                                  on nl.cod_empenho       = em.cod_empenho
                                 and nl.exercicio_empenho = em.exercicio
                                 and nl.cod_entidade      = em.cod_entidade
        ";
        if ( $this->getDado( 'cod_empenho') ) {
            $stSql .= "                                        and em.cod_empenho between ".$this->getDado('cod_empenho')."                                \n";
        }
        if ( $this->getDado( 'cod_empenho_inicial') ) {
            $stSql .= "                                        and em.cod_empenho >= ".$this->getDado('cod_empenho_inicial')."                             \n";
        }
        if ( $this->getDado( 'cod_empenho_final') ) {
            $stSql .= "                                        and em.cod_empenho <= ".$this->getDado('cod_empenho_final')."                               \n";
        }
        if ( $this->getDado( 'exercicio_empenho') ) {
            $stSql .= "                                        and em.exercicio = '".$this->getDado('exercicio_empenho')."'                                 \n";
        }
        $stSql .= "
                                join empenho.pre_empenho as pe
                                  on em.exercicio       = pe.exercicio
                                 and em.cod_pre_empenho = pe.cod_pre_empenho

        ";
        if ( $this->getDado( 'exercicio_empenho') ) {
            $stSql .= "                                            and em.exercicio = '".$this->getDado('exercicio_empenho')."'                             \n";
        }
        $stSql .= "
                
                                join sw_cgm as cgm_pe
                                  on pe.cgm_beneficiario = cgm_pe.numcgm
                         
                           left join empenho.pre_empenho_despesa as ped
                                  on pe.cod_pre_empenho = ped.cod_pre_empenho
                                 and pe.exercicio       = ped.exercicio
                
                           left join orcamento.despesa as de
                                  on ped.cod_despesa = de.cod_despesa
                                 and ped.exercicio   = de.exercicio
                
                           left join orcamento.recurso('".$this->getDado('exercicio_empenho')."') as rec
                                  on de.cod_recurso = rec.cod_recurso
                                 and de.exercicio   = rec.exercicio
                
                                join orcamento.entidade as en
                                  on op.cod_entidade = en.cod_entidade
                                 and op.exercicio    = en.exercicio
                
                                join sw_cgm as cgm
                                  on en.numcgm = cgm.numcgm
                            
                            group by op.cod_ordem
                                   , op.exercicio
                                   , op.cod_entidade
                                   , to_char(op.dt_emissao   , 'dd/mm/yyyy')
                                   , to_char(op.dt_vencimento, 'dd/mm/yyyy')
                                   , op.observacao
                                   , cgm.nom_cgm
                                   , num_exercicio_empenho
                                   , em.exercicio
                                   , de.cod_recurso
                                   , rec.masc_recurso_red
                                   , rec.cod_recurso
                                   , rec.cod_detalhamento
                                   , pe.cgm_beneficiario
                                   , pe.implantado
                                   , cgm_pe.nom_cgm
                                   , empenho.retorna_notas_empenhos(op.exercicio,op.cod_ordem, op.cod_entidade)
                                   , pl.exercicio_liquidacao
                                   , nota_liq_paga.cod_nota
                                   , nota_liq_paga.vl_pago
                                   , nota_liq_paga.vl_anulado
                                   , nota_liq_paga.saldo_pagamento
                             ) as tabela
                    group by cod_ordem
                           , exercicio
                           , cod_entidade
                           , dt_emissao
                           , dt_vencimento
                           , observacao
                           , entidade
                           , cod_recurso
                           , masc_recurso_red
                           , cod_detalhamento
                           , cgm_beneficiario
                           , implantado
                           , beneficiario
                           , dt_estorno
                           , nota_empenho
                           , exercicio_empenho
                           , vl_nota
                           , vl_nota_original
                           , vl_nota_anulacoes
                     ) as tbl
               where num_exercicio_empenho > 0
        ";

        return $stSql;
    }

    public function recuperaListaAnulacao(&$rsRecordSet, $stFiltro='', $stOrder='', $boTransacao ='')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if (!$stOrder) {
            $stOrder = " order by op.cod_entidade, op.cod_ordem ";
        }
        $this->setDado( 'stFiltro', $stFiltro );

        $stSql = $this->montaRecuperaListaAnulacao().$stOrder;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaAnulacao()
    {
        $stSql  = "select  op.exercicio \n";
        $stSql .= "       ,op.cod_entidade \n";
        $stSql .= "       ,op.cod_ordem \n";
        $stSql .= "       ,to_char(op.dt_emissao, 'dd/mm/yyyy' ) as dt_emissao \n";
        $stSql .= "       ,to_char(op.dt_vencimento, 'dd/mm/yyyy' ) as dt_vencimento \n";
        $stSql .= "       ,itensOP.cgm_beneficiario \n";
        $stSql .= "       ,itensOP.credor as beneficiario \n";
        $stSql .= "       ,coalesce(sum(itensOP.vl_pagamento),0.00) as valor_op \n";
        $stSql .= "       ,coalesce(sum(itensOP.vl_anulado),0.00) as valor_anulado \n";
        $stSql .= "       ,itensOP.credor as beneficiario \n";
        $stSql .= "       ,itensOP.implantado \n";
        $stSql .= "       ,'A Pagar' as situacao \n";
        $stSql .= "from empenho.ordem_pagamento as op \n";
        $stSql .= "     join ( \n";
        $stSql .= "            select  emp.exercicio as exercicio_empenho \n";
        $stSql .= "                   ,emp.cod_entidade \n";
        $stSql .= "                   ,emp.cod_empenho \n";
        $stSql .= "                   ,cgm.numcgm as cgm_beneficiario \n";
        $stSql .= "                   ,cgm.nom_cgm as credor \n";
        $stSql .= "                   ,pagamento.cod_ordem \n";
        $stSql .= "                   ,pagamento.exercicio \n";
        $stSql .= "                   ,pagamento.vl_pagamento \n";
        $stSql .= "                   ,pagamento.vl_anulado \n";
        $stSql .= "                   ,pre.implantado \n";
        $stSql .= "            from empenho.empenho as emp \n";
        $stSql .= "                 join empenho.pre_empenho as pre \n";
        $stSql .= "                      on ( \n";
        $stSql .= "                              pre.exercicio       = emp.exercicio \n";
        $stSql .= "                          and pre.cod_pre_empenho = emp.cod_pre_empenho \n";
        $stSql .= "                         ) \n";
        $stSql .= "                 join sw_cgm as cgm \n";
        $stSql .= "                      on ( cgm.numcgm = pre.cgm_beneficiario ) \n";
        $stSql .= "                 join empenho.nota_liquidacao as nl \n";
        $stSql .= "                      on (  \n";
        $stSql .= "                               nl.exercicio_empenho = emp.exercicio \n";
        $stSql .= "                           and nl.cod_empenho       = emp.cod_empenho \n";
        $stSql .= "                           and nl.cod_entidade      = emp.cod_entidade \n";
        $stSql .= "                         ) \n";
        $stSql .= "                 join ( \n";
        $stSql .= "                        select   pl.cod_entidade \n";
        $stSql .= "                                ,pl.exercicio_liquidacao \n";
        $stSql .= "                                ,pl.cod_nota \n";
        $stSql .= "                                ,pl.exercicio \n";
        $stSql .= "                                ,pl.cod_ordem \n";
        $stSql .= "                                ,coalesce(sum(pl.vl_pagamento),0.00) as vl_pagamento \n";
        $stSql .= "                                ,coalesce(sum(opla.vl_anulado),0.00) as vl_anulado \n";
        $stSql .= "                                ,coalesce(sum(pg_liquidacao.vl_pago   ), 0.00) as vl_pago \n";
        $stSql .= "                                ,coalesce(sum(pg_liquidacao.vl_anulado), 0.00) as vl_pago_anulado \n";
        $stSql .= "                        from empenho.pagamento_liquidacao as pl \n";
        $stSql .= "                             left join -- empenho.ordem_pagamento_liquidacao_anulada as opla \n";
        $stSql .= "                                       ( select  exercicio \n";
        $stSql .= "                                                ,cod_entidade \n";
        $stSql .= "                                                ,cod_ordem \n";
        $stSql .= "                                                ,exercicio_liquidacao \n";
        $stSql .= "                                                ,cod_nota \n";
        $stSql .= "                                                ,coalesce(sum(vl_anulado), 0.00) as vl_anulado \n";
        $stSql .= "                                         from empenho.ordem_pagamento_liquidacao_anulada as opla \n";
        $stSql .= "                                         group by exercicio \n";
        $stSql .= "                                                 ,cod_entidade \n";
        $stSql .= "                                                 ,cod_ordem \n";
        $stSql .= "                                                 ,exercicio_liquidacao \n";
        $stSql .= "                                                 ,cod_nota \n";
        $stSql .= "                                       ) as opla \n";
        $stSql .= "                                  on ( \n";
        $stSql .= "                                           opla.exercicio    = pl.exercicio  \n";
        $stSql .= "                                       and opla.cod_entidade = pl.cod_entidade \n";
        $stSql .= "                                       and opla.cod_ordem    = pl.cod_ordem \n";
        $stSql .= "                                       and opla.exercicio_liquidacao = pl.exercicio_liquidacao \n";
        $stSql .= "                                       and opla.cod_nota     = pl.cod_nota \n";
        $stSql .= "                                     ) \n";
        $stSql .= "                             left join ( \n";
        $stSql .= "                                       select  plnlp.exercicio                               \n";
        $stSql .= "                                              ,plnlp.cod_entidade                        \n";
        $stSql .= "                                              ,plnlp.cod_ordem                        \n";
        $stSql .= "                                              ,plnlp.exercicio_liquidacao                        \n";
        $stSql .= "                                              ,plnlp.cod_nota                        \n";
        $stSql .= "                                              ,coalesce( sum(nlp.vl_pago)    , 0.00 ) as vl_pago                        \n";
        $stSql .= "                                              ,coalesce( sum(nlpa.vl_anulado), 0.00 ) as vl_anulado                        \n";
        $stSql .= "                                       from empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp                        \n";
        $stSql .= "                                            LEFT JOIN ( SELECT exercicio                        \n";
        $stSql .= "                                                              ,cod_entidade                        \n";
        $stSql .= "                                                              ,cod_nota                        \n";
        $stSql .= "                                                              ,timestamp                        \n";
        $stSql .= "                                                              ,sum(vl_pago) as vl_pago                        \n";
        $stSql .= "                                                          FROM empenho.nota_liquidacao_paga as nlp                        \n";
        $stSql .= "                                                         WHERE cod_entidade in ( ".$this->getDado('cod_entidade').")           \n";
        $stSql .= "                                                      GROUP BY exercicio, cod_entidade, cod_nota, timestamp                        \n";
        $stSql .= "                                            ) as nlp ON (     nlp.exercicio =  plnlp.exercicio_liquidacao                        \n";
        $stSql .= "                                                          AND nlp.cod_entidade = plnlp.cod_entidade                        \n";
        $stSql .= "                                                          AND nlp.cod_nota = plnlp.cod_nota                        \n";
        $stSql .= "                                                          AND nlp.timestamp = plnlp.timestamp                        \n";
        $stSql .= "                                            )                        \n";
        $stSql .= "                                            LEFT JOIN ( SELECT exercicio                        \n";
        $stSql .= "                                                              ,cod_entidade                        \n";
        $stSql .= "                                                              ,cod_nota                        \n";
        $stSql .= "                                                              ,timestamp                        \n";
        $stSql .= "                                                              ,sum(vl_anulado) as vl_anulado                        \n";
        $stSql .= "                                                          FROM empenho.nota_liquidacao_paga_anulada as nlpa                        \n";
        $stSql .= "                                                         WHERE cod_entidade in (".$this->getDado('cod_entidade').")           \n";
        $stSql .= "                                                      GROUP BY exercicio, cod_entidade, cod_nota, timestamp                        \n";
        $stSql .= "                                            ) as nlpa ON (    nlpa.exercicio    = nlp.exercicio                        \n";
        $stSql .= "                                                          AND nlpa.cod_entidade = nlp.cod_entidade                        \n";
        $stSql .= "                                                          AND nlpa.cod_nota     = nlp.cod_nota                        \n";
        $stSql .= "                                                          AND nlpa.timestamp    = nlp.timestamp                        \n";
        $stSql .= "                                            )                        \n";
        $stSql .= "                                   group by    plnlp.exercicio                        \n";
        $stSql .= "                                              ,plnlp.cod_entidade                        \n";
        $stSql .= "                                              ,plnlp.cod_ordem                        \n";
        $stSql .= "                                              ,plnlp.exercicio_liquidacao                        \n";
        $stSql .= "                                              ,plnlp.cod_nota                        \n";
        $stSql .= "                                       ) as pg_liquidacao \n";
        $stSql .= "                                         on (      pg_liquidacao.exercicio_liquidacao = pl.exercicio_liquidacao \n";
        $stSql .= "                                               and pg_liquidacao.cod_entidade = pl.cod_entidade \n";
        $stSql .= "                                               and pg_liquidacao.cod_nota     = pl.cod_nota \n";
        $stSql .= "                                               and pg_liquidacao.cod_ordem    = pl.cod_ordem \n";
        $stSql .= "                                               and pg_liquidacao.exercicio    = pl.exercicio \n";
        $stSql .= "                                            ) \n";
        $stSql .= "                        group by  pl.cod_entidade \n";
        $stSql .= "                                 ,pl.exercicio_liquidacao \n";
        $stSql .= "                                 ,pl.cod_nota \n";
        $stSql .= "                                 ,pl.exercicio \n";
        $stSql .= "                                 ,pl.cod_ordem \n";
        $stSql .= "                      ) as pagamento   \n";
        $stSql .= "                        on ( \n";
        $stSql .= "                                 pagamento.cod_nota = nl.cod_nota \n";
        $stSql .= "                             and pagamento.exercicio_liquidacao = nl.exercicio \n";
        $stSql .= "                             and pagamento.cod_entidade = nl.cod_entidade \n";
        $stSql .= "                           ) \n";
        $stSql .= "            where nl.exercicio_empenho = '".$this->getDado('exercicio_empenho')."' \n";
        $stSql .= "                  and pagamento.cod_entidade in (".$this->getDado('cod_entidade').") \n";
        $stSql .= "                  /* PAGAMENTO */ \n";
        $stSql .= "                  and (pagamento.vl_pagamento - pagamento.vl_anulado) > (pagamento.vl_pago - pagamento.vl_pago_anulado) \n";
        $stSql .= "          ) as itensOP \n";
        $stSql .= "            on ( \n";
        $stSql .= "                    itensOP.cod_entidade = op.cod_entidade \n";
        $stSql .= "                and itensOP.exercicio    = op.exercicio \n";
        $stSql .= "                and itensOP.cod_ordem    = op.cod_ordem \n";
        $stSql .= "               ) \n";
        $stSql .= " ". $this->getDado( 'stFiltro' ) ." \n";
        $stSql .= "group by  op.exercicio \n";
        $stSql .= "         ,op.cod_entidade \n";
        $stSql .= "         ,op.cod_ordem \n";
        $stSql .= "         ,op.dt_emissao \n";
        $stSql .= "         ,op.dt_vencimento \n";
        $stSql .= "         ,op.observacao \n";
        $stSql .= "         ,itensOP.cgm_beneficiario \n";
        $stSql .= "         ,itensOP.credor \n";
        $stSql .= "         ,situacao \n";
        $stSql .= "       ,itensOP.implantado \n";

        return $stSql;
    }

    public function montaRecuperaUltimoEstorno()
    {
        $stSql  = "select  pl.cod_ordem                                                             \n";
        $stSql .= "       ,pl.exercicio                                                             \n";
        $stSql .= "       ,pl.cod_entidade                                                          \n";
        $stSql .= "       ,to_char(nlpa.timestamp_anulada,'dd/mm/yyyy') as dt_estorno               \n";
        $stSql .= "from  empenho.nota_liquidacao_paga_anulada as nlpa                               \n";
        $stSql .= "     ,empenho.nota_liquidacao_paga as nlp                                        \n";
        $stSql .= "     ,empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp                 \n";
        $stSql .= "     ,empenho.pagamento_liquidacao as pl                                         \n";
        $stSql .= "     ,empenho.nota_liquidacao      as nl                                         \n";
        $stSql .= "where     nlpa.timestamp    = nlp.timestamp                                      \n";
        $stSql .= "      and nlpa.exercicio    = nlp.exercicio                                      \n";
        $stSql .= "      and nlpa.cod_nota     = nlp.cod_nota                                       \n";
        $stSql .= "      and nlpa.cod_entidade = nlp. cod_entidade                                  \n";
        $stSql .= "      and plnlp.cod_nota     = nlp.cod_nota                                      \n";
        $stSql .= "      and plnlp.exercicio_liquidacao = nlp.exercicio                             \n";
        $stSql .= "      and plnlp.cod_entidade = nlp.cod_entidade                                  \n";
        $stSql .= "      and plnlp.timestamp    = nlp.timestamp                                     \n";
        $stSql .= "      and pl.cod_ordem    = plnlp.cod_ordem                                      \n";
        $stSql .= "      and pl.exercicio    = plnlp.exercicio                                      \n";
        $stSql .= "      and pl.cod_entidade = plnlp.cod_entidade                                   \n";
        $stSql .= "      and pl.exercicio_liquidacao = plnlp.exercicio_liquidacao                   \n";
        $stSql .= "      and pl.cod_nota     = plnlp.cod_nota                                       \n";
        $stSql .= "      and nl.exercicio    = pl.exercicio_liquidacao                              \n";
        $stSql .= "      and nl.cod_nota     = pl.cod_nota                                          \n";
        $stSql .= "      and nl.cod_entidade = pl.cod_entidade                                      \n";
        $stSql .= "      and pl.cod_ordem    =  ". $this->getDado('cod_ordem')    ."                \n";
        $stSql .= "      and pl.exercicio    = '". $this->getDado('exercicio')    ."'               \n";
        $stSql .= "      and pl.cod_entidade =  ". $this->getDado('cod_entidade') ."                \n";
        $stSql .= "group by  pl.cod_ordem                                                           \n";
        $stSql .= "         ,pl.exercicio                                                           \n";
        $stSql .= "         ,pl.cod_entidade                                                        \n";
        $stSql .= "         ,nlpa.timestamp_anulada                                                 \n";
        $stSql .= "order by nlpa.timestamp_anulada desc limit 1                                     \n";

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaUltimoEstorno(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaUltimoEstorno();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaUltimoPagamento()
    {
        $stSql  = "select  pl.cod_ordem                                                      \n";
        $stSql .= "       ,pl.exercicio                                                      \n";
        $stSql .= "       ,pl.cod_entidade                                                   \n";
        $stSql .= "       ,to_char(nlp.timestamp,'dd/mm/yyyy') as dt_pagamento               \n";
        $stSql .= "                                                                          \n";
        $stSql .= "from  empenho.nota_liquidacao_paga         as nlp                         \n";
        $stSql .= "      left join empenho.nota_liquidacao_paga_anulada as nlpa on (         \n";
        $stSql .= "            nlpa.timestamp    = nlp.timestamp                             \n";
        $stSql .= "        and nlpa.exercicio    = nlp.exercicio                             \n";
        $stSql .= "        and nlpa.cod_nota     = nlp.cod_nota                              \n";
        $stSql .= "        and nlpa.cod_entidade = nlp. cod_entidade                         \n";
        $stSql .= "      )                                                                   \n";
        $stSql .= "     ,empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp          \n";
        $stSql .= "     ,empenho.pagamento_liquidacao as pl                                  \n";
        $stSql .= "     ,empenho.nota_liquidacao      as nl                                  \n";
        $stSql .= "                                                                          \n";
        $stSql .= "WHERE     nlpa.cod_nota is null                                           \n";
        $stSql .= "      and plnlp.cod_nota     = nlp.cod_nota                               \n";
        $stSql .= "      and plnlp.exercicio_liquidacao = nlp.exercicio                      \n";
        $stSql .= "      and plnlp.cod_entidade = nlp.cod_entidade                           \n";
        $stSql .= "      and plnlp.timestamp    = nlp.timestamp                              \n";
        $stSql .= "                                                                          \n";
        $stSql .= "      and pl.cod_ordem    = plnlp.cod_ordem                               \n";
        $stSql .= "      and pl.exercicio    = plnlp.exercicio                               \n";
        $stSql .= "      and pl.cod_entidade = plnlp.cod_entidade                            \n";
        $stSql .= "      and pl.exercicio_liquidacao = plnlp.exercicio_liquidacao            \n";
        $stSql .= "      and pl.cod_nota     = plnlp.cod_nota                                \n";
        $stSql .= "                                                                          \n";
        $stSql .= "      and nl.exercicio    = pl.exercicio_liquidacao                       \n";
        $stSql .= "      and nl.cod_nota     = pl.cod_nota                                   \n";
        $stSql .= "      and nl.cod_entidade = pl.cod_entidade                               \n";
        $stSql .= "                                                                          \n";
        $stSql .= "      and pl.cod_ordem    =  ". $this->getDado('cod_ordem')    ."         \n";
        $stSql .= "      and pl.exercicio    = '". $this->getDado('exercicio')    ."'        \n";
        $stSql .= "      and pl.cod_entidade =  ". $this->getDado('cod_entidade') ."         \n";
        $stSql .= "group by  pl.cod_ordem                                                    \n";
        $stSql .= "         ,pl.exercicio                                                    \n";
        $stSql .= "         ,pl.cod_entidade                                                 \n";
        $stSql .= "         ,nlp.timestamp                                                   \n";
        $stSql .= "                                                                          \n";
        $stSql .= "order by nlp.timestamp desc                                               \n";
        $stSql .= "limit 1                                                                   \n";

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaUltimoPagamento(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaUltimoPagamento();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMaiorDataOrdem()
    {
        $stSql  = "SELECT                                                                                       \n";
        $stSql .= "    CASE WHEN to_date('".$this->getDado("stDataLiquidacao")."','dd/mm/yyyy') < to_date('01/01/".$this->getDado("stExercicio")."','dd/mm/yyyy') THEN   \n";
        $stSql .= "        CASE WHEN max(dt_emissao) < to_date('01/01/".$this->getDado("stExercicio")."','dd/mm/yyyy') THEN     \n";
        $stSql .= "            '01/01/".$this->getDado("stExercicio")."'                                        \n";
        $stSql .= "        ELSE                                                                                 \n";
        $stSql .= "            to_char(max(dt_emissao),'dd/mm/yyyy')                                            \n";
        $stSql .= "        END                                                                                  \n";
        $stSql .= "    ELSE                                                                                     \n";
        $stSql .= "        CASE WHEN max(dt_emissao) < to_date('01/01/".$this->getDado("stExercicio")."','dd/mm/yyyy') THEN     \n";
        $stSql .= "            '".$this->getDado("stDataLiquidacao")."'                                         \n";
        $stSql .= "        ELSE                                                                                 \n";
        $stSql .= "            CASE WHEN max(dt_emissao) < to_date('".$this->getDado("stDataLiquidacao")."','dd/mm/yyyy') THEN  \n";
        $stSql .= "                '".$this->getDado("stDataLiquidacao")."'                                     \n";
        $stSql .= "            ELSE                                                                             \n";
        $stSql .= "                to_char(max(dt_emissao),'dd/mm/yyyy')                                        \n";
        $stSql .= "            END                                                                              \n";
        $stSql .= "        END                                                                                  \n";
        $stSql .= "    END AS data_ordem                                                                        \n";
        $stSql .= "FROM                                                                                         \n";
        $stSql .= "    empenho.ordem_pagamento                                                                  \n";

        return $stSql;

    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaMaiorDataOrdem(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaMaiorDataOrdem().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /**
        * Monta a cláusula SQL
        * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
        * @access Public
        * @return String String contendo o SQL
    */
    public function montaRecuperaListaPagamento()
    {
        $stSql  = " select  op.cod_ordem   \n";
        $stSql .= "  \n";
        $stSql .= "        ,op.cod_entidade  \n";
        $stSql .= "        ,op.exercicio  \n";
        $stSql .= "        ,to_char(op.dt_vencimento, 'dd/mm/yyyy') as dt_vencimento  \n";
        $stSql .= "        ,replace(empenho.retorna_notas_empenhos(op.exercicio,op.cod_ordem,op.cod_entidade),'\n','<br>') as nota_empenho   \n";
        $stSql .= "        ,empenho.verifica_adiantamento(op.exercicio,op.cod_ordem,op.cod_entidade) as adiantamento   \n";
        $stSql .= "        ,coalesce(top.total_op , 0.00) as total_op   \n";
        $stSql .= "        ,coalesce(plnlp.vl_pago, 0.00) as total_pago   \n";
        $stSql .= "        ,pl.cgm_beneficiario  \n";
        $stSql .= "        ,pl.beneficiario  \n";
        $stSql .= "        ,cgm_ent.nom_cgm as entidade \n";
        $stSql .= "        ,coalesce( coalesce(top.total_op,0.00) - coalesce(plnlp.vl_pago,0.00), 0.00 ) as valor_pagamento  \n";
        $stSql .= " from empenho.ordem_pagamento as op   \n";
        $stSql .= "  \n";
        $stSql .= "      join (   \n";

        $stSql .= "      select  pl.exercicio \n";
        $stSql .= "              ,pl.cod_entidade \n";
        $stSql .= "              ,pl.cod_ordem \n";
        $stSql .= "              ,coalesce( (coalesce(sum(vl_pagamento), 0.00) - coalesce(sum(opla.vl_anulado),0.00)) , 0.00 ) as total_op \n";
        $stSql .= "              ,coalesce(sum(vl_pagamento), 0.00) as op, \n";
        $stSql .= "               coalesce(sum(opla.vl_anulado),0.00) as anulado \n";
        $stSql .= "       from empenho.pagamento_liquidacao as pl \n";
        $stSql .= "            left join ( \n";
        $stSql .= "                   select  opla.cod_ordem \n";
        $stSql .= "                          ,opla.cod_entidade \n";
        $stSql .= "                          ,opla.exercicio \n";
        $stSql .= "                          ,opla.cod_nota \n";
        $stSql .= "                          ,opla.exercicio_liquidacao \n";
        $stSql .= "                          ,coalesce(sum(opla.vl_anulado),0.00) as vl_anulado \n";
        $stSql .= "                   from empenho.ordem_pagamento_liquidacao_anulada as opla \n";
        $stSql .= "                   group by  opla.cod_ordem \n";
        $stSql .= "                            ,opla.cod_entidade \n";
        $stSql .= "                            ,opla.exercicio \n";
        $stSql .= "                            ,opla.cod_nota \n";
        $stSql .= "                            ,opla.exercicio_liquidacao \n";
        $stSql .= "                 ) as opla \n";
        $stSql .= "                 on (     opla.cod_ordem    = pl.cod_ordem \n";
        $stSql .= "                      and opla.exercicio    = pl.exercicio \n";
        $stSql .= "                      and opla.cod_entidade = pl.cod_entidade \n";
        $stSql .= "                      and opla.cod_nota     = pl.cod_nota \n";
        $stSql .= "                      and opla.exercicio_liquidacao = pl.exercicio_liquidacao \n";
        $stSql .= "                    ) \n";
        $stSql .= "           group by  pl.exercicio   \n";
        $stSql .= "                    ,pl.cod_entidade   \n";
        $stSql .= "                    ,pl.cod_ordem   \n";
        $stSql .= "         ) as top   \n";
        $stSql .= "         on (     top.exercicio    = op.exercicio   \n";
        $stSql .= "              and top.cod_entidade = op.cod_entidade   \n";
        $stSql .= "              and top.cod_ordem    = op.cod_ordem   \n";
        $stSql .= "            )   \n";
        $stSql .= "             \n";
        $stSql .= "      join ( \n";
        $stSql .= "         select \n";
        $stSql .= "              pl.cod_ordem \n";
        $stSql .= "             ,pl.exercicio \n";
        $stSql .= "             ,pl.cod_entidade \n";
        $stSql .= "             ,pe.cgm_beneficiario \n";
        $stSql .= "             ,cgm_ben.nom_cgm as beneficiario \n";
        $stSql .= "             ,em.exercicio as exercicio_empenho \n";
        $stSql .= "         from     \n";
        $stSql .= "             empenho.pagamento_liquidacao as pl \n";
        $stSql .= "      join empenho.nota_liquidacao as nl   \n";
        $stSql .= "           on (     nl.exercicio    = pl.exercicio_liquidacao   \n";
        $stSql .= "                and nl.cod_entidade = pl.cod_entidade   \n";
        $stSql .= "                and nl.cod_nota     = pl.cod_nota   \n";
        $stSql .= "              )   \n";
        $stSql .= "      join empenho.empenho as em   \n";
        $stSql .= "           on (     em.cod_empenho  = nl.cod_empenho   \n";
        $stSql .= "                and em.cod_entidade = nl.cod_entidade   \n";
        $stSql .= "                and em.exercicio    = nl.exercicio_empenho   \n";
        $stSql .= "              )   \n";
        $stSql .= "      join empenho.pre_empenho as pe   \n";
        $stSql .= "           on (     pe.cod_pre_empenho =  em.cod_pre_empenho   \n";
        $stSql .= "                and pe.exercicio       =  em.exercicio   \n";
        $stSql .= "              )   \n";
        $stSql .= "      join sw_cgm as cgm_ben  \n";
        $stSql .= "           on ( cgm_ben.numcgm = pe.cgm_beneficiario )  \n";
        $stSql .= "  \n";
        $stSql .= " where 1=1 \n";
        if ($this->getDado("cod_empenho")) {
            $stSql .= " and em.cod_empenho = " . $this->getDado("cod_empenho"). "\n";
        }
        if ($this->getDado("exercicio_empenho")) {
            $stSql .= " and em.exercicio = '" . $this->getDado("exercicio_empenho"). "'\n";
        }
        if ($this->getDado("cod_nota")) {
            $stSql .= " and nl.cod_nota = " . $this->getDado("cod_nota"). "\n";
        }
        if ($this->getDado("cgm_beneficiario")) {
            $stSql .= " and pe.cgm_beneficiario = " . $this->getDado("cgm_beneficiario"). "\n";
        }
        $stSql .= "         group by       \n";
        $stSql .= "             pl.exercicio   \n";
        $stSql .= "             ,pl.cod_entidade   \n";
        $stSql .= "             ,pl.cod_ordem   \n";
        $stSql .= "             ,pe.cgm_beneficiario \n";
        $stSql .= "             ,cgm_ben.nom_cgm \n";
        $stSql .= "             ,em.exercicio \n";
        $stSql .= "         )    as pl   \n";
        $stSql .= "           on (     pl.exercicio    = op.exercicio   \n";
        $stSql .= "                and pl.cod_entidade = op.cod_entidade   \n";
        $stSql .= "                and pl.cod_ordem    = op.cod_ordem   \n";
        $stSql .= "              )   \n";
        $stSql .= "      left join   \n";
        $stSql .= "           (   \n";
        $stSql .= "             select plnlp.exercicio   \n";
        $stSql .= "                   ,plnlp.cod_entidade   \n";
        $stSql .= "                   ,plnlp.cod_ordem   \n";
        $stSql .= "                   ,coalesce(sum(tnlp.vl_pago),0.00) as vl_pago   \n";
        $stSql .= "             from empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp   \n";
        $stSql .= "             left join (   \n";
        $stSql .= "                        select  nlp.exercicio   \n";
        $stSql .= "                               ,nlp.cod_entidade   \n";
        $stSql .= "                               ,nlp.cod_nota   \n";
        $stSql .= "                               ,nlp.timestamp   \n";
        $stSql .= "                               ,coalesce( (coalesce(sum(vl_pago), 0.00)-coalesce(sum(vl_pago_anulado),0.00)), 0.00 ) as vl_pago   \n";
        $stSql .= "                        from empenho.nota_liquidacao_paga as nlp   \n";
        $stSql .= "                             left join (   \n";
        $stSql .= "                                        select  nlpa.exercicio   \n";
        $stSql .= "                                               ,nlpa.cod_nota   \n";
        $stSql .= "                                               ,nlpa.cod_entidade   \n";
        $stSql .= "                                               ,nlpa.timestamp   \n";
        $stSql .= "                                               ,coalesce(sum(vl_anulado),0.00) as vl_pago_anulado   \n";
        $stSql .= "                                        from empenho.nota_liquidacao_paga_anulada as nlpa   \n";
        $stSql .= "                                        group by  nlpa.exercicio   \n";
        $stSql .= "                                                 ,nlpa.cod_nota   \n";
        $stSql .= "                                                 ,nlpa.cod_entidade   \n";
        $stSql .= "                                                 ,nlpa.timestamp   \n";
        $stSql .= "                                       ) as nlpa   \n";
        $stSql .= "                                       on (     nlpa.exercicio    = nlp.exercicio   \n";
        $stSql .= "                                            and nlpa.cod_nota     = nlp.cod_nota   \n";
        $stSql .= "                                            and nlpa.cod_entidade = nlp.cod_entidade   \n";
        $stSql .= "                                            and nlpa.timestamp    = nlp.timestamp   \n";
        $stSql .= "                                          )   \n";
        $stSql .= "                        group by  nlp.exercicio   \n";
        $stSql .= "                                 ,nlp.cod_entidade   \n";
        $stSql .= "                                 ,nlp.cod_nota   \n";
        $stSql .= "                                 ,nlp.timestamp   \n";
        $stSql .= "                      ) as tnlp   \n";
        $stSql .= "                      on (     tnlp.exercicio    = plnlp.exercicio_liquidacao   \n";
        $stSql .= "                           and tnlp.cod_nota     = plnlp.cod_nota   \n";
        $stSql .= "                           and tnlp.cod_entidade = plnlp.cod_entidade   \n";
        $stSql .= "                           and tnlp.timestamp    = plnlp.timestamp  \n";
        $stSql .= "                         )   \n";
        $stSql .= "                         \n";
        $stSql .= "             group by plnlp.exercicio   \n";
        $stSql .= "                     ,plnlp.cod_entidade   \n";
        $stSql .= "                     ,plnlp.cod_ordem   \n";
        $stSql .= "  \n";
        $stSql .= "  \n";
        $stSql .= "           ) as plnlp   \n";
        $stSql .= "      \n";
        $stSql .= "           on (     plnlp.exercicio    = pl.exercicio   \n";
        $stSql .= "                and plnlp.cod_entidade = pl.cod_entidade   \n";
        $stSql .= "                and plnlp.cod_ordem    = pl.cod_ordem   \n";
        $stSql .= "              )   \n";
        $stSql .= "      join orcamento.entidade as oe  \n";
        $stSql .= "           on (     oe.cod_entidade = op.cod_entidade  \n";
        $stSql .= "                and oe.exercicio    = op.exercicio  \n";
        $stSql .= "              )  \n";
        $stSql .= "      join sw_cgm as cgm_ent  \n";
        $stSql .= "           on ( cgm_ent.numcgm = oe.numcgm )  \n";

        return $stSql;
    }

    /**
        * Retorna Dados para a lista de Pagamento de OP's
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaListaPagamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaListaPagamento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    /**
        * Monta a cláusula SQL
        * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
        * @access Public
        * @return String String contendo o SQL
    */
    public function montaRecuperaDadosPagamento()
    {
        $stSql  = "                                                                              ";
        $stSql .= "        SELECT                                                              \n";
        $stSql .= "            EOP.COD_ORDEM,                                                  \n";
        $stSql .= "            EMP.EXERCICIO_EMPENHO,                                          \n";
        $stSql .= "            EOP.EXERCICIO,                                                  \n";
        $stSql .= "            EOP.COD_ENTIDADE,                                               \n";
        $stSql .= "            TO_CHAR(EOP.DT_VENCIMENTO, 'dd/mm/yyyy') AS DT_VENCIMENTO,      \n";
        $stSql .= "            TO_CHAR(EOP.DT_EMISSAO, 'dd/mm/yyyy') AS DT_EMISSAO,            \n";
        $stSql .= "            CGME.NOM_CGM AS ENTIDADE,                                       \n";
        $stSql .= "            EMP.NOM_CGM AS BENEFICIARIO ,                                    \n";
        $stSql .= "            EMPENHO.fn_consultar_valor_pagamento_ordem(eop.EXERCICIO,eop.COD_ORDEM,eop.COD_ENTIDADE) AS VALOR_PAGAMENTO, \n";
        $stSql .= "            EMP.CGM_BENEFICIARIO,                                           \n";
        $stSql .= "            replace(empenho.retorna_notas_empenhos(eop.exercicio,eop.cod_ordem,eop.cod_entidade),'\n','<br>') as nota_empenho,    \n";
        $stSql .= "            EMP.implantado                                                  \n";
        $stSql .= "       FROM                                                                 \n";
        $stSql .= "            EMPENHO.ORDEM_PAGAMENTO AS EOP                                  \n";
        $stSql .= "        LEFT JOIN                                                           \n";
        $stSql .= "            EMPENHO.ORDEM_PAGAMENTO_ANULADA AS EOPA ON                      \n";
        $stSql .= "                EOP.COD_ORDEM    = EOPA.COD_ORDEM AND                       \n";
        $stSql .= "                EOP.EXERCICIO    = EOPA.EXERCICIO AND                       \n";
        $stSql .= "                EOP.COD_ENTIDADE = EOPA.COD_ENTIDADE                        \n";
        $stSql .= "        LEFT JOIN                                                            \n";
        $stSql .= "            (                                                                \n";
        $stSql .= "            SELECT                                                           \n";
        $stSql .= "                PL.COD_ORDEM,                                                \n";
        $stSql .= "                PL.EXERCICIO,                                                \n";
        $stSql .= "                PL.COD_ENTIDADE,                                             \n";
        $stSql .= "                CGM.NOM_CGM,                                                 \n";
        $stSql .= "                PE.CGM_BENEFICIARIO,                                         \n";
        $stSql .= "                PE.IMPLANTADO,                                               \n";
        $stSql .= "                NL.EXERCICIO_EMPENHO,                                        \n";
        $stSql .= "                NL.COD_EMPENHO,                                              \n";
        $stSql .= "                NL.COD_NOTA                                                  \n";
        $stSql .= "            FROM                                                             \n";
        $stSql .= "                EMPENHO.PAGAMENTO_LIQUIDACAO    as PL,                       \n";
        $stSql .= "                EMPENHO.NOTA_LIQUIDACAO         as NL,                       \n";
        $stSql .= "                EMPENHO.EMPENHO                 as E,                        \n";
        $stSql .= "                EMPENHO.PRE_EMPENHO             as PE,                       \n";
        $stSql .= "                SW_CGM                          as CGM                       \n";
        $stSql .= "            WHERE                                                            \n";
        $stSql .= "                PL.COD_NOTA             = NL.COD_NOTA       AND              \n";
        $stSql .= "                PL.EXERCICIO_LIQUIDACAO = NL.EXERCICIO      AND              \n";
        $stSql .= "                PL.COD_ENTIDADE         = NL.COD_ENTIDADE   AND              \n";
        $stSql .= "                                                                             \n";
        $stSql .= "                NL.COD_EMPENHO          = E.COD_EMPENHO     AND              \n";
        $stSql .= "                NL.EXERCICIO_EMPENHO    = E.EXERCICIO       AND              \n";
        $stSql .= "                NL.COD_ENTIDADE         = E.COD_ENTIDADE    AND              \n";
        $stSql .= "                                                                             \n";
        $stSql .= "                E.COD_PRE_EMPENHO       = PE.COD_PRE_EMPENHO    AND          \n";
        $stSql .= "                E.EXERCICIO             = PE.EXERCICIO          AND          \n";
        $stSql .= "                                                                             \n";
        $stSql .= "                PE.CGM_BENEFICIARIO     = CGM.NUMCGM                         \n";
        $stSql .= "        ) AS EMP ON (                                                        \n";
        $stSql .= "            EOP.COD_ORDEM       = EMP.COD_ORDEM AND                          \n";
        $stSql .= "            EOP.EXERCICIO       = EMP.EXERCICIO AND                          \n";
        $stSql .= "            EOP.COD_ENTIDADE    = EMP.COD_ENTIDADE                           \n";
        $stSql .= "        )                                                                    \n";
        $stSql .= "        LEFT JOIN                                                           \n";
        $stSql .= "            ORCAMENTO.ENTIDADE AS OE                                        \n";
        $stSql .= "        ON                                                                  \n";
        $stSql .= "          ( OE.COD_ENTIDADE = EOP.COD_ENTIDADE                              \n";
        $stSql .= "        AND OE.EXERCICIO    = EOP.EXERCICIO    )                            \n";
        $stSql .= "        LEFT JOIN                                                           \n";
        $stSql .= "            SW_CGM AS CGME                                                  \n";
        $stSql .= "        ON                                                                  \n";
        $stSql .= "            CGME.NUMCGM = OE.NUMCGM                                         \n";
        $stSql .= "        LEFT JOIN (                                                                      \n";
        $stSql .= "            select                                                                       \n";
        $stSql .= "                plnlp.cod_ordem,                                                         \n";
        $stSql .= "                plnlp.exercicio,                                                         \n";
        $stSql .= "                plnlp.cod_entidade                                                       \n";
        $stSql .= "            from                                                                         \n";
        $stSql .= "                empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp               \n";
        $stSql .= "                    LEFT OUTER JOIN empenho.nota_liquidacao_paga_anulada as nlpa ON      \n";
        $stSql .= "                        plnlp.cod_entidade          = nlpa.cod_entidade AND              \n";
        $stSql .= "                        plnlp.cod_nota              = nlpa.cod_nota     AND              \n";
        $stSql .= "                        plnlp.exercicio_liquidacao  = nlpa.exercicio    AND              \n";
        $stSql .= "                        plnlp.timestamp             = nlpa.timestamp                     \n";
        $stSql .= "            WHERE                                                                        \n";
        $stSql .= "                nlpa.cod_nota is null ".$this->getDado("stFiltro").") as PLNLP           \n";
        $stSql .= "        ON (                                                                             \n";
        $stSql .= "            EOP.cod_ordem    = PLNLP.cod_ordem   AND                                     \n";
        $stSql .= "            EOP.exercicio    = PLNLP.exercicio   AND                                     \n";
        $stSql .= "            EOP.cod_entidade = PLNLP.cod_entidade                                        \n";
        $stSql .= "        )                                                                                \n";

        return $stSql;
    }

    /**
        * Retorna Dados para a lista de Estorno de Pagamento de OP's
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaListaEstorno(&$rsRecordSet, $stCondicao = "" , $stOrdem = " op.cod_ordem, op.exercicio " , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stGroup  = " \n";
        $stGroup .= "group by op.cod_entidade \n";
        $stGroup .= "        ,op.cod_ordem \n";
        $stGroup .= "        ,op.exercicio \n";
        $stGroup .= "        ,tpl.vl_op \n";
        $stGroup .= "        ,itens.vl_prestado \n";
        $stGroup .= "        ,pe.cgm_beneficiario \n";
        $stGroup .= "        ,ent.nom_cgm \n";
        $stGroup .= "        ,ben.nom_cgm \n";

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaListaEstorno().$stCondicao.$stGroup.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaEstorno()
    {
        $stSql  = "select  op.cod_entidade \n";
        $stSql .= "       ,op.cod_ordem \n";
        $stSql .= "       ,op.exercicio \n";
        $stSql .= "       ,tpl.vl_op \n";
        $stSql .= "       ,coalesce(itens.vl_prestado,0.00) as vl_prestado \n";
        $stSql .= "       ,replace(empenho.retorna_notas_empenhos(op.exercicio,op.cod_ordem,op.cod_entidade),'\n','<br>') as nota_empenho \n";
        $stSql .= "       ,pe.cgm_beneficiario \n";
        $stSql .= "       ,ent.nom_cgm as entidade \n";
        $stSql .= "       ,ben.nom_cgm as beneficiario \n";
        $stSql .= " \n";
        $stSql .= "from empenho.ordem_pagamento as op \n";
        $stSql .= "join ( \n";
        $stSql .= "        select  pl.cod_ordem \n";
        $stSql .= "               ,pl.exercicio \n";
        $stSql .= "               ,pl.cod_entidade \n";
        $stSql .= "               ,coalesce(coalesce(sum(pl.vl_pagamento),0.00) - coalesce(sum(opla.vl_anulado),0.00 ), 0.00) as vl_op \n";
        $stSql .= "        from empenho.pagamento_liquidacao as pl \n";
        $stSql .= "             left join ( select  opla.cod_ordem \n";
        $stSql .= "                           ,opla.exercicio \n";
        $stSql .= "                           ,opla.cod_entidade \n";
        $stSql .= "                           ,coalesce( sum(opla.vl_anulado), 0.00 ) as vl_anulado \n";
        $stSql .= "                    from empenho.ordem_pagamento_liquidacao_anulada as opla \n";
        $stSql .= "                    group by opla.cod_ordem \n";
        $stSql .= "                            ,opla.exercicio \n";
        $stSql .= "                            ,opla.cod_entidade \n";
        $stSql .= "                  ) as opla \n";
        $stSql .= "                  on(     opla.cod_ordem    = pl.cod_ordem \n";
        $stSql .= "                      and opla.exercicio    = pl.exercicio \n";
        $stSql .= "                      and opla.cod_entidade = pl.cod_entidade \n";
        $stSql .= "                    ) \n";
        $stSql .= "        group by  pl.cod_ordem \n";
        $stSql .= "                 ,pl.exercicio \n";
        $stSql .= "                 ,pl.cod_entidade \n";
        $stSql .= " \n";
        $stSql .= "     ) as tpl \n";
        $stSql .= "     on (     tpl.cod_ordem    = op.cod_ordem \n";
        $stSql .= "          and tpl.exercicio    = op.exercicio \n";
        $stSql .= "          and tpl.cod_entidade = op.cod_entidade \n";
        $stSql .= "        ) \n";
        $stSql .= " \n";
        $stSql .= "join ( \n";
        $stSql .= "        select  plnlp.cod_ordem \n";
        $stSql .= "               ,plnlp.exercicio \n";
        $stSql .= "               ,plnlp.cod_entidade \n";
        $stSql .= "               ,coalesce( sum(nlp.vl_pago),0.00 ) as vl_pago \n";
        $stSql .= " \n";
        $stSql .= "        from empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp \n";
        $stSql .= "             join ( \n";
        $stSql .= "                    select  nlp.cod_nota \n";
        $stSql .= "                           ,nlp.exercicio \n";
        $stSql .= "                           ,nlp.cod_entidade \n";
        $stSql .= "                           ,nlp.timestamp \n";
        $stSql .= "                           ,coalesce( coalesce(sum(nlp.vl_pago), 0.00) - coalesce( sum(nlpa.vl_pago_anulado),0.00) ) as vl_pago \n";
        $stSql .= "                    from empenho.nota_liquidacao_paga as nlp \n";
        $stSql .= "                         left join ( \n";
        $stSql .= "                                     select  nlpa.cod_nota \n";
        $stSql .= "                                            ,nlpa.exercicio \n";
        $stSql .= "                                            ,nlpa.cod_entidade \n";
        $stSql .= "                                            ,nlpa.timestamp \n";
        $stSql .= "                                            ,coalesce(sum(nlpa.vl_anulado),0.00) as vl_pago_anulado \n";
        $stSql .= "                                     from empenho.nota_liquidacao_paga_anulada as nlpa \n";
        $stSql .= "                                     group by  nlpa.cod_nota \n";
        $stSql .= "                                              ,nlpa.exercicio \n";
        $stSql .= "                                              ,nlpa.cod_entidade \n";
        $stSql .= "                                              ,nlpa.timestamp \n";
        $stSql .= "                                   ) as nlpa \n";
        $stSql .= "                                   on ( \n";
        $stSql .= "                                           nlpa.cod_nota     = nlp.cod_nota \n";
        $stSql .= "                                       and nlpa.exercicio    = nlp.exercicio \n";
        $stSql .= "                                       and nlpa.cod_entidade = nlp.cod_entidade \n";
        $stSql .= "                                       and nlpa.timestamp    = nlp.timestamp \n";
        $stSql .= "                                      ) \n";
        $stSql .= "                    group by  nlp.cod_nota \n";
        $stSql .= "                             ,nlp.exercicio \n";
        $stSql .= "                             ,nlp.timestamp \n";
        $stSql .= "                             ,nlp.cod_entidade \n";
        $stSql .= "                  ) as nlp \n";
        $stSql .= "                  on ( \n";
        $stSql .= "                            nlp.cod_nota     = plnlp.cod_nota \n";
        $stSql .= "                        and nlp.exercicio    = plnlp.exercicio_liquidacao \n";
        $stSql .= "                        and nlp.cod_entidade = plnlp.cod_entidade \n";
        $stSql .= "                        and nlp.timestamp    = plnlp.timestamp \n";
        $stSql .= "                     ) \n";
        $stSql .= "                  left join tesouraria.pagamento as tpag on(\n";
        $stSql .= "                                              nlp.cod_nota     = tpag.cod_nota\n";
        $stSql .= "                                          and nlp.exercicio    = tpag.exercicio\n";
        $stSql .= "                                          and nlp.cod_entidade = tpag.cod_entidade\n";
        $stSql .= "                                          and nlp.timestamp    = tpag.timestamp\n";
        $stSql .= "                                      )\n";
        $stSql .= "                          where tpag.cod_nota is null\n";
        $stSql .= "                  \n";
    if($this->getDado('stFiltro'))
        $stSql .= "        and ".$this->getDado('stFiltro')." \n";
        $stSql .= "        group by plnlp.cod_ordem \n";
        $stSql .= "                ,plnlp.exercicio \n";
        $stSql .= "                ,plnlp.cod_entidade \n";
        $stSql .= "     ) as pag \n";
        $stSql .= "     on ( \n";
        $stSql .= "             pag.cod_ordem    = op.cod_ordem \n";
        $stSql .= "         and pag.exercicio    = op.exercicio \n";
        $stSql .= "         and pag.cod_entidade = op.cod_entidade \n";
        $stSql .= "        ) \n";
        $stSql .= "join empenho.pagamento_liquidacao as pl \n";
        $stSql .= "     on ( \n";
        $stSql .= "             pl.cod_ordem    = op.cod_ordem \n";
        $stSql .= "         and pl.exercicio    = op.exercicio \n";
        $stSql .= "         and pl.cod_entidade = op.cod_entidade \n";
        $stSql .= "        ) \n";
        $stSql .= "join empenho.nota_liquidacao as nl \n";
        $stSql .= "     on ( \n";
        $stSql .= "             nl.cod_nota     = pl.cod_nota \n";
        $stSql .= "         and nl.exercicio    = pl.exercicio_liquidacao \n";
        $stSql .= "         and nl.cod_entidade = pl.cod_entidade \n";
        $stSql .= "        ) \n";
        $stSql .= "join empenho.empenho as em \n";
        $stSql .= "     on ( \n";
        $stSql .= "             em.cod_empenho  = nl.cod_empenho \n";
        $stSql .= "         and em.exercicio    = nl.exercicio_empenho \n";
        $stSql .= "         and em.cod_entidade = nl.cod_entidade \n";
        $stSql .= "        ) \n";
        $stSql .= " LEFT JOIN                                                               \n";
        $stSql .= " (                                                                       \n";
        $stSql .= "     SELECT                                                              \n";
        $stSql .= "          cod_empenho                                                    \n";
        $stSql .= "         ,exercicio                                                      \n";
        $stSql .= "         ,cod_entidade                                                   \n";
        $stSql .= "         ,coalesce(SUM(valor_item),0.00) as vl_prestado                  \n";
        $stSql .= "     FROM                                                                \n";
        $stSql .= "         empenho.item_prestacao_contas as eipc                           \n";
        $stSql .= "     WHERE                                                               \n";
        $stSql .= "     NOT EXISTS ( SELECT num_item                                        \n";
        $stSql .= "                     FROM empenho.item_prestacao_contas_anulado          \n";
        $stSql .= "                  WHERE                                                  \n";
        $stSql .= "                         cod_empenho     = eipc.cod_empenho              \n";
        $stSql .= "                     AND exercicio       = eipc.exercicio                \n";
        $stSql .= "                     AND cod_entidade    = eipc.cod_entidade             \n";
        $stSql .= "                     AND num_item        = eipc.num_item                 \n";
        $stSql .= "                 )                                                       \n";
        $stSql .= "    GROUP BY                                                             \n";
        $stSql .= "         cod_empenho,exercicio,cod_entidade                              \n";
        $stSql .= "   ) AS itens ON (                                                       \n";
        $stSql .= "                      itens.cod_empenho  = em.cod_empenho               \n";
        $stSql .= "                  AND itens.exercicio    = em.exercicio                 \n";
        $stSql .= "                  AND itens.cod_entidade = em.cod_entidade              \n";
        $stSql .= " )                                                                       \n";
        $stSql .= "join empenho.pre_empenho as pe \n";
        $stSql .= "     on ( \n";
        $stSql .= "             pe.cod_pre_empenho = em.cod_pre_empenho \n";
        $stSql .= "         and pe.exercicio       = em.exercicio \n";
        $stSql .= "        ) \n";
        $stSql .= " \n";
        $stSql .= "join orcamento.entidade as oe \n";
        $stSql .= "     on ( \n";
        $stSql .= "             oe.cod_entidade = op.cod_entidade \n";
        $stSql .= "         and oe.exercicio    = op.exercicio \n";
        $stSql .= "        ) \n";
        $stSql .= "join sw_cgm as ent \n";
        $stSql .= "     on ( ent.numcgm = oe.numcgm ) \n";
        $stSql .= " \n";
        $stSql .= "join sw_cgm as ben \n";
        $stSql .= "     on ( ben.numcgm = pe.cgm_beneficiario ) \n";

        return $stSql;
    }

    /**
        * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaDadosPagamento.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosPagamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosPagamento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaDadosPagamentoBordero(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosPagamentoBordero().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosPagamentoBordero()
    {
        $stSql  = " 
            SELECT * FROM                                                                                                                        
                 ( SELECT EOP.COD_ORDEM                                                                                                    
                        , EMP.EXERCICIO_EMPENHO
                        , EOP.EXERCICIO                                                                                                    
                        , EOP.COD_ENTIDADE
                        , TO_CHAR(EOP.DT_VENCIMENTO, 'dd/mm/yyyy') AS DT_VENCIMENTO
                        , TO_CHAR(EOP.DT_EMISSAO, 'dd/mm/yyyy') AS DT_EMISSAO
                        , CGMEMP.NOM_CGM AS BENEFICIARIO 
                        , EMPENHO.fn_consultar_valor_pagamento_ordem(eop.EXERCICIO,eop.COD_ORDEM,eop.COD_ENTIDADE) AS VALOR_PAGAMENTO
                        , coalesce(eopa.vl_anulado,0.00) AS vl_anulado              
                        , EMP.CGM_BENEFICIARIO
                        , coalesce(sum(emp.vl_pago_nota),0.00) AS vl_pago_nota
                        , replace(empenho.retorna_notas_empenhos(eop.exercicio,eop.cod_ordem,eop.cod_entidade),'','<br>') AS nota_empenho
                        , EMP.implantado

                     FROM EMPENHO.ORDEM_PAGAMENTO AS EOP                                                                                           

                LEFT JOIN ( SELECT opa.cod_ordem                                                                                             
                                 , opa.exercicio                                                                                             
                                 , opa.cod_entidade                                                                                          
                                 , coalesce(sum(opla.vl_anulado),0.00) as vl_anulado                                                         
                              FROM  EMPENHO.ORDEM_PAGAMENTO_ANULADA AS OPA                                                                     
                        INNER JOIN empenho.ordem_pagamento_liquidacao_anulada as opla                                                    
                                ON opa.exercicio    = opla.exercicio                                                                  
                               AND opa.cod_ordem    = opla.cod_ordem                                                                  
                               AND opa.cod_entidade = opla.cod_entidade                                                               
                               AND opa.timestamp    = opla.timestamp                                                                  
                               
                          GROUP BY opa.cod_ordem
                                 , opa.exercicio
                                 , opa.cod_entidade
                        ) AS EOPA
                       ON eopa.cod_ordem    = eop.cod_ordem                                                                           
                      AND eopa.exercicio    = eop.exercicio                                                                           
                      AND eopa.cod_entidade = eop.cod_entidade                                                                     
            
                LEFT JOIN( SELECT PL.COD_ORDEM
                                , PL.EXERCICIO
                                , PL.COD_ENTIDADE
                                , PE.CGM_BENEFICIARIO
                                , PE.IMPLANTADO
                                , NL.EXERCICIO_EMPENHO
                                , NL.COD_EMPENHO
                                , NL.COD_NOTA
                                , sum(NLP.vl_pago) AS vl_pago_nota
                             FROM EMPENHO.PAGAMENTO_LIQUIDACAO AS PL                                                                              
                                , EMPENHO.NOTA_LIQUIDACAO      AS NL                                                                                
                         
                         LEFT JOIN (  SELECT nlp.exercicio                                                                                        
                                           , nlp.cod_entidade                                                                                     
                                           , nlp.cod_nota                                                                                         
                                           , nlp.timestamp                                                                                        
                                           , coalesce(sum(nlp.vl_pago),0.00) - coalesce(sum(nlp.vl_anulado),0.00) as vl_pago                      
                                        
                                        FROM ( SELECT cod_nota                                                                                    
                                                    , cod_entidade                                                                                
                                                    , exercicio                                                                                   
                                                    , timestamp                                                                                   
                                                    , sum(vl_pago) AS vl_pago                                                                     
                                                    , 0.00 as vl_anulado                                                                          
                                                 FROM empenho.nota_liquidacao_paga                                                                   
                                             GROUP BY cod_nota
                                                    , timestamp
                                                    , cod_entidade
                                                    , exercicio
                                                    , vl_anulado                                    

                                        UNION                                                                                                     

                                              SELECT cod_nota                                                                                     
                                                   , cod_entidade                                                                                 
                                                   , exercicio                                                                                    
                                                   , timestamp                                                                                    
                                                   , 0.00 as vl_pago                                                                              
                                                   , sum(vl_anulado) AS vl_anulado                                                                
                                                FROM  empenho.nota_liquidacao_paga_anulada                                                          
                                            GROUP BY cod_nota
                                                   , timestamp
                                                   , cod_entidade
                                                   , exercicio
                                                   , vl_pago
                                           ) AS NLP

                                 INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS PLNLP
                                         ON nlp.cod_nota     = plnlp.cod_nota
                                        AND nlp.exercicio    = plnlp.exercicio_liquidacao
                                        AND nlp.cod_entidade = plnlp.cod_entidade
                                        AND nlp.timestamp    = plnlp.timestamp  \n";
            
            if ( $this->getDado('cod_ordem') )
                $stSql .= "            AND plnlp.cod_ordem = ".$this->getDado('cod_ordem')." \n";

            $stSql .= "           GROUP BY nlp.exercicio, nlp.timestamp, nlp.cod_entidade, nlp.cod_nota                                        
                               ) AS NLP
                               ON nlp.cod_nota     = nl.cod_nota                                                                           
                              AND nlp.exercicio    = nl.exercicio                                                                         
                              AND nlp.cod_entidade = nl.cod_entidade                                                                   
                            
                             , EMPENHO.EMPENHO     AS E
                             , EMPENHO.PRE_EMPENHO AS PE                                                                               
                         
                         WHERE PL.COD_NOTA             = NL.COD_NOTA       
                           AND PL.EXERCICIO_LIQUIDACAO = NL.EXERCICIO      
                           AND PL.COD_ENTIDADE         = NL.COD_ENTIDADE  \n"; 
                            
        if ( $this->getDado('cod_ordem') )
            $stSql .= " AND pl.cod_ordem = ".$this->getDado('cod_ordem')." \n";
        
        if ( $this->getDado('exercicio') )
            $stSql .= " AND pl.exercicio = '".$this->getDado('exercicio')."' \n";
        
        if ( $this->getDado('cod_entidade') )
            $stSql .= " AND pl.cod_entidade = ".$this->getDado('cod_entidade')." \n";

        $stSql .= "        AND NL.COD_EMPENHO          = E.COD_EMPENHO
                           AND NL.EXERCICIO_EMPENHO    = E.EXERCICIO
                           AND NL.COD_ENTIDADE         = E.COD_ENTIDADE

                           AND E.COD_PRE_EMPENHO       = PE.COD_PRE_EMPENHO
                           AND E.EXERCICIO             = PE.EXERCICIO                                                                            

                     GROUP BY PL.COD_ORDEM
                            , PL.EXERCICIO
                            , PL.COD_ENTIDADE
                            , PE.CGM_BENEFICIARIO
                            , PE.IMPLANTADO
                            , NL.EXERCICIO_EMPENHO
                            , NL.COD_EMPENHO
                            , NL.COD_NOTA

                     ) AS EMP
                    ON EOP.COD_ORDEM    = EMP.COD_ORDEM 
                   AND EOP.EXERCICIO    = EMP.EXERCICIO
                   AND EOP.COD_ENTIDADE = EMP.COD_ENTIDADE                                                                                   
            
              LEFT JOIN ORCAMENTO.ENTIDADE AS OE                                                                                                 
                     ON OE.COD_ENTIDADE = EOP.COD_ENTIDADE                                                                                       
                    AND OE.EXERCICIO    = EOP.EXERCICIO
                     
              LEFT JOIN SW_CGM AS CGMEMP
                     ON CGMEMP.NUMCGM = EMP.CGM_BENEFICIARIO
                     
                  WHERE eop.cod_ordem IS NOT NULL ";
        
        if ( $this->getDado('cod_ordem') )
            $stSql .= " AND EOP.cod_ordem = ".$this->getDado('cod_ordem')." \n";
        
        if ( $this->getDado('exercicio') )
            $stSql .= " AND eop.exercicio = '".$this->getDado('exercicio')."' \n";
        
        if ( $this->getDado('cod_entidade') )
            $stSql .= " AND eop.cod_entidade = ".$this->getDado('cod_entidade')." \n";
    
        $stSql .= "
              GROUP BY eop.exercicio
                    , eop.dt_vencimento
                    , eop.dt_emissao
                    , emp.exercicio_empenho
                    , eop.COD_ORDEM
                    , eop.COD_ENTIDADE
                    , EMP.CGM_BENEFICIARIO
                    , CGMEMP.nom_cgm
                    , VALOR_PAGAMENTO
                    , EMP.implantado
                    , eopa.vl_anulado 

                 ORDER BY eop.cod_ordem                                                                                                           
                ) AS tbl                                                                                                                             
                
            WHERE (valor_pagamento - vl_anulado ) >= vl_pago_nota ";

        return $stSql;
    }

    /**
        * Monta a cláusula SQL que retorna os itens a pagar de uma ordem de pagamento
        * @access Public
        * @return String String contendo o SQL
    */
    public function montaRecuperaItensPagamento()
    {
        $stSql  = "SELECT * FROM ( \n";
        $stSql .= "SELECT             \n";
        $stSql .= "\n";
        $stSql .= "     eem.cod_empenho,                                        \n";
        $stSql .= "     eem.exercicio as ex_empenho,                            \n";
        $stSql .= "     to_char(eem.dt_empenho,'dd/mm/yyyy') as dt_empenho,     \n";
        $stSql .= "     enl.cod_nota,                                           \n";
        $stSql .= "     enl.exercicio as ex_nota,                               \n";
        $stSql .= "     to_char(enl.dt_liquidacao,'dd/mm/yyyy') as dt_nota,     \n";
        $stSql .= "     enl.cod_entidade,                                       \n";
        $stSql .= "     coalesce(sum(pag.vl_pago),0.00) as vl_pago, \n";
        $stSql .= "     coalesce( (epl.vl_pagamento - coalesce(opla.vl_anulado,0.00))- coalesce(sum(pag.vl_pago),0.00), 0.00 ) as vl_pagamento, \n";
        $stSql .= "     CASE WHEN ode.cod_recurso IS NOT NULL THEN ode.cod_recurso  \n";
        $stSql .= "      ELSE     rpe.recurso                                   \n";
        $stSql .= "     END as cod_recurso,                                     \n";
        $stSql .= "     ece.conta_contrapartida                                 \n";
        $stSql .= " FROM                                                        \n";
        $stSql .= "     empenho.pagamento_liquidacao as epl                   \n";
        $stSql .= "\n";
        $stSql .= "     left join (\n";
        $stSql .= "                select  opla.cod_nota             \n";
        $stSql .= "                       ,opla.exercicio_liquidacao \n";
        $stSql .= "                       ,opla.cod_entidade         \n";
        $stSql .= "                       ,opla.cod_ordem            \n";
        $stSql .= "                       ,opla.exercicio           \n";
        $stSql .= "                       ,sum(opla.vl_anulado) as vl_anulado\n";
        $stSql .= "\n";
        $stSql .= "                from empenho.ordem_pagamento_liquidacao_anulada as opla \n";
        $stSql .= "                group by opla.cod_nota             \n";
        $stSql .= "                        ,opla.exercicio_liquidacao \n";
        $stSql .= "                        ,opla.cod_entidade         \n";
        $stSql .= "                        ,opla.cod_ordem            \n";
        $stSql .= "                        ,opla.exercicio           \n";
        $stSql .= "         ) as opla\n";
        $stSql .= "         on (\n";
        $stSql .= "                  opla.cod_nota             = epl.cod_nota\n";
        $stSql .= "              AND opla.exercicio_liquidacao = epl.exercicio_liquidacao\n";
        $stSql .= "              AND opla.cod_entidade         = epl.cod_entidade\n";
        $stSql .= "              AND opla.cod_ordem            = epl.cod_ordem\n";
        $stSql .= "              AND opla.exercicio            = epl.exercicio\n";
        $stSql .= "            )\n";
        $stSql .= "     \n";
        $stSql .= "       left join \n";
        $stSql .= "            ( \n";
        $stSql .= "              select plnlp.exercicio \n";
        $stSql .= "                    ,plnlp.cod_entidade \n";
        $stSql .= "                    ,plnlp.cod_ordem \n";
        $stSql .= "                    ,plnlp.timestamp \n";
        $stSql .= "                    ,plnlp.cod_nota\n";
        $stSql .= "                    ,plnlp.exercicio_liquidacao\n";
        $stSql .= "                    ,coalesce(sum(tnlp.vl_pago),0.00) as vl_pago \n";
        $stSql .= "              from empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp \n";
        $stSql .= "              left join ( \n";
        $stSql .= "              \n";
        $stSql .= "                         select  nlp.exercicio \n";
        $stSql .= "                                ,nlp.cod_entidade \n";
        $stSql .= "                                ,nlp.cod_nota \n";
        $stSql .= "                                ,nlp.timestamp                                 \n";
        $stSql .= "                                ,coalesce( coalesce(vl_pago, 0.00)-coalesce(vl_pago_anulado,0.00), 0.00 ) as vl_pago \n";
        $stSql .= "                         from empenho.nota_liquidacao_paga as nlp \n";
        $stSql .= "                              left join ( \n";
        $stSql .= "                                         select  nlpa.exercicio \n";
        $stSql .= "                                                ,nlpa.cod_nota \n";
        $stSql .= "                                                ,nlpa.cod_entidade \n";
        $stSql .= "                                                ,nlpa.timestamp \n";
        $stSql .= "                                                ,coalesce(sum(vl_anulado),0.00) as vl_pago_anulado \n";
        $stSql .= "                                         from empenho.nota_liquidacao_paga_anulada as nlpa \n";
        $stSql .= "                                         group by  nlpa.exercicio \n";
        $stSql .= "                                                  ,nlpa.cod_nota \n";
        $stSql .= "                                                  ,nlpa.cod_entidade \n";
        $stSql .= "                                                  ,nlpa.timestamp \n";
        $stSql .= "                                        ) as nlpa \n";
        $stSql .= "                                        on (     nlpa.exercicio    = nlp.exercicio \n";
        $stSql .= "                                             and nlpa.cod_nota     = nlp.cod_nota \n";
        $stSql .= "                                             and nlpa.cod_entidade = nlp.cod_entidade \n";
        $stSql .= "                                             and nlpa.timestamp    = nlp.timestamp \n";
        $stSql .= "                                           ) \n";
        $stSql .= "                                         \n";
        $stSql .= "                                          \n";
        $stSql .= "                       ) as tnlp \n";
        $stSql .= "                       on (     tnlp.exercicio    = plnlp.exercicio_liquidacao \n";
        $stSql .= "                            and tnlp.cod_nota     = plnlp.cod_nota \n";
        $stSql .= "                            and tnlp.cod_entidade = plnlp.cod_entidade \n";
        $stSql .= "                            and tnlp.timestamp    = plnlp.timestamp \n";
        $stSql .= "                          ) \n";
        $stSql .= "\n";
        $stSql .= "              group by plnlp.exercicio \n";
        $stSql .= "                      ,plnlp.cod_entidade \n";
        $stSql .= "                      ,plnlp.cod_ordem \n";
        $stSql .= "                      ,plnlp.timestamp \n";
        $stSql .= "                      ,plnlp.cod_nota\n";
        $stSql .= "                      ,plnlp.exercicio_liquidacao\n";
        $stSql .= "                      \n";
        $stSql .= "            ) as pag \n";
        $stSql .= "\n";
        $stSql .= "            on (     pag.exercicio    = epl.exercicio \n";
        $stSql .= "                 and pag.cod_entidade = epl.cod_entidade \n";
        $stSql .= "                 and pag.cod_ordem    = epl.cod_ordem \n";
        $stSql .= "                 and pag.cod_nota     = epl.cod_nota\n";
        $stSql .= "                 and pag.exercicio_liquidacao = epl.exercicio_liquidacao\n";
        $stSql .= "\n";
        $stSql .= "               ) \n";
        $stSql .= "     join empenho.nota_liquidacao as enl\n";
        $stSql .= "          on (\n";
        $stSql .= "                   epl.exercicio_liquidacao = enl.exercicio            \n";
        $stSql .= "               AND epl.cod_nota = enl.cod_nota                         \n";
        $stSql .= "               AND epl.cod_entidade = enl.cod_entidade                 \n";
        $stSql .= "             )\n";
        $stSql .= "\n";
        $stSql .= "     join empenho.empenho as eem\n";
        $stSql .= "          on (\n";
        $stSql .= "                   enl.exercicio_empenho = eem.exercicio               \n";
        $stSql .= "               AND enl.cod_entidade = eem.cod_entidade                 \n";
        $stSql .= "               AND enl.cod_empenho = eem.cod_empenho                   \n";
        $stSql .= "             )\n";
        $stSql .= "     join empenho.pre_empenho as epe                              \n";
        $stSql .= "          on (\n";
        $stSql .= "                   eem.exercicio = epe.exercicio                       \n";
        $stSql .= "               AND eem.cod_pre_empenho = epe.cod_pre_empenho           \n";
        $stSql .= "             )\n";
        $stSql .= "     LEFT JOIN empenho.contrapartida_empenho as ece                    \n";
        $stSql .= "         ON (                                                          \n";
        $stSql .= "                   eem.exercicio    = ece.exercicio                    \n";
        $stSql .= "               AND eem.cod_entidade = ece.cod_entidade                 \n";
        $stSql .= "               AND eem.cod_empenho  = ece.cod_empenho                  \n";
        $stSql .= "            )                                                          \n";
        $stSql .= "    LEFT JOIN                                                \n";
        $stSql .= "     empenho.pre_empenho_despesa as epd                      \n";
        $stSql .= "    ON  (                                                    \n";
        $stSql .= "                 epe.exercicio = epd.exercicio               \n";
        $stSql .= "         AND     epe.cod_pre_empenho = epd.cod_pre_empenho   \n";
        $stSql .= "        )                                                    \n";
        $stSql .= "    LEFT JOIN                                                \n";
        $stSql .= "     orcamento.despesa as ode                                \n";
        $stSql .= "    ON  (                                                    \n";
        $stSql .= "                 epd.exercicio = ode.exercicio               \n";
        $stSql .= "         AND     epd.cod_despesa = ode.cod_despesa           \n";
        $stSql .= "        )                                                    \n";
        $stSql .= "    LEFT JOIN                                                \n";
        $stSql .= "     empenho.restos_pre_empenho as rpe                       \n";
        $stSql .= "    ON   (                                                   \n";
        $stSql .= "                 epe.exercicio = rpe.exercicio               \n";
        $stSql .= "         AND     epe.cod_pre_empenho = rpe.cod_pre_empenho   \n";
        $stSql .= "         )                                                   \n";
        $stSql .= " Where 1=1 \n";
        $stSql .= $this->getDado( 'filtro' );
        $stSql .= $this->getDado( 'groupby');
        $stSql .= "\n ) as tbl \n"; // Where vl_pagamento > 0.00 \n";

        return $stSql;
    }
    /**
        * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaItensPagamento.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaItensPagamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaItensPagamento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /**
        * Monta a cláusula SQL que retorna os itens a pagar de uma ordem de pagamento
        * @access Public
        * @return String String contendo o SQL
    */
    public function montaRecuperaItensEstorno()
    {
        $stSql  = "SELECT * FROM ( \n";
        $stSql .= "SELECT                                                       \n";
        $stSql .= "     eem.cod_empenho,                                         \n";
        $stSql .= "     eem.exercicio as ex_empenho,                             \n";
        $stSql .= "     to_char(eem.dt_empenho,'dd/mm/yyyy') as dt_empenho,      \n";
        $stSql .= "     enl.cod_nota,                                            \n";
        $stSql .= "     enl.exercicio as ex_nota,                                \n";
        $stSql .= "     to_char(enl.dt_liquidacao,'dd/mm/yyyy') as dt_nota,      \n";
        $stSql .= "     enl.cod_entidade,                                        \n";
        $stSql .= "     coalesce(sum(pag.vl_pago),0.00) as vl_pago,              \n";
        $stSql .= "     CASE WHEN coalesce(itens.vl_prestado,0.00) != 0.00 THEN coalesce(sum(pag.vl_pago),0.00)-coalesce(itens.vl_prestado,0.00) ELSE coalesce(sum(pag.vl_pago),0.00) END as vl_pagonaoprestado,              \n";
        $stSql .= "     coalesce(itens.vl_prestado,0.00) as vl_prestado,         \n";
        $stSql .= "     pag.timestamp,                                           \n";
        $stSql .= "     to_char(pag.timestamp, 'dd/mm/yyyy') as dt_pagamento,    \n";
        $stSql .= "     epl.vl_pagamento, \n";
        $stSql .= "     opla.vl_anulado, \n";
        $stSql .= "     CASE WHEN ode.cod_recurso IS NOT NULL THEN ode.cod_recurso   \n";
        $stSql .= "      ELSE     rpe.recurso                                    \n";
        $stSql .= "     END as cod_recurso,                                       \n";
        $stSql .= "     pag.cod_plano,                                          \n";
        $stSql .= "     pag.exercicio_plano                                      \n";
        $stSql .= " FROM                                                         \n";
        $stSql .= "     empenho.pagamento_liquidacao as epl                    \n";
        $stSql .= "     left join ( \n";
        $stSql .= "                select  opla.cod_nota              \n";
        $stSql .= "                       ,opla.exercicio_liquidacao  \n";
        $stSql .= "                       ,opla.cod_entidade          \n";
        $stSql .= "                       ,opla.cod_ordem             \n";
        $stSql .= "                       ,opla.exercicio            \n";
        $stSql .= "                       ,sum(opla.vl_anulado) as vl_anulado \n";
        $stSql .= " \n";
        $stSql .= "                from empenho.ordem_pagamento_liquidacao_anulada as opla  \n";
        $stSql .= "                group by opla.cod_nota              \n";
        $stSql .= "                        ,opla.exercicio_liquidacao  \n";
        $stSql .= "                        ,opla.cod_entidade          \n";
        $stSql .= "                        ,opla.cod_ordem             \n";
        $stSql .= "                        ,opla.exercicio            \n";
        $stSql .= "         ) as opla \n";
        $stSql .= "         on ( \n";
        $stSql .= "                  opla.cod_nota             = epl.cod_nota \n";
        $stSql .= "              AND opla.exercicio_liquidacao = epl.exercicio_liquidacao \n";
        $stSql .= "              AND opla.cod_entidade         = epl.cod_entidade \n";
        $stSql .= "              AND opla.cod_ordem            = epl.cod_ordem \n";
        $stSql .= "              AND opla.exercicio            = epl.exercicio \n";
        $stSql .= "            ) \n";
        $stSql .= " \n";
        $stSql .= "      \n";
        $stSql .= "       left join  \n";
        $stSql .= "            (  \n";
        $stSql .= "             \n";
        $stSql .= "              select plnlp.exercicio  \n";
        $stSql .= "                    ,plnlp.cod_entidade  \n";
        $stSql .= "                    ,plnlp.cod_ordem  \n";
        $stSql .= "                    ,plnlp.cod_nota \n";
        $stSql .= "                    ,plnlp.timestamp  \n";
        $stSql .= "                    ,plnlp.exercicio_liquidacao \n";
        $stSql .= "                    ,coalesce(sum(tnlp.vl_pago),0.00) as vl_pago  \n";
        $stSql .= "                    ,tnlp.exercicio_plano                \n";
        $stSql .= "                    ,tnlp.cod_plano                  \n";
        $stSql .= "              from empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp  \n";
        $stSql .= "              left join (  \n";
        $stSql .= "                         select  nlp.exercicio  \n";
        $stSql .= "                                ,nlp.cod_entidade  \n";
        $stSql .= "                                ,nlp.cod_nota  \n";
        $stSql .= "                                ,nlp.timestamp  \n";
        $stSql .= "                                ,coalesce(vl_pago, 0.00)-coalesce(vl_pago_anulado,0.00) as vl_pago  \n";
        $stSql .= "                                ,nlcp.exercicio as exercicio_plano                       \n";
        $stSql .= "                                ,nlcp.cod_plano                                          \n";
        $stSql .= "                         from empenho.nota_liquidacao_paga as nlp  \n";
        $stSql .= "                              left join (  \n";
        $stSql .= "                                         select  nlpa.exercicio  \n";
        $stSql .= "                                                ,nlpa.cod_nota  \n";
        $stSql .= "                                                ,nlpa.cod_entidade  \n";
        $stSql .= "                                                ,nlpa.timestamp  \n";
        $stSql .= "                                                ,coalesce(sum(vl_anulado),0.00) as vl_pago_anulado  \n";
        $stSql .= "                                         from empenho.nota_liquidacao_paga_anulada as nlpa  \n";
        $stSql .= "                                         group by  nlpa.exercicio  \n";
        $stSql .= "                                                  ,nlpa.cod_nota  \n";
        $stSql .= "                                                  ,nlpa.cod_entidade  \n";
        $stSql .= "                                                  ,nlpa.timestamp  \n";
        $stSql .= "                                        ) as nlpa  \n";
        $stSql .= "                                        on (     nlpa.exercicio    = nlp.exercicio  \n";
        $stSql .= "                                             and nlpa.cod_nota     = nlp.cod_nota  \n";
        $stSql .= "                                             and nlpa.cod_entidade = nlp.cod_entidade  \n";
        $stSql .= "                                             and nlpa.timestamp    = nlp.timestamp  \n";
        $stSql .= "                                           )  \n";
        $stSql .= "                              left join empenho.nota_liquidacao_conta_pagadora as nlcp           \n";
        $stSql .= "                                     ON (    nlcp.exercicio_liquidacao   = nlp.exercicio          \n";
        $stSql .= "                                         AND nlcp.cod_entidade           = nlp.cod_entidade          \n";
        $stSql .= "                                         AND nlcp.cod_nota               = nlp.cod_nota          \n";
        $stSql .= "                                         AND nlcp.timestamp              = nlp.timestamp           \n";
        $stSql .= "                                     )          \n";
        $stSql .= "                       ) as tnlp  \n";
        $stSql .= "                       on (     tnlp.exercicio    = plnlp.exercicio_liquidacao  \n";
        $stSql .= "                            and tnlp.cod_nota     = plnlp.cod_nota  \n";
        $stSql .= "                            and tnlp.cod_entidade = plnlp.cod_entidade  \n";
        $stSql .= "                            and tnlp.timestamp    = plnlp.timestamp \n";
        $stSql .= "                          )  \n";
        $stSql .= "              group by plnlp.exercicio  \n";
        $stSql .= "                      ,plnlp.cod_entidade  \n";
        $stSql .= "                      ,plnlp.cod_ordem  \n";
        $stSql .= "                      ,plnlp.cod_nota \n";
        $stSql .= "                      ,plnlp.timestamp  \n";
        $stSql .= "                      ,plnlp.exercicio_liquidacao \n";
        $stSql .= "                      ,tnlp.exercicio_plano   \n";
        $stSql .= "                      ,tnlp.cod_plano            \n";
        $stSql .= "                       \n";
        $stSql .= "            ) as pag  \n";
        $stSql .= "            on (     pag.exercicio    = epl.exercicio  \n";
        $stSql .= "                 and pag.cod_entidade = epl.cod_entidade  \n";
        $stSql .= "                 and pag.cod_ordem    = epl.cod_ordem \n";
        $stSql .= "                 and pag.cod_nota     = epl.cod_nota \n";
        $stSql .= "                 and pag.exercicio_liquidacao = epl.exercicio_liquidacao \n";
        $stSql .= "               )  \n";
        $stSql .= "     join empenho.nota_liquidacao as enl \n";
        $stSql .= "          on ( \n";
        $stSql .= "                   epl.exercicio_liquidacao = enl.exercicio             \n";
        $stSql .= "               AND epl.cod_nota = enl.cod_nota                          \n";
        $stSql .= "               AND epl.cod_entidade = enl.cod_entidade                  \n";
        $stSql .= "             ) \n";
        $stSql .= " \n";
        $stSql .= "     join empenho.empenho as eem \n";
        $stSql .= "          on ( \n";
        $stSql .= "                   enl.exercicio_empenho = eem.exercicio                \n";
        $stSql .= "               AND enl.cod_entidade = eem.cod_entidade                  \n";
        $stSql .= "               AND enl.cod_empenho = eem.cod_empenho                    \n";
        $stSql .= "             )                                                           \n";
        $stSql .= " LEFT JOIN                                                               \n";
        $stSql .= " (                                                                       \n";
        $stSql .= "     SELECT                                                              \n";
        $stSql .= "          cod_empenho                                                    \n";
        $stSql .= "         ,exercicio                                                      \n";
        $stSql .= "         ,cod_entidade                                                   \n";
        $stSql .= "         ,coalesce(SUM(valor_item),0.00) as vl_prestado                  \n";
        $stSql .= "     FROM                                                                \n";
        $stSql .= "         empenho.item_prestacao_contas as eipc                           \n";
        $stSql .= "     WHERE                                                               \n";
        $stSql .= "     NOT EXISTS ( SELECT num_item                                        \n";
        $stSql .= "                     FROM empenho.item_prestacao_contas_anulado          \n";
        $stSql .= "                  WHERE                                                  \n";
        $stSql .= "                         cod_empenho     = eipc.cod_empenho              \n";
        $stSql .= "                     AND exercicio       = eipc.exercicio                \n";
        $stSql .= "                     AND cod_entidade    = eipc.cod_entidade             \n";
        $stSql .= "                     AND num_item        = eipc.num_item                 \n";
        $stSql .= "                 )                                                       \n";
        $stSql .= "    GROUP BY                                                             \n";
        $stSql .= "         cod_empenho,exercicio,cod_entidade                              \n";
        $stSql .= "   ) AS itens ON (                                                       \n";
        $stSql .= "                      itens.cod_empenho  = eem.cod_empenho               \n";
        $stSql .= "                  AND itens.exercicio    = eem.exercicio                 \n";
        $stSql .= "                  AND itens.cod_entidade = eem.cod_entidade              \n";
        $stSql .= " )                                                                       \n";
        $stSql .= "     join empenho.pre_empenho as epe                                     \n";
        $stSql .= "          on (                                                           \n";
        $stSql .= "                   eem.exercicio = epe.exercicio                         \n";
        $stSql .= "               AND eem.cod_pre_empenho = epe.cod_pre_empenho             \n";
        $stSql .= "             ) \n";
        $stSql .= "     \n";
        $stSql .= "    LEFT JOIN                                                 \n";
        $stSql .= "     empenho.pre_empenho_despesa as epd                       \n";
        $stSql .= "    ON  (                                                     \n";
        $stSql .= "                 epe.exercicio = epd.exercicio                \n";
        $stSql .= "         AND     epe.cod_pre_empenho = epd.cod_pre_empenho    \n";
        $stSql .= "        )                                                     \n";
        $stSql .= "    LEFT JOIN                                                 \n";
        $stSql .= "     orcamento.despesa as ode                                 \n";
        $stSql .= "    ON  (                                                     \n";
        $stSql .= "                 epd.exercicio = ode.exercicio                \n";
        $stSql .= "         AND     epd.cod_despesa = ode.cod_despesa            \n";
        $stSql .= "        )                                                     \n";
        $stSql .= "    LEFT JOIN                                                 \n";
        $stSql .= "     empenho.restos_pre_empenho as rpe                        \n";
        $stSql .= "    ON   (                                                    \n";
        $stSql .= "                 epe.exercicio = rpe.exercicio                \n";
        $stSql .= "         AND     epe.cod_pre_empenho = rpe.cod_pre_empenho    \n";
        $stSql .= "         )                                                    \n";
        $stSql .= " Where 1=1 \n";
        $stSql .= $this->getDado( 'filtro' );
        $stSql .= $this->getDado( 'groupby');
        $stSql .= "\n ) as tbl Where vl_pago > 0.00 \n";

        return $stSql;
    }
    /**
        * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaItensEstorno.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaItensEstorno(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaItensEstorno().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaLiquidacoesAnulacaoOP(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaLiquidacoesAnulacaoOP().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaLiquidacoesAnulacaoOP()
    {
        $stSql  = "SELECT  pl.cod_ordem ||'/'|| pl.exercicio as OP \n";
        $stSql .= "       ,pl.cod_nota \n";
        $stSql .= "       ,pl.exercicio_liquidacao \n";
        $stSql .= "       ,pl.vl_pagamento \n";
        $stSql .= "       ,( \n";
        $stSql .= "         SELECT coalesce(sum(opla.vl_anulado),0.00) \n";
        $stSql .= "         FROM   empenho.ordem_pagamento_liquidacao_anulada as opla \n";
        $stSql .= "         WHERE  pl.exercicio    = opla.exercicio \n";
        $stSql .= "           AND  pl.cod_entidade = opla.cod_entidade \n";
        $stSql .= "           AND  pl.cod_ordem    = opla.cod_ordem \n";
        $stSql .= "           AND  pl.exercicio_liquidacao = opla.exercicio_liquidacao \n";
        $stSql .= "           AND  pl.cod_nota     = opla.cod_nota \n";
        $stSql .= "           AND  opla.timestamp <= to_timestamp('".$this->getDado('timestamp')."','yyyy-mm-dd hh24:mi:ss.us') \n";
        $stSql .= "        ) as vl_anulado_ate_periodo \n";
        $stSql .= "       ,( \n";
        $stSql .= "         SELECT coalesce(opla.vl_anulado,0.00) \n";
        $stSql .= "         FROM   empenho.ordem_pagamento_liquidacao_anulada as opla \n";
        $stSql .= "         WHERE  pl.exercicio    = opla.exercicio \n";
        $stSql .= "           AND  pl.cod_entidade = opla.cod_entidade \n";
        $stSql .= "           AND  pl.cod_ordem    = opla.cod_ordem \n";
        $stSql .= "           AND  pl.exercicio_liquidacao = opla.exercicio_liquidacao \n";
        $stSql .= "           AND  pl.cod_nota     = opla.cod_nota \n";
        $stSql .= "           AND  opla.timestamp  = to_timestamp('".$this->getDado('timestamp')."','yyyy-mm-dd hh24:mi:ss.us') \n";
        $stSql .= "        ) as vl_anulado_atual \n";
        $stSql .= "       ,( \n";
        $stSql .= "         SELECT to_char(opla.timestamp, 'dd/mm/yyyy' ) \n";
        $stSql .= "         FROM   empenho.ordem_pagamento_liquidacao_anulada as opla \n";
        $stSql .= "         WHERE  pl.exercicio    = opla.exercicio \n";
        $stSql .= "           AND  pl.cod_entidade = opla.cod_entidade \n";
        $stSql .= "           AND  pl.cod_ordem    = opla.cod_ordem \n";
        $stSql .= "           AND  pl.exercicio_liquidacao = opla.exercicio_liquidacao \n";
        $stSql .= "           AND  pl.cod_nota     = opla.cod_nota \n";
        $stSql .= "           AND  opla.timestamp  = to_timestamp('".$this->getDado('timestamp')."','yyyy-mm-dd hh24:mi:ss.us') \n";
        $stSql .= "        ) as dt_anulacao \n";
        $stSql .= "       ,( \n";
        $stSql .= "         SELECT opa.motivo \n";
        $stSql .= "         FROM   empenho.ordem_pagamento_anulada as opa \n";
        $stSql .= "         WHERE  pl.exercicio    = opa.exercicio \n";
        $stSql .= "           AND  pl.cod_entidade = opa.cod_entidade \n";
        $stSql .= "           AND  pl.cod_ordem    = opa.cod_ordem \n";
        $stSql .= "           AND  opa.timestamp  = to_timestamp('".$this->getDado('timestamp')."','yyyy-mm-dd hh24:mi:ss.us') \n";
        $stSql .= "        ) as motivo \n";
        $stSql .= "FROM     empenho.pagamento_liquidacao  as pl \n";
        $stSql .= "WHERE     pl.cod_ordem = ".$this->getDado('cod_ordem')." \n";
        $stSql .= "      and pl.exercicio = '".$this->getDado('exercicio')."'\n";
        $stSql .= "      and pl.cod_entidade = ".$this->getDado('cod_entidade')." \n";

        return $stSql;
    }

    /**
        * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecupera/elatorioOP.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaRelatorioOP(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRelatorioOP().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );       
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelatorioOP()
    {
        $stSql  = " SELECT                                                                                                              \n";
        $stSql .= "     tabela.*                                                                                                        \n";
        $stSql .= "     ,mu.*                                                                                                           \n";
        $stSql .= "     ,(select valor from administracao.configuracao                                                                             \n";
        $stSql .= "       where cod_modulo = 2 and parametro = 'nom_prefeitura' AND  exercicio  = '".$this->getDado('exercicio')."' ) as nom_prefeitura                                     \n";
        $stSql .= "     ,publico.fn_mascara_dinamica( ( SELECT valor FROM administracao.configuracao WHERE parametro = 'masc_despesa' AND exercicio = '".$this->getDado('exercicio')."' ), dotacao ) as dotacao_formatada \n";
        $stSql .= "     ,cod_recurso as recurso_formatado \n";
        $stSql .= " FROM                                                                                                                \n";
        $stSql .= " (                                                                                                                   \n";
        $stSql .= "     SELECT                                                                                                          \n";
        $stSql .= "          pl.cod_ordem                                                                                               \n";
        $stSql .= "         ,pl.vl_pagamento                                                                                            \n";
        $stSql .= "         ,( empenho.fn_consultar_valor_liquidado_nota( nl.exercicio, em.cod_empenho, em.cod_entidade, nl.cod_nota) - \n";
        $stSql .= "         empenho.fn_consultar_valor_liquidado_anulado_nota( nl.exercicio, em.cod_empenho, em.cod_entidade, nl.cod_nota ) \n";
        $stSql .= "         )as vl_liquidado                                                                                            \n";
        $stSql .= "         ,to_char(op.dt_emissao,'yyyy-mm-dd') as dt_emissao                                                          \n";
        $stSql .= "         ,lpad(nl.cod_nota::varchar,6,'0') as cod_nota                                                                        \n";
        $stSql .= "         ,nl.cod_nota as cod_nota_relatorio                                                     \n";
        $stSql .= "         ,nl.cod_empenho                                                                                             \n";
        $stSql .= "         ,nl.cod_entidade                                                                                            \n";
        $stSql .= "         ,cgme.nom_cgm as nom_entidade                                                                               \n";
        $stSql .= "         ,lpad(nl.cod_empenho::varchar,6,'0') as cod_empenho                                                                  \n";
        $stSql .= "         ,nl.cod_empenho as cod_empenho_relatorio                                                                    \n";
        $stSql .= "         ,to_char(nl.dt_liquidacao,'dd/mm/yyyy') as dt_liquidacao                                                    \n";
        $stSql .= "         ,op.observacao                                                                                              \n";
        $stSql .= "         ,nl.exercicio_empenho                                                                                       \n";
        $stSql .= "         ,nl.exercicio as exercicio_nota                                                                             \n";
        $stSql .= "         ,op.exercicio as exercicio_ordem                                                                            \n";
        $stSql .= "         ,cg.nom_cgm                                                                                                 \n";
        $stSql .= "         ,CASE WHEN pf.numcgm IS NOT NULL THEN pf.cpf                                                                \n";
        $stSql .= "               ELSE pj.cnpj                                                                                          \n";
        $stSql .= "          END as cpf_cnpj                                                                                            \n";
        $stSql .= "         ,CASE WHEN pf.numcgm IS NOT NULL THEN 'CPF'                                                                 \n";
        $stSql .= "               ELSE 'CNPJ'                                                                                           \n";
        $stSql .= "          END as cpfcnpj                                                                                             \n";
        $stSql .= "         ,re.cod_fonte as cod_recurso                                                                         \n";
        $stSql .= "         ,de.cod_despesa as dotacao_reduzida                                                                         \n";
        $stSql .= "         ,cd.descricao AS nom_conta                                                                                  \n";
        $stSql .= "         ,re.nom_recurso                                                                                             \n";
        $stSql .= "         ,pao.num_pao                                                                                             \n";
        $stSql .= "         ,ppa.acao.num_acao                                                                                          \n";
        $stSql .= "         ,pao.nom_pao                                                                                          \n";
        
        
        $stSql .= "         ,m_b.num_banco                                                                                          \n";
        $stSql .= "         ,m_b.nom_banco                                                                                          \n";
        $stSql .= "         ,m_a.num_agencia                                                                                          \n";
        $stSql .= "         ,m_a.nom_agencia                                                                                          \n";
        $stSql .= "         ,c_fc.num_conta                                                                                           \n";
        // Tem-se que considerar o empenho pois uma OP pode ter empenhos diferentes
        $stSql .= "         ,empenho.fn_consultar_valor_pagamento_anulado_ordem_empenho(nl.exercicio, pl.cod_ordem, em.cod_entidade, em.cod_empenho ) as vl_anulado \n";
        $stSql .= "         ,de.num_orgao                                                                                               \n";
        $stSql .= "             ||'.'||de.num_unidade                                                                                   \n";
        $stSql .= "             ||'.'||de.cod_funcao                                                                                    \n";
        $stSql .= "             ||'.'||de.cod_subfuncao                                                                                 \n";
        $stSql .= "             ||'.'||ppa.programa.num_programa                                                                                  \n";
        $stSql .= "             ||'.'||ppa.acao.num_acao                                                                                       \n";
        $stSql .= "             ||'.'||replace(cd.cod_estrutural,'.','')                                                                \n";
        $stSql .= "             AS dotacao                                                                                              \n";
        $stSql .= "     FROM                                                                                                            \n";
        $stSql .= "          empenho.pagamento_liquidacao as pl                                                                         \n";
        $stSql .= "         ,empenho.ordem_pagamento      as op                                                                         \n";
        $stSql .= "         ,empenho.nota_liquidacao      as nl                                                                         \n";
        $stSql .= "         ,empenho.empenho              as em                                                                         \n";
        $stSql .= "    LEFT JOIN                                                                                                        \n";
        $stSql .= "      orcamento.entidade AS OE                                                                                       \n";
        $stSql .= "    ON (                                                                                                             \n";
        $stSql .= "       OE.COD_ENTIDADE = EM.COD_ENTIDADE AND                                                                         \n";
        $stSql .= "       OE.EXERCICIO    = EM.EXERCICIO       )                                                                        \n";
        $stSql .= "    LEFT JOIN                                                                                                        \n";
        $stSql .= "      sw_cgm AS CGME                                                                                                 \n";
        $stSql .= "    ON (                                                                                                             \n";
        $stSql .= "        CGME.NUMCGM = OE.NUMCGM  )                                                                                   \n";
        $stSql .= "         ,empenho.pre_empenho          as pe                                                                         \n";
        $stSql .= "         ,empenho.pre_empenho_despesa  as pd                                                                         \n";
        $stSql .= "         ,orcamento.despesa            as de                                                                         \n";

        $stSql .= "    JOIN orcamento.programa_ppa_programa                                                                             \n";
        $stSql .= "      ON programa_ppa_programa.cod_programa = de.cod_programa                                                        \n";
        $stSql .= "     AND programa_ppa_programa.exercicio   = de.exercicio                                                            \n";
        $stSql .= "    JOIN ppa.programa                                                                                                \n";
        $stSql .= "      ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa                                          \n";
        $stSql .= "    JOIN orcamento.pao_ppa_acao                                                                                      \n";
        $stSql .= "      ON pao_ppa_acao.num_pao = de.num_pao                                                                           \n";
        $stSql .= "     AND pao_ppa_acao.exercicio = de.exercicio                                                                       \n";
        $stSql .= "    JOIN ppa.acao                                                                                                    \n";
        $stSql .= "      ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao                                                                   \n";
        $stSql .= "            ,orcamento.recurso as re                                                                                 \n";
        $stSql .= "         ,orcamento.pao                as pao                                                                        \n";
        $stSql .= "         ,orcamento.conta_despesa      as cd                                                                         \n";
        $stSql .= "         ,sw_cgm                       as cg                                                                         \n";
        $stSql .= "           LEFT JOIN                                                                                                 \n";
        $stSql .= "           sw_cgm_pessoa_fisica        as pf                                                                         \n";
        $stSql .= "            ON (cg.numcgm = pf.numcgm)                                                                               \n";
        
        $stSql .= "     LEFT JOIN compras.fornecedor_conta as c_fc                                                                      \n";
        $stSql .= "        ON ( c_fc.cgm_fornecedor = cg.numcgm                                                                         \n";
        $stSql .= "        AND  c_fc.padrao = true )                                                                                    \n";
        $stSql .= "                                                                                                                     \n";
        $stSql .= "     LEFT JOIN monetario.agencia AS m_a                                                                              \n";
        $stSql .= "     ON ( c_fc.cod_agencia = m_a.cod_agencia                                                                         \n";
        $stSql .= "         AND   c_fc.cod_banco  = m_a.cod_banco  )                                                                    \n";
        $stSql .= "                                                                                                                     \n";
        $stSql .= "     LEFT JOIN monetario.banco AS m_b                                                                                \n";
        $stSql .= "    ON ( m_a.cod_banco = m_b.cod_banco )                                                                             \n";
        
        $stSql .= "           LEFT JOIN                                                                                                 \n";
        $stSql .= "           sw_cgm_pessoa_juridica     as pj                                                                          \n";
        $stSql .= "            ON (cg.numcgm = pj.numcgm)                                                                               \n";
        $stSql .= "         ,sw_municipio                as mu                                                                          \n";
        $stSql .= "     WHERE   pl.exercicio_liquidacao = nl.exercicio                                                                  \n";
        $stSql .= "     AND     pl.cod_nota             = nl.cod_nota                                                                   \n";
        $stSql .= "     AND     pl.cod_entidade         = nl.cod_entidade                                                               \n";
        $stSql .= "     AND     pl.cod_ordem            = op.cod_ordem                                                                  \n";
        $stSql .= "     AND     pl.exercicio            = op.exercicio                                                                  \n";
        if($this->getDado('exercicio'))
            $stSql .= " AND     op.exercicio            = '".$this->getDado('exercicio')."'                                             \n";
        if($this->getDado('cod_ordem'))
            $stSql .= " AND     op.cod_ordem            = ".$this->getDado('cod_ordem')."                                             \n";
        if($this->getDado('cod_entidade'))
            $stSql .= " AND     op.cod_entidade         = ".$this->getDado('cod_entidade')."                                             \n";
        $stSql .= "     AND     pl.cod_entidade         = op.cod_entidade                                                               \n";
        $stSql .= "     AND     nl.cod_empenho          = em.cod_empenho                                                                \n";
        $stSql .= "     AND     nl.exercicio_empenho    = em.exercicio                                                                  \n";
        $stSql .= "     AND     nl.cod_entidade         = em.cod_entidade                                                               \n";
        $stSql .= "     AND     em.cod_pre_empenho      = pe.cod_pre_empenho                                                            \n";
        $stSql .= "     AND     em.exercicio            = pe.exercicio                                                                  \n";
        $stSql .= "     AND     pe.cod_pre_empenho      = pd.cod_pre_empenho                                                            \n";
        $stSql .= "     AND     pe.exercicio            = pd.exercicio                                                                  \n";
        $stSql .= "     AND     pd.cod_despesa          = de.cod_despesa                                                                \n";
        $stSql .= "     AND     pd.exercicio            = de.exercicio                                                                  \n";
        $stSql .= "     AND     pd.cod_conta            = cd.cod_conta                                                                  \n";
        $stSql .= "     AND     pd.exercicio            = cd.exercicio                                                                  \n";
        $stSql .= "     AND     pe.cgm_beneficiario     = cg.numcgm                                                                     \n";
        $stSql .= "     AND     cg.cod_municipio        = mu.cod_municipio                                                              \n";
        $stSql .= "     AND     cg.cod_uf               = mu.cod_uf                                                                     \n";
        $stSql .= "     --Recurso                                                                                                       \n";
        $stSql .= "     AND     de.cod_recurso      = re.cod_recurso                                                                    \n";
        $stSql .= "     AND     de.exercicio        = re.exercicio                                                                      \n";
        $stSql .= "     --PAO                                                                                                       \n";
        $stSql .= "     AND     de.num_pao         = pao.num_pao                                                                    \n";
        $stSql .= "     AND     de.exercicio        = pao.exercicio                                                                  \n";
        $stSql .= " ) as tabela                                                                                                         \n";
        $stSql .= ",(                                                                                                                   \n";
        $stSql .= "     SELECT valor::integer as cod_uf FROM administracao.configuracao where cod_modulo=2 and parametro='cod_uf' AND  exercicio  = '".Sessao::getExercicio()."'               \n";
        $stSql .= " ) as conf_uf                                                                                                        \n";
        $stSql .= ",(                                                                                                                   \n";
        $stSql .= "     SELECT valor::integer as cod_municipio FROM administracao.configuracao where cod_modulo=2 and parametro='cod_municipio' AND  exercicio  = '".Sessao::getExercicio()."'  \n";
        $stSql .= " ) as conf_municipio                                                                                                 \n";
        $stSql .= " ,sw_municipio as mu                                                                                                 \n";
        $stSql .= " WHERE mu.cod_uf         = conf_uf.cod_uf                                                                            \n";
        $stSql .= " AND   mu.cod_municipio  = conf_municipio.cod_municipio                                                              \n";

        return $stSql;
    }

    /**
        * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaRelatorioRestos.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaRelatorioRestos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRelatorioRestos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql ); 
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelatorioRestos()
    {
        $stSql  = " SELECT                                                                                                  
                            tabela.*                                                                                            
                            ,mu.*                                                                                               
                            ,(select valor 
                                    from administracao.configuracao                                                      
                                    where cod_modulo = 2 
                                    and parametro = 'nom_prefeitura' 
                                    AND  exercicio  = '".$this->getDado('exercicio')."' 
                            ) as nom_prefeitura                         
                            ,CASE WHEN tabela.masc_despesa != '' THEN                                                           
                                    publico.fn_mascara_dinamica( tabela.masc_despesa , dotacao )                                     
                            ELSE                                                                                               
                                    dotacao                                                                                          
                            END AS dotacao_formatada                                                                           
                    FROM(                                                                                                        
                            SELECT                                                                                              
                                    pl.cod_ordem                                                                                   
                                    ,pl.vl_pagamento                                                                                
                                    ,to_char(op.dt_emissao,'yyyy-mm-dd') as dt_emissao                                              
                                    ,lpad(nl.cod_nota::VARCHAR,6,'0') as cod_nota
                                    ,nl.cod_nota as cod_nota_relatorio
                                    ,nl.cod_empenho                                                                                 
                                    ,nl.cod_entidade                                                                                
                                    ,cgme.nom_cgm as nom_entidade                                                                   
                                    ,lpad(nl.cod_empenho::VARCHAR,6,'0') as cod_empenho                                             
                                    ,nl.cod_empenho as cod_empenho_relatorio
                                    ,to_char(nl.dt_liquidacao,'dd/mm/yyyy') as dt_liquidacao                                        
                                    ,op.observacao                                                                                  
                                    ,nl.exercicio_empenho                                                                           
                                    ,nl.exercicio as exercicio_nota                                                                 
                                    ,op.exercicio as exercicio_ordem                                                                
                                    ,cg.nom_cgm                                                                                     
                                    ,CASE WHEN pf.numcgm IS NOT NULL THEN 
                                        pf.cpf                                                    
                                    ELSE 
                                        pj.cnpj                                                                              
                                    END as cpf_cnpj                                                                                
                                    ,CASE WHEN pf.numcgm IS NOT NULL THEN 
                                        'CPF'                                                     
                                    ELSE 
                                        'CNPJ'                                                                               
                                    END as cpfcnpj                                                                                 
                                    ,rpe.recurso  as cod_recurso                                                                   
                                    ,rpe.recurso  as recurso_formatado                                                             
                                    ,'' as nom_recurso                                                                             
                                    ,rpe.num_pao                                                                                   
                                    ,'' as nom_pao                                                                                 
                                    ,m_b.num_banco                                                                                        
                                    ,m_b.nom_banco                                                                                          
                                    ,m_a.num_agencia                                                                                        
                                    ,m_a.nom_agencia                                                                                        
                                    ,c_fc.num_conta                                                                                         
                                    ,conta_despesa.descricao as nom_conta                                                          
                                    ,opa.motivo                                                                                     
                                    ,to_char(opa.timestamp,'dd/mm/yyyy') as dt_anulacao                                             
                                    ,opla.vl_anulado                                                                                
                                    ,rpe.num_orgao                                                                                  
                                        ||'.'||rpe.num_unidade                                                                      
                                        ||'.'||rpe.cod_funcao                                                                       
                                        ||'.'||rpe.cod_subfuncao                                                                    
                                        ||'.'||rpe.cod_programa                                                                     
                                        ||'.'||rpe.num_pao                                                                          
                                        ||'.'||replace(rpe.cod_estrutural,'.','')                                                   
                                    AS dotacao
                                    ,tabela.masc_despesa                                                                          
                            FROM                                                                                                
                                empenho.pagamento_liquidacao as pl                                                           
               
                            LEFT JOIN empenho.ordem_pagamento as op
                                 ON pl.cod_ordem    = op.cod_ordem                                                      
                                AND pl.exercicio    = op.exercicio                                                      
                                AND pl.cod_entidade = op.cod_entidade
                            LEFT JOIN empenho.ordem_pagamento_anulada opa                                                           
                                 ON opa.cod_ordem    = op.cod_ordem                                                        
                                AND opa.exercicio    = op.exercicio                                                        
                                AND opa.cod_entidade = op.cod_entidade                                                     
                            LEFT JOIN empenho.ordem_pagamento_liquidacao_anulada as opla                                          
                                 ON opla.cod_ordem = op.cod_ordem                                                         
                                AND opla.exercicio = op.exercicio                                                         
                                AND opla.cod_entidade = op.cod_entidade                                                   
                            LEFT JOIN empenho.nota_liquidacao as nl
                                 ON pl.exercicio_liquidacao = nl.exercicio                                                      
                                AND pl.cod_nota             = nl.cod_nota                                                       
                                AND pl.cod_entidade         = nl.cod_entidade                                                             
                            LEFT JOIN empenho.empenho as em                                                             
                                 ON nl.cod_empenho          = em.cod_empenho                                                    
                                AND nl.exercicio_empenho    = em.exercicio                                                      
                                AND nl.cod_entidade         = em.cod_entidade
                            LEFT JOIN orcamento.entidade AS OE                                                                           
                                 ON OE.COD_ENTIDADE = EM.COD_ENTIDADE
                                AND OE.EXERCICIO    = EM.EXERCICIO 
                            LEFT JOIN SW_CGM AS CGME                                                                                     
                                 ON CGME.NUMCGM = OE.NUMCGM
                            LEFT JOIN empenho.pre_empenho as pe
                                 ON em.cod_pre_empenho      = pe.cod_pre_empenho                                                
                                AND em.exercicio            = pe.exercicio
                            LEFT JOIN empenho.restos_pre_empenho as rpe
                                 ON pe.cod_pre_empenho      = rpe.cod_pre_empenho                                               
                                AND pe.exercicio            = rpe.exercicio    
                            LEFT JOIN orcamento.conta_despesa                                                                            
                                 ON REPLACE(conta_despesa.cod_estrutural, '.', '')  = rpe.cod_estrutural                               
                                AND conta_despesa.exercicio                         = '".$this->getDado('exercicio')."'                                       
                            LEFT JOIN sw_cgm as cg
                                 ON pe.cgm_beneficiario = cg.numcgm     
                            LEFT JOIN sw_municipio as mu
                                 ON cg.cod_municipio = mu.cod_municipio                                                  
                                AND cg.cod_uf        = mu.cod_uf
                            LEFT JOIN sw_cgm_pessoa_fisica as pf
                                 ON cg.numcgm = pf.numcgm
                            LEFT JOIN sw_cgm_pessoa_juridica as pj
                                 ON cg.numcgm = pj.numcgm                            
                            LEFT JOIN monetario.agencia AS m_a
                                 ON m_a.numcgm_agencia = pj.numcgm
                            LEFT JOIN monetario.banco AS m_b
                                ON m_b.cod_banco = m_a.cod_banco
                            LEFT JOIN compras.fornecedor_conta  as c_fc
                                 ON c_fc.cod_agencia    = m_a.cod_agencia
                                AND c_fc.cod_banco      = m_a.cod_banco ";
            if ( $this->getDado('conta_padrao') ) {
                $stSql .= " AND c_fc.padrao = true ";
            }

        $stSql .= "      ,( SELECT max(valor) AS masc_despesa                                                            
                                    FROM administracao.configuracao                                                              
                                    WHERE  parametro  = 'masc_despesa'                                                           
                                      AND  exercicio  = '".$this->getDado('exercicio')."'                                        
                                      AND  cod_modulo = 8                                                                        
                            ) as tabela
                        ";
        $stSql .= "                   
                    ) as tabela                                                                                         
                    ,(                                                                                                  
                        SELECT  valor as cod_uf 
                                FROM administracao.configuracao 
                                WHERE cod_modulo=2 
                                AND parametro='cod_uf' 
                                AND  exercicio  = '".Sessao::getExercicio()."'              
                    ) as conf_uf                                                                                            
                    ,(                                                                                                      
                        SELECT  valor as cod_municipio 
                                FROM administracao.configuracao 
                                WHERE cod_modulo=2 
                                AND parametro='cod_municipio' 
                                AND  exercicio  = '".Sessao::getExercicio()."'
                    ) as conf_municipio                                                                                     
                    ,sw_municipio as mu
                    
                WHERE mu.cod_uf         = conf_uf.cod_uf::INTEGER                                                        
                AND   mu.cod_municipio  = conf_municipio.cod_municipio::INTEGER  
        ";                                        

        return $stSql;
    }

    /**
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function relatorioOrdensPagamento(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRelatorioOrdensPagamento().$stFiltro." ".$stOrdem;
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRelatorioOrdensPagamento()
    {
        $stSql  = " SELECT * FROM empenho.fn_relatorio_ordens_pagamento( '".$this->getDado('cod_ordem_inicial')."'      \n";
        $stSql .= "                                                     ,'".$this->getDado('cod_ordem_final')."'        \n";
        $stSql .= "                                                     ,'".$this->getDado('cod_empenho_inicial')."'    \n";
        $stSql .= "                                                     ,'".$this->getDado('cod_empenho_final')."'      \n";
        $stSql .= "                                                     ,'".$this->getDado('exercicio_empenho')."'      \n";
        $stSql .= "                                                     ,'".$this->getDado('cod_entidade')."'           \n";
        $stSql .= "                                                     ,'".$this->getDado('cod_recurso')."'            \n";
        $stSql .= "                                                     ,'".$this->getDado('masc_recurso_red')."'       \n";
        $stSql .= "                                                     ,'".$this->getDado('cod_detalhamento')."'       \n";
        $stSql .= "                                                     ,'".$this->getDado('dt_inicial')."'             \n";
        $stSql .= "                                                     ,'".$this->getDado('dt_final')."'               \n";
        $stSql .= "                                                     ,''                                             \n";
        $stSql .= "                                                     ,''                                             \n";
        $stSql .= "                                                     ,'".$this->getDado('numcgm')."'                 \n";
        $stSql .= "                                                     ,'".$this->getDado('situacao')."'               \n";
        $stSql .= "                                                     ,'".$this->getDado('tipo')."'                   \n";
        $stSql .= "                                                    ) as retorno (                                   \n";
        $stSql .= "                                                                  cod_ordem      integer             \n";
        $stSql .= "                                                                 ,dt_emissao     text                \n";
        $stSql .= "                                                                 ,valor          numeric             \n";
        $stSql .= "                                                                 ,valor_pago     numeric             \n";
        $stSql .= "                                                                 ,valor_anulado  numeric             \n";
        $stSql .= "                                                                 ,saldo_op       numeric             \n";
        $stSql .= "                                                                 ,situacao       character varying   \n";
        $stSql .= "                                                                 ,dt_pagamento   text                \n";
        $stSql .= "                                                                 ,dt_anulado     text                \n";
        $stSql .= "                                                                 ,cod_empenho    integer             \n";
        $stSql .= "                                                                 ,dt_empenho     text                \n";
        $stSql .= "                                                                 ,credor         character varying   \n";
        $stSql .= "                                                                 ,nota_fiscal_mg text                \n";
        $stSql .= "                                                    )                                               \n";

        return $stSql;

    }

    public function montaRecuperaRelacionamentoReemitir()
    {
        $stSql = "  SELECT                                                  
                            op.cod_entidade
                            ,op.cod_ordem                                                 
                            ,to_char(op.dt_vencimento,'dd/mm/yyyy') as dt_vencimento
                            ,to_char(oa.timestamp,'dd/mm/yyyy') as dt_anulado       
                            ,oa.timestamp              
                            ,sum(opla.vl_anulado) as valor
                            ,op.exercicio                         
                            ,c.nom_cgm as beneficiario
                            ,pe.implantado
                    FROM                                                               
                         empenho.ordem_pagamento AS op                                 
                    JOIN empenho.ordem_pagamento_anulada AS oa 
                         ON op.cod_ordem = oa.cod_ordem                           
                        AND op.exercicio = oa.exercicio                           
                        AND op.cod_entidade = oa.cod_entidade                     
                    JOIN empenho.ordem_pagamento_liquidacao_anulada as opla 
                         ON opla.exercicio    = oa.exercicio             
                        AND opla.cod_ordem    = oa.cod_ordem             
                        AND opla.cod_entidade = oa.cod_entidade          
                        AND opla.timestamp    = oa.timestamp             
                    JOIN empenho.pagamento_liquidacao AS pl
                         ON op.cod_ordem = pl.cod_ordem                               
                        AND op.exercicio = pl.exercicio                               
                        AND op.cod_entidade = pl.cod_entidade 
                    JOIN empenho.nota_liquidacao AS nl
                         ON pl.exercicio_liquidacao = nl.exercicio                    
                        AND pl.cod_nota = nl.cod_nota                                 
                        AND pl.cod_entidade = nl.cod_entidade                         
                    JOIN empenho.empenho AS ee
                         ON nl.cod_empenho = ee.cod_empenho                           
                        AND nl.exercicio_empenho = ee.exercicio                       
                        AND nl.cod_entidade = ee.cod_entidade 
                    JOIN empenho.pre_empenho AS pe
                         ON ee.exercicio = pe.exercicio
                        AND ee.cod_pre_empenho = pe.cod_pre_empenho
                    JOIN sw_cgm AS c                                                   
                        ON pe.cgm_beneficiario = c.numcgm
                    
                    WHERE 1=1                                                   
            ";
        return $stSql;
    }

    /**
        * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaDadosPagamento.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaRelacionamentoReemitir(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stGrupo  = " \nGROUP BY                              
                            op.cod_entidade
                            ,op.cod_ordem
                            ,to_char(op.dt_vencimento,'dd/mm/yyyy')
                            ,to_char(oa.timestamp,'dd/mm/yyyy')    
                            ,oa.timestamp        
                            ,op.exercicio
                            ,c.nom_cgm
                            ,pe.implantado
                    ";

        $stSql = $this->montaRecuperaRelacionamentoReemitir().$stCondicao.$stGrupo.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaRelacionamentoManutencaoDatas(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRelacionamentoManutencaoDatas().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamentoManutencaoDatas()
    {
        $stSql  = " SELECT                                                                 \n";
        $stSql .= "    op.exercicio,                                                       \n";
        $stSql .= "    op.cod_ordem,                                                       \n";
        $stSql .= "    empenho.retorna_notas(op.exercicio,op.cod_ordem,op.cod_entidade) as exercicio_nota,  \n";
        $stSql .= "    sum(pl.vl_pagamento) as vl_pagamento,                               \n";
        $stSql .= "    to_char(op.dt_emissao ,'dd/mm/yyyy') as dt_emissao                  \n";
        $stSql .= " FROM                                                                   \n";
        $stSql .= "    empenho.empenho             e,                                  \n";
        $stSql .= "    empenho.nota_liquidacao         nl,                             \n";
        $stSql .= "    empenho.pagamento_liquidacao    pl,                             \n";
        $stSql .= "    empenho.ordem_pagamento         op                              \n";
        $stSql .= " WHERE                                                                  \n";
        $stSql .= "    e.cod_empenho       = nl.cod_empenho            AND                 \n";
        $stSql .= "    e.cod_entidade      = nl.cod_entidade           AND                 \n";
        $stSql .= "    e.exercicio         = nl.exercicio_empenho      AND                 \n";
        $stSql .= "                                                                        \n";
        $stSql .= "    nl.exercicio        = pl.exercicio_liquidacao   AND                 \n";
        $stSql .= "    nl.cod_nota         = pl.cod_nota               AND                 \n";
        $stSql .= "    nl.cod_entidade     = pl.cod_entidade           AND                 \n";
        $stSql .= "                                                                        \n";
        $stSql .= "    pl.exercicio        = op.exercicio              AND                 \n";
        $stSql .= "    pl.cod_ordem        = op.cod_ordem              AND                 \n";
        $stSql .= "    pl.cod_entidade     = op.cod_entidade           AND                 \n";
        $stSql .= "                                                                        \n";
        $stSql .= "    e.cod_empenho       = ".$this->getDado('cod_empenho')."    AND    \n";
        $stSql .= "    e.cod_entidade      = ".$this->getDado('cod_entidade')."   AND    \n";
        $stSql .= "    e.exercicio         = '".$this->getDado('exercicio')."'             \n";
        $stSql .= " GROUP BY                                                               \n";
        $stSql .= "    op.exercicio,                                                       \n";
        $stSql .= "    op.cod_ordem,                                                       \n";
        $stSql .= "    op.cod_entidade,                                                    \n";
        $stSql .= "    to_char(op.dt_emissao ,'dd/mm/yyyy')                                \n";

        return $stSql;
    }

    public function recuperaContasPagadoras(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaContasPagadoras();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaContasPagadoras()
    {
        $stSql  ="                                                                                                              \n";
        $stSql .="        SELECT * FROM (                                                                                       \n";
        $stSql .="            SELECT  pl.cod_ordem                                                                              \n";
        $stSql .="                   ,nlcp.cod_plano                                                                            \n";
        $stSql .="                   ,conta.nom_conta                                                                           \n";
        $stSql .="                   ,conta.cod_recurso                                                                         \n";
        $stSql .="                   ,conta.nom_recurso                                                                         \n";
        $stSql .="                   ,(coalesce(sum(nlp.vl_pago),0.00) - coalesce(sum(nlpa.vl_anulado),0.00)) as vl_pago        \n";
        $stSql .="              FROM  empenho.nota_liquidacao as nl                                                             \n";
        $stSql .="                    JOIN empenho.pagamento_liquidacao as pl ON (                                              \n";
        $stSql .="                            nl.exercicio    = pl.exercicio_liquidacao                                         \n";
        $stSql .="                        AND nl.cod_entidade = pl.cod_entidade                                                 \n";
        $stSql .="                        AND nl.cod_nota     = pl.cod_nota                                                     \n";
        $stSql .="                    )                                                                                          \n";
        $stSql .="                    JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp ON (                       \n";
        $stSql .="                            plnlp.exercicio            = pl.exercicio                                          \n";
        $stSql .="                        AND plnlp.cod_entidade         = pl.cod_entidade                                       \n";
        $stSql .="                        AND plnlp.cod_ordem            = pl.cod_ordem                                          \n";
        $stSql .="                        AND plnlp.exercicio_liquidacao = pl.exercicio_liquidacao                               \n";
        $stSql .="                        AND plnlp.cod_nota             = pl.cod_nota                                           \n";
        $stSql .="                    )                                                                                          \n";
        $stSql .="                    JOIN empenho.nota_liquidacao_paga as nlp ON (                                              \n";
        $stSql .="                            nlp.exercicio    = plnlp.exercicio_liquidacao                                      \n";
        $stSql .="                        AND nlp.cod_entidade = plnlp.cod_entidade                                              \n";
        $stSql .="                        AND nlp.cod_nota     = plnlp.cod_nota                                                  \n";
        $stSql .="                        AND nlp.timestamp    = plnlp.timestamp                                                 \n";
        $stSql .="                    )                                                                                          \n";
        $stSql .="                    LEFT JOIN (                                                                                \n";
        $stSql .="                            SELECT exercicio, cod_entidade, cod_nota, timestamp,                               \n";
        $stSql .="                                   coalesce(sum(vl_anulado),0.00) as vl_anulado                                \n";
        $stSql .="                              FROM empenho.nota_liquidacao_paga_anulada                                        \n";
    if($this->getDado('cod_nota'))
           $stSql .= "                          WHERE cod_nota IN (".$this->getDado('cod_nota').")                              \n";
        $stSql .="                          GROUP BY                                                                             \n";
        $stSql .="                             exercicio, cod_entidade, cod_nota, timestamp                                      \n";
        $stSql .="                     ) AS nlpa ON (                                                                            \n";
        $stSql .="                             nlpa.exercicio    = nlp.exercicio                                                 \n";
        $stSql .="                         AND nlpa.cod_entidade = nlp.cod_entidade                                              \n";
        $stSql .="                         AND nlpa.cod_nota     = nlp.cod_nota                                                   \n";
        $stSql .="                         AND nlpa.timestamp    = nlp.timestamp                                                  \n";
        $stSql .="                     )                                                                                          \n";
        $stSql .="                     JOIN empenho.nota_liquidacao_conta_pagadora AS nlcp ON (                                   \n";
        $stSql .="                             nlcp.exercicio_liquidacao = nlp.exercicio                                          \n";
        $stSql .="                         AND nlcp.cod_entidade         = nlp.cod_entidade                                       \n";
        $stSql .="                         AND nlcp.cod_nota             = nlp.cod_nota                                           \n";
        $stSql .="                         AND nlcp.timestamp            = nlp.timestamp                                          \n";
        $stSql .="                     )                                                                                          \n";
        $stSql .="                     JOIN ( SELECT   pa.exercicio                                                               \n";
        $stSql .="                                    ,pa.cod_plano                                                               \n";
        $stSql .="                                    ,pc.nom_conta                                                               \n";
        $stSql .="                                    ,rec.cod_recurso                                                             \n";
        $stSql .="                                    ,rec.nom_recurso                                                            \n";
        $stSql .="                              FROM   contabilidade.plano_analitica as pa                                        \n";
        $stSql .="                                    ,contabilidade.plano_conta as pc                                            \n";
        $stSql .="                                    ,contabilidade.plano_recurso as pr                                          \n";
        $stSql .="                                    ,orcamento.recurso as rec                                                   \n";
        $stSql .="                             WHERE   pa.cod_conta = pc.cod_conta                                                \n";
        $stSql .="                                 AND pa.exercicio = pc.exercicio                                                \n";
        $stSql .="                                 AND pa.cod_plano = pr.cod_plano                                                \n";
        $stSql .="                                 AND pa.exercicio = pr.exercicio                                                \n";
        $stSql .="                                 AND pr.cod_recurso = rec.cod_recurso                                           \n";
        $stSql .="                                 AND pr.exercicio   = rec.exercicio                                             \n";
        $stSql .="                           ) AS conta ON (                                                                      \n";
        $stSql .="                             conta.cod_plano = nlcp.cod_plano                                                   \n";
        $stSql .="                         AND conta.exercicio = nlcp.exercicio                                                   \n";
        $stSql .="                     )                                                                                          \n";
        $stSql .="              WHERE      pl.cod_ordem         = ".$this->getDado('cod_ordem')."                                 \n";
        $stSql .="                     AND pl.exercicio         = '".$this->getDado('exercicio')."'                               \n";
        $stSql .="                     AND pl.cod_entidade      = ".$this->getDado('cod_entidade')."                              \n";
        $stSql .="                     AND nl.exercicio_empenho = '".$this->getDado('exercicio_empenho')."'                       \n";
    if($this->getDado('cod_nota'))
            $stSql .= "                AND pl.cod_nota IN (".$this->getDado('cod_nota').")                                        \n";
        $stSql .="   GROUP BY                                                                                                     \n";
        $stSql .="                    pl.cod_ordem                                                                                \n";
        $stSql .="                   ,nlcp.cod_plano                                                                              \n";
        $stSql .="                   ,conta.nom_conta                                                                             \n";
        $stSql .="                   ,conta.cod_recurso                                                                           \n";
        $stSql .="                   ,conta.nom_recurso                                                                           \n";
        $stSql .="             ORDER BY nlcp.cod_plano                                                                            \n";
        $stSql .="        ) as tbl WHERE vl_pago > 0.00                                                                           \n";

        return $stSql;
    }

    public function recuperaRetencoes(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRetencoes();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRetencoes()
    {
         $stSql  ="  SELECT opr.exercicio                                                       \n";
         $stSql .="        ,opr.cod_ordem                                                       \n";
         $stSql .="        ,opr.cod_entidade                                                    \n";
         $stSql .="        ,opr.cod_plano                                                       \n";
         $stSql .="        ,pc.nom_conta                                                        \n";
         $stSql .="        ,ocr.nom_conta_receita                                               \n";
         $stSql .="        ,opr.cod_receita                                                     \n";
         $stSql .="        ,opr.vl_retencao                                                     \n";
         $stSql .="        ,arrec.cod_arrecadacao                                               \n";
         $stSql .="        ,arrec.timestamp_arrecadacao                                         \n";
         $stSql .="        ,transf.cod_lote                                                     \n";
         $stSql .="        ,transf.tipo as tipo_transf                                          \n";
         $stSql .="        ,CASE WHEN OPR.cod_receita is not null                               \n";
         $stSql .="             THEN 'O'                                                        \n";
         $stSql .="             ELSE 'E'                                                        \n";
         $stSql .="         END AS tipo                                                         \n";
         $stSql .="        ,opr.sequencial                                                      \n";
         $stSql .="        ,e_op_re.cod_recibo_extra                                            \n";
         $stSql .="        ,e_op_re.tipo_recibo                                                 \n";
         $stSql .="   FROM empenho.ordem_pagamento_retencao as OPR                              \n";
         $stSql .="        JOIN contabilidade.plano_analitica as PA                             \n";
         $stSql .="        ON (     pa.cod_plano = opr.cod_plano                                \n";
         $stSql .="             AND pa.exercicio = opr.exercicio )                              \n";
         $stSql .="        JOIN contabilidade.plano_conta as PC                                 \n";
         $stSql .="        ON (     pa.cod_conta = pc.cod_conta                                 \n";
         $stSql .="             AND pa.exercicio = pc.exercicio )                               \n";
         $stSql .="        LEFT JOIN (                                                          \n";
         $stSql .="            SELECT rec.cod_receita                                           \n";
         $stSql .="                   ,ocr.cod_estrutural                                       \n";
         if (Sessao::getExercicio() > 2012) {
            $stSql .="                   ,configuracao_lancamento_receita.cod_conta                \n";
         }
         $stSql .="                   ,ocr.exercicio                                            \n";
         $stSql .="                   ,ocr.descricao AS nom_conta_receita                       \n";
         $stSql .="              FROM orcamento.conta_receita as OCR                            \n";
         $stSql .="                   JOIN orcamento.receita as rec                             \n";
         $stSql .="                   ON (     rec.cod_conta = ocr.cod_conta                    \n";
         $stSql .="                        AND rec.exercicio = ocr.exercicio )                  \n";
         if (Sessao::getExercicio() > 2012) {
             $stSql .="                    join contabilidade.configuracao_lancamento_receita       \n";
             $stSql .="                      on configuracao_lancamento_receita.cod_conta_receita = OCR.cod_conta \n";
             $stSql .="                     and configuracao_lancamento_receita.exercicio = OCR.exercicio \n";
             $stSql .="               LEFT JOIN contabilidade.desdobramento_receita \n";
             $stSql .="                      ON desdobramento_receita.cod_receita_secundaria = rec.cod_receita \n";
             $stSql .="                     AND desdobramento_receita.exercicio   = rec.exercicio \n";
         }
         $stSql .="             WHERE ocr.exercicio = '".$this->getDado('exercicio')."'         \n";
         $stSql .="                   AND rec.cod_entidade = ".$this->getDado('cod_entidade')." \n";
         if (Sessao::getExercicio() > 2012) {
            $stSql .="                   AND configuracao_lancamento_receita.estorno = 'f'         \n";
            $stSql .="                   AND desdobramento_receita.cod_receita_secundaria IS NULL  \n";
         }
         $stSql .="        ) as ocr                                                             \n";
         if (Sessao::getExercicio() > 2012) {
             $stSql .="        ON (     ocr.exercicio = pc.exercicio                     \n";
             $stSql .="             AND ocr.cod_receita = OPR.cod_receita  )                  \n";

         } else {
             $stSql .="        ON (     '4.'||ocr.cod_estrutural = pc.cod_estrutural                \n";
             $stSql .="             AND ocr.exercicio            = pc.exercicio  )                  \n";
         }
         $stSql .="        LEFT JOIN (  SELECT  aopr.cod_arrecadacao                                                                        \n";
         $stSql .="                            ,aopr.timestamp_arrecadacao                                                                  \n";
         $stSql .="                            ,aopr.cod_plano                                                                              \n";
         $stSql .="                            ,aopr.exercicio                                                                              \n";
         $stSql .="                            ,aopr.cod_ordem                                                                              \n";
         $stSql .="                            ,aopr.cod_entidade                                                                           \n";
         $stSql .="                            ,aopr.sequencial                                                                             \n";
         $stSql .="                       FROM tesouraria.arrecadacao_ordem_pagamento_retencao as aopr                                      \n";
         $stSql .="                      WHERE  aopr.cod_entidade = ".$this->getDado('cod_entidade')."                                      \n";
         $stSql .="                         AND aopr.cod_ordem = ".$this->getDado('cod_ordem')."                                            \n";
         $stSql .="                         AND aopr.exercicio = '".$this->getDado('exercicio')."'                                          \n";
         $stSql .="                         AND NOT EXISTS ( SELECT cod_arrecadacao                                                         \n";
         $stSql .="                                            FROM tesouraria.arrecadacao_estornada_ordem_pagamento_retencao as aeopr      \n";
         $stSql .="                                           WHERE     aopr.cod_arrecadacao       = aeopr.cod_arrecadacao                      \n";
         $stSql .="                                                 AND aopr.timestamp_arrecadacao = aeopr.timestamp_arrecadacao                \n";
         $stSql .="                                                 AND aopr.cod_plano             = aeopr.cod_plano                            \n";
         $stSql .="                                                 AND aopr.exercicio             = aeopr.exercicio                            \n";
         $stSql .="                                                 AND aopr.cod_ordem             = aeopr.cod_ordem                            \n";
         $stSql .="                                                 AND aopr.cod_entidade          = aeopr.cod_entidade                         \n";
         $stSql .="                         )                                                                                               \n";
         $stSql .="        ) as arrec ON (     arrec.cod_plano    =  opr.cod_plano                                                          \n";
         $stSql .="                        AND arrec.exercicio    =  opr.exercicio                                                          \n";
         $stSql .="                        AND arrec.cod_ordem    =  opr.cod_ordem                                                          \n";
         $stSql .="                        AND arrec.cod_entidade =  opr.cod_entidade                                                       \n";
         $stSql .="                        AND arrec.sequencial   =  opr.sequencial                                                         \n";
         $stSql .="        )                                                                                                                \n";
         $stSql .="        LEFT JOIN (  SELECT  topr.cod_lote                                                                               \n";
         $stSql .="                            ,topr.tipo                                                                                   \n";
         $stSql .="                            ,topr.cod_plano                                                                              \n";
         $stSql .="                            ,topr.exercicio                                                                              \n";
         $stSql .="                            ,topr.cod_ordem                                                                              \n";
         $stSql .="                            ,topr.cod_entidade                                                                           \n";
         $stSql .="                       FROM tesouraria.transferencia_ordem_pagamento_retencao as topr                                    \n";
         $stSql .="                      WHERE  topr.cod_entidade = ".$this->getDado('cod_entidade')."                                      \n";
         $stSql .="                         AND topr.cod_ordem = ".$this->getDado('cod_ordem')."                                            \n";
         $stSql .="                         AND topr.exercicio = '".$this->getDado('exercicio')."'                                          \n";
         $stSql .="                         AND NOT EXISTS ( SELECT teopr.cod_lote_estorno                                                 \n";
         $stSql .="                                            FROM tesouraria.transferencia_estornada_ordem_pagamento_retencao as teopr   \n";
         $stSql .="                                           WHERE     topr.cod_lote              = teopr.cod_lote                        \n";
         $stSql .="                                                 AND topr.tipo                  = teopr.tipo                            \n";
         $stSql .="                                                 AND topr.cod_plano             = teopr.cod_plano                       \n";
         $stSql .="                                                 AND topr.exercicio             = teopr.exercicio                       \n";
         $stSql .="                                                 AND topr.cod_ordem             = teopr.cod_ordem                       \n";
         $stSql .="                                                 AND topr.cod_entidade          = teopr.cod_entidade                    \n";
         $stSql .="                         )                                                                                              \n";
         $stSql .="        ) as transf ON (    transf.cod_plano    =  opr.cod_plano                                                        \n";
         $stSql .="                        AND transf.exercicio    =  opr.exercicio                                                        \n";
         $stSql .="                        AND transf.cod_ordem    =  opr.cod_ordem                                                        \n";
         $stSql .="                        AND transf.cod_entidade =  opr.cod_entidade                                                     \n";
         $stSql .="        )                                                                       \n";
         $stSql .="        left JOIN(                                                              \n";
         $stSql .="         select t_re.exercicio                                                  \n";
         $stSql .="              , t_re.cod_entidade                                               \n";
         $stSql .="              , t_re.cod_recibo_extra                                           \n";
         $stSql .="              , t_re.tipo_recibo                                                \n";
         $stSql .="              , t_re.cod_plano                                                  \n";
         $stSql .="              , e_opre.cod_ordem                                                \n";
         $stSql .="           from tesouraria.recibo_extra as t_re                                 \n";
         $stSql .="          right join empenho.ordem_pagamento_recibo_extra as e_opre             \n";
         $stSql .="             on (e_opre.exercicio = t_re.exercicio                              \n";
         $stSql .="            AND  e_opre.cod_entidade = t_re.cod_entidade                        \n";
         $stSql .="            AND  e_opre.cod_recibo_extra = t_re.cod_recibo_extra                \n";
         $stSql .="            AND  e_opre.tipo_recibo = t_re.tipo_recibo)                         \n";
         $stSql .="          WHERE  e_opre.cod_ordem         = ".$this->getDado('cod_ordem')."     \n";
         $stSql .="            AND  e_opre.exercicio         = '".$this->getDado('exercicio')."'   \n";
         $stSql .="            AND  e_opre.cod_entidade      = ".$this->getDado('cod_entidade')."  \n";
         $stSql .="        ) as e_op_re ON (opr.cod_ordem         = e_op_re.cod_ordem              \n";
         $stSql .="                AND  opr.exercicio         = e_op_re.exercicio                  \n";
         $stSql .="                AND  opr.cod_entidade      = e_op_re.cod_entidade               \n";
         $stSql .="                AND  opr.cod_plano      = e_op_re.cod_plano                     \n";
         $stSql .="        )                                                                       \n";
         $stSql .="  WHERE  opr.cod_ordem         = ".$this->getDado('cod_ordem')."                \n";
         $stSql .="     AND opr.exercicio         = '".$this->getDado('exercicio')."'              \n";
         $stSql .="     AND opr.cod_entidade      = ".$this->getDado('cod_entidade')."             \n";

         return $stSql;
    }

    public function verificaAdiantamento(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaVerificaAdiantamento();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaVerificaAdiantamento()
    {
        $stSql  = "SELECT                                                                      \n";
        $stSql .= "       empenho.verifica_adiantamento('".$this->getDado("exercicio")."'      \n";
        $stSql .= "                                     ,".$this->getDado("cod_ordem")."       \n";
        $stSql .= "                                     ,".$this->getDado("cod_entidade")."    \n";
        $stSql .= "                                    ) as adiantamento                       \n";

        return $stSql;

    }

    public function montaRecuperaListaChequesOrdemPagamento()
    {
        $stSql  = "\n    SELECT cheque_emissao.cod_agencia";
        $stSql .= "\n         , cheque_emissao.cod_banco";
        $stSql .= "\n         , cheque_emissao.cod_conta_corrente";
        $stSql .= "\n         , cheque_emissao.num_cheque";
        $stSql .= "\n         , to_char(cheque_emissao.data_emissao, 'dd/mm/yyyy') AS data_emissao";
        $stSql .= "\n         , to_real(cheque_emissao.valor) AS valor";
        $stSql .= "\n         , (CASE";
        $stSql .= "\n               WHEN cheque_emissao_anulada.data_anulacao IS NULL THEN 'Emitido'";
        $stSql .= "\n               ELSE 'Anulado'";
        $stSql .= "\n            END) AS status";
        $stSql .= "\n         , conta_corrente.banco";
        $stSql .= "\n         , conta_corrente.agencia";
        $stSql .= "\n         , conta_corrente.conta_corrente";
        $stSql .= "\n      FROM empenho.ordem_pagamento";
        $stSql .= "\nINNER JOIN tesouraria.cheque_emissao_ordem_pagamento";
        $stSql .= "\n        ON cheque_emissao_ordem_pagamento.cod_ordem    = ordem_pagamento.cod_ordem";
        $stSql .= "\n       AND cheque_emissao_ordem_pagamento.exercicio    = ordem_pagamento.exercicio";
        $stSql .= "\n       AND cheque_emissao_ordem_pagamento.cod_entidade = ordem_pagamento.cod_entidade";
        $stSql .= "\nINNER JOIN tesouraria.cheque_emissao";
        $stSql .= "\n        ON cheque_emissao.cod_agencia        = cheque_emissao_ordem_pagamento.cod_agencia";
        $stSql .= "\n       AND cheque_emissao.cod_banco 	      = cheque_emissao_ordem_pagamento.cod_banco";
        $stSql .= "\n       AND cheque_emissao.cod_conta_corrente = cheque_emissao_ordem_pagamento.cod_conta_corrente";
        $stSql .= "\n       AND cheque_emissao.num_cheque 	      = cheque_emissao_ordem_pagamento.num_cheque";
        $stSql .= "\n       AND cheque_emissao.timestamp_emissao  = cheque_emissao_ordem_pagamento.timestamp_emissao";
        $stSql .= "\n LEFT JOIN tesouraria.cheque_emissao_anulada";
        $stSql .= "\n        ON cheque_emissao_anulada.cod_agencia        = cheque_emissao.cod_agencia";
        $stSql .= "\n       AND cheque_emissao_anulada.cod_banco 	      = cheque_emissao.cod_banco";
        $stSql .= "\n       AND cheque_emissao_anulada.cod_conta_corrente = cheque_emissao.cod_conta_corrente";
        $stSql .= "\n       AND cheque_emissao_anulada.num_cheque 	      = cheque_emissao.num_cheque";
        $stSql .= "\n       AND cheque_emissao_anulada.timestamp_emissao  = cheque_emissao.timestamp_emissao";
        $stSql .= "\nINNER JOIN (";
        $stSql .= "\n                SELECT conta_corrente.num_conta_corrente AS conta_corrente";
        $stSql .= "\n                 , agencia.num_agencia||' - '||agencia.nom_agencia AS agencia";
        $stSql .= "\n                 , banco.num_banco||' - '||banco.nom_banco AS banco";
        $stSql .= "\n                 , conta_corrente.cod_conta_corrente";
        $stSql .= "\n                 , conta_corrente.cod_agencia";
        $stSql .= "\n                 , conta_corrente.cod_banco";
        $stSql .= "\n                      FROM monetario.conta_corrente";
        $stSql .= "\n            INNER JOIN monetario.agencia";
        $stSql .= "\n                ON agencia.cod_agencia = conta_corrente.cod_agencia";
        $stSql .= "\n               AND agencia.cod_banco   = conta_corrente.cod_banco";
        $stSql .= "\n            INNER JOIN monetario.banco";
        $stSql .= "\n                ON banco.cod_banco = agencia.cod_banco";
        $stSql .= "\n           ) AS conta_corrente";
        $stSql .= "\n        ON conta_corrente.cod_agencia 	          = cheque_emissao.cod_agencia";
        $stSql .= "\n           AND conta_corrente.cod_banco  	      = cheque_emissao.cod_banco";
        $stSql .= "\n           AND conta_corrente.cod_conta_corrente = cheque_emissao.cod_conta_corrente";
        $stSql .= "\n     WHERE";

        if ($this->getDado('cod_ordem')) {
            $stSql .= " ordem_pagamento.cod_ordem    = ".$this->getDado('cod_ordem')." AND ";
        }

        if ($this->getDado('exercicio')) {
            $stSql .= " ordem_pagamento.exercicio    = '".$this->getDado('exercicio')."' AND ";
        }

        if ($this->getDado('cod_entidade')) {
            $stSql .= " ordem_pagamento.cod_entidade = ".$this->getDado('cod_entidade')." AND ";
        }

        return substr($stSql, 0, (strlen($stSql)-5));
    }

    public function recuperaListaChequesOrdemPagamento(&$rsRecordSet, $stFiltro='', $stOrder='', $boTransacao=false)
    {
        $this->executaRecupera('montaRecuperaListaChequesOrdemPagamento', $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }


    public function recuperaDadosPagamentoBorderoContaRecurso(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosPagamentoBorderoContaRecurso().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosPagamentoBorderoContaRecurso()
    {
        $stSql  = " SELECT * FROM                                                                                                                        
         (    SELECT                                                                                                                          
                     EOP.COD_ORDEM,                                                                                                           
                     EMP.EXERCICIO_EMPENHO,                                                                                                   
                     EOP.EXERCICIO,                                                                                                           
                     EOP.COD_ENTIDADE,                                                                                                        
                     TO_CHAR(EOP.DT_VENCIMENTO, 'dd/mm/yyyy') AS DT_VENCIMENTO,                                                               
                     TO_CHAR(EOP.DT_EMISSAO, 'dd/mm/yyyy') AS DT_EMISSAO,                                                                     
                     CGMEMP.NOM_CGM AS BENEFICIARIO ,                                                                                            
                     EMPENHO.fn_consultar_valor_pagamento_ordem(eop.EXERCICIO,eop.COD_ORDEM,eop.COD_ENTIDADE) AS VALOR_PAGAMENTO,             
                     coalesce(eopa.vl_anulado,0.00) as vl_anulado,                                                                            
                     EMP.CGM_BENEFICIARIO,                                                                                                    
                     coalesce(sum(emp.vl_pago_nota),0.00) as vl_pago_nota,                                                                    
                     replace(empenho.retorna_notas_empenhos(eop.exercicio,eop.cod_ordem,eop.cod_entidade),'','<br>') as nota_empenho,         
                     EMP.implantado                                                                                                           
                FROM                                                                                                                          
                     EMPENHO.ORDEM_PAGAMENTO AS EOP                                                                                           
                     LEFT JOIN (                                                                                                              
                             SELECT opa.cod_ordem                                                                                             
                                   ,opa.exercicio                                                                                             
                                   ,opa.cod_entidade                                                                                          
                                   ,coalesce(sum(opla.vl_anulado),0.00) as vl_anulado                                                         
                             FROM  EMPENHO.ORDEM_PAGAMENTO_ANULADA AS OPA                                                                     
                                   JOIN empenho.ordem_pagamento_liquidacao_anulada as opla                                                    
                                   ON (    opa.exercicio    = opla.exercicio                                                                  
                                       AND opa.cod_ordem    = opla.cod_ordem                                                                  
                                       AND opa.cod_entidade = opla.cod_entidade                                                               
                                       AND opa.timestamp    = opla.timestamp                                                                  
                                   )                                                                                                          
                          GROUP BY opa.cod_ordem, opa.exercicio, opa.cod_entidade                                                             
                     ) as EOPA ON (  eopa.cod_ordem = eop.cod_ordem                                                                           
                                 AND eopa.exercicio = eop.exercicio                                                                           
                                 AND eopa.cod_entidade = eop.cod_entidade                                                                     
                     )                                                                                                                        
                 LEFT JOIN                                                                                                                    
                     (                                                                                                                        
                     SELECT                                                                                                                   
                         PL.COD_ORDEM,                                                                                                        
                         PL.EXERCICIO,                                                                                                        
                         PL.COD_ENTIDADE,                                                                                                     
                         PE.CGM_BENEFICIARIO,                                                                                                 
                         PE.IMPLANTADO,                                                                                                       
                         NL.EXERCICIO_EMPENHO,                                                                                                
                         NL.COD_EMPENHO,                                                                                                      
                         NL.COD_NOTA,
                         PE.cod_pre_empenho,                                                                                                         
                         sum(NLP.vl_pago) as vl_pago_nota                                                                                     
                     FROM                                                                                                                     
                         EMPENHO.PAGAMENTO_LIQUIDACAO    as PL,                                                                               
                         EMPENHO.NOTA_LIQUIDACAO         as NL                                                                                
                         LEFT JOIN (                                                                                                          
                                  SELECT nlp.exercicio                                                                                        
                                        ,nlp.cod_entidade                                                                                     
                                        ,nlp.cod_nota                                                                                         
                                        ,nlp.timestamp                                                                                        
                                        ,coalesce(sum(nlp.vl_pago),0.00) - coalesce(sum(nlp.vl_anulado),0.00) as vl_pago                      
                                    FROM (SELECT  cod_nota                                                                                    
                                                 ,cod_entidade                                                                                
                                                 ,exercicio                                                                                   
                                                 ,timestamp                                                                                   
                                                 ,sum(vl_pago) as vl_pago                                                                     
                                                 ,0.00 as vl_anulado                                                                          
                                          FROM empenho.nota_liquidacao_paga                                                                   
                                         GROUP BY cod_nota, timestamp, cod_entidade, exercicio, vl_anulado                                    
                                                                                                                                              
                                    UNION                                                                                                     
                                                                                                                                              
                                          SELECT cod_nota                                                                                     
                                                ,cod_entidade                                                                                 
                                                ,exercicio                                                                                    
                                                ,timestamp                                                                                    
                                                ,0.00 as vl_pago                                                                              
                                                ,sum(vl_anulado) as vl_anulado                                                                
                                          FROM  empenho.nota_liquidacao_paga_anulada                                                          
                                         GROUP BY cod_nota, timestamp, cod_entidade, exercicio, vl_pago                                       
                                        ) as NLP                                                                                              
                                 GROUP BY nlp.exercicio, nlp.timestamp, nlp.cod_entidade, nlp.cod_nota                                        
                         ) as NLP ON (   nlp.cod_nota = nl.cod_nota                                                                           
                                     AND nlp.exercicio = nl.exercicio                                                                         
                                     AND nlp.cod_entidade = nl.cod_entidade                                                                   
                         ),                                                                                                                   
                         EMPENHO.EMPENHO                 as E,                                                                                
                         EMPENHO.PRE_EMPENHO             as PE                                                                               
                     WHERE                                                                                                                    
                         PL.COD_NOTA             = NL.COD_NOTA       AND                                                                      
                         PL.EXERCICIO_LIQUIDACAO = NL.EXERCICIO      AND                                                                      
                         PL.COD_ENTIDADE         = NL.COD_ENTIDADE   AND     
        ";
        
        if($this->getDado('cod_ordem'))
            $stSql .= "             pl.cod_ordem = ".$this->getDado('cod_ordem')." AND ";                                                                  
        
        if($this->getDado('exercicio'))
            $stSql .= "             pl.exercicio = '".$this->getDado('exercicio')."' AND ";                                                                
        
        if($this->getDado('cod_entidade'))
            $stSql .= "             pl.cod_entidade = ".$this->getDado('cod_entidade')." AND ";
        
        $stSql .= "                                                                                                                                      
                         NL.COD_EMPENHO          = E.COD_EMPENHO     AND                                                                      
                         NL.EXERCICIO_EMPENHO    = E.EXERCICIO       AND                                                                      
                         NL.COD_ENTIDADE         = E.COD_ENTIDADE    AND                                                                      
                                                                                                                                              
                         E.COD_PRE_EMPENHO       = PE.COD_PRE_EMPENHO    AND                                                                  
                         E.EXERCICIO             = PE.EXERCICIO                                                                            
                                                                                                                                              
                 GROUP BY                                                                                                                     
                         PL.COD_ORDEM,                                                                                                        
                         PL.EXERCICIO,                                                                                                        
                         PL.COD_ENTIDADE,                                                                                                     
                         PE.CGM_BENEFICIARIO,                                                                                                 
                         PE.IMPLANTADO,                                                                                                       
                         NL.EXERCICIO_EMPENHO,                                                                                                
                         NL.COD_EMPENHO,                                                                                                      
                         NL.COD_NOTA,
                         PE.cod_pre_empenho                                                                                                          
                                                                                                                                              
                 ) AS EMP ON (                                                                                                                
                     EOP.COD_ORDEM       = EMP.COD_ORDEM AND                                                                                  
                     EOP.EXERCICIO       = EMP.EXERCICIO AND                                                                                  
                     EOP.COD_ENTIDADE    = EMP.COD_ENTIDADE                                                                                   
                 )                                                                                                                            
                 LEFT JOIN                                                                                                                    
                     ORCAMENTO.ENTIDADE AS OE                                                                                                 
                 ON                                                                                                                           
                   ( OE.COD_ENTIDADE = EOP.COD_ENTIDADE                                                                                       
                 AND OE.EXERCICIO    = EOP.EXERCICIO    )                                                                                     
                 
                LEFT JOIN SW_CGM as CGMEMP 
                    ON CGMEMP.NUMCGM = EMP.CGM_BENEFICIARIO                                                          
                
                JOIN empenho.pre_empenho_despesa
                    ON pre_empenho_despesa.exercicio = EMP.exercicio
                    AND pre_empenho_despesa.cod_pre_empenho = EMP.cod_pre_empenho
                
                JOIN orcamento.despesa
                    ON despesa.exercicio        = pre_empenho_despesa.exercicio
                    AND despesa.cod_despesa     = pre_empenho_despesa.cod_despesa

             WHERE eop.cod_ordem is not null   
        ";                                                                                               
        if($this->getDado('cod_ordem'))
            $stSql .= "     AND EOP.cod_ordem = ".$this->getDado('cod_ordem');
        if($this->getDado('exercicio'))
            $stSql .= "     AND eop.exercicio = '".$this->getDado('exercicio')."'";
        if($this->getDado('cod_entidade'))
            $stSql .= "     AND eop.cod_entidade = ".$this->getDado('cod_entidade');
        if($this->getDado('cod_recurso'))
            $stSql .= "    and despesa.cod_recurso = ".$this->getDado('cod_recurso');

        $stSql .= "                                                                                                                                      
             GROUP BY eop.exercicio,eop.dt_vencimento,eop.dt_emissao,emp.exercicio_empenho,eop.COD_ORDEM,eop.COD_ENTIDADE,EMP.CGM_BENEFICIARIO,CGMEMP.nom_cgm,VALOR_PAGAMENTO,EMP.implantado,eopa.vl_anulado 
                                                                                                                                              
             ORDER BY eop.cod_ordem                                                                                                           
         ) as tbl                                                                                                                             
                                                                                                                                              
         where (valor_pagamento - vl_anulado ) > vl_pago_nota                                                                                 
        ";

        return $stSql;
    }



}
