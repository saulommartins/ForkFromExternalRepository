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
    * Classe de mapeamento da tabela EMPENHO.ORDEM_PAGAMENTO_LIQUIDACAO_ANULADA
    * Data de Criação: 21/08/2006

    * @author Analista: Diego Victoria
    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TEmpenhoOrdemPagamentoLiquidacaoAnulada.class.php 64368 2016-01-28 12:04:02Z franver $

    $Revision: 30668 $
    $Name$
    $Author: tonismar $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.03.05
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  EMPENHO.ORDEM_PAGAMENTO_LIQUIDACAO_ANULADA
  * Data de Criação: 21/08/2006

  * @author Analista: Diego Victoria
  * @author Analista: Cleisson Barbosa
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoOrdemPagamentoLiquidacaoAnulada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoOrdemPagamentoLiquidacaoAnulada()
{
    parent::Persistente();
    $this->setTabela('empenho.ordem_pagamento_liquidacao_anulada');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_entidade,cod_ordem,exercicio_liquidacao,cod_nota,timestamp');

    $this->AddCampo('exercicio'           ,'varchar'  ,true ,'4'   ,true ,true );
    $this->AddCampo('cod_entidade'        ,'integer'  ,true ,''     ,true ,true );
    $this->AddCampo('cod_ordem'           ,'integer'  ,true ,''     ,true ,true );
    $this->AddCampo('exercicio_liquidacao','varchar'  ,true ,'4'     ,true ,true );
    $this->AddCampo('cod_nota'            ,'integer'  ,true ,''     ,true ,true );
    $this->AddCampo('timestamp'           ,'timestamp',true ,''     ,true ,true );
    $this->AddCampo('vl_anulado'          ,'numeric'  ,true ,'14,2',false,false);
}

function recuperaValorAnular(&$rsRecordSet, $boTransacao='')
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaValorAnular();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
/*
Na função acima, quando a nota tinha mais de um pagamento replicava os registros no retorno.
Quando o pagamento da nota tinha mais de um estorno trazia os valores errados.
Faltava levar a tabela pagamento_liquidacao_nota_liquidacao_paga para o agrupamento,
e faltava ligar a chave do timestamp entre e nota_liquidacao_paga e a pagamento_liquidacao_nota_liquidacao_paga
*/
function montaRecuperaValorAnular()
{
    $stSql  = " select  pl.cod_nota                                                                                                                                     \n";
    $stSql .= "        ,pl.exercicio_liquidacao                                                                                                                         \n";
    $stSql .= "        ,coalesce(pl.vl_pagamento     ,0) as vl_pagamento                                                                                                \n";
    $stSql .= "        ,coalesce(opla.vl_anulado     ,0) as vl_pagamento_anulado                                                                                        \n";
    $stSql .= "        ,coalesce(pagamento.vl_pago   ,0) as vl_pago                                                                                                     \n";
    $stSql .= "        ,coalesce(pagamento.vl_anulado,0) as vl_pago_anulado                                                                                             \n";
    $stSql .= "        ,(coalesce(pl.vl_pagamento,0)-coalesce(opla.vl_anulado,0)) - (coalesce(pagamento.vl_pago,0)-coalesce(pagamento.vl_anulado,0)) as vl_a_anular     \n";
    $stSql .= " from empenho.pagamento_liquidacao as pl                                                                                                                 \n";
    $stSql .= "      left join                                                                                                                                          \n";
    $stSql .= "                (                                                                                                                                        \n";
    $stSql .= "                  select  exercicio                                                                                                                      \n";
    $stSql .= "                         ,cod_entidade                                                                                                                   \n";
    $stSql .= "                         ,cod_ordem                                                                                                                      \n";
    $stSql .= "                         ,exercicio_liquidacao                                                                                                           \n";
    $stSql .= "                         ,cod_nota                                                                                                                       \n";
    $stSql .= "                         ,coalesce(sum(vl_anulado), 0) as vl_anulado                                                                                     \n";
    $stSql .= "                  from empenho.ordem_pagamento_liquidacao_anulada as opla                                                                                \n";
    $stSql .= "                  group by exercicio                                                                                                                     \n";
    $stSql .= "                          ,cod_entidade                                                                                                                  \n";
    $stSql .= "                          ,cod_ordem                                                                                                                     \n";
    $stSql .= "                          ,exercicio_liquidacao                                                                                                          \n";
    $stSql .= "                          ,cod_nota                                                                                                                      \n";
    $stSql .= "                ) as opla                                                                                                                                \n";
    $stSql .= "           on (                                                                                                                                          \n";
    $stSql .= "                    pl.exercicio            = opla.exercicio                                                                                             \n";
    $stSql .= "                and pl.cod_entidade         = opla.cod_entidade                                                                                          \n";
    $stSql .= "                and pl.cod_ordem            = opla.cod_ordem                                                                                             \n";
    $stSql .= "                and pl.exercicio_liquidacao = opla.exercicio_liquidacao                                                                                  \n";
    $stSql .= "                and pl.cod_nota             = opla.cod_nota                                                                                              \n";
    $stSql .= "              )                                                                                                                                          \n";
    $stSql .= "       left join (                                                                                                                                       \n";
    $stSql .= "                 select  nlp.exercicio                                                                                                                   \n";
    $stSql .= "                        ,nlp.cod_entidade                                                                                                                \n";
    $stSql .= "                        ,nlp.cod_nota                                                                                                                    \n";
    $stSql .= "                        ,plnlp.cod_ordem                                                                                                                 \n";
    $stSql .= "                        ,coalesce(sum(nlp.vl_pago)    , 0) as vl_pago                                                                                    \n";
    $stSql .= "                        ,sum(coalesce(nlpa.vl_anulado, 0)) as vl_anulado                                                                                 \n";
    $stSql .= "                        ,coalesce(coalesce(sum(nlp.vl_pago),0) - coalesce(sum(nlpa.vl_anulado),0), 0) as pago_menos_anulado                              \n";
    $stSql .= "                 from                                                                                                                                    \n";
    $stSql .= "                 empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp                                                                              \n";
    $stSql .= "                 left join empenho.nota_liquidacao_paga as nlp on(                                                                                       \n";
    $stSql .= "                     plnlp.exercicio    = nlp.exercicio                                                                                                  \n";
    $stSql .= "                 and plnlp.cod_entidade = nlp.cod_entidade                                                                                               \n";
    $stSql .= "                 and plnlp.cod_nota     = nlp.cod_nota                                                                                                   \n";
    $stSql .= "                 and plnlp.timestamp    = nlp.timestamp                                                                                                  \n";
    $stSql .= "                 )                                                                                                                                       \n";
    $stSql .= "                 left join(                                                                                                                              \n";
    $stSql .= "                    select                                                                                                                               \n";
    $stSql .= "                        exercicio,                                                                                                                       \n";
    $stSql .= "                        cod_entidade,                                                                                                                    \n";
    $stSql .= "                        cod_nota,                                                                                                                        \n";
    $stSql .= "                        timestamp,                                                                                                                       \n";
    $stSql .= "                        coalesce(sum(nlpa.vl_anulado), 0) as vl_anulado                                                                                  \n";
    $stSql .= "                    from                                                                                                                                 \n";
    $stSql .= "                        empenho.nota_liquidacao_paga_anulada as nlpa                                                                                     \n";
    $stSql .= "                    group by                                                                                                                             \n";
    $stSql .= "                        exercicio,                                                                                                                       \n";
    $stSql .= "                        cod_entidade,                                                                                                                    \n";
    $stSql .= "                        cod_nota,                                                                                                                        \n";
    $stSql .= "                        timestamp                                                                                                                        \n";
    $stSql .= "                    )    as nlpa                                                                                                                         \n";
    $stSql .= "                                                                                                                                                         \n";
    $stSql .= "                           on (     nlp.exercicio    = nlpa.exercicio                                                                                    \n";
    $stSql .= "                                and nlp.cod_entidade = nlpa.cod_entidade                                                                                 \n";
    $stSql .= "                                and nlp.cod_nota     = nlpa.cod_nota                                                                                     \n";
    $stSql .= "                                and nlp.timestamp    = nlpa.timestamp                                                                                    \n";
    $stSql .= "                              )                                                                                                                          \n";
    $stSql .= "                 where         nlp.cod_entidade = ".$this->getDado('cod_entidade')."                                                                     \n";
    $stSql .= "                 group by  nlp.exercicio                                                                                                                 \n";
    $stSql .= "                          ,nlp.cod_entidade                                                                                                              \n";
    $stSql .= "                          ,nlp.cod_nota                                                                                                                  \n";
    $stSql .= "                          ,plnlp.cod_ordem                                                                                                               \n";
    $stSql .= "                 order by nlp.cod_nota, nlp.exercicio                                                                                                    \n";
    $stSql .= "                ) as pagamento on (                                                                                                                      \n";
    $stSql .= "                                       pagamento.exercicio    = pl.exercicio_liquidacao                                                                  \n";
    $stSql .= "                                   and pagamento.cod_entidade = pl.cod_entidade                                                                          \n";
    $stSql .= "                                   and pagamento.cod_nota     = pl.cod_nota                                                                              \n";
    $stSql .= "                                   and pagamento.cod_ordem    = pl.cod_ordem                                                                             \n";
    $stSql .= "                                  )                                                                                                                      \n";
    $stSql .= " where     pl.cod_entidade = ".$this->getDado('cod_entidade')."                                                                                          \n";
    $stSql .= "      and pl.cod_ordem    = ".$this->getDado('cod_ordem')."                                                                                              \n";
    $stSql .= "      and pl.exercicio    = '".$this->getDado('exercicio')."'                                                                                            \n";
    if ($this->getDado('verifica_saldo') != '') {
        $stSql .= "      and (coalesce(pl.vl_pagamento,0)-coalesce(opla.vl_anulado,0)) - (coalesce(pagamento.vl_pago,0)-coalesce(pagamento.vl_anulado,0)) > 0              \n";
    }
    $stSql .= " order by pl.cod_nota, pl.exercicio_liquidacao                                                                                                           \n";

    return $stSql;
}

}
