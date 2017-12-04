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
* $Revision: 25648 $
* $Name$
* $Author: gris $
* $Date: 2007-09-26 11:50:48 -0300 (Qua, 26 Set 2007) $
*
* Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
   varAchou    VARCHAR;

BEGIN

   SELECT proname INTO varAchou
   FROM pg_proc
   where proname ilike 'fn_exportacao_pagamento';

   IF FOUND THEN

      DROP FUNCTION tcern.fn_exportacao_pagamento(VARCHAR,VARCHAR,VARCHAR,VARCHAR);

   END IF;

END;

$$ LANGUAGE 'plpgsql';

SELECT manutencao();

DROP FUNCTION manutencao();


CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$

DECLARE

   varAchou    VARCHAR;

BEGIN

   SELECT typname INTO varAchou
     FROM pg_type
    where typname ilike 'colunasfnexportacaopagamento';

   IF FOUND THEN
      drop type colunasFnExportacaoPagamento cascade;
   END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();

DROP FUNCTION manutencao();


CREATE TYPE colunasFnExportacaoPagamento AS (
    cod_entidade       integer
   ,cod_empenho        integer
   ,data_pagamento     varchar
   ,num_serie          varchar
   ,num_nota           varchar
   ,data_nota          varchar
   ,vl_pago            numeric
   ,cpf_cnpj           varchar
   ,conta_corrente     varchar
   ,cod_validacao      varchar
   ,modelo             varchar
   ,ordem_bancaria     varchar    
);

CREATE OR REPLACE FUNCTION tcern.fn_exportacao_pagamento(varchar,varchar,varchar,varchar) RETURNS SETOF
colunasFnExportacaoPagamento AS $$
DECLARE
    stExercicio         ALIAS FOR $1        ;
    inCodEntidade       ALIAS FOR $2        ;
    stDataInicial       ALIAS FOR $3        ;
    stDataFinal         ALIAS FOR $4        ;
    stSql               VARCHAR   := ''   ;
    reRegistro          RECORD              ;    
    
    rwExportacaoPagamento            colunasFnExportacaoPagamento%ROWTYPE;
BEGIN
    stSql := '
       CREATE TEMPORARY TABLE tmp_conta AS
        SELECT   CP.exercicio_liquidacao
                ,CP.cod_entidade
                ,CP.cod_nota
                ,CP.timestamp
                ,CPB.conta_corrente
        FROM     contabilidade.pagamento             AS CP
                ,contabilidade.lancamento_empenho    AS CLE
                ,contabilidade.lancamento            AS CL
        
                ,contabilidade.valor_lancamento      AS CVL
                ,contabilidade.conta_credito          AS CCC
                ,contabilidade.plano_analitica       AS CPA
                ,contabilidade.plano_banco           AS CPB
        WHERE  CP.exercicio_liquidacao = '|| quote_literal(stExercicio) ||'
           AND CP.cod_entidade IN ('||inCodEntidade||')
           AND CP.exercicio                = CLE.exercicio
           AND CP.cod_lote                 = CLE.cod_lote
           AND CP.tipo                     = CLE.tipo
           AND CP.sequencia                = CLE.sequencia
           AND CP.cod_entidade             = CLE.cod_entidade
        
           AND CLE.exercicio               = CL.exercicio
           AND CLE.cod_lote                = CL.cod_lote
           AND CLE.tipo                    = CL.tipo
           AND CLE.sequencia               = CL.sequencia
           AND CLE.cod_entidade            = CL.cod_entidade
           --AND CLE.tipo                    = ''P''
        
           AND CL.exercicio                = CVL.exercicio
           AND CL.cod_lote                 = CVL.cod_lote
           --AND CL.tipo                     = ''P''
           AND CL.tipo                     = CVL.tipo
           AND CL.sequencia                = CVL.sequencia
           AND CL.cod_entidade             = CVL.cod_entidade
        
           AND CVL.exercicio               = CCC.exercicio
           AND CVL.cod_lote                = CCC.cod_lote
           AND CVL.tipo                    = CCC.tipo
           AND CVL.sequencia               = CCC.sequencia
           AND CVL.cod_entidade            = CCC.cod_entidade
           AND CVL.tipo_valor              = CCC.tipo_valor
           AND CVL.tipo_valor              = ''C''
        
           AND CCC.exercicio               = CPA.exercicio
           AND CCC.cod_plano               = CPA.cod_plano
        
           AND CPA.exercicio               = CPB.exercicio
           AND CPA.cod_plano               = CPB.cod_plano
        
        GROUP BY CP.exercicio_liquidacao
                ,CP.cod_entidade
                ,CP.cod_nota
                ,CP.timestamp
                ,CPB.conta_corrente
        
        ORDER BY CP.exercicio_liquidacao
                ,CP.cod_entidade
                ,CP.cod_nota
                ,CP.timestamp
                ,CPB.conta_corrente
    ';

    EXECUTE stSql;
    CREATE UNIQUE INDEX unq_tmp_conta     ON tmp_conta  (exercicio_liquidacao, cod_entidade, cod_nota, timestamp);

    stSql := '
      SELECT   nliq.cod_entidade
              ,nliq.cod_empenho	
              ,to_char(nlpa.timestamp,''dd/mm/yyyy'')::varchar as data_pagamento
              --,nofi.num_serie
              --,nofi.num_nota
              --,to_char(nofi.dt_nota,''dd/mm/yyyy'')::varchar as data_nota
              ,nlpa.vl_pago
              ,case when  pf.cpf is not null  then pf.cpf
                    when pj.cnpj is not null  then pj.cnpj
                     else ''''
              end as cpf_cnpj
              ,REPLACE(tmp.conta_corrente, ''-'', '''')::varchar AS conta_corrente
              , nlpa.observacao::varchar AS ordem_bancaria
              /*, REPLACE(lancamento.complemento, ''- '', '''')::varchar AS ordem_bancaria*/
              ,nliq.cod_nota
              
              , nota_fiscal.nro_serie AS num_serie
              , nota_fiscal.nro_nota AS num_nota
              , to_char(nota_fiscal.data_emissao,''dd/mm/yyyy'')::varchar as data_nota
              , nota_fiscal.cod_validacao AS cod_validacao
              , nota_fiscal.modelo AS modelo

      FROM  empenho.nota_liquidacao_paga   as nlpa
            inner JOIN tmp_conta as tmp
                    ON (    nlpa.exercicio      = tmp.exercicio_liquidacao
                        AND nlpa.cod_entidade   = tmp.cod_entidade
                        AND nlpa.cod_nota       = tmp.cod_nota
                        AND nlpa.timestamp      = tmp.timestamp
                        )
                        
            LEFT JOIN empenho.nota_liquidacao_paga_anulada AS nlpan
               ON (     nlpan.exercicio = nlpa.exercicio
                    AND nlpan.cod_entidade = nlpa.cod_entidade
                    AND nlpan.cod_nota = nlpa.cod_nota
                    AND nlpan.timestamp = nlpa.timestamp )
            
            , empenho.nota_liquidacao        as nliq
            
            LEFT JOIN tcern.nota_fiscal
                   ON nota_fiscal.exercicio = nliq.exercicio
                  AND nota_fiscal.cod_entidade = nliq.cod_entidade
                  AND nota_fiscal.cod_nota_liquidacao = nliq.cod_nota

	 LEFT JOIN empenho.atributo_liquidacao_valor as serie
	       on(     serie.cod_atributo = 2001
		   AND nliq.cod_nota  = serie.cod_nota		   
		   AND nliq.cod_entidade  = serie.cod_entidade
		   AND nliq.exercicio =  serie.exercicio) 

	 LEFT JOIN empenho.atributo_liquidacao_valor as numeroNota
	       on(     numeroNota.cod_atributo = 2002
		   AND nliq.cod_nota  = numeroNota.cod_nota
		   AND nliq.cod_entidade  = numeroNota.cod_entidade
		   AND nliq.exercicio =  numeroNota.exercicio) 

	LEFT JOIN empenho.atributo_liquidacao_valor as dataNota
	       on(     dataNota.cod_atributo = 2003
		   AND nliq.cod_nota  = dataNota.cod_nota
		   AND nliq.cod_entidade  = dataNota.cod_entidade
		   AND nliq.exercicio =  dataNota.exercicio)
                   
             /*JOIN contabilidade.liquidacao
               ON liquidacao.exercicio = nliq.exercicio
              AND liquidacao.cod_entidade = nliq.cod_entidade
              AND liquidacao.cod_nota = nliq.cod_nota
             JOIN contabilidade.lancamento
               ON lancamento.exercicio = liquidacao.exercicio
              AND lancamento.cod_entidade = liquidacao.cod_entidade
              AND lancamento.tipo = liquidacao.tipo
              AND lancamento.cod_lote = liquidacao.cod_lote
              AND lancamento.sequencia = liquidacao.sequencia*/
              
              ,empenho.pre_empenho            as pree
              LEFT JOIN   sw_cgm_pessoa_fisica as pf
                  ON ( pree.cgm_beneficiario = pf.numcgm )
              LEFT JOIN   sw_cgm_pessoa_juridica as pj
                  ON ( pree.cgm_beneficiario = pj.numcgm )
              ,empenho.empenho                as empe
              LEFT JOIN   compras.ordem as orco
              ON (orco.exercicio_empenho  = empe.exercicio
              AND orco.cod_entidade       = empe.cod_entidade
              AND orco.cod_empenho        = empe.cod_empenho
              )
              LEFT JOIN   compras.nota_fiscal_fornecedor_ordem as nofio
              ON (nofio.exercicio    = orco.exercicio
              AND nofio.cod_entidade = orco.cod_entidade
              AND nofio.tipo         = orco.tipo
              AND nofio.cod_ordem    = orco.cod_ordem
              )
              LEFT JOIN   compras.nota_fiscal_fornecedor as nofi
              ON (nofi.cgm_fornecedor = nofio.cgm_fornecedor
              AND nofi.cod_nota       = nofio.cod_nota
              )
     WHERE  empe.exercicio = '|| quote_literal(stExercicio) ||'
      AND   nlpa.cod_entidade IN ('||inCodEntidade||')
      AND   to_date(to_char(nlpa.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') 
                                                                               AND to_date('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
      AND (
            nlpan.timestamp_anulada IS NULL
        OR  to_date(to_char(nlpan.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') NOT BETWEEN to_date('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') 
                                                                                    AND to_date('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
      )
      
      AND   nlpa.exercicio          = nliq.exercicio
      AND   nlpa.cod_entidade       = nliq.cod_entidade
      AND   nlpa.cod_nota           = nliq.cod_nota
      AND   nliq.exercicio_empenho  = empe.exercicio
      AND   nliq.cod_entidade       = empe.cod_entidade
      AND   nliq.cod_empenho        = empe.cod_empenho
      AND   empe.exercicio          = pree.exercicio
      AND   empe.cod_pre_empenho    = pree.cod_pre_empenho      
      
      ORDER BY nliq.cod_entidade
              ,nliq.cod_empenho
              ,to_char(nlpa.timestamp,''dd/mm/yyyy'')
              ,nota_fiscal.nro_serie
              ,nota_fiscal.nro_nota
              --,nofi.num_serie
              --,nofi.num_nota
    ';

    FOR reRegistro IN EXECUTE stSql LOOP
  
        rwExportacaoPagamento.cod_entidade := reRegistro.cod_entidade;
        rwExportacaoPagamento.cod_empenho := reRegistro.cod_empenho;
        rwExportacaoPagamento.data_pagamento := reRegistro.data_pagamento;        
        rwExportacaoPagamento.vl_pago := reRegistro.vl_pago;
        rwExportacaoPagamento.cpf_cnpj := reRegistro.cpf_cnpj;        
        rwExportacaoPagamento.conta_corrente := reRegistro.conta_corrente;
        rwExportacaoPagamento.num_serie := reRegistro.num_serie;
        rwExportacaoPagamento.num_nota :=  reRegistro.num_nota;
        rwExportacaoPagamento.data_nota :=  reRegistro.data_nota;
        rwExportacaoPagamento.cod_validacao :=  reRegistro.cod_validacao;
        rwExportacaoPagamento.modelo :=  reRegistro.modelo;
        rwExportacaoPagamento.ordem_bancaria := reRegistro.ordem_bancaria; 
	    
        RETURN NEXT rwExportacaoPagamento;
    END LOOP;

    DROP INDEX unq_tmp_conta;

    DROP TABLE tmp_conta;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';
