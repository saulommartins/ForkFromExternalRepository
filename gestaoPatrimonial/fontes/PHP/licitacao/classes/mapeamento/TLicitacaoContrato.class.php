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

    * Classe de mapeamento da tabela licitacao.contrato
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TLicitacaoContrato.class.php 65317 2016-05-12 17:40:05Z carlos.silva $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TLicitacaoContrato extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

function __construct()
{
    parent::Persistente();
    $this->setTabela("licitacao.contrato");

    $this->setCampoCod('num_contrato');
    $this->setComplementoChave('exercicio,cod_entidade');

    $this->AddCampo('num_contrato'             ,'sequence',true ,''    ,true ,false                            );
    $this->AddCampo('exercicio'                ,'char'    ,true ,'4'   ,true ,'TLicitacaoLicitacao'            );
    $this->AddCampo('cod_entidade'             ,'integer' ,true ,''    ,true ,'TLicitacaoLicitacao'            );
    $this->AddCampo('cod_tipo_documento'       ,'integer' ,false,''    ,false,'TAdministracaoModeloDocumento'  );
    $this->AddCampo('cod_tipo_contrato'        ,'integer' ,false,''    ,false,'TLicitacaoTipoContrato'         );
    $this->AddCampo('cod_documento'            ,'integer' ,false,''    ,false,'TAdministracaoModeloDocumento'  );
    $this->AddCampo('cgm_responsavel_juridico' ,'integer' ,true ,''    ,false,'TCGM'                           );
    $this->AddCampo('cgm_contratado'           ,'integer' ,true ,''    ,false,'TComprasFornecedor'             );
    $this->AddCampo('dt_assinatura'            ,'date'    ,true ,''    ,false,false                            );
    $this->AddCampo('vencimento'               ,'date'    ,true ,''    ,false,false                            );
    $this->AddCampo('valor_contratado'         ,'numeric' ,true ,'14,2',false,false                            );
    $this->AddCampo('valor_garantia'           ,'numeric' ,true ,'14,2',false,false                            );
    $this->AddCampo('inicio_execucao'          , 'date'   ,true ,''    ,false,false                            );
    $this->AddCampo('fim_execucao'             , 'date'   ,true ,''    ,false,false                            );

    $this->AddCampo('num_orgao'                ,'integer' ,true ,''    ,true ,'TOrcamentoOrgao'                );
    $this->AddCampo('num_unidade'              ,'integer' ,true ,''    ,true ,'TOrcamentoUnidade'              );
    $this->AddCampo('numero_contrato'          ,'integer' ,true ,''    ,true ,false                            );
    $this->AddCampo('tipo_objeto'              ,'integer' ,true ,''    ,true ,'TComprasTipoObjeto'             );
    $this->AddCampo('objeto'                   ,'char'    ,true ,''    ,true ,false                            );
    $this->AddCampo('forma_fornecimento'       ,'char'    ,true ,'50'  ,true ,false                            );
    $this->AddCampo('forma_pagamento'          ,'char'    ,true ,'100' ,true ,false                            );
    $this->AddCampo('cgm_signatario'           ,'integer' ,true ,''    ,true ,'TCGM'                           );
    $this->AddCampo('prazo_execucao'           ,'char'    ,true ,'100' ,true ,false                            );
    $this->AddCampo('multa_rescisoria'         ,'char'    ,true ,'250' ,true ,false                            );
    $this->AddCampo('justificativa'            ,'char'    ,true ,'250' ,true ,false                            );
    $this->AddCampo('razao'                    ,'char'    ,true ,'250' ,true ,false                            );
    $this->AddCampo('fundamentacao_legal'      ,'char'    ,true ,'250' ,true ,false                            );
    $this->AddCampo('cod_tipo_instrumento'     ,'integer' ,false,''    ,false,true                             );
    $this->AddCampo('cod_garantia'             ,'integer' ,false,''    ,false,true                             );
    $this->AddCampo('multa_inadimplemento'     ,'char'    ,false,'100' ,false,false                            );
    $this->AddCampo('cgm_representante_legal'  ,'integer' ,false,''    ,false,true                             );
}

function recuperaDadosContrato(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosContrato().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosContrato()
{
    $stSql  = " SELECT					   			                                  \n";
    $stSql .= "        contrato.exercicio          			    	                  \n";
    $stSql .= "		 , contrato.cod_entidade            			                  \n";
    $stSql .= "		 , contrato.num_contrato             			                  \n";
    $stSql .= "		 , contrato.cod_tipo_documento       			                  \n";
    $stSql .= "		 , contrato.cod_documento            			                  \n";
    $stSql .= "		 , contrato.cgm_responsavel_juridico 			                  \n";
    $stSql .= "		 , contrato.cgm_contratado           			                  \n";
    $stSql .= "		 , to_char(contrato.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura \n";
    $stSql .= "		 , contrato.vencimento               			                  \n";
    $stSql .= "		 , contrato.valor_contratado         			                  \n";
    $stSql .= "		 , contrato.valor_garantia           			                  \n";
    $stSql .= "		 , contrato.inicio_execucao          			                  \n";
    $stSql .= "		 , contrato.fim_execucao        	 			                  \n";
    $stSql .= "      , sw_cgm.nom_cgm        as nom_entidade			              \n";
    $stSql .= "      , credor.nom_cgm        as nom_credor				              \n";

    $stSql .= "      , contrato.num_orgao                                             \n";
    $stSql .= "      , contrato.num_unidade                                           \n";
    $stSql .= "      , contrato.numero_contrato                                       \n";
    $stSql .= "      , contrato.objeto                                                \n";
    $stSql .= "      , contrato.forma_fornecimento                                    \n";
    $stSql .= "      , contrato.forma_pagamento                                       \n";
    $stSql .= "      , contrato.cgm_signatario                                        \n";
    $stSql .= "      , contrato.prazo_execucao                                        \n";
    $stSql .= "      , contrato.multa_rescisoria                                      \n";
    $stSql .= "      , contrato.justificativa                                         \n";
    $stSql .= "      , contrato.razao                                                 \n";
    $stSql .= "      , contrato.fundamentacao_legal                                   \n";
    $stSql .= "      , contrato.cod_garantia                                          \n";
    $stSql .= "      , contrato.multa_inadimplemento                                  \n";
    $stSql .= "      , contrato.cod_tipo_instrumento                                  \n";
    $stSql .= "      , contrato.cgm_representante_legal                               \n";
    $stSql .= "                                                                       \n";
    $stSql .= "   FROM													              \n";
    $stSql .= "   	  licitacao.contrato								              \n";
    $stSql .= "        JOIN ( SELECT numcgm								              \n";
    $stSql .= "                    , nom_cgm							              \n";
    $stSql .= "                 FROM sw_cgm ) as credor					              \n";
    $stSql .= "          ON ( credor.numcgm = contrato.cgm_contratado )	              \n";
    $stSql .= "      , orcamento.entidade								              \n";
    $stSql .= "      , sw_cgm											              \n";
    $stSql .= "  WHERE													              \n";
    $stSql .= "        contrato.cod_entidade = entidade.cod_entidade	              \n";
    $stSql .= "    AND contrato.exercicio    = entidade.exercicio		              \n";
    $stSql .= "    AND entidade.numcgm       = sw_cgm.numcgm			              \n";

    return $stSql;
}

function recuperaContrato(&$rsRecordSet, $stFiltro = "",$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaContrato().$stFiltro;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

///arrumar
function montaRecuperaContrato()
{
    $stSql = "    SELECT objeto.cod_objeto
                       , objeto.descricao
                       , contrato.exercicio

                    FROM licitacao.contrato

               LEFT JOIN licitacao.contrato_licitacao
                      ON (contrato.num_contrato = contrato_licitacao.num_contrato
                     AND contrato.exercicio     = contrato_licitacao.exercicio
                     AND contrato.cod_entidade  = contrato_licitacao.cod_entidade)

               LEFT JOIN licitacao.contrato_compra_direta
                      ON (contrato.num_contrato = contrato_compra_direta.num_contrato
                     AND contrato.exercicio     = contrato_compra_direta.exercicio
                     AND contrato.cod_entidade  = contrato_compra_direta.cod_entidade)

               LEFT JOIN licitacao.licitacao
                      ON (licitacao.cod_licitacao = contrato_licitacao.cod_licitacao
                     AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade
                     AND licitacao.exercicio      = contrato_licitacao.exercicio_licitacao
                     AND licitacao.cod_entidade   = contrato_licitacao.cod_entidade)

               LEFT JOIN compras.compra_direta
                      ON (compra_direta.cod_compra_direta = contrato_compra_direta.cod_compra_direta
                     AND compra_direta.cod_modalidade     = contrato_compra_direta.cod_modalidade
                     AND compra_direta.exercicio_entidade = contrato_compra_direta.exercicio
                     AND compra_direta.cod_entidade       = contrato_compra_direta.cod_entidade)

               LEFT JOIN compras.objeto
                      ON (   licitacao.cod_objeto     = objeto.cod_objeto
                          OR compra_direta.cod_objeto = objeto.cod_objeto)
                   WHERE objeto.cod_objeto IS NOT NULL \n";

    return $stSql;
}

function montaRecuperaRelacionamento()
{
    $stSql = "SELECT contrato.cgm_responsavel_juridico
                   , TO_CHAR(contrato.dt_assinatura,'dd/mm/yyyy') as dt_assinatura           
                   , TO_CHAR(contrato.vencimento,'dd/mm/yyyy') as vencimento                 
                   , contrato.cgm_contratado                                                 
                   , sw_cgm.nom_cgm                                                          
                   , cgm_contratado.nom_cgm as nom_contratado                                
                   , objeto.descricao                                                        
                   , contrato_licitacao.cod_licitacao                                        
                   , contrato_licitacao.cod_modalidade                                       
                   , contrato.cod_entidade                                                   
                   , contrato.num_contrato                                                   
                   , contrato.cod_documento                                                  
                   , contrato.cod_tipo_documento                                             
                   , contrato.valor_garantia                                                 
                   , contrato.valor_contratado                                               
                   , TO_CHAR( contrato.inicio_execucao,'dd/mm/yyyy') as inicio_execucao      
                   , TO_CHAR( contrato.fim_execucao   ,'dd/mm/yyyy') as fim_execucao         
                   , licitacao.cod_mapa                                                      
                   , contrato_licitacao.exercicio                                            
                   , contrato_licitacao.exercicio_licitacao                                  
                   , (SELECT descricao FROM licitacao.tipo_contrato where cod_tipo = contrato.cod_tipo_contrato) AS tipo_descricao
                   , (SELECT cod_tipo FROM licitacao.tipo_contrato where cod_tipo = contrato.cod_tipo_contrato) AS cod_tipo_contrato
                   , contrato.num_orgao                                                     
                   , contrato.num_unidade                                                   
                   , contrato.numero_contrato                                               
                   , contrato.objeto
                   , contrato.tipo_objeto AS cod_tipo_objeto
                   , tipo_objeto.descricao AS tipo_objeto
                   , contrato.forma_fornecimento                                            
                   , contrato.forma_pagamento                                               
                   , contrato.cgm_signatario
                   , cgm_signatario.nom_cgm AS nom_signatario
                   , contrato.prazo_execucao                                                
                   , contrato.multa_rescisoria                                              
                   , contrato.justificativa                                                 
                   , contrato.razao                                                         
                   , contrato.fundamentacao_legal
                   , orgao.nom_orgao
                   , unidade.nom_unidade
                   , entidade.nom_cgm AS nom_entidade
                   , contrato.cod_garantia               
                   , contrato.multa_inadimplemento       
                   , contrato.cod_tipo_instrumento       
                   , contrato.cgm_representante_legal
                   , modalidade.descricao AS modalidade
                
                FROM licitacao.licitacao

	      INNER JOIN licitacao.contrato_licitacao 
	  	          ON licitacao.cod_licitacao = contrato_licitacao.cod_licitacao              
                 AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade            
                 AND licitacao.cod_entidade = contrato_licitacao.cod_entidade                
                 AND licitacao.exercicio = contrato_licitacao.exercicio_licitacao

	      INNER JOIN licitacao.contrato
		          ON contrato_licitacao.cod_entidade = contrato.cod_entidade                 
                 AND contrato_licitacao.exercicio = contrato.exercicio                       
                 AND contrato_licitacao.num_contrato = contrato.num_contrato     

          INNER JOIN compras.objeto
                  ON licitacao.cod_objeto = objeto.cod_objeto       

          INNER JOIN sw_cgm
         		  ON sw_cgm.numcgm = contrato.cgm_responsavel_juridico                                                             
		  
          INNER JOIN sw_cgm as cgm_contratado    
		          ON cgm_contratado.numcgm = contrato.cgm_contratado 

          INNER JOIN compras.modalidade
		          ON modalidade.cod_modalidade = licitacao.cod_modalidade   
               
           LEFT JOIN sw_cgm as cgm_signatario                                            
                  ON cgm_signatario.numcgm = contrato.cgm_signatario
            
          INNER JOIN ( SELECT sw_cgm.nom_cgm
                            , entidade.cod_entidade
                            , entidade.exercicio
                         FROM orcamento.entidade
                   INNER JOIN sw_cgm
                           ON sw_cgm.numcgm    = entidade.numcgm
                     ) AS entidade
                   ON entidade.cod_entidade = contrato.cod_entidade
                  AND entidade.exercicio    = contrato.exercicio
                     
           LEFT JOIN orcamento.orgao
                  ON orgao.num_orgao = contrato.num_orgao
                 AND orgao.exercicio = contrato.exercicio
        
           LEFT JOIN orcamento.unidade
                  ON unidade.num_unidade = contrato.num_unidade
                 AND unidade.num_orgao   = contrato.num_orgao
                 AND unidade.exercicio   = contrato.exercicio
                 
           LEFT JOIN compras.tipo_objeto
                  ON tipo_objeto.cod_tipo_objeto = contrato.tipo_objeto
                  
               WHERE 1=1  \n";
            
    if ( $this->getDado('num_contrato') ) {
        $stSql.= "   AND contrato.num_contrato = ".$this->getDado('num_contrato')."           \n";
    }
    if ( $this->getDado('cod_entidade') ) {
        $stSql.= "   and contrato.cod_entidade = ".$this->getDado('cod_entidade')."           \n";
    }
    if ( $this->getDado('exercicio') ) {
        $stSql.= "   and contrato.exercicio = '".$this->getDado('exercicio')."'               \n";
    }

    return $stSql;
}

function recuperaNaoAnulados(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaNaoAnulados",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaNaoAnulados()
{
    $stSql  =" SELECT contrato.cgm_responsavel_juridico                                             \n";
    $stSql .= "      , TO_CHAR(contrato.dt_assinatura,'dd/mm/yyyy') as dt_assinatura                \n";
    $stSql .= ", contrato_licitacao.cod_licitacao                                                             \n";
    $stSql .= ", contrato.cod_entidade                                                              \n";
    $stSql .= ", contrato.num_contrato                                                              \n";
    $stSql .= ", contrato.valor_contratado                                                          \n";
    $stSql .= ", licitacao.cod_mapa                                                                 \n";
    $stSql .= ", licitacao.cod_entidade                                                             \n";
    $stSql .= ", sw_cgm.nom_cgm                                                                     \n";
    $stSql .= "FROM licitacao.contrato                                                              \n";

    $stSql .= "INNER JOIN licitacao.contrato_licitacao                                              \n";
    $stSql .= "ON contrato.num_contrato = contrato_licitacao.num_contrato                           \n";
    $stSql .= "AND contrato.cod_entidade = contrato_licitacao.cod_entidade                          \n";
    $stSql .= "AND contrato.exercicio = contrato_licitacao.exercicio                                \n";

    $stSql .= "INNER JOIN licitacao.licitacao                                                       \n";
    $stSql .= "ON licitacao.cod_licitacao = contrato_licitacao.cod_licitacao                        \n";
    $stSql .= "AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade                     \n";
    $stSql .= "AND licitacao.cod_entidade = contrato_licitacao.cod_entidade                         \n";
    $stSql .= "AND licitacao.exercicio = contrato_licitacao.exercicio_licitacao                     \n";

    $stSql .= "INNER JOIN orcamento.entidade                                                        \n";
    $stSql .= "ON licitacao.exercicio = entidade.exercicio                                          \n";
    $stSql .= "AND licitacao.cod_entidade = entidade.cod_entidade                                   \n";
    $stSql .= "INNER JOIN sw_cgm                                                                    \n";
    $stSql .= "ON sw_cgm.numcgm = entidade.numcgm                                                   \n";
    $stSql .= "WHERE NOT EXISTS (    SELECT 1                                                       \n";
    $stSql .= "                        FROM licitacao.contrato_anulado                              \n";
    $stSql .= "                       WHERE contrato_anulado.num_contrato = contrato.num_contrato   \n";
    $stSql .= "                         AND contrato_anulado.cod_entidade = contrato.cod_entidade   \n";
    $stSql .= "                         AND contrato_anulado.exercicio = contrato.exercicio         \n";
    $stSql .= "                 )";

    return $stSql;
}

function recuperaProjAtiv(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaProjAtiv",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaProjAtiv()
{
    $stSql  = " SELECT cotacao_fornecedor_item.vl_cotacao                                                                                         \n";
    $stSql .= "      , catalogo_item.cod_item                                                                                                     \n";
    $stSql .= "      , catalogo_item.descricao                                                                                                    \n";
    $stSql .= "      , pao.num_pao                                                                                                                \n";
    $stSql .= "      , pao.nom_pao                                                                                                                \n";
    $stSql .= " FROM licitacao.licitacao                                                                                                          \n";
    $stSql .= "INNER JOIN licitacao.cotacao_licitacao                                                                                             \n";
    $stSql .= "        ON cotacao_licitacao.cod_licitacao = licitacao.cod_licitacao                                                               \n";
    $stSql .= "       AND cotacao_licitacao.cod_modalidade = licitacao.cod_modalidade                                                             \n";
    $stSql .= "       AND cotacao_licitacao.cod_entidade = licitacao.cod_entidade                                                                 \n";
    $stSql .= "       AND cotacao_licitacao.exercicio_licitacao = licitacao.exercicio                                                             \n";
    $stSql .= "       AND cotacao_licitacao.cgm_fornecedor = ".$this->getDado('cgm_fornecedor')."                                                 \n";
    $stSql .= "INNER JOIN compras.cotacao_fornecedor_item                                                                                         \n";
    $stSql .= "        ON cotacao_fornecedor_item.exercicio = cotacao_licitacao.exercicio_cotacao                                                 \n";
    $stSql .= "       AND cotacao_fornecedor_item.cod_cotacao = cotacao_licitacao.cod_cotacao                                                     \n";
    $stSql .= "       AND cotacao_fornecedor_item.cod_item = cotacao_licitacao.cod_item                                                           \n";
    $stSql .= "       AND cotacao_fornecedor_item.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor                                               \n";
    $stSql .= "       AND cotacao_fornecedor_item.lote = cotacao_licitacao.lote                                                                   \n";
    $stSql .= "INNER JOIN compras.julgamento_item                                                                                                 \n";
    $stSql .= "        ON julgamento_item.exercicio = cotacao_licitacao.exercicio_cotacao                                                         \n";
    $stSql .= "       AND julgamento_item.cod_cotacao = cotacao_licitacao.cod_cotacao                                                             \n";
    $stSql .= "       AND julgamento_item.cod_item = cotacao_licitacao.cod_item                                                                   \n";
    $stSql .= "       AND julgamento_item.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor                                                       \n";
    $stSql .= "       AND julgamento_item.lote = cotacao_licitacao.lote                                                                           \n";
    $stSql .= "       AND julgamento_item.ordem = 1                                                                                               \n";
    $stSql .= "INNER JOIN compras.mapa_item                                                                                                       \n";
    $stSql .= "        ON mapa_item.exercicio = licitacao.exercicio_mapa                                                                          \n";
    $stSql .= "       AND mapa_item.cod_mapa = licitacao.cod_mapa                                                                                 \n";
    $stSql .= "       AND mapa_item.cod_item = cotacao_licitacao.cod_item                                                                         \n";
    $stSql .= "INNER JOIN compras.solicitacao_item_dotacao                                                                                        \n";
    $stSql .= "        ON solicitacao_item_dotacao.exercicio = mapa_item.exercicio_solicitacao                                                    \n";
    $stSql .= "       AND solicitacao_item_dotacao.cod_entidade = mapa_item.cod_entidade                                                          \n";
    $stSql .= "       AND solicitacao_item_dotacao.cod_solicitacao = mapa_item.cod_solicitacao                                                    \n";
    $stSql .= "       AND solicitacao_item_dotacao.cod_centro = mapa_item.cod_centro                                                              \n";
    $stSql .= "       AND solicitacao_item_dotacao.cod_item = mapa_item.cod_item                                                                  \n";
    $stSql .= "INNER JOIN orcamento.despesa                                                                                                       \n";
    $stSql .= "        ON despesa.exercicio = solicitacao_item_dotacao.exercicio                                                                  \n";
    $stSql .= "       AND despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa                                                              \n";
    $stSql .= "INNER JOIN orcamento.pao                                                                                                           \n";
    $stSql .= "        ON pao.exercicio = despesa.exercicio                                                                                       \n";
    $stSql .= "       AND pao.num_pao = despesa.num_pao                                                                                           \n";
    $stSql .= "INNER JOIN almoxarifado.catalogo_item                                                                                              \n";
    $stSql .= "        ON catalogo_item.cod_item = solicitacao_item_dotacao.cod_item                                                              \n";
    $stSql .= "WHERE NOT EXISTS ( SELECT 1                                                                                                        \n";
    $stSql .= "                     FROM compras.cotacao_fornecedor_item_desclassificacao                                                         \n";
    $stSql .= "                    WHERE cotacao_fornecedor_item_desclassificacao.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor         \n";
    $stSql .= "                      AND cotacao_fornecedor_item_desclassificacao.cod_item = cotacao_fornecedor_item.cod_item                     \n";
    $stSql .= "                      AND cotacao_fornecedor_item_desclassificacao.cod_cotacao = cotacao_fornecedor_item.cod_cotacao               \n";
    $stSql .= "                      AND cotacao_fornecedor_item_desclassificacao.exercicio = cotacao_fornecedor_item.exercicio                   \n";
    $stSql .= "                      AND cotacao_fornecedor_item_desclassificacao.lote = cotacao_fornecedor_item.lote                             \n";
    $stSql .= "                 )                                                                                                                 \n";
    $stSql .= " AND licitacao.cod_licitacao = ".$this->getDado('cod_licitacao')."                                                                 \n";
    $stSql .= " AND licitacao.cod_modalidade = ".$this->getDado('cod_modalidade')."                                                               \n";
    $stSql .= " AND licitacao.cod_entidade = ".$this->getDado('cod_entidade')."                                                                   \n";
    $stSql .= " AND licitacao.exercicio = '".$this->getDado('exercicio')."'                                                                       \n";

    return $stSql;
}

function recuperaProjAtivCompraDireta(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaProjAtivCompraDireta",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaProjAtivCompraDireta()
{
    $stSql= "
        SELECT cotacao_fornecedor_item.vl_cotacao
             , catalogo_item.cod_item
             , catalogo_item.descricao
             , pao.num_pao
             , pao.nom_pao
             , cotacao_fornecedor_item.cgm_fornecedor
             , cotacao.cod_cotacao
             , to_char(julgamento.timestamp,'dd/mm/yyyy') as data_julgamento
          FROM  compras.compra_direta

    INNER JOIN compras.mapa
            ON compra_direta.cod_mapa = mapa.cod_mapa
           AND compra_direta.exercicio_mapa = mapa.exercicio

    INNER JOIN compras.mapa_cotacao
            ON mapa.cod_mapa = mapa_cotacao.cod_mapa
           AND mapa.exercicio = mapa_cotacao.exercicio_mapa

    INNER JOIN compras.cotacao
            ON mapa_cotacao.cod_cotacao = cotacao.cod_cotacao
           AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio
           AND NOT EXISTS( SELECT 1 from compras.cotacao_anulada
                              WHERE cotacao.cod_cotacao = cotacao_anulada.cod_cotacao
                                AND cotacao.exercicio = cotacao_anulada.exercicio
                           )

    INNER JOIN compras.julgamento
            ON cotacao.cod_cotacao = julgamento.cod_cotacao
           AND cotacao.exercicio = julgamento.exercicio

    INNER JOIN compras.julgamento_item
            ON julgamento.cod_cotacao = julgamento_item.cod_cotacao
           AND julgamento.exercicio = julgamento_item.exercicio

    INNER JOIN compras.cotacao_fornecedor_item
            ON julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
           AND julgamento_item.exercicio = cotacao_fornecedor_item.exercicio
           AND julgamento_item.cod_item = cotacao_fornecedor_item.cod_item
           AND julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
           AND julgamento_item.lote = cotacao_fornecedor_item.lote
           AND NOT EXISTS ( SELECT 1
                              FROM compras.cotacao_fornecedor_item_desclassificacao
                             WHERE cotacao_fornecedor_item_desclassificacao.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                               AND cotacao_fornecedor_item_desclassificacao.cod_item = cotacao_fornecedor_item.cod_item
                               AND cotacao_fornecedor_item_desclassificacao.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                               AND cotacao_fornecedor_item_desclassificacao.exercicio = cotacao_fornecedor_item.exercicio
                               AND cotacao_fornecedor_item_desclassificacao.lote = cotacao_fornecedor_item.lote
                          )

    INNER JOIN  compras.mapa_item
            ON  mapa_item.exercicio = mapa_cotacao.exercicio_mapa
           AND  mapa_item.cod_mapa = mapa_cotacao.cod_mapa
           AND  mapa_item.cod_item = cotacao_fornecedor_item.cod_item

    INNER JOIN  compras.solicitacao_item_dotacao
            ON  solicitacao_item_dotacao.exercicio = mapa_item.exercicio_solicitacao
           AND  solicitacao_item_dotacao.cod_entidade = mapa_item.cod_entidade
           AND  solicitacao_item_dotacao.cod_solicitacao = mapa_item.cod_solicitacao
           AND  solicitacao_item_dotacao.cod_centro = mapa_item.cod_centro
           AND  solicitacao_item_dotacao.cod_item = mapa_item.cod_item

    INNER JOIN  orcamento.despesa
            ON  despesa.exercicio = solicitacao_item_dotacao.exercicio
           AND  despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa

    INNER JOIN  orcamento.pao
            ON  pao.exercicio = despesa.exercicio
           AND  pao.num_pao = despesa.num_pao
    INNER JOIN  almoxarifado.catalogo_item
            ON  catalogo_item.cod_item = solicitacao_item_dotacao.cod_item

         WHERE  julgamento_item.ordem = 1
            AND compra_direta.cod_compra_direta = ".$this->getDado('cod_compra_direta')."
            AND julgamento_item.cgm_fornecedor = ".$this->getDado('cgm_fornecedor')."
            AND compra_direta.cod_modalidade = ".$this->getDado('cod_modalidade')."
            AND compra_direta.cod_entidade = ".$this->getDado('cod_entidade')."
            AND compra_direta.exercicio_entidade = '".$this->getDado('exercicio')."'";

    return $stSql;
}

function recuperaValorContrato(&$rsRecordSet, $stFiltro = "",$boTransacao = "")
{
    $stOrder = "";

    return $this->executaRecupera("montaRecuperaValorContrato",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaValorContrato()
{
    $stSql = "SELECT sum(cotacao_fornecedor_item.vl_cotacao) as valor_contrato                                                                  \n";
    $stSql.= "  FROM compras.cotacao_fornecedor_item                                                                                            \n";
    $stSql.= "      ,compras.cotacao_item                                                                                                       \n";
    $stSql.= "      ,compras.cotacao                                                                                                            \n";
    $stSql.= "      ,compras.julgamento_item                                                                                                    \n";
    $stSql.= "      ,licitacao.cotacao_licitacao                                                                                                \n";
    $stSql.= " WHERE cotacao_fornecedor_item.cod_item = cotacao_licitacao.cod_item                                                              \n";
    $stSql.= "   AND cotacao_fornecedor_item.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor                                                  \n";
    $stSql.= "   AND cotacao_fornecedor_item.cod_cotacao = cotacao_licitacao.cod_cotacao                                                        \n";
    $stSql.= "   AND cotacao_fornecedor_item.exercicio = cotacao_licitacao.exercicio_cotacao                                                    \n";
    $stSql.= "   AND cotacao_fornecedor_item.lote = cotacao_licitacao.lote                                                                      \n";
    $stSql.= "   AND cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao                                                             \n";
    $stSql.= "   AND cotacao_item.exercicio = cotacao_fornecedor_item.exercicio                                                                 \n";
    $stSql.= "   AND cotacao_item.cod_item = cotacao_fornecedor_item.cod_item                                                                   \n";
    $stSql.= "   AND julgamento_item.exercicio = cotacao_item.exercicio                                                                         \n";
    $stSql.= "   AND julgamento_item.cod_cotacao = cotacao_item.cod_cotacao                                                                     \n";
    $stSql.= "   AND julgamento_item.cod_item = cotacao_item.cod_item                                                                           \n";
    $stSql.= "   AND julgamento_item.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor                                                          \n";
    $stSql.= "   AND julgamento_item.lote = cotacao_licitacao.lote                                                                              \n";
    $stSql.= "   AND julgamento_item.ordem = 1                                                                                                  \n";

    $stSql.= "   AND cotacao_item.cod_cotacao = cotacao.cod_cotacao                                                                             \n";
    $stSql.= "   AND cotacao_item.exercicio = cotacao.exercicio                                                                                 \n";

    $stSql.= "   AND NOT EXISTS ( SELECT 1
                                    FROM compras.cotacao_fornecedor_item_desclassificacao
                                   WHERE cotacao_fornecedor_item_desclassificacao.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                                     AND cotacao_fornecedor_item_desclassificacao.cod_item = cotacao_fornecedor_item.cod_item
                                     AND cotacao_fornecedor_item_desclassificacao.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                                     AND cotacao_fornecedor_item_desclassificacao.exercicio = cotacao_fornecedor_item.exercicio
                                     AND cotacao_fornecedor_item_desclassificacao.lote = cotacao_fornecedor_item.lote
                                )";

    $stSql.="AND NOT EXISTS( SELECT 1 from compras.cotacao_anulada
                              WHERE cotacao.cod_cotacao = cotacao_anulada.cod_cotacao
                                AND cotacao.exercicio = cotacao_anulada.exercicio
                            )";

    if ($this->getDado('cod_licitacao'))
        $stSql.= "   AND cotacao_licitacao.cod_licitacao = ".$this->getDado('cod_licitacao')." \n";

    if ($this->getDado('cod_modalidade'))
        $stSql.= "   AND cotacao_licitacao.cod_modalidade = ".$this->getDado('cod_modalidade')." \n";

    if ($this->getDado('cod_entidade'))
        $stSql.= "   AND cotacao_licitacao.cod_entidade = ".$this->getDado('cod_entidade')." \n";

    if ($this->getDado('exercicio'))
        $stSql.= "   AND cotacao_licitacao.exercicio_cotacao = '".$this->getDado('exercicio')."' \n";

    if ($this->getDado('cgm_fornecedor'))
        $stSql.= "   AND cotacao_fornecedor_item.cgm_fornecedor = ".$this->getDado('cgm_fornecedor')." \n";

    return $stSql;
}

function recuperaContratoEsfinge(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratoEsfinge",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
function montaRecuperaContratoEsfinge()
{
   $stSql = "
            SELECT contrato.num_contrato
                 , contrato_licitacao.cod_licitacao
                 , objeto.descricao as desc_objeto
                 , cgm_responsavel.nom_cgm as nom_responsavel
                 , CASE WHEN sw_cgm_pessoa_fisica is null
                        THEN 2 -- juridica
                        ELSE 1 -- fisica
                   END as cod_tipo_pessoa_contratado
                 , CASE WHEN sw_cgm_pessoa_fisica is null
                        THEN sw_cgm_pessoa_juridica.cnpj
                        ELSE sw_cgm_pessoa_fisica.cpf
                   END as cod_cic_contratado
                 , cgm_contratado.nom_cgm as nom_contratado
                 , to_char(contrato.dt_assinatura,'dd/mm/yyyy') as dt_assinatura
                 , to_char(contrato.vencimento,'dd/mm/yyyy') as vencimento
                 , contrato.valor_contratado
                 , contrato.valor_garantia
              FROM licitacao.contrato

             INNER JOIN licitacao.contrato_licitacao
                ON contrato.num_contrato = contrato_licitacao.num_contrato
               AND contrato.cod_entidade = contrato_licitacao.cod_entidade
               AND contrato.exercicio = contrato_licitacao.exercicio

             INNER JOIN licitacao.licitacao
                ON licitacao.cod_licitacao = contrato_licitacao.cod_licitacao
               AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade
               AND licitacao.cod_entidade = contrato_licitacao.cod_entidade
               AND licitacao.exercicio = contrato_licitacao.exercicio_licitacao

             INNER JOIN compras.objeto
                     ON objeto.cod_objeto = licitacao.cod_objeto
             INNER JOIN sw_cgm as cgm_responsavel
                     ON cgm_responsavel.numcgm = contrato.cgm_responsavel_juridico
             INNER JOIN sw_cgm as cgm_contratado
                     ON cgm_contratado.numcgm = contrato.cgm_contratado
              LEFT JOIN sw_cgm_pessoa_fisica
                     ON sw_cgm_pessoa_fisica.numcgm = contrato.cgm_contratado
              LEFT JOIN sw_cgm_pessoa_juridica
                     ON sw_cgm_pessoa_juridica.numcgm = contrato.cgm_contratado
             WHERE contrato.dt_assinatura between to_date('".$this->getDado( 'dt_inicial')."','dd/mm/yyyy')
               AND to_date('".$this->getDado( 'dt_final')."','dd/mm/yyyy')
               AND contrato.cod_entidade in (".$this->getDado( 'cod_entidade').")
               AND contrato.exercicio = '".$this->getDado( 'exercicio')."'";

   return $stSql;
}

function recuperaNaoAnuladosContratado(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaNaoAnuladosContratado",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaNaoAnuladosContratado()
{
    $stSql = " SELECT contrato.cgm_responsavel_juridico
                    , TO_CHAR(contrato.dt_assinatura,'dd/mm/yyyy') as dt_assinatura
                    , contrato.num_contrato
                    , contrato.numero_contrato
                    , contrato.cod_entidade
                    , contrato.exercicio as exercicio_contrato
                    , contrato.valor_contratado
                    , contrato.cgm_contratado
                    , sw_cgm.nom_cgm
                    , cgm_entidade.nom_cgm AS entidade
                 FROM licitacao.contrato

           INNER JOIN sw_cgm
                   ON sw_cgm.numcgm = contrato.cgm_contratado

           INNER JOIN orcamento.entidade
                   ON contrato.cod_entidade = entidade.cod_entidade
                  AND contrato.exercicio = entidade.exercicio

           INNER JOIN sw_cgm AS cgm_entidade
                   ON entidade.numcgm = cgm_entidade.numcgm

           INNER JOIN licitacao.contrato_licitacao
                   ON contrato.num_contrato = contrato_licitacao.num_contrato
                  AND contrato.cod_entidade = contrato_licitacao.cod_entidade
                  AND contrato.exercicio = contrato_licitacao.exercicio

           INNER JOIN licitacao.licitacao
                   ON licitacao.cod_licitacao = contrato_licitacao.cod_licitacao
                  AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade
                  AND licitacao.cod_entidade = contrato_licitacao.cod_entidade
                  AND licitacao.exercicio = contrato_licitacao.exercicio_licitacao

                WHERE NOT EXISTS (SELECT 1
                                    FROM licitacao.contrato_anulado
                                   WHERE contrato_anulado.num_contrato = contrato.num_contrato
                                     AND contrato_anulado.cod_entidade = contrato.cod_entidade
                                     AND contrato_anulado.exercicio = contrato.exercicio
                                )";

    return $stSql;
}

function recuperaDadosAditivos(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosAditivos().$stFiltro.$stOrder;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosAditivos()
{
    $stSql = "SELECT contrato.cgm_responsavel_juridico
                    ,TO_CHAR(contrato.dt_assinatura,'dd/mm/yyyy') as dt_assinatura
                    ,TO_CHAR(contrato.vencimento,'dd/mm/yyyy') as vencimento
                    ,contrato.cgm_contratado
                    ,sw_cgm.nom_cgm
                    ,cgm_contratado.nom_cgm as nom_contratado
                    ,cgm_entidade.nom_cgm as nom_entidade
                    ,objeto.descricao
                    ,contrato.cod_entidade
                    ,contrato.num_contrato
                    ,contrato.numero_contrato
                    ,contrato.cod_documento
                    ,contrato.cod_tipo_documento
                    ,contrato.valor_garantia
                    ,contrato.valor_contratado
                    ,TO_CHAR( contrato.inicio_execucao,'dd/mm/yyyy') as inicio_execucao
                    ,TO_CHAR( contrato.fim_execucao   ,'dd/mm/yyyy') as fim_execucao
                    ,licitacao.cod_mapa

                FROM licitacao.contrato
                    ,licitacao.licitacao
                    ,licitacao.contrato_licitacao
                    ,compras.objeto
                    ,sw_cgm
                    ,sw_cgm as cgm_contratado
                    ,sw_cgm as cgm_entidade
                    ,orcamento.entidade

               WHERE licitacao.cod_licitacao = contrato_licitacao.cod_licitacao
                 AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade
                 AND licitacao.cod_entidade = contrato_licitacao.cod_entidade
                 AND licitacao.exercicio = contrato_licitacao.exercicio_licitacao

                 AND contrato_licitacao.cod_entidade = contrato.cod_entidade
                 AND contrato_licitacao.exercicio = contrato.exercicio
                 AND contrato_licitacao.num_contrato = contrato.num_contrato

                 AND licitacao.cod_objeto = objeto.cod_objeto
                 AND sw_cgm.numcgm = contrato.cgm_responsavel_juridico
                 AND cgm_contratado.numcgm = contrato.cgm_contratado
                 AND entidade.cod_entidade = contrato.cod_entidade
                 AND entidade.exercicio = contrato.exercicio
                 AND entidade.numcgm = cgm_entidade.numcgm
                 AND contrato.num_contrato = ".$this->getDado('num_contrato')."
                 and contrato.cod_entidade = ".$this->getDado('cod_entidade')."
                 and contrato.exercicio = '".$this->getDado('exercicio')."' \n";

    return $stSql;
}

function recuperaContratoCompraDireta(&$rsRecordSet, $stFiltro = "",$boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaContratoCompraDireta",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratoCompraDireta()
{
    $stSql =" SELECT contrato.num_contrato ,                                                         \n";
    $stSql.= "       contrato.exercicio ,                                                            \n";
    $stSql.= "       contrato.cod_entidade ,                                                         \n";
    $stSql.= "       contrato.cod_tipo_documento ,                                                   \n";
    $stSql.= "       contrato.cod_documento ,                                                        \n";
    $stSql.= "       contrato.cgm_responsavel_juridico ,                                             \n";
    $stSql.= "       contrato.cgm_contratado ,                                                       \n";
    $stSql.= "       TO_CHAR(contrato.dt_assinatura,'dd/mm/yyyy') AS dt_assinatura ,                 \n";
    $stSql.= "       TO_CHAR(contrato.vencimento,'dd/mm/yyyy') AS vencimento ,                       \n";
    $stSql.= "       contrato.valor_contratado ,                                                     \n";
    $stSql.= "       contrato.valor_garantia ,                                                       \n";
    $stSql.= "       TO_CHAR(contrato.inicio_execucao,'dd/mm/yyyy') AS inicio_execucao ,             \n";
    $stSql.= "       TO_CHAR(contrato.fim_execucao,'dd/mm/yyyy') AS fim_execucao ,                   \n";
    $stSql.= "       contrato_compra_direta.cod_compra_direta ,                                      \n";
    $stSql.= "       contrato_compra_direta.exercicio_compra_direta ,                                      \n";
    $stSql.= "       contrato_compra_direta.cod_modalidade                                           \n";
    $stSql.= "  FROM licitacao.contrato                                                              \n";
    $stSql.= "     , licitacao.contrato_compra_direta                                                \n";
    $stSql.= " WHERE contrato_compra_direta.num_contrato = contrato.num_contrato                     \n";
    $stSql.= "   AND contrato_compra_direta.cod_entidade = contrato.cod_entidade                     \n";
    $stSql.= "   AND contrato_compra_direta.exercicio = contrato.exercicio                           \n";
    $stSql.= "   AND contrato.num_contrato = ".$this->getDado('num_contrato')."                      \n";
    $stSql.= "   and contrato.cod_entidade = ".$this->getDado('cod_entidade')."                      \n";
    $stSql.= "   and contrato.exercicio = '".$this->getDado('exercicio')."'                          \n";

    return $stSql;
}

function recuperaNaoAnuladosContratadoCompraDireta(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaNaoAnuladosContratadoCompraDireta",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaNaoAnuladosContratadoCompraDireta()
{
    $stSql = "SELECT contrato.cgm_responsavel_juridico
                   , TO_CHAR(contrato.dt_assinatura,'dd/mm/yyyy') as dt_assinatura
                   , contrato.num_contrato
                   , contrato.cod_entidade
                   , contrato.exercicio as exercicio_contrato
                   , contrato.valor_contratado
                   , contrato.cgm_contratado
                   , sw_cgm.nom_cgm
                   , cgm_entidade.nom_cgm AS entidade
                FROM licitacao.contrato

               INNER JOIN sw_cgm
                       ON sw_cgm.numcgm = contrato.cgm_contratado

               INNER JOIN orcamento.entidade
                       ON contrato.cod_entidade = entidade.cod_entidade
                      AND contrato.exercicio = entidade.exercicio

               INNER JOIN sw_cgm AS cgm_entidade
                       ON entidade.numcgm = cgm_entidade.numcgm

              INNER JOIN licitacao.contrato_compra_direta
                      ON contrato.num_contrato = contrato_compra_direta.num_contrato
                     AND contrato.cod_entidade = contrato_compra_direta.cod_entidade
                     AND contrato.exercicio    = contrato_compra_direta.exercicio

              ";

    return $stSql;
}

function recuperaDadosAditivosCompraDireta(&$rsRecordSet, $stFiltro = "",$boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaDadosAditivosCompraDireta",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaDadosAditivosCompraDireta()
{
    $stSql = "SELECT contrato.cgm_responsavel_juridico                                          \n";
    $stSql.= "     , TO_CHAR(contrato.dt_assinatura,'dd/mm/yyyy') as dt_assinatura              \n";
    $stSql.= "     , TO_CHAR(contrato.vencimento,'dd/mm/yyyy') as vencimento                    \n";
    $stSql.= "     , contrato.cgm_contratado                                                    \n";
    $stSql.= "     , sw_cgm.nom_cgm                                                             \n";
    $stSql.= "     , cgm_contratado.nom_cgm as nom_contratado                                   \n";
    $stSql.= "     , cgm_entidade.nom_cgm as nom_entidade                                       \n";
    $stSql.= "     , objeto.descricao                                                           \n";
    $stSql.= "     , contrato.cod_entidade                                                      \n";
    $stSql.= "     , contrato.num_contrato                                                      \n";
    $stSql.= "     , contrato.cod_documento                                                     \n";
    $stSql.= "     , contrato.cod_tipo_documento                                                \n";
    $stSql.= "     , contrato.valor_garantia                                                    \n";
    $stSql.= "     , contrato.valor_contratado                                                  \n";
    $stSql.= "     , TO_CHAR( contrato.inicio_execucao,'dd/mm/yyyy') as inicio_execucao         \n";
    $stSql.= "     , TO_CHAR( contrato.fim_execucao   ,'dd/mm/yyyy') as fim_execucao            \n";
    $stSql.= "     , compra_direta.cod_mapa                                                     \n";
    $stSql.= "  FROM licitacao.contrato                                                         \n";
    $stSql.= "     , compras.compra_direta                                                      \n";
    $stSql.= "     , compras.objeto                                                             \n";
    $stSql.= "     , licitacao.contrato_compra_direta                                           \n";
    $stSql.= "     , sw_cgm                                                                     \n";
    $stSql.= "     , sw_cgm as cgm_contratado                                                   \n";
    $stSql.= "     , sw_cgm as cgm_entidade                                                     \n";
    $stSql.= "     , orcamento.entidade                                                         \n";
    $stSql.= " WHERE contrato.num_contrato = contrato_compra_direta.num_contrato                \n";
    $stSql.= "   AND contrato.cod_entidade = contrato_compra_direta.cod_entidade                \n";
    $stSql.= "   AND contrato.exercicio = contrato_compra_direta.exercicio                      \n";
    $stSql.= "   AND contrato_compra_direta.cod_compra_direta = compra_direta.cod_compra_direta \n";
    $stSql.= "   AND contrato_compra_direta.cod_modalidade = compra_direta.cod_modalidade       \n";
    $stSql.= "   AND contrato_compra_direta.cod_entidade = compra_direta.cod_entidade           \n";
    $stSql.= "   AND contrato_compra_direta.exercicio = compra_direta.exercicio_mapa            \n";
    $stSql.= "   AND compra_direta.cod_objeto = objeto.cod_objeto                               \n";
    $stSql.= "   AND sw_cgm.numcgm = contrato.cgm_responsavel_juridico                          \n";
    $stSql.= "   AND cgm_contratado.numcgm = contrato.cgm_contratado                            \n";
    $stSql.= "   AND entidade.cod_entidade = contrato.cod_entidade                              \n";
    $stSql.= "   AND entidade.exercicio = contrato.exercicio                                    \n";
    $stSql.= "   AND entidade.numcgm = cgm_entidade.numcgm                                      \n";
    $stSql.= "   AND contrato.num_contrato = ".$this->getDado('num_contrato')."                 \n";
    $stSql.= "   AND contrato.cod_entidade = ".$this->getDado('cod_entidade')."                 \n";
    $stSql.= "   AND contrato.exercicio = '".$this->getDado('exercicio')."'                     \n";

    return $stSql;
}

function recuperaContratoEmpenho(&$rsRecordSet, $stFiltro = "",$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaContratoEmpenho().$stFiltro;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratoEmpenho()
{
    $stSql = " SELECT contrato.num_contrato
                    , contrato.exercicio
                    , objeto.descricao
                    , to_char(contrato.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura
                 FROM licitacao.contrato
            LEFT JOIN licitacao.contrato_licitacao
                   ON (contrato.num_contrato = contrato_licitacao.num_contrato
                  AND contrato.exercicio = contrato_licitacao.exercicio
                  AND contrato.cod_entidade = contrato_licitacao.cod_entidade)

            LEFT JOIN licitacao.contrato_compra_direta
                   ON (contrato.num_contrato = contrato_compra_direta.num_contrato
                  AND contrato.exercicio = contrato_compra_direta.exercicio
                  AND contrato.cod_entidade = contrato_compra_direta.cod_entidade)

            LEFT JOIN licitacao.licitacao
                   ON (licitacao.cod_licitacao = contrato_licitacao.cod_licitacao
                  AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade
                  AND licitacao.exercicio = contrato_licitacao.exercicio_licitacao
                  AND licitacao.cod_entidade = contrato_licitacao.cod_entidade)

            LEFT JOIN compras.compra_direta
                   ON (compra_direta.cod_compra_direta = contrato_compra_direta.cod_compra_direta
                  AND compra_direta.cod_modalidade = contrato_compra_direta.cod_modalidade
                  AND compra_direta.exercicio_entidade = contrato_compra_direta.exercicio
                  AND compra_direta.cod_entidade = contrato_compra_direta.cod_entidade)

            LEFT JOIN compras.objeto
                   ON (   licitacao.cod_objeto   = objeto.cod_objeto
                       OR compra_direta.cod_objeto   = objeto.cod_objeto
                      )

            WHERE objeto.cod_objeto IS NOT NULL ";

    return $stSql;
}

}
