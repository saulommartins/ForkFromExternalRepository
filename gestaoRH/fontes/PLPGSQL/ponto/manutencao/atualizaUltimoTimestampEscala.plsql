
select atualizarBanco('
                        CREATE OR REPLACE FUNCTION ponto.fn_atualiza_ultimo_timestamp_escala( )
                           RETURNS TRIGGER AS $$
                        DECLARE
                           rEscalaAtual      RECORD;
                           iCodEscala        INTEGER;
                           tNewTimestamp     TIMESTAMP;
                           cAux              VARCHAR;

                        BEGIN

                           IF TG_OP=''INSERT'' THEN
                              --
                              -- Define a escala a ser inserida
                              --
                              iCodEscala      := NEW.cod_escala;
                              tNewTimestamp   := NEW.timestamp;

                              --
                              -- Verifica a existencia da ultima escala
                              --
                              SELECT escala.*
                                INTO rEscalaAtual
                                FROM ponto.escala
                               WHERE escala.cod_escala = iCodEscala
                              ;
                              IF FOUND THEN
                                 tNewTimestamp := (''now''::TEXT)::TIMESTAMP(3) WITH TIME ZONE;
                                 IF COALESCE(rEscalaAtual.ultimo_timestamp, ''1800-01-01'') <= tNewTimestamp  THEN
                                    UPDATE ponto.escala
                                       SET ultimo_timestamp =  tNewTimestamp
                                     WHERE cod_escala       = iCodEscala
                                    ;
                                 ELSE
                                    cAux := TO_CHAR(iCodEscala,''9999'');
                                    RAISE EXCEPTION ''Tabela ponto.escala inconsistente, contate suporte. Escala:%'', cAux;
                                 END IF;
                              ELSE
                                 RAISE EXCEPTION ''Falha de integridade referencial, tabela ponto.escala.: %'', TG_OP;
                                 RAISE EXCEPTION ''Código escala: %'', iCodEscala;
                              END IF;
                           ELSE
                              RAISE EXCEPTION ''Operação não permitida para tabela ponto.escala.: %'', TG_OP;
                           END IF;

                           RETURN NEW;
                        END;
                        $$ LANGUAGE plpgsql;
');

