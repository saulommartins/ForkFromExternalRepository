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
     * Classe de mapeamento para a tabela IMOBILIARIO.CONFRONTACAO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMConfrontacao.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.10  2007/03/19 20:22:55  dibueno
Bug #8416#

Revision 1.9  2007/02/22 15:01:39  dibueno
Bug #8416#

Revision 1.8  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.CONFRONTACAO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMConfrontacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMConfrontacao()
{
    parent::Persistente();
    $this->setTabela('imobiliario.confrontacao');

    $this->setCampoCod('cod_confrontacao');
    $this->setComplementoChave('cod_lote');

    $this->AddCampo('cod_confrontacao','integer',true,'',true,false);
    $this->AddCampo('cod_lote','integer',true,'',true,true);
    $this->AddCampo('cod_ponto','integer',true,'',false,true);
}

function montaRecuperaRelacionamento()
{
    $stSQL  = " SELECT                                                            \n";
    $stSQL .= "     C.cod_confrontacao,                                           \n";
    $stSQL .= "     C.cod_lote,                                                   \n";
    $stSQL .= "     C.valor,                                                      \n";
    $stSQL .= "     C.cod_ponto,                                                  \n";
    $stSQL .= "     C.nom_ponto,                                                  \n";
    $stSQL .= "     C.sigla,                                                      \n";
    $stSQL .= "     CD.descricao,                                                 \n";
    $stSQL .= "     CL.cod_lote_confrontacao,                                     \n";
    $stSQL .= "     CT.cod_trecho,                                                \n";
    $stSQL .= "     CT.cod_logradouro,                                            \n";
    $stSQL .= "     CT.sequencia,                                                 \n";
    $stSQL .= "     CASE WHEN CL.cod_lote_confrontacao IS NOT NULL THEN 'Lote'    \n";
    $stSQL .= "     WHEN CT.cod_trecho IS NOT NULL                                \n";
    $stSQL .= "     THEN 'Trecho'                                                 \n";
    $stSQL .= "     ELSE 'Outros'                                                 \n";
    $stSQL .= "     END AS tipo,                                                  \n";
    $stSQL .= "     CASE WHEN CT.principal THEN 'Sim' ELSE 'Não' END AS principal \n";
    $stSQL .= " FROM                                                              \n";
    $stSQL .= "     (                                                             \n";
    $stSQL .= "     SELECT                                                        \n";
    $stSQL .= "        C.COD_CONFRONTACAO,                                        \n";
    $stSQL .= "        C.COD_LOTE,                                                \n";
    $stSQL .= "        CE.VALOR,                                                  \n";
    $stSQL .= "        CE.TIMESTAMP,                                              \n";
    $stSQL .= "        PC.COD_PONTO,                                              \n";
    $stSQL .= "        PC.COD_PONTO_OPOSTO,                                       \n";
    $stSQL .= "        PC.NOM_PONTO,                                              \n";
    $stSQL .= "        PC.SIGLA                                                   \n";
    $stSQL .= "     FROM                                                          \n";
    $stSQL .= "        imobiliario.confrontacao AS C,                                 \n";
    $stSQL .= "        imobiliario.vw_confrontacao_extensao_atual AS CE,              \n";
    $stSQL .= "        imobiliario.ponto_cardeal AS PC                                \n";
    $stSQL .= "     WHERE                                                         \n";
    $stSQL .= "        C.COD_CONFRONTACAO = CE.COD_CONFRONTACAO AND               \n";
    $stSQL .= "        C.COD_LOTE         = CE.COD_LOTE AND                       \n";
    $stSQL .= "        C.COD_PONTO        = PC.COD_PONTO ) AS C                   \n";
    $stSQL .= " LEFT JOIN                                                         \n";
    $stSQL .= "     imobiliario.confrontacao_lote AS CL                               \n";
    $stSQL .= " ON                                                                \n";
    $stSQL .= "     C.COD_CONFRONTACAO = CL.COD_CONFRONTACAO AND                  \n";
    $stSQL .= "     C.COD_LOTE         = CL.COD_LOTE                              \n";
    $stSQL .= " LEFT JOIN                                                         \n";
    $stSQL .= " ( SELECT                                                          \n";
    $stSQL .= "           T.cod_trecho,                                           \n";
    $stSQL .= "           T.cod_logradouro,                                       \n";
    $stSQL .= "           T.sequencia,                                            \n";
    $stSQL .= "           T.extensao,                                             \n";
    $stSQL .= "           CT.cod_confrontacao,                                    \n";
    $stSQL .= "           CT.cod_lote,                                            \n";
    $stSQL .= "           CT.principal,                                           \n";
    $stSQL .= "           L.cod_uf,                                               \n";
    $stSQL .= "           L.cod_municipio                                        \n";
    $stSQL .= "       FROM                                                        \n";
    $stSQL .= "           imobiliario.trecho T                                    \n";
    $stSQL .= "           INNER JOIN imobiliario.confrontacao_trecho CT           \n";
    $stSQL .= "           ON T.COD_TRECHO = CT.COD_TRECHO AND                     \n";
    $stSQL .= "           T.COD_LOGRADOURO = CT.COD_LOGRADOURO                    \n";
    $stSQL .= "           INNER JOIN sw_logradouro L                              \n";
    $stSQL .= "           ON L.COD_LOGRADOURO = T.COD_LOGRADOURO                  \n";
    $stSQL .= "       ) AS CT                                                     \n";
    $stSQL .= " ON                                                                \n";
    $stSQL .= "     C.COD_CONFRONTACAO = CT.COD_CONFRONTACAO AND                  \n";
    $stSQL .= "     C.COD_LOTE         = CT.COD_LOTE                              \n";
    $stSQL .= " LEFT JOIN                                                         \n";
    $stSQL .= "     imobiliario.confrontacao_diversa AS CD                        \n";
    $stSQL .= " ON                                                                \n";
    $stSQL .= "     C.COD_CONFRONTACAO = CD.COD_CONFRONTACAO AND                  \n";
    $stSQL .= "     C.COD_LOTE         = CD.COD_LOTE                              \n";

    return $stSQL;
}

function listaImoveisLogradouro(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

    $stSql = $this->montaListaImoveisLogradouro( $stFiltro ).$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaImoveisLogradouro($stFiltro)
{
    $stSQL  = " SELECT                                                                  \n";
    $stSQL .= "     imobiliario.fn_busca_imoveis_logradouro( $stFiltro ) as valor       \n";

    return $stSQL;

}

function listaEmpresasLogradouro(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

    $stSql = $this->montaListaEmpresasLogradouro( $stFiltro ).$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaEmpresasLogradouro($stFiltro)
{
    $stSQL  = " SELECT                                                                  \n";
    $stSQL .= "     imobiliario.fn_busca_empresas_logradouro( $stFiltro ) as valor       \n";

    return $stSQL;

}

}
