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
     * Classe de mapeamento para a tabela IMOBILIARIO.CONFRONTACAO_TRECHO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMConfrontacaoTrecho.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.CONFRONTACAO_TRECHO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMConfrontacaoTrecho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMConfrontacaoTrecho()
{
    parent::Persistente();
    $this->setTabela('imobiliario.confrontacao_trecho');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_confrontacao,cod_lote');

    $this->AddCampo('cod_confrontacao','integer',true,'',true,true);
    $this->AddCampo('cod_lote','integer',true,'',true,true);
    $this->AddCampo('cod_trecho','integer',true,'',false,true);
    $this->AddCampo('cod_logradouro','integer',true,'',false,true);
    $this->AddCampo('principal','boolean',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSQL  = " SELECT                                                      \n";
    $stSQL .= "     L.cod_lote,                                             \n";
    $stSQL .= "     L.dt_inscricao,                                         \n";
    $stSQL .= "     CT.cod_confrontacao,                                    \n";
    $stSQL .= "     CT.cod_lote,                                            \n";
    $stSQL .= "     CT.cod_trecho,                                          \n";
    $stSQL .= "     CT.cod_logradouro,                                      \n";
    $stSQL .= "     CT.principal,                                           \n";
    $stSQL .= "     NL.nom_logradouro,                                      \n";
    $stSQL .= "     NL.cod_tipo,                                            \n";
    $stSQL .= "     NL.nom_tipo,                                            \n";
    $stSQL .= "     NL.nom_tipo || ' ' || NL.nom_logradouro as nom_completo \n";
    $stSQL .= " FROM                                                        \n";
    $stSQL .= "     imobiliario.lote AS L,                                      \n";
    $stSQL .= "     imobiliario.confrontacao AS C,                              \n";
    $stSQL .= "     imobiliario.confrontacao_trecho AS CT,                      \n";
    $stSQL .= "     ( SELECT                                                \n";
    $stSQL .= "           COD_TRECHO,                                       \n";
    $stSQL .= "           COD_LOGRADOURO                                    \n";
    $stSQL .= "       FROM                                                  \n";
    $stSQL .= "           imobiliario.trecho                                    \n";
    $stSQL .= "       GROUP BY                                              \n";
    $stSQL .= "           COD_TRECHO,                                       \n";
    $stSQL .= "           COD_LOGRADOURO                                    \n";
    $stSQL .= "     ) AS T,                                                 \n";
    $stSQL .= "     (                                                       \n";
    $stSQL .= "       SELECT                                                \n";
    $stSQL .= "           NL.*,                                             \n";
    $stSQL .= "           TL.nom_tipo                                       \n";
    $stSQL .= "       FROM                                                  \n";
    $stSQL .= "           sw_nome_logradouro AS NL,                        \n";
    $stSQL .= "           (                                                 \n";
    $stSQL .= "            SELECT                                           \n";
    $stSQL .= "                MAX(timestamp) AS timestamp,                 \n";
    $stSQL .= "                cod_logradouro                               \n";
    $stSQL .= "            FROM                                             \n";
    $stSQL .= "                sw_nome_logradouro                          \n";
    $stSQL .= "            GROUP BY cod_logradouro                          \n";
    $stSQL .= "            ORDER BY cod_logradouro                          \n";
    $stSQL .= "           ) NLA,                                            \n";
    $stSQL .= "           sw_tipo_logradouro AS TL                         \n";
    $stSQL .= "       WHERE                                                 \n";
    $stSQL .= "           NL.COD_LOGRADOURO = NLA.COD_LOGRADOURO AND        \n";
    $stSQL .= "           NL.TIMESTAMP      = NLA.TIMESTAMP      AND        \n";
    $stSQL .= "           NL.COD_TIPO       = TL.COD_TIPO                   \n";
    $stSQL .= "       ORDER BY                                              \n";
    $stSQL .= "           NL.COD_LOGRADOURO                                 \n";
    $stSQL .= "     ) AS NL                                                 \n";
    $stSQL .= " WHERE                                                       \n";
    $stSQL .= "     L.COD_LOTE         = C.COD_LOTE          AND            \n";
    $stSQL .= "     C.COD_LOTE         = CT.COD_LOTE         AND            \n";
    $stSQL .= "     C.COD_CONFRONTACAO = CT.COD_CONFRONTACAO AND            \n";
    $stSQL .= "     CT.COD_TRECHO      = T.COD_TRECHO        AND            \n";
    $stSQL .= "     CT.COD_LOGRADOURO  = T.COD_LOGRADOURO    AND            \n";
    $stSQL .= "     T.COD_LOGRADOURO   = NL.COD_LOGRADOURO                  \n";

    return $stSQL;
}

function recuperaListaConfrontacaoTrecho(&$rsRecordSet, $inCodTrecho, $inCodLogradouro, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaConfrontacaoTrecho($inCodTrecho, $inCodLogradouro);
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaListaConfrontacaoTrecho($inCodTrecho, $inCodLogradouro)
{
    $stSQL = "
        SELECT valor AS nro_lote
          FROM imobiliario.confrontacao_trecho
    INNER JOIN imobiliario.lote_localizacao
            ON confrontacao_trecho.cod_lote = lote_localizacao.cod_lote
         WHERE confrontacao_trecho.cod_trecho = ".$inCodTrecho."
           AND confrontacao_trecho.cod_logradouro = ".$inCodLogradouro;

    return $stSQL;
}

}
