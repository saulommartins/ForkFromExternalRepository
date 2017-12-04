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
/**
    * PLpgsql Recibo de Pensï¿½o Judicial
    * Data de Criação: 24/06/2008
    
    
    * @author Analista: Dagiane Vieira  
    * @author Desenvolvedor: Diego Lemos de Souza
    
    * @ignore
       
    * Casos de uso: uc-04.05.65
    
    $Id:$    
*/

CREATE OR REPLACE FUNCTION reciboPensaoJudicial(INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,BOOLEAN,VARCHAR,VARCHAR) RETURNS SETOF colunasReciboPensaoJudicial AS $$
DECLARE
    inCodPeriodoMovimentacao    ALIAS FOR $1;
    inFolha                     ALIAS FOR $2;
    inCodComplementar           ALIAS FOR $3;
    stTipoFiltro                ALIAS FOR $4;
    stCodigosFiltro             ALIAS FOR $5;
    boDuplicar                  ALIAS FOR $6;
    stEntidade                  ALIAS FOR $7;
    stOrden                     ALIAS FOR $8;
    stSql                       VARCHAR;  
    reRegistro                  RECORD;
    nuValor                     NUMERIC(14,2);
    stExercicio                 VARCHAR;
    rwReciboPensaoJudicial      colunasReciboPensaoJudicial%ROWTYPE;
BEGIN
    SELECT max(valor) as exercicio
      into stExercicio
      FROM administracao.configuracao 
     WHERE parametro = 'ano_exercicio';

    stSql := '  SELECT pensao.cod_pensao
                     , pensao.timestamp
                     , dependente.numcgm                                                                 
                     , servidor.numcgm as numcgm_servidor                                                
                     , contrato.registro
                     , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm_servidor   
                     , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = dependente.numcgm) as nom_cgm          
                     , sw_cgm_pessoa_fisica.cpf
                     , sw_cgm_pessoa_fisica.rg
                     , to_char(sw_cgm_pessoa_fisica.dt_nascimento,''dd/mm/yyyy'') as dt_nascimento
                     , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = responsavel_legal.numcgm) as nom_cgm_responsavel          
                     , recuperaDescricaoOrgao(contrato_servidor_orgao.cod_orgao,'|| quote_literal(stExercicio ||'-01-01') ||') as orgao
                     , (SELECT trim(descricao) FROM organograma.local WHERE cod_local = contrato_servidor_local.cod_local) as local
                     , (SELECT num_agencia FROM monetario.agencia WHERE cod_banco = pensao_banco.cod_banco AND cod_agencia = pensao_banco.cod_agencia) as num_agencia
                     , num_banco
                     , nom_banco
                     , pensao_banco.conta_corrente
                     , to_char(pensao.dt_inclusao,''dd/mm/yyyy'') as dt_inclusao
                     , pensao.percentual
                     , to_char(pensao.dt_limite,''dd/mm/yyyy'') as dt_limite
                     , pensao.observacao
                     , calculado_dependente.valor
                  FROM pessoal'|| stEntidade ||'.servidor_dependente                             
            INNER JOIN pessoal'|| stEntidade ||'.servidor_contrato_servidor                      
                    ON servidor_dependente.cod_servidor = servidor_contrato_servidor.cod_servidor
            INNER JOIN pessoal'|| stEntidade ||'.contrato                                        
                    ON servidor_contrato_servidor.cod_contrato = contrato.cod_contrato                                                                                                                                                                                                                                                                                                 
            INNER JOIN ultimo_contrato_servidor_orgao('|| quote_literal(stEntidade) ||','|| inCodPeriodoMovimentacao ||') as contrato_servidor_orgao
                    ON contrato_servidor_orgao.cod_contrato = contrato.cod_contrato                   
            INNER JOIN pessoal'|| stEntidade ||'.dependente                                      
                    ON servidor_dependente.cod_dependente = dependente.cod_dependente
            INNER JOIN pessoal'|| stEntidade ||'.servidor                                        
                    ON servidor_dependente.cod_servidor = servidor.cod_servidor 
            INNER JOIN sw_cgm_pessoa_fisica
                    ON dependente.numcgm = sw_cgm_pessoa_fisica.numcgm
            INNER JOIN pessoal'|| stEntidade ||'.pensao
                    ON servidor_dependente.cod_servidor = pensao.cod_servidor
                   AND servidor_dependente.cod_dependente = pensao.cod_dependente
            INNER JOIN (  SELECT cod_pensao
                              , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.pensao
                        GROUP BY cod_pensao) as max_pensao   
                    ON pensao.cod_pensao = max_pensao.cod_pensao
                   AND pensao.timestamp = max_pensao.timestamp
            INNER JOIN pessoal'|| stEntidade ||'.pensao_banco
                    ON pensao.cod_pensao = pensao_banco.cod_pensao
                   AND pensao.timestamp = pensao_banco.timestamp                   
            INNER JOIN monetario.banco            
                    ON banco.cod_banco = pensao_banco.cod_banco
             LEFT JOIN ultimo_contrato_servidor_local('|| quote_literal(stEntidade) ||','|| inCodPeriodoMovimentacao ||') as contrato_servidor_local
                    ON contrato.cod_contrato = contrato_servidor_local.cod_contrato                           
             LEFT JOIN pessoal'|| stEntidade ||'.responsavel_legal
                    ON responsavel_legal.cod_pensao = pensao.cod_pensao     
                   AND responsavel_legal.timestamp = pensao.timestamp';
                   
    IF inFolha = 0 THEN
        stSql := stSql  ||' INNER JOIN folhapagamento'|| stEntidade ||'.evento_complementar_calculado_dependente as calculado_dependente
                                   ON dependente.cod_dependente = calculado_dependente.cod_dependente
                           INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_complementar
                                   ON calculado_dependente.cod_registro = registro_evento_complementar.cod_registro
                                  AND calculado_dependente.cod_evento = registro_evento_complementar.cod_evento
                                  AND calculado_dependente.cod_configuracao = registro_evento_complementar.cod_configuracao
                                  AND calculado_dependente.timestamp_registro = registro_evento_complementar.timestamp
                                  AND registro_evento_complementar.cod_complementar '|| inCodComplementar ||'
                                  AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;
    END IF;        
    
    IF inFolha = 1 THEN
        stSql := stSql  ||'  INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                                ON registro_evento_periodo.cod_contrato = contrato.cod_contrato
                                               AND registro_evento_periodo.cod_periodo_movimentacao =  '|| inCodPeriodoMovimentacao||'

                                     INNER JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento
                                                 ON ultimo_registro_evento.cod_registro = registro_evento_periodo.cod_registro                  

                                    INNER JOIN folhapagamento'|| stEntidade ||'.evento_calculado_dependente as calculado_dependente
                                                ON dependente.cod_dependente = calculado_dependente.cod_dependente
                                              AND calculado_dependente.cod_registro = ultimo_registro_evento.cod_registro
                                              AND calculado_dependente.cod_evento = ultimo_registro_evento.cod_evento
                                              AND calculado_dependente.timestamp_registro = ultimo_registro_evento.timestamp';
    END IF;
    
    IF inFolha = 2 THEN
        stSql := stSql  ||' INNER JOIN folhapagamento'|| stEntidade ||'.evento_ferias_calculado_dependente as calculado_dependente
                                   ON dependente.cod_dependente = calculado_dependente.cod_dependente
                           INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_ferias
                                   ON calculado_dependente.cod_registro = registro_evento_ferias.cod_registro
                                  AND calculado_dependente.cod_evento = registro_evento_ferias.cod_evento
                                  AND calculado_dependente.desdobramento = registro_evento_ferias.desdobramento
                                  AND calculado_dependente.timestamp_registro = registro_evento_ferias.timestamp
                                  AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;
    END IF;    
    
    IF inFolha = 3 THEN
        stSql := stSql  ||' INNER JOIN folhapagamento'|| stEntidade ||'.evento_decimo_calculado_dependente as calculado_dependente
                                   ON dependente.cod_dependente = calculado_dependente.cod_dependente
                           INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_decimo
                                   ON calculado_dependente.cod_registro = registro_evento_decimo.cod_registro
                                  AND calculado_dependente.cod_evento = registro_evento_decimo.cod_evento
                                  AND calculado_dependente.desdobramento = registro_evento_decimo.desdobramento
                                  AND calculado_dependente.timestamp_registro = registro_evento_decimo.timestamp
                                  AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;
    END IF;    
    
    IF inFolha = 4 THEN
        stSql := stSql  ||' INNER JOIN folhapagamento'|| stEntidade ||'.evento_rescisao_calculado_dependente as calculado_dependente
                                   ON dependente.cod_dependente = calculado_dependente.cod_dependente
                           INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                                   ON calculado_dependente.cod_registro = registro_evento_rescisao.cod_registro
                                  AND calculado_dependente.cod_evento = registro_evento_rescisao.cod_evento
                                  AND calculado_dependente.desdobramento = registro_evento_rescisao.desdobramento
                                  AND calculado_dependente.timestamp_registro = registro_evento_rescisao.timestamp
                                  AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;
    END IF;      
    
    stSql := stSql  ||' WHERE NOT EXISTS ( SELECT 1                                                              
                                            FROM pessoal'|| stEntidade ||'.dependente_excluido          
                                           WHERE dependente_excluido.cod_dependente = servidor_dependente.cod_dependente
                                             AND dependente_excluido.cod_servidor = servidor_dependente.cod_servidor)
                                             AND NOT EXISTS ( SELECT 1
                                                                FROM pessoal'|| stEntidade ||'.pensao_excluida
                                                               WHERE pensao_excluida.cod_pensao = pensao.cod_pensao
                                                                 AND pensao_excluida.timestamp = pensao.timestamp)';
                          
    IF stTipoFiltro = 'cgm_dependente' THEN
        stSql := stSql || ' AND dependente.numcgm IN ('|| stCodigosFiltro ||')';
    END IF;
    
    IF stTipoFiltro = 'matricula_dependente_servidor' THEN
        stSql := stSql || ' AND dependente.cod_dependente IN ('|| stCodigosFiltro ||')';                      
    END IF;
    
    IF stTipoFiltro = 'lotacao_grupo' THEN
        stSql := stSql || ' AND contrato_servidor_orgao.cod_orgao IN ('|| stCodigosFiltro ||')';                                            
    END IF;
    
    IF stTipoFiltro = 'local_grupo' THEN
        stSql := stSql || ' AND contrato_servidor_local.cod_local IN ('|| stCodigosFiltro ||')';                                                                  
    END IF;            
    
    IF stOrden = 'a' THEN
        stSql := stSql || ' ORDER BY nom_cgm';        
    END IF;    
    
    IF stOrden = 'n' THEN
        stSql := stSql || ' ORDER BY numcgm';
    END IF;
    
    IF stOrden = 'oa' THEN
        stSql := stSql || ' ORDER BY orgao,nom_cgm';        
    END IF;    
    
    IF stOrden = 'on' THEN
        stSql := stSql || ' ORDER BY orgao,numcgm';
    END IF;    
    
    IF stOrden = 'la' THEN
        stSql := stSql || ' ORDER BY local,nom_cgm';        
    END IF;    
    
    IF stOrden = 'ln' THEN
        stSql := stSql || ' ORDER BY local,numcgm';
    END IF;        

    FOR reRegistro IN EXECUTE stSql LOOP
        stSql := 'SELECT valor 
                    FROM pessoal'|| stEntidade ||'.pensao_valor
                   WHERE cod_pensao = '|| reRegistro.cod_pensao ||'
                     AND timestamp = '|| quote_literal(reRegistro.timestamp) ||'';
        nuValor := selectIntoNumeric(stSql);
        
        rwReciboPensaoJudicial.numcgm              := reRegistro.numcgm;                      
        rwReciboPensaoJudicial.numcgm_servidor     := reRegistro.numcgm_servidor;             
        rwReciboPensaoJudicial.registro            := reRegistro.registro;                    
        rwReciboPensaoJudicial.nom_cgm_servidor    := reRegistro.nom_cgm_servidor;            
        rwReciboPensaoJudicial.nom_cgm             := reRegistro.nom_cgm;                     
        rwReciboPensaoJudicial.cpf                 := reRegistro.cpf;                         
        rwReciboPensaoJudicial.rg                  := reRegistro.rg;                          
        rwReciboPensaoJudicial.dt_nascimento       := reRegistro.dt_nascimento;               
        rwReciboPensaoJudicial.nom_cgm_responsavel := reRegistro.nom_cgm_responsavel;         
        rwReciboPensaoJudicial.orgao               := reRegistro.orgao;                       
        rwReciboPensaoJudicial.local               := reRegistro.local;                       
        rwReciboPensaoJudicial.num_agencia         := reRegistro.num_agencia;                 
        rwReciboPensaoJudicial.num_banco           := reRegistro.num_banco ||'-'|| reRegistro.nom_banco;                   
        rwReciboPensaoJudicial.conta_corrente      := reRegistro.conta_corrente;              
        rwReciboPensaoJudicial.dt_inclusao         := reRegistro.dt_inclusao;                 
        rwReciboPensaoJudicial.percentual          := reRegistro.percentual;                     
        rwReciboPensaoJudicial.dt_limite           := reRegistro.dt_limite;                   
        rwReciboPensaoJudicial.observacao          := reRegistro.observacao;                                                                                             
        rwReciboPensaoJudicial.valor_calculado     := to_real(reRegistro.valor);
        rwReciboPensaoJudicial.valor_calculado_extenso := publico.fn_extenso(reRegistro.valor);
        IF nuValor IS NOT NULL THEN
            rwReciboPensaoJudicial.valor               := to_real(nuValor);        
            rwReciboPensaoJudicial.desconto_fixado     := 'valor';
        ELSE            
            rwReciboPensaoJudicial.valor               := '';        
            rwReciboPensaoJudicial.desconto_fixado     := 'funcao';
        END IF;
        RETURN NEXT rwReciboPensaoJudicial;            
        IF boDuplicar IS TRUE THEN
            rwReciboPensaoJudicial.numcgm              := reRegistro.numcgm;                      
            rwReciboPensaoJudicial.numcgm_servidor     := reRegistro.numcgm_servidor;             
            rwReciboPensaoJudicial.registro            := reRegistro.registro;                    
            rwReciboPensaoJudicial.nom_cgm_servidor    := reRegistro.nom_cgm_servidor;            
            rwReciboPensaoJudicial.nom_cgm             := reRegistro.nom_cgm;                     
            rwReciboPensaoJudicial.cpf                 := reRegistro.cpf;                         
            rwReciboPensaoJudicial.rg                  := reRegistro.rg;                          
            rwReciboPensaoJudicial.dt_nascimento       := reRegistro.dt_nascimento;               
            rwReciboPensaoJudicial.nom_cgm_responsavel := reRegistro.nom_cgm_responsavel;         
            rwReciboPensaoJudicial.orgao               := reRegistro.orgao;                       
            rwReciboPensaoJudicial.local               := reRegistro.local;                       
            rwReciboPensaoJudicial.num_agencia         := reRegistro.num_agencia;                 
            rwReciboPensaoJudicial.num_banco           := reRegistro.num_banco ||'-'|| reRegistro.nom_banco;                   
            rwReciboPensaoJudicial.conta_corrente      := reRegistro.conta_corrente;              
            rwReciboPensaoJudicial.dt_inclusao         := reRegistro.dt_inclusao;                 
            rwReciboPensaoJudicial.percentual          := reRegistro.percentual;                     
            rwReciboPensaoJudicial.dt_limite           := reRegistro.dt_limite;                   
            rwReciboPensaoJudicial.observacao          := reRegistro.observacao;                                                                                     
            rwReciboPensaoJudicial.valor_calculado     := to_real(reRegistro.valor);
            rwReciboPensaoJudicial.valor_calculado_extenso := publico.fn_extenso(reRegistro.valor);
            
            IF nuValor IS NOT NULL THEN
                rwReciboPensaoJudicial.valor               := to_real(nuValor);        
                rwReciboPensaoJudicial.desconto_fixado     := 'valor';
            ELSE            
                rwReciboPensaoJudicial.valor               := '';        
                rwReciboPensaoJudicial.desconto_fixado     := 'funcao';
            END IF;            
            
            RETURN NEXT rwReciboPensaoJudicial;                                 
        END IF;
    END LOOP;
END;       
$$ LANGUAGE 'plpgsql';