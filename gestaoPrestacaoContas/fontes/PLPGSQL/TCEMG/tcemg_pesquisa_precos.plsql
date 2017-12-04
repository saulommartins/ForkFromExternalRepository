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
* Script de DDL e DML
*
* $id: $
*/
   
   CREATE OR REPLACE FUNCTION TCEMG.pesquisa_precos (stExercicio VARCHAR, stCodEntidade VARCHAR, stMes INTEGER ) RETURNS SETOF RECORD AS $$
   
   DECLARE
        recRegistro RECORD;
        stSql       VARCHAR := '';
        
    BEGIN
        stSql := 'SELECT
                  mapa_item.lote::varchar || mapa_item.cod_item::varchar AS sequencial
                  
            FROM licitacao.licitacao
            
            JOIN licitacao.participante
              ON participante.cod_licitacao = licitacao.cod_licitacao
             AND participante.cod_modalidade = licitacao.cod_modalidade
             AND participante.cod_entidade = licitacao.cod_entidade
             AND participante.exercicio = licitacao.exercicio
             
            JOIN compras.fornecedor
              ON fornecedor.cgm_fornecedor = participante.cgm_fornecedor
              
            JOIN compras.nota_fiscal_fornecedor
              ON nota_fiscal_fornecedor.cgm_fornecedor = fornecedor.cgm_fornecedor
             
            JOIN compras.cotacao_fornecedor_item
              ON cotacao_fornecedor_item.cgm_fornecedor = fornecedor.cgm_fornecedor
             
            JOIN compras.nota_fiscal_fornecedor_ordem
              ON nota_fiscal_fornecedor_ordem.cgm_fornecedor = nota_fiscal_fornecedor.cgm_fornecedor
             AND nota_fiscal_fornecedor_ordem.cod_nota = nota_fiscal_fornecedor.cod_nota
             
            JOIN compras.ordem
              ON ordem.exercicio = nota_fiscal_fornecedor_ordem.exercicio
             AND ordem.cod_entidade = nota_fiscal_fornecedor_ordem.cod_entidade
             AND ordem.cod_ordem = nota_fiscal_fornecedor_ordem.cod_ordem
             AND ordem.tipo = nota_fiscal_fornecedor_ordem.tipo
             
            JOIN compras.ordem_item
              ON ordem_item.cod_entidade = ordem.cod_entidade
             AND ordem_item.cod_ordem = ordem.cod_ordem
             AND ordem_item.exercicio = ordem.exercicio
             AND ordem_item.tipo = ordem.tipo
            
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_cotacao
              ON mapa_cotacao.exercicio_mapa = mapa.exercicio
             AND mapa_cotacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.cotacao
              ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
             AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
            JOIN compras.mapa_item_dotacao
              ON mapa_item_dotacao.exercicio = mapa_item.exercicio
             AND mapa_item_dotacao.cod_entidade = mapa_item.cod_entidade
             AND mapa_item_dotacao.cod_solicitacao = mapa_item.cod_solicitacao
             AND mapa_item_dotacao.cod_mapa = mapa_item.cod_mapa
             AND mapa_item_dotacao.cod_centro = mapa_item.cod_centro
             AND mapa_item_dotacao.cod_item = mapa_item.cod_item
             AND mapa_item_dotacao.lote = mapa_item.lote
             AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
             AND mapa_item_dotacao.cod_entidade = mapa_item.cod_entidade
             
            JOIN compras.solicitacao_item_dotacao
              ON solicitacao_item_dotacao.exercicio = mapa_item_dotacao.exercicio_solicitacao
             AND solicitacao_item_dotacao.cod_entidade = mapa_item_dotacao.cod_entidade
             AND solicitacao_item_dotacao.cod_solicitacao = mapa_item_dotacao.cod_solicitacao
             AND solicitacao_item_dotacao.cod_centro = mapa_item_dotacao.cod_centro
             AND solicitacao_item_dotacao.cod_item = mapa_item_dotacao.cod_item
             AND solicitacao_item_dotacao.cod_conta = mapa_item_dotacao.cod_conta
             AND solicitacao_item_dotacao.cod_despesa = mapa_item_dotacao.cod_despesa
             
            JOIN orcamento.despesa
              ON despesa.exercicio = solicitacao_item_dotacao.exercicio
             AND despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
             
            JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro = ''tcemg_codigo_orgao_entidade_sicom''
             AND configuracao_entidade.cod_modulo = 55
             AND configuracao_entidade.exercicio = despesa.exercicio
             AND configuracao_entidade.cod_entidade = despesa.cod_entidade
              
            WHERE TO_DATE(TO_CHAR(licitacao.timestamp,''dd/mm/yyyy''), ''dd/mm/yyyy'') BETWEEN TO_DATE(''01/' || stMes || '/' || stExercicio || ''', ''dd/mm/yyyy'')
              AND last_day(TO_DATE(''' || stExercicio || '-' || stMes || '-' || '01'',''yyyy-mm-dd''))
              AND licitacao.exercicio = ''' || stExercicio || '''
              AND licitacao.cod_entidade IN (' || stCodEntidade || ')
            ';
            
    FOR recRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT recRegistro;
    END LOOP;
    
    RETURN;
    
END;
    
$$ LANGUAGE 'plpgsql';