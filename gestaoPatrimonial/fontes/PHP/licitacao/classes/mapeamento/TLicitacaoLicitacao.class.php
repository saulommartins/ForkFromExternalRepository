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

    * Classe de mapeamento da tabela licitacao.licitacao
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.05.15

    $Id: TLicitacaoLicitacao.class.php 66191 2016-07-28 14:03:35Z carlos.silva $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TLicitacaoLicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela("licitacao.licitacao");

    $this->setCampoCod('cod_licitacao');
    $this->setComplementoChave('cod_modalidade,cod_entidade,exercicio');

    $this->AddCampo('cod_licitacao'       ,'integer'    ,true  ,''     ,true,false);
    $this->AddCampo('cod_modalidade'      ,'integer'    ,true  ,''     ,true,'TComprasModalidade');
    $this->AddCampo('cod_entidade'        ,'integer'    ,true  ,''     ,true,'TOrcamentoEntidade');
    $this->AddCampo('exercicio'           ,'char'       ,true  ,'4'    ,true,'TOrcamentoEntidade');
    $this->AddCampo('cod_tipo_objeto'     ,'integer'    ,true  ,''     ,false,'TComprasTipoObjeto');
    $this->AddCampo('cod_objeto'          ,'integer'    ,true  ,''     ,false,'TComprasObjeto');
    $this->AddCampo('cod_criterio'        ,'integer'    ,true  ,''     ,false,'TLicitacaoCriterioJulgamento');
    $this->AddCampo('cod_tipo_licitacao'  ,'integer'    ,true  ,''     ,false,'TLicitacaoTipoLicitacao');
    $this->AddCampo('cod_mapa'            ,'integer'    ,true  ,''     ,false,'TComprasMapa');
    $this->AddCampo('exercicio_mapa'      ,'char'       ,true  ,'4'    ,false,'TComprasMapa','exercicio');
    $this->AddCampo('cod_processo'        ,'integer'    ,true  ,''     ,false,'TProtocoloProcesso');
    $this->AddCampo('exercicio_processo'  ,'char'       ,true  ,'4'    ,false,'TProtocoloProcesso','ano_exercicio');
    $this->AddCampo('vl_cotado'           ,'numeric'    ,true  ,'14,2' ,false,false);
    $this->AddCampo('timestamp'           ,'timestamp'  ,true  ,''     ,false,false);
    $this->AddCampo('num_orgao'           ,'integer'    ,true  ,''     ,false,false);
    $this->AddCampo('num_unidade'         ,'integer'    ,true  ,''     ,false,false);
    $this->AddCampo('cod_regime'          ,'integer'    ,false ,''     ,false,false);
    $this->AddCampo('tipo_chamada_publica','integer'    ,false ,''     ,false,true);
    $this->AddCampo('registro_precos'     ,'boolean'    ,false ,''     ,false,true);
}

function proximoCodigoLicitacao(&$inCodLicitacao , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    // buscar configuração na base
    require_once TCOM."TComprasConfiguracao.class.php";
    $obTComprasConf = new TComprasConfiguracao();
    $obTComprasConf->pegaConfiguracao( $valor , 'numeracao_licitacao' , $boTransacao);

    switch ($valor) {
        case 'geral':
                $stSql  = " SELECT coalesce(max(" . $this->getCampoCod() . "::int),0) + 1 as proximoCodigoLicitacao " ;
                $stSql .= "   FROM " . $this->getTabela() . " ";
                $stSql .= "  WHERE exercicio = '". $this->getDado('exercicio')."'";
                $obErro = $obConexao->executaSQL( $rsCodigo , $stSql, "", $boTransacao );
                $inCodLicitacao = $rsCodigo->getCampo( 'proximocodigolicitacao' );
            break;
        case 'modalidade':
                $stSql  = " SELECT coalesce(max(" . $this->getCampoCod() . "::int),0) + 1 as proximoCodigoLicitacao " ;
                $stSql .= "   FROM " . $this->getTabela() . " ";
                $stSql .= "  WHERE cod_modalidade = ". $this->getDado('cod_modalidade');
                $stSql .= "    AND exercicio = '". $this->getDado('exercicio')."'";
                $obErro = $obConexao->executaSQL( $rsCodigo , $stSql, "", $boTransacao );
                $inCodLicitacao = $rsCodigo->getCampo( 'proximocodigolicitacao' );
            break;
        case 'entidade':
                $stSql  = " SELECT coalesce(max(" . $this->getCampoCod() . "::int),0) + 1 as proximoCodigoLicitacao " ;
                $stSql .= "   FROM " . $this->getTabela() . " ";
                $stSql .= "  WHERE cod_entidade = ". $this->getDado('cod_entidade');
                $stSql .= "    AND exercicio = '". $this->getDado('exercicio')."'";
                $obErro = $obConexao->executaSQL( $rsCodigo , $stSql, "", $boTransacao );
                $inCodLicitacao = $rsCodigo->getCampo( 'proximocodigolicitacao' );
            break;
        case 'entidademodalidade':
                $stSql  = " SELECT coalesce(max(" . $this->getCampoCod() . "::int),0) + 1 as proximoCodigoLicitacao " ;
                $stSql .= "   FROM " . $this->getTabela() . " ";
                $stSql .= "  WHERE cod_entidade = ". $this->getDado('cod_entidade');
                $stSql .= "    AND cod_modalidade = ". $this->getDado('cod_modalidade');
                $stSql .= "    AND exercicio = '". $this->getDado('exercicio')."'";
                $obErro = $obConexao->executaSQL( $rsCodigo , $stSql, "", $boTransacao );
                $inCodLicitacao = $rsCodigo->getCampo( 'proximocodigolicitacao' );
            break;
    } // switch

    return $obErro;
}

function inclusao($boTransacao = "")
{
    $obErro = new Erro;
    if ( $this->getDado( 'cod_licitacao' ) == '' ) {
        $this->proximoCodigoLicitacao( $inCodLicitacao , $boTransacao);

        $this->setDado( 'cod_licitacao' , $inCodLicitacao );
    }

    $obErro = parent::inclusao( $boTransacao );

    return $obErro;
}

function recuperaLicitacaoCompleta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLicitacaoCompleta().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaLicitacaoCompleta()
{
$stSql = "
       SELECT ll.cod_entidade                                
            , ll.cod_licitacao                               
            , ll.cod_processo||'/'||ll.exercicio_processo as processo 
            , cm.descricao                                   
            , cm.cod_modalidade                              
            , ll.cod_modalidade                              
            , ll.cod_mapa||'/'||ll.exercicio_mapa as mapa_compra 
            , ll.cod_entidade||' - '||cgm.nom_cgm as entidade    
            , ll.cod_modalidade||' - '||cm.descricao as modalidade 
            , ll.cod_objeto                                  
            , ll.timestamp                                   
            , ll.cod_tipo_objeto                             
            , ll.cod_tipo_licitacao                          
            , ll.cod_criterio                                
            , ll.vl_cotado                                   
            , ll.exercicio                                   
            , to_char(ll.timestamp::date, 'dd/mm/yyyy') as dt_licitacao 
            , to_char(edital.dt_entrega_propostas::date, 'dd/mm/yyyy') as dt_entrega_proposta 
            , to_char(edital.dt_validade_proposta::date, 'dd/mm/yyyy') as dt_validade_proposta 
            , to_char(edital.dt_aprovacao_juridico::date, 'dd/mm/yyyy') as dt_aprovacao_proposta 
            , edital.condicoes_pagamento as condicoes_pagamento 
        
        FROM  Licitacao.licitacao as ll
                     
    LEFT JOIN licitacao.licitacao_anulada AS la
           ON ll.cod_licitacao  = la.cod_licitacao
          AND ll.cod_modalidade = la.cod_modalidade
          AND ll.cod_entidade   = la.cod_entidade
          AND ll.exercicio      = la.exercicio
     
    LEFT JOIN licitacao.edital
           ON ll.cod_licitacao  = edital.cod_licitacao     
          AND ll.cod_modalidade = edital.cod_modalidade   
          AND ll.cod_entidade   = edital.cod_entidade       
          AND ll.exercicio      = edital.exercicio
          
    INNER JOIN compras.modalidade as cm     
            ON ll.cod_modalidade = cm.cod_modalidade
    
    INNER JOIN orcamento.entidade as oe                         
            ON ll.cod_entidade = oe.cod_entidade           
           AND ll.exercicio    = oe.exercicio 
            
    INNER JOIN  sw_cgm as cgm
            ON oe.numcgm = cgm.numcgm
            
    WHERE
            -- Para as modalidades 1,2,3,4,5,6,7,10,11 é obrigatório exister um edital
            CASE WHEN ll.cod_modalidade in (1,2,3,4,5,6,7,10,11) THEN
                    
                    edital.cod_licitacao IS NOT NULL
               AND edital.cod_modalidade IS NOT NULL
               AND edital.cod_entidade   IS NOT NULL 
               AND edital.exercicio      IS NOT NULL 

              -- Para as modalidades 8,9 é facultativo possuir um edital
              WHEN ll.cod_modalidade in (8,9) THEN
                    
                    edital.cod_licitacao  IS NULL
                 OR edital.cod_modalidade IS NULL
                 OR edital.cod_entidade   IS NULL 
                 OR edital.exercicio      IS NULL 

	         OR edital.cod_licitacao  IS NOT NULL
	         OR edital.cod_modalidade IS NOT NULL
	         OR edital.cod_entidade   IS NOT NULL 
	         OR edital.exercicio      IS NOT NULL 
            END \n ";

if ($this->getDado('cod_entidade'))
    $stSql .=" AND ll.cod_entidade in (".$this->getDado('cod_entidade').") \n";

if ($this->getDado('cod_licitacao'))
    $stSql .=" AND ll.cod_licitacao = ".$this->getDado('cod_licitacao')." \n";

if ($this->getDado('cod_processo'))
    $stSql .=" AND ll.cod_processo = ".$this->getDado('cod_processo')." \n";

if ($this->getDado('cod_mapa'))
    $stSql .=" AND ll.cod_mapa = ".$this->getDado('cod_mapa')." \n";

if ($this->getDado('cod_modalidade'))
    $stSql .=" AND ll.cod_modalidade = ".$this->getDado('cod_modalidade')." \n";

if ($this->getDado('cod_tipo_licitacao'))
    $stSql .=" AND ll.cod_tipo_licitacao = ".$this->getDado('cod_tipo_licitacao')." \n";

if ($this->getDado('cod_criterio'))
    $stSql .=" AND ll.cod_criterio = ".$this->getDado('cod_criterio')." \n";

if ($this->getDado('cod_objeto'))
    $stSql .=" AND ll.cod_objeto = ".$this->getDado('cod_objeto')." \n";

if ($this->getDado('cod_tipo_objeto'))
    $stSql .=" AND ll.cod_tipo_objeto = ".$this->getDado('cod_tipo_objeto')." \n";

if ($this->getDado('exercicio'))
    $stSql .=" AND ll.exercicio = '".$this->getDado('exercicio')."' \n";

if ($this->getDado('exercicio_processo'))
    $stSql .=" AND ll.exercicio_processo = '".$this->getDado('exercicio_processo')."' \n";

if ($this->getDado('exercicio_mapa'))
    $stSql .=" AND ll.exercicio_mapa = '".$this->getDado('exercicio_mapa')."' \n";

return $stSql;
}

/**
 * Recupera a adjudicacao desta licitacao
 */
function recuperaAdjudicacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAdjudicacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

/**
 * Monta a string sql para recuperar a adjudicacao
 * Nao sei se o where apenas com esses campos basta......
 */
function montaRecuperaAdjudicacao()
{
  $stSql = "SELECT
             ad.num_adjudicacao, ad.timestamp
            FROM licitacao.adjudicacao as ad
            WHERE
              cod_licitacao='".$this->getDado("cod_licitacao")."' AND
              cod_modalidade='".$this->getDado('cod_modalidade')."' AND
              cod_entidade='".$this->getDado('cod_entidade')."' AND
              exercicio_licitacao='".$this->getDado('exercicio')."'";

  return $stSql;
}

function recuperaItensCotados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if( $this->getDado('cod_licitacao') != "" )
        $stFiltroClasse  = " AND licitacao.cod_licitacao = ".$this->getDado('cod_licitacao')." \n";
    if( $this->getDado('cod_modalidade') != "" )
        $stFiltroClasse .= " AND licitacao.cod_modalidade = ".$this->getDado('cod_modalidade')." \n";
    if( $this->getDado('cod_entidade') != "" )
        $stFiltroClasse .= " AND licitacao.cod_entidade = ".$this->getDado('cod_entidade')." \n";
    if( $this->getDado('exercicio') != "" )
        $stFiltroClasse .= " AND licitacao.exercicio = ".$this->getDado('exercicio')." \n";
    if( $this->getDado('cod_tipo_objeto') != "" )
        $stFiltroClasse .= " AND licitacao.cod_tipo_objeto = ".$this->getDado('cod_tipo_objeto')." \n";
    if( $this->getDado('cod_objeto') != "" )
        $stFiltroClasse .= " AND licitacao.cod_objeto = ".$this->getDado('cod_objeto')." \n";
    if( $this->getDado('cod_criterio') != "" )
        $stFiltroClasse .= " AND licitacao.cod_criterio = ".$this->getDado('cod_criterio')." \n";
    if( $this->getDado('cod_tipo_licitacao') != "" )
        $stFiltroClasse .= " AND licitacao.cod_tipo_licitacao = ".$this->getDado('cod_tipo_licitacao')." \n";
    if( $this->getDado('cod_mapa') != "" )
        $stFiltroClasse .= " AND licitacao.cod_mapa = ".$this->getDado('cod_mapa')." \n";
    if( $this->getDado('exercicio_mapa') != "" )
        $stFiltroClasse .= " AND licitacao.exercicio_mapa = ".$this->getDado('exercicio_mapa')." \n";
    if( $this->getDado('cod_processo') != "" )
        $stFiltroClasse .= " AND licitacao.cod_processo = ".$this->getDado('cod_processo')." \n";
    if( $this->getDado('exercicio_processo') != "" )
        $stFiltroClasse .= " AND licitacao.exercicio_processo = ".$this->getDado('exercicio_processo')." \n";
    if( $this->getDado('vl_cotado') != "" )
        $stFiltroClasse .= " AND licitacao.vl_cotado = ".$this->getDado('vl_cotado')." \n";
    if( $this->getDado('timestamp') != "" )
        $stFiltroClasse .= " AND licitacao.timestamp = ".$this->getDado('timestamp')." \n";

    $stFiltro = $stFiltroClasse.$stFiltro;
    $stFiltro = ($stFiltro!="")?" WHERE ".substr($stFiltro,4,strlen($stFiltro)):"";

    $stSql = $this->montaRecuperaItensCotados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaItensCotados()
{
    $stSql  = " select licitacao.exercicio                                       \n";
    $stSql .= "      , licitacao.cod_entidade                                    \n";
    $stSql .= "      , licitacao.cod_modalidade                                  \n";
    $stSql .= "      , licitacao.cod_licitacao                                   \n";
    $stSql .= "      , cotacao_item.cod_item                                     \n";
    $stSql .= "      , catalogo_item.descricao_resumida                          \n";
    $stSql .= "      , mapa_item.lote                                            \n";
    $stSql .= "      , cotacao_item.quantidade                                   \n";
    $stSql .= "      , mapa_item.vl_total                                        \n";
    $stSql .= "   from licitacao.licitacao                                       \n";
    $stSql .= "   join compras.mapa_cotacao                                      \n";
    $stSql .= "     on mapa_cotacao.exercicio_mapa = licitacao.exercicio_mapa    \n";
    $stSql .= "    and mapa_cotacao.cod_mapa       = licitacao.cod_mapa          \n";
    $stSql .= "   join compras.cotacao_item                                      \n";
    $stSql .= "     on cotacao_item.exercicio   = mapa_cotacao.exercicio_cotacao \n";
    $stSql .= "    and cotacao_item.cod_cotacao = mapa_cotacao.cod_cotacao       \n";
    $stSql .= "   join compras.mapa_item                                         \n";
    $stSql .= "     on mapa_item.exercicio    = mapa_cotacao.exercicio_mapa      \n";
    $stSql .= "    and mapa_item.cod_mapa     = licitacao.cod_mapa               \n";
    $stSql .= "    and mapa_item.cod_entidade = licitacao.cod_entidade           \n";
    $stSql .= "    and mapa_item.cod_item     = cotacao_item.cod_item            \n";
    $stSql .= "   join almoxarifado.catalogo_item                                \n";
    $stSql .= "     on catalogo_item.cod_item = cotacao_item.cod_item            \n";

    return $stSql;
}

function recuperaObjetoLicitacao(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = "";
    $stFiltro = "";
    if( $this->getDado('cod_licitacao') != "" )
        $stFiltro  = " AND licitacao.cod_licitacao = ".$this->getDado('cod_licitacao')."   \n";
    if( $this->getDado('cod_modalidade') != "" )
        $stFiltro .= " AND licitacao.cod_modalidade = ".$this->getDado('cod_modalidade')." \n";
    if( $this->getDado('cod_entidade') != "" )
        $stFiltro .= " AND licitacao.cod_entidade = ".$this->getDado('cod_entidade')."     \n";
    if( $this->getDado('exercicio') != "" )
        $stFiltro .= " AND licitacao.exercicio = '".$this->getDado('exercicio')."'          \n";

    $stFiltro = ($stFiltro!="")?" WHERE ".substr($stFiltro,4,strlen($stFiltro)):"";

    $stSql = $this->montaRecuperaObjetoLicitacao().$stFiltro;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaObjetoLicitacao()
{
    $stSql  = " select licitacao.cod_licitacao                  \n";
    $stSql .= "      , licitacao.cod_modalidade                 \n";
    $stSql .= "      , licitacao.cod_entidade                   \n";
    $stSql .= "      , licitacao.exercicio                      \n";
    $stSql .= "      , objeto.cod_objeto                        \n";
    $stSql .= "      , objeto.descricao                         \n";
    $stSql .= "   from licitacao.licitacao                      \n";
    $stSql .= "   join compras.objeto                           \n";
    $stSql .= "     on objeto.cod_objeto = licitacao.cod_objeto \n";

    return $stSql;
}

function recuperaDescricaoJulgamentoObjeto(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDescricaoJulgamentoObjeto().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaDescricaoJulgamentoObjeto()
{
    $stSql  = " SELECT                                                              \n";
    $stSql .= "      objeto.descricao as descricao_objeto                           \n";
    $stSql .= "    , tipo_objeto.descricao as descricao_tipo_objeto                 \n";
    $stSql .= "    , criterio_julgamento.descricao as descricao_criterio_julgamento \n";
    $stSql .= "    , mapa.cod_tipo_licitacao as mapa_cod_tipo_licitacao             \n";
    $stSql .= "    , tipo_licitacao.descricao as tipo_licitacao                     \n";
    $stSql .= " FROM                                                                \n";
    $stSql .= "        compras.objeto                                               \n";
    $stSql .= "      , compras.tipo_objeto                                          \n";
    $stSql .= "      , licitacao.criterio_julgamento                                \n";
    $stSql .= "      , compras.mapa                                                 \n";
    $stSql .= "      , compras.tipo_licitacao                                       \n";
    $stSql .= " WHERE                                                               \n";
    $stSql .= "      mapa.cod_tipo_licitacao = tipo_licitacao.cod_tipo_licitacao    \n";
    $stSql .= " AND  objeto.cod_objeto = ".$this->getDado('cod_objeto')."           \n";
    $stSql .= " AND  cod_tipo_objeto   = ".$this->getDado('cod_tipo_objeto')."      \n";
    $stSql .= " AND  cod_criterio      = ".$this->getDado('cod_criterio')."         \n";
    $stSql .= " AND  mapa.cod_mapa     = ".$this->getDado('cod_mapa')."             \n";
    $stSql .= " AND  mapa.exercicio    = '".$this->getDado('exercicio')." '         \n";

    return $stSql;
}

function recuperaLicitacaoEdital(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLicitacaoEdital().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaLicitacaoEdital()
{
    $stSql  ="SELECT                                            \n";
    $stSql .="    l.cod_licitacao                               \n";
    $stSql .="    ,e.num_edital                                 \n";
    $stSql .="FROM                                              \n";
    $stSql .="    licitacao.licitacao as l                      \n";
    $stSql .="    ,licitacao.edital as e                        \n";
    $stSql .="WHERE                                             \n";
    $stSql .="        l.cod_licitacao  = e.cod_licitacao        \n";
    $stSql .="    AND l.cod_modalidade = e.cod_modalidade       \n";
    $stSql .="    AND l.cod_entidade   = e.cod_entidade         \n";
    $stSql .="    AND l.exercicio      = e.exercicio_licitacao  \n";

    if ($this->getDado('cod_licitacao'))
        $stSql .=" AND l.cod_licitacao = ".$this->getDado('cod_licitacao')." \n";
    if ($this->getDado('exercicio'))
        $stSql .= "AND l.exercicio = '".$this->getDado('exercicio')."' \n";
    if ($this->getDado('num_edital'))
        $stSql .= " AND e.num_edital = '".$this->getDado('num_edital')."' \n";

    return $stSql;
}

function recuperaItensLicitadosEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaItensLicitadosEsfinge().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaItensLicitadosEsfinge()
{
    $stSql = "
        select licitacao.cod_licitacao
        , catalogo_item.cod_item
        , catalogo_item.descricao_resumida
        , to_char( homologacao.timestamp, 'dd/mm/yyyy' ) as dt_homologacao
        , mapa_item.quantidade
        , mapa_item.lote
        , unidade_medida.nom_unidade
        from compras.mapa_item

        join almoxarifado.catalogo_item
        on catalogo_item.cod_item = mapa_item.cod_item

        join administracao.unidade_medida
        on unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
        and unidade_medida.cod_unidade = catalogo_item.cod_unidade

        join licitacao.licitacao
        on licitacao.exercicio_mapa = mapa_item.exercicio
        and licitacao.cod_mapa = mapa_item.cod_mapa

        join licitacao.cotacao_licitacao
        on cotacao_licitacao.cod_licitacao = licitacao.cod_licitacao
        and cotacao_licitacao.cod_modalidade = licitacao.cod_modalidade
        and cotacao_licitacao.cod_entidade = licitacao.cod_entidade
        and cotacao_licitacao.exercicio_licitacao = licitacao.exercicio

        join licitacao.adjudicacao
        on adjudicacao.cod_licitacao = cotacao_licitacao.cod_licitacao
        and adjudicacao.cod_modalidade = cotacao_licitacao.cod_modalidade
        and adjudicacao.cod_entidade = cotacao_licitacao.cod_entidade
        and adjudicacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
        and adjudicacao.lote = cotacao_licitacao.lote
        and adjudicacao.cod_cotacao = cotacao_licitacao.cod_cotacao
        and adjudicacao.cod_item = cotacao_licitacao.cod_item
        and adjudicacao.exercicio_cotacao = cotacao_licitacao.exercicio_cotacao
        and adjudicacao.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor

        join licitacao.homologacao
        on homologacao.num_adjudicacao = adjudicacao.num_adjudicacao
        and homologacao.cod_entidade = adjudicacao.cod_entidade
        and homologacao.cod_modalidade = adjudicacao.cod_modalidade
        and homologacao.cod_licitacao = adjudicacao.cod_licitacao
        and homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
        and homologacao.cod_item = adjudicacao.cod_item
        and homologacao.cod_cotacao = adjudicacao.cod_cotacao
        and homologacao.lote = adjudicacao.lote
        and homologacao.exercicio_cotacao = adjudicacao.exercicio_cotacao
        and homologacao.cgm_fornecedor = adjudicacao.cgm_fornecedor

        where licitacao.exercicio = '".$this->getDado( 'exercicio' )."'
        and licitacao.cod_entidade in ( ".$this->getDado( 'cod_entidade' )." )
        and licitacao.timestamp >= to_date( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )
        and licitacao.timestamp <= to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
    ";

    return $stSql;
}

function recuperaLicitacaoFornecedores(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaLicitacaoFornecedores",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
function montaRecuperaLicitacaoFornecedores()
{
    $stSql = "
          SELECT  julgamento_item.cgm_fornecedor
               ,  sw_cgm.nom_cgm
            FROM  licitacao.licitacao
      INNER JOIN  licitacao.cotacao_licitacao
              ON  cotacao_licitacao.cod_licitacao = licitacao.cod_licitacao
             AND  cotacao_licitacao.cod_modalidade = licitacao.cod_modalidade
             AND  cotacao_licitacao.cod_entidade = licitacao.cod_entidade
             AND  cotacao_licitacao.exercicio_licitacao = licitacao.exercicio
      INNER JOIN  compras.julgamento_item
              ON  julgamento_item.exercicio = cotacao_licitacao.exercicio_cotacao
             AND  julgamento_item.cod_cotacao = cotacao_licitacao.cod_cotacao
             AND  julgamento_item.cod_item = cotacao_licitacao.cod_item
             AND  julgamento_item.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor
             AND  julgamento_item.lote = cotacao_licitacao.lote
             AND  julgamento_item.ordem = 1
      INNER JOIN  sw_cgm
              ON  sw_cgm.numcgm = julgamento_item.cgm_fornecedor

           WHERE  EXISTS (   SELECT  1
                                  FROM  licitacao.homologacao
                             LEFT JOIN  licitacao.homologacao_anulada
                                    ON  homologacao_anulada.num_homologacao = homologacao.num_homologacao
                                   AND  homologacao_anulada.num_adjudicacao = homologacao.num_adjudicacao
                                   AND  homologacao_anulada.cod_entidade = homologacao.cod_entidade
                                   AND  homologacao_anulada.cod_modalidade = homologacao.cod_modalidade
                                   AND  homologacao_anulada.cod_licitacao = homologacao.cod_licitacao
                                   AND  homologacao_anulada.exercicio_licitacao = homologacao.exercicio_licitacao
                                   AND  homologacao_anulada.cod_item = homologacao.cod_item
                                   AND  homologacao_anulada.cod_cotacao = homologacao.cod_cotacao
                                   AND  homologacao_anulada.lote = homologacao.lote
                                   AND  homologacao_anulada.exercicio_cotacao = homologacao.exercicio_cotacao
                                   AND  homologacao_anulada.cgm_fornecedor = homologacao.cgm_fornecedor
                                 WHERE  homologacao.cod_entidade = licitacao.cod_entidade
                                   AND  homologacao.cod_modalidade = licitacao.cod_modalidade
                                   AND  homologacao.cod_licitacao = licitacao.cod_licitacao
                                   AND  homologacao.exercicio_licitacao = licitacao.exercicio
                                   AND  homologacao.lote = julgamento_item.lote
                                   AND  homologacao.cod_cotacao = julgamento_item.cod_cotacao
                                   AND  homologacao.cod_item = julgamento_item.cod_item
                                   AND  homologacao.cgm_fornecedor = julgamento_item.cgm_fornecedor
                                   AND  julgamento_item.ordem = 1
                                   AND  homologacao_anulada.num_homologacao is null
                            )
            AND NOT EXISTS  ( SELECT 1
                                  FROM licitacao.contrato, licitacao.contrato_licitacao
                                 WHERE contrato.num_contrato = contrato_licitacao.num_contrato
                                   AND contrato.exercicio = contrato_licitacao.exercicio
                                   AND contrato.cod_entidade = contrato_licitacao.cod_entidade

                                 AND contrato_licitacao.cod_licitacao = licitacao.cod_licitacao
                                 AND contrato_licitacao.cod_modalidade = licitacao.cod_modalidade
                                 AND contrato_licitacao.cod_entidade = licitacao.cod_entidade
                                 AND contrato_licitacao.exercicio = licitacao.exercicio

                                   AND contrato.cgm_contratado = cotacao_licitacao.cgm_fornecedor
                                 -- a condição abaixo serve para listar tb os fornedores que tiveram contratos anulados
                                 AND not exists ( select 1
                                                    from licitacao.contrato_anulado
                                                   where contrato.num_contrato = contrato_anulado.num_contrato
                                                     and contrato.exercicio    = contrato_anulado.exercicio
                                                     and contrato.cod_entidade = contrato_anulado.cod_entidade )
                               ) AND
    ";
    $stFiltro = "";
    if ( $this->getDado( 'cod_licitacao' ) ) {
        $stFiltro .= " licitacao.cod_licitacao = ".$this->getDado( 'cod_licitacao' )." AND ";
    }
    if ( $this->getDado( 'cod_modalidade' ) ) {
        $stFiltro .= " licitacao.cod_modalidade = ".$this->getDado( 'cod_modalidade' )." AND ";
    }
    if ( $this->getDado( 'cod_entidade' ) ) {
        $stFiltro .= " licitacao.cod_entidade = ".$this->getDado( 'cod_entidade' )." AND ";
    }
    if ( $this->getDado( 'exercicio' ) ) {
        $stFiltro .= " licitacao.exercicio = '".$this->getDado( 'exercicio' )."' AND ";
    }
    if ($stFiltro != '') {
        $stSql .= ' '.substr( $stFiltro, 0, strlen($stFiltro) -4 );
    }
    $stSql .= " GROUP BY    julgamento_item.cgm_fornecedor,sw_cgm.nom_cgm ";

    return $stSql;
}

function recuperaProcessoLicitatorioEsfinge(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    return $this->executaRecupera( "montaRecuperaProcessoLicitatorioEsfinge", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

function montaRecuperaProcessoLicitatorioEsfinge()
{
    $stSql = "
            select licitacao.cod_licitacao
                 , edital.num_edital
                 , licitacao.cod_tipo_licitacao
                 , comissao_licitacao.cod_comissao
                 , to_char(norma.dt_assinatura,'dd/mm/yyyy') as dt_assinatura
                 , licitacao.cod_tipo_objeto
                 , licitacao.cod_modalidade
                 , licitacao.cod_criterio
                 , to_char(edital.dt_abertura_propostas,'dd/mm/yyyy') as dt_abertura_propostas
                 , to_char(contrato.dt_assinatura,'dd/mm/yyyy') as contrato_dt_assinatura
                 , objeto.descricao
                 , licitacao.vl_cotado
                 , to_char(max_timestamp_homologacao.timestamp,'dd/mm/yyyy') as timestamp_homologacao
                 , contrato.valor_garantia
                 , pregoeiro_licitacao.cpf
              from licitacao.licitacao

              join licitacao.edital
                on edital.cod_licitacao = licitacao.cod_licitacao
               and edital.cod_modalidade = licitacao.cod_modalidade
               and edital.cod_entidade = licitacao.cod_entidade
               and edital.exercicio_licitacao = licitacao.exercicio

              join licitacao.comissao_licitacao
                on comissao_licitacao.cod_licitacao = licitacao.cod_licitacao
               and comissao_licitacao.cod_modalidade = licitacao.cod_modalidade
               and comissao_licitacao.cod_entidade = licitacao.cod_entidade
               and comissao_licitacao.exercicio = licitacao.exercicio

              join licitacao.comissao
                on comissao.cod_comissao = comissao_licitacao.cod_comissao

              join licitacao.tipo_comissao
                on tipo_comissao.cod_tipo_comissao = comissao.cod_tipo_comissao
               and tipo_comissao.cod_tipo_comissao <> 4

              join normas.norma
                on norma.cod_norma = comissao.cod_norma

             join licitacao.contrato_licitacao
                on contrato_licitacao.cod_licitacao = licitacao.cod_licitacao
               and contrato_licitacao.cod_modalidade = licitacao.cod_modalidade
               and contrato_licitacao.cod_entidade = licitacao.cod_entidade
               and contrato_licitacao.exercicio = licitacao.exercicio

              join licitacao.contrato
                on contrato.num_contrato = contrato_licitacao.num_contrato
               and contrato.cod_entidade = contrato_licitacao.cod_entidade
               and contrato.exercicio = contrato_licitacao.exercicio

              join compras.objeto
                on objeto.cod_objeto = licitacao.cod_objeto

              join (
                      select homologacao.cod_licitacao
                           , homologacao.cod_modalidade
                           , homologacao.cod_entidade
                           , homologacao.exercicio_licitacao
                           , max(homologacao.timestamp) as timestamp
                        from licitacao.homologacao
                       where homologacao.timestamp < to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
                       group by homologacao.cod_licitacao
                           , homologacao.cod_modalidade
                           , homologacao.cod_entidade
                           , homologacao.exercicio_licitacao
                  ) as max_timestamp_homologacao
               on max_timestamp_homologacao.cod_licitacao = licitacao.cod_licitacao
              and max_timestamp_homologacao.cod_modalidade = licitacao.cod_modalidade
              and max_timestamp_homologacao.cod_entidade = licitacao.cod_entidade
              and max_timestamp_homologacao.exercicio_licitacao = licitacao.exercicio

             left join (
                          select licitacao.cod_licitacao
                               , licitacao.cod_modalidade
                               , licitacao.cod_entidade
                               , licitacao.exercicio
                               , sw_cgm_pessoa_fisica.cpf
                            from licitacao.licitacao

                            join licitacao.comissao_licitacao
                              on comissao_licitacao.cod_licitacao = licitacao.cod_licitacao
                             and comissao_licitacao.cod_modalidade = licitacao.cod_modalidade
                             and comissao_licitacao.cod_entidade = licitacao.cod_entidade
                             and comissao_licitacao.exercicio = licitacao.exercicio

                            join licitacao.comissao_membros
                              on comissao_membros.cod_comissao = comissao_licitacao.cod_comissao
                             and comissao_membros.cod_tipo_membro = 3

                            join sw_cgm_pessoa_fisica
                              on comissao_membros.numcgm = sw_cgm_pessoa_fisica.numcgm
                       ) as pregoeiro_licitacao
                    on pregoeiro_licitacao.cod_licitacao = licitacao.cod_licitacao

            where licitacao.exercicio = '".$this->getDado( 'exercicio' )."'
              and licitacao.cod_entidade in ( ".$this->getDado( 'cod_entidade' )." )
              and licitacao.timestamp >= to_date( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )
              and licitacao.timestamp <= to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
        ";

        return $stSql;
}

function recuperaLicitacaoNaoHomologada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLicitacaoNaoHomologada().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaLicitacaoNaoHomologada()
{
    $stSql = " SELECT DISTINCT licitacao.cod_licitacao,";
    $stSql.= "        licitacao.exercicio,";
    $stSql.= "        licitacao.cod_modalidade,";
    $stSql.= "        licitacao.cod_entidade,";
    $stSql.= "        to_char(licitacao.edital.dt_aprovacao_juridico,'dd/mm/yyyy') as dt_aprovacao_juridico,";
    $stSql.= "        licitacao.licitacao.cod_objeto,";
    $stSql.= "        licitacao.licitacao.timestamp";
    $stSql.= "   FROM licitacao.edital";
    $stSql.= "  INNER JOIN licitacao.licitacao";
    $stSql.= "    ON licitacao.cod_licitacao  = edital.cod_licitacao";
    $stSql.= "   AND licitacao.cod_modalidade = edital.cod_modalidade";
    $stSql.= "   AND licitacao.cod_entidade   = edital.cod_entidade";
    $stSql.= "   AND licitacao.exercicio      = edital.exercicio_licitacao";

    $stSql.= "  LEFT JOIN licitacao.homologacao";
    $stSql.= "	       ON licitacao.cod_licitacao = homologacao.cod_licitacao";
    $stSql.= " 	      AND licitacao.cod_modalidade = homologacao.cod_modalidade";
    $stSql.= " 	      AND licitacao.cod_entidade = homologacao.cod_entidade";
    $stSql.= " 	      AND licitacao.exercicio = homologacao.exercicio_licitacao";

    $stSql.= " INNER JOIN compras.modalidade";
    $stSql.= "         ON modalidade.cod_modalidade = licitacao.cod_modalidade";
    $stSql.= " INNER JOIN orcamento.entidade";
    $stSql.= "         ON ( entidade.exercicio = licitacao.exercicio";
    $stSql.= "        AND entidade.cod_entidade = licitacao.cod_entidade)";
    $stSql.= " INNER JOIN sw_cgm";
    $stSql.= "         ON ( sw_cgm.numcgm = entidade.numcgm )";
    $stSql.= " WHERE licitacao.exercicio = '".$this->getDado('exercicio')."'";
    $stSql.= "   AND licitacao.cod_licitacao = '".$this->getDado('cod_licitacao')."'";
    $stSql.= "   AND licitacao.cod_modalidade = '".$this->getDado('cod_modalidade')."'";
    $stSql.= "   AND licitacao.cod_entidade = '".$this->getDado('cod_entidade')."'";
    $stSql.= "   AND ( homologacao.num_homologacao is null )";
    $stSql.= "    or ( EXISTS ( select 1";
    $stSql.= "			 FROM licitacao.homologacao_anulada";
    $stSql.= "  	        WHERE licitacao.cod_licitacao = homologacao_anulada.cod_licitacao";
    $stSql.= "                    AND licitacao.cod_modalidade = homologacao_anulada.cod_modalidade";
    $stSql.= "	                  AND licitacao.cod_entidade = homologacao_anulada.cod_entidade";
    $stSql.= "                    AND licitacao.exercicio = homologacao_anulada.exercicio_licitacao";
    $stSql.= "              )";
    $stSql.= "      )";

    return $stSql;
}

  public function recuperaItensDetalhesAutorizacaoEmpenhoLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;
      $stSql = $this->montaRecuperaItensDetalhesAutorizacaoEmpenhoLicitacao().$stFiltro.$stOrdem;
      $this->stDebug = $stSql;
      $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

      return $obErro;
  }

  public function montaRecuperaItensDetalhesAutorizacaoEmpenhoLicitacao()
  {
      $stSql = "
           SELECT mapa_item.cod_solicitacao
                , mapa_item.cod_item
                , mapa_item.cod_centro
                , CASE
                        WHEN mapa_item.lote = 0 THEN
                            'Unico'::varchar
                        ELSE
                            mapa_item.lote::varchar
                    END AS lote
                , mapa_item_dotacao.quantidade - coalesce (mapa_item_anulacao.quantidade,0)::numeric(14,2) as quantidade
                , (( cotacao_fornecedor_item.vl_cotacao / cotacao_item.quantidade ) * (mapa_item_dotacao.quantidade - coalesce (mapa_item_anulacao.quantidade,0) ))::numeric(14,2) AS vl_total
                , ( cotacao_fornecedor_item.vl_cotacao / cotacao_item.quantidade )::numeric(14,2) AS vl_unitario
                , conta_despesa.cod_estrutural
                , julgamento_item.cgm_fornecedor
                , ( SELECT nom_cgm FROM sw_cgm WHERE numcgm = julgamento_item.cgm_fornecedor ) AS fornecedor
             FROM licitacao.licitacao
             JOIN compras.mapa
               ON licitacao.cod_mapa = mapa.cod_mapa
              AND licitacao.exercicio_mapa = mapa.exercicio
             JOIN compras.mapa_item
               ON mapa_item.cod_mapa = mapa.cod_mapa
              AND mapa_item.exercicio = mapa.exercicio
             JOIN compras.mapa_item_dotacao
               ON mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
              AND mapa_item_dotacao.exercicio             = mapa_item.exercicio
              AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
              AND mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
              AND mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
              AND mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
              AND mapa_item_dotacao.cod_item              = mapa_item.cod_item
              AND mapa_item_dotacao.lote                  = mapa_item.lote
        LEFT JOIN compras.mapa_item_anulacao
               ON mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio
              AND mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa
              AND mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
              AND mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade
              AND mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
              AND mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro
              AND mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item
              AND mapa_item_anulacao.lote                  = mapa_item_dotacao.lote
              AND mapa_item_anulacao.cod_conta             = mapa_item_dotacao.cod_conta
              AND mapa_item_anulacao.cod_despesa           = mapa_item_dotacao.cod_despesa
             JOIN compras.mapa_cotacao
               ON mapa_cotacao.cod_mapa       = mapa.cod_mapa
              AND mapa_cotacao.exercicio_mapa = mapa.exercicio
             JOIN compras.julgamento_item
               ON julgamento_item.cod_cotacao = mapa_cotacao.cod_cotacao
              AND julgamento_item.exercicio   = mapa_cotacao.exercicio_cotacao
              AND julgamento_item.cod_item    = mapa_item.cod_item
              AND julgamento_item.lote        = mapa_item.lote
             JOIN compras.cotacao_fornecedor_item
               ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
              AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
              AND cotacao_fornecedor_item.lote           = julgamento_item.lote
              AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
              AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
             JOIN compras.solicitacao_item
               ON solicitacao_item.cod_solicitacao = mapa_item.cod_solicitacao
              AND solicitacao_item.exercicio       = mapa_item.exercicio_solicitacao
              AND solicitacao_item.cod_entidade    = mapa_item.cod_entidade
              AND solicitacao_item.cod_centro      = mapa_item.cod_centro
              AND solicitacao_item.cod_item        = mapa_item.cod_item
             JOIN compras.solicitacao_item_dotacao
               ON solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
              AND solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
              AND solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
              AND solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
              AND solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item
              AND solicitacao_item_dotacao.cod_despesa     = mapa_item_dotacao.cod_despesa
             JOIN compras.cotacao_item
               ON cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
              AND cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
              AND cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
              AND cotacao_item.lote        = cotacao_fornecedor_item.lote
             JOIN orcamento.conta_despesa
               ON conta_despesa.cod_conta  = solicitacao_item_dotacao.cod_conta
              AND conta_despesa.exercicio  = solicitacao_item_dotacao.exercicio
             JOIN orcamento.despesa
               ON despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
              AND despesa.exercicio   = solicitacao_item_dotacao.exercicio
            WHERE julgamento_item.ordem = 1
      ";

      return $stSql;
  }

  public function recuperaItensDetalhesAutorizacaoEmpenhoParcialLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;
      $stGroupBy = " GROUP BY mapa_item.cod_solicitacao
                            , mapa_item.cod_item
                            , mapa_item.cod_centro
                            , centro_custo.descricao
                            , mapa_item.lote
                            , mapa_item_dotacao.quantidade
                            , mapa_item_anulacao.quantidade
                            , cotacao_fornecedor_item.vl_cotacao
                            , cotacao_item.quantidade
                            , desdobramento.cod_conta
                            , desdobramento.cod_estrutural
                            , desdobramento.descricao
                            , julgamento_item.cgm_fornecedor
                            , mapa_item_dotacao.cod_despesa
                            , mapa_item_dotacao.exercicio
                            , conta_despesa.cod_estrutural
                            , conta_despesa.descricao
                            , despesa_atual.cod_despesa
                            , conta_despesa_atual.descricao
                            , conta_despesa_atual.cod_estrutural
                            , despesa_atual.exercicio
                            , homologacao.cod_cotacao
                            , homologacao.exercicio_cotacao
                            , cod_despesa_empenho
                            , cod_conta_empenho
                            , pre_empenho_despesa.total_quantidade_despesa ";
      $stSql = $this->montaRecuperaItensDetalhesAutorizacaoEmpenhoParcialLicitacao().$stFiltro.$stGroupBy.$stOrdem;
      $this->stDebug = $stSql;
      $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

      return $obErro;
  }

  public function montaRecuperaItensDetalhesAutorizacaoEmpenhoParcialLicitacao()
  {
      $stSql = "
           SELECT mapa_item.cod_solicitacao
                , mapa_item.cod_item
                , mapa_item.cod_centro
                , centro_custo.descricao AS nom_centro
                , CASE
                        WHEN mapa_item.lote = 0 THEN
                            'Unico'::varchar
                        ELSE
                            mapa_item.lote::varchar
                  END AS lote
                , COALESCE(cotacao_item.quantidade, 0) AS quantidade
                , COALESCE(mapa_item_dotacao.quantidade, 0.00) - COALESCE(mapa_item_anulacao.quantidade,0)::numeric(14,2) as quantidade_total_autorizada_dotacao
                , (COALESCE(mapa_item_dotacao.quantidade, 0.00) - COALESCE(mapa_item_anulacao.quantidade,0)::numeric(14,2)) - COALESCE(pre_empenho_despesa.total_quantidade_despesa, 0.00) AS quantidade_restante_dotacao 
                , cotacao_fornecedor_item.vl_cotacao::numeric(14,2) AS vl_total
                , ( cotacao_fornecedor_item.vl_cotacao / cotacao_item.quantidade )::numeric(14,2) AS vl_unitario
                , sum(coalesce(item_pre_empenho.quantidade, 0.00)) as quantidade_autorizacoes
                , desdobramento.cod_conta as cod_desdobramento
                , desdobramento.cod_estrutural as desdobramento
                , desdobramento.descricao as nom_desdobramento
                , mapa_item_dotacao.cod_despesa
                , conta_despesa.descricao as nom_despesa
                , conta_despesa.cod_estrutural as estrutural_despesa
                , mapa_item_dotacao.exercicio as exercicio_despesa
                , empenho.fn_saldo_dotacao(mapa_item_dotacao.exercicio,mapa_item_dotacao.cod_despesa) as saldo_despesa
                , despesa_atual.cod_despesa as cod_despesa_atual
                , conta_despesa_atual.descricao as nom_despesa_atual
                , conta_despesa_atual.cod_estrutural as estrutural_despesa_atual
                , despesa_atual.exercicio as exercicio_despesa_atual
                , empenho.fn_saldo_dotacao(despesa_atual.exercicio,despesa_atual.cod_despesa) as saldo_despesa_atual
                , julgamento_item.cgm_fornecedor
                , ( SELECT nom_cgm FROM sw_cgm WHERE numcgm = julgamento_item.cgm_fornecedor ) AS fornecedor
                , ((coalesce(cotacao_item.quantidade, 0.00) - sum(coalesce(item_pre_empenho.quantidade, 0.00)))
                   *
                   (cotacao_fornecedor_item.vl_cotacao / coalesce(cotacao_item.quantidade, 0.00)))::numeric(14,2) as vl_cotacao_saldo
                , coalesce(cotacao_item.quantidade, 0.00) - sum(coalesce(item_pre_empenho.quantidade, 0.00)) as quantidade_saldo
                , homologacao.cod_cotacao
                , homologacao.exercicio_cotacao
                , CASE WHEN pre_empenho_despesa.countDespesa = 1
                            THEN pre_empenho_despesa.cod_despesa[1]
                  END AS cod_despesa_empenho
                , CASE WHEN pre_empenho_despesa.countDespesa = 1
                            THEN pre_empenho_despesa.cod_conta[1]
                  END AS cod_conta_empenho

             FROM licitacao.licitacao

       INNER JOIN compras.mapa
               ON licitacao.cod_mapa = mapa.cod_mapa
              AND licitacao.exercicio_mapa = mapa.exercicio

       INNER JOIN compras.mapa_item
               ON mapa_item.cod_mapa = mapa.cod_mapa
              AND mapa_item.exercicio = mapa.exercicio

        LEFT JOIN compras.mapa_item_dotacao
               ON mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
              AND mapa_item_dotacao.exercicio             = mapa_item.exercicio
              AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
              AND mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
              AND mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
              AND mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
              AND mapa_item_dotacao.cod_item              = mapa_item.cod_item
              AND mapa_item_dotacao.lote                  = mapa_item.lote

        LEFT JOIN compras.mapa_item_anulacao
               ON mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio
              AND mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa
              AND mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
              AND mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade
              AND mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
              AND mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro
              AND mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item
              AND mapa_item_anulacao.lote                  = mapa_item_dotacao.lote
              AND mapa_item_anulacao.cod_conta             = mapa_item_dotacao.cod_conta
              AND mapa_item_anulacao.cod_despesa           = mapa_item_dotacao.cod_despesa

       INNER JOIN compras.mapa_cotacao
               ON mapa_cotacao.cod_mapa       = mapa.cod_mapa
              AND mapa_cotacao.exercicio_mapa = mapa.exercicio

             JOIN compras.julgamento_item
               ON julgamento_item.cod_cotacao = mapa_cotacao.cod_cotacao
              AND julgamento_item.exercicio   = mapa_cotacao.exercicio_cotacao
              AND julgamento_item.cod_item    = mapa_item.cod_item
              AND julgamento_item.lote        = mapa_item.lote

       INNER JOIN compras.cotacao_fornecedor_item
               ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
              AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
              AND cotacao_fornecedor_item.lote           = julgamento_item.lote
              AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
              AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor

       INNER JOIN compras.solicitacao_item
               ON solicitacao_item.cod_solicitacao = mapa_item.cod_solicitacao
              AND solicitacao_item.exercicio       = mapa_item.exercicio_solicitacao
              AND solicitacao_item.cod_entidade    = mapa_item.cod_entidade
              AND solicitacao_item.cod_centro      = mapa_item.cod_centro
              AND solicitacao_item.cod_item        = mapa_item.cod_item

        LEFT JOIN compras.solicitacao_item_dotacao
               ON solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
              AND solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
              AND solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
              AND solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
              AND solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item
              AND solicitacao_item_dotacao.cod_despesa     = mapa_item_dotacao.cod_despesa

       INNER JOIN compras.cotacao_item
               ON cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
              AND cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
              AND cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
              AND cotacao_item.lote        = cotacao_fornecedor_item.lote

        LEFT JOIN orcamento.conta_despesa as desdobramento
               ON desdobramento.cod_conta  = solicitacao_item_dotacao.cod_conta
              AND desdobramento.exercicio  = solicitacao_item_dotacao.exercicio

        LEFT JOIN orcamento.despesa
               ON despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
              AND despesa.exercicio   = solicitacao_item_dotacao.exercicio

        LEFT JOIN orcamento.conta_despesa
               ON conta_despesa.cod_conta  = despesa.cod_conta
              AND conta_despesa.exercicio  = despesa.exercicio

        LEFT JOIN orcamento.conta_despesa as conta_despesa_atual
               ON conta_despesa_atual.cod_estrutural  = conta_despesa.cod_estrutural
              AND conta_despesa_atual.exercicio       = '".Sessao::getExercicio()."'

        LEFT JOIN orcamento.despesa as despesa_atual
               ON despesa_atual.cod_conta 	 = conta_despesa_atual.cod_conta
              AND despesa_atual.exercicio    = conta_despesa_atual.exercicio
              AND despesa_atual.cod_recurso  = despesa.cod_recurso
              AND despesa_atual.cod_programa = despesa.cod_programa
              AND despesa_atual.num_pao		 = despesa.num_pao
              AND despesa_atual.cod_funcao 	 = despesa.cod_funcao

        LEFT JOIN empenho.item_pre_empenho_julgamento
               ON item_pre_empenho_julgamento.exercicio_julgamento = cotacao_fornecedor_item.exercicio
              AND item_pre_empenho_julgamento.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
              AND item_pre_empenho_julgamento.cod_item             = cotacao_fornecedor_item.cod_item
              AND item_pre_empenho_julgamento.lote                 = cotacao_fornecedor_item.lote
              AND item_pre_empenho_julgamento.cgm_fornecedor       = cotacao_fornecedor_item.cgm_fornecedor

        LEFT JOIN ( SELECT item_pre_empenho_julgamento.exercicio_julgamento
                         , item_pre_empenho_julgamento.cod_cotacao
                         , item_pre_empenho_julgamento.cod_item 
                         , item_pre_empenho_julgamento.lote
                         , item_pre_empenho_julgamento.cgm_fornecedor
                         , array_length(publico.concatenar_array( pre_empenho_despesa.cod_despesa ), 1) AS countDespesa
                         , publico.concatenar_array( pre_empenho_despesa.cod_despesa ) AS cod_despesa
                         , publico.concatenar_array( pre_empenho_despesa.cod_conta ) AS cod_conta
                         , SUM(item_pre_empenho.quantidade) AS total_quantidade_despesa
                      
                      FROM empenho.item_pre_empenho_julgamento
                      
                INNER JOIN empenho.pre_empenho_despesa
                        ON pre_empenho_despesa.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
                       AND pre_empenho_despesa.exercicio       = item_pre_empenho_julgamento.exercicio
                       
                INNER JOIN empenho.autorizacao_empenho
                        ON autorizacao_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
                       AND autorizacao_empenho.exercicio       = item_pre_empenho_julgamento.exercicio
                       
                INNER JOIN empenho.item_pre_empenho 
                        ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
                       AND item_pre_empenho.exercicio       = item_pre_empenho_julgamento.exercicio 
                       AND item_pre_empenho.num_item        = item_pre_empenho_julgamento.num_item

                     /*
                      *ESTÁ COMENTADO, POIS FOI DEFINIDO, POR HORA, QUE OS ITENS DE AUTORIZAÇÕES ANULADAS
                      *NÃO FICAM DISPONIVEIS NOVAMENTE PARA AUTORIZAÇÃO DE EMPENHO.
                 LEFT JOIN empenho.autorizacao_anulada
                        ON autorizacao_anulada.exercicio       = autorizacao_empenho.exercicio
                       AND autorizacao_anulada.cod_entidade    = autorizacao_empenho.cod_entidade
                       AND autorizacao_anulada.cod_autorizacao = autorizacao_empenho.cod_autorizacao
                      WHERE autorizacao_anulada.cod_autorizacao IS NULL
                     */ \n";
            
        if ($this->getDado('inCodDespesa')) {
            $stSql .= " WHERE pre_empenho_despesa.cod_despesa = ".$this->getDado('inCodDespesa')." \n";
        }

        $stSql .= "
                  GROUP BY item_pre_empenho_julgamento.exercicio_julgamento
                         , item_pre_empenho_julgamento.cod_cotacao
                         , item_pre_empenho_julgamento.cod_item 
                         , item_pre_empenho_julgamento.lote
                         , item_pre_empenho_julgamento.cgm_fornecedor
                  ) AS pre_empenho_despesa
               ON pre_empenho_despesa.exercicio_julgamento = cotacao_fornecedor_item.exercicio
              AND pre_empenho_despesa.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
              AND pre_empenho_despesa.cod_item             = cotacao_fornecedor_item.cod_item
              AND pre_empenho_despesa.lote                 = cotacao_fornecedor_item.lote
              AND pre_empenho_despesa.cgm_fornecedor       = cotacao_fornecedor_item.cgm_fornecedor

        LEFT JOIN empenho.item_pre_empenho
               ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
              AND item_pre_empenho.exercicio       = item_pre_empenho_julgamento.exercicio
              AND item_pre_empenho.num_item        = item_pre_empenho_julgamento.num_item
        
        /*
        *ESTÁ COMENTADO, POIS FOI DEFINIDO, POR HORA, QUE OS ITENS DE AUTORIZAÇÕES ANULADAS
        *NÃO FICAM DISPONIVEIS NOVAMENTE PARA AUTORIZAÇÃO DE EMPENHO.
        LEFT JOIN empenho.autorizacao_empenho
               ON autorizacao_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
              AND autorizacao_empenho.exercicio       = item_pre_empenho_julgamento.exercicio

        LEFT JOIN empenho.autorizacao_anulada
               ON autorizacao_anulada.exercicio       = autorizacao_empenho.exercicio
              AND autorizacao_anulada.cod_entidade    = autorizacao_empenho.cod_entidade
              AND autorizacao_anulada.cod_autorizacao = autorizacao_empenho.cod_autorizacao
        */

       INNER JOIN almoxarifado.centro_custo
               ON centro_custo.cod_centro = mapa_item.cod_centro

       INNER JOIN licitacao.cotacao_licitacao
               ON cotacao_licitacao.cod_licitacao 	    = licitacao.cod_licitacao
              AND cotacao_licitacao.cod_modalidade 	    = licitacao.cod_modalidade
              AND cotacao_licitacao.cod_entidade 	    = licitacao.cod_entidade
              AND cotacao_licitacao.exercicio_licitacao = licitacao.exercicio
              AND cotacao_licitacao.cod_item            = cotacao_fornecedor_item.cod_item
              AND cotacao_licitacao.cgm_fornecedor 	    = cotacao_fornecedor_item.cgm_fornecedor
              AND cotacao_licitacao.cod_cotacao         = cotacao_fornecedor_item.cod_cotacao
              AND cotacao_licitacao.exercicio_cotacao   = cotacao_fornecedor_item.exercicio
              AND cotacao_licitacao.lote                = cotacao_fornecedor_item.lote

       INNER JOIN licitacao.adjudicacao
               ON adjudicacao.cod_licitacao         = cotacao_licitacao.cod_licitacao
              AND adjudicacao.cod_modalidade        = cotacao_licitacao.cod_modalidade
              AND adjudicacao.cod_entidade          = cotacao_licitacao.cod_entidade
              AND adjudicacao.exercicio_licitacao   = cotacao_licitacao.exercicio_licitacao
              AND adjudicacao.lote                  = cotacao_licitacao.lote
              AND adjudicacao.cod_cotacao           = cotacao_licitacao.cod_cotacao
              AND adjudicacao.cod_item              = cotacao_licitacao.cod_item
              AND adjudicacao.exercicio_cotacao     = cotacao_licitacao.exercicio_cotacao
              AND adjudicacao.cgm_fornecedor        = cotacao_licitacao.cgm_fornecedor

       INNER JOIN licitacao.homologacao
               ON homologacao.num_adjudicacao       = adjudicacao.num_adjudicacao
              AND homologacao.cod_entidade          = adjudicacao.cod_entidade
              AND homologacao.cod_modalidade        = adjudicacao.cod_modalidade
              AND homologacao.cod_licitacao         = adjudicacao.cod_licitacao 
              AND homologacao.exercicio_licitacao   = adjudicacao. exercicio_licitacao
              AND homologacao.cod_item              = adjudicacao.cod_item
              AND homologacao.cod_cotacao           = adjudicacao.cod_cotacao
              AND homologacao.lote                  = adjudicacao.lote
              AND homologacao.exercicio_cotacao     = adjudicacao.exercicio_cotacao
              AND homologacao.cgm_fornecedor        = adjudicacao.cgm_fornecedor

            WHERE julgamento_item.ordem = 1
              --AND autorizacao_anulada.cod_autorizacao IS NULL
      ";

      return $stSql;
  }

  public function recuperaLicitacaoResponsavelTCMGO(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = " ORDER BY  ll.cod_licitacao ";
        $stSql = $this->montaRecuperaLicitacaoResponsavelTCMGO().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        return $obErro;
  }

  public function montaRecuperaLicitacaoResponsavelTCMGO()
  {
        $stSql = " SELECT ll.cod_entidade                                                    
                      , ll.cod_licitacao                                                   
                      , ll.cod_processo||'/'||ll.exercicio_processo as processo            
                      , cm.descricao                                                       
                      , cm.cod_modalidade                                                  
                      , ll.cod_modalidade                                                  
                      , ll.cod_mapa||'/'||ll.exercicio_mapa as mapa_compra                 
                      , ll.cod_entidade||' - '||cgm.nom_cgm as entidade                    
                      , ll.cod_modalidade||' - '||cm.descricao as modalidade               
                      , ll.cod_objeto                                                      
                      , ll.cod_regime                                                      
                      , ll.timestamp                                                       
                      , ll.cod_tipo_objeto                                                 
                      , ll.cod_tipo_licitacao                                              
                      , ll.cod_criterio                                                    
                      , ll.vl_cotado                                                       
                      , ll.exercicio                                                       
                      , to_char(ll.timestamp::date, 'dd/mm/yyyy') as dt_licitacao          
                      , LPAD(ll.num_orgao::VARCHAR, 2, '0') || '.' || LPAD(ll.num_unidade::VARCHAR, 2, '0') AS unidade_orcamentaria       
                      , homologadas.dt_homologacao
                      , ll.tipo_chamada_publica
                      , comissao_licitacao.cod_comissao
          
                   FROM licitacao.licitacao as ll
                
              LEFT JOIN licitacao.licitacao_anulada as la                            
                     ON ll.cod_licitacao  = la.cod_licitacao      
                    AND ll.cod_modalidade = la.cod_modalidade    
                    AND ll.cod_entidade   = la.cod_entidade                             
                    AND ll.exercicio      = la.exercicio
                    
              LEFT JOIN (   SELECT cotacao_licitacao.cod_licitacao 
                                 , cotacao_licitacao.cod_modalidade 
                                 , cotacao_licitacao.cod_entidade 
                                 , cotacao_licitacao.exercicio_licitacao 
                                 , homologacao.homologado          
                                 , to_char(homologacao.timestamp::date, 'dd/mm/yyyy') as dt_homologacao    
                              
                              FROM licitacao.cotacao_licitacao                      

                        INNER JOIN licitacao.adjudicacao
                                ON adjudicacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                               AND adjudicacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                               AND adjudicacao.cod_entidade        = cotacao_licitacao.cod_entidade
                               AND adjudicacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
                               AND adjudicacao.lote                = cotacao_licitacao.lote
                               AND adjudicacao.cod_cotacao         = cotacao_licitacao.cod_cotacao
                               AND adjudicacao.cod_item            = cotacao_licitacao.cod_item
                               AND adjudicacao.exercicio_cotacao   = cotacao_licitacao.exercicio_cotacao
                               AND adjudicacao.cgm_fornecedor      = cotacao_licitacao.cgm_fornecedor

                       INNER JOIN licitacao.homologacao 
                               ON homologacao.num_adjudicacao     = adjudicacao.num_adjudicacao
                              AND homologacao.cod_entidade        = adjudicacao.cod_entidade
                              AND homologacao.cod_modalidade      = adjudicacao.cod_modalidade
                              AND homologacao.cod_licitacao       = adjudicacao.cod_licitacao
                              AND homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                              AND homologacao.cod_item            = adjudicacao.cod_item
                              AND homologacao.cod_cotacao         = adjudicacao.cod_cotacao
                              AND homologacao.lote                = adjudicacao.lote
                              AND homologacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                              AND homologacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
                              
                         GROUP BY cotacao_licitacao.cod_licitacao 
                                , cotacao_licitacao.cod_modalidade 
                                , cotacao_licitacao.cod_entidade 
                                , cotacao_licitacao.exercicio_licitacao 
                                , homologacao.homologado
                                , homologacao.timestamp
                ) AS homologadas
                ON homologadas.cod_licitacao       = ll.cod_licitacao 
               AND homologadas.cod_modalidade      = ll.cod_modalidade
               AND homologadas.cod_entidade        = ll.cod_entidade
               AND homologadas.exercicio_licitacao = ll.exercicio
        
        INNER JOIN compras.modalidade as cm 
                ON ll.cod_modalidade = cm.cod_modalidade
               
        INNER JOIN orcamento.entidade as oe
                ON ll.cod_entidade = oe.cod_entidade                                  
               AND ll.exercicio    = oe.exercicio               
                
        INNER JOIN sw_cgm as cgm
                ON oe.numcgm = cgm.numcgm
               
        JOIN licitacao.comissao_licitacao
            ON comissao_licitacao.cod_licitacao     = ll.cod_licitacao
            AND comissao_licitacao.cod_modalidade   = ll.cod_modalidade
            AND comissao_licitacao.cod_entidade     = ll.cod_entidade
            AND comissao_licitacao.exercicio        = ll.exercicio

        WHERE 1 = 1 \n ";
      
      $stConsulta = Sessao::read('consulta');
      if (!$stConsulta) {
          $stSql .="    AND la.cod_licitacao is NULL                    \n";
      }
      Sessao::remove('consulta');

       if ($this->getDado('cod_homologada') == 2) {
             $stSql.=  " AND homologadas.homologado = 't'             \n";
       } else if ($this->getDado('cod_homologada') == 3) {
             $stSql.=  " AND ( homologadas.homologado = 'f' 
                                OR NOT EXISTS (
                                        SELECT 1
                                          FROM licitacao.homologacao 
                                         WHERE ll.cod_licitacao    = homologacao.cod_licitacao 
                                             AND ll.cod_modalidade = homologacao.cod_modalidade 
                                             AND ll.cod_entidade   = homologacao.cod_entidade 
                                             AND ll.exercicio      = homologacao.exercicio_licitacao 
                                        ) 
                                      )       \n";
       }
       
      if ($this->getDado('cod_entidade')) {
        $stSql.=  "AND ll.cod_entidade in (".$this->getDado('cod_entidade').")             \n";
      }

      if ($this->getDado('cod_processo')) {
        $stSql.=  "AND ll.cod_processo in (".$this->getDado('cod_processo').")             \n";
      }

      if ($this->getDado('exercicio_processo')) {
        $stSql.=  "AND ll.exercicio_processo = '".$this->getDado('exercicio_processo')."'  \n";
      }

      if ($this->getDado('cod_modalidade')) {
        $stSql.=  "AND ll.cod_modalidade IN (".$this->getDado('cod_modalidade').") \n";
      }
      if ($this->getDado('exercicio')) {
       $stSql.=  "AND ll.exercicio = '".$this->getDado('exercicio')."'                     \n";
      }

      if ($this->getDado('cod_licitacao')) {
       $stSql.=  "AND ll.cod_licitacao = '".$this->getDado('cod_licitacao')."'             \n";
      }

      if ($this->getDado('cod_mapa')) {
       $stSql.=  "AND ll.cod_mapa = '".$this->getDado('cod_mapa')."'             \n";
      }

      if ($this->getDado('exercicio_mapa')) {
       $stSql.=  "AND ll.exercicio_mapa = '".$this->getDado('exercicio_mapa')."'             \n";
      }

      if ($this->getDado('cod_tipo_licitacao')) {
       $stSql.=  "AND ll.cod_tipo_licitacao = '".$this->getDado('cod_tipo_licitacao')."'             \n";
      }

      if ($this->getDado('cod_criterio')) {
       $stSql.=  "AND ll.cod_criterio = '".$this->getDado('cod_criterio')."'             \n";
      }

      if ($this->getDado('cod_objeto')) {
       $stSql.=  "AND ll.cod_objeto = '".$this->getDado('cod_objeto')."'             \n";
      }

      if ($this->getDado('cod_tipo_objeto')) {
       $stSql.=  "AND ll.cod_tipo_objeto = '".$this->getDado('cod_tipo_objeto')."'             \n";
      }
      
      return $stSql;
  }

  public function recuperaLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;
      $stOrdem = " ORDER BY  ll.cod_licitacao ";
      $stSql = $this->montaRecuperaLicitacao().$stFiltro.$stOrdem;
      $this->stDebug = $stSql;
      $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

      return $obErro;
  }

  public function montaRecuperaLicitacao()
  {
      $stSql = " SELECT ll.cod_entidade                                                    
                      , ll.cod_licitacao                                                   
                      , ll.cod_processo||'/'||ll.exercicio_processo as processo            
                      , cm.descricao                                                       
                      , cm.cod_modalidade                                                  
                      , ll.cod_modalidade                                                  
                      , ll.cod_mapa||'/'||ll.exercicio_mapa as mapa_compra                 
                      , ll.cod_entidade||' - '||cgm.nom_cgm as entidade                    
                      , ll.cod_modalidade||' - '||cm.descricao as modalidade               
                      , ll.cod_objeto                                                      
                      , ll.cod_regime                                                      
                      , ll.timestamp                                                       
                      , ll.cod_tipo_objeto                                                 
                      , ll.cod_tipo_licitacao                                              
                      , ll.cod_criterio                                                    
                      , ll.vl_cotado                                                       
                      , ll.exercicio                                                       
                      , to_char(ll.timestamp::date, 'dd/mm/yyyy') as dt_licitacao          
                      , LPAD(ll.num_orgao::VARCHAR, 2, '0') || '.' || LPAD(ll.num_unidade::VARCHAR, 2, '0') AS unidade_orcamentaria       
                      , homologadas.dt_homologacao
                      , ll.tipo_chamada_publica
          
                   FROM licitacao.licitacao as ll
                
              LEFT JOIN licitacao.licitacao_anulada as la                            
                     ON ll.cod_licitacao  = la.cod_licitacao      
                    AND ll.cod_modalidade = la.cod_modalidade    
                    AND ll.cod_entidade   = la.cod_entidade                        		
                    AND ll.exercicio      = la.exercicio
                    
              LEFT JOIN (   SELECT cotacao_licitacao.cod_licitacao 
                                 , cotacao_licitacao.cod_modalidade 
                                 , cotacao_licitacao.cod_entidade 
                                 , cotacao_licitacao.exercicio_licitacao 
                                 , homologacao.homologado          
                                 , to_char(homologacao.timestamp::date, 'dd/mm/yyyy') as dt_homologacao    
                              
                              FROM licitacao.cotacao_licitacao

                        INNER JOIN compras.mapa_cotacao
                                ON mapa_cotacao.cod_cotacao         = cotacao_licitacao.cod_cotacao
                               AND mapa_cotacao.exercicio_cotacao   = cotacao_licitacao.exercicio_cotacao

                        INNER JOIN compras.cotacao
                                ON cotacao.exercicio    = mapa_cotacao.exercicio_cotacao
                               AND cotacao.cod_cotacao  = mapa_cotacao.cod_cotacao
                               AND cotacao.cod_cotacao  = (SELECT MAX(MC.cod_cotacao)
                                                             FROM compras.mapa_cotacao AS MC
                                                            WHERE MC.exercicio_mapa = mapa_cotacao.exercicio_mapa
                                                              AND MC.cod_mapa = mapa_cotacao.cod_mapa)
  
                        INNER JOIN licitacao.adjudicacao
                                ON adjudicacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                               AND adjudicacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                               AND adjudicacao.cod_entidade        = cotacao_licitacao.cod_entidade
                               AND adjudicacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
                               AND adjudicacao.lote                = cotacao_licitacao.lote
                               AND adjudicacao.cod_cotacao         = cotacao_licitacao.cod_cotacao
                               AND adjudicacao.cod_item            = cotacao_licitacao.cod_item
                               AND adjudicacao.exercicio_cotacao   = cotacao_licitacao.exercicio_cotacao
                               AND adjudicacao.cgm_fornecedor      = cotacao_licitacao.cgm_fornecedor

                       INNER JOIN licitacao.homologacao 
                               ON homologacao.num_adjudicacao     = adjudicacao.num_adjudicacao
                              AND homologacao.cod_entidade        = adjudicacao.cod_entidade
                              AND homologacao.cod_modalidade      = adjudicacao.cod_modalidade
                              AND homologacao.cod_licitacao       = adjudicacao.cod_licitacao
                              AND homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                              AND homologacao.cod_item            = adjudicacao.cod_item
                              AND homologacao.cod_cotacao         = adjudicacao.cod_cotacao
                              AND homologacao.lote                = adjudicacao.lote
                              AND homologacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                              AND homologacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
                              
                         GROUP BY cotacao_licitacao.cod_licitacao 
                                , cotacao_licitacao.cod_modalidade 
                                , cotacao_licitacao.cod_entidade 
                                , cotacao_licitacao.exercicio_licitacao 
                                , homologacao.homologado
                                , homologacao.timestamp
                ) AS homologadas
                ON homologadas.cod_licitacao       = ll.cod_licitacao 
               AND homologadas.cod_modalidade      = ll.cod_modalidade
               AND homologadas.cod_entidade        = ll.cod_entidade
               AND homologadas.exercicio_licitacao = ll.exercicio
        
        INNER JOIN compras.modalidade as cm 
                ON ll.cod_modalidade = cm.cod_modalidade
               
        INNER JOIN orcamento.entidade as oe
                ON ll.cod_entidade = oe.cod_entidade                                  
               AND ll.exercicio    = oe.exercicio               
                
        INNER JOIN sw_cgm as cgm
                ON oe.numcgm = cgm.numcgm
               
             WHERE 1 = 1 \n ";
      
      $stConsulta = Sessao::read('consulta');
      if (!$stConsulta) {
          $stSql .="    AND la.cod_licitacao is NULL                    \n";
      }
      Sessao::remove('consulta');

       if ($this->getDado('cod_homologada') == 2) {
             $stSql.=  " AND homologadas.homologado = 't'             \n";
       } else if ($this->getDado('cod_homologada') == 3) {
             $stSql.=  " AND ( homologadas.homologado = 'f' 
                                OR NOT EXISTS (
                                        SELECT 1
                                          FROM licitacao.homologacao 
                                         WHERE ll.cod_licitacao    = homologacao.cod_licitacao 
                                             AND ll.cod_modalidade = homologacao.cod_modalidade 
                                             AND ll.cod_entidade   = homologacao.cod_entidade 
                                             AND ll.exercicio      = homologacao.exercicio_licitacao 
                                        ) 
                                      )       \n";
       }
       
      if ($this->getDado('cod_entidade')) {
        $stSql.=  "AND ll.cod_entidade in (".$this->getDado('cod_entidade').")             \n";
      }

      if ($this->getDado('cod_processo')) {
        $stSql.=  "AND ll.cod_processo in (".$this->getDado('cod_processo').")             \n";
      }

      if ($this->getDado('exercicio_processo')) {
        $stSql.=  "AND ll.exercicio_processo = '".$this->getDado('exercicio_processo')."'  \n";
      }

      if ($this->getDado('cod_modalidade')) {
        $stSql.=  "AND ll.cod_modalidade IN (".$this->getDado('cod_modalidade').") \n";
      }
      if ($this->getDado('exercicio')) {
       $stSql.=  "AND ll.exercicio = '".$this->getDado('exercicio')."'                     \n";
      }

      if ($this->getDado('cod_licitacao')) {
       $stSql.=  "AND ll.cod_licitacao = '".$this->getDado('cod_licitacao')."'             \n";
      }

      if ($this->getDado('cod_mapa')) {
       $stSql.=  "AND ll.cod_mapa = '".$this->getDado('cod_mapa')."'             \n";
      }

      if ($this->getDado('exercicio_mapa')) {
       $stSql.=  "AND ll.exercicio_mapa = '".$this->getDado('exercicio_mapa')."'             \n";
      }

      if ($this->getDado('cod_tipo_licitacao')) {
       $stSql.=  "AND ll.cod_tipo_licitacao = '".$this->getDado('cod_tipo_licitacao')."'             \n";
      }

      if ($this->getDado('cod_criterio')) {
       $stSql.=  "AND ll.cod_criterio = '".$this->getDado('cod_criterio')."'             \n";
      }

      if ($this->getDado('cod_objeto')) {
       $stSql.=  "AND ll.cod_objeto = '".$this->getDado('cod_objeto')."'             \n";
      }

      if ($this->getDado('cod_tipo_objeto')) {
       $stSql.=  "AND ll.cod_tipo_objeto = '".$this->getDado('cod_tipo_objeto')."'             \n";
      }
      
      return $stSql;
  }

# Recupera somente licitações que não tem julgamento

function recuperaLicitacaoNaoJulgada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLicitacaoNaoJulgada().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaLicitacaoNaoJulgada()
{
    $stSql  = "                                                                                 \n";
    $stSql .= "            SELECT                                                               \n";
    $stSql .= "                    licitacao.cod_licitacao                                      \n";
    $stSql .= "                 ,  licitacao.exercicio                                          \n";
    $stSql .= "                 ,  licitacao.cod_modalidade                                     \n";
    $stSql .= "                 ,  licitacao.cod_mapa                                           \n";
    $stSql .= "                 ,  cotacao_anulada.cod_cotacao                                  \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "              FROM  licitacao.licitacao                                          \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "        INNER JOIN  compras.mapa_cotacao                                         \n";
    $stSql .= "                ON  mapa_cotacao.cod_mapa       = licitacao.cod_mapa             \n";
    $stSql .= "               AND  mapa_cotacao.exercicio_mapa = licitacao.exercicio_mapa       \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "        LEFT JOIN  compras.cotacao_anulada                                       \n";
    $stSql .= "                ON  mapa_cotacao.cod_cotacao    = cotacao_anulada.cod_cotacao    \n";
    $stSql .= "               AND  mapa_cotacao.exercicio_cotacao = cotacao_anulada.exercicio   \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "             WHERE  1=1                                                          \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "        AND EXISTS                                                               \n";
    $stSql .= "            (                                                                    \n";
    $stSql .= "                SELECT  1                                                        \n";
    $stSql .= "                  FROM  compras.julgamento                                       \n";
    $stSql .= "                 WHERE  julgamento.cod_cotacao = mapa_cotacao.cod_cotacao        \n";
    $stSql .= "                   AND  julgamento.exercicio   = mapa_cotacao.exercicio_cotacao  \n";
    $stSql .= "            )                                                                    \n";
    $stSql .= "                                                                                 \n";

    if($this->getDado('cod_modalidade'))
        $stSql .=  " AND licitacao.cod_modalidade = ".$this->getDado('cod_modalidade')."        \n";

    if($this->getDado('exercicio'))
        $stSql .=  " AND licitacao.exercicio = '".$this->getDado('exercicio')."'                \n";

    if($this->getDado('cod_licitacao'))
        $stSql .=  " AND licitacao.cod_licitacao = ".$this->getDado('cod_licitacao')."          \n";

    if($this->getDado('cod_entidade'))
        $stSql .=  " AND licitacao.cod_entidade = ".$this->getDado('cod_entidade')."            \n";

    return $stSql;
}

function recuperaDadosLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosLicitacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaDadosLicitacao()
{
    $stSql = " SELECT despesa.cod_despesa                                                  \n";
    $stSql.= "        , conta_despesa.cod_conta                                            \n";
    $stSql.= "        , conta_despesa.cod_estrutural                                       \n";
    $stSql.= "        , sum (reserva_saldos.vl_reserva) AS vl_reservado                    \n";
    $stSql.= "        , orcamento.orgao.num_orgao                                          \n";
    $stSql.= "        , orcamento.orgao.nom_orgao                                      \n";
    $stSql.= "        , despesa.num_pao                                                    \n";
    $stSql.= "        , pao.nom_pao                                                        \n";
    $stSql.= "                                                                             \n";
    $stSql.= "   FROM licitacao.licitacao                                                  \n";
    $stSql.= "                                                                             \n";
    $stSql.= "   JOIN compras.mapa_item_reserva                                            \n";
    $stSql.= "     ON mapa_item_reserva.exercicio_mapa = licitacao.exercicio_mapa          \n";
    $stSql.= "    AND mapa_item_reserva.cod_mapa       = licitacao.cod_mapa                \n";
    $stSql.= "                                                                             \n";
    $stSql.= "	 JOIN orcamento.reserva_saldos                                             \n";
    $stSql.= "     ON reserva_saldos.exercicio   = mapa_item_reserva.exercicio_reserva     \n";
    $stSql.= "    AND reserva_saldos.cod_reserva = mapa_item_reserva.cod_reserva           \n";
    $stSql.= "                                                                             \n";
    $stSql.= "   JOIN orcamento.despesa                                                    \n";
    $stSql.= "     ON despesa.exercicio   = reserva_saldos.exercicio                       \n";
    $stSql.= "    AND despesa.cod_despesa = reserva_saldos.cod_despesa                     \n";
    $stSql.= "                                                                             \n";
    $stSql.= "   JOIN orcamento.orgao                                                      \n";
    $stSql.= "     ON orcamento.orgao.exercicio = despesa.exercicio                        \n";
    $stSql.= "    AND orcamento.orgao.num_orgao = despesa.num_orgao                        \n";
    $stSql.= "                                                                             \n";
    $stSql.= "                                                                             \n";
    $stSql.= "   JOIN orcamento.pao                                                        \n";
    $stSql.= "     ON pao.exercicio = despesa.exercicio                                    \n";
    $stSql.= "    AND pao.num_pao   = despesa.num_pao                                      \n";
    $stSql.= "                                                                             \n";
    $stSql.= "    JOIN orcamento.conta_despesa                                             \n";
    $stSql.= "      ON conta_despesa.exercicio = despesa.exercicio                         \n";
    $stSql.= "     AND conta_despesa.cod_conta = despesa.cod_conta                         \n";
    $stSql.= "                                                                             \n";
    $stSql.= "   WHERE licitacao.cod_licitacao   = ".$this->getDado('cod_licitacao')."     \n";
    $stSql.= "     AND licitacao.exercicio       = '".$this->getDado('exercicio')."'       \n";
    $stSql.= "     AND licitacao.cod_entidade    = ".$this->getDado('cod_entidade')."      \n";
    $stSql.= "     AND licitacao.cod_modalidade  = ".$this->getDado('cod_modalidade')."    \n";
    $stSql.= "                                                                             \n";
    $stSql.= "   GROUP BY despesa.cod_despesa                                              \n";
    $stSql.= "      , conta_despesa.cod_conta                                              \n";
    $stSql.= "      , conta_despesa.cod_estrutural                                         \n";
    $stSql.= "      , orcamento.orgao.num_orgao                                            \n";
    $stSql.= "      , despesa.num_pao                                                      \n";
    $stSql.= "      , pao.nom_pao                                                          \n";
    $stSql.= "      , orcamento.orgao.nom_orgao                                        \n";

    return $stSql;
}

function recuperaNorma(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaNorma().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaNorma()
{
    $stSql = " select comissao_membros.cod_comissao                                    \n";
    $stSql.= "        , comissao_membros.numcgm                                        \n";
    $stSql.= "        , comissao_membros.cod_norma                                     \n";
    $stSql.= "        , comissao_membros.cod_tipo_membro                               \n";
    $stSql.= "        , sw_cgm.nom_cgm                                                 \n";
    $stSql.= "        , num_norma                                                      \n";
    $stSql.= "        , nom_norma                                                      \n";
    $stSql.= "        , norma.exercicio                                                \n";
    $stSql.= "        , tipo_norma.nom_tipo_norma                                      \n";
    $stSql.= "   from licitacao.comissao_membros                                       \n";
    $stSql.= "   join sw_cgm                                                           \n";
    $stSql.= "     on comissao_membros.numcgm = sw_cgm.numcgm                          \n";
    $stSql.= "   join normas.norma                                                     \n";
    $stSql.= "     on (norma.cod_norma = comissao_membros.cod_norma)                   \n";
    $stSql.= "   left join normas.tipo_norma                                           \n";
    $stSql.= "	    on (norma.cod_tipo_norma = tipo_norma .cod_tipo_norma)             \n";
    $stSql.= "   where comissao_membros.numcgm = ".$this->getDado('numcgm')."          \n";

    return $stSql;
}

function recuperaValorLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaValorLicitacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
}

function montaRecuperaValorLicitacao()
{
    $stSql  = "  SELECT ( SELECT SUM( vl_total )                                                \n ";
    $stSql .= "             FROM compras.mapa_item                                              \n ";
    $stSql .= "            WHERE mapa_item.exercicio = mapa.exercicio                           \n ";
    $stSql .= "              AND mapa_item.cod_mapa = mapa.cod_mapa                             \n ";
    $stSql .= "          )                                                                      \n ";
    $stSql .= "          - coalesce( ( SELECT sum( vl_total )                                   \n ";
    $stSql .= "                          FROM compras.mapa_item_anulacao                        \n ";
    $stSql .= "                         WHERE mapa_item_anulacao.cod_mapa = mapa.cod_mapa       \n ";
    $stSql .= "                           AND mapa_item_anulacao.exercicio = mapa.exercicio     \n ";
    $stSql .= "                       ), 0                                                      \n ";
    $stSql .= "                     ) as valor_total                                            \n ";
    $stSql .= "    FROM licitacao.licitacao, compras.mapa                                       \n ";
    $stSql .= "   where licitacao.cod_mapa = mapa.cod_mapa                                      \n ";
    $stSql .= "     AND licitacao.exercicio = mapa.exercicio                                    \n ";

    if($this->getDado('cod_modalidade'))
        $stSql .=  " AND licitacao.cod_modalidade = ".$this->getDado('cod_modalidade')."        \n ";

    if($this->getDado('exercicio'))
        $stSql .=  " AND licitacao.exercicio = '".$this->getDado('exercicio')."'                \n ";

    if($this->getDado('cod_licitacao'))
        $stSql .=  " AND licitacao.cod_licitacao = ".$this->getDado('cod_licitacao')."          \n ";

    if($this->getDado('cod_entidade'))
        $stSql .=  " AND licitacao.cod_entidade = ".$this->getDado('cod_entidade')."            \n ";

    return $stSql;
}

function recuperaParecerLicTcemg(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaParecerLicTcemg().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
}

function montaRecuperaParecerLicTcemg()
{
    $stSql = "SELECT 10 as tipo_registro
                   , LPAD(orgao_sicom.valor::varchar,2,'0') as cod_orgao
                   , LPAD((LPAD(''||licitacao.num_orgao,2, '0')||LPAD(''||licitacao.num_unidade,2, '0')), 5, '0') AS codunidadesub
                   , config_licitacao.exercicio_licitacao
                   , config_licitacao.num_licitacao AS num_processo_licitatorio
                   , to_char(edital.dt_aprovacao_juridico, 'ddmmyyyy') as data_parecer
                   , 2 as tipo_parecer
                   , pf.cpf as cpf
                FROM licitacao.licitacao	
                JOIN licitacao.edital
                  ON edital.exercicio_licitacao = licitacao.exercicio
                 AND edital.cod_licitacao   = licitacao.cod_licitacao
                 AND edital.cod_modalidade  = licitacao.cod_modalidade
                 AND edital.cod_entidade    = licitacao.cod_entidade
                JOIN licitacao.cotacao_licitacao AS CL
                  ON CL.cod_licitacao       = licitacao.cod_licitacao
                 AND CL.cod_modalidade      = licitacao.cod_modalidade
                 AND CL.cod_entidade        = licitacao.cod_entidade
                 AND CL.exercicio_licitacao = licitacao.exercicio
                JOIN licitacao.adjudicacao AS A
                  ON A.cod_licitacao        = CL.cod_licitacao
                 AND A.cod_modalidade       = CL.cod_modalidade
                 AND A.cod_entidade         = CL.cod_entidade
                 AND A.exercicio_licitacao  = CL.exercicio_licitacao
                 AND A.lote                 = CL.lote
                 AND A.cod_cotacao          = CL.cod_cotacao
                 AND A.cod_item             = CL.cod_item
                 AND A.exercicio_cotacao    = CL.exercicio_cotacao
                 AND A.cgm_fornecedor       = CL.cgm_fornecedor
                JOIN licitacao.homologacao AS H
                  ON H.num_adjudicacao      = A.num_adjudicacao
                 AND H.cod_entidade         = A.cod_entidade
                 AND H.cod_modalidade       = A.cod_modalidade
                 AND H.cod_licitacao        = A.cod_licitacao
                 AND H.exercicio_licitacao  = A.exercicio_licitacao
                 AND H.cod_item             = A.cod_item
                 AND H.cod_cotacao          = A.cod_cotacao
                 AND H.lote                 = A.lote
                 AND H.exercicio_cotacao    = A.exercicio_cotacao
                 AND H.cgm_fornecedor       = A.cgm_fornecedor
                 AND (SELECT num_homologacao
                        FROM licitacao.homologacao_anulada AS HANUL
                       WHERE HANUL.num_homologacao      = H.num_homologacao
                         AND HANUL.cod_licitacao        = H.cod_licitacao
		                 AND HANUL.cod_modalidade       = H.cod_modalidade
		                 AND HANUL.cod_entidade         = H.cod_entidade
		                 AND HANUL.num_adjudicacao      = H.num_adjudicacao
		                 AND HANUL.exercicio_licitacao  = H.exercicio_licitacao
		                 AND HANUL.lote                 = H.lote
		                 AND HANUL.cod_cotacao          = H.cod_cotacao
		                 AND HANUL.cod_item             = H.cod_item
		                 AND HANUL.exercicio_cotacao    = H.exercicio_cotacao
		                 AND HANUL.cgm_fornecedor       = H.cgm_fornecedor
                     ) IS NULL	
           LEFT JOIN public.sw_cgm_pessoa_fisica as pf
                  ON pf.numcgm = edital.responsavel_juridico
                JOIN (SELECT valor::integer 
                            , configuracao_entidade.exercicio
                            , configuracao_entidade.cod_entidade
                        FROM tcemg.orgao 
                  INNER JOIN administracao.configuracao_entidade
                          ON configuracao_entidade.valor::integer = orgao.num_orgao   
                       WHERE configuracao_entidade.cod_entidade IN (1,2,3)  AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
                    )  AS orgao_sicom
                  ON orgao_sicom.exercicio='".Sessao::getExercicio()."'
                 AND orgao_sicom.cod_entidade = licitacao.cod_entidade
                 
            JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 ) AS config_licitacao
              ON config_licitacao.cod_entidade = licitacao.cod_entidade
             AND config_licitacao.cod_licitacao = licitacao.cod_licitacao
             AND config_licitacao.cod_modalidade = licitacao.cod_modalidade
             AND config_licitacao.exercicio = licitacao.exercicio                 
               
               WHERE H.timestamp BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                 AND licitacao.cod_modalidade NOT IN (8,9)
                 AND NOT EXISTS (SELECT 1
                                   FROM licitacao.licitacao_anulada
                                   WHERE licitacao_anulada.exercicio = licitacao.exercicio
                                     AND licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                                     AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                     AND licitacao_anulada.cod_entidade = licitacao.cod_entidade)
               GROUP BY 1,2,3,4,5,6,7,8";
    return $stSql;
}


function recuperaLicitacaoMembro(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLicitacaoMembro().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
}

function montaRecuperaLicitacaoMembro()
{
    $stSql = "   SELECT ll.cod_entidade||' - '||cgm.nom_cgm AS entidade                                                                        
                      , ll.cod_modalidade||' - '||cm.descricao AS modalidade                   
                      , ll.cod_modalidade
                      , ll.cod_licitacao
                      , ll.exercicio
                FROM                                                
                    licitacao.licitacao AS ll                      
                    ,compras.modalidade AS cm                       
                    ,orcamento.entidade AS oe                       
                    ,sw_cgm as cgm                                  
                WHERE                                               
                    ll.cod_modalidade = cm.cod_modalidade           
                AND ll.cod_entidade   = oe.cod_entidade           
                AND ll.exercicio      = oe.exercicio                 
                AND oe.numcgm         = cgm.numcgm
                
                AND EXISTS (SELECT * 
                              FROM licitacao.membro_adicional
                             WHERE membro_adicional.cod_licitacao   = ll.cod_licitacao
                               AND membro_adicional.cod_modalidade  = ll.cod_modalidade
                               AND membro_adicional.cod_entidade    = ll.cod_entidade
                               AND membro_adicional.exercicio       = ll.exercicio)
                            
                AND NOT EXISTS (SELECT * 
                                  FROM licitacao.licitacao_anulada as la 
                                 WHERE ll.cod_licitacao  = la.cod_licitacao 
                                   AND ll.cod_modalidade = la.cod_modalidade 
                                   AND ll.cod_entidade   = la.cod_entidade 
                                   AND ll.exercicio      = la.exercicio )
            ";
    return $stSql;
}

function recuperaDadosLicitacaoItens(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosLicitacaoItens().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
}

function montaRecuperaDadosLicitacaoItens()
{
    $stSql = "   SELECT ll.cod_entidade                                                    
                                 , ll.cod_licitacao                                                   
                                 , ll.cod_processo||'/'||ll.exercicio_processo as processo            
                                 , modalidade.descricao                                                       
                                 , modalidade.cod_modalidade                                                  
                                 , ll.cod_modalidade                                                  
                                 , ll.cod_mapa||'/'||ll.exercicio_mapa as mapa_compra                 
                                 , ll.cod_entidade::varchar||' - '|| entidade_nom_cgm.nom_cgm as entidade                    
                                 , ll.cod_modalidade||' - '||modalidade.descricao as modalidade               
                                 , ll.cod_objeto                                                      
                                 , ll.cod_regime                                                      
                                 , ll.timestamp                                                       
                                 , ll.cod_tipo_objeto||' - '|| tipo_objeto.descricao as tipo_objeto                                             
                                 , ll.cod_tipo_licitacao                                              
                                 , ll.cod_criterio                                                    
                                 , ll.vl_cotado                                                       
                                 , ll.exercicio                                                       
                                 , to_char(ll.timestamp::date, 'dd/mm/yyyy') as dt_licitacao          
                                 , LPAD(ll.num_orgao::VARCHAR, 2, '0') || '.' || LPAD(ll.num_unidade::VARCHAR, 2, '0') AS unidade_orcamentaria                                  
                                 , autorizacao_empenho.cod_autorizacao||'/'|| ll.exercicio as autorizacao
                                 , item_pre_empenho_julgamento.cgm_fornecedor||' - '|| sw_cgm.nom_cgm as fornecedor
                                 , lpad(item_pre_empenho.num_item::varchar, 4, '0') as num_item
                                 , item_pre_empenho.quantidade
                                 , item_pre_empenho.vl_total
                                 , item_pre_empenho.vl_total/item_pre_empenho.quantidade as vl_unitario
                                 , catalogo_item.cod_item
                                 , catalogo_item.descricao                                 
                                 , ll.cod_objeto ||' - '||objeto.descricao as objeto
                                 
                         FROM licitacao.licitacao as ll  
                         
                   INNER JOIN compras.modalidade
                               ON modalidade.cod_modalidade = ll.cod_modalidade
                        
               
                 INNER JOIN compras.mapa_cotacao
                             ON mapa_cotacao.exercicio_mapa = ll.exercicio
                           AND mapa_cotacao.cod_mapa = ll.cod_mapa
              

               INNER JOIN compras.julgamento  
                           ON julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                         AND julgamento.exercicio = mapa_cotacao.exercicio_mapa
        
               INNER JOIN compras.julgamento_item  
                           ON julgamento_item.cod_cotacao = julgamento.cod_cotacao 
                         AND julgamento_item.exercicio = julgamento.exercicio 
                                
               INNER JOIN empenho.item_pre_empenho_julgamento  
                           ON item_pre_empenho_julgamento.exercicio_julgamento = julgamento_item.exercicio  
                         AND item_pre_empenho_julgamento.cod_cotacao = julgamento_item.cod_cotacao  
                         AND item_pre_empenho_julgamento.cod_item = julgamento_item.cod_item  
                         AND item_pre_empenho_julgamento.lote = julgamento_item.lote  
                         AND item_pre_empenho_julgamento.cgm_fornecedor = julgamento_item.cgm_fornecedor

                INNER JOIN empenho.item_pre_empenho
                           ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho 
                          AND item_pre_empenho.exercicio = item_pre_empenho_julgamento.exercicio
                          AND item_pre_empenho.num_item = item_pre_empenho_julgamento.num_item
                          
                INNER JOIN almoxarifado.catalogo_item
                             ON catalogo_item.cod_item = julgamento_item.cod_item

                INNER JOIN empenho.pre_empenho
                           ON pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                         AND pre_empenho.exercicio = item_pre_empenho.exercicio

               INNER JOIN empenho.autorizacao_empenho  
                           ON autorizacao_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                          AND autorizacao_empenho.exercicio = pre_empenho.exercicio
                                                 
               INNER JOIN sw_cgm
                           ON sw_cgm.numcgm = item_pre_empenho_julgamento.cgm_fornecedor       
                           
               INNER JOIN compras.objeto
                           ON objeto.cod_objeto = ll.cod_objeto
                           
               INNER JOIN compras.tipo_objeto 
                           ON tipo_objeto.cod_tipo_objeto =  ll.cod_tipo_objeto
               INNER JOIN (
                                            SELECT  licitacao.cod_licitacao  
                                                       , licitacao.cod_modalidade
                                                       , licitacao.cod_entidade
                                                       , licitacao.exercicio
                                                       , cgm.nom_cgm
                                              FROM licitacao.licitacao           
                                      INNER JOIN orcamento.entidade
                                                 ON entidade.cod_entidade =  licitacao.cod_entidade
                                               AND entidade.exercicio = licitacao.exercicio
                                     INNER JOIN sw_cgm as cgm
                                                 ON cgm.numcgm = entidade.numcgm        
                                        GROUP BY licitacao.cod_licitacao  
                                                       , licitacao.cod_modalidade
                                                       , licitacao.cod_entidade
                                                       , licitacao.exercicio
                                                       , cgm.nom_cgm
                                 ) as entidade_nom_cgm
                             ON entidade_nom_cgm.cod_licitacao = ll.cod_licitacao 
                           AND entidade_nom_cgm.cod_modalidade = ll.cod_modalidade
                           AND entidade_nom_cgm.cod_entidade = ll.cod_entidade
                           AND entidade_nom_cgm.exercicio = ll.exercicio
                           
           
          
                 WHERE ll.cod_entidade = ".$this->getDado('inCodEntidade') ."   
                      AND ll.cod_licitacao = ".$this->getDado('inCodLicitacao') ."                                                   
                      AND ll.cod_modalidade =  ".$this->getDado('inCodModalidade') ."  
                      AND ll.exercicio =  '".$this->getDado('stExercicioLicitacao') ."'  
             
                           
ORDER BY autorizacao_empenho.cod_autorizacao, item_pre_empenho.num_item 
                      ";
    return $stSql;
}
function recuperaAutorizacaoLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAutorizacaoLicitacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
}

function montaRecuperaAutorizacaoLicitacao()
{
    $stSql = "   SELECT ll.cod_entidade                                                    
                                 , ll.cod_licitacao                                                   
                                 , ll.cod_processo                                                                                
                                 , ll.cod_modalidade                                                  
                                 , ll.cod_mapa
                                 , ll.cod_objeto                                                      
                                 , ll.cod_regime                                                      
                                 , ll.timestamp                                                       
                                 , ll.cod_tipo_objeto                            
                                 , ll.cod_tipo_licitacao                                              
                                 , ll.cod_criterio                                                    
                                 , ll.vl_cotado                                                       
                                 , ll.exercicio                                                       
                                 , to_char(ll.timestamp::date, 'dd/mm/yyyy') as dt_licitacao      
                                                 
                                 , autorizacao_empenho.cod_autorizacao as autorizacao
                         FROM licitacao.licitacao as ll  
                         
                   INNER JOIN compras.modalidade
                               ON modalidade.cod_modalidade = ll.cod_modalidade
                 INNER JOIN compras.mapa_cotacao
                             ON mapa_cotacao.exercicio_mapa = ll.exercicio
                           AND mapa_cotacao.cod_mapa = ll.cod_mapa

               INNER JOIN compras.julgamento  
                           ON julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                         AND julgamento.exercicio = mapa_cotacao.exercicio_mapa
        
               INNER JOIN compras.julgamento_item  
                           ON julgamento_item.cod_cotacao = julgamento.cod_cotacao 
                         AND julgamento_item.exercicio = julgamento.exercicio 
                                
               INNER JOIN empenho.item_pre_empenho_julgamento  
                           ON item_pre_empenho_julgamento.exercicio_julgamento = julgamento_item.exercicio  
                         AND item_pre_empenho_julgamento.cod_cotacao = julgamento_item.cod_cotacao  
                         AND item_pre_empenho_julgamento.cod_item = julgamento_item.cod_item  
                         AND item_pre_empenho_julgamento.lote = julgamento_item.lote  
                         AND item_pre_empenho_julgamento.cgm_fornecedor = julgamento_item.cgm_fornecedor

                INNER JOIN empenho.item_pre_empenho
                           ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho 
                          AND item_pre_empenho.exercicio = item_pre_empenho_julgamento.exercicio
                          AND item_pre_empenho.num_item = item_pre_empenho_julgamento.num_item    
                INNER JOIN empenho.pre_empenho
                           ON pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                         AND pre_empenho.exercicio = item_pre_empenho.exercicio
               INNER JOIN empenho.autorizacao_empenho  
                           ON autorizacao_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                          AND autorizacao_empenho.exercicio = pre_empenho.exercicio            
          
                 WHERE ll.cod_entidade = ".$this->getDado('inCodEntidade') ."   
                      AND ll.cod_licitacao = ".$this->getDado('inCodLicitacao') ."                                                   
                      AND ll.cod_modalidade =  ".$this->getDado('inCodModalidade') ."  
                      AND ll.exercicio =  '".$this->getDado('stExercicioLicitacao') ."'  

                ORDER BY autorizacao_empenho.cod_autorizacao ";
                
       return $stSql;
    }

    public function recuperaStatusLicitacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaStatusLicitacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaStatusLicitacao()
    {
        $stSql = "
            SELECT  licitacao.cod_entidade                                                    
                                 , licitacao.cod_licitacao                                                   
                                 , licitacao.cod_processo                                                                                
                                 , licitacao.cod_modalidade                                                  
                                 , licitacao.cod_mapa
                                 , licitacao.cod_objeto                                                      
                                 , licitacao.cod_regime                                                      
                                 , licitacao.timestamp                                                       
                                 , licitacao.cod_tipo_objeto                            
                                 , licitacao.cod_tipo_licitacao                                              
                                 , licitacao.cod_criterio                                                    
                                 , licitacao.vl_cotado                                                       
                                 , licitacao.exercicio                                                    
                                
                          
                            , CASE WHEN licitacao_anulada.cod_licitacao IS NULL THEN
                               'Ativa'
                              ELSE
                               'Anulada'
                              END as status
              FROM  licitacao.licitacao
        LEFT JOIN  licitacao.licitacao_anulada
                  ON  licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                AND  licitacao_anulada.cod_entidade = licitacao.cod_entidade
                AND  licitacao_anulada.exercicio = licitacao.exercicio
                AND  licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
        
            WHERE licitacao.cod_licitacao = ".$this->getDado('inCodLicitacao')." 
                AND licitacao.cod_entidade = ".$this->getDado('inCodEntidade')." 
                AND licitacao.cod_modalidade = ".$this->getDado('inCodModalidade')."                
                AND licitacao.exercicio  = '".$this->getDado('stExercicioLicitacao')."'  ";

        return $stSql;
    }
    
    public function recuperaManutencaoParticipanteLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;
      $stSql = $this->montaRecuperaManutencaoParticipanteLicitacao().$stFiltro.$stOrdem;
      $this->stDebug = $stSql;
      $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
      return $obErro;
    }
    
    public function montaRecuperaManutencaoParticipanteLicitacao()
    {
          $stSql  = "    SELECT licitacao.cod_processo
			      , licitacao.exercicio_processo
                              , edital.num_edital
                              , modalidade.descricao                                                         
                              , licitacao.exercicio
                              , licitacao.cod_objeto
                              , licitacao.timestamp
                              , licitacao.cod_licitacao||'/'||licitacao.exercicio as num_licitacao                 
                              , licitacao.cod_entidade                                                      
                              , sw_cgm.nom_cgm as entidade                                              
                              , licitacao.cod_modalidade                                                    
                              , licitacao.cod_licitacao                                                     
                              , licitacao.cod_processo                                                      
                              , licitacao.exercicio_processo                                                
                              , licitacao.cod_mapa                                                          
                              
                           FROM licitacao.licitacao
                           
                      LEFT JOIN licitacao.edital
                             ON edital.cod_licitacao   = licitacao.cod_licitacao                                
                            AND edital.cod_modalidade  = licitacao.cod_modalidade                               
                            AND edital.cod_entidade    = licitacao.cod_entidade                                 
                            AND edital.exercicio       = licitacao.exercicio                                    
                            
                     INNER JOIN licitacao.comissao_licitacao                            
                             ON comissao_licitacao.cod_licitacao  = licitacao.cod_licitacao                                 
                            AND comissao_licitacao.cod_modalidade = licitacao.cod_modalidade                                
                            AND comissao_licitacao.cod_entidade   = licitacao.cod_entidade                                  
                            AND comissao_licitacao.exercicio      = licitacao.exercicio                                     
                     
                     INNER JOIN compras.modalidade                                
                             ON licitacao.cod_modalidade = modalidade.cod_modalidade                                
                     
                     INNER JOIN orcamento.entidade
                             ON entidade.cod_entidade = licitacao.cod_entidade                                    
                            AND entidade.exercicio    = licitacao.exercicio                                       
                     
                     INNER JOIN sw_cgm                                                      
                             ON entidade.numcgm = sw_cgm.numcgm
                     
                      WHERE 1=1 \n";
  
          if ( $this->getDado( 'num_edital' ) ) {
              $stSql .= " AND edital.num_edital = '". $this->getDado( 'num_edital' )."' \n";
          }
          
          if ( $this->getDado( 'exercicio' ) ) {
              $stSql .= " AND licitacao.exercicio = '". $this->getDado( 'exercicio' )."' \n";
          }
          
          if ( $this->getDado( 'cod_entidade' ) ) {
              $stSql .= " AND licitacao.cod_entidade in ( ". $this->getDado( 'cod_entidade' )." ) \n";
          }
  
          if ( $this->getDado( 'cod_modalidade' ) ) {
              $stSql .= " AND licitacao.cod_modalidade = ". $this->getDado( 'cod_modalidade' )." \n";
          }
  
          if ( $this->getDado( 'cod_licitacao' ) ) {
              $stSql .= " AND licitacao.cod_licitacao = ". $this->getDado( 'cod_licitacao' )." \n";
          }
  
          if ( $this->getDado( 'cod_processo' ) ) {
              $stSql .= " AND licitacao.cod_processo = ". $this->getDado( 'cod_processo' )." \n";
          }
  
          if ( $this->getDado( 'cod_mapa' ) ) {
              $stSql .= " AND licitacao.cod_mapa = ". $this->getDado( 'cod_mapa' )." \n";
          }
  
          if ( $this->getDado( 'cod_tipo_licitacao' ) ) {
              $stSql .= " AND licitacao.cod_tipo_licitacao = ". $this->getDado( 'cod_tipo_licitacao' )." \n";
          }
  
          if ( $this->getDado( 'cod_criterio' ) ) {
              $stSql .= " AND licitacao.cod_criterio = ". $this->getDado( 'cod_criterio' )." \n";
          }
  
          if ( $this->getDado( 'cod_tipo_objeto' ) ) {
              $stSql .= " AND licitacao.cod_tipo_objeto = ". $this->getDado( 'cod_tipo_objeto' )." \n";
          }
  
          if ( $this->getDado( 'cod_objeto' ) ) {
              $stSql .= " AND licitacao.cod_objeto = ". $this->getDado( 'cod_objeto' )." \n";
          }
  
          if ( $this->getDado( 'cod_comissao' ) ) {
              $stSql .= " AND comissao_licitacao.cod_comissao = ". $this->getDado( 'cod_comissao' )." \n";
          }
  
          return $stSql;
    }
      
    
    public function recuperaManutencaoParticipanteLicitacaoLabel(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;
      $stSql = $this->montaRecuperaManutencaoParticipanteLicitacaoLabel().$stFiltro.$stOrdem;
      $this->stDebug = $stSql;
      $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
      return $obErro;
    }

    public function montaRecuperaManutencaoParticipanteLicitacaoLabel()
    {
          $stSql  = "    SELECT licitacao.cod_processo
			      , licitacao.exercicio_processo
                              , edital.num_edital                                                        
                              , modalidade.descricao                                                         
                              , licitacao.exercicio AS exercicio_licitacao
                              , licitacao.cod_objeto
                              , licitacao.timestamp
                              , licitacao.cod_licitacao||'/'||licitacao.exercicio as num_licitacao                 
                              , licitacao.cod_entidade                                                      
                              , sw_cgm.nom_cgm as entidade                                              
                              , licitacao.cod_modalidade                                                    
                              , licitacao.cod_licitacao                                                     
                              , licitacao.cod_mapa                                                          
                              , modalidade.descricao as nom_modalidade
                              , sw_cgm.nom_cgm as nom_entidade
                     
                           FROM licitacao.licitacao
                           
                      LEFT JOIN licitacao.edital
                             ON edital.cod_licitacao   = licitacao.cod_licitacao                                
                            AND edital.cod_modalidade  = licitacao.cod_modalidade                               
                            AND edital.cod_entidade    = licitacao.cod_entidade                                 
                            AND edital.exercicio       = licitacao.exercicio                                    
                            
                     INNER JOIN licitacao.comissao_licitacao                            
                             ON comissao_licitacao.cod_licitacao  = licitacao.cod_licitacao                                 
                            AND comissao_licitacao.cod_modalidade = licitacao.cod_modalidade                                
                            AND comissao_licitacao.cod_entidade   = licitacao.cod_entidade                                  
                            AND comissao_licitacao.exercicio      = licitacao.exercicio                                     
                     
                     INNER JOIN compras.modalidade                                
                             ON licitacao.cod_modalidade = modalidade.cod_modalidade                                
                     
                     INNER JOIN orcamento.entidade
                             ON entidade.cod_entidade = licitacao.cod_entidade                                    
                            AND entidade.exercicio    = licitacao.exercicio                                       
                     
                     INNER JOIN sw_cgm                                                      
                             ON entidade.numcgm = sw_cgm.numcgm
                             
                      LEFT JOIN licitacao.homologacao 
                             ON licitacao.cod_licitacao  = homologacao.cod_licitacao    
                            AND licitacao.cod_modalidade = homologacao.cod_modalidade  
                            AND licitacao.cod_entidade   = homologacao.cod_entidade      
                            AND licitacao.exercicio      = homologacao.exercicio_licitacao  
                     
                      WHERE 1=1 \n";
  
          if ( $this->getDado( 'num_edital' ) ) {
              $stSql .= " AND edital.num_edital = '". $this->getDado( 'num_edital' )."' \n";
          }
          
          if ( $this->getDado( 'exercicio' ) ) {
              $stSql .= " AND licitacao.exercicio = '". $this->getDado( 'exercicio' )."' \n";
          }
          
          if ( $this->getDado( 'cod_entidade' ) ) {
              $stSql .= " AND licitacao.cod_entidade in ( ". $this->getDado( 'cod_entidade' )." ) \n";
          }
  
          if ( $this->getDado( 'cod_modalidade' ) ) {
              $stSql .= " AND licitacao.cod_modalidade = ". $this->getDado( 'cod_modalidade' )." \n";
          }
  
          if ( $this->getDado( 'cod_licitacao' ) ) {
              $stSql .= " AND licitacao.cod_licitacao = ". $this->getDado( 'cod_licitacao' )." \n";
          }
          
          return $stSql;
      }
      
    public function __destruct() {}
}

?>