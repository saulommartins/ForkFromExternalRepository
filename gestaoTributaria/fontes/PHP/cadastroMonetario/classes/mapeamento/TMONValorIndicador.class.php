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
    * Classe de MAPEAMENTO para MONETARIO.VALOR_INDICADOR
    * Data de Criacao: 20/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TMONValorIndicador.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.08
*/

/*
$Log$
Revision 1.5  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

/*include_once    ("../../../includes/Constante.inc.php");*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONValorIndicador extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
function TMONValorIndicador()
{
    parent::Persistente();
    $this->setTabela('monetario.valor_indicador');

    $this->setCampoCod('cod_indicador');
    $this->setComplementoChave('inicio_vigencia');

    $this->AddCampo('cod_indicador','integer',true,'',true,false);
    $this->AddCampo('valor','numeric',true,'',true,false);
    $this->AddCampo('inicio_vigencia','date',true,'',false,false);
}

function montaRecuperaRelacionamentoInclusao()
{
    $stSql  = " SELECT                                  \n";
    $stSql .= "     ie.cod_indicador,                   \n";
    $stSql .= "     ie.descricao,                       \n";
    $stSql .= "     ie.abreviatura                      \n";
    $stSql .= " FROM                                    \n";
    $stSql .= "     monetario.indicador_economico as ie \n";

return $stSql;
}
function RecuperaRelacionamentoInclusao(&$rsRecordSet, $stFiltro , $stOrdem , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoInclusao();

    $stSql .= $stFiltro. ' '. $stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoAlteracao()
{
    $stSql  = " SELECT                                      \n";
    $stSql .= "     ie.cod_indicador,                       \n";
    $stSql .= "     ie.descricao,                           \n";
    $stSql .= "     ie.abreviatura,                         \n";
    $stSql .= "     to_char (vi.inicio_vigencia, 'dd/mm/YYYY') as inicio_vigencia, \n";
    $stSql .= "     vi.valor                                \n";
    $stSql .= " FROM                                        \n";
    $stSql .= "     monetario.indicador_economico as ie     \n";
    $stSql .= " INNER JOIN                                  \n";
    $stSql .= "     (                                       \n";
    $stSql .= "     SELECT                                  \n";
    $stSql .= "         VI.*                                \n";
    $stSql .= "     FROM                                    \n";
    $stSql .= "         monetario.valor_indicador AS VI,    \n";
    $stSql .= "         (                                   \n";
    $stSql .= "         SELECT                              \n";
    $stSql .= "             MAX (INICIO_VIGENCIA) AS INVIG, \n";
    $stSql .= "             COD_INDICADOR                   \n";
    $stSql .= "         FROM                                \n";
    $stSql .= "             monetario.valor_indicador       \n";
    $stSql .= "         GROUP BY                            \n";
    $stSql .= "             COD_INDICADOR                   \n";
    $stSql .= "         ) AS MAX                            \n";
    $stSql .= "         WHERE                               \n";
    $stSql .= "             VI.COD_INDICADOR = MAX.COD_INDICADOR    \n";
    $stSql .= "             AND                             \n";
    $stSql .= "             VI.INICIO_VIGENCIA = MAX.INVIG  \n";
    $stSql .= "     ) AS VI                                 \n";
    $stSql .= " ON                                          \n";
    $stSql .= " ie.COD_INDICADOR = vi.COD_INDICADOR         \n";

return $stSql;
}

function RecuperaRelacionamentoAlteracao(&$rsRecordSet, $stFiltro , $stOrdem , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoAlteracao();

    $stSql .= $stFiltro. ' '. $stOrdem;

    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * conjunto de funcao para recuperar os codigos da Formula referente
    * @access Public
    * @param  INTEGER Codigo do Acrescimo referente
    * @return SQL
*/
function montaRecuperaRelacionamentoExclusao()
{
    $stSql  = " SELECT                                  \n";
    $stSql .= "     vi.cod_indicador,                   \n";
    $stSql .= "     to_char (vi.inicio_vigencia, 'dd/mm/YYYY') as inicio_vigencia, \n";
    $stSql .= "     vi.valor,                           \n";
    $stSql .= "     ie.descricao,                       \n";
    $stSql .= "     ie.abreviatura                      \n";
    $stSql .= " FROM                                    \n";
    $stSql .= "     monetario.valor_indicador as vi     \n";
    $stSql .= " INNER JOIN                              \n";
    $stSql .= "     monetario.indicador_economico as ie \n";
    $stSql .= " ON                                      \n";
    $stSql .= "     vi.cod_indicador = ie.cod_indicador \n";

return $stSql;
}
function RecuperaRelacionamentoExclusao(&$rsRecordSet, $stFiltro , $stOrdem , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoExclusao();
    $stOrdem = ' ORDER BY ie.cod_indicador, inicio_vigencia DESC';
    $stSql .= $stFiltro. ' '. $stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * conjunto de funcao para recuperar os codigos da Formula referente
    * @access Public
    * @param  INTEGER Codigo do Acrescimo referente
    * @return SQL
*/
function montaRecuperaRelacionamentoUltimaVigencia($codigo)
{
    $stSql  = " SELECT                          \n";
    $stSql .= "     *                           \n";
    $stSql .= " FROM                            \n";
    $stSql .= "     monetario.valor_indicador   \n";
    $stSql .= " WHERE                           \n";
    $stSql .= "     cod_indicador = '$codigo'   \n";
    $stSql .= " ORDER BY                        \n";
    $stSql .= "     inicio_vigencia DESC        \n";
    $stSql .= " LIMIT 1                         \n";

return $stSql;
}
function RecuperaRelacionamentoUltimaVigencia(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "", $codigo)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelacionamentoUltimaVigencia( $codigo );

    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}// fecha classe de mapeamento
