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
  * Classe de mapeamento da tabela PESSOAL.ASSENTAMENTO_MOV_SEFIP_SAIDA
  * Data de Criação: 02/02/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.ASSENTAMENTO_MOV_SEFIP_SAIDA
  * Data de Criação: 02/02/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAssentamentoMovSefipSaida extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoMovSefipSaida()
{
    parent::Persistente();
    $this->setTabela('pessoal.assentamento_mov_sefip_saida');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_assentamento,timestamp');

    $this->AddCampo('cod_assentamento','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,true);
    $this->AddCampo('cod_sefip_saida','integer',true,'',false,true);

}

function montaRecuperaRelacionamento()
{
    $stSQL .= " SELECT                                                   \n";
    $stSQL .= "     PAS.cod_sefip_saida,                                 \n";
    $stSQL .= "     SE.num_sefip                                         \n";
    $stSQL .= " FROM                                                     \n";
    $stSQL .= "   (select                                                \n";
    $stSQL .= "         cod_assentamento, max(timestamp) as timestamp    \n";
    $stSQL .= "     from                                                 \n";
    $stSQL .= "         pessoal.assentamento                         \n";
    $stSQL .= "     group by cod_assentamento) as pa,                    \n";
    $stSQL .= "    pessoal.assentamento_mov_sefip_saida as PAS,      \n";
    $stSQL .= "    pessoal.mov_sefip_saida as MSS,                   \n";
    $stSQL .= "    pessoal.sefip as SE                               \n";
    $stSQL .= " WHERE                                                    \n";
    $stSQL .= "     PA.cod_assentamento = PAS.cod_assentamento and       \n";
    $stSQL .= "     PA.timestamp = PAS.timestamp  and                    \n";
    $stSQL .= "     PAS.cod_sefip_saida = MSS.cod_sefip_saida and        \n";
    $stSQL .= "     MSS.cod_sefip_saida = SE.cod_sefip                   \n";

    return $stSQL;
}

function recuperaAfastamentoTemporariosSefip(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsLista     = new RecordSet;
    $stSql = $this->montaRecuperaAfastamentoTemporariosSefip().$stFiltro.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAfastamentoTemporariosSefip()
{
    $stSql .= "SELECT assentamento_gerado.*                                                                                                 \n";
    $stSql .= "     , sefip.*                                                                                                               \n";
    $stSql .= "  FROM pessoal.assentamento_gerado                                                                                           \n";
    $stSql .= "     , (SELECT cod_assentamento_gerado                                                                                       \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                                   \n";
    $stSql .= "          FROM pessoal.assentamento_gerado                                                                                   \n";
    $stSql .= "       GROUP BY cod_assentamento_gerado) as max_assentamento_gerado                                                          \n";
    $stSql .= "     , pessoal.assentamento_gerado_contrato_servidor                                                                         \n";
    $stSql .= "     , pessoal.assentamento_assentamento                                                                                     \n";
    $stSql .= "     , pessoal.assentamento                                                                                                  \n";
    $stSql .= "     , (SELECT cod_assentamento                                                                                              \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                                   \n";
    $stSql .= "          FROM pessoal.assentamento                                                                                          \n";
    $stSql .= "       GROUP BY cod_assentamento) as max_assentamento                                                                        \n";
    $stSql .= "     , pessoal.classificacao_assentamento                                                                                    \n";
    $stSql .= "     , pessoal.assentamento_mov_sefip_saida                                                                                  \n";
    $stSql .= "     , pessoal.sefip                                                                                                         \n";
    $stSql .= " WHERE assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado                         \n";
    $stSql .= "   AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp                                                     \n";
    $stSql .= "   AND assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado           \n";
    $stSql .= "   AND assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento                                     \n";
    $stSql .= "   AND assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao                            \n";
    $stSql .= "   AND assentamento_assentamento.cod_assentamento = assentamento.cod_assentamento                                            \n";
    $stSql .= "   AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                                     \n";
    $stSql .= "   AND assentamento.timestamp = max_assentamento.timestamp                                                                   \n";
    $stSql .= "   AND assentamento.cod_assentamento = assentamento_mov_sefip_saida.cod_assentamento                                         \n";
    $stSql .= "   AND assentamento.timestamp = assentamento_mov_sefip_saida.timestamp                                                       \n";
    $stSql .= "   AND assentamento_mov_sefip_saida.cod_sefip_saida = sefip.cod_sefip                                                        \n";
    $stSql .= "   AND assentamento_gerado.cod_assentamento_gerado NOT IN (SELECT cod_assentamento_gerado                                    \n";
    $stSql .= "                                                             FROM pessoal.assentamento_gerado_excluido)                      \n";

    return $stSql;
}

}
