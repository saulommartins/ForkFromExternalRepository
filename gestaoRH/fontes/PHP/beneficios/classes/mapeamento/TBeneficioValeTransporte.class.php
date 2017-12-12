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
* Classe de mapeamento da tabela BENEFICIO.VALE_TRANSPORTE
* Data de Criação: 07/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage mapeamento

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.06.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  BENEFICIO.VALE_TRANSPORTE
  * Data de Criação: 07/07/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TBeneficioValeTransporte extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TBeneficioValeTransporte()
{
    parent::Persistente();
    $this->setTabela('beneficio.vale_transporte');

    $this->setCampoCod('cod_vale_transporte');
    $this->setComplementoChave('');

    $this->AddCampo('cod_vale_transporte','integer',true,'',true,false);
    $this->AddCampo('fornecedor_vale_transporte_fornecedor_numcgm','integer',true,'',false,true);

}

function recuperaRelacionamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaRelacionamento().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT vale_transporte.*                                                     \n";
    $stSql .= "     , itinerario.*                                                          \n";
    $stSql .= "     , trim(linha1.descricao) as destino                                     \n";
    $stSql .= "     , trim(linha2.descricao) as origem                                      \n";
    $stSql .= "     , tabela1.sigla_uf as sigla_uf_o                                        \n";
    $stSql .= "     , tabela1.nom_uf as nom_uf_o                                            \n";
    $stSql .= "     , tabela2.sigla_uf as sigla_uf_d                                        \n";
    $stSql .= "     , tabela2.nom_uf as nom_uf_d                                            \n";
    $stSql .= "     , tabela1.nom_municipio as nom_municipio_o                              \n";
    $stSql .= "     , tabela2.nom_municipio as nom_municipio_d                              \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                        \n";
    $stSql .= "     , sw_cgm.numcgm                                                         \n";
    $stSql .= "     , custo.valor                                                           \n";
    $stSql .= "FROM beneficio.vale_transporte                                               \n";
    $stSql .= "JOIN beneficio.itinerario                                                    \n";
    $stSql .= "  ON itinerario.vale_transporte_cod_vale_transporte = vale_transporte.cod_vale_transporte \n";
    $stSql .= "JOIN ( SELECT sw_municipio.cod_municipio                                     \n";
    $stSql .= "            , sw_municipio.nom_municipio                                     \n";
    $stSql .= "            , sw_uf.*                                                        \n";
    $stSql .= "         FROM sw_municipio                                                   \n";
    $stSql .= "         JOIN sw_uf                                                          \n";
    $stSql .= "           ON sw_uf.cod_uf = sw_municipio.cod_uf                             \n";
    $stSql .= "   ) AS tabela2                                                              \n";
    $stSql .= "  ON tabela2.cod_municipio = itinerario.municipio_destino                    \n";
    $stSql .= " AND tabela2.cod_uf        = itinerario.uf_destino                           \n";
    $stSql .= "JOIN beneficio.custo                                                         \n";
    $stSql .= "  ON custo.vale_transporte_cod_vale_transporte = vale_transporte.cod_vale_transporte ";
    $stSql .= "JOIN ( SELECT sw_municipio.cod_municipio                                     \n";
    $stSql .= "            , sw_municipio.nom_municipio                                     \n";
    $stSql .= "            , sw_uf.*                                                        \n";
    $stSql .= "         FROM sw_municipio                                                   \n";
    $stSql .= "         JOIN sw_uf                                                          \n";
    $stSql .= "           ON sw_uf.cod_uf = sw_municipio.cod_uf                             \n";
    $stSql .= "   ) AS tabela1                                                              \n";
    $stSql .= "  ON tabela1.cod_municipio = itinerario.municipio_origem                     \n";
    $stSql .= " AND tabela1.cod_uf        = itinerario.uf_origem                            \n";
    $stSql .= "JOIN beneficio.linha AS linha1                                               \n";
    $stSql .= "  ON linha1.cod_linha = itinerario.cod_linha_destino                         \n";
    $stSql .= "JOIN beneficio.linha AS linha2                                               \n";
    $stSql .= "  ON linha2.cod_linha = itinerario.cod_linha_origem                          \n";
    $stSql .= "JOIN sw_cgm                                                                  \n";
    $stSql .= "  ON vale_transporte.fornecedor_vale_transporte_fornecedor_numcgm = sw_cgm.numcgm ";
    $stSql .= "JOIN ( SELECT vale_transporte_cod_vale_transporte                            \n";
    $stSql .= "            , max(inicio_vigencia) as inicio_vigencia                        \n";
    $stSql .= "         FROM beneficio.custo                                                \n";
    $stSql .= "     GROUP BY vale_transporte_cod_vale_transporte                            \n";
    $stSql .= "   ) as max_custo                                                            \n";
    $stSql .= "  ON max_custo.vale_transporte_cod_vale_transporte = custo.vale_transporte_cod_vale_transporte \n";
    $stSql .= " AND max_custo.inicio_vigencia                     = custo.inicio_vigencia   \n";
    $stSql .= "JOIN compras.fornecedor                                                      \n";
    $stSql .= "  ON fornecedor.cgm_fornecedor = vale_transporte.fornecedor_vale_transporte_fornecedor_numcgm \n";
    $stSql .= "WHERE 1=1                                                                    \n";

    return $stSql;
}

function recuperaRelacionamentoRelatorio(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaRelacionamentoRelatorio().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoRelatorio()
{
    $stSql  .= "SELECT                                                                      \n";
    $stSql  .= "    trim(linha1.descricao) as destino,                                      \n";
    $stSql  .= "    trim(linha2.descricao) as origem,                                       \n";
    $stSql  .= "    tabela1.nom_municipio as nom_municipio_o,                               \n";
    $stSql  .= "    tabela2.nom_municipio as nom_municipio_d,                               \n";
    $stSql  .= "    to_char(custo.inicio_vigencia,'dd/mm/yyyy') as vigencia,                \n";
    $stSql  .= "    custo.valor as custo,                                                   \n";
    $stSql  .= "    sw_cgm.nom_cgm,                                                            \n";
    $stSql  .= "    sw_cgm.numcgm                                                              \n";
    $stSql .= "FROM beneficio.vale_transporte                                               \n";
    $stSql .= "JOIN beneficio.itinerario                                                    \n";
    $stSql .= "  ON itinerario.vale_transporte_cod_vale_transporte = vale_transporte.cod_vale_transporte \n";
    $stSql .= "JOIN ( SELECT sw_municipio.cod_municipio                                     \n";
    $stSql .= "            , sw_municipio.nom_municipio                                     \n";
    $stSql .= "            , sw_uf.*                                                        \n";
    $stSql .= "         FROM sw_municipio                                                   \n";
    $stSql .= "         JOIN sw_uf                                                          \n";
    $stSql .= "           ON sw_uf.cod_uf = sw_municipio.cod_uf                             \n";
    $stSql .= "   ) AS tabela2                                                              \n";
    $stSql .= "  ON tabela2.cod_municipio = itinerario.municipio_destino                    \n";
    $stSql .= " AND tabela2.cod_uf        = itinerario.uf_destino                           \n";
    $stSql .= "JOIN beneficio.custo                                                         \n";
    $stSql .= "  ON custo.vale_transporte_cod_vale_transporte = vale_transporte.cod_vale_transporte ";
    $stSql .= "JOIN ( SELECT sw_municipio.cod_municipio                                     \n";
    $stSql .= "            , sw_municipio.nom_municipio                                     \n";
    $stSql .= "            , sw_uf.*                                                        \n";
    $stSql .= "         FROM sw_municipio                                                   \n";
    $stSql .= "         JOIN sw_uf                                                          \n";
    $stSql .= "           ON sw_uf.cod_uf = sw_municipio.cod_uf                             \n";
    $stSql .= "   ) AS tabela1                                                              \n";
    $stSql .= "  ON tabela1.cod_municipio = itinerario.municipio_origem                     \n";
    $stSql .= " AND tabela1.cod_uf        = itinerario.uf_origem                            \n";
    $stSql .= "JOIN beneficio.linha AS linha1                                               \n";
    $stSql .= "  ON linha1.cod_linha = itinerario.cod_linha_destino                         \n";
    $stSql .= "JOIN beneficio.linha AS linha2                                               \n";
    $stSql .= "  ON linha2.cod_linha = itinerario.cod_linha_origem                          \n";
    $stSql .= "JOIN sw_cgm                                                                  \n";
    $stSql .= "  ON vale_transporte.fornecedor_vale_transporte_fornecedor_numcgm = sw_cgm.numcgm ";
    $stSql .= "JOIN ( SELECT vale_transporte_cod_vale_transporte                            \n";
    $stSql .= "            , max(inicio_vigencia) as inicio_vigencia                        \n";
    $stSql .= "         FROM beneficio.custo                                                \n";
    $stSql .= "     GROUP BY vale_transporte_cod_vale_transporte                            \n";
    $stSql .= "   ) as max_custo                                                            \n";
    $stSql .= "  ON max_custo.vale_transporte_cod_vale_transporte = custo.vale_transporte_cod_vale_transporte \n";
    $stSql .= " AND max_custo.inicio_vigencia                     = custo.inicio_vigencia   \n";
    $stSql .= "JOIN compras.fornecedor                                                      \n";
    $stSql .= "  ON fornecedor.cgm_fornecedor = vale_transporte.fornecedor_vale_transporte_fornecedor_numcgm \n";
    $stSql .= "WHERE 1=1                                                                    \n";

    return $stSql;
}

function recuperaRelacionamentoConcessao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaRelacionamentoConcessao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoConcessao()
{
    $stSql .= "SELECT vale_transporte.*                         \n";
    $stSql .= "  FROM beneficio.vale_transporte                 \n";
    $stSql .= "     , beneficio.concessao_vale_transporte       \n";
    $stSql .= " WHERE vale_transporte.cod_vale_transporte = concessao_vale_transporte.cod_vale_transporte \n";

    return $stSql;
}

}
