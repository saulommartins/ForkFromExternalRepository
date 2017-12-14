
select atualizarBanco('
                        CREATE OR REPLACE FUNCTION ponto.fn_atualiza_ultimo_timestamp_conf_ponto( )
                                          RETURNS TRIGGER AS $$
                        DECLARE
                           rPontoAtual       RECORD;
                           iCodConfiguracao  INTEGER;
                           tNewTimestamp     TIMESTAMP;
                           cAux              VARCHAR;

                        BEGIN
                           IF TG_OP=''INSERT'' THEN
                              --
                              -- Define a configuracao a ser inserida
                              --
                              iCodConfiguracao  := NEW.cod_configuracao;
                              tNewTimestamp     := NEW.timestamp;

                              --
                              -- Verifica a existencia da ultima configuracao
                              --
                              SELECT configuracao_relogio_ponto.*
                                INTO rPontoAtual
                                FROM ponto.configuracao_relogio_ponto
                               WHERE configuracao_relogio_ponto.cod_configuracao = iCodConfiguracao;

                              IF FOUND THEN
                                 tNewTimestamp := (''now''::text)::TIMESTAMP(3) WITH TIME ZONE ;
                                 If COALESCE(rPontoAtual.ultimo_timestamp, ''1800-01-01'') <= tNewTimestamp  THEN
                                    UPDATE ponto.configuracao_relogio_ponto
                                       SET ultimo_timestamp = tNewTimestamp
                                     WHERE cod_configuracao = iCodConfiguracao;
                                 ELSE
                                    cAux := TO_CHAR(iCodConfiguracao,''9999'');
                                    RAISE EXCEPTION ''Tabela ponto.configuracao_relogio_ponto inconsistente, contate suporte. Configuração:%'', cAux;
                                 END IF;
                              ELSE
                                 RAISE EXCEPTION ''Falha de integridade referencial, tabela ponto.configuracao_relogio_ponto.: %'', TG_OP;
                                 RAISE EXCEPTION ''Código configuração: %'', iCodConfiguracao;
                              END IF;
                           ELSE
                              RAISE EXCEPTION ''Operação não permitida para tabela ponto.configuracao_relogio_ponto.: %'', TG_OP;
                           END IF;

                           RETURN NEW;

                        END;
                        $$ LANGUAGE plpgsql;
');
