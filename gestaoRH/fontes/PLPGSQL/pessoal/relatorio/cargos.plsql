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
--/**
--    * PLPGSQL para retorno dos dados do relatorio de cargos
--    * Data de Criação: 15/04/2009
--    
--
--    * @author Diego Lemos de Souza
--       
--    * Casos de uso: uc-04.04.11
--           
--    $Id: $
--*/


CREATE OR REPLACE FUNCTION recuperaRelatorioCargos(INTEGER,VARCHAR,VARCHAR,VARCHAR,VARCHAR,BOOLEAN) RETURNS SETOF linhaRelatorioCargos AS $$
DECLARE
    inExercicio                     ALIAS FOR $1;
    stEntidade                      ALIAS FOR $2;
    stTipoFiltro                    ALIAS FOR $3;
    stCodigos                       ALIAS FOR $4;
    stOrdenacao                     ALIAS FOR $5;
    boAgrupar                       ALIAS FOR $6;    
    stSql                           VARCHAR;
    stSqlAgrupar                    VARCHAR;
    stSqlDetalhesCargos             VARCHAR;
    stSqlJoinFuncaoCargo            VARCHAR;
    stSqlJoinFuncaoCargoFiltro      VARCHAR;
    stSqlGroupBy                    VARCHAR;
    reRegistro                      RECORD;
    rwRelatorioCargos               linhaRelatorioCargos%ROWTYPE;
    inCodPeriodoMovimentacao        INTEGER;
    stDataFinalPeriodoMovimentacao  VARCHAR;
BEGIN

    stSql := '  SELECT periodo_movimentacao.cod_periodo_movimentacao
                     , periodo_movimentacao.dt_final
                  FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                     , folhapagamento'|| stEntidade ||'.periodo_movimentacao_situacao
                     , (SELECT max(timestamp) as timestamp
                          FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao_situacao
                         WHERE situacao = ''a'') as max_timestamp
                 WHERE periodo_movimentacao.cod_periodo_movimentacao = periodo_movimentacao_situacao.cod_periodo_movimentacao 
                   AND periodo_movimentacao_situacao.timestamp       = max_timestamp.timestamp
                   AND periodo_movimentacao_situacao.situacao        = ''a'' ';
                   
    FOR reRegistro IN EXECUTE stSql 
    LOOP
        inCodPeriodoMovimentacao        := reRegistro.cod_periodo_movimentacao;
        stDataFinalPeriodoMovimentacao  := reRegistro.dt_final;
    END LOOP;
    
    stSqlDetalhesCargos := 'SELECT cargo.cod_cargo
                                 , trim(cargo.descricao) as descricao
                                 , cargo.cargo_cc
                                 , cargo.funcao_gratificada
                                 , cbo.codigo as codigo_cbo
                                 , false as especialidade
                                 , null as cod_especialidade
                                 , padrao.cod_padrao
                                 , trim(padrao.descricao) as descricao_padrao
                                 , padrao.horas_mensais
                                 , padrao.horas_semanais
                                 , to_real(padrao_padrao.valor) as valor
                                 , to_char(padrao_padrao.vigencia, ''dd/mm/yyyy'') as vigencia
                              FROM pessoal'|| stEntidade ||'.cargo
                        INNER JOIN (SELECT cbo_cargo.cod_cargo
                                         , cbo_cargo.cod_cbo
                                      FROM pessoal'|| stEntidade ||'.cbo_cargo
                                         , (  SELECT cod_cargo
                                                   , max(timestamp) as timestamp
                                                FROM pessoal'|| stEntidade ||'.cbo_cargo
                                            GROUP BY cod_cargo) as max_cbo_cargo
                                     WHERE max_cbo_cargo.cod_cargo = cbo_cargo.cod_cargo
                                       AND max_cbo_cargo.timestamp = cbo_cargo.timestamp
                                   ) AS cbo_cargo 
                                ON cbo_cargo.cod_cargo = cargo.cod_cargo
                        INNER JOIN pessoal'|| stEntidade ||'.cbo
                                ON cbo.cod_cbo = cbo_cargo.cod_cbo
                        INNER JOIN (SELECT cargo_padrao.cod_padrao
                                         , cargo_padrao.cod_cargo
                                      FROM pessoal'|| stEntidade ||'.cargo_padrao
                                         , (  SELECT cod_cargo
                                                   , max(timestamp) as timestamp
                                                FROM pessoal'|| stEntidade ||'.cargo_padrao
                                            GROUP BY cod_cargo) as max_cargo_padrao
                                     WHERE max_cargo_padrao.cod_cargo = cargo_padrao.cod_cargo
                                       AND max_cargo_padrao.timestamp = cargo_padrao.timestamp
                                   ) AS cargo_padrao
                                ON cargo_padrao.cod_cargo = cargo.cod_cargo
                        INNER JOIN folhapagamento'|| stEntidade ||'.padrao
                                ON cargo_padrao.cod_padrao = padrao.cod_padrao
                        INNER JOIN (SELECT padrao_padrao.cod_padrao
                                         , padrao_padrao.valor
                                         , padrao_padrao.vigencia
                                      FROM folhapagamento'|| stEntidade ||'.padrao_padrao
                                         , (  SELECT cod_padrao
                                                   , max(timestamp) as timestamp
                                                FROM folhapagamento'|| stEntidade ||'.padrao_padrao
                                               WHERE to_char(vigencia, ''yyyy-mm-dd'') <= '|| quote_literal(stDataFinalPeriodoMovimentacao) ||'
                                            GROUP BY cod_padrao) as max_padrao_padrao
                                     WHERE max_padrao_padrao.cod_padrao = padrao_padrao.cod_padrao
                                       AND max_padrao_padrao.timestamp = padrao_padrao.timestamp
                                   ) as padrao_padrao
                                ON padrao_padrao.cod_padrao = padrao.cod_padrao
    
                            UNION
                    
                            SELECT especialidade.cod_cargo
                                 , (SELECT trim(descricao) FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = especialidade.cod_cargo)||'' / ''|| especialidade.descricao as descricao
                                 , (SELECT cargo_cc FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = especialidade.cod_cargo) as cargo_cc
                                 , (SELECT funcao_gratificada FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = especialidade.cod_cargo) as funcao_gratificada
                                 , cbo.codigo
                                 , true as especialidade
                                 , especialidade.cod_especialidade as cod_especialidade
                                 , padrao.cod_padrao
                                 , trim(padrao.descricao) as descricao_padrao
                                 , padrao.horas_mensais
                                 , padrao.horas_semanais
                                 , to_real(padrao_padrao.valor) as valor
                                 , to_char(padrao_padrao.vigencia, ''dd/mm/yyyy'') as vigencia
                              FROM pessoal'|| stEntidade ||'.especialidade
                        INNER JOIN (SELECT cbo_especialidade.cod_especialidade
                                         , cbo_especialidade.cod_cbo
                                      FROM pessoal'|| stEntidade ||'.cbo_especialidade
                                         , (  SELECT cod_especialidade
                                                   , max(timestamp) as timestamp
                                                FROM pessoal'|| stEntidade ||'.cbo_especialidade
                                            GROUP BY cod_especialidade) as max_cbo_especialidade
                                     WHERE max_cbo_especialidade.cod_especialidade = cbo_especialidade.cod_especialidade
                                       AND max_cbo_especialidade.timestamp = cbo_especialidade.timestamp
                                   ) AS cbo_especialidade
                                ON cbo_especialidade.cod_especialidade = especialidade.cod_especialidade
                        INNER JOIN pessoal'|| stEntidade ||'.cbo
                                ON cbo.cod_cbo = cbo_especialidade.cod_cbo           
                        INNER JOIN (SELECT especialidade_padrao.cod_padrao
                                         , especialidade_padrao.cod_especialidade
                                      FROM pessoal'|| stEntidade ||'.especialidade_padrao
                                         , (  SELECT cod_especialidade
                                                   , max(timestamp) as timestamp
                                                FROM pessoal'|| stEntidade ||'.especialidade_padrao
                                            GROUP BY cod_especialidade) as max_especialidade_padrao
                                     WHERE max_especialidade_padrao.cod_especialidade = especialidade_padrao.cod_especialidade
                                       AND max_especialidade_padrao.timestamp = especialidade_padrao.timestamp
                                   ) AS especialidade_padrao
                                ON especialidade_padrao.cod_especialidade = especialidade.cod_especialidade
                        INNER JOIN folhapagamento'|| stEntidade ||'.padrao
                                ON especialidade_padrao.cod_padrao = padrao.cod_padrao
                        INNER JOIN (SELECT padrao_padrao.cod_padrao
                                         , padrao_padrao.valor
                                         , padrao_padrao.vigencia
                                      FROM folhapagamento'|| stEntidade ||'.padrao_padrao
                                         , (  SELECT cod_padrao
                                                   , max(timestamp) as timestamp
                                                FROM folhapagamento'|| stEntidade ||'.padrao_padrao
                                               WHERE to_char(vigencia, ''yyyy-mm-dd'') <= '|| quote_literal(stDataFinalPeriodoMovimentacao) ||'
                                            GROUP BY cod_padrao) as max_padrao_padrao
                                     WHERE max_padrao_padrao.cod_padrao = padrao_padrao.cod_padrao
                                       AND max_padrao_padrao.timestamp = padrao_padrao.timestamp
                                   ) as padrao_padrao
                                ON padrao_padrao.cod_padrao = padrao.cod_padrao';    
                                
                                
    IF trim(stTipoFiltro) = '' THEN
    
    	stSql := 'SELECT NULL as agrupamento
                       , NULL as count_servidores
                       , NULL as cod_local
                       , NULL as cod_orgao
                       , NULL as cod_sub_divisao
                       , cargo_detalhes.cod_cargo
                       , cargo_detalhes.cod_especialidade
                       , cargo_detalhes.descricao as descricao_cargo
                       , cargo_detalhes.codigo_cbo
                       , cargo_detalhes.cargo_cc
                       , cargo_detalhes.funcao_gratificada
                       , cargo_detalhes.cod_padrao
                       , cargo_detalhes.descricao_padrao
                       , cargo_detalhes.horas_mensais
                       , cargo_detalhes.horas_semanais
                       , cargo_detalhes.valor
                       , cargo_detalhes.vigencia
    	            FROM ('|| stSqlDetalhesCargos ||') as cargo_detalhes';
    	          
    ELSE
    
        IF stTipoFiltro = 'reg_sub_car_esp_grupo' THEN
            stSqlJoinFuncaoCargo       := ', cod_cargo, cod_especialidade_cargo as cod_especialidade, cod_regime, cod_sub_divisao, desc_regime, desc_sub_divisao';
            stSqlJoinFuncaoCargoFiltro := ' AND (cargo_detalhes.cod_especialidade = cadastro.cod_especialidade OR (cadastro.cod_especialidade is null)) ';
        ELSE
            stSqlJoinFuncaoCargo       := ', cod_funcao as cod_cargo, cod_especialidade_funcao as cod_especialidade, cod_regime_funcao as cod_regime, cod_sub_divisao_funcao as cod_sub_divisao, desc_regime_funcao as desc_regime, desc_sub_divisao_funcao as desc_sub_divisao';
            stSqlJoinFuncaoCargoFiltro := ' AND (cargo_detalhes.cod_especialidade = cadastro.cod_especialidade OR (cadastro.cod_especialidade is null)) ';
        END IF;
    
        stSql := '    SELECT NULL as agrupamento
                           , NULL as cod_orgao
                           , NULL as cod_local
                           , NULL as cod_sub_divisao
                           , count(cadastro.cod_contrato) as count_servidores
                           , cargo_detalhes.cod_cargo
                           , cargo_detalhes.cod_especialidade
                           , cargo_detalhes.descricao as descricao_cargo
                           , cargo_detalhes.codigo_cbo
                           , cargo_detalhes.cargo_cc
                           , cargo_detalhes.funcao_gratificada
                           , cargo_detalhes.cod_padrao
                           , cargo_detalhes.descricao_padrao
                           , cargo_detalhes.horas_mensais
                           , cargo_detalhes.horas_semanais
                           , cargo_detalhes.valor
                           , cargo_detalhes.vigencia
                        FROM (
                                SELECT cod_contrato, cod_orgao, orgao, desc_orgao, cod_local, desc_local, valor_atributo '|| stSqlJoinFuncaoCargo ||' FROM recuperarContratoServidor(''l,o,oo,f,rf,sf,ef,ec,ca,car,cas'','|| quote_literal(stEntidade) ||',0,'|| quote_literal(stTipoFiltro) ||','|| quote_literal(stCodigos) ||','|| quote_literal(inExercicio) ||')
                             ) as cadastro
                  INNER JOIN (
	                           '|| stSqlDetalhesCargos ||'
	                         ) as cargo_detalhes
	                      ON cargo_detalhes.cod_cargo = cadastro.cod_cargo
                       WHERE recuperarSituacaoDoContrato(cadastro.cod_contrato,0,'|| quote_literal(stEntidade) ||') IN (''A'')'
                            ||stSqlJoinFuncaoCargoFiltro ||'
                    GROUP BY agrupamento
                           , cargo_detalhes.cod_cargo
                           , cargo_detalhes.cod_especialidade
                           , cargo_detalhes.descricao
                           , cargo_detalhes.codigo_cbo
                           , cargo_detalhes.cargo_cc
                           , cargo_detalhes.funcao_gratificada
                           , cargo_detalhes.cod_padrao
                           , cargo_detalhes.descricao_padrao
                           , cargo_detalhes.horas_mensais
                           , cargo_detalhes.horas_semanais
                           , cargo_detalhes.valor
                           , cargo_detalhes.vigencia';
                            
        IF boAgrupar IS TRUE THEN
            IF stTipoFiltro = 'reg_sub_car_esp_grupo' THEN
            
                stSql := replace(stSql, 'NULL as agrupamento', 'cargo_detalhes.cod_cargo||'' - ''|| cargo_detalhes.descricao as agrupamento');
                stSql := replace(stSql, 'NULL as cod_sub_divisao', 'max(cadastro.cod_sub_divisao) as cod_sub_divisao');
                
            ELSIF stTipoFiltro = 'reg_sub_fun_esp_grupo' THEN
            
                stSql := replace(stSql, 'NULL as agrupamento', 'cargo_detalhes.cod_cargo||'' - ''|| cargo_detalhes.descricao as agrupamento');
                stSql := replace(stSql, 'NULL as cod_sub_divisao', 'max(cadastro.cod_sub_divisao) as cod_sub_divisao');
                
            ELSIF stTipoFiltro = 'sub_divisao_grupo' THEN
            
                stSql := replace(stSql, 'NULL as agrupamento', 'cadastro.desc_regime||'' / ''|| cadastro.desc_sub_divisao as agrupamento');
                stSql := replace(stSql, 'NULL as cod_sub_divisao', 'max(cadastro.cod_sub_divisao) as cod_sub_divisao');
                
            ELSIF stTipoFiltro = 'lotacao_grupo' THEN
            
                stSql := replace(stSql, 'NULL as agrupamento', 'cadastro.orgao||'' - ''|| cadastro.desc_orgao as agrupamento');
                stSql := replace(stSql, 'NULL as cod_orgao', 'max(cadastro.cod_orgao) as cod_orgao');
                
            ELSIF stTipoFiltro = 'local_grupo' THEN
            
                stSql := replace(stSql, 'NULL as agrupamento', 'cadastro.cod_local||'' - ''|| cadastro.desc_local as agrupamento');
                stSql := replace(stSql, 'NULL as cod_local', 'max(cadastro.cod_local) as cod_local');
                
            ELSIF stTipoFiltro = 'atributo_servidor_grupo' THEN
            
                stSql := replace(stSql, 'NULL as agrupamento', 'cadastro.valor_atributo as agrupamento');
                
            END IF;
        END IF;
        
    END IF; 
    
    IF stOrdenacao = '3' THEN
    	stSql := stSql || ' ORDER BY cargo_detalhes.codigo_cbo ';
    ELSIF stOrdenacao = '1' THEN
    	stSql := stSql || ' ORDER BY cargo_detalhes.cod_cargo ';
    ELSE
    	stSql := stSql || ' ORDER BY cargo_detalhes.descricao ';
    END IF;
    

    
    FOR reRegistro IN EXECUTE stSql 
    LOOP
        rwRelatorioCargos.agrupamento        := reRegistro.agrupamento;
        rwRelatorioCargos.count_servidores   := reRegistro.count_servidores;
        rwRelatorioCargos.cod_local          := reRegistro.cod_local;
        rwRelatorioCargos.cod_orgao          := reRegistro.cod_orgao;
        rwRelatorioCargos.cod_sub_divisao    := reRegistro.cod_sub_divisao;
        rwRelatorioCargos.cod_cargo          := reRegistro.cod_cargo;
        rwRelatorioCargos.cod_especialidade  := reRegistro.cod_especialidade;
        rwRelatorioCargos.descricao_cargo    := reRegistro.descricao_cargo;
        rwRelatorioCargos.codigo_cbo         := reRegistro.codigo_cbo;
        rwRelatorioCargos.cargo_cc           := reRegistro.cargo_cc;
        rwRelatorioCargos.funcao_gratificada := reRegistro.funcao_gratificada;
        rwRelatorioCargos.cod_padrao         := reRegistro.cod_padrao;
        rwRelatorioCargos.descricao_padrao   := reRegistro.descricao_padrao;
        rwRelatorioCargos.horas_mensais      := reRegistro.horas_mensais;
        rwRelatorioCargos.horas_semanais     := reRegistro.horas_semanais;
        rwRelatorioCargos.valor              := reRegistro.valor;
        rwRelatorioCargos.vigencia           := reRegistro.vigencia;
        
        RETURN NEXT rwRelatorioCargos;
    END LOOP;

END;
$$ LANGUAGE 'plpgsql';




CREATE OR REPLACE FUNCTION recuperaRelatorioCargosServidores(INTEGER,VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF linhaRelatorioCargosServidores AS $$
DECLARE
    inExercicio                     ALIAS FOR $1;
    stEntidade                      ALIAS FOR $2;
    stTipoFiltro                    ALIAS FOR $3;
    stCodigos                       ALIAS FOR $4;
    stCodCargo                      ALIAS FOR $5;
    stCodEspecialidade              ALIAS FOR $6;
    stSql                           VARCHAR;
    stSqlAgrupar                    VARCHAR;
    stSqlDetalhesCargos             VARCHAR;
    stSqlJoinFuncaoCargo            VARCHAR;
    stSqlJoinFuncaoCargoFiltro      VARCHAR;
    stSqlGroupBy                    VARCHAR;
    reRegistro                      RECORD;
    rwRelatorioCargosServidores     linhaRelatorioCargosServidores%ROWTYPE;
    inCodPeriodoMovimentacao        INTEGER;
    stDataFinalPeriodoMovimentacao  VARCHAR;
BEGIN

    stSql := '  SELECT periodo_movimentacao.cod_periodo_movimentacao
                     , periodo_movimentacao.dt_final
                  FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                     , folhapagamento'|| stEntidade ||'.periodo_movimentacao_situacao
                     , (SELECT max(timestamp) as timestamp
                          FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao_situacao
                         WHERE situacao = ''a'') as max_timestamp
                 WHERE periodo_movimentacao.cod_periodo_movimentacao = periodo_movimentacao_situacao.cod_periodo_movimentacao 
                   AND periodo_movimentacao_situacao.timestamp       = max_timestamp.timestamp
                   AND periodo_movimentacao_situacao.situacao        = ''a'' ';
                   
    FOR reRegistro IN EXECUTE stSql 
    LOOP
        inCodPeriodoMovimentacao        := reRegistro.cod_periodo_movimentacao;
        stDataFinalPeriodoMovimentacao  := reRegistro.dt_final;
    END LOOP;
    
    stSqlDetalhesCargos := 'SELECT cargo.cod_cargo
                                 , false as especialidade
                                 , null as cod_especialidade
                              FROM pessoal'|| stEntidade ||'.cargo
                             UNION
                            SELECT especialidade.cod_cargo
                                 , true as especialidade
                                 , especialidade.cod_especialidade as cod_especialidade
                              FROM pessoal'|| stEntidade ||'.especialidade';
                                
    IF stTipoFiltro = 'reg_sub_car_esp_grupo' THEN
        stSqlJoinFuncaoCargo       := ', cod_cargo
                                       , cod_especialidade_cargo as cod_especialidade
                                       , desc_regime
                                       , desc_sub_divisao';
        stSqlJoinFuncaoCargoFiltro := ' AND (cargo_detalhes.cod_especialidade = cadastro.cod_especialidade OR (cadastro.cod_especialidade is null)) ';
    ELSE
        stSqlJoinFuncaoCargo       := ', cod_funcao as cod_cargo
                                       , cod_especialidade_funcao as cod_especialidade
                                       , desc_regime_funcao as desc_regime
                                       , desc_sub_divisao_funcao as desc_sub_divisao';
        stSqlJoinFuncaoCargoFiltro := ' AND (cargo_detalhes.cod_especialidade = cadastro.cod_especialidade OR (cadastro.cod_especialidade is null)) ';
    END IF;
    

    stSql := 'SELECT cadastro.registro as matricula
                   , cadastro.nom_cgm as nome
                   , cadastro.desc_regime||'' - ''|| cadastro.desc_sub_divisao as regime_sub_divisao
                   , to_char(cadastro.dt_admissao, ''dd/mm/yyyy'') as dt_admissao
                   , COALESCE(to_real(cadastro.horas_mensais), ''0,00'') as horas_mensais
                   , COALESCE(to_real(cadastro.horas_semanais), ''0,00'') as horas_semanais
                FROM (
                      SELECT cod_contrato
                           , registro
                           , nom_cgm
                           , dt_admissao
                           , horas_mensais
                           , horas_semanais 
                           '|| stSqlJoinFuncaoCargo ||' 
                       FROM recuperarContratoServidor(''cgm,f,sf,rf,sf,ca,car,cas,s,anp'','|| quote_literal(stEntidade) ||',0,'|| quote_literal(stTipoFiltro) ||','|| quote_literal(stCodigos) ||','|| quote_literal(inExercicio) ||')
                     ) as cadastro
          INNER JOIN (
                      '|| stSqlDetalhesCargos ||'
                     ) as cargo_detalhes
                  ON cargo_detalhes.cod_cargo = cadastro.cod_cargo
               WHERE recuperarSituacaoDoContrato(cadastro.cod_contrato,0,'|| quote_literal(stEntidade) ||') IN (''A'')'
                     ||stSqlJoinFuncaoCargoFiltro;
                     
    IF trim(stCodCargo) != '' THEN 
        stSql := stSql ||' AND cargo_detalhes.cod_cargo = '|| trim(stCodCargo);
    END IF;
    
    IF trim(stCodEspecialidade) != '' THEN 
        stSql := stSql ||' AND cargo_detalhes.cod_especialidade = '|| trim(stCodEspecialidade);
    ELSE
        stSql := stSql ||' AND cargo_detalhes.cod_especialidade IS NULL ';
    END IF;
    
    stSql := stSql ||' ORDER BY nome, regime_sub_divisao ';
    

    
    FOR reRegistro IN EXECUTE stSql 
    LOOP
        rwRelatorioCargosServidores.matricula           := reRegistro.matricula;
        rwRelatorioCargosServidores.nome                := reRegistro.nome;
        rwRelatorioCargosServidores.dt_admissao         := reRegistro.dt_admissao;
        rwRelatorioCargosServidores.regime_sub_divisao  := reRegistro.regime_sub_divisao;
        rwRelatorioCargosServidores.horas_mensais       := reRegistro.horas_mensais;
        rwRelatorioCargosServidores.horas_semanais      := reRegistro.horas_semanais;
        
        RETURN NEXT rwRelatorioCargosServidores;
    END LOOP;

END;
$$ LANGUAGE 'plpgsql';
