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
     * Classe de mapeamento para a tabela IMOBILIARIO.UNIDADE_AUTONOMA
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMUnidadeAutonoma.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.7  2006/09/18 09:12:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.UNIDADE_AUTONOMA
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
  * @author Desenvolvedor: Fábio Bertoldi Rodrigues

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMUnidadeAutonoma extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMUnidadeAutonoma()
{
    parent::Persistente();
    $this->setTabela('imobiliario.unidade_autonoma');

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_municipal,cod_tipo,cod_construcao');

    $this->AddCampo('inscricao_municipal','integer',true,'',true,true);
    $this->AddCampo('cod_tipo','integer',true,'',true,true);
    $this->AddCampo('cod_construcao','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                                             \n";
    $stSql  = "    UNI.*,                                                         \n";
    $stSql  = "    CASE                                                           \n";
    $stSql  = "        WHEN                                                       \n";
    $stSql  = "            CAST( UNI.COD_CONSTRUCAO_DEPENDENTE AS VARCHAR ) = 0   \n";
    $stSql  = "        THEN                                                       \n";
    $stSql  = "            'Autônoma'                                             \n";
    $stSql  = "        ELSE                                                       \n";
    $stSql  = "            'Dependente'                                           \n";
    $stSql  = "    END AS TIPO_UNIDADE                                            \n";
    $stSql  = "FROM                                                               \n";
    $stSql  = "    (SELECT                                                        \n";
    $stSql  = "            UA.inscricao_municipal,                                \n";
    $stSql  = "            UA.COD_TIPO,                                           \n";
    $stSql  = "            UA.COD_CONSTRUCAO,                                     \n";
    $stSql  = "            UA.TIMESTAMP,                                          \n";
    $stSql  = "            0 AS COD_CONSTRUCAO_DEPENDENTE,                        \n";
    $stSql  = "            AUA.AREA,                                              \n";
    $stSql  = "            TE.NOM_TIPO                                            \n";
    $stSql  = "        FROM                                                       \n";
    $stSql  = "            imobiliario.unidade_autonoma AS UA                         \n";
    $stSql  = "        INNER JOIN                                                 \n";
    $stSql  = "             ( SELECT                                              \n";
    $stSql  = "                AUA.*                                              \n";
    $stSql  = "             FROM                                                  \n";
    $stSql  = "                imobiliario.area_unidade_autonoma AS AUA,              \n";
    $stSql  = "                (SELECT                                            \n";
    $stSql  = "                    MAX (TIMESTAMP) AS TIMESTAMP,                  \n";
    $stSql  = "                    INSCRICAO_MUNICIPAL                            \n";
    $stSql  = "                 FROM                                              \n";
    $stSql  = "                    imobiliario.area_unidade_autonoma                  \n";
    $stSql  = "                 GROUP BY                                          \n";
    $stSql  = "                    INSCRICAO_MUNICIPAL) AS MAUA                   \n";
    $stSql  = "             WHERE                                                 \n";
    $stSql  = "                AUA.INSCRICAO_MUNICIPAL = MAUA.INSCRICAO_MUNICIPAL \n";
    $stSql  = "                AND AUA.TIMESTAMP = MAUA.TIMESTAMP) AS AUA         \n";
    $stSql  = "        ON                                                         \n";
    $stSql  = "           UA.INSCRICAO_MUNICIPAL = AUA.INSCRICAO_MUNICIPAL        \n";
    $stSql  = "        LEFT JOIN                                                  \n";
    $stSql  = "            imobiliario.tipo_edificacao AS TE                          \n";
    $stSql  = "        ON                                                         \n";
    $stSql  = "            UA.COD_TIPO = TE.COD_TIPO                              \n";
    $stSql  = "    UNION                                                          \n";
    $stSql  = "        SELECT                                                     \n";
    $stSql  = "            UD.INSCRICAO_MUNICIPAL,                                \n";
    $stSql  = "            UD.COD_TIPO,                                           \n";
    $stSql  = "            UD.COD_CONSTRUCAO,                                     \n";
    $stSql  = "            UD.TIMESTAMP,                                          \n";
    $stSql  = "            UD.COD_CONSTRUCAO_DEPENDENTE,                          \n";
    $stSql  = "            AUD.AREA,                                              \n";
    $stSql  = "            TE.NOM_TIPO                                            \n";
    $stSql  = "        FROM                                                       \n";
    $stSql  = "            imobiliario.unidade_dependente AS UD                       \n";
    $stSql  = "        INNER JOIN                                                 \n";
    $stSql  = "             ( SELECT                                              \n";
    $stSql  = "                AUD.*                                              \n";
    $stSql  = "             FROM                                                  \n";
    $stSql  = "                imobiliario.area_unidade_dependente AS AUD,            \n";
    $stSql  = "                (SELECT                                            \n";
    $stSql  = "                    MAX (TIMESTAMP) AS TIMESTAMP,                  \n";
    $stSql  = "                    INSCRICAO_MUNICIPAL                            \n";
    $stSql  = "                 FROM                                              \n";
    $stSql  = "                    imobiliario.area_unidade_dependente                \n";
    $stSql  = "                 GROUP BY                                          \n";
    $stSql  = "                    INSCRICAO_MUNICIPAL) AS MAUD                   \n";
    $stSql  = "             WHERE                                                 \n";
    $stSql  = "                AUD.INSCRICAO_MUNICIPAL = MAUD.INSCRICAO_MUNICIPAL \n";
    $stSql  = "                AND AUD.TIMESTAMP = MAUD.TIMESTAMP) AS AUD         \n";
    $stSql  = "        ON                                                         \n";
    $stSql  = "           UD.INSCRICAO_MUNICIPAL = AUD.INSCRICAO_MUNICIPAL        \n";
    $stSql  = "        LEFT JOIN                                                  \n";
    $stSql  = "            imobiliario.tipo_edificacao AS TE                          \n";
    $stSql  = "        ON                                                         \n";
    $stSql  = "            UD.COD_TIPO = TE.COD_TIPO                              \n";
    $stSql  = "    ) AS UNI                                                       \n";

    return $stSql;
}

function recuperaVerificaUnidadeAutonoma(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaVerificaUnidadeAutonoma().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaVerificaUnidadeAutonoma()
{
    $stSQL .= " SELECT                                                          \n";
    $stSQL .= "     UA.*                                                        \n";
    $stSQL .= " FROM                                                            \n";
    $stSQL .= "     imobiliario.unidade_autonoma UA                             \n";
    $stSQL .= " LEFT JOIN (                                                     \n";
    $stSQL .= "    SELECT                                                       \n";
    $stSQL .= "        BAL.*                                                    \n";
    $stSQL .= "    FROM                                                         \n";
    $stSQL .= "        imobiliario.baixa_unidade_autonoma AS BAL,               \n";
    $stSQL .= "        (                                                        \n";
    $stSQL .= "        SELECT                                                   \n";
    $stSQL .= "            MAX (TIMESTAMP) AS TIMESTAMP,                        \n";
    $stSQL .= "            inscricao_municipal,                                 \n";
    $stSQL .= "            cod_tipo,                                            \n";
    $stSQL .= "            cod_construcao                                       \n";
    $stSQL .= "        FROM                                                     \n";
    $stSQL .= "            imobiliario.baixa_unidade_autonoma                   \n";
    $stSQL .= "        GROUP BY                                                 \n";
    $stSQL .= "            inscricao_municipal,                                 \n";
    $stSQL .= "            cod_tipo,                                            \n";
    $stSQL .= "            cod_construcao                                       \n";
    $stSQL .= "        ) AS BT                                                  \n";
    $stSQL .= "    WHERE                                                        \n";
    $stSQL .= "        BAL.inscricao_municipal = BT.inscricao_municipal AND     \n";
    $stSQL .= "        BAL.cod_tipo = BT.cod_tipo AND                           \n";
    $stSQL .= "        BAL.cod_construcao = BT.cod_construcao AND               \n";
    $stSQL .= "        BAL.timestamp = BT.timestamp                             \n";
    $stSQL .= " ) bua                                                           \n";
    $stSQL .= " ON                                                              \n";
    $stSQL .= "    bua.inscricao_municipal = ua.inscricao_municipal AND         \n";
    $stSQL .= "    bua.cod_tipo= ua.cod_tipo AND                                \n";
    $stSQL .= "    bua.cod_construcao = ua.cod_construcao                       \n";
    $stSQL .= " WHERE                                                           \n";
    $stSQL .= "    ((bua.dt_inicio IS NULL) OR (bua.dt_inicio IS NOT NULL AND bua.dt_termino IS NOT NULL) AND bua.inscricao_municipal = ua.inscricao_municipal AND                        \n";
    $stSQL .= "    bua.cod_tipo= ua.cod_tipo AND                                \n";
    $stSQL .= "    bua.cod_construcao = ua.cod_construcao)                      \n";

    return $stSQL;

}

}
