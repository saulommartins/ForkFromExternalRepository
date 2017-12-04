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
     * Classe de mapeamento para a tabela IMOBILIARIO.IMOVEL
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMImovel.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.22  2007/01/18 12:38:40  dibueno
Alteração de acordo com a PL de 'busca de endereco do imovel'

Revision 1.21  2006/10/09 10:08:16  cercato
consulta para ipopupImovel.

Revision 1.20  2006/09/27 09:23:15  cercato
adicionadafuncao para recuperar mascara no componente ipopupimovel.

Revision 1.19  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.IMOVEL
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMImovel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMImovel()
{
    parent::Persistente();
    $this->setTabela('imobiliario.imovel');

    $this->setCampoCod('inscricao_municipal');
    $this->setComplementoChave('');

    $this->AddCampo('inscricao_municipal','integer',true,'',true,false);
    $this->AddCampo('cod_sublote','integer',true,'',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
    $this->AddCampo('dt_cadastro','date',true,'',false,false);
    $this->AddCampo('complemento','varchar',true,'50',false,false);
    $this->AddCampo('numero','varchar',false,'10',false,false);
    $this->AddCampo('cep','varchar',false,'10',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSQL  = " SELECT                                                      \n";
    $stSQL .= "     *                                                       \n";
    $stSQL .= " FROM                                                        \n";
    $stSQL .= "     (                                                       \n";
    $stSQL .= "     SELECT                                                  \n";
    $stSQL .= "     I.inscricao_municipal,                                  \n";
    $stSQL .= "     I.timestamp,                                            \n";
    $stSQL .= "     I.cod_lote,                                             \n";
    $stSQL .= "     I.cod_sublote,                                          \n";
    $stSQL .= "     IMV.numero,                                             \n";
    $stSQL .= "     IMV.complemento,                                        \n";
    $stSQL .= "     MIA.mat_registro_imovel,                                \n";
    $stSQL .= "     MIA.zona,                                               \n";
    $stSQL .= "     TO_CHAR( I.dt_cadastro, 'DD/MM/YYYY') AS dt_cadastro,   \n";
    $stSQL .= "     TO_CHAR( L.dt_inscricao,'DD/MM/YYYY') AS dt_inscricao,  \n";
    $stSQL .= "     LL.cod_localizacao,                                     \n";
    $stSQL .= "     LL.valor,                                               \n";
    $stSQL .= "     IP.cod_processo,                                        \n";
    $stSQL .= "     IP.ano_exercicio,                                       \n";
    $stSQL .= "     LOC.cod_nivel,                                          \n";
    $stSQL .= "     LOC.cod_vigencia,                                       \n";
    $stSQL .= "     LOC.valor_composto,                                     \n";
    $stSQL .= "     LOC.valor_reduzido,                                     \n";
    $stSQL .= "     LOC.valor AS valor_localizacao,                         \n";
    $stSQL .= "     LOC.nom_localizacao,                                    \n";
    $stSQL .= "     LOC.mascara,                                            \n";
    $stSQL .= "     LOC.nom_nivel,                                          \n";
    $stSQL .= "     IM.creci,                                               \n";
    $stSQL .= "     IC.cod_condominio,                                      \n";
    $stSQL .= "     TLO.nom_tipo||' '||NLO.nom_logradouro as logradouro,    \n";
    $stSQL .= "     NLO.nom_logradouro,                                     \n";
    $stSQL .= "     B.cod_bairro,                                           \n";
    $stSQL .= "     B.nom_bairro,                                           \n";
    $stSQL .= "     B.cod_municipio,                                        \n";
    $stSQL .= "     M.nom_municipio,                                        \n";
    $stSQL .= "     imobiliario.fn_calcula_area_imovel( I.inscricao_municipal ) AS area_imovel,                        \n";
    $stSQL .= "     imobiliario.fn_calcula_area_imovel_lote( I.inscricao_municipal ) AS area_imovel_lote,              \n";
    $stSQL .= "     imobiliario.fn_calcula_area_imovel_construcao( I.inscricao_municipal ) AS area_imovel_construcao,  \n";
    $stSQL .= "     proprietario.numcgm,                                    \n";
    $stSQL .= "     CASE                                                    \n";
    $stSQL .= "         WHEN                                                \n";
    $stSQL .= "             LR.cod_lote IS NOT NULL                         \n";
    $stSQL .= "         THEN                                                \n";
    $stSQL .= "             'rural'                                         \n";
    $stSQL .= "         WHEN                                                \n";
    $stSQL .= "             LU.cod_lote IS NOT NULL                         \n";
    $stSQL .= "         THEN                                                \n";
    $stSQL .= "             'urbano'                                        \n";
    $stSQL .= "     END AS TIPO_LOTE                                        \n";
    $stSQL .= "     FROM                                                    \n";
    $stSQL .= "         imobiliario.vw_imovel_ativo           AS I          \n";
    $stSQL .= "   INNER JOIN imobiliario.proprietario                       \n";
    $stSQL .= "           ON proprietario.inscricao_municipal = I.inscricao_municipal \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "             (SELECT                                         \n";
    $stSQL .= "                IMOP.*                                       \n";
    $stSQL .= "             FROM                                            \n";
    $stSQL .= "                imobiliario.imovel_processo AS IMOP,             \n";
    $stSQL .= "                (SELECT                                      \n";
    $stSQL .= "                    MAX (TIMESTAMP) AS TIMESTAMP,            \n";
    $stSQL .= "                    INSCRICAO_MUNICIPAL                      \n";
    $stSQL .= "                 FROM                                        \n";
    $stSQL .= "                    imobiliario.imovel_processo                  \n";
    $stSQL .= "                 GROUP BY                                    \n";
    $stSQL .= "                    INSCRICAO_MUNICIPAL) AS IMOPP            \n";
    $stSQL .= "             WHERE                                           \n";
    $stSQL .= "                IMOP.INSCRICAO_MUNICIPAL = IMOPP.INSCRICAO_MUNICIPAL \n";
    $stSQL .= "                AND IMOP.TIMESTAMP = IMOPP.TIMESTAMP) AS IP  \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         IP.inscricao_municipal = I.inscricao_municipal      \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         imobiliario.imovel_imobiliaria        AS IM             \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         I.inscricao_municipal = IM.inscricao_municipal      \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         imobiliario.imovel        AS IMV                        \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         I.inscricao_municipal = IMV.inscricao_municipal     \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         imobiliario.imovel_condominio         AS IC             \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         I.inscricao_municipal = IC.inscricao_municipal      \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         imobiliario.vw_matricula_imovel_atual AS MIA            \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         I.inscricao_municipal = MIA.inscricao_municipal,    \n";
    $stSQL .= "         imobiliario.vw_lote_ativo             AS L,             \n";
    $stSQL .= "         imobiliario.lote_localizacao          AS LL             \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         imobiliario.lote_rural                AS LR             \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         LR.cod_lote = LL.cod_lote                           \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         imobiliario.lote_urbano               AS LU             \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         LU.cod_lote = LL.cod_lote ,                         \n";
    $stSQL .= "     imobiliario.vw_localizacao_ativa          AS LOC,           \n";
    $stSQL .= "     imobiliario.lote_bairro                   AS LB,            \n";
    $stSQL .= "     sw_bairro                            AS B,             \n";
    $stSQL .= "     sw_municipio                            AS M,             \n";
    $stSQL .= "     imobiliario.imovel_confrontacao           AS ICO,           \n";
    $stSQL .= "     imobiliario.confrontacao_trecho           AS CT,            \n";
    $stSQL .= "     sw_logradouro                        AS LO,            \n";
    $stSQL .= "     sw_nome_logradouro                   AS NLO,           \n";
    $stSQL .= "     sw_tipo_logradouro                   AS TLO            \n";
    $stSQL .= " WHERE                                                       \n";
    $stSQL .= "     I.cod_lote = L.cod_lote                         AND     \n";
    $stSQL .= "     L.cod_lote = LL.cod_lote                        AND     \n";
    $stSQL .= "     LL.cod_localizacao = LOC.cod_localizacao        AND     \n";
    $stSQL .= "     LB.cod_lote            = L.cod_lote             AND     \n";
    $stSQL .= "     LB.cod_bairro          = B.cod_bairro           AND     \n";
    $stSQL .= "     LB.cod_municipio       = B.cod_municipio        AND     \n";
    $stSQL .= "     LB.cod_uf              = B.cod_uf               AND     \n";
    $stSQL .= "     ICO.inscricao_municipal = I.inscricao_municipal AND     \n";
    $stSQL .= "     ICO.cod_lote            = L.cod_lote            AND     \n";
    $stSQL .= "     CT.cod_confrontacao    = ICO.cod_confrontacao   AND     \n";
    $stSQL .= "     CT.cod_lote            = ICO.cod_lote           AND     \n";
    $stSQL .= "     CT.cod_logradouro      = LO.cod_logradouro      AND     \n";
    $stSQL .= "     CT.principal           = true                   AND     \n";
    $stSQL .= "     NLO.cod_logradouro     = LO.cod_logradouro      AND     \n";
    $stSQL .= "     NLO.cod_tipo           = TLO.cod_tipo           AND     \n";
    $stSQL .= "     M.cod_uf               = B.cod_uf               AND     \n";
    $stSQL .= "     M.cod_municipio        = B.cod_municipio             \n";
    $stSQL .= " ) AS IMOVEL                                                 \n";

    return $stSQL;
}

function recuperaRelacionamentoConsulta(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoConsulta().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoConsulta()
{
    $atSql  = " SELECT                                                                                      \r";
    $atSql .= "     *                                                                                       \r";
    $atSql .= " FROM                                                                                        \r";
    $atSql .= "     (                                                                                       \r";
    $atSql .= "     SELECT                                                                                  \r";
    $atSql .= "         I.inscricao_municipal,
                        calculafracaoideal( I.inscricao_municipal) AS fracao_ideal,                         \r";
    $atSql .= "         I.timestamp,                                                                        \r";
    $atSql .= "         IL.cod_lote,                                                                        \r";
    $atSql .= "         I.cod_sublote,                                                                      \r";
    $atSql .= "         I.numero,                                                                           \r";
    $atSql .= "         I.complemento,                                                                      \r";
    $atSql .= "         MIA.mat_registro_imovel,                                                            \r";
    $atSql .= "         MIA.zona,                                                                           \r";
    $atSql .= "         TO_CHAR( I.dt_cadastro, 'DD/MM/YYYY') AS dt_cadastro,                               \r";
    $atSql .= "         IP.cod_processo,                                                                    \r";
    $atSql .= "         IP.ano_exercicio,                                                                   \r";
    $atSql .= "         IM.creci,                                                                           \r";
    $atSql .= "         IC.cod_condominio,                                                                  \r";
    $atSql .= "         AC.area_real,                                                                       \r";
    $atSql .= "         imobiliario.fn_consulta_logradouro( I.inscricao_municipal ) AS                      \r";
    $atSql .= " logradouro,                                                                                 \r";
    $atSql .= "         imobiliario.fn_calcula_area_imovel( I.inscricao_municipal ) AS area_imovel,                        \r";
    $atSql .= "         imobiliario.fn_calcula_area_imovel_lote( I.inscricao_municipal ) AS area_imovel_lote,              \r";
    $atSql .= "         imobiliario.fn_calcula_area_imovel_construcao( I.inscricao_municipal ) AS area_imovel_construcao,  \r";
    $atSql .= "         TO_CHAR( IBI.dt_inicio, 'DD/MM/YYYY') AS dt_baixa,                                  \r";
    $atSql .= "         TO_CHAR( IBI.dt_termino, 'DD/MM/YYYY') AS dt_termino,                               \r";
    $atSql .= "         IBI.justificativa                                                                   \r";
    $atSql .= "     FROM                                                                                    \r";
    $atSql .= "         imobiliario.imovel AS I                                                             \r";
    $atSql .= "     LEFT JOIN imobiliario.imovel_lote AS IL ON                                              \r";
    $atSql .= "         IL.inscricao_municipal = I.inscricao_municipal                                      \r";
    $atSql .= "                                                                                             \r";
    $atSql .= "     LEFT JOIN                                                                               \r";
    $atSql .= "         (SELECT                                                                             \r";
    $atSql .= "             IMOP.*                                                                          \r";
    $atSql .= "         FROM                                                                                \r";
    $atSql .= "             imobiliario.imovel_processo AS IMOP,                                            \r";
    $atSql .= "             (SELECT                                                                         \r";
    $atSql .= "                 MAX (TIMESTAMP) AS TIMESTAMP,                                               \r";
    $atSql .= "                 INSCRICAO_MUNICIPAL                                                         \r";
    $atSql .= "              FROM                                                                           \r";
    $atSql .= "                 imobiliario.imovel_processo                                                 \r";
    $atSql .= "              GROUP BY                                                                       \r";
    $atSql .= "                 INSCRICAO_MUNICIPAL                                                         \r";
    $atSql .= "              ) AS IMOPP                                                                     \r";
    $atSql .= "         WHERE                                                                               \r";
    $atSql .= "             IMOP.INSCRICAO_MUNICIPAL = IMOPP.INSCRICAO_MUNICIPAL                            \r";
    $atSql .= "             AND IMOP.TIMESTAMP = IMOPP.TIMESTAMP                                            \r";
    $atSql .= "         ) AS IP ON                                                                          \r";
    $atSql .= "         IP.inscricao_municipal = I.inscricao_municipal                                      \r";
    $atSql .= "                                                                                             \r";
    $atSql .= "     LEFT JOIN imobiliario.imovel_imobiliaria AS IM ON                                       \r";
    $atSql .= "         IM.inscricao_municipal = I.inscricao_municipal                                      \r";
    $atSql .= "                                                                                             \r";
    $atSql .= "     LEFT JOIN (                                                                             \r";
    $atSql .= "        SELECT                                                                               \r";
    $atSql .= "            BAI.*                                                                            \r";
    $atSql .= "        FROM                                                                                 \r";
    $atSql .= "            imobiliario.baixa_imovel AS BAI,                                                 \r";
    $atSql .= "            (                                                                                \r";
    $atSql .= "            SELECT                                                                           \r";
    $atSql .= "                MAX (TIMESTAMP) AS TIMESTAMP,                                                \r";
    $atSql .= "                inscricao_municipal                                                          \r";
    $atSql .= "            FROM                                                                             \r";
    $atSql .= "                imobiliario.baixa_imovel                                                     \r";
    $atSql .= "            GROUP BY                                                                         \r";
    $atSql .= "                inscricao_municipal                                                          \r";
    $atSql .= "            ) AS BI                                                                          \r";
    $atSql .= "        WHERE                                                                                \r";
    $atSql .= "            BAI.inscricao_municipal = BI.inscricao_municipal AND                             \r";
    $atSql .= "            BAI.timestamp = BI.timestamp                                                     \r";
    $atSql .= "    ) IBI ON                                                                                 \r";
    $atSql .= "         IBI.inscricao_municipal = I.inscricao_municipal                                     \r";
    $atSql .= "                                                                                             \r";
    $atSql .= "     LEFT JOIN imobiliario.imovel_condominio AS IC ON                                        \r";
    $atSql .= "         IC.inscricao_municipal = I.inscricao_municipal                                      \r";
    $atSql .= "                                                                                             \r";
    $atSql .= "     LEFT JOIN                                                                               \r";
    $atSql .= "          (SELECT                                                                            \r";
    $atSql .= "                  IC.INSCRICAO_MUNICIPAL,                                                    \r";
    $atSql .= "                  CC.COD_CONDOMINIO,                                                         \r";
    $atSql .= "                  SUM(AC.AREA_REAL) AS AREA_REAL                                             \r";
    $atSql .= "           FROM                                                                              \r";
    $atSql .= "                  imobiliario.area_construcao       AS AC,                                   \r";
    $atSql .= "                  imobiliario.construcao_condominio AS CC,                                   \r";
    $atSql .= "                  imobiliario.imovel_condominio     AS IC                                    \r";
    $atSql .= "           WHERE                                                                             \r";
    $atSql .= "                  AC.COD_CONSTRUCAO = CC.COD_CONSTRUCAO AND                                  \r";
    $atSql .= "                  CC.COD_CONDOMINIO = IC.COD_CONDOMINIO                                      \r";
    $atSql .= "           GROUP BY                                                                          \r";
    $atSql .= "                  IC.INSCRICAO_MUNICIPAL, CC.COD_CONDOMINIO                                  \r";
    $atSql .= "           ) AS AC                                                                           \r";
    $atSql .= "     ON                                                                                      \r";
    $atSql .= "           AC.INSCRICAO_MUNICIPAL = I.INSCRICAO_MUNICIPAL AND                                \r";
    $atSql .= "           AC.COD_CONDOMINIO      = IC.COD_CONDOMINIO                                        \r";
    $atSql .= "     LEFT JOIN imobiliario.vw_matricula_imovel_atual AS MIA ON                               \r";
    $atSql .= "         MIA.inscricao_municipal = I.inscricao_municipal                                     \r";
    $atSql .= " ) AS IMOVEL                                                                                 \r";

    return $atSql;
}

function recuperaRelacionamentoAlteracao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoAlteracao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoAlteracao()
{
    $atSql  = " SELECT                                                                                      \n";
    $atSql .= "     *                                                                                       \n";
    $atSql .= " FROM                                                                                        \n";
    $atSql .= "     (                                                                                       \n";
    $atSql .= "     SELECT                                                                                  \n";
    $atSql .= "         I.inscricao_municipal,                                                              \n";
    $atSql .= "         I.timestamp,                                                                        \n";
    $atSql .= "         IL.cod_lote,                                                                        \n";
    $atSql .= "         I.cod_sublote,                                                                      \n";
    $atSql .= "         I.numero,                                                                           \n";
    $atSql .= "         I.complemento,                                                                      \n";
    $atSql .= "         I.cep,                                                                              \n";
    $atSql .= "         MIA.mat_registro_imovel,                                                            \n";
    $atSql .= "         MIA.zona,                                                                           \n";
    $atSql .= "         TO_CHAR( I.dt_cadastro, 'DD/MM/YYYY') AS dt_cadastro,                               \n";
    $atSql .= "         IP.cod_processo,                                                                    \n";
    $atSql .= "         IP.ano_exercicio,                                                                   \n";
    $atSql .= "         IM.creci,                                                                           \n";
    $atSql .= "         IC.cod_condominio,                                                                  \n";
    $atSql .= "         CO.nom_condominio,                                                                  \n";
    $atSql .= "         TO_CHAR( IBI.timestamp, 'DD/MM/YYYY') AS dt_baixa,                                  \n";
    $atSql .= "         IBI.justificativa,                                                                  \n";
    $atSql .= "         CT.cod_confrontacao                                                                 \n";
    $atSql .= "     FROM                                                                                    \n";
    $atSql .= "         imobiliario.imovel AS I                                                             \n";
    $atSql .= "     INNER JOIN imobiliario.imovel_lote AS IL ON                                             \n";
    $atSql .= "         IL.inscricao_municipal = I.inscricao_municipal                                      \n";
    $atSql .= "                                                                                             \n";
    $atSql .= "     INNER JOIN imobiliario.imovel_confrontacao AS ICO ON                                    \n";
    $atSql .= "         ICO.inscricao_municipal = I.inscricao_municipal                                     \n";
    $atSql .= "                                                                                             \n";
    $atSql .= "     LEFT JOIN imobiliario.confrontacao_trecho AS CT ON                                      \n";
    $atSql .= "         CT.cod_lote = ICO.cod_lote AND                                                      \n";
    $atSql .= "         CT.cod_confrontacao = ICO.cod_confrontacao AND                                      \n";
    $atSql .= "         CT.principal = 't'                                                                  \n";
    $atSql .= "                                                                                             \n";
    $atSql .= "     LEFT JOIN                                                                               \n";
    $atSql .= "         (SELECT                                                                             \n";
    $atSql .= "             IMOP.*                                                                          \n";
    $atSql .= "         FROM                                                                                \n";
    $atSql .= "             imobiliario.imovel_processo AS IMOP,                                            \n";
    $atSql .= "             (SELECT                                                                         \n";
    $atSql .= "                 MAX (TIMESTAMP) AS TIMESTAMP,                                               \n";
    $atSql .= "                 INSCRICAO_MUNICIPAL                                                         \n";
    $atSql .= "              FROM                                                                           \n";
    $atSql .= "                 imobiliario.imovel_processo                                                 \n";
    $atSql .= "              GROUP BY                                                                       \n";
    $atSql .= "                 INSCRICAO_MUNICIPAL                                                         \n";
    $atSql .= "              ) AS IMOPP                                                                     \n";
    $atSql .= "         WHERE                                                                               \n";
    $atSql .= "             IMOP.INSCRICAO_MUNICIPAL = IMOPP.INSCRICAO_MUNICIPAL                            \n";
    $atSql .= "             AND IMOP.TIMESTAMP = IMOPP.TIMESTAMP                                            \n";
    $atSql .= "         ) AS IP ON                                                                          \n";
    $atSql .= "         IP.inscricao_municipal = I.inscricao_municipal                                      \n";
    $atSql .= "                                                                                             \n";
    $atSql .= "     LEFT JOIN imobiliario.imovel_imobiliaria AS IM ON                                       \n";
    $atSql .= "         IM.inscricao_municipal = I.inscricao_municipal                                      \n";
    $atSql .= "                                                                                             \n";
    $atSql .= "     LEFT JOIN imobiliario.baixa_imovel AS IBI ON                                            \n";
    $atSql .= "         IBI.inscricao_municipal = I.inscricao_municipal                                     \n";
    $atSql .= "                                                                                             \n";
    $atSql .= "     LEFT JOIN imobiliario.imovel_condominio AS IC ON                                        \n";
    $atSql .= "         IC.inscricao_municipal = I.inscricao_municipal                                      \n";
    $atSql .= "                                                                                             \n";
    $atSql .= "     LEFT JOIN imobiliario.condominio AS CO ON                                               \n";
    $atSql .= "         IC.cod_condominio = CO.cod_condominio                                               \n";
    $atSql .= "                                                                                             \n";
    $atSql .= "     LEFT JOIN imobiliario.vw_matricula_imovel_atual AS MIA ON                               \n";
    $atSql .= "         MIA.inscricao_municipal = I.inscricao_municipal                                     \n";
    $atSql .= " ) AS IMOVEL                                                                                 \n";

    return $atSql;
}

function recuperaRelacionamentoLista(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoLista().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoLista()
{
    $stSQL  = " SELECT                                                      \n";
    $stSQL .= "     *                                                       \n";
    $stSQL .= " FROM                                                        \n";
    $stSQL .= "     (                                                       \n";
    $stSQL .= "     SELECT                                                  \n";
    $stSQL .= "     I.inscricao_municipal,                                  \n";
    $stSQL .= "     I.cod_lote,                                             \n";
    $stSQL .= "     I.cod_sublote,                                          \n";
    $stSQL .= "     LL.cod_localizacao,                                     \n";
    $stSQL .= "     LL.valor,                                               \n";
    $stSQL .= "     IP.cod_processo,                                        \n";
    $stSQL .= "     IP.ano_exercicio,                                       \n";
    $stSQL .= "     LOC.valor_composto,                                     \n";
    $stSQL .= "     IC.cod_condominio,                                      \n";
    $stSQL .= "     CC.nom_condominio,                                      \n";
    $stSQL .= "     II.creci,                                               \n";
    $stSQL .= "     CASE                                                    \n";
    $stSQL .= "         WHEN                                                \n";
    $stSQL .= "             CGMC.nom_cgm IS NOT NULL                        \n";
    $stSQL .= "         THEN                                                \n";
    $stSQL .= "             CGMC.nom_cgm                                    \n";
    $stSQL .= "         WHEN                                                \n";
    $stSQL .= "             CGMI.nom_cgm IS NOT NULL                        \n";
    $stSQL .= "         THEN                                                \n";
    $stSQL .= "             CGMI.nom_cgm                                    \n";
    $stSQL .= "     END AS NOME_CGM,                                        \n";
    $stSQL .= "     CASE                                                    \n";
    $stSQL .= "         WHEN                                                \n";
    $stSQL .= "             LR.cod_lote IS NOT NULL                         \n";
    $stSQL .= "         THEN                                                \n";
    $stSQL .= "             'rural'                                         \n";
    $stSQL .= "         WHEN                                                \n";
    $stSQL .= "             LU.cod_lote IS NOT NULL                         \n";
    $stSQL .= "         THEN                                                \n";
    $stSQL .= "             'urbano'                                        \n";
    $stSQL .= "     END AS TIPO_LOTE                                        \n";
    $stSQL .= "     FROM                                                    \n";
    $stSQL .= "         imobiliario.vw_imovel_ativo           AS I              \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         imobiliario.imovel_condominio         as IC             \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         I.inscricao_municipal = IC.inscricao_municipal      \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         imobiliario.condominio                as CC             \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         IC.cod_condominio = CC.cod_condominio               \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "             (SELECT                                         \n";
    $stSQL .= "                IMOP.*                                       \n";
    $stSQL .= "             FROM                                            \n";
    $stSQL .= "                imobiliario.imovel_processo AS IMOP,             \n";
    $stSQL .= "                (SELECT                                      \n";
    $stSQL .= "                    MAX (TIMESTAMP) AS TIMESTAMP,            \n";
    $stSQL .= "                    INSCRICAO_MUNICIPAL                      \n";
    $stSQL .= "                 FROM                                        \n";
    $stSQL .= "                    imobiliario.imovel_processo                  \n";
    $stSQL .= "                 GROUP BY                                    \n";
    $stSQL .= "                    INSCRICAO_MUNICIPAL) AS IMOPP            \n";
    $stSQL .= "             WHERE                                           \n";
    $stSQL .= "                IMOP.INSCRICAO_MUNICIPAL = IMOPP.INSCRICAO_MUNICIPAL \n";
    $stSQL .= "                AND IMOP.TIMESTAMP = IMOPP.TIMESTAMP) AS IP  \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         I.inscricao_municipal = IP.inscricao_municipal      \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         imobiliario.imovel_imobiliaria        as II             \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         I.inscricao_municipal = II.inscricao_municipal      \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         imobiliario.corretor                  as COR            \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         II.creci = COR.creci                                \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         imobiliario.imobiliaria               as IMO            \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         II.creci = IMO.creci                                \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         sw_cgm                        as CGMC           \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         COR.numcgm = CGMC.numcgm                            \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         sw_cgm                        as CGMI           \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         IMO.numcgm = CGMI.numcgm,                           \n";
    $stSQL .= "         imobiliario.vw_lote_ativo             AS L,             \n";
    $stSQL .= "         imobiliario.lote_localizacao          AS LL             \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         imobiliario.lote_rural                AS LR             \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         LR.cod_lote = LL.cod_lote                           \n";
    $stSQL .= "     LEFT JOIN                                               \n";
    $stSQL .= "         imobiliario.lote_urbano               AS LU             \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         LU.cod_lote = LL.cod_lote,                          \n";
    $stSQL .= "         imobiliario.vw_localizacao_ativa      AS LOC            \n";
    $stSQL .= "     WHERE                                                   \n";
    $stSQL .= "         I.cod_lote = L.cod_lote                         AND \n";
    $stSQL .= "         L.cod_lote = LL.cod_lote                        AND \n";
    $stSQL .= "         LL.cod_localizacao = LOC.cod_localizacao            \n";
    $stSQL .= " ) AS IMOVEL                                                 \n";

    return $stSQL;

}

function recuperaRelacionamentoMovimentacoes(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = "select tbl.*, (select nom_cgm from sw_cgm where numcgm = tbl.numcgm) as nom_cgm from ( \n";
    $stSql .= $this->montaRecuperaRelacionamentoMovimentacoes().$stFiltro.$stOrdem ;
    $stSql .= ") as tbl ";

    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoMovimentacoes()
{
    $stSql  = "SELECT                                                             \n";

    $stSql .= "    IM.INSCRICAO_MUNICIPAL,                                            \n";
    $stSql .= "    max(CG.NUMCGM) as numcgm,                                                               \n";
    $stSql .= "    max(TI.cod_transferencia) as cod_transferencia,
                   CASE WHEN ilr.cod_lote IS NOT NULL THEN
                        true
                   ELSE
                        false
                   END AS lote_rural \n";

    $stSql .= "FROM                                                    \n";
    $stSql .= "    IMOBILIARIO.LOTE_LOCALIZACAO AS LT,                 \n";
    $stSql .= "    IMOBILIARIO.PROPRIETARIO AS PR,                     \n";
    $stSql .= "    sw_cgm AS CG,                                        \n";
    $stSql .= "    IMOBILIARIO.IMOVEL_LOTE AS IM                      \n";

    $stSql .= "
               LEFT JOIN
                    imobiliario.lote_rural AS ilr
               ON
                    ilr.cod_lote = im.cod_lote \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "    IMOBILIARIO.TRANSFERENCIA_IMOVEL AS TI     \n";
    $stSql .= "ON                                                                     \n";
    $stSql .= "    TI.inscricao_municipal = IM.inscricao_municipal    \n";

    $stSql .= "WHERE                                                  \n";
    $stSql .= "    IM.COD_LOTE = LT.COD_LOTE AND                       \n";
    $stSql .= "    IM.INSCRICAO_MUNICIPAL = PR.INSCRICAO_MUNICIPAL AND \n";
    $stSql .= "    PR.NUMCGM = CG.NUMCGM                               \n";

    return $stSql;
}

function recuperaRelacionamentoProcesso(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoProcesso().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoProcesso()
{
    $stSQL  = " SELECT                                                      \n";
    $stSQL .= "     ip.inscricao_municipal as inscricao_municipal,          \n";
    $stSQL .= "     ip.cod_processo as cod_processo,                        \n";
    $stSQL .= "     ip.ano_exercicio as ano_exercicio,                      \n";
    $stSQL .= "     lpad(ip.cod_processo::varchar,5,'0') || '/' || ip.ano_exercicio as cod_processo_ano,                      \n";
    $stSQL .= "     ip.timestamp as timestamp,                              \n";
    $stSQL .= "     to_char(ip.timestamp,'dd/mm/yyyy') as data,             \n";
    $stSQL .= "     to_char(ip.timestamp,'hh24:mi:ss') as hora                 \n";
    $stSQL .= " FROM                                                        \n";
    $stSQL .= "         imobiliario.imovel_processo AS ip                       \n";

    return $stSQL;

}

function recuperaRelacionamentoRelatorio(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if ($stOrder) {
        $stOrder = " ORDER BY ".$stOrder;
    }
    $stSql = $this->montaRecuperaRelacionamentoRelatorio().$stFiltro.$stOrder;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoRelatorio()
{
$stSQL  = "SELECT                                                             \n";
$stSQL .= "     I.inscricao_municipal,                                        \n";
$stSQL .= "     IL.cod_lote,                                                  \n";
$stSQL .= "     LOC.cod_nivel,                                                \n";
$stSQL .= "     LOC.valor_composto||' '||LOC.nom_localizacao as localizacao,  \n";
$stSQL .= "     IM.creci,                                                     \n";
$stSQL .= "     B.nom_bairro,                                                 \n";
$stSQL .= "     TLO.nom_tipo||' '||NLO.nom_logradouro as logradouro,          \n";
$stSQL .= "     C.nom_cgm||' - '||IP.cota as proprietario_cota,               \n";
$stSQL .= " CASE                                                              \n";
$stSQL .= "     WHEN LR.cod_lote IS NOT NULL THEN 'Rural'                     \n";
$stSQL .= "     WHEN LU.cod_lote IS NOT NULL THEN 'Urbano'                    \n";
$stSQL .= " END AS TIPO_LOTE,                                                 \n";
$stSQL .= " CASE                                                              \n";
$stSQL .= "    WHEN BI.inscricao_municipal IS NOT NULL THEN 'Baixado'         \n";
$stSQL .= "    WHEN BI.inscricao_municipal IS NULL THEN     'Ativo'           \n";
$stSQL .= " END AS situacao                                                   \n";
$stSQL .= " FROM                                                              \n";
$stSQL .= "     imobiliario.imovel_lote               AS IL,                      \n";
$stSQL .= "     imobiliario.imovel                    AS I                        \n";
$stSQL .= "     LEFT JOIN imobiliario.imovel_imobiliaria  AS IM ON                \n";
$stSQL .= "         I.inscricao_municipal = IM.inscricao_municipal            \n";
$stSQL .= "     LEFT JOIN imobiliario.baixa_imovel        AS BI ON                \n";
$stSQL .= "         BI.inscricao_municipal = I.inscricao_municipal            \n";
$stSQL .= "     LEFT JOIN imobiliario.vw_matricula_imovel_atual AS MIA ON         \n";
$stSQL .= "         MIA.inscricao_municipal = I.inscricao_municipal,          \n";
$stSQL .= "     imobiliario.proprietario              AS IP,                      \n";
$stSQL .= "     sw_cgm                           AS C,                       \n";
$stSQL .= "     imobiliario.vw_lote_ativo             AS L,                       \n";
$stSQL .= "     imobiliario.lote_localizacao          AS LL                       \n";
$stSQL .= "     LEFT JOIN imobiliario.lote_rural          AS LR ON                \n";
$stSQL .= "         LR.cod_lote = LL.cod_lote                                 \n";
$stSQL .= "     LEFT JOIN imobiliario.lote_urbano         AS LU ON                \n";
$stSQL .= "         LU.cod_lote = LL.cod_lote ,                               \n";
$stSQL .= "     imobiliario.vw_localizacao_ativa      AS LOC,                     \n";
$stSQL .= "     imobiliario.lote_bairro               AS LB,                      \n";
$stSQL .= "     sw_bairro                        AS B,                       \n";
$stSQL .= "     imobiliario.imovel_confrontacao       AS IC,                      \n";
$stSQL .= "     imobiliario.confrontacao_trecho       AS CT,                      \n";
$stSQL .= "     sw_logradouro                    AS LO,                      \n";
$stSQL .= "     sw_nome_logradouro               AS NLO,                     \n";
$stSQL .= "     sw_tipo_logradouro               AS TLO                      \n";
$stSQL .= " WHERE                                                             \n";
$stSQL .= "     I.inscricao_municipal  = IP.inscricao_municipal AND           \n";
$stSQL .= "     IP.numcgm              = C.numcgm               AND           \n";
$stSQL .= "     IL.inscricao_municipal = I.inscricao_municipal  AND           \n";
$stSQL .= "     IL.cod_lote            = L.cod_lote             AND           \n";
$stSQL .= "     LL.cod_lote            = L.cod_lote             AND           \n";
$stSQL .= "     LL.cod_localizacao     = LOC.cod_localizacao    AND           \n";
$stSQL .= "     LB.cod_lote            = L.cod_lote             AND           \n";
$stSQL .= "     LB.cod_bairro          = B.cod_bairro           AND           \n";
$stSQL .= "     LB.cod_municipio       = B.cod_municipio        AND           \n";
$stSQL .= "     LB.cod_uf              = B.cod_uf               AND           \n";
$stSQL .= "     IC.inscricao_municipal = I.inscricao_municipal  AND           \n";
$stSQL .= "     IC.cod_lote            = IL.cod_lote            AND           \n";
$stSQL .= "     CT.cod_confrontacao    = IC.cod_confrontacao    AND           \n";
$stSQL .= "     CT.cod_lote            = IC.cod_lote            AND           \n";
$stSQL .= "     CT.cod_logradouro      = LO.cod_logradouro      AND           \n";
$stSQL .= "     CT.principal           = true                   AND           \n";
$stSQL .= "     NLO.cod_logradouro     = LO.cod_logradouro      AND           \n";
$stSQL .= "     NLO.cod_tipo           = TLO.cod_tipo                         \n";

return $stSQL;
}

function recuperaRelacionamentoListaAlteracao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoListaAlteracao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoListaAlteracao()
{
    $stSQL  = " SELECT                                                               \n";
    $stSQL .= "      *                                                               \n";
    $stSQL .= " FROM                                                                 \n";
    $stSQL .= "      (                                                               \n";
    $stSQL .= "      SELECT                                                          \n";
    $stSQL .= "          I.inscricao_municipal,                                      \n";
    $stSQL .= "          I.cod_lote,                                                 \n";
    $stSQL .= "          I.cod_sublote,                                              \n";
    $stSQL .= "          LL.cod_localizacao,                                         \n";
    $stSQL .= "          LL.valor,                                                   \n";
    $stSQL .= "          LOC.valor_composto,                                         \n";
    $stSQL .= "          II.creci,                                                   \n";
    $stSQL .= "          IP.cod_processo,                                            \n";
    $stSQL .= "          IP.ano_exercicio,                                           \n";

    $stSQL .= "          CASE                                                        \n";
    $stSQL .= "          WHEN                                                        \n";
    $stSQL .= "              CGMC.nom_cgm IS NOT NULL                                \n";
    $stSQL .= "          THEN                                                        \n";
    $stSQL .= "              CGMC.nom_cgm                                            \n";
    $stSQL .= "          WHEN                                                        \n";
    $stSQL .= "              CGMI.nom_cgm IS NOT NULL                                \n";
    $stSQL .= "          THEN                                                        \n";
    $stSQL .= "              CGMI.nom_cgm                                            \n";
    $stSQL .= "          END AS NOME_CGM,                                            \n";
    $stSQL .= "          CASE                                                        \n";
    $stSQL .= "          WHEN                                                        \n";
    $stSQL .= "              LR.cod_lote IS NOT NULL                                 \n";
    $stSQL .= "          THEN                                                        \n";
    $stSQL .= "              'rural'                                                 \n";
    $stSQL .= "          WHEN                                                        \n";
    $stSQL .= "              LU.cod_lote IS NOT NULL                                 \n";
    $stSQL .= "          THEN                                                        \n";
    $stSQL .= "              'urbano'                                                \n";
    $stSQL .= "          END AS TIPO_LOTE                                            \n";
    $stSQL .= "      FROM                                                            \n";
    $stSQL .= "          imobiliario.vw_imovel_ativo AS I                            \n";
    $stSQL .= "                                                                      \n";
    $stSQL .= "          LEFT JOIN imobiliario.imovel_imobiliaria  AS II  ON         \n";
    $stSQL .= "              I.inscricao_municipal = II.inscricao_municipal          \n";
    $stSQL .= "                                                                      \n";
    $stSQL .= "          LEFT JOIN imobiliario.corretor AS COR ON                    \n";
    $stSQL .= "              II.creci = COR.creci                                    \n";
    $stSQL .= "                                                                      \n";
    $stSQL .= "          LEFT JOIN imobiliario.imobiliaria as IMO ON                 \n";
    $stSQL .= "              II.creci = IMO.creci                                    \n";
    $stSQL .= "                                                                      \n";
    $stSQL .= "          LEFT JOIN sw_cgm AS CGMC ON                                 \n";
    $stSQL .= "              COR.numcgm = CGMC.numcgm                                \n";
    $stSQL .= "                                                                      \n";
    $stSQL .= "          LEFT JOIN sw_cgm AS CGMI ON                                 \n";
    $stSQL .= "              IMO.numcgm = CGMI.numcgm                                \n";
    $stSQL .= "                                                                      \n";

    $stSQL .= "   LEFT JOIN                                               \n";
    $stSQL .= "             (SELECT                                         \n";
    $stSQL .= "                IMOP.*                                       \n";
    $stSQL .= "             FROM                                            \n";
    $stSQL .= "                imobiliario.imovel_processo AS IMOP,             \n";
    $stSQL .= "                (SELECT                                      \n";
    $stSQL .= "                    MAX (TIMESTAMP) AS TIMESTAMP,            \n";
    $stSQL .= "                    INSCRICAO_MUNICIPAL                      \n";
    $stSQL .= "                 FROM                                        \n";
    $stSQL .= "                    imobiliario.imovel_processo                  \n";
    $stSQL .= "                 GROUP BY                                    \n";
    $stSQL .= "                    INSCRICAO_MUNICIPAL) AS IMOPP            \n";
    $stSQL .= "             WHERE                                           \n";
    $stSQL .= "                IMOP.INSCRICAO_MUNICIPAL = IMOPP.INSCRICAO_MUNICIPAL \n";
    $stSQL .= "                AND IMOP.TIMESTAMP = IMOPP.TIMESTAMP) AS IP  \n";
    $stSQL .= "     ON                                                      \n";
    $stSQL .= "         I.inscricao_municipal = IP.inscricao_municipal      \n";

    $stSQL .= "            LEFT JOIN (                                               \n";
    $stSQL .= "                SELECT                                                \n";
    $stSQL .= "                    BAT.*                                             \n";
    $stSQL .= "                FROM                                                  \n";
    $stSQL .= "                    imobiliario.baixa_lote AS BAT,                    \n";
    $stSQL .= "                    (                                                 \n";
    $stSQL .= "                    SELECT                                            \n";
    $stSQL .= "                        MAX (TIMESTAMP) AS TIMESTAMP,                 \n";
    $stSQL .= "                        cod_lote                                      \n";
    $stSQL .= "                    FROM                                              \n";
    $stSQL .= "                        imobiliario.baixa_lote                        \n";
    $stSQL .= "                    GROUP BY                                          \n";
    $stSQL .= "                        cod_lote                                      \n";
    $stSQL .= "                    ) AS BT                                           \n";
    $stSQL .= "                WHERE                                                 \n";
    $stSQL .= "                    BAT.cod_lote = BT.cod_lote AND                    \n";
    $stSQL .= "                    BAT.timestamp = BT.timestamp                      \n";
    $stSQL .= "            ) bl                                                      \n";
    $stSQL .= "            ON                                                        \n";
    $stSQL .= "            I.cod_lote = bl.cod_lote,                                 \n";

    $stSQL .= "         imobiliario.lote_localizacao AS LL                           \n";
    $stSQL .= "                                                                      \n";
    $stSQL .= "          LEFT JOIN imobiliario.lote_rural AS LR ON                   \n";
    $stSQL .= "              LR.cod_lote = LL.cod_lote                               \n";
    $stSQL .= "                                                                      \n";
    $stSQL .= "          LEFT JOIN imobiliario.lote_urbano AS LU ON                  \n";
    $stSQL .= "              LU.cod_lote = LL.cod_lote,                              \n";
    $stSQL .= "                                                                      \n";
    $stSQL .= "         (                                                            \n";
    $stSQL .= "             SELECT                                                   \n";
    $stSQL .= "                 loc.codigo_composto AS valor_composto,               \n";
    $stSQL .= "                 loc.cod_localizacao                                  \n";
    $stSQL .= "             FROM                                                     \n";
    $stSQL .= "                 imobiliario.localizacao loc                          \n";
    $stSQL .= "             LEFT JOIN (                                              \n";
    $stSQL .= "                 SELECT                                               \n";
    $stSQL .= "                       BAT.*                                          \n";
    $stSQL .= "                 FROM                                                 \n";
    $stSQL .= "                       imobiliario.baixa_localizacao AS BAT,          \n";
    $stSQL .= "                 (                                                    \n";
    $stSQL .= "                       SELECT                                         \n";
    $stSQL .= "                             MAX (TIMESTAMP) AS TIMESTAMP,            \n";
    $stSQL .= "                             cod_localizacao                          \n";
    $stSQL .= "                       FROM                                           \n";
    $stSQL .= "                             imobiliario.baixa_localizacao            \n";
    $stSQL .= "                       GROUP BY                                       \n";
    $stSQL .= "                             cod_localizacao                          \n";
    $stSQL .= "                ) AS BT                                               \n";
    $stSQL .= "                WHERE                                                 \n";
    $stSQL .= "                       BAT.cod_localizacao = BT.cod_localizacao AND   \n";
    $stSQL .= "                       BAT.timestamp = BT.timestamp                   \n";
    $stSQL .= "             ) bl                                                     \n";
    $stSQL .= "             ON                                                       \n";
    $stSQL .= "                bl.cod_localizacao = loc.cod_localizacao              \n";

    $stSQL .= "                 WHERE                                                \n";
    $stSQL .= "                    ((bl.dt_inicio IS NULL) OR (bl.dt_inicio IS NOT NULL AND bl.dt_termino IS NOT NULL) AND bl.cod_localizacao = loc.cod_localizacao)                              \n";

    $stSQL .= "                                                                      \n";
    $stSQL .= "         ) AS LOC                                                     \n";
    $stSQL .= "      WHERE                                                           \n";
    $stSQL .= "         ((bl.dt_inicio IS NULL) OR (bl.dt_inicio IS NOT NULL AND bl.dt_termino IS NOT NULL) AND bl.cod_lote = i.cod_lote) AND                                                    \n";
    $stSQL .= "          LL.cod_lote         = I.cod_lote AND                        \n";
    $stSQL .= "          LOC.cod_localizacao = LL.cod_localizacao                    \n";
    $stSQL .= " ) AS IMOVEL                                                          \n";

    return $stSQL;
}

function recuperaImoveisAtivosLote(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaImoveisAtivosLote().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

//funcao que lista imoveis ativos de acordo com o numero do lote
function montaRecuperaListaImoveisAtivosLote()
{
    $stSql  = "SELECT\n";
    $stSql .= "    IL.inscricao_municipal,\n";
    $stSql .= "    bi.dt_inicio,\n";
    $stSql .= "    bi.dt_termino\n";
    $stSql .= "FROM\n";
    $stSql .= "    imobiliario.imovel_lote AS IL\n";
    $stSql .= "LEFT JOIN (                                                  \n";
    $stSql .= "    SELECT                                                   \n";
    $stSql .= "        BAL.*                                                \n";
    $stSql .= "    FROM                                                     \n";
    $stSql .= "        imobiliario.baixa_imovel AS BAL,                     \n";
    $stSql .= "        (                                                    \n";
    $stSql .= "        SELECT                                               \n";
    $stSql .= "            MAX (TIMESTAMP) AS TIMESTAMP,                    \n";
    $stSql .= "            inscricao_municipal                              \n";
    $stSql .= "        FROM                                                 \n";
    $stSql .= "            imobiliario.baixa_imovel                         \n";
    $stSql .= "        GROUP BY                                             \n";
    $stSql .= "            inscricao_municipal                              \n";
    $stSql .= "        ) AS BT                                              \n";
    $stSql .= "    WHERE                                                    \n";
    $stSql .= "        BAL.inscricao_municipal = BT.inscricao_municipal AND\n";
    $stSql .= "        BAL.timestamp = BT.timestamp                     \n";
    $stSql .= ") bi                                                     \n";
    $stSql .= "ON                                                       \n";
    $stSql .= "    IL.inscricao_municipal = bi.inscricao_municipal      \n";
    $stSql .= "WHERE                                                    \n";
    $stSql .= "    ((bi.dt_inicio IS NULL) OR (bi.dt_inicio IS NOT NULL AND bi.dt_termino IS NOT NULL) AND bi.inscricao_municipal=IL.inscricao_municipal)                      \n";

    return $stSql;
}

function recuperaImoveisAtivosCgm(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaImoveisAtivosCgm().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaImoveisAtivosCgm()
{
    $stSql  = "SELECT                                                      \n";
    $stSql .= "    I.INSCRICAO_MUNICIPAL,                                  \n";
    $stSql .= "    IP.NUMCGM                                               \n";
    $stSql .= "FROM                                                        \n";
    $stSql .= "    IMOBILIARIO.IMOVEL AS I                                 \n";
    $stSql .= "LEFT JOIN (                                                 \n";
    $stSql .= "    SELECT                                                  \n";
    $stSql .= "        BAL.*                                               \n";
    $stSql .= "    FROM                                                    \n";
    $stSql .= "        imobiliario.baixa_imovel AS BAL,                    \n";
    $stSql .= "        (                                                   \n";
    $stSql .= "        SELECT                                              \n";
    $stSql .= "            MAX (TIMESTAMP) AS TIMESTAMP,                   \n";
    $stSql .= "            inscricao_municipal                             \n";
    $stSql .= "        FROM                                                \n";
    $stSql .= "            imobiliario.baixa_imovel                        \n";
    $stSql .= "        GROUP BY                                            \n";
    $stSql .= "            inscricao_municipal                             \n";
    $stSql .= "        ) AS BT                                             \n";
    $stSql .= "    WHERE                                                   \n";
    $stSql .= "        BAL.inscricao_municipal = BT.inscricao_municipal AND\n";
    $stSql .= "        BAL.timestamp = BT.timestamp                        \n";
    $stSql .= ") bi                                                        \n";
    $stSql .= "ON                                                          \n";
    $stSql .= "    I.inscricao_municipal = bi.inscricao_municipal          \n";
    $stSql .= " LEFT JOIN imobiliario.proprietario AS IP                   \n";
    $stSql .= " ON                                                         \n";
    $stSql .= " IP.inscricao_municipal = I.inscricao_municipal             \n";
    $stSql .= "WHERE                                                        \n";
    $stSql .= "    ((bi.dt_inicio IS NULL) OR (bi.dt_inicio IS NOT NULL AND bi.dt_termino IS NOT NULL) AND bi.inscricao_municipal=I.inscricao_municipal)                      \n";

    return $stSql;
}

function recuperaImoveisAtivos(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaImoveisAtivos().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaImoveisAtivos()
{
    $stSql  = "SELECT                                                      \n";
    $stSql .= "    I.INSCRICAO_MUNICIPAL                                   \n";
    $stSql .= "FROM                                                        \n";
    $stSql .= "    IMOBILIARIO.IMOVEL AS I                                 \n";
    $stSql .= "LEFT JOIN (                                                 \n";
    $stSql .= "    SELECT                                                  \n";
    $stSql .= "        BAL.*                                               \n";
    $stSql .= "    FROM                                                    \n";
    $stSql .= "        imobiliario.baixa_imovel AS BAL,                    \n";
    $stSql .= "        (                                                   \n";
    $stSql .= "        SELECT                                              \n";
    $stSql .= "            MAX (TIMESTAMP) AS TIMESTAMP,                   \n";
    $stSql .= "            inscricao_municipal                             \n";
    $stSql .= "        FROM                                                \n";
    $stSql .= "            imobiliario.baixa_imovel                        \n";
    $stSql .= "        GROUP BY                                            \n";
    $stSql .= "            inscricao_municipal                             \n";
    $stSql .= "        ) AS BT                                             \n";
    $stSql .= "    WHERE                                                   \n";
    $stSql .= "        BAL.inscricao_municipal = BT.inscricao_municipal AND\n";
    $stSql .= "        BAL.timestamp = BT.timestamp                        \n";
    $stSql .= ") bi                                                        \n";
    $stSql .= "ON                                                          \n";
    $stSql .= "    I.inscricao_municipal = bi.inscricao_municipal          \n";
    $stSql .= "WHERE                                                        \n";
    $stSql .= "    ((bi.dt_inicio IS NULL) OR (bi.dt_inicio IS NOT NULL AND bi.dt_termino IS NOT NULL) AND bi.inscricao_municipal=I.inscricao_municipal)                      \n";

    return $stSql;
}

function recuperaRelacionamentoImovelBaixado(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

    $stSql = $this->montaRecuperaRelacionamentoImovelBaixado().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoImovelBaixado()
{
    $stSQL  = " SELECT                                                               \n";
    $stSQL .= "      *                                                               \n";
    $stSQL .= " FROM                                                                 \n";
    $stSQL .= "      (                                                               \n";
    $stSQL .= "      SELECT                                                          \n";
    $stSQL .= "          I.inscricao_municipal,                                      \n";
    $stSQL .= "          bi.timestamp,                                               \n";
    $stSQL .= "          bi.dt_inicio,                                               \n";
    $stSQL .= "          bi.justificativa,                                           \n";
    $stSQL .= "          iil.cod_lote,                                               \n";
    $stSQL .= "          I.cod_sublote,                                              \n";
    $stSQL .= "          LL.cod_localizacao,                                         \n";
    $stSQL .= "          LL.valor,                                                   \n";
    $stSQL .= "          LOC.valor_composto,                                         \n";
    $stSQL .= "          II.creci,                                                   \n";
    $stSQL .= "          CASE                                                        \n";
    $stSQL .= "          WHEN                                                        \n";
    $stSQL .= "              CGMC.nom_cgm IS NOT NULL                                \n";
    $stSQL .= "          THEN                                                        \n";
    $stSQL .= "              CGMC.nom_cgm                                            \n";
    $stSQL .= "          WHEN                                                        \n";
    $stSQL .= "              CGMI.nom_cgm IS NOT NULL                                \n";
    $stSQL .= "          THEN                                                        \n";
    $stSQL .= "              CGMI.nom_cgm                                            \n";
    $stSQL .= "          END AS NOME_CGM,                                            \n";
    $stSQL .= "          CASE                                                        \n";
    $stSQL .= "          WHEN                                                        \n";
    $stSQL .= "              LR.cod_lote IS NOT NULL                                 \n";
    $stSQL .= "          THEN                                                        \n";
    $stSQL .= "              'rural'                                                 \n";
    $stSQL .= "          WHEN                                                        \n";
    $stSQL .= "              LU.cod_lote IS NOT NULL                                 \n";
    $stSQL .= "          THEN                                                        \n";
    $stSQL .= "              'urbano'                                                \n";
    $stSQL .= "          END AS TIPO_LOTE                                            \n";
    $stSQL .= "      FROM                                                            \n";
    $stSQL .= "          imobiliario.imovel AS I                                        \n";
    $stSQL .= "          INNER JOIN (                                                   \n";
    $stSQL .= "                SELECT                                                   \n";
    $stSQL .= "                    BAL.*                                                \n";
    $stSQL .= "                FROM                                                     \n";
    $stSQL .= "                    imobiliario.baixa_imovel AS BAL,                     \n";
    $stSQL .= "                    (                                                    \n";
    $stSQL .= "                    SELECT                                               \n";
    $stSQL .= "                        MAX (TIMESTAMP) AS TIMESTAMP,                    \n";
    $stSQL .= "                        inscricao_municipal                              \n";
    $stSQL .= "                    FROM                                                 \n";
    $stSQL .= "                        imobiliario.baixa_imovel                         \n";
    $stSQL .= "                    GROUP BY                                             \n";
    $stSQL .= "                        inscricao_municipal                              \n";
    $stSQL .= "                    ) AS BT                                              \n";
    $stSQL .= "                WHERE                                                    \n";
    $stSQL .= "                    BAL.inscricao_municipal = BT.inscricao_municipal AND \n";
    $stSQL .= "                    BAL.timestamp = BT.timestamp                         \n";
    $stSQL .= "          ) bi                                                           \n";
    $stSQL .= "          ON                                                             \n";
    $stSQL .= "               i.inscricao_municipal = bi.inscricao_municipal AND        \n";
    $stSQL .= "               bi.dt_termino IS NULL                                     \n";
    $stSQL .= "          JOIN (                                                         \n";
    $stSQL .= "                SELECT                                                   \n";
    $stSQL .= "                    iil.inscricao_municipal,                             \n";
    $stSQL .= "                    iil.timestamp,                                       \n";
    $stSQL .= "                    iil.cod_lote                                         \n";
    $stSQL .= "                FROM                                                     \n";
    $stSQL .= "                    imobiliario.imovel_lote iil,                         \n";
    $stSQL .= "                    (                                                    \n";
    $stSQL .= "                        SELECT                                           \n";
    $stSQL .= "                            max(imovel_lote.timestamp) AS timestamp,     \n";
    $stSQL .= "                            imovel_lote.inscricao_municipal              \n";
    $stSQL .= "                        FROM                                             \n";
    $stSQL .= "                            imobiliario.imovel_lote                      \n";
    $stSQL .= "                        GROUP BY                                         \n";
    $stSQL .= "                            imovel_lote.inscricao_municipal              \n";
    $stSQL .= "                    ) il                                                 \n";
    $stSQL .= "                WHERE                                                    \n";
    $stSQL .= "                    iil.inscricao_municipal = il.inscricao_municipal AND iil.timestamp = il.timestamp    \n";
    $stSQL .= "           ) iil                                                         \n";
    $stSQL .= "           ON                                                            \n";
    $stSQL .= "                i.inscricao_municipal = iil.inscricao_municipal          \n";

    $stSQL .= "          LEFT JOIN imobiliario.imovel_imobiliaria  AS II  ON            \n";
    $stSQL .= "              I.inscricao_municipal = II.inscricao_municipal             \n";

    $stSQL .= "          LEFT JOIN imobiliario.corretor AS COR ON                       \n";
    $stSQL .= "              II.creci = COR.creci                                       \n";

    $stSQL .= "          LEFT JOIN imobiliario.imobiliaria as IMO ON                    \n";
    $stSQL .= "              II.creci = IMO.creci                                       \n";

    $stSQL .= "          LEFT JOIN sw_cgm AS CGMC ON                                    \n";
    $stSQL .= "              COR.numcgm = CGMC.numcgm                                   \n";

    $stSQL .= "          LEFT JOIN sw_cgm AS CGMI ON                                    \n";
    $stSQL .= "              IMO.numcgm = CGMI.numcgm                                   \n";

    $stSQL .= "          LEFT JOIN (                                                    \n";
    $stSQL .= "            SELECT                                                       \n";
    $stSQL .= "                BAL.*                                                    \n";
    $stSQL .= "            FROM                                                         \n";
    $stSQL .= "                imobiliario.baixa_lote AS BAL,                           \n";
    $stSQL .= "                (                                                        \n";
    $stSQL .= "                SELECT                                                   \n";
    $stSQL .= "                    MAX (TIMESTAMP) AS TIMESTAMP,                        \n";
    $stSQL .= "                    cod_lote                                             \n";
    $stSQL .= "                FROM                                                     \n";
    $stSQL .= "                    imobiliario.baixa_lote                               \n";
    $stSQL .= "                GROUP BY                                                 \n";
    $stSQL .= "                    cod_lote                                             \n";
    $stSQL .= "                ) AS BT                                                  \n";
    $stSQL .= "            WHERE                                                        \n";
    $stSQL .= "                BAL.cod_lote = BT.cod_lote AND                           \n";
    $stSQL .= "                BAL.timestamp = BT.timestamp                             \n";
    $stSQL .= "          ) bl                                                           \n";
    $stSQL .= "          ON                                                             \n";
    $stSQL .= "              iil.cod_lote = bl.cod_lote,                                \n";

    $stSQL .= "         imobiliario.lote_localizacao AS LL                              \n";

    $stSQL .= "          LEFT JOIN imobiliario.lote_rural AS LR ON                      \n";
    $stSQL .= "              LR.cod_lote = LL.cod_lote                                  \n";

    $stSQL .= "          LEFT JOIN imobiliario.lote_urbano AS LU ON                     \n";
    $stSQL .= "              LU.cod_lote = LL.cod_lote,                                 \n";

    $stSQL .= "         (                                                               \n";
    $stSQL .= "             SELECT                                                      \n";
    $stSQL .= "                 loc.codigo_composto AS valor_composto,                  \n";
    $stSQL .= "                 loc.cod_localizacao                                  \n";
    $stSQL .= "             FROM                                                     \n";
    $stSQL .= "                 imobiliario.localizacao loc                          \n";

    $stSQL .= "                 LEFT JOIN (                                          \n";
    $stSQL .= "                    SELECT                                             \n";
    $stSQL .= "                        BAL.*                                          \n";
    $stSQL .= "                    FROM                                               \n";
    $stSQL .= "                        imobiliario.baixa_localizacao AS BAL,          \n";
    $stSQL .= "                        (                                              \n";
    $stSQL .= "                        SELECT                                         \n";
    $stSQL .= "                            MAX (TIMESTAMP) AS TIMESTAMP,              \n";
    $stSQL .= "                            cod_localizacao                            \n";
    $stSQL .= "                        FROM                                           \n";
    $stSQL .= "                            imobiliario.baixa_localizacao              \n";
    $stSQL .= "                        GROUP BY                                       \n";
    $stSQL .= "                            cod_localizacao                            \n";
    $stSQL .= "                        ) AS BLL                                        \n";
    $stSQL .= "                    WHERE                                              \n";
    $stSQL .= "                        BAL.cod_localizacao = BLL.cod_localizacao AND   \n";
    $stSQL .= "                        BAL.timestamp = BLL.timestamp                   \n";
    $stSQL .= "                 ) bl                                                \n";
    $stSQL .= "                 ON                                                    \n";
    $stSQL .= "                    loc.cod_localizacao = bl.cod_localizacao\n";

    $stSQL .= "              WHERE                                                \n";
    $stSQL .= "                ((bl.dt_inicio IS NULL) OR (bl.dt_inicio IS NOT NULL AND bl.dt_termino IS NOT NULL) AND bl.cod_localizacao = loc.cod_localizacao)\n";
    $stSQL .= "         ) AS LOC                                                     \n";
    $stSQL .= "      WHERE                                                           \n";
    $stSQL .= "          ((bl.dt_inicio IS NULL) OR (bl.dt_inicio IS NOT NULL AND bl.dt_termino IS NOT NULL) AND bl.cod_lote=iil.cod_lote) AND\n";
    $stSQL .= "          LL.cod_lote         = iil.cod_lote AND                        \n";
    $stSQL .= "          LOC.cod_localizacao = LL.cod_localizacao                    \n";
    $stSQL .= " ) AS IMOVEL                                                          \n";

    return $stSQL;
}

function recuperaRelatorioCadastroImobiliario(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelatorioCadastroImobiliario().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    #$this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioCadastroImobiliario()
{
    $stSQL  = "SELECT \n";
    $stSQL .= "    I.inscricao_municipal, \n";
    $stSQL .= "    I.numero, \n";
    $stSQL .= "    I.complemento, \n";
    $stSQL .= "    I.dt_cadastro, \n";
    $stSQL .= "    II.creci, \n";
    $stSQL .= "    select split_part ( imobiliario.fn_busca_endereco_imovel( I.inscricao_municipal ), '§', 1)||' '||split_part ( imobiliario.fn_busca_endereco_imovel( I.inscricao_municipal ), '§', 3)||', '||split_part ( imobiliario.fn_busca_endereco_imovel( I.inscricao_municipal ), '§', 4) as endereco, \n";
    $stSQL .= "    ICO.cod_condominio, \n";
    $stSQL .= "    C.numcgm, \n";
    $stSQL .= "    C.nom_cgm||' - '||IP.cota as proprietario_cota, \n";
    $stSQL .= "    COALESCE (LR.cod_lote, L.cod_lote) AS cod_lote, \n";
    $stSQL .= "    CASE \n";
    $stSQL .= "        WHEN LR.cod_lote IS NOT NULL THEN 'Rural' \n";
    $stSQL .= "        ELSE 'Urbano' \n";
    $stSQL .= "    END AS tipo_lote, \n";
    $stSQL .= "    B.nom_bairro, \n";
    $stSQL .= "    LL.valor as valor_lote, \n";
    $stSQL .= "    LOC.cod_localizacao, \n";
    $stSQL .= "    LOC.codigo_composto||' '||LOC.nom_localizacao as localizacao, \n";
    $stSQL .= "    TLO.nom_tipo||' '||NLO.nom_logradouro as logradouro, \n";
    $stSQL .= "    IL.timestamp, \n";
    $stSQL .= "    I.cep, \n";
    $stSQL .= "    CASE \n";
    $stSQL .= "        WHEN ((BI.inscricao_municipal IS NULL) or (BI.dt_termino IS NOT NULL)) THEN 'Ativo' \n";
    $stSQL .= "        WHEN BI.inscricao_municipal IS NOT NULL THEN 'Baixado' \n";
    $stSQL .= "    END AS situacao, \n";
    $stSQL .= "    I.oid as oid_temp \n";
    $stSQL .= "FROM \n";
    $stSQL .= "    imobiliario.imovel AS I \n";

    $stSQL .= "LEFT JOIN  \n";
    $stSQL .= "    imobiliario.imovel_condominio ICO \n";
    $stSQL .= "ON \n";
    $stSQL .= "    ICO.inscricao_municipal = I.inscricao_municipal \n";

    $stSQL .= "LEFT JOIN \n";
    $stSQL .= "    imobiliario.imovel_imobiliaria II \n";
    $stSQL .= "ON \n";
    $stSQL .= "    II.inscricao_municipal = I.inscricao_municipal \n";

    $stSQL .= "LEFT JOIN ( \n";
    $stSQL .= "    SELECT \n";
    $stSQL .= "        BAI.* \n";
    $stSQL .= "    FROM \n";
    $stSQL .= "        imobiliario.baixa_imovel AS BAI, \n";
    $stSQL .= "        ( \n";
    $stSQL .= "        SELECT \n";
    $stSQL .= "            MAX (TIMESTAMP) AS TIMESTAMP, \n";
    $stSQL .= "            inscricao_municipal \n";
    $stSQL .= "        FROM \n";
    $stSQL .= "            imobiliario.baixa_imovel \n";
    $stSQL .= "        GROUP BY \n";
    $stSQL .= "            inscricao_municipal \n";
    $stSQL .= "        ) AS BI \n";
    $stSQL .= "    WHERE \n";
    $stSQL .= "        BAI.inscricao_municipal = BI.inscricao_municipal AND \n";
    $stSQL .= "        BAI.timestamp = BI.timestamp  \n";
    $stSQL .= ") BI \n";
    $stSQL .= "ON \n";
    $stSQL .= "    BI.inscricao_municipal = I.inscricao_municipal \n";

    $stSQL .= "INNER JOIN ( \n";
    $stSQL .= "    SELECT \n";
    $stSQL .= "        IIL.* \n";
    $stSQL .= "    FROM \n";
    $stSQL .= "        imobiliario.imovel_lote IIL, \n";
    $stSQL .= "        (SELECT \n";
    $stSQL .= "            MAX (TIMESTAMP) AS TIMESTAMP, \n";
    $stSQL .= "            INSCRICAO_MUNICIPAL \n";
    $stSQL .= "        FROM \n";
    $stSQL .= "            imobiliario.imovel_lote \n";
    $stSQL .= "        GROUP BY \n";
    $stSQL .= "            INSCRICAO_MUNICIPAL \n";
    $stSQL .= "        ) AS IL \n";
    $stSQL .= "    WHERE \n";
    $stSQL .= "            IIL.INSCRICAO_MUNICIPAL = IL.INSCRICAO_MUNICIPAL \n";
    $stSQL .= "        AND IIL.TIMESTAMP = IL.TIMESTAMP \n";
    $stSQL .= ") AS IL  \n";
    $stSQL .= "ON \n";
    $stSQL .= "    I.inscricao_municipal = IL.inscricao_municipal \n";

    $stSQL .= "INNER JOIN \n";
    $stSQL .= "    (  select ip.*
                         from imobiliario.proprietario as ip
                            , ( select max(numcgm) as numcgm
                                     , inscricao_municipal
                                  from imobiliario.proprietario
                              group by inscricao_municipal
                              ) as maxip
                        where ip.inscricao_municipal = maxip.inscricao_municipal
                          and ip.numcgm = maxip.numcgm
                    ) as IP	\n";
    $stSQL .= "ON \n";
    $stSQL .= "    I.inscricao_municipal  = IP.inscricao_municipal \n";

    $stSQL .= "INNER JOIN \n";
    $stSQL .= "    sw_cgm                           AS C \n";
    $stSQL .= "ON \n";
    $stSQL .= "    IP.numcgm              = C.numcgm \n";

    $stSQL .= "LEFT JOIN \n";
    $stSQL .= "    imobiliario.lote_rural          AS LR \n";
    $stSQL .= "ON \n";
    $stSQL .= "    LR.cod_lote = IL.cod_lote \n";

    $stSQL .= "LEFT JOIN \n";
    $stSQL .= "    imobiliario.lote_urbano          AS L \n";
    $stSQL .= "ON \n";
    $stSQL .= "    IL.cod_lote            = L.cod_lote  \n";

    $stSQL .= "INNER JOIN \n";
    $stSQL .= "    imobiliario.lote_localizacao     AS LL \n";
    $stSQL .= "ON \n";
    $stSQL .= "    LL.cod_lote            = IL.cod_lote  \n";

    $stSQL .= "INNER JOIN \n";
    $stSQL .= "    imobiliario.localizacao          AS LOC \n";
    $stSQL .= "ON \n";
    $stSQL .= "    LL.cod_localizacao     = LOC.cod_localizacao \n";

    $stSQL .= "INNER JOIN \n";
    $stSQL .= "    imobiliario.lote_bairro  LB \n";
    $stSQL .= "ON \n";
    $stSQL .= "    LB.cod_lote            = IL.cod_lote \n";

    $stSQL .= "INNER JOIN \n";
    $stSQL .= "    sw_bairro                        B \n";
    $stSQL .= "ON \n";
    $stSQL .= "    LB.cod_bairro          = B.cod_bairro           AND \n";
    $stSQL .= "    LB.cod_municipio       = B.cod_municipio        AND \n";
    $stSQL .= "    LB.cod_uf              = B.cod_uf \n";

    $stSQL .= "INNER JOIN \n";
    $stSQL .= "    imobiliario.imovel_confrontacao  IC \n";
    $stSQL .= "ON \n";
    $stSQL .= "    IC.inscricao_municipal = I.inscricao_municipal  AND \n";
    $stSQL .= "    IC.cod_lote            = IL.cod_lote \n";

    $stSQL .= "INNER JOIN \n";
    $stSQL .= "    imobiliario.confrontacao_trecho  CT \n";
    $stSQL .= "ON \n";
    $stSQL .= "    CT.cod_confrontacao    = IC.cod_confrontacao    AND \n";
    $stSQL .= "    CT.cod_lote            = IC.cod_lote            AND \n";
    $stSQL .= "    CT.principal           = true \n";

    $stSQL .= "INNER JOIN \n";
    $stSQL .= "    sw_logradouro                    LO \n";
    $stSQL .= "ON \n";
    $stSQL .= "    LO.cod_logradouro =     CT.cod_logradouro \n";

    $stSQL .= "INNER JOIN (  \n";
    $stSQL .= "    SELECT \n";
    $stSQL .= "        cod_logradouro, \n";
    $stSQL .= "        cod_tipo, \n";
    $stSQL .= "        nom_logradouro,  \n";
    $stSQL .= "        max(timestamp) \n";
    $stSQL .= "    FROM  \n";
    $stSQL .= "        sw_nome_logradouro \n";
    $stSQL .= "    GROUP BY  \n";
    $stSQL .= "        cod_logradouro, cod_tipo, nom_logradouro  \n";
    $stSQL .= ")NLO \n";
    $stSQL .= "ON \n";
    $stSQL .= "    NLO.cod_logradouro     = LO.cod_logradouro \n";

    $stSQL .= "INNER JOIN \n";
    $stSQL .= "    sw_tipo_logradouro               TLO \n";
    $stSQL .= "ON \n";
    $stSQL .= "    NLO.cod_tipo           = TLO.cod_tipo \n";

    return $stSQL;
}

function recuperaMaxInscricaoImobiliario(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaMaxInscricaoImobiliario();
    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMaxInscricaoImobiliario()
{
    $stSQL  = " SELECT \n";
    $stSQL .= "    MAX(inscricao_municipal) AS total \n";
    $stSQL .= " FROM \n";
    $stSQL .= "     imobiliario.imovel \n";

    return $stSQL;
}

function recuperaInscricaoImobiliario(&$rsRecordSet, $stFiltro, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaInscricaoImobiliario().$stFiltro;
    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaInscricaoImobiliario()
{
    $stSQL  = "     SELECT                                                   \n";
    $stSQL .= "         I.INSCRICAO_MUNICIPAL, \n";
    $stSQL .= "         I.NUMERO, \n";
    $stSQL .= "         I.COMPLEMENTO, \n";
    $stSQL .= "         ( \n";
    $stSQL .= "             SELECT \n";
    $stSQL .= "                 l[2] \n";
    $stSQL .= "             FROM \n";
    $stSQL .= "                 imobiliario.fn_consulta_logradouro( I.inscricao_municipal ) AS l \n";
    $stSQL .= "         ) \n";
    $stSQL .= "          AS logradouro \n";
    $stSQL .= "     FROM                                                     \n";
    $stSQL .= "         IMOBILIARIO.IMOVEL AS I                              \n";
    $stSQL .= "     LEFT JOIN (                                                   \n";
    $stSQL .= "         SELECT                                                    \n";
    $stSQL .= "             BAL.*                                                 \n";
    $stSQL .= "         FROM                                                      \n";
    $stSQL .= "             imobiliario.baixa_imovel AS BAL,                      \n";
    $stSQL .= "             (                                                     \n";
    $stSQL .= "             SELECT                                                \n";
    $stSQL .= "                 MAX (TIMESTAMP) AS TIMESTAMP,                     \n";
    $stSQL .= "                 inscricao_municipal                               \n";
    $stSQL .= "             FROM                                                  \n";
    $stSQL .= "                 imobiliario.baixa_imovel                          \n";
    $stSQL .= "             GROUP BY                                              \n";
    $stSQL .= "                 inscricao_municipal                               \n";
    $stSQL .= "             ) AS BT                                               \n";
    $stSQL .= "         WHERE                                                     \n";
    $stSQL .= "             BAL.inscricao_municipal = BT.inscricao_municipal AND \n";
    $stSQL .= "             BAL.timestamp = BT.timestamp                      \n";
    $stSQL .= "     ) bi                                                      \n";
    $stSQL .= "     ON                                                        \n";
    $stSQL .= "         I.inscricao_municipal = bi.inscricao_municipal \n";
    $stSQL .= "     WHERE                                                   \n";
    $stSQL .= "         ((bi.dt_inicio IS NULL) OR (bi.dt_inicio IS NOT NULL AND \n";
    $stSQL .= "         bi.dt_termino IS NOT NULL) AND bi.inscricao_municipal=I.inscricao_municipal) \n";

    return $stSQL;
}

function recuperaImovelPopup(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaImovelPopup().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaImovelPopup()
{
    $stSQL = "
        SELECT
            imovel.inscricao_municipal,
            localizacao.codigo_composto AS valor_composto,
            lote_localizacao.valor,
            sw_tipo_logradouro.nom_tipo||' '||sw_nome_logradouro.nom_logradouro as logradouro,
            imovel.numero,
            imovel.complemento

        FROM
            imobiliario.imovel

        LEFT JOIN
            (
                SELECT
                    tmp.*
                FROM
                    imobiliario.baixa_imovel AS tmp
                INNER JOIN
                    (
                        SELECT
                            max(timestamp) AS timestamp,
                            inscricao_municipal
                        FROM
                            imobiliario.baixa_imovel
                        GROUP BY
                            inscricao_municipal
                    )AS tmp2
                ON
                    tmp2.inscricao_municipal = tmp.inscricao_municipal
                    AND tmp2.timestamp = tmp.timestamp
            )AS baixa_imovel
        ON
            baixa_imovel.inscricao_municipal = imovel.inscricao_municipal

        INNER JOIN
            imobiliario.imovel_confrontacao
        ON
            imovel_confrontacao.inscricao_municipal = imovel.inscricao_municipal

        INNER JOIN
            imobiliario.confrontacao_trecho
        ON
            confrontacao_trecho.cod_confrontacao = imovel_confrontacao.cod_confrontacao
            AND confrontacao_trecho.cod_lote = imovel_confrontacao.cod_lote
            AND confrontacao_trecho.principal = true

        INNER JOIN
            sw_nome_logradouro
        ON
            sw_nome_logradouro.cod_logradouro = confrontacao_trecho.cod_logradouro

        INNER JOIN
            sw_tipo_logradouro
        ON
            sw_tipo_logradouro.cod_tipo = sw_nome_logradouro.cod_tipo

        INNER JOIN
            (
                SELECT
                    tmp.*
                FROM
                    imobiliario.imovel_lote AS tmp
                INNER JOIN
                    (
                        SELECT
                            inscricao_municipal,
                            max(timestamp) AS timestamp
                        FROM
                            imobiliario.imovel_lote
                        GROUP BY
                            inscricao_municipal
                    )AS tmp2
                ON
                    tmp2.inscricao_municipal = tmp.inscricao_municipal
                    AND tmp2.timestamp = tmp.timestamp
            )AS imovel_lote
        ON
            imovel_lote.inscricao_municipal = imovel.inscricao_municipal

        INNER JOIN
            imobiliario.lote_localizacao
        ON
            lote_localizacao.cod_lote = imovel_lote.cod_lote

        INNER JOIN
            imobiliario.localizacao
        ON
            localizacao.cod_localizacao = lote_localizacao.cod_localizacao

        LEFT JOIN
            (
                SELECT
                    tmp.*
                FROM
                    imobiliario.baixa_localizacao AS tmp
                INNER JOIN
                    (
                        SELECT
                            max(timestamp) AS timestamp,
                            cod_localizacao
                        FROM
                            imobiliario.baixa_localizacao
                        GROUP BY
                            cod_localizacao
                    )AS tmp2
                ON
                    tmp2.cod_localizacao = tmp.cod_localizacao
                    AND tmp2.timestamp = tmp.timestamp
            )AS baixa_localizacao
        ON
            baixa_localizacao.cod_localizacao = localizacao.cod_localizacao

        WHERE
            (baixa_imovel.dt_inicio IS NULL OR (baixa_imovel.dt_inicio IS NOT NULL AND baixa_imovel.dt_termino IS NOT NULL ))
            AND (baixa_localizacao.dt_inicio IS NULL OR (baixa_localizacao.dt_inicio IS NOT NULL AND baixa_localizacao.dt_termino IS NOT NULL ))
    ";

    return $stSQL;
}

} // FECHA CLASSE
