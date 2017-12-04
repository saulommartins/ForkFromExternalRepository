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
 *  PL que auxilia na migração do Organograma.
 *
 *  É responsável por criar um novo órgão, bem como um novo local para ser
 *  utilizado na migração. Atualiza nas tabelas de_para os novos valores
 *  que serão correspondentes ao antigo setor (Não informado) e o antigo
 *  local (Não Informado).
 *
 *  @author Diogo Zarpelon.
 */

CREATE OR REPLACE FUNCTION organograma.fn_insere_orgao_nao_informado() RETURNS BOOLEAN AS $$

DECLARE

    boSucesso        BOOLEAN := false;

    inCodOrganograma INTEGER;
    inCodOrgao       INTEGER;
    inCodOrgaoAux    INTEGER;
    inCodLogradouro  INTEGER;
    inCodCalendario  INTEGER;
    inCodLocal       INTEGER;
    
    reRegistro       RECORD;

    stNomLogradouro  VARCHAR;
    stDataCriacao    DATE;

    stSQL            VARCHAR := '';

BEGIN

    -- Recupera o Organograma Ativo.
    SELECT cod_organograma INTO inCodOrganograma FROM organograma.organograma WHERE ativo = true;
    
    -- Recupera o cod_orgao auxiliar para buscar informações como Calendário e Norma.
    SELECT cod_orgao INTO inCodOrgaoAux FROM organograma.orgao_nivel WHERE orgao_nivel.cod_organograma = inCodOrganograma;
    
    -- Recupera o cod_calendario para ser inserido o novo órgão.
    SELECT cod_calendar INTO inCodCalendario FROM organograma.orgao WHERE cod_orgao = inCodOrgaoAux;

    -- Recupera o maior cod_orgao.
    SELECT MAX(cod_orgao)+1 INTO inCodOrgao FROM organograma.orgao;
      
    -- Recupera a data para ser inserido no novo órgão.
    SELECT CURRENT_DATE INTO stDataCriacao;
      
    -- INSERE o novo órgão (Não informado) para atualizar a tabela de_para_setor com o antigo setor (Não informado).
    INSERT INTO organograma.orgao
                (cod_orgao, num_cgm_pf, cod_calendar, cod_norma, descricao, criacao)
         VALUES
                (inCodOrgao, 0, inCodCalendario, 0, 'Não Informado', stDataCriacao);

    -- INSERE o nome do novo órgão em organograma.orgao_descricao
    INSERT INTO organograma.orgao_descricao
                (cod_orgao, timestamp, descricao)
         VALUES
                (inCodOrgao, stDataCriacao, 'Não Informado');

    -- Recupera o nro de níveis do Organograma.
    stSql := 'SELECT * FROM organograma.nivel WHERE cod_organograma = '||inCodOrganograma;

    FOR reRegistro IN EXECUTE stSql LOOP
    
        INSERT
          INTO organograma.orgao_nivel
             (   cod_orgao
               , cod_nivel
               , cod_organograma
               , valor
             )
        VALUES
             (   inCodOrgao
               , reRegistro.cod_nivel
               , reRegistro.cod_organograma
               , '0'
             );

    END LOOP;

    -- Recupera o nome do logradouro para chegar ao cod_logradouro.
    SELECT valor INTO stNomLogradouro FROM administracao.configuracao WHERE cod_modulo = 2 AND exercicio = '2008' AND parametro = 'logradouro';

    -- Recupera o cod_logradouro para futura inserção na organograma.local.
    SELECT cod_logradouro INTO inCodLogradouro FROM sw_nome_logradouro WHERE nom_logradouro = ''||stNomLogradouro||'';
    
    -- Recupera o maior cod_local.
    SELECT MAX(cod_local)+1 INTO inCodLocal FROM organograma.local;

    -- INSERE o novo Local.
    INSERT INTO organograma.local
                (cod_local, cod_logradouro, dificil_acesso, insalubre, descricao)
         VALUES
                (inCodLocal, inCodLogradouro, false, false, 'Não Informado');
         
    -- ATUALIZAÇÃO NAS TABELAS DE-PARA.
    
    -- ATUALIZA a tabela de_para_setor com o novo Órgão cadastrado (Não Informado) para o antigo setor (Não informado).
    UPDATE  organograma.de_para_setor
       SET  cod_orgao_organograma = inCodOrgao
     WHERE  ano_exercicio    = '0000'
       AND  cod_orgao        = 0
       AND  cod_unidade      = 0
       AND  cod_departamento = 0
       AND  cod_setor        = 0;

    -- ATUALIZA a tabela de_para_local com o novo Local cadastrado (Não Informado) para o antigo local (Não informado).
    UPDATE  organograma.de_para_local
       SET  cod_local_organograma = inCodLocal
     WHERE  ano_exercicio    = '0000'
       AND  cod_orgao        = 0
       AND  cod_unidade      = 0
       AND  cod_departamento = 0
       AND  cod_setor        = 0
       AND  cod_local        = 0;

    IF (inCodLocal > 0 AND inCodOrgao > 0) THEN
        boSucesso := true;
    END IF;
    
RETURN boSucesso;

END;

$$ LANGUAGE 'plpgsql';
