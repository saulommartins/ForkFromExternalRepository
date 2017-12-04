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
     * Classe de mapeamento para a tabela IMOBILIARIO.CONDOMINIO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMCondominio.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.14
*/

/*
$Log$
Revision 1.6  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.CONDOMINIO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMCondominio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMCondominio()
{
    parent::Persistente();
    $this->setTabela('imobiliario.condominio');

    $this->setCampoCod('cod_condominio');
    $this->setComplementoChave('');

    $this->AddCampo('cod_condominio','integer',true,'',true,false);
    $this->AddCampo('cod_tipo','integer',true,'',false,true);
    $this->AddCampo('nom_condominio','varchar',true,'160',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                               \n";
    $stSql .= "     DISTINCT                                         \n";
    $stSql .= "     C.*,                                             \n";
    $stSql .= "     TC.nom_tipo,                                     \n";
    $stSql .= "     CC.numcgm,                                       \n";
    $stSql .= "     CAC.area_total_comum,                            \n";
    $stSql .= "     CAC.timestamp,                                   \n";
    $stSql .= "     CP.cod_processo,                                 \n";
    $stSql .= "     CP.ano_exercicio,                                \n";
    $stSql .= "     CGM.nom_cgm                                      \n";

//    $stSql .= "     LL.cod_lote,                                     \n";
//    $stSql .= "     LL.valor,                                        \n";
//    $stSql .= "     L.nom_localizacao,                               \n";
//    $stSql .= "     L.codigo_composto                                \n";

    $stSql .= " FROM                                                 \n";
    $stSql .= "     imobiliario.tipo_condominio         AS TC,           \n";
    $stSql .= "     imobiliario.condominio              AS C             \n";
    $stSql .= " LEFT OUTER JOIN                                      \n";
    $stSql .= "      imobiliario.condominio_cgm  AS CC                   \n";
    $stSql .= " ON                                                   \n";
    $stSql .= "      C.cod_condominio = CC.cod_condominio            \n";

/*    $stSql .= " LEFT OUTER JOIN                                      \n";
    $stSql .= "      imobiliario.lote_condominio           AS LC         \n";
    $stSql .= " ON                                                   \n";
    $stSql .= "      LC.cod_condominio = C.cod_condominio            \n";

    $stSql .= " LEFT OUTER JOIN                                      \n";
    $stSql .= "      imobiliario.lote_localizacao  AS LL                 \n";
    $stSql .= " ON                                                   \n";
    $stSql .= "      LL.cod_lote = LC.cod_lote                       \n";

    $stSql .= " LEFT OUTER JOIN                                      \n";
    $stSql .= "      imobiliario.localizacao  AS L                       \n";
    $stSql .= " ON                                                   \n";
    $stSql .= "      L.cod_localizacao = LL.cod_localizacao          \n";
*/
    $stSql .= " LEFT OUTER JOIN                                      \n";
    $stSql .= "      imobiliario.condominio_processo  AS CP              \n";
    $stSql .= " ON                                                   \n";
    $stSql .= "      C.cod_condominio = CP.cod_condominio            \n";
    $stSql .= " LEFT OUTER JOIN                                      \n";
    $stSql .= "      sw_cgm  AS CGM                                 \n";
    $stSql .= " ON                                                   \n";
    $stSql .= "      CC.numcgm = CGM.numcgm                          \n";
    $stSql .= "INNER JOIN                                            \n";
    $stSql .= "    ( SELECT                                          \n";
    $stSql .= "        CAC.*                                         \n";
    $stSql .= "    FROM                                              \n";
    $stSql .= "        imobiliario.condominio_area_comum AS CAC,     \n";
    $stSql .= "        ( SELECT                                      \n";
    $stSql .= "            MAX (TIMESTAMP) AS TIMESTAMP,             \n";
    $stSql .= "            COD_CONDOMINIO                            \n";
    $stSql .= "        FROM                                          \n";
    $stSql .= "            imobiliario.condominio_area_comum         \n";
    $stSql .= "        GROUP BY                                      \n";
    $stSql .= "            cod_condominio) AS MCAC                   \n";
    $stSql .= "    WHERE                                             \n";
    $stSql .= "        CAC.cod_condominio = MCAC.cod_condominio AND  \n";
    $stSql .= "        CAC.TIMESTAMP =  MCAC.TIMESTAMP ) AS CAC      \n";
    $stSql .= "ON                                                    \n";
    $stSql .= "    C.cod_condominio = CAC.cod_condominio             \n";
    $stSql .= " WHERE                                                \n";
    $stSql .= "     C.cod_tipo       = TC.cod_tipo                   \n";

    return $stSql;
}

function recuperaRelacionamentoLista(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoLista().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoLista()
{
    $stSql  = " SELECT                                               \n";
    $stSql .= "     C.*,                                             \n";
    $stSql .= "     TC.nom_tipo,                                     \n";
    $stSql .= "     CC.numcgm,                                       \n";
//    $stSql .= "     CAC.area_total_comum,                            \n";
//    $stSql .= "     CP.cod_processo,                                 \n";
//    $stSql .= "     CP.ano_exercicio,                                \n";
    $stSql .= "     CGM.nom_cgm                                      \n";
    $stSql .= " FROM                                                 \n";
    $stSql .= "     imobiliario.tipo_condominio         AS TC,           \n";
    $stSql .= "     imobiliario.condominio              AS C             \n";
    $stSql .= " LEFT OUTER JOIN                                      \n";
    $stSql .= "      imobiliario.condominio_cgm  AS CC                   \n";
    $stSql .= " ON                                                   \n";
    $stSql .= "      C.cod_condominio = CC.cod_condominio            \n";
/*    $stSql .= " LEFT OUTER JOIN                                      \n";
    $stSql .= "      imobiliario.condominio_processo  AS CP              \n";
    $stSql .= " ON                                                   \n";
    $stSql .= "      C.cod_condominio = CP.cod_condominio            \n";*/
    $stSql .= " LEFT OUTER JOIN                                      \n";
    $stSql .= "      sw_cgm  AS CGM                                 \n";
    $stSql .= " ON                                                   \n";
    $stSql .= "      CC.numcgm = CGM.numcgm                          \n";
/*    $stSql .= "INNER JOIN                                            \n";
    $stSql .= "    ( SELECT                                          \n";
    $stSql .= "        CAC.*                                         \n";
    $stSql .= "    FROM                                              \n";
    $stSql .= "        imobiliario.condominio_area_comum AS CAC,     \n";
    $stSql .= "        ( SELECT                                      \n";
    $stSql .= "            MAX (TIMESTAMP) AS TIMESTAMP,             \n";
    $stSql .= "            COD_CONDOMINIO                            \n";
    $stSql .= "        FROM                                          \n";
    $stSql .= "            imobiliario.condominio_area_comum         \n";
    $stSql .= "        GROUP BY                                      \n";
    $stSql .= "            cod_condominio) AS MCAC                   \n";
    $stSql .= "    WHERE                                             \n";
    $stSql .= "        CAC.cod_condominio = MCAC.cod_condominio AND  \n";
    $stSql .= "        CAC.TIMESTAMP =  MCAC.TIMESTAMP ) AS CAC      \n";
    $stSql .= "ON                                                    \n";
    $stSql .= "    C.cod_condominio = CAC.cod_condominio             \n";*/
    $stSql .= " WHERE                                                \n";
    $stSql .= "     C.cod_tipo       = TC.cod_tipo                   \n";

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
    $stSQL  = " SELECT                                                                                      \n";
    $stSQL .= "     cp.cod_condominio as cod_condominio,                                                    \n";
    $stSQL .= "     cp.cod_processo as cod_processo,                                                        \n";
    $stSQL .= "     cp.ano_exercicio as ano_exercicio,                                                      \n";
    $stSQL .= "     lpad(cp.cod_processo::varchar,5,'0') || '/' || cp.ano_exercicio as cod_processo_ano,    \n";
    $stSQL .= "     cp.timestamp as timestamp,                                                              \n";
    $stSQL .= "     to_char(cp.timestamp,'dd/mm/yyyy') as data,                                             \n";
    $stSQL .= "     to_char(cp.timestamp,'hh24:mi:ss') as hora,                                             \n";
    $stSQL .= "     cac.area_total_comum                                                                    \n";
    $stSQL .= " FROM                                                                                        \n";
    $stSQL .= "     imobiliario.condominio_processo AS cp                                                   \n";
    $stSQL .= "     LEFT JOIN imobiliario.condominio_area_comum AS cac ON                                   \n";
    $stSQL .= "             cp.cod_condominio = cac.cod_condominio                                          \n";
    $stSQL .= "         AND cp.timestamp      = cac.timestamp                                               \n";

    return $stSQL;
}

function recuperaCondominio(&$rsRecordSet, $stFiltro, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCondominio().$stFiltro;
    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCondominio()
{
    $stSQL = "
        SELECT
            condominio.nom_condominio,
            condominio.cod_condominio,
            tipo_condominio.cod_tipo,
            tipo_condominio.nom_tipo

        FROM
            imobiliario.condominio

        INNER JOIN
            imobiliario.tipo_condominio
        ON
            tipo_condominio.cod_tipo = condominio.cod_tipo ";

    return $stSQL;
}

function recuperaImoveisDoCondominio(&$rsRecordSet, $stFiltro, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaImoveisDoCondominio().$stFiltro;
    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaImoveisDoCondominio()
{
    $stSQL = "
        SELECT
            imovel_condominio.inscricao_municipal,
            (
                SELECT
                    proprietario.numcgm
                FROM
                    imobiliario.proprietario
                WHERE
                    proprietario.inscricao_municipal = imovel_condominio.inscricao_municipal
                LIMIT 1
            )AS numcgm_proprietario,
            (
                SELECT
                    (
                        SELECT
                            sw_cgm.nom_cgm
                        FROM
                            sw_cgm
                        WHERE
                            sw_cgm.numcgm = proprietario.numcgm
                    )
                FROM
                    imobiliario.proprietario
                WHERE
                    proprietario.inscricao_municipal = imovel_condominio.inscricao_municipal
                LIMIT 1
            )AS nomcgm_proprietario,
            arrecadacao.fn_consulta_endereco_todos( imovel_condominio.inscricao_municipal, 1, 1 ) AS logradouro,
            (
                SELECT cod_lote
                  FROM imobiliario.imovel_lote
            INNER JOIN ( SELECT MAX(timestamp) as timestamp
                                , inscricao_municipal
                            FROM imobiliario.imovel_lote
                        GROUP BY inscricao_municipal
                        ) AS tmp
                    ON tmp.inscricao_municipal = imovel_lote.inscricao_municipal
                   AND tmp.timestamp = imovel_lote.timestamp
                 WHERE imovel_lote.inscricao_municipal = imovel_condominio.inscricao_municipal
            )AS cod_lote

        FROM
            imobiliario.imovel_condominio
    ";

    return $stSQL;
}

}
