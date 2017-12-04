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
     * Classe de mapeamento para a tabela IMOBILIARIO.TRECHO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMTrecho.class.php 63138 2015-07-29 14:06:41Z evandro $

     * Casos de uso: uc-05.01.06
*/

/*
$Log$
Revision 1.7  2007/07/09 21:34:33  cercato
alteracao para o cgm funcionar da mesma forma que no cadastro economico e utilizar as novas tabelas sw_cgm_logradouro e sw_cgm_logradouro_correspondencia.

Revision 1.6  2006/09/18 09:12:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
/**
  * Efetua conexão com a tabela  IMOBILIARIO.TRECHO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMTrecho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMTrecho()
{
    parent::Persistente();
    $this->setTabela('imobiliario.trecho');

    $this->setCampoCod('cod_trecho');
    $this->setComplementoChave('cod_logradouro');

    $this->AddCampo('cod_trecho','integer',true,'',true,false);
    $this->AddCampo('cod_logradouro','integer',true,'',true,true);
    $this->AddCampo('sequencia','integer',true,'',false,false);
    $this->AddCampo('extensao','numeric',true,'8,2',false,false);

}

function recuperaProximaSequencia(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaProximaSequencia().$stFiltro.$stOrdem;
    $stSql .= " GROUP BY cod_logradouro ";
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaProximaSequencia()
{
    $stSQL .= " SELECT                                \n";
    $stSQL .= "     MAX(sequencia) + 1  AS sequencia, \n";
    $stSQL .= "     cod_logradouro                    \n";
    $stSQL .= " FROM                                  \n";
    $stSQL .= "     imobiliario.trecho                    \n";

    return $stSQL;
}

function retornaSomaExtensao(&$rsRecordSet, $stFiltro='', $stOrder='',$boTransacao='')
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = ' SELECT sum(extensao)as extensao_total FROM imobiliario.trecho '.$stFiltro.$stOrder;
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
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
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSQL .= "    SELECT sw_uf.cod_uf                                                                    \n";
    $stSQL .= "         , sw_uf.nom_uf                                                                    \n";
    $stSQL .= "         , sw_uf.sigla_uf                                                                  \n";
    $stSQL .= "         , sw_municipio.cod_municipio                                                      \n";
    $stSQL .= "         , sw_municipio.nom_municipio                                                      \n";
    $stSQL .= "         , sw_municipio.cod_municipio||' - '||sw_municipio.nom_municipio as municipio_nome \n";
    $stSQL .= "         , sw_logradouro.cod_logradouro                                                    \n";
    $stSQL .= "         , sw_nome_logradouro.nom_logradouro                                               \n";
    $stSQL .= "         , sw_tipo_logradouro.cod_tipo                                                     \n";
    $stSQL .= "         , sw_tipo_logradouro.nom_tipo                                                     \n";
    $stSQL .= "         , sw_uf.cod_uf||' - '||sw_uf.nom_uf As uf_nome                                    \n";
    $stSQL .= "         , sw_tipo_logradouro.nom_tipo||' '||CASE                                          \n";
    $stSQL .= "                                            WHEN STRPOS(nom_logradouro,'\''') > 0 THEN                    \n";
    $stSQL .= "                                                REPLACE(substring(nom_logradouro from '.*\''.*'),'\','')  \n";
    $stSQL .= "                                            WHEN STRPOS(nom_logradouro,'\"') > 0 THEN                      \n";
    $stSQL .= "                                                REPLACE(substring(nom_logradouro from '.*\".*'),'\"','''') \n";
    $stSQL .= "                                            ELSE nom_logradouro                                                  \n";
    $stSQL .= "                                            END as tipo_nome                                              \n";
    $stSQL .= "         , sw_bairro.cod_bairro                                                            \n";
    $stSQL .= "         , sw_bairro.nom_bairro                                                            \n";
    $stSQL .= "         , trecho.sequencia                                                                \n";
    $stSQL .= "         , COALESCE(trecho.sequencia, 0 ) + 1 AS prox_sequencia                            \n";
    $stSQL .= "         , trecho.extensao                                                                 \n";
    $stSQL .= "         , substr(sw_cep_logradouro.cep,1,5)||'-'||substr(sw_cep_logradouro.cep,6,3) AS cep\n";
    $stSQL .= "      FROM sw_cep_logradouro                                                               \n";
    $stSQL .= "INNER JOIN sw_logradouro                                                                   \n";
    $stSQL .= "        ON sw_cep_logradouro.cod_logradouro = sw_logradouro.cod_logradouro                 \n";
    $stSQL .= "INNER JOIN sw_bairro_logradouro                                                            \n";
    $stSQL .= "        ON sw_logradouro.cod_logradouro = sw_bairro_logradouro.cod_logradouro              \n";
    $stSQL .= "INNER JOIN sw_bairro                                                                       \n";
    $stSQL .= "        ON sw_bairro_logradouro.cod_uf        = sw_bairro.cod_uf                           \n";
    $stSQL .= "       AND sw_bairro_logradouro.cod_municipio = sw_bairro.cod_municipio                    \n";
    $stSQL .= "       AND sw_bairro_logradouro.cod_bairro    = sw_bairro.cod_bairro                       \n";
    $stSQL .= "INNER JOIN sw_municipio                                                                    \n";
    $stSQL .= "        ON sw_bairro.cod_uf        = sw_municipio.cod_uf                                   \n";
    $stSQL .= "       AND sw_bairro.cod_municipio = sw_municipio.cod_municipio                            \n";
    $stSQL .= "INNER JOIN sw_uf                                                                           \n";
    $stSQL .= "        ON sw_municipio.cod_uf = sw_uf.cod_uf                                              \n";
    $stSQL .= "INNER JOIN ( SELECT MAX(timestamp) AS timestamp                                            \n";
    $stSQL .= "                  , cod_logradouro                                                         \n";
    $stSQL .= "               FROM sw_nome_logradouro                                                     \n";
    $stSQL .= "           GROUP BY cod_logradouro                                                         \n";
    $stSQL .= "           ) AS nome_logradouro                                                            \n";
    $stSQL .= "        ON sw_logradouro.cod_logradouro = nome_logradouro.cod_logradouro                   \n";
    $stSQL .= "INNER JOIN sw_nome_logradouro                                                              \n";
    $stSQL .= "        ON nome_logradouro.timestamp      = sw_nome_logradouro.timestamp                   \n";
    $stSQL .= "       AND nome_logradouro.cod_logradouro = sw_nome_logradouro.cod_logradouro              \n";
    $stSQL .= "INNER JOIN sw_tipo_logradouro                                                              \n";
    $stSQL .= "        ON sw_nome_logradouro.cod_tipo = sw_tipo_logradouro.cod_tipo                       \n";
    $stSQL .= " LEFT JOIN (     SELECT trecho.cod_trecho                                                  \n";
    $stSQL .= "                      , trecho.cod_logradouro                                              \n";
    $stSQL .= "                      , trecho.sequencia                                                   \n";
    $stSQL .= "                      , trecho.extensao                                                    \n";
    $stSQL .= "                   FROM (   SELECT MAX(sequencia) AS sequencia                             \n";
    $stSQL .= "                                 , cod_logradouro                                          \n";
    $stSQL .= "                              FROM imobiliario.trecho                                      \n";
    $stSQL .= "                          GROUP BY cod_logradouro ) AS imob_trecho                         \n";
    $stSQL .= "             INNER JOIN imobiliario.trecho                                                 \n";
    $stSQL .= "                     ON imob_trecho.sequencia      = trecho.sequencia                      \n";
    $stSQL .= "                    AND imob_trecho.cod_logradouro = trecho.cod_logradouro                 \n";
    $stSQL .= "           ) AS trecho                                                                     \n";
    $stSQL .= "        ON sw_logradouro.cod_logradouro = trecho.cod_logradouro                            \n";
    $stSQL .= "WHERE (1=1)                                                                                \n";

    return $stSQL;
}

function montaRecuperaRelacionamentoRelatorio()
{
    $stSQL .= " SELECT                                                     \n";
    $stSQL .= "     T.*,                                                   \n";
    $stSQL .= "     T.cod_logradouro as trecho,                            \n";
    $stSQL .= "     TL.nom_tipo||' '||NL.nom_logradouro as logradouro,     \n";
    $stSQL .= "     (select valor_m2_territorial                           \n";
    $stSQL .= "         from imobiliario.trecho_valor_m2                   \n";
    $stSQL .= "         where cod_logradouro = t.cod_logradouro            \n";
    $stSQL .= "            and cod_trecho = t.cod_trecho                   \n";
    $stSQL .= "         order by timestamp desc                            \n";
    $stSQL .= "         limit 1) as valor_m2_territorial,                  \n";
    $stSQL .= "     (select valor_m2_predial                               \n";
    $stSQL .= "         from imobiliario.trecho_valor_m2                   \n";
    $stSQL .= "         where cod_logradouro = t.cod_logradouro            \n";
    $stSQL .= "            and cod_trecho = t.cod_trecho                   \n";
    $stSQL .= "         order by timestamp desc                            \n";
    $stSQL .= "         limit 1) as valor_m2_predial,                      \n";
    $stSQL .= "     to_char((select dt_vigencia                            \n";
    $stSQL .= "         from imobiliario.trecho_valor_m2                   \n";
    $stSQL .= "         where cod_logradouro = t.cod_logradouro            \n";
    $stSQL .= "            and cod_trecho = t.cod_trecho                   \n";
    $stSQL .= "         order by timestamp desc                            \n";
    $stSQL .= "         limit 1), 'dd/mm/YYYY') as dt_vigencia             \n";
    $stSQL .= " FROM                                                       \n";
    $stSQL .= "    imobiliario.trecho      AS T,                           \n";
    $stSQL .= "    sw_nome_logradouro AS NL,                               \n";
    $stSQL .= "    sw_tipo_logradouro AS TL,                               \n";
    $stSQL .= "    sw_logradouro      AS L                                 \n";
    $stSQL .= " WHERE                                                      \n";
    $stSQL .= "     T.cod_logradouro = L.cod_logradouro AND                \n";
    $stSQL .= "     L.cod_logradouro = NL.cod_logradouro AND               \n";
    $stSQL .= "     NL.cod_tipo      = TL.cod_tipo                         \n";

    return $stSQL;
}

function recuperaTrechoBaixado(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaTrechoBaixado().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTrechoBaixado()
{
    $stSQL .= "SELECT                                                           \n";
    $stSQL .= "    t.cod_trecho,                                                \n";
    $stSQL .= "    t.cod_logradouro,                                            \n";
    $stSQL .= "    t.sequencia,                                                 \n";
    $stSQL .= "    t.extensao,                                                  \n";
    $stSQL .= "    to_char(BTT.dt_inicio,'dd/mm/yyyy') as dt_inicio,            \n";
    $stSQL .= "    BTT.timestamp,                                               \n";
    $stSQL .= "    BTT.justificativa,                                           \n";
    $stSQL .= "    TL.nom_tipo,                                                 \n";
    $stSQL .= "    NL.nom_logradouro,                                           \n";
    $stSQL .= "    TL.nom_tipo||' '||NL.nom_logradouro as tipo_nome,            \n";
    $stSQL .= "    L.cod_logradouro||'.'||T.sequencia AS codigo_sequencia       \n";
    $stSQL .= " FROM                                                            \n";
    $stSQL .= "    imobiliario.trecho AS t                                      \n";
    $stSQL .= " INNER JOIN (                                                    \n";
    $stSQL .= "    SELECT                                                       \n";
    $stSQL .= "        BAT.*                                                    \n";
    $stSQL .= "    FROM                                                         \n";
    $stSQL .= "        imobiliario.baixa_trecho AS BAT,                         \n";
    $stSQL .= "        (                                                        \n";
    $stSQL .= "        SELECT                                                   \n";
    $stSQL .= "            MAX (TIMESTAMP) AS TIMESTAMP,                        \n";
    $stSQL .= "            cod_logradouro,                                      \n";
    $stSQL .= "            cod_trecho                                           \n";
    $stSQL .= "        FROM                                                     \n";
    $stSQL .= "            imobiliario.baixa_trecho                             \n";
    $stSQL .= "        GROUP BY                                                 \n";
    $stSQL .= "            cod_logradouro,                                      \n";
    $stSQL .= "            cod_trecho                                           \n";
    $stSQL .= "        ) AS BT                                                  \n";
    $stSQL .= "    WHERE                                                        \n";
    $stSQL .= "        BAT.cod_trecho = BT.cod_trecho AND                       \n";
    $stSQL .= "        BAT.cod_logradouro = BT.cod_logradouro AND               \n";
    $stSQL .= "        BAT.timestamp = BT.timestamp                             \n";
    $stSQL .= " ) BTT                                                           \n";
    $stSQL .= " ON                                                              \n";
    $stSQL .= "   t.cod_trecho = BTT.cod_trecho AND                             \n";
    $stSQL .= "   t.cod_logradouro = BTT.cod_logradouro AND                     \n";
    $stSQL .= "   BTT.dt_termino IS NULL,                                       \n";
    $stSQL .= "   sw_logradouro AS L,                                           \n";
    $stSQL .= "   sw_tipo_logradouro AS TL,                                     \n";
    $stSQL .= "   sw_nome_logradouro AS NL,                                     \n";
    $stSQL .= "   ( SELECT                                                      \n";
    $stSQL .= "           MAX(timestamp) AS timestamp,                          \n";
    $stSQL .= "           cod_logradouro                                        \n";
    $stSQL .= "     FROM                                                        \n";
    $stSQL .= "           sw_nome_logradouro                                    \n";
    $stSQL .= "       GROUP BY cod_logradouro                                   \n";
    $stSQL .= "       ORDER BY cod_logradouro                                   \n";
    $stSQL .= "   ) AS MNL                                                      \n";
    $stSQL .= " WHERE                                                           \n";
    $stSQL .= "     t.cod_logradouro  = L.cod_logradouro AND                    \n";
    $stSQL .= "     L.cod_logradouro  = NL.cod_logradouro AND                   \n";
    $stSQL .= "     NL.cod_logradouro = MNL.cod_logradouro AND                  \n";
    $stSQL .= "     NL.timestamp      = MNL.timestamp AND                       \n";
    $stSQL .= "     NL.cod_tipo       = TL.cod_tipo                             \n";

    return $stSQL;
}
}
