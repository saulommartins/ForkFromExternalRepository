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

    * $Id: TCIMImovelLote.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.5  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.IMOVEL_LOTE
  * Data de Criação: 11/05/2005

  * @author Analista: Fabio
  * @author Desenvolvedor: Lucas Teixeira Stephanou

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMImovelLote extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMImovelLote()
{
    parent::Persistente();
    $this->setTabela('imobiliario.imovel_lote');

    $this->setComplementoChave('inscricao_municipal,timestamp');

    $this->AddCampo('inscricao_municipal'   , 'integer'  , true   ,'' , true   , true);
    $this->AddCampo('timestamp'             , 'timestamp', false  ,'' , true   , false);
    $this->AddCampo('cod_lote'              , 'integer'  , true   ,'' , false  , true);
}

function recuperaRelacionamentoAglutinar(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoAglutinar().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoAglutinar()
{
    $stSQL .= " SELECT DISTINCT                   \n";
    $stSQL .= "     IML.inscricao_municipal       \n";
    $stSQL .= " FROM                              \n";
    $stSQL .= "     imobiliario.imovel_lote  AS  IML  \n";

    return $stSQL;
}

function recuperaDataLoteImovel(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDataLoteImovel().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDataLoteImovel()
{
    $stSql .= "SELECT                                                                    \n";
    $stSql .= "    TO_CHAR( L.dt_inscricao, 'dd/mm/yyyy' ) as dt_inscricao,                 \n";
    $stSql .= "    L.cod_lote,                                                           \n";
    $stSql .= "    I.inscricao_municipal                                                 \n";
    $stSql .= "FROM                                                                      \n";
    $stSql .= "    imobiliario.imovel AS I                                               \n";
    $stSql .= "    INNER JOIN (                                                          \n";
    $stSql .= "        SELECT                                                            \n";
    $stSql .= "            IIL.*                                                         \n";
    $stSql .= "        FROM                                                              \n";
    $stSql .= "            imobiliario.imovel_lote IIL,                                  \n";
    $stSql .= "            (SELECT                                                       \n";
    $stSql .= "                MAX (TIMESTAMP) AS TIMESTAMP,                             \n";
    $stSql .= "                INSCRICAO_MUNICIPAL                                       \n";
    $stSql .= "            FROM                                                          \n";
    $stSql .= "                imobiliario.imovel_lote                                   \n";
    $stSql .= "            GROUP BY                                                      \n";
    $stSql .= "                INSCRICAO_MUNICIPAL                                       \n";
    $stSql .= "            ) AS IL                                                       \n";
    $stSql .= "        WHERE                                                             \n";
    $stSql .= "                IIL.INSCRICAO_MUNICIPAL = IL.INSCRICAO_MUNICIPAL          \n";
    $stSql .= "            AND IIL.TIMESTAMP = IL.TIMESTAMP                              \n";
    $stSql .= "    ) AS IL ON                                                            \n";
    $stSql .= "    I.inscricao_municipal = IL.inscricao_municipal,                       \n";
    $stSql .= "    imobiliario.lote L                                                    \n";
    $stSql .= "WHERE                                                                     \n";
    $stSql .= "    IL.cod_lote = L.cod_lote                                              \n";

    return $stSql;
}

}
