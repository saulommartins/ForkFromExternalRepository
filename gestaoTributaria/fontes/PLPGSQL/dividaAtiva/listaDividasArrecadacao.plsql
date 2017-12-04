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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: listaDividasArrecadacao.plsql 61352 2015-01-09 18:14:18Z evandro $
*
* Caso de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.9  2007/09/21 14:55:50  cercato
correcao para trazer lancamentos estornados.

Revision 1.8  2007/07/27 18:15:48  cercato
correcao para mostrar grupo de credito correto.

Revision 1.7  2007/06/11 13:39:41  cercato
alteracao para aceitar qualquer parcela desde que esteja vencida.

Revision 1.6  2007/03/26 19:02:46  cercato
correcao para consulta por cgm

Revision 1.5  2007/02/23 18:48:05  cercato
alteracoes em funcao das mudancas no ER.

Revision 1.4  2006/10/06 16:55:26  dibueno
Alterações para buscar ultimo timestamp venal da inscricao municipal

Revision 1.3  2006/10/05 11:35:29  dibueno
busca do vencimento em formato BR

Revision 1.2  2006/10/03 17:53:13  dibueno
retirada a lista de parcelas

Revision 1.1  2006/09/29 11:14:56  dibueno
*** empty log message ***

*/

CREATE OR REPLACE FUNCTION divida.fn_lista_divida_arrecadacao ( inExercicio     INTEGER
                                                                , inCodGrupo      INTEGER
                                                                , inCodCredito    INTEGER
                                                                , inCodEspecie    INTEGER
                                                                , inCodGenero     INTEGER
                                                                , inCodNatureza   INTEGER
                                                                , inNumCgmInicial INTEGER
                                                                , inNumCgmFinal   BIGINT
                                                                , inCodIIInicial  INTEGER
                                                                , inCodIIFinal    INTEGER
                                                                , inCodIEInicial  INTEGER
                                                                , inCodIEFinal    INTEGER
                                                                , dtDataInicial   VARCHAR
                                                                , dtDataFinal     VARCHAR
                                                                , flValorInicial  NUMERIC
                                                                , flValorFinal    NUMERIC
                                                                , stExercicio     VARCHAR
                                                                ) RETURNS         SETOF RECORD AS $$
DECLARE
    stNumeracaoAnt  varchar;
    inRetorno       integer;
    dtDI            date := NULL;
    dtDF            date := NULL;
    reRegistro      RECORD;
    stSql           VARCHAR;
    stSelectImovel  VARCHAR;
    stGroupByImovel VARCHAR;
    stFiltro        varchar := '';
    stFiltro2       varchar := '';
    stJoins         varchar := '';
    stFrom          varchar := '';
    exercicioAux   varchar := stExercicio;
BEGIN
/**
* FUNCIONAMENTO
*   Antes de executar a consulta, é verificado todos os filtros, aonde a tabela de maior proximidade
*   com o filtro mais exclusivo torna-se a tabela-mãe.
*/


/* CREDITO */
IF ( inCodCredito > 0 ) THEN
    stFrom := '
                                                    FROM arrecadacao.calculo
                                              INNER JOIN arrecadacao.calculo_cgm
                                                      ON calculo_cgm.cod_calculo = calculo.cod_calculo
                                              INNER JOIN arrecadacao.lancamento_calculo
                                                      ON lancamento_calculo.cod_calculo = calculo.cod_calculo
                                              INNER JOIN arrecadacao.lancamento
                                                      ON lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
                                                     AND lancamento.divida         = FALSE
                                                     AND lancamento.ativo          = TRUE
              ';
    stFiltro := stFiltro ||'
                                                     AND credito.cod_credito  = '|| inCodCredito::varchar  ||'
                                                     AND credito.cod_especie  = '|| inCodEspecie::varchar  ||'
                                                     AND credito.cod_genero   = '|| inCodGenero::varchar   ||'
                                                     AND credito.cod_natureza = '|| inCodNatureza::varchar ||'
                                                     AND calculo_grupo_credito.cod_calculo IS NULL
                           ';

END IF;

/* CGM */
IF ( inNumCgmInicial > 0 ) THEN
    stFrom := '
                                                    FROM arrecadacao.calculo_cgm
                                              INNER JOIN arrecadacao.calculo
                                                      ON calculo.cod_calculo = calculo_cgm.cod_calculo
                                               LEFT JOIN arrecadacao.calculo_grupo_credito
                                                      ON calculo_grupo_credito.cod_calculo   = calculo.cod_calculo
                                                     AND calculo_grupo_credito.ano_exercicio = calculo.exercicio::varchar
                                              INNER JOIN arrecadacao.lancamento_calculo
                                                      ON lancamento_calculo.cod_calculo = calculo.cod_calculo
                                              INNER JOIN arrecadacao.lancamento
                                                      ON lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
                                                     AND lancamento.divida         = FALSE
                                                     AND lancamento.ativo          = TRUE
              ';
    --Validacoes para os campos de CGM
    IF ( inNumCgmFinal > 0) THEN
        stFiltro := stFiltro ||'
                                                     AND calculo_cgm.numcgm BETWEEN '|| inNumCgmInicial::varchar ||' AND '|| inNumCgmFinal::varchar ||'
                           ';
    ELSE
        stFiltro := stFiltro ||'
                                                     AND calculo_cgm.numcgm = '|| inNumCgmInicial::varchar ||'
                           ';
    END IF; 
    

END IF;


IF ( inCodGrupo > 0 ) THEN
    stFrom := '
                                                    FROM arrecadacao.calculo_grupo_credito
                                              INNER JOIN arrecadacao.calculo
                                                      ON calculo.cod_calculo = calculo_grupo_credito.cod_calculo
                                              INNER JOIN arrecadacao.calculo_cgm
                                                      ON calculo_cgm.cod_calculo = calculo.cod_calculo
                                              INNER JOIN arrecadacao.lancamento_calculo
                                                      ON lancamento_calculo.cod_calculo = calculo.cod_calculo
                                              INNER JOIN arrecadacao.lancamento
                                                      ON lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
                                                     AND lancamento.divida         = FALSE
                                                     AND lancamento.ativo          = TRUE
              ';
    stFiltro := stFiltro ||'
                                                     AND calculo_grupo_credito.cod_grupo = '|| inCodGrupo::varchar ||'
                           ';

    IF ( inExercicio > 0 ) THEN
        stFiltro := stFiltro||'
                                                     AND calculo_grupo_credito.ano_exercicio = '|| inExercicio ||'::varchar
                              ';
    END IF;
    IF ( exercicioAux != '' ) THEN
        IF ( inExercicio = 0 ) THEN
            stFiltro := stFiltro||'
                                                     AND calculo.exercicio = '|| stExercicio ||'::varchar
                                  ';
        END IF;
    END IF;
END IF;


stSelectImovel  := '
                                                       , now() AS timestamp_venal
                   ';
stGroupByImovel := ' ';

IF ( inCodIIInicial > 0 ) THEN

    stSelectImovel  := '
                                                       , arrecadacao.buscaUltimoTimestampVenal( imovel_calculo.inscricao_municipal ) AS timestamp_venal
                       ';
    stGroupByImovel := '
                                                       , imovel_calculo.inscricao_municipal
                       ';

    stFrom := '
                                                    FROM arrecadacao.imovel_calculo
                                              INNER JOIN arrecadacao.calculo
                                                      ON calculo.cod_calculo = imovel_calculo.cod_calculo
                                               LEFT JOIN arrecadacao.calculo_grupo_credito
                                                      ON calculo_grupo_credito.cod_calculo = calculo.cod_calculo
                                              INNER JOIN arrecadacao.calculo_cgm
                                                      ON calculo_cgm.cod_calculo = calculo.cod_calculo
                                              INNER JOIN arrecadacao.lancamento_calculo
                                                      ON lancamento_calculo.cod_calculo = calculo.cod_calculo
                                              INNER JOIN arrecadacao.lancamento
                                                      ON lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
                                                     AND lancamento.divida         = FALSE
                                                     AND lancamento.ativo          = TRUE
              ';
  IF ( exercicioAux != '' ) THEN
        IF ( inExercicio = 0 ) THEN
            stFiltro := stFiltro ||'
                                                     AND calculo.exercicio = '|| stExercicio ||'::varchar
                                   ';
        END IF;
  END IF;
  IF ( inCodIIFinal > 0) THEN
      stFiltro := stFiltro ||'
                                                     AND imovel_calculo.inscricao_municipal BETWEEN '|| inCodIIInicial::varchar ||' AND '|| inCodIIFinal::varchar ||'
                             ';
  ELSE
      stFiltro := stFiltro ||'
                                                     AND imovel_calculo.inscricao_municipal = '|| inCodIIInicial::varchar ||'
                             ';
  END IF;
END IF;

IF ( inCodIEInicial > 0 ) THEN
    stFrom := '
                                                    FROM arrecadacao.cadastro_economico_calculo
                                              INNER JOIN arrecadacao.calculo
                                                      ON calculo.cod_calculo = cadastro_economico_calculo.cod_calculo
                                              INNER JOIN arrecadacao.calculo_cgm
                                                      ON calculo_cgm.cod_calculo = calculo.cod_calculo
                                               LEFT JOIN arrecadacao.calculo_grupo_credito
                                                      ON calculo_grupo_credito.cod_calculo = calculo.cod_calculo
                                              INNER JOIN arrecadacao.lancamento_calculo
                                                      ON lancamento_calculo.cod_calculo = calculo.cod_calculo
                                              INNER JOIN arrecadacao.lancamento
                                                      ON lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
                                                     AND lancamento.divida         = FALSE
                                                     AND lancamento.ativo          = TRUE
              ';
  IF ( exercicioAux != '' ) THEN
        IF ( inExercicio = 0 ) THEN
            stFiltro := stFiltro ||'
                                                     AND calculo.exercicio = '|| stExercicio ||'::varchar
                                   ';
        END IF;
  END IF;
  IF ( inCodIEFinal > 0) THEN
      stFiltro := stFiltro ||'
                                                     AND cadastro_economico_calculo.inscricao_economica BETWEEN '|| inCodIEInicial::varchar ||' AND '|| inCodIEFinal::varchar ||'
                             ';
  ELSE
      stFiltro := stFiltro ||'
                                                     AND cadastro_economico_calculo.inscricao_economica = '|| inCodIEInicial::varchar ||'
                             ';
  END IF;
END IF;

IF (stNumeracaoAnt != '') THEN
    stFrom := '
                                                    FROM arrecadacao.calculo_cgm
                                              INNER JOIN arrecadacao.calculo
                                                      ON calculo.cod_calculo = calculo_cgm.cod_calculo
                                              INNER JOIN arrecadacao.lancamento_calculo
                                                      ON lancamento_calculo.cod_calculo = calculo.cod_calculo
                                               LEFT JOIN arrecadacao.calculo_grupo_credito
                                                      ON calculo_grupo_credito.cod_calculo = calculo.cod_calculo
                                              INNER JOIN arrecadacao.lancamento
                                                      ON lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
                                                     AND lancamento.divida         = FALSE
                                                     AND lancamento.ativo          = TRUE
              ';
      stFiltro := stFiltro ||'
                                                     AND carne.numeracao = '|| stNumeracaoAnt::varchar ||'
                             ';
      IF ( exercicioAux != '' ) THEN
        IF ( inExercicio = 0 ) THEN
            stFiltro := stFiltro ||'
                                                     AND calculo.exercicio = '|| stExercicio ||'::varchar
                                   ';
        END IF;
      END IF;
END IF;


IF ( DtDataInicial != '' ) THEN
    stFiltro2 := stFiltro2 ||'
                               AND tabela2.vencimento_base > '|| dtDataInicial ||'
                             ';
END IF;
IF ( DtDataFinal != '' ) THEN
    stFiltro2 := stFiltro2 ||'
                               AND tabela2.vencimento_base < '|| dtDataFinal ||'
                             ';
END IF;

IF ( FlValorInicial > 0 ) THEN
    stFiltro2 := stFiltro2 ||'
                               AND tabela2.valor_aberto > '|| flValorInicial ||'
                             ';
END IF;
IF ( FlValorFinal > 0 ) THEN
    stFiltro2 := stFiltro2 ||'
                               AND tabela2.valor_aberto < '|| flValorFinal ||'
                             ';
END IF;



    stSql := '
                            SELECT *
                              FROM (
                                     SELECT ( SELECT divida.fn_calcula_valor_divida_lancamento ( tabela.cod_lancamento, tabela.vencimento_base))::NUMERIC AS valor_aberto
                                          , tabela.valor_lancamento::NUMERIC AS lancamento
                                          , tabela.cod_lancamento
                                          , tabela.numcgm
                                          , tabela.nom_cgm
                                          , tabela.vinculo
                                          , tabela.id_vinculo
                                          , tabela.inscricao
                                          , tabela.tipo_inscricao
                                          , tabela.vencimento_base
                                          , TO_CHAR(tabela.vencimento_base, ''dd/mm/YYYY'')::VARCHAR AS vencimento_base_br
                                          , tabela.timestamp_venal::TIMESTAMP
                                          , divida.fn_recupera_nro_parcelas_divida_lancamento( tabela.cod_lancamento)   AS nro_parcelas
                                          , arrecadacao.fn_busca_lancamento_situacao( tabela.cod_lancamento )           AS situacao_lancamento
                                       FROM (
                                                  SELECT lancamento.valor                                                                                                AS valor_lancamento
                                                       , lancamento.cod_lancamento
                                                       , calculo_cgm.numcgm
                                                       , sw_cgm.nom_cgm
                                                       , arrecadacao.buscaVinculoLancamento ( lancamento.cod_lancamento, calculo.exercicio::INTEGER )::VARCHAR           AS vinculo
                                                       , arrecadacao.buscaIdVinculo(lancamento.cod_lancamento)::VARCHAR                                                  AS id_vinculo
                                                       , arrecadacao.buscaInscricaoLancamento ( lancamento.cod_lancamento )::INTEGER                                     AS inscricao
                                                       , ( SELECT arrecadacao.buscaTipoInscricaoLancamento( lancamento.cod_lancamento ) )                                AS tipo_inscricao
                                                       , ( SELECT arrecadacao.fn_busca_vencimento_base_lancamento( lancamento.cod_lancamento, carne.exercicio ))::DATE   AS vencimento_base
                                                         '|| stSelectImovel ||'
                                                         '|| stFrom         ||'
                                              INNER JOIN arrecadacao.parcela
                                                      ON parcela.cod_lancamento = lancamento.cod_lancamento
                                                     AND parcela.nr_parcela    != 0
                                              INNER JOIN arrecadacao.carne
                                                      ON carne.cod_parcela = parcela.cod_parcela
                                               LEFT JOIN arrecadacao.carne_devolucao
                                                      ON carne_devolucao.numeracao    = carne.numeracao
                                                     AND carne_devolucao.cod_convenio = carne.cod_convenio
                                                     AND carne_devolucao.cod_motivo  != 10
                                               LEFT JOIN arrecadacao.pagamento
                                                      ON pagamento.numeracao    = carne.numeracao
                                                     AND pagamento.cod_convenio = carne.cod_convenio
                                              INNER JOIN monetario.credito
                                                      ON credito.cod_credito  = calculo.cod_credito
                                                     AND credito.cod_especie  = calculo.cod_especie
                                                     AND credito.cod_genero   = calculo.cod_genero
                                                     AND credito.cod_natureza = calculo.cod_natureza
                                               LEFT JOIN monetario.carteira
                                                      ON carteira.cod_convenio = credito.cod_convenio
                                              INNER JOIN sw_cgm
                                                      ON sw_cgm.numcgm = calculo_cgm.numcgm
                                                   WHERE parcela.cod_parcela NOT IN (
                                                                                          SELECT parcela_origem.cod_parcela
                                                                                            FROM divida.parcela_origem
                                                                                      INNER JOIN divida.divida_parcelamento
                                                                                              ON divida_parcelamento.num_parcelamento = parcela_origem.num_parcelamento
                                                                                       LEFT JOIN divida.divida_estorno
                                                                                              ON divida_estorno.cod_inscricao = divida_parcelamento.cod_inscricao
                                                                                             AND divida_estorno.exercicio     = divida_parcelamento.exercicio
                                                                                           WHERE divida.parcela_origem.cod_parcela = parcela.cod_parcela
                                                                                             AND divida_estorno.cod_inscricao IS NULL
                                                                                             AND divida_estorno.exercicio     IS NULL
                                                                                    )
                                                     AND carne_devolucao.numeracao IS NULL
                                                     AND pagamento.numeracao is null
                                                     AND now()::timestamp > parcela.vencimento::timestamp
             ';

    IF ( stNumeracaoAnt = '' ) THEN
          stSql := stSql||'
                                                     AND carne.numeracao = (
                                                                               SELECT ultima_numeracao.numeracao
                                                                                 FROM arrecadacao.carne AS ultima_numeracao
                                                                                WHERE ultima_numeracao.cod_parcela = parcela.cod_parcela
                                                                             ORDER BY timestamp DESC LIMIT 1
                                                                           )
                          ';
    END IF;

    stSql := stSql ||' '|| stFiltro;

/* GROUP BY */
    stSql := stSql||'
                                                GROUP BY lancamento.cod_lancamento
                                                       , lancamento.valor
                                                       , calculo_cgm.numcgm
                                                       , sw_cgm.nom_cgm
                                                       , calculo.exercicio
                                                       , carne.exercicio
                                                         '|| stGroupByImovel ||'
                    ';


/* ORDER BY */
    stSql := stSql ||'
                                                ORDER BY lancamento.cod_lancamento DESC
                                            ) AS tabela
                                   ) AS tabela2
                             WHERE tabela2.lancamento           > 0
                               AND tabela2.valor_aberto         > 0
                               AND tabela2.situacao_lancamento != ''Anulação''
                               AND tabela2.situacao_lancamento != ''Quitado''
                     ';

    IF ( stFiltro2 != '' ) THEN
        stSql := stSql ||' '|| stFiltro2;
    END IF;

    stSql := stSql ||';';

    FOR reRegistro IN EXECUTE stSql LOOP
        RETURN NEXT reRegistro;
    END LOOP;
    RETURN;
END;
$$ LANGUAGE 'plpgsql';

