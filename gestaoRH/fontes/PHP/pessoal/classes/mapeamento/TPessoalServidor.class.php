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
  * Classe de mapeamento da tabela PESSOAL.SERVIDOR
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.SERVIDOR
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalServidor extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalServidor()
{
    parent::Persistente();
    $this->setTabela('pessoal.servidor');

    $this->setCampoCod('cod_servidor');
    $this->setComplementoChave('');

    $this->AddCampo('cod_servidor',     'integer',  true,'',true,false);
    $this->AddCampo('cod_uf',           'integer,', false,'',false,false);
    $this->AddCampo('cod_municipio',    'integer',  false,'',false,false);
    $this->AddCampo('cod_raca',         'integer',  true,'',false,true);
    $this->AddCampo('cod_estado_civil', 'integer',  true,'',false,true);
    $this->AddCampo('numcgm',           'integer',  true,'',false,false);
    $this->AddCampo('nr_titulo_eleitor','char',     true,'10',false,false);
    $this->AddCampo('zona_titulo',      'char',     true,'5',false,false);
    $this->AddCampo('secao_titulo',     'char',     true,'5',false,false);
    $this->AddCampo('caminho_foto',     'varchar',  false,'80',false,false);
    $this->AddCampo('nome_pai',         'varchar',  false,'80',false,false);
    $this->AddCampo('nome_mae',         'varchar',  true ,'80',false,false);
}

function montaRecuperaRelacionamento()
{
    $stSql  = "    SELECT PS.*                                                                     \n";
    $stSql .= "         , CR.cod_rais                                                              \n";
    $stSql .= "         , to_char(CGM_FISICA_SERVIDOR.dt_nascimento,'dd/mm/yyyy') as dt_nascimento \n";
    $stSql .= "         , CGM_FISICA_SERVIDOR.sexo                                                 \n";
    $stSql .= "         , CGM_FISICA_SERVIDOR.cpf                                                  \n";
    $stSql .= "         , PSC.cod_cid as cod_cid                                                   \n";
    $stSql .= "         , PSR.nr_carteira_res                                                      \n";
    $stSql .= "         , PSR.cat_reservista                                                       \n";
    $stSql .= "         , PSR.origem_reservista                                                    \n";
    $stSql .= "         , CGM_FISICA_SERVIDOR.servidor_pis_pasep                                                  \n";
    $stSql .= "         , to_char(PSPP.dt_pis_pasep,'dd/mm/yyyy') as dt_pis_pasep                  \n";
    $stSql .= "         , CGM_CONJUGE.nom_cgm as nome_conjuge                                      \n";
    $stSql .= "         , CGM_CONJUGE.numcgm as numcgm_conjuge                                     \n";
    $stSql .= "      FROM pessoal.servidor PS                                                      \n";

    $stSql .= "      JOIN sw_cgm_pessoa_fisica CGM_FISICA_SERVIDOR                                 \n";
    $stSql .= "        ON CGM_FISICA_SERVIDOR.numcgm = PS.numcgm                                   \n";
    $stSql .= "      JOIN sw_cgm CGM_SERVIDOR                                                      \n";
    $stSql .= "        ON CGM_SERVIDOR.numcgm = CGM_FISICA_SERVIDOR.numcgm                         \n";
    $stSql .= "      JOIN cse.raca CR                                                              \n";
    $stSql .= "        ON CR.cod_raca  = PS.cod_raca                                               \n";
    $stSql .= " LEFT JOIN pessoal.servidor_reservista PSR                                          \n";
    $stSql .= "        ON PSR.cod_servidor = PS.cod_servidor                                       \n";
    $stSql .= " LEFT JOIN ( SELECT M_PSEC.*                                                        \n";
    $stSql .= "               FROM pessoal.servidor_conjuge M_PSEC                                 \n";
    $stSql .= "               JOIN (   SELECT cod_servidor                                         \n";
    $stSql .= "                             , MAX(timestamp) as timestamp                          \n";
    $stSql .= "                          FROM pessoal.servidor_conjuge                             \n";
    $stSql .= "                      GROUP BY cod_servidor                                         \n";
    $stSql .= "                    ) MAX_PSEC                                                      \n";
    $stSql .= "                 ON MAX_PSEC.cod_servidor = M_PSEC.cod_servidor                     \n";
    $stSql .= "                AND MAX_PSEC.timestamp    = M_PSEC.timestamp                        \n";
    $stSql .= "                AND M_PSEC.bo_excluido = false                                      \n";
    $stSql .= "           ) as PS_CONJUGE                                                          \n";
    $stSql .= "        ON PS_CONJUGE.cod_servidor = PS.cod_servidor                                \n";
    $stSql .= "        LEFT JOIN ( SELECT CGM_FISICA_CONJUGE.*                                     \n";
    $stSql .= "                         , CGM_CONJUGE.nom_cgm                                      \n";
    $stSql .= "                      FROM sw_cgm_pessoa_fisica CGM_FISICA_CONJUGE                  \n";
    $stSql .= "                      JOIN sw_cgm CGM_CONJUGE                                       \n";
    $stSql .= "                        ON CGM_CONJUGE.numcgm = CGM_FISICA_CONJUGE.numcgm           \n";
    $stSql .= "                  ) as CGM_CONJUGE                                                  \n";
    $stSql .= "        ON CGM_CONJUGE.numcgm = PS_CONJUGE.numcgm                                   \n";
    $stSql .= " LEFT JOIN ( SELECT PSC.*                                                           \n";
    $stSql .= "               FROM pessoal.servidor_cid as PSC                                     \n";
    $stSql .= "                  , (   SELECT cod_servidor                                         \n";
    $stSql .= "                             , max(timestamp) as timestamp                          \n";
    $stSql .= "                          FROM pessoal.servidor_cid                                 \n";
    $stSql .= "                      GROUP BY cod_servidor                                         \n";
    $stSql .= "                    ) as MAX_PSC                                                    \n";
    $stSql .= "              WHERE PSC.cod_servidor = MAX_PSC.cod_servidor                         \n";
    $stSql .= "                AND PSC.timestamp    = MAX_PSC.timestamp                            \n";
    $stSql .= "           ) as PSC                                                                 \n";
    $stSql .= "        ON PSC.cod_servidor = PS.cod_servidor                                       \n";
    $stSql .= " LEFT JOIN ( SELECT PSPP.*                                                          \n";
    $stSql .= "               FROM pessoal.servidor_pis_pasep as PSPP                              \n";
    $stSql .= "                  , (   SELECT cod_servidor                                         \n";
    $stSql .= "                             , max(timestamp) as timestamp                          \n";
    $stSql .= "                          FROM pessoal.servidor_pis_pasep                           \n";
    $stSql .= "                      GROUP BY cod_servidor) as MAX_PSPP                            \n";
    $stSql .= "              WHERE PSPP.cod_servidor = MAX_PSPP.cod_servidor                       \n";
    $stSql .= "                AND PSPP.timestamp    = MAX_PSPP.timestamp                          \n";
    $stSql .= "           ) as PSPP                                                                \n";
    $stSql .= "        ON PSPP.cod_servidor = PS.cod_servidor                                      \n";

    return $stSql;
}

function montaConsultaCGMServidor()
{
    $stSql  = " Select                                                                      \n";
    $stSql .= "     numcgm                                                                  \n";
    $stSql .= " from                                                                        \n";
    $stSql .= "     pessoal.servidor                                                        \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaConsultaCGMServidor.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaCGMServidor(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    //$stOrdem = ' order by ' . $stOrdem;
    $stSql = $this->montaConsultaCGMServidor().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaRelacionamentoRelatorio.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoRelatorio().$stFiltro.$stOrdem;
    $this->setDebug( $stSql ); 
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoRelatorio()
{
    $stSql  = " SELECT DISTINCT 
                        servidor.*                                                                                            
                        , sw_cgm.*                                                                                              
                        , contrato.registro
                        , contrato.cod_contrato
                        , sw_escolaridade.descricao as escolaridade
                        , sw_cgm_pessoa_fisica.cpf                                                                              
                        , sw_cgm_pessoa_fisica.cod_categoria_cnh
                        , TO_CHAR(sw_cgm_pessoa_fisica.dt_validade_cnh, 'dd/mm/yyyy') as dt_validade_cnh
                        , sw_categoria_habilitacao.nom_categoria
                        , sw_cgm_pessoa_fisica.orgao_emissor
                        , TO_CHAR(sw_cgm_pessoa_fisica.dt_emissao_rg, 'dd/mm/yyyy') AS dt_emissao_rg
                        , sw_cgm_pessoa_fisica.num_cnh                                                                          
                        , sw_cgm_pessoa_fisica.rg                                                                               
                        , sw_cgm_pessoa_fisica.sexo                                                                             
                        , to_char(sw_cgm_pessoa_fisica.dt_nascimento,'dd/mm/yyyy') as dt_nascimento                             
                        , sw_municipio.nom_municipio                                                                            
                        , sw_uf.sigla_uf                                                                                        
                        , sw_pais.nom_pais                                                                                      
                        , sw_pais.nacionalidade                                                                                 
                        , to_char(servidor_pis_pasep.dt_pis_pasep,'dd/mm/yyyy') as dt_pis_pasep                                 
                        , sw_cgm_pessoa_fisica.servidor_pis_pasep                                                               
                        , servidor_reservista.nr_carteira_res                                                                   
                        , servidor_reservista.cat_reservista                                                                    
                        , servidor_reservista.origem_reservista                                                                 
                        , pessoal_servidor_conjuge.nome_conjuge                                                                 
                        , pessoal_cid.sigla                                                                                     
                        , pessoal_cid.descricao
                        , conselho.sigla as sigla_conselho
                        , contrato_servidor_conselho.nr_conselho
                        , TO_CHAR(contrato_servidor_conselho.dt_validade, 'dd/mm/yyyy') AS dt_validade_conselho
                        , contrato_servidor_padrao.cod_padrao
                        , contrato_servidor_forma_pagamento.cod_forma_pagamento
                        , contrato_servidor.cod_tipo_pagamento
                        , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as lotacao
                        , local.descricao as local
                        , orgao.cod_orgao
                        , contrato_servidor_local.cod_local
                        , contrato_servidor_nomeacao_posse.dt_posse
                        , local.descricao AS filtro_local
                        , contrato_servidor_situacao.situacao
                FROM sw_cgm 

          INNER JOIN sw_cgm_pessoa_fisica 
                  ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

          INNER JOIN sw_municipio
                  ON sw_municipio.cod_municipio  = sw_cgm.cod_municipio
                 AND sw_municipio.cod_uf         = sw_cgm.cod_uf                

          INNER JOIN sw_uf
                 ON sw_uf.cod_uf = sw_municipio.cod_uf
          
          INNER JOIN sw_pais
                  ON sw_pais.cod_pais = sw_uf.cod_pais
          
          INNER JOIN pessoal.servidor
                  ON servidor.numcgm = sw_cgm_pessoa_fisica.numcgm
          
          INNER JOIN pessoal.servidor_contrato_servidor
                  ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
          
          INNER JOIN pessoal.contrato_servidor
                  ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato
          
          INNER JOIN pessoal.contrato
                  ON contrato.cod_contrato = contrato_servidor.cod_contrato
          
           LEFT JOIN sw_escolaridade
                  ON sw_escolaridade.cod_escolaridade = sw_cgm_pessoa_fisica.cod_escolaridade
          
           LEFT JOIN ( SELECT  servidor_pis_pasep.dt_pis_pasep                                                              
                            , servidor_pis_pasep.cod_servidor                                                              
                         FROM pessoal.servidor_pis_pasep                                                                   
                            , ( SELECT cod_servidor                                                                      
                                        , max(timestamp) as timestamp                                                           
                                FROM pessoal.servidor_pis_pasep                                                        
                                GROUP BY cod_servidor
                            ) as max_servidor_pis_pasep                                           
                            WHERE servidor_pis_pasep.cod_servidor = max_servidor_pis_pasep.cod_servidor                        
                            AND servidor_pis_pasep.timestamp    = max_servidor_pis_pasep.timestamp
                ) as servidor_pis_pasep    
                  ON servidor_pis_pasep.cod_servidor = servidor.cod_servidor                                               
           
           LEFT JOIN pessoal.servidor_reservista                                                                           
                  ON servidor_reservista.cod_servidor = servidor.cod_servidor                                              
           
           LEFT JOIN ( SELECT  servidor_conjuge.cod_servidor                                                                
                                    ,sw_cgm.numcgm  as numcgm_conjuge                                                             
                                    ,sw_cgm.nom_cgm as nome_conjuge                                                               
                            FROM pessoal.servidor_conjuge                                                                     
                            , ( SELECT  cod_servidor                                                                      
                                        ,max(timestamp) as timestamp                                                       
                                FROM pessoal.servidor_conjuge                                                          
                                GROUP BY cod_servidor
                            ) as max_servidor_conjuge                                             
                            , sw_cgm                                                                                       
                            WHERE servidor_conjuge.cod_servidor = max_servidor_conjuge.cod_servidor                            
                            AND servidor_conjuge.timestamp = max_servidor_conjuge.timestamp                                  
                            AND servidor_conjuge.numcgm = sw_cgm.numcgm
                ) as pessoal_servidor_conjuge                         
                  ON pessoal_servidor_conjuge.cod_servidor = servidor.cod_servidor                                         
           
           LEFT JOIN ( SELECT  pessoal.servidor_cid.cod_servidor                                                            
                                    ,pessoal.cid.sigla                                                                            
                                    ,pessoal.cid.descricao                                                                        
                            FROM pessoal.servidor_cid
                            , ( SELECT  cod_servidor                                                                      
                                        , max(timestamp) as timestamp                                                       
                                FROM pessoal.servidor_cid                                                              
                                GROUP BY cod_servidor
                            ) as max_servidor_cid
                            , pessoal.cid
                            WHERE servidor_cid.cod_servidor = max_servidor_cid.cod_servidor                                    
                            AND servidor_cid.timestamp = max_servidor_cid.timestamp                                          
                            AND servidor_cid.cod_cid = cid.cod_cid
                ) as pessoal_cid                                           
                  ON pessoal_cid.cod_servidor = servidor.cod_servidor
           
           LEFT JOIN pessoal.contrato_servidor_conselho
                  ON contrato_servidor_conselho.cod_contrato = contrato_servidor.cod_contrato
           
           LEFT JOIN pessoal.conselho
                  ON conselho.cod_conselho = contrato_servidor_conselho.cod_conselho
           
          INNER JOIN pessoal.contrato_servidor_orgao as pcso
                  ON contrato_servidor.cod_contrato = pcso.cod_contrato
                 AND pcso.timestamp = (  select timestamp
                                            from pessoal.contrato_servidor_orgao
                                            where cod_contrato = contrato_servidor.cod_contrato
                                            order by timestamp desc
                                            limit 1)
          INNER JOIN organograma.orgao
                  ON pcso.cod_orgao = orgao.cod_orgao
               
          INNER JOIN organograma.vw_orgao_nivel
                  ON orgao.cod_orgao = vw_orgao_nivel.cod_orgao
          
           LEFT JOIN pessoal.contrato_servidor_local
                  ON contrato_servidor_local.cod_contrato = contrato_servidor.cod_contrato
          
           LEFT JOIN organograma.local as local
                  ON contrato_servidor_local.cod_local = local.cod_local
          
           LEFT JOIN pessoal.atributo_contrato_servidor_valor
                  ON atributo_contrato_servidor_valor.cod_contrato = contrato_servidor.cod_contrato
          
           LEFT JOIN administracao.atributo_dinamico
                  ON atributo_dinamico.cod_modulo         = atributo_contrato_servidor_valor.cod_modulo
                 AND atributo_dinamico.cod_cadastro      = atributo_contrato_servidor_valor.cod_cadastro
                 AND atributo_dinamico.cod_atributo      = atributo_contrato_servidor_valor.cod_atributo
          
          INNER JOIN pessoal.contrato_servidor_padrao
                  ON contrato_servidor.cod_contrato = contrato_servidor_padrao.cod_contrato
                 AND contrato_servidor_padrao.timestamp = ( select timestamp
                                                               from pessoal.contrato_servidor_padrao
                                                               where cod_contrato = contrato_servidor.cod_contrato
                                                               order by timestamp desc
                                                               limit 1)
          
          INNER JOIN pessoal.contrato_servidor_situacao
                  ON contrato_servidor.cod_contrato = contrato_servidor_situacao.cod_contrato
                 AND contrato_servidor_situacao.timestamp = ( select timestamp
                                                               from pessoal.contrato_servidor_situacao
                                                               where cod_contrato = contrato_servidor.cod_contrato
                                                               order by timestamp desc
                                                               limit 1)

          INNER JOIN pessoal.contrato_servidor_forma_pagamento
                  ON contrato_servidor_forma_pagamento.cod_contrato = contrato_servidor.cod_contrato
          
          INNER JOIN pessoal.contrato_servidor_nomeacao_posse
                  ON contrato_servidor_nomeacao_posse.cod_contrato = contrato_servidor.cod_contrato
                 AND contrato_servidor_nomeacao_posse.timestamp = ( SELECT MAX(timestamp)
                                                                      FROM pessoal.contrato_servidor_nomeacao_posse AS csnp
                                                                     WHERE csnp.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato)
          
          INNER JOIN sw_categoria_habilitacao
                  ON sw_categoria_habilitacao.cod_categoria = sw_cgm_pessoa_fisica.cod_categoria_cnh
    ";
    
    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaConsultaRegistrosServidor.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRegistrosServidor(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaConsultaRegistrosServidor().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaConsultaRegistrosServidor()
{
    $stSql  = " SELECT                                        
                     ps.cod_servidor,                          
                     pc.registro,                              
                     pc.cod_contrato
                 FROM                                          
                     pessoal.servidor as ps,                   
                     pessoal.servidor_contrato_servidor as sc, 
                     pessoal.contrato_servidor as cs,          
                     pessoal.contrato as pc                    
                 WHERE                                         
                         ps.cod_servidor = sc.cod_servidor     
                     AND sc.cod_contrato = cs.cod_contrato     
                     AND cs.cod_contrato = pc.cod_contrato ";

    return $stSql;
}
function recuperaServidoresExportaTCMBA(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : " ORDER BY nom_cgm";
    $stSql = $this->montaRecuperaServidoresExportaTCMBA().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaServidoresExportaTCMBA()
{
    $stSql  = "   SELECT nom_cgm                                                                                                         \n";
    $stSql .= "        , cpf                                                                                                             \n";
    $stSql .= "        , sexo                                                                                                            \n";
    $stSql .= "        , to_char(dt_nascimento,'dd/mm/yyyy') as dt_nascimento                                                            \n";
    $stSql .= "        , contrato_servidor_sub_divisao_funcao.cod_sub_divisao                                                            \n";
    $stSql .= "        , to_char(contrato_servidor_nomeacao_posse.dt_admissao,'dd/mm/yyyy') as dt_admissao                               \n";
    $stSql .= "        , to_char(contrato_servidor_nomeacao_posse.dt_admissao,'mm/yyyy') as dt_admissao_competencia                      \n";
    $stSql .= "        , to_char(contrato_servidor_caso_causa.dt_rescisao,'mm/yyyy') as dt_rescisao                                      \n";
    $stSql .= "        , contrato.*                                                                                                      \n";
    $stSql .= "        , contrato_servidor_funcao.cod_cargo                                                                              \n";
    $stSql .= "        , salario                                                                                                         \n";
    $stSql .= "        , contrato_servidor_orgao.cod_orgao                                                                               \n";
    $stSql .= "        , norma.num_norma                                                                                                 \n";
    $stSql .= "        , upper(norma.descricao) as norma                                                                                 \n";
    $stSql .= "     FROM pessoal.servidor                                                                      \n";
    $stSql .= "        , pessoal.servidor_contrato_servidor                                                    \n";
    $stSql .= "        , pessoal.contrato                                                                      \n";
    $stSql .= "        , pessoal.contrato_servidor                                                             \n";
    $stSql .= ( $this->getDado("stJoin") != "" ) ? $this->getDado("stJoin") : "";
    $stSql .= "LEFT JOIN pessoal.contrato_servidor_caso_causa                                                  \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = contrato_servidor_caso_causa.cod_contrato                                      \n";
    $stSql .= "        , pessoal.contrato_servidor_sub_divisao_funcao                                          \n";
    $stSql .= "        , (   SELECT cod_contrato                                                                                         \n";
    $stSql .= "                   , max(timestamp) as timestamp                                                                          \n";
    $stSql .= "                FROM pessoal.contrato_servidor_sub_divisao_funcao                               \n";
    $stSql .= "            GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao                                            \n";
    $stSql .= "        , pessoal.contrato_servidor_nomeacao_posse                                              \n";
    $stSql .= "        , (   SELECT cod_contrato                                                                                         \n";
    $stSql .= "                   , max(timestamp) as timestamp                                                                          \n";
    $stSql .= "                FROM pessoal.contrato_servidor_nomeacao_posse                                   \n";
    $stSql .= "            GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse                                                \n";
    $stSql .= "        , pessoal.contrato_servidor_funcao                                                      \n";
    $stSql .= "        , (   SELECT cod_contrato                                                                                         \n";
    $stSql .= "                   , max(timestamp) as timestamp                                                                          \n";
    $stSql .= "                FROM pessoal.contrato_servidor_funcao                                           \n";
    $stSql .= "            GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                        \n";
    $stSql .= "        , pessoal.contrato_servidor_salario                                                     \n";
    $stSql .= "        , (   SELECT cod_contrato                                                                                         \n";
    $stSql .= "                   , max(timestamp) as timestamp                                                                          \n";
    $stSql .= "                FROM pessoal.contrato_servidor_salario                                          \n";
    $stSql .= "            GROUP BY cod_contrato) as max_contrato_servidor_salario                                                       \n";
    $stSql .= "        , pessoal.contrato_servidor_orgao                                                       \n";
    $stSql .= "        , (   SELECT cod_contrato                                                                                         \n";
    $stSql .= "                   , max(timestamp) as timestamp                                                                          \n";
    $stSql .= "                FROM pessoal.contrato_servidor_orgao                                            \n";
    $stSql .= "            GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                         \n";
    $stSql .= "        , normas.norma                                                                                                    \n";
    $stSql .= "        , sw_cgm                                                                                                          \n";
    $stSql .= "        , sw_cgm_pessoa_fisica                                                                                            \n";
    $stSql .= "    WHERE servidor.numcgm = sw_cgm.numcgm                                                                                 \n";
    $stSql .= "      AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                   \n";
    $stSql .= "      AND servidor.cod_servidor = servidor_contrato_servidor.cod_servidor                                                 \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_contrato = contrato.cod_contrato                                                 \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato                                        \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato                     \n";
    $stSql .= "      AND contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato       \n";
    $stSql .= "      AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp             \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato                         \n";
    $stSql .= "      AND contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato               \n";
    $stSql .= "      AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp                     \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato                                 \n";
    $stSql .= "      AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                               \n";
    $stSql .= "      AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp                                     \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_contrato = contrato_servidor_salario.cod_contrato                                \n";
    $stSql .= "      AND contrato_servidor_salario.cod_contrato = max_contrato_servidor_salario.cod_contrato                             \n";
    $stSql .= "      AND contrato_servidor_salario.timestamp = max_contrato_servidor_salario.timestamp                                   \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                                  \n";
    $stSql .= "      AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                 \n";
    $stSql .= "      AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                                       \n";
    $stSql .= "      AND contrato_servidor.cod_norma = norma.cod_norma                                                                   \n";

    return $stSql;
}

function recuperaServidoresTCMBACompetencia(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    //$stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : " ORDER BY nom_cgm";
    $stSql = $this->montaRecuperaServidoresTCMBACompetencia().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaServidoresTCMBACompetencia()
{
$stSql  = "SELECT servidor_contrato_servidor.cod_contrato                                                                                                                       \n";
$stSql .= "     , servidor.cod_servidor                                                                                                                                         \n";
$stSql .= "     , to_char(contrato_servidor_nomeacao_posse.dt_admissao,'mm/yyyy') as admissao                                                                                \n";
$stSql .= "     , to_char(contrato_servidor_caso_causa.dt_rescisao,'mm/yyyy') as rescisao                                                                                    \n";
$stSql .= "     , cod_regime                                                                                                                                                    \n";
$stSql .= "     , cod_sub_divisao                                                                                                                                               \n";
$stSql .= "  FROM pessoal.servidor                                                                                                                                              \n";
$stSql .= "       LEFT JOIN pessoal.servidor_contrato_servidor                                                                                                                  \n";
$stSql .= "         ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                          \n";
$stSql .= "       LEFT JOIN (SELECT contrato_servidor_nomeacao_posse.*                                                                                                          \n";
$stSql .= "                    FROM (SELECT cod_contrato                                                                                                                        \n";
$stSql .= "           	                   , max(timestamp) as timestamp                                                                                                        \n";
$stSql .= "      	                    FROM pessoal.contrato_servidor_nomeacao_posse                                                                                           \n";
$stSql .= "       		              GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse                                                                            \n";
$stSql .= "       		           , pessoal.contrato_servidor_nomeacao_posse                                                                                                   \n";
$stSql .= "       	           WHERE contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp                                                \n";
$stSql .= "       	             AND contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato ) as contrato_servidor_nomeacao_posse    \n";
$stSql .= "         ON contrato_servidor_nomeacao_posse.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                  \n";
$stSql .= "       LEFT JOIN (SELECT contrato_servidor_caso_causa.*                                                                                                              \n";
$stSql .= "                    FROM (SELECT cod_contrato                                                                                                                        \n";
$stSql .= "           	                   , max(timestamp) as timestamp                                                                                                        \n";
$stSql .= "      	                    FROM pessoal.contrato_servidor_caso_causa                                                                                               \n";
$stSql .= "       		              GROUP BY cod_contrato) as max_contrato_servidor_caso_causa                                                                                \n";
$stSql .= "       		           , pessoal.contrato_servidor_caso_causa                                                                                                       \n";
$stSql .= "       	           WHERE contrato_servidor_caso_causa.timestamp    = max_contrato_servidor_caso_causa.timestamp                                                     \n";
$stSql .= "       	             AND contrato_servidor_caso_causa.cod_contrato = max_contrato_servidor_caso_causa.cod_contrato ) as contrato_servidor_caso_causa                \n";
$stSql .= "         ON contrato_servidor_caso_causa.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                      \n";
$stSql .= "       LEFT JOIN ( SELECT contrato_servidor_regime_funcao.*                                                                                                          \n";
$stSql .= "                     FROM pessoal.contrato_servidor_regime_funcao                                                                                                    \n";
$stSql .= "                        , (SELECT cod_contrato                                                                                                                       \n";
$stSql .= "                                , max(timestamp) as timestamp                                                                                                        \n";
$stSql .= "                             FROM pessoal.contrato_servidor_regime_funcao                                                                                            \n";
$stSql .= "                           GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao                                                                             \n";
$stSql .= "                    WHERE contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato                                            \n";
$stSql .= "                      AND contrato_servidor_regime_funcao.timestamp = max_contrato_servidor_regime_funcao.timestamp) as contrato_servidor_regime_funcao              \n";
$stSql .= "         ON servidor_contrato_servidor.cod_contrato = contrato_servidor_regime_funcao.cod_contrato                                                                   \n";
$stSql .= "       LEFT JOIN ( SELECT contrato_servidor_sub_divisao_funcao.*                                                                                                     \n";
$stSql .= "                     FROM pessoal.contrato_servidor_sub_divisao_funcao                                                                                               \n";
$stSql .= "                        , (SELECT cod_contrato                                                                                                                       \n";
$stSql .= "                                , max(timestamp) as timestamp                                                                                                        \n";
$stSql .= "                             FROM pessoal.contrato_servidor_sub_divisao_funcao                                                                                       \n";
$stSql .= "                            GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao                                                                       \n";
$stSql .= "                    WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato                                  \n";
$stSql .= "                      AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp)  as contrato_servidor_sub_divisao_funcao   \n";
$stSql .= "         ON servidor_contrato_servidor.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato                                                              \n";
$stSql .= "WHERE 1 = 1                                                                                                                                                          \n";

return $stSql;
}

function recuperaServidorEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaServidorEsfinge().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaServidorEsfinge()
{
    $stSql = "
select contrato.registro
      ,sw_cgm.nom_cgm
      ,to_char(sw_cgm_pessoa_fisica.dt_nascimento, 'dd/mm/yyyy') as dt_nascimento
      ,servidor.nome_mae
      ,servidor.nome_pai
      ,sw_cgm_pessoa_fisica.cpf
      ,sw_cgm_pessoa_fisica.rg
      ,servidor.nr_titulo_eleitor
      ,servidor_reservista.nr_carteira_res
      ,substring(sw_cgm_pessoa_fisica.servidor_pis_pasep from 1 for 12) || substring(sw_cgm_pessoa_fisica.servidor_pis_pasep from 14 for 1) as servidor_pis_pasep
      ,case sw_cgm_pessoa_fisica.sexo
         when 'm' then 1
         when 'f' then 2
       end as cod_sexo
from pessoal.contrato
join pessoal.contrato_servidor
  on contrato_servidor.cod_contrato = contrato.cod_contrato
join pessoal.servidor_contrato_servidor
  on servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
join pessoal.servidor
  on servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
join (select servidor_pis_pasep.cod_servidor
       from  pessoal.servidor_pis_pasep
        ,(select servidor_pis_pasep.cod_servidor
                ,max(servidor_pis_pasep.timestamp) as timestamp
           from pessoal.servidor_pis_pasep
          where servidor_pis_pasep.timestamp < to_date('".$this->getDado("dt_final")."', 'dd/mm/yyyy')
      group by servidor_pis_pasep.cod_servidor) as max_servidor_pis_pasep
      
 where max_servidor_pis_pasep.cod_servidor = servidor_pis_pasep.cod_servidor
 and max_servidor_pis_pasep.timestamp = servidor_pis_pasep.timestamp) as servidor_pis_pasep
  on servidor_pis_pasep.cod_servidor = servidor.cod_servidor
join pessoal.servidor_reservista
  on servidor_reservista.cod_servidor = servidor.cod_servidor
join sw_cgm
  on servidor.numcgm = sw_cgm.numcgm
join sw_cgm_pessoa_fisica
  on sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
";
 
    return $stSql;
}

function montaRecuperaServidorPessoaFisica()
{
    $stSql  = "   SELECT servidor.*                                                                                            \n";
    $stSql .= "        , sw_cgm.*                                                                                              \n";
    $stSql .= "        , sw_cgm_pessoa_fisica.*                                                                                \n";
    $stSql .= "     FROM sw_cgm                                                                                                \n";
    $stSql .= "        , sw_cgm_pessoa_fisica                                                                                  \n";
    $stSql .= "        , pessoal.servidor                                                            \n";
    $stSql .= "    WHERE servidor.numcgm = sw_cgm.numcgm                                                                       \n";
    $stSql .= "      AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                         \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaServidorPessoaFisica.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaServidorPessoaFisica(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    //$stOrdem = ' order by ' . $stOrdem;
    $stSql = $this->montaRecuperaServidorPessoaFisica().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaServidorRemessaBancaria.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/

function recuperaServidorRemessaBanPara(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaServidorRemessaBanPara",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaServidorRemessaBanPara()
{
    $stSql  = "  SELECT * FROM (\n";
    $stSql .= "         SELECT UPPER(sw_cgm.nom_cgm) as nom_cgm\n";
    $stSql .= "              , sw_cgm.numcgm\n";
    $stSql .= "              , UPPER(sw_cgm.bairro) as bairro\n";
    $stSql .= "              , sw_cgm.cep\n";
    $stSql .= "              , sw_cgm.cod_municipio\n";
    $stSql .= "              , sw_cgm.cod_pais\n";
    $stSql .= "              , sw_cgm.cod_uf\n";
    $stSql .= "              , UPPER(sw_cgm.complemento) as complemento\n";
    $stSql .= "              , sw_cgm.dt_cadastro\n";
    $stSql .= "              , sw_cgm.fone_residencial\n";
    $stSql .= "              , UPPER(sw_cgm.logradouro) as logradouro\n";
    $stSql .= "              , UPPER(sw_cgm.numero) as numero\n";
    $stSql .= "              , sw_cgm_pessoa_fisica.cod_escolaridade\n";
    $stSql .= "              , sw_cgm_pessoa_fisica.cod_uf_orgao_emissor\n";
    $stSql .= "              , sw_cgm_pessoa_fisica.cpf\n";
    $stSql .= "              , sw_cgm_pessoa_fisica.dt_nascimento\n";
    $stSql .= "              , UPPER(sw_cgm_pessoa_fisica.orgao_emissor) as orgao_emissor\n";
    $stSql .= "              , sw_cgm_pessoa_fisica.rg\n";
    $stSql .= "              , UPPER(sw_cgm_pessoa_fisica.sexo) as sexo\n";
    $stSql .= "              , ( SELECT UPPER(nom_municipio) as nom_municipio FROM sw_municipio WHERE cod_municipio = sw_cgm.cod_municipio and cod_uf = sw_cgm.cod_uf ) as cidade\n";
    $stSql .= "              , ( SELECT sigla_uf FROM sw_uf WHERE cod_uf = sw_cgm.cod_uf and cod_pais = sw_cgm.cod_pais ) as uf\n";
    $stSql .= "              , ( SELECT sigla_uf FROM sw_uf WHERE cod_uf = sw_cgm_pessoa_fisica.cod_uf_orgao_emissor and cod_pais = sw_cgm.cod_pais ) as uf_orgao_emissor\n";
    $stSql .= "              , contrato_servidor.cod_estado_civil\n";
    $stSql .= "              , UPPER(contrato_servidor.nome_mae) as nome_mae\n";
    $stSql .= "              , UPPER(contrato_servidor.nome_pai) as nome_pai\n";
    $stSql .= "              , contrato_servidor.cod_contrato\n";
    $stSql .= "              , contrato_servidor.registro\n";
    $stSql .= "              , contrato_servidor.num_agencia\n";
    $stSql .= "              , contrato_servidor.cod_agencia\n";
    $stSql .= "              , contrato_servidor.num_banco\n";
    $stSql .= "              , contrato_servidor.cod_banco\n";
    $stSql .= "              , contrato_servidor.nr_conta\n";
    $stSql .= "              , contrato_servidor.cod_orgao\n";
    $stSql .= "              , contrato_servidor.cod_local\n";
    $stSql .= "              , contrato_servidor.dt_admissao\n";
    $stSql .= "              , contrato_servidor.numcgm_conjuge\n";
    $stSql .= "              , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = contrato_servidor.numcgm_conjuge) as nom_cgm_conjuge\n";
    $stSql .= "           FROM (\n";
    $stSql .= "                   SELECT cod_contrato \n";
    $stSql .= "                        , numcgm\n";
    $stSql .= "                        , nom_cgm \n";
    $stSql .= "                        , registro\n";
    $stSql .= "                        , nome_pai\n";
    $stSql .= "                        , nome_mae\n";
    $stSql .= "                        , nr_conta_salario as nr_conta \n";
    $stSql .= "                        , num_banco_salario as num_banco \n";
    $stSql .= "                        , cod_banco_salario as cod_banco\n";
    $stSql .= "                        , num_agencia_salario as num_agencia\n";
    $stSql .= "                        , cod_agencia_salario as cod_agencia\n";
    $stSql .= "                        , cod_orgao \n";
    $stSql .= "                        , cod_local \n";
    $stSql .= "                        , cod_estado_civil\n";
    $stSql .= "                        , dt_admissao\n";
    $stSql .= "                        , numcgm_conjuge\n";
    $stSql .= "                     FROM recuperarContratoServidor('cgm,cs,o,l,con,anp','".Sessao::getEntidade()."',".($this->getDado('inCodPeriodoMovimentacao')?$this->getDado('inCodPeriodoMovimentacao'):0).",'geral','','".Sessao::getExercicio()."')\n";
    $stSql .= "                ) as contrato_servidor\n";
    $stSql .= "     INNER JOIN sw_cgm\n";
    $stSql .= "             ON contrato_servidor.numcgm = sw_cgm.numcgm\n";
    $stSql .= "     INNER JOIN sw_cgm_pessoa_fisica\n";
    $stSql .= "             ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm\n";
    $stSql .= "              ) AS contrato\n";

    return $stSql;
}

/**
 * Recupera os dados para o MANAD (Manual Normativo de Arquivos Digitais)
 *
 * @access Public
 * @param  Object  $rsRecordSet Objeto RecordSet
 * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
 * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
 * @param  Boolean $boTransacao
 * @return Object  Objeto Erro
 */
function recuperaDadosMANAD(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if (trim($stFiltro)) {
        $stFiltro = (strpos($stFiltro,"WHERE") === false) ? " WHERE $stFiltro " : $stFiltro;
    }

    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY") === false) ? " ORDER BY $stOrdem " : $stOrdem;
    }

    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecurepaDadosMANAD()
{
    $stSql .= "";

    return $stSql;
}

}
