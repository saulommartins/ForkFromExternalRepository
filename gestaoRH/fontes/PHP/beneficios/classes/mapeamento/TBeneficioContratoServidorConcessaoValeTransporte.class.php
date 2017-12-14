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
    * Classe de mapeamento da tabela BENEFICIO.CONTRATO_CONCESSAO_VALE_TRANSPORTE
    * Data de Criação: 11/10/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: tiago $
    $Date: 2007-06-28 14:46:50 -0300 (Qui, 28 Jun 2007) $

    * Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  BENEFICIO.CONTRATO_SERVIDOR_CONCESSAO_VALE_TRANSPORTE
  * Data de Criação: 11/10/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TBeneficioContratoServidorConcessaoValeTransporte extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TBeneficioContratoServidorConcessaoValeTransporte()
{
    parent::Persistente();
    $this->setTabela('beneficio.contrato_servidor_concessao_vale_transporte');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,cod_concessao,cod_mes,exercicio');

    $this->AddCampo('cod_contrato','integer',true,'',true,true);
    $this->AddCampo('cod_mes','integer',true,'',true,true);
    $this->AddCampo('cod_concessao','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('vigencia','date',true,'',false,false);

}

/**
  * Dado um cod_concessao, retorna a chave (cod_concessao,cod_mes,exercicio) vigente.
  * Ou seja, o menor mês da maior vigência, desde que a vigência seja maior ou igual ao dia atual.
  * Será utilizada nas inicializacoes dos meses posteriores.
  */
function montaRecuperaContratoServidorVigenciaAtual()
{
    $stSql .= "  SELECT MIN(Bcscvt.cod_mes) AS cod_mes                                                \n";
    $stSql .= "       , Bcscvt.cod_concessao                                                          \n";
    $stSql .= "       , Bcscvt.exercicio                                                              \n";
    $stSql .= "       , TO_CHAR(Bcscvt.vigencia,'dd/mm/yyyy') AS vigencia                             \n";
    $stSql .= "       , Bcscvt.cod_contrato                                                           \n";
    $stSql .= "    FROM beneficio.contrato_servidor_concessao_vale_transporte AS Bcscvt           \n";
    $stSql .= "   WHERE Bcscvt.vigencia = (                                                           \n";
    $stSql .= "         SELECT MAX(Bcscvt.vigencia)                                                   \n";
    $stSql .= "           FROM beneficio.contrato_servidor_concessao_vale_transporte AS Bcscvt    \n";
    $stSql .= "          WHERE Bcscvt.cod_concessao = ".$this->getDado('cod_concessao')."             \n";
    $stSql .= "            AND Bcscvt.vigencia <= now() )                                             \n";
    $stSql .= "     AND Bcscvt.cod_concessao = ".$this->getDado('cod_concessao')."                    \n";
    $stSql .= "GROUP BY                                                                               \n";
    $stSql .= "         Bcscvt.cod_concessao                                                          \n";
    $stSql .= "       , Bcscvt.exercicio                                                              \n";
    $stSql .= "       , Bcscvt.vigencia                                                               \n";
    $stSql .= "       , Bcscvt.cod_contrato                                                           \n";

    return $stSql;
}

function recuperaContratoServidorVigenciaAtual(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaContratoServidorVigenciaAtual().$stFiltro.$stOrdem;
    //$this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
  * Executa um join entre as tabelas contrato_servidor_concessao_vale_transporte e
  * concessao_vale_transporte, buscando contratos de acordo com o campo inicializado
  * da tabela concessao_vale_transporte.
  */
function montaRecuperaContratoServidorConcessaoValeTransporteSituacao()
{
    $stSql .= "SELECT Bcscvt.cod_contrato                                                 \n";
    $stSql .= "     , Bcscvt.cod_mes                                                      \n";
    $stSql .= "     , Bcscvt.cod_concessao                                                \n";
    $stSql .= "     , Bcscvt.exercicio                                                    \n";
    $stSql .= "     , TO_CHAR(Bcscvt.vigencia,'dd/mm/yyyy') AS vigencia                   \n";
    $stSql .= "     , Pc.registro                                                         \n";
    $stSql .= "  FROM beneficio.contrato_servidor_concessao_vale_transporte AS Bcscvt \n";
    $stSql .= "     , beneficio.concessao_vale_transporte AS Bcvt                     \n";
    $stSql .= "     , pessoal.contrato AS Pc                                          \n";
    $stSql .= " WHERE Bcscvt.cod_concessao = Bcvt.cod_concessao                           \n";
    $stSql .= "   AND Bcscvt.exercicio = Bcvt.exercicio                                   \n";
    $stSql .= "   AND Bcscvt.cod_mes = Bcvt.cod_mes                                       \n";
    $stSql .= "   AND Bcscvt.cod_contrato = Pc.cod_contrato                               \n";

    return $stSql;
}

function recuperaContratoServidorConcessaoValeTransporteSituacao(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaContratoServidorConcessaoValeTransporteSituacao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
  * Lista os contratos (distintos) que possuem uma concessao
  **/
function montaRecuperaContratoConcessao()
{
    $stSql .= "   SELECT Bcscvt.cod_contrato                                                  \n";
    $stSql .= "        , Pc.registro                                                          \n";
    $stSql .= "     FROM beneficio.contrato_servidor_concessao_vale_transporte AS Bcscvt  \n";
    $stSql .= "        , pessoal.contrato AS Pc                                           \n";
    $stSql .= "    WHERE Pc.cod_contrato = Bcscvt.cod_contrato                                \n";
    $stSql .= " GROUP BY Bcscvt.cod_contrato                                                  \n";
    $stSql .= "        , Pc.registro                                                          \n";
    $stSql .= " ORDER BY Bcscvt.cod_contrato                                                  \n";

    return $stSql;
}

function recuperaContratoConcessao(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaContratoConcessao().$stFiltro.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaUltimaVigenciaConcessao(&$rsRecordSet, $boTransacao  = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaUltimaVigenciaConcessao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaUltimaVigenciaConcessao()
{
    $stSql   = 'SELECT max(vigencia) AS max_vigencia FROM beneficio.contrato_servidor_concessao_vale_transporte';
    $stSql  .= '	WHERE cod_contrato = '.$this->getDado('cod_contrato');
    $stSql  .= '	AND cod_concessao = '.$this->getDado('cod_concessao');

    return $stSql;
}

}
