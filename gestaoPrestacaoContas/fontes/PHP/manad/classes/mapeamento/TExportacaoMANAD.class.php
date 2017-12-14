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
    * Classe de mapeamento para arquivos do MANAD
    * Data de CriaÃ§Ã£o: 04/02/2013

    * @author Analista: Valtair Lacerda
    * @author Desenvolvedor: Eduardo Schitz

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TExportacaoMANAD extends Persistente
{
/**
    * MÃ©todo Construtor
    * @access Private
*/
function TExportacaoMANAD()
{
    parent::Persistente();
    $this->setTabela('');

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo('exercicio'               ,'char'    , false ,'04' ,false,false );
    $this->AddCampo('cod_entidade'            ,'integer' , false ,''   ,false,false );
    $this->AddCampo('cod_periodo_movimentacao','integer' , false ,''   ,false,false );
    $this->AddCampo('dt_inicial'              ,'date'    ,true ,''   ,false,false);
    $this->AddCampo('dt_final'                ,'date'    ,true ,''   ,false,false);
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiÃ§Ã£o do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosData(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosData().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * MÃ©todo para montar SQL para recuperar dados do tipo 0050
    * @access Private
    * @return String $stSql
*/
function montaRecuperaDadosData()
{
    $stSql  = " SELECT  exercicio
                        FROM fn_transparenciaExportacaoEmpenho( '".$this->getDado("stExercicioLogado")."'
                                     , '".$this->getDado("dtInicial")."'
                                  , '".$this->getDado("dtFinal")."'
                                     , '".$this->getDado("stCodEntidades")."')
                                    as tabela ( num_orgao           integer,
                                         num_unidade     integer         ,
                                         cod_funcao      integer         ,
                                         cod_subfuncao   integer         ,
                                         cod_programa    integer         ,
                                         num_pao         integer         ,
                                         cod_recurso     integer         ,
                                         cod_estrutural  varchar         ,
                                         cod_empenho     integer         ,
                                         dt_empenho      date            ,
                                         vl_empenhado    numeric(14,2)   ,
                                         sinal           varchar(1)      ,
                                         cgm             integer         ,
                                         historico       varchar         ,
                                         cod_pre_empenho integer         ,
                                         exercicio       char(4)         ,
                                         cod_entidade    integer         ,
                                         ordem           integer         ,
                                         oid             oid             ,
                                         caracteristica  integer         ,
                                         modalidade      integer         ,
                                         nro_licitacao   text            ,
                                         nom_modalidades text            ,
                                         preco           text
                                              )
                    WHERE tabela.cod_entidade in ( '".$this->getDado("stCodEntidades")."')
                GROUP BY exercicio
                ORDER BY exercicio
        ";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiÃ§Ã£o do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDados0050(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDados0050().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * MÃ©todo para montar SQL para recuperar dados do tipo 0050
    * @access Private
    * @return String $stSql
*/
function montaRecuperaDados0050()
{
    $stSql  = "SELECT '0050' AS reg                                             \n";
    $stSql .= "     , sw_cgm.nom_cgm AS nome                                    \n";
    $stSql .= "     , 'CNPJ DA PREFEITURA' AS cnpj                              \n";
    $stSql .= "     , sw_cgm_pessoa_fisica.cpf                                  \n";
    $stSql .= "     , responsavel_tecnico.num_registro AS crc                   \n";
    $stSql .= "     , '".$this->getDado('stDtInicial')."' AS dt_ini                \n";
    $stSql .= "     , '".$this->getDado('stDtFinal')."' AS dt_fin                \n";
    $stSql .= "     , (SELECT valor FROM administracao.configuracao WHERE parametro = 'logradouro' AND exercicio = '".$this->getDado('exercicio')."' ) AS end  \n";
    $stSql .= "     , (SELECT valor FROM administracao.configuracao WHERE parametro = 'numero' AND exercicio = '".$this->getDado('exercicio')."' ) AS num      \n";
    $stSql .= "     , (SELECT valor FROM administracao.configuracao WHERE parametro = 'complemento' AND exercicio = '".$this->getDado('exercicio')."' ) AS compl \n";
    $stSql .= "     , (SELECT valor FROM administracao.configuracao WHERE parametro = 'bairro' AND exercicio = '".$this->getDado('exercicio')."' ) AS bairro   \n";
    $stSql .= "     , (SELECT valor FROM administracao.configuracao WHERE parametro = 'cep' AND exercicio = '".$this->getDado('exercicio')."' ) AS cep         \n";
    $stSql .= "     , (SELECT sigla_uf FROM sw_uf WHERE cod_uf = (SELECT valor FROM administracao.configuracao WHERE parametro = 'cod_uf' AND exercicio = '2012' )) AS uf \n";
    $stSql .= "     , 'CAIXA POSTAL' AS cp                                      \n";
    $stSql .= "     , 'CEP CAIXA POSTAL' AS cep_cp                              \n";
    $stSql .= "     , (SELECT valor FROM administracao.configuracao WHERE parametro = 'fone' AND exercicio = '".$this->getDado('exercicio')."' ) AS fone       \n";
    $stSql .= "     , (SELECT valor FROM administracao.configuracao WHERE parametro = 'fax' AND exercicio = '".$this->getDado('exercicio')."' ) AS fax         \n";
    $stSql .= "     , (SELECT valor FROM administracao.configuracao WHERE parametro = 'e_mail' AND exercicio = '".$this->getDado('exercicio')."' ) AS email    \n";
    $stSql .= "  FROM economico.responsavel_tecnico                             \n";
    $stSql .= "  JOIN administracao.configuracao                                \n";
    $stSql .= "    ON configuracao.valor = responsavel_tecnico.numcgm           \n";
    $stSql .= "   AND parametro = 'manad_numcgm_contador_responsavel'           \n";
    $stSql .= "   AND exercicio = '".$this->getDado('exercicio')."'             \n";
    $stSql .= "  JOIN sw_cgm                                                    \n";
    $stSql .= "    ON sw_cgm.numcgm = responsavel_tecnico.numcgm                \n";
    $stSql .= "  JOIN sw_cgm_pessoa_fisica                                      \n";
    $stSql .= "    ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm               \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiÃ§Ã£o do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDados0100(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDados0100().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * MÃ©todo para montar SQL para recuperar dados do tipo 0050
    * @access Private
    * @return String $stSql
*/
function montaRecuperaDados0100()
{
       $stSql  = "       SELECT usuario.numcgm  \n";
       $stSql .= "                 , sw_cgm_pessoa_fisica.cpf \n";
       $stSql .= "          FROM administracao.usuario  \n";
       $stSql .= "  INNER JOIN sw_cgm  \n";
       $stSql .= "              ON sw_cgm.numcgm = usuario.numcgm \n";
       $stSql .= "     LEFT JOIN sw_cgm_pessoa_fisica \n";
       $stSql .= "              ON sw_cgm_pessoa_fisica.numcgm = usuario.numcgm \n";
       $stSql .= "        WHERE usuario.username =  '".$this->getDado("username")."'  \n";

    return $stSql;
}

function recuperaDadosI005(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosI005().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosI005()
{
    $stSQL  = " SELECT 'I005' as reg,                                                                                                      \n";
    $stSQL .= "       'G' as ind_esc,                          \n";
    $stSQL .= "       to_char(lote.dt_lote,'dd/mm/yyyy')                                           AS dt_lote                \n";
    $stSQL .= "     , CASE WHEN historico_contabil.complemento = true                                                        \n";
    $stSQL .= "            THEN historico_contabil.nom_historico || ' ' || lancamento.complemento                            \n";
    $stSQL .= "            ELSE historico_contabil.nom_historico                                                             \n";
    $stSQL .= "       END                                                                         AS historico               \n";
    $stSQL .= "     , abs(valor_lancamento_debito.vl_lancamento)                                  AS vl_lancamento_debito    \n";
    $stSQL .= "     , plano_conta_debito.cod_estrutural                                           AS cod_estrutural_debito   \n";
    $stSQL .= "     , plano_conta_debito.nom_conta                                                AS nom_conta_debito        \n";
    $stSQL .= "     , abs(valor_lancamento_credito.vl_lancamento)                                 AS vl_lancamento_credito   \n";
    $stSQL .= "     , plano_conta_credito.cod_estrutural                                          AS cod_estrutural_credito  \n";
    $stSQL .= "     , plano_conta_credito.nom_conta                                               AS nom_conta_credito       \n";
    $stSQL .= "  FROM                                                                                                        \n";
    $stSQL .= "       contabilidade.lote                                                                                     \n";
    $stSQL .= "     , contabilidade.lancamento                                                                               \n";
    $stSQL .= "     , contabilidade.historico_contabil                                                                       \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     , contabilidade.valor_lancamento     AS valor_lancamento_debito                                          \n";
    $stSQL .= "     , contabilidade.conta_debito                                                                             \n";
    $stSQL .= "     , contabilidade.plano_analitica      AS plano_analitica_debito                                           \n";
    $stSQL .= "     , contabilidade.plano_conta          AS plano_conta_debito                                               \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     , contabilidade.valor_lancamento     AS valor_lancamento_credito                                         \n";
    $stSQL .= "     , contabilidade.conta_credito                                                                            \n";
    $stSQL .= "     , contabilidade.plano_analitica      AS plano_analitica_credito                                          \n";
    $stSQL .= "     , contabilidade.plano_conta          AS plano_conta_credito                                              \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "   WHERE     lancamento.exercicio                    = lote.exercicio                                           \n";
    $stSQL .= "     AND     lancamento.cod_entidade                 = lote.cod_entidade                                      \n";
    $stSQL .= "     AND     lancamento.tipo                         = lote.tipo                                              \n";
    $stSQL .= "     AND     lancamento.cod_lote                     = lote.cod_lote                                          \n";
    $stSQL .= "     AND     lancamento.exercicio                    = historico_contabil.exercicio                           \n";
    $stSQL .= "     AND     lancamento.cod_historico                = historico_contabil.cod_historico                       \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     lancamento.exercicio                    = valor_lancamento_debito.exercicio                      \n";
    $stSQL .= "     AND     lancamento.cod_entidade                 = valor_lancamento_debito.cod_entidade                   \n";
    $stSQL .= "     AND     lancamento.sequencia                    = valor_lancamento_debito.sequencia                      \n";
    $stSQL .= "     AND     lancamento.cod_lote                     = valor_lancamento_debito.cod_lote                       \n";
    $stSQL .= "     AND     lancamento.tipo                         = valor_lancamento_debito.tipo                           \n";
    $stSQL .= "     AND     valor_lancamento_debito.tipo_valor      = 'D'                                                    \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     valor_lancamento_debito.exercicio       = conta_debito.exercicio                                 \n";
    $stSQL .= "     AND     valor_lancamento_debito.cod_entidade    = conta_debito.cod_entidade                              \n";
    $stSQL .= "     AND     valor_lancamento_debito.sequencia       = conta_debito.sequencia                                 \n";
    $stSQL .= "     AND     valor_lancamento_debito.cod_lote        = conta_debito.cod_lote                                  \n";
    $stSQL .= "     AND     valor_lancamento_debito.tipo            = conta_debito.tipo                                      \n";
    $stSQL .= "     AND     valor_lancamento_debito.tipo_valor      = conta_debito.tipo_valor                                \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     conta_debito.exercicio                  = plano_analitica_debito.exercicio                       \n";
    $stSQL .= "     AND     conta_debito.cod_plano                  = plano_analitica_debito.cod_plano                       \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     plano_analitica_debito.exercicio        = plano_conta_debito.exercicio                           \n";
    $stSQL .= "     AND     plano_analitica_debito.cod_conta        = plano_conta_debito.cod_conta                           \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     lancamento.exercicio                    = valor_lancamento_credito.exercicio                     \n";
    $stSQL .= "     AND     lancamento.cod_entidade                 = valor_lancamento_credito.cod_entidade                  \n";
    $stSQL .= "     AND     lancamento.sequencia                    = valor_lancamento_credito.sequencia                     \n";
    $stSQL .= "     AND     lancamento.cod_lote                     = valor_lancamento_credito.cod_lote                      \n";
    $stSQL .= "     AND     lancamento.tipo                         = valor_lancamento_credito.tipo                          \n";
    $stSQL .= "     AND     valor_lancamento_credito.tipo_valor     = 'C'                                                    \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     valor_lancamento_credito.exercicio      = conta_credito.exercicio                                \n";
    $stSQL .= "     AND     valor_lancamento_credito.cod_entidade   = conta_credito.cod_entidade                             \n";
    $stSQL .= "     AND     valor_lancamento_credito.sequencia      = conta_credito.sequencia                                \n";
    $stSQL .= "     AND     valor_lancamento_credito.cod_lote       = conta_credito.cod_lote                                 \n";
    $stSQL .= "     AND     valor_lancamento_credito.tipo           = conta_credito.tipo                                     \n";
    $stSQL .= "     AND     valor_lancamento_credito.tipo_valor     = conta_credito.tipo_valor                               \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     conta_credito.exercicio                 = plano_analitica_credito.exercicio                      \n";
    $stSQL .= "     AND     conta_credito.cod_plano                 = plano_analitica_credito.cod_plano                      \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     plano_analitica_credito.exercicio       = plano_conta_credito.exercicio                          \n";
    $stSQL .= "     AND     plano_analitica_credito.cod_conta       = plano_conta_credito.cod_conta                          \n";

    $stSQL .= "     AND     lote.exercicio      = '".$this->getDado("stExercicio")."'                                          \n";
    $stSQL .= "     AND     lote.cod_entidade   IN (".$this->getDado('stCodEntidades').")                                         \n";

    if ( $this->getDado("dtInicial") == $this->getDado("dtFinal") ) {
        $stSQL .= " AND     lote.dt_lote = to_date('".$this->getDado("dtInicial")."','dd/mm/yyyy')                         \n";
    } else {
        $stSQL .= " AND     lote.dt_lote between to_date('".$this->getDado("dtInicial")."','dd/mm/yyyy')                   \n";
        $stSQL .= "                          and to_date('".$this->getDado("dtFinal")."','dd/mm/yyyy')                     \n";
    }
    $stSQL .= " ORDER BY lote.dt_lote, lancamento.oid                                                                        \n";

    return $stSQL;
}

function recuperaDadosI050(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosI050();
    $this->setDebug ($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosI050()
{
    $stSql  = "
     SELECT 'I050' as  reg
               , '01/01/'||'2013' as dt_inc_alt
               , CASE WHEN cod_estrutural_retorno like '1.%' THEN
                    1
                 WHEN cod_estrutural_retorno like '2.%' THEN
                    2
                 WHEN cod_estrutural_retorno like '2.3%' THEN
                    3
                 WHEN cod_estrutural_retorno like '3.%' THEN
                    4
                 WHEN cod_estrutural_retorno like '4.%' THEN
                    5
                 WHEN cod_estrutural_retorno like '5.%' OR cod_estrutural_retorno like '6.%' OR cod_estrutural_retorno like '7.%' OR cod_estrutural_retorno like '8.%' THEN
                    9
                 END AS ind_nat
               , CASE WHEN sintetica.cod_conta is null THEN
                   'A'
                 ELSE
                   'S'
                  END AS ind_grp_cta
               , replace(cod_estrutural_retorno, '.','') as cod_grp_cta
               , replace(publico.fn_mascara_completa('0.0.0.0.0.00.00.00.00.00', publico.fn_codigo_superior(cod_estrutural_retorno)), '.','') as cod_grp_cta_sup
               , nivel
               , nom_conta_retorno as nome_grp_cta
       FROM  contabilidade.fn_rl_balancete_verificacao('".$this->getDado("stExercicio")."','".$this->getDado("stFiltro")."','".$this->getDado("dtInicial")."','".$this->getDado("dtFinal")."','A')
            AS retorno( cod_estrutural_retorno varchar
               , nivel integer
               , nom_conta_retorno varchar
               , cod_sistema integer
               , indicador_superavit char
               , vl_saldo_anterior numeric
               , vl_saldo_debitos  numeric
               , vl_saldo_creditos numeric
               , vl_saldo_atual    numeric
               )

 LEFT JOIN (SELECT  cod_conta
                           ,  cod_estrutural
                   FROM  contabilidade.plano_conta
                 WHERE  exercicio = '".$this->getDado( 'stExercicio' )."'
                      AND  NOT EXISTS  (   SELECT 1
                                             FROM contabilidade.plano_analitica
                                            WHERE plano_analitica.cod_conta = plano_conta.cod_conta
                                              AND plano_analitica.exercicio = plano_conta.exercicio
                                       )) as sintetica
           ON sintetica.cod_conta = cod_conta
GROUP BY reg
              , dt_inc_alt
              , ind_nat
              , ind_grp_cta
              , cod_grp_cta
              , cod_grp_cta_sup
              , nivel, nome_grp_cta
   ORDER BY cod_grp_cta
";

     return $stSql;
}

function recuperaDadosI100(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro();
    $obConexao   = new Conexao();
    $rsRecordSet = new RecordSet();

    $stSql = $this->montaRecuperaDadosI100();
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
}

function montaRecuperaDadosI100()
{
    $stSql  ="   SELECT 'I100' as reg                                                                               \n";
    $stSql .="             , centro_custo.cod_centro  as cod_ccus                                       \n";
    $stSql .="             , centro_custo.descricao as ccus                                                 \n";
    $stSql .="             , '0101'||centro_custo_entidade.exercicio as dt_inc_alt                \n";
    $stSql .="      FROM almoxarifado.centro_custo                                                         \n";
    $stSql .="LEFT JOIN almoxarifado.centro_custo_entidade                                          \n";
    $stSql .="        ON centro_custo_entidade.cod_centro  = centro_custo.cod_centro  \n";

      return $stSql;
}

function recuperaDadosI150(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosI150().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosI150()
{
    $stSql  = " SELECT                                                                                      \n";
    $stSql .= "    'I150' as reg , *,                                                                                       \n";
    $stSql .= "     replace(cod_estrutural,'.','')  as cod_cta,  \n";
    $stSql .= "    '".$this->getDado("mesAno")."' as comp_saldo, \n";
    $stSql .= "   replace(replace(vl_saldo_anterior, '.',','),'-','')  as vl_sld_ini, \n";
    $stSql .= "    CASE WHEN vl_saldo_anterior > 0 THEN 'D' WHEN  vl_saldo_anterior < 0 THEN 'C' END AS ind_sld_ini, \n";
    $stSql .= "    replace(replace(vl_saldo_debitos, '.',','),'-','')  as vl_deb,  \n";
    $stSql .= "    replace(replace(vl_saldo_creditos, '.',','),'-','') as vl_cred,  \n";
    $stSql .= "    replace(replace(vl_saldo_atual, '.',','),'-','') as vl_sld_fin,  \n";
    $stSql .= "    CASE WHEN vl_saldo_atual > 0 THEN 'D' WHEN  vl_saldo_atual < 0 THEN 'C' END AS ind_sld_fin \n";
    $stSql .= " FROM                                                                                        \n";
    $stSql .= "   contabilidade.fn_rl_balancete_verificacao('".$this->getDado("stExercicio")."','".$this->getDado("stFiltro")."','".$this->getDado("dtInicial")."','".$this->getDado("dtFinal")."','A')\n";
    $stSql .= "     as retorno( cod_estrutural varchar                                                      \n";
    $stSql .= "                ,nivel integer                                                               \n";
    $stSql .= "                ,nom_conta varchar                                                           \n";
    $stSql .= "                ,cod_sistema integer                                                         \n";
    $stSql .= "                ,indicador_superavit char                                                    \n";
    $stSql .= "                ,vl_saldo_anterior numeric                                                   \n";
    $stSql .= "                ,vl_saldo_debitos  numeric                                                   \n";
    $stSql .= "                ,vl_saldo_creditos numeric                                                   \n";
    $stSql .= "                ,vl_saldo_atual    numeric                                                   \n";
    $stSql .= "                )                                                                            \n";

    return $stSql;
}
/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaRelatorio.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosI200(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "", $stExercicio = "", $stCodigoEntidade = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosI200().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
/**
    * Monta sql para recuperaRelatorio
    * @access Private
    * @return String
*/
function montaRecuperaDadosI200()
{
    $stSql  = "SELECT 'I200' as reg                                                                                                          \n";
    $stSql .= "--    , tabela.*                                                                                                                     \n";
    $stSql .= "    , CASE WHEN tabela.cod_historico > 800 THEN  'E'  ELSE 'N' END AS ind_lcto               \n";
    $stSql .= "    , (SELECT replace(cod_estrutural,'.','')                                                                           \n";
    $stSql .= "         FROM contabilidade.plano_conta                                                                              \n";
    $stSql .= "        WHERE cod_conta = (SELECT *                                                                                  \n";
    $stSql .= "                             FROM contabilidade.fn_recupera_contra_partida(tabela.exercicio,tabela.cod_lote,tabela.tipo,tabela.sequencia,tabela.tipo_valor,tabela.cod_entidade)  \n";
    $stSql .= "                          )                                                                                                              \n";
    $stSql .= "          AND exercicio = tabela.exercicio) AS cod_cp                                                          \n";
    $stSql .= "    , tabela.sequencia                                                                                                       \n";
    $stSql .= "    , tabela.cod_lote as num_lcto                                                                                      \n";
    $stSql .= "    , tabela.nom_lote                                                                                                         \n";
    $stSql .= "    , tabela.cod_historico as hist_lcto                                                                               \n";
    $stSql .= "    , tabela.nom_historico                                                                                                  \n";
    $stSql .= "    , tabela.exercicio                                                                                                         \n";
    $stSql .= "    , tabela.cod_entidade                                                                                                 \n";
    $stSql .= "    , tabela.cod_recibo_extra                                                                                           \n";
    $stSql .= "    , replace(replace(tabela.vl_lancamento, '.',','),'-','')  as vl_deb_cred                             \n";
    $stSql .= "    , tabela.tipo_valor  as ind_deb_cred                                                                           \n";
    $stSql .= "    , replace(tabela.dt_lote, '/','')  as dt_lcto                                                                     \n";
    $stSql .= "    , tabela.cod_plano                                                                                                      \n";
    $stSql .= "    , pc.nom_conta                                                                                                          \n";
    $stSql .= "    , replace(pc.cod_estrutural,'.','') as cod_cta                                                               \n";
    $stSql .= "    , tabela.tipo                                                                                                                   \n";
    $stSql .= "    , tabela.complemento                                                                                                   \n";
    $stSql .= "    , tabela.nom_cgm                                                                                                         \n";
    $stSql .= "FROM (                                                                                                                              \n";
    $stSql .= "    SELECT l.cod_lote                                                                                                         \n";
    $stSql .= "              , lo.nom_lote                                                                                                      \n";
    $stSql .= "              , l.sequencia                                                                                                      \n";
    $stSql .= "              , l.tipo                                                                                                                \n";
    $stSql .= "              , l.cod_historico                                                                                                  \n";
    $stSql .= "              , l.exercicio                                                                                                         \n";
    $stSql .= "              , l.cod_entidade                                                                                                 \n";
    $stSql .= "              , l.complemento,                                                                                                \n";
    $stSql .= "    hc.nom_historico||' '||l.complemento||CASE WHEN (tret.cod_recibo_extra IS NOT NULL) \n";
    $stSql .= "       OR (tret2.cod_recibo_extra IS NOT NULL) OR (ret.cod_recibo_extra IS NOT NULL)       \n";
    $stSql .= "          THEN ' - Recibo: '                                                                                                     \n";
    $stSql .= "             ||coalesce(cast(tret.cod_recibo_extra as varchar),'')                                           \n";
    $stSql .= "        --   ||coalesce(cast(tret2.cod_recibo_extra as varchar),'')                                         \n";
    $stSql .= "             ||coalesce(cast(ret.cod_recibo_extra as varchar),'')                                            \n";
    $stSql .= "          ELSE ' '                                                                                                                   \n";
    $stSql .= "       END                                                                                                                           \n";
    $stSql .= "       ||CASE WHEN (tt.observacao IS NOT NULL)                                                                \n";
    $stSql .= "                   OR (tte.observacao IS NOT NULL)                                                                  \n";
    $stSql .= "                   OR (tarrec.observacao IS NOT NULL)                                                             \n";
    $stSql .= "           THEN ' - '||coalesce(tt.observacao, '')                                                                    \n";
    $stSql .= "                     ||coalesce(tte.observacao,'')                                                                       \n";
    $stSql .= "           ELSE ' '                                                                                                                  \n";
    $stSql .= "        END                                                                                                                          \n";
    $stSql .= "     AS nom_historico,                                                                                                        \n";
    $stSql .= "            CASE WHEN (tret.cod_recibo_extra is null ) THEN                                                  \n";
    $stSql .= "                       tret2.cod_recibo_extra                      \n";
    $stSql .= "                 ELSE                                              \n";
    $stSql .= "                       tret.cod_recibo_extra                       \n";
    $stSql .= "            END AS cod_recibo_extra,                               \n";
    $stSql .= "            vl.vl_lancamento,                                      \n";
    $stSql .= "            vl.tipo_valor,                                         \n";
    $stSql .= "            cgm.nom_cgm,                                           \n";
    $stSql .= "            to_char( lo.dt_lote, 'dd/mm/yyyy') AS dt_lote,         \n";
    $stSql .= "            CASE WHEN cc.cod_plano is not null THEN cc.cod_plano   \n";
    $stSql .= "                 ELSE cd.cod_plano                                 \n";
    $stSql .= "            END AS cod_plano                                       \n";
    $stSql .= "    FROM                                                           \n";
    $stSql .= "           contabilidade.lancamento         AS l,                  \n";
    $stSql .= "           contabilidade.lote               AS lo,                 \n";
    $stSql .= "           contabilidade.historico_contabil AS hc,                 \n";
    $stSql .= "           orcamento.entidade               as en,                 \n";
    $stSql .= "           sw_cgm                           as cgm,                \n";
    $stSql .= "           contabilidade.valor_lancamento   AS vl                  \n";
    $stSql .= "    LEFT JOIN                                                      \n";
    $stSql .= "           contabilidade.conta_credito AS cc                       \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                cc.cod_lote     = vl.cod_lote       AND            \n";
    $stSql .= "                cc.tipo         = vl.tipo           AND            \n";
    $stSql .= "                cc.sequencia    = vl.sequencia      AND            \n";
    $stSql .= "                cc.exercicio    = vl.exercicio      AND            \n";
    $stSql .= "                cc.tipo_valor   = vl.tipo_valor     AND            \n";
    $stSql .= "                cc.cod_entidade = vl.cod_entidade                  \n";
    $stSql .= "            )                                                      \n";
    $stSql .= "    LEFT JOIN                                                      \n";
    $stSql .= "           contabilidade.conta_debito AS cd                        \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                cd.cod_lote     = vl.cod_lote       AND            \n";
    $stSql .= "                cd.tipo         = vl.tipo           AND            \n";
    $stSql .= "                cd.sequencia    = vl.sequencia      AND            \n";
    $stSql .= "                cd.exercicio    = vl.exercicio      AND            \n";
    $stSql .= "                cd.tipo_valor   = vl.tipo_valor     AND            \n";
    $stSql .= "                cd.cod_entidade = vl.cod_entidade                  \n";
    $stSql .= "            )                                                      \n";
    $stSql .= "     LEFT JOIN                                                     \n";
    $stSql .= "           tesouraria.transferencia AS tt                          \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                tt.cod_lote     = vl.cod_lote       AND            \n";
    $stSql .= "                tt.tipo         = vl.tipo           AND            \n";
    $stSql .= "                tt.exercicio    = vl.exercicio      AND            \n";
    $stSql .= "                tt.cod_entidade = vl.cod_entidade                  \n";
    $stSql .= "            )                                                      \n";
    $stSql .= "     LEFT JOIN                                                     \n";
    $stSql .= "            tesouraria.transferencia_estornada  AS tte             \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                tte.cod_lote_estorno  = vl.cod_lote AND            \n";
    $stSql .= "                tte.tipo         = vl.tipo           AND           \n";
    $stSql .= "                tte.exercicio    = vl.exercicio      AND           \n";
    $stSql .= "                tte.cod_entidade = vl.cod_entidade                 \n";
    $stSql .= "            )                                                      \n";
    $stSql .= "      LEFT JOIN tesouraria.recibo_extra_transferencia AS ret       \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                ret.cod_lote     = vl.cod_lote       AND           \n";
    $stSql .= "                ret.tipo         = vl.tipo           AND           \n";
    $stSql .= "                ret.exercicio    = vl.exercicio      AND           \n";
    $stSql .= "                ret.cod_entidade = vl.cod_entidade                 \n";
    $stSql .= "            )                                                    \n";
    $stSql .= "     LEFT JOIN (SELECT tbl.exercicio                                      \n";
    $stSql .= "               ,tbl.cod_entidade                                          \n";
    $stSql .= "               ,tbll.tipo                                                  \n";
    $stSql .= "               ,tbll.cod_lote                                              \n";
    $stSql .= "               ,ta.observacao                                             \n";
    $stSql .= "         FROM                                                             \n";
    $stSql .= "                tesouraria.boletim_liberado AS tbl                        \n";
    $stSql .= "         LEFT JOIN tesouraria.arrecadacao AS ta                           \n";
    $stSql .= "                   ON  ta.exercicio    = tbl.exercicio                    \n";
    $stSql .= "                   AND ta.cod_entidade = tbl.cod_entidade                 \n";
    $stSql .= "                   AND ta.cod_boletim      = tbl.cod_boletim                    \n";
    $stSql .= "         JOIN tesouraria.boletim_liberado_lote as tbll               \n";
    $stSql .= "             	  ON tbll.cod_boletim = tbl.cod_boletim                      \n";
    $stSql .= "                  AND tbll.cod_entidade = tbl.cod_entidade               \n";
    $stSql .= "                	 AND tbll.exercicio = tbll.exercicio                        \n";
    $stSql .= "                	 AND tbll.timestamp_liberado = tbll.timestamp_liberado      \n";
    $stSql .= "                	 AND tbll.timestamp_fechamento = tbll.timestamp_fechamento  \n";
    $stSql .= "         WHERE                                                            \n";
    $stSql .= "                ta.exercicio    = tbl.exercicio                           \n";
    $stSql .= "                AND ta.cod_entidade = tbl.cod_entidade                    \n";
    $stSql .= "                AND ta.cod_boletim  = tbl.cod_boletim                     \n";
    $stSql .= "        ) AS tarrec                                                       \n";
    $stSql .= "          ON (                                                            \n";
    $stSql .= "             tarrec.cod_lote    = vl.cod_lote    AND                      \n";
    $stSql .= "            tarrec.tipo         = vl.tipo        AND                      \n";
    $stSql .= "            tarrec.exercicio    = vl.exercicio   AND                      \n";
    $stSql .= "            tarrec.cod_entidade = vl.cod_entidade                         \n";
    $stSql .= "            )                                                           \n";
    $stSql .= "     LEFT JOIN (SELECT                                                              \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote_estorno,               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.tipo,                           \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio,                      \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade,                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_recibo_extra             \n";
    $stSql .= "             FROM                                                                   \n";
    $stSql .= "                 tesouraria.transferencia_estornada                                 \n";
    $stSql .= "             LEFT JOIN tesouraria.recibo_extra_transferencia ON (                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.exercicio =                  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio AND                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_entidade =               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade  AND               \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_lote =                   \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote                        \n";
    $stSql .= "                 )                                                                  \n";
    $stSql .= "             WHERE                                                                  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio = ".$this->getDado('stExercicio')." AND  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade IN (".$this->getDado('stEntidade').") \n";
    $stSql .= "             GROUP BY                                                               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote_estorno,               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.tipo,                           \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio,                      \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade,                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_recibo_extra             \n";
    $stSql .= "                                                                                    \n";
    $stSql .= "            ) AS tret                                                               \n";
    $stSql .= "              ON (                                                                  \n";
    $stSql .= "                 tret.cod_lote_estorno  = vl.cod_lote    AND                        \n";
    $stSql .= "                 tret.tipo         = vl.tipo        AND                             \n";
    $stSql .= "                 tret.exercicio    = vl.exercicio   AND                             \n";
    $stSql .= "                 tret.cod_entidade = vl.cod_entidade                                \n";
    $stSql .= "                 )                                                                  \n";
    $stSql .= "      LEFT JOIN (SELECT                                                             \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote,                       \n";
    $stSql .= "                 tesouraria.transferencia_estornada.tipo,                           \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio,                      \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade,                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_recibo_extra             \n";
    $stSql .= "             FROM                                                                   \n";
    $stSql .= "                 tesouraria.transferencia_estornada                                 \n";
    $stSql .= "             LEFT JOIN tesouraria.recibo_extra_transferencia ON (                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.exercicio =                  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio AND                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_entidade =               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade  AND               \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_lote =                   \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote                        \n";
    $stSql .= "                 )                                                                  \n";
    $stSql .= "             WHERE                                                                  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio = ".$this->getDado('stExercicio')." AND  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade IN (".$this->getDado('stEntidade').") \n";
    $stSql .= "             GROUP BY                                                               \n";
    $stSql .= "                     tesouraria.transferencia_estornada.cod_lote,                   \n";
    $stSql .= "                      tesouraria.transferencia_estornada.tipo,                      \n";
    $stSql .= "                      tesouraria.transferencia_estornada.exercicio,                 \n";
    $stSql .= "                      tesouraria.transferencia_estornada.cod_entidade,              \n";
    $stSql .= "                      tesouraria.recibo_extra_transferencia.cod_recibo_extra        \n";
    $stSql .= "                                                                                    \n";
    $stSql .= "            ) AS tret2                                                              \n";
    $stSql .= "              ON (                                                                  \n";
    $stSql .= "                 tret2.cod_lote     = vl.cod_lote     AND                           \n";
    $stSql .= "                 tret2.tipo         = vl.tipo         AND                           \n";
    $stSql .= "                 tret2.exercicio    = vl.exercicio    AND                           \n";
    $stSql .= "            tret2.cod_entidade = vl.cod_entidade                                    \n";
    $stSql .= "            )                                                                       \n";
    $stSql .= "    WHERE                                                          \n";
    $stSql .= "            vl.cod_lote      = l.cod_lote        AND               \n";
    $stSql .= "            vl.tipo          = l.tipo            AND               \n";
    $stSql .= "            vl.sequencia     = l.sequencia       AND               \n";
    $stSql .= "            vl.exercicio     = l.exercicio       AND               \n";
    $stSql .= "            vl.cod_entidade  = l.cod_entidade    AND               \n";
    $stSql .= "            lo.cod_lote      = l.cod_lote        AND               \n";
    $stSql .= "            lo.exercicio     = l.exercicio       AND               \n";
    $stSql .= "            lo.tipo          = l.tipo            AND               \n";
    $stSql .= "            lo.cod_entidade  = l.cod_entidade    AND               \n";
    $stSql .= "            hc.cod_historico = l.cod_historico   AND               \n";
    $stSql .= "            hc.exercicio     = l.exercicio       AND               \n";
    $stSql .= "            en.cod_entidade  = l.cod_entidade    AND               \n";
    $stSql .= "            en.exercicio     = l.exercicio       AND               \n";
    $stSql .= "            cgm.numcgm       = en.numcgm                           \n";
    $stSql .= "             AND l.cod_entidade IN (".$this->getDado('stEntidade').")  ";
   $stSql  .= "             AND l.exercicio = '". $this->getDado('stExercicio')."' ";
   $stSql  .= "             AND lo.dt_lote >= TO_DATE('".$this->getDado("dtInicial")."','dd/mm/yyyy' )";
   $stSql  .= "             AND lo.dt_lote <= TO_DATE('".$this->getDado("dtFinal")."','dd/mm/yyyy') ";
    $stSql .= "    ORDER BY                                                       \n";
    $stSql .= "            to_date(dt_lote,'yyyy-mm-dd'),                         \n";
    $stSql .= "            l.cod_lote,                                            \n";
    $stSql .= "            l.cod_entidade,                                        \n";
    $stSql .= "            l.tipo,                                                \n";
    $stSql .= "            l.sequencia ASC,                                       \n";
    $stSql .= "            vl.vl_lancamento DESC,                                 \n";
    $stSql .= "            vl.tipo_valor DESC                                     \n";
    $stSql .= "    ) AS tabela,                                                   \n";
    $stSql .= "    contabilidade.plano_analitica AS pa,                           \n";
    $stSql .= "    contabilidade.plano_conta     AS pc                            \n";
    $stSql .= "WHERE                                                              \n";
    $stSql .= "    tabela.cod_plano = pa.cod_plano     AND                        \n";
    $stSql .= "    tabela.exercicio = pa.exercicio     AND                        \n";
    $stSql .= "    pa.cod_conta     = pc.cod_conta     AND                        \n";
    $stSql .= "    pa.exercicio     = pc.exercicio                                \n";
    $stSql .= "GROUP BY                                                         \n";
    $stSql .= "            tabela.cod_lote,                                     \n";
    $stSql .= "            tabela.nom_lote,                                     \n";
    $stSql .= "            tabela.sequencia,                                    \n";
    $stSql .= "            tabela.tipo,                                         \n";
    $stSql .= "            tabela.cod_historico,                                \n";
    $stSql .= "            tabela.nom_historico,                                \n";
    $stSql .= "            tabela.exercicio,                                    \n";
    $stSql .= "            tabela.cod_entidade,                                 \n";
    $stSql .= "            tabela.complemento,                                  \n";
    $stSql .= "            tabela.cod_recibo_extra,                             \n";
    $stSql .= "            tabela.vl_lancamento,                                \n";
    $stSql .= "            tabela.tipo_valor,                                   \n";
    $stSql .= "            tabela.nom_cgm,                                      \n";
    $stSql .= "            tabela.dt_lote,                                      \n";
    $stSql .= "            tabela.cod_plano,                                    \n";
    $stSql .= "            pc.nom_conta,                                        \n";
    $stSql .= "            pc.cod_estrutural                                    \n";
    $stSql .= "ORDER BY pc.cod_estrutural                                    \n";

    return $stSql;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaRelatorio.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosI250(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "", $stExercicio = "", $stCodigoEntidade = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosI250().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
/**
    * Monta sql para recuperaRelatorio
    * @access Private
    * @return String
*/
function montaRecuperaDadosI250()
{
    $stSql  = "SELECT 'I250' as reg                                                                                                         \n";
    $stSql .= "        --  , tabela.*                                                                                                                \n";

    $stSql .= "        , tabela.sequencia        \n";
    $stSql .= "        , tabela.cod_historico   \n";
    $stSql .= "        , tabela.exercicio        \n";
    $stSql .= "        , tabela.cod_entidade     \n";
    $stSql .= "        , tabela.cod_recibo_extra \n";
    $stSql .= "       , replace(replace(tabela.vl_lancamento, '.',','),'-','')  as vl_grp_cta \n";
    $stSql .= "        , tabela.tipo_valor  as sld_fin    \n";
    $stSql .= "        , replace(tabela.dt_lote, '/','')  as dt_res        \n";
    $stSql .= "        , tabela.cod_plano                                                                                                          \n";
    $stSql .= "        , pc.nom_conta                                                                                                       \n";
    $stSql .= "        ,replace(pc.cod_estrutural,'.','') as cod_grp_cta  \n";
    $stSql .= "FROM (                                                                                                                              \n";
    $stSql .= "    SELECT l.cod_lote                                                                                                         \n";
    $stSql .= "              , lo.nom_lote                                                                                                      \n";
    $stSql .= "              , l.sequencia                                                                                                      \n";
    $stSql .= "              , l.tipo                                                                                                                \n";
    $stSql .= "              , l.cod_historico                                                                                                  \n";
    $stSql .= "              , l.exercicio                                                                                                         \n";
    $stSql .= "              , l.cod_entidade                                                                                                 \n";
    $stSql .= "              , l.complemento,                                                                                                \n";
    $stSql .= "    hc.nom_historico||' '||l.complemento||CASE WHEN (tret.cod_recibo_extra IS NOT NULL) \n";
    $stSql .= "       OR (tret2.cod_recibo_extra IS NOT NULL) OR (ret.cod_recibo_extra IS NOT NULL)       \n";
    $stSql .= "          THEN ' - Recibo: '                                                                                                     \n";
    $stSql .= "             ||coalesce(cast(tret.cod_recibo_extra as varchar),'')                                           \n";
    $stSql .= "        --   ||coalesce(cast(tret2.cod_recibo_extra as varchar),'')                                         \n";
    $stSql .= "             ||coalesce(cast(ret.cod_recibo_extra as varchar),'')                                            \n";
    $stSql .= "          ELSE ' '                                                                                                                   \n";
    $stSql .= "       END                                                                                                                           \n";
    $stSql .= "       ||CASE WHEN (tt.observacao IS NOT NULL)                                                                \n";
    $stSql .= "                   OR (tte.observacao IS NOT NULL)                                                                  \n";
    $stSql .= "                   OR (tarrec.observacao IS NOT NULL)                                                             \n";
    $stSql .= "           THEN ' - '||coalesce(tt.observacao, '')                                                                    \n";
    $stSql .= "                     ||coalesce(tte.observacao,'')                                                                       \n";
//    $stSql .= "                     ||coalesce(tarrec.observacao,'')            \n";
    $stSql .= "           ELSE ' '                                                                                                                  \n";
    $stSql .= "        END                                                                                                                          \n";
    $stSql .= "     AS nom_historico,                                             \n";
    $stSql .= "            CASE WHEN (tret.cod_recibo_extra is null ) THEN        \n";
    $stSql .= "                       tret2.cod_recibo_extra                      \n";
    $stSql .= "                 ELSE                                              \n";
    $stSql .= "                       tret.cod_recibo_extra                       \n";
    $stSql .= "            END AS cod_recibo_extra,                               \n";
    $stSql .= "            vl.vl_lancamento,                                      \n";
    $stSql .= "            vl.tipo_valor,                                         \n";
    $stSql .= "            cgm.nom_cgm,                                           \n";
    $stSql .= "            to_char( lo.dt_lote, 'dd/mm/yyyy') AS dt_lote,         \n";
    $stSql .= "            CASE WHEN cc.cod_plano is not null THEN cc.cod_plano   \n";
    $stSql .= "                 ELSE cd.cod_plano                                 \n";
    $stSql .= "            END AS cod_plano                                       \n";
    $stSql .= "    FROM                                                           \n";
    $stSql .= "           contabilidade.lancamento         AS l,                  \n";
    $stSql .= "           contabilidade.lote               AS lo,                 \n";
    $stSql .= "           contabilidade.historico_contabil AS hc,                 \n";
    $stSql .= "           orcamento.entidade               as en,                 \n";
    $stSql .= "           sw_cgm                           as cgm,                \n";
    $stSql .= "           contabilidade.valor_lancamento   AS vl                  \n";
    $stSql .= "    LEFT JOIN                                                      \n";
    $stSql .= "           contabilidade.conta_credito AS cc                       \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                cc.cod_lote     = vl.cod_lote       AND            \n";
    $stSql .= "                cc.tipo         = vl.tipo           AND            \n";
    $stSql .= "                cc.sequencia    = vl.sequencia      AND            \n";
    $stSql .= "                cc.exercicio    = vl.exercicio      AND            \n";
    $stSql .= "                cc.tipo_valor   = vl.tipo_valor     AND            \n";
    $stSql .= "                cc.cod_entidade = vl.cod_entidade                  \n";
    $stSql .= "            )                                                      \n";
    $stSql .= "    LEFT JOIN                                                      \n";
    $stSql .= "           contabilidade.conta_debito AS cd                        \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                cd.cod_lote     = vl.cod_lote       AND            \n";
    $stSql .= "                cd.tipo         = vl.tipo           AND            \n";
    $stSql .= "                cd.sequencia    = vl.sequencia      AND            \n";
    $stSql .= "                cd.exercicio    = vl.exercicio      AND            \n";
    $stSql .= "                cd.tipo_valor   = vl.tipo_valor     AND            \n";
    $stSql .= "                cd.cod_entidade = vl.cod_entidade                  \n";
    $stSql .= "            )                                                      \n";
    $stSql .= "     LEFT JOIN                                                     \n";
    $stSql .= "           tesouraria.transferencia AS tt                          \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                tt.cod_lote     = vl.cod_lote       AND            \n";
    $stSql .= "                tt.tipo         = vl.tipo           AND            \n";
    $stSql .= "                tt.exercicio    = vl.exercicio      AND            \n";
    $stSql .= "                tt.cod_entidade = vl.cod_entidade                  \n";
    $stSql .= "            )                                                      \n";
    $stSql .= "     LEFT JOIN                                                     \n";
    $stSql .= "            tesouraria.transferencia_estornada  AS tte             \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                tte.cod_lote_estorno  = vl.cod_lote AND            \n";
    $stSql .= "                tte.tipo         = vl.tipo           AND           \n";
    $stSql .= "                tte.exercicio    = vl.exercicio      AND           \n";
    $stSql .= "                tte.cod_entidade = vl.cod_entidade                 \n";
    $stSql .= "            )                                                      \n";
    $stSql .= "      LEFT JOIN tesouraria.recibo_extra_transferencia AS ret       \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                ret.cod_lote     = vl.cod_lote       AND           \n";
    $stSql .= "                ret.tipo         = vl.tipo           AND           \n";
    $stSql .= "                ret.exercicio    = vl.exercicio      AND           \n";
    $stSql .= "                ret.cod_entidade = vl.cod_entidade                 \n";
    $stSql .= "            )                                                    \n";
    $stSql .= "     LEFT JOIN (SELECT tbl.exercicio                                      \n";
    $stSql .= "               ,tbl.cod_entidade                                          \n";
    $stSql .= "               ,tbll.tipo                                                  \n";
    $stSql .= "               ,tbll.cod_lote                                              \n";
    $stSql .= "               ,ta.observacao                                             \n";
    $stSql .= "         FROM                                                             \n";
    $stSql .= "                tesouraria.boletim_liberado AS tbl                        \n";
    $stSql .= "         LEFT JOIN tesouraria.arrecadacao AS ta                           \n";
    $stSql .= "                   ON  ta.exercicio    = tbl.exercicio                    \n";
    $stSql .= "                   AND ta.cod_entidade = tbl.cod_entidade                 \n";
    $stSql .= "                   AND ta.cod_boletim      = tbl.cod_boletim                    \n";
    $stSql .= "         JOIN tesouraria.boletim_liberado_lote as tbll               \n";
    $stSql .= "             	  ON tbll.cod_boletim = tbl.cod_boletim                      \n";
    $stSql .= "                  AND tbll.cod_entidade = tbl.cod_entidade               \n";
    $stSql .= "                	 AND tbll.exercicio = tbll.exercicio                        \n";
    $stSql .= "                	 AND tbll.timestamp_liberado = tbll.timestamp_liberado      \n";
    $stSql .= "                	 AND tbll.timestamp_fechamento = tbll.timestamp_fechamento  \n";
    $stSql .= "         WHERE                                                            \n";
    $stSql .= "                ta.exercicio    = tbl.exercicio                           \n";
    $stSql .= "                AND ta.cod_entidade = tbl.cod_entidade                    \n";
    $stSql .= "                AND ta.cod_boletim  = tbl.cod_boletim                     \n";
    $stSql .= "        ) AS tarrec                                                       \n";
    $stSql .= "          ON (                                                            \n";
    $stSql .= "             tarrec.cod_lote    = vl.cod_lote    AND                      \n";
    $stSql .= "            tarrec.tipo         = vl.tipo        AND                      \n";
    $stSql .= "            tarrec.exercicio    = vl.exercicio   AND                      \n";
    $stSql .= "            tarrec.cod_entidade = vl.cod_entidade                         \n";
    $stSql .= "            )                                                           \n";
    $stSql .= "     LEFT JOIN (SELECT                                                              \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote_estorno,               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.tipo,                           \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio,                      \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade,                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_recibo_extra             \n";
    $stSql .= "             FROM                                                                   \n";
    $stSql .= "                 tesouraria.transferencia_estornada                                 \n";
    $stSql .= "             LEFT JOIN tesouraria.recibo_extra_transferencia ON (                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.exercicio =                  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio AND                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_entidade =               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade  AND               \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_lote =                   \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote                        \n";
    $stSql .= "                 )                                                                  \n";
    $stSql .= "             WHERE                                                                  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio = ".$this->getDado('stExercicio')." AND  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade IN (".$this->getDado('stEntidade').") \n";
    $stSql .= "             GROUP BY                                                               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote_estorno,               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.tipo,                           \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio,                      \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade,                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_recibo_extra             \n";
    $stSql .= "                                                                                    \n";
    $stSql .= "            ) AS tret                                                               \n";
    $stSql .= "              ON (                                                                  \n";
    $stSql .= "                 tret.cod_lote_estorno  = vl.cod_lote    AND                        \n";
    $stSql .= "                 tret.tipo         = vl.tipo        AND                             \n";
    $stSql .= "                 tret.exercicio    = vl.exercicio   AND                             \n";
    $stSql .= "                 tret.cod_entidade = vl.cod_entidade                                \n";
    $stSql .= "                 )                                                                  \n";
    $stSql .= "      LEFT JOIN (SELECT                                                             \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote,                       \n";
    $stSql .= "                 tesouraria.transferencia_estornada.tipo,                           \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio,                      \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade,                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_recibo_extra             \n";
    $stSql .= "             FROM                                                                   \n";
    $stSql .= "                 tesouraria.transferencia_estornada                                 \n";
    $stSql .= "             LEFT JOIN tesouraria.recibo_extra_transferencia ON (                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.exercicio =                  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio AND                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_entidade =               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade  AND               \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_lote =                   \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote                        \n";
    $stSql .= "                 )                                                                  \n";
    $stSql .= "             WHERE                                                                  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio = ".$this->getDado('stExercicio')." AND  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade IN (".$this->getDado('stEntidade').") \n";
    $stSql .= "             GROUP BY                                                               \n";
    $stSql .= "                     tesouraria.transferencia_estornada.cod_lote,                   \n";
    $stSql .= "                      tesouraria.transferencia_estornada.tipo,                      \n";
    $stSql .= "                      tesouraria.transferencia_estornada.exercicio,                 \n";
    $stSql .= "                      tesouraria.transferencia_estornada.cod_entidade,              \n";
    $stSql .= "                      tesouraria.recibo_extra_transferencia.cod_recibo_extra        \n";
    $stSql .= "                                                                                    \n";
    $stSql .= "            ) AS tret2                                                              \n";
    $stSql .= "              ON (                                                                  \n";
    $stSql .= "                 tret2.cod_lote     = vl.cod_lote     AND                           \n";
    $stSql .= "                 tret2.tipo         = vl.tipo         AND                           \n";
    $stSql .= "                 tret2.exercicio    = vl.exercicio    AND                           \n";
    $stSql .= "            tret2.cod_entidade = vl.cod_entidade                                    \n";
    $stSql .= "            )                                                                       \n";
    $stSql .= "    WHERE                                                          \n";
    $stSql .= "            vl.cod_lote      = l.cod_lote        AND               \n";
    $stSql .= "            vl.tipo          = l.tipo            AND               \n";
    $stSql .= "            vl.sequencia     = l.sequencia       AND               \n";
    $stSql .= "            vl.exercicio     = l.exercicio       AND               \n";
    $stSql .= "            vl.cod_entidade  = l.cod_entidade    AND               \n";
    $stSql .= "            lo.cod_lote      = l.cod_lote        AND               \n";
    $stSql .= "            lo.exercicio     = l.exercicio       AND               \n";
    $stSql .= "            lo.tipo          = l.tipo            AND               \n";
    $stSql .= "            lo.cod_entidade  = l.cod_entidade    AND               \n";
    $stSql .= "            hc.cod_historico = l.cod_historico   AND               \n";
    $stSql .= "            hc.exercicio     = l.exercicio       AND               \n";
    $stSql .= "            en.cod_entidade  = l.cod_entidade    AND               \n";
    $stSql .= "            en.exercicio     = l.exercicio       AND               \n";
    $stSql .= "            cgm.numcgm       = en.numcgm                           \n";
    $stSql .= "             AND l.cod_entidade IN (".$this->getDado('stEntidade').")  ";
   $stSql  .= "             AND l.exercicio = '". $this->getDado('stExercicio')."' ";
   $stSql  .= "             AND lo.dt_lote >= TO_DATE('".$this->getDado("dtInicial")."','dd/mm/yyyy' )";
   $stSql  .= "             AND lo.dt_lote <= TO_DATE('".$this->getDado("dtFinal")."','dd/mm/yyyy') ";

    $stSql .= "    ORDER BY                                                       \n";
    $stSql .= "            to_date(dt_lote,'yyyy-mm-dd'),                         \n";
    $stSql .= "            l.cod_lote,                                            \n";
    $stSql .= "            l.cod_entidade,                                        \n";
    $stSql .= "            l.tipo,                                                \n";
    $stSql .= "            l.sequencia ASC,                                       \n";
    $stSql .= "            vl.vl_lancamento DESC,                                 \n";
    $stSql .= "            vl.tipo_valor DESC                                     \n";
    $stSql .= "    ) AS tabela,                                                   \n";
    $stSql .= "    contabilidade.plano_analitica AS pa,                           \n";
    $stSql .= "    contabilidade.plano_conta     AS pc                            \n";
    $stSql .= "WHERE tabela.cod_historico > 800 AND                     \n";
    $stSql .= "    tabela.cod_plano = pa.cod_plano     AND                        \n";
    $stSql .= "    tabela.exercicio = pa.exercicio     AND                        \n";
    $stSql .= "    pa.cod_conta     = pc.cod_conta     AND                        \n";
    $stSql .= "    pa.exercicio     = pc.exercicio                                \n";
    $stSql .= "GROUP BY                                                         \n";
    $stSql .= "            tabela.cod_lote,                                     \n";
    $stSql .= "            tabela.nom_lote,                                     \n";
    $stSql .= "            tabela.sequencia,                                    \n";
    $stSql .= "            tabela.tipo,                                         \n";
    $stSql .= "            tabela.cod_historico,                                \n";
    $stSql .= "            tabela.nom_historico,                                \n";
    $stSql .= "            tabela.exercicio,                                    \n";
    $stSql .= "            tabela.cod_entidade,                                 \n";
    $stSql .= "            tabela.complemento,                                  \n";
    $stSql .= "            tabela.cod_recibo_extra,                             \n";
    $stSql .= "            tabela.vl_lancamento,                                \n";
    $stSql .= "            tabela.tipo_valor,                                   \n";
    $stSql .= "            tabela.nom_cgm,                                      \n";
    $stSql .= "            tabela.dt_lote,                                      \n";
    $stSql .= "            tabela.cod_plano,                                    \n";
    $stSql .= "            pc.nom_conta,                                        \n";
    $stSql .= "            pc.cod_estrutural                                    \n";
    $stSql .= "ORDER BY pc.cod_estrutural                                    \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiÃ§Ã£o do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosK050(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosK050().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    //$this->debug();exit();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * MÃ©todo para montar SQL para recuperar dados do tipo K050
    * @access Private
    * @return String $stSql
*/
function montaRecuperaDadosK050()
{
    $inCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');
    $stEntidade = $this->getDado('entidade');
    $stExercicio = $this->getDado('exercicio');
    $inEntidade = $this->getDado('cod_entidade');

    $stSql .= "SELECT * FROM  (                                                                         \n";
    $stSql .= "           SELECT 'K050'  as reg                                                         \n";
    $stSql .= "       , (
                          SELECT sw_cgm_pessoa_juridica.cnpj
                            FROM sw_cgm_pessoa_juridica
                            JOIN sw_cgm
                              ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                            JOIN orcamento.entidade
                              ON entidade.numcgm = sw_cgm.numcgm
                           WHERE entidade.cod_entidade = ".$inEntidade."
                             AND entidade.exercicio = '".$stExercicio."'
                        ) as cnpj_cei                                                                           \n";
    $stSql .= "                 , to_char((select pega0datafinalcompetenciadoperiodomovimento(".$inCodPeriodoMovimentacao.")::date), 'dd/mm/yyyy') as dt_inc_alt \n";
    $stSql .= "                 , contrato.registro as cod_reg_trab                                     \n";
    $stSql .= "                 , servidor_contrato.*                                                   \n";
    $stSql .= "              FROM pessoal".$stEntidade.".contrato                                       \n";
    $stSql .= "                 , (                                                                     \n";
    $stSql .= "                    -- Inicio consulta servidores (ativos, aposentados e rescindidos)    \n";
    $stSql .= "                        SELECT                                                           \n";
    $stSql .= "                               contrato_servidor.cod_contrato                            \n";
    $stSql .= "                             , sw_cgm_pessoa_fisica.cpf as cpf                           \n";
    $stSql .= "                             , replace(replace(sw_cgm_pessoa_fisica.servidor_pis_pasep, '.',''), '-','') as nit            \n";
    $stSql .= "                             , cod_categoria as cod_categ                                \n";
    $stSql .= "                             , sw_cgm.nom_cgm as nome_trab                               \n";
    $stSql .= "                             , to_char(sw_cgm_pessoa_fisica.dt_nascimento,'dd/mm/yyyy') as dt_nasc \n";
    $stSql .= "                             , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_admissao::date,'dd/mm/yyyy') as dt_admissao \n";
    $stSql .= "                             , to_char(ultimo_contrato_servidor_caso_causa.dt_rescisao,'dd/mm/yyyy') as dt_demissao \n";
    $stSql .= "                             ,  CASE WHEN normas.cod_tipo_norma = 0 THEN 9  \n";
    $stSql .= "                                 WHEN normas.cod_tipo_norma = 2 THEN 1  \n";
    $stSql .= "                                 WHEN normas.cod_tipo_norma = 1 THEN 2  \n";
    $stSql .= "                                 WHEN normas.cod_tipo_norma = 4 OR normas.cod_tipo_norma = 5 OR normas.cod_tipo_norma = 6 THEN 3  \n";
    $stSql .= "                            END AS tipo_ato_nom                          \n";
    $stSql .= "                             , normas.numero_norma as nm_ato_nom                         \n";
    $stSql .= "                             , normas.dt_publicacao as dt_ato_nom                        \n";
    $stSql .= "                             , (CASE WHEN ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao IN (4,7) THEN 3     \n";
    $stSql .= "                                    WHEN ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao IN (1,2) THEN 4      \n";
    $stSql .= "                                    WHEN ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao IN (3,99,5) THEN 9   \n";
    $stSql .= "                                    WHEN ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao IN (6) THEN 5        \n";
    $stSql .= "                                    WHEN ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao IN (8,100) THEN 7    \n";
    $stSql .= "                                    ELSE 9                                               \n";
    $stSql .= "                               END ) as ind_vinc                                         \n";
    $stSql .= "                             , (SELECT descricao FROM pessoal".$stEntidade.".cargo WHERE cod_cargo = ultimo_contrato_servidor_funcao.cod_cargo) as desc_cargo \n";
    $stSql .= "                             , ( SELECT orgao                                            \n";
    $stSql .= "                                   FROM organograma.vw_orgao_nivel                       \n";
    $stSql .= "                                  WHERE cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao ) as cod_ltc                   \n";
    $stSql .= "                             , recuperaDescricaoOrgao(ultimo_contrato_servidor_orgao.cod_orgao, to_date((select pega0datafinalcompetenciadoperiodomovimento(".$inCodPeriodoMovimentacao.")), 'yyyy-mm-dd')) as desc_ltc \n";
    $stSql .= "                             , cbo.codigo as cod_cbo                                     \n";
    $stSql .= "                          FROM pessoal".$stEntidade.".contrato_servidor                  \n";
    $stSql .= "                    INNER JOIN pessoal".$stEntidade.".servidor_contrato_servidor         \n";
    $stSql .= "                            ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato \n";
    $stSql .= "                    INNER JOIN pessoal".$stEntidade.".servidor                           \n";
    $stSql .= "                            ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor \n";
    $stSql .= "                    INNER JOIN sw_cgm                                                    \n";
    $stSql .= "                            ON servidor.numcgm = sw_cgm.numcgm                           \n";
    $stSql .= "                    INNER JOIN sw_cgm_pessoa_fisica                                      \n";
    $stSql .= "                            ON sw_cgm_pessoa_fisica.numcgm=sw_cgm.numcgm                 \n";
    $stSql .= "                    INNER JOIN ultimo_contrato_servidor_orgao('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_orgao   \n";
    $stSql .= "                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_orgao.cod_contrato  \n";
    $stSql .= "                    INNER JOIN ultimo_contrato_servidor_nomeacao_posse('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_nomeacao_posse \n";
    $stSql .= "                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_nomeacao_posse.cod_contrato \n";
    $stSql .= "                    INNER JOIN ultimo_contrato_servidor_funcao('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_funcao \n";
    $stSql .= "                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_funcao.cod_contrato \n";
    $stSql .= "                    INNER JOIN ultimo_contrato_servidor_regime_funcao('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_regime_funcao \n";
    $stSql .= "                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_regime_funcao.cod_contrato \n";
    $stSql .= "                    INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_sub_divisao_funcao \n";
    $stSql .= "                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_sub_divisao_funcao.cod_contrato \n";
    $stSql .= "                    INNER JOIN (SELECT cbo_cargo.cod_cargo                               \n";
    $stSql .= "                                     , cbo_cargo.cod_cbo                                 \n";
    $stSql .= "                                     , cbo.codigo                                        \n";
    $stSql .= "                                  FROM pessoal".$stEntidade.".cbo,                       \n";
    $stSql .= "                                       pessoal".$stEntidade.".cbo_cargo                  \n";
    $stSql .= "                                  INNER JOIN ( SELECT cbo_cargo.cod_cargo                \n";
    $stSql .= "                                                     ,max(cbo_cargo.timestamp) as timestamp \n";
    $stSql .= "                                                 FROM pessoal".$stEntidade.".cbo_cargo   \n";
    $stSql .= "                                                WHERE cbo_cargo.timestamp <= (select ultimotimestampperiodomovimentacao(".$inCodPeriodoMovimentacao.",'".$stEntidade."')) \n";
    $stSql .= "                                                GROUP BY cbo_cargo.cod_cargo) as max_cbo_cargo \n";
    $stSql .= "                                          ON max_cbo_cargo.cod_cargo = cbo_cargo.cod_cargo \n";
    $stSql .= "                                         AND max_cbo_cargo.timestamp = cbo_cargo.timestamp \n";
    $stSql .= "                                 WHERE cbo.cod_cbo=cbo_cargo.cod_cbo) as cbo               \n";
    $stSql .= "                            ON cbo.cod_cargo = ultimo_contrato_servidor_funcao.cod_cargo   \n";
    $stSql .= "                     LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_especialidade_funcao \n";
    $stSql .= "                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_especialidade_funcao.cod_contrato \n";
    $stSql .= "                     LEFT JOIN ultimo_contrato_servidor_caso_causa('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_caso_causa \n";
    $stSql .= "                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_caso_causa.cod_contrato \n";
    $stSql .= "                           AND ultimo_contrato_servidor_caso_causa.dt_rescisao <= to_date((select pega0datafinalcompetenciadoperiodomovimento(".$inCodPeriodoMovimentacao.")::DATE), 'yyyy-mm-dd') \n";
    $stSql .= "                     LEFT JOIN (SELECT  cod_norma                                        \n";
    $stSql .= "                                       , cod_tipo_norma                                  \n";
    $stSql .= "                                       ,norma.num_norma||'/'||norma.exercicio as numero_norma \n";
    $stSql .= "                                       ,to_char(norma.dt_publicacao,'dd/mm/yyyy') as dt_publicacao \n";
    $stSql .= "                                  FROM normas.norma ) as normas                          \n";
    $stSql .= "                            ON normas.cod_norma = contrato_servidor.cod_norma            \n";
    $stSql .= "                     -- Fim consulta servidores (ativos, aposentados e rescindidos)      \n";
    $stSql .= "         ) as servidor_contrato                                                          \n";
    $stSql .= "            WHERE contrato.cod_contrato = servidor_contrato.cod_contrato                 \n";
    $stSql .= "            ) as servidores                                                              \n";
    $stSql .= "            WHERE to_date(servidores.dt_admissao,'dd-mm-yyyy') < (select pega0datafinalcompetenciadoperiodomovimento(".$inCodPeriodoMovimentacao.")::date) \n";
    $stSql .= "            ORDER BY nome_trab, cod_reg_trab                                             \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiÃ§Ã£o do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosK100(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosK100().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * MÃ©todo para montar SQL para recuperar dados do tipo K100
    * @access Private
    * @return String $stSql
*/
function montaRecuperaDadosK100()
{
    $inCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');
    $stEntidade = $this->getDado('entidade');
    $stExercicio = $this->getDado('exercicio');
    $inEntidade = $this->getDado('cod_entidade');

    $stSql .= "  SELECT 'K100' as reg                                                                           \n";
    $stSql .= "       , to_char((select pega0datafinalcompetenciadoperiodomovimento(".$inCodPeriodoMovimentacao.")::date), 'ddmmyyyy') as dt_inc_alt \n";
    $stSql .= "     --  , '".$this->getDado('dtInicial')."'as dt_inc_alt                                               \n";
    $stSql .= "       , contrato_servidor_orgao.orgao as cod_ltc                                                \n";
    $stSql .= "       , (
                          SELECT sw_cgm_pessoa_juridica.cnpj
                            FROM sw_cgm_pessoa_juridica
                            JOIN sw_cgm
                              ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                            JOIN orcamento.entidade
                              ON entidade.numcgm = sw_cgm.numcgm
                           WHERE entidade.cod_entidade = ".$inEntidade."
                             AND entidade.exercicio = '".$stExercicio."'
                        ) as cnpj_cei                                                                           \n";
    $stSql .= "       , recuperaDescricaoOrgao(ultimo_contrato_servidor_orgao.cod_orgao,
                                               to_date((select pega0datafinalcompetenciadoperiodomovimento(".$inCodPeriodoMovimentacao.")), 'yyyy-mm-dd')
                                              ) as desc_ltc                                                     \n";
    $stSql .= "       , '' as cnpj_cei_tom                                                                      \n";
    $stSql .= "    FROM pessoal".$stEntidade.".contrato_servidor                                                \n";
    $stSql .= "INNER JOIN pessoal".$stEntidade.".contrato                                                       \n";
    $stSql .= "           ON contrato.cod_contrato = contrato_servidor.cod_contrato                             \n";
    $stSql .= "   INNER JOIN (SELECT cod_contrato, orgao                                                        \n";
    $stSql .= "                 FROM organograma.vw_orgao_nivel,                                                \n";
    $stSql .= "                      ultimo_contrato_servidor_orgao('', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_orgao \n";
    $stSql .= "                WHERE vw_orgao_nivel.cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao ) as contrato_servidor_orgao                             \n";
    $stSql .= "           ON contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato              \n";
    $stSql .= "   INNER JOIN ultimo_contrato_servidor_orgao('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_orgao   \n";
    $stSql .= "                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_orgao.cod_contrato  \n";
    $stSql .= "   GROUP BY contrato_servidor_orgao.orgao                                                        \n";
    $stSql .= "          , ultimo_contrato_servidor_orgao.cod_orgao                                             \n";
    $stSql .= "   ORDER BY contrato_servidor_orgao.orgao                                                        \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiÃ§Ã£o do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosK150(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosK150().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * MÃ©todo para montar SQL para recuperar dados do tipo K150
    * @access Private
    * @return String $stSql
*/
function montaRecuperaDadosK150()
{
    $inCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');
    $stEntidade = $this->getDado('entidade');
    $stExercicio = $this->getDado('exercicio');
    $inEntidade = $this->getDado('cod_entidade');

    $stSql .= "  SELECT 'K150' as reg                                                                           \n";
    $stSql .= "       , (
                          SELECT sw_cgm_pessoa_juridica.cnpj
                            FROM sw_cgm_pessoa_juridica
                            JOIN sw_cgm
                              ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                            JOIN orcamento.entidade
                              ON entidade.numcgm = sw_cgm.numcgm
                           WHERE entidade.cod_entidade = ".$inEntidade."
                             AND entidade.exercicio = '".$stExercicio."'
                        ) as cnpj_cei                                                                           \n";
    $stSql .= "       , to_char((select pega0datafinalcompetenciadoperiodomovimento(".$inCodPeriodoMovimentacao.")::date), 'ddmmyyyy') as dt_inc_alt \n";
    $stSql .= "       , folhas.codigo as cod_rubrica                                                               \n";
    $stSql .= "       , folhas.descricao as desc_rubrica                                                           \n";
    $stSql .= "    FROM pessoal".$stEntidade.".contrato_servidor                                                \n";
    $stSql .= "INNER JOIN pessoal".$stEntidade.".contrato                                                       \n";
    $stSql .= "           ON contrato.cod_contrato = contrato_servidor.cod_contrato                             \n";
    $stSql .= "   INNER JOIN (SELECT cod_contrato, orgao                                                        \n";
    $stSql .= "                 FROM organograma.vw_orgao_nivel,                                                \n";
    $stSql .= "                      ultimo_contrato_servidor_orgao('', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_orgao \n";
    $stSql .= "                WHERE vw_orgao_nivel.cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao ) as contrato_servidor_orgao                             \n";
    $stSql .= "           ON contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato              \n";
    $stSql .= "    INNER JOIN (                                                                                 \n";
    $stSql .= "          SELECT '1'::varchar as ind_fl                                                          \n";
    $stSql .= "               , registro_evento_periodo.cod_contrato                                            \n";
    $stSql .= "               , evento.codigo                                                                   \n";
    $stSql .= "               , evento.descricao                                                                \n";
    $stSql .= "               , evento.natureza                                                                 \n";
    $stSql .= "               , evento_calculado.valor                                                          \n";
    $stSql .= "               , evento_calculado.quantidade                                                     \n";
    $stSql .= "               , CASE WHEN evento_calculado.desdobramento IS NULL THEN ''::varchar               \n";
    $stSql .= "               ELSE evento_calculado.desdobramento END AS desdobramento                          \n";
    $stSql .= "          FROM folhapagamento".$stEntidade.".registro_evento_periodo                             \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento_calculado                                    \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento                                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo_evento                            \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo                                   \n";
    $stSql .= "         WHERE registro_evento_periodo.cod_registro             = evento_calculado.cod_registro  \n";
    $stSql .= "           AND evento_calculado.cod_evento                      = evento.cod_evento              \n";
    $stSql .= "           AND evento.cod_evento                                = sequencia_calculo_evento.cod_evento \n";
    $stSql .= "           AND sequencia_calculo_evento.cod_sequencia           = sequencia_calculo.cod_sequencia \n";
    $stSql .= "           AND registro_evento_periodo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."  \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        UNION                                                                                    \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "         SELECT '3'::varchar as ind_fl                                                           \n";
    $stSql .= "             , registro_evento_ferias.cod_contrato                                               \n";
    $stSql .= "             , evento.codigo                                                                     \n";
    $stSql .= "             , evento.descricao                                                                  \n";
    $stSql .= "             , evento.natureza                                                                   \n";
    $stSql .= "             , evento_ferias_calculado.valor                                                     \n";
    $stSql .= "             , evento_ferias_calculado.quantidade                                                \n";
    $stSql .= "             , evento_ferias_calculado.desdobramento                                             \n";
    $stSql .= "          FROM folhapagamento".$stEntidade.".registro_evento_ferias                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento_ferias_calculado                             \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento                                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo_evento                            \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo                                   \n";
    $stSql .= "         WHERE registro_evento_ferias.cod_registro             = evento_ferias_calculado.cod_registro  \n";
    $stSql .= "           AND registro_evento_ferias.desdobramento            = evento_ferias_calculado.desdobramento \n";
    $stSql .= "           AND registro_evento_ferias.timestamp                = evento_ferias_calculado.timestamp_registro \n";
    $stSql .= "           AND registro_evento_ferias.cod_evento               = evento_ferias_calculado.cod_evento \n";
    $stSql .= "           AND evento_ferias_calculado.cod_evento              = evento.cod_evento               \n";
    $stSql .= "           AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento \n";
    $stSql .= "           AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia \n";
    $stSql .= "           AND registro_evento_ferias.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."   \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        UNION                                                                                    \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        SELECT '2'::varchar as ind_fl                                                            \n";
    $stSql .= "             , registro_evento_decimo.cod_contrato                                               \n";
    $stSql .= "             , evento.codigo                                                                     \n";
    $stSql .= "             , evento.descricao                                                                  \n";
    $stSql .= "             , evento.natureza                                                                   \n";
    $stSql .= "             , evento_decimo_calculado.valor                                                     \n";
    $stSql .= "             , evento_decimo_calculado.quantidade                                                \n";
    $stSql .= "             , evento_decimo_calculado.desdobramento                                             \n";
    $stSql .= "          FROM folhapagamento".$stEntidade.".registro_evento_decimo                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento_decimo_calculado                             \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento                                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo_evento                            \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo                                   \n";
    $stSql .= "         WHERE registro_evento_decimo.cod_registro             = evento_decimo_calculado.cod_registro    \n";
    $stSql .= "           AND registro_evento_decimo.cod_evento               = evento_decimo_calculado.cod_evento      \n";
    $stSql .= "           AND registro_evento_decimo.desdobramento            = evento_decimo_calculado.desdobramento   \n";
    $stSql .= "           AND registro_evento_decimo.timestamp                = evento_decimo_calculado.timestamp_registro \n";
    $stSql .= "           AND evento_decimo_calculado.cod_evento              = evento.cod_evento               \n";
    $stSql .= "           AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento     \n";
    $stSql .= "           AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia \n";
    $stSql .= "           AND registro_evento_decimo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."   \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        UNION                                                                                    \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        SELECT '6'::varchar as ind_fl                                                            \n";
    $stSql .= "             , registro_evento_rescisao.cod_contrato                                             \n";
    $stSql .= "             , evento.codigo                                                                     \n";
    $stSql .= "             , evento.descricao                                                                  \n";
    $stSql .= "             , evento.natureza                                                                   \n";
    $stSql .= "             , evento_rescisao_calculado.valor                                                   \n";
    $stSql .= "             , evento_rescisao_calculado.quantidade                                              \n";
    $stSql .= "             , evento_rescisao_calculado.desdobramento                                           \n";
    $stSql .= "          FROM folhapagamento".$stEntidade.".registro_evento_rescisao                            \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento_rescisao_calculado                           \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento                                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo_evento                            \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo                                   \n";
    $stSql .= "         WHERE registro_evento_rescisao.cod_registro             = evento_rescisao_calculado.cod_registro    \n";
    $stSql .= "           AND registro_evento_rescisao.cod_evento               = evento_rescisao_calculado.cod_evento      \n";
    $stSql .= "           AND registro_evento_rescisao.desdobramento            = evento_rescisao_calculado.desdobramento   \n";
    $stSql .= "           AND registro_evento_rescisao.timestamp                = evento_rescisao_calculado.timestamp_registro \n";
    $stSql .= "           AND evento_rescisao_calculado.cod_evento              = evento.cod_evento             \n";
    $stSql .= "           AND evento.cod_evento                                 = sequencia_calculo_evento.cod_evento       \n";
    $stSql .= "           AND sequencia_calculo_evento.cod_sequencia            = sequencia_calculo.cod_sequencia           \n";
    $stSql .= "           AND registro_evento_rescisao.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao." \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        UNION                                                                                    \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        SELECT '4'::varchar as ind_fl                                                            \n";
    $stSql .= "             , registro_evento_complementar.cod_contrato                                         \n";
    $stSql .= "             , evento.codigo                                                                     \n";
    $stSql .= "             , evento.descricao                                                                  \n";
    $stSql .= "             , evento.natureza                                                                   \n";
    $stSql .= "             , evento_complementar_calculado.valor                                               \n";
    $stSql .= "             , evento_complementar_calculado.quantidade                                          \n";
    $stSql .= "             , CASE WHEN evento_complementar_calculado.desdobramento IS NULL THEN ''::varchar    \n";
    $stSql .= "               ELSE evento_complementar_calculado.desdobramento END AS desdobramento             \n";
    $stSql .= "          FROM folhapagamento".$stEntidade.".registro_evento_complementar                        \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento_complementar_calculado                       \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento                                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo_evento                            \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo                                   \n";
    $stSql .= "         WHERE registro_evento_complementar.cod_registro             = evento_complementar_calculado.cod_registro    \n";
    $stSql .= "           AND registro_evento_complementar.cod_evento               = evento_complementar_calculado.cod_evento      \n";
    $stSql .= "           AND registro_evento_complementar.cod_configuracao         = evento_complementar_calculado.cod_configuracao \n";
    $stSql .= "           AND registro_evento_complementar.timestamp                = evento_complementar_calculado.timestamp_registro \n";
    $stSql .= "           AND evento_complementar_calculado.cod_evento              = evento.cod_evento         \n";
    $stSql .= "           AND evento.cod_evento                                     = sequencia_calculo_evento.cod_evento   \n";
    $stSql .= "           AND sequencia_calculo_evento.cod_sequencia                = sequencia_calculo.cod_sequencia       \n";
    $stSql .= "           AND registro_evento_complementar.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao." \n";
    $stSql .= "        ) as folhas                                                                              \n";
    $stSql .= "       ON contrato.cod_contrato = folhas.cod_contrato                                            \n";
    $stSql .= "   GROUP BY folhas.codigo, folhas.descricao                                                      \n";
    $stSql .= "   ORDER BY folhas.codigo                                                                        \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiÃ§Ã£o do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosK250(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosK250().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * MÃ©todo para montar SQL para recuperar dados do tipo K250
    * @access Private
    * @return String $stSql
*/
function montaRecuperaDadosK250()
{
    $inCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');
    $stEntidade = $this->getDado('entidade');
    $stExercicio = $this->getDado('exercicio');
    $inEntidade = $this->getDado('cod_entidade');

    $stSql .= "SELECT resultado.reg
                      , resultado.cnpj_cei
                      , resultado.cod_ltc
                      , resultado.cod_reg_trab
                      , resultado.cod_contrato
                      , resultado.dt_comp
                      , resultado.dt_pgto
                      , resultado.cod_cbo
                      , resultado.cod_ocorr
                      , resultado.desc_cargo
                      , resultado.ind_fl
                      , SUM(resultado.quant_dep_irr)           AS quant_dep_irrf
                      , SUM(resultado.quant_dep_sal_familia)   AS quant_dep_sal_familia
                      , SUM(resultado.vl_base_irrf)            AS vl_base_irrf
                      , SUM(resultado.vl_base_ps)              AS vl_base_ps

                FROM (
                        SELECT tabela.reg
                            , tabela.cnpj_cei
                            , tabela.cod_ltc
                            , tabela.cod_reg_trab
                            , tabela.cod_contrato
                            , tabela.dt_comp
                            , tabela.dt_pgto
                            , tabela.cod_cbo
                            , tabela.cod_ocorr
                            , tabela.desc_cargo
                            , consulta_folhas.ind_fl
                            , consulta_folhas.quant_dep_irr
                            , consulta_folhas.quant_dep_sal_familia
                            , consulta_folhas.vl_base_irrf
                            , consulta_folhas.vl_base_ps

                FROM (
                        SELECT DISTINCT 'K250' AS reg
                                        , (
                                            SELECT sw_cgm_pessoa_juridica.cnpj
                                              FROM sw_cgm_pessoa_juridica
                                              JOIN sw_cgm
                                                ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                                              JOIN orcamento.entidade
                                                ON entidade.numcgm = sw_cgm.numcgm
                                             WHERE entidade.cod_entidade = ".$inEntidade."
                                               AND entidade.exercicio    = '".$stExercicio."'
                                        )                                                                                    AS cnpj_cei
                                        , contrato_servidor_orgao.orgao                                                      AS cod_ltc
                                        , contrato.registro                                                                  AS cod_reg_trab
                                        , contrato.cod_contrato
                                        , to_char((SELECT pega0datafinalcompetenciadoperiodomovimento(".$inCodPeriodoMovimentacao.")::DATE), 'mmyyyy') AS dt_comp
                                        , to_char((SELECT pega0datafinalcompetenciadoperiodomovimento(".$inCodPeriodoMovimentacao.")::DATE), 'ddmmyyyy') AS dt_pgto
                                        , cbo.codigo                                                                         AS cod_cbo
                                        , contrato_servidor.cod_categoria                                                    AS cod_ocorr
                                        , (
                                            SELECT descricao
                                              FROM pessoal.cargo
                                             WHERE cod_cargo = ultimo_contrato_servidor_funcao.cod_cargo
                                        )                                                                                    AS desc_cargo
                                  FROM pessoal".$stEntidade.".contrato_servidor
                                  JOIN pessoal".$stEntidade.".contrato
                                    ON contrato.cod_contrato = contrato_servidor.cod_contrato
                                  JOIN ultimo_contrato_servidor_funcao('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_funcao
                                    ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_funcao.cod_contrato
                                  JOIN (
                                            SELECT cbo_cargo.cod_cargo
                                                   , cbo_cargo.cod_cbo
                                                   , cbo.codigo
                                              FROM pessoal.cbo,
                                                   pessoal.cbo_cargo
                                              JOIN (
                                                    SELECT cbo_cargo.cod_cargo
                                                           , max(cbo_cargo.timestamp) as timestamp
                                                      FROM pessoal.cbo_cargo
                                                     WHERE cbo_cargo.timestamp <= (select ultimotimestampperiodomovimentacao('".$inCodPeriodoMovimentacao."','".$stEntidade."'))
                                                     GROUP BY cbo_cargo.cod_cargo
                                                ) as max_cbo_cargo
                                                ON max_cbo_cargo.cod_cargo = cbo_cargo.cod_cargo
                                               AND max_cbo_cargo.timestamp = cbo_cargo.timestamp
                                             WHERE cbo.cod_cbo=cbo_cargo.cod_cbo
                                    ) as cbo
                                    ON cbo.cod_cargo = ultimo_contrato_servidor_funcao.cod_cargo
                                  JOIN (
                                        SELECT cod_contrato, orgao
                                          FROM organograma.vw_orgao_nivel
                                               , ultimo_contrato_servidor_orgao('', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_orgao
                                         WHERE vw_orgao_nivel.cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao
                                    ) as contrato_servidor_orgao
                                    ON contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato
                                 --WHERE contrato.registro = 61
                                 GROUP BY contrato_servidor_orgao.orgao
                                          , contrato.registro
                                          , contrato.cod_contrato
                                          , cbo.codigo
                                          , contrato_servidor.cod_categoria
                                          , ultimo_contrato_servidor_funcao.cod_cargo
                     ) AS tabela

                JOIN (
                      SELECT folhas.ind_fl
                             , folhas.cod_contrato
                             , CASE WHEN evento_dependentes_irrf.codigo IS NOT NULL
                    THEN folhas.quantidade
                                    ELSE 0
                               END                                                                                              AS quant_dep_irr
                             , CASE WHEN evento_salario_familia.codigo  IS NOT NULL
                                    THEN folhas.quantidade
                                    ELSE 0
                               END                                                                                              AS quant_dep_sal_familia
                             , CASE WHEN eventos_base_irrf.codigo IS NOT NULL
                                         AND folhas.ind_fl != '2'
                                    THEN folhas.valor
                                    ELSE 0.00
                               END                                                                                              AS vl_base_irrf
                             , CASE WHEN eventos_base_desc_prev.codigo IS NOT NULL
                                         AND eventos_base_desc_prev.cod_tipo = 2
                                         AND folhas.desdobramento != 'D'
                                    THEN folhas.valor
                                    ELSE 0.00
                               END                                                                                              AS vl_base_ps
                        FROM (
                              SELECT DISTINCT '1'::varchar as ind_fl
                                              , registro_evento_periodo.cod_contrato
                                              , evento.codigo
                                              , evento_calculado.valor
                                              , evento_calculado.quantidade
                                              , CASE WHEN evento_calculado.desdobramento IS NULL
                                                     THEN ''::varchar
                                                     ELSE evento_calculado.desdobramento
                                                END AS desdobramento
                                        FROM folhapagamento".$stEntidade.".registro_evento_periodo
                                             , folhapagamento".$stEntidade.".evento_calculado
                                             , folhapagamento".$stEntidade.".evento
                                             , folhapagamento".$stEntidade.".sequencia_calculo_evento
                                             , folhapagamento".$stEntidade.".sequencia_calculo
                                       WHERE registro_evento_periodo.cod_registro             = evento_calculado.cod_registro
                                         AND evento_calculado.cod_evento                      = evento.cod_evento
                                         AND evento.cod_evento                                = sequencia_calculo_evento.cod_evento
                                         AND sequencia_calculo_evento.cod_sequencia           = sequencia_calculo.cod_sequencia
                                         AND registro_evento_periodo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."
                                UNION

                               SELECT '3'::varchar as ind_fl
                                      , registro_evento_ferias.cod_contrato
                                      , evento.codigo
                                      , evento_ferias_calculado.valor
                                      , evento_ferias_calculado.quantidade
                                      , evento_ferias_calculado.desdobramento
                                 FROM folhapagamento".$stEntidade.".registro_evento_ferias
                                      , folhapagamento".$stEntidade.".evento_ferias_calculado
                                      , folhapagamento".$stEntidade.".evento
                                      , folhapagamento".$stEntidade.".sequencia_calculo_evento
                                      , folhapagamento".$stEntidade.".sequencia_calculo
                                WHERE registro_evento_ferias.cod_registro             = evento_ferias_calculado.cod_registro
                                  AND registro_evento_ferias.desdobramento            = evento_ferias_calculado.desdobramento
                                  AND registro_evento_ferias.timestamp                = evento_ferias_calculado.timestamp_registro
                                  AND registro_evento_ferias.cod_evento               = evento_ferias_calculado.cod_evento
                                  AND evento_ferias_calculado.cod_evento              = evento.cod_evento
                                  AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento
                                  AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia
                                  AND registro_evento_ferias.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."

                                UNION

                               SELECT '2'::varchar as ind_fl
                                      , registro_evento_decimo.cod_contrato
                                      , evento.codigo
                                      , evento_decimo_calculado.valor
                                      , evento_decimo_calculado.quantidade
                                      , evento_decimo_calculado.desdobramento
                                 FROM folhapagamento".$stEntidade.".registro_evento_decimo
                                      , folhapagamento".$stEntidade.".evento_decimo_calculado
                                      , folhapagamento".$stEntidade.".evento
                                      , folhapagamento".$stEntidade.".sequencia_calculo_evento
                                      , folhapagamento".$stEntidade.".sequencia_calculo
                                WHERE registro_evento_decimo.cod_registro             = evento_decimo_calculado.cod_registro
                                  AND registro_evento_decimo.cod_evento               = evento_decimo_calculado.cod_evento
                                  AND registro_evento_decimo.desdobramento            = evento_decimo_calculado.desdobramento
                                  AND registro_evento_decimo.timestamp                = evento_decimo_calculado.timestamp_registro
                                  AND evento_decimo_calculado.cod_evento              = evento.cod_evento
                                  AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento
                                  AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia
                                  AND registro_evento_decimo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."

                                UNION

                               SELECT DISTINCT
                                                CASE WHEN evento_rescisao_calculado.desdobramento = 'S' THEN '1'::varchar
                                                         WHEN evento_rescisao_calculado.desdobramento = 'D' THEN '2'::varchar
                                                         WHEN evento_rescisao_calculado.desdobramento = 'P'  THEN '3'::varchar
                                                         WHEN evento_rescisao_calculado.desdobramento = 'V' THEN '6'::varchar
                                                ELSE '6'::varchar
                                                END as ind_fl
                                               , registro_evento_rescisao.cod_contrato
                                               , evento.codigo
                                               , evento_rescisao_calculado.valor
                                               , evento_rescisao_calculado.quantidade
                                               , evento_rescisao_calculado.desdobramento
                                          FROM folhapagamento".$stEntidade.".registro_evento_rescisao
                                               , folhapagamento".$stEntidade.".evento_rescisao_calculado
                                               , folhapagamento".$stEntidade.".evento
                                               , folhapagamento".$stEntidade.".sequencia_calculo_evento
                                               , folhapagamento".$stEntidade.".sequencia_calculo
                                         WHERE registro_evento_rescisao.cod_registro             = evento_rescisao_calculado.cod_registro
                                           AND registro_evento_rescisao.cod_evento               = evento_rescisao_calculado.cod_evento
                                           AND registro_evento_rescisao.desdobramento            = evento_rescisao_calculado.desdobramento
                                           AND registro_evento_rescisao.timestamp                = evento_rescisao_calculado.timestamp_registro
                                           AND evento_rescisao_calculado.cod_evento              = evento.cod_evento
                                           AND evento.cod_evento                                 = sequencia_calculo_evento.cod_evento
                                           AND sequencia_calculo_evento.cod_sequencia            = sequencia_calculo.cod_sequencia
                                           AND registro_evento_rescisao.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."

                                UNION

                               SELECT DISTINCT '4'::varchar as ind_fl
                                               , registro_evento_complementar.cod_contrato
                                               , evento.codigo
                                               , evento_complementar_calculado.valor
                                               , evento_complementar_calculado.quantidade
                                               , CASE WHEN evento_complementar_calculado.desdobramento IS NULL
                                                      THEN ''::varchar
                                                      ELSE evento_complementar_calculado.desdobramento
                                                 END AS desdobramento
                                          FROM folhapagamento".$stEntidade.".registro_evento_complementar
                                               , folhapagamento".$stEntidade.".evento_complementar_calculado
                                               , folhapagamento".$stEntidade.".evento
                                               , folhapagamento".$stEntidade.".sequencia_calculo_evento
                                               , folhapagamento".$stEntidade.".sequencia_calculo
                                         WHERE registro_evento_complementar.cod_registro             = evento_complementar_calculado.cod_registro
                                           AND registro_evento_complementar.cod_evento               = evento_complementar_calculado.cod_evento
                                           AND registro_evento_complementar.cod_configuracao         = evento_complementar_calculado.cod_configuracao
                                           AND registro_evento_complementar.timestamp                = evento_complementar_calculado.timestamp_registro
                                           AND evento_complementar_calculado.cod_evento              = evento.cod_evento
                                           AND evento.cod_evento                                     = sequencia_calculo_evento.cod_evento
                                           AND sequencia_calculo_evento.cod_sequencia                = sequencia_calculo.cod_sequencia
                                           AND registro_evento_complementar.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."
                            ) AS folhas
                   LEFT JOIN (
                                SELECT evento.codigo
                                       , tabela_irrf_evento.cod_tabela
                                       , tabela_irrf_evento.cod_tipo
                                       , tabela_irrf_evento.cod_evento
                                  FROM folhapagamento".$stEntidade.".evento
                                  JOIN (
                                         SELECT tabela_irrf_evento.cod_tabela
                                                , tabela_irrf_evento.cod_tipo
                                                , tabela_irrf_evento.cod_evento
                                           FROM folhapagamento".$stEntidade.".tabela_irrf_evento
                                           JOIN (
                                                    SELECT cod_tabela
                                                           , cod_tipo
                                                           , MAX(timestamp) AS timestamp
                                                      FROM folhapagamento".$stEntidade.".tabela_irrf_evento
                                                     WHERE cod_tabela = 1
                                                       AND cod_tipo   = 7
                                                       AND timestamp <= (select ultimotimestampperiodomovimentacao(".$inCodPeriodoMovimentacao.",''))
                                                     GROUP BY cod_tabela
                                                              , cod_tipo
                                                ) AS max_tabela_irrf_evento
                                             ON max_tabela_irrf_evento.cod_tabela = tabela_irrf_evento.cod_tabela
                                            AND max_tabela_irrf_evento.cod_tipo   = tabela_irrf_evento.cod_tipo
                                            AND max_tabela_irrf_evento.timestamp  = tabela_irrf_evento.timestamp
                                    ) AS tabela_irrf_evento
                                    ON tabela_irrf_evento.cod_evento = evento.cod_evento
                            ) AS eventos_base_irrf
                          ON eventos_base_irrf.codigo = folhas.codigo

                   LEFT JOIN (
                                SELECT evento.codigo
                                       , previdencia_evento.cod_tipo
                                       , previdencia_evento.cod_previdencia
                                  FROM folhapagamento".$stEntidade.".evento
                                  JOIN (
                                         SELECT previdencia_evento.cod_tipo
                                                , previdencia_evento.cod_previdencia
                                                , previdencia_evento.cod_evento
                                           FROM folhapagamento".$stEntidade.".previdencia_evento
                                           JOIN (
                                                    SELECT cod_tipo
                                                           , cod_previdencia
                                                           , MAX(timestamp) AS timestamp
                                                      FROM folhapagamento".$stEntidade.".previdencia_evento
                                                     WHERE timestamp <= (select ultimotimestampperiodomovimentacao(".$inCodPeriodoMovimentacao.",''))
                                                  GROUP BY cod_tipo
                                                         , cod_previdencia
                                                ) AS max_previdencia_evento
                                             ON max_previdencia_evento.cod_tipo        = previdencia_evento.cod_tipo
                                            AND max_previdencia_evento.cod_previdencia = previdencia_evento.cod_previdencia
                                            AND max_previdencia_evento.timestamp       = previdencia_evento.timestamp
                                    ) AS previdencia_evento
                                    ON previdencia_evento.cod_evento = evento.cod_evento
                            ) AS eventos_base_desc_prev
                          ON eventos_base_desc_prev.codigo = folhas.codigo

                   LEFT JOIN (
                                SELECT evento.codigo
                                  FROM folhapagamento".$stEntidade.".evento
                                  JOIN (
                                        SELECT salario_familia_evento.cod_evento
                                               , salario_familia_evento.cod_regime_previdencia
                                               , salario_familia_evento.cod_tipo
                                          FROM folhapagamento".$stEntidade.".salario_familia_evento
                                          JOIN (
                                                 SELECT salario_familia.cod_regime_previdencia
                                                        , salario_familia.timestamp
                                                   FROM folhapagamento".$stEntidade.".salario_familia
                                                   JOIN (
                                                            SELECT cod_regime_previdencia
                                                                   , MAX(timestamp) AS timestamp
                                                              FROM folhapagamento.salario_familia
                                                             WHERE vigencia <= (select ultimotimestampperiodomovimentacao(".$inCodPeriodoMovimentacao.",''))
                                                             GROUP BY cod_regime_previdencia
                                                        ) AS max_salario_familia
                                                     ON max_salario_familia.cod_regime_previdencia = salario_familia.cod_regime_previdencia
                                                    AND max_salario_familia.timestamp              = salario_familia.timestamp
                                            ) AS salario_familia
                                            ON salario_familia.cod_regime_previdencia = salario_familia_evento.cod_regime_previdencia
                                           AND salario_familia.timestamp              = salario_familia_evento.timestamp
                                    ) AS salario_familia_evento
                                   ON salario_familia_evento.cod_evento = evento.cod_evento
                            ) AS evento_salario_familia
                          ON evento_salario_familia.codigo = folhas.codigo

                   LEFT JOIN (
                                SELECT evento.codigo
                                  FROM folhapagamento".$stEntidade.".evento
                                  JOIN (
                                         SELECT tabela_irrf_evento.cod_tabela
                                                , tabela_irrf_evento.cod_tipo
                                                , tabela_irrf_evento.cod_evento
                                           FROM folhapagamento".$stEntidade.".tabela_irrf_evento
                                           JOIN (
                                                    SELECT cod_tabela
                                                           , cod_tipo
                                                           , MAX(timestamp) AS timestamp
                                                      FROM folhapagamento".$stEntidade.".tabela_irrf_evento
                                                     WHERE --cod_tabela = 1
                                                       --AND cod_tipo   = 1
                                                       timestamp <= (select ultimotimestampperiodomovimentacao(".$inCodPeriodoMovimentacao.",''))
                                                     GROUP BY cod_tabela
                                                              , cod_tipo
                                                ) AS max_tabela_irrf_evento
                                             ON max_tabela_irrf_evento.cod_tabela = tabela_irrf_evento.cod_tabela
                                            AND max_tabela_irrf_evento.cod_tipo   = tabela_irrf_evento.cod_tipo
                                            AND max_tabela_irrf_evento.timestamp  = tabela_irrf_evento.timestamp
                                    ) AS tabela_irrf_evento
                                    ON tabela_irrf_evento.cod_evento = evento.cod_evento
                            ) AS evento_dependentes_irrf
                          ON evento_dependentes_irrf.codigo = folhas.codigo
                ) AS consulta_folhas
                  ON tabela.cod_contrato = consulta_folhas.cod_contrato

               GROUP BY tabela.reg
                        , tabela.cnpj_cei
                        , tabela.cod_ltc
                        , tabela.cod_reg_trab
                        , tabela.cod_contrato
                        , tabela.dt_comp
                        , tabela.dt_pgto
                        , tabela.cod_cbo
                        , tabela.cod_ocorr
                        , tabela.desc_cargo
                        , consulta_folhas.ind_fl
                        , consulta_folhas.quant_dep_irr
                        , consulta_folhas.quant_dep_sal_familia
                        , consulta_folhas.vl_base_irrf
                        , consulta_folhas.vl_base_ps
               ORDER BY consulta_folhas.ind_fl

                    ) AS resultado
                GROUP BY resultado.reg
                         , resultado.cnpj_cei
                         , resultado.cod_ltc
                         , resultado.cod_reg_trab
                         , resultado.cod_contrato
                         , resultado.dt_comp
                         , resultado.dt_pgto
                         , resultado.cod_cbo
                         , resultado.cod_ocorr
                         , resultado.desc_cargo
                         , resultado.ind_fl";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiÃ§Ã£o do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosK300(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosK300().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * MÃ©todo para montar SQL para recuperar dados do tipo K300
    * @access Private
    * @return String $stSql
*/
function montaRecuperaDadosK300()
{
    $inCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');
    $stEntidade = $this->getDado('entidade');
    $stExercicio = $this->getDado('exercicio');
    $inEntidade = $this->getDado('cod_entidade');

    $stSql .= "  SELECT 'K300' as reg                                                                           \n";
    $stSql .= "       , (
                          SELECT sw_cgm_pessoa_juridica.cnpj
                            FROM sw_cgm_pessoa_juridica
                            JOIN sw_cgm
                              ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                            JOIN orcamento.entidade
                              ON entidade.numcgm = sw_cgm.numcgm
                           WHERE entidade.cod_entidade = ".$inEntidade."
                             AND entidade.exercicio = '".$stExercicio."'
                        ) as cnpj_cei                                                                           \n";
    $stSql .= "       , folhas.ind_fl as ind_fl                                                                 \n";
    $stSql .= "       , contrato_servidor_orgao.orgao as cod_ltc                                                \n";
    $stSql .= "       , contrato.registro as cod_reg_trab                                                       \n";
    $stSql .= "       , to_char((select pega0datafinalcompetenciadoperiodomovimento(".$inCodPeriodoMovimentacao.")::date), 'mmyyyy') as dt_comp \n";
    $stSql .= "       , folhas.codigo as cod_rubr                                                               \n";
    $stSql .= "       , folhas.valor  as vlr_rubr                                                               \n";
    $stSql .= "       , CASE WHEN folhas.natureza NOT IN ('P','D') THEN 'O'                                     \n";
    $stSql .= "              ELSE folhas.natureza                                                               \n";
    $stSql .= "         END as ind_rubr                                                                         \n";
    $stSql .= "       ,CASE WHEN eventos_base_irrf.codigo IS NOT NULL AND folhas.ind_fl  = '2' THEN '2'         \n";
    $stSql .= "             WHEN eventos_base_irrf.codigo IS NOT NULL AND folhas.ind_fl != '2' THEN '1'         \n";
    $stSql .= "             WHEN eventos_base_irrf.codigo IS NULL AND folhas.natureza NOT IN ('P','D') THEN '9' \n";
    $stSql .= "             ELSE '3'                                                                            \n";
    $stSql .= "         END as ind_base_irrf                                                                    \n";
    $stSql .= "       , CASE WHEN evento_salario_familia.codigo IS NOT NULL THEN '4'                            \n";
    $stSql .= "              WHEN eventos_base_desc_prev.codigo IS NOT NULL AND eventos_base_desc_prev.cod_tipo = 1 THEN '3' \n";
    $stSql .= "              WHEN eventos_base_desc_prev.codigo IS NOT NULL AND eventos_base_desc_prev.cod_tipo = 2 AND folhas.desdobramento != 'D' THEN '1' \n";
    $stSql .= "              WHEN eventos_base_desc_prev.codigo IS NOT NULL AND eventos_base_desc_prev.cod_tipo = 2 AND folhas.desdobramento  = 'D' THEN '2' \n";
    $stSql .= "              WHEN eventos_base_desc_prev.codigo IS NULL AND folhas.natureza NOT IN ('P','D') THEN '9' \n";
    $stSql .= "              ELSE '8'                                                                           \n";
    $stSql .= "         END as ind_base_ps                                                                      \n";
    $stSql .= "    FROM pessoal".$stEntidade.".contrato_servidor                                                \n";
    $stSql .= "INNER JOIN pessoal".$stEntidade.".contrato                                                       \n";
    $stSql .= "           ON contrato.cod_contrato = contrato_servidor.cod_contrato                             \n";
    $stSql .= "   INNER JOIN (SELECT cod_contrato, orgao                                                        \n";
    $stSql .= "                 FROM organograma.vw_orgao_nivel,                                                \n";
    $stSql .= "                      ultimo_contrato_servidor_orgao('', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_orgao \n";
    $stSql .= "                WHERE vw_orgao_nivel.cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao ) as contrato_servidor_orgao                             \n";
    $stSql .= "           ON contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato              \n";
    $stSql .= "    INNER JOIN (                                                                                 \n";
    $stSql .= "          SELECT '1'::varchar as ind_fl                                                          \n";
    $stSql .= "               , registro_evento_periodo.cod_contrato                                            \n";
    $stSql .= "               , evento.codigo                                                                   \n";
    $stSql .= "               , evento.descricao                                                                \n";
    $stSql .= "               , evento.natureza                                                                 \n";
    $stSql .= "               , evento_calculado.valor                                                          \n";
    $stSql .= "               , evento_calculado.quantidade                                                     \n";
    $stSql .= "               , CASE WHEN evento_calculado.desdobramento IS NULL THEN ''::varchar               \n";
    $stSql .= "               ELSE evento_calculado.desdobramento END AS desdobramento                          \n";
    $stSql .= "          FROM folhapagamento".$stEntidade.".registro_evento_periodo                             \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento_calculado                                    \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento                                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo_evento                            \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo                                   \n";
    $stSql .= "         WHERE registro_evento_periodo.cod_registro             = evento_calculado.cod_registro  \n";
    $stSql .= "           AND evento_calculado.cod_evento                      = evento.cod_evento              \n";
    $stSql .= "           AND evento.cod_evento                                = sequencia_calculo_evento.cod_evento \n";
    $stSql .= "           AND sequencia_calculo_evento.cod_sequencia           = sequencia_calculo.cod_sequencia \n";
    $stSql .= "           AND registro_evento_periodo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."  \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        UNION                                                                                    \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "         SELECT '3'::varchar as ind_fl                                                           \n";
    $stSql .= "             , registro_evento_ferias.cod_contrato                                               \n";
    $stSql .= "             , evento.codigo                                                                     \n";
    $stSql .= "             , evento.descricao                                                                  \n";
    $stSql .= "             , evento.natureza                                                                   \n";
    $stSql .= "             , SUM(evento_ferias_calculado.valor) as valor                                                     \n";
    $stSql .= "             , SUM(evento_ferias_calculado.quantidade) as quantidade                                                \n";
    $stSql .= "     --        , evento_ferias_calculado.desdobramento                                             \n";
    $stSql .= "             , ' ' as desdobramento                                             \n";
    $stSql .= "          FROM folhapagamento".$stEntidade.".registro_evento_ferias                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento_ferias_calculado                             \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento                                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo_evento                            \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo                                   \n";
    $stSql .= "         WHERE registro_evento_ferias.cod_registro             = evento_ferias_calculado.cod_registro  \n";
    $stSql .= "           AND registro_evento_ferias.desdobramento            = evento_ferias_calculado.desdobramento \n";
    $stSql .= "           AND registro_evento_ferias.timestamp                = evento_ferias_calculado.timestamp_registro \n";
    $stSql .= "           AND registro_evento_ferias.cod_evento               = evento_ferias_calculado.cod_evento \n";
    $stSql .= "           AND evento_ferias_calculado.cod_evento              = evento.cod_evento               \n";
    $stSql .= "           AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento \n";
    $stSql .= "           AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia \n";
    $stSql .= "           AND registro_evento_ferias.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."   \n";
    $stSql .= "  GROUP BY ind_fl                                                                                        \n";
    $stSql .= "                , registro_evento_ferias.cod_contrato                                               \n";
    $stSql .= "                , evento.codigo                                                                     \n";
    $stSql .= "                , evento.descricao                                                                  \n";
    $stSql .= "                , evento.natureza                                   \n";
    $stSql .= "         --     , evento_ferias_calculado.quantidade                                                \n";
    $stSql .= "         -- , evento_ferias_calculado.desdobramento      \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        UNION                                                                                    \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        SELECT '2'::varchar as ind_fl                                                            \n";
    $stSql .= "             , registro_evento_decimo.cod_contrato                                               \n";
    $stSql .= "             , evento.codigo                                                                     \n";
    $stSql .= "             , evento.descricao                                                                  \n";
    $stSql .= "             , evento.natureza                                                                   \n";
    $stSql .= "             , evento_decimo_calculado.valor                                                     \n";
    $stSql .= "             , evento_decimo_calculado.quantidade                                                \n";
    $stSql .= "             , evento_decimo_calculado.desdobramento                                             \n";
    $stSql .= "          FROM folhapagamento".$stEntidade.".registro_evento_decimo                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento_decimo_calculado                             \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento                                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo_evento                            \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo                                   \n";
    $stSql .= "         WHERE registro_evento_decimo.cod_registro             = evento_decimo_calculado.cod_registro    \n";
    $stSql .= "           AND registro_evento_decimo.cod_evento               = evento_decimo_calculado.cod_evento      \n";
    $stSql .= "           AND registro_evento_decimo.desdobramento            = evento_decimo_calculado.desdobramento   \n";
    $stSql .= "           AND registro_evento_decimo.timestamp                = evento_decimo_calculado.timestamp_registro \n";
    $stSql .= "           AND evento_decimo_calculado.cod_evento              = evento.cod_evento               \n";
    $stSql .= "           AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento     \n";
    $stSql .= "           AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia \n";
    $stSql .= "           AND registro_evento_decimo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."   \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        UNION                                                                                    \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        SELECT \n";
    $stSql .= "                     CASE WHEN evento_rescisao_calculado.desdobramento = 'S' THEN '1'::varchar \n";
    $stSql .= "                              WHEN evento_rescisao_calculado.desdobramento = 'D' THEN '2'::varchar \n";
    $stSql .= "                              WHEN evento_rescisao_calculado.desdobramento = 'P'  THEN '3'::varchar \n";
    $stSql .= "                              WHEN evento_rescisao_calculado.desdobramento = 'V' THEN '6'::varchar  \n";
    $stSql .= "                              ELSE '6'::varchar   \n";
    $stSql .= "                                 END as ind_fl                                                            \n";
    $stSql .= "             , registro_evento_rescisao.cod_contrato                                             \n";
    $stSql .= "             , evento.codigo                                                                     \n";
    $stSql .= "             , evento.descricao                                                                  \n";
    $stSql .= "             , evento.natureza                                                                   \n";
    $stSql .= "             , evento_rescisao_calculado.valor                                                   \n";
    $stSql .= "             , evento_rescisao_calculado.quantidade                                              \n";
    $stSql .= "             , evento_rescisao_calculado.desdobramento                                           \n";
    $stSql .= "          FROM folhapagamento".$stEntidade.".registro_evento_rescisao                            \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento_rescisao_calculado                           \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento                                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo_evento                            \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo                                   \n";
    $stSql .= "         WHERE registro_evento_rescisao.cod_registro             = evento_rescisao_calculado.cod_registro    \n";
    $stSql .= "           AND registro_evento_rescisao.cod_evento               = evento_rescisao_calculado.cod_evento      \n";
    $stSql .= "           AND registro_evento_rescisao.desdobramento            = evento_rescisao_calculado.desdobramento   \n";
    $stSql .= "           AND registro_evento_rescisao.timestamp                = evento_rescisao_calculado.timestamp_registro \n";
    $stSql .= "           AND evento_rescisao_calculado.cod_evento              = evento.cod_evento             \n";
    $stSql .= "           AND evento.cod_evento                                 = sequencia_calculo_evento.cod_evento       \n";
    $stSql .= "           AND sequencia_calculo_evento.cod_sequencia            = sequencia_calculo.cod_sequencia           \n";
    $stSql .= "           AND registro_evento_rescisao.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao." \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        UNION                                                                                    \n";
    $stSql .= "                                                                                                 \n";
    $stSql .= "        SELECT '4'::varchar as ind_fl                                                            \n";
    $stSql .= "             , registro_evento_complementar.cod_contrato                                         \n";
    $stSql .= "             , evento.codigo                                                                     \n";
    $stSql .= "             , evento.descricao                                                                  \n";
    $stSql .= "             , evento.natureza                                                                   \n";
    $stSql .= "             , evento_complementar_calculado.valor                                               \n";
    $stSql .= "             , evento_complementar_calculado.quantidade                                          \n";
    $stSql .= "             , CASE WHEN evento_complementar_calculado.desdobramento IS NULL THEN ''::varchar    \n";
    $stSql .= "               ELSE evento_complementar_calculado.desdobramento END AS desdobramento             \n";
    $stSql .= "          FROM folhapagamento".$stEntidade.".registro_evento_complementar                        \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento_complementar_calculado                       \n";
    $stSql .= "             , folhapagamento".$stEntidade.".evento                                              \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo_evento                            \n";
    $stSql .= "             , folhapagamento".$stEntidade.".sequencia_calculo                                   \n";
    $stSql .= "         WHERE registro_evento_complementar.cod_registro             = evento_complementar_calculado.cod_registro    \n";
    $stSql .= "           AND registro_evento_complementar.cod_evento               = evento_complementar_calculado.cod_evento      \n";
    $stSql .= "           AND registro_evento_complementar.cod_configuracao         = evento_complementar_calculado.cod_configuracao \n";
    $stSql .= "           AND registro_evento_complementar.timestamp                = evento_complementar_calculado.timestamp_registro \n";
    $stSql .= "           AND evento_complementar_calculado.cod_evento              = evento.cod_evento         \n";
    $stSql .= "           AND evento.cod_evento                                     = sequencia_calculo_evento.cod_evento   \n";
    $stSql .= "           AND sequencia_calculo_evento.cod_sequencia                = sequencia_calculo.cod_sequencia       \n";
    $stSql .= "           AND registro_evento_complementar.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao." \n";
    $stSql .= "        ) as folhas                                                                              \n";
    $stSql .= "       ON contrato.cod_contrato = folhas.cod_contrato                                            \n";
    $stSql .= "LEFT JOIN (SELECT evento.codigo as codigo                                                        \n";
    $stSql .= "                     FROM folhapagamento".$stEntidade.".evento,                                  \n";
    $stSql .= "                          folhapagamento".$stEntidade.".tabela_irrf_evento                       \n";
    $stSql .= "                          INNER JOIN ( SELECT cod_tabela,                                        \n";
    $stSql .= "                                              cod_tipo,                                          \n";
    $stSql .= "                                              timestamp                                          \n";
    $stSql .= "                                         FROM folhapagamento".$stEntidade.".tabela_irrf_evento   \n";
    $stSql .= "                                        WHERE cod_tabela = 1                                     \n";
    $stSql .= "                                          AND cod_tipo = 7                                       \n";
    $stSql .= "                                          AND timestamp <= (select ultimotimestampperiodomovimentacao(".$inCodPeriodoMovimentacao.",'')) \n";
    $stSql .= "                                        ORDER BY timestamp                                       \n";
    $stSql .= "                                         DESC LIMIT 1) as max_tabela_irrf_evento                 \n";
    $stSql .= "                           ON max_tabela_irrf_evento.cod_tabela = tabela_irrf_evento.cod_tabela  \n";
    $stSql .= "                          AND max_tabela_irrf_evento.cod_tipo   = tabela_irrf_evento.cod_tipo    \n";
    $stSql .= "                          AND max_tabela_irrf_evento.timestamp  = tabela_irrf_evento.timestamp   \n";
    $stSql .= "                     WHERE tabela_irrf_evento.cod_evento = evento.cod_evento                     \n";
    $stSql .= "                     ) as eventos_base_irrf                                                      \n";
    $stSql .= "              ON eventos_base_irrf.codigo = folhas.codigo                                        \n";
    $stSql .= "       LEFT JOIN ( SELECT evento.codigo ,                                                        \n";
    $stSql .= "                            evento.descricao,                                                    \n";
    $stSql .= "                            previdencia_evento.cod_tipo,                                         \n";
    $stSql .= "                            previdencia_evento.cod_previdencia                                   \n";
    $stSql .= "           FROM folhapagamento".$stEntidade.".evento,                                            \n";
    $stSql .= "                folhapagamento".$stEntidade.".previdencia_previdencia                            \n";
    $stSql .= "                INNER JOIN (SELECT previdencia_previdencia.cod_previdencia,                      \n";
    $stSql .= "                                   max(timestamp) as timestamp                                   \n";
    $stSql .= "                              FROM folhapagamento".$stEntidade.".previdencia_previdencia         \n";
    $stSql .= "                             WHERE timestamp <= (select ultimotimestampperiodomovimentacao(".$inCodPeriodoMovimentacao.",'')) \n";
    $stSql .= "                               AND previdencia_previdencia.tipo_previdencia = 'o'                \n";
    $stSql .= "                              GROUP BY cod_previdencia                                           \n";
    $stSql .= "                ) as max_previdencia_previdencia                                                 \n";
    $stSql .= "                 ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia \n";
    $stSql .= "                                AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp \n";
    $stSql .= "                ,folhapagamento".$stEntidade.".previdencia_evento                                \n";
    $stSql .= "                 INNER JOIN (SELECT previdencia_evento.cod_previdencia,                          \n";
    $stSql .= "                                    previdencia_evento.cod_tipo,                                 \n";
    $stSql .= "                                    max(timestamp) as timestamp                                  \n";
    $stSql .= "                               FROM folhapagamento".$stEntidade.".previdencia_evento             \n";
    $stSql .= "                               WHERE timestamp <= (select ultimotimestampperiodomovimentacao(".$inCodPeriodoMovimentacao.",'')) \n";
    $stSql .= "                               GROUP BY previdencia_evento.cod_previdencia,                      \n";
    $stSql .= "                                        previdencia_evento.cod_tipo ) as max_previdencia_evento  \n";
    $stSql .= "                         ON max_previdencia_evento.cod_previdencia = previdencia_evento.cod_previdencia \n";
    $stSql .= "                         AND max_previdencia_evento.cod_tipo = previdencia_evento.cod_tipo       \n";
    $stSql .= "                         AND max_previdencia_evento.timestamp = previdencia_evento.timestamp     \n";
    $stSql .= "           WHERE previdencia_evento.cod_evento = evento.cod_evento                               \n";
    $stSql .= "             AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia    \n";
    $stSql .= "          ) as eventos_base_desc_prev                                                            \n";
    $stSql .= "       ON eventos_base_desc_prev.codigo = folhas.codigo                                          \n";
    $stSql .= "LEFT JOIN (SELECT salario_familia_evento.cod_evento,                                             \n";
    $stSql .= "                      salario_familia.cod_regime_previdencia,                                    \n";
    $stSql .= "                      evento.codigo as codigo                                                    \n";
    $stSql .= "                 FROM folhapagamento".$stEntidade.".salario_familia_evento ,                     \n";
    $stSql .= "                      folhapagamento".$stEntidade.".evento,                                      \n";
    $stSql .= "                      folhapagamento".$stEntidade.".salario_familia                              \n";
    $stSql .= "                      INNER JOIN ( SELECT cod_regime_previdencia,                                \n";
    $stSql .= "                                          max(timestamp) as timestamp                            \n";
    $stSql .= "                                     FROM folhapagamento".$stEntidade.".salario_familia          \n";
    $stSql .= "                                    WHERE vigencia <= (select ultimotimestampperiodomovimentacao(".$inCodPeriodoMovimentacao.",'')) \n";
    $stSql .= "                                          GROUP BY cod_regime_previdencia                        \n";
    $stSql .= "                                           ) as max_sf                                           \n";
    $stSql .= "                               ON max_sf.cod_regime_previdencia = salario_familia.cod_regime_previdencia \n";
    $stSql .= "                              AND max_sf.timestamp = salario_familia.timestamp                   \n";
    $stSql .= "                WHERE salario_familia.cod_regime_previdencia = salario_familia_evento.cod_regime_previdencia \n";
    $stSql .= "                  AND salario_familia.timestamp = salario_familia_evento.timestamp               \n";
    $stSql .= "                  AND salario_familia_evento.cod_evento = evento.cod_evento                      \n";
    $stSql .= "                  AND cod_tipo = 1                                                               \n";
    $stSql .= "               ) as evento_salario_familia                                                       \n";
    $stSql .= "             ON evento_salario_familia.codigo = folhas.codigo                                    \n";
    $stSql .= "   LEFT JOIN (SELECT codigo                                                                      \n";
    $stSql .= "                FROM folhapagamento".$stEntidade.".evento,                                       \n";
    $stSql .= "                     folhapagamento".$stEntidade.".tabela_irrf_evento                            \n";
    $stSql .= "               WHERE cod_tabela = 1                                                              \n";
    $stSql .= "                 AND cod_tipo = 1                                                                \n";
    $stSql .= "                 AND tabela_irrf_evento.cod_evento = evento.cod_evento                           \n";
    $stSql .= "                 AND timestamp <= (select ultimotimestampperiodomovimentacao(".$inCodPeriodoMovimentacao.",'')) \n";
    $stSql .= "              ORDER BY timestamp                                                                 \n";
    $stSql .= "              DESC LIMIT 1 ) as evento_dependentes_irrf                                          \n";
    $stSql .= "           ON evento_dependentes_irrf.codigo = folhas.codigo                                     \n";
    $stSql .= " GROUP BY reg                  \n";
    $stSql .= "                 , cnpj_cei                                        \n";
    $stSql .= "                , ind_fl                                          \n";
    $stSql .= "                , cod_ltc                                   \n";
    $stSql .= "                , cod_reg_trab                          \n";
    $stSql .= "                , dt_comp \n";
    $stSql .= "                , cod_rubr                           \n";
    $stSql .= "                , vlr_rubr                           \n";
    $stSql .= "                , ind_rubr                         \n";
    $stSql .= "                , ind_base_irrf                \n";
    $stSql .= "                ,  ind_base_ps                \n";
    $stSql .= "   ORDER BY cod_reg_trab, ind_fl, cod_rubr                                                       \n";

    return $stSql;
}

function recuperaDadosL300(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosL300();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosL300()
{
    $stSQL .= "SELECT                          \n";
    $stSQL .= "     'L300' as reg, \n";
    $stSQL .= "     numero_da_lei||'/'||substr(data_da_lei,7,8) as nm_lei_decreto,                                                                                          \n";
    $stSQL .= "     substr(data_da_lei,7,8) as exercicio_da_lei,                                                            \n";
    $stSQL .= "     data_da_lei as dt_lei_decreto,                                                                                            \n";
    $stSQL .= "     num_norma as cod_norma,                                                                                 \n";
    $stSQL .= "     exercicio,                                                                                              \n";
    $stSQL .= "     dt_publicacao,                                                                                          \n";
    $stSQL .= "     replace(valor_credito_adicional, '.',',') as vl_cred_adicional ,                                                                                \n";
    $stSQL .= "     replace(valor_reducao_dotacoes, '.',',') as vl_red_dotacoes,                                                                                 \n";
    $stSQL .= "     tipo_credito_adicional as tip_cred_adicional,                                                                                 \n";
    $stSQL .= "     origem_do_recurso          as tip_orig_recurso                                                                             \n";
    $stSQL .= "FROM (                                                                                                       \n";
    $stSQL .= "     SELECT DISTINCT                                                                                         \n";
    $stSQL .= "        suplementacao.cod_suplementacao,                                                                                \n";
    $stSQL .= "        norma.num_norma,                                                                                        \n";
    $stSQL .= "        norma.exercicio,                                                                                        \n";
    $stSQL .= "        to_char(norma.dt_publicacao,'ddmmyyyy')as dt_publicacao,                                                \n";
    $stSQL .= "        CASE WHEN norma.cod_tipo_norma=1 THEN                                                                   \n";
    $stSQL .= "            tcers.fn_retorno_atributo_normas(norma.cod_tipo_norma,norma.cod_norma,'Número da Lei')                 \n";
    $stSQL .= "            ELSE                                                                                             \n";
    $stSQL .= "            CAST(norma.num_norma as varchar)                                                                    \n";
    $stSQL .= "        END as numero_da_lei,                                                                                \n";
    $stSQL .= "        CASE WHEN norma.cod_tipo_norma=1 THEN                                                                   \n";
    $stSQL .= "            tcers.fn_retorno_atributo_normas(norma.cod_tipo_norma,norma.cod_norma,'Data da Lei')                   \n";
    $stSQL .= "            ELSE                                                                                             \n";
    $stSQL .= "            to_char(norma.dt_publicacao,'dd/mm/yyyy')                                                           \n";
    $stSQL .= "        END as data_da_lei,                                                                                  \n";
    $stSQL .= "        tcers.fn_total_valor_credito('".$this->getDado("stExercicio")."',suplementacao.cod_suplementacao, '".$this->getDado("stCodEntidades")."') as valor_credito_adicional,    \n";
    $stSQL .= "        tcers.fn_total_valor_reducao('".$this->getDado("stExercicio")."',suplementacao.cod_suplementacao, '".$this->getDado("stCodEntidades")."') as valor_reducao_dotacoes,     \n";
    $stSQL .= "        CASE                                                                                                 \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (1,2,3,4,5,13,14,15)   THEN 1                                            \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (6,7,8,9,10)        THEN 2                                               \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (11)                THEN 3                                               \n";
    $stSQL .= "        END AS tipo_credito_adicional,                                                                       \n";
    $stSQL .= "        CASE                                                                                                 \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (5,10)              THEN 1                                               \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (4,9)               THEN 2                                               \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (2,7)               THEN 3                                               \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (3,8)               THEN 4                                               \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (1,6,12,13,14,15, 16)            THEN 5                                               \n";
    $stSQL .= "              --  WHEN suplementacao.cod_tipo IN (14,15)             THEN 6                                               \n";
    $stSQL .= "        END AS origem_do_recurso                                                                             \n";
    $stSQL .= "     FROM                                                                                                    \n";
    $stSQL .= "        normas.norma                                ,                                                   \n";
    $stSQL .= "        orcamento.suplementacao,                                                                        \n";
    $stSQL .= "        contabilidade.transferencia_despesa  ,                                                          \n";
    $stSQL .= "        contabilidade.lote                                                                            \n";
    $stSQL .= "     WHERE                                                                                                   \n";
//  $stSQL .= "            norma.exercicio = '".$this->getDado("stExercicio")."'                                               \n";
    $stSQL .= "               transferencia_despesa.cod_suplementacao     = suplementacao.cod_suplementacao                                                  \n";
    $stSQL .= "        AND transferencia_despesa.exercicio             = suplementacao.exercicio                                                          \n";
    $stSQL .= "        AND transferencia_despesa.cod_lote              = lote.cod_lote                                                           \n";
    $stSQL .= "        AND suplementacao.cod_tipo              not in (12,16)                                                                    \n";
    $stSQL .= "        AND transferencia_despesa.exercicio             = lote.exercicio                                                          \n";
    $stSQL .= "        AND transferencia_despesa.tipo                  = lote.tipo                                                               \n";
    $stSQL .= "        AND transferencia_despesa.cod_entidade          = lote.cod_entidade                                                       \n";
    $stSQL .= "        AND lote.dt_lote between to_date('".$this->getDado("dtInicial")."','dd/mm/yyyy') AND to_date('".$this->getDado("dtFinal")."','dd/mm/yyyy')\n";
    $stSQL .= "        AND suplementacao.dt_suplementacao between to_date('".$this->getDado("dtInicial")."','dd/mm/yyyy') AND to_date('".$this->getDado("dtFinal")."','dd/mm/yyyy')\n";
    $stSQL .= "        AND transferencia_despesa.cod_entidade          IN (".$this->getDado("stCodEntidades").")                               \n";
    $stSQL .= "        AND norma.cod_norma             = suplementacao.cod_norma                                                          \n";
    $stSQL .= "        AND suplementacao.cod_suplementacao || suplementacao.exercicio NOT IN (                                                    \n";
    $stSQL .= "           SELECT                                                                                            \n";
    $stSQL .= "              cod_suplementacao || exercicio                                                                 \n";
    $stSQL .= "           FROM                                                                                              \n";
    $stSQL .= "              orcamento.suplementacao_anulada                                                                \n";
    $stSQL .= "           WHERE                                                                                             \n";
    $stSQL .= "              exercicio   = '".$this->getDado("stExercicio")."'                                              \n";
    $stSQL .= "        )                                                                                                    \n";
    $stSQL .= "        AND suplementacao.cod_suplementacao || suplementacao.exercicio NOT IN (                                                    \n";
    $stSQL .= "           SELECT                                                                                            \n";
    $stSQL .= "             cod_suplementacao_anulacao || exercicio                                                         \n";
    $stSQL .= "           FROM                                                                                              \n";
    $stSQL .= "             orcamento.suplementacao_anulada                                                                 \n";
    $stSQL .= "           WHERE                                                                                             \n";
    $stSQL .= "             exercicio   = '".$this->getDado("stExercicio")."'                                               \n";
    $stSQL .= "        )                                                                                                    \n";
    $stSQL .= ") as tabela                                                                                                  \n";

    return $stSQL;
}

function recuperaDadosL350(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro();
    $obConexao   = new Conexao();
    $rsRecordSet = new RecordSet();
    $stSql = $this->montaRecuperaDadosL350();
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
}

function montaRecuperaDadosL350()
{
    $stSql .=" SELECT 'L350' as reg
                        , orgao.exercicio as exerc
                        , orgao.num_orgao as cod_org
                        , orgao.nom_orgao  as nome_org
                        , orgao.usuario_responsavel
                 FROM orcamento.orgao

          INNER JOIN (SELECT  tabela.num_orgao
                             , tabela.num_unidade
                             , tabela.cod_funcao
                             , tabela.cod_subfuncao
                             , tabela.cod_programa
                             , tabela.cod_recurso
                             , tabela.exercicio
                             , tabela.cod_empenho
                             , tabela.cgm
                      FROM fn_transparenciaExportacaoEmpenho( '".$this->getDado("stExercicioLogado")."'
                                     , '".$this->getDado("dtInicial")."'
                                  , '".$this->getDado("dtFinal")."'
                                     , '".$this->getDado("stCodEntidades")."')

                   as tabela ( num_orgao           integer,
                        num_unidade     integer         ,
                        cod_funcao      integer         ,
                        cod_subfuncao   integer         ,
                        cod_programa    integer         ,
                        num_pao         integer         ,
                        cod_recurso     integer         ,
                        cod_estrutural  varchar         ,
                        cod_empenho     integer         ,
                        dt_empenho      date            ,
                        vl_empenhado    numeric(14,2)   ,
                        sinal           varchar(1)      ,
                        cgm             integer         ,
                        historico       varchar         ,
                        cod_pre_empenho integer         ,
                        exercicio       char(4)         ,
                        cod_entidade    integer         ,
                        ordem           integer         ,
                        oid             oid             ,
                        caracteristica  integer         ,
                        modalidade      integer         ,
                        nro_licitacao   text            ,
                        nom_modalidades text            ,
                        preco           text
                             )
                    WHERE tabela.cod_entidade in (".$this->getDado("stCodEntidades").") ) as empenhos
         ON empenhos.num_orgao = orgao.num_orgao
        AND empenhos.exercicio = orgao.exercicio
     WHERE orgao.exercicio <=  '".$this->getDado("stExercicioLogado")."'
 GROUP BY orgao.exercicio, reg, orgao.num_orgao, orgao.nom_orgao   , orgao.usuario_responsavel

UNION

SELECT 'L350' as reg
               , orgao_todos.exercicio as exerc
               , orgao_todos.num_orgao as cod_org
               , orgao_todos.nom_orgao  as nome_org
               , orgao_todos.usuario_responsavel
        FROM orcamento.orgao as orgao_todos
     WHERE orgao_todos.exercicio = '".$this->getDado("stExercicioLogado")."'
 GROUP BY orgao_todos.exercicio, reg, orgao_todos.num_orgao, orgao_todos.nom_orgao   , orgao_todos.usuario_responsavel

 ORDER BY exerc, cod_org   ";

      return $stSql;
}

function recuperaDadosL400(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosL400().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosL400()
{
    $stSql = "
                    SELECT 'L400' as reg
                              , orgao.exercicio as exerc
                              , orgao.num_orgao as cod_org
                              , unidade.num_unidade as cod_un_orc
                              , uniorcam.identificador as tip_un_orc
                              , unidade.nom_unidade as nom_un_orc
                              , PJ.cnpj

                       FROM manad.uniorcam
                              , sw_cgm_pessoa_juridica AS PJ
                              , orcamento.unidade

               INNER JOIN orcamento.orgao
                           ON (unidade.exercicio   = orgao.exercicio
                          AND unidade.num_orgao    = orgao.num_orgao)

                     WHERE (unidade.exercicio   = uniorcam.exercicio
                          AND unidade.num_unidade  = uniorcam.num_unidade
                          AND unidade.num_orgao    = uniorcam.num_orgao
                          AND PJ.numcgm       = uniorcam.numcgm)
                          AND uniorcam.exercicio = '".$this->getDado('stExercicioLogado')."'
                          AND uniorcam.identificador IN(".$this->getDado('identificador').")

                 GROUP BY orgao.exercicio
                               , orgao.num_orgao
                               , unidade.num_unidade
                               , uniorcam.identificador
                               , unidade.nom_unidade
                              , PJ.cnpj

                   UNION

                   SELECT 'L400' as reg
                              , uniorcam.exercicio as exerc
                              , uniorcam.num_orgao as cod_org
                              , uniorcam.num_unidade as cod_un_orc
                              , uniorcam.identificador as tip_un_orc
                              , unidade.nom_unidade as nom_un_orc
                              , PJ.cnpj

                      FROM manad.uniorcam

              INNER JOIN sw_cgm_pessoa_juridica AS PJ
                          ON PJ.numcgm = uniorcam.numcgm

              INNER JOIN orcamento.unidade
                          ON unidade.num_unidade = uniorcam.num_unidade
                        AND unidade.exercicio = uniorcam.exercicio
                        AND unidade.num_orgao = uniorcam.num_orgao

              INNER JOIN (SELECT  tabela.num_orgao
                             , tabela.num_unidade
                             , tabela.cod_funcao
                             , tabela.cod_subfuncao
                             , tabela.cod_programa
                             , tabela.cod_recurso
                             , tabela.exercicio
                             , tabela.cod_empenho
                             , tabela.cgm

                    FROM fn_transparenciaExportacaoEmpenho( '".$this->getDado("stExercicioLogado")."'
                                     , '".$this->getDado("dtInicial")."'
                                  , '".$this->getDado("dtFinal")."'
                                     , '".$this->getDado("stCodEntidades")."')
                                      as tabela ( num_orgao           integer,
                                           num_unidade     integer         ,
                                           cod_funcao      integer         ,
                                           cod_subfuncao   integer         ,
                                           cod_programa    integer         ,
                                           num_pao         integer         ,
                                           cod_recurso     integer         ,
                                           cod_estrutural  varchar         ,
                                           cod_empenho     integer         ,
                                           dt_empenho      date            ,
                                           vl_empenhado    numeric(14,2)   ,
                                           sinal           varchar(1)      ,
                                           cgm             integer         ,
                                           historico       varchar         ,
                                           cod_pre_empenho integer         ,
                                           exercicio       char(4)         ,
                                           cod_entidade    integer         ,
                                           ordem           integer         ,
                                           oid             oid             ,
                                           caracteristica  integer         ,
                                           modalidade      integer         ,
                                           nro_licitacao   text            ,
                                           nom_modalidades text            ,
                                           preco           text
                                                )
                                       WHERE tabela.cod_entidade in (".$this->getDado("stCodEntidades").")
                               ) as empenhos
                            ON empenhos.num_orgao = uniorcam.num_orgao
                           AND empenhos.exercicio = uniorcam.exercicio
                           AND empenhos.num_unidade = uniorcam.num_unidade

            GROUP BY uniorcam.exercicio
                          , uniorcam.num_orgao
                          , uniorcam.num_unidade
                          , uniorcam.identificador
                          , unidade.nom_unidade
                          , PJ.cnpj

           ORDER BY exerc ";

    return $stSql;
    }

function recuperaDadosL450(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro();
    $obConexao   = new Conexao();
    $rsRecordSet = new RecordSet();

    $stSql = $this->montaRecuperaDadosL450();
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
}

function montaRecuperaDadosL450()
{
    $stSql = "
       SELECT 'L450' as reg
                    , exercicio as exerc
                   , cod_funcao  as cod_fun
                    , descricao as nom_fun
         FROM orcamento.funcao
    WHERE exercicio =  '".$this->getDado('stExercicioLogado')."'
UNION
   SELECT 'L450' as reg
                    , funcao.exercicio as exerc
                   , funcao.cod_funcao  as cod_fun
                    ,funcao.descricao as nom_fun
         FROM orcamento.funcao


 INNER JOIN (SELECT tabela.cod_funcao
                  , tabela.exercicio

                  FROM fn_transparenciaExportacaoEmpenho( '".$this->getDado("stExercicioLogado")."'
                                     , '".$this->getDado("dtInicial")."'
                                  , '".$this->getDado("dtFinal")."'
                                     , '".$this->getDado("stCodEntidades")."')
                   as tabela ( num_orgao           integer,
                        num_unidade     integer         ,
                        cod_funcao      integer         ,
                        cod_subfuncao   integer         ,
                        cod_programa    integer         ,
                        num_pao         integer         ,
                        cod_recurso     integer         ,
                        cod_estrutural  varchar         ,
                        cod_empenho     integer         ,
                        dt_empenho      date            ,
                        vl_empenhado    numeric(14,2)   ,
                        sinal           varchar(1)      ,
                        cgm             integer         ,
                        historico       varchar         ,
                        cod_pre_empenho integer         ,
                        exercicio       char(4)         ,
                        cod_entidade    integer         ,
                        ordem           integer         ,
                        oid             oid             ,
                        caracteristica  integer         ,
                        modalidade      integer         ,
                        nro_licitacao   text            ,
                        nom_modalidades text            ,
                        preco           text
                             )
                    WHERE tabela.cod_entidade in (".$this->getDado("stCodEntidades").")
            ) as empenhos
         ON empenhos.cod_funcao = funcao.cod_funcao
        AND empenhos.exercicio = funcao.exercicio

WHERE funcao.exercicio <=  '".$this->getDado('stExercicioLogado')."'

 ORDER BY exerc, cod_fun
";

    return $stSql;
}

function recuperaDadosL500(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro();
    $obConexao   = new Conexao();
    $rsRecordSet = new RecordSet();

    $stSql = $this->montaRecuperaDadosL500();
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
}

function montaRecuperaDadosL500()
{
      $stSql  ="        SELECT 'L500' as reg
                                        , subfuncao.exercicio as exerc
                                        , subfuncao.cod_subfuncao  as cod_subfun
                                        , subfuncao.descricao as nom_subfun
                                     FROM orcamento.subfuncao
                                   WHERE subfuncao.exercicio = '".$this->getDado("stExercicioLogado")."'

                          UNION
                           SELECT 'L500' as reg
                                               , subfuncao.exercicio as exerc
                                              , subfuncao.cod_subfuncao  as cod_subfun
                                               , subfuncao.descricao as nom_subfun
                                       FROM orcamento.subfuncao


                          INNER JOIN (SELECT tabela.cod_subfuncao
                                            , tabela.exercicio

                                          FROM fn_transparenciaExportacaoEmpenho( '".$this->getDado("stExercicioLogado")."'
                                     , '".$this->getDado("dtInicial")."'
                                  , '".$this->getDado("dtFinal")."'
                                     , '".$this->getDado("stCodEntidades")."')
                                             as tabela ( num_orgao           integer,
                                                  num_unidade     integer         ,
                                                  cod_funcao      integer         ,
                                                  cod_subfuncao   integer         ,
                                                  cod_programa    integer         ,
                                                  num_pao         integer         ,
                                                  cod_recurso     integer         ,
                                                  cod_estrutural  varchar         ,
                                                  cod_empenho     integer         ,
                                                  dt_empenho      date            ,
                                                  vl_empenhado    numeric(14,2)   ,
                                                  sinal           varchar(1)      ,
                                                  cgm             integer         ,
                                                  historico       varchar         ,
                                                  cod_pre_empenho integer         ,
                                                  exercicio       char(4)         ,
                                                  cod_entidade    integer         ,
                                                  ordem           integer         ,
                                                  oid             oid             ,
                                                  caracteristica  integer         ,
                                                  modalidade      integer         ,
                                                  nro_licitacao   text            ,
                                                  nom_modalidades text            ,
                                                  preco           text
                                                       )
                                              WHERE tabela.cod_entidade in (".$this->getDado("stCodEntidades").")
                                      ) as empenhos
                                   ON empenhos.cod_subfuncao = subfuncao.cod_subfuncao
                                  AND empenhos.exercicio = subfuncao.exercicio
                                          WHERE subfuncao.exercicio <= '".$this->getDado("stExercicioLogado")."'
                             ORDER BY exerc, cod_subfun   ";

    return $stSql;
}

function recuperaDadosL550(&$rsRecordSet, $boTransacao = "")
{
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet();
        $stSql = $this->montaRecuperaDadosL550();
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaDadosL550()
    {
        $stSql  ="       SELECT 'L550' as reg
                        , programa.exercicio as exerc
                         , programa.cod_programa  as cod_progr
                        , programa.descricao as nom_progr
                 FROM orcamento.programa
        WHERE programa.exercicio = '2013'


UNION
    SELECT 'L550' as reg
                        , programa.exercicio as exerc
                         , programa.cod_programa  as cod_progr
                        , programa.descricao as nom_progr
                 FROM orcamento.programa

INNER JOIN (SELECT tabela.cod_programa
                  , tabela.exercicio

                   FROM fn_transparenciaExportacaoEmpenho( '".$this->getDado("stExercicioLogado")."'
                                     , '".$this->getDado("dtInicial")."'
                                  , '".$this->getDado("dtFinal")."'
                                     , '".$this->getDado("stCodEntidades")."')
                   as tabela ( num_orgao           integer,
                        num_unidade     integer         ,
                        cod_funcao      integer         ,
                        cod_subfuncao   integer         ,
                        cod_programa    integer         ,
                        num_pao         integer         ,
                        cod_recurso     integer         ,
                        cod_estrutural  varchar         ,
                        cod_empenho     integer         ,
                        dt_empenho      date            ,
                        vl_empenhado    numeric(14,2)   ,
                        sinal           varchar(1)      ,
                        cgm             integer         ,
                        historico       varchar         ,
                        cod_pre_empenho integer         ,
                        exercicio       char(4)         ,
                        cod_entidade    integer         ,
                        ordem           integer         ,
                        oid             oid             ,
                        caracteristica  integer         ,
                        modalidade      integer         ,
                        nro_licitacao   text            ,
                        nom_modalidades text            ,
                        preco           text
                             )
                    WHERE tabela.cod_entidade in (".$this->getDado("stCodEntidades").")
            ) as empenhos
         ON empenhos.cod_programa = programa.cod_programa
        AND empenhos.exercicio = programa.exercicio
             WHERE programa.exercicio <= '2013'

         ORDER BY exerc, cod_progr  ";

        return $stSql;
    }
    /**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosL600(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosL600().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosL600()
{
    $stSql = "
     SELECT 'L600' as reg
                , ep.exercicio as exercicio
                , '' as cod_subprogr
                , 'SUBPROGRAMA' as nom_subprogr
         FROM empenho.pre_empenho AS ep
       WHERE ep.exercicio =  '".$this->getDado("stExercicioLogado")."'

        UNION
       SELECT 'L600' as reg
                , ee.exercicio as exercicio
                , '' as cod_subprogr
                , 'SUBPROGRAMA' as nom_subprogr
         FROM empenho.restos_pre_empenho AS ee
       WHERE ee.exercicio =  '".$this->getDado("stExercicioLogado")."'

        UNION
       SELECT 'L600' as reg
                 , ep.exercicio as exercicio
                 , '' as cod_subprogr
                 , 'SUBPROGRAMA' as nom_subprogr

         FROM empenho.pre_empenho AS ep

 INNER JOIN (SELECT tabela.cod_empenho
                , tabela.exercicio

        FROM fn_transparenciaExportacaoEmpenho( '".$this->getDado("stExercicioLogado")."'
                                     , '".$this->getDado("dtInicial")."'
                                  , '".$this->getDado("dtFinal")."'
                                     , '".$this->getDado("stCodEntidades")."')
                         as tabela ( num_orgao           integer,
                              num_unidade     integer         ,
                              cod_funcao      integer         ,
                              cod_subfuncao   integer         ,
                              cod_programa    integer         ,
                              num_pao         integer         ,
                              cod_recurso     integer         ,
                              cod_estrutural  varchar         ,
                              cod_empenho     integer         ,
                              dt_empenho      date            ,
                              vl_empenhado    numeric(14,2)   ,
                              sinal           varchar(1)      ,
                              cgm             integer         ,
                              historico       varchar         ,
                              cod_pre_empenho integer         ,
                              exercicio       char(4)         ,
                              cod_entidade    integer         ,
                              ordem           integer         ,
                              oid             oid             ,
                              caracteristica  integer         ,
                              modalidade      integer         ,
                              nro_licitacao   text            ,
                              nom_modalidades text            ,
                              preco           text
                                   )
                          WHERE tabela.cod_entidade in (".$this->getDado("stCodEntidades").")
                  ) as empenhos
               ON empenhos.cod_empenho = ep.cod_pre_empenho
              AND empenhos.exercicio = ep.exercicio

          WHERE ep.exercicio <= '".$this->getDado("stExercicioLogado")."'


      ORDER BY exercicio
";

    return $stSql;
}

 function recuperaDadosL650(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
    SELECT 'L650' as reg
                      , LPAD(pao.num_pao,4,0) AS cod_acao
                      , pao.num_pao as cod_proj_ativ_oe
                      , pao.nom_pao AS nom_proj_ativ_oe
                      --, (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) AS tip_proj_ativ_oe
                      , CASE WHEN (SELECT parametro from administracao.configuracao where exercicio = '" . $this->getDado('stExercicioLogado') . "' and cod_modulo=8 and valor = '" . $this->getDado('stCodEntidade') . "' and parametro like '%cod_entidade_%') = 'cod_entidade_rpps' THEN 1 ELSE 2 END AS tip_proj_ativ_oe
                      , pao.exercicio as exerc

              FROM orcamento.pao
        INNER JOIN orcamento.despesa
                ON despesa.exercicio = pao.exercicio
               AND despesa.num_pao = pao.num_pao
          WHERE pao.exercicio = '".$this->getDado("stExercicioLogado")."'
               AND despesa.cod_entidade in ('".$this->getDado("stCodEntidade")."')
      GROUP BY cod_acao, cod_proj_ativ_oe, nom_proj_ativ_oe, tip_proj_ativ_oe, exerc

UNION
    SELECT 'L650' as reg
                      , LPAD(pao.num_pao,4,0) AS cod_acao
                      , pao.num_pao as cod_proj_ativ_oe
                      , pao.nom_pao AS nom_proj_ativ_oe
                      --, (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) AS tip_proj_ativ_oe
                      , CASE WHEN (SELECT parametro from administracao.configuracao where exercicio = '" . $this->getDado('stExercicioLogado') . "' and cod_modulo=8 and valor = '" . $this->getDado('stCodEntidade') . "' and parametro like '%cod_entidade_%') = 'cod_entidade_rpps' THEN 1 ELSE 2 END AS tip_proj_ativ_oe
                      , pao.exercicio as exerc

              FROM orcamento.pao
        INNER JOIN orcamento.despesa
                ON despesa.exercicio = pao.exercicio
               AND despesa.num_pao = pao.num_pao

    INNER JOIN (SELECT tabela.num_pao
                  , tabela.exercicio

                 FROM fn_transparenciaExportacaoEmpenho( '".$this->getDado("stExercicioLogado")."'
                                     , '".$this->getDado("dtInicial")."'
                                  , '".$this->getDado("dtFinal")."'
                                     , '".$this->getDado("stCodEntidade")."')
                   as tabela ( num_orgao           integer,
                        num_unidade     integer         ,
                        cod_funcao      integer         ,
                        cod_subfuncao   integer         ,
                        cod_programa    integer         ,
                        num_pao         integer         ,
                        cod_recurso     integer         ,
                        cod_estrutural  varchar         ,
                        cod_empenho     integer         ,
                        dt_empenho      date            ,
                        vl_empenhado    numeric(14,2)   ,
                        sinal           varchar(1)      ,
                        cgm             integer         ,
                        historico       varchar         ,
                        cod_pre_empenho integer         ,
                        exercicio       char(4)         ,
                        cod_entidade    integer         ,
                        ordem           integer         ,
                        oid             oid             ,
                        caracteristica  integer         ,
                        modalidade      integer         ,
                        nro_licitacao   text            ,
                        nom_modalidades text            ,
                        preco           text
                             )
                    WHERE tabela.cod_entidade in (".$this->getDado("stCodEntidade").")
            ) as empenhos
         ON empenhos.num_pao = pao.num_pao
        AND empenhos.exercicio = pao.exercicio

                WHERE pao.exercicio <= '".$this->getDado("stExercicioLogado")."'
               AND despesa.cod_entidade in (".$this->getDado("stCodEntidade").")
 GROUP BY cod_acao, cod_proj_ativ_oe, nom_proj_ativ_oe, tip_proj_ativ_oe, exerc


      ORDER BY exerc , cod_proj_ativ_oe

        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
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
    public function recuperaDadosL700(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

        $stSql = "select valor from administracao.configuracao where cod_modulo = 2 and parametro = 'samlink_host'";
        $obErro = $obConexao->executaSQL( $rsSamLink, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setDado("boTemSiam", !$rsSamLink->eof() );
            $stSql = $this->montaRecuperaDadosL700().$stCondicao.$stOrdem;
            $this->setDebug( $stSql );
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        }

        return $obErro;
    }

        function MontaRecuperaDadosL700()
        {
//            $stSql  ="     SELECT * FROM (                                                               \n";
//            $stSql  .="SELECT  'L700' as reg,                                                                \n";
//            $stSql  .=" OD.exercicio,                                                        \n";
//            $stSql  .="replace(OD.cod_estrutural,'.','') as cod_cta_desp,                 \n";
//            $stSql  .=" OD.descricao as nom_despesa,                                                        \n";
//            $stSql  .=" tcers.tipo_conta_rubrica('OD.exercicio','OD.cod_estrutural') as ind_tipo_conta,     \n";
//            $stSql  .=" publico.fn_nivel(OD.cod_estrutural) as nm_nivel_conta                           \n";
//
//            $stSql  .=" FROM   orcamento.conta_despesa AS OD                                    \n";
//            $stSql  .=" WHERE     \n";
//            $stSql  .="   OD.exercicio <= '".$this->getDado('stExercicioLogado')."'             \n";
//            $stSql  .=" ) AS tabela                                  \n";
//            $stSql  .="  ORDER BY exercicio DESC , cod_cta_desp    \n";

            $stSql  ="  SELECT 'L700' as reg
        , OD.exercicio
        , replace(OD.cod_estrutural,'.','') as cod_cta_desp
        , OD.descricao as nom_despesa
        , tcers.tipo_conta_rubrica('OD.exercicio','OD.cod_estrutural') as ind_tipo_conta
        , publico.fn_nivel(OD.cod_estrutural) as nm_nivel_conta
     FROM orcamento.conta_despesa AS OD
    WHERE OD.exercicio = '".$this->getDado("stExercicioLogado")."'
UNION
    SELECT 'L700' as reg
        , OD.exercicio
        , replace(OD.cod_estrutural,'.','') as cod_cta_desp
        , OD.descricao as nom_despesa
        , tcers.tipo_conta_rubrica('OD.exercicio','OD.cod_estrutural') as ind_tipo_conta
        , publico.fn_nivel(OD.cod_estrutural) as nm_nivel_conta
     FROM orcamento.conta_despesa AS OD


    INNER JOIN (SELECT tabela.cod_estrutural
                  , tabela.exercicio

              FROM fn_transparenciaExportacaoEmpenho( '".$this->getDado("stExercicioLogado")."'
                                     , '".$this->getDado("dtInicial")."'
                                  , '".$this->getDado("dtFinal")."'
                                     , '".$this->getDado("stCodEntidade")."')
                   as tabela ( num_orgao           integer,
                        num_unidade     integer         ,
                        cod_funcao      integer         ,
                        cod_subfuncao   integer         ,
                        cod_programa    integer         ,
                        num_pao         integer         ,
                        cod_recurso     integer         ,
                        cod_estrutural  varchar         ,
                        cod_empenho     integer         ,
                        dt_empenho      date            ,
                        vl_empenhado    numeric(14,2)   ,
                        sinal           varchar(1)      ,
                        cgm             integer         ,
                        historico       varchar         ,
                        cod_pre_empenho integer         ,
                        exercicio       char(4)         ,
                        cod_entidade    integer         ,
                        ordem           integer         ,
                        oid             oid             ,
                        caracteristica  integer         ,
                        modalidade      integer         ,
                        nro_licitacao   text            ,
                        nom_modalidades text            ,
                        preco           text
                             )
                    WHERE tabela.cod_entidade in (".$this->getDado("stCodEntidade").")
            ) as empenhos
         ON empenhos.cod_estrutural = OD.cod_estrutural
        AND empenhos.exercicio = OD.exercicio


                WHERE OD.exercicio <='".$this->getDado("stExercicioLogado")."'  ";

        return $stSql;
    }
/**
 * Função para Exportação de dados do MANAD.
 *
 * @param  Object  $rsRecordSet Objeto RecordSet
 * @param  String  $stCondicao  String de condição do SQL (WHERE)
 * @param  Boolean $boTransacao
 * @return Object  Objeto Erro
 */
function recuperaDadosL750(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro();
    $obConexao   = new Conexao();
    $rsRecordSet = new RecordSet();

    $stSql = $this->montaRecuperaDadosL750();
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
}

function montaRecuperaDadosL750()
{
 $stSql =" SELECT   'L750' as reg
                            ,  to_char(dt_empenho, 'yyyy') as exerc
                            , tabela.cgm AS cod_fornecedor
                            , sw_cgm.nom_cgm AS nom_fornecedor
                            , CASE WHEN ((sw_cgm_pessoa_juridica.cnpj is null))
                            THEN
                              '1'
                            ELSE
                              '2'
                            END as tipo_fornecedor
                            , CASE WHEN
                            ((fi.timestamp_fim is  null AND fi.timestamp_inicio is null) OR (fi.timestamp_fim is not null ))
                            THEN
                              'Ativo'
                            ELSE
                              'Inativo'
                            END as status
                            , fi.motivo
                            , (SELECT array_to_string(
                                   ARRAY(
                                          SELECT nom_atividade
                                            FROM economico.atividade
                                            JOIN compras.fornecedor_atividade
                                              ON fornecedor_atividade.cod_atividade = atividade.cod_atividade
                                           WHERE fornecedor_atividade.cgm_fornecedor = tabela.cgm
                                        ) , ', ')
                              ) as nom_atividade
                            , fornecedor_classificacao.cod_catalogo
                            , fornecedor_classificacao.cod_classificacao
                            , sw_cgm_pessoa_juridica.cnpj as cnpj_fornecedor
                            , sw_cgm_pessoa_fisica.cpf as cpf_fornecedor
                            , sw_cgm.logradouro||', '||sw_cgm.numero||' '||sw_cgm.complemento||', bairro '||sw_cgm.bairro as end_fornecedor
                            , sw_municipio.nom_municipio as cid_fornecedor
                            , sw_uf.sigla_uf as uf_fornecedor
                            , sw_cgm.cep as cep_fornecedor
                            , catalogo_classificacao.descricao   as desc_tip_forn
                            , fornecedor_documentos.num_documento as nit_fornecedor

                 FROM fn_transparenciaExportacaoEmpenho( '".$this->getDado("stExercicioLogado")."'
                                     , '".$this->getDado("dtInicial")."'
                                  , '".$this->getDado("dtFinal")."'
                                     , '".$this->getDado("stCodEntidades")."')
                   as tabela ( num_orgao           integer,
                        num_unidade     integer         ,
                        cod_funcao      integer         ,
                        cod_subfuncao   integer         ,
                        cod_programa    integer         ,
                        num_pao         integer         ,
                        cod_recurso     integer         ,
                        cod_estrutural  varchar         ,
                        cod_empenho     integer         ,
                        dt_empenho      date            ,
                        vl_empenhado    numeric(14,2)   ,
                        sinal           varchar(1)      ,
                        cgm             integer         ,
                        historico       varchar         ,
                        cod_pre_empenho integer         ,
                        exercicio       char(4)         ,
                        cod_entidade    integer         ,
                        ordem           integer         ,
                        oid             oid             ,
                        caracteristica  integer         ,
                        modalidade      integer         ,
                        nro_licitacao   text            ,
                        nom_modalidades text            ,
                        preco           text
                             )
               INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = tabela.cgm

               LEFT JOIN sw_cgm_pessoa_fisica
                        ON sw_cgm_pessoa_fisica.numcgm = tabela.cgm

               LEFT JOIN sw_cgm_pessoa_juridica
                        ON sw_cgm_pessoa_juridica.numcgm = tabela.cgm

               LEFT JOIN sw_municipio
                        ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
                      AND sw_municipio.cod_uf = sw_cgm.cod_uf

              LEFT JOIN sw_uf
                       ON sw_uf.cod_uf = sw_municipio.cod_uf

              LEFT JOIN compras.fornecedor_classificacao
                       ON fornecedor_classificacao.cgm_fornecedor = tabela.cgm

              LEFT JOIN almoxarifado.catalogo_classificacao
                       ON catalogo_classificacao.cod_classificacao = fornecedor_classificacao.cod_classificacao
                     AND catalogo_classificacao.cod_catalogo = fornecedor_classificacao.cod_catalogo

              LEFT JOIN (SELECT coalesce(cfi.cgm_fornecedor,null) as cgm_fornecedor
                                , cfi.timestamp_inicio
                                , cfi.timestamp_fim
                                , cfi.motivo
                             FROM compras.fornecedor_inativacao as cfi
                                , (SELECT max(timestamp_inicio) as timestamp_inicio
                                        , cgm_fornecedor
                                     FROM compras.fornecedor_inativacao
                                 GROUP BY cgm_fornecedor
                                   ) as ativacao
                              WHERE ativacao.cgm_fornecedor = cfi.cgm_fornecedor
                                AND ativacao.timestamp_inicio = cfi.timestamp_inicio
                             ) as fi
                      ON  fi.cgm_fornecedor = tabela.cgm

                LEFT JOIN (SELECT num_documento,  cgm_fornecedor
                                   FROM licitacao.certificacao_documentos
                                 WHERE certificacao_documentos.cod_documento = 0
                               ) as fornecedor_documentos
                         ON fornecedor_documentos.cgm_fornecedor = tabela.cgm
                            WHERE tabela.cod_entidade in (".$this->getDado("stCodEntidades").")

              GROUP BY reg
                           ,exerc
                            , cod_fornecedor
                            , nom_fornecedor
                            , tipo_fornecedor
                            , status
                            , fi.motivo
                            , nom_atividade
                            , fornecedor_classificacao.cod_catalogo
                            , fornecedor_classificacao.cod_classificacao
                            , cnpj_fornecedor
                            , cpf_fornecedor
                            , end_fornecedor
                            , cid_fornecedor
                            , uf_fornecedor
                            , Cep_fornecedor
                            , desc_tip_forn
                            , nit_fornecedor
order by exerc ";

    return $stSql;
}
}
