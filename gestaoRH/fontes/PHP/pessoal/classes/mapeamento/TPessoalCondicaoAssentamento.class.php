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
    * Classe de mapeamento da tabela PESSOAL.CONDICAO_ASSENTAMENTO
    * Data de Criação: 04/08/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso:  uc-04.04.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CONDICAO_ASSENTAMENTO
  * Data de Criação: 04/08/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCondicaoAssentamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCondicaoAssentamento()
{
    parent::Persistente();
    $this->setTabela('pessoal.condicao_assentamento');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_condicao,cod_assentamento,timestamp');

    $this->AddCampo('cod_condicao','integer',true,'',true,false);
    $this->AddCampo('cod_assentamento','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);

}

function recuperaAssentamentos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY paa.descricao ";
    $stSql  = $this->montaRecuperaAssentamentos().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAssentamentos()
{
    $stSQL  ="SELECT                                                        \n";
    $stSQL .="  ca.cod_condicao,                                            \n";
    $stSQL .="  ca.cod_assentamento,                                        \n";
    $stSQL .="  ca.timestamp,                                               \n";
    $stSQL .="  paa.sigla,                                                    \n";
    $stSQL .="  paa.descricao                                                 \n";
    $stSQL .="FROM                                                          \n";
    $stSQL .="  pessoal.condicao_assentamento as ca,                    \n";
    $stSQL .="  pessoal.assentamento as a                               \n";
    $stSQL .="LEFT JOIN                                                      \n";
    $stSQL .="      pessoal.assentamento_assentamento as paa             \n";
    $stSQL .="ON                                                             \n";
    $stSQL .="       a.cod_assentamento = paa.cod_assentamento,               \n";
    $stSQL .="  (SELECT                                                     \n";
    $stSQL .="      cod_condicao,                                           \n";
    $stSQL .="      cod_assentamento,                                       \n";
    $stSQL .="      max(timestamp) as timestamp                             \n";
    $stSQL .="  FROM                                                        \n";
    $stSQL .="      pessoal.condicao_assentamento                       \n";
    $stSQL .="  GROUP BY                                                    \n";
    $stSQL .="      cod_assentamento,                                       \n";
    $stSQL .="      cod_condicao                                            \n";
    $stSQL .="  ) as ult,                                                   \n";
    $stSQL .="   (SELECT                                                    \n";
    $stSQL .="      cod_assentamento,                                       \n";
    $stSQL .="      max(timestamp) as timestamp                             \n";
    $stSQL .="   FROM pessoal.assentamento                                  \n";
    $stSQL .="   GROUP BY cod_assentamento ) as a_ult                       \n";
    $stSQL .="WHERE                                                         \n";
    $stSQL .="       ca.cod_assentamento       = a.cod_assentamento         \n";
    $stSQL .="   AND ca.timestamp              = ult.timestamp              \n";
    $stSQL .="   AND ca.cod_assentamento       = ult.cod_assentamento       \n";
    $stSQL .="   AND ca.cod_condicao           = ult.cod_condicao           \n";
    $stSQL .="   AND ca.cod_condicao::varchar||ca.cod_assentamento::varchar||ca.timestamp::varchar not in( \n";
    $stSQL .="  SELECT                                                        \n";
    $stSQL .="   cod_condicao::varchar||cod_assentamento::varchar||timestamp::varchar \n";
    $stSQL .="  FROM                                                        \n";
    $stSQL .="  pessoal.condicao_assentamento_excluido           \n";
    $stSQL .="  )                                                           \n";
    $stSQL .="AND a.timestamp = a_ult.timestamp                             \n";
    $stSQL .="AND a.cod_assentamento = a_ult.cod_assentamento               \n";

    return $stSQL;
}
}
