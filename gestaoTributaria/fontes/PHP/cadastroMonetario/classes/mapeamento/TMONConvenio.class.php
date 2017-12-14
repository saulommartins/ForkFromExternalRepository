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
  * Classe de Mapeamento para Convênio
  * Data de criação : 09/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TMONConvenio.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.05.04
**/

/*
$Log$
Revision 1.25  2007/08/06 18:59:41  cercato
Bug#9792#

Revision 1.24  2007/02/08 10:35:26  cercato
alteracoes para o credito trabalhar com conta corrente.

Revision 1.23  2007/02/07 15:56:19  cercato
alteracoes para o convenio trabalhar com numero de variacao.

Revision 1.22  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

class TMONConvenio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TMONConvenio()
{
    parent::Persistente();
    $this->setTabela('monetario.convenio');

    $this->setCampoCod('cod_convenio');
    $this->setComplementoChave('');

    $this->AddCampo('cod_convenio' ,'integer',true,''   ,true ,false);
    $this->AddCampo('num_convenio' ,'integer',true,''   ,false,false);
    $this->AddCampo('cod_tipo'     ,'integer',true,''   ,false,true );
    $this->AddCampo('taxa_bancaria','numeric',false,'8,2',false,false);
    $this->AddCampo('cedente',      'numeric',false,'20,0',false,false);
}

function recuperaTodos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaTodos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    //$this->debug(); exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTodos()
{
    $stSql  = "  select                             \r\n";
    $stSql .= "      c.cod_convenio,                \r\n";
    $stSql .= "      c.num_convenio,                \r\n";
    $stSql .= "      c.cod_tipo,                    \r\n";
    $stSql .= "      c.taxa_bancaria,               \r\n";
    $stSql .= "      c.cedente,                     \r\n";
    $stSql .= "      tc.*                   \r\n";
    $stSql .= "  from                               \r\n";
    $stSql .= "      monetario.convenio as c        \r\n";
    $stSql .= "  INNER JOIN                         \r\n";
    $stSql .= "      monetario.tipo_convenio as tc  \r\n";
    $stSql .= "  ON                                 \r\n";
    $stSql .= "      c.cod_tipo = tc.cod_tipo       \r\n";

    return $stSql;
}

function recuperaConvenioBanco(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

    $stSql = $this->montaRecuperaConvenioBanco().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    //$this->debug(); exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConvenioBanco()
{
    //-----------------------------------------------------
    /*
    $stSql  = " SELECT                                          \n";
    $stSql .= "        ag.cod_agencia,                          \n";
    $stSql .= "        ag.nom_agencia,                          \n";
    $stSql .= "        ag.num_agencia,                          \n";
    $stSql .= "        bc.num_banco,                            \n";
    $stSql .= "        bc.nom_banco,                            \n";
    $stSql .= "        tc.nom_tipo,                             \n";
    $stSql .= "        tc.cod_funcao,                           \n";
    $stSql .= "        tc.cod_biblioteca,                       \n";
    $stSql .= "        tc.cod_modulo,                           \n";
    $stSql .= "        ca.cod_carteira,                         \n";
    $stSql .= "        ca.variacao,                             \n";
    $stSql .= "        co.cod_tipo,                             \n";
    $stSql .= "        co.cod_convenio,                         \n";
    $stSql .= "        co.num_convenio,                         \n";
    $stSql .= "        co.cedente,                              \n";
    $stSql .= "        co.taxa_bancaria,                        \n";
    $stSql .= "        ccc.cod_banco                            \n";

    $stSql .= "    from                                         \n";
    $stSql .= "        monetario.agencia as ag                  \n";
    $stSql .= "    inner join                                   \n";

    $stSql .= "(
    SELECT
        max (cod_banco) as codigobanco,
        cod_agencia, cod_convenio, cod_banco
        FROM
        monetario.conta_corrente_convenio
        GROUP BY
        COD_agencia, cod_convenio, cod_banco
    ) AS ccc                                                    \n";

    $stSql .= " ON ccc.cod_agencia = ag.cod_agencia             \n";

    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.banco as bc                       \n";
    $stSql .= " ON ccc.cod_banco = bc.cod_banco                 \n";

    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.convenio as co                    \n";
    $stSql .= " ON ccc.cod_convenio = co.cod_convenio           \n";

    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.tipo_convenio as tc               \n";
    $stSql .= " ON tc.cod_tipo = co.cod_tipo                    \n";

    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.carteira as ca                    \n";
    $stSql .= " ON ca.cod_convenio = co.cod_convenio            \n";
    */

    $stSql  = " SELECT DISTINCT                                 \n";
    $stSql .= "     ccc.cod_agencia,                            \n";
    $stSql .= "     ccc.cod_banco,                              \n";
    $stSql .= "     ccc.cod_convenio,                           \n";
    $stSql .= "     ccc.cod_conta_corrente,                     \n";
    $stSql .= "     mcc.num_conta_corrente,                     \n";
    $stSql .= "     ban.cod_banco,                              \n";
    $stSql .= "     ban.nom_banco,                              \n";
    $stSql .= "     ban.num_banco,                              \n";
    $stSql .= "     ag.cod_agencia,                             \n";
    $stSql .= "     ag.num_agencia,                             \n";
    $stSql .= "     ag.nom_agencia,                             \n";
    $stSql .= "     ca.cod_carteira,                            \n";
    $stSql .= "     ca.variacao,                                \n";
    $stSql .= "     con.taxa_bancaria,                          \n";
    $stSql .= "     con.cedente,                                \n";
    $stSql .= "     con.cod_tipo,                               \n";
    $stSql .= "     con.num_convenio,                           \n";
    $stSql .= "     tc.nom_tipo,                                \n";
    $stSql .= "     tc.cod_modulo,                              \n";
    $stSql .= "     tc.cod_biblioteca,                          \n";
    $stSql .= "     tc.cod_funcao                               \n";
    $stSql .= " FROM                                            \n";
    $stSql .= "     monetario.conta_corrente_convenio as ccc    \n";

    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.conta_corrente as mcc             \n";
    $stSql .= " ON                                              \n";
    $stSql .= "     mcc.cod_conta_corrente = ccc.cod_conta_corrente \n";
/*
    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     contabilidade.plano_banco as cpb            \n";
    $stSql .= " ON                                              \n";
    $stSql .= "     cpb.cod_conta_corrente = mcc.cod_conta_corrente
                    AND cpb.cod_banco = mcc.cod_banco
                    AND cpb.cod_agencia = mcc.cod_agencia       \n";
*/
    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.banco as ban                      \n";
    $stSql .= " ON                                              \n";
    $stSql .= "     ban.cod_banco = ccc.cod_banco               \n";
    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.agencia as ag                     \n";
    $stSql .= " ON                                              \n";
    $stSql .= "     ag.cod_banco = ccc.cod_banco                \n";
    $stSql .= "     AND                                         \n";
    $stSql .= "     ag.cod_agencia = ccc.cod_agencia            \n";
    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.convenio as con                   \n";
    $stSql .= " ON                                              \n";
    $stSql .= "     ccc.cod_convenio = con.cod_convenio         \n";
    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.tipo_convenio as tc               \n";
    $stSql .= " ON                                              \n";
    $stSql .= "     con.cod_tipo = tc.cod_tipo                  \n";
    $stSql .= " LEFT JOIN                                      \n";
    $stSql .= "     monetario.carteira as ca                    \n";
    $stSql .= " ON ca.cod_convenio = con.cod_convenio            \n";

    return $stSql;
}

function recuperaConvenioBancoGF(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

    $stSql = $this->montaRecuperaConvenioBancoGF().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    //$this->debug(); exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConvenioBancoGF()
{
    $stSql  = " SELECT DISTINCT                                 \n";
    $stSql .= "     ccc.cod_agencia,                            \n";
    $stSql .= "     ccc.cod_banco,                              \n";
    $stSql .= "     ccc.cod_convenio,                           \n";
    $stSql .= "     ccc.cod_conta_corrente,                     \n";
    $stSql .= "     mcc.num_conta_corrente,                     \n";
    $stSql .= "     ban.cod_banco,                              \n";
    $stSql .= "     ban.nom_banco,                              \n";
    $stSql .= "     ban.num_banco,                              \n";
    $stSql .= "     ag.cod_agencia,                             \n";
    $stSql .= "     ag.num_agencia,                             \n";
    $stSql .= "     ag.nom_agencia,                             \n";
    $stSql .= "     ca.cod_carteira,                            \n";
    $stSql .= "     ca.variacao,                                \n";
    $stSql .= "     con.taxa_bancaria,                          \n";
    $stSql .= "     con.cedente,                                \n";
    $stSql .= "     con.cod_tipo,                               \n";
    $stSql .= "     con.num_convenio,                           \n";
    $stSql .= "     tc.nom_tipo,                                \n";
    $stSql .= "     tc.cod_modulo,                              \n";
    $stSql .= "     tc.cod_biblioteca,                          \n";
    $stSql .= "     tc.cod_funcao                               \n";
    $stSql .= " FROM                                            \n";
    $stSql .= "     monetario.conta_corrente_convenio as ccc    \n";

    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.conta_corrente as mcc             \n";
    $stSql .= " ON                                              \n";
    $stSql .= "     mcc.cod_conta_corrente = ccc.cod_conta_corrente \n";

    $stSql .= " LEFT JOIN                                      \n";
    $stSql .= "     contabilidade.plano_banco as cpb            \n";
    $stSql .= " ON                                              \n";
    $stSql .= "     cpb.cod_conta_corrente = mcc.cod_conta_corrente
                    AND cpb.cod_banco = mcc.cod_banco
                    AND cpb.cod_agencia = mcc.cod_agencia       \n";

    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.banco as ban                      \n";
    $stSql .= " ON                                              \n";
    $stSql .= "     ban.cod_banco = ccc.cod_banco               \n";
    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.agencia as ag                     \n";
    $stSql .= " ON                                              \n";
    $stSql .= "     ag.cod_banco = ccc.cod_banco                \n";
    $stSql .= "     AND                                         \n";
    $stSql .= "     ag.cod_agencia = ccc.cod_agencia            \n";
    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.convenio as con                   \n";
    $stSql .= " ON                                              \n";
    $stSql .= "     ccc.cod_convenio = con.cod_convenio         \n";
    $stSql .= " INNER JOIN                                      \n";
    $stSql .= "     monetario.tipo_convenio as tc               \n";
    $stSql .= " ON                                              \n";
    $stSql .= "     con.cod_tipo = tc.cod_tipo                  \n";
    $stSql .= " LEFT JOIN                                      \n";
    $stSql .= "     monetario.carteira as ca                    \n";
    $stSql .= " ON ca.cod_convenio = con.cod_convenio            \n";

    return $stSql;
}

function recuperaConvenioContas(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaConvenioContas( ).$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConvenioContas()
{
    $stSql .= "    select                                            \n";
    $stSql .= "        ccc.cod_convenio,                             \n";
    $stSql .= "        ccc.cod_conta_corrente,                       \n";
    $stSql .= "        ccc.cod_banco,                                \n";
    $stSql .= "        ccc.cod_agencia,                              \n";
    $stSql .= "        ccc.variacao,                                 \n";
    $stSql .= "        cc.num_conta_corrente,                        \n";
    $stSql .= "        ma.num_agencia,                               \n";
    $stSql .= "        mb.num_banco                                  \n";

    $stSql .= "    from                                              \n";
    $stSql .= "        monetario.conta_corrente_convenio as ccc      \n";

    $stSql .= "    INNER JOIN                                        \n";
    $stSql .= "        monetario.conta_corrente as cc                \n";
    $stSql .= "    ON                                                \n";
    $stSql .= "        cc.cod_conta_corrente = ccc.cod_conta_corrente\n";

    $stSql .= "    INNER JOIN                                        \n";
    $stSql .= "        monetario.agencia as ma                       \n";
    $stSql .= "    ON                                                \n";
    $stSql .= "        ma.cod_agencia = ccc.cod_agencia              \n";
    $stSql .= "        AND ma.cod_banco = ccc.cod_banco              \n";

    $stSql .= "    INNER JOIN                                        \n";
    $stSql .= "        monetario.banco as mb                         \n";
    $stSql .= "    ON                                                \n";
    $stSql .= "        mb.cod_banco = ccc.cod_banco                  \n";

    return $stSql;
}

function verificaReferenciaConvenio(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

    $stSql = $this->montaverificaReferenciaConvenio().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    //$this->debug(); exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaverificaReferenciaConvenio()
{
    $stSql  = " SELECT DISTINCT \n";
    $stSql .= "     mc.cod_convenio, \n";
    $stSql .= "     mc.num_convenio, \n";
    $stSql .= "     mc.referencia, \n";
    $stSql .= "     mc.referencia_carteira, \n";
    $stSql .= "     mc.referencia_credito, \n";
    $stSql .= "     mc.referencia_carne \n";
    $stSql .= " FROM ( \n";
    $stSql .= "     SELECT \n";
    $stSql .= "         mc.*, \n";
    $stSql .= "         CASE WHEN COALESCE(mct.cod_convenio, mcr.cod_convenio, ac.cod_convenio) IS NOT NULL THEN \n";
    $stSql .= "             true \n";
    $stSql .= "         ELSE \n";
    $stSql .= "             false \n";
    $stSql .= "         END AS referencia, \n";
    $stSql .= "         CASE WHEN mct.cod_convenio IS NOT NULL THEN \n";
    $stSql .= "             true \n";
    $stSql .= "         ELSE \n";
    $stSql .= "             false \n";
    $stSql .= "         END AS referencia_carteira, \n";
    $stSql .= "         CASE WHEN mcr.cod_convenio IS NOT NULL THEN \n";
    $stSql .= "             true \n";
    $stSql .= "         ELSE \n";
    $stSql .= "             false \n";
    $stSql .= "         END AS referencia_credito, \n";
    $stSql .= "         CASE WHEN ac.cod_convenio IS NOT NULL THEN \n";
    $stSql .= "             true \n";
    $stSql .= "         ELSE \n";
    $stSql .= "             false \n";
    $stSql .= "         END AS referencia_carne \n";
    $stSql .= "     FROM \n";
    $stSql .= "         monetario.convenio AS mc \n";
    $stSql .= "     LEFT JOIN \n";
    $stSql .= "         monetario.carteira AS mct \n";
    $stSql .= "     ON \n";
    $stSql .= "         mct.cod_convenio = mc.cod_convenio \n";
    $stSql .= "     LEFT JOIN \n";
    $stSql .= "         monetario.credito AS mcr \n";
    $stSql .= "     ON \n";
    $stSql .= "         mcr.cod_convenio = mc.cod_convenio \n";
    $stSql .= "     LEFT JOIN \n";
    $stSql .= "         arrecadacao.carne AS ac \n";
    $stSql .= "     ON \n";
    $stSql .= "         ac.cod_convenio = mc.cod_convenio \n";
    $stSql .= " )AS mc \n";
    $stSql .= " WHERE \n";
    $stSql .= "     mc.referencia = true \n";

    return $stSql;
}

}
