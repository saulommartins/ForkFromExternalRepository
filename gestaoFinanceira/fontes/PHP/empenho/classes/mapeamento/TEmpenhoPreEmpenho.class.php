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
    * Classe de mapeamento da tabela EMPENHO.PRE_EMPENHO
    * Data de Criação: 30/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.02, uc-02.03.03, uc-02.08.02

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TEmpenhoPreEmpenho extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('empenho.pre_empenho');
    
        $this->setCampoCod('cod_pre_empenho');
        $this->setComplementoChave('exercicio');
    
        $this->AddCampo('exercicio','char',true,'04',true,true);
        $this->AddCampo('cod_pre_empenho','	integer',true,'',true,false);
        $this->AddCampo('cgm_beneficiario','integer',true,'',false,false);
        $this->AddCampo('descricao','varchar',true,'160',false,false);
        $this->AddCampo('cod_tipo','integer',true,'',false,true);
        $this->AddCampo('cod_historico','integer',true,'',false,true);
        $this->AddCampo('cgm_usuario','integer',true,'',false,false);
        $this->AddCampo('implantado' ,'boolean',false,'',false,false);
    
    }

    /**
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaRelatorioAutorizacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRelatorioAutorizacao();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    /**
        * Seta dados para fazer o recuperaRelacionamento
        * @access Public
        * @return String $stSql
    */
    public function montaRelatorioAutorizacao()
    {
        $stSql  = "
                  SELECT tabela.*
                       , publico.fn_mascara_dinamica( ( SELECT valor FROM administracao.configuracao WHERE parametro = 'masc_despesa' AND exercicio ='".$this->getDado( "exercicio" )."' )
                                                    , tabela.num_orgao
                                                      ||'.'||tabela.num_unidade
                                                      ||'.'||tabela.cod_funcao
                                                      ||'.'||tabela.cod_subfuncao
                                                      ||'.'||tabela.num_programa
                                                      ||'.'||tabela.num_acao
                                                      ||'.'||replace(cd.cod_estrutural,'.','')
                                                    ) AS dotacao
                       , cd.descricao AS nom_conta
                       , tabela.nom_pao
                       , tabela.cod_recurso
                       , tabela.nom_recurso
                    FROM (SELECT tabela.*
                               , CGM.nom_cgm as nom_entidade
                               , de.cod_funcao
                               , de.cod_subfuncao
                               , de.cod_programa
                               , de.num_pao
                               , pao.nom_pao
                               , rec.cod_recurso
                               , rec.nom_recurso
                               , de.cod_despesa as dotacao_reduzida
                               , CAST(ppa.programa.num_programa AS varchar) as num_programa
                               , CAST(ppa.acao.num_acao AS VARCHAR) as num_acao
                            FROM (SELECT pe.cod_pre_empenho
                                       , pe.descricao
                                       , aa.motivo
                                       , TO_CHAR(aa.dt_anulacao,'dd/mm/yyyy') as dt_anulacao
                                       , ae.cod_entidade
                                       , ae.cod_autorizacao
                                       , to_char(ae.dt_autorizacao, 'dd/mm/yyyy')  as dt_autorizacao
                                       , to_char(ae.dt_autorizacao, 'dd/mm/yyyy')  as dt_autorizacao
                                       , it.vl_total                 as valor_total
                                       , (it.vl_total/it.quantidade) as valor_unitario
                                       , it.num_item
                                       , it.quantidade               as quantidade
                                       , it.nom_unidade
                                       , it.sigla_unidade as simbolo
                                       , it.nom_item
                                       , it.complemento
                                       , cg.numcgm as num_fornecedor
                                       , cg.nom_cgm
                                       , oe.numcgm
                                       , CASE WHEN pf.numcgm IS NOT NULL
                                              THEN pf.cpf
                                              ELSE pj.cnpj
                                          END as cpf_cnpj
                                       , cg.tipo_logradouro||' '||cg.logradouro||' '||cg.numero||' '||cg.complemento as endereco
                                       , mu.nom_municipio
                                       , CASE WHEN cg.fone_residencial IS NOT NULL
                                              THEN cg.fone_residencial
                                              ELSE cg.fone_comercial
                                          END as telefone
                                       , uf.sigla_uf
                                       , pd.cod_despesa
                                       , pd.cod_conta
                                       , ae.exercicio
                                       , ae.num_orgao
                                       , oo.nom_orgao as num_nom_orgao
                                       , TO_CHAR(ore.dt_validade_final ,'dd/mm/yyyy') as dt_validade_final
                                       , ou.num_unidade
                                       , ou.nom_unidade  as num_nom_unidade
                                    FROM empenho.pre_empenho          as pe
                               LEFT JOIN empenho.autorizacao_empenho as ae
                                      ON ae.cod_pre_empenho = pe.cod_pre_empenho
                                     AND ae.exercicio       = pe.exercicio
                               LEFT JOIN empenho.autorizacao_reserva as ar
                                      ON ar.cod_autorizacao = ae.cod_autorizacao
                                     AND ar.exercicio       = ae.exercicio
                                     AND ar.cod_entidade    = ae.cod_entidade
                               LEFT JOIN orcamento.reserva as ore
                                      ON ore.cod_reserva = ar.cod_reserva
                                     AND ore.exercicio   = ar.exercicio
                               LEFT JOIN empenho.autorizacao_anulada as aa
                                      ON ae.cod_entidade     = aa.cod_entidade
                                     AND ae.exercicio        = aa.exercicio
                                     AND ae.cod_autorizacao  = aa.cod_autorizacao
                               LEFT JOIN empenho.pre_empenho_despesa as pd
                                      ON pe.cod_pre_empenho   = pd.cod_pre_empenho
                                     AND pe.exercicio        = pd.exercicio
                                       , empenho.item_pre_empenho     as it
                                       , orcamento.unidade            as ou
                                       , orcamento.orgao              as oo
                                       , orcamento.entidade           as oe
                                       , administracao.unidade_medida as um
                                       , sw_cgm                       as cg
                               LEFT JOIN sw_cgm_pessoa_fisica         as pf
                                      ON cg.numcgm = pf.numcgm
                               LEFT JOIN sw_cgm_pessoa_juridica       as pj
                                      ON cg.numcgm = pj.numcgm
                                       , sw_municipio                 as mu
                                       , sw_uf                        as uf
                                   WHERE pe.cod_pre_empenho  = it.cod_pre_empenho
                                     AND pe.exercicio        = it.exercicio
                                     AND pe.cod_pre_empenho  = ae.cod_pre_empenho
                                     AND pe.exercicio        = ae.exercicio
                                     --Orgão
                                     AND ae.num_orgao        = ou.num_orgao
                                     AND ae.num_unidade      = ou.num_unidade
                                     AND ae.exercicio        = ou.exercicio
                                     AND ou.num_orgao        = oo.num_orgao
                                     AND ou.exercicio        = oo.exercicio
                                     --Unidade
                                     AND ae.num_orgao        = ou.num_orgao
                                     AND ae.num_unidade      = ou.num_unidade
                                     AND ae.exercicio        = ou.exercicio
                                     -- Entidade
                                     AND ae.cod_entidade = OE.cod_entidade
                                     AND ae.exercicio    = OE.exercicio
                                     --CGM
                                     AND pe.cgm_beneficiario = cg.numcgm
                                     --Municipio
                                     AND cg.cod_municipio    = mu.cod_municipio
                                     AND cg.cod_uf           = mu.cod_uf
                                     --Uf
                                     AND mu.cod_uf           = uf.cod_uf
                                     --Unidade Medida
                                     AND it.cod_unidade      = um.cod_unidade
                                     AND it.nom_unidade      = um.nom_unidade
                                     " . $this->getDado( "filtro" ) . "
                                     ORDER BY ae.cod_pre_empenho, it.num_item
                                 ) as tabela
                       LEFT JOIN orcamento.despesa as de
                              ON de.cod_despesa = tabela.cod_despesa
                             AND de.exercicio   = tabela.exercicio
                       LEFT JOIN orcamento.pao as pao
                              ON de.num_pao   = pao.num_pao
                             AND de.exercicio = pao.exercicio
                       LEFT JOIN orcamento.recurso as rec
                              ON de.cod_recurso = rec.cod_recurso
                             AND de.exercicio   = rec.exercicio
                       LEFT JOIN orcamento.programa_ppa_programa
                              ON programa_ppa_programa.cod_programa = de.cod_programa
                             AND programa_ppa_programa.exercicio    = de.exercicio
                       LEFT JOIN ppa.programa
                              ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                       LEFT JOIN orcamento.pao_ppa_acao
                              ON pao_ppa_acao.num_pao = de.num_pao
                             AND pao_ppa_acao.exercicio = de.exercicio
                       LEFT JOIN ppa.acao
                              ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                               , sw_cgm as cgm
                           WHERE CGM.numcgm = tabela.numcgm
                         ) as tabela
               LEFT JOIN orcamento.conta_despesa as cd
                      ON cd.cod_conta  = tabela.cod_conta
                     AND cd.exercicio  = tabela.exercicio
        ";
        return $stSql;
    }

    /**
        * Seta os dados pra fazer o recuperaSaldoAnterior
        * @access Private
        * @return $stSql
    */
    public function montaRecuperaSaldoAnterior()
    {
        $stSql  = "SELECT                                                              \n";
        $stSql .= "  empenho.fn_saldo_dotacao (                                    \n";
        $stSql .= "                               '".$this->getDado( "exercicio" )."'  \n";
        $stSql .= "                               ,".$this->getDado( "cod_despesa" )." \n";
        $stSql .= "                               ) AS saldo_anterior                  \n";
        return $stSql;
    }

    /**
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaSaldoAnterior(&$rsRecordSet, $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    
        $stSql = $this->montaRecuperaSaldoAnterior();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    /**
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaSaldoAnteriorDataAtual(&$rsRecordSet, $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    
        $stSql = $this->montaRecuperaSaldoAnteriorDataAtual();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    /**
        * Seta os dados pra fazer o recuperaSaldoAnteriorDataAtual
        * @access Private
        * @return $stSql
    */
    public function montaRecuperaSaldoAnteriorDataAtual()
    {
        $stSql  = "SELECT                                                              \n";
        $stSql .= "  empenho.fn_saldo_dotacao_data_atual (                                    \n";
        $stSql .= "                               '".$this->getDado( "exercicio" )."'  \n";
        $stSql .= "                               ,".$this->getDado( "cod_despesa" )." \n";
        $stSql .= "                               ,'".Sessao::read('data_reserva_saldo_GF')."' \n";
        $stSql .= "                               ) AS saldo_anterior                  \n";
    
        return $stSql;
    }
    
     /**
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaSaldoAnteriorDataEmpenho(&$rsRecordSet, $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    
        $stSql = $this->montaRecuperaSaldoAnteriorDataEmpenho();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    /**
        * Seta os dados pra fazer o montaRecuperaSaldoAnteriorDataEmpenho
        * @access Private
        * @return $stSql
    */
    public function montaRecuperaSaldoAnteriorDataEmpenho()
    {
        $stSql  = "SELECT empenho.fn_saldo_dotacao_data_empenho ( '".$this->getDado( "exercicio" )."'
                                                                 , ".$this->getDado( "cod_despesa" )."
                                                                 ,'".$this->getDado( "dt_empenho" )."'
                                                                 , ".$this->getDado( "entidade" )."
                                                                 , '".$this->getDado( "tipo_emissao" )."'
                                                                ) AS saldo_anterior ";
    
        return $stSql;
    }
    
     /**
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaSaldoAnteriorDataAtualEmpenho(&$rsRecordSet, $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    
        $stSql = $this->montaRecuperaSaldoAnteriorDataAtualEmpenho();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    /**
        * Seta os dados pra fazer o recuperaSaldoAnteriorDataAtualEmpenho
        * @access Private
        * @return $stSql
    */
    public function montaRecuperaSaldoAnteriorDataAtualEmpenho()
    {
        $stSql  = "SELECT empenho.fn_saldo_dotacao_data_atual_empenho (  '".$this->getDado( "exercicio" )."'  
                                                                       , ".$this->getDado( "cod_despesa" )." 
                                                                       , '".Sessao::read('data_reserva_saldo_GF')."'
                                                                       , '".$this->getDado( "dt_empenho" )."'
                                                                       ,  ".$this->getDado( "entidade" )."
                                                                       ,  '".$this->getDado( "tipo_emissao" )."'
                                                                      ) AS saldo_anterior ";
        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExportacao.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosTransferencia(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosTransferencia();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaRecuperaDadosTransferencia()
    {
        $stSql = " SELECT pre_empenho.cgm_beneficiario AS cod_credor,
                          sw_cgm.nom_cgm AS credor,
                          CASE WHEN sw_cgm_pessoa_fisica.cpf <> ''
                               THEN sw_cgm_pessoa_fisica.cpf
                               ELSE sw_cgm_pessoa_juridica.cnpj
                          END AS cpf_cnpj_credor
                   FROM empenho.pre_empenho
                   JOIN sw_cgm
                     ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario
              LEFT JOIN sw_cgm_pessoa_fisica
                     ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
              LEFT JOIN sw_cgm_pessoa_juridica
                     ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
              WHERE empenho.pre_empenho.exercicio = '".$this->getDado('exercicio')."'
              GROUP BY cod_credor, credor, cpf_cnpj_credor";
    
        return $stSql;
    }

    public function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stOrdem .= " ) as tbl
    LEFT JOIN ( SELECT baixa_cadastro_economico.*
              FROM economico.baixa_cadastro_economico
             INNER JOIN (SELECT inscricao_economica, max(timestamp) as timestamp
                       FROM economico.baixa_cadastro_economico
                              GROUP BY inscricao_economica) as max_baixa
            ON max_baixa.inscricao_economica  = baixa_cadastro_economico.inscricao_economica
               AND max_baixa.timestamp        = baixa_cadastro_economico.timestamp) as baixa_cadastro_economico
      ON baixa_cadastro_economico.inscricao_economica = tbl.inscricao_economica
    
    WHERE baixa_cadastro_economico.timestamp IS NULL
    ";
        $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function MontaRecuperaDadosExportacao()
    {
        $stSql   = "";
        $stSql .= " SELECT tbl.* FROM( ";
        $stSql .= "SELECT                                                                                                          \n";
        $stSql .= "    SW.numcgm,                                                                                                  \n";
        $stSql .= "    SW.nom_cgm,                                                                                                 \n";
        $stSql .= "    CASE WHEN PF.numcgm IS NOT NULL THEN replace(replace(replace(PF.cpf,'-',''),'\r',''),'\n','') ELSE replace(replace(replace(PJ.cnpj,'-',''),'\r',''),'\n','') END AS cpf_cnpj,   \n";
        $stSql .= "    CASE                                                                                                        \n";
        $stSql .= "        WHEN PJ.insc_estadual = '' THEN '0'                                                                     \n";
        $stSql .= "        ELSE replace(PJ.insc_estadual,'-','')                                                                   \n";
        $stSql .= "    END as insc_estadual,                                                                                       \n";
        $stSql .= "    CASE                                                                                                        \n";
        $stSql .= "        WHEN EF.inscricao_economica IS NOT NULL THEN replace(EF.inscricao_economica::varchar,'-','')::integer            \n";
        $stSql .= "        WHEN ED.inscricao_economica IS NOT NULL THEN replace(ED.inscricao_economica::varchar,'-','')::integer            \n";
        $stSql .= "        WHEN EA.inscricao_economica IS NOT NULL THEN replace(EA.inscricao_economica::varchar,'-','')::integer            \n";
        $stSql .= "        ELSE NULL                                                                                               \n";
        $stSql .= "    END AS insc_municipal,                                                                                      \n";
        $stSql .= "
                 CASE
                        WHEN EF.inscricao_economica IS NOT NULL THEN EF.inscricao_economica
                        WHEN ED.inscricao_economica IS NOT NULL THEN ED.inscricao_economica
                        WHEN EA.inscricao_economica IS NOT NULL THEN EA.inscricao_economica
                ELSE NULL                                                                                                             END AS inscricao_economica,
        ";
        $stSql .= "    SW.tipo_logradouro||' '||SW.logradouro||' n:'||SW.numero||' '||SW.complemento||' '||SW.bairro  AS endereco, \n";
        $stSql .= "    SM.nom_municipio,                                                                                           \n";
        $stSql .= "    SF.sigla_uf as nom_uf,                                                                                      \n";
        $stSql .= "    SW.cep,                                                                                                     \n";
        $stSql .= "    SW.fone_comercial,                                                                                          \n";
        $stSql .= "    '' AS fax,                                                                                                  \n";
        $stSql .= "    TC.tipo                                                                                                     \n";
        $stSql .= "FROM                                                                                                            \n";
        $stSql .= "    sw_municipio            AS SM,                                                                              \n";
        $stSql .= "    sw_uf                   AS SF,                                                                              \n";
        $stSql .= "    tcers.credor            AS TC,                                                                              \n";
        $stSql .= "    (select max(exercicio) as exercicio,numcgm from tcers.credor group by numcgm ) as TC2,                      \n";
        $stSql .= "    sw_cgm                  AS SW                                                                               \n";
        $stSql .= "LEFT JOIN                                                                                                       \n";
        $stSql .= "    sw_cgm_pessoa_fisica    AS PF                                                                               \n";
        $stSql .= "ON                                                                                                              \n";
        $stSql .= "    SW.numcgm     = PF.numcgm                                                                                   \n";
        $stSql .= "LEFT JOIN                                                                                                       \n";
        $stSql .= "    sw_cgm_pessoa_juridica    AS PJ                                                                             \n";
        $stSql .= "ON                                                                                                              \n";
        $stSql .= "    SW.numcgm     = PJ.numcgm                                                                                   \n";
        $stSql .= "LEFT JOIN                                                                                                       \n";
        $stSql .= "    economico.cadastro_economico_empresa_fato AS EF                                                             \n";
        $stSql .= "ON                                                                                                              \n";
        $stSql .= "    EF.numcgm               = SW.numcgm                                                                         \n";
        $stSql .= "LEFT JOIN                                                                                                       \n";
        $stSql .= "    economico.cadastro_economico_empresa_direito AS ED                                                          \n";
        $stSql .= "ON                                                                                                              \n";
        $stSql .= "    ED.numcgm               = SW.numcgm                                                                         \n";
        $stSql .= "LEFT JOIN                                                                                                       \n";
        $stSql .= "    economico.cadastro_economico_autonomo AS EA                                                                 \n";
        $stSql .= "ON                                                                                                              \n";
        $stSql .= "    EA.numcgm               = SW.numcgm                                                                         \n";
        $stSql .= "WHERE                                                                                                           \n";
        $stSql .= "    SW.numcgm               = TC.numcgm AND                                                                     \n";
        $stSql .= "    SW.cod_municipio        = SM.cod_municipio AND                                                              \n";
        $stSql .= "    SW.cod_uf               = SM.cod_uf AND                                                                     \n";
        $stSql .= "    SM.cod_uf               = SF.cod_uf AND                                                                     \n";
//      $stSql .= "    TC.exercicio            = ".$this->getDado("inExercicio")." AND                                         \n";
        $stSql .= "    TC.exercicio            = TC2.exercicio AND                                                                 \n";
        $stSql .= "    TC.numcgm               = TC2.numcgm AND                                                                    \n";
        $stSql .= "    TC.numcgm in                                                                                                \n";
        $stSql .= "        (SELECT                                                                                                 \n";
        $stSql .= "            EP.cgm_beneficiario                                                                                 \n";
        $stSql .= "        FROM                                                                                                    \n";
        $stSql .= "            empenho.empenho         AS EE,                                                                      \n";
        $stSql .= "            empenho.pre_empenho     AS EP                                                                       \n";
        $stSql .= "        WHERE                                                                                                   \n";
        $stSql .= "            EE.exercicio            = EP.exercicio AND                                                          \n";
        $stSql .= "            EE.cod_pre_empenho      = EP.cod_pre_empenho)                                                       \n";
        $stSql .= "  GROUP BY                                                                                                      \n";
        $stSql .= "                                                                                                                \n";
        $stSql .= "  SW.numcgm,                                                                                                    \n";
        $stSql .= "  SW.nom_cgm,                                                                                                   \n";
        $stSql .= "  cpf_cnpj,                                                                                                     \n";
        $stSql .= "  insc_estadual,                                                                                                \n";
        $stSql .= "  insc_municipal,                                                                                               \n";
        $stSql .= "  endereco,                                                                                                     \n";
        $stSql .= "  SM.nom_municipio,                                                                                             \n";
        $stSql .= "  SF.sigla_uf,                                                                                                  \n";
        $stSql .= "  SW.cep,                                                                                                       \n";
        $stSql .= "  SW.fone_comercial,                                                                                            \n";
        $stSql .= "  fax,                                                                                                          \n";
        $stSql .= "  TC.tipo,
  ef.inscricao_economica,
  ed.inscricao_economica,
  ea.inscricao_economica
                                                                                                      \n";

        return $stSql;
    }

    /*
     * FUNÇÃO CRIADA PARA GERAR ARQUIVO CREDOR.TXT.
     * SERÁ MOSTRADO SOMENTE UM REGISTRO DE CADA CGM, SE UMA CGM POSSUIR DOIS OU MAIS INSCRIÇÕES ECONOMICAS.
     * SERÁ MOSTRADO SOMENTE À DE NÚMERO MAIOR.
     */
    public function recuperaDadosExportacaoCredor(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stOrdem .= " ) as tbl ";
        $stSql = $this->montaRecuperaDadosExportacaoCredor().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function MontaRecuperaDadosExportacaoCredor()
    {
        $stSql  = "";
        $stSql .= " SELECT tbl.* FROM( ";
        $stSql .= "SELECT                                                                                                          \n";
        $stSql .= "    SW.numcgm,                                                                                                  \n";
        $stSql .= "    SW.nom_cgm,                                                                                                 \n";
        $stSql .= "    CASE WHEN PF.numcgm IS NOT NULL THEN replace(replace(replace(PF.cpf,'-',''),'\r',''),'\n','') ELSE replace(replace(replace(PJ.cnpj,'-',''),'\r',''),'\n','') END AS cpf_cnpj,   \n";
        $stSql .= "    CASE                                                                                                        \n";
        $stSql .= "        WHEN PJ.insc_estadual = '' THEN '0'                                                                     \n";
        $stSql .= "        ELSE replace(PJ.insc_estadual,'-','')                                                                   \n";
        $stSql .= "    END as insc_estadual,                                                                                       \n";
        $stSql .= "    CASE                                                                                                        \n";
        $stSql .= "        WHEN max(EF.inscricao_economica) IS NOT NULL THEN max(replace(EF.inscricao_economica::varchar,'-','')::integer)            \n";
        $stSql .= "        WHEN max(ED.inscricao_economica) IS NOT NULL THEN max(replace(ED.inscricao_economica::varchar,'-','')::integer)            \n";
        $stSql .= "        WHEN max(EA.inscricao_economica) IS NOT NULL THEN max(replace(EA.inscricao_economica::varchar,'-','')::integer)            \n";
        $stSql .= "        ELSE NULL                                                                                               \n";
        $stSql .= "    END AS insc_municipal,                                                                                      \n";
        $stSql .= "
                 CASE
                        WHEN max(EF.inscricao_economica) IS NOT NULL THEN max(EF.inscricao_economica)
                        WHEN max(ED.inscricao_economica) IS NOT NULL THEN max(ED.inscricao_economica)
                        WHEN max(EA.inscricao_economica) IS NOT NULL THEN max(EA.inscricao_economica)
                ELSE NULL                                                                                                             END AS inscricao_economica,
        ";
        $stSql .= "    sem_acentos(sw_tipo_logradouro.nom_tipo)||' '||sem_acentos(sw_nome_logradouro.nom_logradouro)||' n:'||SW.numero||' '||sem_acentos(SW.complemento)||' '||sem_acentos(sw_bairro.nom_bairro)  AS endereco, \n";
        $stSql .= "    SM.nom_municipio,                                                                                           \n";
        $stSql .= "    SF.sigla_uf as nom_uf,                                                                                      \n";
        $stSql .= "    sw_cgm_logradouro.cep,                                                                                                     \n";
        $stSql .= "    SW.fone_comercial,                                                                                          \n";
        $stSql .= "    '' AS fax,                                                                                                  \n";
        $stSql .= "    TC.tipo                                                                                                     \n";
        $stSql .= "FROM                                                                                                            \n";
        $stSql .= "    sw_municipio            AS SM,                                                                              \n";
        $stSql .= "    sw_uf                   AS SF,                                                                              \n";
        $stSql .= "    tcers.credor            AS TC,                                                                              \n";
        $stSql .= "    (select max(exercicio) as exercicio,numcgm from tcers.credor group by numcgm ) as TC2,                      \n";
        $stSql .= "    sw_cgm                  AS SW                                                                               \n";
        $stSql .= "LEFT JOIN                                                                                                       \n";
        $stSql .= "    sw_cgm_pessoa_fisica    AS PF                                                                               \n";
        $stSql .= "ON                                                                                                              \n";
        $stSql .= "    SW.numcgm     = PF.numcgm                                                                                   \n";
        $stSql .= "LEFT JOIN                                                                                                       \n";
        $stSql .= "    sw_cgm_pessoa_juridica    AS PJ                                                                             \n";
        $stSql .= "ON                                                                                                              \n";
        $stSql .= "    SW.numcgm     = PJ.numcgm                                                                                   \n";
        $stSql .= "LEFT JOIN                                                                                                            \n";
        $stSql .= "    sw_cgm_logradouro                                                                                           \n";
        $stSql .= "ON                                                                                                              \n";
        $stSql .= "    sw_cgm_logradouro.numcgm = SW.numcgm                                                                        \n";
        $stSql .= "LEFT JOIN                                                                                                            \n";
        $stSql .= "    sw_nome_logradouro                                                                                          \n";
        $stSql .= "ON                                                                                                              \n";
        $stSql .= "    sw_nome_logradouro.cod_logradouro = sw_cgm_logradouro.cod_logradouro                                        \n";
        $stSql .= "AND sw_nome_logradouro.timestamp = ( SELECT MAX(timestamp) FROM sw_nome_logradouro AS logradouro WHERE logradouro.cod_logradouro = sw_nome_logradouro.cod_logradouro ) \n";
        $stSql .= "LEFT JOIN                                                                                                            \n";
        $stSql .= "    sw_tipo_logradouro                                                                                          \n";
        $stSql .= "ON                                                                                                              \n";
        $stSql .= "    sw_tipo_logradouro.cod_tipo = sw_nome_logradouro.cod_tipo                                                   \n";
        $stSql .= "LEFT JOIN                                                                                                            \n";
        $stSql .= "    sw_bairro                                                                                                   \n";
        $stSql .= "ON(                                                                                                             \n";
        $stSql .= "    sw_bairro.cod_bairro = sw_cgm_logradouro.cod_bairro                                                         \n";
        $stSql .= "AND                                                                                                             \n";
        $stSql .= "    sw_bairro.cod_municipio = sw_cgm_logradouro.cod_municipio                                                   \n";
        $stSql .= "AND                                                                                                             \n";
        $stSql .= "    sw_bairro.cod_uf = sw_cgm_logradouro.cod_uf)                                                                \n";
        $stSql .= "LEFT JOIN                                                                                                       \n";
        $stSql .= "    economico.cadastro_economico_empresa_fato AS EF                                                             \n";
        $stSql .= "ON                                                                                                              \n";
        $stSql .= "    EF.numcgm               = SW.numcgm                                                                         \n";
        $stSql .= "LEFT JOIN                                                                                                       \n";
        $stSql .= "    economico.cadastro_economico_empresa_direito AS ED                                                          \n";
        $stSql .= "ON                                                                                                              \n";
        $stSql .= "    ED.numcgm               = SW.numcgm                                                                         \n";
        $stSql .= "LEFT JOIN                                                                                                       \n";
        $stSql .= "    economico.cadastro_economico_autonomo AS EA                                                                 \n";
        $stSql .= "ON                                                                                                              \n";
        $stSql .= "    EA.numcgm               = SW.numcgm                                                                         \n";
        $stSql .= "WHERE                                                                                                           \n";
        $stSql .= "    SW.numcgm               = TC.numcgm AND                                                                     \n";
        $stSql .= "    SW.cod_municipio        = SM.cod_municipio AND                                                              \n";
        $stSql .= "    SW.cod_uf               = SM.cod_uf AND                                                                     \n";
        $stSql .= "    SM.cod_uf               = SF.cod_uf AND                                                                     \n";
        $stSql .= "    TC.exercicio            = TC2.exercicio AND                                                                 \n";
        $stSql .= "    TC.numcgm               = TC2.numcgm AND                                                                    \n";
        $stSql .= "    TC.numcgm in                                                                                                \n";
        $stSql .= "        (SELECT                                                                                                 \n";
        $stSql .= "            EP.cgm_beneficiario                                                                                 \n";
        $stSql .= "        FROM                                                                                                    \n";
        $stSql .= "            empenho.empenho         AS EE,                                                                      \n";
        $stSql .= "            empenho.pre_empenho     AS EP                                                                       \n";
        $stSql .= "        WHERE                                                                                                   \n";
        $stSql .= "            EE.exercicio            = EP.exercicio AND                                                          \n";
        $stSql .= "            EE.cod_pre_empenho      = EP.cod_pre_empenho)                                                       \n";
        $stSql .= "  GROUP BY                                                                                                      \n";
        $stSql .= "                                                                                                                \n";
        $stSql .= "  SW.numcgm,                                                                                                    \n";
        $stSql .= "  SW.nom_cgm,                                                                                                   \n";
        $stSql .= "  cpf_cnpj,                                                                                                     \n";
        $stSql .= "  insc_estadual,                                                                                                \n";
        $stSql .= "  endereco,                                                                                                     \n";
        $stSql .= "  sw_cgm_logradouro.cod_logradouro,                                                                             \n";
        $stSql .= "  SM.nom_municipio,                                                                                             \n";
        $stSql .= "  SF.sigla_uf,                                                                                                  \n";
        $stSql .= "  sw_cgm_logradouro.cep,                                                                                        \n";
        $stSql .= "  SW.fone_comercial,                                                                                            \n";
        $stSql .= "  fax,                                                                                                          \n";
        $stSql .= "  TC.tipo                                                                                                       \n";

        return $stSql;
    }
    
    public function recuperaSaldoDotacaoCompra(&$rsRecordSet, $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    
        $stSql = $this->montaRecuperaSaldoDotacaoCompra();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }


    public function montaRecuperaSaldoDotacaoCompra()
    {
        $stSql  = "SELECT                                                              \n";
        $stSql .= "  empenho.fn_saldo_dotacao_compras(                                    \n";
        $stSql .= "                               '".$this->getDado( "exercicio" )."'  \n";
        $stSql .= "                               ,".$this->getDado( "cod_despesa" )." \n";
        $stSql .= "                               ) AS saldo_anterior                  \n";
    
        return $stSql;
    }

}
