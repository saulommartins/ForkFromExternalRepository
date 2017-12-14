<?php
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
?>
<?php
/**
 * Página de Filtro de Responsavel Licitacao
 * Data de Criação   : 21/01/2015
 * @author Analista: Ane Caroline Fiegenbaum Pereira
 * @author Desenvolvedor: Evandro Melos
 * $Id: $
 * $Name: $
 * $Revision: $
 * $Author: $
 * $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOResponsavelLicitacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMGOResponsavelLicitacao()
    {
        parent::Persistente();
        $this->setTabela('tcmgo.responsavel_licitacao');
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_entidade, cod_modalidade, cod_licitacao');

        $this->AddCampo('exercicio'                     , 'varchar',  true,     '4',  true, false);
        $this->AddCampo('cod_entidade'                  , 'integer',  true,     '',   true, false);
        $this->AddCampo('cod_modalidade'                , 'integer',  true,     '',   true, false);
        $this->AddCampo('cod_licitacao'                 , 'integer',  true,     '',   true, false);
        $this->AddCampo('cgm_resp_abertura_licitacao'   , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_edital'               , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_pesquisa'             , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_recurso_orcamentario' , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_conducao_licitacao'   , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_homologacao'          , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_adjudicacao'          , 'integer',  false,    '',   false, false);
    }
    
    public function recuperaResponsavel(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaResponsavel($stFiltro).$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaRecuperaResponsavel($stFiltro)
    {            
        $stSql = "
            SELECT nom_cgm
            FROM sw_cgm
           WHERE sw_cgm.numcgm = ".$stFiltro.";";

        return $stSql;
    }

    function recuperaResponsaveisLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaResponsaveisLicitacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
    }

    function montaRecuperaResponsaveisLicitacao()
    {
        $stSql ="   SELECT 10 as tipo_registro
                            , LPAD(''||despesa.num_orgao,2, '0') as cod_orgao
                            , LPAD(''||despesa.num_unidade,2, '0') AS codunidade
                            , licitacao.exercicio as exercicio_licitacao
                            , licitacao.exercicio::varchar||LPAD(''||licitacao.cod_entidade::varchar,2, '0')||LPAD(''||licitacao.cod_modalidade::varchar,2, '0')||LPAD(''||licitacao.cod_licitacao::varchar,4, '0') AS num_processo_licitatorio 
                            , dadosResponsaveis.tipo_responsabilidade
                            , dadosResponsaveis.cpf
                            , dadosResponsaveis.nom_cgm as nome_responsavel
                            ,CASE WHEN (comissao_membros.numcgm = dadosResponsaveis.numcgm) THEN
                                    comissao_membros.cargo
                                  WHEN (membro_adicional.numcgm = dadosResponsaveis.numcgm) THEN
                                    membro_adicional.cargo
                            END AS cargo_responsavel
                            , dadosResponsaveis.logradouro as logra_res_responsavel
                            , dadosResponsaveis.bairro as setor_logra_responsavel
                            , dadosResponsaveis.nom_municipio as cidade_logra_responsavel
                            , dadosResponsaveis.sigla_uf as uf_cidade_logra_responsavel
                            , dadosResponsaveis.cep as cep_logra_responsavel
                            , CASE WHEN dadosResponsaveis.fone_residencial != '' THEN
                                        dadosResponsaveis.fone_residencial 
                                    ELSE
                                        dadosResponsaveis.fone_celular 
                            END as fone_responsavel
                            , dadosResponsaveis.e_mail as email
                            , CASE dadosResponsaveis.cod_escolaridade 
                                    WHEN 0  THEN 0
                                    WHEN 1  THEN 0
                                    WHEN 2  THEN 01
                                    WHEN 4  THEN 01
                                    WHEN 5  THEN 02
                                    WHEN 6  THEN 03
                                    WHEN 7  THEN 04
                                    WHEN 8  THEN 05
                                    WHEN 9  THEN 06
                                    WHEN 10 THEN 10
                                    WHEN 11 THEN 12
                                    WHEN 12 THEN 09
                                    WHEN 13 THEN 11
                                    WHEN 14 THEN 08
                                    WHEN 15 THEN 07
                            END as escolaridade
                            , ''::VARCHAR(15) as brancos                            
                                                        
                    FROM tcmgo.responsavel_licitacao

                INNER JOIN (
                            SELECT sw_cgm_pessoa_fisica.cpf
                                   , responsavel_licitacao.exercicio
                                   , responsavel_licitacao.cod_licitacao
                                   , responsavel_licitacao.cod_modalidade
                                   , responsavel_licitacao.cod_entidade
                                   , sw_cgm_pessoa_fisica.numcgm
                                   , sw_escolaridade.cod_escolaridade
                                   , sw_cgm.nom_cgm
                                   , sw_cgm.logradouro
                                   , sw_cgm.bairro
                                   , sw_municipio.nom_municipio
                                   , sw_uf.sigla_uf
                                   , sw_cgm.cep
                                   , sw_cgm.fone_residencial
                                   , sw_cgm.fone_celular 
                                   , sw_cgm.e_mail                                    
                                   , 1 AS tipo_responsabilidade
                                    
                            FROM tcmgo.responsavel_licitacao
                            JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm =  responsavel_licitacao.cgm_resp_abertura_licitacao
                            JOIN sw_cgm
                                ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm                        
                            JOIN sw_municipio
                                ON sw_municipio.cod_municipio   = sw_cgm.cod_municipio
                                AND sw_municipio.cod_uf         = sw_cgm.cod_uf
                            JOIN sw_uf
                                ON sw_uf.cod_uf = sw_municipio.cod_uf
                            JOIN sw_escolaridade
                                ON sw_escolaridade.cod_escolaridade = sw_cgm_pessoa_fisica.cod_escolaridade
                           
                        UNION ALL                        

                            SELECT sw_cgm_pessoa_fisica.cpf
                                   , responsavel_licitacao.exercicio
                                   , responsavel_licitacao.cod_licitacao
                                   , responsavel_licitacao.cod_modalidade
                                   , responsavel_licitacao.cod_entidade
                                   , sw_cgm_pessoa_fisica.numcgm
                                   , sw_escolaridade.cod_escolaridade
                                   , sw_cgm.nom_cgm
                                   , sw_cgm.logradouro
                                   , sw_cgm.bairro
                                   , sw_municipio.nom_municipio
                                   , sw_uf.sigla_uf
                                   , sw_cgm.cep
                                   , sw_cgm.fone_residencial 
                                   , sw_cgm.fone_celular 
                                   , sw_cgm.e_mail                                    
                                    , 2 AS tipo_responsabilidade
                                    
                            FROM tcmgo.responsavel_licitacao
                            JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm =  responsavel_licitacao.cgm_resp_edital
                             JOIN sw_cgm
                                ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm                        
                            JOIN sw_municipio
                                ON sw_municipio.cod_municipio   = sw_cgm.cod_municipio
                                AND sw_municipio.cod_uf         = sw_cgm.cod_uf
                            JOIN sw_uf
                                ON sw_uf.cod_uf = sw_municipio.cod_uf
                            JOIN sw_escolaridade
                                ON sw_escolaridade.cod_escolaridade = sw_cgm_pessoa_fisica.cod_escolaridade

                        UNION ALL
                       
                            SELECT sw_cgm_pessoa_fisica.cpf
                                   , responsavel_licitacao.exercicio
                                   , responsavel_licitacao.cod_licitacao
                                   , responsavel_licitacao.cod_modalidade
                                   , responsavel_licitacao.cod_entidade
                                   , sw_cgm_pessoa_fisica.numcgm
                                   , sw_escolaridade.cod_escolaridade
                                   , sw_cgm.nom_cgm
                                   , sw_cgm.logradouro
                                   , sw_cgm.bairro
                                   , sw_municipio.nom_municipio
                                   , sw_uf.sigla_uf
                                   , sw_cgm.cep
                                   , sw_cgm.fone_residencial 
                                   , sw_cgm.fone_celular 
                                   , sw_cgm.e_mail                                    
                                    , 3 AS tipo_responsabilidade
                                    
                            FROM tcmgo.responsavel_licitacao
                            JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm =  responsavel_licitacao.cgm_resp_pesquisa
                             JOIN sw_cgm
                                ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm                        
                            JOIN sw_municipio
                                ON sw_municipio.cod_municipio   = sw_cgm.cod_municipio
                                AND sw_municipio.cod_uf         = sw_cgm.cod_uf
                            JOIN sw_uf
                                ON sw_uf.cod_uf = sw_municipio.cod_uf
                            JOIN sw_escolaridade
                                ON sw_escolaridade.cod_escolaridade = sw_cgm_pessoa_fisica.cod_escolaridade

                        UNION ALL
                       
                            SELECT sw_cgm_pessoa_fisica.cpf
                                   , responsavel_licitacao.exercicio
                                   , responsavel_licitacao.cod_licitacao
                                   , responsavel_licitacao.cod_modalidade
                                   , responsavel_licitacao.cod_entidade
                                   , sw_cgm_pessoa_fisica.numcgm
                                   , sw_escolaridade.cod_escolaridade
                                   , sw_cgm.nom_cgm
                                   , sw_cgm.logradouro
                                   , sw_cgm.bairro
                                   , sw_municipio.nom_municipio
                                   , sw_uf.sigla_uf
                                   , sw_cgm.cep
                                   , sw_cgm.fone_residencial 
                                   , sw_cgm.fone_celular 
                                   , sw_cgm.e_mail                                    
                                   , 4 AS tipo_responsabilidade
                                   
                            FROM tcmgo.responsavel_licitacao
                            JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm =  responsavel_licitacao.cgm_resp_recurso_orcamentario
                             JOIN sw_cgm
                                ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm                        
                            JOIN sw_municipio
                                ON sw_municipio.cod_municipio   = sw_cgm.cod_municipio
                                AND sw_municipio.cod_uf         = sw_cgm.cod_uf
                            JOIN sw_uf
                                ON sw_uf.cod_uf = sw_municipio.cod_uf
                            JOIN sw_escolaridade
                                ON sw_escolaridade.cod_escolaridade = sw_cgm_pessoa_fisica.cod_escolaridade

                        UNION ALL
                       
                            SELECT sw_cgm_pessoa_fisica.cpf
                                   , responsavel_licitacao.exercicio
                                   , responsavel_licitacao.cod_licitacao
                                   , responsavel_licitacao.cod_modalidade
                                   , responsavel_licitacao.cod_entidade
                                   , sw_cgm_pessoa_fisica.numcgm
                                   , sw_escolaridade.cod_escolaridade
                                   , sw_cgm.nom_cgm
                                   , sw_cgm.logradouro
                                   , sw_cgm.bairro
                                   , sw_municipio.nom_municipio
                                   , sw_uf.sigla_uf
                                   , sw_cgm.cep
                                   , sw_cgm.fone_residencial 
                                   , sw_cgm.fone_celular 
                                   , sw_cgm.e_mail                                    
                                   , 5 AS tipo_responsabilidade
                                   
                            FROM tcmgo.responsavel_licitacao
                            JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm =  responsavel_licitacao.cgm_resp_conducao_licitacao
                             JOIN sw_cgm
                                ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm                        
                            JOIN sw_municipio
                                ON sw_municipio.cod_municipio   = sw_cgm.cod_municipio
                                AND sw_municipio.cod_uf         = sw_cgm.cod_uf
                            JOIN sw_uf
                                ON sw_uf.cod_uf = sw_municipio.cod_uf
                            JOIN sw_escolaridade
                                ON sw_escolaridade.cod_escolaridade = sw_cgm_pessoa_fisica.cod_escolaridade

                        UNION ALL
                       
                            SELECT sw_cgm_pessoa_fisica.cpf
                                   , responsavel_licitacao.exercicio
                                   , responsavel_licitacao.cod_licitacao
                                   , responsavel_licitacao.cod_modalidade
                                   , responsavel_licitacao.cod_entidade
                                   , sw_cgm_pessoa_fisica.numcgm
                                   , sw_escolaridade.cod_escolaridade
                                   , sw_cgm.nom_cgm
                                   , sw_cgm.logradouro
                                   , sw_cgm.bairro
                                   , sw_municipio.nom_municipio
                                   , sw_uf.sigla_uf
                                   , sw_cgm.cep
                                   , sw_cgm.fone_residencial 
                                   , sw_cgm.fone_celular 
                                   , sw_cgm.e_mail                                    
                                   , 6 AS tipo_responsabilidade
                                   
                            FROM tcmgo.responsavel_licitacao
                            JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm = responsavel_licitacao.cgm_resp_homologacao
                             JOIN sw_cgm
                                ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm                        
                            JOIN sw_municipio
                                ON sw_municipio.cod_municipio   = sw_cgm.cod_municipio
                                AND sw_municipio.cod_uf         = sw_cgm.cod_uf
                            JOIN sw_uf
                                ON sw_uf.cod_uf = sw_municipio.cod_uf
                            JOIN sw_escolaridade
                                ON sw_escolaridade.cod_escolaridade = sw_cgm_pessoa_fisica.cod_escolaridade
                        
                        UNION ALL
                     
                            SELECT sw_cgm_pessoa_fisica.cpf
                                   , responsavel_licitacao.exercicio
                                   , responsavel_licitacao.cod_licitacao
                                   , responsavel_licitacao.cod_modalidade
                                   , responsavel_licitacao.cod_entidade
                                   , sw_cgm_pessoa_fisica.numcgm
                                   , sw_escolaridade.cod_escolaridade
                                   , sw_cgm.nom_cgm
                                   , sw_cgm.logradouro
                                   , sw_cgm.bairro
                                   , sw_municipio.nom_municipio
                                   , sw_uf.sigla_uf
                                   , sw_cgm.cep
                                   , sw_cgm.fone_residencial 
                                   , sw_cgm.fone_celular 
                                   , sw_cgm.e_mail                                    
                                   , 7 AS tipo_responsabilidade
                            FROM tcmgo.responsavel_licitacao
                            JOIN sw_cgm_pessoa_fisica
                                ON sw_cgm_pessoa_fisica.numcgm =  responsavel_licitacao.cgm_resp_adjudicacao
                            JOIN sw_cgm
                                ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm                        
                            JOIN sw_municipio
                                ON sw_municipio.cod_municipio   = sw_cgm.cod_municipio
                                AND sw_municipio.cod_uf         = sw_cgm.cod_uf
                            JOIN sw_uf
                                ON sw_uf.cod_uf = sw_municipio.cod_uf
                            JOIN sw_escolaridade
                                ON sw_escolaridade.cod_escolaridade = sw_cgm_pessoa_fisica.cod_escolaridade

                    ) AS dadosResponsaveis
                          ON responsavel_licitacao.exercicio     = dadosResponsaveis.exercicio
                        AND responsavel_licitacao.cod_licitacao  = dadosResponsaveis.cod_licitacao
                        AND responsavel_licitacao.cod_modalidade = dadosResponsaveis.cod_modalidade
                        AND responsavel_licitacao.cod_entidade   = dadosResponsaveis.cod_entidade

                    INNER JOIN licitacao.licitacao    
                      ON dadosResponsaveis.exercicio      = licitacao.exercicio
                     AND dadosResponsaveis.cod_licitacao  = licitacao.cod_licitacao
                     AND dadosResponsaveis.cod_modalidade = licitacao.cod_modalidade
                     AND dadosResponsaveis.cod_entidade   = licitacao.cod_entidade

                JOIN licitacao.comissao_licitacao 
                    ON comissao_licitacao.cod_licitacao     = licitacao.cod_licitacao
                    AND comissao_licitacao.cod_modalidade   = licitacao.cod_modalidade
                    AND comissao_licitacao.cod_entidade     = licitacao.cod_entidade
                    AND comissao_licitacao.exercicio        = licitacao.exercicio
                         
                JOIN licitacao.comissao_membros
                    ON comissao_membros.cod_comissao = comissao_licitacao.cod_comissao
                    AND comissao_membros.numcgm      = dadosResponsaveis.numcgm
                    
                LEFT JOIN licitacao.membro_adicional
                    ON membro_adicional.cod_licitacao   = licitacao.cod_licitacao
                    AND membro_adicional.cod_modalidade = licitacao.cod_modalidade
                    AND membro_adicional.cod_entidade   = licitacao.cod_entidade
                    AND membro_adicional.exercicio      = licitacao.exercicio

                    left JOIN compras.mapa_cotacao
                         ON mapa_cotacao.exercicio_mapa = licitacao.exercicio
                        AND mapa_cotacao.cod_mapa = licitacao.cod_mapa
             
                    left JOIN compras.cotacao
                         ON cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
                        AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
            
                    left JOIN compras.julgamento
                         ON julgamento.exercicio   = cotacao.exercicio
                        AND julgamento.cod_cotacao = cotacao.cod_cotacao
            
                    JOIN licitacao.homologacao
                         ON homologacao.cod_licitacao  = licitacao.cod_licitacao
                        AND homologacao.cod_modalidade = licitacao.cod_modalidade
                        AND homologacao.cod_entidade   = licitacao.cod_entidade
                        AND homologacao.exercicio_licitacao = licitacao.exercicio
                        AND (
                                SELECT homologacao_anulada.num_homologacao 
                                FROM licitacao.homologacao_anulada
                                WHERE homologacao_anulada.cod_licitacao     = licitacao.cod_licitacao
                                AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
                                AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
                                AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
                                AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
                                AND homologacao.cod_item                    = homologacao_anulada.cod_item
                                AND homologacao.lote                        = homologacao_anulada.lote
                    ) IS NULL
                    
              INNER JOIN tcmgo.orgao
                      ON orgao.num_orgao = licitacao.num_orgao
                     AND orgao.exercicio = licitacao.exercicio
                     
              INNER JOIN compras.mapa
                      ON mapa.exercicio = licitacao.exercicio_mapa
                     AND mapa.cod_mapa  = licitacao.cod_mapa
                     
              INNER JOIN compras.mapa_solicitacao
                      ON mapa_solicitacao.exercicio = mapa.exercicio
                     AND mapa_solicitacao.cod_mapa  = mapa.cod_mapa
                    
              INNER JOIN compras.mapa_item
                      ON mapa_item.exercicio             = mapa_solicitacao.exercicio
                     AND mapa_item.cod_entidade          = mapa_solicitacao.cod_entidade
                     AND mapa_item.cod_solicitacao       = mapa_solicitacao.cod_solicitacao
                     AND mapa_item.cod_mapa              = mapa_solicitacao.cod_mapa
                     AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
                   
              INNER JOIN compras.solicitacao_item
                      ON solicitacao_item.exercicio         = mapa_item.exercicio_solicitacao
                     AND solicitacao_item.cod_entidade     = mapa_item.cod_entidade
                     AND solicitacao_item.cod_solicitacao  = mapa_item.cod_solicitacao
                     AND solicitacao_item.cod_centro       = mapa_item.cod_centro
                     AND solicitacao_item.cod_item         = mapa_item.cod_item

              INNER JOIN compras.solicitacao_item_dotacao
                      ON solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
                     AND solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
                     AND solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                     AND solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
                     AND solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item
                                           
              INNER JOIN orcamento.despesa
                      ON despesa.exercicio   = solicitacao_item_dotacao.exercicio
                     AND despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa

                   WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                     AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
                     AND licitacao.exercicio = '" . $this->getDado('exercicio') . "'
                     AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
                     AND licitacao.cod_modalidade NOT IN (8,9)
                     AND NOT EXISTS( SELECT 1
                                       FROM licitacao.licitacao_anulada
                                      WHERE licitacao_anulada.cod_licitacao   = licitacao.cod_licitacao
                                        AND licitacao_anulada.cod_modalidade  = licitacao.cod_modalidade
                                        AND licitacao_anulada.cod_entidade    = licitacao.cod_entidade
                                        AND licitacao_anulada.exercicio       = licitacao.exercicio )
                         
        GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18
        ORDER BY num_processo_licitatorio";
        return $stSql;
    }
    
    function recuperaComissaoLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaComissaoLicitacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
    }

    function montaRecuperaComissaoLicitacao()
    {
        $stSql = "SELECT DISTINCT 20 as tipo_registro
                        , LPAD(''||despesa.num_orgao,2, '0') as cod_orgao
                        , LPAD(''||despesa.num_unidade,2, '0') AS cod_unidade
                        , licitacao.exercicio as exercicio_licitacao
                        , licitacao.exercicio::varchar||LPAD(''||licitacao.cod_entidade::varchar,2, '0')||LPAD(''||licitacao.cod_modalidade::varchar,2, '0')||LPAD(''||licitacao.cod_licitacao::varchar,4, '0') AS nro_processo_licitatorio
                        , CASE WHEN comissao.cod_tipo_comissao = 1 THEN 2
                                WHEN comissao.cod_tipo_comissao = 2 THEN 1
                                WHEN comissao.cod_tipo_comissao = 3 THEN 2
                        END AS tipo_comissao
                        , tipo_membro.cod_tipo_membro as cod_atribuicao
                        , tipo_membro.cpf as cpf_membro_comissao                        
                        , CASE WHEN (norma.cod_tipo_norma = 4) THEN 
                                    1
                            ELSE 
                                    2
                        END AS tipo_ato_momeacao
                        , norma.num_norma as nro_ato_nomeacao
                        , to_char(norma.dt_assinatura,'ddmmyyyy') as data_ato_nomeacao
                        , to_char(norma.dt_publicacao,'ddmmyyyy') as inicio_vigencia
                        , to_char(norma_data_termino.dt_termino,'ddmmyyyy') as final_vigencia
                        , sem_acentos(sw_cgm.nom_cgm) as nom_membro_com_lic
                        , CASE WHEN (comissao_membros.numcgm = tipo_membro.numcgm) THEN 
                                    comissao_membros.cargo
                            WHEN (membro_adicional.numcgm = tipo_membro.numcgm) THEN 
                                    membro_adicional.cargo
                        END AS cargo
                        , CASE WHEN (comissao_membros.numcgm = tipo_membro.numcgm) THEN 
                                    comissao_membros.natureza_cargo
                               WHEN (membro_adicional.numcgm = tipo_membro.numcgm) THEN 
                                    membro_adicional.natureza_cargo
                        END AS natureza_cargo
                        , sem_acentos(sw_cgm.logradouro) as logra_res_membro
                        , sw_cgm.bairro as setor_logra_membro
                        , sw_municipio.nom_municipio as cidade_logra_membro
                        , sw_uf.sigla_uf as uf_cidade_lograMembro
                        , sw_cgm.cep as cep_logra_membro
                        , CASE WHEN sw_cgm.fone_residencial != '' THEN 
                                    sw_cgm.fone_residencial 
                                ELSE
                                    sw_cgm.fone_celular 
                        END as fone_membro
                        , sw_cgm.e_mail as email
                        , CASE sw_escolaridade.cod_escolaridade 
                                    WHEN 0  THEN 0
                                    WHEN 1  THEN 0
                                    WHEN 2  THEN 01
                                    WHEN 4  THEN 01
                                    WHEN 5  THEN 02
                                    WHEN 6  THEN 03
                                    WHEN 7  THEN 04
                                    WHEN 8  THEN 05
                                    WHEN 9  THEN 06
                                    WHEN 10 THEN 10
                                    WHEN 11 THEN 12
                                    WHEN 12 THEN 09
                                    WHEN 13 THEN 11
                                    WHEN 14 THEN 08
                                    WHEN 15 THEN 07
                            END as escolaridade
                        
            FROM licitacao.licitacao    
            
         INNER JOIN licitacao.comissao_licitacao
                 ON comissao_licitacao.exercicio = licitacao.exercicio
                AND comissao_licitacao.cod_licitacao = licitacao.cod_licitacao
                AND comissao_licitacao.cod_modalidade = licitacao.cod_modalidade
                AND comissao_licitacao.cod_entidade = licitacao.cod_entidade

         INNER JOIN licitacao.comissao
                 ON comissao.cod_comissao = comissao_licitacao.cod_comissao
                AND ( comissao.cod_tipo_comissao = 1
                   OR comissao.cod_tipo_comissao = 2
                   OR comissao.cod_tipo_comissao = 3 )
                
         INNER JOIN normas.norma
                 ON norma.cod_norma = comissao.cod_norma
                AND ( norma.cod_tipo_norma = 2
                   OR norma.cod_tipo_norma = 4 )

         INNER JOIN normas.norma_data_termino
                 ON norma_data_termino.cod_norma = norma.cod_norma

        INNER JOIN licitacao.comissao_membros
                ON comissao_licitacao.cod_comissao = comissao_membros.cod_comissao

         LEFT JOIN licitacao.membro_adicional
                ON membro_adicional.exercicio = licitacao.exercicio
               AND membro_adicional.cod_licitacao = licitacao.cod_licitacao
               AND membro_adicional.cod_modalidade = licitacao.cod_modalidade
               AND membro_adicional.cod_entidade = licitacao.cod_entidade

        INNER JOIN (  SELECT  pf.numcgm
                            , pf.CPF
                            , CASE  WHEN (membro_adicional.numcgm = pf.numcgm) THEN 2
                                    WHEN (comissao_membros.cod_tipo_membro = 1) THEN 2
                                    WHEN (comissao_membros.cod_tipo_membro = 2) THEN 3
                                    WHEN (comissao_membros.cod_tipo_membro = 3) THEN 6
                            END AS cod_tipo_membro           
                            , pf.cod_escolaridade                 
                    FROM licitacao.licitacao
                    JOIN licitacao.comissao_licitacao --COD_COMISSAO
                        ON comissao_licitacao.exercicio = licitacao.exercicio
                        AND comissao_licitacao.cod_licitacao = licitacao.cod_licitacao
                        AND comissao_licitacao.cod_modalidade = licitacao.cod_modalidade
                        AND comissao_licitacao.cod_entidade = licitacao.cod_entidade
                    JOIN licitacao.comissao_membros
                        ON comissao_licitacao.cod_comissao = comissao_membros.cod_comissao
                    LEFT JOIN licitacao.membro_adicional
                        ON membro_adicional.exercicio = licitacao.exercicio
                        AND membro_adicional.cod_licitacao = licitacao.cod_licitacao
                        AND membro_adicional.cod_modalidade = licitacao.cod_modalidade
                        AND membro_adicional.cod_entidade = licitacao.cod_entidade
                    LEFT JOIN sw_cgm_pessoa_fisica as pf
                        ON pf.numcgm = comissao_membros.numcgm
                        OR pf.numcgm = membro_adicional.numcgm
                    GROUP BY 1,2,3
            ) AS tipo_membro
                ON comissao_membros.numcgm = tipo_membro.numcgm
                OR membro_adicional.numcgm = tipo_membro.numcgm

        INNER JOIN sw_cgm
                ON sw_cgm.numcgm = tipo_membro.numcgm

        INNER JOIN sw_municipio
                ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
               AND sw_municipio.cod_uf        = sw_cgm.cod_uf

        INNER JOIN sw_uf
                ON sw_uf.cod_uf = sw_municipio.cod_uf

        INNER JOIN sw_escolaridade
                ON sw_escolaridade.cod_escolaridade = tipo_membro.cod_escolaridade
                      
        INNER JOIN compras.mapa_cotacao
                ON mapa_cotacao.exercicio_mapa = licitacao.exercicio
               AND mapa_cotacao.cod_mapa       = licitacao.cod_mapa
                     
        INNER JOIN compras.cotacao
                ON cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
               AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
            
        INNER JOIN compras.julgamento
                ON julgamento.exercicio   = cotacao.exercicio
               AND julgamento.cod_cotacao = cotacao.cod_cotacao
                    
        INNER JOIN licitacao.homologacao
                ON homologacao.cod_licitacao  = licitacao.cod_licitacao
               AND homologacao.cod_modalidade = licitacao.cod_modalidade
               AND homologacao.cod_entidade   = licitacao.cod_entidade
               AND homologacao.exercicio_licitacao = licitacao.exercicio
               AND (
                    SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                    WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                    AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                    AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                    AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                    AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                    AND homologacao.cod_item=homologacao_anulada.cod_item
                    AND homologacao.lote=homologacao_anulada.lote
            ) IS NULL
                              
     INNER JOIN tcmgo.orgao
            ON orgao.num_orgao = licitacao.num_orgao
           AND orgao.exercicio = licitacao.exercicio
           
    INNER JOIN compras.mapa
            ON mapa.exercicio = licitacao.exercicio_mapa
           AND mapa.cod_mapa  = licitacao.cod_mapa
           
    INNER JOIN compras.mapa_solicitacao
            ON mapa_solicitacao.exercicio = mapa.exercicio
           AND mapa_solicitacao.cod_mapa  = mapa.cod_mapa
          
    INNER JOIN compras.mapa_item
            ON mapa_item.exercicio             = mapa_solicitacao.exercicio
           AND mapa_item.cod_entidade          = mapa_solicitacao.cod_entidade
           AND mapa_item.cod_solicitacao       = mapa_solicitacao.cod_solicitacao
           AND mapa_item.cod_mapa              = mapa_solicitacao.cod_mapa
           AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
         
    INNER JOIN compras.solicitacao_item
            ON solicitacao_item.exercicio        = mapa_item.exercicio_solicitacao
           AND solicitacao_item.cod_entidade     = mapa_item.cod_entidade
           AND solicitacao_item.cod_solicitacao  = mapa_item.cod_solicitacao
           AND solicitacao_item.cod_centro       = mapa_item.cod_centro
           AND solicitacao_item.cod_item         = mapa_item.cod_item

    INNER JOIN compras.solicitacao_item_dotacao
            ON solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
           AND solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
           AND solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
           AND solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
           AND solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item
                                 
    INNER JOIN orcamento.despesa
            ON despesa.exercicio   = solicitacao_item_dotacao.exercicio
           AND despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa       
                   
         WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
           AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
           AND licitacao.exercicio = '" . $this->getDado('exercicio') . "'
           AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
           AND licitacao.cod_modalidade NOT IN (8,9)
           AND NOT EXISTS( SELECT 1
                           FROM licitacao.licitacao_anulada
                           WHERE licitacao_anulada.cod_licitacao    = licitacao.cod_licitacao
                               AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                               AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                               AND licitacao_anulada.exercicio      = licitacao.exercicio
                       )
          AND NOT EXISTS( SELECT 1
                           FROM licitacao.membro_excluido
                           WHERE membro_excluido.numcgm = tipo_membro.numcgm
                       )
         GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24 ";
        return $stSql;
    }
    
    public function __destruct(){}

}
?>