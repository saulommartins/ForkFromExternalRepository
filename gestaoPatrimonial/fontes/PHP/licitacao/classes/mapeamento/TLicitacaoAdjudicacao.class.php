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
    * Classe de mapeamento da tabela licitacao.adjudicacao
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 21451 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-03-29 16:46:34 -0300 (Qui, 29 Mar 2007) $

    * Casos de uso: uc-03.05.20
*/
/*
$Log$
Revision 1.10  2007/03/29 19:46:34  hboaventura
Bug #8954#

Revision 1.9  2006/11/29 15:26:07  leandro.zis
atualizado

Revision 1.8  2006/11/29 15:19:04  andre.almeida
Atualizado

Revision 1.7  2006/11/29 14:56:39  andre.almeida
Atualizado

Revision 1.6  2006/11/27 12:02:55  leandro.zis
adicionado geraçao do termo de adjudicacao

Revision 1.5  2006/11/26 02:26:38  andre.almeida
Atualizado

Revision 1.4  2006/11/24 19:19:42  andre.almeida
Adicionado o campo cod_modelo e cod_tipo_modelo.

Revision 1.3  2006/11/24 17:00:27  andre.almeida
Adicionado consultasa e alterado os campos.

Revision 1.2  2006/11/08 10:51:41  larocca
Inclusão dos Casos de Uso

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.adjudicacao
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoAdjudicacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoAdjudicacao()
{
    parent::Persistente();
    $this->setTabela("licitacao.adjudicacao");

    $this->setCampoCod('num_adjudicacao');
    $this->setComplementoChave( 'cod_entidade, cod_modalidade, cod_licitacao, exercicio_licitacao, cod_item, cod_cotacao, lote, exercicio_cotacao, cgm_fornecedor' );

    $this->AddCampo( 'num_adjudicacao'    , 'integer'      , true , ''  , true , false );
    $this->AddCampo( 'timestamp'          , 'char'      , false, ''  , true , false );
    $this->AddCampo( 'cod_entidade'       , 'integer'      , true , ''  , true , 'TLicitacaoCotacaoLicitacao' );
    $this->AddCampo( 'cod_modalidade'     , 'integer'      , true , ''  , true , 'TLicitacaoCotacaoLicitacao' );
    $this->AddCampo( 'cod_licitacao'      , 'integer'      , true , ''  , true , 'TLicitacaoCotacaoLicitacao' );
    $this->AddCampo( 'exercicio_licitacao', 'char'         , true , '4' , true , 'TLicitacaoCotacaoLicitacao' );
    $this->AddCampo( 'cod_item'           , 'integer'      , true , ''  , true , 'TLicitacaoCotacaoLicitacao' );
    $this->AddCampo( 'cgm_fornecedor'     , 'integer'      , true , ''  , true , 'TLicitacaoCotacaoLicitacao' );
    $this->AddCampo( 'cod_cotacao'        , 'integer'      , true , ''  , true , 'TLicitacaoCotacaoLicitacao' );
    $this->AddCampo( 'lote'               , 'integer'      , true , ''  , true , 'TLicitacaoCotacaoLicitacao' );
    $this->AddCampo( 'exercicio_cotacao'  , 'char'         , true , '4' , true , 'TLicitacaoCotacaoLicitacao' );
    $this->AddCampo( 'adjudicado'         , 'boolean'      , true , ''  , false, false );
    $this->AddCampo( 'cod_documento'      , 'integer'      , true , ''  , false, 'TAdministracaoModeloDocumento' );
    $this->AddCampo( 'cod_tipo_documento' , 'integer'      , true , ''  , false, 'TAdministracaoModeloDocumento' );

}

function recuperaItensComStatus(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if( $this->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->getDado( "exercicio" ) != "" )
        $stFiltro .= "and licitacao.exercicio      = '".$this->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->getDado( "exercicio" )."' \n";
    if( $this->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->getDado( "cod_entidade" ) != "" )
        $stFiltro .= "and licitacao.cod_entidade   = ".$this->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->getDado( "cod_entidade" )." \n";
    if( $this->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->getDado( "cod_modalidade" ) != "" )
        $stFiltro .= "and licitacao.cod_modalidade = ".$this->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->getDado( "cod_modalidade" )." \n";
    if( $this->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->getDado( "cod_licitacao" ) != "" )
        $stFiltro .= "and licitacao.cod_licitacao  = ".$this->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->getDado( "cod_licitacao" )." \n";

    if( $stFiltro )
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro)-4);

    $stFiltro .= "group by  adjudicacao.num_adjudicacao
            , licitacao.cod_entidade
            , licitacao.cod_modalidade
            , licitacao.cod_licitacao
            , licitacao.exercicio
            , cotacao_licitacao.exercicio_cotacao
            , cotacao_licitacao.cod_cotacao
            , cotacao_item.lote
            , cotacao_licitacao.cod_item
            , julgamento_vencedor.vl_cotacao
            , catalogo_item.descricao_resumida
            , catalogo_item.descricao
            , julgamento_vencedor.cgm_fornecedor
            , adjudicacao.cod_documento
            , adjudicacao.cod_tipo_documento
            , homologacao.homologado
            , homologacao.motivo
            , adjudicacao.adjudicado
            , adjudicacao.num_adjudicacao_anulada
            , adjudicacao.motivo
            , homologacao.motivo
            , mapa_item.exercicio
            , mapa_item.cod_mapa
            , unidade_medida.nom_unidade
            , cotacao_item.quantidade
    ";

    $stOrdem = "order by cotacao_licitacao.cod_item";

    $stSql  = $this->montaRecuperaItensComStatus().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaItensComStatus()
{
$stSql = "
        select adjudicacao.num_adjudicacao
             , licitacao.cod_entidade
             , licitacao.cod_modalidade
             , licitacao.cod_licitacao
             , licitacao.exercicio as licitacao_exercicio
             , cotacao_licitacao.exercicio_cotacao as cotacao_exercicio
             , cotacao_licitacao.cod_cotacao
             , cotacao_item.lote
             , cotacao_licitacao.cod_item
             , unidade_medida.nom_unidade
             , mapa_item.exercicio as exercicio_mapa
             , mapa_item.cod_mapa
             , cotacao_item.quantidade
             --, sum(mapa_item.quantidade) - coalesce(sum(mapa_item_anulacao.quantidade),0) as quantidade
             , publico.fn_numeric_br(sum(mapa_item.vl_total) - coalesce(sum(mapa_item_anulacao.vl_total),0)) as vl_total
             , publico.fn_numeric_br(julgamento_vencedor.vl_cotacao) as vl_cotacao
             , julgamento_vencedor.vl_cotacao / (sum(mapa_item.quantidade) - coalesce(sum(mapa_item_anulacao.quantidade),0))::numeric(14,2) as vl_unitario

             , ((sum(mapa_item.vl_total) - coalesce(sum(mapa_item_anulacao.vl_total),0)) / (sum(mapa_item.quantidade) - coalesce(sum(mapa_item_anulacao.quantidade),0)))::numeric(14,2) as vl_unitario_referencia

             , catalogo_item.descricao_resumida
             , catalogo_item.descricao
             , julgamento_vencedor.cgm_fornecedor
             , adjudicacao.cod_documento
             , adjudicacao.cod_tipo_documento
             , case when homologacao.homologado = true then 'Homologado'
                    when homologacao.motivo is not null or adjudicacao.motivo is not null then 'Anulado'
                    when adjudicacao.adjudicado = true and adjudicacao.motivo is null then 'Adjudicado'
                    else 'A Adjudicar'
                end as status
             , adjudicacao.num_adjudicacao_anulada
             , case when adjudicacao.motivo is not null then adjudicacao.motivo
                      when homologacao.motivo is not null then homologacao.motivo
                      else null
                 end as justificativa_anulacao
          from licitacao.licitacao
          join (
                 select cod_licitacao, cod_modalidade, cod_entidade, exercicio_licitacao, lote, cod_cotacao, cod_item, exercicio_cotacao
                   from licitacao.cotacao_licitacao
                  group by cod_licitacao, cod_modalidade, cod_entidade, exercicio_licitacao, lote, cod_cotacao, cod_item, exercicio_cotacao
               ) as cotacao_licitacao
            on cotacao_licitacao.cod_licitacao       = licitacao.cod_licitacao
           and cotacao_licitacao.cod_modalidade      = licitacao.cod_modalidade
           and cotacao_licitacao.cod_entidade        = licitacao.cod_entidade
           and cotacao_licitacao.exercicio_licitacao = licitacao.exercicio
          join (
                 select julgamento_item.exercicio
                      , julgamento_item.cod_cotacao
                      , julgamento_item.cod_item
                      , julgamento_item.lote
                      , julgamento_item.cgm_fornecedor
                      , cotacao_fornecedor_item.vl_cotacao
                   from compras.julgamento_item
                   join compras.cotacao_fornecedor_item
                     on (     cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                          and cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                          and cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                          and cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                          and cotacao_fornecedor_item.lote           = julgamento_item.lote
                        )
                  where julgamento_item.ordem = 1
                ) as julgamento_vencedor
           on julgamento_vencedor.exercicio   = cotacao_licitacao.exercicio_cotacao
          and julgamento_vencedor.cod_cotacao = cotacao_licitacao.cod_cotacao
          and julgamento_vencedor.cod_item    = cotacao_licitacao.cod_item
          and julgamento_vencedor.lote        = cotacao_licitacao.lote

         join compras.mapa_item
           on mapa_item.exercicio  = licitacao.exercicio_mapa
          and mapa_item.cod_mapa   = licitacao.cod_mapa
          and mapa_item.cod_item   = cotacao_licitacao.cod_item

         left join compras.mapa_item_anulacao
           on mapa_item.exercicio  = mapa_item_anulacao.exercicio
          and mapa_item.exercicio_solicitacao  = mapa_item_anulacao.exercicio_solicitacao
          and mapa_item.cod_mapa   = mapa_item_anulacao.cod_mapa
          and mapa_item.cod_entidade   = mapa_item_anulacao.cod_entidade
          and mapa_item.cod_solicitacao   = mapa_item_anulacao.cod_solicitacao
          and mapa_item.cod_centro   = mapa_item_anulacao.cod_centro
          and mapa_item.lote        = mapa_item_anulacao.lote
          and mapa_item.cod_item   = mapa_item_anulacao.cod_item

         join almoxarifado.catalogo_item
           on catalogo_item.cod_item = cotacao_licitacao.cod_item

         join administracao.unidade_medida
           on catalogo_item.cod_grandeza = unidade_medida.cod_grandeza
          and catalogo_item.cod_unidade = unidade_medida.cod_unidade

         join compras.cotacao_item
             on cotacao_item.exercicio   = cotacao_licitacao.exercicio_cotacao
           and cotacao_item.cod_cotacao = cotacao_licitacao.cod_cotacao
           and cotacao_item.lote        = cotacao_licitacao.lote
           and cotacao_item.cod_item    = cotacao_licitacao.cod_item

         left join (
                    select adjudicacao.num_adjudicacao
                           , adjudicacao.cod_licitacao
                           , adjudicacao.cod_modalidade
                           , adjudicacao.cod_entidade
                           , adjudicacao.exercicio_licitacao
                           , adjudicacao.lote
                           , adjudicacao.cod_cotacao
                           , adjudicacao.cgm_fornecedor
                           , adjudicacao.cod_item
                           , adjudicacao.exercicio_cotacao
                           , adjudicacao.cod_documento
                           , adjudicacao.cod_tipo_documento
                           , adjudicacao.adjudicado
                           , adjudicacao.timestamp
                           , adjudicacao_anulada.motivo
                           , adjudicacao_anulada.num_adjudicacao as num_adjudicacao_anulada
                        from licitacao.adjudicacao
                        left join licitacao.adjudicacao_anulada
                              on adjudicacao_anulada.num_adjudicacao       = adjudicacao.num_adjudicacao
                             and adjudicacao_anulada.cod_entidade          = adjudicacao.cod_entidade
                             and adjudicacao_anulada.cod_modalidade        = adjudicacao.cod_modalidade
                             and adjudicacao_anulada.cod_licitacao         = adjudicacao.cod_licitacao
                             and adjudicacao_anulada.exercicio_licitacao   = adjudicacao.exercicio_licitacao
                             and adjudicacao_anulada.cod_item              = adjudicacao.cod_item
                             and adjudicacao_anulada.cgm_fornecedor        = adjudicacao.cgm_fornecedor
                             and adjudicacao_anulada.cod_cotacao           = adjudicacao.cod_cotacao
                             and adjudicacao_anulada.lote                  = adjudicacao.lote
                             and adjudicacao_anulada.exercicio_cotacao     = adjudicacao.exercicio_cotacao
                           ) as adjudicacao
                          on adjudicacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                         and adjudicacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                         and adjudicacao.cod_entidade        = cotacao_licitacao.cod_entidade
                         and adjudicacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
                         and adjudicacao.lote                = cotacao_licitacao.lote
                         and adjudicacao.cod_cotacao         = cotacao_licitacao.cod_cotacao
                         and adjudicacao.cod_item            = cotacao_licitacao.cod_item
                         and adjudicacao.exercicio_cotacao   = cotacao_licitacao.exercicio_cotacao

         left join (
                      select homologacao.num_homologacao
                           , homologacao.cod_tipo_documento
                           , homologacao.cod_documento
                           , homologacao.cod_entidade
                           , homologacao.cod_modalidade
                           , homologacao.cod_licitacao
                           , homologacao.exercicio_licitacao
                           , homologacao.cod_item
                           , homologacao.cgm_fornecedor
                           , homologacao.cod_cotacao
                           , homologacao.lote
                           , homologacao.exercicio_cotacao
                           , homologacao.num_adjudicacao
                           , homologacao.homologado
                           , homologacao_anulada.motivo
                           , homologacao_anulada.revogacao
                        from licitacao.homologacao
                      left join licitacao.homologacao_anulada
                              on homologacao_anulada.num_homologacao       = homologacao.num_homologacao
                              and homologacao_anulada.cod_licitacao         = homologacao.cod_licitacao
                              and homologacao_anulada.cod_modalidade        = homologacao.cod_modalidade
                              and homologacao_anulada.cod_entidade          = homologacao.cod_entidade
                              and homologacao_anulada.num_adjudicacao       = homologacao.num_adjudicacao
                              and homologacao_anulada.exercicio_licitacao   = homologacao.exercicio_licitacao
                              and homologacao_anulada.lote                  = homologacao.lote
                              and homologacao_anulada.cod_cotacao           = homologacao.cod_cotacao
                              and homologacao_anulada.cgm_fornecedor        = homologacao.cgm_fornecedor
                              and homologacao_anulada.cod_item              = homologacao.cod_item
                              and homologacao_anulada.exercicio_cotacao     = homologacao.exercicio_cotacao
                    ) as homologacao
           on homologacao.num_adjudicacao       = adjudicacao.num_adjudicacao
          and homologacao.cod_entidade          = adjudicacao.cod_entidade
          and homologacao.cod_modalidade        = adjudicacao.cod_modalidade
          and homologacao.cod_licitacao         = adjudicacao.cod_licitacao
          and homologacao.exercicio_licitacao   = adjudicacao.exercicio_licitacao
          and homologacao.cod_item              = adjudicacao.cod_item
          and homologacao.cgm_fornecedor        = adjudicacao.cgm_fornecedor
          and homologacao.cod_cotacao           = adjudicacao.cod_cotacao
          and homologacao.lote                  = adjudicacao.lote
          and homologacao.exercicio_cotacao     = adjudicacao.exercicio_cotacao
 ";

    return $stSql;
}

function recuperaItensRelatorio(&$rsItensRelatorio, $stFiltro='', $stOrdem='')
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaItensRelatorio().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsItensRelatorio, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaItensRelatorio()
{
$stSql = "
     select  sw_cgm.nom_cgm as nom_fornecedor
        , catalogo_item.descricao as desc_item
        , cotacao_item.quantidade as qtd
        , cotacao_fornecedor_item.vl_cotacao as valor
     from (
                  select adjudicacao.num_adjudicacao
                       , adjudicacao.cod_licitacao
                       , adjudicacao.cod_modalidade
                       , adjudicacao.cod_entidade
                       , adjudicacao.exercicio_licitacao
                       , adjudicacao.lote
                       , adjudicacao.cod_cotacao
                       , adjudicacao.cgm_fornecedor
                       , adjudicacao.cod_item
                       , adjudicacao.exercicio_cotacao
                       , adjudicacao.cod_documento
                       , adjudicacao.cod_tipo_documento
                       , adjudicacao.adjudicado
                    from licitacao.adjudicacao
               left join licitacao.adjudicacao_anulada
                      on adjudicacao_anulada.num_adjudicacao       = adjudicacao.num_adjudicacao
                     and adjudicacao_anulada.cod_entidade          = adjudicacao.cod_entidade
                     and adjudicacao_anulada.cod_modalidade        = adjudicacao.cod_modalidade
                     and adjudicacao_anulada.cod_licitacao         = adjudicacao.cod_licitacao
                     and adjudicacao_anulada.exercicio_licitacao   = adjudicacao.exercicio_licitacao
                     and adjudicacao_anulada.cod_item              = adjudicacao.cod_item
                     and adjudicacao_anulada.cgm_fornecedor        = adjudicacao.cgm_fornecedor
                     and adjudicacao_anulada.cod_cotacao           = adjudicacao.cod_cotacao
                     and adjudicacao_anulada.lote                  = adjudicacao.lote
                     and adjudicacao_anulada.exercicio_cotacao     = adjudicacao.exercicio_cotacao
                   where adjudicacao_anulada.num_adjudicacao is null
          ) as adjudicacao
     join compras.cotacao_item
       on cotacao_item.exercicio   = adjudicacao.exercicio_cotacao
      and cotacao_item.cod_cotacao = adjudicacao.cod_cotacao
      and cotacao_item.lote        = adjudicacao.lote
      and cotacao_item.cod_item    = adjudicacao.cod_item
     join compras.cotacao_fornecedor_item
       on cotacao_fornecedor_item.exercicio      = adjudicacao.exercicio_cotacao
      and cotacao_fornecedor_item.cod_cotacao    = adjudicacao.cod_cotacao
      and cotacao_fornecedor_item.cod_item       = adjudicacao.cod_item
      and cotacao_fornecedor_item.cgm_fornecedor = adjudicacao.cgm_fornecedor
      and cotacao_fornecedor_item.lote           = adjudicacao.lote
     join sw_cgm
           on sw_cgm.numcgm = adjudicacao.cgm_fornecedor
     join almoxarifado.catalogo_item
       on catalogo_item.cod_item = adjudicacao.cod_item
    where adjudicacao.cod_entidade = ".$this->getDado('cod_entidade')."
      and adjudicacao.cod_modalidade = ".$this->getDado('cod_modalidade')."
      and adjudicacao.cod_licitacao = ".$this->getDado('cod_licitacao')."
      and adjudicacao.exercicio_licitacao = '".$this->getDado('exercicio')."'
      and adjudicacao.adjudicado = true
\n";

return $stSql;
}

}
