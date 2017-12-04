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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson  W
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso:

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.contrato_compra_direta
  * Data de Criação: 03/10/2008

  * @author Analista: Gelson W
  * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasContratoCompraDireta extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function TComprasContratoCompraDireta()
    {
        parent::Persistente();
        $this->setTabela("licitacao.contrato_compra_direta");

        $this->setCampoCod('num_contrato');
        $this->setComplementoChave('cod_entidade,exercicio,cod_compra_direta,cod_modalidade');

        $this->AddCampo( 'num_contrato'			,'sequence' 	,true	, ''	,true	,false  );
        $this->AddCampo( 'cod_entidade'       		,'integer'	,true	, '' 	,true  	,true	);
        $this->AddCampo( 'exercicio'		    	,'char'		,true	, '4' 	,true  	,true	);
        $this->AddCampo( 'cod_compra_direta'   		,'integer'	,true	, '' 	,true  	,true	);
        $this->AddCampo( 'cod_modalidade'       	,'integer'	,true	, '' 	,false 	,true 	);
        $this->AddCampo( 'exercicio_compra_direta'      ,'integer'	,true	, '' 	,false 	,true 	);
    }

    public function recuperaValorContrato(&$rsRecordSet, $stFiltro = "",$boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaValorContrato",$rsRecordSet,$stFiltro,"",$boTransacao);
    }

    public function montaRecuperaValorContrato()
    {
        $stSql= "SELECT sum(cotacao_fornecedor_item.vl_cotacao) as valor_contrato                                                                       \n";
        $stSql.="  FROM compras.compra_direta                                                                                                           \n";

        $stSql.=" INNER JOIN compras.mapa                                                                                                               \n";
        $stSql.="         ON compra_direta.cod_mapa = mapa.cod_mapa                                                                                     \n";
        $stSql.="        AND compra_direta.exercicio_mapa = mapa.exercicio                                                                              \n";

        $stSql.=" INNER JOIN compras.mapa_cotacao                                                                                                       \n";
        $stSql.="         ON mapa.cod_mapa = mapa_cotacao.cod_mapa                                                                                      \n";
        $stSql.="        AND mapa.exercicio = mapa_cotacao.exercicio_mapa                                                                               \n";

        $stSql.="  INNER JOIN compras.cotacao                                                                                                           \n";
        $stSql.="          ON mapa_cotacao.cod_cotacao = cotacao.cod_cotacao                                                                            \n";
        $stSql.="          AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio                                                                       \n";
        $stSql.="          AND NOT EXISTS( SELECT 1 from compras.cotacao_anulada                                                                        \n";
        $stSql.="                           WHERE cotacao.cod_cotacao = cotacao_anulada.cod_cotacao                                                     \n";
        $stSql.="                             AND cotacao.exercicio = cotacao_anulada.exercicio                                                         \n";
        $stSql.="                        )                                                                                                              \n";

        $stSql.="  INNER JOIN compras.julgamento                                                                                                        \n";
        $stSql.="          ON cotacao.cod_cotacao = julgamento.cod_cotacao                                                                              \n";
        $stSql.="         AND cotacao.exercicio = julgamento.exercicio                                                                                  \n";

        $stSql.="  INNER JOIN compras.julgamento_item                                                                                                   \n";
        $stSql.="          ON julgamento.cod_cotacao = julgamento_item.cod_cotacao                                                                      \n";
        $stSql.="         AND julgamento.exercicio = julgamento_item.exercicio                                                                          \n";

        $stSql.="  INNER JOIN compras.cotacao_fornecedor_item                                                                                           \n";
        $stSql.="          ON julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao                                                         \n";
        $stSql.="         AND julgamento_item.exercicio = cotacao_fornecedor_item.exercicio                                                             \n";
        $stSql.="         AND julgamento_item.cod_item = cotacao_fornecedor_item.cod_item                                                               \n";
        $stSql.="         AND julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor                                                   \n";
        $stSql.="         AND julgamento_item.lote = cotacao_fornecedor_item.lote                                                                       \n";
        $stSql.="         AND NOT EXISTS ( SELECT 1                                                                                                     \n";
        $stSql.="                            FROM compras.cotacao_fornecedor_item_desclassificacao                                                      \n";
        $stSql.="                           WHERE cotacao_fornecedor_item_desclassificacao.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor      \n";
        $stSql.="                             AND cotacao_fornecedor_item_desclassificacao.cod_item = cotacao_fornecedor_item.cod_item                  \n";
        $stSql.="                             AND cotacao_fornecedor_item_desclassificacao.cod_cotacao = cotacao_fornecedor_item.cod_cotacao            \n";
        $stSql.="                             AND cotacao_fornecedor_item_desclassificacao.exercicio = cotacao_fornecedor_item.exercicio                \n";
        $stSql.="                             AND cotacao_fornecedor_item_desclassificacao.lote = cotacao_fornecedor_item.lote                          \n";
        $stSql.="                        )                                                                                                              \n";

        $stSql.="  INNER JOIN compras.cotacao_item                                                                                                      \n";
        $stSql.="          ON cotacao_fornecedor_item.cod_cotacao = cotacao_item.cod_cotacao                                                            \n";
        $stSql.="         AND cotacao_fornecedor_item.exercicio = cotacao_item.exercicio                                                                \n";
        $stSql.="         AND cotacao_fornecedor_item.cod_item = cotacao_item.cod_item                                                                  \n";
        $stSql.="         AND cotacao_fornecedor_item.lote = cotacao_item.lote                                                                          \n";

        if ($this->getDado('cod_compra_direta'))
            $stSql.= "   AND compra_direta.cod_compra_direta = ".$this->getDado('cod_compra_direta')."                                                  \n";

        if ($this->getDado('cod_modalidade'))
            $stSql.= "   AND compra_direta.cod_modalidade = ".$this->getDado('cod_modalidade')."                                                        \n";

        if ($this->getDado('cod_entidade'))
            $stSql.= "   AND compra_direta.cod_entidade = ".$this->getDado('cod_entidade')."                                                            \n";

        if ($this->getDado('exercicio'))
            $stSql.= " AND compra_direta.exercicio_entidade = '".$this->getDado('exercicio')."'                                                      \n";

        if ($this->getDado('cgm_fornecedor'))
            $stSql.= "   AND cotacao_fornecedor_item.cgm_fornecedor = ".$this->getDado('cgm_fornecedor')."                                              \n";

        if ($this->getDado('ordem'))
            $stSql.= "   AND julgamento_item.ordem = ".$this->getDado('ordem')."                                                                        \n";

        return $stSql;
    }

    public function recuperaContratosCompraDireta(&$rsRecordSet, $stFiltro = "",$boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaContratosCompraDireta",$rsRecordSet,$stFiltro,"",$boTransacao);
    }

    public function montaRecuperaContratosCompraDireta()
    {
        $stSql = "SELECT contrato.num_contrato
                       , contrato.numero_contrato
                       , contrato.exercicio as exercicio_contrato
                       , contrato.cod_entidade
                       , contrato.valor_contratado
                       , contrato.cod_tipo_documento
                       , contrato.cod_documento
                       , contrato.cgm_responsavel_juridico
                       , contrato.cgm_contratado
                       , to_char(contrato.dt_assinatura,'dd/mm/yyyy') as dt_assinatura
                       , to_char(contrato.vencimento,'dd/mm/yyyy') as vencimento
                       , contrato.valor_contratado
                       , contrato.valor_garantia
                       , to_char(contrato.inicio_execucao,'dd/mm/yyyy') as inicio_execucao
                       , to_char(contrato.fim_execucao,'dd/mm/yyyy') as fim_execucao
                       , sw_cgm.nom_cgm as entidade
                       , to_char(contrato.dt_assinatura,'dd/mm/yyyy') as dt_assinatura
                       , contrato_compra_direta.cod_modalidade
                       , contrato_compra_direta.cod_compra_direta
                       , tipo_objeto.descricao
                       , (SELECT nom_cgm from sw_cgm where numcgm = contrato.cgm_contratado) as nom_contratado
                       , (SELECT nom_cgm from sw_cgm where numcgm = contrato.cgm_responsavel_juridico) as responsavel_juridico
                       , contrato_compra_direta.exercicio_compra_direta
                       , (SELECT descricao FROM licitacao.tipo_contrato where cod_tipo = contrato.cod_tipo_contrato) AS tipo_descricao

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
                       , entidade_contrato.nom_cgm AS nom_entidade
                       , contrato.cod_garantia
                       , contrato.multa_inadimplemento
                       , contrato.cod_tipo_instrumento
                       , contrato.cgm_representante_legal 
               
                    FROM licitacao.contrato_compra_direta
                       , orcamento.entidade
                       , sw_cgm
                       , compras.compra_direta
                       , compras.tipo_objeto
                       , licitacao.contrato
                       
               LEFT JOIN sw_cgm as cgm_signatario                                            
                      ON cgm_signatario.numcgm = contrato.cgm_signatario
                
              INNER JOIN ( SELECT sw_cgm.nom_cgm
                                , entidade.cod_entidade
                                , entidade.exercicio
                             FROM orcamento.entidade
                       INNER JOIN sw_cgm
                               ON sw_cgm.numcgm    = entidade.numcgm
                         ) AS entidade_contrato
                       ON entidade_contrato.cod_entidade = contrato.cod_entidade
                      AND entidade_contrato.exercicio    = contrato.exercicio
                         
               LEFT JOIN orcamento.orgao
                      ON orgao.num_orgao = contrato.num_orgao
                     AND orgao.exercicio = contrato.exercicio
            
               LEFT JOIN orcamento.unidade
                      ON unidade.num_unidade = contrato.num_unidade
                     AND unidade.num_orgao   = contrato.num_orgao
                     AND unidade.exercicio   = contrato.exercicio
                     
                   WHERE contrato.num_contrato = contrato_compra_direta.num_contrato
                     AND contrato.exercicio = contrato_compra_direta.exercicio
                     AND contrato.cod_entidade = contrato_compra_direta.cod_entidade
                     AND contrato.cod_entidade = entidade.cod_entidade
                     AND contrato.exercicio = entidade.exercicio
                     AND entidade.numcgm = sw_cgm.numcgm
                     AND compra_direta.cod_compra_direta = contrato_compra_direta.cod_compra_direta
                     AND compra_direta.cod_modalidade = contrato_compra_direta.cod_modalidade
                     AND compra_direta.cod_entidade = contrato_compra_direta.cod_entidade
                     AND compra_direta.cod_entidade = contrato_compra_direta.cod_entidade
                     AND compra_direta.exercicio_entidade = contrato_compra_direta.exercicio_compra_direta
                     AND compra_direta.cod_tipo_objeto = tipo_objeto.cod_tipo_objeto";

        return $stSql;
    }
}
