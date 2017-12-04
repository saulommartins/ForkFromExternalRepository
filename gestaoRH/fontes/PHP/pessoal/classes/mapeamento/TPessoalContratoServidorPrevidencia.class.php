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
  * Classe de mapeamento da tabela PESSOAL.CONTRATO_SERVIDOR_PREVIDENCIA
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CONTRATO_SERVIDOR_PREVIDENCIA
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorPrevidencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorPrevidencia()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_previdencia');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,cod_previdencia,timestamp');

    $this->AddCampo('cod_contrato','INTEGER',true,'',true,true);
    $this->AddCampo('cod_previdencia','INTEGER',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('bo_excluido','boolean',true,'false',false,false);
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT contrato_servidor_previdencia.*                                                              \n";
    $stSql .= "  FROM pessoal.contrato_servidor_previdencia                                                        \n";
    $stSql .= "     , (  SELECT cod_contrato                                                                       \n";
    $stSql .= "               , max(timestamp) as timestamp                                                        \n";
    $stSql .= "            FROM pessoal.contrato_servidor_previdencia                                              \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                 \n";
    $stSql .= " WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato  \n";
    $stSql .= "   AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp        \n";

    return $stSql;
}

function recuperaPrevidencias(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY contrato_servidor_previdencia.cod_contrato ";
    $stSql  = $this->montaRecuperaPrevidencias().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPrevidencias()
{
    $stSql  = "SELECT previdencia_previdencia.*                                                                    \n";
    $stSql .= "  FROM pessoal.contrato_servidor_previdencia                                                        \n";
    $stSql .= "     , (  SELECT cod_contrato                                                                       \n";
    $stSql .= "               , max(timestamp) as timestamp                                                        \n";
    $stSql .= "            FROM pessoal.contrato_servidor_previdencia                                              \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                 \n";
    $stSql .= "     , folhapagamento.previdencia                                                                   \n";
    $stSql .= "     , folhapagamento.previdencia_previdencia                                                       \n";
    $stSql .= "     , (  SELECT cod_previdencia                                                                    \n";
    $stSql .= "               , max(timestamp) as timestamp                                                        \n";
    $stSql .= "            FROM folhapagamento.previdencia_previdencia                                             \n";
    $stSql .= "        GROUP BY cod_previdencia) as max_previdencia_previdencia                                    \n";
    $stSql .= " WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato  \n";
    $stSql .= "   AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp        \n";
    $stSql .= "   AND contrato_servidor_previdencia.cod_previdencia = previdencia.cod_previdencia                  \n";
    $stSql .= "   AND previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia                        \n";
    $stSql .= "   AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia        \n";
    $stSql .= "   AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                    \n";

    return $stSql;
}

}
