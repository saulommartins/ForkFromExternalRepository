-- Function: pega1valorplanosaude()

-- DROP FUNCTION pega1valorplanosaude();

CREATE OR REPLACE FUNCTION pega1ValorPlanoSaude()
  RETURNS numeric AS
$BODY$

DECLARE
    inCodContrato              INTEGER;
    inCodEvento                INTEGER;
    inCodPeriodoMovimentacao   INTEGER;
    nuValor                    NUMERIC := 0.00;    
    stEntidade                 VARCHAR;
 BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    inCodContrato := recuperarBufferInteiro('inCodContrato');
    inCodEvento := recuperarBufferInteiroPilha('inCodEvento');
    inCodPeriodoMovimentacao := recuperarBufferInteiro('inCodPeriodoMovimentacao');
       
    nuValor := selectIntoNumeric('SELECT sum(beneficiario.valor) 
                                    FROM beneficio'||stEntidade||'.beneficiario
                                      INNER JOIN (SELECT cod_contrato,
                                                 cgm_fornecedor,
                                                 max(timestamp) as timestamp,
                                                 cgm_beneficiario,
                                                 cod_periodo_movimentacao
                                            FROM beneficio'||stEntidade||'.beneficiario               
                                               GROUP BY cod_contrato,
                                                 cgm_fornecedor,                    
                                                 cgm_beneficiario,
                                                 cod_periodo_movimentacao ) as max_beneficiario
                                          ON max_beneficiario.cod_contrato = beneficiario.cod_contrato
                                         AND max_beneficiario.cgm_fornecedor = beneficiario.cgm_fornecedor
                                         AND max_beneficiario.timestamp = beneficiario.timestamp
                                         AND max_beneficiario.cgm_beneficiario = beneficiario.cgm_beneficiario
                                         AND max_beneficiario.cod_periodo_movimentacao = beneficiario.cod_periodo_movimentacao                               
                                   
                                       INNER JOIN  (SELECT evento.cod_evento,
                                                           fornec.cgm_fornecedor
                                                      FROM folhapagamento'||stEntidade||'.configuracao_beneficio_fornecedor as fornec
                                                         , folhapagamento'||stEntidade||'.beneficio_evento as evento 
                                                            INNER JOIN (SELECT cod_configuracao,
                                                                       cod_tipo,
                                                                       cod_evento,
                                                                       max(timestamp) as timestamp
                                                                      FROM folhapagamento'||stEntidade||'.beneficio_evento
                                                                      GROUP BY cod_configuracao, cod_tipo, cod_evento ) max_evento
                                                                    ON max_evento.cod_configuracao = evento.cod_configuracao
                                                                       AND max_evento.cod_tipo = evento.cod_tipo
                                                                       AND max_evento.cod_evento = evento.cod_evento
                                                                       AND max_evento.timestamp = evento.timestamp
                                                                          
                                                              WHERE fornec.cod_configuracao = evento.cod_configuracao
                                                                AND fornec.timestamp = evento.timestamp
                                                                AND evento.cod_tipo=2 )  as evento_fornecedor
                                                    ON evento_fornecedor.cgm_fornecedor = beneficiario.cgm_fornecedor

                                        INNER JOIN folhapagamento'||stEntidade||'.periodo_movimentacao
                                            ON periodo_movimentacao.cod_periodo_movimentacao = beneficiario.cod_periodo_movimentacao
                                                
                                      WHERE beneficiario.dt_inicio <= periodo_movimentacao.dt_final 
                                        AND (beneficiario.dt_fim >= periodo_movimentacao.dt_inicial OR beneficiario.dt_fim IS NULL)
                                        AND beneficiario.timestamp_excluido IS NULL   
                                        AND beneficiario.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                        AND beneficiario.cod_contrato = '||inCodContrato||'
                                        AND evento_fornecedor.cod_evento = '||inCodEvento )
          ;
    IF nuValor IS NULL THEN
       nuValor := 0;
    END IF;
    
    RETURN nuValor;
END;
$BODY$
  LANGUAGE 'plpgsql';

