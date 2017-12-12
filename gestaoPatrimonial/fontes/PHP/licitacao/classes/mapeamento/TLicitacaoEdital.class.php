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
    * Classe de mapeamento da tabela licitacao.edital
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TLicitacaoEdital.class.php 65904 2016-06-28 18:33:08Z michel $

    * Casos de uso: uc-03.05.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TLicitacaoEdital extends Persistente
{
  /**
      * Método Construtor
      * @access Private
  */
  public function __construct()
  {
    parent::Persistente();
    $this->setTabela("licitacao.edital");

    $this->setCampoCod('num_edital');
    $this->setComplementoChave('exercicio');
    $this->AddCampo('num_edital'                    ,'sequence',true  ,''   ,true,false);
    $this->AddCampo('exercicio'                     ,'varchar' ,false ,'4'  ,true,false);
    $this->AddCampo('exercicio_licitacao'           ,'varchar' ,false ,'4'  ,false,'TLicitacaoLicitacao','exercicio');
    $this->AddCampo('cod_entidade'                  ,'integer' ,false ,''   ,false,'TLicitacaoLicitacao');
    $this->AddCampo('cod_modalidade'                ,'integer' ,false ,''   ,false,'TLicitacaoLicitacao');
    $this->AddCampo('cod_licitacao'                 ,'integer' ,false ,''   ,false,'TLicitacaoLicitacao');
    $this->AddCampo('dt_entrega_propostas'          ,'date'    ,false ,''   ,false,false);
    $this->AddCampo('hora_entrega_propostas'        ,'varchar' ,false ,''   ,false,false);
    $this->AddCampo('local_entrega_propostas'       ,'varchar' ,false ,'100',false,false);
    $this->AddCampo('local_abertura_propostas'      ,'varchar' ,false ,'100',false,false);
    $this->AddCampo('local_entrega_material'        ,'varchar' ,false ,'100',false,false);
    $this->AddCampo('dt_validade_proposta'          ,'date'    ,false ,''   ,false,false);
    $this->AddCampo('observacao_validade_proposta'  ,'text'    ,false ,''   ,false,false);
    $this->AddCampo('condicoes_pagamento'           ,'varchar' ,false ,'80' ,false,false);
    $this->AddCampo('hora_abertura_propostas'       ,'varchar' ,false ,''   ,false,false);
    $this->AddCampo('dt_abertura_propostas'         ,'date'    ,false ,''   ,false,false);
    $this->AddCampo('responsavel_juridico'          ,'integer' ,false ,''   ,false,false);
    $this->AddCampo('dt_aprovacao_juridico'         ,'date'    ,false ,''   ,false,false);
    $this->AddCampo('cod_tipo_documento'            ,'integer' ,false ,''   ,false,false);
    $this->AddCampo('cod_documento'                 ,'integer' ,false ,''   ,false,false);
    $this->AddCampo('dt_final_entrega_propostas'    ,'date'    ,false ,''   ,false,false);
    $this->AddCampo('hora_final_entrega_propostas'  ,'varchar' ,false ,''   ,false,false);
  }

  public function recuperaListaEdital(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaEdital().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
  }

  public function montaRecuperaListaEdital()
  {
        $stSql  = "    SELECT le.num_edital                                                        \n";
        $stSql .= "         , cp.descricao                                                         \n";
        $stSql .= "         , le.exercicio                                                         \n";
        $stSql .= "         , le.cod_entidade                                                      \n";
        $stSql .= "         , ll.cod_licitacao||'/'||ll.exercicio as num_licitacao                 \n";
        $stSql .= "         , ll.cod_entidade                                                      \n";
        $stSql .= "         , cgm.nom_cgm as entidade                                              \n";
        $stSql .= "         , ll.cod_modalidade                                                    \n";
        $stSql .= "         , ll.cod_licitacao                                                     \n";
        $stSql .= "         , ll.cod_processo                                                      \n";
        $stSql .= "         , ll.exercicio_processo                                                \n";
        $stSql .= "         , le.cod_modalidade                                                    \n";
        $stSql .= "         , ll.cod_mapa                                                          \n";
        $stSql .= "         , le.dt_entrega_propostas                                              \n";
        $stSql .= "         , le.hora_entrega_propostas                                            \n";
        $stSql .= "         , le.local_entrega_propostas                                           \n";
        $stSql .= "         , le.local_abertura_propostas                                          \n";
        $stSql .= "         , le.dt_abertura_propostas                                             \n";
        $stSql .= "         , le.hora_abertura_propostas                                           \n";
        $stSql .= "         , le.condicoes_pagamento                                               \n";
        $stSql .= "         , le.dt_validade_proposta                                              \n";
        $stSql .= "         , le.dt_validade_proposta-le.dt_entrega_propostas as qtd_dias_validade \n";
        $stSql .= "      FROM licitacao.edital as le                                               \n";
        $stSql .= "INNER JOIN licitacao.licitacao ll                                               \n";
        $stSql .= "        ON le.cod_licitacao   = ll.cod_licitacao                                \n";
        $stSql .= "       AND le.cod_modalidade  = ll.cod_modalidade                               \n";
        $stSql .= "       AND le.cod_entidade    = ll.cod_entidade                                 \n";
        $stSql .= "       AND le.exercicio       = ll.exercicio                                    \n";
        $stSql .= "INNER JOIN licitacao.comissao_licitacao as cl                                   \n";
        $stSql .= "        ON cl.cod_licitacao  = ll.cod_licitacao                                 \n";
        $stSql .= "       AND cl.cod_modalidade = ll.cod_modalidade                                \n";
        $stSql .= "       AND cl.cod_entidade   = ll.cod_entidade                                  \n";
        $stSql .= "       AND cl.exercicio      = ll.exercicio                                     \n";
        $stSql .= "INNER JOIN compras.modalidade as cp                                             \n";
        $stSql .= "        ON ll.cod_modalidade = cp.cod_modalidade                                \n";
        $stSql .= "INNER JOIN orcamento.entidade as oe                                             \n";
        $stSql .= "        ON oe.cod_entidade = le.cod_entidade                                    \n";
        $stSql .= "       AND oe.exercicio    = le.exercicio                                       \n";
        $stSql .= "INNER JOIN sw_cgm as cgm                                                        \n";
        $stSql .= "        ON oe.numcgm = cgm.numcgm                                               \n";
        $stSql .= "     WHERE 1=1                                                                  \n";

        if ( $this->getDado( 'num_edital' ) ) {
            $stSql .= " AND le.num_edital = ". $this->getDado( 'num_edital' );
        }

        if ( $this->getDado( 'exercicio_edital' ) ) {
            $stSql .= " AND le.exercicio = '". $this->getDado( 'exercicio_edital' )."'";
        }

        if ( $this->getDado( 'exercicio_licitacao' ) ) {
            $stSql .= " AND le.exercicio_licitacao = ". $this->getDado( 'exercicio_licitacao' );
        }

        if ( $this->getDado( 'cod_entidade' ) ) {
            $stSql .= " AND le.cod_entidade in ( ". $this->getDado( 'cod_entidade' )." ) ";
        }

        if ( $this->getDado( 'cod_modalidade' ) ) {
            $stSql .= " AND le.cod_modalidade = ". $this->getDado( 'cod_modalidade' );
        }

        if ( $this->getDado( 'cod_licitacao' ) ) {
            $stSql .= " AND le.cod_licitacao = ". $this->getDado( 'cod_licitacao' );
        }

        if ( $this->getDado( 'cod_processo' ) ) {
            $stSql .= " AND ll.cod_processo = ". $this->getDado( 'cod_processo' );
        }

        if ( $this->getDado( 'cod_mapa' ) ) {
            $stSql .= " AND ll.cod_mapa = ". $this->getDado( 'cod_mapa' );
        }

        if ( $this->getDado( 'cod_tipo_licitacao' ) ) {
            $stSql .= " AND ll.cod_tipo_licitacao = ". $this->getDado( 'cod_tipo_licitacao' );
        }

        if ( $this->getDado( 'cod_criterio' ) ) {
            $stSql .= " AND ll.cod_criterio = ". $this->getDado( 'cod_criterio' );
        }

        if ( $this->getDado( 'cod_tipo_objeto' ) ) {
            $stSql .= " AND ll.cod_tipo_objeto = ". $this->getDado( 'cod_tipo_objeto' );
        }

        if ( $this->getDado( 'cod_objeto' ) ) {
            $stSql .= " AND ll.cod_objeto = ". $this->getDado( 'cod_objeto' );
        }

        if ( $this->getDado( 'cod_comissao' ) ) {
            $stSql .= " AND cl.cod_comissao = ". $this->getDado( 'cod_comissao' );
        }

        return $stSql;
    }

  /*
   * Retorna o numero e o objeto da licitação associados a este edital
   */
  public function recuperaLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLicitacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
  }

  public function montaRecuperaLicitacao()
  {
    //minha tabela fisica
    $tab = $this->getTabela();

    $stSql  =" SELECT licitacao.edital.num_edital,                                                         \n";
    $stSql .="       licitacao.edital.cod_licitacao,                                                       \n";
    $stSql .="       licitacao.edital.exercicio,                                                           \n";
    $stSql .="       licitacao.edital.exercicio_licitacao,                                                 \n";
    $stSql .="       licitacao.edital.cod_modalidade,                                                      \n";
    $stSql .="       modalidade.descricao as modalidade_descricao,                                         \n";
    $stSql .="       licitacao.edital.cod_entidade,                                                        \n";
    $stSql .="       sw_cgm.nom_cgm,                                                                       \n";
    $stSql .="       to_char(licitacao.edital.dt_aprovacao_juridico,'dd/mm/yyyy') as dt_aprovacao_juridico,\n";
    $stSql .="       licitacao.licitacao.cod_objeto,                                                       \n";
    $stSql .="       licitacao.licitacao.timestamp                                                         \n";
    $stSql .="FROM licitacao.edital                                                                        \n";
    $stSql .="INNER JOIN licitacao.licitacao                                                               \n";
    $stSql .="        ON licitacao.cod_licitacao  = edital.cod_licitacao                                   \n";
    $stSql .="       AND licitacao.cod_modalidade = edital.cod_modalidade                                  \n";
    $stSql .="       AND licitacao.cod_entidade   = edital.cod_entidade                                    \n";
    $stSql .="       AND licitacao.exercicio      = edital.exercicio_licitacao                             \n";
    $stSql .="INNER JOIN compras.modalidade                                                                \n";
    $stSql .="        ON modalidade.cod_modalidade = licitacao.cod_modalidade                              \n";
    $stSql .="INNER JOIN orcamento.entidade                                                                \n";
    $stSql .="        ON ( entidade.exercicio = licitacao.exercicio                                        \n";
    $stSql .="             AND entidade.cod_entidade = licitacao.cod_entidade)                             \n";
    $stSql .="INNER JOIN sw_cgm                                                                            \n";
    $stSql .="        ON ( sw_cgm.numcgm = entidade.numcgm )                                               \n";
    $stSql .="WHERE edital.exercicio                  = '".Sessao::getExercicio()."'  \n";
    $stSql .="AND edital.exercicio_licitacao        = '".Sessao::getExercicio()."'  \n";

    $numEdital = $this->getDado('num_edital');
    if ($numEdital) {
      $stSql .="    AND ".$tab.".num_edital=".$numEdital."         \n";
    }

    return $stSql;
  }

  public function recuperaEditalObjeto(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaEditalObjeto().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
  }

  public function montaRecuperaEditalObjeto()
  {
    $stSql  = " select                                   \n";
    $stSql .= "     le.num_edital,                       \n";
    $stSql .= "     le.exercicio,                        \n";
    $stSql .= "     le.cod_tipo_documento,               \n";
    $stSql .= "     le.cod_documento,                    \n";
    $stSql .= "     le.responsavel_juridico,             \n";
    $stSql .= "     le.exercicio_licitacao,              \n";
    $stSql .= "     le.cod_entidade,                     \n";
    $stSql .= "     le.cod_modalidade,                   \n";
    $stSql .= "     le.cod_licitacao,                    \n";
    $stSql .= "     le.local_entrega_propostas,          \n";
    $stSql .= "     le.cod_documento,                    \n";
    $stSql .= "     to_char(le.dt_entrega_propostas, 'dd/mm/yyyy') as dt_entrega_propostas,  \n";
    $stSql .= "     le.hora_entrega_propostas,           \n";
    $stSql .= "     le.local_abertura_propostas,         \n";
    $stSql .= "     to_char(le.dt_abertura_propostas, 'dd/mm/yyyy') as dt_abertura_propostas, \n";
    $stSql .= "     le.hora_abertura_propostas,          \n";
    $stSql .= "     le.dt_validade_proposta,                \n";
    $stSql .= "     le.condicoes_pagamento,              \n";
    $stSql .= "     le.local_entrega_material,           \n";
    $stSql .= "     to_char(le.dt_aprovacao_juridico, 'dd/mm/yyyy') as dt_aprovacao_juridico, \n";
    $stSql .= "     cgm.nom_cgm,                         \n";
    $stSql .= "     ll.cod_processo,	      		   \n";
    $stSql .= "     ll.exercicio_processo,               \n";
    $stSql .= "     lh.num_homologacao,                  \n";
    $stSql .= "     cm.descricao as nom_modalidade,      \n";
    $stSql .= "     cgm2.nom_cgm as nom_entidade,        \n";
    $stSql .= "     co.cod_objeto,                       \n";
    $stSql .= "     co.descricao,                        \n";
    $stSql .= "     lea.justificativa                    \n";
    $stSql .= " from                                     \n";
    $stSql .= "     licitacao.edital as le               \n";
    $stSql .= " left join                                \n";
    $stSql .= "     licitacao.edital_anulado as lea      \n";
    $stSql .= " on                                       \n";
    $stSql .= "         le.num_edital = lea.num_edital   \n";
    $stSql .= "     and le.exercicio = lea.exercicio,    \n";
    $stSql .= "     sw_cgm as cgm,                       \n";
    $stSql .= "     sw_cgm as cgm2,                      \n";
    $stSql .= "     compras.modalidade as cm,            \n";
    $stSql .= "     orcamento.entidade as oe,            \n";
    $stSql .= "     compras.objeto as co,                 \n";
    $stSql .= "     licitacao.licitacao as ll           \n";
    $stSql .= " left join                                      \n";
    $stSql .= "     licitacao.homologacao as lh                \n";
    $stSql .= " on                                             \n";
    $stSql .= "         ll.cod_licitacao = lh.cod_licitacao    \n";
    $stSql .= "     and ll.cod_modalidade = lh.cod_modalidade  \n";
    $stSql .= "     and ll.cod_entidade = lh.cod_entidade      \n";
    $stSql .= "     and ll.exercicio = lh.exercicio_licitacao  \n";
    $stSql .= " where                                    \n";
    $stSql .= "     cgm.numcgm = le.responsavel_juridico \n";
    $stSql .= "     and le.num_edital = ".$this->getDado( 'num_edital' )." \n";
    $stSql .= "     and le.exercicio  = '".$this->getDado( 'exercicio'  )."' \n";
    $stSql .= "     and le.cod_licitacao = ll.cod_licitacao   \n";
    $stSql .= "     and le.cod_modalidade = ll.cod_modalidade \n";
    $stSql .= "     and le.cod_entidade = ll.cod_entidade     \n";
    $stSql .= "     and le.exercicio = ll.exercicio           \n";
    $stSql .= "     and le.cod_modalidade = cm.cod_modalidade \n";
    $stSql .= "     and oe.cod_entidade = le.cod_entidade     \n";
    $stSql .= "     and oe.exercicio = le.exercicio           \n";
    $stSql .= "     and oe.numcgm = cgm2.numcgm               \n";
    $stSql .= "     and ll.cod_objeto = co.cod_objeto         \n";

    return $stSql;
  }

  public function recuperaEdital(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaEdital().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
  }

  public function montaRecuperaEdital()
  {
    $stSql  = " select                                   
                       le.num_edital,                    
                       le.exercicio,                     
                       le.cod_tipo_documento,            
                       le.cod_documento,                 
                       le.responsavel_juridico,          
                       le.exercicio_licitacao,           
                       le.cod_entidade,                  
                       le.cod_modalidade,                
                       le.cod_licitacao,                 
                       le.local_entrega_propostas,       
                       le.cod_documento,                 
                       to_char(le.dt_entrega_propostas, 'dd/mm/yyyy') as dt_entrega_propostas,
                       to_char(le.dt_final_entrega_propostas, 'dd/mm/yyyy') as dt_final_entrega_propostas,
                       le.hora_entrega_propostas,
                       le.hora_final_entrega_propostas,
                       diff_datas_em_dias(now()::date,le.dt_abertura_propostas::date) as dias_abertura_propostas,
                       le.local_abertura_propostas,
                       to_char(le.dt_abertura_propostas, 'dd/mm/yyyy') as dt_abertura_propostas,
                       le.hora_abertura_propostas,
                       to_char(le.dt_validade_proposta, 'dd/mm/yyyy') as dt_validade_proposta,
                       le.observacao_validade_proposta,     
                       le.condicoes_pagamento,              
                       le.local_entrega_material,           
                       to_char(le.dt_aprovacao_juridico, 'dd/mm/yyyy') as dt_aprovacao_juridico,
                       cgm.nom_cgm,
                       ll.cod_processo,
                       ll.exercicio_processo,
                       lh.num_homologacao,   
                       cm.descricao as nom_modalidade,
                       cgm2.nom_cgm as nom_entidade,  
                       lea.justificativa              
                   from                               
                       licitacao.edital as le         
                   left join                          
                       licitacao.edital_anulado as lea
                   on                                 
                           le.num_edital = lea.num_edital
                       and le.exercicio = lea.exercicio, 
                       sw_cgm as cgm,                    
                       sw_cgm as cgm2,                   
                       compras.modalidade as cm,         
                       orcamento.entidade as oe,         
                       licitacao.licitacao as ll         
                   left join                                    
                       licitacao.homologacao as lh              
                   on                                           
                           ll.cod_licitacao = lh.cod_licitacao  
                       and ll.cod_modalidade = lh.cod_modalidade
                       and ll.cod_entidade = lh.cod_entidade    
                       and ll.exercicio = lh.exercicio_licitacao
                   where                                    
                       cgm.numcgm = le.responsavel_juridico 
                       and le.num_edital = ".$this->getDado( 'num_edital' )."   
                       and le.exercicio  = '".$this->getDado( 'exercicio'  )."' 
                       and le.cod_licitacao = ll.cod_licitacao   
                       and le.cod_modalidade = ll.cod_modalidade 
                       and le.cod_entidade = ll.cod_entidade     
                       and le.exercicio = ll.exercicio           
                       and le.cod_modalidade = cm.cod_modalidade 
                       and oe.cod_entidade = le.cod_entidade     
                       and oe.exercicio = le.exercicio           
                       and oe.numcgm = cgm2.numcgm               \n";

    return $stSql;
  }

    public function recuperaLicitacaoDocumentosParticipante(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaLicitacaoDocumentosParticipante",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaLicitacaoDocumentosParticipante()
    {
        $stSql  = " SELECT  le.num_edital,
                            cp.descricao,
                            ll.exercicio, 
                            ll.cod_entidade,
                            ll.cod_licitacao||'/'||ll.exercicio as num_licitacao,
                            ll.cod_entidade,
                            cgm.nom_cgm as entidade,
                            ll.cod_modalidade,
                            ll.cod_licitacao,
                            LPAD((ll.cod_processo::VARCHAR), 5, '0') as cod_processo,
                            ll.exercicio_processo,
                            ll.cod_modalidade,
                            le.exercicio as exercicio_edital,
                            ll.cod_mapa,
                            ll.exercicio_mapa,
                            mapa.cod_tipo_licitacao,
                            le.num_edital || '/' || le.exercicio AS num_edital_lista 
                      
                      FROM  licitacao.licitacao as ll

                 LEFT JOIN  licitacao.edital as le
                        ON  ll.cod_licitacao  = le.cod_licitacao
                       AND  ll.cod_modalidade = le.cod_modalidade
                       AND  ll.cod_entidade   = le.cod_entidade
                       AND  ll.exercicio      = le.exercicio

                INNER JOIN  compras.mapa
                        ON  mapa.cod_mapa = ll.cod_mapa
                       AND  mapa.exercicio = ll.exercicio_mapa";

         if ($_REQUEST['stAcao'] == 'reemitir') {
              $stSql.= " INNER JOIN compras.mapa_cotacao AS mc
                                ON mc.cod_mapa       = ll.cod_mapa
                           AND mc.exercicio_mapa = ll.exercicio_mapa
                       AND NOT EXISTS (SELECT 1
                                      FROM compras.cotacao_anulada
                                WHERE cotacao_anulada.cod_cotacao = mc.cod_cotacao
                              AND cotacao_anulada.exercicio   = mc.exercicio_cotacao)

                      AND EXISTS (  SELECT 1
                              FROM compras.cotacao_fornecedor_item AS cf
                             WHERE cf.cod_cotacao  = mc.cod_cotacao
                               AND cf.exercicio    = mc.exercicio_cotacao)";
         }

     $stSql.= " INNER JOIN  compras.modalidade as cp
                        ON  cp.cod_modalidade = ll.cod_modalidade

                INNER JOIN  orcamento.entidade as oe
                        ON  oe.cod_entidade = ll.cod_entidade
                       AND  oe.exercicio = ll.exercicio

                INNER JOIN  sw_cgm as cgm
                        ON  cgm.numcgm = oe.numcgm

                INNER JOIN  licitacao.comissao_licitacao
                        ON  comissao_licitacao.exercicio      = ll.exercicio
                       AND  comissao_licitacao.cod_entidade   = ll.cod_entidade
                       AND  comissao_licitacao.cod_modalidade = ll.cod_modalidade
                       AND  comissao_licitacao.cod_licitacao  = ll.cod_licitacao

                     WHERE
                            ( EXISTS  (   SELECT  1
                                          FROM  licitacao.participante_documentos
                                    
                                    INNER JOIN  licitacao.participante
                                            ON  participante.cod_licitacao = participante_documentos.cod_licitacao
                                           AND  participante.cgm_fornecedor = participante_documentos.cgm_fornecedor
                                           AND  participante.cod_modalidade = participante_documentos.cod_modalidade
                                           AND  participante.cod_entidade = participante_documentos.cod_entidade
                                           AND  participante.exercicio = participante_documentos.exercicio
                                    
                                    INNER JOIN  licitacao.licitacao_documentos
                                            ON  licitacao_documentos.cod_documento = participante_documentos.cod_documento
                                           AND  licitacao_documentos.cod_licitacao = participante_documentos.cod_licitacao
                                           AND  licitacao_documentos.cod_modalidade = participante_documentos.cod_modalidade
                                           AND  licitacao_documentos.cod_entidade = participante_documentos.cod_entidade
                                           AND  licitacao_documentos.exercicio = participante_documentos.exercicio
                                         
                                         WHERE  participante_documentos.cod_licitacao = ll.cod_licitacao
                                           AND  participante_documentos.cod_modalidade = ll.cod_modalidade
                                           AND  participante_documentos.cod_entidade = ll.cod_entidade
                                           AND  participante_documentos.exercicio = ll.exercicio
                                    ) OR ll.cod_modalidade IN (6,7)
                ) AND

          EXISTS ( --- esta condição serve para excluir da listagem os mapas que foram totalmente anulados
            select mapa_itens.cod_mapa
                 , mapa_itens.exercicio
                 , mapa_itens.quantidade - coalesce ( mapa_item_anulacao.quantidade, 0 ) as quantidade
                 , mapa_itens.vl_total   - coalesce ( mapa_item_anulacao.vl_total  , 0 ) as vl_total
              from (select mapa_item.cod_mapa
                         , mapa_item.exercicio
                         , mapa_item.cod_item
                         , mapa_item.lote
                         , sum(mapa_item.quantidade) as quantidade
                         , sum(mapa_item.vl_total) as vl_total
                      from compras.mapa_item
                     Group by mapa_item.cod_mapa
                            , mapa_item.exercicio
                            , mapa_item.cod_item
                            , mapa_item.lote) as mapa_itens
                ----- buscando as possiveis anulações
                left join ( select mapa_item_anulacao.cod_mapa
                               , mapa_item_anulacao.exercicio
                               , mapa_item_anulacao.cod_item
                               , mapa_item_anulacao.lote
                               , sum ( mapa_item_anulacao.vl_total   ) as vl_total
                               , sum ( mapa_item_anulacao.quantidade ) as quantidade
                            from compras.mapa_item_anulacao
                          group by cod_mapa
                                 , exercicio
                                 , cod_item
                                 , lote ) as mapa_item_anulacao
                     on ( mapa_itens.cod_mapa  = mapa_item_anulacao.cod_mapa
                    and   mapa_itens.exercicio = mapa_item_anulacao.exercicio
                    and   mapa_itens.cod_item  = mapa_item_anulacao.cod_item
                    and   mapa_itens.lote      = mapa_item_anulacao.lote)
            where mapa_itens.quantidade - coalesce ( mapa_item_anulacao.quantidade, 0 ) > 0
              and mapa_itens.vl_total   - coalesce ( mapa_item_anulacao.vl_total  , 0 ) > 0
              and mapa_itens.cod_mapa  = mapa.cod_mapa
              and mapa_itens.exercicio = mapa.exercicio
            ) and
        ";
        
        if ( $this->getDado( 'num_edital' ) ) {
            $stSql .= " le.num_edital = ". $this->getDado( 'num_edital' )." and ";
        }

        if ( $this->getDado( 'exercicio_licitacao' ) ) {
            $stSql .= " ll.exercicio = '". $this->getDado( 'exercicio_licitacao' )."' and ";
        }

        if ( $this->getDado( 'cod_entidade' ) ) {
            $stSql .= " ll.cod_entidade in ( ". $this->getDado( 'cod_entidade' )." ) and ";
        }

        if ( $this->getDado( 'cod_modalidade' ) ) {
            $stSql .= " ll.cod_modalidade = ". $this->getDado( 'cod_modalidade' ). " and ";
        }

        if ( $this->getDado( 'cod_licitacao' ) ) {
            $stSql .= " ll.cod_licitacao = ". $this->getDado( 'cod_licitacao' ). " and ";
        }

        if ( $this->getDado( 'cod_processo' ) ) {
            $stSql .= "ll.cod_processo = ". $this->getDado( 'cod_processo' ). " and ";
        }

        if ( $this->getDado( 'cod_mapa' ) ) {
            $stSql .= "ll.cod_mapa = ". $this->getDado( 'cod_mapa' ). " and ";
        }

        if ( $this->getDado( 'cod_tipo_licitacao' ) ) {
            $stSql .= "ll.cod_tipo_licitacao = ". $this->getDado( 'cod_tipo_licitacao' ). " and ";
        }

        if ( $this->getDado( 'cod_criterio' ) ) {
            $stSql .= "ll.cod_criterio = ". $this->getDado( 'cod_criterio' ). " and ";
        }

        if ( $this->getDado( 'cod_objeto' ) ) {
            $stSql .= "ll.cod_objeto = ". $this->getDado( 'cod_objeto' ). " and ";
        }

        if ( $this->getDado( 'cod_comissao' ) ) {
            $stSql .= "comissao_licitacao.cod_comissao = ". $this->getDado( 'cod_comissao' ). " and ";
        }

        $stSql .= " NOT EXISTS (   SELECT  1
                                     FROM  licitacao.edital_anulado
                                    WHERE  edital_anulado.num_edital = le.num_edital
                                      AND  edital_anulado.exercicio  = le.exercicio
                                    )
                    
                    -- Para as modalidades 1,2,3,4,5,6,7,10,11 é obrigatório exister um edital
                    AND CASE WHEN ll.cod_modalidade in (1,2,3,4,5,6,7,10,11) THEN
                            
                           le.cod_licitacao  IS NOT NULL
                       AND le.cod_modalidade IS NOT NULL
                       AND le.cod_entidade   IS NOT NULL 
                       AND le.exercicio      IS NOT NULL 
        
                      -- Para as modalidades 8,9 é facultativo possuir um edital
                      WHEN ll.cod_modalidade in (8,9) THEN
                            
                            le.cod_licitacao  IS NULL
                         OR le.cod_modalidade IS NULL
                         OR le.cod_entidade   IS NULL 
                         OR le.exercicio      IS NULL 
        
                         OR le.cod_licitacao  IS NOT NULL
                         OR le.cod_modalidade IS NOT NULL
                         OR le.cod_entidade   IS NOT NULL 
                         OR le.exercicio      IS NOT NULL 
                    END ";

    return $stSql;

    }

    public function recuperaLicitacaoDocumentosParticipanteHabilitar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaLicitacaoDocumentosParticipanteHabilitar",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaLicitacaoDocumentosParticipanteHabilitar()
    {
        $stSql  = " SELECT
                            le.num_edital,
                            cp.descricao,
                            ll.exercicio,
                            ll.cod_entidade,
                            ll.cod_licitacao||'/'||ll.exercicio as num_licitacao,
                            ll.cod_entidade,
                            cgm.nom_cgm as entidade,
                            ll.cod_modalidade,
                            ll.cod_tipo_objeto,
                            ll.cod_licitacao,
                            ll.cod_processo,
                            ll.exercicio_processo,
                            ll.cod_modalidade,
                            ll.cod_mapa,
                            comissao.cod_comissao,
                            ll.exercicio_mapa,
                            mapa.cod_tipo_licitacao,
                            mapa_cotacao.exercicio_cotacao,
                            mapa_cotacao.cod_cotacao,
                            le.num_edital || '/' || le.exercicio as num_edital_lista
                      
                      FROM  licitacao.licitacao as ll

                 LEFT JOIN  licitacao.edital as le
                        ON  ll.cod_licitacao  = le.cod_licitacao
                       AND  ll.cod_modalidade = le.cod_modalidade
                       AND  ll.cod_entidade   = le.cod_entidade
                       AND  ll.exercicio      = le.exercicio
                
                INNER JOIN  compras.mapa
                        ON  mapa.cod_mapa = ll.cod_mapa
                       AND  mapa.exercicio = ll.exercicio_mapa
                       
                INNER JOIN  compras.mapa_cotacao
                        ON  mapa_cotacao.cod_mapa = ll.cod_mapa
                       AND  mapa_cotacao.exercicio_mapa = ll.exercicio_mapa

".($this->getDado('acao') == 'excluir'?"
                INNER JOIN  compras.julgamento
                        ON  mapa_cotacao.exercicio_cotacao  = julgamento.exercicio
                       AND  mapa_cotacao.cod_cotacao        = julgamento.cod_cotacao
":"")."
                INNER JOIN  compras.modalidade as cp
                        ON  cp.cod_modalidade = ll.cod_modalidade
                        
                INNER JOIN  orcamento.entidade as oe
                        ON  oe.cod_entidade = ll.cod_entidade
                       AND  oe.exercicio = ll.exercicio
                       
                 LEFT JOIN  licitacao.comissao_licitacao
                        ON  le.exercicio      = comissao_licitacao.exercicio
                       AND  le.cod_entidade   = comissao_licitacao.cod_entidade
                       AND  le.cod_modalidade = comissao_licitacao.cod_modalidade
                       AND  le.cod_licitacao  = comissao_licitacao.cod_licitacao
                       
                 LEFT JOIN  licitacao.comissao
                        ON  comissao_licitacao.cod_comissao = comissao.cod_comissao
                        
                INNER JOIN  sw_cgm as cgm
                        ON  cgm.numcgm = oe.numcgm
                        
                     WHERE ( EXISTS  (   SELECT  1
                                          FROM  licitacao.participante_documentos
                                    
                                    INNER JOIN  licitacao.participante
                                            ON  participante.cod_licitacao = participante_documentos.cod_licitacao
                                           AND  participante.cgm_fornecedor = participante_documentos.cgm_fornecedor
                                           AND  participante.cod_modalidade = participante_documentos.cod_modalidade
                                           AND  participante.cod_entidade = participante_documentos.cod_entidade
                                           AND  participante.exercicio = participante_documentos.exercicio
                                    
                                    INNER JOIN  licitacao.licitacao_documentos
                                            ON  licitacao_documentos.cod_documento = participante_documentos.cod_documento
                                           AND  licitacao_documentos.cod_licitacao = participante_documentos.cod_licitacao
                                           AND  licitacao_documentos.cod_modalidade = participante_documentos.cod_modalidade
                                           AND  licitacao_documentos.cod_entidade = participante_documentos.cod_entidade
                                           AND  licitacao_documentos.exercicio = participante_documentos.exercicio
                                    
                                         WHERE  participante_documentos.cod_licitacao = ll.cod_licitacao
                                           AND  participante_documentos.cod_modalidade = ll.cod_modalidade
                                           AND  participante_documentos.cod_entidade = ll.cod_entidade
                                           AND  participante_documentos.exercicio = ll.exercicio
                                     ) OR ll.cod_modalidade IN (6,7)
                      ) AND \n ";
                      
        if ( $this->getDado( 'num_edital' ) ) {
            $stSql .= " le.num_edital = ". $this->getDado( 'num_edital' )." and ";
        }

        if ( $this->getDado( 'exercicio_licitacao' ) ) {
            $stSql .= " ll.exercicio = '". $this->getDado( 'exercicio_licitacao' )."' and ";
        }

        if ( $this->getDado( 'cod_entidade' ) ) {
            $stSql .= " ll.cod_entidade in ( ". $this->getDado( 'cod_entidade' )." ) and ";
        }

        if ( $this->getDado( 'cod_modalidade' ) ) {
            $stSql .= " ll.cod_modalidade = ". $this->getDado( 'cod_modalidade' ). " and ";
        }

        if ( $this->getDado( 'cod_licitacao' ) ) {
            $stSql .= " ll.cod_licitacao = ". $this->getDado( 'cod_licitacao' ). " and ";
        }

        if ( $this->getDado( 'cod_processo' ) ) {
            $stSql .= "ll.cod_processo = ". $this->getDado( 'cod_processo' ). " and ";
        }

        if ( $this->getDado( 'cod_mapa' ) ) {
            $stSql .= "ll.cod_mapa = ". $this->getDado( 'cod_mapa' ). " and ";
        }

        if ( $this->getDado( 'cod_tipo_licitacao' ) ) {
            $stSql .= "ll.cod_tipo_licitacao = ". $this->getDado( 'cod_tipo_licitacao' ). " and ";
        }

        if ( $this->getDado( 'cod_criterio' ) ) {
            $stSql .= "ll.cod_criterio = ". $this->getDado( 'cod_criterio' ). " and ";
        }

        if ( $this->getDado( 'cod_tipo_objeto' ) ) {
            $stSql .= "ll.cod_tipo_objeto = ". $this->getDado( 'cod_tipo_objeto' ). " and ";
        }

        if ( $this->getDado( 'cod_objeto' ) ) {
            $stSql .= "ll.cod_objeto = ". $this->getDado( 'cod_objeto' ). " and ";
        }

        if ( $this->getDado( 'cod_comissao' ) ) {
            $stSql .= "comissao.cod_comissao = ". $this->getDado( 'cod_comissao' ). " and ";
        }

        $stSql .= " NOT EXISTS (   SELECT  1
                                     FROM  licitacao.edital_anulado
                                    WHERE  edital_anulado.num_edital = le.num_edital
                                      AND  edital_anulado.exercicio = le.exercicio
                                    )

                AND NOT EXISTS (   SELECT  1
                                     FROM  licitacao.edital_suspenso
                                    WHERE  edital_suspenso.num_edital = le.num_edital
                                      AND  edital_suspenso.exercicio = le.exercicio
                                    )
                
                -- Para as modalidades 1,2,3,4,5,6,7,10,11 é obrigatório exister um edital
                AND CASE WHEN ll.cod_modalidade in (1,2,3,4,5,6,7,10,11) THEN
                        
                       le.cod_licitacao  IS NOT NULL
                   AND le.cod_modalidade IS NOT NULL
                   AND le.cod_entidade   IS NOT NULL 
                   AND le.exercicio      IS NOT NULL 
    
                  -- Para as modalidades 8,9 é facultativo possuir um edital
                  WHEN ll.cod_modalidade in (8,9) THEN
                        
                        le.cod_licitacao  IS NULL
                     OR le.cod_modalidade IS NULL
                     OR le.cod_entidade   IS NULL 
                     OR le.exercicio      IS NULL 
    
                     OR le.cod_licitacao  IS NOT NULL
                     OR le.cod_modalidade IS NOT NULL
                     OR le.cod_entidade   IS NOT NULL 
                     OR le.exercicio      IS NOT NULL 
                END ";

        return $stSql;

    }

function recuperaEditalSuspender(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaEditalSuspender().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaEditalSuspender()
{
    $stSql  = "
    SELECT licitacao.cod_entidade
         , edital.num_edital
         , sw_cgm.nom_cgm AS nom_entidade
         , licitacao.cod_modalidade
         , modalidade.descricao AS nom_modalidade
         , licitacao.exercicio
         , edital.cod_licitacao
         , licitacao.cod_objeto
         , CASE WHEN (edital_suspenso.justificativa <> '') THEN
                    'Suspenso'
                ELSE 'Ativo'
           END as situacao
         , edital_suspenso.justificativa
      FROM licitacao.licitacao
 LEFT JOIN licitacao.edital
        ON licitacao.cod_licitacao = edital.cod_licitacao
       AND licitacao.cod_modalidade = edital.cod_modalidade
       AND licitacao.cod_entidade = edital.cod_entidade
       AND licitacao.exercicio = edital.exercicio_licitacao
 LEFT JOIN licitacao.edital_suspenso
        ON edital_suspenso.num_edital = edital.num_edital
       AND edital_suspenso.exercicio = edital.exercicio
INNER JOIN compras.modalidade
        ON modalidade.cod_modalidade = licitacao.cod_modalidade
INNER JOIN orcamento.entidade
        ON entidade.exercicio = licitacao.exercicio
       AND entidade.cod_entidade = licitacao.cod_entidade
INNER JOIN sw_cgm
        ON sw_cgm.numcgm = entidade.numcgm
 LEFT JOIN compras.mapa_cotacao
        ON licitacao.cod_mapa = mapa_cotacao.cod_mapa
       AND licitacao.exercicio_mapa = mapa_cotacao.exercicio_mapa
       AND mapa_cotacao.cod_cotacao
    NOT IN ( SELECT cotacao_anulada.cod_cotacao
               FROM compras.cotacao_anulada
              WHERE cotacao_anulada.exercicio = licitacao.exercicio
           )
     WHERE NOT EXISTS ( SELECT 1
                          FROM empenho.item_pre_empenho_julgamento
                         WHERE item_pre_empenho_julgamento.exercicio = mapa_cotacao.exercicio_cotacao
                           AND item_pre_empenho_julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                      )
       AND NOT EXISTS ( SELECT 1
                          FROM licitacao.edital_anulado
                         WHERE edital_anulado.num_edital = edital.num_edital
                           AND edital_anulado.exercicio = edital.exercicio
                      )

    ";
    if ($this->getDado('cod_licitacao')) {
        $stSql .="  and edital.cod_licitacao = ".$this->getDado('cod_licitacao')."\n";
    }

    if ($this->getDado('cod_modalidade')) {
        $stSql .="  and licitacao.cod_modalidade = ".$this->getDado('cod_modalidade')."\n";
    }

    if ($this->getDado('cod_entidade')) {
        $stSql .=" and licitacao.cod_entidade = ".$this->getDado('cod_entidade')."\n";
    }

    if ($this->getDado('num_edital')) {
      $stSql .="    AND edital.num_edital = ".$this->getDado('num_edital')."      \n";
    }
    if ($this->getDado('exercicio')) {
      $stSql .="    AND edital.exercicio = '".$this->getDado('exercicio')."'        \n";
    }

    return $stSql;
}

}
