CREATE OR REPLACE FUNCTION empenho.busca_data_liquidacao( inCodNota       INTEGER
                                                        , inCodEntidade   INTEGER
                                                        , stExercicio     CHAR(4)
                                                        ) RETURNS         DATE AS $$
DECLARE
    dtRetorno   DATE;
BEGIN
    SELECT to_char(MIN(nota_liquidacao_paga.timestamp), 'yyyy-mm-dd') AS data_liquidacao
      INTO dtRetorno
      FROM empenho.nota_liquidacao_paga
     WHERE nota_liquidacao_paga.cod_nota     = inCodNota
       AND nota_liquidacao_paga.cod_entidade = inCodEntidade
       AND nota_liquidacao_paga.exercicio    = stExercicio
         ;

    RETURN dtRetorno;
END;
$$ LANGUAGE 'plpgsql';
