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
/*
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 63750 $
* $Id: fn_soma_valor_item_servico.plsql 63750 2015-10-05 18:50:17Z carlos.silva $
* $Author: carlos.silva $
* $Date: 2015-10-05 15:50:17 -0300 (Mon, 05 Oct 2015) $
*
*/

CREATE OR REPLACE FUNCTION patrimonio.fn_soma_valor_item_servico(VARCHAR, INTEGER) RETURNS NUMERIC AS $$
DECLARE
    stExercicio  ALIAS FOR $1;
    inCodMapa    ALIAS FOR $2;
    vl_total     NUMERIC := 0;
    stSQL        VARCHAR := '';
BEGIN
    stSQL := '  SELECT COALESCE(SUM(cotacao_fornecedor_item.vl_cotacao), 0.00)
                  FROM compras.cotacao_item
            INNER JOIN (
		    SELECT adjudicacao.num_adjudicacao
			, adjudicacao.timestamp
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

		    FROM licitacao.adjudicacao
		    
	       LEFT JOIN licitacao.adjudicacao_anulada
		      ON adjudicacao_anulada.num_adjudicacao     = adjudicacao.num_adjudicacao
		     AND adjudicacao_anulada.cod_entidade        = adjudicacao.cod_entidade
		     AND adjudicacao_anulada.cod_modalidade      = adjudicacao.cod_modalidade
		     AND adjudicacao_anulada.cod_licitacao       = adjudicacao.cod_licitacao
		     AND adjudicacao_anulada.exercicio_licitacao = adjudicacao.exercicio_licitacao
		     AND adjudicacao_anulada.cod_item            = adjudicacao.cod_item
		     AND adjudicacao_anulada.cgm_fornecedor      = adjudicacao.cgm_fornecedor
		     AND adjudicacao_anulada.cod_cotacao         = adjudicacao.cod_cotacao
		     AND adjudicacao_anulada.lote                = adjudicacao.lote
		     AND adjudicacao_anulada.exercicio_cotacao   = adjudicacao.exercicio_cotacao
			
		   WHERE adjudicacao_anulada.num_adjudicacao IS NULL

	     ) AS adjudicacao
                      
	      ON cotacao_item.exercicio   = adjudicacao.exercicio_cotacao
	     AND cotacao_item.cod_cotacao = adjudicacao.cod_cotacao
	     AND cotacao_item.lote        = adjudicacao.lote
	     AND cotacao_item.cod_item    = adjudicacao.cod_item
	  
	    JOIN almoxarifado.catalogo_item
	      ON cotacao_item.cod_item = catalogo_item.cod_item

	    JOIN almoxarifado.tipo_item
	      ON tipo_item.cod_tipo = catalogo_item.cod_tipo

	    JOIN compras.cotacao_fornecedor_item
	      ON   cotacao_fornecedor_item.exercicio   = cotacao_item.exercicio
	     AND   cotacao_fornecedor_item.cod_cotacao = cotacao_item.cod_cotacao
	     AND   cotacao_fornecedor_item.cod_item    = cotacao_item.cod_item
	     AND   cotacao_fornecedor_item.lote        = cotacao_item.lote

	    JOIN   compras.mapa_cotacao
	      ON   cotacao_item.cod_cotacao = mapa_cotacao.cod_cotacao
	     AND   cotacao_item.exercicio   = mapa_cotacao .exercicio_cotacao

	    JOIN   compras.julgamento_item
	      ON   cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
	     AND   cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
	     AND   cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
	     AND   cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
	     AND   cotacao_fornecedor_item.lote           = julgamento_item.lote

	    JOIN  compras.mapa
	      ON  mapa_cotacao.cod_mapa       = mapa.cod_mapa
	     AND  mapa_cotacao.exercicio_mapa = mapa.exercicio

	    JOIN  compras.mapa_item
	      ON  mapa_item.exercicio = mapa.exercicio
	     AND  mapa_item.cod_mapa  = mapa.cod_mapa
	     AND  mapa_item.cod_item  = cotacao_fornecedor_item.cod_item
	     AND  mapa_item.lote      = cotacao_fornecedor_item.lote

	    JOIN  compras.mapa_solicitacao
	      ON  mapa_solicitacao.exercicio             = mapa_item.exercicio
	     AND  mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
	     AND  mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
	     AND  mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
	     AND  mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
	 
	   WHERE mapa_cotacao.exercicio_mapa = '''||stExercicio||'''
	     AND mapa_cotacao.cod_mapa       = '||inCodMapa||'
	     AND julgamento_item.ordem       = 1 
	     AND tipo_item.cod_tipo          = 3';

	EXECUTE stSQL INTO vl_total;

	RETURN vl_total;
END;

$$ LANGUAGE 'plpgsql';